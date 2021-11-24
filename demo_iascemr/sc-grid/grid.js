
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.


function CLSAnesthesiaGrid(){
	var me = this;
	var canvasObj;
	var ctx;		
	var isIPad = false;		
	var gbEventType = "";
	var gbGridPointStack = new Array();
	var gbArrUnDoReDoStack = new Array();
	var gbArrStackRedo = new Array();
	var arrGridPointDB = new Array();
	var gbIntStackCounter = 0, gbIntUnDoReDoStackCounter = 0, gbIntTextCounter = 0, gbIntGPDCounter = 0;
	var mouseX = 0, mouseY = 0;
	var mouseXReblock = 0, mouseYReblock = 0;		
	var mousePressed = false;
	var intImgDwonTriId = 0, intImgUpTriId = 0, intImgCirWtIntColorId = 0, intImgCirWtOutColorId = 0;
	var gbArrDownTirangle = new Array(), gbArrUpTirangle = new Array(), gbArrCirWithIntColor = new Array(), gbArrCirWtOutColor = new Array();
	var objLineReblock = null;
	var lineReblockTime = null;
	var gbArrDownTirTime = new Array(), gbArrUpTirTime = new Array(), gbArrCirWithIntColTime = new Array(), gbArrCirWtOutColTime = new Array();	
	var gbBlDragStart = false;
	var gbDragImageId = "";		
	var gbImgDnTri, gbImgUpTri, gbImgWtIntColorCir, gbImgWtOutIntColorCir, gbImgEraser, gbImgReblock, gbImgUndo, gbImgRedo;
	var gbBlDoDrag = false;
	var intInterval = 50;
	this.JSIsArray = function(obj) {
		if (obj == null){
			return false;
		}
		else{
			return obj.constructor == Array;
		}
	};
	this.blockMove = function(e) {
		// Tell Safari not to move the window.
		e.preventDefault() ;
	};
	this.init = function(){
		/*var gridTime = document.getElementById('bp_temp6').value;
		if(gridTime != ""){
			this.calculateTime('bp_temp6');
		}*/
		var divCanvasObj = document.getElementById("divCanvas");
		divCanvasObj.addEventListener("touchmove", this.blockMove, false );
		canvasObj = document.getElementById("cCanvas");
		canvasObj.addEventListener("mousedown", this.drawGridPoints, false ); 
		canvasObj.addEventListener("mouseup", this.onMouseUp, false ); 
		canvasObj.addEventListener("mousemove", this.onMouseMove, false ); 
		
		canvasObj.addEventListener("touchstart", this.drawGridPoints, false ); 
		canvasObj.addEventListener("touchend", this.onTouchEnd, false ); 
		canvasObj.addEventListener("touchmove", this.onTouchMove, false ); 						
		
		/*if(document.getElementById("chbx_ReblockId")){
			document.getElementById("chbx_ReblockId").addEventListener("mousedown", this.drawReblock, false);
		}*/
					
		isIPad = (new RegExp( "iPad", "i" )).test(navigator.userAgent);
		var image = new Image();
		image.src = "sc-grid/images/bgTest.jpg";
		canvasObj.width = image.width;
		canvasObj.height = image.height;			
		ctx = canvasObj.getContext("2d");
		
		gbImgDnTri = new Image();
		gbImgDnTri.src = "sc-grid/images/TDn.png";
		
		gbImgUpTri = new Image();
		gbImgUpTri.src = "sc-grid/images/TUp.png";
		
		gbImgWtIntColorCir = new Image();
		gbImgWtIntColorCir.src = "sc-grid/images/CFill.bak.png";
		
		gbImgWtOutIntColorCir = new Image();
		gbImgWtOutIntColorCir.src = "sc-grid/images/CDr.png";
		
		gbImgEraser = new Image();
		gbImgEraser.src = "sc-grid/images/eraser.gif";
		
		gbImgReblock = new Image();
		gbImgReblock.style.height = "15";
		gbImgReblock.style.width = "1";
		gbImgReblock.src = "sc-grid/images/red_dot.png";
		
		gbImgUndo = new Image();
		gbImgUndo.src = "sc-grid/images/undo-icon.png";
		
		gbImgRedo = new Image();
		gbImgRedo.src = "sc-grid/images/Redo-icon.png";
		var arrGridPointDB = new Array;
		var strGridPointDB = document.getElementById("hidAnesthesiaGridData").value;
		arrGridPointDB = strGridPointDB.split("~");
		if((this.JSIsArray(arrGridPointDB) == true) && (arrGridPointDB.length > 0)){
			this.drawDBData(arrGridPointDB);
		}
	};
	this.drawDBData = function(arrGridPointDB){
		//alert(arrGridPointDB);
		for(var a = 0; a < arrGridPointDB.length; a++){
			//alert(arrGridPointDB[a]);
			if(arrGridPointDB[a]){
				var arrTemp = new Array();
				arrTemp = arrGridPointDB[a].split(",");
				//alert(arrTemp[6]);
				var drawEventDB = arrTemp[6];
				var drawXDB = arrTemp[1];
				var drawYDB = arrTemp[2];								
				var drawImgIdDB = arrTemp[5];	
				var opDB = arrTemp[7];		
				//alert(drawEventDB+"-"+drawXDB+"-"+drawYDB+"-"+drawImgIdDB+"-"+opDB);				
				var arrDrwaingPoint = new Array(drawXDB, drawYDB, drawImgIdDB, opDB);
				switch(drawEventDB){
					case "funDrawDownTirangle":					
						//Down Arrow						
						me.drawDownTirangle('', arrDrwaingPoint, true);
					break;
					case "funDrawUpTirangle":					
						//Up Arrow
						me.drawUpTirangle('', arrDrwaingPoint, true);
					break;					
					case "funCircleWtInterColor":					
						//Circle with internal color
						me.circleWtInterColor('', arrDrwaingPoint, true);
					break;	
					case "funCircleWtOutInterColor":					
						//Circle with out internal color
						me.circleWtOutInterColor('', arrDrwaingPoint, true);
					break;
					case "funDrawTextCan":					
						//Text
						me.drawTextCan('', arrDrwaingPoint, true);
					break;
					case "funDrawReblock":					
						//Reblock
						me.drawReblock('', arrDrwaingPoint, true);
					break;
				}
			}
		}
	};
	this.drawDownTirangle = function(e, arrDrwaingPoint, blDb){
		arrDrwaingPoint = arrDrwaingPoint || new Array();			
		blDb = blDb || false;
		//Down Arrow
		if(typeof(e) == "object"){			
			this.getEvt(e);
			var strChkPostionAvail = "NO";													
			var dnTri = new Image();
			var imgId = "imgDwonTri-"+intImgDwonTriId;			
			dnTri.id = imgId;			
			dnTri.src = gbImgDnTri.src;						
			strChkPostionAvail = this.chkPostionAvail(dnTri.src, mouseX, mouseY);			
			if(strChkPostionAvail == "YES"){	
				var path = dnTri.src;
				var temp = intImgDwonTriId;
				gbArrDownTirangle[temp] = new DragImage(path, mouseX, mouseY, imgId);
				//alert(gbArrDownTirangle);					
				gbArrDownTirTime[temp] = setInterval(function() {
					if((gbEventType == "funDrawDownTirangle") || (gbEventType == "drag")){
						gbArrDownTirangle[temp].upIm = dnTri.src;
						gbArrDownTirangle[temp].upId = imgId;
						gbArrDownTirangle[temp].update();			
					}
				}, intInterval);
				this.mintaneGridPointStack(dnTri.src, mouseX, mouseY, dnTri.width, dnTri.height, imgId, "funDrawDownTirangle");
				this.mintaneUnDoReDoStack("funDrawDownTirangle", mouseX, mouseY, dnTri.width, dnTri.height, imgId);
				intImgDwonTriId++;
			}
			else if(strChkPostionAvail == "NO"){
				alert("Image allready in postion!");
			}
		}
		else if( (blDb == true) && (this.JSIsArray(arrDrwaingPoint) == true) ){
			var dnTri = new Image();				
			var drawDBX = arrDrwaingPoint[0];			
			var drawDBY = arrDrwaingPoint[1];			
			var imgId = arrDrwaingPoint[2];				
			dnTri.id = imgId;			
			dnTri.src = gbImgDnTri.src;										
			var path = dnTri.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			//alert(drawDBX+"--"+drawDBY+"--"+imgId+"--"+intImageId)
			gbArrDownTirangle[intImageId] = new DragImage(path, drawDBX, drawDBY, imgId, 14, 14);
			//alert(gbArrDownTirangle);
			gbArrDownTirTime[intImageId] = setInterval(function() {
				if((gbEventType == "funDrawDownTirangle") || (gbEventType == "drag") || (gbEventType == "")){
					gbArrDownTirangle[intImageId].upIm = dnTri.src;
					gbArrDownTirangle[intImageId].upId = imgId;
					gbArrDownTirangle[intImageId].update();			
				}
			}, intInterval);
			this.mintaneGridPointStack(dnTri.src, drawDBX, drawDBY, dnTri.width, dnTri.height, imgId, "funDrawDownTirangle");
			this.mintaneUnDoReDoStack("funDrawDownTirangle", drawDBX, drawDBY, dnTri.width, dnTri.height, imgId);
			intImgDwonTriId = intImageId;
			intImgDwonTriId++;
		}
		else if( (blDb == false) && (this.JSIsArray(arrDrwaingPoint) == true) ){			
			var dnTri = new Image();				
			var drawX = arrDrwaingPoint[0];			
			var drawY = arrDrwaingPoint[1];			
			var imgId = arrDrwaingPoint[4];				
			dnTri.id = imgId;			
			dnTri.src = gbImgDnTri.src;										
			var path = dnTri.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			gbArrDownTirangle[intImageId] = new DragImage(path, drawX, drawY, imgId);
			//alert(gbEventType);
			gbArrDownTirTime[intImageId] = setInterval(function() {
				if((gbEventType == "funDrawDownTirangle") || (gbEventType == "drag") || (gbEventType == "redo")){
					gbArrDownTirangle[intImageId].upIm = dnTri.src;
					gbArrDownTirangle[intImageId].upId = imgId;
					gbArrDownTirangle[intImageId].update();			
				}
			}, intInterval);
			this.mintaneGridPointStack(dnTri.src, drawX, drawY, dnTri.width, dnTri.height, imgId, "funDrawDownTirangle");
			this.mintaneUnDoReDoStack("funDrawDownTirangle", drawX, drawY, dnTri.width, dnTri.height, imgId);
		}						
	};
	this.drawUpTirangle = function(e, arrDrwaingPoint, blDb){
		//Up Arrow
		arrDrwaingPoint = arrDrwaingPoint || new Array();
		blDb = blDb || false;
		if(typeof(e) == "object"){
			this.getEvt(e);
			var strChkPostionAvail = "NO";									
			var upTri = new Image();
			var imgId = "imgUpTri-"+intImgUpTriId;			
			upTri.id = imgId;			
			upTri.src = gbImgUpTri.src;			
			strChkPostionAvail = this.chkPostionAvail(upTri.src, mouseX, mouseY, upTri.width, upTri.height);			
			if(strChkPostionAvail == "YES"){	
				var path = upTri.src;
				var temp = intImgUpTriId;
				gbArrUpTirangle[temp] = new DragImage(path, mouseX, mouseY, imgId);
				//alert(gbArrUpTirangle[temp]);
				gbArrUpTirTime[temp] = setInterval(function() {
					if((gbEventType == "funDrawUpTirangle") || (gbEventType == "drag")){
						gbArrUpTirangle[temp].upIm = upTri.src;
						gbArrUpTirangle[temp].upId = imgId;
						gbArrUpTirangle[temp].update();			
					}
				}, intInterval);			
				this.mintaneGridPointStack(upTri.src, mouseX, mouseY, upTri.width, upTri.height, imgId, "funDrawUpTirangle");
				this.mintaneUnDoReDoStack("funDrawUpTirangle", mouseX, mouseY, upTri.width, upTri.height, imgId);
				intImgUpTriId++;
			}
			else if(strChkPostionAvail == "NO"){
				alert("Image allready in postion!");
			}
		}
		else if( (blDb == true) && (this.JSIsArray(arrDrwaingPoint) == true) ){
			var upTri = new Image();
			var drawDBX = arrDrwaingPoint[0];			
			var drawDBY = arrDrwaingPoint[1];			
			var imgId = arrDrwaingPoint[2];	
			upTri.id = imgId;			
			upTri.src = gbImgUpTri.src;	
			var path = upTri.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			gbArrUpTirangle[intImageId] = new DragImage(path, drawDBX, drawDBY, imgId, 14, 14);
			//alert(gbArrUpTirangle[intImageId]);
			gbArrUpTirTime[intImageId] = setInterval(function() {
				if((gbEventType == "funDrawUpTirangle") || (gbEventType == "drag") || (gbEventType == "")){
					gbArrUpTirangle[intImageId].upIm = upTri.src;
					gbArrUpTirangle[intImageId].upId = imgId;
					gbArrUpTirangle[intImageId].update();			
				}
			}, intInterval);
			this.mintaneGridPointStack(upTri.src, drawDBX, drawDBY, upTri.width, upTri.height, imgId, "funDrawUpTirangle");
			this.mintaneUnDoReDoStack("funDrawUpTirangle", drawDBX, drawDBY, upTri.width, upTri.height, imgId);
			intImgUpTriId = intImageId;
			intImgUpTriId++;
		}
		else if( (blDb == false) && (this.JSIsArray(arrDrwaingPoint) == true) ){	
			var upTri = new Image();
			var drawX = arrDrwaingPoint[0];
			var drawY = arrDrwaingPoint[1];
			var imgId = arrDrwaingPoint[4];
			upTri.id = imgId;			
			upTri.src = gbImgUpTri.src;	
			var path = upTri.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			gbArrUpTirangle[intImageId] = new DragImage(path, drawX, drawY, imgId);
			//alert(gbArrUpTirangle[intImageId]);
			gbArrUpTirTime[intImageId] = setInterval(function() {
				if((gbEventType == "funDrawUpTirangle") || (gbEventType == "drag") || (gbEventType == "redo")){
					gbArrUpTirangle[intImageId].upIm = upTri.src;
					gbArrUpTirangle[intImageId].upId = imgId;
					gbArrUpTirangle[intImageId].update();			
				}
			}, intInterval);
			this.mintaneGridPointStack(upTri.src, mouseX, drawY, upTri.width, upTri.height, imgId, "funDrawUpTirangle");
			this.mintaneUnDoReDoStack("funDrawUpTirangle", drawX, drawY, upTri.width, upTri.height, imgId);
		}
	};
	this.circleWtInterColor = function(e, arrDrwaingPoint, blDb){			
		//Circle with Color
		arrDrwaingPoint = arrDrwaingPoint || new Array();
		blDb = blDb || false;
		if(typeof(e) == "object"){
			this.getEvt(e);
			var strChkPostionAvail = "NO";									
			var cirWtIntColorTri = new Image();
			var imgId = "imgCirWithintColor-"+intImgCirWtIntColorId;			
			cirWtIntColorTri.id = imgId;			
			cirWtIntColorTri.src = gbImgWtIntColorCir.src;			
			strChkPostionAvail = this.chkPostionAvail(cirWtIntColorTri.src, mouseX, mouseY, cirWtIntColorTri.width, cirWtIntColorTri.height);
			if(strChkPostionAvail == "YES"){	
				var path = cirWtIntColorTri.src;
				var temp = intImgCirWtIntColorId;
				gbArrCirWithIntColor[temp] = new DragImage(path, mouseX, mouseY, imgId);
				//alert(gbArrCirWithIntColor[temp]);
				gbArrCirWithIntColTime[temp] = setInterval(function() {
					if((gbEventType == "funCircleWtInterColor") || (gbEventType == "drag")){
						gbArrCirWithIntColor[temp].upIm = cirWtIntColorTri.src;
						gbArrCirWithIntColor[temp].upId = imgId;
						gbArrCirWithIntColor[temp].update();			
					}
				}, intInterval);			
				this.mintaneGridPointStack(cirWtIntColorTri.src, mouseX, mouseY, cirWtIntColorTri.width, cirWtIntColorTri.height, imgId, "funCircleWtInterColor");
				this.mintaneUnDoReDoStack("funCircleWtInterColor", mouseX, mouseY, cirWtIntColorTri.width, cirWtIntColorTri.height, imgId);
				intImgCirWtIntColorId++;
			}
			else if(strChkPostionAvail == "NO"){
				alert("Image allready in postion!");
			}
		}
		else if( (blDb == true) && (this.JSIsArray(arrDrwaingPoint) == true) ){
			var cirWtIntColorTri = new Image();
			var drawDBX = arrDrwaingPoint[0];			
			var drawDBY = arrDrwaingPoint[1];			
			var imgId = arrDrwaingPoint[2];	
			cirWtIntColorTri.id = imgId;			
			cirWtIntColorTri.src = gbImgWtIntColorCir.src;							
			var path = cirWtIntColorTri.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			gbArrCirWithIntColor[intImageId] = new DragImage(path, drawDBX, drawDBY, imgId, 14, 14);
			//alert(gbArrCirWithIntColor[intImageId]);
			gbArrCirWithIntColTime[intImageId] = setInterval(function() {
				if((gbEventType == "funCircleWtInterColor") || (gbEventType == "drag") || (gbEventType == "")){
					gbArrCirWithIntColor[intImageId].upIm = cirWtIntColorTri.src;
					gbArrCirWithIntColor[intImageId].upId = imgId;
					gbArrCirWithIntColor[intImageId].update();			
				}
			}, intInterval);
			this.mintaneGridPointStack(cirWtIntColorTri.src, drawDBX, drawDBY, cirWtIntColorTri.width, cirWtIntColorTri.height, imgId, "funCircleWtInterColor");
			this.mintaneUnDoReDoStack("funCircleWtInterColor", drawDBX, drawDBY, cirWtIntColorTri.width, cirWtIntColorTri.height, imgId);
			intImgCirWtIntColorId = intImageId;
			intImgCirWtIntColorId++;
		}
		else if( (blDb == false) && (this.JSIsArray(arrDrwaingPoint) == true) ){	
			var cirWtIntColorTri = new Image();
			var drawX = arrDrwaingPoint[0];
			var drawY = arrDrwaingPoint[1];
			var imgId = arrDrwaingPoint[4];
			cirWtIntColorTri.id = imgId;			
			cirWtIntColorTri.src = gbImgWtIntColorCir.src;							
			var path = cirWtIntColorTri.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			gbArrCirWithIntColor[intImageId] = new DragImage(path, drawX, drawY, imgId);
			//alert(gbArrCirWithIntColor[intImageId]);
			gbArrCirWithIntColTime[intImageId] = setInterval(function() {
				if((gbEventType == "funCircleWtInterColor") || (gbEventType == "drag") || (gbEventType == "redo")){
					gbArrCirWithIntColor[intImageId].upIm = cirWtIntColorTri.src;
					gbArrCirWithIntColor[intImageId].upId = imgId;
					gbArrCirWithIntColor[intImageId].update();			
				}
			}, intInterval);
			this.mintaneGridPointStack(cirWtIntColorTri.src, drawX, drawY, cirWtIntColorTri.width, cirWtIntColorTri.height, imgId, "funCircleWtInterColor");
			this.mintaneUnDoReDoStack("funCircleWtInterColor", drawX, drawY, cirWtIntColorTri.width, cirWtIntColorTri.height, imgId);
		}
	};	
	this.circleWtOutInterColor = function(e, arrDrwaingPoint, blDb){
		//Circle with out color
		arrDrwaingPoint = arrDrwaingPoint || new Array();
		blDb = blDb || false;
		if(typeof(e) == "object"){		
			this.getEvt(e);
			var strChkPostionAvail = "NO";									
			var cirWtOutColor = new Image();
			var imgId = "imgCirWtOutColor-"+intImgCirWtOutColorId;			
			cirWtOutColor.id = imgId;			
			cirWtOutColor.src = gbImgWtOutIntColorCir.src;			
			strChkPostionAvail = this.chkPostionAvail(cirWtOutColor.src, mouseX, mouseY, cirWtOutColor.width, cirWtOutColor.height);
			if(strChkPostionAvail == "YES"){	
				var path = cirWtOutColor.src;
				var temp = intImgCirWtOutColorId;
				gbArrCirWtOutColor[temp] = new DragImage(path, mouseX, mouseY, imgId);
				//alert(gbArrCirWithIntColor[temp]);
				gbArrCirWtOutColTime[temp] = setInterval(function() {
					if((gbEventType == "funCircleWtOutInterColor") || (gbEventType == "drag")){
						gbArrCirWtOutColor[temp].upIm = cirWtOutColor.src;
						gbArrCirWtOutColor[temp].upId = imgId;
						gbArrCirWtOutColor[temp].update();
					}
				}, intInterval);			
				this.mintaneGridPointStack(cirWtOutColor.src, mouseX, mouseY, cirWtOutColor.width, cirWtOutColor.height, imgId, "funCircleWtOutInterColor");
				this.mintaneUnDoReDoStack("funCircleWtOutInterColor", mouseX, mouseY, cirWtOutColor.width, cirWtOutColor.height, imgId);
				intImgCirWtOutColorId++;
			}
			else if(strChkPostionAvail == "NO"){
				alert("Image allready in postion!");
			}
		}
		else if( (blDb == true) && (this.JSIsArray(arrDrwaingPoint) == true) ){
			var cirWtOutColor = new Image();
			var drawDBX = arrDrwaingPoint[0];			
			var drawDBY = arrDrwaingPoint[1];			
			var imgId = arrDrwaingPoint[2];			
			cirWtOutColor.id = imgId;			
			cirWtOutColor.src = gbImgWtOutIntColorCir.src;
			var path = cirWtOutColor.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			gbArrCirWtOutColor[intImageId] = new DragImage(path, drawDBX, drawDBY, imgId, 14, 14);
			//alert(gbArrCirWithIntColor[intImageId]);
			gbArrCirWtOutColTime[intImageId] = setInterval(function() {
				if((gbEventType == "funCircleWtOutInterColor") || (gbEventType == "drag") || (gbEventType == "")){
					gbArrCirWtOutColor[intImageId].upIm = cirWtOutColor.src;
					gbArrCirWtOutColor[intImageId].upId = imgId;
					gbArrCirWtOutColor[intImageId].update();			
				}
			}, intInterval);	
			this.mintaneGridPointStack(cirWtOutColor.src, drawDBX, drawDBY, cirWtOutColor.width, cirWtOutColor.height, imgId, "funCircleWtOutInterColor");
			this.mintaneUnDoReDoStack("funCircleWtOutInterColor", drawDBX, drawDBY, cirWtOutColor.width, cirWtOutColor.height, imgId);
			intImgCirWtOutColorId = intImageId;
			intImgCirWtOutColorId++;
		}
		else if( (blDb == false) && (this.JSIsArray(arrDrwaingPoint) == true) ){				
			var cirWtOutColor = new Image();
			var drawX = arrDrwaingPoint[0];
			var drawY = arrDrwaingPoint[1];
			var imgId = arrDrwaingPoint[4];			
			cirWtOutColor.id = imgId;			
			cirWtOutColor.src = gbImgWtOutIntColorCir.src;
			var path = cirWtOutColor.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			gbArrCirWtOutColor[intImageId] = new DragImage(path, drawX, drawY, imgId);
			//alert(gbArrCirWithIntColor[intImageId]);
			gbArrCirWtOutColTime[intImageId] = setInterval(function() {
				if((gbEventType == "funCircleWtOutInterColor") || (gbEventType == "drag") || (gbEventType == "redo")){
					gbArrCirWtOutColor[intImageId].upIm = cirWtOutColor.src;
					gbArrCirWtOutColor[intImageId].upId = imgId;
					gbArrCirWtOutColor[intImageId].update();			
				}
			}, intInterval);
			this.mintaneGridPointStack(cirWtOutColor.src, drawX, drawY, cirWtOutColor.width, cirWtOutColor.height, imgId, "funCircleWtOutInterColor");
			this.mintaneUnDoReDoStack("funCircleWtOutInterColor", drawX, drawY, cirWtOutColor.width, cirWtOutColor.height, imgId);
		}
	};		
	this.drawTextCan = function(e, arrDrwaingPoint, blDb){
		//Text
		arrDrwaingPoint = arrDrwaingPoint || new Array();
		blDb = blDb || false;
		if(typeof(e) == "object"){		
			this.getEvt(e);
			var x = mouseX;
			var y = mouseY;
			var reply = prompt("Please Enter Your Text", "");
			ctx.font = "bold 12px Verdana";						
			ctx.fillStyle = '#060000';			
			ctx.fillText(reply, x, y);
			ctx.fill();
			ctx.stroke();
			this.mintaneGridPointStack("funDrawTextCan", x, y, reply.length * 12, 14, gbIntTextCounter, "funDrawTextCan", reply);
			this.mintaneUnDoReDoStack("funDrawTextCan", x, y, reply.length * 12, 14, gbIntTextCounter, reply);
			gbIntTextCounter++;
		}
		else if( (blDb == true) && (this.JSIsArray(arrDrwaingPoint) == true) ){
			var drawDBX = arrDrwaingPoint[0];
			var drawDBY = arrDrwaingPoint[1];
			var textId = arrDrwaingPoint[2];			
			var reply = arrDrwaingPoint[3];					
			ctx.font = "bold 12px Verdana";						
			ctx.fillStyle = '#060000';			
			ctx.fillText(reply, drawDBX, drawDBY);
			ctx.fill();
			ctx.stroke();
			this.mintaneGridPointStack("funDrawTextCan", drawDBX, drawDBY, reply.length * 12, 14, intImageId, "funDrawTextCan", reply);
			this.mintaneUnDoReDoStack("funDrawTextCan", drawDBX, drawDBY, reply.length * 12, 14, intImageId, reply);
			gbIntTextCounter = intImageId;
			gbIntTextCounter++;
		}
		else if( (blDb == false) && (this.JSIsArray(arrDrwaingPoint) == true) ){
			var drawX = arrDrwaingPoint[0];
			var drawY = arrDrwaingPoint[1];
			var textId = arrDrwaingPoint[4];			
			var reply = arrDrwaingPoint[5];			
			ctx.font = "bold 12px Verdana";						
			ctx.fillStyle = '#060000';			
			ctx.fillText(reply, drawX, drawY);
			ctx.fill();
			ctx.stroke();
			this.mintaneGridPointStack("funDrawTextCan", drawX, drawY, reply.length * 12, 14, textId, "funDrawTextCan", reply);				
			this.mintaneUnDoReDoStack("funDrawTextCan", drawX, drawY, reply.length * 12, 14, textId, reply);
		}
	};
	this.drawReblock = function(e, arrDrwaingPoint, blDb){
		//Draw Reblock Line
		var tempEventType = gbEventType;
		if((gbEventType != "drag") || (gbEventType == "") || (gbEventType != "redo")){
			gbEventType = "reblock";
		}
		arrDrwaingPoint = arrDrwaingPoint || new Array();
		blDb = blDb || false;
		if(typeof(e) == "object"){					
			me.getEvt(e);
			var y = 1;
			var strChkPostionAvail = "NO";
			var lineReblock = new Image();
			var imgId = "imgLineReblock-0";			
			lineReblock.id = imgId;			
			lineReblock.src = gbImgReblock.src;
			lineReblock.height = canvasObj.height;
			lineReblock.width = "3";
			var x = me.calculateReblockTime('bp_temp6') + 27;
			strChkPostionAvail = me.chkPostionAvail(lineReblock.src, x, y, lineReblock.width, lineReblock.height);
			if(strChkPostionAvail == "YES"){	
				var path = lineReblock.src;				
				objLineReblock = new DragReblock(path, x, y, imgId, lineReblock.width, lineReblock.height, "reblock");				
				lineReblockTime = setInterval(function() {
					//if((gbEventType == "reblock") || (gbEventType == "drag")){
						//alert(mouseXReblock +"--"+ y +"--"+ gbEventType);
						objLineReblock.upIm = lineReblock.src;
						objLineReblock.upId = imgId;
						objLineReblock.updateReblock();			
					//}
				}, intInterval);			
				me.mintaneGridPointStack(lineReblock.src, x, y, lineReblock.width, lineReblock.height, imgId, "funDrawReblock");
				me.mintaneUnDoReDoStack("funDrawReblock", x, y, lineReblock.width, lineReblock.height, imgId);
			}
			else if(strChkPostionAvail == "NO"){
				alert("Image allready in postion!");
			}
		}
		else if( (blDb == true) && (this.JSIsArray(arrDrwaingPoint) == true) ){
			var lineReblock = new Image();
			var drawDBX = arrDrwaingPoint[0];
			var drawDBY = arrDrwaingPoint[1];
			var imgId = arrDrwaingPoint[2];			
			lineReblock.id = imgId;			
			lineReblock.src = gbImgReblock.src;				
			lineReblock.height = canvasObj.height;
			lineReblock.width = "3";				
			var path = lineReblock.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			//alert(path+"--"+drawDBX+"--"+drawDBY+"--"+imgId+"--"+lineReblock.width+"--"+lineReblock.height);
			objLineReblock = new DragReblock(path, drawDBX, drawDBY, imgId, lineReblock.width, lineReblock.height, "reblock");
			//alert(objLineReblock);
			lineReblockTime = setInterval(function() {
				if((gbEventType == "reblock") || (gbEventType == "drag") || (gbEventType == "")){
					objLineReblock.upIm = lineReblock.src;
					objLineReblock.upId = imgId;
					objLineReblock.updateReblock();			
				}
			}, intInterval);	
			this.mintaneGridPointStack(lineReblock.src, drawDBX, drawDBY, lineReblock.width, lineReblock.height, imgId, "funDrawReblock");
			this.mintaneUnDoReDoStack("funDrawReblock", drawDBX, drawDBY, lineReblock.width, lineReblock.height, imgId);
			intImgCirWtOutColorId = intImageId;
			intImgCirWtOutColorId++;
		}
		else if( (blDb == false) && (this.JSIsArray(arrDrwaingPoint) == true) ){					
			var lineReblock = new Image();
			var drawX = arrDrwaingPoint[0];
			var drawY = arrDrwaingPoint[1];
			var imgId = arrDrwaingPoint[4];			
			lineReblock.id = imgId;			
			lineReblock.src = gbImgReblock.src;				
			lineReblock.height = canvasObj.height;
			lineReblock.width = "3";				
			var path = lineReblock.src;
			var arrTemp = imgId.split("-");
			var intImageId = parseInt(arrTemp[1]);
			//alert(path+"--"+drawX+"--"+drawY+"--"+imgId+"--"+lineReblock.width+"--"+lineReblock.height);
			objLineReblock = new DragReblock(path, drawX, drawY, imgId, lineReblock.width, lineReblock.height, "reblock");
			//alert(objLineReblock);
			lineReblockTime = setInterval(function() {
				if((gbEventType == "reblock") || (gbEventType == "drag") || (gbEventType == "redo")){
					objLineReblock.upIm = lineReblock.src;
					objLineReblock.upId = imgId;
					objLineReblock.updateReblock();			
				}
			}, intInterval);
			this.mintaneGridPointStack(lineReblock.src, drawX, drawY, lineReblock.width, lineReblock.height, imgId, "funDrawReblock");
			this.mintaneUnDoReDoStack("funDrawReblock", drawX, drawY, lineReblock.width, lineReblock.height, imgId);
		}
		gbEventType = tempEventType;
	};
	this.clear = function(left, top, width, height) {
		ctx.clearRect(left, top, width, height);			
	}
	this.doDrag = function(e){
		this.getEvt(e);
		gbBlDoDrag = true;
	};
	this.getEvt = function(e){
		if(isIPad == false){			
			this.onMouseDown(e);
		}			
		else{
			this.onTouchStart(e);
		}
	};
	this.chkPostionAvail = function(imageName, x, y, width, height, arrImageId){
		var returnVal = "";
		if(gbGridPointStack.length > 0){
			for(a = 0; a < gbGridPointStack.length; a++){
				//alert(typeof(gbGridPointStack[a]));
				if((gbGridPointStack[a]) && (this.JSIsArray(gbGridPointStack[a]) == true)){
					if((gbGridPointStack[a][0] == imageName) && (gbGridPointStack[a][1] == x) && (gbGridPointStack[a][2] == y)){					
						returnVal = "NO";
					}
					else if(gbGridPointStack[a][0] == imageName){
						//alert("DRAG");
						if(isIPad == false){
							var left = gbGridPointStack[a][1];
							var right = gbGridPointStack[a][1] + gbGridPointStack[a][3];
							var top = gbGridPointStack[a][2];
							var bottom = gbGridPointStack[a][2] + gbGridPointStack[a][4];
							if ((x < right) && (x > left) && (y < bottom) && (y > top)){
								returnVal = "DRAG";
							}
						}
						if(isIPad == true){
							var left = gbGridPointStack[a][1] - gbGridPointStack[a][3];					
							var right = gbGridPointStack[a][1] + gbGridPointStack[a][3];
							var top = gbGridPointStack[a][2] - gbGridPointStack[a][4];
							var bottom = gbGridPointStack[a][2] + gbGridPointStack[a][4];
							if ((x < right) && (x > left) && (y < bottom) && (y > top)){
								returnVal = "DRAG";
							}
						}
					}
					else{
						returnVal = "YES";
					}	
				}
			}
		}
		else{
			returnVal = "YES";
		}
		//alert(returnVal);
		return (returnVal) ? returnVal : "YES";
	};	
	this.updateGridPointStack = function(imageName, x, y, imageId){			
		for(var a = 0; a < gbGridPointStack.length; a++){	
			if((gbGridPointStack[a]) && (this.JSIsArray(gbGridPointStack[a]) == true)){							
				if((gbGridPointStack[a][0] == imageName) && (gbGridPointStack[a][5] == imageId)){
					gbGridPointStack[a][1] = x;
					gbGridPointStack[a][2] = y;
				}
			}
		}
		for(var a = 0; a < gbArrUnDoReDoStack.length; a++){				
			if((gbArrUnDoReDoStack[a])  && (this.JSIsArray(gbArrUnDoReDoStack[a]) == true)){
				if((gbArrUnDoReDoStack[a][5] == imageId)){
					gbArrUnDoReDoStack[a][1] = x;
					gbArrUnDoReDoStack[a][2] = y;
				}
			}
		}
	};
	this.mintaneGridPointStack = function(drawImage, x, y, width, height, imageId, drawEvent, op){
		op = op || "";
		gbGridPointStack[gbIntStackCounter] = new Array(8);
		gbGridPointStack[gbIntStackCounter][0] = drawImage;
		gbGridPointStack[gbIntStackCounter][1] = x;
		gbGridPointStack[gbIntStackCounter][2] = y;
		gbGridPointStack[gbIntStackCounter][3] = width;
		gbGridPointStack[gbIntStackCounter][4] = height;
		gbGridPointStack[gbIntStackCounter][5] = imageId;
		gbGridPointStack[gbIntStackCounter][6] = drawEvent;
		gbGridPointStack[gbIntStackCounter][7] = op;
		//document.getElementById("txtTemp").value = gbGridPointStack.join("~");
		gbIntStackCounter++;			
	};
	this.mintaneUnDoReDoStack = function(drawEvent, x, y, width, height, imageId, op){
		op = op || "";
		gbArrUnDoReDoStack[gbIntUnDoReDoStackCounter] = new Array(7);
		gbArrUnDoReDoStack[gbIntUnDoReDoStackCounter][0] = drawEvent;
		gbArrUnDoReDoStack[gbIntUnDoReDoStackCounter][1] = x;
		gbArrUnDoReDoStack[gbIntUnDoReDoStackCounter][2] = y;
		gbArrUnDoReDoStack[gbIntUnDoReDoStackCounter][3] = width;
		gbArrUnDoReDoStack[gbIntUnDoReDoStackCounter][4] = height;
		gbArrUnDoReDoStack[gbIntUnDoReDoStackCounter][5] = imageId;
		gbArrUnDoReDoStack[gbIntUnDoReDoStackCounter][6] = op;
		gbIntUnDoReDoStackCounter++;			
	};
	this.drawGridPoints = function(e){
		if(gbEventType == ""){
			alert("Please Select Drawing Event!");
			return false;
		}						
		//alert(e);
		switch(gbEventType){
			case "funDrawDownTirangle":
				gbBlDoDrag = false;
				me.drawDownTirangle(e);
			break;
			case "funDrawUpTirangle":
				gbBlDoDrag = false;
				me.drawUpTirangle(e);
			break;				
			case "funCircleWtInterColor":
				gbBlDoDrag = false;
				me.circleWtInterColor(e);
			break;
			case "funCircleWtOutInterColor":
				gbBlDoDrag = false;
				me.circleWtOutInterColor(e);
			break;
			case "text":
				gbBlDoDrag = false;
				me.drawTextCan(e);
			break;
			case "reblock":
				me.drawReblock(e);
			break;
			case "drag":
				me.doDrag(e);															
			break;
		}
	};
	this.unselectColor = function(){
		var tdCon = new Array("tdArr1", "tdArr2", "tdRfill", "tdRblank", "tdRText", "tdErase", "tdUndo", "tdRedo", "tdDrag");
		for (var i in tdCon){
			if(document.getElementById(tdCon[i])){
				document.getElementById(tdCon[i]).style.backgroundColor = "#FFFFFF";
			}
		}
	}
	this.setSelectColor = function (strObj){
		if(document.getElementById(strObj)){					
			document.getElementById(strObj).style.backgroundColor = "#FFFFCC";
		}
	};	
	this.setEvent = function(eventType, strObj){
		strObj = strObj || "";
		gbEventType = eventType;
		switch(gbEventType){				
			case "funDrawDownTirangle":
				this.unselectColor();
				this.setSelectColor(strObj);
			break;
			case "funDrawUpTirangle":
				this.unselectColor();
				this.setSelectColor(strObj);			
			break;				
			case "funCircleWtInterColor":
				this.unselectColor();
				this.setSelectColor(strObj);			
			break;
			case "funCircleWtOutInterColor":
				this.unselectColor();
				this.setSelectColor(strObj);				
			break;
			case "text":
				this.unselectColor();
				this.setSelectColor(strObj);			
			break;				
			case "erase":
				this.unselectColor();
				this.setSelectColor(strObj);			
			break;
			case "reblock":				
			break;			
			case "undo":
				this.unselectColor();
				this.setSelectColor(strObj);
			break;
			case "redo":
				this.unselectColor();
				this.setSelectColor(strObj);
			break;
			case "drag":
				this.unselectColor();
				this.setSelectColor(strObj);
			break;		
		}
	};
	this.erase = function(){	
		if(confirm("Are you sure to Erase whole Drwaing!")){			
			canvasObj = document.getElementById("cCanvas");
			ctx = canvasObj.getContext("2d");
			gbEventType = "";
			gbGridPointStack = new Array();
			gbArrUnDoReDoStack = new Array();
			gbArrStackRedo = new Array();
			gbIntStackCounter = 0, gbIntUnDoReDoStackCounter = 0, gbIntTextCounter = 0, gbIntGPDCounter = 0;
			mouseX = 0, mouseY = 0;
			mouseXReblock = 0, mouseYReblock = 0;		
			mousePressed = false;
			intImgDwonTriId = 0, intImgUpTriId = 0, intImgCirWtIntColorId = 0, intImgCirWtOutColorId = 0;			
			for (var i in gbArrDownTirTime){
				clearInterval(gbArrDownTirTime[i]);
			}
			for (var i in gbArrUpTirTime){
				clearInterval(gbArrUpTirTime[i]);
			}
			for (var i in gbArrCirWithIntColTime){
				clearInterval(gbArrCirWithIntColTime[i]);
			}
			for (var i in gbArrCirWtOutColTime){
				clearInterval(gbArrCirWtOutColTime[i]);
			}
			clearInterval(lineReblockTime);			
			gbArrDownTirangle = new Array(), gbArrUpTirangle = new Array(), gbArrCirWithIntColor = new Array(), gbArrCirWtOutColor = new Array();
			objLineReblock = null;
			gbArrDownTirTime = new Array(), gbArrUpTirTime = new Array(), gbArrCirWithIntColTime = new Array(), gbArrCirWtOutColTime = new Array();
			lineReblockTime = null;
			gbBlDragStart = false;
			gbDragImageId = "";	
			gbBlDoDrag = false;
			canvasObj.width = canvasObj.width;
			canvasObj.height = canvasObj.height;
			ctx.clearRect(0,0,canvasObj.width,canvasObj.height) ;										
			var image = new Image();
			image.src = "sc-grid/images/bgTest.jpg";
			canvasObj.width = image.width;
			canvasObj.height = image.height;
		}
	};
	this.processUndo = function(){		
		//document.getElementById("tdRedo").onclick = me.processRedo;
		gbEventType = "undo";
		if(gbArrUnDoReDoStack.length > 0){
			//alert(gbArrUnDoReDoStack);
			document.getElementById("tdUndo").disabled = true;
			var arrUnDoStackOrignal = new Array();
			for(a = gbArrUnDoReDoStack.length-1, i = 0 ; a >= 0; a--){
				if(gbArrUnDoReDoStack[a]){
					arrUnDoStackOrignal[i] = gbArrUnDoReDoStack[a];
					i++;
				}
			}
			//arrUnDoStackOrignal.reverse();
			//alert(gbArrUnDoReDoStack+"<br>"+arrUnDoStackOrignal);
			var drawEvent = arrUnDoStackOrignal[0][0];
			var drawX = arrUnDoStackOrignal[0][1];
			var drawY = arrUnDoStackOrignal[0][2];
			var drawW = arrUnDoStackOrignal[0][3];
			var drawH = arrUnDoStackOrignal[0][4];
			var drawImgId = arrUnDoStackOrignal[0][5];
			var op = arrUnDoStackOrignal[0][6];
			//canvas = document.getElementById("cCanvas");
			//var ctx = canvas.getContext("2d");
			//alert(drawEvent+"-"+drawX+"-"+drawY+"-"+drawW+"-"+drawH+"-"+drawImgId+"-"+op);
			switch(drawEvent){
				case "funDrawDownTirangle":					
					//Down Arrow
					me.delObjDrag(drawEvent, drawImgId);
					ctx.clearRect(drawX, drawY, drawW, drawH);
					gbArrStackRedo.push(arrUnDoStackOrignal[0]);
					//alert(gbArrStackRedo)
					delete arrUnDoStackOrignal[0];																	
					gbArrUnDoReDoStack = me.popFromArray(gbArrUnDoReDoStack);
				break;
				case "funDrawUpTirangle":					
					//Up Arrow
					me.delObjDrag(drawEvent, drawImgId);		
					ctx.clearRect(drawX, drawY, drawW, drawH);
					gbArrStackRedo.push(arrUnDoStackOrignal[0]);
					delete arrUnDoStackOrignal[0];
					gbArrUnDoReDoStack = me.popFromArray(gbArrUnDoReDoStack);
				break;					
				case "funCircleWtInterColor":					
					//Circle With Inter Color
					me.delObjDrag(drawEvent, drawImgId);		
					ctx.clearRect(drawX, drawY, drawW, drawH);
					gbArrStackRedo.push(arrUnDoStackOrignal[0]);
					delete arrUnDoStackOrignal[0];
					gbArrUnDoReDoStack = me.popFromArray(gbArrUnDoReDoStack);
				break;	
				case "funCircleWtOutInterColor":					
					//Circle Without Inter Color
					me.delObjDrag(drawEvent, drawImgId);
					ctx.clearRect(drawX, drawY, drawW, drawH);
					var popped = arrUnDoStackOrignal[0];					
					gbArrStackRedo.push(arrUnDoStackOrignal[0]);
					delete arrUnDoStackOrignal[0];
					gbArrUnDoReDoStack = me.popFromArray(gbArrUnDoReDoStack);
				break;				
				case "funDrawTextCan":					
					//Text
					ctx.clearRect(drawX, drawY - 10, drawW,drawH);
					var popped = arrUnDoStackOrignal[0];					
					gbArrStackRedo.push(arrUnDoStackOrignal[0]);
					delete arrUnDoStackOrignal[0];
					gbArrUnDoReDoStack = me.popFromArray(gbArrUnDoReDoStack);
				break;	
				case "funDrawReblock":					
					//Reblock
					me.delObjDrag(drawEvent, drawImgId);
					ctx.clearRect(drawX, drawY, drawW, drawH);
					var popped = arrUnDoStackOrignal[0];					
					gbArrStackRedo.push(arrUnDoStackOrignal[0]);
					delete arrUnDoStackOrignal[0];
					gbArrUnDoReDoStack = me.popFromArray(gbArrUnDoReDoStack);
				break;				
			}
			document.getElementById("tdUndo").disabled = false;
		}
		else{
			document.getElementById("tdUndo").disabled = true;
			document.getElementById("tdUndo").onclick = "";
			document.getElementById("tdRedo").disabled = false;
			//document.getElementById("tdRedo").onclick = processRedo;
			alert("No Undo Action Exits!");
		}
	};
	this.processRedo = function(){
		//document.getElementById("tdUndo").onclick = me.processUndo;
		gbEventType = "redo";	
		if(gbArrStackRedo.length > 0){
			document.getElementById("tdRedo").disabled = true;
			//alert(gbArrStackRedo);			
			var arrReDoStackOrignal = new Array();						
			for(a = gbArrStackRedo.length-1, i = 0 ; a >= 0; a--){				
				arrReDoStackOrignal[i] = gbArrStackRedo[a];
				i++;
			}
			//alert(gbArrStackRedo+"<br>"+arrReDoStackOrignal);
			//alert(arrReDoStackOrignal);			
			var drawEvent = arrReDoStackOrignal[0][0];
			var drawX = arrReDoStackOrignal[0][1];
			var drawY = arrReDoStackOrignal[0][2];
			var drawW = arrReDoStackOrignal[0][3];
			var drawH = arrReDoStackOrignal[0][4];			
			var drawImgId = arrReDoStackOrignal[0][5];	
			var op = arrReDoStackOrignal[0][6];		
			//alert(drawEvent+"-"+drawX+"-"+drawY+"-"+drawW+"-"+drawH+"-"+drawImgId+"-"+op);				
			var arrDrwaingPoint = new Array(drawX, drawY, drawW, drawH, drawImgId, op);
			switch(drawEvent){
				case "funDrawDownTirangle":					
					//Down Arrow						
					me.drawDownTirangle('',arrDrwaingPoint);
					delete arrReDoStackOrignal[0];
					gbArrStackRedo.pop();
				break;
				case "funDrawUpTirangle":					
					//Up Arrow
					me.drawUpTirangle('',arrDrwaingPoint);
					delete arrReDoStackOrignal[0];
					gbArrStackRedo.pop();
				break;					
				case "funCircleWtInterColor":					
					//Circle with internal color
					me.circleWtInterColor('',arrDrwaingPoint);
					delete arrReDoStackOrignal[0];
					gbArrStackRedo.pop();
				break;	
				case "funCircleWtOutInterColor":					
					//Circle with out internal color
					me.circleWtOutInterColor('',arrDrwaingPoint);
					delete arrReDoStackOrignal[0];
					gbArrStackRedo.pop();
				break;
				case "funDrawTextCan":					
					//Text
					me.drawTextCan('',arrDrwaingPoint);
					delete arrReDoStackOrignal[0];
					gbArrStackRedo.pop();
				break;
				case "funDrawReblock":					
					//Reblock
					me.drawReblock('',arrDrwaingPoint);
					delete arrReDoStackOrignal[0];
					gbArrStackRedo.pop();
				break;
			}
			//document.getElementById("txtTemp").value = gbGridPointStack.join("~");
			document.getElementById("tdRedo").disabled = false;
		}
		else{
			document.getElementById("tdRedo").disabled = true;
			document.getElementById("tdRedo").onclick = "";
			document.getElementById("tdUndo").disabled = false;
			//document.getElementById("tdUndo").onclick = processUndo;
			alert("No Redo Action Exits!");
		}	
	};
	this.delObjDrag = function(drawEvent, drawImgId){
		switch(drawEvent){
			case "funDrawDownTirangle":					
				//Down Arrow
				var arrTemp = drawImgId.split("-");
				var intImageId = parseInt(arrTemp[1]);					
				clearInterval(gbArrDownTirTime[intImageId]);	
				delete gbArrDownTirTime[intImageId];
				delete gbArrDownTirangle[intImageId];
				this.delObjGridPoint(drawEvent, drawImgId);
			break;
			case "funDrawUpTirangle":					
				//Down Arrow
				var arrTemp = drawImgId.split("-");
				var intImageId = parseInt(arrTemp[1]);
				clearInterval(gbArrUpTirTime[intImageId]);
				delete gbArrUpTirTime[intImageId];
				delete gbArrUpTirangle[intImageId];
				this.delObjGridPoint(drawEvent, drawImgId);					
			break;
			case "funCircleWtInterColor":					
				//Circle Wt Inter Color
				var arrTemp = drawImgId.split("-");
				var intImageId = parseInt(arrTemp[1]);
				clearInterval(gbArrCirWithIntColTime[intImageId]);
				delete gbArrCirWithIntColTime[intImageId];
				delete gbArrCirWithIntColor[intImageId];
				this.delObjGridPoint(drawEvent, drawImgId);					
			break;
			case "funCircleWtOutInterColor":					
				//Circle WtOut Inter Color
				var arrTemp = drawImgId.split("-");
				var intImageId = parseInt(arrTemp[1]);
				clearInterval(gbArrCirWtOutColTime[intImageId]);
				delete gbArrCirWtOutColTime[intImageId];
				delete gbArrCirWtOutColor[intImageId];
				this.delObjGridPoint(drawEvent, drawImgId);
			break;				
			case "funDrawReblock":					
				//Reblock
				var arrTemp = drawImgId.split("-");
				var intImageId = parseInt(arrTemp[1]);
				clearInterval(gbArrCirWithIntColTime[intImageId]);
				delete gbArrCirWithIntColTime[intImageId];
				delete objLineReblock;
				this.delObjGridPoint(drawEvent, drawImgId);					
			break;
		}
	};
	this.delObjGridPoint = function(drawEvent, drawImgId){
		for(var a = 0; a < gbGridPointStack.length; a++){
			if((gbGridPointStack[a]) && (this.JSIsArray(gbGridPointStack[a]) == true)){
				if((gbGridPointStack[a][5] == drawImgId) && (gbGridPointStack[a][6] == drawEvent)){
					delete gbGridPointStack[a];
					a = gbGridPointStack.length + 1;
				}
			}
		}
	};
	this.popFromArray = function(popingArr, doPop){
		doPop = doPop || true;
		if(doPop == true){
			popingArr.pop();
		}
		var arrTemp = new Array();			
		//alert(popingArr);
		var a = 0;
		var i = 0;
		for(a = 0; a < popingArr.length; a++){
			if(popingArr[a]){
				arrTemp[i] = popingArr[a];
				i++;
			}
		}
		return arrTemp;
	};
	this.onMouseDown = function(e){
		//alert('onMouseDown');							
		mousePressed = true;	
	};
	this.onMouseUp = function(e){					
		mouseX = canvasObj.width;
		mouseY = canvasObj.height;			
		mouseXReblock = canvasObj.width;
		mouseYReblock = canvasObj.height;			
		mousePressed = false;
	};
	this.onMouseMove = function(e){
		//alert('onMouseMove');			
		mouseX = e.offsetX;
		mouseY = e.offsetY;
		mouseXReblock = e.offsetX;
		mouseYReblock = e.offsetY;
	};
	this.onTouchStart = function(e){
		//alert('onTouchStart');				
		var touch = me.getTouchEvent( event );			
		var localPosition = me.getCanvasLocalCoordinates(touch.pageX,touch.pageY);			
		var lastPenPoint = {x: localPosition.x, y: localPosition.y};
		mouseX = lastPenPoint.x;
		mouseY = lastPenPoint.y;
		mouseXReblock = lastPenPoint.x;
		mouseYReblock = lastPenPoint.y;
		mousePressed = true;			
	};
	this.onTouchEnd = function(e){
		//alert('onTouchEnd');
		mouseX = canvasObj.width;
		mouseY = canvasObj.height;	
		mouseXReblock = canvasObj.width;
		mouseYReblock = canvasObj.height;
		mousePressed = false;
	};
	this.onTouchMove = function(e){
		//alert('onTouchMove');						
		var touch = me.getTouchEvent( event );					
		var localPosition = me.getCanvasLocalCoordinates(touch.pageX,touch.pageY);			
		var lastPenPoint = {x: localPosition.x, y: localPosition.y};
		mouseX = lastPenPoint.x;
		mouseY = lastPenPoint.y;
		mouseXReblock = lastPenPoint.x;
		mouseYReblock = lastPenPoint.y;
	}
	this.getTouchEvent = function() {						
		return(isIPad ? window.event.targetTouches[ 0 ] : event);
	};
	this.getCanvasLocalCoordinates = function(pageX, pageY ) {
		if(isIPad == true){
			//alert(pageX+"---"+window.event.targetTouches[ 0 ].pageX+"---"+canvasObj.offsetLeft);	
			var tempX = pageX - canvasObj.offsetLeft;
			var tempY = pageY - canvasObj.offsetTop;				
			return({					
				x: (tempX - 30),
				y: (tempY - 50)
			});
		}
	};
	function DragImage(src, x, y, imageId, srcW, srcH, op) {
		srcW = srcW || 0;
		srcH = srcH || 0;
		op = op || "";
		//document.getElementById("txtTemp").value = op;		
		var that = this;
		var startX = 0, startY = 0;
		var drag = false;
		this.x = x;
		this.y = y;
		this.imageId = imageId;			
		var img = new Image();			
		img.src = src;
		var srcWidth = 0, srcHeight = 0; 
		if(parseInt(srcW) > 0){
			srcWidth = parseInt(srcW);
		}
		else{
			srcWidth = parseInt(img.width);
		}
		if(parseInt(srcH) > 0){
			srcHeight = parseInt(srcH);
		}
		else{
			srcHeight = parseInt(img.height);
		}
		this.upIm;
		this.upId;
		this.update = function() {
			//alert('this.update');
			if (mousePressed){
				//alert('mousePressed');
				if(isIPad == true){
					var left = parseInt(that.x) - parseInt(img.width);					
					var right = parseInt(that.x) + parseInt(img.width);												
					var top = parseInt(that.y) - parseInt(img.height);
					var bottom = parseInt(that.y) + parseInt(img.height);
					if (!drag){
						startX = parseInt(mouseX) - parseInt(that.x);
						startY = parseInt(mouseY) - parseInt(that.y);					  
					}
					//document.getElementById("txtTemp1").value = mouseX+"--"+right+"--"+mouseX+"--"+left+"--"+mouseY+"--"+bottom+"--"+mouseY+"--"+top+"[["+that.x+"]]"+img.width+"**"+op;
					if ((parseInt(mouseX) <= right) && (parseInt(mouseX) >= left) && (parseInt(mouseY) <= bottom) && (parseInt(mouseY) >= top) && (gbBlDoDrag == true)){
						drag = true;
						gbBlDragStart = true;
						gbDragImageId = that.imageId;
					}
				}
				else{
					var left = parseInt(that.x);					
					var right = parseInt(that.x) + parseInt(img.width);						
					var top = parseInt(that.y);
					var bottom = parseInt(that.y) + parseInt(img.height);
					if (!drag){
						startX = parseInt(mouseX) - parseInt(that.x);
						startY = parseInt(mouseY) - parseInt(that.y);					  
					}	
					//document.getElementById("txtTemp").value = parseInt(mouseX)+"--"+parseInt(right)+"--"+parseInt(mouseX)+"--"+parseInt(left)+"--"+parseInt(mouseY)+"--"+parseInt(bottom)+"--"+parseInt(mouseY)+"--"+parseInt(top)+"[["+that.x+"]]"+img.width+"**"+op;					
					if ((parseInt(mouseX) <= right) && (parseInt(mouseX) >= left) && (parseInt(mouseY) <= bottom) && (parseInt(mouseY) >= top) && (gbBlDoDrag == true)){
						//document.getElementById("txtTemp1").value = parseInt(mouseX)+"--"+parseInt(right)+"--"+parseInt(mouseX)+"--"+parseInt(left)+"--"+parseInt(mouseY)+"--"+parseInt(bottom)+"--"+parseInt(mouseY)+"--"+parseInt(top)+"[["+that.x+"]]"+img.width+"**"+op;
						drag = true;
						gbBlDragStart = true;
						gbDragImageId = that.imageId;
					}							
				}
			}
			else{
			   drag = false;
			}
			if (drag){
				that.x = parseInt(mouseX) - startX;
				that.y = parseInt(mouseY) - startY;					
			}
			if(isIPad == true){
				if (drag){							
					me.clear(left, top, right, bottom);
				}
				else{
					me.clear(left, top, srcWidth, srcHeight);
				}
			}
			else{
				me.clear(left, top, srcWidth, srcHeight);				
			}				
			me.updateGridPointStack(this.upIm, parseInt(that.x), parseInt(that.y), this.upId);
			ctx.drawImage(img, parseInt(that.x), parseInt(that.y), srcWidth, srcHeight);
		}		
	};
	function DragReblock(src, x, y, imageId, srcW, srcH, op) {
		srcW = srcW || 0;
		srcH = srcH || 0;
		op = op || "";			
		//document.getElementById("txtTemp").value = op;		
		var that = this;
		var startX = 0, startY = 0;
		var drag = false;
		this.x = x;
		this.y = y;
		this.imageId = imageId;			
		var img = new Image();			
		img.src = src;
		var srcWidth = 0, srcHeight = 0; 
		if(parseInt(srcW) > 0){
			srcWidth = parseInt(srcW);
		}
		else{
			srcWidth = parseInt(img.width);
		}
		if(parseInt(srcH) > 0){
			srcHeight = parseInt(srcH);
		}
		else{
			srcHeight = parseInt(img.height);
		}
		this.upIm;
		this.upId;
		//alert(mousePressed);
		this.updateReblock = function() {
			//alert('this.update');
			if (mousePressed){
				//alert('mousePressed');
				if(isIPad == true){
					var left = parseInt(that.x) - parseInt(img.width);					
					var right = parseInt(that.x) + parseInt(img.width);						
					if(op == "reblock"){
						var top = 1;							
						mouseYReblock = 1;
					}
					else{
						var top = parseInt(that.y) - parseInt(img.height);
					}
					var bottom = parseInt(that.y) + parseInt(img.height);
					if (!drag){
						startX = parseInt(mouseXReblock) - parseInt(that.x);
						startY = parseInt(mouseYReblock) - parseInt(that.y);					  
					}
					//document.getElementById("txtTemp1").value = mouseXReblock+"--"+right+"--"+mouseXReblock+"--"+left+"--"+mouseYReblock+"--"+bottom+"--"+mouseYReblock+"--"+top+"[["+that.x+"]]"+img.width+"**"+op;
					if ((parseInt(mouseXReblock) <= right) && (parseInt(mouseXReblock) >= left) && (parseInt(mouseYReblock) <= bottom) && (parseInt(mouseYReblock) >= top) && (gbBlDoDrag == true)){
						drag = true;
						gbBlDragStart = true;
						gbDragImageId = that.imageId;
					}
				}
				else{
					var left = parseInt(that.x);					
					var right = parseInt(that.x) + parseInt(img.width);
					if(op == "reblock"){
						var top = 1;							
						mouseYReblock = 1;
					}
					else{
						var top = parseInt(that.y);
					}
					var bottom = parseInt(that.y) + parseInt(img.height);
					if (!drag){
						startX = parseInt(mouseXReblock) - parseInt(that.x);
						startY = parseInt(mouseYReblock) - parseInt(that.y);					  
					}	
					//document.getElementById("txtTemp").value = parseInt(mouseXReblock)+"--"+parseInt(right)+"--"+parseInt(mouseXReblock)+"--"+parseInt(left)+"--"+parseInt(mouseYReblock)+"--"+parseInt(bottom)+"--"+parseInt(mouseYReblock)+"--"+parseInt(top)+"[["+that.x+"]]"+img.width+"**"+op;					
					if ((parseInt(mouseXReblock) <= right) && (parseInt(mouseXReblock) >= left) && (parseInt(mouseYReblock) <= bottom) && (parseInt(mouseYReblock) >= top) && (gbBlDoDrag == true)){
						//document.getElementById("txtTemp1").value = parseInt(mouseXReblock)+"--"+parseInt(right)+"--"+parseInt(mouseXReblock)+"--"+parseInt(left)+"--"+parseInt(mouseYReblock)+"--"+parseInt(bottom)+"--"+parseInt(mouseYReblock)+"--"+parseInt(top)+"[["+that.x+"]]"+img.width+"**"+op;
						drag = true;
						gbBlDragStart = true;
						gbDragImageId = that.imageId;
					}							
				}
			}
			else{
			   drag = false;
			}
			if (drag){
				that.x = parseInt(mouseXReblock) - startX;
				that.y = parseInt(mouseYReblock) - startY;					
			}
			if(isIPad == true){
				if(op == "reblock"){
					me.clear(left, 1, right, srcHeight);
				}
				else{
					if (drag){							
						me.clear(left, top, right, bottom);
					}
					else{
						me.clear(left, top, srcWidth, srcHeight);
					}
				}
			}
			else{
				me.clear(left, top, srcWidth, srcHeight);				
			}				
			me.updateGridPointStack(this.upIm, parseInt(that.x), parseInt(that.y), this.upId);
			ctx.drawImage(img, parseInt(that.x), parseInt(that.y), srcWidth, srcHeight);
		}
	};
	this.saveAnesthesiaGrid = function(){
		//alert(gbGridPointStack);
		var strGridImage = canvasObj.toDataURL("image/png");		
		document.getElementById("hidGridImgData").value = strGridImage;
		document.getElementById("hidAnesthesiaGridData").value = gbGridPointStack.join("~");
		//alert(document.getElementById("hidAnesthesiaGridData").value);
		//document.frmSCGrid.submit();
	};
	this.getTimeGrid = function(intHour, intMin){
		var v = new Date(); 
		v.setHours(intHour);
		v.setMinutes(intMin);
		var t = "";
		for(var a = 1; a <= 21; a++){			
			if(a > 1){
				v.setMinutes(v.getMinutes()+15);
			}
			var m = new String(v.getMinutes());
			if(m.length == 1){
				m = "0"+m;
			}
			var h = parseInt(v.getHours());
			var nH = 0
			if(h > 12){
				nH = h - 12;
				if(top.document.getElementById("show_military_time")) {
					if(top.document.getElementById("show_military_time").value=="YES") {
						nH = h;
					}
				}
			}
			else{
				nH = h;
			}
			var ho = new String(nH);
			if(ho.length == 1){
				ho = "0"+ho;
			}
			if(a > 1){
				t += "<span class=\"timerPad\"></span><span class=\"timerWidth\">" + ho + ":" + m + "</span>";
			}
			else{
				t += "<span class=\"timerWidth\">" + ho + ":" + m + "</span>";
			}
		}
		return t;
	};
	this.calculateTime = function(txtTime){
		var strTime = document.getElementById(txtTime).value;
		var arrTime = strTime.split(":");
		var intHour = parseInt(arrTime[0]);
		var intMin = parseInt(arrTime[1]);
		if(intHour > 0 && intMin >= 0){
			if((intMin >= 0) && (intMin < 15)){
				//Quadrant 1:
				var t = this.getTimeGrid(intHour, 0);
				document.getElementById("divGridTimer").innerHTML = t;
			}
			else if((intMin >= 15) && (intMin < 30)){
				//Quadrant 2:
				var t = this.getTimeGrid(intHour, 15);
				document.getElementById("divGridTimer").innerHTML = t;
			}
			else if((intMin >= 30) && (intMin < 45)){
				//Quadrant 3:
				var t = this.getTimeGrid(intHour, 30);				
				document.getElementById("divGridTimer").innerHTML = t;
			}
			else if((intMin >= 45) && (intMin <= 59)){
				//Quadrant 4:
				var t = this.getTimeGrid(intHour, 45);
				document.getElementById("divGridTimer").innerHTML = t;
			}
		}
		else{
			alert('Please Enter Start Time!')
		}	
	};
	this.calculateReblockTime = function(txtTime){
		var strTime = document.getElementById(txtTime).value;
		var arrTime = strTime.split(":");
		var intHour = parseInt(arrTime[0]);
		var intMin = parseInt(arrTime[1]);
		var r = 0
		if(intHour > 0 && intMin > 0){
			if((intMin > 0) && (intMin < 15)){
				//Quadrant 1:
				r = this.getReblockPixel(intHour, 0);
			}
			else if((intMin >= 15) && (intMin < 30)){
				//Quadrant 2:
				r = this.getReblockPixel(intHour, 15);
			}
			else if((intMin >= 30) && (intMin < 45)){
				//Quadrant 3:
				r = this.getReblockPixel(intHour, 30);
			}
			else if((intMin >= 45) && (intMin < 59)){
				//Quadrant 4:
				r = this.getReblockPixel(intHour, 45);
			}
			return r;
		}
		else{
			alert('Please Enter Start Time!')
		}	
	};
	this.getReblockPixel = function(intHour, intMin){
		var d = new Date(); 
		var v = new Date(); 
		v.setHours(intHour);
		v.setMinutes(intMin);
		var intStartMin = 0, intCurrentMin = 0, intAddPixel = 0, intPixel = intReblockPixelX = 0;
		var m = 0, h = 0, cH = 0, nH = 0;
		for(var a = 1; a <= 21; a++){			
			if(a > 1){
				v.setMinutes(v.getMinutes() + 15);
				intPixel = intPixel + 50;
			}
			else{
				v.setMinutes(v.getMinutes());
			}
			m = parseInt(v.getMinutes());
			h = parseInt(v.getHours());
			cH = parseInt(d.getHours());
			if(cH > 12){
				nH = cH - 12;
			}
			else{
				nH = cH;
			}
			if(h == nH){
				intStartMin = m;
				intCurrentMin = parseInt(d.getMinutes());
				if((intCurrentMin >= 0 && intCurrentMin < 15) || (intCurrentMin >= 15 && intCurrentMin < 30) || (intCurrentMin >= 30 && intCurrentMin < 45) || (intCurrentMin >= 45 && intCurrentMin < 60)){
					intAddPixel = intStartMin - intCurrentMin;
					intAddPixel = Math.abs(intAddPixel);
					intAddPixel = intAddPixel * 3.2;
					intAddPixel = parseInt(Math.abs(intAddPixel));
					intReblockPixelX = intPixel +  intAddPixel;					
				}
			}
			if(intReblockPixelX > 0){
				a = 22;
			}
		}
		return intReblockPixelX;
	};
	this.showHideReblockLine = function(obj, e){
		if(obj.checked == true){
			this.drawReblock(e);
		}
		else if(obj.checked == false){
			clearInterval(lineReblockTime);
			objLineReblock = null;
			lineReblockTime = null;
			var drawImgId = "imgLineReblock-"+0;	
			this.delObjGridPoint("funDrawReblock", drawImgId);
			this.clear(0, 0, canvasObj.width, canvasObj.height);
		}
	};
}
var objAnesthesiaGrid = new CLSAnesthesiaGrid();
function init(){
	objAnesthesiaGrid.init();
}
function setEvent(eventType, strObj){
	objAnesthesiaGrid.setEvent(eventType, strObj);
}
function erase(){
	objAnesthesiaGrid.erase();
}
function processUndo(){
	objAnesthesiaGrid.processUndo();
}
function processRedo(){
	objAnesthesiaGrid.processRedo();
}
function saveAnesthesiaGrid(){
	objAnesthesiaGrid.saveAnesthesiaGrid()
}
function calculateTime(txt){
	objAnesthesiaGrid.calculateTime(txt);
}
function showHideReblockLine(obj, e){
	objAnesthesiaGrid.showHideReblockLine(obj, e);
}
window.addEventListener("load", init, false);