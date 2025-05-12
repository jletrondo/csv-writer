<?php

use Jletrondo\CsvWriter\CsvWriter;

beforeEach(function () {
    $this->file = 'tests/output/output';
    // Clean up the file before each test
    // $files = glob('tests/output/output*');
    // foreach ($files as $file) {
    //     if (is_file($file)) {
    //         unlink($file);
    //     }
    // }
});

it('writes a CSV string with header and rows', function () {
    $writer = new CsvWriter($this->file . '1.csv');
    $writer->setHeader(['id', 'name']);
    $writer->writeHeader();
    $writer->writeRows([
        [1, 'Alice'],
        [2, 'Bob'],
    ]);
    $writer->close();

    $csv = file_get_contents($this->file . '1.csv');

    // UTF-8 BOM + header + rows
    expect($csv)->toContain("\xEF\xBB\xBFid,name");
    expect($csv)->toContain("1,Alice");
    expect($csv)->toContain("2,Bob");
});

it('writes a CSV string without header', function () {
    $writer = new CsvWriter($this->file . '2.csv', [
        'has_header' => false,
    ]);
    $writer->writeRows([
        [1, 'Tina'],
        [2, 'Carl'],
    ]);
    $writer->close();

    $csv = file_get_contents($this->file . '2.csv');

    expect($csv)->not()->toContain('id,name');
    expect($csv)->toContain("1,Tina");
    expect($csv)->toContain("2,Carl");
});

it('writes to a file and checks file content', function () {
    $writer = new CsvWriter($this->file . '3.csv', [
        'has_header' => true,
    ]);
    $writer->setHeader(['id', 'name']);
    $writer->writeHeader();
    $writer->writeRow([1, 'Dave']);
    $writer->close();

    $content = file_get_contents($this->file . '3.csv');
    expect($content)->toContain("\xEF\xBB\xBFid,name");
    expect($content)->toContain("1,Dave");
});

it('uses custom delimiter - Pipe', function () {
    $writer = new CsvWriter($this->file . '4.csv', [
        'delimiter' => '|',
        'has_header' => true,
    ]);
    $writer->setHeader(['id', 'name']);
    $writer->writeHeader();
    $writer->writeRow([1, 'Alice']);
    $writer->close();

    $csv = file_get_contents($this->file . '4.csv');

    expect($csv)->toContain("id|name");
    expect($csv)->toContain("1|Alice");
});

it('uses custom delimiter - Semicolon', function () {
    $writer = new CsvWriter($this->file . '4.csv', [
        'delimiter' => ';',
        'has_header' => true,
    ]);
    $writer->setHeader(['id', 'name']);
    $writer->writeHeader();
    $writer->writeRow([1, 'Alice']);
    $writer->close();

    $csv = file_get_contents($this->file . '4.csv');

    expect($csv)->toContain("id;name");
    expect($csv)->toContain("1;Alice");
});

it('writes rows with special characters and escapes them', function () {
    $writer = new CsvWriter($this->file . '5.csv', [
        'has_header' => true,
    ]);
    $writer->setHeader(['text']);
    $writer->writeHeader();
    $writer->writeRow(['He said, "Hello!"']);
    $writer->close();

    $csv = file_get_contents($this->file . '5.csv');

    // Should properly enclose and escape quotes
    expect($csv)->toContain('"He said, ""Hello!"""');
});

it('writes a CSV with multiple columns and multiple rows', function () {
    $writer = new CsvWriter($this->file . 'multi.csv', [
        'has_header' => true,
    ]);
    $writer->setHeader(['id', 'name', 'email', 'age']);
    $writer->writeHeader();
    $writer->writeRows([
        [1, 'Alice', 'alice@example.com', 30],
        [2, 'Bob', 'bob@example.com', 25],
        [3, 'Charlie', 'charlie@example.com', 35],
    ]);
    $writer->close();

    $csv = file_get_contents($this->file . 'multi.csv');

    expect($csv)->toContain("\xEF\xBB\xBFid,name,email,age");
    expect($csv)->toContain("1,Alice,alice@example.com,30");
    expect($csv)->toContain("2,Bob,bob@example.com,25");
    expect($csv)->toContain("3,Charlie,charlie@example.com,35");
});

it('writes rows from column-oriented arrays using addRowsFromColumns', function () {
    $writer = new CsvWriter($this->file . 'columns.csv', [
        'has_header' => true,
    ]);
    $writer->setHeader(['first name', 'last name']);
    $writer->writeHeader();

    $columns = [
        'first name' => ['Bob', 'Alice', 'Dave'],
        'last name'  => ['Duval', 'Caminos', 'Philips'],
    ];
    $writer->addRowsFromColumns($columns);
    $writer->close();

    $csv = file_get_contents($this->file . 'columns.csv');

    expect($csv)->toContain("\xEF\xBB\xBF\"first name\",\"last name\"");
    expect($csv)->toContain("Bob,Duval");
    expect($csv)->toContain("Alice,Caminos");
    expect($csv)->toContain("Dave,Philips");
});

it('throws an exception if columns have different number of rows in addRowsFromColumns', function () {
    $writer = new CsvWriter($this->file . 'invalid_columns.csv', [
        'has_header' => true,
    ]);
    $writer->setHeader(['a', 'b']);
    $writer->writeHeader();

    $columns = [
        'a' => [1, 2],
        'b' => [3],
    ];

    expect(fn () => $writer->addRowsFromColumns($columns))
        ->toThrow(\InvalidArgumentException::class, 'All columns must have the same number of rows.');
});

it('does nothing if addRowsFromColumns is called with an empty array', function () {
    $writer = new CsvWriter($this->file . 'empty_columns.csv', [
        'has_header' => true,
    ]);
    $writer->setHeader(['foo', 'bar']);
    $writer->writeHeader();

    $writer->addRowsFromColumns([]);
    $writer->close();

    $csv = file_get_contents($this->file . 'empty_columns.csv');

    // Only header should be present
    expect($csv)->toContain("foo,bar");
    // No data rows
    $lines = array_filter(explode("\n", trim($csv)));
    expect(count($lines))->toBe(1);
});
