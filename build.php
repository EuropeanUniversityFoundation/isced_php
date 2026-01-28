<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use EasyRdf\Graph;
use EasyRdf\RdfNamespace;

RdfNamespace::set('dc', 'http://purl.org/dc/elements/1.1/');
RdfNamespace::set('dcterms', 'http://purl.org/dc/terms/');
RdfNamespace::set('ns5', 'http://publications.europa.eu/ontology/euvoc#');
RdfNamespace::set('skos', 'http://www.w3.org/2004/02/skos/core#');

$baseUri = 'http://data.europa.eu/snb/isced-f/25831c2';
$requests = 0;

$localeCopies = [
    'en' => ['en_GB'],
    'no' => ['nb'],
    'pt' => ['pt_PT'],
];

$tree = [
    'labels' => [],
    'broad' => [],
];

$graph = Graph::newAndLoad($baseUri);
echo($baseUri . "\n");
$requests += 1;

$topConcept = $graph->resourcesMatching('skos:hasTopConcept')[0];

$labels = $topConcept->allLiterals('skos:prefLabel');
foreach ($labels as $label) {
    /** @var \EasyRdf\Literal $label */
    $tree['labels'][$label->getLang()] = $label->getValue();
}
ksort($tree['labels']);

$broadConcepts = $graph->resourcesMatching('skos:topConceptOf');

foreach ($broadConcepts as $broad) {
    usleep(5000);
    $broadTree = [
        'labels' => [],
        'narrow' => [],
    ];

    $broadUri = $broad->getUri();
    $broadGraph = Graph::newAndLoad($broadUri);
    echo($broadUri . "\n");
    $requests += 1;
    $broadConcept = $broadGraph->resource($broadUri);

    $broadIdentifier = $broadConcept->get('dc:identifier');
    /** @var \EasyRdf\Literal $identifier */
    $broadId = $broadIdentifier->__toString();

    $broadLabels = $broadConcept->allLiterals('skos:prefLabel');
    foreach ($broadLabels as $label) {
        /** @var \EasyRdf\Literal $label */
        $broadTree['labels'][$label->getLang()] = $label->getValue();
    }
    ksort($broadTree['labels']);

    $narrowConcepts = $broadGraph->resourcesMatching('skos:broader');

    foreach ($narrowConcepts as $narrow) {
        usleep(5000);
        $narrowTree = [
            'labels' => [],
            'detailed' => [],
        ];

        $narrowUri = $narrow->getUri();
        $narrowGraph = Graph::newAndLoad($narrowUri);
        echo($narrowUri . "\n");
        $requests += 1;
        $narrowConcept = $narrowGraph->resource($narrowUri);

        $narrowIdentifier = $narrowConcept->get('dc:identifier');
        /** @var \EasyRdf\Literal $identifier */
        $narrowId = $narrowIdentifier->__toString();

        $narrowLabels = $narrowConcept->allLiterals('skos:prefLabel');
        foreach ($narrowLabels as $label) {
            /** @var \EasyRdf\Literal $label */
            $narrowTree['labels'][$label->getLang()] = $label->getValue();
        }
        ksort($narrowTree['labels']);

        $detailedConcepts = $narrowGraph->resourcesMatching('skos:broader');

        foreach ($detailedConcepts as $detailed) {
            usleep(5000);
            $detailedTree = [
                'labels' => [],
            ];

            $detailedUri = $detailed->getUri();

            if ($detailedUri != $narrowUri) {
                $detailedGraph = Graph::newAndLoad($detailedUri);
                echo($detailedUri . "\n");
                $requests += 1;
                $detailedConcept = $detailedGraph->resource($detailedUri);

                $detailedIdentifier = $detailedConcept->get('dc:identifier');
                /** @var \EasyRdf\Literal $identifier */
                $detailedId = $detailedIdentifier->__toString();

                $labels = $detailedConcept->allLiterals('skos:prefLabel');
                foreach ($labels as $label) {
                    /** @var \EasyRdf\Literal $label */
                    $detailedTree['labels'][$label->getLang()] = $label->getValue();
                }
                ksort($detailedTree['labels']);

                $narrowTree['detailed'][$detailedId] = $detailedTree;
            }
            
        }

        ksort($narrowTree['detailed']);
        $broadTree['narrow'][$narrowId] = $narrowTree;
            
    }

    ksort($broadTree['narrow']);
    $tree['broad'][$broadId] = $broadTree;

}

ksort($tree['broad']);

$treeJson = __DIR__ . '/tmp/tree.json';
file_put_contents($treeJson, json_encode($tree, JSON_PRETTY_PRINT));

$data = [];

foreach ($tree['broad'] as $broadId => $broadField) {
    $data[(string) $broadId] = [
        'label' => $broadField['labels']['en'],
        'broad' => (string) $broadId,
        'narrow' => null,
        'detailed' => null,
    ];

    foreach ($broadField['narrow'] as $narrowId => $narrowField) {
        $data[(string) $narrowId] = [
            'label' => $narrowField['labels']['en'],
            'broad' => (string) $broadId,
            'narrow' => (string) $narrowId,
            'detailed' => null,
        ];

        foreach ($narrowField['detailed'] as $detailedId => $detailedField) {
            $data[(string) $detailedId] = [
                'label' => $detailedField['labels']['en'],
                'broad' => (string) $broadId,
                'narrow' => (string) $narrowId,
                'detailed' => (string) $detailedId,
            ];
        }
    }
}

$dataJson = __DIR__ . '/tmp/data.json';
file_put_contents($dataJson, json_encode($data, JSON_PRETTY_PRINT));

echo(strval($requests) . " requests made.\n");
