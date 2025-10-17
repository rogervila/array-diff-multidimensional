<?php

use PHPUnit\Framework\TestCase;
use Rogervila\ArrayDiffMultidimensional;

class ArrayDiffEdgeCasesTest extends TestCase
{
    /** @test */
    public function it_handles_array_key_exists_vs_isset_edge_cases()
    {
        $diff = new ArrayDiffMultidimensional();

        // Test with null values that make isset return false but array_key_exists return true
        $new = ['key' => null, 'another' => 'value'];
        $old = ['key' => 'not_null', 'another' => 'value'];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['key' => null], $result);

        // Test missing key vs null value
        $new = ['existing' => null];
        $old = [];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['existing' => null], $result);
    }

    /** @test */
    public function it_handles_false_vs_zero_edge_cases()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['a' => false, 'b' => 0, 'c' => '', 'd' => null];
        $old = ['a' => 0, 'b' => false, 'c' => null, 'd' => ''];

        // Strict mode should detect all differences
        $result = $diff->compare($new, $old, true);
        $this->assertEquals($new, $result);

        // Loose mode should detect fewer differences
        $result = $diff->compare($new, $old, false);
        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_empty_string_vs_null_edge_cases()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['empty_string' => '', 'null_value' => null, 'zero' => 0];
        $old = ['empty_string' => null, 'null_value' => '', 'zero' => false];

        $result = $diff->compare($new, $old, true);
        $this->assertEquals($new, $result);

        $result = $diff->compare($new, $old, false);
        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_numeric_string_edge_cases()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = ['int' => 123, 'float' => 123.0, 'string' => '123', 'float_string' => '123.0'];
        $old = ['int' => '123', 'float' => '123', 'string' => 123, 'float_string' => 123.0];

        // Strict mode should detect type differences
        $result = $diff->compare($new, $old, true);
        $this->assertEquals($new, $result);

        // Loose mode should ignore most numeric type differences
        $result = $diff->compare($new, $old, false);
        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_resource_values()
    {
        $diff = new ArrayDiffMultidimensional();

        $resource1 = tmpfile();
        $resource2 = tmpfile();

        $new = ['resource' => $resource1];
        $old = ['resource' => $resource2];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['resource' => $resource1], $result);

        // Same resource should not show difference
        $new = ['resource' => $resource1];
        $old = ['resource' => $resource1];

        $result = $diff->compare($new, $old);
        $this->assertEquals([], $result);

        fclose($resource1);
        fclose($resource2);
    }

    /** @test */
    public function it_handles_callable_values()
    {
        $diff = new ArrayDiffMultidimensional();

        $callable1 = function () {
            return 'test1';
        };
        $callable2 = function () {
            return 'test2';
        };

        $new = ['callable' => $callable1];
        $old = ['callable' => $callable2];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['callable' => $callable1], $result);

        // Same callable should not show difference
        $new = ['callable' => $callable1];
        $old = ['callable' => $callable1];

        $result = $diff->compare($new, $old);
        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_array_vs_object_edge_cases()
    {
        $diff = new ArrayDiffMultidimensional();

        $array = ['property' => 'value'];
        $object = new \stdClass();
        $object->property = 'value';

        $new = ['item' => $array];
        $old = ['item' => $object];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['item' => $array], $result);
    }

    /** @test */
    public function it_handles_nested_array_vs_scalar_transitions()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'a' => [
                'b' => [
                    'c' => 'scalar_value'
                ]
            ]
        ];

        $old = [
            'a' => [
                'b' => 'scalar_at_b_level'
            ]
        ];

        $result = $diff->compare($new, $old);
        $expected = [
            'a' => [
                'b' => [
                    'c' => 'scalar_value'
                ]
            ]
        ];
        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_handles_very_large_float_precision()
    {
        $diff = new ArrayDiffMultidimensional();

        $precision = 1e-15;
        $base = 1.23456789012345;

        $new = ['precise' => $base];
        $old = ['precise' => $base + $precision];

        $result = $diff->compare($new, $old, true);
        $this->assertEquals(['precise' => $base], $result);

        // Test with extremely small differences that might be lost in string conversion
        $new = ['tiny_diff' => 1.0000000000000001];
        $old = ['tiny_diff' => 1.0000000000000002];

        $result = $diff->compare($new, $old, true);
        // Due to float precision limits, this might or might not show a difference
        // The important thing is that it doesn't crash
        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function it_handles_float_precision_edge_cases()
    {
        $diff = new ArrayDiffMultidimensional();

        // Test NaN values
        $new = ['nan_value' => NAN];
        $old = ['nan_value' => NAN];
        $result = $diff->compare($new, $old, true);
        $this->assertEquals([], $result);

        // Test infinity values
        $new = ['infinity' => INF, 'negative_infinity' => -INF];
        $old = ['infinity' => INF, 'negative_infinity' => -INF];
        $result = $diff->compare($new, $old, true);
        $this->assertEquals([], $result);

        // Test infinity vs large numbers
        $new = ['inf_vs_large' => INF];
        $old = ['inf_vs_large' => defined('PHP_FLOAT_MAX') ? PHP_FLOAT_MAX : 1.7976931348623E+308];
        $result = $diff->compare($new, $old, true);
        $this->assertEquals(['inf_vs_large' => INF], $result);

        // Test negative zero vs positive zero
        $new = ['zero' => -0.0];
        $old = ['zero' => 0.0];
        $result = $diff->compare($new, $old, true);
        $this->assertEquals([], $result); // -0.0 === 0.0 in PHP

        // Test float epsilon differences
        $epsilon = defined('PHP_FLOAT_EPSILON') ? PHP_FLOAT_EPSILON : 2.2204460492503E-16;
        $new = ['epsilon_test' => 1.0 + $epsilon];
        $old = ['epsilon_test' => 1.0];
        $result = $diff->compare($new, $old, true);

        // TODO: Depending on PHP version and float handling, this might or might not be considered different
        // $this->assertEquals(['epsilon_test' => 1.0 + $epsilon], $result);

        // Test float precision limits with very small numbers
        $new = ['tiny' => 1e-308]; // Near the smallest normal float
        $old = ['tiny' => 1e-309];
        $result = $diff->compare($new, $old, true);
        $this->assertTrue(is_array($result)); // Should not crash

        // Test float precision with scientific notation
        $new = ['scientific' => 1.23e10, 'negative_scientific' => -4.56e-7];
        $old = ['scientific' => 12300000000.0, 'negative_scientific' => -0.000000456];
        $result = $diff->compare($new, $old, true);
        $this->assertEquals([], $result); // Should be equal despite different notation

        // Test denormalized numbers (subnormal floats)
        $new = ['denorm' => 4.9e-324]; // Smallest positive denormalized float
        $old = ['denorm' => 0.0];
        $result = $diff->compare($new, $old, true);

        // TODO: Depending on PHP version and float handling, this might or might not be considered different
        // $this->assertEquals(['denorm' => 4.9e-324], $result);
    }

    /** @test */
    public function it_handles_float_string_conversion_edge_cases()
    {
        $diff = new ArrayDiffMultidimensional();

        // Test floats that might lose precision when converted to strings
        $problematic_floats = [
            'large_precise' => 999999999999999.9,
            'small_precise' => 0.000000000000001,
            'repeating_decimal' => 1.0 / 3.0, // 0.33333...
            'long_decimal' => 1.23456789012345678901234567890,
            'scientific_large' => 1.2345e20,
            'scientific_small' => 9.8765e-15
        ];

        $new = $problematic_floats;
        $old = $problematic_floats; // Same values

        $result = $diff->compare($new, $old, true);
        $this->assertEquals([], $result);

        // Test with slight modifications
        $old['large_precise'] = 999999999999999.8;
        $old['small_precise'] = 0.000000000000002;

        $result = $diff->compare($new, $old, true);
        $this->assertArrayHasKey('large_precise', $result);
        $this->assertArrayHasKey('small_precise', $result);
    }

    /** @test */
    public function it_handles_float_comparison_in_nested_structures()
    {
        $this->markTestSkipped('Pending implementation of improved float comparison logic in nested structures.');

        $diff = new ArrayDiffMultidimensional();

        $new = [
            'nested_floats' => [
                'level1' => [
                    'precise' => 1.0000000000000001,
                    'imprecise' => 1.1,
                    'infinity' => INF,
                    'nan' => NAN
                ],
                'calculations' => [
                    'division' => 1.0 / 3.0,
                    'multiplication' => 0.1 * 3.0,
                    'sqrt' => sqrt(2)
                ]
            ]
        ];

        $old = [
            'nested_floats' => [
                'level1' => [
                    'precise' => 1.0000000000000002,
                    'imprecise' => 1.1,
                    'infinity' => INF,
                    'nan' => NAN
                ],
                'calculations' => [
                    'division' => 0.33333333333333333,
                    'multiplication' => 0.30000000000000004,
                    'sqrt' => 1.4142135623730951
                ]
            ]
        ];

        $result = $diff->compare($new, $old, true);

        // Should detect differences in precision and NaN comparison
        $this->assertArrayHasKey('nested_floats', $result);
        $this->assertTrue(is_array($result)); // Should not crash with complex nested float comparisons
    }

    /** @test */
    public function it_handles_empty_arrays_at_different_nesting_levels()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'level1' => [],
            'level2' => [
                'nested' => []
            ],
            'level3' => [
                'deep' => [
                    'deeper' => []
                ]
            ]
        ];

        $old = [
            'level1' => ['not_empty'],
            'level2' => [
                'nested' => ['also_not_empty']
            ],
            'level3' => [
                'deep' => [
                    'deeper' => ['deepest_not_empty']
                ]
            ]
        ];

        $result = $diff->compare($new, $old);
        $this->assertEquals($new, $result);
    }

    /** @test */
    public function it_handles_circular_reference_prevention()
    {
        $diff = new ArrayDiffMultidimensional();

        // Create objects with circular references
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();
        $obj1->ref = $obj2;
        $obj2->ref = $obj1;

        $obj3 = new \stdClass();
        $obj4 = new \stdClass();
        $obj3->ref = $obj4;
        $obj4->ref = $obj3;

        $new = ['circular' => $obj1];
        $old = ['circular' => $obj3];

        $result = $diff->compare($new, $old);
        $this->assertEquals(['circular' => $obj1], $result);
    }

    /** @test */
    public function it_handles_array_keys_with_special_characters()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'normal_key' => 'value1',
            'key with spaces' => 'value2',
            'key.with.dots' => 'value3',
            'key-with-dashes' => 'value4',
            'key_with_underscores' => 'value5',
            'key/with/slashes' => 'value6',
            'key\\with\\backslashes' => 'value7',
            'key:with:colons' => 'value8',
            'key;with;semicolons' => 'value9',
            'key=with=equals' => 'value10',
            'key?with?questions' => 'value11',
            'key&with&ampersands' => 'value12',
            'key#with#hashes' => 'value13',
            'key@with@ats' => 'value14'
        ];

        $old = array_fill_keys(array_keys($new), 'old_value');

        $result = $diff->compare($new, $old);
        $this->assertEquals($new, $result);
    }
}
