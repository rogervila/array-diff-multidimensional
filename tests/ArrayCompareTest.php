<?php

namespace Rogervila\Test;

use Rogervila\ArrayDiffMultidimensional;
use PHPUnit\Framework\TestCase;

class ArrayCompareTest extends TestCase
{
	/** @test */
	public function it_returns_an_array()
	{
		$diff = new ArrayDiffMultidimensional();
		$this->assertTrue(is_array($diff->compare([], [])));
	}

	/** @test */
	public function it_detects_the_difference_on_string_value()
	{
		$diff = new ArrayDiffMultidimensional();

		$old = [
			'a' => 'b',
			'c' => uniqid(),
		];

		$new = [
			'a' => 'b',
			'c' => uniqid(),
		];

		$this->assertEquals(count($diff->compare($new, $old)), 1);
		$this->assertTrue(isset($diff->compare($new, $old)['c']));
	}

	/** @test */
	public function it_detects_change_from_string_to_array()
	{
		$diff = new ArrayDiffMultidimensional();

		$new = [
			'a' => 'b',
			'c' => [
				'd' => uniqid(),
				'e' => uniqid(),
			],
		];

		$old = [
			'a' => 'b',
			'c' => uniqid(),
		];

		$this->assertEquals(count($diff->compare($new, $old)), 1);
		$this->assertTrue(is_array($diff->compare($new, $old)['c']));
	}

	/** @test */
	public function it_detects_changes_on_nested_arrays()
	{
		$diff = new ArrayDiffMultidimensional();

		$new = [
			'a' => 'b',
			'c' => [
				'd' => 'e',
				'f' => uniqid(),
			],
		];

		$old = [
			'a' => 'b',
			'c' => [
				'd' => 'e',
				'f' => uniqid(),
			],
		];

		$this->assertEquals(count($diff->compare($new, $old)), 1);
		$this->assertTrue(isset($diff->compare($new, $old)['c']['f']));
	}

	/** @test */
	public function it_detects_change_from_float_to_array()
	{
		$diff = new ArrayDiffMultidimensional();
		$newfloat = array_rand(array(
			(defined('PHP_FLOAT_MAX') ? PHP_FLOAT_MAX : 1.0),
			1.0000000000002,
			1.0000000000002293847234,
			1.908172398123987,
		));
		$oldfloat = array_rand(array(
			1.0000000000002123,
			1.981723987123871823,
			1.374384728374923784,
		));

		$new = [
			'a' => 'b',
			'c' => $newfloat,
		];

		$old = [
			'a' => 'b',
			'c' => $oldfloat,
		];

		var_dump($diff->compare($new, $old));
		$this->assertEquals(count($diff->compare($new, $old)), 1);
		$this->assertEquals($diff->compare($new, $old)['c'], $newfloat);
		//$this->assertTrue(is_float($diff->compare($new, $old)['c']));
	}
}
