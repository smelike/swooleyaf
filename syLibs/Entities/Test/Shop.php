<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-3-5
 * Time: 15:30
 */
namespace Entities\Test;

use Constant\Project;
use DB\Entities\MysqlEntity;

class Shop extends MysqlEntity {
    public function __construct() {
        parent::__construct(Project::COMMON_DBNAME_DEFAULT, 'shop');
    }

    /**
     * 主键ID
     * @var int
     */
    public $id;

    /**
     * 店铺标题
     * @var string
     */
    public $title = '';

    /**
     * 店铺图片
     * @var string
     */
    public $images = '';

    /**
     * 纬度
     * @var float
     */
    public $lat = 0.00;

    /**
     * 经度
     * @var float
     */
    public $lng = 0.00;

    /**
     * 创建时间戳
     * @var int
     */
    public $created = 0;

    /**
     * 修改时间戳
     * @var int
     */
    public $updated = 0;
}