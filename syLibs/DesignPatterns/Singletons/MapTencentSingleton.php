<?php
/**
 * 腾讯地图单例类
 * User: 姜伟
 * Date: 2017/6/19 0019
 * Time: 11:13
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;
use Log\Log;
use Map\Tencent\CoordinateTranslate;
use Map\Tencent\IpLocation;
use Map\Tencent\PlaceSearch;
use Tool\Tool;
use Traits\SingletonTrait;

class MapTencentSingleton {
    use SingletonTrait;

    private $urlPlaceSearch = 'http://apis.map.qq.com/ws/place/v1/search';
    private $urlCoordinateTranslate = 'http://apis.map.qq.com/ws/coord/v1/translate';
    private $urlIpLocation = 'http://apis.map.qq.com/ws/location/v1/ip';

    /**
     * 开发密钥
     * @var string
     */
    private $key = '';

    private function __construct() {
        $this->init();
    }

    private function init() {
        $configs = \Yaconf::get('map.' . SY_ENV);

        $this->setKey((string)Tool::getArrayVal($configs, 'tencent.key', '', true));
    }

    /**
     * @return \DesignPatterns\Singletons\MapTencentSingleton
     */
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getKey() : string {
        return $this->key;
    }

    /**
     * @param string $key
     * @throws TencentMapException
     */
    public function setKey(string $key) {
        if(preg_match('/^[0-9A-Z]{5}(\-[0-9A-Z]{5}){5}$/', $key) > 0){
            $this->key = $key;
        } else {
            throw new TencentMapException('密钥不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * 发送POST请求
     * @param string $url 请求地址
     * @param array $data 数据
     * @param array $configs 配置数组
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    private function sendPost(string $url,array $data,array $configs=[]) {
        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 1000);
        $referer = Tool::getArrayVal($configs, 'referer', '');
        $headers = Tool::getArrayVal($configs, 'headers', false);
        if($headers){
            $nowHeaders = $headers;
            $nowHeaders[] = 'Expect:';
        } else {
            $nowHeaders = [
                'Expect:',
            ];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $nowHeaders);
        if(strlen($referer) > 0){
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        $execRes = curl_exec($ch);
        $errorNo = curl_errno($ch);
        $errorMsg = curl_error($ch);
        curl_close($ch);

        if($errorNo == 0){
            $resData = Tool::jsonDecode($execRes);
            if(is_array($resData)){
                return $resData;
            } else {
                Log::error('解析POST响应失败,响应数据=' . $execRes, ErrorCode::MAP_TENCENT_POST_ERROR);

                throw new TencentMapException('解析POST响应失败', ErrorCode::MAP_TENCENT_POST_ERROR);
            }
        } else {
            Log::error('curl发送腾讯地图post请求出错,错误码=' . $errorNo . ',错误信息=' . $errorMsg, ErrorCode::MAP_TENCENT_POST_ERROR);

            throw new TencentMapException('POST请求出错', ErrorCode::MAP_TENCENT_POST_ERROR);
        }
    }

    /**
     * 发送GET请求
     * @param string $url 请求地址
     * @param array $data 数据
     * @param array $configs 配置数组
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    private function sendGet(string $url,array $data,array $configs=[]) {
        $nowUrl = $url . '?' . http_build_query($data);
        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 1000);
        $referer = Tool::getArrayVal($configs, 'referer', '');
        $headers = Tool::getArrayVal($configs, 'headers', []);

        $ch = curl_init($nowUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if(strlen($referer) > 0){
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        $execRes = curl_exec($ch);
        $errorNo = curl_errno($ch);
        $errorMsg = curl_error($ch);
        curl_close($ch);

        if($errorNo == 0){
            $resData = Tool::jsonDecode($execRes);
            if(is_array($resData)){
                return $resData;
            } else {
                Log::error('解析GET响应失败,响应数据=' . $execRes, ErrorCode::MAP_TENCENT_GET_ERROR);

                throw new TencentMapException('解析GET响应失败', ErrorCode::MAP_TENCENT_GET_ERROR);
            }
        } else {
            Log::error('curl发送腾讯地图get请求出错,错误码=' . $errorNo . ',错误信息=' . $errorMsg, ErrorCode::MAP_TENCENT_GET_ERROR);

            throw new TencentMapException('GET请求出错', ErrorCode::MAP_TENCENT_GET_ERROR);
        }
    }

    /**
     * 搜索地区
     * @param \Map\Tencent\PlaceSearch $search 搜索类
     * @param string $searchType 搜索类型 region:地区搜索 nearby:圆形区域搜索 rectangle:矩形区域搜索
     * @param string $getType 获取类型
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function searchPlace(PlaceSearch $search,string $searchType,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($search->getKeyword()) == 0){
            throw new TencentMapException('搜索关键字不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $search->getContentByType($getType, $configs);

        $data = [
            'keyword' => $search->getKeyword(),
            'boundary' => $search->getAreaSearchContent($searchType),
            'page_size' => $search->getPageSize(),
            'page_index' => $search->getPageIndex(),
            'output' => $search->getOutput(),
            'key' => $this->key,
        ];
        if(strlen($search->getFilter()) > 0){
            $data['filter'] = $search->getFilter();
        }
        if(strlen($search->getOrderBy()) > 0){
            $data['orderby'] = $search->getOrderBy();
        }

        $getRes = $this->sendGet($this->urlPlaceSearch, $data, $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['data'];
            $resArr['total_num'] = $getRes['count'];
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 坐标转换
     * @param \Map\Tencent\CoordinateTranslate $coord
     * @param string $getType
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function translateCoord(CoordinateTranslate $coord,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if (empty($coord->getCoords())) {
            throw new TencentMapException('源坐标不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $coord->getContentByType($getType, $configs);

        $data = [
            'locations' => implode(';', $coord->getCoords()),
            'type' => $coord->getFromType(),
            'key' => $this->key,
            'output' => $coord->getOutput(),
        ];

        $getRes = $this->sendGet($this->urlCoordinateTranslate, $data, $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['locations'];
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * IP定位
     * @param \Map\Tencent\IpLocation $ipLocation
     * @param string $getType
     * @return array
     * @throws \Exception\Map\TencentMapException
     */
    public function getLocationByIp(IpLocation $ipLocation,string $getType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($ipLocation->getIp()) == 0){
            throw new TencentMapException('ip不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        $configs = [];
        $ipLocation->getContentByType($getType, $configs);

        $data = [
            'ip' => $ipLocation->getIp(),
            'key' => $this->key,
            'output' => $ipLocation->getOutput(),
        ];

        $getRes = $this->sendGet($this->urlIpLocation, $data, $configs);
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['result'];
        } else {
            $resArr['code'] = ErrorCode::MAP_TENCENT_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }
}