<?php
/**
 * 微信公共类
 * User: 姜伟
 * Date: 2017/1/21 0021
 * Time: 9:05
 */
namespace Wx;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;
use Log\Log;
use SyServer\BaseServer;
use Tool\Tool;
use Traits\SimpleTrait;

final class WxUtil {
    use SimpleTrait;

    private static $urlUnifiedOrder = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    private static $urlAccessToken = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
    private static $urlJsTicket = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=';
    private static $urlShorturl = 'https://api.mch.weixin.qq.com/tools/shorturl';
    private static $urlQrCode = 'http://paysdk.weixin.qq.com/example/qrcode.php?data=';
    private static $urlOrderClose = 'https://api.mch.weixin.qq.com/pay/closeorder';
    private static $urlOrderQuery = 'https://api.mch.weixin.qq.com/pay/orderquery';
    private static $urlOrderRefund = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    private static $urlRefundQuery = 'https://api.mch.weixin.qq.com/pay/refundquery';
    private static $urlDownloadBill = 'https://api.mch.weixin.qq.com/pay/downloadbill';
    private static $urlMicroPay = 'https://api.mch.weixin.qq.com/pay/micropay';
    private static $urlAuthorizeBase = 'https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code&appid=';
    private static $urlAuthorizeInfo = 'https://api.weixin.qq.com/sns/userinfo?lang=zh_CN&access_token=';
    private static $urlAuthorizeRefreshToken = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?grant_type=refresh_token&appid=';
    private static $urlSendTemplateMsg = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=';
    private static $urlUserInfo = 'https://api.weixin.qq.com/cgi-bin/user/info?lang=zh_CN&access_token=';
    private static $urlUserInfoList = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=';
    private static $urlGetMenu = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=';
    private static $urlCreateMenu = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=';
    private static $urlDeleteMenu = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=';
    private static $urlIpList = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=';
    private static $urlCompanyPay = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    private static $urlQueryCompanyPay = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';
    private static $urlDownloadMedia = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=';

    private static $errorsShortUrl = [
        'XML_FORMAT_ERROR' => 'XML格式错误',
        'POST_DATA_EMPTY' => 'post数据为空',
        'LACK_PARAMS' => '缺少参数',
        'APPID_NOT_EXIST' => 'APPID不存在',
        'MCHID_NOT_EXIST' => 'MCHID不存在',
        'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',
        'REQUIRE_POST_METHOD' => '请使用post方法',
        'SIGNERROR' => '签名错误',
    ];

    private static $chars = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z',
    ];

    /**
     * 生成随机字符串
     * @param int $length 需要获取的随机字符串长度
     * @return string
     */
    public static function createNonceStr(int $length=32) : string {
        $resStr = '';
        for ($i = 0; $i < $length; $i++) {
            $resStr .= self::$chars[mt_rand(0, 35)];
        }

        return $resStr;
    }

    /**
     * 数组格式化成url参数
     * @param array $data
     * @return string
     */
    private static function arrayToUrlParams(array $data) {
        $buff = '';
        foreach ($data as $key => $value) {
            if (($key != 'sign') && (!is_array($value)) && (strlen($value . '') > 0)) {
                $buff .= $key . '=' . $value . '&';
            }
        }

        return trim($buff, '&');
    }

    /**
     * 生成签名
     * @param array $data
     * @param string $appId
     * @return string
     */
    public static function createSign(array $data,string $appId) {
        //签名步骤一：按字典序排序参数
        ksort($data);
        //签名步骤二：格式化后加入KEY
        $needStr1 = self::arrayToUrlParams($data) . '&key='. WxConfigSingleton::getInstance()->getShopConfig($appId)->getPayKey();
        //签名步骤三：MD5加密
        $needStr2 = md5($needStr1);
        //签名步骤四：所有字符转为大写
        return strtoupper($needStr2);
    }

    /**
     * 数组转xml
     * @param array $data
     * @return string
     * @throws \Exception\Wx\WxException
     */
    public static function arrayToXml(array $data) : string {
        if (count($data) == 0) {
            throw new WxException('数组为空', ErrorCode::WX_PARAM_ERROR);
        }

        $xml = '<xml>';
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';

        return $xml;
    }

    /**
     * xml转数组
     * @param string $xml
     * @return array
     * @throws \Exception\Wx\WxException
     */
    public static function xmlToArray(string $xml) : array {
        if (strlen($xml . '') == 0) {
            throw new WxException('xml数据异常', ErrorCode::WX_PARAM_ERROR);
        }

        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $element = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonStr = Tool::jsonEncode($element);
        return Tool::jsonDecode($jsonStr);
    }

    /**
     * 发送post请求
     * @param string|array $data 数据
     * @param string $url 请求地址
     * @param array $configs 配置数组
     * @param array $extends 扩展信息数组
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    private static function sendPost(string $url, $data,array $configs=[],array &$extends=[]) {
        $dataStr = '';
        if(is_string($data)){
            $dataStr = $data;
        } else if(is_array($data)){
            $dataType = Tool::getArrayVal($configs, 'data_type', 'query');
            if($dataType == 'xml'){
                $dataStr = self::arrayToXml($data);
            } else if($dataType == 'json'){
                $dataStr = Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE);
            } else if($dataType == 'query'){
                $dataStr = http_build_query($data);
            }
        }
        if(strlen($dataStr) == 0){
            throw new WxException('数据格式不合法', ErrorCode::WX_PARAM_ERROR);
        }

        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 2000);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);

        if (Tool::getArrayVal($configs, 'ssl_verify', true)) { //是否需要ssl认证，默认需要
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        //设置header
        curl_setopt($ch, CURLOPT_HEADER, Tool::getArrayVal($configs, 'headers', false));
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (Tool::getArrayVal($configs, 'use_cert', false)) { //是否需要证书，默认不需要
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, Tool::getArrayVal($configs, 'sslcert_path', ''));
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, Tool::getArrayVal($configs, 'sslkey_path', ''));
        }

        $resData = curl_exec($ch);
        $errorNo = curl_errno($ch);
        $extends['head'] = curl_getinfo($ch);
        curl_close($ch);
        if ($errorNo == 0) {
            return $resData;
        } else {
            throw new WxException('curl出错，错误码=' . $errorNo, ErrorCode::WX_POST_ERROR);
        }
    }

    /**
     * 发送get请求
     * @param string $url 请求地址
     * @param int $timeout 执行超时时间,单位为毫秒，默认为2s
     * @param array $extends 扩展信息数组
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    private static function sendGetReq(string $url,int $timeout=2000,array &$extends=[]) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $errorNo = curl_errno($ch);
        $extends['head'] = curl_getinfo($ch);
        curl_close($ch);
        if ($errorNo == 0) {
            return $data;
        } else {
            throw new WxException('curl出错，错误码=' . $errorNo, ErrorCode::WX_GET_ERROR);
        }
    }

    /**
     * 发起jsapi支付
     * @param \Wx\UnifiedOrder $order 订单信息
     * @param string $platType 平台类型 shop：公众号 open：第三方平台
     * @param string $appId 授权者微信号
     * @return array
     */
    public static function applyJsPay(UnifiedOrder $order,string $platType,string $appId='') : array {
        $resArr = [
            'code' => 0,
        ];

        //发起统一下单
        $orderDetail = $order->getDetail();
        $reqXml = self::arrayToXml($orderDetail);
        $resXml = self::sendPost(self::$urlUnifiedOrder, $reqXml);
        $resData = self::xmlToArray($resXml);
        if($resData['return_code'] == 'FAIL'){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if($resData['result_code'] == 'FAIL'){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            //获取支付参数
            $payConfig = new JsPayConfig($orderDetail['appid']);
            $payConfig->setTimeStamp(time() . '');
            $payConfig->setPackage($resData['prepay_id']);
            //获取js参数
            $jsConfig = new JsConfig($orderDetail['appid']);
            $resArr['data'] = [
                'config' => $jsConfig->getDetail($platType, $appId),
                'pay' => $payConfig->getDetail(),
            ];
            unset($payConfig, $jsConfig);
        }

        return $resArr;
    }

    /**
     * 发起扫码支付
     * @param \Wx\UnifiedOrder $order 订单信息
     * @return array
     */
    public static function applyNativePay(UnifiedOrder $order) : array {
        $resArr = [
            'code' => 0,
        ];

        //发起统一下单
        $orderDetail = $order->getDetail();
        $reqXml = self::arrayToXml($orderDetail);
        $resXml = self::sendPost(self::$urlUnifiedOrder, $reqXml);
        $resData = self::xmlToArray($resXml);
        if($resData['return_code'] == 'FAIL'){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if($resData['result_code'] == 'FAIL'){
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            $resArr['data'] = [
                'code_url' => self::$urlQrCode . urlencode($resData['code_url']),
                'prepay_id' => $resData['prepay_id'],
            ];
        }

        return $resArr;
    }

    /**
     * 发起扫码支付模式一的预支付请求
     * @param \Wx\PayNativePre $prePay 预支付信息
     * @return string
     */
    public static function applyPreNativePay(PayNativePre $prePay) : string {
        //生成支付链接
        $payDetail = $prePay->getDetail();
        $codeUrl = 'weixin://wxpay/bizpayurl?sign=' . $payDetail['sign']
                   . '&appid=' . $payDetail['appid']
                   . '&mch_id=' . $payDetail['mch_id']
                   . '&product_id=' . $payDetail['product_id']
                   . '&time_stamp=' . $payDetail['time_stamp']
                   . '&nonce_str=' . $payDetail['nonce_str'];
        //转换成短链接
        $shortUrl = new ShortUrl($payDetail['appid']);
        $shortUrl->setLongUrl($codeUrl);
        $urlDetail = $shortUrl->getDetail();
        $reqXml = self::arrayToXml($urlDetail);
        $resXml = self::sendPost(self::$urlShorturl, $reqXml);
        $resData = self::xmlToArray($resXml);
        if ($resData['return_code'] == 'FAIL') {
            Log::error($resData['return_msg'], ErrorCode::WX_PARAM_ERROR);
            $url = self::$urlQrCode . urlencode($codeUrl);
        } else if ($resData['result_code'] == 'FAIL') {
            $error = Tool::getArrayVal(self::$errorsShortUrl, $resData['err_code'], $resData['err_code']);
            Log::error($error, ErrorCode::WX_PARAM_ERROR);
            $url = self::$urlQrCode . urlencode($codeUrl);
        } else {
            $url = self::$urlQrCode . urlencode($resData['short_url']);
        }

        return $url;
    }

    /**
     * 发起企业付款
     * @param \Wx\PayCompany $companyPay 企业付款对象
     * @return array
     */
    public static function applyCompanyPay(PayCompany $companyPay) : array {
        $resArr = [
            'code' => 0,
        ];

        $companyDetail = $companyPay->getDetail();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($companyDetail['mch_appid']);
        $reqXml = self::arrayToXml($companyDetail);
        $resXml = self::sendPost(self::$urlCompanyPay, $reqXml, [
            'use_cert' => true,
            'sslcert_path' => $shopConfig->getSslCert(),
            'sslkey_path' => $shopConfig->getSslKey(),
        ]);
        $resData = self::xmlToArray($resXml);
        if ($resData['return_code'] == 'FAIL') {
            Log::error($resData['return_msg'], ErrorCode::WX_PARAM_ERROR);

            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if ($resData['result_code'] == 'FAIL') {
            Log::error($resData['err_code'], ErrorCode::WX_PARAM_ERROR);

            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            $resArr['data'] = $resData;
        }

        return $resArr;
    }

    /**
     * 发起企业付款查询
     * @param \Wx\PayCompanyQuery $query 企业付款查询对象
     * @return array
     */
    public static function applyCompanyPayQuery(PayCompanyQuery $query) : array {
        $resArr = [
            'code' => 0,
        ];

        $queryDetail = $query->getDetail();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($queryDetail['appid']);
        $reqXml = self::arrayToXml($queryDetail);
        $resXml = self::sendPost(self::$urlQueryCompanyPay, $reqXml, [
            'use_cert' => true,
            'sslcert_path' => $shopConfig->getSslCert(),
            'sslkey_path' => $shopConfig->getSslKey(),
        ]);
        $resData = self::xmlToArray($resXml);
        if ($resData['return_code'] == 'FAIL') {
            Log::error($resData['return_msg'], ErrorCode::WX_PARAM_ERROR);

            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if ($resData['result_code'] == 'FAIL') {
            Log::error($resData['err_code'], ErrorCode::WX_PARAM_ERROR);

            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            $resArr['data'] = $resData;
        }

        return $resArr;
    }

    /**
     * 获取access token
     * @param string $appId
     * @return string
     */
    public static function getAccessToken(string $appId) : string {
        return (string)BaseServer::getProjectCache('wx01_' . $appId, 'value', '');
    }

    /**
     * 获取jsapi ticket
     * @param string $appId
     * @return string
     */
    public static function getJsTicket(string $appId) : string {
        return (string)BaseServer::getProjectCache('wx02_' . $appId, 'value', '');
    }

    /**
     * 刷新access token
     * @param string $appId
     * @return string
     * @throws \Exception\Wx\WxException
     */
    public static function refreshAccessToken(string $appId) : string {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        if(is_null($shopConfig)){
            throw new WxException('微信appid不支持', ErrorCode::WX_PARAM_ERROR);
        }

        $url = self::$urlAccessToken . '&appid=' . $shopConfig->getAppId() . '&secret=' . $shopConfig->getSecret();
        $data = self::sendGetReq($url);
        $dataArr = Tool::jsonDecode($data);
        if(!is_array($dataArr)){
            throw new WxException('获取access token出错', ErrorCode::WX_PARAM_ERROR);
        } else if(!isset($dataArr['access_token'])){
            throw new WxException($dataArr['errmsg'], ErrorCode::WX_PARAM_ERROR);
        }

        $redisKey = Server::REDIS_PREFIX_WX_ACCESS_TOKEN . $shopConfig->getAppId();
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, [
            'app_id' => $appId,
            'access_token' => $dataArr['access_token'],
            'expire_time' => time() + 7000,
        ]);

        return $dataArr['access_token'];
    }

    /**
     * 刷新jsapi ticket
     * @param string $appId
     * @param string $accessToken
     * @return mixed
     * @throws \Exception\Wx\WxException
     */
    public static function refreshJsTicket(string $appId,string $accessToken) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        if(is_null($shopConfig)){
            throw new WxException('微信appid不支持', ErrorCode::WX_PARAM_ERROR);
        }

        $url = self::$urlJsTicket . $accessToken;
        $data = self::sendGetReq($url);
        $dataArr = Tool::jsonDecode($data);
        if(!is_array($dataArr)){
            throw new WxException('获取js ticket出错', ErrorCode::WX_PARAM_ERROR);
        } else if($dataArr['errcode'] > 0){
            throw new WxException($dataArr['errmsg'], ErrorCode::WX_PARAM_ERROR);
        }

        $redisKey = Server::REDIS_PREFIX_WX_JS_TICKET . $shopConfig->getAppId();
        CacheSimpleFactory::getRedisInstance()->hMset($redisKey, [
            'app_id' => $appId,
            'js_ticket' => $dataArr['ticket'],
            'expire_time' => time() + 7000,
        ]);

        return $dataArr['ticket'];
    }

    /**
     * 校验数据签名合法性
     * @param array $data 待校验数据
     * @param string $appId
     * @return bool
     */
    public static function checkSign(array $data,string $appId) : bool {
        if (isset($data['sign']) && is_string($data['sign'])) {
            $sign = $data['sign'];
            $nowSign = self::createSign($data, $appId);
            if ($sign === $nowSign) {
                return true;
            }
        }

        return false;
    }

    /**
     * 查询订单详情
     * @param \Wx\OrderQuery $query 查询对象
     * @return array
     */
    public static function queryOrder(OrderQuery $query) : array {
        $resArr = [
            'code' => 0
        ];

        $queryDetail = $query->getDetail();
        $reqXml = self::arrayToXml($queryDetail);
        $resXml = self::sendPost(self::$urlOrderQuery, $reqXml);
        $resData = self::xmlToArray($resXml);
        if ($resData['return_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if ($resData['result_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            $resArr['data'] = $resData;
        }

        return $resArr;
    }

    /**
     * 查询退款详情
     * @param \Wx\RefundQuery $query 查询对象
     * @return array
     */
    public static function queryRefund(RefundQuery $query) : array {
        $resArr = [
            'code' => 0
        ];

        $queryDetail = $query->getDetail();
        $reqXml = self::arrayToXml($queryDetail);
        $resXml = self::sendPost(self::$urlRefundQuery, $reqXml);
        $resData = self::xmlToArray($resXml);
        if ($resData['return_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if ($resData['result_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            $resArr['data'] = $resData;
        }

        return $resArr;
    }

    /**
     * 申请订单退款
     * @param \Wx\OrderRefund $refund 退款对象
     * @return array
     */
    public static function applyOrderRefund(OrderRefund $refund) : array {
        $resArr = [
            'code' => 0
        ];

        $refundDetail = $refund->getDetail();
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($refundDetail['appid']);
        $reqXml = self::arrayToXml($refundDetail);
        $resXml = self::sendPost(self::$urlOrderRefund, $reqXml, [
            'use_cert' => true,
            'sslcert_path' => $shopConfig->getSslCert(),
            'sslkey_path' => $shopConfig->getSslKey(),
        ]);
        $resData = self::xmlToArray($resXml);
        if ($resData['return_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if ($resData['result_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        }
        $resArr['data'] = $resData;

        return $resArr;
    }

    /**
     * 下载对账单
     * @param \Wx\OrderBill $bill 对账单对象
     * @return array
     */
    public static function downloadBill(OrderBill $bill) : array {
        $resArr = [
            'code' => 0
        ];

        $billDetail = $bill->getDetail();
        $reqXml = self::arrayToXml($billDetail);
        $resXml = self::sendPost(self::$urlDownloadBill, $reqXml);
        if (substr($resXml, 0, 5) == '<xml>') {
            $resData = self::xmlToArray($resXml);
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else {
            echo $resXml;

            $resArr['data'] = [
                'return_code' => 'SUCCESS',
            ];
        }

        return $resArr;
    }

    /**
     * 关闭订单
     * @param \Wx\OrderClose $close
     * @return array
     */
    public static function closeOrder(OrderClose $close) : array {
        $resArr = [
            'code' => 0
        ];

        $closeDetail = $close->getDetail();
        $reqXml = self::arrayToXml($closeDetail);
        $resXml = self::sendPost(self::$urlOrderClose, $reqXml);
        $resData = self::xmlToArray($resXml);
        if ($resData['return_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if ($resData['result_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            $resArr['data'] = $resData;
        }

        return $resArr;
    }

    /**
     * 发起刷卡支付
     * @param \Wx\PayMicro $pay
     * @return array
     */
    public static function applyMicroPay(PayMicro $pay) : array {
        $resArr = [
            'code' => 0
        ];

        $payDetail = $pay->getDetail();
        $reqXml = self::arrayToXml($payDetail);
        $resXml = self::sendPost(self::$urlMicroPay, $reqXml);
        $resData = self::xmlToArray($resXml);
        if ($resData['return_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['return_msg'];
        } else if ($resData['result_code'] == 'FAIL') {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['err_code_des'];
        } else {
            $resArr['data'] = $resData;
        }

        return $resArr;
    }

    /**
     * 获取用户授权地址
     * @param string $redirectUrl 跳转地址
     * @param string $type 授权类型 base：静默授权 user：手动授权
     * @param string $appId
     * @return string
     */
    public static function getAuthorizeUrl(string $redirectUrl,string $type,string $appId) : string {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
            . WxConfigSingleton::getInstance()->getShopConfig($appId)->getAppId()
            . '&redirect_uri='
            . urlencode($redirectUrl)
            . '&response_type=code&scope=';
        if ($type == 'base') {
            $url .= 'snsapi_base';
        } else {
            $url .= 'snsapi_userinfo';
        }
        $url .= '&state=STATE#wechat_redirect';

        return $url;
    }

    /**
     * 下载微信媒体图片文件
     * @param string $mediaId 媒体ID
     * @param string $path 下载目录,带最后的/
     * @param string $appId
     * @return array
     */
    public static function downloadMediaImage(string $mediaId,string $path,string $appId) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlDownloadMedia . self::getAccessToken($appId) . '&media_id=' . $mediaId;
        $getRes = self::sendGetReq($url, 3000);
        $getData = Tool::jsonDecode($getRes);
        if(is_array($getData) && isset($getData['errcode']) && ($getData['errcode'] == 40001)){ //解决微信缓存刷新导致access token失效的问题
            $url = self::$urlDownloadMedia . self::getAccessToken($appId) . '&media_id=' . $mediaId;
            $getRes = self::sendGetReq($url);
            $getData = Tool::jsonDecode($getRes);
        }

        if(is_array($getData)){
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        } else {
            $fileName = substr($path, -1) == '/' ? $path : $path . '/';
            $fileName .= $mediaId . '.jpg';
            file_put_contents($fileName, $getRes);
            $resArr['data'] = [
                'image_path' => $fileName,
            ];
        }

        return $resArr;
    }

    /**
     * 处理用户静默授权
     * @param string $code 换取授权access_token的票据
     * @param string $appId
     * @return array
     */
    public static function handleUserAuthorizeBase(string $code,string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $url = self::$urlAuthorizeBase . $shopConfig->getAppId() . '&secret=' . $shopConfig->getSecret() . '&code=' . $code;
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if (isset($getData['access_token'])) {
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 处理用户手动授权
     * @param string $code 换取授权access_token的票据
     * @param string $appId
     * @return array
     */
    public static function handleUserAuthorizeInfo(string $code,string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $url = self::$urlAuthorizeBase . $shopConfig->getAppId() . '&secret=' . $shopConfig->getSecret() . '&code=' . $code;
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if (!isset($getData['access_token'])) {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
            return $resArr;
        }

        $openid = $getData['openid'];
        $url = self::$urlAuthorizeInfo . $getData['access_token'] . '&openid=' . $openid;
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if(isset($getData['errcode']) && ($getData['errcode'] == 40001)){
            $url = self::$urlUserInfo . self::getAccessToken($appId) . '&openid=' . $openid;
            $getRes = self::sendGetReq($url);
            $getData = Tool::jsonDecode($getRes);
        }

        if(isset($getData['openid'])){
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 发送模版消息
     * @param \Wx\TemplateMsg $msg
     * @param string $appId
     * @return array
     */
    public static function sendTemplateMsg(TemplateMsg $msg,string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $msgDetail = $msg->getDetail();
        $url = self::$urlSendTemplateMsg . self::getAccessToken($appId);
        $sendRes = self::sendPost($url, $msgDetail, [
            'data_type' => 'json',
        ]);
        $resData = Tool::jsonDecode($sendRes);
        if ($resData['errcode'] == 0) {
            $resData['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取单个用户信息详情
     * @param \Wx\UserInfo $userInfo
     * @param string $appId
     * @return array
     */
    public static function getUserInfo(UserInfo $userInfo,string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        if ($userInfo->getOpenid() != '') {
            $url = self::$urlUserInfo . self::getAccessToken($appId);
            $getRes = self::sendGetReq($url);
            $getData = Tool::jsonDecode($getRes);
            if (isset($getData['openid'])) {
                $resArr['data'] = $getData;
            } else {
                $resArr['code'] = ErrorCode::WX_GET_ERROR;
                $resArr['message'] = $getData['errmsg'];
            }
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = '用户openid不能为空';
        }

        return $resArr;
    }

    /**
     * 批量获取用户信息详情
     * @param array $infoList
     * @param string $appId
     * @return array
     */
    public static function getUserInfoList(array $infoList,string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $saveArr = [
            'user_list' => [],
        ];
        foreach ($infoList as $eInfo) {
            if (($eInfo instanceof UserInfo) && ($eInfo->getOpenid() != '')) {
                $saveArr['user_list'][] = [
                    'openid' => $eInfo->getOpenid(),
                    'lang' => 'zh-CN',
                ];
            }
        }
        if (empty($saveArr['user_list'])) {
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = '用户信息列表不能为空';
            return $resArr;
        }

        $url = self::$urlUserInfoList . self::getAccessToken($appId);
        $sendRes = self::sendPost($url, $saveArr, [
            'data_type' => 'json',
        ]);
        $resData = Tool::jsonDecode($sendRes);
        if (isset($resData['user_info_list'])) {
            $resArr['data'] = $resData['user_info_list'];
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取菜单
     * @param string $appId
     * @return array
     */
    public static function getMenu(string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlGetMenu . self::getAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $resData = Tool::jsonDecode($getRes);
        if (isset($resData['menu'])) {
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = '获取菜单列表失败';
        }

        return $resArr;
    }

    /**
     * 删除菜单
     * @param string $appId
     * @return array
     */
    public static function deleteMenu(string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlDeleteMenu . self::getAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $resData = Tool::jsonDecode($getRes);
        if ($resData['errcode'] == 0) {
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $resData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 创建菜单
     * @param array $menuList 菜单列表
     * @param string $appId
     * @return array
     */
    public static function createMenu(array $menuList,string $appId) : array {
        $resArr = [
            'code' => 0
        ];

        $saveArr = [
            'button' => [],
        ];
        foreach ($menuList as $eMenu) {
            if ($eMenu instanceof Menu) {
                $saveArr['button'][] = $eMenu->getDetail();
            }

            if (count($saveArr['button']) == 3) {
                break;
            }
        }
        if (empty($saveArr['button'])) {
            $resArr['code'] = ErrorCode::WX_PARAM_ERROR;
            $resArr['message'] = '菜单列表不能为空';
            return $resArr;
        }

        $url = self::$urlCreateMenu . self::getAccessToken($appId);
        $sendRes = self::sendPost($url, $saveArr, [
            'data_type' => 'json',
        ]);
        $resData = Tool::jsonDecode($sendRes);
        if ($resData['errcode'] == 0) {
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WX_POST_ERROR;
            $resArr['message'] = $resData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取微信服务器IP列表
     * @param string $appId
     * @return string
     */
    public static function getIpList(string $appId) : string {
        $resArr = [
            'code' => 0
        ];

        $url = self::$urlIpList . self::getAccessToken($appId);
        $getRes = self::sendGetReq($url);
        $getData = Tool::jsonDecode($getRes);
        if (isset($getData['ip_list'])) {
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WX_GET_ERROR;
            $resArr['message'] = $getData['errmsg'];
        }

        return $resArr;
    }

    /**
     * 获取前端js分享配置
     * @param string $appId
     * @param string $url 链接地址
     * @param string $timestamp 时间戳
     * @param string $nonceStr 随机字符串
     * @return array 空数组为获取失败
     */
    public static function getJsShareConfig(string $appId,string $url,string $timestamp='',string $nonceStr=''){
        $ticket = self::getJsTicket($appId);
        if(strlen($ticket) > 0){
            $nowTime = preg_match('/^[1-4][0-9]{9}$/', $timestamp) > 0 ? $timestamp : time() . '';
            $nonce = strlen($nonceStr) >= 16 ? $nonceStr : self::createNonceStr();
            $needStr = 'jsapi_ticket=' . $ticket . '&noncestr=' . $nonce . '&timestamp=' . $nowTime . '&url=' . $url;
            return [
                'appId' => WxConfigSingleton::getInstance()->getShopConfig($appId)->getAppId(),
                'nonceStr' => $nonce,
                'timestamp' => $nowTime,
                'signature' => sha1($needStr),
            ];
        } else {
            return [];
        }
    }
}