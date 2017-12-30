<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/12/4 0004
 * Time: 11:34
 */
namespace AliDaYu;

use Constant\ErrorCode;
use Exception\Ali\AliDaYuException;

class DaYuSmsQuery extends DaYuSmsBase {
    /**
     * 流水号
     * @var string
     */
    private $bizId = '';
    /**
     * 接收号码
     * @var string
     */
    private $recNum = '';
    /**
     * 发送日期
     * @var string
     */
    private $queryDate = '';
    /**
     * 页码
     * @var int
     */
    private $page = 1;
    /**
     * 每页数量
     * @var int
     */
    private $limit = 10;

    public function __construct(){
        parent::__construct('alibaba.aliqin.fc.sms.num.query');
        $this->page = 1;
        $this->limit = 10;
    }

    private function __clone(){
    }

    /**
     * @param string $bizId
     */
    public function setBizId(string $bizId){
        if(strlen(trim($bizId)) > 0){
            $this->bizId = trim($bizId);
        }
    }

    /**
     * @param string $recNum
     * @throws \Exception\Ali\AliDaYuException
     */
    public function setRecNum(string $recNum){
        if (preg_match('/^1\d{10}$/', $recNum) > 0) {
            $this->recNum = $recNum;
        } else {
            throw new AliDaYuException('接收号码不合法', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
    }

    /**
     * @param string $queryDate
     * @throws \Exception\Ali\AliDaYuException
     */
    public function setQueryDate(string $queryDate){
        if(strlen($queryDate) == 8){
            $this->queryDate = $queryDate;
        } else {
            throw new AliDaYuException('发送日期不合法', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
    }

    /**
     * @param int $page
     * @throws \Exception\Ali\AliDaYuException
     */
    public function setPage(int $page){
        if($page >= 1){
            $this->page = $page;
        } else {
            throw new AliDaYuException('页码必须大于0', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
    }

    /**
     * @param int $limit
     * @throws \Exception\Ali\AliDaYuException
     */
    public function setLimit(int $limit){
        if(($limit >= 1) && ($limit <= 50)){
            $this->limit = $limit;
        } else {
            throw new AliDaYuException('每页数量必须在1-50之间', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
    }

    public function getDetail() : array {
        if(strlen($this->recNum) == 0){
            throw new AliDaYuException('接收号码必须填写', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
        if(strlen($this->queryDate) == 0){
            throw new AliDaYuException('发送日期必须填写', ErrorCode::ALIDAYU_PARAM_ERROR);
        }

        $resArr = $this->getBaseDetail();
        $resArr['rec_num'] = $this->recNum;
        $resArr['query_date'] = $this->queryDate;
        $resArr['current_page'] = $this->page;
        $resArr['page_size'] = $this->limit;
        if(strlen($this->bizId) > 0){
            $resArr['biz_id'] = $this->bizId;
        }
        DaYuUtil::createSmsSign($resArr);

        return $resArr;
    }
}