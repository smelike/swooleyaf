<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/3/2 0002
 * Time: 11:30
 */
namespace Constant;

use Traits\SimpleTrait;

final class Project {
    use SimpleTrait;

    //公共常量
    const COMMON_PAGE_DEFAULT = 1; //默认页数
    const COMMON_LIMIT_DEFAULT = 10; //默认分页限制
    const COMMON_DBNAME_DEFAULT = 'sytrain'; //默认数据库名

    //订单常量,处理类型常量格式 前4位为产品类型，中间3位为操作者类型，后3位为操作行为
    const ORDER_HANDLE_TYPE_GOODS_USER_ORDER = '0001000000'; //商品订单处理类型-用户下单
    const ORDER_HANDLE_TYPE_GOODS_SHOP_CONFIRM = '0001001000'; //商品订单处理类型-商家确认消费
    const ORDER_PAY_TYPE_GOODS = '0001'; //支付类型-商品
    const ORDER_REFUND_TYPE_GOODS = '5001'; //退款类型-商品

    //支付常量
    const PAY_TYPE_WX = 1; //类型-微信支付
    const PAY_TYPE_ALI = 2; //类型-支付宝支付
    const PAY_MODEL_TYPE_WX_JS = 'a00'; //模式类型-微信js支付
    const PAY_MODEL_TYPE_WX_NATIVE_DYNAMIC = 'a01'; //模式类型-微信动态扫码支付
    const PAY_MODEL_TYPE_WX_NATIVE_STATIC = 'a02'; //模式类型-微信静态扫码支付
    const PAY_MODEL_TYPE_ALI_WEB = 'b00'; //模式类型-支付宝网页支付
    const PAY_MODEL_TYPE_ALI_CODE = 'b01'; //模式类型-支付宝扫码支付

    //登录常量
    const LOGIN_TYPE_WX_AUTH_BASE = 'a000'; //类型-微信静默授权
    const LOGIN_TYPE_WX_AUTH_USER = 'a001'; //类型-微信手动授权
    const LOGIN_TYPE_WX_SCAN = 'a002'; //类型-微信扫码
    const LOGIN_TYPE_QQ = 'a100'; //类型-QQ
    const LOGIN_TYPE_EMAIL = 'a101'; //类型-邮箱
    const LOGIN_TYPE_PHONE = 'a102'; //类型-手机号码
    const LOGIN_TYPE_ACCOUNT = 'a103'; //类型-账号
}
