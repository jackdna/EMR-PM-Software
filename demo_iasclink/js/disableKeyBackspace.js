
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

// JavaScript Document
document.onkeydown = keyCatcher;
function keyCatcher() 
{
	var e = event.srcElement.tagName;
	//var f= event.srcElement.tagType;
	//alert(f);
 	//
	if (event.keyCode == 8 && e != "INPUT" && e != "TEXTAREA") 
	{
		event.cancelBubble = true;
		event.returnValue = false;
	}
}