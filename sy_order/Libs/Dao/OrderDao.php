<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/9/18 0018
 * Time: 9:39
 */
namespace Dao;

use AliPay\AliPayUtil;
use AliPay\PayQrCode;
use AliPay\PayWap;
use Constant\ErrorCode;
use Constant\Project;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\MysqlSingleton;
use Exception\Common\CheckException;
use Interfaces\Containers\PayContainer;
use Log\Log;
use Request\SyRequest;
use Tool\SySession;
use Tool\Tool;
use Traits\SimpleDaoTrait;
use Wx\PayNativePre;
use Wx\UnifiedOrder;
use Wx\WxUtil;

class OrderDao {
    use SimpleDaoTrait;

    private static $payModelMap = [
        'a00' => [
            'verify' => 'verifyPayModelAliCode',
            'handle' => 'handlePayModelAliCode',
        ],
        'a01' => [
            'verify' => 'verifyPayModelAliWeb',
            'handle' => 'handlePayModelAliWeb',
        ],
        'b00' => [
            'verify' => 'verifyPayModelWxJs',
            'handle' => 'handlePayModelWxJs',
        ],
        'b01' => [
            'verify' => 'verifyPayModelWxNativeDynamic',
            'handle' => 'handlePayModelWxNativeDynamic',
        ],
        'b02' => [
            'verify' => 'verifyPayModelWxNativeStatic',
            'handle' => 'handlePayModelWxNativeStatic',
        ],
    ];
    private static $payContentMap = [
        Project::ORDER_PAY_TYPE_GOODS => [
            'apply' => 'handleApplyPayGoods',
            'complete' => 'handleCompletePayGoods',
        ],
    ];
    /**
     * @var \Interfaces\Containers\PayContainer
     */
    private static $payContainer = null;
    private static $payList = [];

    /**
     * @SyFilter-{"field": "ali_timeout","explain": "订单过期时间","type": "string","rules": {"min": 0,"max": 20}}
     * @return array
     */
    private static function verifyPayModelAliCode() {
        return [
            'ali_timeout' => (string)SyRequest::getParams('ali_timeout', ''),
        ];
    }

    /**
     * @SyFilter-{"field": "ali_returnurl","explain": "同步通知链接","type": "string","rules": {"required": 1,"url": 1}}
     * @SyFilter-{"field": "ali_timeout","explain": "订单过期时间","type": "string","rules": {"min": 0,"max": 20}}
     * @return array
     * @throws \Exception\Common\CheckException
     */
    private static function verifyPayModelAliWeb() {
        $returnUrl = (string)SyRequest::getParams('ali_returnurl', '');
        if(strlen($returnUrl) == 0){
            throw new CheckException('同步通知链接不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'ali_returnurl' => $returnUrl,
            'ali_timeout' => (string)SyRequest::getParams('ali_timeout', ''),
        ];
    }

    /**
     * @return array
     * @throws \Exception\Common\CheckException
     */
    private static function verifyPayModelWxJs() {
        $openid = SySession::get('user.openid2', '');
        if (strlen($openid) == 0) {
            throw new CheckException('请先微信登录', ErrorCode::USER_NOT_LOGIN_WX_AUTH);
        } else if (preg_match('/^[0-9a-zA-Z\-\_]{28}$/', $openid) == 0) {
            throw new CheckException('用户openid不合法', ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'wx_openid' => $openid,
        ];
    }

    /**
     * @return array
     */
    private static function verifyPayModelWxNativeDynamic() {
        return [];
    }

    /**
     * @return array
     */
    private static function verifyPayModelWxNativeStatic() {
        return [];
    }

    private static function handlePayModelAliCode(array $data) {
        $pay = new PayQrCode();
        $pay->setSubject($data['pay_name']);
        $pay->setTotalAmount($data['pay_money']);
        $pay->setAttach($data['pay_attach']);
        $pay->setTimeoutExpress($data['ali_timeout']);
        $pay->setOutTradeNo($data['pay_sn']);
        $payRes = AliPayUtil::applyQrCodePay($pay);
        if ($payRes['code'] > 0) {
            throw new CheckException($payRes['message'], ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'qr_code' => $payRes['data']['qr_code'],
        ];
    }

    private static function handlePayModelAliWeb(array $data) {
        $pay = new PayWap();
        $pay->setReturnUrl($data['ali_returnurl']);
        $pay->setSubject($data['pay_name']);
        $pay->setTotalAmount($data['pay_money']);
        $pay->setAttach($data['pay_attach']);
        $pay->setTimeoutExpress($data['ali_timeout']);
        $pay->setOutTradeNo($data['pay_sn']);

        return [
            'html' => AliPayUtil::createWapPayHtml($pay),
        ];
    }

    private static function handlePayModelWxJs(array $data) {
        $order = new UnifiedOrder(UnifiedOrder::TRADE_TYPE_JSAPI);
        $order->setBody($data['pay_name']);
        $order->setTotalFee($data['pay_money']);
        $order->setOutTradeNo($data['pay_sn']);
        $order->setAttach($data['pay_attach']);
        $order->setOpenid($data['wx_openid']);
        $applyRes = WxUtil::applyJsPay($order, 'shop');
        if($applyRes['code'] > 0){
            throw new CheckException($applyRes['message'], ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'config' => $applyRes['data']['config'],
            'api' => $applyRes['data']['pay'],
        ];
    }

    private static function handlePayModelWxNativeDynamic(array $data) {
        $order = new UnifiedOrder(UnifiedOrder::TRADE_TYPE_NATIVE);
        $order->setBody($data['pay_name']);
        $order->setTotalFee($data['pay_money']);
        $order->setOutTradeNo($data['pay_sn']);
        $order->setAttach($data['pay_attach']);
        $applyRes = WxUtil::applyNativePay($order);
        if($applyRes['code'] > 0){
            throw new CheckException($applyRes['message'], ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'code_url' => $applyRes['data']['code_url']
        ];
    }

    private static function handlePayModelWxNativeStatic(array $data) {
        $prePay = new PayNativePre();
        $prePay->setProductId($data['pay_sn']);
        $applyRes = WxUtil::applyPreNativePay($prePay);

        $redis = CacheSimpleFactory::getRedisInstance();
        $redisKey = Server::REDIS_PREFIX_WX_NATIVE_PRE . $data['pay_sn'];
        $redis->set($redisKey, Tool::jsonEncode([
            'pay_name' => $data['pay_name'],
            'pay_money' => $data['pay_money'],
            'pay_attach' => $data['pay_attach'],
            'pay_sn' => $data['pay_sn'],
        ], JSON_UNESCAPED_UNICODE), 7200);

        return [
            'code_url' => $applyRes
        ];
    }

    /**
     * @SyFilter-{"field": "order_sn","explain": "订单单号","type": "string","rules": {"min": 22,"max": 22}}
     * @return array
     * 格式如下:
     * [
     *      'pay_name' => 'aaa' --支付名称,不能超过60个汉字
     *      'pay_money' => 100 --支付金额,单位为分,必须大于0
     *      'pay_attach' => '123_daf' --附加数据,没有则设置成空字符串,只设置一些关键数据且长度不能超过127字节
     *      'pay_sn' => '00002017061322131212345678' --支付单号
     * ]
     * @throws \Exception\Common\CheckException
     */
    private static function handleApplyPayGoods() {
        $orderSn = (string)SyRequest::getParams('order_sn', '');
        if(strlen($orderSn) == 0){
            throw new CheckException('订单单号不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        return [
            'pay_name' => 'aaa',
            'pay_money' => 100,
            'pay_attach' => '123_daf',
            'pay_sn' => '00012017061322131212345678',
        ];
    }

    public static function applyPay(string $applyType){
        $modelType = substr($applyType, 0, 3);
        $modelFuncArr = Tool::getArrayVal(self::$payModelMap, $modelType, null);
        if(is_null($modelFuncArr)){
            throw new CheckException('支付模式不支持', ErrorCode::COMMON_PARAM_ERROR);
        }
        $modelVerifyFunc = $modelFuncArr['verify'];
        $modelVerifyRes = self::$modelVerifyFunc();

        $contentType = substr($applyType, 3);
        $contentFuncArr = Tool::getArrayVal(self::$payContentMap, $contentType, null);
        if(is_null($contentFuncArr)){
            throw new CheckException('支付内容不支持', ErrorCode::COMMON_PARAM_ERROR);
        }
        $contentApplyFunc = $contentFuncArr['apply'];
        $contentApplyRes = self::$contentApplyFunc();

        $modelHandleFunc = $modelFuncArr['handle'];
        $modelData = array_merge($modelVerifyRes, $contentApplyRes);

        return self::$modelHandleFunc($modelData);
    }

    /**
     * @param string $payType 支付类型
     * @return \Interfaces\PayService
     */
    public static function getPayService(string $payType) {
        if(isset(self::$payList[$payType])){
            return self::$payList[$payType];
        }

        if(is_null(self::$payContainer)){
            self::$payContainer = new PayContainer();
        }

        $service = self::$payContainer->getObj($payType);
        if(!is_null($service)){
            self::$payList[$payType] = $service;
        }

        return $service;
    }

    public static function completePay(array $data){
        $payType = substr($data['pay_sn'], 0, 4);
        $payService = self::getPayService($payType);
        if(is_null($payService)){
            throw new CheckException('支付类型不支持', ErrorCode::COMMON_PARAM_ERROR);
        }

        $successRes = [];
        try {
            MysqlSingleton::getInstance()->getConn()->beginTransaction();
            $successRes = $payService->handlePaySuccess($data);
            MysqlSingleton::getInstance()->getConn()->commit();
        } catch (\Exception $e) {
            MysqlSingleton::getInstance()->getConn()->rollBack();
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            throw new CheckException('支付处理失败', ErrorCode::COMMON_SERVER_ERROR);
        } finally {
            if(!empty($successRes)){
                $payService->handlePaySuccessAttach($successRes);
            }
        }
    }
}