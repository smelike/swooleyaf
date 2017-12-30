<?php
/**
 * 二维码支付
 * User: jw
 * Date: 17-4-11
 * Time: 下午10:18
 */
namespace AliPay;

use Constant\ErrorCode;
use DesignPatterns\Singletons\AliConfigSingleton;
use Exception\Ali\AliPayException;

class PayQrCode extends BaseTrade {
    public function __construct() {
        parent::__construct();

        $baseConfig = AliConfigSingleton::getInstance()->getPayBaseConfig();
        $this->setMethod('alipay.trade.precreate');
        $this->notify_url = $baseConfig->getUrlNotify();
        $this->setBizContent('seller_id', $baseConfig->getSellerId());
    }

    /**
     * 支付宝服务器主动通知商户服务器里指定的页面http/https路径
     * @var string
     */
    private $notify_url = '';

    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 订单总金额，单位为元
     * @var string
     */
    private $total_amount = '';

    /**
     * 订单标题
     * @var string
     */
    private $subject = '';

    /**
     * 商品的描述
     * @var string
     */
    private $body = '';

    /**
     * 订单允许的最晚付款时间，逾期将关闭交易
     * @var string
     */
    private $timeout_express = '';

    /**
     * @param string $subject
     * @throws \Exception\Ali\AliPayException
     */
    public function setSubject(string $subject) {
        if (strlen($subject . '') > 0) {
            $this->setBizContent('subject', mb_substr($subject . '', 0, 80));
        } else {
            throw new AliPayException('商品标题不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

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
     * @param string $timeoutExpress
     */
    public function setTimeoutExpress(string $timeoutExpress) {
        if (strlen($timeoutExpress . '') > 0) {
            $this->setBizContent('timeout_express', $timeoutExpress . '');
        }
    }

    /**
     * @param int $totalAmount
     * @throws \Exception\Ali\AliPayException
     */
    public function setTotalAmount(int $totalAmount) {
        if ($totalAmount > 0) {
            $this->setBizContent('total_amount', number_format(($totalAmount / 100), 2, '.', ''));
        } else {
            throw new AliPayException('订单总金额必须大于0', ErrorCode::ALIPAY_PARAM_ERROR);
        }
    }

    /**
     * @param string $attach
     * @throws \Exception\Ali\AliPayException
     */
    public function setAttach(string $attach) {
        $length = strlen($attach . '');
        if ($length > 128) {
            throw new AliPayException('附加数据不合法', ErrorCode::ALIPAY_PARAM_ERROR);
        } else if ($length > 0) {
            $this->setBizContent('body', $attach . '');
        }
    }

    public function getDetail() : array {
        $bizContent = $this->getBizContent();
        if (!isset($bizContent['subject'])) {
            throw new AliPayException('商品标题不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($bizContent['out_trade_no'])) {
            throw new AliPayException('商户订单号不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }
        if (!isset($bizContent['total_amount'])) {
            throw new AliPayException('订单总金额不能为空', ErrorCode::ALIPAY_PARAM_ERROR);
        }

        $resArr = $this->getContentArr();
        $resArr['notify_url'] = $this->notify_url;
        $sign = AliPayUtil::createSign($resArr, $resArr['sign_type']);
        $resArr['sign'] = $sign;

        return $resArr;
    }
}