<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/8/11 0011
 * Time: 9:23
 */
namespace Validator\Impl\Double;

use Constant\Server;
use Validator\Base\BaseValidator;
use Validator\ValidatorService;

class DoubleMax  extends BaseValidator implements ValidatorService {
    public function __construct() {
        parent::__construct();
        $this->validatorType = Server::VALIDATOR_DOUBLE_TYPE_MAX;
    }

    private function __clone() {
    }

    public function validator($data, $compareData) : string {
        if ($data === null) {
            return '';
        }

        $trueData = $this->verifyDoubleData($data);
        if ($trueData === null) {
            return '必须是数值';
        } else if(is_numeric($compareData)){
            $maxNum = (double)$compareData;
            if($trueData > $maxNum) {
                return '不能大于' . $maxNum;
            }

            return '';
        } else {
            return '规则不合法';
        }
    }
}