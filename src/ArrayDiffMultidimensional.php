<?php

namespace Rogervila;

class ArrayDiffMultidimensional
{

    /**
     * Returns an array with the differences between $array1 and $array2.
     * Compares array1 against one or more other arrays and returns the values in array1
     * that are not present in any of the other arrays.
     * @param mixed $array1
     * @param mixed $array2
     * @return array
     * @internal param array $aArray1
     * @internal param array $aArray2
     */
    public static function compare($array1, $array2)
    {
        $result = array();

        if (!is_array($array2)) {
            return $array1;
        }

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
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

            if ($value != $array2[$key]) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
