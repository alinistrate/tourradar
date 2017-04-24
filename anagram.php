<?php

/**
 * Verify is the given strings form anagrams of each other
 *
 * @param $string1
 * @param $string2
 * @return bool
 */
function isAnagram($string1, $string2)
{
    if (invalidInputs($string1, $string2)) {
        return false;
    }

    return (count_chars(sanatizeString($string1), 1) == count_chars(sanatizeString($string2), 1));
}

/**
 * Verify is the strings are valid
 *
 * @param $string1
 * @param $string2
 * @return bool
 */
function invalidInputs($string1, $string2)
{
    return (!is_string($string1) || !is_string($string2));
}

/**
 * Sanatize the given string
 *
 * @param $string
 * @return mixed
 */
function sanatizeString($string)
{
    return str_replace(" ", "", strtolower($string));
}

var_dump(isAnagram('admirer', 'married'));
var_dump(isAnagram('AstroNomers', 'no more stars'));
var_dump(isAnagram(1, 'no more stars'));
var_dump(isAnagram('random url', 'random urt'));
