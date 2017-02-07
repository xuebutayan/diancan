<?php
/**
 * 显示大厅列表
 * ====================================================
 * 众达网络科技有限公司
 * ====================================================
 * $ ID: Hall.php 2017/2/7 15:01 zdwl_cp $
 */
namespace app\admin\controller;

use think\Db;

class Hall extends Common
{

    private $dinner_table_status = array(
        'UNMANNED' => 0,
        'SEATING' => 1,
        'PAYING' => 2,
        'CLEANING' => 3
    );

    /**
     * 加载大厅
     * @return mixed
     */
    public function index() {

        // 大厅餐桌信息
        $hall_unmanned = $this->getHallByStatu($this->dinner_table_status['UNMANNED']);
        $hall_seating = $this->getHallByStatu($this->dinner_table_status['SEATING']);
        $hall_paying = $this->getHallByStatu($this->dinner_table_status['PAYING']);
        $this->assign('hall_unmanned', $hall_unmanned);
        $this->assign('hall_seating', $hall_seating);
        $this->assign('hall_paying', $hall_paying);
        return $this->fetch();
    }

    /**
     * 获取大厅餐桌信息
     * @return array
     */
    protected function getHall() {

        $hall = Db::name('DinnerTable') -> field('*') -> where('1 = 1') ->order('sort ASC, id ASC') -> select();

        return $hall;
    }

    /**
     * 根据 餐桌状态获得大厅信息
     * @param $statu 餐桌状态
     * @return array
     */
    protected function getHallByStatu($statu) {
        $hall = Db::name('DinnerTable') -> field('*')
                                        -> where('table_status = '.$statu)
                                        -> order('sort ASC, id ASC') -> select();
        return $hall;
    }


}