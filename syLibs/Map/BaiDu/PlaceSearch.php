<?php
/**
 * 地区搜索
 * User: 姜伟
 * Date: 2017/6/19 0019
 * Time: 15:46
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Tool\Tool;

class PlaceSearch extends BaseConfig {
    const LL_COORDINATE_TYPE_WGS = 1; //经纬度坐标类型-GPS
    const LL_COORDINATE_TYPE_GCJ = 2; //经纬度坐标类型-国测局
    const LL_COORDINATE_TYPE_BD = 3; //经纬度坐标类型-百度
    const LL_COORDINATE_TYPE_BD_MC = 4; //经纬度坐标类型-百度墨卡托米制
    const SCOPE_BASE = 1; //结果详细程度-基本信息
    const SCOPE_DETAIL = 2; //结果详细程度-POI详细信息
    const PLACE_SEARCH_REGION_CITY_LIMIT_NO = 'false'; //区域搜索城市限定标识-不限定城市
    const PLACE_SEARCH_REGION_CITY_LIMIT_YES = 'true'; //区域搜索城市限定标识-限定城市
    const PLACE_SEARCH_TYPE_REGION = 'region'; //区域搜索类型-地区
    const PLACE_SEARCH_TYPE_NEARBY = 'nearby'; //区域搜索类型-圆形区域
    const PLACE_SEARCH_TYPE_RECTANGLE = 'rectangle'; //区域搜索类型-矩形区域

    public function __construct() {
        parent::__construct();

        $this->scope = self::SCOPE_BASE;
        $this->coordinateType = self::LL_COORDINATE_TYPE_BD;
        $this->pageSize = 10;
        $this->pageIndex = 1;
        $this->areaRegionCityLimit = self::PLACE_SEARCH_REGION_CITY_LIMIT_NO;
    }

    private function __clone() {
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());

        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 检索关键字数组,最多支持10个关键字检索
     * @var array
     */
    private $keywords = [];
    /**
     * 标签数组
     * @var array
     */
    private $tags = [];
    /**
     * 结果详细程度
     * @var int
     */
    private $scope = 0;
    /**
     * 过滤条件
     * @var string
     */
    private $filter = '';
    /**
     * 坐标类型
     * @var int
     */
    private $coordinateType = 0;
    /**
     * 每页条目数,最大限制为20条
     * @var int
     */
    private $pageSize = 0;
    /**
     * 页数,默认第1页
     * @var int
     */
    private $pageIndex = 0;
    /**
     * 区域搜索地区名称,市级以上行政区域
     * @var string
     */
    private $areaRegionName = '';
    /**
     * 区域搜索是否只返回指定region（城市）内的POI
     * @var string
     */
    private $areaRegionCityLimit = '';
    /**
     * 圆形范围搜索中心点经度
     * @var string
     */
    private $areaNearbyLng = '';
    /**
     * 圆形范围搜索中心点纬度
     * @var string
     */
    private $areaNearbyLat = '';
    /**
     * 圆形范围搜索半径,单位为米
     * @var int
     */
    private $areaNearbyRadius = 0;
    /**
     * 矩形范围搜索西南角经度
     * @var string
     */
    private $areaRectangleLng1 = '';
    /**
     * 矩形范围搜索西南角纬度
     * @var string
     */
    private $areaRectangleLat1 = '';
    /**
     * 矩形范围搜索东北角经度
     * @var string
     */
    private $areaRectangleLng2 = '';
    /**
     * 矩形范围搜索东北角纬度
     * @var string
     */
    private $areaRectangleLat2 = '';

    /**
     * 添加检索关键字
     * @param string $keyword 检索关键字
     * @throws \Exception\Map\BaiduMapException
     */
    public function addKeyword(string $keyword) {
        $str1 = preg_replace([
            '/[\-\_\.\~\!\*\'\(\)\;\:\@\&\=\+\$\,\/\?\%\#\[\]]+/',
            '/\s+/',
        ], [
            '',
            ' ',
        ], $keyword);
        $str2 = trim($str1);
        if(strlen($str2) == 0){
            throw new BaiduMapException('检索关键字不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        if(!in_array($str2, $this->keywords, true)){
            if(count($this->keywords) >= 10){
                throw new BaiduMapException('检索关键字数量超过最大限制', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            $this->keywords[] = $str2;
        }
    }

    /**
     * @return array
     */
    public function getKeywords() : array {
        return $this->keywords;
    }

    /**
     * 添加标签
     * @param string $tag 标签
     * @throws \Exception\Map\BaiduMapException
     */
    public function addTag(string $tag) {
        $str1 = preg_replace([
            '/\,+/',
            '/\s+/'
        ], [
            '',
            ' ',
        ], $tag);
        $str2 = trim($str1);
        if(strlen($str2) == 0){
            throw new BaiduMapException('标签不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        if(!in_array($str2, $this->tags, true)){
            $this->tags[] = $str2;
        }
    }

    /**
     * @return array
     */
    public function getTags() : array {
        return $this->tags;
    }

    /**
     * @return int
     */
    public function getScope() : int {
        return $this->scope;
    }

    /**
     * @param int $scope
     * @throws \Exception\Map\BaiduMapException
     */
    public function setScope(int $scope) {
        if(in_array($scope, [self::SCOPE_BASE, self::SCOPE_DETAIL], true)){
            $this->scope = $scope;
        } else {
            throw new BaiduMapException('结果详细程度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getFilter() : string {
        return $this->filter;
    }

    /**
     * @param string $filter
     * @throws \Exception\Map\BaiduMapException
     */
    public function setFilter(string $filter) {
        $trueFilter = trim($filter);
        if(strlen($trueFilter) > 0){
            $this->filter = $filter;
        } else {
            throw new BaiduMapException('过滤条件不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getCoordinateType() : int {
        return $this->coordinateType;
    }

    /**
     * @param int $coordinateType
     * @throws \Exception\Map\BaiduMapException
     */
    public function setCoordinateType(int $coordinateType) {
        if(in_array($coordinateType, [self::LL_COORDINATE_TYPE_WGS, self::LL_COORDINATE_TYPE_GCJ, self::LL_COORDINATE_TYPE_BD, self::LL_COORDINATE_TYPE_BD_MC], true)){
            $this->coordinateType = $coordinateType;
        } else {
            throw new BaiduMapException('坐标类型不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getPageSize() : int {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     * @throws \Exception\Map\BaiduMapException
     */
    public function setPageSize(int $pageSize) {
        if(($pageSize > 0) && ($pageSize <= 20)){
            $this->pageSize = $pageSize;
        } else {
            throw new BaiduMapException('每页条目数只能在1-20之间', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getPageIndex() : int {
        return $this->pageIndex;
    }

    /**
     * @param int $pageIndex
     * @throws \Exception\Map\BaiduMapException
     */
    public function setPageIndex(int $pageIndex) {
        if($pageIndex > 0){
            $this->pageIndex = $pageIndex;
        } else {
            throw new BaiduMapException('页数必须大于0', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAreaRegionName() : string {
        return $this->areaRegionName;
    }

    /**
     * @param string $areaRegionName
     * @throws \Exception\Map\BaiduMapException
     */
    public function setAreaRegionName(string $areaRegionName) {
        $str1 = preg_replace([
            '/[\-\_\.\~\!\*\'\(\)\;\:\@\&\=\+\$\,\/\?\%\#\[\]]+/',
            '/\s+/',
        ], [
            '',
            ' ',
        ], $areaRegionName);
        $str2 = trim($str1);
        if(strlen($str2) == 0){
            throw new BaiduMapException('区域名称不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $this->areaRegionName = $str2;
    }

    /**
     * @return string
     */
    public function getAreaRegionCityLimit() : string {
        return $this->areaRegionCityLimit;
    }

    /**
     * @param string $areaRegionCityLimit
     * @throws \Exception\Map\BaiduMapException
     */
    public function setAreaRegionCityLimit(string $areaRegionCityLimit) {
        if(in_array($areaRegionCityLimit, [self::PLACE_SEARCH_REGION_CITY_LIMIT_NO, self::PLACE_SEARCH_REGION_CITY_LIMIT_YES], true)){
            $this->areaRegionCityLimit = $areaRegionCityLimit;
        } else {
            throw new BaiduMapException('城市限制标识不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @param string $lng 经度
     * @param string $lat 纬度
     * @throws \Exception\Map\BaiduMapException
     */
    public function getAreaNearbyLngAndLat(string $lng,string $lat) {
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng) == 0){
            throw new BaiduMapException('圆形范围搜索中心点经度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat) == 0){
            throw new BaiduMapException('圆形范围搜索中心点纬度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $this->areaNearbyLng = $lng;
        $this->areaNearbyLat = $lat;
    }

    /**
     * @return string
     */
    public function getAreaNearbyLng() : string {
        return $this->areaNearbyLng;
    }

    /**
     * @return string
     */
    public function getAreaNearbyLat() : string {
        return $this->areaNearbyLat;
    }

    /**
     * @return int
     */
    public function getAreaNearbyRadius() : int {
        return $this->areaNearbyRadius;
    }

    /**
     * @param int $areaNearbyRadius
     * @throws \Exception\Map\BaiduMapException
     */
    public function setAreaNearbyRadius(int $areaNearbyRadius) {
        if($areaNearbyRadius > 0){
            $this->areaNearbyRadius = $areaNearbyRadius;
        } else {
            throw new BaiduMapException('圆形范围搜索半径必须大于0', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @param string $lng1 西南角经度
     * @param string $lat1 西南角纬度
     * @param string $lng2 东北角经度
     * @param string $lat2 东北角纬度
     * @throws \Exception\Map\BaiduMapException
     */
    public function setAreaRectangLngAndLat(string $lng1,string $lat1,string $lng2,string $lat2) {
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng1) == 0){
            throw new BaiduMapException('矩形范围搜索西南角经度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat1) == 0){
            throw new BaiduMapException('矩形范围搜索西南角纬度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng2) == 0){
            throw new BaiduMapException('矩形范围搜索东北角经度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat2) == 0){
            throw new BaiduMapException('矩形范围搜索东北角纬度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
        if((double)$lat1 >= (double)$lat2){
            throw new BaiduMapException('矩形范围搜索东北角纬度必须大于西南角纬度', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $this->areaRectangleLng1 = $lng1;
        $this->areaRectangleLat1 = $lat1;
        $this->areaRectangleLng2 = $lng2;
        $this->areaRectangleLat2 = $lat2;
    }

    /**
     * @return string
     */
    public function getAreaRectangleLng1() : string {
        return $this->areaRectangleLng1;
    }

    /**
     * @return string
     */
    public function getAreaRectangleLat1() : string {
        return $this->areaRectangleLat1;
    }

    /**
     * @return string
     */
    public function getAreaRectangleLng2() : string {
        return $this->areaRectangleLng2;
    }

    /**
     * @return string
     */
    public function getAreaRectangleLat2() : string {
        return $this->areaRectangleLat2;
    }

    /**
     * 根据搜索类型获取搜索内容
     * @param string $searchType 搜索类型
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function getAreaSearchContent(string $searchType) : array {
        $content = [];
        if($searchType == self::PLACE_SEARCH_TYPE_REGION){
            if(strlen($this->areaRegionName) == 0){
                throw new BaiduMapException('区域名称不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            $content['region'] = $this->areaRegionName;
            $content['city_limit'] = $this->areaRegionCityLimit;
        } else if($searchType == self::PLACE_SEARCH_TYPE_NEARBY){
            if((strlen($this->areaNearbyLat) == 0) || (strlen($this->areaNearbyLng) == 0)){
                throw new BaiduMapException('中心点经度和纬度都不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }
            if($this->areaNearbyRadius <= 0){
                throw new BaiduMapException('搜索半径必须大于0', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            $content['location'] = $this->areaNearbyLat . ',' . $this->areaNearbyLng;
            $content['radius'] = $this->areaNearbyRadius;
        } else if($searchType == self::PLACE_SEARCH_TYPE_RECTANGLE){
            if(strlen($this->areaRectangleLng1) == 0){
                throw new BaiduMapException('矩形范围搜索经度和纬度都不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            $content['bounds'] = $this->areaRectangleLat1 . ',' . $this->areaRectangleLng1 . ',' . $this->areaRectangleLat2 . ','
                                 . $this->areaRectangleLng2;
        } else {
            throw new BaiduMapException('区域搜索类型不支持', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        return $content;
    }
}