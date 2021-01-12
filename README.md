# Array Diff Multidimensional

[![Build Status](https://github.com/rogervila/array-diff-multidimensional/workflows/build/badge.svg)](https://github.com/rogervila/array-diff-multidimensional/actions)
[![StyleCI](https://styleci.io/repos/82589676/shield?branch=master)](https://styleci.io/repos/82589676)
[![Total Downloads](https://img.shields.io/packagist/dt/rogervila/array-diff-multidimensional)](https://packagist.org/packages/rogervila/array-diff-multidimensional)
[![Latest Stable Version](https://img.shields.io/packagist/v/rogervila/array-diff-multidimensional)](https://packagist.org/packages/rogervila/array-diff-multidimensional)
[![License](https://img.shields.io/packagist/l/rogervila/array-diff-multidimensional)](https://packagist.org/packages/rogervila/array-diff-multidimensional)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0d8faa82-5cd3-44dd-9759-a8a1b7b55fce/big.png)](https://insight.sensiolabs.com/projects/0d8faa82-5cd3-44dd-9759-a8a1b7b55fce)

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

var_dump(ArrayDiffMultidimensional::compare($new, $old));

```

The result of comparing `$new` with `$old` will return a new array with the changes:

```php
[
	'c' => [
		'f' => 'Hello'
 	],
]
```

## Strict vs Loose comparisons

**Comparisons are strict by default**, but you can specify that you want to do a loose comparison passing a boolean as a third parameter for `compare` method, or calling the `looseComparison`

```php
// This will deactivate the strict comparison mode
ArrayDiffMultidimensional::compare($new, $old, false);

// This method call is equivalent
ArrayDiffMultidimensional::looseComparison($new, $old);
```

Also, a `strictComparison` method is available for more clarity
```php
// Comparisons are strict by default
ArrayDiffMultidimensional::compare($new, $old);

// This method call is equivalent
ArrayDiffMultidimensional::strictComparison($new, $old);
```

## License

Array Diff Multidimensional is an open-sourced package licensed under the [MIT license](http://opensource.org/licenses/MIT).
