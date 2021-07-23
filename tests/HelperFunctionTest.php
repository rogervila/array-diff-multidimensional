<?php

namespace Rogervila\Test;

use PHPUnit\Framework\TestCase;

class HelperFunctionTest extends TestCase
{
    /** @test */
    public function it_returns_an_array()
    {
        $this->assertTrue(is_array(array_diff_multidimensional([], [])));
    }

    /** @test */
    public function it_calls_compare_method()
    {
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

        $this->assertEquals(
            [
                'c' => [
                    'f' => 'Hello'
                ],
            ],
            array_diff_multidimensional($new, $old)
        );
    }

    /** @test */
    public function loose_comparisons()
    {
        $new = [
            'a' => 'b',
            'c' => 288,
        ];

        $old = [
            'a' => 'b',
            'c' => '288',
        ];

        $this->assertEquals(0, count(array_diff_multidimensional($new, $old, false)));
        $this->assertFalse(isset(array_diff_multidimensional($new, $old, false)['c']));
    }

    /** @test */
    public function strict_comparisons()
    {
        $new = [
            'a' => 'b',
            'c' => 288,
        ];

        $old = [
            'a' => 'b',
            'c' => '288',
        ];

        $this->assertEquals(1, count(array_diff_multidimensional($new, $old)));
        $this->assertTrue(isset(array_diff_multidimensional($new, $old)['c']));
        $this->assertEquals(288, array_diff_multidimensional($new, $old)['c']);

        $this->assertEquals(1, count(array_diff_multidimensional($new, $old, true)));
        $this->assertTrue(isset(array_diff_multidimensional($new, $old, true)['c']));
        $this->assertEquals(288, array_diff_multidimensional($new, $old, true)['c']);
    }
}
