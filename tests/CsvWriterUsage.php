<?php

require 'vendor/autoload.php';

use Jletrondo\CsvWriter\CsvWriter;

$writer = new CsvWriter('tests/output/output.csv');
$writer->setHeader(['first name', 'last name']);
$writer->writeHeader();

$header_rows = [
    'first name' => ['Jason', 'Alice', 'Dave'],
    'last name'  => ['Duval', 'Caminos', 'Philips']
];
$writer->addRowsFromColumns($header_rows);

$csv = $writer->close();
echo $csv;