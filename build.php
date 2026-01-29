<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brick\VarExporter\VarExporter;
use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'autoescape' => false,
]);
$twig->addExtension(new IntlExtension());

RdfNamespace::set('dc', 'http://purl.org/dc/elements/1.1/');
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
    /** @var \EasyRdf\Literal $broadIdentifier */
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
        /** @var \EasyRdf\Literal $narrowIdentifier */
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
                /** @var \EasyRdf\Literal $detailedIdentifier */
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

$dataFile = __DIR__ . '/data/isced.php';
$dataFileHeader = "<?php\n\n";
$dataExport = VarExporter::export(
    $data,
    VarExporter::ADD_RETURN | VarExporter::TRAILING_COMMA_IN_ARRAY,
);
$dataFileContent = $dataFileHeader . $dataExport;
file_put_contents($dataFile, $dataFileContent);

echo(strval($requests) . " requests made.\n");

$translations = [];

$enLabel = $tree['labels']['en'];
foreach ($tree['labels'] as $langcode => $label) {
    /** @var string $enLabel */
    $translations[$langcode][$enLabel] = $label;
}

foreach ($tree['broad'] as $broad) {
    /** @var string $enLabel */
    $enLabel = $broad['labels']['en'];
    foreach ($broad['labels'] as $langcode => $label) {
        $translations[$langcode][$enLabel] = $label;
    }

    foreach ($broad['narrow'] as $narrow) {
        /** @var string $enLabel */
        $enLabel = $narrow['labels']['en'];
        foreach ($narrow['labels'] as $langcode => $label) {
            $translations[$langcode][$enLabel] = $label;
        }

        foreach ($narrow['detailed'] as $detailed) {
            /** @var string $enLabel */
            $enLabel = $detailed['labels']['en'];
            foreach ($detailed['labels'] as $langcode => $label) {
                $translations[$langcode][$enLabel] = $label;
            }
        }
    }
}

echo(strval(count($translations, COUNT_RECURSIVE)) . " translations.\n");

$messages = [];
foreach ($translations as $langcode => $list) {
    $messages[$langcode] = [];
    foreach ($list as $key => $value) {
        $messages[$langcode][] = [
            'msgid' => $key,
            'msgstr' => $value,
        ];
    }
}

echo(strval(count($messages, COUNT_RECURSIVE)) . " messages.\n");

foreach ($localeCopies as $source => $targets) {
    foreach ($targets as $target) {
        $messages[$target] = $messages[$source];
    }
}

foreach ($messages as $langcode => $messages) {
    if ($langcode != 'en') {
        $twigData = [
            'langcode' => $langcode,
            'messages' => $messages,
        ];

        $content = $twig->render('translation.po.twig', $twigData);

        $dir = __DIR__ . '/translations/' . $langcode . '/LC_MESSAGES/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($dir . 'isced.po', $content);
    }
}
