# Words to numbers conversion

Drupal 8 module with unit test class.

Converts words to numbers.

Outputs a string if the output is a mix of numbers and words.
Outputs a float when the string only contains a number
Outputs a string if the string contains different numbers.
Outputs a boolean (false) in other cases.


### Prerequisites

Drupal 8.

PhpUnit.

PHP 7.

### Installing

Download and enable the module.
Go to the path /test for a simple test form.

## Running the tests

vendor/bin/phpunit -c core/phpunit.xml.dist modules/custom/string_float

