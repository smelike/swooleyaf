<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-8
 * Time: 下午10:48
 */
namespace AliPay;

use Constant\ErrorCode;
use DesignPatterns\Singletons\AliConfigSingleton;
use Exception\Ali\AliPayException;
use Log\Log;
use Tool\Tool;
use Traits\SimpleTrait;

final class AliPayUtil {
    use SimpleTrait;

    const CODE_RESPONSE_SUCCESS = '10000'; //状态码-响应成功

    private static $urlGateWay = 'https://openapi.alipay.com/gateway.do';

    /**
     * 校验$value是否非空
     * @param mixed $value
     * @return bool true：空 false:非空
     */
    private static function checkEmpty($value) : bool {
        if (!isset($value)) {
            return true;
        }
        if ($value === null) {
            return true;
        }
        if (trim($value) === '') {
            return true;
        }

        return false;
    }

    /**
     * 转换字符集编码
     * @param mixed $data
     * @param string $targetCharset
     * @return string
     */
    private static function convertCharset($data, $targetCharset) {
        if (!empty($data)) {
            if (strcasecmp('UTF-8', $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, 'UTF-8');
            }
        }

        return $data;
    }

    /**
     * 获取待签名字符串
     * @param array $params
     * @return string
     */
    private static function getSignContent(array $params) : string {
        $needStr = '';
        ksort($params);

        foreach ($params as $k => $v) {
            if ((false === self::checkEmpty($v)) && ('@' != substr($v, 0, 1))) {
                // 转换成目标字符集
                $needStr .= '&' . $k . '=' . self::convertCharset($v, 'UTF-8');
            }
        }
        unset ($k, $v);

        return strlen($needStr) > 0 ? substr($needStr, 1) : '';
    }

    /**
     * 生成签名字符串
     * @param array $data 数据数组
     * @param string $signType 签名方式，只支持RSA和RSA2
     * @return string
     */
    public static function createSign(array $data,string $signType='RSA') : string {
        $dataStr = self::getSignContent($data);
        $priKey = AliConfigSingleton::getInstance()->getPayBaseConfig()->getPriRsaKey();
        $key = "-----BEGIN RSA PRIVATE KEY-----" . PHP_EOL . wordwrap($priKey, 64, "\n", true) . PHP_EOL . "-----END RSA PRIVATE KEY-----";
        if ("RSA2" == $signType) {
            openssl_sign($dataStr, $signature, $key, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($dataStr, $signature, $key);
        }

        return base64_encode($signature);
    }

    /**
     * 校验签名
     * @param array $data 数据数组
     * @param string $verifyType 校验类型 1：不校验数据签名类型 2：校验数据签名类型
     * @param string $signType 签名类型,只支持RSA和RSA2
     * @return bool
     */
    public static function verifyData(array $data,string $verifyType='1',string $signType='RSA') : bool {
        if (isset($data['sign']) && is_string($data['sign']) && (strlen($data['sign'] . '') > 0)) {
            $sign = $data['sign'];
            $data['sign'] = null;
            if ($verifyType == '1') {
                $data['sign_type'] = null;
            }

            $dataStr = self::getSignContent($data);
            $pubKey = AliConfigSingleton::getInstance()->getPayBaseConfig()->getPubAliKey();
            $key = "-----BEGIN PUBLIC KEY-----" . PHP_EOL . wordwrap($pubKey, 64, "\n", true) . PHP_EOL . "-----END PUBLIC KEY-----";
            if ("RSA2" == $signType) {
                $result = (bool)openssl_verify($dataStr, base64_decode($sign), $key, OPENSSL_ALGO_SHA256);
            } else {
                $result = (bool)openssl_verify($dataStr, base64_decode($sign), $key);
            }

            return $result;
        }

        return false;
    }

    /**
     * 生成网页支付表单
     * @param PayWap $wap 网页支付对象
     * @return string
     */
    public static function createWapPayHtml(PayWap $wap) : string {
        $data = $wap->getDetail();
        $html = '<form id="alipaysubmit" name="alipaysubmit" action="' . self::$urlGateWay . '?charset=utf-8" method="POST">';
        foreach ($data as $key => $eData) {
            if (false === self::checkEmpty($eData)) {
                $val = str_replace("'", "&apos;", $eData);
                $html .= '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
            }
        }
        $html .= '<input type="submit" value="ok" style="display:none;" /></form><script>document.forms["alipaysubmit"].submit();</script>';

        return $html;
    }

    /**
     * 发起扫码支付
     * @param PayQrCode $qrCode 扫码支付对象
     * @return array
     */
    public static function applyQrCodePay(PayQrCode $qrCode) : array {
        $resArr = [
            'code' => 0,
        ];

        $data = $qrCode->getDetail();
        $sendRes = self::sendPostReq(self::$urlGateWay, $data);
        $resData = Tool::jsonDecode($sendRes);
        if (isset($resData['alipay_trade_precreate_response'])) {
            if ($resData['alipay_trade_precreate_response']['code'] . '' == self::CODE_RESPONSE_SUCCESS) {
                $resArr['data'] = [
                    'out_trade_no' => $resData['alipay_trade_precreate_response']['out_trade_no'],
                    'qr_code' => $resData['alipay_trade_precreate_response']['qr_code'],
                ];
            } else {
                Log::error(Tool::jsonEncode($resData['alipay_trade_precreate_response'], JSON_UNESCAPED_UNICODE), ErrorCode::ALIPAY_POST_ERROR);

                $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
                $resArr['message'] = $resData['alipay_trade_precreate_response']['sub_msg'];
            }
        } else {
            Log::error($sendRes, ErrorCode::ALIPAY_POST_ERROR);

            $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
            $resArr['message'] = '支付宝返回数据格式出错';
        }

        return $resArr;
    }

    /**
     * 发起订单查询
     * @param TradeQuery $query 查询订单对象
     * @return array
     */
    public static function applyQueryTrade(TradeQuery $query) : array {
        $resArr = [
            'code' => 0,
        ];

        $data = $query->getDetail();
        $sendRes = self::sendPostReq(self::$urlGateWay, $data);
        $resData = Tool::jsonDecode($sendRes);
        if (isset($resData['alipay_trade_query_response'])) {
            if ($resData['alipay_trade_query_response']['code'] . '' == self::CODE_RESPONSE_SUCCESS) {
                $resArr['data'] = $resData['alipay_trade_query_response'];
            } else {
                Log::error(Tool::jsonEncode($resData['alipay_trade_query_response'], JSON_UNESCAPED_UNICODE), ErrorCode::ALIPAY_POST_ERROR);

                $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
                $resArr['message'] = $resData['alipay_trade_query_response']['sub_msg'];
            }
        } else {
            Log::error($sendRes, ErrorCode::ALIPAY_POST_ERROR);

            $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
            $resArr['message'] = '支付宝返回数据格式出错';
        }

        return $resArr;
    }

    /**
     * 发起订单撤销
     * @param TradeCancel $cancel 撤销订单对象
     * @return array
     */
    public static function applyCancelTrade(TradeCancel $cancel) : array {
        $resArr = [
            'code' => 0,
        ];

        $data = $cancel->getDetail();
        $sendRes = self::sendPostReq(self::$urlGateWay, $data);
        $resData = Tool::jsonDecode($sendRes);
        if (isset($resData['alipay_trade_cancel_response'])) {
            if ($resData['alipay_trade_query_response']['code'] . '' == self::CODE_RESPONSE_SUCCESS) {
                $resArr['data'] = $resData['alipay_trade_cancel_response'];
            } else {
                Log::error(Tool::jsonEncode($resData['alipay_trade_cancel_response'], JSON_UNESCAPED_UNICODE), ErrorCode::ALIPAY_POST_ERROR);

                $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
                $resArr['message'] = $resData['alipay_trade_cancel_response']['sub_msg'];
            }
        } else {
            Log::error($sendRes, ErrorCode::ALIPAY_POST_ERROR);

            $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
            $resArr['message'] = '支付宝返回数据格式出错';
        }

        return $resArr;
    }

    /**
     * 发起订单退款
     * @param TradeRefund $query 退款订单对象
     * @return array
     */
    public static function applyRefundTrade(TradeRefund $refund) : array {
        $resArr = [
            'code' => 0,
        ];

        $data = $refund->getDetail();
        $sendRes = self::sendPostReq(self::$urlGateWay, $data);
        $resData = Tool::jsonDecode($sendRes);
        if (isset($resData['alipay_trade_refund_response'])) {
            if ($resData['alipay_trade_refund_response']['code'] . '' == self::CODE_RESPONSE_SUCCESS) {
                $resArr['data'] = $resData['alipay_trade_refund_response'];
            } else {
                Log::error(Tool::jsonEncode($resData['alipay_trade_refund_response'], JSON_UNESCAPED_UNICODE), ErrorCode::ALIPAY_POST_ERROR);

                $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
                $resArr['message'] = $resData['alipay_trade_refund_response']['sub_msg'];
            }
        } else {
            Log::error($sendRes, ErrorCode::ALIPAY_POST_ERROR);

            $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
            $resArr['message'] = '支付宝返回数据格式出错';
        }

        return $resArr;
    }

    /**
     * 发起订单退款查询
     * @param TradeRefundQuery $query 退款查询订单对象
     * @return array
     */
    public static function applyRefundQueryTrade(TradeRefundQuery $refundQuery) : array {
        $resArr = [
            'code' => 0,
        ];

        $data = $refundQuery->getDetail();
        $sendRes = self::sendPostReq(self::$urlGateWay, $data);
        $resData = Tool::jsonDecode($sendRes);
        if (isset($resData['alipay_trade_fastpay_refund_query_response'])) {
            if ($resData['alipay_trade_fastpay_refund_query_response']['code'] . '' == self::CODE_RESPONSE_SUCCESS) {
                $resArr['data'] = $resData['alipay_trade_fastpay_refund_query_response'];
            } else {
                Log::error(Tool::jsonEncode($resData['alipay_trade_fastpay_refund_query_response'], JSON_UNESCAPED_UNICODE), ErrorCode::ALIPAY_POST_ERROR);

                $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
                $resArr['message'] = $resData['alipay_trade_fastpay_refund_query_response']['sub_msg'];
            }
        } else {
            Log::error($sendRes, ErrorCode::ALIPAY_POST_ERROR);

            $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
            $resArr['message'] = '支付宝返回数据格式出错';
        }

        return $resArr;
    }

    /**
     * 发起订单关闭
     * @param TradeClose $close 关闭订单对象
     * @return array
     */
    public static function applyCloseTrade(TradeClose $close) : array {
        $resArr = [
            'code' => 0,
        ];

        $data = $close->getDetail();
        $sendRes = self::sendPostReq(self::$urlGateWay, $data);
        $resData = Tool::jsonDecode($sendRes);
        if (isset($resData['alipay_trade_close_response'])) {
            if ($resData['alipay_trade_close_response']['code'] . '' == self::CODE_RESPONSE_SUCCESS) {
                $resArr['data'] = $resData['alipay_trade_close_response'];
            } else {
                Log::error(Tool::jsonEncode($resData['alipay_trade_close_response'], JSON_UNESCAPED_UNICODE), ErrorCode::ALIPAY_POST_ERROR);

                $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
                $resArr['message'] = $resData['alipay_trade_close_response']['sub_msg'];
            }
        } else {
            Log::error($sendRes, ErrorCode::ALIPAY_POST_ERROR);

            $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
            $resArr['message'] = '支付宝返回数据格式出错';
        }

        return $resArr;
    }

    /**
     * 发起下载订单对账单
     * @param TradeBillDownload $billDownload 对账单订单对象
     * @return array
     */
    public static function applyDownloadTradeBill(TradeBillDownload $billDownload) : array {
        $resArr = [
            'code' => 0,
        ];

        $data = $billDownload->getDetail();
        $sendRes = self::sendPostReq(self::$urlGateWay, $data);
        $resData = Tool::jsonDecode($sendRes);
        if (isset($resData['alipay_data_dataservice_bill_downloadurl_query_response'])) {
            if ($resData['alipay_data_dataservice_bill_downloadurl_query_response']['code'] . '' == self::CODE_RESPONSE_SUCCESS) {
                $resArr['data'] = [
                    'download_url' => $resData['alipay_data_dataservice_bill_downloadurl_query_response']['bill_download_url'],
                ];
            } else {
                Log::error(Tool::jsonEncode($resData['alipay_data_dataservice_bill_downloadurl_query_response'], JSON_UNESCAPED_UNICODE), ErrorCode::ALIPAY_POST_ERROR);

                $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
                $resArr['message'] = $resData['alipay_data_dataservice_bill_downloadurl_query_response']['sub_msg'];
            }
        } else {
            Log::error($sendRes, ErrorCode::ALIPAY_POST_ERROR);

            $resArr['code'] = ErrorCode::ALIPAY_POST_ERROR;
            $resArr['message'] = '支付宝返回数据格式出错';
        }

        return $resArr;
    }

    /**
     * 发送POST请求
     * @param string $url 请求地址
     * @param array $data 请求参数
     * @param array $configs 配置数组
     * @return mixed
     * @throws \Exception\Ali\AliPayException
     */
    private static function sendPostReq(string $url,array $data,array $configs=[]) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 2000);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
            'Expect:',
        ]);

        $resData = curl_exec($ch);
        $errorNo = curl_errno($ch);
        if ($errorNo == 0) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if (200 !== $httpCode) {
                throw new AliPayException($resData, ErrorCode::ALIPAY_POST_ERROR);
            }
        } else {
            curl_close($ch);
            throw new AliPayException('curl出错，错误码=' . $errorNo, ErrorCode::ALIPAY_POST_ERROR);
        }

        return $resData;
    }
}