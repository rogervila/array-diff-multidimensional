# Array Diff Multidimensional

[![Build Status](https://travis-ci.org/rogervila/array-diff-multidimensional.svg?branch=master)](https://travis-ci.org/rogervila/array-diff-multidimensional)
[![Build status](https://ci.appveyor.com/api/projects/status/wues2pcnb6s07bbl?svg=true)](https://ci.appveyor.com/project/roger-vila/array-diff-multidimensional)
[![Code Climate](https://codeclimate.com/github/rogervila/array-diff-multidimensional/badges/gpa.svg)](https://codeclimate.com/github/rogervila/array-diff-multidimensional)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0d8faa82-5cd3-44dd-9759-a8a1b7b55fce/big.png)](https://insight.sensiolabs.com/projects/0d8faa82-5cd3-44dd-9759-a8a1b7b55fce)

Works like the [PHP array_diff()](http://php.net/manual/es/function.array-diff.php) function, but with multidimensional arrays.

## Install

Via [composer](http://getcomposer.org):

```shell
composer require rogervila/array-diff-multidimensional
```

## Usage

```php
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

var_dump(ArrayDiffMultidimensional::compare($new,$old));

```

The result of comparing `$new` with `$old` will return a new array with the changes:

```php
[
	'c' => [
		'f' => 'Hello'
 	],
]
```

## License

Array Diff Multidimensional is an open-sourced package licensed under the [MIT license](http://opensource.org/licenses/MIT).
