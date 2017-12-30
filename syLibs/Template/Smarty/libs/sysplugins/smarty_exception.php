<?php

/**
 * Smarty exception class
 *
 * @package Smarty
 */
class Smarty_Exception extends Exception
{
    public static $escape = false;

    public function __toString()
    {
        return ' --> Smarty: ' . (self::$escape ? htmlentities($this->message) : $this->message) . ' <-- ';
    }
}
