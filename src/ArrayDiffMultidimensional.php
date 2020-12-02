<?php

namespace Rogervila;

class ArrayDiffMultidimensional
{

    /**
     * Returns an array with the differences between $array1 and $array2
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function compare($array1, $array2)
    {
        $result = array();

        foreach ($array1 as $key => $value) {
            if (!is_array($array2) || !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $recursiveArrayDiff = static::compare($value, $array2[$key]);

                if (count($recursiveArrayDiff)) {
                    $result[$key] = $recursiveArrayDiff;
                }

                continue;
            }

            $value1 = $value;
            $value2 = $array2[$key];
            if (is_float($value1) || is_float($value2)) {
                $value1 = (string)$value1;
                $value2 = (string)$value2;
            }

            if ($value1 != $value2) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
