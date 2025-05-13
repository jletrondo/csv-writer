<?php

require 'vendor/autoload.php';

use Jletrondo\CsvWriter\CsvWriter;
try {
    $writer = new CsvWriter('tests/output/output.csv');
    $writer->setHeader(['first name', 'last name']);
    $writer->writeHeader();

    $header_rows = [
        'first name' => ['Jason', 'Alice', 'Dave'],
        'last name'  => ['Duval', 'Caminos']
    ];
    $writer->addRowsFromColumns($header_rows);

    $csv = $writer->close();
    echo $csv;
} catch (Exception $e) {
    echo $e->getMessage();
}
