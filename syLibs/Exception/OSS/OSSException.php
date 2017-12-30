<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/4 0004
 * Time: 10:06
 */
namespace Exception\OSS;

use Exception\BaseException;

class OSSException extends BaseException {
    public function __construct( $message, $code ) {
        parent::__construct( $message, $code );
        $this->tipName = 'OSS上传异常';
    }
}