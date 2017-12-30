<?php
/**
 * 地区详情
 * User: 姜伟
 * Date: 2017/6/19 0019
 * Time: 21:19
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;
use Tool\Tool;

class PlaceDetail extends BaseConfig {
    const SCOPE_BASE = 1; //结果详细程度-基本信息
    const SCOPE_DETAIL = 2; //结果详细程度-POI详细信息

    public function __construct() {
        parent::__construct();

        $this->scope = self::SCOPE_BASE;
    }

    private function __clone() {
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());

        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * poi的uid数组
     * @var array
     */
    private $uids = [];
    /**
     * 结果详细程度
     * @var int
     */
    private $scope = 0;

    /**
     * 添加uid
     * @param string $uid
     * @throws \Exception\Map\BaiduMapException
     */
    public function addUid(string $uid) {
        if (preg_match('/^[0-9a-z]{24}$/', $uid) == 0) {
            throw new BaiduMapException('uid不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        if(!in_array($uid, $this->uids, true)){
            if (count($this->uids) >= 10) {
                throw new BaiduMapException('uid数量超过限制', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            $this->uids[] = $uid;
        }
    }

    /**
     * @return array
     */
    public function getUids() : array {
        return $this->uids;
    }

    /**
     * @return int
     */
    public function getScope() : int {
        return $this->scope;
    }

    /**
     * @param int $scope
     * @throws \Exception\Map\BaiduMapException
     */
    public function setScope(int $scope) {
        if(in_array($scope, [self::SCOPE_BASE, self::SCOPE_DETAIL], true)){
            $this->scope = $scope;
        } else {
            throw new BaiduMapException('结果详细程度不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }
}