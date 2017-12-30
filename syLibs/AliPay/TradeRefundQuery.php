<?php
/**
 * 订单退款查询
 * User: jw
 * Date: 17-4-12
 * Time: 下午11:13
 */
namespace AliPay;

use Constant\ErrorCode;
use Exception\Ali\AliPayException;

class TradeRefundQuery extends BaseTrade {
    public function __construct() {
        parent::__construct();
        $this->setMethod('alipay.trade.fastpay.refund.query');
    }

    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 支付宝交易号
     * @var string
     */
    private $trade_no = '';

    /**
     * 退款单号
     * @var string
     */
    private $out_request_no = '';

    /**
     * @param string $outTradeNo
     * @throws \Exception\Ali\AliPayException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[0-9]{16,64}$/', $outTradeNo . '') > 0) {
            $this->setBizContent('out_trade_no', $outTradeNo . '');
        } else {
            throw new AliPayException('商户订单号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $tradeNo
     * @throws \Exception\Ali\AliPayException
     */
    public function setTradeNo(string $tradeNo) {
        if (preg_match('/^[0-9]{16,64}$/', $tradeNo . '') > 0) {
            $this->setBizContent('trade_no', $tradeNo . '');
        } else {
            throw new AliPayException('支付宝交易号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $refundNo
     */
    public function setRefundNo(string $refundNo) {
        if (preg_match('/^[0-9]{16,64}$/', $refundNo . '') > 0) {
            $this->setBizContent('out_request_no', $refundNo . '');
        } else {
            throw new AliPayException('退款单号不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        $bizContent = $this->getBizContent();
        if ((!isset($bizContent['out_trade_no'])) && (!isset($bizContent['trade_no']))) {
            throw new AliPayException('商户订单号和支付宝交易号不能都为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($bizContent['out_request_no'])) {
            throw new AliPayException('退款单号不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        $resArr = $this->getContentArr();
        $sign = AliPayUtil::createSign($resArr, $resArr['sign_type']);
        $resArr['sign'] = $sign;

        return $resArr;
    }
}