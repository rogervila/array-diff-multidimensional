<?php

use PHPUnit\Framework\TestCase;
use Rogervila\ArrayDiffMultidimensional;

class ArrayCompareTest extends TestCase
{
    /** @test */
    public function it_returns_an_array()
    {
        $diff = new ArrayDiffMultidimensional();
        $this->assertTrue(is_array($diff->compare([], [])));
    }

    /** @test */
    public function it_fails_if_first_argument_is_not_an_array()
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException(\InvalidArgumentException::class);
            $diff = new ArrayDiffMultidimensional();

            $diff->compare('this should be an array', 'whatever');
        } else {
            var_dump('Skipped since current PHPUnit version does not support expectException');
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function it_does_not_change_if_second_argument_is_not_an_array()
    {
        $diff = new ArrayDiffMultidimensional();

        $old = [
            'a' => 'b',
            'c' => [
                'd' => 'e',
                'ff' => [
                    'test'
                ]
            ],
        ];

        $new = 'anything except an array';

        $this->assertEquals($old, $diff->compare($old, $new));
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

        $this->assertEquals(1, count($diff->compare($new, $old)));
        $this->assertTrue(isset($diff->compare($new, $old)['c']));
        $this->assertTrue(is_string($diff->compare($new, $old)['c']));
        $this->assertFalse(isset($diff->compare($new, $old)['a']));
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

        $this->assertEquals(1, count($diff->compare($new, $old)));
        $this->assertTrue(is_array($diff->compare($new, $old)['c']));
        $this->assertFalse(isset($diff->compare($new, $old)['a']));
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

        $this->assertEquals(1, count($diff->compare($new, $old)));
        $this->assertTrue(isset($diff->compare($new, $old)['c']['f']));
        $this->assertFalse(isset($diff->compare($new, $old)['a']));
    }

    /** @test */
    public function it_detects_change_from_float_to_array()
    {
        $diff = new ArrayDiffMultidimensional();
        $newfloat = defined('PHP_FLOAT_MAX') ? PHP_FLOAT_MAX : 1.0000000000002;
        $oldfloat = 1.0000000000004;

        $new = [
            'a' => 'b',
            'c' => $newfloat,
        ];

        $old = [
            'a' => 'b',
            'c' => $oldfloat,
        ];

        $this->assertEquals(1, count($diff->compare($new, $old)));
        $this->assertEquals($newfloat, $diff->compare($new, $old)['c']);
        $this->assertTrue(is_float($diff->compare($new, $old)['c']));
        $this->assertFalse(isset($diff->compare($new, $old)['a']));
    }

    /** @test */
    public function it_detects_floats_do_not_change()
    {
        $diff = new ArrayDiffMultidimensional();
        $floatval = defined('PHP_FLOAT_MAX') ? PHP_FLOAT_MAX : 1.0000000000005;

        $new = [
            'a' => 'b',
            'c' => $floatval,
        ];

        $old = [
            'a' => 'd',
            'c' => $floatval,
        ];

        $this->assertEquals(1, count($diff->compare($new, $old)));
        $this->assertEquals('b', $diff->compare($new, $old)['a']);
        $this->assertFalse(isset($diff->compare($new, $old)['c']));
    }

    /** @test */
    public function it_works_with_deep_levels()
    {
        $diff = new ArrayDiffMultidimensional();

        $old = [
            'a' => 'b',
            'c' => [
                'd' => [
                    'e' => [
                        'f' => [
                            'g' => [
                                'h' => 'old'
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $new = [
            'a' => 'b',
            'c' => [
                'd' => [
                    'e' => [
                        'f' => [
                            'g' => [
                                'h' => 'new'
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $this->assertEquals(1, count($diff->compare($new, $old)));
        $this->assertEquals('new', $diff->compare($new, $old)['c']['d']['e']['f']['g']['h']);
        $this->assertFalse(isset($diff->compare($new, $old)['a']));
    }

    /** @test */
    public function it_detects_new_array_items()
    {
        $diff = new ArrayDiffMultidimensional();
        $value = 'this should be detected';

        $new = [
            'a' => 'b',
            'c' => 'd',
            'd' =>  $value,
        ];

        $old = [
            'a' => 'b',
            'c' => 'd',
        ];

        $this->assertEquals(1, count($diff->compare($new, $old)));
        $this->assertTrue(isset($diff->compare($new, $old)['d']));
        $this->assertEquals($value, $diff->compare($new, $old)['d']);
        $this->assertFalse(isset($diff->compare($new, $old)['a']));
        $this->assertFalse(isset($diff->compare($new, $old)['c']));
    }

    /** @test */
    public function it_detects_loose_changes_with_strict_mode()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'a' => 'b',
            'c' => 1714,
        ];

        $old = [
            'a' => 'b',
            'c' => '1714',
        ];

        $this->assertEquals(1, count($diff->compare($new, $old)));
        $this->assertTrue(isset($diff->compare($new, $old)['c']));
        $this->assertEquals(1714, $diff->compare($new, $old)['c']);

        $this->assertEquals(1, count($diff->compare($new, $old, true)));
        $this->assertTrue(isset($diff->compare($new, $old, true)['c']));
        $this->assertEquals(1714, $diff->compare($new, $old, true)['c']);

        $this->assertEquals(1, count($diff->strictComparison($new, $old)));
        $this->assertTrue(isset($diff->strictComparison($new, $old)['c']));
        $this->assertEquals(1714, $diff->strictComparison($new, $old)['c']);
    }

    /** @test */
    public function it_does_not_detect_loose_changes_without_strict_mode()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'a' => 'b',
            'c' => 1714,
        ];

        $old = [
            'a' => 'b',
            'c' => '1714',
        ];

        $this->assertEquals(0, count($diff->compare($new, $old, false)));
        $this->assertFalse(isset($diff->compare($new, $old, false)['c']));

        $this->assertEquals(0, count($diff->looseComparison($new, $old)));
        $this->assertFalse(isset($diff->looseComparison($new, $old)['c']));
    }

    /** @test */
    public function it_detects_epsilon_change_with_strict_mode()
    {
        if (defined('PHP_FLOAT_EPSILON')) {
            $diff = new ArrayDiffMultidimensional();

            $new = [123];
            $old = [PHP_FLOAT_EPSILON + 123];

            $this->assertEquals(1, count($diff->compare($new, $old)));
            $this->assertTrue(isset($diff->compare($new, $old)[0]));
            $this->assertTrue(is_int($diff->compare($new, $old)[0]));
            $this->assertEquals(123, $diff->compare($new, $old)[0]);
        } else {
            var_dump('Skipped since current PHP version does not have PHP_FLOAT_EPSILON defined');
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function it_does_not_detect_epsilon_change_with_strict_mode()
    {
        if (defined('PHP_FLOAT_EPSILON')) {
            $diff = new ArrayDiffMultidimensional();

            $new = [123];
            $old = [PHP_FLOAT_EPSILON + 123];

            $this->assertEquals(0, count($diff->looseComparison($new, $old)));
            $this->assertFalse(isset($diff->looseComparison($new, $old)[0]));
        } else {
            var_dump('Skipped since current PHP version does not have PHP_FLOAT_EPSILON defined');
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function it_detects_empty_array_change_with_strict_mode()
    {
        $diff = new ArrayDiffMultidimensional();

        $a = [[]];
        $b = [1];

        $this->assertEquals($a, $diff->compare($a, $b));
        $this->assertTrue(isset($diff->compare($a, $b)[0]));
    }

    /** @test */
    public function it_detects_empty_array_change_with_strict_mode_on_multiple_dimensions()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'a' => 'b',
            'c' => [
                'd' => [],
            ]
        ];

        $old = [
            'a' => 'b',
            'c' => [
                'd' => 1,
            ]
        ];

        $this->assertEquals([
            'c' => [
                'd' => [],
            ]
        ], $diff->compare($new, $old));
    }
}
