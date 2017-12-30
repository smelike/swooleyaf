<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/8/13 0013
 * Time: 0:12
 */
namespace Entities\Test;

use DB\Entities\MongoEntity;

class UserLoginHistory extends MongoEntity {
    public function __construct() {
        parent::__construct('xshwplog', 'UserLoginHistory');
    }

    /**
     * 用户ID
     * @var string
     */
    public $uid;

    /**
     * 登录类型
     * @var int
     */
    public $type = 1;

    /**
     * 登录IP
     * @var string
     */
    public $ip = '';

    /**
     * 会话id
     * @var string
     */
    public $session_id = '';
    /**
     * 用户名
     * @var string
     */
    public $user_name = '';
    /**
     * 创建时间戳
     * @var int
     */
    public $created;
    /**
     * 时间日期
     * @var string
     */
    public $datetime = '';
}