<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-19
 * Time: 下午1:55
 */
class PayController extends CommonController {
    public function init() {
        parent::init();
    }

    /**
     * 发起支付申请
     */
    public function applyPayAction() {
        $allParams = \Request\SyRequest::getParams();
        $allParams['_sytoken'] = \Tool\SySession::getSessionId();
        $applyRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/applyPay', $allParams);
        $this->sendRsp($applyRes);
    }

    /**
     * 处理微信支付通知
     */
    public function handleWxPayNotifyAction() {
        $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleWxPayNotify', [
            'wx_xml' => \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', ''),
        ]);
        $resData = \Tool\Tool::jsonDecode($handleRes);

        $this->sendRsp($resData['data']);
    }

    /**
     * 处理微信扫码预支付通知
     */
    public function handleWxPrePayNotifyAction() {
        $handleRes = \SyModule\SyModuleOrder::getInstance()->sendApiReq('/Index/Pay/handleWxPrePayNotify', [
            'wx_xml' => \Tool\Tool::getArrayVal($GLOBALS, 'HTTP_RAW_POST_DATA', ''),
        ]);
        $resData = \Tool\Tool::jsonDecode($handleRes);

        $this->sendRsp($resData['data']);
    }

    /**
     * 处理支付宝网页支付同步回跳地址
     * @api {get} /Pay/handleAliWebRedirect 处理支付宝网页支付同步回跳地址
     * @apiDescription 处理支付宝网页支付同步回跳地址
     * @apiGroup OrderPay
     * @apiParam {string} url 同步回跳URL地址
     * @apiParam {string} _sytoken 令牌标识
     * @apiSuccess HandleSuccess 处理成功
     * @apiSuccessExample success:
     *     HTTP/1.1 302
     *     {
     *         "Location": "http://www.baidu.com"
     *     }
     * @apiSuccess HandleFail 处理失败
     * @apiSuccessExample fail:
     *     跳转地址不正确
     * @SyFilter-{"field": "url","explain": "同步回跳URL地址","type": "string","rules": {"required": 1,"url": 1}}
     * @SyFilter-{"field": "_sytoken","explain": "令牌标识","type": "string","rules": {"required": 1,"min": 1}}
     */
    public function handleAliWebRedirectAction() {
        $expireTime = time() + 604800;
        $allParams = \Request\SyRequest::getParams();
        \Response\SyResponseHttp::cookie('token', $allParams['_sytoken'], $expireTime, '/', \SyServer\HttpServer::getServerConfig('cookiedomain_base', ''));
        \Response\SyResponseHttp::redirect($allParams['url']);
    }
}