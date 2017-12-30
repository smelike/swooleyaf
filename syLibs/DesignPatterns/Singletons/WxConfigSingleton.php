<?php
/**
 * 微信配置单例类
 * User: 姜伟
 * Date: 2017/6/17 0017
 * Time: 11:18
 */
namespace DesignPatterns\Singletons;

use Constant\Server;
use Tool\Tool;
use Traits\SingletonTrait;
use Wx\WxConfigOpenAuthorizer;
use Wx\WxConfigOpenCommon;
use Wx\WxConfigShop;

class WxConfigSingleton {
    use SingletonTrait;

    /**
     * 默认商户平台app id
     * @var string
     */
    private $defaultShopAppId = '';
    /**
     * 商户平台配置列表
     * @var array
     */
    private $shopConfigs = [];
    /**
     * 商户平台列表
     * @var array
     */
    private $shopApps = [];
    /**
     * 开放平台公共配置
     * @var WxConfigOpenCommon
     */
    private $openCommonConfig = null;
    /**
     * 开放平台授权者配置数组
     * @var array
     */
    private $openAuthConfigs = null;

    /**
     * @return \DesignPatterns\Singletons\WxConfigSingleton
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){
        $this->init();
    }

    private function __clone(){
    }

    /**
     * 初始化配置
     */
    private function init(){
        $configs = \Yaconf::get('wx.' . SY_ENV);

        //初始化商户平台配置
        $shops = Tool::getArrayVal($configs, 'shops', []);
        foreach ($shops as $eTag => $eShop) {
            $shopConfig = new WxConfigShop();
            $shopConfig->setExpireJsTicket((int)Tool::getArrayVal($eShop, 'expire.jsticket', 0, true));
            $shopConfig->setExpireAccessToken((int)Tool::getArrayVal($eShop,'expire.accesstoken', 0, true));
            $shopConfig->setClientIp((string)Tool::getArrayVal($eShop, 'clientip', '', true));
            $shopConfig->setAppId((string)Tool::getArrayVal($eShop, 'appid', '', true));
            $shopConfig->setSecret((string)Tool::getArrayVal($eShop, 'secret', '', true));
            $shopConfig->setPayMchId((string)Tool::getArrayVal($eShop, 'pay.mchid', '', true));
            $shopConfig->setPayKey((string)Tool::getArrayVal($eShop, 'pay.key', '', true));
            $shopConfig->setPayNotifyUrl((string)Tool::getArrayVal($eShop, 'pay.url.notify', '', true));
            $shopConfig->setPayAuthUrl((string)Tool::getArrayVal($eShop, 'pay.url.auth', '', true));
            $shopConfig->setSslCert((string)Tool::getArrayVal($eShop, 'ssl.cert', '', true));
            $shopConfig->setSslKey((string)Tool::getArrayVal($eShop, 'ssl.key', '', true));
            $shopConfig->setTemplates((array)Tool::getArrayVal($eShop, 'templates', [], true));
            $this->shopConfigs[$shopConfig->getAppId()] = $shopConfig;
            $this->shopApps[$eTag] = $shopConfig->getAppId();
            if ($eTag == Server::WX_APP_SY) {
                $this->defaultShopAppId = $shopConfig->getAppId();
            }
        }

        //初始化开放平台公共配置
        $openCommonConfig = new WxConfigOpenCommon();
        $openCommonConfig->setExpireComponentAccessToken((int)Tool::getArrayVal($configs,'open.expire.component.accesstoken', 0, true));
        $openCommonConfig->setExpireAuthorizerJsTicket((int)Tool::getArrayVal($configs,'open.expire.authorizer.jsticket', 0, true));
        $openCommonConfig->setExpireAuthorizerAccessToken((int)Tool::getArrayVal($configs,'open.expire.authorizer.accesstoken', 0, true));
        $openCommonConfig->setAppId((string)Tool::getArrayVal($configs, 'open.appid', '', true));
        $openCommonConfig->setSecret((string)Tool::getArrayVal($configs, 'open.secret', '', true));
        $openCommonConfig->setToken((string)Tool::getArrayVal($configs, 'open.token', '', true));
        $openCommonConfig->setAesKeyBefore((string)Tool::getArrayVal($configs, 'open.aeskey.before', '', true));
        $openCommonConfig->setAesKeyNow((string)Tool::getArrayVal($configs, 'open.aeskey.now', '', true));
        $openCommonConfig->setAuthUrlDomain((string)Tool::getArrayVal($configs, 'open.authurl.domain', '', true));
        $openCommonConfig->setAuthUrlCallback((string)Tool::getArrayVal($configs, 'open.authurl.callback', '', true));
        $this->openCommonConfig = $openCommonConfig;

        //TODO: 初始化开放平台授权者配置
        $this->openAuthConfigs = [];
    }

    /**
     * 获取默认商户平台app id
     * @return string
     */
    public function getDefaultShopAppId() : string {
        return $this->defaultShopAppId;
    }

    /**
     * 获取商户平台app id
     * @param string $tag 商户标识
     * @return string|null
     */
    public function getShopAppId(string $tag) {
        return Tool::getArrayVal($this->shopApps, $tag, null);
    }

    /**
     * 获取商户平台配置
     * @param string $appId
     * @return \Wx\WxConfigShop|null
     */
    public function getShopConfig(string $appId=''){
        $trueAppId = $appId === '' ? $this->defaultShopAppId : $appId;

        return Tool::getArrayVal($this->shopConfigs, $trueAppId, null);
    }

    /**
     * 设置商户平台配置
     * @param \Wx\WxConfigShop $config
     */
    public function setShopConfig(WxConfigShop $config){
        $this->shopConfigs[$config->getAppId()] = $config;
    }

    /**
     * 移除商户平台配置
     * @param string $appId
     */
    public function removeShopConfig(string $appId) {
        unset($this->shopConfigs[$appId]);
    }

    /**
     * 获取商户模板ID
     * @param string $name 模板名称
     * @param string $appId
     * @return string|null
     */
    public function getShopTemplateId(string $name,string $appId='') {
        $trueAppId = $appId === '' ? $this->defaultShopAppId : $appId;
        $shopConfig = Tool::getArrayVal($this->shopConfigs, $trueAppId, null);
        if (is_null($shopConfig)) {
            return null;
        }

        return Tool::getArrayVal($shopConfig->getTemplates(), $name, null);
    }

    /**
     * 获取开放平台公共配置
     * @return \Wx\WxConfigOpenCommon
     */
    public function getOpenCommonConfig(){
        return $this->openCommonConfig;
    }

    /**
     * 设置开放平台公共配置
     * @param \Wx\WxConfigOpenCommon $config
     */
    public function setOpenCommonConfig(WxConfigOpenCommon $config) {
        $this->openCommonConfig = $config;
    }

    /**
     * 获取开放平台授权者配置
     * @param string $appId 授权者微信号
     * @return \Wx\WxConfigOpenAuthorizer|null
     */
    public function getOpenAuthorizerConfigs(string $appId=''){
        $trueAppId = $appId === '' ? $this->defaultShopAppId : $appId;

        return Tool::getArrayVal($this->openAuthConfigs, $trueAppId, null);
    }

    /**
     * 设置开放平台授权者配置
     * @param \Wx\WxConfigOpenAuthorizer $config 授权者配置
     */
    public function setOpenAuthorizerConfigs(WxConfigOpenAuthorizer $config){
        $this->openAuthConfigs[$config->getAppId()] = $config;
    }

    /**
     * 移除开放平台授权者配置
     * @param string $appId 授权者微信号
     */
    public function removeOpenAuthorizerConfigs(string $appId){
        unset($this->openAuthConfigs[$appId]);
    }
}