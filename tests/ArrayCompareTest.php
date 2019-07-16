<?php

namespace Rogervila\Test;

use Rogervila\ArrayDiffMultidimensional;
use PHPUnit\Framework\TestCase;

class ArrayCompareTest extends TestCase
{
	public function testReturnsAnArray()
	{
		$diff = new ArrayDiffMultidimensional();

		$this->assertTrue(is_array($diff->compare([], [])));
	}

	public function testDetectsTheDifferenceOnStringValue()
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

	public function testDetectsChangeFromStringToArray()
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

	public function testDetectsChangesOnNestedArrays()
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
}
