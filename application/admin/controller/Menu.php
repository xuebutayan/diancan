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
        return $this->fetch();
    }
}
