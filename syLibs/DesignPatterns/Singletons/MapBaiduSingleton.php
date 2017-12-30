<?php
/**
 * 百度地图单例类
 * User: 姜伟
 * Date: 2017/6/19 0019
 * Time: 11:50
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Log\Log;
use Map\BaiDu\CoordinateTranslate;
use Map\BaiDu\IpLocation;
use Map\BaiDu\PlaceDetail;
use Map\BaiDu\PlaceSearch;
use Map\BaiDu\ReqCheck;
use Tool\Tool;
use Traits\SingletonTrait;

class MapBaiduSingleton {
    use SingletonTrait;

    private $urlPlaceSearch = 'http://api.map.baidu.com/place/v2/search';
    private $urlPlaceDetail = 'http://api.map.baidu.com/place/v2/detail';
    private $urlCoordinateTranslate = 'http://api.map.baidu.com/geoconv/v1/';
    private $urlIpLocation = 'http://api.map.baidu.com/location/ip';

    /**
     * 开发密钥
     * @var string
     */
    private $ak = '';

    private function __construct() {
        $this->init();
    }

    private function init() {
        $configs = \Yaconf::get('map.' . SY_ENV);

        $this->setAk((string)Tool::getArrayVal($configs, 'baidu.ak', '', true));
    }

    /**
     * @return \DesignPatterns\Singletons\MapBaiduSingleton
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
    public function getAk() : string {
        return $this->ak;
    }

    /**
     * @param string $ak
     * @throws BaiduMapException
     */
    public function setAk(string $ak) {
        if(preg_match('/^[0-9a-zA-Z]{32}$/', $ak) > 0){
            $this->ak = $ak;
        } else {
            throw new BaiduMapException('密钥不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * 发送POST请求
     * @param string $url 请求地址
     * @param array $data 数据
     * @param array $configs 配置数组
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    private function sendPost(string $url,array $data,array $configs=[]) {
        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 1000);
        $referer = Tool::getArrayVal($configs, 'referer', '');
        $userAgent = Tool::getArrayVal($configs, 'user_agent', '');
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
        if(strlen($userAgent) > 0){
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
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
                echo $execRes;
                Log::error('解析POST响应失败,响应数据=' . $execRes, ErrorCode::MAP_BAIDU_POST_ERROR);

                throw new BaiduMapException('解析POST响应失败', ErrorCode::MAP_BAIDU_POST_ERROR);
            }
        } else {
            Log::error('curl发送百度地图post请求出错,错误码=' . $errorNo . ',错误信息=' . $errorMsg, ErrorCode::MAP_BAIDU_POST_ERROR);

            throw new BaiduMapException('POST请求出错', ErrorCode::MAP_BAIDU_POST_ERROR);
        }
    }

    /**
     * 发送GET请求
     * @param string $url 请求地址
     * @param array $data 数据
     * @param array $configs 配置数组
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    private function sendGet(string $url,array $data,array $configs=[]) {
        $nowUrl = $url . '?' . http_build_query($data);
        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 1000);
        $referer = Tool::getArrayVal($configs, 'referer', '');
        $userAgent = Tool::getArrayVal($configs, 'user_agent', '');
        $headers = Tool::getArrayVal($configs, 'headers', []);

        $ch = curl_init($nowUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if(strlen($referer) > 0){
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        if(strlen($userAgent) > 0){
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
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
                Log::error('解析GET响应失败,响应数据=' . $execRes, ErrorCode::MAP_BAIDU_GET_ERROR);

                throw new BaiduMapException('解析GET响应失败', ErrorCode::MAP_BAIDU_GET_ERROR);
            }
        } else {
            Log::error('curl发送百度地图get请求出错,错误码=' . $errorNo . ',错误信息=' . $errorMsg, ErrorCode::MAP_BAIDU_GET_ERROR);

            throw new BaiduMapException('GET请求出错', ErrorCode::MAP_BAIDU_GET_ERROR);
        }
    }

    /**
     * 搜索地区
     * @param \Map\BaiDu\PlaceSearch $search 搜索类
     * @param string $searchType 搜索类型 region:地区搜索 nearby:圆形区域搜索 rectangle:矩形区域搜索
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function searchPlace(PlaceSearch $search,string $searchType) : array {
        $resArr = [
            'code' => 0,
        ];

        if(empty($search->getKeywords())){
            throw new BaiduMapException('检索关键字不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $data = [
            'query' => implode('', $search->getKeywords()),
            'output' => $search->getOutput(),
            'scope' => $search->getScope(),
            'coord_type' => $search->getCoordinateType(),
            'page_size' => $search->getPageSize(),
            'page_num' => $search->getPageIndex() - 1,
            'ak' => $this->ak,
            'timestamp' => time(),
        ];
        if(!empty($search->getTags())){
            $data['tag'] = implode(',', $search->getTags());
        }
        if(strlen($search->getFilter()) > 0){
            $data['filter'] = $search->getFilter();
        }
        $trueData = array_merge($data, $search->getAreaSearchContent($searchType));

        $reqCheck = new ReqCheck();
        $reqCheck->setObj($search);
        $reqCheck->setReqData($trueData);
        $reqCheck->setReqUrl($this->urlPlaceSearch);
        $reqCheck->checkReq();

        $getRes = $this->sendGet($this->urlPlaceSearch, $reqCheck->getReqData(), $reqCheck->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['results'];
            $resArr['total_num'] = $getRes['total'];
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 获取poi点的详细信息
     * @param \Map\BaiDu\PlaceDetail $detail
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function getPoiDetail(PlaceDetail $detail) : array {
        $resArr = [
            'code' => 0,
        ];

        if(empty($detail->getUids())){
            throw new BaiduMapException('uid不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $reqCheck = new ReqCheck();
        $reqCheck->setObj($detail);
        $reqCheck->setReqData([
            'uids' => implode(',', $detail->getUids()),
            'output' => $detail->getOutput(),
            'scope' => $detail->getScope(),
            'ak' => $this->ak,
            'timestamp' => time(),
        ]);
        $reqCheck->setReqUrl($this->urlPlaceDetail);
        $reqCheck->checkReq();

        $getRes = $this->sendGet($this->urlPlaceDetail, $reqCheck->getReqData(), $reqCheck->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['result'];
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * 坐标转换
     * @param \Map\BaiDu\CoordinateTranslate $coord
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function translateCoord(CoordinateTranslate $coord) : array {
        $resArr = [
            'code' => 0,
        ];

        if (empty($coord->getCoords())) {
            throw new BaiduMapException('源坐标不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $reqCheck = new ReqCheck();
        $reqCheck->setObj($coord);
        $reqCheck->setReqData([
            'coords' => implode(';', $coord->getCoords()),
            'ak' => $this->ak,
            'from' => $coord->getFromType(),
            'to' => $coord->getToType(),
            'output' => $coord->getOutput(),
        ]);
        $reqCheck->setReqUrl($this->urlCoordinateTranslate);
        $reqCheck->checkReq();

        $getRes = $this->sendGet($this->urlCoordinateTranslate, $reqCheck->getReqData(), $reqCheck->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes['result'];
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }

    /**
     * IP定位
     * @param \Map\BaiDu\IpLocation $ipLocation
     * @return array
     * @throws \Exception\Map\BaiduMapException
     */
    public function getLocationByIp(IpLocation $ipLocation) : array {
        $resArr = [
            'code' => 0,
        ];

        if(strlen($ipLocation->getIp()) == 0){
            throw new BaiduMapException('ip不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        $data = [
            'ip' => $ipLocation->getIp(),
            'ak' => $this->ak,
        ];
        if(strlen($ipLocation->getReturnCoordType()) > 0){
            $data['coor'] = $ipLocation->getReturnCoordType();
        }

        $reqCheck = new ReqCheck();
        $reqCheck->setObj($ipLocation);
        $reqCheck->setReqData($data);
        $reqCheck->setReqUrl($this->urlIpLocation);
        $reqCheck->checkReq();

        $getRes = $this->sendGet($this->urlIpLocation, $reqCheck->getReqData(), $reqCheck->getReqConfigs());
        if($getRes['status'] == 0){
            $resArr['data'] = $getRes;
            unset($resArr['data']['status']);
        } else {
            $resArr['code'] = ErrorCode::MAP_BAIDU_GET_ERROR;
            $resArr['message'] = $getRes['message'];
        }

        return $resArr;
    }
}