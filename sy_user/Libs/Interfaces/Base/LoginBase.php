<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-19
 * Time: 下午3:15
 */
namespace Interfaces\Base;

abstract class LoginBase {
    public $loginType = '';
    public $className = '';

    public function __construct() {
    }
}