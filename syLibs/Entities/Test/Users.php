<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-8
 * Time: 上午12:40
 */
namespace Entities\Test;

use DB\Entities\MysqlEntity;

class Users extends MysqlEntity {
    public function __construct() {
        parent::__construct('syuser', 'users');
    }

    /**
     * @var string
     */
    public $uid;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var int
     */
    public $sex = 0;

    /**
     * @var string
     */
    public $phone = '';

    /**
     * @var string
     */
    public $pwd = '';

    /**
     * @var string
     */
    public $pwd_salt = '';

    /**
     * @var int
     */
    public $created = 0;

    /**
     * @var int
     */
    public $updated = 0;
}