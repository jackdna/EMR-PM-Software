
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

var bvrsn = 0;
window.ondevicemotion = function(event) {
    if (navigator.platform.indexOf("iPad") != -1) {
	var version = 1;
	if (event.acceleration) version = window.devicePixelRatio;
	bvrsn = version;
    }
    window.ondevicemotion = null;
}

//
var oSimpDrw={};

//
function SimpleDrawing(cID){
var me = this;	
var canvas;
var pen;	
var lastPenPoint;
var isIPad;
var touch = "";
var cID=cID || "canvas";
var sig_data=null;
var paint=false,flgsave=false;
this.currentColor="#000000";
var imglen=0;
this.left_pos=0;
this.top_pos=0;	//helps in mouse positioning on canvas if left is set on absolute container.
this.init = function() {			
	canvas = document.getElementById(''+cID);
	sig_data = document.getElementById("sig_data"+cID);
	sig_img = document.getElementById("sig_img"+cID);
	
	if(canvas==null||sig_data==null){		
		alert("Required fields are missing!");	
	}
	
	this.left_pos = canvas.getAttribute("data-left-pos");
	if(typeof(this.left_pos) == "undefined"||this.left_pos==null||this.left_pos==""){ this.left_pos=0; }
	
	this.top_pos = canvas.getAttribute("data-top-pos");
	if(typeof(this.top_pos) == "undefined"||this.top_pos==null||this.top_pos==""){ this.top_pos=0; }
	
	
	pen = canvas.getContext('2d');
	pen.lineCap = "round";
	lastPenPoint = null;
	isIPad = (new RegExp( "iPad", "i" )).test(navigator.userAgent);
	
	if(isIPad){
	canvas.addEventListener('touchstart', this.onTouchStart, false ); 
	canvas.addEventListener('touchmove', this.onTouchMove, false ); 				
	}else{
	canvas.addEventListener('mousedown', this.onTouchStart, false ); 
	canvas.addEventListener('mousemove', this.onTouchMove, false ); 
	canvas.addEventListener('mouseup', this.onMouseUp, false ); 
	canvas.addEventListener('mouseleave', this.onMouseLeave, false ); 
	canvas.addEventListener('mouseout', this.onMouseLeave, false );
	}

	//
	imglen = me.strImgLen();	
	
	//Test --
	var strPrevD=sig_img.value;
	if(strPrevD != ""){
		var imgPrevD = new Image();
		imgPrevD.src =""+strPrevD;		
		imgPrevD.onload = function()
		{
			if(pen){						
				pen.drawImage(imgPrevD, 0,0);
				me.createImage();
			}
		}		
	}else{
		me.createImage();	
	}
	
	//
	
};
	
this.getCanvasLocalCoordinates = function (pageX, pageY ) {
	return({
		x: (pageX - canvas.offsetLeft-this.left_pos),
		y: (pageY - canvas.offsetTop-this.top_pos)
	});
};

this.getTouchEvent = function () {
	return(isIPad ? window.event.targetTouches[ 0 ] : window.event);
};

//Touch Events --
this.onTouchStart = function () {
	var touch = me.getTouchEvent( event );  
	var mX=(touch.pageX) ? touch.pageX : touch.clientX;
	var mY=(touch.pageY) ? touch.pageY : touch.clientY;	
	paint = true; // start painting
	
	var localPosition = me.getCanvasLocalCoordinates(mX,mY);
	lastPenPoint = {x: localPosition.x,y: localPosition.y};
	pen.strokeStyle = me.currentColor; 	
	pen.beginPath();
	pen.moveTo( lastPenPoint.x, lastPenPoint.y );
	flgsave=true;	
};

this.onTouchMove = function () {
	if(paint){
		touch = me.getTouchEvent( event );     
		var mX=(touch.pageX) ? touch.pageX : touch.clientX;
		var mY=(touch.pageY) ? touch.pageY : touch.clientY;	
		
		var localPosition = me.getCanvasLocalCoordinates(mX, mY);
		lastPenPoint = {x: localPosition.x,y: localPosition.y};
		pen.strokeStyle = me.currentColor;		
		//alert(pen.strokeStyle);
		pen.lineTo( lastPenPoint.x, lastPenPoint.y );		 
		// Render the line.
		pen.stroke();
		flgsave=true;	
	}
};

this.onMouseUp = function () {
	if(flgsave) { me.createImage(); }
	paint = false;
};

this.onMouseLeave = function(){
	if(flgsave) { me.createImage(); }
	paint = false;
};


//Touch Events --

/*
init = function () {
	SimpleDrawing();
	canvas.addEventListener('touchstart', onTouchStart, false ); 
	canvas.addEventListener('touchmove', onTouchMove, false ); 				
	//canvas.addEventListener('mousedown', onTouchStart, false ); 
	//canvas.addEventListener('mouseover', onTouchMove, false ); 				
};
*/

this.BlockMove = function (event) {			
	event.preventDefault() ;
};

this.clearCanvas = function (){								
	pen.setTransform(1, 0, 0, 1, 0, 0);
	pen.clearRect(0,0,canvas.width,canvas.height) ;
	sig_data.value=0;
	me.createImage();//
	/*
	clear data in related fields: pending
	*/
	/*
	if(document.getElementById('imageSig').style.display == "inline-block"){
		document.getElementById('imageSig').style.display = "none";
		document.getElementById('canvas').style.display = "inline-block"
		document.getElementById('saveImage').style.display = "inline-block"
	}
	*/
};

this.getImg = function (){	
	if(bvrsn==""&&navigator.platform.indexOf("iPad") != -1){	
		var strData = canvas.toDataURL("image/jpeg"); 
	}else{ //ipad						
		var strData = canvas.toDataURL(); 
	}
	return strData;
}

this.strImgLen = function (){	
	var strData = me.getImg();
	return strData.length;
};

this.createImage = function (){	
	
	var strData = me.getImg();	
	sig_data.value =(strData.length>imglen) ? strData : "" ;	
	//document.sig.submit();				
};

}

function simpdrw_setColor(clr,id){	
	
	//alert(oSimpDrw+" - "+oSimpDrw[id].currentColor+" - "+clr+" - "+id);
	
	oSimpDrw[id].currentColor=clr;	
}