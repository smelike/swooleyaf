<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/9/18 0018
 * Time: 14:41
 */
namespace Interfaces\Impl\Pay;

use Constant\Project;
use Interfaces\Base\PayBase;
use Interfaces\PayService;

class Goods extends PayBase implements PayService {
    public function __construct() {
        $this->payType = Project::ORDER_PAY_TYPE_GOODS;
    }

    private function __clone() {
    }

    public function handlePaySuccess(array $data) : array {
        // TODO: Implement handlePaySuccess() method.
    }

    public function handlePaySuccessAttach(array $data) {
        // TODO: Implement handlePaySuccessAttach() method.
    }
}