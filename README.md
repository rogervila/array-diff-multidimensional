# Array Diff Multidimensional

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
