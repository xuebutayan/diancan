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
    // 餐桌状态
    private $dinner_table_status = array(
        'UNMANNED' => 0,
        'SEATING' => 3,
        'PAYING' => 12,
        'CLEANING' => 13
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

        $this->assign('update_time', date('H:i:s', time()));
        
        return $this->fetch();
    }

    /**
     * 餐桌订单页面
     * @return mixed
     */
    public function order() {
        $id = input('id');

        $table = Db::table('SCT') -> field('Nm, Oi') -> where('ID', $id) -> find();

        // 餐桌查看状态更改
        if ($this->checkDtStatus($id) == 0) {
            // 未查看,修改状态
            Db::table('SCT') -> where('ID', $id) -> update(['St' => 1]);
        }

        $this->assign('table_name', $table['Nm']);
        $this->assign('order', $this->getOrder($id));
        $this->assign('dishes', $this->getDishesList($table['Oi'], $id));
        $this->assign('STATUS', $this->dinner_table_status);

        return $this->fetch();
    }

    /**
     * 显示退菜页面
     * @return mixed
     */
    public function foodback() {
        $id = input('id');

        $foodback = Db::table('OCK') -> field('ID,Oi,Ti,Ci,Cn,Cp,Cs,St,I1,I2,I3,Dt,Td,Bz')
                                     -> where('ID', $id) -> find();

        $foodback['table_name'] = Db::table('SCT') -> where('ID', $foodback['Ti']) -> value('Nm');
        $foodback['Cp'] = $foodback['Cp'] / 100;
        $foodback['Tp'] = $foodback['Cp'] * $foodback['Cs'];
        $foodback['status'] = $foodback['Td'] == 2 ? "已退菜" : "未退";

        $this->assign('foodback', $foodback);

        return $this->fetch();
    }

    /**
     * 退菜/拒退 操作接口
     *
     */
    public function disheseidt() {
        $id = input('param.id/d',0);
        $bz = input('param.bz');
        $type = input('param.type');
        $oid = input('param.Oi');
        $msg = $type == 0 ? "拒退成功" : "退菜成功";
        $flag = Db::table('OCK')->where(['id' => $id])->update(['Td' => $type, 'Bz' => $bz]);
        if ($flag) {
            // 修改订单总金额
            $pz = $this->getOrderAmount($oid);
            Db::table('ODR') -> where(['id' => $oid]) -> update(['Pz' => $pz]);
            exit(json_encode(['status' => 1, 'msg' => $msg, 'url' => '']));
        } else {
            exit(json_encode(['status' => 0, 'msg' => '失败', 'url' => '']));
        }
    }

    /**
     * 结账操作
     * @return mixed
     */
    public function settle() {
        $oid = input('oid');
        $Ti = input('Ti');
        $Pt = input('Pt');

        // 判断订单状态
        if ($this->getOrderStatus($oid) == $this->dinner_table_status['SEATING']) {
            // 结账
            Db::table('ODR') -> where('ID', $oid) -> update(['Ps' => $Pt, 'Ds' => date('Y-m-d H:i:s', time()), 'St' => $this->dinner_table_status['PAYING']]);
            Db::table('OCK') -> where(['Oi' => $oid, 'Ti' => $Ti]) -> update(['St' => $this->dinner_table_status['PAYING']]);
            Db::table('SCT') -> where('ID', $Ti) -> update(['Ni' => $this->dinner_table_status['PAYING']]);

            $ohid = Db::table('ODR') -> where('ID', $oid) -> value('ohid');
            if ($ohid) {
                Db::name('ohistory') -> where('id', $ohid) -> update(['status' => $this->dinner_table_status['paying']]);
            }

            exit(json_encode(['status' => 1, 'msg' => '结账成功！', 'url' => '']));
        } else {
            exit(json_encode(['status' => 0, 'msg' => '订单状态错误，无法结账！', 'url' => '']));
        }

        return $this->fetch();
    }

    /**
     * 清台操作
     */
    public function clearDt() {
        $Ti = input('Ti');
        $oid = input('oid');
        // 判断订单是否已结账
        if ($this->getOrderStatus($oid) == $this->dinner_table_status['PAYING']) {
            // 已结账，可清台
            Db::table('SCT') -> where('ID', $Ti) -> update(['Ni' => 0, 'Oi' => 0, 'St' => 0]);
            Db::table('ODR') -> where('ID', $oid) -> update(['St' => $this->dinner_table_status['CLEANING']]);
            Db::table('OCK') -> where(['Oi' => $oid, 'Ti' => $Ti]) -> update(['St' => $this->dinner_table_status['CLEANING']]);
            exit(json_encode(['status' => 1, 'msg' => '该餐桌已清台！', 'url' => '']));
        } else {
            // 未结账
            exit(json_encode(['status' => 0, 'msg' => '该餐桌未结账，请结账后在清台！', 'url' => '']));
        }

    }

    /**
     * 大厅页面轮询
     *
     */
    public function poll() {

        // 新订单定义： 状态：顾客下单 St = 0;
        $info = Db::table('SCT') -> field('ID, Nm, Ns, Ni, St') -> where(['St' => 0, 'Ni' => $this->dinner_table_status['SEATING']]) -> select();
        if (!empty($info)) {
            exit(json_encode(['status' => 1, 'msg' => '有新的订单等待查看！', 'info' => $info, 'url' => url('hall/tableinfo'), 'time' => date('H:i:s', time())]));
        } else {
            exit(json_encode(['status' => 0, 'msg' => '无新的订单！', 'url' => '', 'time' => date('H:i:s', time())]));
        }

    }

    /**
     * 显示新订单提醒页面
     * @return mixed
     */
    public function tableinfo() {
        $info = Db::table('SCT') -> field('ID, Nm, Ni, St') -> where(['St' => 0, 'Ni' => $this->dinner_table_status['SEATING']]) -> select();

        $this->assign('tableinfo', $info);
        return $this->fetch();
    }

    /**
     * 新订单查看状态更新
     */
    public function updatestatus() {
        $flag = Db::table('SCT') -> where(['St' => 0, 'Ni' => $this->dinner_table_status['SEATING']]) -> update(['St' => 1]);
        if ($flag) {
            exit(json_encode(['status' => 1]));
        }
    }


    /**
     * 获取大厅餐桌信息
     * @return array
     */
    protected function getHall() {

        // $hall = Db::name('DinnerTable') -> field('*') -> where('state = 1') ->order('sort ASC, id ASC') -> select();
        $hall = Db::table('SCT') -> field('*') -> where('DD', 0) -> order('No ASC') -> select();

        return $hall;
    }

    /**
     * 根据 餐桌状态获得大厅信息
     * @return array
     */
    protected function getHallByStatu() {
        // $hall = Db::name('DinnerTable') -> field('*') -> where('state = 1') -> order('sort ASC, id ASC') -> select();
        $hall = Db::table('SCT') -> field('*') -> where('DD', 0) -> order('No ASC') -> select();

        $dtinfo = array();
        foreach ($hall as $key => $value) {
            if ($value['Ni'] == $this->dinner_table_status['UNMANNED']) {
                $dtinfo['unmanned'][$key] = $value;     
            }
            if ($value['Ni'] == $this->dinner_table_status['SEATING']) {
                $dtinfo['seating'][$key] = $value; 
            }
            if ($value['Ni'] == $this->dinner_table_status['PAYING']) {
                $dtinfo['paying'][$key] = $value;  
            }
        }
        return $dtinfo;
    }

    /**
     * 获得订单信息
     * @param $id 餐桌所属ID
     * @return array 订单信息
     */
    protected function getOrder($id) {

        $oid = Db::table('SCT') -> where('ID', $id) -> value('Oi');

        $order = Db::table('ODR') -> field('ID,Ti,Yi,Pz,Ps,Pt,St,Dt,I4,Ds,Bz') -> where('ID', $oid) -> find();

        $order['Pz'] = $order['Pz'] / 100;

        return $order;
    }

    /**
     * 获得订单菜品列表
     * @param $oid 订单ID
     * @param $id  餐桌ID
     * @return array 订单菜品列表
     */
    protected function getDishesList($oid, $id) {
        $dishes = Db::table('OCK') -> field('ID,Oi,Ti,Ci,Cn,Cp,Cs,St,I1,I2,I3,Dt,Td,Bz,combo,dishes')
                                   -> where('Oi', $oid) -> where('Ti', $id) -> order('Td ASC') -> select();
        foreach ($dishes as $k => $v) {
            $dishes[$k]['Cp'] = $v['Cp'] / 100;
            $dishes[$k]['St'] = Config('table_status.'.$v['St']);
            // 获取套餐菜品列表
            if ($v['combo'] == 1) {
                $dishes[$k]['dishes'] = $this->getSetMeal($v['dishes']);
            }

        }
        return $dishes;
    }

    /**
     * 获得订单总金额
     * @param $oid 订单ID
     * @return int 总金额
     */
    protected function getOrderAmount($oid) {
        $dishes = Db::table('OCK') -> field('Cp,Cs')
            -> where('Oi', $oid) -> where('Td', 0) -> select();
        $amount = 0;
        foreach ($dishes as $k => $v) {
            $amount += ($v['Cp'] * $v['Cs']);
        }

        return $amount;
    }

    /**
     * 获得订单状态
     * @param $id 订单ID
     * @return mixed 订单状态
     */
    protected function getOrderStatus($id) {
        return Db::table('ODR') -> where('ID', $id) -> value('St');
    }

    /**
     * 检验餐桌查看状态
     * @param $id 餐桌ID
     * @return mixed 餐桌是否已查看
     */
    protected function checkDtStatus($id) {
        return Db::table('SCT') -> where('ID', $id) -> value('St');
    }

    protected function getSetMeal($dishes) {
        // 获得菜品列表
        $list = Db::table('SCD') -> field('Nm, Ns') -> where('ID', 'in', $dishes) -> order('No ASC') -> select();
        foreach ($list as $k => $v) {
            $list[$k]['Ns'] = $v['Ns'] / 100;
        }
        return $list;
    }

}