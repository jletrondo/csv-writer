# CsvWriter

A simple and flexible CSV writer library for PHP.

## Features

- Write CSV files with custom delimiters, enclosures, and escape characters
- Supports writing to file or to string
- Easily set headers and write header rows
- Write rows as arrays or associative arrays
- Add rows from column-oriented arrays
- UTF-8 BOM support for Excel compatibility

## Installation
To use the CsvReader library, include the file in your project and ensure that you have the necessary dependencies installed. You can install it via Composer:

```bash
composer require jletrondo/csv-writer
```

## Usage
### Creating an Instance
To use the CsvReader library, include the following use statement at the top of your PHP file:
```php
use Jletrondo\CsvReader\CsvReader;
```
You can create an instance of the CsvWriter class by passing optional parameters to the constructor.

```php
    use Jletrondo\CsvWriter\CsvWriter;

    $writer = new CsvWriter('output.csv');
    $writer->setHeader(['Name', 'Email', 'Age']);
    $writer->writeHeader();
    $writer->writeRow(['John Doe', 'john@example.com', 30]);
    $writer->writeRow(['Jane Smith', 'jane@example.com', 25]);
    $writer->close();
```

### Write to String (no file)
```php
    use Jletrondo\CsvWriter\CsvWriter;
    $writer = new CsvWriter(null); // No file path = write to string
    $writer->setHeader(['Product', 'Price']);
    $writer->writeHeader();
    $writer->writeRows([
        ['Apple', 1.2],
        ['Banana', 0.8],
    ]);
    $csvString = $writer->close();
    echo $csvString;
```

### Associative Arrays (header mapping)
```php
    use Jletrondo\CsvWriter\CsvWriter;

    $writer = new CsvWriter('users.csv');
    $writer->setHeader(['id', 'username', 'email']);
    $writer->writeHeader();
    $writer->writeRow(['id' => 1, 'username' => 'alice', 'email' => 'alice@example.com']);
    $writer->writeRow(['id' => 2, 'username' => 'bob', 'email' => 'bob@example.com']);
    $writer->close();
```

### Column-Oriented Data

```php
    use Jletrondo\CsvWriter\CsvWriter;

    $writer = new CsvWriter('columns.csv');
    $writer->setHeader(['id', 'score']);
    $writer->writeHeader();
    $columns = [
        'id' => [1, 2, 3],
        'score' => [90, 85, 88],
    ];
    $writer->addRowsFromColumns($columns);
    $writer->close();
```

### Custom Delimiter, Enclosure, Escape

```php
    use Jletrondo\CsvWriter\CsvWriter;

    $params = [
        'delimiter' => ';',
        'enclosure' => "'",
        'escape' => '\\',
        'has_header' => true,
    ];
    $writer = new CsvWriter('custom.csv', $params);
    $writer->setHeader(['a', 'b']);
    $writer->writeHeader();
    $writer->writeRow(['x', 'y']);
    $writer->close();
```

## API

### Constructor

```php
CsvWriter::construct(?string $output_path = null, array $params = [])
```

- `output_path`: File path to write to, or `null` for string output.
- `params`: Optional. Keys: `delimiter`, `enclosure`, `escape`, `has_header`.

### Methods

- `setDelimiter(string $delimiter): void`
- `setEnclosure(string $enclosure): void`
- `setEscape(string $escape): void`
- `setHasHeader(bool $has_header): void`
- `setHeader(array $header): void`
- `writeHeader(): void`
- `writeRow(array $row): void`
- `writeRows(array $rows): void`
- `addRowsFromColumns(array $columns): void`
- `close(): ?string` — Finalize and close. Returns CSV string if writing to string.


## License

MIT

---
