<?php

class Expression
{
  const FORMAT_CLEAN = '%s %s %s';
  const FORMAT_PARENTHESIS = '(%s %s %s)';

  public $commands = array(
    '+' => self::FORMAT_CLEAN,
    '-' => self::FORMAT_CLEAN,
    '*' => self::FORMAT_PARENTHESIS,
    '/' => self::FORMAT_PARENTHESIS
  );

  public $left, $operand, $right;

  public function __construct($pieces) {
    list($this->left, $this->right, $this->operand) = $pieces;
  }

  public function isValid() {
    return in_array($this->operand, array_keys($this->commands));
  }

  public function __toString() {
    return sprintf($this->commands[$this->operand], $this->left, $this->operand, $this->right);
  }
}
class Converter
{
  public function convert($string) {
    $pieces = $this->split($string);
    $expression = new Expression($pieces);
    return $expression;
  }

  public function render(array $output) {
    return implode(' ', $output);
  }

  public function split($string) {
    return explode(' ', $string);
  }
}
