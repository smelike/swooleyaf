<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/12/11 0011
 * Time: 9:20
 */
namespace SyTask;

use Constant\Server;
use Tool\BaseContainer;

class SyModuleTaskContainer extends BaseContainer {
    public function __construct(){
        $this->registryMap = [
            Server::MODULE_NAME_API,
            Server::MODULE_NAME_ORDER,
            Server::MODULE_NAME_SERVICE,
            Server::MODULE_NAME_USER,
        ];

        $this->bind(Server::MODULE_NAME_API, function() {
            return new SyModuleApiTask();
        });
        $this->bind(Server::MODULE_NAME_ORDER, function() {
            return new SyModuleOrderTask();
        });
        $this->bind(Server::MODULE_NAME_SERVICE, function() {
            return new SyModuleServiceTask();
        });
        $this->bind(Server::MODULE_NAME_USER, function() {
            return new SyModuleUserTask();
        });
    }
}