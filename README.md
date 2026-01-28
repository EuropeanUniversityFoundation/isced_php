# ISCED-F for PHP

This library provides a curated list of ISCED-F 2013 fields of study for use in PHP applications.

## Installation

    composer require euf/isced

## Usage example

```php
<?php

require_once 'vendor/autoload.php';

use Isced\IscedFieldsOfStudy;

$iscedF = new IscedFieldsOfStudy();

$list = $iscedF->getList();

try {
  $is = $iscedF->isBroad("0711");
} catch (\Throwable $th) {
  echo($th->getMessage());
}

$tree = $iscedF->getTree();

echo(json_encode($tree, JSON_PRETTY_PRINT) . "\n");

```

## Development

    git clone https://github.com/EuropeanUniversityFoundation/elm_vocabularies_php.git
    composer update --prefer-stable
    phpstan analyze src/ data/ --level=10
    phpcs src/ data/

