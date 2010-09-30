function msiehoverfix() {
	var a, pse = document.getElementById("topnav").getElementsByTagName("LI");
	for (a=0; a<pse.length; a++) {
		pse[a].onmouseover=function() {
			this.className+=" hoverfix";
		}
		pse[a].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" hoverfix\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", msiehoverfix);