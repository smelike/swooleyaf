<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-04
 * Time: 18:14
 */
namespace Wx;

use Constant\ErrorCode;
use Exception\Wx\WxException;

class Menu {
    private static $typeList = [
        'pic_weixin',
        'pic_sysphoto',
        'pic_photo_or_album',
        'view',
        'view_limited',
        'click',
        'media_id',
        'location_select',
        'scancode_push',
        'scancode_waitmsg',
    ];

    public function __construct() {
    }

    /**
     * 菜单标题
     * @var string
     */
    private $name = '';

    /**
     * 子菜单
     * @var array
     */
    private $sub_button = [];

    /**
     * 响应动作类型
     * @var string
     */
    private $type = '';

    /**
     * 菜单KEY值，用于消息接口推送
     * @var string
     */
    private $key = '';

    /**
     * 网页链接，用户点击菜单可打开链接
     * @var string
     */
    private $url = '';

    /**
     * 媒体ID
     * @var string
     */
    private $media_id = '';

    /**
     * @param string $name
     * @throws \Exception\Wx\WxException
     */
    public function setName(string $name) {
        if (strlen($name . '') > 0) {
            $this->name = mb_substr($name . '', 0, 5);
        } else {
            throw new WxException('菜单名称不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param array $sub
     * @throws \Exception\Wx\WxException
     */
    public function addSub(array $sub) {
        if (empty($sub)) {
            throw new WxException('子菜单不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        if (count($this->sub_button) < 5) {
            $this->sub_button[] = $sub;
        } else {
            throw  new WxException('子菜单不能超过5个', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $type
     * @throws \Exception\Wx\WxException
     */
    public function setType(string $type) {
        if (in_array($type, self::$typeList)) {
            $this->type = $type;
        } else {
            throw  new WxException('响应动作类型不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $key
     */
    public function setKey(string $key) {
        $this->key = substr($key . '', 0, 128);
    }

    /**
     * @param string $url
     * @throws \Exception\Wx\WxException
     */
    public function setUrl(string $url) {
        if (preg_match('/^(http|https)\:\/\/\S+$/', $url . '') > 0) {
            $this->url = $url . '';
        } else {
            throw new WxException('网页链接不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @param string $mediaId
     * @throws \Exception\Wx\WxException
     */
    public function setMediaId(string $mediaId) {
        if (strlen($mediaId . '') > 0) {
            $this->media_id = $mediaId . '';
        } else {
            throw new WxException('媒体ID不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        $resArr = [];
        $saveArr = get_object_vars($this);
        foreach ($saveArr as $key => $value) {
            if (is_array($value)) {
                $resArr[$key] = $value;
            } else if (strlen($value . '') > 0) {
                $resArr[$key] = $value;
            }
        }

        if (!isset($resArr['name'])) {
            throw new WxException('菜单名称不能为空', ErrorCode::WX_PARAM_ERROR);
        }

        return $resArr;
    }
}