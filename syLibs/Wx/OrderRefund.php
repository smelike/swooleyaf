<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-03
 * Time: 23:44
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;

class OrderRefund {
    public function __construct(string $appId) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->sign_type = 'MD5';
        $this->nonce_str = WxUtil::createNonceStr();
        $this->refund_fee_type = 'CNY';
        $this->op_user_id = $shopConfig->getPayMchId();
    }

    /**
     * 公众号ID
     * @var string
     */
    private $appid = '';

    /**
     * 商户号
     * @var string
     */
    private $mch_id = '';

    /**
     * 设备号
     * @var string
     */
    private $device_info = '';

    /**
     * 随机字符串
     * @var string
     */
    private $nonce_str = '';

    /**
     * 签名
     * @var string
     */
    private $sign = '';

    /**
     * 签名类型
     * @var string
     */
    private $sign_type = '';

    /**
     * 微信订单号
     * @var string
     */
    private $transaction_id = '';

    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 商户退款单号
     * @var string
     */
    private $out_refund_no = '';

    /**
     * 订单总金额，单位为分
     * @var int
     */
    private $total_fee = 0;

    /**
     * 退款总金额，单位为分
     * @var int
     */
    private $refund_fee = 0;

    /**
     * 货币种类
     * @var string
     */
    private $refund_fee_type = '';

    /**
     * 操作员
     * @var string
     */
    private $op_user_id = '';

    /**
     * @param string $transactionId
     * @throws \Exception\Wx\WxException
     */
    public function setTransactionId(string $transactionId) {
        if (preg_match('/^4[0-9]{27}$/', $transactionId . '') > 0) {
            $this->transaction_id = $transactionId . '';
        } else {
            throw new WxException('微信订单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[a-zA-Z0-9]{1,32}$/', $outTradeNo . '') > 0) {
            $this->out_trade_no = $outTradeNo . '';
        } else {
            throw new WxException('商户订单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outRefundNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutRefundNo(string $outRefundNo) {
        if (preg_match('/^[a-zA-Z0-9]{1,32}$/', $outRefundNo . '') > 0) {
            $this->out_refund_no = $outRefundNo . '';
        } else {
            throw new WxException('商户退款单号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param int $totalFee
     * @throws \Exception\Wx\WxException
     */
    public function setTotalFee(int $totalFee) {
        if ($totalFee > 0) {
            $this->total_fee = $totalFee;
        } else {
            throw new WxException('订单金额不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param int $refundFee
     * @throws \Exception\Wx\WxException
     */
    public function setRefundFee(int $refundFee) {
        if ($refundFee > 0) {
            $this->refund_fee = $refundFee;
        } else {
            throw new WxException('退款金额不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        $resArr = [];
        $saveArr = get_object_vars($this);
        foreach ($saveArr as $key => $value) {
            if (strlen($value . '') > 0) {
                $resArr[$key] = $value;
            }
        }

        if (isset($resArr['transaction_id'])) {
            unset($resArr['out_trade_no']);
        } else if (isset($resArr['out_trade_no'])) {
            unset($resArr['transaction_id']);
        } else {
            throw new WxException('微信订单号和商户订单号必须设置一个', ErrorCode::WX_PARAM_ERROR);
        }

        if (!isset($resArr['out_refund_no'])) {
            throw new WxException('商户退款单号不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        if ($resArr['total_fee'] == 0) {
            throw new WxException('订单金额必须大于0', ErrorCode::WX_PARAM_ERROR);
        } else if ($resArr['refund_fee'] == 0) {
            throw new WxException('退款金额必须大于0', ErrorCode::WX_PARAM_ERROR);
        } else if ($resArr['refund_fee'] > $resArr['total_fee']) {
            throw new WxException('订单金额必须大于等于退款金额', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}