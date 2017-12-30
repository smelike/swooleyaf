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

class DoubleMin  extends BaseValidator implements ValidatorService {
    public function __construct() {
        parent::__construct();
        $this->validatorType = Server::VALIDATOR_DOUBLE_TYPE_MIN;
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
            $minNum = (double)$compareData;
            if($trueData < $minNum) {
                return '不能小于' . $minNum;
            }

            return '';
        } else {
            return '规则不合法';
        }
    }
}