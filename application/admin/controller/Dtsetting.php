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
use think\Loader;

class Dtsetting extends Common {

    protected $QR_code_url = "http://www.liantu.com/api.php?el=L&m=10&w=281&text=";

    /**
     *  餐桌管理列表
     */
    public function index() {

        $hall = $this->getHall();
        $this->assign('tables', $hall);
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
     * 添加餐桌
     * @return mixed
     */
    public function add()
    {
        if (request()->isPost()) {
            //修改处理
            $params = input('post.');
            $data = [
                'table_name' => $params['table_name'],
                'table_num'  => $params['table_num'],
                'sort'       => $params['sort'],
                'state'      => $params['state'],
            ];
            //验证规则
            $validate = Loader::validate('Dtsetting');

            if (isset($params['id'])) {
                //更新操作
                if(!$validate->check($data)){
                    $error = $validate->getError();
                    exit(json_encode(['status' => 0, 'msg' => $error, 'url' => '']));
                }
                $flag = Db::name('DinnerTable')->where('id',$params['id'])->update($data);
                if ($flag) {
                    // TODO 添加Log日志
                    exit(json_encode(['status' => 1, 'msg' => '修改成功', 'url' => url('admin/index')]));
                } else {
                    exit(json_encode(['status' => 0, 'msg' => '修改失败,请稍后重试', 'url' => '']));
                }
            }else{
                //新增
                if(!$validate->check($data)){
                    $error = $validate->getError();
                    exit(json_encode(['status' => 0, 'msg' => $error, 'url' => '']));
                }

                $flag=Db::name('DinnerTable')->insert($data);

                if ($flag) {
                    // TODO 添加Log日志
                    exit(json_encode(['status' => 1, 'msg' => '添加成功', 'url' => url('Dtsetting/index')]));
                } else {
                    exit(json_encode(['status' => 0, 'msg' => '添加失败,请稍后重试', 'url' => '']));
                }
            }


        } else {
            $this->assign('QR_CODE_URL', $this->QR_code_url.get_url());
            return $this->fetch();
        }
    }

    /**
     * 修改用户信息
     */
    public function edit($id)
    {
        $data = Db::name('DinnerTable')->find($id);
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 删除用户信息
     */
    public function dele()
    {
        $id = input('param.id/d',0);
        $flag = Db::name('DinnerTable')->where(['id' => $id])->update(['state' => 0]);
        if ($flag) {
            exit(json_encode(['status' => 1, 'msg' => '删除成功', 'url' => url('Dtsetting/index')]));
        } else {
            exit(json_encode(['status' => 0, 'msg' => '删除失败', 'url' => '']));
        }
    }



}
