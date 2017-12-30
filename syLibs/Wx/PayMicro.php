<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-04
 * Time: 1:47
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;

class PayMicro {
    public function __construct(string $appId) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->sign_type = 'MD5';
        $this->nonce_str = WxUtil::createNonceStr();
        $this->fee_type = 'CNY';
        $this->spbill_create_ip = $shopConfig->getClientIp();
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
     * 商品描述
     * @var string
     */
    private $body = '';

    /**
     * 商品详情
     * @var string
     */
    private $detail = '';

    /**
     * 附加数据
     * @var string
     */
    private $attach = '';

    /**
     * 商户订单号
     * @var string
     */
    private $out_trade_no = '';

    /**
     * 订单金额
     * @var int
     */
    private $total_fee = 0;

    /**
     * 货币类型
     * @var string
     */
    private $fee_type = '';

    /**
     * 终端IP
     * @var string
     */
    private $spbill_create_ip = '';

    /**
     * 商品标记
     * @var string
     */
    private $goods_tag = '';

    /**
     * 授权码
     * @var string
     */
    private $auth_code = '';

    /**
     * @param string $body
     * @throws \Exception\Wx\WxException
     */
    public function setBody(string $body) {
        if (strlen($body . '') > 0) {
            $this->body = mb_substr($body . '', 0, 40);
        } else {
            throw new WxException('商品名称不能为空', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $attach
     * @throws \Exception\Wx\WxException
     */
    public function setAttach(string $attach) {
        if (strlen($attach . '') <= 127) {
            $this->attach = $attach . '';
        } else {
            throw new WxException('附加数据不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[0-9]{1,32}$/', $outTradeNo . '') > 0) {
            $this->out_trade_no = $outTradeNo . '';
        } else {
            throw new WxException('商户单号不合法', ErrorCode::WX_PARAM_ERROR);
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
            throw new WxException('支付金额不能小于0', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $authCode
     * @throws \Exception\Wx\WxException
     */
    public function setAuthCode(string $authCode) {
        if (preg_match('/^1[0-5][0-9]{16}$/', $authCode . '') > 0) {
            $this->auth_code = $authCode . '';
        } else {
            throw new WxException('授权码不合法', ErrorCode::WX_PARAM_ERROR);
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

        if (!isset($resArr['body'])) {
            throw new WxException('商品名称不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if (!isset($resArr['out_trade_no'])) {
            throw new WxException('商户单号不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if ($resArr['total_fee'] == 0) {
            throw new WxException('支付金额必须大于0', ErrorCode::WX_PARAM_ERROR);
        }
        if (!isset($resArr['auth_code'])) {
            throw new WxException('授权码不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}