<?php
/**
 * 后台统计功能
 * ====================================================
 * 众达网络科技有限公司
 * ====================================================
 * $ ID: Statistical.php 2017/2/13 9:23 zdwl_cp $
 */
namespace app\admin\controller;

use Think\Db;

class Statistical extends Common
{

    private $pay_order_status = [12, 13];

    /**
     * 统计列表首页
     * @return mixed
     */
    public function index() {

        $start = input('start', date('Y-m-d', time()));
        $end = input('end', date('Y-m-d', time()));
        $type = input('type', 'default');

        $date = $this->getDate($type, $start, $end);
        $statistics = array(
            'revenue' => $this->getRevenue($date),
            'oNum' => $this->getOrderNum($date),
            'dNum' => $this->getDishesNum($date, 0),
            'tdNum' => $this->getDishesNum($date, 2),
            'sells' => $this->getSells($date)
        );
        $datetime = new \DateTime($end);
        $date['end'] = empty($end) ? $datetime->modify("-1 day")->format("Y-m-d") : $end;

        $this->assign('date', $date);
        $this->assign('type', $type);
        $this->assign('jsonOrder', $this->getJsonOrder($date));
        $this->assign('stat', $statistics);
        return $this->fetch();
    }

    /**
     * 统计窗口
     * @return mixed
     */
    public function dilog() {

        $start = input('start', date('Y-m-d', time()));
        $end = input('end', date('Y-m-d', time()));
        $type = input('type');

        $datetime = new \DateTime($end);
        $end = $datetime->modify("+1 day")->format("Y-m-d");
        $dished = Db::table('OCK') -> field('Cn, count(ID) AS num') -> group('Ci')
            -> where('St', 'in', $this->pay_order_status)
            -> where('Td', $type)
            -> where('Dt','between time', [$start, $end]) -> select();

        foreach($dished as $k => $v) {
            $dished[$k]['color'] = $this->getRandColor();
        }
        $this->assign('jsonDished', json_encode($dished));

        return $this->fetch();
    }

    /**
     * 获得营业额
     * @param $date
     * @return string
     */
    protected function getRevenue($date) {

        $total = Db::table('ODR') -> where('St', 'in', $this->pay_order_status)
                                  -> where('Ds','between time', [$date['start'], $date['end']]) -> sum('Pz');
        return number_format($total / 100);
    }

    /**
     * 获得点单数数量
     * @param $date
     * @return int
     */
    protected function getOrderNum($date) {

        $oNum = Db::table('ODR') -> where('St', 'in', $this->pay_order_status)
                                 -> where('Ds','between time', [$date['start'], $date['end']]) -> count('ID');

        return $oNum;
    }

    /**
     * 获得 点菜数，退菜数数量
     * @param $date
     * @param $type 类型 type=0 点菜数，type=2 退菜数
     * @return int
     */
    protected function getDishesNum($date, $type) {

        $dishesNum = Db::table('OCK') -> where('St', 'in', $this->pay_order_status)
            -> where('Td', $type)
            -> where('Dt','between time', [$date['start'], $date['end']]) -> count('ID');

        return $dishesNum;
    }

    /**
     * 获得畅销菜品列表
     * @param $date
     * @return array
     */
    protected function getSells($date) {

        $sells = Db::table('OCK') -> field('Cn, count(ID) AS num') -> group('Ci')
            -> where('St', 'in', $this->pay_order_status)
            -> where('Td', 0)
            -> where('Dt','between time', [$date['start'], $date['end']]) -> order('num DESC') -> limit(10) -> select();

        // echo Db::table('OCK') -> getLastSql();

        return $sells;
    }

    /**
     * 对开始，结束时间 数据处理
     * @param string $type 类型
     * @param string $start 开始日期
     * @param string $end 结束日期
     * @return array 处理后数据
     */
    protected function getDate($type = 'default', $start = '', $end = '') {

        $datetime = new \DateTime($end);

        $date = array(
            'start' => '',
            'end' => ''
        );
        switch($type) {
            case 'today':
                $date['start'] = date('Y-m-d', time());
                $date['end'] = $datetime->modify("+1 day")->format("Y-m-d");
                break;
            case 'week':
                $datetime->modify('this week');
                $date['start'] = $datetime->format('Y-m-d');
                $date['end'] = $datetime->modify("+1 day")->format("Y-m-d");
                break;
            case 'mouth':
                $date['start'] = date('Y-m-01', time());
                $date['end'] = $datetime->modify("+1 day")->format("Y-m-d");
                break;
            case 'year':
                $date['start'] = date('Y-01-01', time());
                $date['end'] = $datetime->modify("+1 day")->format("Y-m-d");
                break;
            case 'default':
                $date['start'] = $start;
                $date['end'] = $datetime->modify("+1 day")->format("Y-m-d");
                break;
        }
        return $date;
    }

    /**
     * 获得营业额 JSON数据
     * @param $date 开始结束日期数组
     * @return string JSON数据
     */
    protected function getJsonRevenue($date) {

        $revenue = Db::table('ODR') -> field('SUM(Pz) / 100 AS revenue, substr(Ds FROM 6 FOR 5) AS date') -> where('St', 'in', $this->pay_order_status)
            -> where('Ds','between time', [$date['start'], $date['end']]) -> group('substr(Ds FROM 1 FOR 10)') -> select();

        return json_encode($revenue);
    }

    /**
     * 获得营业额及订单量统计JSON数据
     * @param $date 开始结束日期数组
     * @return string JSON数据
     */
    protected function getJsonOrder($date) {

        // TODO 该方法太过耗费时间，需要优化
        $day_num = $this->getDaysPoor($date);
        $order = array();
        for ($i = 0; $i < $day_num; $i++) {
            $datetime = new \DateTime($date['start']);
            $start = $datetime -> modify('+'.$i.' day') -> format('Y-m-d');
            $datetime = new \DateTime($date['start']);
            $end = $datetime -> modify('+'.($i+1).' day') -> format('Y-m-d');

            $order[$i]['date'] = $start;
            $revenue = Db::table('ODR') -> where('St', 'in', $this->pay_order_status)  -> where('Ds', 'between time', [$start, $end]) -> sum('Pz');
            $order[$i]['revenue'] = $revenue / 100;
            $order[$i]['oNum'] = Db::table('ODR') -> where('St', 'in', $this->pay_order_status)  -> where('Ds', 'between time', [$start, $end]) -> count('ID');
            $order[$i]['dNum'] = Db::table('OCK') -> where('St', 'in', $this->pay_order_status) -> where('Td', 0) -> where('Dt', 'between time', [$start, $end]) -> count('ID');
            $order[$i]['tdNum'] = Db::table('OCK') -> where('St', 'in', $this->pay_order_status) -> where('Td', 2) -> where('Dt', 'between time', [$start, $end]) -> count('ID');
        }
        return json_encode($order);
    }

    /**
     * 获得天数相差值
     * @param $date 开始结束日期数组
     * @return float 日期相差值
     */
    protected function getDaysPoor($date) {

        $startdate=strtotime($date['start']);
        $enddate=strtotime($date['end']);

        return round(($enddate-$startdate)/3600/24) + 1 ;
    }

    /**
     * 获得随机颜色值
     * @return string 颜色值
     */
    protected function getRandColor() {
        $colors = array();
        for($i = 0;$i<6;$i++){
            $colors[] = dechex(rand(0,15));
        }
        return '#'.implode('',$colors);
    }

}