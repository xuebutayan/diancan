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


        var_dump($this->getJsonOrder($date));

        $this->assign('date', $date);
        $this->assign('type', $type);
        $this->assign('jsonRevenue', $this->getJsonRevenue($date));
        $this->assign('stat', $statistics);
        return $this->fetch();
    }

    protected function getRevenue($date) {

        $total = Db::table('ODR') -> where('St', 'in', $this->pay_order_status)
                                  -> where('Ds','between time', [$date['start'], $date['end']]) -> sum('Pz');
        return number_format($total / 100);
    }

    protected function getOrderNum($date) {

        $oNum = Db::table('ODR') -> where('St', 'in', $this->pay_order_status)
                                 -> where('Ds','between time', [$date['start'], $date['end']]) -> count('ID');

        return $oNum;
    }

    protected function getDishesNum($date, $type) {

        $dishesNum = Db::table('OCK') -> where('St', 'in', $this->pay_order_status)
            -> where('Td', $type)
            -> where('Dt','between time', [$date['start'], $date['end']]) -> count('ID');

        return $dishesNum;
    }

    protected function getSells($date) {

        $sells = Db::table('OCK') -> field('Cn, count(ID) AS num') -> group('Ci')
            -> where('St', 'in', $this->pay_order_status)
            -> where('Dt','between time', [$date['start'], $date['end']]) -> order('num DESC') -> limit(10) -> select();

        // echo Db::table('OCK') -> getLastSql();

        return $sells;
    }

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

    protected function getJsonRevenue($date) {

        $revenue = Db::table('ODR') -> field('SUM(Pz) / 100 AS revenue, substr(Ds FROM 6 FOR 5) AS date') -> where('St', 'in', $this->pay_order_status)
            -> where('Ds','between time', [$date['start'], $date['end']]) -> group('substr(Ds FROM 1 FOR 10)') -> select();

        return json_encode($revenue);
    }

    protected function getJsonOrder($date) {
        $oNum = Db::table('ODR') -> field('count(ID) AS oNum, substr(Ds FROM 6 FOR 5) AS day')
            -> where('St', 'in', $this->pay_order_status)
            -> where('Ds','between time', [$date['start'], $date['end']]) -> group('substr(Ds FROM 1 FOR 10)') -> select();

        $dNum = Db::table('OCK') -> field('count(ID) AS dNum, substr(Dt FROM 6 FOR 5) AS day')
            -> where('St', 'in', $this->pay_order_status)
            -> where('Td', 0)
            -> where('Dt','between time', [$date['start'], $date['end']]) -> group('substr(Dt FROM 1 FOR 10)') -> select();

        $tdNum = Db::table('OCK') -> field('count(ID) AS tdNum, substr(Dt FROM 6 FOR 5) AS day')
            -> where('St', 'in', $this->pay_order_status)
            -> where('Td', 2)
            -> where('Dt','between time', [$date['start'], $date['end']]) -> group('substr(Dt FROM 1 FOR 10)') -> select();

        $datetime = new \DateTime($date['start']);

        echo $this->getDaysPoor($date);

//        return json_decode(['oNum' => $oNum, 'dNum' => $dNum, 'tdNum' => $tdNum]);
    }

    protected function getDaysPoor($date) {

        $startdate=strtotime($date['start']);
        $enddate=strtotime($date['end']);

        return round(($enddate-$startdate)/3600/24) + 1 ;
    }

}