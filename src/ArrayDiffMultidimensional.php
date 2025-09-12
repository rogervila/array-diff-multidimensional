<?php

namespace Rogervila;

class ArrayDiffMultidimensional
{
    /**
     * Returns an array with the differences between $array1 and $array2
     * $strict variable defines if comparison must be strict or not
     *
     * @param array $array1
     * @param mixed $array2
     * @param bool $strict
     *
     * @return array
     */
    public static function compare($array1, $array2, $strict = true)
    {
        if (!is_array($array1)) {
            throw new \InvalidArgumentException('$array1 must be an array!');
        }

        if (!is_array($array2)) {
            return $array1;
        }

        $result = array();

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            $value2 = $array2[$key];

            if (is_array($value)) {
                if (empty($value)) {
                    if (!is_array($value2) || !empty($value2)) {
                        $result[$key] = $value;
                    }
                    continue;
                }

                // Only recurse if both are arrays
                if (is_array($value2)) {
                    $recursiveArrayDiff = static::compare($value, $value2, $strict);
                    if (!empty($recursiveArrayDiff)) {
                        $result[$key] = $recursiveArrayDiff;
                    }
                } else {
                    $result[$key] = $value;
                }
                continue;
            }

            // Handle float comparison optimization
            if ($strict) {
                // Strict comparison - only convert if both are floats
                if (is_float($value) && is_float($value2)) {
                    if ((string) $value !== (string) $value2) {
                        $result[$key] = $value;
                    }
                } else {
                    if ($value !== $value2) {
                        $result[$key] = $value;
                    }
                }
            } else {
                // Loose comparison - convert if either is float
                if (is_float($value) || is_float($value2)) {
                    if ((string) $value != (string) $value2) {
                        $result[$key] = $value;
                    }
                } else {
                    if ($value != $value2) {
                        $result[$key] = $value;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns an array with a strict comparison between $array1 and $array2
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function strictComparison($array1, $array2)
    {
        return static::compare($array1, $array2, true);
    }

    /**
     * Returns an array with a loose comparison between $array1 and $array2
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function looseComparison($array1, $array2)
    {
        return static::compare($array1, $array2, false);
    }
}
