<?php
abstract class Expression
{
  abstract public function render($parenthesis = false);
  abstract public function isValid();

  public function __toString() {
    return $this->render();
  }

}

class OperationExpression extends Expression
{
  const FORMAT_CLEAN = '%s %s %s';
  const FORMAT_PARENTHESIS = '%s %s %s';

  public $commands = array(
    '+' => self::FORMAT_CLEAN,
    '-' => self::FORMAT_CLEAN,
    '*' => self::FORMAT_PARENTHESIS,
    '/' => self::FORMAT_PARENTHESIS
  );

  public $left, $operator, $right;

  public function __construct($left, $right, $operator) {
    $this->left = $left;
    $this->right = $right;
    $this->operator = $operator;
    if (!($this->left instanceof Expression)) {
      $this->left = new SingleExpression($this->left);
    }
    if (!($this->right instanceof Expression)) {
      $this->right = new SingleExpression($this->right);
    }
    $this->operator = new OperatorExpression($this->operator);
  }

  public function isValid() {
    return $this->operator->isValid() && $this->left->isValid() && $this->right->isValid();
  }

  public function isValidOperand($operand) {
    return $operand instanceof Expression;
  }

  public function render($parenthesis = false) {
    $multiply = $this->operator->isMultiply();
    $parts = array_filter(array($this->left->render($multiply), $this->operator, $this->right->render($multiply)));
    $output = implode(' ', $parts);
    return $parenthesis ? "($output)" : $output;
  }
}

class SingleExpression extends Expression
{
  public $char;
  public function __construct($char) {
    $this->char = $char;
  }
  public function render($parenthesis = false) {
    return (string) $this->char;
  }
  public function isValid() {
    return is_numeric($this->char);
  }
}
class OperatorExpression extends Expression
{
  public $operator;
  public function __construct($operator) {
    $this->operator = $operator;
  }
  public function render($parenthesis = false) {
    return (string) $this->operator;
  }
  public function isMultiply() {
    return in_array($this->operator, array('*', '/'));
  }
  public function isValid() {
    return in_array($this->operator, array('+', '-', '*', '/'));
  }
}

class Converter
{
  public function convert($string) {
    $pieces = $this->split($string);
    $pieces = $this->map($pieces);
    $expression = $this->reduce($pieces);
    return $this->render($expression);
  }

  private function map($pieces) {
#    foreach ($pieces as $i => $char) {
#      $pieces[$i] = new SingleExpression($char);
#    }
    return $pieces;
  }
  private function reduce($pieces) {
    $limit = (int) (count($pieces) / 2);
    for ($i = 0; $i < $limit; $i++) {
      $j = 0;
      while ($j <= count($pieces) - 3) {
        $slice = array_slice($pieces, $j, 3);
        $expression = new OperationExpression($slice[0], $slice[1], $slice[2]);
        if ($expression->isValid()) {
          $before = array_slice($pieces, 0, $j);
          $after  = array_slice($pieces, $j + 3);
          $pieces = array_merge($before, array($expression), $after);
        }
        $j++;
      }
    }
    return $pieces;
  }

  public function render(array $output) {
    return implode(' ', $output);
  }

  public function split($string) {
    return explode(' ', $string);
  }
}
