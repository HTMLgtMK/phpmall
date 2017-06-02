<?php
namespace app\admin\controller;
use think\Controller;

class Index extends Controller
{
  public function index()
  {
    //return '<h1>admin page</h1>';
    return $this->fetch();
  }
}
