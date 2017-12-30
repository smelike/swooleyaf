<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/12/4 0004
 * Time: 11:34
 */

namespace AliDaYu;

use Constant\ErrorCode;
use Exception\Ali\AliDaYuException;
use Tool\Tool;

class DaYuSmsSend extends DaYuSmsBase {
    /**
     * 短信类型
     * @var string
     */
    private $smsType = '';
    /**
     * 接收手机号码
     * @var string
     */
    private $recNum = '';
    /**
     * 签名名称
     * @var string
     */
    private $signName = '';
    /**
     * 模板ID
     * @var string
     */
    private $templateCode = '';
    /**
     * 模板参数
     * @var array
     */
    private $smsParams = [];
    /**
     * @var array
     */
    private $badSmsSignNames = [];

    public function __construct() {
        parent::__construct('alibaba.aliqin.fc.sms.num.send');
        $this->smsType = 'normal';
        $this->badSmsSignNames = [
            '大鱼测试',
            '活动验证',
            '变更验证',
            '登录验证',
            '注册验证',
            '身份验证',
        ];
    }

    private function __clone(){
    }

    /**
     * @param string $recNum
     * @throws \Exception\Ali\AliDaYuException
     */
    public function setRecNum(string $recNum) {
        if (preg_match('/^(\,1\d{10}){1,200}$/', ',' . $recNum) > 0) {
            $this->recNum = $recNum;
        } else {
            throw new AliDaYuException('接收号码不合法', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
    }

    /**
     * @param string $signName
     * @throws \Exception\Ali\AliDaYuException
     */
    public function setSignName(string $signName) {
        if (strlen($signName) == 0) {
            throw new AliDaYuException('签名名称不能为空', ErrorCode::ALIDAYU_PARAM_ERROR);
        } else if (in_array($signName, $this->badSmsSignNames)) {
            throw new AliDaYuException('签名名称不能为系统默认签名', ErrorCode::ALIDAYU_PARAM_ERROR);
        }

        $this->signName = $signName;
    }

    /**
     * @param string $templateId
     * @throws \Exception\Ali\AliDaYuException
     */
    public function setTemplateId(string $templateId) {
        if (strlen($templateId) > 0) {
            $this->templateCode = $templateId;
        } else {
            throw new AliDaYuException('模板ID不能为空', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
    }

    /**
     * @param array $params
     */
    public function setSmsParams(array $params) {
        $this->smsParams = $params;
    }

    public function getDetail() : array {
        if (strlen($this->recNum) == 0) {
            throw new AliDaYuException('接收号码不能为空', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
        if (strlen($this->signName) == 0) {
            throw new AliDaYuException('签名名称不能为空', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
        if (strlen($this->templateCode) == 0) {
            throw new AliDaYuException('模板ID不能为空', ErrorCode::ALIDAYU_PARAM_ERROR);
        }

        $resArr = $this->getBaseDetail();
        $resArr['sms_type'] = $this->smsType;
        $resArr['sms_free_sign_name'] = $this->signName;
        $resArr['rec_num'] = $this->recNum;
        $resArr['sms_template_code'] = $this->templateCode;
        if (!empty($this->smsParams)) {
            $resArr['sms_param'] = Tool::jsonEncode($this->smsParams, JSON_UNESCAPED_UNICODE);
        }
        DaYuUtil::createSmsSign($resArr);

        return $resArr;
    }
}