<?php

namespace Rogervila\Test;

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
}
