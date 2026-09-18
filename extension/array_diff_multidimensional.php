<?php

function array_diff_multidimensional(array $array1, mixed $array2, bool $strict = true): array
{
    if (!is_array($array2)) {
        return $array1;
    }

    if (count($array1) === 0 || $array1 === $array2) {
        return [];
    }

    $result = [];
    $epsilon = defined('PHP_FLOAT_EPSILON') ? PHP_FLOAT_EPSILON : 2.2204460492503E-16;

    foreach ($array1 as $key => $value) {
        // Use isset for better performance, fall back to array_key_exists for null values
        if (!isset($array2[$key]) && !array_key_exists($key, $array2)) {
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
                $recursiveArrayDiff = array_diff_multidimensional($value, $value2, $strict);
                if (!empty($recursiveArrayDiff)) {
                    $result[$key] = $recursiveArrayDiff;
                }
            } else {
                $result[$key] = $value;
            }
            continue;
        }

        // Handle scalar value comparison optimization
        if ($strict) {
            // Strict comparison - optimize float handling
            if (is_float($value) && is_float($value2)) {
                // Use epsilon comparison for float precision
                if (abs($value - $value2) > $epsilon) {
                    $result[$key] = $value;
                }
            } elseif ($value !== $value2) {
                $result[$key] = $value;
            }
        } else {
            // Loose comparison - convert if either is float
            if (is_float($value) || is_float($value2)) {
                if ((string) $value != (string) $value2) {
                    $result[$key] = $value;
                }
            } elseif ($value != $value2) {
                $result[$key] = $value;
            }
        }
    }

    return $result;
}
