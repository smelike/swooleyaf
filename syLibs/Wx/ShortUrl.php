<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-2
 * Time: 上午11:00
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;

class ShortUrl {
    public function __construct(string $appId) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->nonce_str = Tool::createNonceStr(32);
        $this->sign_type = 'MD5';
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
     * URL链接
     * @var string
     */
    private $long_url = '';

    /**
     * 随机字符串
     * @var string
     */
    private $nonce_str = '';

    /**
     * 签名类型
     * @var string
     */
    private $sign_type = '';

    /**
     * 签名
     * @var string
     */
    private $sign = '';

    /**
     * @param string $longUrl
     * @throws \Exception\Wx\WxException
     */
    public function setLongUrl(string $longUrl) {
        if (preg_match('/^weixin/', $longUrl) > 0) {
            $this->long_url = $longUrl;
        } else {
            throw new WxException('长链接不合法', ErrorCode::WX_PARAM_ERROR);
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

        if (!isset($resArr['long_url'])) {
            throw  new WxException('长链接不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}