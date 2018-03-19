<?php

namespace Rogervila;

class ArrayDiffMultidimensional
{

    /**
     * Returns an array with items that are present in first array,
     * but not in the second one. If second argument is not
     * array first array is returned.
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
