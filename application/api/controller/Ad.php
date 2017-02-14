<?php
namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;

class Ad extends Controller{
	protected $domain;
	function _initialize(){
		$request = Request::instance();
		$this->domain = $request->domain();
	}
	function banner(){
		$list = Db::name('banner_detail')->where(['pid'=>1])->select();
		foreach ($list as &$v) {
			$v['img'] = $this->domain.$v['img'];
		}
		return json($list);
	}
	function ads(){
		$list = Db::name('banner_detail')->where(['pid'=>2])->select();
		foreach ($list as &$v) {
			$v['img'] = $this->domain.$v['img'];
		}
		return json($list);
	}
}