<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-3-26
 * Time: 1:09
 */
namespace Validator\Impl\String;

use Constant\Server;
use Validator\Base\BaseValidator;
use Validator\ValidatorService;

class StringBaseImage extends BaseValidator implements ValidatorService {
    public function __construct() {
        parent::__construct();
        $this->validatorType = Server::VALIDATOR_STRING_TYPE_BASE_IMAGE;
    }

    private function __clone() {
    }

    public function validator($data, $compareData) : string {
        if ($data === null) {
            return '';
        }

        $trueData = $this->verifyStringData($data);
        if ($trueData === null) {
            return '必须是字符串';
        } else if(preg_match('/^(data\:image\/([A-Za-z]{3,4})\;base64\,)/', $trueData) > 0){
            return '';
        } else {
            return '必须是base64编码图片';
        }
    }
}