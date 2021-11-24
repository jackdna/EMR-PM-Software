
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

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