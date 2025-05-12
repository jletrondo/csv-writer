<?php

require 'vendor/autoload.php';

use Jletrondo\CsvWriter\CsvWriter;

$writer = new CsvWriter('tests/output/output.csv');
$writer->setHeader(['Name', 'Email']);
$writer->writeHeader();
$writer->writeRow(['John Doe', 'john@example.com']);
$writer->close();