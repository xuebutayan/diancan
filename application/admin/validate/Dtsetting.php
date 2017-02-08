<?php
/**
 * 餐桌数据验证
 * ====================================================
 * 众达网络科技有限公司
 * ====================================================
 * $ ID: Dtsetting.php 2017/2/8 13:58 zdwl_cp $
 */
namespace app\admin\validate;

use think\Validate;

class Dtsetting extends Validate {
    protected $rule =   [
        'table_name'  => 'require|unique:dinner_table|max:25',
        'table_num'   => 'require|number|egt:1',
        'sort'        => 'number',
    ];

    protected $message  =   [
        'table_name.require' => '餐桌名称必须',
        'table_name.unique'  => '餐桌名称必须唯一',
        'table_name.max'     => '餐桌名称最多不能超过25个字符',
        'table_num.require'  => '可坐人数必须',
        'table_num.number'   => '可坐人数必须为数字',
        'table_num.egt'      => '可坐人数必须大于零',
        'sort.number'        => '排序必须为数字',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
    ];
}