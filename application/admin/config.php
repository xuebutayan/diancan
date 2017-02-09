<?php
/**
 * Created by nango.
 * User: nango
 * Date: 2016-08-03
 * Time: 13:30
 */
$root = request()->root();
define('__ROOT__',str_replace('/index.php','',$root));
return [
// 应用调试模式
    'app_debug'              => false,
     // 应用Trace
    'app_trace'              => false,
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__PUBLIC__' => __ROOT__.'/static/admin',
        '__COMMON__' => __ROOT__.'/static/common'
    ],

    // 餐桌状态配置
    'table_status' => [
        '0' => '空桌待客',
        '3' => '顾客下单',
        '12' => '结账完成',
        '13' => '打扫清台',
    ],
];