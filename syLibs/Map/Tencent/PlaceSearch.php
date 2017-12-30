<?php
/**
 * 地区搜索
 * User: 姜伟
 * Date: 2017/6/19 0019
 * Time: 15:41
 */
namespace Map\Tencent;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;
use Tool\Tool;

class PlaceSearch extends BaseConfig {
    const REGION_AUTO_EXTENT_NO = 0;
    const REGION_AUTO_EXTENT_YES = 1;
    const PLACE_SEARCH_TYPE_REGION = 'region'; //区域搜索类型-地区
    const PLACE_SEARCH_TYPE_NEARBY = 'nearby'; //区域搜索类型-圆形区域
    const PLACE_SEARCH_TYPE_RECTANGLE = 'rectangle'; //区域搜索类型-矩形区域

    public function __construct() {
        parent::__construct();

        $this->pageSize = 10;
        $this->pageIndex = 1;
        $this->areaRegionAutoExtend = self::REGION_AUTO_EXTENT_YES;
    }

    private function __clone() {
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());

        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 搜索关键字
     * @var string
     */
    private $keyword = '';
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
     * 筛选条件
     * @var string
     */
    private $filter = '';
    /**
     * 排序方式
     * @var string
     */
    private $orderBy = '';
    /**
     * 区域搜索城市名称
     * @var string
     */
    private $areaRegionCityName = '';
    /**
     * 区域搜索是否自动扩大范围 0:仅在当前城市搜索 1:若当前城市搜索无结果,则自动扩大范围
     * @var int
     */
    private $areaRegionAutoExtend = 1;
    /**
     * 区域搜索经度
     * @var string
     */
    private $areaRegionLng = '';
    /**
     * 区域搜索纬度
     * @var string
     */
    private $areaRegionLat = '';
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
     * @return string
     */
    public function getKeyword() : string {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     * @throws \Exception\Map\TencentMapException
     */
    public function setKeyword(string $keyword) {
        $trueWord = trim($keyword);
        if(strlen($trueWord) > 0){
            $this->keyword = $trueWord;
        } else {
            throw new TencentMapException('搜索关键字不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
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
     * @throws \Exception\Map\TencentMapException
     */
    public function setPageSize(int $pageSize) {
        if(($pageSize > 0) && ($pageSize <= 20)){
            $this->pageSize = $pageSize;
        } else {
            throw new TencentMapException('每页条目数只能在1-20之间', ErrorCode::MAP_TENCENT_PARAM_ERROR);
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
     * @throws \Exception\Map\TencentMapException
     */
    public function setPageIndex(int $pageIndex) {
        if($pageIndex > 0){
            $this->pageIndex = $pageIndex;
        } else {
            throw new TencentMapException('页数必须大于0', ErrorCode::MAP_TENCENT_PARAM_ERROR);
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
     * @throws \Exception\Map\TencentMapException
     */
    public function setFilter(string $filter) {
        $trueFilter = trim($filter);
        if(strlen($trueFilter) > 0){
            $this->filter = $filter;
        } else {
            throw new TencentMapException('筛选条件不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getOrderBy() : string {
        return $this->orderBy;
    }

    /**
     * 设置排序
     * @param string $field 排序字段
     * @param bool $isAsc 是否为升序,true:升序 false:降序
     * @throws \Exception\Map\TencentMapException
     */
    public function setOrderBy(string $field,bool $isAsc=true) {
        $trueField = trim($field);
        if(strlen($trueField) > 0){
            $this->orderBy = $isAsc ? $trueField . ' asc' : $trueField . 'desc';
        } else {
            throw new TencentMapException('排序字段不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAreaRegionCityName() : string {
        return $this->areaRegionCityName;
    }

    /**
     * @param string $areaRegionCityName
     * @throws \Exception\Map\TencentMapException
     */
    public function setAreaRegionCityName(string $areaRegionCityName) {
        $cityName = trim($areaRegionCityName);
        if(strlen($cityName) > 0){
            $this->areaRegionCityName = $cityName;
        } else {
            throw new TencentMapException('区域搜索城市名称不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getAreaRegionAutoExtend() : int {
        return $this->areaRegionAutoExtend;
    }

    /**
     * @param int $areaRegionAutoExtend
     * @throws \Exception\Map\TencentMapException
     */
    public function setAreaRegionAutoExtend(int $areaRegionAutoExtend) {
        if(in_array($areaRegionAutoExtend, [self::REGION_AUTO_EXTENT_NO, self::REGION_AUTO_EXTENT_YES], true)){
            $this->areaRegionAutoExtend = $areaRegionAutoExtend;
        } else {
            throw new TencentMapException('区域搜索自动扩大标识不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @param string $lng 经度
     * @param string $lat 纬度
     * @throws \Exception\Map\TencentMapException
     */
    public function setAreaRegionLngAndLat(string $lng,string $lat) {
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng) == 0){
            throw new TencentMapException('区域搜索经度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat) == 0){
            throw new TencentMapException('区域搜索纬度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $this->areaRegionLng = $lng;
        $this->areaRegionLat = $lat;
    }

    /**
     * @return string
     */
    public function getAreaRegionLng() : string {
        return $this->areaRegionLng;
    }

    /**
     * @return string
     */
    public function getAreaRegionLat() : string {
        return $this->areaRegionLat;
    }

    /**
     * @param string $lng 经度
     * @param string $lat 纬度
     * @throws \Exception\Map\TencentMapException
     */
    public function getAreaNearbyLngAndLat(string $lng,string $lat) {
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng) == 0){
            throw new TencentMapException('圆形范围搜索中心点经度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat) == 0){
            throw new TencentMapException('圆形范围搜索中心点纬度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
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
     * @throws \Exception\Map\TencentMapException
     */
    public function setAreaNearbyRadius(int $areaNearbyRadius) {
        if($areaNearbyRadius > 0){
            $this->areaNearbyRadius = $areaNearbyRadius;
        } else {
            throw new TencentMapException('圆形范围搜索半径必须大于0', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @param string $lng1 西南角经度
     * @param string $lat1 西南角纬度
     * @param string $lng2 东北角经度
     * @param string $lat2 东北角纬度
     * @throws \Exception\Map\TencentMapException
     */
    public function setAreaRectangLngAndLat(string $lng1,string $lat1,string $lng2,string $lat2) {
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng1) == 0){
            throw new TencentMapException('矩形范围搜索西南角经度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat1) == 0){
            throw new TencentMapException('矩形范围搜索西南角纬度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
        if(preg_match('/^[-]?(\d(\.\d+)?|[1-9]\d(\.\d+)?|1[0-7]\d(\.\d+)?|180)$/', $lng2) == 0){
            throw new TencentMapException('矩形范围搜索东北角经度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
        if(preg_match('/^[\-]?(\d(\.\d+)?|[1-8]\d(\.\d+)?|90)$/', $lat2) == 0){
            throw new TencentMapException('矩形范围搜索东北角纬度不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
        if((double)$lat1 >= (double)$lat2){
            throw new TencentMapException('矩形范围搜索东北角纬度必须大于西南角纬度', ErrorCode::MAP_TENCENT_PARAM_ERROR);
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
     * @return string
     * @throws \Exception\Map\TencentMapException
     */
    public function getAreaSearchContent(string $searchType) : string {
        if($searchType == self::PLACE_SEARCH_TYPE_REGION){
            if(strlen($this->areaRegionCityName) == 0){
                throw new TencentMapException('区域名称不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
            }

            if((strlen($this->areaRegionLng) > 0) && (strlen($this->areaRegionLat) > 0)){
                $content = 'region(' . $this->areaRegionCityName . ',' . $this->areaRegionAutoExtend . ',' . $this->areaRegionLat . ','
                           . $this->areaRegionLng . ')';
            } else {
                $content = 'region(' . $this->areaRegionCityName . ',' . $this->areaRegionAutoExtend . ')';
            }
        } else if($searchType == self::PLACE_SEARCH_TYPE_NEARBY){
            if((strlen($this->areaNearbyLat) == 0) || (strlen($this->areaNearbyLng) == 0)){
                throw new TencentMapException('中心点经度和纬度都不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
            }
            if($this->areaNearbyRadius <= 0){
                throw new TencentMapException('搜索半径必须大于0', ErrorCode::MAP_TENCENT_PARAM_ERROR);
            }

            $content = 'nearby(' . $this->areaNearbyLat . ',' . $this->areaNearbyLng . ',' . $this->areaNearbyRadius . ')';
        } else if($searchType == self::PLACE_SEARCH_TYPE_RECTANGLE){
            if(strlen($this->areaRectangleLng1) == 0){
                throw new TencentMapException('矩形范围搜索经度和纬度都不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
            }

            $content = 'rectangle(' . $this->areaRectangleLat1 . ',' . $this->areaRectangleLng1 . ',' . $this->areaRectangleLat2 . ','
                       . $this->areaRectangleLng2;
        } else {
            throw new TencentMapException('区域搜索类型不支持', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        return $content;
    }
}