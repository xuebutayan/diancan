<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;
//菜品分类
class Sort extends Controller {
	function index(){
		$request = Request::instance();
		$domain = $request->domain();
		$api = [];
		//获取套餐分组
		$groups = Db::table('SCD')->field('groups')->distinct(true)->where(['DD'=>0])->select();
		$groups = array_column($groups, 'groups');
		//获取菜品分类
		$list1 = Db::table('SCL')->order('ID asc')->select();
		//获取所有菜品
		$list2 = Db::table('SCD')->where(['DD'=>0])->select();
		//获取套餐
		$list3 = Db::name('combo')->order('listorder asc')->select();
		foreach ($list1 as $k => $v) {
			foreach ($list2 as $k2 => $v2) {
				//处理图片
				if($v2['pic']) $v2['pic'] = $domain.$v2['pic'];
				else $v2['pic'] = $domain.'/static/common/images/0.jpg';
				//处理价格
				$v2['Ns'] = $v2['Ns']/100;
				$cs = explode(',',trim($v2['Nt']));//所属类目
				if(in_array($v['ID'],$cs)) $v['items'][] = $v2;
			}
			//处理套餐
			foreach ($list3 as $k3 => $v3) {
				//处理套餐中菜品
				$dishes = $this->format_dish($list2,$v3['dishes'],$groups);
				$v3['dishes'] = $dishes;
				$v3['combo']=1;
				$v3['Nh']=0;
				$v3['ID'] = $v3['id'];unset($v3['id']);
				$v3['Nm'] = $v3['name'];unset($v3['name']);
				$v3['Nt'] = $v3['cats'];unset($v3['cats']);
				$v3['Ns'] = $v3['price'];
				//处理图片
				if($v3['pic']) $v3['pic'] = $domain.$v3['pic'];
				else $v3['pic'] = $domain.'/static/common/images/0.jpg';
				$cs = explode(',',trim($v3['Nt']));//所属类目
				if(in_array($v['ID'],$cs)) $v['items'][] = $v3;
			}
			$api[] = $v;
		}
		//dump($api);
		return json($api);
	}
	/**
	 * [format_dish description]
	 * @param  [type] $list   菜品数组
	 * @param  [type] $dishes 套餐中选中的菜品
	 * @param  [type] $groups 所有分组
	 * @return [type]         [description]
	 */
	protected function format_dish($list,$dishes,$groups){
		$foods = [];

		$d = explode(',',$dishes);
		//套餐中的菜
		$new_dishes = [];
		foreach ($list as $v) {
			if(in_array($v['ID'],$d)) $new_dishes[] = $v;
		}

		foreach ($groups as $v) {
			$items = [];
			$items['name'] = $v;
			$childs = [];
			foreach ($new_dishes as $p) {
				if($p['groups']==$v) $childs[] = $p;
			}
			$items['childs'] = $childs;
			if(empty($childs)) continue;
			$foods[] = $items;
		}
		return $foods;
	}
	function test(){
		//获取套餐分组
		$groups = Db::table('SCD')->field('groups')->distinct(true)->where(['DD'=>0])->select();
		$groups = array_column($groups, 'groups');
		$list = Db::table('SCD')->where(['DD'=>0])->select();
		$dishes = '2,7,8,13,16';
		$re = $this->format_dish($list,$dishes,$groups);
		dump($re);
	}
}