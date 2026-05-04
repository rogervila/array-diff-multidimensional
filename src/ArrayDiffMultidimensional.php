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

        if ($array1 === []) {
            return [];
        }

        if ($array2 === []) {
            return $array1;
        }

        if ($array1 === $array2) {
            return [];
        }
        $epsilon = null;
        $result = [];

        foreach ($array1 as $key => $value) {
            // Use isset for better performance, fall back to array_key_exists for null values
            if (!isset($array2[$key]) && !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            $value2 = $array2[$key];

            if (is_array($value)) {
                if ($value === []) {
                    if (!is_array($value2) || $value2 !== []) {
                        $result[$key] = $value;
                    }
                    continue;
                }

                // Only recurse if both are arrays
                if (is_array($value2)) {
                    $recursiveArrayDiff = $strict
                        ? self::compareStrictArray($value, $value2, $epsilon)
                        : self::compareLooseArray($value, $value2);

                    if ($recursiveArrayDiff !== []) {
                        $result[$key] = $recursiveArrayDiff;
                    }
                } else {
                    $result[$key] = $value;
                }
                continue;
            }

            if ($strict) {
                if ($value === $value2) {
                    continue;
                }

                if (is_float($value) && is_float($value2)) {
                    if ($epsilon === null) {
                        $epsilon = self::resolveFloatEpsilon();
                    }

                    if (abs($value - $value2) > $epsilon) {
                        $result[$key] = $value;
                    }
                    continue;
                }

                $result[$key] = $value;
                continue;
            }

            if ($value == $value2) {
                continue;
            }

            // Preserve float-string tolerance from previous loose comparison logic
            if ((is_float($value) || is_float($value2)) && (string) $value == (string) $value2) {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @param float|null $epsilon
     *
     * @return array
     */
    private static function compareStrictArray($array1, $array2, $epsilon = null)
    {
        if ($array1 === []) {
            return [];
        }

        if ($array2 === []) {
            return $array1;
        }

        if ($array1 === $array2) {
            return [];
        }

        $result = [];

        foreach ($array1 as $key => $value) {
            if (!isset($array2[$key]) && !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            $value2 = $array2[$key];

            if (is_array($value)) {
                if ($value === []) {
                    if (!is_array($value2) || $value2 !== []) {
                        $result[$key] = $value;
                    }
                    continue;
                }

                if (is_array($value2)) {
                    $recursiveArrayDiff = self::compareStrictArray($value, $value2, $epsilon);
                    if ($recursiveArrayDiff !== []) {
                        $result[$key] = $recursiveArrayDiff;
                    }
                } else {
                    $result[$key] = $value;
                }
                continue;
            }

            if ($value === $value2) {
                continue;
            }

            if (is_float($value) && is_float($value2)) {
                if ($epsilon === null) {
                    $epsilon = self::resolveFloatEpsilon();
                }

                if (abs($value - $value2) > $epsilon) {
                    $result[$key] = $value;
                }
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    private static function compareLooseArray($array1, $array2)
    {
        if ($array1 === []) {
            return [];
        }

        if ($array2 === []) {
            return $array1;
        }

        if ($array1 === $array2) {
            return [];
        }

        $result = [];

        foreach ($array1 as $key => $value) {
            if (!isset($array2[$key]) && !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            $value2 = $array2[$key];

            if (is_array($value)) {
                if ($value === []) {
                    if (!is_array($value2) || $value2 !== []) {
                        $result[$key] = $value;
                    }
                    continue;
                }

                if (is_array($value2)) {
                    $recursiveArrayDiff = self::compareLooseArray($value, $value2);
                    if ($recursiveArrayDiff !== []) {
                        $result[$key] = $recursiveArrayDiff;
                    }
                } else {
                    $result[$key] = $value;
                }
                continue;
            }

            if ($value == $value2) {
                continue;
            }

            if ((is_float($value) || is_float($value2)) && (string) $value == (string) $value2) {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @return float
     */
    private static function resolveFloatEpsilon()
    {
        static $epsilon = null;

        if ($epsilon === null) {
            $epsilon = defined('PHP_FLOAT_EPSILON') ? PHP_FLOAT_EPSILON : 2.2204460492503E-16;
        }

        return $epsilon;
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
