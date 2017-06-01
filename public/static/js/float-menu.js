window.onload=function(){
	if(document.querySelector(".float-menu")){
		var plus = document.querySelector(".plus");
		var floatMenu = document.querySelector(".float-menu");
		plus.addEventListener("click", function(){
		floatMenu.className.indexOf("open") > -1?floatMenu.className = "float-menu":floatMenu.className = "float-menu open";
		},false);
	}
}