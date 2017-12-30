<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-1
 * Time: 上午7:39
 */
namespace Wx;

use DesignPatterns\Singletons\WxConfigSingleton;

class JsConfig {
    public function __construct(string $appId) {
        $this->appId = $appId;
        $this->timestamp = time();
        $this->nonceStr = WxUtil::createNonceStr();
    }

    /**
     * @var string
     */
    private $appId = '';

    /**
     * @var int
     */
    private $timestamp = 0;

    /**
     * @var string
     */
    private $nonceStr = '';

    /**
     * @param string $platType 平台类型 shop：公众号 open：第三方平台
     * @param string $appId 授权者微信号
     * @return array
     */
    public function getDetail(string $platType='shop',string $appId='') : array {
        $resArr = [
            'appId' => $this->appId,
            'timestamp' => $this->timestamp,
            'nonceStr' => $this->nonceStr,
        ];

        if ($platType == 'shop') { //公众号获取jsapi_ticket
            $ticket = WxUtil::getJsTicket($this->appId);
        } else { //第三方平台获取jsapi_ticket
            $ticket = WxOpenUtil::getJsTicket($appId);
        }

        $needStr = 'jsapi_ticket=' . $ticket . '&noncestr=' . $this->nonceStr . '&timestamp=' . $this->timestamp . '&url=' . WxConfigSingleton::getInstance()->getShopConfig($this->appId)->getPayAuthUrl();
        $resArr['signature'] = sha1($needStr);

        return $resArr;
    }
}