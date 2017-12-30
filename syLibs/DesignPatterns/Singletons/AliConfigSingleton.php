<?php
/**
 * 支付宝配置单例类
 * User: 姜伟
 * Date: 2017/6/17 0017
 * Time: 19:15
 */
namespace DesignPatterns\Singletons;

use AliConfigs\DaYu;
use AliConfigs\PayBase;
use Tool\Tool;
use Traits\SingletonTrait;

class AliConfigSingleton {
    use SingletonTrait;

    /**
     * 支付基础配置
     * @var \AliConfigs\PayBase
     */
    private $payBaseConfig = null;
    /**
     * 大鱼配置
     * @var \AliConfigs\DaYu
     */
    private $dayuConfig = null;

    private function __construct() {
        $this->init();
    }

    /**
     * 初始化
     */
    public function init() {
        $configs = \Yaconf::get('ali.' . SY_ENV);

        //设置支付基础配置
        $payBaseConfig = new PayBase();
        $payBaseConfig->setAppId((string)Tool::getArrayVal($configs, 'pay.base.appid', '', true));
        $payBaseConfig->setSellerId((string)Tool::getArrayVal($configs, 'pay.base.seller.id', '', true));
        $payBaseConfig->setUrlNotify((string)Tool::getArrayVal($configs, 'pay.base.url.notify', '', true));
        $payBaseConfig->setUrlReturn((string)Tool::getArrayVal($configs, 'pay.base.url.return', '', true));
        $payBaseConfig->setPriRsaKey((string)Tool::getArrayVal($configs, 'pay.base.prikey.rsa', '', true));
        $payBaseConfig->setPubRsaKey((string)Tool::getArrayVal($configs, 'pay.base.pubkey.rsa', '', true));
        $payBaseConfig->setPubAliKey((string)Tool::getArrayVal($configs, 'pay.base.pubkey.alipay', '', true));
        $this->payBaseConfig = $payBaseConfig;

        //设置大鱼配置
        $dayuConfig = new DaYu();
        $dayuConfig->setAppKey((string)Tool::getArrayVal($configs, 'dayu.app.key', '', true));
        $dayuConfig->setAppSecret((string)Tool::getArrayVal($configs, 'dayu.app.secret', '', true));
        $this->dayuConfig = $dayuConfig;
    }

    /**
     * @return \DesignPatterns\Singletons\AliConfigSingleton
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 获取支付基础配置
     * @return \AliConfigs\PayBase
     */
    public function getPayBaseConfig() {
        return $this->payBaseConfig;
    }

    /**
     * 获取大鱼配置
     * @return \AliConfigs\DaYu
     */
    public function getDaYuConfig() {
        return $this->dayuConfig;
    }
}