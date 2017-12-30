<?php
/**
 * 企业付款查询
 * User: jw
 * Date: 17-4-14
 * Time: 下午11:30
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;

class PayCompanyQuery {
    public function __construct(string $appId) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->nonce_str = WxUtil::createNonceStr();
    }

    /**
     * 公众账号ID
     * @var string
     */
    private $appid = '';

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
     * 商户订单号
     * @var string
     */
    private $partner_trade_no = '';

    /**
     * 商户号
     * @var string
     */
    private $mch_id = '';

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[0-9]{1,32}$/', $outTradeNo . '') > 0) {
            $this->partner_trade_no = $outTradeNo . '';
        } else {
            throw new WxException('商户单号不合法', ErrorCode::WX_PARAM_ERROR);
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

        if (!isset($resArr['partner_trade_no'])) {
            throw new WxException('商户单号不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}