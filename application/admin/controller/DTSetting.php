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

            ];
            //验证规则
            $validate = Loader::validate('Admin');

            if (isset($params['id'])) {
                //更新操作
                if($params['old_password']){
                    $info = Db::name('admin')->field('password,encrypt')->find($params['id']);
                    $password = get_password($params['old_password'],$info['encrypt']);
                    if($info['password'] != $password){
                        exit(json_encode(['status' => 0, 'msg' => '原密码不正确', 'url' => '']));
                    }
                }
                if(!$validate->scene('edit')->check($data)){
                    $error = $validate->getError();
                    exit(json_encode(['status' => 0, 'msg' => $error, 'url' => '']));
                }
                $data['encrypt'] = get_randomstr();//6位hash值
                $data['password'] = get_password($data['password'],$data['encrypt']);

                unset($data['repassword']);
                $flag = Db::name('admin')->where('id',$params['id'])->update($data);
                if ($flag) {
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
                unset($data['repassword']);
                $data['encrypt'] = get_randomstr();//6位hash值
                $data['password'] = get_password($data['password'],$data['encrypt']);
                $data['logintime'] = time();
                $data['createtime'] = time();
                $data['loginip'] = request()->ip();
                $data['username'] = $params['user_name'];
                $flag=Db::name('admin')->insert($data);
                if ($flag) {
                    exit(json_encode(['status' => 1, 'msg' => '添加成功', 'url' => url('admin/index')]));
                } else {
                    exit(json_encode(['status' => 0, 'msg' => '添加失败,请稍后重试', 'url' => '']));
                }
            }


        } else {
            return $this->fetch();
        }
    }

}
