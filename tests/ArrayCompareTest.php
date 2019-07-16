<?php

namespace Rogervila\Test;

use Rogervila\ArrayDiffMultidimensional;
use PHPUnit\Framework\TestCase;

class ArrayCompareTest extends TestCase
{
	protected $diff;

	protected function setUp()
	{
		$this->diff = new ArrayDiffMultidimensional();
	}

	public function testReturnsAnArray()
	{
		$this->assertTrue(is_array($this->diff->compare([], [])));
	}

	public function testDetectsTheDifferenceOnStringValue()
	{
		$old = [
			'a' => 'b',
			'c' => uniqid(),
		];

		$new = [
			'a' => 'b',
			'c' => uniqid(),
		];

		$this->assertEquals(count($this->diff->compare($new, $old)), 1);
		$this->assertTrue(isset($this->diff->compare($new, $old)['c']));
	}

	public function testDetectsChangeFromStringToArray()
	{
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

		$this->assertEquals(count($this->diff->compare($new, $old)), 1);
		$this->assertTrue(is_array($this->diff->compare($new, $old)['c']));
	}

	public function testDetectsChangesOnNestedArrays()
	{
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

		$this->assertEquals(count($this->diff->compare($new, $old)), 1);
		$this->assertTrue(isset($this->diff->compare($new, $old)['c']['f']));
	}
}
