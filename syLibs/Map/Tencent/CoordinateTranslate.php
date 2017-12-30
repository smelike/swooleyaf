<?php
/**
 * 坐标转换类
 * User: 姜伟
 * Date: 2017/6/20 0020
 * Time: 19:56
 */
namespace Map\Tencent;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;
use Tool\Tool;

class CoordinateTranslate extends BaseConfig {
    const COORDINATE_TYPE_GPS = 1; //坐标类型-GPS
    const COORDINATE_TYPE_SOGOU = 2; //坐标类型-搜狗
    const COORDINATE_TYPE_BD = 3; //坐标类型-百度
    const COORDINATE_TYPE_MAPBAR = 4; //坐标类型-mapbar
    const COORDINATE_TYPE_GOOGLE = 5; //坐标类型-google
    const COORDINATE_TYPE_SOGOU_MC = 6; //坐标类型-搜狗墨卡托

    public function __construct() {
        parent::__construct();

        $this->fromType = self::COORDINATE_TYPE_GOOGLE;
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
     * 添加坐标
     * @param string $lng
     * @param string $lat
     * @throws \Exception\Map\TencentMapException
     */
    public function addCoordinate(string $lng,string $lat) {
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng) == 0){
            throw new TencentMapException('源坐标经度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat) == 0){
            throw new TencentMapException('源坐标纬度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $str = $lat . ',' . $lng;
        if(!in_array($str, $this->coords, true)){
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
     */
    public function setFromType(int $fromType) {
        if(in_array($fromType, [self::COORDINATE_TYPE_GPS, self::COORDINATE_TYPE_SOGOU, self::COORDINATE_TYPE_BD, self::COORDINATE_TYPE_MAPBAR, self::COORDINATE_TYPE_GOOGLE, self::COORDINATE_TYPE_SOGOU_MC,], true)){
            $this->fromType = $fromType;
        } else {
            throw new TencentMapException('源坐标类型不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }
}