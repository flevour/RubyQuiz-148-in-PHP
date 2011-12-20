<?php

class Expression
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

  public function __construct($pieces) {
    list($this->left, $this->right, $this->operator) = $pieces;
    if (is_numeric($this->left)) {
      $this->left = new IntExpression($this->left);
    }
    if (is_numeric($this->right)) {
      $this->right = new IntExpression($this->right);
    }
  }

  public function isValid() {
    return in_array($this->operator, array_keys($this->commands)) &&
      $this->isValidOperand($this->left) &&
      $this->isValidOperand($this->right);
  }

  public function isValidOperand($operand) {
    return $operand instanceof IntExpression || $operand instanceof Expression;
  }

  public function render($parenthesis = false) {
    $multiply = in_array($this->operator, array('*', '/'));
    $parts = array_filter(array($this->left, $this->operator, $this->right->render($multiply)));
    $output = implode(' ', $parts);
    return $parenthesis ? "($output)" : $output;
  }

  public function __toString() {
    return $this->render();
  }
}

class IntExpression
{
  public $int;
  public function __construct($int) {
    $this->int = $int;
  }
  public function render() {
    return $this->int;
  }
  public function __toString() {
    return $this->render();
  }
}

class Converter
{
  public function convert($string) {
    $pieces = $this->split($string);
    $expression = $this->reduce($pieces);
    return $this->render($expression);
  }

  private function reduce($pieces) {
    $limit = (int) (count($pieces) / 2);
    for ($i = 0; $i < $limit; $i++) {
      $j = 0;
      while ($j <= count($pieces) - 3) {
        $slice = array_slice($pieces, $j, 3);
        $expression = new Expression($slice);
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
