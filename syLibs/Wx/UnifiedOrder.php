<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-3-31
 * Time: 上午7:42
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Tool\Tool;

class UnifiedOrder {
    const TRADE_TYPE_JSAPI = 'JSAPI'; //支付方式-jsapi
    const TRADE_TYPE_NATIVE = 'NATIVE'; //支付方式-扫码
    const TRADE_TYPE_MWEB = 'MWEB'; //支付方式-h5
    const SCENE_TYPE_IOS = 'IOS'; //场景类型-ios
    const SCENE_TYPE_ANDROID = 'Android'; //场景类型-android
    const SCENE_TYPE_WAP = 'Wap'; //场景类型-wap

    private static $tradeTypes = [
        self::TRADE_TYPE_JSAPI,
        self::TRADE_TYPE_NATIVE,
        self::TRADE_TYPE_MWEB,
    ];

    private static $sceneTypes = [
        self::SCENE_TYPE_IOS,
        self::SCENE_TYPE_ANDROID,
        self::SCENE_TYPE_WAP,
    ];

    /**
     * UnifiedOrder constructor.
     * @param string $tag 初始化标识
     * @param string $appId
     * @throws \Exception\Wx\WxException
     */
    public function __construct(string $tag,string $appId) {
        if(!in_array($tag, self::$tradeTypes)){
            throw new WxException('统一下单初始化错误', ErrorCode::WX_PARAM_ERROR);
        }

        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->fee_type = 'CNY';
        $this->nonce_str = WxUtil::createNonceStr();
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->notify_url = $shopConfig->getPayNotifyUrl();
        $this->device_info = 'WEB';
        $this->sign_type = 'MD5';
        $this->trade_type = $tag;
        if ($tag != self::TRADE_TYPE_MWEB) {
            $this->spbill_create_ip = $shopConfig->getClientIp();
        }
    }

    /**
     * 公众账号ID
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
     * 标价币种
     * @var string
     */
    private $fee_type = '';

    /**
     * 标价金额，单位为分
     * @var int
     */
    private $total_fee = 0;

    /**
     * 终端IP
     * @var string
     */
    private $spbill_create_ip = '';

    /**
     * 交易起始时间，格式为yyyyMMddHHmmss，
     * @var string
     */
    private $time_start = '';

    /**
     * 交易结束时间，格式为yyyyMMddHHmmss，
     * @var string
     */
    private $time_expire = '';

    /**
     * 商品标记，使用代金券或立减优惠功能时需要的参数
     * @var string
     */
    private $goods_tag = '';

    /**
     * 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数
     * @var string
     */
    private $notify_url = '';

    /**
     * 交易类型，取值如下：JSAPI，NATIVE，APP等
     * @var string
     */
    private $trade_type = '';

    /**
     * 商品ID trade_type=NATIVE时（即扫码支付），此参数必传
     * @var string
     */
    private $product_id = '';

    /**
     * 用户标识 trade_type=JSAPI时（即公众号支付），此参数必传
     * @var string
     */
    private $openid = '';

    /**
     * 签名类型 签名类型，默认为MD5，支持HMAC-SHA256和MD5
     * @var string
     */
    private $sign_type = '';

    /**
     * 签名
     * @var string
     */
    private $sign = '';

    /**
     * 场景信息，json格式
     * @var string
     */
    private $scene_info = '';

    /**
     * @param string $body
     * @throws \Exception\Wx\WxException
     */
    public function setBody(string $body) {
        if (mb_strlen($body . '') > 0) {
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
            throw new WxException('附加数据过长', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $outTradeNo
     * @throws \Exception\Wx\WxException
     */
    public function setOutTradeNo(string $outTradeNo) {
        if (preg_match('/^[0-9]{1,32}$/', $outTradeNo . '') > 0) {
            $this->out_trade_no = $outTradeNo . '';
            if($this->trade_type == 'NATIVE'){
                $this->product_id = $outTradeNo . '';
            }
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
     * @param string $openid
     * @throws \Exception\Wx\WxException
     */
    public function setOpenid(string $openid) {
        if (preg_match('/^[0-9a-zA-Z\-\_]{28}$/', $openid . '') > 0) {
            $this->openid = $openid . '';
        } else {
            throw new WxException('用户openid不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $ip
     * @throws \Exception\Wx\WxException
     */
    public function setTerminalIp(string $ip) {
        if (preg_match('/^(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){4}$/', '.' . $ip) > 0) {
            $this->spbill_create_ip = $ip;
        } else {
            throw new WxException('终端IP不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $desc
     * @throws \Exception\Wx\WxException
     */
    public function setSceneInfo(string $type,string $name,string $desc) {
        if (!in_array($type, self::$sceneTypes)) {
            throw new WxException('场景类型不支持', ErrorCode::WX_PARAM_ERROR);
        }

        $trueName = preg_replace('/\s+/', '', $name);
        if (($type == self::SCENE_TYPE_WAP) && (preg_match('/^(http|https)\:\/\/\S+$/', $trueName) == 0)) {
            throw new WxException('网站地址不合法', ErrorCode::WX_PARAM_ERROR);
        } else if (($type != self::SCENE_TYPE_WAP) && (strlen($trueName) == 0)) {
            throw new WxException('应用名不合法', ErrorCode::WX_PARAM_ERROR);
        }

        if (strlen(trim($desc)) == 0) {
            if ($type == self::SCENE_TYPE_IOS) {
                throw new WxException('bundle id不能为空', ErrorCode::WX_PARAM_ERROR);
            } else if ($type == self::SCENE_TYPE_ANDROID) {
                throw new WxException('包名不能为空', ErrorCode::WX_PARAM_ERROR);
            } else if ($type == self::SCENE_TYPE_WAP) {
                throw new WxException('网站名不能为空', ErrorCode::WX_PARAM_ERROR);
            }
        }

        if ($type == self::SCENE_TYPE_IOS) {
            $sceneData = [
                'h5_info' => [
                    'type' => $type,
                    'app_name' => $trueName,
                    'bundle_id' => trim($desc),
                ],
            ];
        } else if ($type == self::SCENE_TYPE_ANDROID) {
            $sceneData = [
                'h5_info' => [
                    'type' => $type,
                    'app_name' => $trueName,
                    'package_name' => trim($desc),
                ],
            ];
        } else {
            $sceneData = [
                'h5_info' => [
                    'type' => $type,
                    'wap_url' => $trueName,
                    'wap_name' => trim($desc),
                ],
            ];
        }

        $this->scene_info = Tool::jsonEncode($sceneData, JSON_UNESCAPED_UNICODE);
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
        if ($resArr['total_fee'] <= 0) {
            throw new WxException('支付金额不能小于0', ErrorCode::WX_PARAM_ERROR);
        }
        if (($resArr['trade_type'] == self::TRADE_TYPE_JSAPI) && (!isset($resArr['openid']))) {
            throw new WxException('用户openid不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if (($resArr['trade_type'] == self::TRADE_TYPE_NATIVE) && (!isset($resArr['product_id']))) {
            throw new WxException('商品ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if (($resArr['trade_type'] == self::TRADE_TYPE_MWEB)) {
            if (!isset($resArr['spbill_create_ip'])) {
                throw new WxException('终端IP不能为空', ErrorCode::WX_PARAM_ERROR);
            }
            if (!isset($resArr['scene_info'])) {
                throw new WxException('场景信息不能为空', ErrorCode::WX_PARAM_ERROR);
            }
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}