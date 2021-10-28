<?php

use Rogervila\ArrayDiffMultidimensional;

if (!function_exists('array_diff_multidimensional')) {

    /**
     * Returns an array with the differences between $array1 and $array2
     * $strict variable defines if comparison must be strict or not
     *
     * @param array $array1
     * @param array $array2
     * @param bool $strict
     *
     * @return array
     */
    function array_diff_multidimensional($array1, $array2, $strict = true)
    {
        return ArrayDiffMultidimensional::compare($array1, $array2, $strict);
    }
}
