<?php
/**
 *菜单栏目
 */

namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Request;

class Menu extends Common{
    function index(){
    	$cat = Db::table('SCL')->order('ID asc')->order('No asc')->select();
    	$cid = $cat[0]['ID'];
    	//菜品
    	$dishes = Db::table('SCD')->where(['DD'=>0])->order('No asc')->select();
    	$new_dishes = [];
    	foreach ($dishes as $v) {
    		if(in_array($cid,array_filter(explode(',',$v['Nt'])))) $new_dishes[]=$v;
    	}
    	//套餐
    	$combo = Db::name('combo')->order('listorder asc')->select();
    	$new_combo = [];
    	foreach ($combo as $v) {
    		if(in_array($cid,array_filter(explode(',',$v['cats'])))) $new_combo[]=$v;
    	}
    	$this->assign('dishes',$new_dishes);
    	$this->assign('combo',$new_combo);
    	$this->assign('cat',$cat);//dump($cat);
        return $this->fetch();
    }
    function menus(){
    	$id = input('get.cid');
    	$dishes = Db::table('SCD')->where(['DD'=>0])->order('No asc')->select();
    	$combo = Db::name('combo')->order('listorder asc')->select();
    	$re = '';
    	foreach ($dishes as $v) {
    		if(in_array($id,array_filter(explode(',',$v['Nt'])))){
    			$v['url'] = url('Dish/edit','id='.$v['ID']);
    			$re .= '$'.$v['ID'].'|'.$v['Nm'].'|'.$v['Nt'].'|'.$v['No'].'|'.$v['Ni'].'|'.$v['Np'].'|'.$v['Ns'].'|'.$v['Nh'].'|'.$v['url'].'|'.$v['pic'];
    		}
    	}
    	//套餐菜品显示
    	$re2 = '';
    	foreach ($combo as $v) {
    		if(in_array($id,array_filter(explode(',',$v['cats'])))){
    			$v['url'] = url('combo/edit','id='.$v['id']);
    			$re2 .= '$'.$v['id'].'|'.$v['name'].'[套]|'.$v['cats'].'|'.$v['pic'].'|'.$v['price'].'|'.$v['url'];
    		}
    	}
    	return json(['dishes'=>$re,'combo'=>$re2]);
    }
    public function add()
    {

        if (request()->isPost()) {
        	$params = input('post.');
        	$params['Nm'] = $params['Nm']?$params['Nm']:'未命名';
        	$params['No'] = $params['No']?$params['No']:99;
            $data = [
                'Nm' => $params['Nm'],
                'No' => $params['No'],
                'Nh' => $params['Nh'],
                'Ns'=>0
            ];

            if (isset($params['ID'])) {
                $flag = Db::table('SCL')->where('id',$params['ID'])->update($data);
                if ($flag) {
                    exit(json_encode(['status' => 1, 'msg' => '修改成功', 'url' => url('menu/index')]));
                } else {
                    exit(json_encode(['status' => 0, 'msg' => '修改失败,请稍后重试', 'url' => '']));
                }
            }else{
                //新增
                $flag=Db::table('SCL')->insert($data);
                if ($flag) {
                    exit(json_encode(['status' => 1, 'msg' => '添加成功', 'url' => url('menu/index')]));
                } else {
                    exit(json_encode(['status' => 0, 'msg' => '添加失败,请稍后重试', 'url' => '']));
                }
            }

        } else {
            return $this->fetch();
        }
    }
    function edit1(){
    	$data = [];
    	$this->assign('data',$data);
    	return $this->fetch('edit');
    }
    function edit($id){
    	$data = Db::table('SCL')->find($id);
    	$this->assign('data',$data);
    	return $this->fetch();
    }
}
