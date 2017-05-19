<?php
namespace app\index\controller;

class Index
{
  public function index()
  {
    echo __DIR__;
    return '<h1>index page</h1>';
  }
}
