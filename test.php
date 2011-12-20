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

    $this->assertEquals($expectation, (string) $converter->convert($infix));
  }

  /**
   * @dataProvider dataProviderExpression
   */
  public function testExpression($values,$expectation) {
    $expression = new OperationExpression(new SingleExpression($values[0]), new SingleExpression($values[1]), new SingleExpression($values[2]));
    $this->assertEquals($expectation, $expression->isValid());
  }

  public function dataProviderExpression() {
    return array(
      array(array(5, 3, '+'), true),
      array(array(5, '+', 3), false),
    );
  }

  public function dataProviderConversion() {
    return array(
      array('2 3 +', '2 + 3'),
      array('2 3 5 + +', '2 + 3 + 5'),
      array('2 3 5 + *', '2 * (3 + 5)'),
      array('2 3 5 + *', '2 * (3 + 5)'),
      array('56 34 213.7 + * 678 -', '56 * (34 + 213.7) - 678'),
    );
  }
}
