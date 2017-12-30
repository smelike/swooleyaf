<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-04
 * Time: 16:34
 */
namespace Wx;

use Constant\ErrorCode;
use Exception\Wx\WxException;

class TemplateMsg {
    public function __construct() {
    }

    /**
     * 用户openid
     * @var string
     */
    private $openid = '';

    /**
     * 模版ID
     * @var string
     */
    private $template_id = '';

    /**
     * 重定向链接地址
     * @var string
     */
    private $redirect_url = '';

    /**
     * 模版数据
     * @var array
     */
    private $template_data = [];

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
     * @param string $templateId
     * @throws \Exception\Wx\WxException
     */
    public function setTemplateId(string $templateId) {
        if (strlen($templateId . '') > 0) {
            $this->template_id = $templateId;
        } else {
            throw new WxException('模版ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $redirectUrl
     * @throws \Exception\Wx\WxException
     */
    public function setRedirectUrl(string $redirectUrl) {
        if (preg_match('/^(http|https|ftp)\:\/\/\S+$/', $redirectUrl . '') > 0) {
            $this->redirect_url = $redirectUrl . '';
        } else {
            throw new WxException('重定向链接不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * 模板参数内容
     * 数据格式如下:
     * [
     *     'first' => [
     *         'value' => '1234',
     *         'color' => '#743A3A',
     *     ],
     *     'remark' => [
     *         'value' => '1234',
     *         'color' => '#743A3A',
     *     ],
     * ]
     *
     *@param array $templateData
     */
    public function setTemplateData(array $templateData) {
        $this->template_data = $templateData;
    }

    public function getDetail() : array {
        if (strlen($this->openid) == 0) {
            throw new WxException('用户openid不能为空', ErrorCode::WX_PARAM_ERROR);
        }
        if (strlen($this->template_id) == 0) {
            throw new WxException('模版ID不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        return [
            'touser' => $this->openid,
            'template_id' => $this->template_id,
            'url' => $this->redirect_url,
            'data' => $this->template_data,
        ];
    }
}