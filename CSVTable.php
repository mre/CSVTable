<?php

class CSVTable {

  public function __construct($csv, $delim = ',', $enclosure = '"', $table_separator = '|') {
    $this->csv = $csv;
    $this->delim = $delim;
    $this->enclosure = $enclosure;
    $this->table_separator = $table_separator;

    // Fill the rows with Markdown output
    $this->header = ""; // Table header
    $this->rows = ""; // Table rows
    $this->CSVtoTable($this->csv);
  }

  private function CSVtoTable() {
    $parsed_array = $this->toArray($this->csv);
    $this->length = $this->minRowLength($parsed_array);
    $this->col_widths = $this->maxColumnWidths($parsed_array);

    $header_array = array_shift($parsed_array);
    $this->header = $this->createHeader($header_array);
    $this->rows = $this->createRows($parsed_array);
  }

  /**
   * Convert the CSV into a PHP array
   */
  public function toArray($csv) {
    $parsed = str_getcsv($csv, "\n"); // Parse the rows
    $output = array();
    foreach($parsed as &$row) {
      $row = str_getcsv($row, $this->delim, $this->enclosure); // Parse the items in rows
      array_push($output, $row);
    }
    return $output;
  }

  private function createHeader($header_array) {
    return $this->createRow($header_array) . $this->createSeparator();
  }

  private function createSeparator() {
    $output = "";
    for ($i = 0; $i < $this->length - 1; ++$i) {
      $output .= str_repeat("-", $this->col_widths[$i]);
      $output .= $this->table_separator;
    }
    $last_index = $this->length - 1;
    $output .= str_repeat("-", $this->col_widths[$last_index]);
    return $output . "\n";
  }

  protected function createRows($rows) {
    $output = "";
    foreach ($rows as $row) {
      $output .= $this->createRow($row);
    }
    return $output;
  }

  /**
   * Add padding to a string
   */
  private function padded($str, $width) {
    if ($width < strlen($str)) {
      return $str;
    }
    $padding_length = $width - strlen($str);
    $padding = str_repeat(" ", $padding_length);
    return $str . $padding;
  }

  protected function createRow($row) {
    $output = "";
    // Only create as many columns as the minimal number of elements
    // in all rows. Otherwise this would not be a valid Markdown table
    for ($i = 0; $i < $this->length - 1; ++$i) {
      $element = $this->padded($row[$i], $this->col_widths[$i]);
      $output .= $element;
      $output .= $this->table_separator;
    }
    // Don't append a separator to the last element
    $last_index = $this->length - 1;
    $element = $this->padded($row[$last_index], $this->col_widths[$last_index]);
    $output .= $element;
    $output .= "\n"; // row ends with a newline
    return $output;
  }

  private function minRowLength($arr) {
    $min = PHP_INT_MAX;
    foreach ($arr as $row) {
      $row_length = count($row);
      if ($row_length < $min)
        $min = $row_length;
    }
    return $min;
  }

  /*
   * Calculate the maximum width of each column in characters
   */
  private function maxColumnWidths($arr) {
    // Set all column widths to zero.
    $column_widths = array_fill(0, $this->length, 0);
    foreach ($arr as $row) {
      foreach ($row as $k => $v) {
        if ($column_widths[$k] < strlen($v)) {
          $column_widths[$k] = strlen($v);
        }
        if ($k == $this->length - 1) {
          // We don't need to look any further since these elements
          // will be dropped anyway because all table rows must have the
          // same length to create a valid Markdown table.
          break;
        }
      }
    }
    return $column_widths;
  }

  public function getMarkup() {
    return $this->header . $this->rows;
  }
}

?>
