<?php

use PHPUnit\Framework\TestCase;
use Rogervila\ArrayDiffMultidimensional;

class ArrayDiffPerformanceTest extends TestCase
{
    /** @test */
    public function it_handles_deeply_nested_identical_arrays_efficiently()
    {
        $diff = new ArrayDiffMultidimensional();

        $array = $this->createDeeplyNestedArray(10, 'same_value');

        $start = microtime(true);
        $result = $diff->compare($array, $array);
        $end = microtime(true);

        $this->assertEquals([], $result);
        $this->assertLessThan(0.1, $end - $start, 'Should handle identical nested arrays quickly');
    }

    /** @test */
    public function it_handles_wide_arrays_efficiently()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [];
        $old = [];

        // Create wide arrays with 10000 keys
        for ($i = 0; $i < 10000; $i++) {
            $new["key_$i"] = "value_$i";
            $old["key_$i"] = "value_$i";
        }

        // Change one value in the middle
        $new['key_5000'] = 'changed_value';

        $start = microtime(true);
        $result = $diff->compare($new, $old);
        $end = microtime(true);

        $this->assertEquals(['key_5000' => 'changed_value'], $result);
        $this->assertLessThan(0.5, $end - $start, 'Should handle wide arrays efficiently');
    }

    /** @test */
    public function it_handles_mixed_depth_arrays_efficiently()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [
            'shallow' => 'value',
            'deep' => $this->createDeeplyNestedArray(8, 'deep_value'),
            'wide' => array_fill_keys(range(0, 999), 'wide_value')
        ];

        $old = [
            'shallow' => 'value',
            'deep' => $this->createDeeplyNestedArray(8, 'old_deep_value'),
            'wide' => array_fill_keys(range(0, 999), 'wide_value')
        ];

        $start = microtime(true);
        $result = $diff->compare($new, $old);
        $end = microtime(true);

        $this->assertArrayHasKey('deep', $result);
        $this->assertLessThan(0.2, $end - $start, 'Should handle mixed depth arrays efficiently');
    }

    /** @test */
    public function it_handles_arrays_with_many_empty_subarrays()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = [];
        $old = [];

        for ($i = 0; $i < 1000; $i++) {
            $new["empty_$i"] = [];
            $old["empty_$i"] = [];
        }

        // Add one difference
        $new['empty_500'] = ['not_empty' => 'value'];

        $start = microtime(true);
        $result = $diff->compare($new, $old);
        $end = microtime(true);

        $this->assertEquals(['empty_500' => ['not_empty' => 'value']], $result);
        $this->assertLessThan(0.1, $end - $start, 'Should handle many empty arrays efficiently');
    }

    /** @test */
    public function it_handles_arrays_with_many_null_values()
    {
        $diff = new ArrayDiffMultidimensional();

        $new = array_fill_keys(range(0, 999), null);
        $old = array_fill_keys(range(0, 999), null);

        // Change one null to something else
        $new[500] = 'not_null';

        $start = microtime(true);
        $result = $diff->compare($new, $old);
        $end = microtime(true);

        $this->assertEquals([500 => 'not_null'], $result);
        $this->assertLessThan(0.1, $end - $start, 'Should handle many null values efficiently');
    }

    private function createDeeplyNestedArray($depth, $value)
    {
        if ($depth <= 0) {
            return $value;
        }

        return ['level' => $this->createDeeplyNestedArray($depth - 1, $value)];
    }
}
