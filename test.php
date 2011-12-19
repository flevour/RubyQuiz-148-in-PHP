<?php
require_once(__DIR__ . '/script.php');
class ScriptTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
  }
  /**
   * @dataProvider dataProviderConversion
   */
  public function testConversion($infix, $expectation) {
    $converter = new Converter();

    $this->assertEquals($expectation, $converter->convert($infix));
  }

  public function dataProviderConversion() {
    return array(
      array('1', '1'),
      array('2 3 +', '2 + 3'),
      array('2 3 5 + +', '2 + 3 + 5'),
      array('2 3 5 + *', '2 * (3 + 5)'),
      array('2 3 5 + *', '2 * (3 + 5)'),
#      array('56 34 213.7 + * 678 -', '56 * (34 + 213.7) - 678'),
    );
  }
}
