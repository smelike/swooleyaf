<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-04
 * Time: 0:50
 */
namespace Wx;

use Constant\ErrorCode;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxException;

class OrderBill {
    public function __construct(string $appId) {
        $shopConfig = WxConfigSingleton::getInstance()->getShopConfig($appId);
        $this->appid = $shopConfig->getAppId();
        $this->mch_id = $shopConfig->getPayMchId();
        $this->sign_type = 'MD5';
        $this->nonce_str = WxUtil::createNonceStr();
        $this->tar_type = 'GZIP';
        $this->bill_type = 'ALL';
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
     * 签名
     * @var string
     */
    private $sign = '';

    /**
     * 签名类型
     * @var string
     */
    private $sign_type = '';

    /**
     * 对账单日期
     * @var string
     */
    private $bill_date = '';

    /**
     * 账单类型
     * @var string
     */
    private $bill_type = '';

    /**
     * 压缩账单
     * @var string
     */
    private $tar_type = '';

    /**
     * @param string $billDate
     * @throws \Exception\Wx\WxException
     */
    public function setBillDate(string $billDate) {
        if (preg_match('/^[0-9]{8}$/', $billDate . '') > 0) {
            $this->bill_date = $billDate . '';
        } else {
            throw new WxException('对账单日期不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $billType
     * @throws \Exception\Wx\WxException
     */
    public function setBillType(string $billType) {
        if (in_array($billType, ['ALL', 'SUCCESS', 'REFUND', 'RECHARGE_REFUND'])) {
            $this->bill_type = $billType;
        } else {
            throw new WxException('账单类型不合法', ErrorCode::WX_PARAM_ERROR);
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

        if (!isset($resArr['bill_date'])) {
            throw new WxException('对账单日期不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        $resArr['sign'] = WxUtil::createSign($resArr, $this->appid);

        return $resArr;
    }
}