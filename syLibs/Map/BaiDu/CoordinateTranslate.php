<?php
/**
 * 坐标转换类
 * User: 姜伟
 * Date: 2017/6/20 0020
 * Time: 19:56
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Tool\Tool;

class CoordinateTranslate extends BaseConfig {
    const COORDINATE_TYPE_GPS = 1; //坐标类型-GPS角度
    const COORDINATE_TYPE_GPS_MS = 2; //坐标类型-GPS米制
    const COORDINATE_TYPE_GOOGLE = 3; //坐标类型-google
    const COORDINATE_TYPE_GOOGLE_MS = 4; //坐标类型-google米制
    const COORDINATE_TYPE_BD = 5; //坐标类型-百度
    const COORDINATE_TYPE_BD_MS = 6; //坐标类型-百度米制
    const COORDINATE_TYPE_MAPBAR = 7; //坐标类型-mapbar
    const COORDINATE_TYPE_51 = 8; //坐标类型-51

    public function __construct() {
        parent::__construct();
        $this->fromType = self::COORDINATE_TYPE_GPS;
        $this->toType = self::COORDINATE_TYPE_BD;
    }

    private function __clone() {
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());

        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 源坐标数组
     * @var array
     */
    private $coords = [];
    /**
     * 源坐标类型
     * @var int
     */
    private $fromType = 0;
    /**
     * 目的坐标类型
     * @var int
     */
    private $toType = 0;

    /**
     * 添加坐标
     * @param string $lng
     * @param string $lat
     * @throws \Exception\Map\BaiduMapException
     */
    public function addCoordinate(string $lng,string $lat) {
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng) == 0){
            throw new BaiduMapException('源坐标经度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat) == 0){
            throw new BaiduMapException('源坐标纬度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $str = $lng . ',' . $lat;
        if(!in_array($str, $this->coords, true)){
            if(count($this->coords) >= 100){
                throw new BaiduMapException('源坐标数量超过限制', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            $this->coords[] = $str;
        }
    }

    /**
     * @return array
     */
    public function getCoords() : array {
        return $this->coords;
    }

    /**
     * @return int
     */
    public function getFromType() : int {
        return $this->fromType;
    }

    /**
     * @param int $fromType
     * @throws \Exception\Map\BaiduMapException
     */
    public function setFromType(int $fromType) {
        if(in_array($fromType, [self::COORDINATE_TYPE_GPS, self::COORDINATE_TYPE_GPS_MS, self::COORDINATE_TYPE_GOOGLE, self::COORDINATE_TYPE_GOOGLE_MS, self::COORDINATE_TYPE_BD, self::COORDINATE_TYPE_BD_MS, self::COORDINATE_TYPE_MAPBAR, self::COORDINATE_TYPE_51,], true)){
            $this->fromType = $fromType;
        } else {
            throw new BaiduMapException('源坐标类型不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getToType() : int {
        return $this->toType;
    }

    /**
     * @param int $toType
     * @throws \Exception\Map\BaiduMapException
     */
    public function setToType(int $toType) {
        if(in_array($toType, [self::COORDINATE_TYPE_BD, self::COORDINATE_TYPE_BD_MS], true)){
            $this->toType = $toType;
        } else {
            throw new BaiduMapException('目的坐标类型不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }
}