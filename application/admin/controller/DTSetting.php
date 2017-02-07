<?php
/**
 * 餐桌管理功能
 * ====================================================
 * 众达网络科技有限公司
 * ====================================================
 * $ Id: Dtsetting.php 2017/2/7 14:05 zdwl_cp $
 */
namespace app\admin\controller;

use think\Db;

class Dtsetting extends Common {

    /**
     *
     *
     */
    public function index() {
        // 加载大厅信息

        return $this->fetch();
    }


    protected function getHallInfo() {
        $hallinfo = null;

        // SQL = Select ID,Nm,No,Ns,Ni,Oi From SCT Where DD=0 Order By No Asc
        return $hallinfo;
    }

}
