<?php
/**
 * IP定位
 * User: jw
 * Date: 17-6-21
 * Time: 上午12:07
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Tool\Tool;

class IpLocation extends BaseConfig {
    const COORD_TYPE_BD_MC = ''; //坐标类型-百度墨卡托
    const COORD_TYPE_BD = 'bd09ll'; //坐标类型-百度
    const COORD_TYPE_GCJ = 'gcj02'; //坐标类型-国测局

    public function __construct() {
        parent::__construct();

        $this->returnCoordType = self::COORD_TYPE_BD;
    }

    private function __clone() {
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());

        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * IP
     * @var string
     */
    private $ip = '';
    /**
     * 返回坐标类型
     * @var string
     */
    private $returnCoordType = '';

    /**
     * @return string
     */
    public function getIp() : string {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @throws \Exception\Map\BaiduMapException
     */
    public function setIp(string $ip) {
        if (preg_match('/^(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){3}$/', $ip) > 0) {
            $this->ip = $ip;
        } else {
            throw new BaiduMapException('ip不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getReturnCoordType() : string {
        return $this->returnCoordType;
    }

    /**
     * @param string $returnCoordType
     * @throws \Exception\Map\BaiduMapException
     */
    public function setReturnCoordType(string $returnCoordType) {
        if (in_array($returnCoordType, [self::COORD_TYPE_BD_MC, self::COORD_TYPE_BD, self::COORD_TYPE_GCJ], true)) {
            $this->returnCoordType = $returnCoordType;
        } else {
            throw new BaiduMapException('返回坐标类型不支持', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }
}