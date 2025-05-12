<?php

namespace Jletrondo\CsvWriter;

class CsvWriter
{
    /**
     * @var string $delimiter
     * The character used to separate values in the CSV file.
     */
    private $delimiter = ',';

    /**
     * @var string $enclosure
     * The character used to enclose values in the CSV file.
     */
    private $enclosure = '"';

    /**
     * @var string $escape
     * The character used to escape special characters in the CSV file.
     */
    private $escape = '\\';

    /**
     * @var resource|null $handle
     * File handle for writing.
     */
    private $handle = null;

    /**
     * @var bool $has_header
     * Whether to write a header row.
     */
    private $has_header = true;

    /**
     * @var array $header
     * The header row to write.
     */
    private $header = [];

    /**
     * @var string|null $output_path
     * The file path to write to, or null for string output.
     */
    private $output_path = null;

    /**
     * Constructor.
     *
     * @param string|null $output_path If set, writes to file; otherwise, to string.
     * @param array $params Optional parameters (delimiter, enclosure, escape, has_header).
     */
    public function __construct(?string $output_path = null, array $params = [])
    {
        $this->output_path = $output_path;
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        if ($output_path) {
            $this->handle = fopen($output_path, 'w');
        } else {
            $this->handle = fopen('php://temp', 'w+');
        }
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    public function setEnclosure(string $enclosure): void
    {
        $this->enclosure = $enclosure;
    }

    public function setEscape(string $escape): void
    {
        $this->escape = $escape;
    }

    public function setHasHeader(bool $has_header): void
    {
        $this->has_header = $has_header;
    }

    public function setHeader(array $header): void
    {
        // Support array of arrays with 'header_name'
        if (!empty($header) && is_array($header[0]) && isset($header[0]['header_name'])) {
            $this->header = array_column($header, 'header_name');
        } else {
            $this->header = $header;
        }
    }

    /**
     * Write the header row (if enabled).
     */
    public function writeHeader(): void
    {
        if ($this->has_header && !empty($this->header)) {
            // Add UTF-8 BOM for Excel compatibility
            fwrite($this->handle, "\xEF\xBB\xBF");
            fputcsv($this->handle, $this->header, $this->delimiter, $this->enclosure, $this->escape);
        }
    }

    /**
     * Write a single row to the CSV.
     *
     * @param array $row
     */
    public function writeRow(array $row): void
    {
        if (!empty($this->header) && array_keys($row) !== range(0, count($row) - 1)) {
            // Map associative array to header order
            $orderedRow = [];
            foreach ($this->header as $col) {
                $orderedRow[] = $row[$col] ?? '';
            }
            fputcsv($this->handle, $orderedRow, $this->delimiter, $this->enclosure, $this->escape);
        } else {
            fputcsv($this->handle, $row, $this->delimiter, $this->enclosure, $this->escape);
        }
    }

    /**
     * Write multiple rows to the CSV.
     *
     * @param array $rows
     */
    public function writeRows(array $rows): void
    {
        foreach ($rows as $row) {
            $this->writeRow($row);
        }
    }

    /**
     * Add rows from a column-oriented array (header => [values]).
     *
     * @param array $columns
     * @throws \InvalidArgumentException
     */
    public function addRowsFromColumns(array $columns): void
    {
        if (empty($columns)) {
            return;
        }

        // Ensure all columns have the same number of rows
        $rowCount = null;
        foreach ($columns as $col => $values) {
            if (!is_array($values)) {
                throw new \InvalidArgumentException("Column '$col' must be an array.");
            }
            if ($rowCount === null) {
                $rowCount = count($values);
            } elseif (count($values) !== $rowCount) {
                throw new \InvalidArgumentException("All columns must have the same number of rows.");
            }
        }

        // Build row-oriented arrays and write them
        for ($i = 0; $i < $rowCount; $i++) {
            $row = [];
            foreach ($columns as $col => $values) {
                $row[$col] = $values[$i];
            }
            $this->writeRow($row);
        }
    }

    /**
     * Finalize and close the file handle.
     * If writing to string, returns the CSV content.
     *
     * @return string|null
     */
    public function close(): ?string
    {
        if (!$this->output_path) {
            rewind($this->handle);
            $csv = stream_get_contents($this->handle);
            fclose($this->handle);
            return $csv;
        }
        fclose($this->handle);
        return null;
    }
}