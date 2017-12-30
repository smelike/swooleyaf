<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/9/18 0018
 * Time: 14:30
 */
namespace Interfaces;

interface PayService {
    /**
     * 处理支付结果
     * @param array $data 参数数组
     * @return array 支付处理完成后,如需进行后续处理,比如发送短信,模板消息等,则返回非空数组,如不需后续处理,则返回空数组
     */
    public function handlePaySuccess(array $data) : array;

    /**
     * 支付处理成功后续操作
     * @param array $data 参数数组
     * @return mixed
     */
    public function handlePaySuccessAttach(array $data);
}