<?php
/**
 * 扫码支付模式一返回微信数据类
 * User: Administrator
 * Date: 2017-04-03
 * Time: 2:53
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;

class NativeReturn {
    public function __construct(string $appId) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->result_code = 'SUCCESS';
        $this->return_code = 'SUCCESS';
    }

    /**
     * 返回状态码
     * @var string
     */
    private $return_code = '';

    /**
     * 返回信息
     * @var string
     */
    private $return_msg = '';

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
     * 微信返回的随机字符串
     * @var string
     */
    private $nonce_str = '';

    /**
     * 预支付ID
     * @var string
     */
    private $prepay_id = '';

    /**
     * 业务结果
     * @var string
     */
    private $result_code = '';

    /**
     * 错误描述
     * @var string
     */
    private $err_code_des = '';

    /**
     * 签名
     * @var string
     */
    private $sign = '';

    /**
     * @param string $nonceStr
     */
    public function setNonceStr(string $nonceStr) {
        $this->nonce_str = $nonceStr;
    }

    /**
     * @param string $prepayId
     */
    public function setPrepayId(string $prepayId) {
        $this->prepay_id = $prepayId;
    }

    /**
     * @param string $errDes 返回给用户的错误描述
     * @param string $returnMsg 返回微信的信息
     * @throws \Exception\Wx\WxException
     */
    public function setErrorMsg(string $errDes,string $returnMsg) {
        if (mb_strlen($errDes . '') == 0) {
            throw new WxException('错误描述不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if (mb_strlen($returnMsg . '') == 0) {
            throw new WxException('返回信息不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $this->return_code = 'FAIL';
        $this->return_msg = mb_substr($returnMsg, 0, 40);
        $this->result_code = 'FAIL';
        $this->err_code_des = mb_substr($errDes, 0, 40);
    }

    public function getDetail() : array {
        $resArr = [];
        $saveArr = get_object_vars($this);
        foreach ($saveArr as $key => $value) {
            if (strlen($value . '') > 0) {
                $resArr[$key] = $value;
            }
        }

        if (($this->return_code == 'SUCCESS') && !isset($resArr['nonce_str'])) {
            throw new WxException('随机字符串不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if (($this->return_code == 'SUCCESS') && !isset($resArr['prepay_id'])) {
            throw new WxException('预支付ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}