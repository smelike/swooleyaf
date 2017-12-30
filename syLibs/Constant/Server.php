<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/12 0012
 * Time: 9:30
 */
namespace Constant;

use Traits\SimpleTrait;

final class Server {
    use SimpleTrait;

    public static $totalModules = [
        self::MODULE_NAME_API,
        self::MODULE_NAME_ORDER,
        self::MODULE_NAME_USER,
        self::MODULE_NAME_SERVICE,
    ];

    //微信常量
    const WX_APP_SY = 'a001';

    //模块常量
    //考虑到yac缓存名称长度限制,名称不能超过30个字符串
    const MODULE_NAME_API = 'sy_api';
    const MODULE_NAME_ORDER = 'sy_order';
    const MODULE_NAME_USER = 'sy_user';
    const MODULE_NAME_SERVICE = 'sy_services';

    //服务常量
    const SERVER_PACKAGE_MAX_LENGTH = 12582912; //服务端消息最大长度-12M
    const SERVER_STATUS_CLOSE = '0'; //服务端状态-关闭
    const SERVER_STATUS_OPEN = '1'; //服务端状态-开放
    const SERVER_HTTP_TAG_RESPONSE_EOF = "\r\r\rswoole@yaf\r\r\r"; //服务端http标识-响应结束符
    const SERVER_HTTP_TAG_REQUEST_HEADER = 'swoole-yaf'; //服务端http标识-请求头名称
    const SERVER_DATA_KEY_TASK = '_sytask'; //服务端内部数据键名-task
    const SERVER_DATA_KEY_TOKEN = '_sytoken'; //服务端内部数据键名-token
    const SERVER_TIME_REQ_HANDLE_MAX = 120; //服务端时间-请求最大执行时间,单位为毫秒
    const SERVER_TIME_REQ_HEALTH_MIN = 4000; //服务端时间-请求健康检查最小时间,单位为毫秒

    //REDIS常量 以sy000开头的前缀为框架内部前缀,以sy+3位数字开头的前缀为公共模块前缀
    const REDIS_PREFIX_SESSION = 'sy000001_'; //前缀-session
    const REDIS_PREFIX_TIMER = 'sy001001_'; //前缀-定时器
    const REDIS_PREFIX_CODE_IMAGE = 'sy001002_'; //前缀-验证码图片
    const REDIS_PREFIX_ORDER_SN = 'sy001003_'; //前缀-订单单号
    const REDIS_PREFIX_REQUEST_SIGN = 'sy001004_'; //前缀-请求签名
    const REDIS_PREFIX_MESSAGE_QUEUE = 'sy001005_'; //前缀-消息队列
    const REDIS_PREFIX_IMAGE_DATA = 'sy001006_'; //前缀-图片缓存
    const REDIS_PREFIX_WX_JS_TICKET = 'sy002001_'; //前缀-微信公众号js ticket
    const REDIS_PREFIX_WX_ACCESS_TOKEN = 'sy002002_'; //前缀-微信公众号access token
    const REDIS_PREFIX_WX_COMPONENT_ACCESS_TOKEN = 'sy002003'; //前缀-微信开放平台access token
    const REDIS_PREFIX_WX_AUTHORIZER_CONSTANT = 'sy002004_'; //前缀-微信开放平台授权公众号常量
    const REDIS_PREFIX_WX_AUTHORIZER_JS_TICKET = 'sy002005_'; //前缀-微信开放平台授权公众号js ticket
    const REDIS_PREFIX_WX_AUTHORIZER_ACCESS_TOKEN = 'sy002006_'; //前缀-微信开放平台授权公众号access token
    const REDIS_PREFIX_WX_NATIVE_PRE = 'sy002007_'; //前缀-微信扫码预支付

    //YAC常量,长度不能超过30个字节,因为yac缓存的key长度不能超过40个字节
    const YAC_PREFIX_WX_JS_TICKET = 'wx001'; //前缀-微信公众号js ticket
    const YAC_PREFIX_WX_ACCESS_TOKEN = 'wx002'; //前缀-微信开放平台access token

    //校验器常量
    const VALIDATOR_STRING_TYPE_REQUIRED = 'string_required'; //字符串校验器类型-必填
    const VALIDATOR_STRING_TYPE_MIN = 'string_min'; //字符串校验器类型-最小长度
    const VALIDATOR_STRING_TYPE_MAX = 'string_max'; //字符串校验器类型-最大长度
    const VALIDATOR_STRING_TYPE_REGEX = 'string_regex'; //字符串校验器类型-正则表达式
    const VALIDATOR_STRING_TYPE_PHONE = 'string_phone'; //字符串校验器类型-手机号码
    const VALIDATOR_STRING_TYPE_TEL = 'string_tel'; //字符串校验器类型-联系方式
    const VALIDATOR_STRING_TYPE_EMAIL = 'string_email'; //字符串校验器类型-邮箱
    const VALIDATOR_STRING_TYPE_URL = 'string_url'; //字符串校验器类型-URL链接
    const VALIDATOR_STRING_TYPE_JSON = 'string_json'; //字符串校验器类型-JSON
    const VALIDATOR_STRING_TYPE_SIGN = 'string_sign'; //字符串校验器类型-请求签名
    const VALIDATOR_STRING_TYPE_BASE_IMAGE = 'string_baseimage'; //字符串校验器类型-base64编码图片
    const VALIDATOR_STRING_TYPE_IP = 'string_ip'; //字符串校验器类型-IP
    const VALIDATOR_STRING_TYPE_LNG = 'string_lng'; //字符串校验器类型-经度
    const VALIDATOR_STRING_TYPE_LAT = 'string_lat'; //字符串校验器类型-纬度
    const VALIDATOR_STRING_TYPE_NO_JS = 'string_nojs'; //字符串校验器类型-不允许js脚本
    const VALIDATOR_STRING_TYPE_NO_EMOJI = 'string_noemoji'; //字符串校验器类型-不允许emoji表情
    const VALIDATOR_STRING_TYPE_ZH = 'string_zh'; //字符串校验器类型-中文,数字,字母
    const VALIDATOR_INT_TYPE_REQUIRED = 'int_required'; //整数校验器类型-必填
    const VALIDATOR_INT_TYPE_MIN = 'int_min'; //整数校验器类型-最小值
    const VALIDATOR_INT_TYPE_MAX = 'int_max'; //整数校验器类型-最大值
    const VALIDATOR_INT_TYPE_IN = 'int_in'; //整数校验器类型-取值枚举
    const VALIDATOR_INT_TYPE_BETWEEN = 'int_between'; //整数校验器类型-取值区间
    const VALIDATOR_DOUBLE_TYPE_REQUIRED = 'double_required'; //浮点数校验器类型-必填
    const VALIDATOR_DOUBLE_TYPE_MIN = 'double_min'; //浮点数校验器类型-最小值
    const VALIDATOR_DOUBLE_TYPE_MAX = 'double_max'; //浮点数校验器类型-最大值
    const VALIDATOR_DOUBLE_TYPE_BETWEEN = 'double_between'; //浮点数校验器类型-取值区间

    //路由常量
    const ROUTE_TYPE_BASIC = 'basic'; //类型-基础路由

    //注册常量
    const REGISTRY_NAME_SERVICE_ERROR = 'SERVICE_ERROR'; //名称-服务错误
    const REGISTRY_NAME_REQUEST_HEADER = 'REQUEST_HEADER'; //名称-请求头
    const REGISTRY_NAME_REQUEST_SERVER = 'REQUEST_SERVER'; //名称-服务器信息
    const REGISTRY_NAME_RESPONSE_HEADER = 'RESPONSE_HEADER'; //名称-响应头
    const REGISTRY_NAME_RESPONSE_COOKIE = 'RESPONSE_COOKIE'; //名称-响应cookie

    //图片常量
    const IMAGE_MIME_TYPE_PNG = 'image/png'; //MIME类型-PNG
    const IMAGE_MIME_TYPE_JPEG = 'image/jpeg'; //MIME类型-JPEG
    const IMAGE_MIME_TYPE_GIF = 'image/gif'; //MIME类型-GIF

    //消息队列常量
    const MESSAGE_QUEUE_TOPIC_REDIS_ADD_LOG = 'a000'; //redis主题-添加日志
    const MESSAGE_QUEUE_TOPIC_REDIS_REQ_HEALTH_CHECK = 'a001'; //redis主题-请求健康检查
    const MESSAGE_QUEUE_TOPIC_KAFKA_ADD_MYSQL_LOG = 'b000'; //kafka主题-添加mysql日志

    //任务常量,4位字符串,数字和字母组成,纯数字的为框架内部任务,其他为自定义任务
    const TASK_TYPE_REFRESH_SERVER_REGISTRY = '0001'; //任务类型-刷新服务注册信息
    const TASK_TYPE_CLEAR_API_SIGN_CACHE = '0002'; //任务类型-清理api签名缓存
    const TASK_TYPE_CLEAR_LOCAL_USER_CACHE = '0003'; //任务类型-清除本地用户信息缓存
    const TASK_TYPE_REFRESH_LOCAL_CACHE = 'a000'; //任务类型-刷新本地缓存
}
