<?php

namespace Drupal\string_float;

use SplStack;

/**
 * Provides logic to convert a string into a float.
 */
class StringFloatService {

  const DELIMS = " \-,.!?:;\\/&\(\)\[\]";

  /**
   * Input string representing a float.
   *
   * @var string
   */
  protected $input;

  /**
   * Collection of tokens for the lookup.
   *
   * @var array
   */
  protected $tokens = [
    'zero'        => ['val' => '0', 'power' => 1],
    'a'           => ['val' => '1', 'power' => 1],
    'first'       => ['val' => '1', 'suffix' => 'st', 'power' => 1],
    'one'         => ['val' => '1', 'power' => 1],
    'second'      => ['val' => '2', 'suffix' => 'nd', 'power' => 1],
    'two'         => ['val' => '2', 'power' => 1],
    'third'       => ['val' => '3', 'suffix' => 'rd', 'power' => 1],
    'three'       => ['val' => '3', 'power' => 1],
    'fourth'      => ['val' => '4', 'suffix' => 'th', 'power' => 1],
    'four'        => ['val' => '4', 'power' => 1],
    'fifth'       => ['val' => '5', 'suffix' => 'th', 'power' => 1],
    'five'        => ['val' => '5', 'power' => 1],
    'sixth'       => ['val' => '6', 'suffix' => 'th', 'power' => 1],
    'six'         => ['val' => '6', 'power' => 1],
    'seventh'     => ['val' => '7', 'suffix' => 'th', 'power' => 1],
    'seven'       => ['val' => '7', 'power' => 1],
    'eighth'      => ['val' => '8', 'suffix' => 'th', 'power' => 1],
    'eight'       => ['val' => '8', 'power' => 1],
    'ninth'       => ['val' => '9', 'suffix' => 'th', 'power' => 1],
    'nine'        => ['val' => '9', 'power' => 1],
    'tenth'       => ['val' => '10', 'suffix' => 'th', 'power' => 1],
    'ten'         => ['val' => '10', 'power' => 10],
    'eleventh'    => ['val' => '11', 'suffix' => 'th', 'power' => 10],
    'eleven'      => ['val' => '11', 'power' => 10],
    'twelveth'    => ['val' => '12', 'suffix' => 'th', 'power' => 10],
    'twelfth'    => ['val' => '12', 'suffix' => 'th', 'power' => 10],
    'twelve'      => ['val' => '12', 'power' => 10],
    'thirteenth'  => ['val' => '13', 'suffix' => 'th', 'power' => 10],
    'thirteen'    => ['val' => '13', 'power' => 10],
    'fourteenth'  => ['val' => '14', 'suffix' => 'th', 'power' => 10],
    'fourteen'    => ['val' => '14', 'power' => 10],
    'fifteenth'   => ['val' => '15', 'suffix' => 'th', 'power' => 10],
    'fifteen'     => ['val' => '15', 'power' => 10],
    'sixteenth'   => ['val' => '16', 'suffix' => 'th', 'power' => 10],
    'sixteen'     => ['val' => '16', 'power' => 10],
    'seventeenth' => ['val' => '17', 'suffix' => 'th', 'power' => 10],
    'seventeen'   => ['val' => '17', 'power' => 10],
    'eighteenth'  => ['val' => '18', 'suffix' => 'th', 'power' => 10],
    'eighteen'    => ['val' => '18', 'power' => 10],
    'nineteenth'  => ['val' => '19', 'suffix' => 'th', 'power' => 10],
    'nineteen'    => ['val' => '19', 'power' => 10],
    'twentieth'   => ['val' => '20', 'suffix' => 'th', 'power' => 10],
    'twenty'      => ['val' => '20', 'power' => 10],
    'thirty'      => ['val' => '30', 'power' => 10],
    'forty'       => ['val' => '40', 'power' => 10],
    'fourty'      => ['val' => '40', 'power' => 10],
    'fifty'       => ['val' => '50', 'power' => 10],
    'sixty'       => ['val' => '60', 'power' => 10],
    'seventy'     => ['val' => '70', 'power' => 10],
    'eighty'      => ['val' => '80', 'power' => 10],
    'ninety'      => ['val' => '90', 'power' => 10],
    'hundred'     => ['val' => '100', 'power' => 100],
    'thousand'    => ['val' => '1000', 'power' => 1000],
    'million'     => ['val' => '1000000', 'power' => 1000000],
    'billion'     => ['val' => '1000000000', 'power' => 1000000000],
    'and'         => ['val' => '', 'power' => NULL],
    '-'           => ['val' => '', 'power' => NULL],
  ];

  /**
   * Main functionalities of a stack implemented using a doubly linked list.
   *
   * @var \splstack
   */
  private $splstack;

  /**
   * Float with the calculated output from input string.
   *
   * @var float
   */
  protected $output;

  /**
   * StringFloatService constructor.
   *
   * @param string $input
   *   The input string with words representing a number.
   */
  public function __construct($input) {
    if (!is_string($input)) {
      die(t("The given input is not a string"));
    }

    // Initialise the work stack.
    $this->splstack = new SplStack();
    // Cast to lowercase to make processing uniform.
    $this->input = strtolower($input);
  }

  /**
   * Converts a string to a float by splitting it up while maintaining powers.
   *
   * @return float|string|bool
   *   The calculated number (float if only numeric) or false on error.
   *
   */
  public function process() {
    $powers = array_column($this->tokens, 'power', 'val');

    $mutate = function ($parts) use (&$mutate, $powers) {
      $stack = $this->splstack;
      $sum   = 0;
      $last  = NULL;

      foreach ($parts as $idx => $arr) {
        $part = $arr['val'];

        if (!$stack->isEmpty()) {
          $check = $last ?? $part;

          if ((float) $stack->top() < 20 && (float) $part < 20 ?? (float) $part < $stack->top()) {
            return $stack->top() . (isset($parts[$idx - $stack->count()]['suffix']) ?
                $parts[$idx - $stack->count()]['suffix'] : '') . " " . $mutate(array_slice($parts, $idx));
          }

          if (isset($powers[$check]) && $powers[$check] <= $arr['power'] && $arr['power'] <= 10) {
            return $stack->top() .
              (isset($parts[$idx - $stack->count()]['suffix']) ?
                $parts[$idx - $stack->count()]['suffix'] : '')
              . " " . $mutate(array_slice($parts, $idx));
          }
          if ($stack->top() > $part) {
            if ($last >= 1000) {
              $sum += $stack->pop();
              $stack->push($part);
            }
            else {
              $stack->push($stack->pop() + (float) $part);
            }
          }
          else {
            $stack->push($stack->pop() * (float) $part);
          }
        }
        else {
          $stack->push($part);
        }

        $last = $part;
      }

      return $sum + $stack->pop();
    };

    // The actual split of the input string by given delimiters.
    $prepared = preg_split(
      '/([' . self::DELIMS . '])/',
      $this->input, -1,
      PREG_SPLIT_DELIM_CAPTURE);

    foreach ($prepared as $idx => $word) {
      if (is_array($word)) {
        continue;
      }

      // Part of string to check against the given tokens.
      $maybeNumPart = trim(strtolower($word));
      if (isset($this->tokens[$maybeNumPart])) {
        $item = $this->tokens[$maybeNumPart];
        if (isset($prepared[$idx + 1])) {
          $maybeDelim = $prepared[$idx + 1];
          if ($maybeDelim === " ") {
            $item['delim'] = $maybeDelim;
            unset($prepared[$idx + 1]);
          }
          elseif ($item['power'] == NULL && !isset($tokens[$maybeDelim])) {
            continue;
          }
        }
        $prepared[$idx] = $item;
      }
    }

    $result      = [];
    $accumulator = [];

    $getNumeral = function () use ($mutate, &$accumulator, &$result) {
      $last        = end($accumulator);
      $result[]    = $mutate($accumulator) .
        (isset($last['suffix']) ? $last['suffix'] : '') .
        (isset($last['delim']) ? $last['delim'] : '');
      $accumulator = [];
    };

    foreach ($prepared as $part) {
      if (is_array($part)) {
        $accumulator[] = $part;
      }
      else {
        if (!empty($accumulator)) {
          $getNumeral();
        }
        $result[] = $part;
      }
    }
    if (!empty($accumulator)) {
      $getNumeral();
    }

    $this->output = implode('', array_filter($result));

    // Cast to a float when the output is a digit only.
    $this->output = is_numeric($this->output) ? (float) $this->output : $this->output;

    return $this->output;
  }

  /**
   * Setter for the input string.
   *
   * @param string $input
   *   A written string representing a number.
   */
  public function setInput($input) {
    $this->input = $input;
  }

}
