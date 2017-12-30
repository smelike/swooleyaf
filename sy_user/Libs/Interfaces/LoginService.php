<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-19
 * Time: 下午3:50
 */
namespace Interfaces;

interface LoginService {
    /**
     * 校验相关数据
     * @param array $data 参数数组
     * @return array
     * 必须存在的参数:
     * code: int 状态码 0:成功 >0:出错
     * 可能存在的参数:
     * message: string 出错信息,code>0时必须存在
     * data: array 正确返回数据,code=0时必须存在
     */
    public function verifyData(array $data) : array;

    /**
     * 处理登录
     * @param array $data 参数数组
     * @return array
     * 必须存在的参数:
     * code: int 状态码 0:成功 >0:出错
     * 可能存在的参数:
     * message: string 出错信息,code>0时必须存在
     * data: array 正确返回数据,code=0时必须存在
     */
    public function handleLogin(array $data) : array;

    /**
     * 登录成功后的后续操作
     * @param array $data 参数数组
     * @return array
     * 必须存在的参数:
     * code: int 状态码 0:成功 >0:出错
     * 可能存在的参数:
     * message: string 出错信息,code>0时必须存在
     * data: array 正确返回数据,code=0时必须存在
     */
    public function successLogin(array $data) : array;
}