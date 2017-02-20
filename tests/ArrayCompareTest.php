<?php

namespace Rogervila\Test;

use Rogervila\ArrayDiffMultidimensional;

class ArrayCompareTest extends \PHPUnit_Framework_TestCase
{
	protected $diff;

	public function setUp()
	{
		$this->diff = new ArrayDiffMultidimensional();
	}

	/** @test */
	public function returnsAnArray()
	{
		$this->assertTrue( is_array($this->diff->compare([],[])) );
	}

	/** @test */
	public function DetectsTheDifferenceOnStringValue()
	{
		$old = [
			'a' => 'b',
			'c' => uniqid(),
		];

		$new = [
			'a' => 'b',
			'c' => uniqid(),
		];

		$this->assertEquals( count($this->diff->compare($new,$old)), 1 );
		$this->assertTrue( isset($this->diff->compare($new,$old)['c']) );
	}

	/** @test */
	public function DetectsChangeFromStringToArray()
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

		$this->assertEquals( count($this->diff->compare($new,$old)), 1 );
		$this->assertTrue( is_array($this->diff->compare($new,$old)['c']) );
	}

	/** @test */
	public function DetectsChangesOnNestedArrays()
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

		$this->assertEquals( count($this->diff->compare($new,$old)), 1 );
		$this->assertTrue( isset($this->diff->compare($new,$old)['c']['f']) );
	}
}