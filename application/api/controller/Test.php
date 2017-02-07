<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
class Test extends Controller {
	function index(){
		$request = Request::instance();
		$domain = $request->domain();
		$api = [];
		for ($i=1; $i < 15; $i++) {
			$r = ['id'=>$i,'pic'=>$domain.'/dc/i/c'.$i.'.jpg','audio'=>$domain.'/dc/i/m.mp3','video'=>$domain.'/dc/i/test.mp4'];
			$api[] = $r;
		}
		return json($api);
	}
	function test(){
		$type = input('get.type');
		if($type==1){
			$a = file_get_contents('./i/test1.json');
			echo $a;exit;
		}elseif($type==2){
			$a = file_get_contents('./i/test2.json');
			echo $a;exit;
		}
		elseif($type==3){
			$a = file_get_contents('./i/test3.json');
			echo $a;exit;
		}
	}
}