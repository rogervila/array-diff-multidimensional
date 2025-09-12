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

    /** @test */
    public function it_handles_null_values_correctly()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => null, 'b' => 'test'];
        $old = ['a' => '', 'b' => 'test'];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['a' => null], $result);
    }

    /** @test */
    public function it_handles_null_vs_missing_key()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => null, 'b' => 'test'];
        $old = ['b' => 'test'];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['a' => null], $result);
    }

    /** @test */
    public function it_handles_false_vs_empty_array()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => false, 'b' => []];
        $old = ['a' => [], 'b' => false];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['a' => false, 'b' => []], $result);
    }

    /** @test */
    public function it_handles_zero_values_correctly()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => 0, 'b' => '0', 'c' => 0.0];
        $old = ['a' => false, 'b' => 0, 'c' => '0'];

        $result = $diff->compare($new, $old, true);
        $this->assertEquals(['a' => 0, 'b' => '0', 'c' => 0.0], $result);

        $result = $diff->compare($new, $old, false);
        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_boolean_values()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => true, 'b' => false];
        $old = ['a' => 1, 'b' => 0];

        $result = $diff->compare($new, $old, true);
        $this->assertEquals(['a' => true, 'b' => false], $result);

        $result = $diff->compare($new, $old, false);
        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_mixed_numeric_types()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => 123, 'b' => 123.0, 'c' => '123'];
        $old = ['a' => '123', 'b' => 123, 'c' => 123.0];

        $result = $diff->compare($new, $old, true);
        $this->assertEquals(['a' => 123, 'b' => 123.0, 'c' => '123'], $result);

        $result = $diff->compare($new, $old, false);
        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_very_small_float_differences()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => 1.0000000000001];
        $old = ['a' => 1.0000000000002];

        $result = $diff->compare($new, $old, true);
        $this->assertEquals(['a' => 1.0000000000001], $result);
    }

    /** @test */
    public function it_handles_array_with_numeric_keys()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [0 => 'a', 1 => 'b', 2 => ['c' => 'd']];
        $old = [0 => 'a', 1 => 'x', 2 => ['c' => 'e']];

        $result = $diff->compare($new, $old);
        $this->assertEquals([1 => 'b', 2 => ['c' => 'd']], $result);
    }

    /** @test */
    public function it_handles_empty_vs_null_in_arrays()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => ['b' => []]];
        $old = ['a' => ['b' => null]];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['a' => ['b' => []]], $result);
    }

    /** @test */
    public function it_handles_nested_empty_arrays()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => ['b' => ['c' => []]]];
        $old = ['a' => ['b' => ['c' => ['d' => 'value']]]];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['a' => ['b' => ['c' => []]]], $result);
    }

    /** @test */
    public function it_handles_objects_correctly()
    {
        $diff = new ArrayDiffMultidimensional();

        $obj1 = new \stdClass();
        $obj1->prop = 'value1';

        $obj2 = new \stdClass();
        $obj2->prop = 'value2';

        $new = ['a' => $obj1];
        $old = ['a' => $obj2];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['a' => $obj1], $result);
    }

    /** @test */
    public function it_handles_large_nested_structures()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'level4' => [
                            'level5' => [
                                'data' => 'new_value',
                                'other' => 'same'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $old = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'level4' => [
                            'level5' => [
                                'data' => 'old_value',
                                'other' => 'same'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $diff->compare($new, $old);
        $expected = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'level4' => [
                            'level5' => [
                                'data' => 'new_value'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_handles_array_to_scalar_changes()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => 'scalar_value'];
        $old = ['a' => ['nested' => 'array']];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['a' => 'scalar_value'], $result);
    }

    /** @test */
    public function it_handles_complex_mixed_types()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'string' => 'test',
            'int' => 42,
            'float' => 3.14,
            'bool' => true,
            'null' => null,
            'array' => ['nested' => 'value'],
            'empty_array' => []
        ];

        $old = [
            'string' => 'different',
            'int' => 42,
            'float' => 3.14,
            'bool' => false,
            'null' => null,
            'array' => ['nested' => 'different'],
            'empty_array' => ['not_empty']
        ];

        $result = $diff->compare($new, $old);
        $expected = [
            'string' => 'test',
            'bool' => true,
            'array' => ['nested' => 'value'],
            'empty_array' => []
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_preserves_array_structure_in_results()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'users' => [
                0 => ['name' => 'John', 'age' => 30],
                1 => ['name' => 'Jane', 'age' => 25]
            ]
        ];

        $old = [
            'users' => [
                0 => ['name' => 'John', 'age' => 25],
                1 => ['name' => 'Jane', 'age' => 25]
            ]
        ];

        $result = $diff->compare($new, $old);
        $expected = [
            'users' => [
                0 => ['age' => 30]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_handles_performance_with_large_arrays()
    {
        $diff = new ArrayDiffMultidimensional();

        // Create large arrays for performance testing
        $new = [];
        $old = [];

        for ($i = 0; $i < 1000; $i++) {
            $new[$i] = ['data' => "value_$i", 'meta' => ['id' => $i]];
            $old[$i] = ['data' => "value_$i", 'meta' => ['id' => $i]];
        }

        // Change one value
        $new[500]['data'] = 'changed_value';

        $start = microtime(true);
        $result = $diff->compare($new, $old);
        $end = microtime(true);

        $this->assertEquals([500 => ['data' => 'changed_value']], $result);
        $this->assertLessThan(1.0, $end - $start, 'Performance test: should complete in less than 1 second');
    }
}
