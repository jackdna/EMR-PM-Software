
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

var outT,inT,curid;
var leftPos,widthPos, moveBoth;
function slide(id, c, left, width, right){
	widthPos = -(parseInt(width));	
	var sliderRightOut = top.frames[0].document.getElementById("sliderRightOut").value;
	if(id == 'sliderBarLEFT'){		
		leftPos = parseInt(top.frames[0].document.getElementById(id).style.left);
		if(leftPos < 0){
			curid=id;
			if(inT){
				clearInterval(inT);
			}
			outT = setInterval("moveOutPos2()", 10);				
			top.frames[0].document.getElementById(id).cursor="hand";
			top.frames[0].document.getElementById("imageLeft").innerHTML = '<img src="images/move_back.jpg">';
		}else{
			curid=id;
			if(outT){
				clearInterval(outT);
			}
			inT = setInterval("moveInPos2()", 10);	
			top.frames[0].document.getElementById("imageLeft").innerHTML = '<img src="images/move_forward.jpg">';
		}
	}else{	
		leftPos=parseInt(top.frames[0].document.getElementById(id).style.right);
		if(leftPos < 0){
			curid=id;
			if(inT){
				clearInterval(inT);
			}
			outT = setInterval("moveOutPosRight2()", 15);	
			top.frames[0].document.getElementById(id).cursor="hand";
			top.frames[0].document.getElementById("imageRight").innerHTML = '<img src="images/move_forward.jpg">';
		}else{
			curid=id;
			if(outT){
				clearInterval(outT);
			}
			inT = setInterval("moveInPosRight2()", 15);	
			top.frames[0].document.getElementById("imageRight").innerHTML = '<img src="images/move_back.jpg">';
		}
	}
}

function moveInPos(p, id){
	document.getElementById(id).style.left = p;
}
function moveOutPos(p, id){
	document.getElementById(id).style.left = p;
}

function moveInPos2(){
	if(top.frames[0].document.getElementById("sliderBarLEFT").style.left=='-185px'){
		if(moveBoth == true){
		 	leftPos = top.frames[0].document.getElementById("sliderBarRight").style.right;
			pxPos = leftPos.indexOf('px');
			leftPos = leftPos.substr(0, pxPos);
			widthPos = -255;
			curid = 'sliderBarRight';	
			moveInPosRight2();
		}
	}else{
		if(leftPos >= widthPos){
			var obj = top.frames[0].document.getElementById(curid);
			obj.style.left = parseInt(obj.style.left) - 10 + "px";
			leftPos=parseInt(obj.style.left);
		}else{
			clearInterval(inT);			
		}	
	}
}
function moveOutPos2(){
	if(leftPos < 0){		
		var obj = top.frames[0].document.getElementById(curid);
		obj.style.left = parseInt(obj.style.left) + 10 + "px";
		leftPos=parseInt(obj.style.left);
	}else{
		clearInterval(outT);
	}
}
function moveInPosRight2(){
	if(leftPos >= widthPos){
		var obj = top.frames[0].document.getElementById(curid);
		obj.style.right = parseInt(obj.style.right) - 10 + "px";
		leftPos=parseInt(obj.style.right);
    }else{
		clearInterval(inT);			
	}	
}
function moveOutPosRight2(){
	if(leftPos < 0){
		var obj = top.frames[0].document.getElementById(curid);
		obj.style.right = parseInt(obj.style.right) + 10 + "px";
		leftPos=parseInt(obj.style.right);
	}else{
		clearInterval(outT);
	}
}
// HIDE AND DISPLAY
function showContents(innerC, t, mainC, bar){
	if(bar=="sliderBarLEFT"){
		var lastInnerC = document.getElementById("leftInnerOpen").value;
		var lastMainC = document.getElementById("leftMainOpen").value;
		document.getElementById("leftMainOpen").value = t;
		document.getElementById("leftInnerOpen").value = innerC;
		for(var j = 0; j<lastInnerC; j++){
			if(document.getElementById("innerContent"+lastMainC+''+bar+''+j)){
				document.getElementById("innerContent"+lastMainC+''+bar+''+j).style.display = "none";
			}
		}
	}else{
		var rightlastInnerC = document.getElementById("rightInnerOpen").value;
		var rightlastMainC = document.getElementById("rightMainOpen").value;
		document.getElementById("rightMainOpen").value = t;
		document.getElementById("rightInnerOpen").value = innerC;
		for(var j = 0; j<rightlastInnerC; j++){
			if(document.getElementById("innerContent"+rightlastMainC+''+bar+''+j)){
				document.getElementById("innerContent"+rightlastMainC+''+bar+''+j).style.display = "none";
			}
		}
	}
	for(var i=0; i<innerC; i++){
		document.getElementById("innerContent"+t+''+bar+''+i).style.display = "block";
	}	
}
// HIDE AND DISPLAY
// CODE TO HIDE AND DISPLAY Sliders DONE BY Mamta & Munisha ON BLANK PAGE
function hideSliders(){		
	moveBoth = true;
	leftPos = parseInt(top.frames[0].document.getElementById("sliderBarLEFT").style.left);
	widthPos = -175;
	curid="sliderBarLEFT";
	if(outT){
		clearInterval(outT);				
	}
	inT = setInterval("moveInPos2()", 10);				
	top.frames[0].document.getElementById("imageLeft").innerHTML = '<img src="images/move_forward.jpg">';
	top.frames[0].document.getElementById("imageRight").innerHTML = '<img src="images/move_back.jpg">';
}
function changeColor(setColor){
	top.frames[0].document.getElementById("tabnav").style.background = setColor;
}

// END CODE TO HIDE AND DISPLAY Sliders DONE BY Mamta & Munisha ON BLANK PAGE


//CODE FOR APPLYING BUTTONS(save,cancel,print,save&print and finalize) ON MAIN PAGE
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; 
  document.MM_sr=new Array;
  for(i=0;i<(a.length-2);i+=3)
  	if ((x=MM_findObj(a[i]))!=null){
		document.MM_sr[j++]=x; 
		if(!x.oSrc) 
			x.oSrc=x.src;			
		x.src=a[i+2];
	}
}
//END CODE TO APPLY BUTTONS DONE BY munisha