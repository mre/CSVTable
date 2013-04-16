<?php

require("CSVTable.php");

class CSVTableTest extends PHPUnit_Framework_TestCase
{
    protected function setUp() {
      $this->test_csv .= 
        "First Header,Second Header\n" .
        "Cell,Content Cell\n"          .
        "Content Cell,Another one";

      $this->test_table .=
        "First Header|Second Header\n" .
        "------------|-------------\n" .
        "Cell        |Content Cell \n" .
        "Content Cell|Another one  \n";
    }

    public function testParser() {

      $input = $this->test_csv;

      // Create a new CSV parser
      $parser = new CSVTable($input);

      // Create a Markdown table from the parsed input
      $this->assertEquals($this->test_table, $parser->getMarkup());
    }
}
?>
