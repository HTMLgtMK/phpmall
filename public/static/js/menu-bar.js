/*
 * 菜单栏的js
 * 主要是用于菜单栏的等分
 */
 function adjustMenuItem(){
	 //多个menu-bar
	 var menubars = $(".menu-bar").toArray();
	 for(var i=0;i<menubars.length;i++){
		 var menubar=menubars[i];
		 var width=$(menubar).outerWidth();
		 //获取菜单项数
		 var items=$(menubar).children().filter(".menu-item").toArray();
		 var num=items.length;
		 width=width/num;
		 for(var j=0;j<num;j++){
			 $(items[j]).outerWidth(width);
		 }
	 }
 }
 $(document).ready(function(){
	adjustMenuItem();
 });
 $(window).resize(function(){
	 adjustMenuItem();
 });