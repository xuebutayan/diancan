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
        'Nm'  => 'require|max:25',
        'Ns'  => 'require|number|egt:1',
        'No'  => 'number',
    ];

    protected $message  =   [
        'Nm.require' => '餐桌名称必须',
        'Nm.unique'  => '餐桌名称必须唯一',
        'Nm.max'     => '餐桌名称最多不能超过25个字符',
        'Ns.require' => '可坐人数必须',
        'Ns.number'  => '可坐人数必须为数字',
        'Ns.egt'     => '可坐人数必须大于零',
        'No.number'  => '排序必须为数字',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
    ];
}