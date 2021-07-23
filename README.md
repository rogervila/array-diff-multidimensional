# Array Diff Multidimensional

[![Build Status](https://github.com/rogervila/array-diff-multidimensional/workflows/build/badge.svg)](https://github.com/rogervila/array-diff-multidimensional/actions)
[![StyleCI](https://styleci.io/repos/82589676/shield?branch=master)](https://styleci.io/repos/82589676)
[![Total Downloads](https://img.shields.io/packagist/dt/rogervila/array-diff-multidimensional)](https://packagist.org/packages/rogervila/array-diff-multidimensional)
[![Latest Stable Version](https://img.shields.io/packagist/v/rogervila/array-diff-multidimensional)](https://packagist.org/packages/rogervila/array-diff-multidimensional)
[![License](https://img.shields.io/packagist/l/rogervila/array-diff-multidimensional)](https://packagist.org/packages/rogervila/array-diff-multidimensional)

Works like the [PHP array_diff()](http://php.net/manual/es/function.array-diff.php) function, but with multidimensional arrays.

## Install

Via [composer](http://getcomposer.org):

```shell
composer require rogervila/array-diff-multidimensional
```

## Usage

```php
use Rogervila\ArrayDiffMultidimensional;

$new = [
	'a' => 'b',
	'c' => [
		'd' => 'e',
		'f' => 'Hello',
	],
];

$old = [
	'a' => 'b',
	'c' => [
		'd' => 'e',
		'f' => 'Goodbye',
	],
];

// Compare the arrays by calling the 'compare' class method
$result = ArrayDiffMultidimensional::compare($new, $old)

// Or by calling the global helper function
$result = array_diff_multidimensional($new, $old)


var_dump($result);
```

The result of comparing `$new` with `$old` will return a new array with the changes:

```php
[
	'c' => [
		'f' => 'Hello'
 	],
]
```

## Strict vs. Loose comparisons

**Comparisons are strict by default**, but you can specify that you want to make a loose comparison passing a boolean as the third parameter for `compare` method or calling the `looseComparison`

```php
// Passing 'false' as a third parameter will deactivate the strict comparison mode
ArrayDiffMultidimensional::compare($new, $old, false);

array_diff_multidimensional($new, $old, false);


// This method call is equivalent
ArrayDiffMultidimensional::looseComparison($new, $old);
```

Also, a `strictComparison` method is available for more clarity
```php
// Comparisons are strict by default
ArrayDiffMultidimensional::compare($new, $old);

array_diff_multidimensional($new, $old);


// This method call is equivalent
ArrayDiffMultidimensional::strictComparison($new, $old);
```

## License

Array Diff Multidimensional is an open-sourced package licensed under the [MIT license](http://opensource.org/licenses/MIT).
