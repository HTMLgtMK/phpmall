<?php
namespace app\admin\controller;
use think\Controller;
use \think\Request;

class Main extends Controller
{
  public function index()
  {
    //return '<h1>admin page</h1>';
    //$user = request()->param('user');
    //echo $user;
    //return $this->fetch();
    echo $_POST{"user"};
  }
}
