
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

addEvent(window, 'load', initForm);

var highlight_array = new Array();

function initForm(){
	initializeFocus();
}

// for radio and checkboxes, they have to be cleared manually, so they are added to the
// global array highlight_array so we dont have to loop through the dom every time.  
function initializeFocus(){
	fields = getElementsByClassName(document, "*", "field");
	for(i = 0; i < fields.length; i++) {
		if(fields[i].type == 'radio' || fields[i].type == 'checkbox' || fields[i].type == 'file') {
			fields[i].onclick = function(){clearSafariRadios(); addClassName(this.parentNode.parentNode, "focused", true)};
			fields[i].onfocus = function(){clearSafariRadios(); addClassName(this.parentNode.parentNode, "focused", true)};
			highlight_array.splice(highlight_array.length,0,fields[i]);
		}
		if(fields[i].className.match('addr')){
			fields[i].onfocus = function(){clearSafariRadios();addClassName(this.parentNode.parentNode.parentNode, "focused", true)};
			fields[i].onblur = function(){removeClassName(this.parentNode.parentNode.parentNode, "focused")};
		}
		else {
			fields[i].onfocus = function(){clearSafariRadios();addClassName(this.parentNode.parentNode, "focused", true)};
			fields[i].onblur = function(){removeClassName(this.parentNode.parentNode, "focused")};
		}
	}
}

function clearSafariRadios() {
	for(var i = 0; i < highlight_array.length; i++) {
		if(highlight_array[i].parentNode) {
			removeClassName(highlight_array[i].parentNode.parentNode, 'focused');
		}
	}
}

/*--------------------------------------------------------------------------*/

//http://www.robertnyman.com/2005/11/07/the-ultimate-getelementsbyclassname/
function getElementsByClassName(oElm, strTagName, strClassName){
	var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/\-/g, "\\-");
	var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++){
		oElement = arrElements[i];		
		if(oRegExp.test(oElement.className)){
			arrReturnElements.push(oElement);
		}	
	}
	return (arrReturnElements)
}

//http://www.bigbold.com/snippets/posts/show/2630
function addClassName(objElement, strClass, blnMayAlreadyExist){
   if ( objElement.className ){
      var arrList = objElement.className.split(' ');
      if ( blnMayAlreadyExist ){
         var strClassUpper = strClass.toUpperCase();
         for ( var i = 0; i < arrList.length; i++ ){
            if ( arrList[i].toUpperCase() == strClassUpper ){
               arrList.splice(i, 1);
               i--;
             }
           }
      }
      arrList[arrList.length] = strClass;
      objElement.className = arrList.join(' ');
   }
   else{  
      objElement.className = strClass;
      }
}

//http://www.bigbold.com/snippets/posts/show/2630
function removeClassName(objElement, strClass){
   if ( objElement.className ){
      var arrList = objElement.className.split(' ');
      var strClassUpper = strClass.toUpperCase();
      for ( var i = 0; i < arrList.length; i++ ){
         if ( arrList[i].toUpperCase() == strClassUpper ){
            arrList.splice(i, 1);
            i--;
         }
      }
      objElement.className = arrList.join(' ');
   }
}

//http://ejohn.org/projects/flexible-javascript-events/
function addEvent( obj, type, fn ) {
  if ( obj.attachEvent ) {
    obj["e"+type+fn] = fn;
    obj[type+fn] = function() { obj["e"+type+fn]( window.event ) };
    obj.attachEvent( "on"+type, obj[type+fn] );
  } 
  else{
    obj.addEventListener( type, fn, false );	
  }
}