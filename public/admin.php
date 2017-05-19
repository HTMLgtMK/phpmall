<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
//define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
//require __DIR__ . '/../thinkphp/start.php';

// 绑定Home模块到当前入口文件
//$_GET['m'] = 'admin';
//绑定admin模块到当前入口文件
//define('BIND_MODULE','admin');
// 定义应用目录 thinkWeb
//define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
//require __DIR__ . '/../thinkphp/start.php';

//	定义项目路径
define('APP_PATH',	__DIR__	.	'/../application/');
//	加载框架基础文件
require	__DIR__	.	'/../thinkphp/base.php';
//	绑定当前入口文件到admin模块
\think\Route::bind('admin');
//	关闭admin模块的路由
\think\App::route(false);
//	执行应用
\think\App::run()->send();
