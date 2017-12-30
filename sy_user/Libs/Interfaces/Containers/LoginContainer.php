<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-19
 * Time: 下午3:46
 */
namespace Interfaces\Containers;

use Constant\Project;
use Interfaces\Impl\Login\Account;
use Interfaces\Impl\Login\Email;
use Interfaces\Impl\Login\Phone;
use Interfaces\Impl\Login\QQ;
use Interfaces\Impl\Login\WxAuthBase;
use Interfaces\Impl\Login\WxAuthUser;
use Interfaces\Impl\Login\WxScan;
use Tool\BaseContainer;

class LoginContainer extends BaseContainer {
    public function __construct() {
        $this->registryMap = [
            Project::LOGIN_TYPE_WX_AUTH_BASE,
            Project::LOGIN_TYPE_WX_AUTH_USER,
            Project::LOGIN_TYPE_WX_SCAN,
            Project::LOGIN_TYPE_QQ,
            Project::LOGIN_TYPE_EMAIL,
            Project::LOGIN_TYPE_PHONE,
            Project::LOGIN_TYPE_ACCOUNT,
        ];

        $this->bind(Project::LOGIN_TYPE_WX_AUTH_BASE, function () {
            return new WxAuthBase();
        });

        $this->bind(Project::LOGIN_TYPE_WX_AUTH_USER, function () {
            return new WxAuthUser();
        });

        $this->bind(Project::LOGIN_TYPE_WX_SCAN, function () {
            return new WxScan();
        });

        $this->bind(Project::LOGIN_TYPE_QQ, function () {
            return new QQ();
        });

        $this->bind(Project::LOGIN_TYPE_EMAIL, function () {
            return new Email();
        });

        $this->bind(Project::LOGIN_TYPE_PHONE, function () {
            return new Phone();
        });

        $this->bind(Project::LOGIN_TYPE_ACCOUNT, function () {
            return new Account();
        });
    }
}