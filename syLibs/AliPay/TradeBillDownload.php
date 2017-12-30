<?php
/**
 * 订单对账单下载
 * User: jw
 * Date: 17-4-13
 * Time: 下午8:10
 */
namespace AliPay;

use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class TradeBillDownload extends BaseTrade {
    private static $billTypes = [
        'trade',
        'signcustomer',
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * 账单类型
     * @var string
     */
    private $bill_type = '';

    /**
     * 账单时间：日账单格式为yyyy-MM-dd，月账单格式为yyyy-MM
     * @var string
     */
    private $bill_date = '';

    /**
     * @param string $billType
     * @throws \Exception\Ali\AliPayException
     */
    public function setBillType(string $billType) {
        if (in_array($billType, self::$billTypes)) {
            $this->setBizContent('bill_type', $billType);
        } else {
            throw new AliPayException('账单类型不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $billDate
     * @throws \Exception\Ali\AliPayException
     */
    public function setBillDate(string $billDate) {
        if (preg_match('/^\d{4}(\-\d{2}){1,2}$/', $billDate) > 0) {
            $this->setBizContent('bill_date', $billDate);
        } else {
            throw new AliPayException('账单时间不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        $bizContent = $this->getBizContent();
        if (!isset($bizContent['bill_type'])) {
            throw new AliPayException('账单类型不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($bizContent['bill_date'])) {
            throw new AliPayException('账单时间不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        $resArr = $this->getContentArr();
        $sign = AliPayUtil::createSign($resArr, $resArr['sign_type']);
        $resArr['sign'] = $sign;

        return $resArr;
    }
}