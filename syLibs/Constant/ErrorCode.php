<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/2 0002
 * Time: 11:22
 */
namespace Constant;

use Traits\SimpleTrait;

final class ErrorCode {
    use SimpleTrait;

    //公共错误,取值范围:10000-99999
    const COMMON_SUCCESS = 0;
    const COMMON_MIN_NUM = 10000;
    const COMMON_PARAM_ERROR = 10000;
    const COMMON_SERVER_ERROR = 10001;
    const COMMON_SERVER_EXCEPTION = 10002;
    const COMMON_SERVER_FATAL = 10003;
    const COMMON_SERVER_RESOURCE_NOT_EXIST = 10004;
    const COMMON_ROUTE_MODULE_NOT_ACCEPT = 10005;
    const COMMON_ROUTE_URI_FORMAT_ERROR = 10006;
    const COMMON_ROUTE_CONTROLLER_NOT_EXIST = 10007;
    const COMMON_ROUTE_ACTION_NOT_EXIST = 10008;

    //validator错误,取值范围:100000-100199
    const VALIDATOR_TYPE_ERROR = 100000;
    const VALIDATOR_RULE_EMPTY = 100001;
    const VALIDATOR_RULE_ERROR = 100002;

    //OSS错误,取值范围:100200-100399
    const OSS_CONNECT_ERROR = 100200;
    const OSS_UPLOAD_FILE_ERROR = 100201;
    const OSS_DELETE_FILE_ERROR = 100202;
    const OSS_GET_BUCKET_FILE_ERROR = 100203;
    const OSS_PARAM_ERROR = 100204;

    //MYSQL错误,取值范围:100400-100599
    const MYSQL_CONNECTION_ERROR = 100400;
    const MYSQL_INSERT_ERROR = 100401;
    const MYSQL_DELETE_ERROR = 100402;
    const MYSQL_UPDATE_ERROR = 100403;
    const MYSQL_SELECT_ERROR = 100404;
    const MYSQL_UPSERT_ERROR = 100405;

    //REDIS错误,取值范围:100600-100799
    const REDIS_CONNECTION_ERROR = 100600;
    const REDIS_AUTH_ERROR = 100601;

    //SWOOLE错误,取值范围:100800-100999
    const SWOOLE_SERVER_PARAM_ERROR = 100800;
    const SWOOLE_SERVER_NOT_EXIST_ERROR = 100801;

    //反射错误,取值范围:101000-101199
    const REFLECT_RESOURCE_NOT_EXIST = 101000;
    const REFLECT_ANNOTATION_DATA_ERROR = 101001;

    //邮件错误,取值范围:101200-101399
    const MAIL_SERVER_NOT_EXIST = 101200;
    const MAIL_SMTP_SEND_FAIL = 101201;
    const MAIL_PARAM_ERROR = 101202;

    //微信错误,取值范围:101400-101599
    const WX_PARAM_ERROR = 101400;
    const WX_POST_ERROR = 101401;
    const WX_GET_ERROR = 101402;

    //微信开放平台错误,取值范围:101600-101799
    const WXOPEN_PARAM_ERROR = 101600;
    const WXOPEN_POST_ERROR = 101601;
    const WXOPEN_GET_ERROR = 101602;

    //支付宝支付错误,取值范围:101800-101999
    const ALIPAY_PARAM_ERROR = 101800;
    const ALIPAY_POST_ERROR = 101801;
    const ALIPAY_GET_ERROR = 101802;

    //阿里大于错误,取值范围:102000-102199
    const ALIDAYU_PARAM_ERROR = 102000;
    const ALIDAYU_POST_ERROR = 102001;
    const ALIDAYU_GET_ERROR = 102002;

    //图片错误,取值范围:102200-102399
    const IMAGE_UPLOAD_PARAM_ERROR = 102200;
    const IMAGE_UPLOAD_FAIL = 102201;

    //用户错误,取值范围:102400-102999
    const USER_NOT_LOGIN = 102400;
    const USER_NOT_LOGIN_WX_AUTH = 102401;

    //签名错误,取值范围:103000-103099
    const SIGN_ERROR = 103000;
    const SIGN_TIME_ERROR = 103001;
    const SIGN_NONCE_ERROR = 103002;

    //MONGO错误,取值范围:103100-103199
    const MONGO_CONNECTION_ERROR = 103100;
    const MONGO_PARAM_ERROR = 103101;
    const MONGO_CREATE_ERROR = 103102;
    const MONGO_INSERT_ERROR = 103103;
    const MONGO_DELETE_ERROR = 103104;
    const MONGO_UPDATE_ERROR = 103105;
    const MONGO_SELECT_ERROR = 103106;

    //KAFKA错误,取值范围:103200-103299
    const KAFKA_PRODUCER_ERROR = 103200;
    const KAFKA_CONSUMER_ERROR = 103201;

    //Solr错误,取值范围:103300-103399
    const SOLR_PARAM_ERROR = 103300;
    const SOLR_POST_ERROR = 103301;
    const SOLR_GET_ERROR = 103302;
    const SOLR_ANALYSIS_ERROR = 103303;
    const SOLR_ADD_ERROR = 103304;
    const SOLR_DELETE_ERROR = 103305;
    const SOLR_UPDATE_ERROR = 103306;
    const SOLR_SELECT_ERROR = 103307;

    //Solr错误,取值范围:103400-103599
    const MAP_TENCENT_PARAM_ERROR = 103400;
    const MAP_TENCENT_GET_ERROR = 103401;
    const MAP_TENCENT_POST_ERROR = 103402;
    const MAP_BAIDU_PARAM_ERROR = 103450;
    const MAP_BAIDU_GET_ERROR = 103451;
    const MAP_BAIDU_POST_ERROR = 103452;

    //Twig错误,取值范围:103600-103699
    const TWIG_PARAM_ERROR = 103600;

    //定时器错误,取值范围:103700-103799
    const TIMER_PARAM_ERROR = 103700;
    const TIMER_GET_ERROR = 103701;

    //Cron错误,取值范围:103800-103899
    const CRON_FORMAT_ERROR = 103800;
    const CRON_SECOND_ERROR = 103801;
    const CRON_MINUTE_ERROR = 103802;
    const CRON_HOUR_ERROR = 103803;
    const CRON_DAY_ERROR = 103804;
    const CRON_MONTH_ERROR = 103805;
    const CRON_WEEK_ERROR = 103806;

    //消息队列错误,取值范围:103900-103999
    const MESSAGE_QUEUE_TOPIC_ERROR = 103900;
    const MESSAGE_QUEUE_TOPIC_DATA_ERROR = 103900;

    //etcd配置错误,取值范围:104000-104099
    const ETCD_PARAM_ERROR = 104000;
    const ETCD_SEND_REQ_ERROR = 104001;
    const ETCD_GET_DATA_ERROR = 104002;

    //Smarty错误,取值范围:104100-104199
    const SMARTY_PARAM_ERROR = 104100;

    private static $msgArr = [
        self::COMMON_SUCCESS => '成功',
        self::COMMON_PARAM_ERROR => '参数错误',
        self::COMMON_SERVER_ERROR => '服务出错',
        self::COMMON_SERVER_EXCEPTION => '服务出错',
        self::COMMON_SERVER_FATAL => '服务出错',
        self::COMMON_SERVER_RESOURCE_NOT_EXIST => '资源不存在',
        self::COMMON_ROUTE_MODULE_NOT_ACCEPT => '模块不支持',
        self::COMMON_ROUTE_URI_FORMAT_ERROR => '路由格式错误',
        self::COMMON_ROUTE_CONTROLLER_NOT_EXIST => '控制器不存在',
        self::COMMON_ROUTE_ACTION_NOT_EXIST => '方法不存在',
        self::VALIDATOR_TYPE_ERROR => '校验器不支持',
        self::VALIDATOR_RULE_EMPTY => '校验规则为空',
        self::VALIDATOR_RULE_ERROR => '校验规则格式不合法',
        self::OSS_CONNECT_ERROR => 'OSS连接失败',
        self::OSS_UPLOAD_FILE_ERROR => 'OSS上传文件失败',
        self::OSS_DELETE_FILE_ERROR => 'OSS删除文件失败',
        self::OSS_GET_BUCKET_FILE_ERROR => 'OSS获取bucket文件失败',
        self::OSS_PARAM_ERROR => 'OSS参数出错',
        self::MYSQL_CONNECTION_ERROR => 'MYSQL连接出错',
        self::MYSQL_INSERT_ERROR => 'MYSQL添加数据出错',
        self::MYSQL_UPDATE_ERROR => 'MYSQL修改数据出错',
        self::MYSQL_DELETE_ERROR => 'MYSQL删除数据出错',
        self::MYSQL_SELECT_ERROR => 'MYSQL查询数据出错',
        self::MYSQL_UPSERT_ERROR => 'MYSQL修改或添加数据出错',
        self::REDIS_CONNECTION_ERROR => 'REDIS连接出错',
        self::REDIS_AUTH_ERROR => 'REDIS鉴权失败',
        self::SWOOLE_SERVER_PARAM_ERROR => 'SWOOLE服务参数错误',
        self::SWOOLE_SERVER_NOT_EXIST_ERROR => 'SWOOLE服务不存在',
        self::REFLECT_RESOURCE_NOT_EXIST => '反射资源不存在',
        self::REFLECT_ANNOTATION_DATA_ERROR => '注解数据不正确',
        self::MAIL_SERVER_NOT_EXIST => '邮件服务器不存在',
        self::MAIL_SMTP_SEND_FAIL => '发送SMTP邮件失败',
        self::MAIL_PARAM_ERROR => '邮件参数错误',
        self::WX_PARAM_ERROR => '微信参数错误',
        self::WX_POST_ERROR => '微信发送POST请求出错',
        self::WX_GET_ERROR => '微信发送GET请求出错',
        self::WXOPEN_PARAM_ERROR => '微信开放平台参数错误',
        self::WXOPEN_POST_ERROR => '微信开放平台发送POST请求出错',
        self::WXOPEN_GET_ERROR => '微信开放平台发送GET请求出错',
        self::ALIPAY_PARAM_ERROR => '支付宝支付参数错误',
        self::ALIPAY_POST_ERROR => '支付宝支付发送POST请求出错',
        self::ALIPAY_GET_ERROR => '支付宝支付发送GET请求出错',
        self::ALIDAYU_PARAM_ERROR => '阿里大于参数错误',
        self::ALIDAYU_POST_ERROR => '阿里大于发送POST请求出错',
        self::ALIDAYU_GET_ERROR => '阿里大于发送GET请求出错',
        self::IMAGE_UPLOAD_PARAM_ERROR => '图片上传参数错误',
        self::IMAGE_UPLOAD_FAIL => '图片上传失败',
        self::USER_NOT_LOGIN => '用户未登录',
        self::USER_NOT_LOGIN_WX_AUTH => '用户未微信授权登录',
        self::SIGN_ERROR => '签名值错误',
        self::SIGN_TIME_ERROR => '签名时间错误',
        self::SIGN_NONCE_ERROR => '签名随机字符串错误',
        self::MONGO_CONNECTION_ERROR => 'Mongo连接异常',
        self::MONGO_PARAM_ERROR => 'Mongo参数错误',
        self::MONGO_CREATE_ERROR => 'Mongo创建数据库出错',
        self::MONGO_INSERT_ERROR => 'Mongo添加数据出错',
        self::MONGO_DELETE_ERROR => 'Mongo删除数据出错',
        self::MONGO_UPDATE_ERROR => 'Mongo修改数据出错',
        self::MONGO_SELECT_ERROR => 'Mongo查询数据出错',
        self::KAFKA_PRODUCER_ERROR => 'KAFKA生产者出错',
        self::KAFKA_CONSUMER_ERROR => 'KAFKA消费者出错',
        self::SOLR_PARAM_ERROR => 'Solr参数错误',
        self::SOLR_POST_ERROR => 'Solr发送POST请求出错',
        self::SOLR_GET_ERROR => 'Solr发送GET请求出错',
        self::SOLR_ANALYSIS_ERROR => 'Solr分词错误',
        self::SOLR_ADD_ERROR => 'Solr新增出错',
        self::SOLR_DELETE_ERROR => 'Solr删除出错',
        self::SOLR_UPDATE_ERROR => 'Solr修改出错',
        self::SOLR_SELECT_ERROR => 'Solr查询出错',
        self::MAP_TENCENT_PARAM_ERROR => '百度地图参数错误',
        self::MAP_TENCENT_GET_ERROR => '百度地图发送GET请求出错',
        self::MAP_TENCENT_POST_ERROR => '百度地图发送POST请求出错',
        self::MAP_BAIDU_PARAM_ERROR => '腾讯地图参数错误',
        self::MAP_BAIDU_GET_ERROR => '腾讯地图发送GET请求出错',
        self::MAP_BAIDU_POST_ERROR => '腾讯地图发送POST请求出错',
        self::TWIG_PARAM_ERROR => 'Twig参数错误',
        self::TIMER_PARAM_ERROR => '定时器参数错误',
        self::TIMER_GET_ERROR => '定时器发送GET请求出错',
        self::CRON_FORMAT_ERROR => 'cron格式错误',
        self::CRON_SECOND_ERROR => 'cron秒钟格式错误',
        self::CRON_MINUTE_ERROR => 'cron分钟格式错误',
        self::CRON_HOUR_ERROR => 'cron小时格式错误',
        self::CRON_DAY_ERROR => 'cron日期格式错误',
        self::CRON_MONTH_ERROR => 'cron月份格式错误',
        self::CRON_WEEK_ERROR => 'cron星期格式错误',
        self::MESSAGE_QUEUE_TOPIC_ERROR => '消息队列主题错误',
        self::MESSAGE_QUEUE_TOPIC_DATA_ERROR => '消息队列主题数据错误',
        self::ETCD_PARAM_ERROR => 'ETCD参数错误',
        self::ETCD_SEND_REQ_ERROR => 'ETCD发送请求出错',
        self::ETCD_GET_DATA_ERROR => 'ETCD获取数据出错',
        self::SMARTY_PARAM_ERROR => 'Smarty参数错误',
    ];

    /**
     * 获取错误信息
     * @param int $errorCode 错误码
     * @return mixed|string
     */
    public static function getMsg(int $errorCode){
        return self::$msgArr[$errorCode] ?? '';
    }
}