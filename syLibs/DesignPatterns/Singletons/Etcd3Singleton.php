<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/9/5 0005
 * Time: 14:17
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\Etcd\EtcdException;
use Log\Log;
use Tool\Tool;
use Traits\SingletonTrait;

class Etcd3Singleton {
    use SingletonTrait;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * 支持的请求方式列表
     * @var array
     */
    private $methods = [];
    /**
     * etcd基础域名
     * @var string
     */
    private $baseDomain = '';
    /**
     * 基础前缀
     * @var string
     */
    private $prefixBase = '';
    /**
     * 模块前缀
     * @var string
     */
    private $prefixModules = '';

    private function __construct() {
        Log::setPath(\Yaconf::get('syserver.base.server.log_basepath') . 'sy/');
        $this->init();
    }

    /**
     * @return \DesignPatterns\Singletons\Etcd3Singleton
     */
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function init() {
        $this->methods = [
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_PUT,
            self::METHOD_DELETE,
        ];

        $configs = \Yaconf::get('etcd.' . SY_ENV);
        //检测etcd是否正常启动
        $healthRes = $this->sendReq($configs['domain'] . '/health', []);
        $healthData = Tool::jsonDecode($healthRes);
        if (!is_array($healthData)) {
            Log::error('解析etcd健康检查数据出错,错误信息为：' . $healthRes);
            throw new EtcdException('解析健康检查数据出错', ErrorCode::ETCD_GET_DATA_ERROR);
        }
        $healthStatus = isset($healthData['health']) && ($healthData['health'] == 'true') ? true : false;
        if (!$healthStatus) {
            throw new EtcdException('etcd服务端未正常启动', ErrorCode::ETCD_GET_DATA_ERROR);
        }
        $this->baseDomain = $configs['domain'];
        $this->prefixBase = $configs['prefix']['base'];
        $this->prefixModules = $configs['prefix']['modules'];
    }

    /**
     * @return string
     */
    public function getBaseDomain() : string {
        return $this->baseDomain;
    }

    /**
     * @return string
     */
    public function getPrefixBase() : string {
        return $this->prefixBase;
    }

    /**
     * @return string
     */
    public function getPrefixModules() : string {
        return $this->prefixModules;
    }

    /**
     * @param resource $ch
     * @param array $extends
     * @throws \Exception\Etcd\EtcdException
     */
    private function setCurlOpts(&$ch, array $extends=[]) {
        $timeout = (int)Tool::getArrayVal($extends, 'timeout', 2000);
        if ($timeout <= 0) {
            throw new EtcdException('超时时间必须大于0', ErrorCode::ETCD_PARAM_ERROR);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = Tool::getArrayVal($extends, 'headers', false);
        if (is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
    }

    /**
     * 发送请求
     * @param string $url 请求地址
     * @param array $data 请求参数数组
     * @param array $extends 扩展参数数组
     * @return string
     * @throws \Exception\Etcd\EtcdException
     * @throws \Exception
     */
    private function sendReq(string $url,array $data,array $extends=[]) {
        $method = strtoupper(Tool::getArrayVal($extends, 'method', self::METHOD_GET));
        if (!in_array($method, $this->methods)) {
            throw new EtcdException('请求方式不支持', ErrorCode::ETCD_PARAM_ERROR);
        }

        $res = '';
        $ch = curl_init();
        $this->setCurlOpts($ch, $extends);

        try {
            switch ($method) {
                case self::METHOD_GET:
                    $trueUrl = empty($data) ? $url : $url . '?' . http_build_query($data);
                    curl_setopt($ch, CURLOPT_URL, $trueUrl);
                    break;
                case self::METHOD_POST:
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, Tool::jsonEncode($data));
                    break;
                case self::METHOD_PUT:
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_PUT);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, Tool::jsonEncode($data));
                    break;
                case self::METHOD_DELETE:
                    $trueUrl = empty($data) ? $url : $url . '?' . http_build_query($data);
                    curl_setopt($ch, CURLOPT_URL, $trueUrl);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::METHOD_DELETE);
                    break;
                default:
                    break;
            }

            $res = curl_exec($ch);
            $errorNo = curl_errno($ch);
            if ($errorNo != 0) {
                throw new EtcdException('curl出错，错误码=' . $errorNo, ErrorCode::ETCD_SEND_REQ_ERROR);
            }
        } catch (\Exception $e) {
            $res = false;
            throw $e;
        } finally {
            curl_close($ch);
            if ($res !== false) {
                return $res;
            }
        }
    }

    /**
     * 格式化键名
     * @param string $key 键名
     * @return mixed
     * @throws \Exception\Etcd\EtcdException
     */
    private function formatKey(string $key) {
        $trueKey = preg_replace('/[^0-9a-zA-Z\-\_\/]+/', '', (string)$key);
        if (strlen($trueKey) == 0) {
            throw new EtcdException('键名不能为空', ErrorCode::ETCD_PARAM_ERROR);
        }

        return $trueKey;
    }

    /**
     * 字符串递增
     * @param string $key
     * @return string
     * @throws \Exception\Etcd\EtcdException
     */
    private function incrementString(string $key) {
        $length = strlen($key);
        if($length == 0){
            throw new EtcdException('键名不能为空', ErrorCode::ETCD_PARAM_ERROR);
        } else if($length == 1){
            return chr(ord($key) + 1);
        } else {
            $needStr = substr($key, -1);
            return substr($key, 0, ($length - 1)) . chr(ord($needStr) + 1);
        }
    }

    /**
     * 获取单个键值
     * @param string $key 键名
     * @return bool|string
     */
    public function get(string $key) {
        $trueKey = $this->formatKey($key);

        $getRes = $this->sendReq($this->baseDomain . '/v3alpha/kv/range', [
            'key' => base64_encode($trueKey),
        ], [
            'method' => self::METHOD_POST,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if (!is_array($getData)) {
            Log::error('解析数据失败,错误信息为：' . $getRes);
            return false;
        } else if (isset($getData['error'])) {
            Log::error('获取数据失败,错误信息为：' . $getRes);
            return false;
        }

        return isset($getData['kvs']) && !empty($getData['kvs']) ? base64_decode($getData['kvs'][0]['value']) : false;
    }

    /**
     * 获取多个键值
     * @param string $key 前缀键名
     * @param array $extends 扩展数组
     * @return array|bool
     */
    public function getList(string $key,array $extends=[]) {
        $trueKey = $this->formatKey($key);
        unset($extends['range_end'], $extends['key']);
        $prefix = $this->incrementString($trueKey);

        $data = [
            'key' => base64_encode($trueKey),
            'range_end' => base64_encode($prefix),
        ];
        foreach ($extends as $eKey => $eVal) {
            $data[$eKey] = $eVal;
        }

        $getRes = $this->sendReq($this->baseDomain . '/v3alpha/kv/range', $data, [
            'method' => self::METHOD_POST,
        ]);
        $getData = Tool::jsonDecode($getRes);
        if (!is_array($getData)) {
            Log::error('解析数据失败,错误信息为：' . $getRes);
            return false;
        } else if (isset($getData['error'])) {
            Log::error('获取数据失败,错误信息为：' . $getRes);
            return false;
        }

        $resArr = [
            'data' => [],
            'count' => $getData['count'] ?? 0,
            'more' => $getData['more'] ?? false,
        ];
        if (isset($getData['kvs'])) {
            foreach ($getData['kvs'] as $eData) {
                $resArr['data'][] = [
                    'key' => base64_decode($eData['key']),
                    'value' => isset($eData['value']) ? base64_decode($eData['value']) : false,
                ];
            }
        }

        return $resArr;
    }

    /**
     * 设置配置
     * @param string $key 键名
     * @param string|int $value 键值
     * @param int $ttl 超时时间,单位为秒
     * @return bool true:设置成功 false:设置失败
     */
    public function set(string $key, $value,int $ttl=0) {
        $trueKey = $this->formatKey($key);

        $data = [
            'key' => base64_encode($trueKey),
            'value' => base64_encode((string)$value),
        ];
        if($ttl > 0){
            $grantRes = $this->sendReq($this->baseDomain . '/v3alpha/lease/grant', [
                'TTL' => $ttl,
            ], [
                'method' => self::METHOD_POST,
            ]);
            $grantData = Tool::jsonDecode($grantRes);
            if (!is_array($grantData)) {
                Log::error('解析etcd数据出错,错误信息为：' . $grantRes);
                return false;
            } else if(!isset($grantData['ID'])){
                Log::error('申请租约失败,错误信息为：' . $grantRes);
                return false;
            }
            $data['lease'] = $grantData['ID'];
        }

        $setRes = $this->sendReq($this->baseDomain . '/v3alpha/kv/put', $data, [
            'method' => self::METHOD_POST,
        ]);
        $setData = Tool::jsonDecode($setRes);
        if (!is_array($setData)) {
            Log::error('解析数据出错,返回数据为' . $setRes);
            return false;
        } else if (isset($setData['error'])) {
            Log::error('设置数据出错,返回数据为' . $setRes);
            return false;
        } else {
            return true;
        }
    }

    /**
     * 删除数据
     * @param string $key 键名
     * @param bool $multi 前缀删除标识,true:删除以键名为前缀的所有数据 false:仅删除键名对应的值
     * @return bool|int
     */
    public function del(string $key,bool $multi=false) {
        $trueKey = $this->formatKey($key);

        $data = [
            'key' => base64_encode($trueKey),
        ];
        if($multi){
            $prefix = $this->incrementString($trueKey);
            $data['range_end'] = base64_encode($prefix);
        }

        $delRes = $this->sendReq($this->baseDomain . '/v3alpha/kv/deleterange', $data, [
            'method' => self::METHOD_POST,
        ]);
        $delData = Tool::jsonDecode($delRes);
        if (!is_array($delData)) {
            Log::error('解析数据出错,返回数据为' . $delRes);
            return false;
        } else if (isset($delData['error'])) {
            Log::error('删除数据出错,返回数据为' . $delRes);
            return false;
        } else {
            return isset($delData['deleted']) ? (int)$delData['deleted'] : 0;
        }
    }
}