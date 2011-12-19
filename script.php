<?php

class Converter
{

  const FORMAT_CLEAN = '%s';
  const FORMAT_PARENTHESIS = '(%s)';

  public function convert($string) {
    $commands = array(
      '+' => self::FORMAT_CLEAN,
      '-' => self::FORMAT_CLEAN,
      '*' => self::FORMAT_PARENTHESIS,
      '/' => self::FORMAT_PARENTHESIS
      );
    $pieces = explode(' ', $string);
    if (count($pieces) == 1) {
      return $string;
    }
    $output = array();
    $output[] = array_shift($pieces);
    $output[] = $operand = array_pop($pieces);
    $output[] = sprintf($commands[$operand], $this->convert($this->render($pieces)));
    return $this->render($output);
  }

  public function render(array $output) {
    return implode(' ', $output);
  }
}
