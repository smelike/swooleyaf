<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/2 0002
 * Time: 11:18
 */
namespace Tool;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use Exception\Common\CheckException;
use Traits\SimpleTrait;
use Yaf\Registry;

class Tool {
    use SimpleTrait;

    private static $chars = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z',
    ];

    /**
     * 获取数组值
     * @param array $array 数组
     * @param string|int $key 键值
     * @param object $default 默认值
     * @param bool $isRecursion 是否递归查找,false:不递归 true:递归
     * @return mixed
     */
    public static function getArrayVal(array $array, $key, $default=null,bool $isRecursion=false){
        if(!$isRecursion){
            return $array[$key] ?? $default;
        }

        $index = strpos($key, '.');
        if($index === false){
            return $array[$key] ?? $default;
        }

        $keyFirst = substr($key, 0, $index);
        $keyLeft = substr($key, ($index + 1));
        if(isset($array[$keyFirst]) && is_array($array[$keyFirst])){
            $newData = $array[$keyFirst];
            unset($array);

            return self::getArrayVal($newData, $keyLeft, $default, $isRecursion);
        } else {
            return $default;
        }
    }

    /**
     * array转xml
     * @param array $dataArr
     * @return string
     * @throws CheckException
     */
    public static function arrayToXml(array $dataArr) : string {
        if (count($dataArr) == 0) {
            throw new CheckException('数组为空', ErrorCode::COMMON_PARAM_ERROR);
        }

        $xml = '<xml>';
        foreach ($dataArr as $key => $value) {
            if (is_numeric($value)) {
                $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';

        return $xml;
    }

    /**
     * xml转为array
     * @param string $xml
     * @return array
     * @throws CheckException
     */
    public static function xmlToArray(string $xml) {
        if (strlen($xml . '') == 0) {
            throw new CheckException('xml数据异常', ErrorCode::COMMON_PARAM_ERROR);
        }

        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $element = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonStr = Tool::jsonEncode($element);
        return Tool::jsonDecode($jsonStr);
    }

    /**
     * RSA签名
     * @param array $data 待签名数据
     * @param string $private_key_path 商户私钥文件路径
     * @return string 签名结果
     */
    public static function rsaSign(array $data,string $private_key_path) : string {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        //base64编码
        return base64_encode($sign);
    }

    /**
     * RSA验签
     * @param string $data 待签名数据
     * @param string $public_key_path 公钥文件路径
     * @param string $sign 要校对的的签名结果
     * @return boolean 验证结果
     */
    public static function rsaVerify(string $data,string $public_key_path,string $sign) : bool {
        $pubKey = file_get_contents($public_key_path);
        $res = openssl_get_publickey($pubKey);
        $result = (boolean)openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }

    /**
     * RSA解密
     * @param string $content 需要解密的内容，密文
     * @param string $private_key_path 私钥文件路径
     * @return string 解密后内容，明文
     */
    public static function rsaDecrypt(string $content,string $private_key_path) : string {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
        //用base64将内容还原成二进制
        $content2 = base64_decode($content);
        //把需要解密的内容，按128位拆开解密
        $result = '';
        $length = strlen($content2) / 128;
        for ($i = 0; $i < $length; $i++) {
            $data = substr($content2, $i * 128, 128);
            openssl_private_decrypt($data, $decrypt, $res);
            $result .= $decrypt;
        }
        openssl_free_key($res);
        return $result;
    }

    /**
     * md5签名字符串
     * @param string $needStr 需要签名的字符串
     * @param string $key 私钥
     * @return string 签名结果
     */
    public static function md5Sign(string $needStr,string $key) : string {
        return md5($needStr . $key);
    }

    /**
     * md5验证签名
     * @param string $needStr 需要签名的字符串
     * @param string $sign 签名结果
     * @param string $key 私钥
     * @return boolean 签名结果
     */
    public static function md5Verify(string $needStr,string $sign,string $key) : bool {
        $thisSign = md5($needStr . $key);
        return ($thisSign == $sign) ? true : false;
    }

    /**
     * 获取命令行输入
     * @param string|int $key 键名
     * @param bool $isIndexKey 键名是否为索引 true:是索引 false:不是索引
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getClientOption($key,bool $isIndexKey=false, $default=null) {
        global $argv;

        $option = $default;
        if($isIndexKey){
            if(isset($argv[$key])){
                $option = $argv[$key];
            }
        } else {
            foreach ($argv as $eKey => $eVal) {
                if(($key == $eVal) && isset($argv[$eKey+1])){
                    $option = $argv[$eKey+1];
                    break;
                }
            }
        }

        return $option;
    }

    /**
     * 压缩数据
     * @param mixed $data 需要压缩的数据
     * @return bool|string
     */
    public static function pack($data) {
        return msgpack_pack($data);
    }

    /**
     * 解压数据
     * @param string $data 压缩数据字符串
     * @param string $className 解压类型名称
     * @return mixed
     */
    public static function unpack(string $data,string $className='array') {
        if($className == 'array'){
            return msgpack_unpack($data);
        } else {
            return msgpack_unpack($data, $className);
        }
    }

    /**
     * 序列化数据
     * @param mixed $data
     * @return string
     */
    public static function serialize($data){
        return msgpack_serialize($data);
    }

    /**
     * 反序列化数据
     * @param string $str
     * @param string $className
     * @return mixed
     */
    public static function unserialize(string $str,string $className='array'){
        if($className == 'array'){
            return msgpack_unserialize($str);
        } else {
            return msgpack_unserialize($str, $className);
        }
    }

    /**
     * 把数组转移成json字符串
     * @param array|object $arr
     * @param int|string $options
     * @return bool|string
     */
    public static function jsonEncode($arr, $options=JSON_OBJECT_AS_ARRAY){
        if(is_array($arr) || is_object($arr)){
            return json_encode($arr, $options);
        }

        return false;
    }

    /**
     * 解析json
     * @param string $json
     * @param int|string $assoc
     * @return bool|mixed
     */
    public static function jsonDecode($json, $assoc=JSON_OBJECT_AS_ARRAY){
        if(is_string($json)){
            return json_decode($json, $assoc);
        }

        return false;
    }

    /**
     * 生成随机字符串
     * @param int $length 需要获取的随机字符串长度
     * @return string
     */
    public static function createNonceStr(int $length) : string {
        $resStr = '';
        for ($i = 0; $i < $length; $i++) {
            $resStr .= self::$chars[mt_rand(0, 61)];
        }

        return $resStr;
    }

    /**
     * 加密密码
     * @param string $pwd 密码明文
     * @param string $salt 加密盐
     * @return string
     * @throws CheckException
     */
    public static function encryptPassword(string $pwd,string $salt) : string {
        $length1 = strlen($pwd . '');
        $length2 = strlen($salt . '');
        if (($length1 > 0) && ($length2 > 0)) {
            return hash('sha256', $pwd . $salt);
        } else if ($length1 > 0) {
            throw new CheckException('加密盐不能为空', ErrorCode::COMMON_PARAM_ERROR);
        } else {
            throw new CheckException('密码不能为空', ErrorCode::COMMON_PARAM_ERROR);
        }

    }

    /**
     * 检测密码是否正确
     * @param string $pwd 密码明文
     * @param string $salt 加密盐
     * @param string $sign 当前密文
     * @return bool
     */
    public static function checkPassword(string $pwd,string $salt,string $sign){
        $nowSign = hash('sha256', $pwd . $salt);
        return $nowSign === $sign ? true : false;
    }

    /**
     * 生成唯一单号
     * @return string
     */
    public static function createUniqueSn() : string {
        $redis = CacheSimpleFactory::getRedisInstance();
        $needStr = date('YmdHis');
        $uniqueSn = $needStr . mt_rand(10000000, 99999999);
        $redisKey = Server::REDIS_PREFIX_ORDER_SN . $uniqueSn;
        while($redis->exists($redisKey)) {
            $uniqueSn = $needStr . mt_rand(10000000, 99999999);
            $redisKey = Server::REDIS_PREFIX_ORDER_SN . $uniqueSn;
        }
        $redis->set($redisKey, '1', 10);

        return $uniqueSn;
    }

    /**
     * 格式化字符串
     * @param string $inStr 输入的字符串
     * @param int $formatType 格式化的类型
     *     必然会做的处理:去除js代码,表情符号和首尾空格
     *     1：去除字符串中的特殊符号，并将多个空格缩减成一个英文空格
     *     2：将字符串中的连续多个空格缩减成一个英文空格
     *     3：去除前后空格
     * @return string
     */
    public static function filterStr(string $inStr,int $formatType=1) : string {
        if (strlen($inStr . '') > 0) {
            $patterns = [
                "'<script[^>]*?>.*?</script>'si",
                '/[\xf0-\xf7].{3}/',
            ];
            $replaces = [
                "",
                '',
            ];
            if ($formatType == 1) {
                $patterns[] = '/[\\\%\'\"\<\>\?\@\&\^\$\#\_]+/';
                $patterns[] = '/\s+/';
                $replaces[] = '';
                $replaces[] = ' ';
            } else if ($formatType == 2) {
                $patterns[] = '/\s+/';
                $replaces[] = ' ';
            }

            $saveStr = preg_replace($patterns, $replaces, $inStr);
            return trim($saveStr);
        }

        return '';
    }

    /**
     * 获取客户端IP
     * @param int $model 模式类型 1:从$_SERVER获取 2:从swoole_http_request中获取
     * @return bool|string
     */
    public static function getClientIP(int $model) {
        if($model == 1){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                return trim($ips[0]);
            } else if(isset($_SERVER['HTTP_X_REAL_IP'])){
                return trim($_SERVER['HTTP_X_REAL_IP']);
            } else {
                return self::getArrayVal($_SERVER, 'REMOTE_ADDR', '');
            }
        } else {
            $headers = Registry::get(Server::REGISTRY_NAME_REQUEST_HEADER);
            $servers = Registry::get(Server::REGISTRY_NAME_REQUEST_SERVER);
            if (($headers === false) || ($servers === false)) {
                return false;
            }

            if (isset($headers['x-forwarded-for'])) {
                $ips = explode(',', $headers['x-forwarded-for']);
                return trim($ips[0]);
            }
            if (isset($headers['x-real-ip'])) {
                return trim($headers['x-real-ip']);
            }
            if (isset($headers['proxy_forwarded_for'])) {
                $ips = explode(',', $headers['proxy_forwarded_for']);
                return trim($ips[0]);
            }

            return trim(self::getArrayVal($servers, 'remote_addr', ''));
        }
    }

    /**
     * 解压zip文件
     * @param string $file 文件,包括路径和名称
     * @param string $dist 解压目录
     * @return bool
     * @throws \Exception
     */
    public static function extractZip(string $file,string $dist){
        $zip = null;

        try{
            if (!is_file($file)) {
                throw new CheckException('解压对象不是文件', ErrorCode::COMMON_PARAM_ERROR);
            } else if(!is_readable($file)){
                throw new CheckException('文件不可读', ErrorCode::COMMON_PARAM_ERROR);
            } else if(!is_dir($dist)){
                throw new CheckException('解压目录不存在', ErrorCode::COMMON_PARAM_ERROR);
            } else if(!is_writeable($dist)){
                throw new CheckException('解压目录不可写', ErrorCode::COMMON_PARAM_ERROR);
            }

            $zip = new \ZipArchive();
            if($zip->open($file) !== true){
                throw new CheckException('读取文件失败', ErrorCode::COMMON_PARAM_ERROR);
            }
            if(!$zip->extractTo($dist)){
                throw new CheckException('解压失败', ErrorCode::COMMON_PARAM_ERROR);
            }

            return true;
        } catch (\Exception $e){
            throw $e;
        } finally {
            if($zip){
                $zip->close();
            }
        }
    }

    /**
     * 发送框架http任务请求
     * @param string $url 请求地址
     * @param string $content 请求内容
     * @return bool|mixed
     */
    public static function sendSyHttpTaskReq(string $url,string $content) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Server::SERVER_DATA_KEY_TASK . '=' . urlencode($content));
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $errorNo = curl_errno($ch);
        curl_close($ch);

        return $errorNo == 0 ? $res : false;
    }

    /**
     * 发送框架RPC请求
     * @param string $host 请求域名
     * @param int $port 请求端口
     * @param string $content 请求内容
     * @return bool
     */
    public static function sendSyRpcReq(string $host,int $port,string $content) {
        $client = new \swoole_client(SWOOLE_TCP);
        $client->set([
            'open_tcp_nodelay' => true,
            'open_length_check' => true,
            'package_length_type' => 'L',
            'package_length_offset' => 4,
            'package_body_offset' => 0,
            'package_max_length' => Server::SERVER_PACKAGE_MAX_LENGTH,
            'socket_buffer_size' => Server::SERVER_PACKAGE_MAX_LENGTH,
        ]);
        if(!@$client->connect($host, $port, 2)){
            return false;
        }
        if(!$client->send($content)){
            $client->close();
            return false;
        }

        $res = @$client->recv();
        $client->close();

        return $res;
    }
}
