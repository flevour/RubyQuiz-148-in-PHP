<?php
abstract class Expression
{
  protected $value;
  abstract public function isValid();

  public function __construct($value) {
    $this->value = $value;
  }
  public function __toString() {
    return $this->render();
  }

  public function render($parenthesis = false) {
    return (string) $this->value;
  }
}

class OperationExpression extends Expression
{
  public $left, $operator, $right;

  public function __construct(Expression $left, Expression $right, Expression $operator) {
    $this->left = $left;
    $this->right = $right;
    // force to OperatorExpression
    $this->operator = new OperatorExpression($operator);
  }

  public function isValid() {
    return $this->operator->isValid() && $this->left->isValid() && $this->right->isValid();
  }

  public function render($parenthesis = false) {
    $multiply = $this->operator->isMultiply();
    $parts = array($this->left->render($multiply), $this->operator, $this->right->render($multiply));
    $output = implode(' ', $parts);
    return $parenthesis ? "($output)" : $output;
  }
}

class SingleExpression extends Expression
{
  public function isValid() {
    return is_numeric($this->value);
  }
}
class OperatorExpression extends Expression
{
  public function isMultiply() {
    return in_array($this->value, array('*', '/'));
  }
  public function isValid() {
    return in_array($this->value, array('+', '-', '*', '/'));
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
    foreach ($pieces as $i => $char) {
      $pieces[$i] = new SingleExpression($char);
    }
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
