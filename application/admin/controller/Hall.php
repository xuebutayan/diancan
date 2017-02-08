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
        $dtinfo = $this->getHallByStatu(); 
        
        $this->assign('hall_unmanned', empty($dtinfo['unmanned']) ? null : $dtinfo['unmanned']);
        $this->assign('hall_seating', empty($dtinfo['seating']) ? null : $dtinfo['seating']);
        $this->assign('hall_paying', empty($dtinfo['paying']) ? null : $dtinfo['paying']);
        
        return $this->fetch();
    }

    /**
     * 获取大厅餐桌信息
     * @return array
     */
    protected function getHall() {

        $hall = Db::name('DinnerTable') -> field('*') -> where('state = 1') ->order('sort ASC, id ASC') -> select();

        return $hall;
    }

    /**
     * 根据 餐桌状态获得大厅信息
     * @return array
     */
    protected function getHallByStatu() {
        $hall = Db::name('DinnerTable') -> field('*')
                                        -> where('state = 1')
                                        -> order('sort ASC, id ASC') -> select();
        $dtinfo = array();
        foreach ($hall as $key => $value) {
            if ($value['table_status'] == $this->dinner_table_status['UNMANNED']) {
                $dtinfo['unmanned'][$key] = $value;     
            }
            if ($value['table_status'] == $this->dinner_table_status['SEATING']) {
                $dtinfo['seating'][$key] = $value; 
            }
            if ($value['table_status'] == $this->dinner_table_status['PAYING']) {
                $dtinfo['paying'][$key] = $value;  
            }
        }
        return $dtinfo;
    }


}