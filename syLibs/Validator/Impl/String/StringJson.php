<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/5/17 0017
 * Time: 11:47
 */
namespace Validator\Impl\String;

use Constant\Server;
use Tool\Tool;
use Validator\Base\BaseValidator;
use Validator\ValidatorService;

class StringJson extends BaseValidator implements ValidatorService {
    public function __construct() {
        parent::__construct();
        $this->validatorType = Server::VALIDATOR_STRING_TYPE_JSON;
    }

    private function __clone() {
    }

    public function validator($data, $compareData): string {
        if ($data === null) {
            return '';
        }

        $trueData = $this->verifyStringData($data);
        if ($trueData === null) {
            return '必须是字符串';
        } else {
            $arr = Tool::jsonDecode($trueData);
            if(is_array($arr)){
                return '';
            }

            return '必须是json格式';
        }
    }
}