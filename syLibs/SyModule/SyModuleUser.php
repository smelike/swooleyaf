<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-22
 * Time: 下午10:45
 */
namespace SyModule;

use Constant\Server;

class SyModuleUser extends ModuleRpc {
    /**
     * @var \SyModule\SyModuleUser
     */
    private static $instance = null;

    private function __construct() {
        parent::init();
        $this->moduleName = Server::MODULE_NAME_USER;
    }

    /**
     * @return \SyModule\SyModuleUser
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}