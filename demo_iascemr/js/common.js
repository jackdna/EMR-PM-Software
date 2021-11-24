
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/* Event Functions */

// Add an event to the obj given
// event_name refers to the event trigger, without the "on", like click or mouseover
// func_name refers to the function callback when event is triggered

function addEvent(obj,event_name,func_name){
	if(obj){
		if (obj.attachEvent){
			obj.attachEvent("on"+event_name, func_name);
		}else if(obj.addEventListener){
			obj.addEventListener(event_name,func_name,true);
		}else{
			obj["on"+event_name] = func_name;
		}
	}
}

// Removes an event from the object
function removeEvent(obj,event_name,func_name){
	if (obj.detachEvent){
		obj.detachEvent("on"+event_name,func_name);
	}else if(obj.removeEventListener){
		obj.removeEventListener(event_name,func_name,true);
	}else{
		obj["on"+event_name] = null;
	}
}

// Stop an event from bubbling up the event DOM
function stopEvent(evt){
	evt || window.event;
	if (evt.stopPropagation){
		evt.stopPropagation();
		evt.preventDefault();
	}else if(typeof evt.cancelBubble != "undefined"){
		evt.cancelBubble = true;
		evt.returnValue = false;
	}
	return false;
}

// Get the obj that starts the event
function getElement(evt){
	if (window.event){
		return window.event.srcElement;
	}else{
		return evt.currentTarget;
	}
}
// Get the obj that triggers off the event
function getTargetElement(evt){
	if (window.event){
		return window.event.srcElement;
	}else{
		return evt.target;
	}
}
// For IE only, stops the obj from being selected
function stopSelect(obj){
	if (typeof obj.onselectstart != 'undefined'){
		addEvent(obj,"selectstart",function(){ return false;});
	}
}

/*    Caret Functions     */

// Get the end position of the caret in the object. Note that the obj needs to be in focus first
function getCaretEnd(obj){
	if(typeof obj.selectionEnd != "undefined"){
		return obj.selectionEnd;
	}else if(document.selection&&document.selection.createRange){
		var M=document.selection.createRange();
		try{
			var Lp = M.duplicate();
			Lp.moveToElementText(obj);
		}catch(e){
			var Lp=obj.createTextRange();
		}
		Lp.setEndPoint("EndToEnd",M);
		var rb=Lp.text.length;
		if(rb>obj.value.length){
			return -1;
		}
		return rb;
	}
}
// Get the start position of the caret in the object
function getCaretStart(obj){
	if(typeof obj.selectionStart != "undefined"){
		return obj.selectionStart;
	}else if(document.selection&&document.selection.createRange){
		var M=document.selection.createRange();
		try{
			var Lp = M.duplicate();
			Lp.moveToElementText(obj);
		}catch(e){
			var Lp=obj.createTextRange();
		}
		Lp.setEndPoint("EndToStart",M);
		var rb=Lp.text.length;
		if(rb>obj.value.length){
			return -1;
		}
		return rb;
	}
}
// sets the caret position to l in the object
function setCaret(obj,l){
	obj.focus();
	if (obj.setSelectionRange){
		obj.setSelectionRange(l,l);
	}else if(obj.createTextRange){
		m = obj.createTextRange();		
		m.moveStart('character',l);
		m.collapse();
		m.select();
	}
}
// sets the caret selection from s to e in the object
function setSelection(obj,s,e){
	obj.focus();
	if (obj.setSelectionRange){
		obj.setSelectionRange(s,e);
	}else if(obj.createTextRange){
		m = obj.createTextRange();		
		m.moveStart('character',s);
		m.moveEnd('character',e);
		m.select();
	}
}

/*    Escape function   */
String.prototype.addslashes = function(){
	return this.replace(/(["\\\.\|\[\]\^\*\+\?\$\(\)])/g, '\\$1');
}
String.prototype.trim = function () {
    return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
};
/* --- Escape --- */

/* Offset position from top of the screen */
function curTop(obj){
	toreturn = 0;
	while(obj){
		toreturn += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return toreturn;
}
function curLeft(obj){
	toreturn = 0;
	while(obj){
		toreturn += obj.offsetLeft;
		obj = obj.offsetParent;
	}
	return toreturn;
}
/* ------ End of Offset function ------- */

/* Types Function */

// is a given input a number?
function isNumber(a) {
    return typeof a == 'number' && isFinite(a);
}

/* Object Functions */

function replaceHTML(obj,text){
	while(el = obj.childNodes[0]){
		obj.removeChild(el);
	};
	obj.appendChild(document.createTextNode(text));
}

function fireObjectFunctions(objElem)
{
	try{
	if(typeof(objElem.onblur) == "function")
	{
		objElem.onblur();
	}

	if(typeof(objElem.onchange) == "function")
	{
		objElem.onchange();
	}
	
	if(typeof(objElem.onclick) == "object")
	{
		objElem.click();
	}

	}catch(evt){}
}

//function disable the fields
//returns true is checkbox is checked
function dispHiIndex(obj1,obj2)
	{
		var obj = document.getElementsByName(obj1)
		var obj_hi_index = document.getElementById(obj2)
		
		if(obj_hi_index.checked == true)
		{
			for(i=0;i<obj.length;i++)
			{
				obj[i].setAttribute('disabled', '') ;
			}
		}
		else
		{
			for(i=0;i<obj.length;i++)
			{
				obj[i].setAttribute('disabled', 'disabled') ;
			}
		}
	}
	
	function indexEnt(str_mode){
		var evt = window.event;
		if(evt.keyCode == 13){
			if(str_mode == "check"){
				return false;
			}
			var objId = evt.srcElement.id;
			var name = objId.substr(0,objId.length-1);
			
			var entName = parseInt(objId.substr(objId.length-1,objId.length));
			entName = entName+1;
			var setEnt =  name+entName;

			var field = document.getElementById(setEnt);
			try{
				field.focus();
			}catch(err){
				return false;
			}
			evt.cancelBubble = true;
		}
	}
	
	
function loadXMLDoc(XMLname)
{
	var xmlDoc;
	if (window.XMLHttpRequest)
	{
	xmlDoc=new window.XMLHttpRequest();
	xmlDoc.open("GET",XMLname,false);
	xmlDoc.send();
	return xmlDoc.responseXML;
	}
	// IE 5 and IE 6
	else if (ActiveXObject("Microsoft.XMLDOM"))
	{
	xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
	xmlDoc.async=false;
	xmlDoc.load(XMLname);
	return xmlDoc;
	}
	else{
	parser=new DOMParser();
	xmlDoc=parser.parseFromString(XMLname,"text/xml");
	return xmlDoc;
	}
	alert("Error loading document!");
	return null;
}


function readXMLFile(XMLname,ele,hidID,refPhyfax){
	xmlDoc = loadXMLDoc(XMLname);
	var arrRefPhy = new Array();
	var arrRefPhyID = new Array();
	var arrRefPhyFax= new Array();
	fNameTag  = xmlDoc.getElementsByTagName('refphyFName');
	mNameTag  = xmlDoc.getElementsByTagName('refPhyMname');
	lNameTag  = xmlDoc.getElementsByTagName('refphyLName');
	titleTag  = xmlDoc.getElementsByTagName('refPhyTitle');
	phyIDNameTag  = xmlDoc.getElementsByTagName('refphyId');
	phyFaxTag  = xmlDoc.getElementsByTagName('refFax');
	
	for(i=0; i < fNameTag.length; i++){
		fname = fNameTag[i].text;
		mname = mNameTag[i].text;		
		lname = lNameTag[i].text;
		title = titleTag[i].text;	
		phyID = phyIDNameTag[i].text;
		phyFax= phyFaxTag[i].text;
		name = lname+", "+fname+" ";
		if(mname!='')
		name += mname+" ";
		name += title;
		name = name.replace(/\\/gi, ""); 
		//alert(name)
		arrRefPhy.push(name);
		arrRefPhyID.push(phyID);
		arrRefPhyFax.push(phyFax);
	}
	if(hidID != ""  && document.getElementById(refPhyfax)){ //for consult letter fax values 
		document.getElementById(refPhyfax).value="";
		var obj1 = new actb(document.getElementById(ele.id),arrRefPhy,"","",document.getElementById(hidID),arrRefPhyID,",",document.getElementById(refPhyfax),arrRefPhyFax);
	}else if(hidID != ""){
		var obj1 = new actb(document.getElementById(ele.id),arrRefPhy,"","",document.getElementById(hidID),arrRefPhyID,",");
	}else{
		var obj1 = new actb(document.getElementById(ele.id),arrRefPhy);
	}
	
	document.getElementById(ele.id).blur();SetCaretAtEnd(ele);
	//addEvent(document,"mousedown",obj1.actb_penter);
	
}

var old_ref_val = '';
var old_ref_ele = '';
//ser_root = "/R5-Dev/xml/refphy";
var faxfield="";
function loadPhysicians(ele, hidID, serverRoot,faxfield){
	serverRoot = serverRoot || ser_root;
	hidID = hidID || "";
	trimLname = $.trim(ele.value);
	char = trimLname.substring(1,2);
	if(char == ",")
	len = 1;
	else len = 2;
	if(trimLname.length==len && (old_ref_val!=trimLname || old_ref_ele!=ele)){
		old_ref_ele = ele
		old_ref_val = trimLname;
		XMLname = trimLname.substring(0,2)+".xml";
		//XMLname = "ab.xml"
		readXMLFile(serverRoot+"/"+XMLname,ele,hidID,faxfield);
	}
	
}
function SetCaretAtEnd(elem) {
        var elemLen = elem.value.length;
        // For IE Only
        if (document.selection) {
            // Set focus
            elem.focus();
            // Use IE Ranges
            var oSel = document.selection.createRange();
            // Reset position to 0 & then set at end
            oSel.moveStart('character', -elemLen);
            oSel.moveStart('character', elemLen);
            oSel.moveEnd('character', 0);
            oSel.select();
        }
        else if (elem.selectionStart || elem.selectionStart == '0') {
            // Firefox/Chrome
            elem.selectionStart = elemLen;
            elem.selectionEnd = elemLen;
            elem.focus();
        } // if
} // SetCaretAtEnd()
