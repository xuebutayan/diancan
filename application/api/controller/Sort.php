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
		//获取菜品分类
		$list1 = Db::table('SCL')->order('ID asc')->select();
		//获取所有菜品
		$list2 = Db::table('SCD')->where(['DD'=>0])->select();
		foreach ($list1 as $k => $v) {
			foreach ($list2 as $k2 => $v2) {
				//处理图片
				if($v2['Np']) $pic = $domain.'/dc/i/c'.$v2['ID'].'.jpg';
				else $pic = $domain.'/dc/i/n.jpg';
				$v2['pic'] = $pic;
				//处理价格
				$v2['Ns'] = $v2['Ns']/100;
				$cs = explode(',',trim($v2['Nt']));//所属类目
				if(in_array($v['ID'],$cs)) $v['items'][] = $v2;
			}
			$api[] = $v;
		}
		//dump($api);
		return json($api);
	}
}