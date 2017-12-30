<?php
/**
 * 扫码支付模式一预支付类
 * User: jw
 * Date: 17-4-2
 * Time: 上午10:13
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;

class PayNativePre {
    public function __construct(string $appId) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->time_stamp = time();
        $this->nonce_str = Tool::createNonceStr(32);
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
     * 当前时间戳
     * @var int
     */
    private $time_stamp = 0;

    /**
     * 随机字符串，不长于32位
     * @var string
     */
    private $nonce_str = '';

    /**
     * 商户定义的商品id
     * @var string
     */
    private $product_id = '';

    /**
     * 签名
     * @var string
     */
    private $sign = '';

    /**
     * @param string $productId
     * @throws \Exception\Wx\WxException
     */
    public function setProductId(string $productId) {
        if (preg_match('/^[a-zA-Z0-9]{1,32}$/', $productId . '') > 0) {
            $this->product_id = $productId . '';
        } else {
            throw  new WxException('商品ID不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * 获取预支付订单详情
     * @return array
     * @throws \Exception\Wx\WxException
     */
    public function getDetail() : array {
        $resArr = [];
        $saveArr = get_object_vars($this);
        foreach ($saveArr as $key => $value) {
            if (strlen($value . '') > 0) {
                $resArr[$key] = $value;
            }
        }

        if (!isset($resArr['product_id'])) {
            throw  new WxException('商品ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}