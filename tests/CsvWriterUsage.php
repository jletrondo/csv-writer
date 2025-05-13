<?php

require 'vendor/autoload.php';

use Jletrondo\CsvWriter\CsvWriter;
try {
    $writer = new CsvWriter('tests/output/output.csv');
    $writer->setHeader(['first name', 'last name']);
    $writer->writeHeader();

    $writer->writeRows([
        [1, 'Alice', 'alice@example.com', 30],
        [2, 'Bob', 'bob@example.com', 25],
        [3, 'Charlie', 'charlie@example.com', 35],
    ]);

    // $header_rows = [
    //     'first name' => ['Jason', 'Alice', 'Dave'],
    //     'last name'  => ['Duval', 'Caminos']
    // ];
    // $writer->addRowsFromColumns($header_rows);

    $csv = $writer->close();
    echo $csv;
} catch (Exception $e) {
    echo $e->getMessage();
}
