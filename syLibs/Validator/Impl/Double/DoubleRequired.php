<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/8/11 0011
 * Time: 9:18
 */
namespace Validator\Impl\Double;

use Constant\Server;
use Validator\Base\BaseValidator;
use Validator\ValidatorService;

class DoubleRequired extends BaseValidator implements ValidatorService {
    public function __construct() {
        parent::__construct();
        $this->validatorType = Server::VALIDATOR_DOUBLE_TYPE_REQUIRED;
    }

    private function __clone() {
    }

    public function validator($data, $compareData) : string {
        if($data === null){
            return '必须填写';
        }

        return '';
    }
}