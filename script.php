<?php
abstract class Expression
{
  abstract public function render($parenthesis = false);

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
    if (is_numeric($this->left)) {
      $this->left = new SingleExpression($this->left);
    }
    if (is_numeric($this->right)) {
      $this->right = new SingleExpression($this->right);
    }
    $this->operator = new SingleExpression($this->operator);
  }

  public function isValid() {
    return in_array((string) $this->operator, array_keys($this->commands)) &&
      $this->isValidOperand($this->left) &&
      $this->isValidOperand($this->right);
  }

  public function isValidOperand($operand) {
    return $operand instanceof Expression;
  }

  public function render($parenthesis = false) {
    $multiply = in_array($this->operator, array('*', '/'));
    $parts = array_filter(array($this->left, $this->operator, $this->right->render($multiply)));
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
