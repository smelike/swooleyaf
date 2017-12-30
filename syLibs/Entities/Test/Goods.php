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

class Goods extends MysqlEntity {
    public function __construct() {
        parent::__construct(Project::COMMON_DBNAME_DEFAULT, 'goods');
    }

    /**
     * 主键ID
     * @var int
     */
    public $id;

    /**
     * 商品标题
     * @var string
     */
    public $title = '';

    /**
     * 商品图片
     * @var string
     */
    public $images = '';

    /**
     * 商品价格
     * @var float
     */
    public $price = 0.00;

    /**
     * 店铺ID
     * @var int
     */
    public $shop_id = 0;

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