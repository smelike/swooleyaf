<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-03
 * Time: 23:08
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;

class RefundQuery {
    public function __construct(string $appId) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->sign_type = 'MD5';
        $this->nonce_str = WxUtil::createNonceStr();
    }

    /**
     * 公众账号ID
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
     * 微信退款单号
     * @var string
     */
    private $refund_id = '';

    /**
     * 商户退款单号
     * @var string
     */
    private $out_refund_no = '';

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
     * @param string $refundId
     * @throws \Exception\Wx\WxException
     */
    public function setRefundId(string $refundId) {
        if (preg_match('/^[0-9]{28}$/', $refundId . '') > 0) {
            $this->refund_id = $refundId . '';
        } else {
            throw new WxException('微信退款单号不合法', ErrorCode::WX_PARAM_ERROR);
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

    public function getDetail() : array {
        $resArr = [];
        $saveArr = get_object_vars($this);
        foreach ($saveArr as $key => $value) {
            if (strlen($value . '') > 0) {
                $resArr[$key] = $value;
            }
        }

        if (isset($resArr['transaction_id'])) {
            unset($resArr['out_trade_no'], $resArr['refund_id'], $resArr['out_refund_no']);
        } else if (isset($resArr['out_trade_no'])) {
            unset($resArr['transaction_id'], $resArr['refund_id'], $resArr['out_refund_no']);
        } else if (isset($resArr['refund_id'])) {
            unset($resArr['transaction_id'], $resArr['out_trade_no'], $resArr['out_refund_no']);
        } else if (isset($resArr['out_refund_no'])) {
            unset($resArr['transaction_id'], $resArr['out_trade_no'], $resArr['refund_id']);
        } else {
            throw new WxException('微信订单号,商户订单号,微信退款单号,商户退款单号必须设置其中一个', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}