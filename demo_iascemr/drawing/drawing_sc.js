
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.


var zTimeoutMM=0;
function CLSDrawing(cID){
	// cID will be the numeric, to represent instance in multiple numbers.
	var me = this;
	this.eventType = null, this.lineType =0.5;
	this.currentEraser = "16-16";	
	this.strCDRatioOD = null, this.strCDRatioOS = null;
	var gbEventType = null, gbLineType = 0.5;
	var canvasObj = null, ctx = null;
	var canvasTempObj = null, ctxTemp = null, lastPenPoint = null;
	var currentColor = "#171717";
	var touch = "";
	var isIPad = false;
	var gbEventTypeTemp = null;
	var mouseX = 0, mouseY = 0;	
	var mouseX1 = 0, mouseY1 = 0;	
	var intSelectStartX = 0, intSelectStartY = 0, intSelectWidth = 0, intSelectHieght = 0;
	var intArrowStartX = 0, intArrowStartY = 0;
	var mousePressed = false, gbBlDragStart = false, gbBlDragStartSelectProcess = false;		
	this.arrBlCanvasHaveDrwaing = new Array();
	var sprayWidth = 1;
	var intSelectStartXTempCanvas = 0, intSelectStartYTempCanvas = 0;
	var widthMXSX = 0, hieghtMYSY = 0;
	var widthSelect = 0, hieghtSelect = 0;
	var intLineStartX = 0, intLineStartY = 0;
	var intArcStartX = 0, intArcStartY = 0;
	var intEmtRectStartX = 0, intEmtRectStartY = 0;
	var intRectWidth = 0, intRectHieght = 0;
	var intEmtRoundRectStartX = 0, intEmtRoundRectStartY = 0, intEmtRoundRectEndX = 0, intEmtRoundRectEndY = 0;
	var intRoundRectWidth = 0, intRoundRectHieght = 0;
	var intFilledRectStartX = 0, intFilledRectStartY = 0, intFilledRectWidth = 0, intFilledRectHieght = 0;
	var intFilledRoundRectStartX = 0, intFilledRoundRectStartY = 0, intFilledRoundRectEndX = 0, intFilledRoundRectEndY = 0;
	var intEmtElpsStartX = 0, intEmtElpsStartY = 0;
	var intEmtCirStartX = 0, intEmtCirStartY = 0;
	var intFilledElpsStartX = 0, intFilledElpsStartY = 0;
	var intFilledCirStartX = 0,	intFilledCirStartY = 0;
	var dashed = new Image();
	var arrSelRed = new Array();
	var arrSelGreen = new Array();
	var arrSelBlue = new Array();
	var arrSelAlpha = new Array();
	var arrEmtRect = new Array();
	var outlineLayerData = null, colorLayerData = null;			
	var pixelStack = new Array();
	var newColorR = null, newColorG = null, newColorB = null, newColorA = null, clickedColorR = null, clickedColorG = null, clickedColorB = null, clickedColorA = null;
	var gbArrDrawingImages = new Array();
	var intDrawingImagesCounter = 0;
	var imgPuker = null, imgPukerT = null, imgPukerTSmartTag = null;
	var imgDrusen = null, imgDrusenT = null, imgDrusenTSmartTag = null;
	var imgDryDegeneration = null, imgDryDegenerationT = null;
	var imgRetinalTear = null, imgRetinalTearT = null;
	var imgLatticeDegeneration = null, imgLatticeDegenerationT = null;
	var imgChoroidalNevus = null, imgChoroidalNevusT = null;
	var imgHorseShoeTear = null, imgHorseShoeTearT = null;
	var imgRetinalHemorrhage = null, imgRetinalHemorrhageT = null;
	var imgPallor = null, imgPallorT = null, imgPallorTSmartTag = null;
	
	var imgSPKMild = null, imgSPKMildT = null;
	var imgDisciformScar = null, imgDisciformScarT = null;
	var imgExudates = null, imgExudatesT = null;
	var imgHemorrhageRed = null, imgHemorrhageRedT = null;
	var imgNeovascularization = null, imgNeovascularizationT = null;
	var imgPannus = null, imgPannusT = null;
	var imgPingnelcula = null, imgPingnelculaT = null;
	var imgPRPTreatment = null, imgPRPTreatmentT = null;
	var imgPterygium = null, imgPterygiumT = null;
	var imgRedDot = null, imgRedDotT = null;
	var imgRetinalHemorrhageDot = null, imgRetinalHemorrhageDotT = null;
	var imgUlcer = null, imgUlcerT = null;
	var imgRetinalIrregularHemorrhage = null, imgRetinalIrregularHemorrhageT = null;
	//new icons
	var imgCME = null, imgCMET = null;	
	var imgDruMild = null, imgDruMildT = null;
	var imgDruMod = null, imgDruModT = null;
	var imgFloaters = null, imgFloatersT = null;
	var imgFocalTreatment = null, imgFocalTreatmentT = null;
	//var imgLattice = null, imgLatticeT = null;
	var imgMA = null, imgMAT = null;
	var imgPVD = null, imgPVDT = null;
	var imgRPEChanges = null, imgRPEChangesT = null;
	var imgERM = null, imgERMT = null;
	var imgExudates = null, imgExudatesT = null;

	//
	
	var gbArrDrawingImagesTemp = new Array();
	var gbArrImagesDB = new Array();
	var intImagesCounterTemp = 0;
	var gbDragStart = false;
	//var arrPreClickCol = new Array();
	var gbArrTextMain = new Array();
	var intTextCounterMain = 0;
	var gbArrTextTemp = new Array();
	var intTextCounterTemp = 0;
	var gbArrTextDB = new Array();
	var gbArrCurrentTAXY = new Array();
	
	this.gbArrSmartTagDivID = new Array();
	var intSmartTagX = null, intSmartTagY = null;
	var gbIntCurrentArrDrawingImagesIndex = null;
	var gbIntCurrentArrDrawingImagesDBIndex = null;
	var arrToolDiv = new Array();
	
	var classId = null;
	me.classId = cID;
	
	var strCurrentTool = "tool_1" + "_" + me.classId;
	
	var curImg=null;	
	/*
	var arrMenu=["ERM","Floaters","Retinal Hemorrhage","Exudates","Neovascularization","Pannus","Dry Degeneration","Disciform Scar",
					"Lattice Degeneration","PRP Treatment(Laser)","Choroidal Nevus", "Pallor", "Hemorrhage","Pterygium",
					"Ulcer","Pingnelcula","Horse Shoe Tear","Retinal Tear","SPK Mild","RPE Changes","MA","Focal Treatment",
					"CME","Drusen Mild","Drusen Moderate","Drusen","PVD"];
	*/
	var arrMenu=[];
	var imgPrevD=null;
	var arrTempSel=null;	
	var strDataDefLength=0;
	
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
	this.makeCanvasActive = function() {
		document.getElementById("cCanvas"+me.classId).className = "cCanvas";
	};
	
	this.drawingInit = function(){	//alert('drawingInit')
		arrToolDiv = Array("tool_1" + "_" + me.classId, "tool_2" + "_" + me.classId);
		me.arrBlCanvasHaveDrwaing[0] = false;
		me.arrBlCanvasHaveDrwaing[1] = false;		
		var divCanvasObj = document.getElementById("divCanvas"+me.classId);
		divCanvasObj.addEventListener('touchmove', this.blockMove, false ); 
		canvasObj = document.getElementById("cCanvas"+me.classId);
		canvasObj.onselectstart = function (){ 
			return false; 
		}		
		canvasTempObj = document.getElementById("cCanvasTemp"+me.classId);
		canvasTempObj.onselectstart = function () {
			return false; 
		}		
		
		canvasObj.addEventListener('mousedown', this.drawDrwaingPoints, false ); 
		canvasObj.addEventListener('mouseup', this.onMouseUp, false ); 
		canvasObj.addEventListener('mousemove', this.onMouseMove, false );
		canvasObj.addEventListener('dblclick', this.onMouseDblClick, false);
		canvasObj.addEventListener('click', this.onMouseClick, false ); 
		//canvasObj.addEventListener('keydown',function(){alert('asd')},true);
		
		canvasTempObj.addEventListener('mousedown', this.drawDrwaingPoints, false ); 
		canvasTempObj.addEventListener('mouseup', this.onMouseUp, false ); 
		canvasTempObj.addEventListener('mousemove', this.onMouseMove, false ); 
		
		canvasObj.addEventListener('touchstart', this.drawDrwaingPoints, false ); 
		canvasObj.addEventListener('touchend', this.onTouchEnd, false ); 
		canvasObj.addEventListener('touchmove', this.onTouchMove, false );
		
		canvasTempObj.addEventListener('touchstart', this.drawDrwaingPoints, false ); 
		canvasTempObj.addEventListener('touchend', this.onTouchEnd, false ); 
		canvasTempObj.addEventListener('touchmove', this.onTouchMove, false );			
					
		isIPad = (new RegExp( "iPad", "i" )).test(navigator.userAgent);			
		ctx = canvasObj.getContext("2d");
		ctxTemp = canvasTempObj.getContext("2d");		
		
		//dashed.src = 'iDoc-Drawing/images/dashed2.gif';
		
		imgPuker = new Image();
		//imgPuker.src = "iDoc-Drawing/images/Pucker.png";
		imgPukerT = new Image();
		//imgPukerT.src = "iDoc-Drawing/images/puckerT.png";
		imgPukerTSmartTag = new Image();
		//imgPukerTSmartTag.src = "iDoc-Drawing/images/puckerTLink.png";
				
		imgDrusen = new Image();
		//imgDrusen.src = "iDoc-Drawing/images/Drusen.png";
		imgDrusenT = new Image();
		//imgDrusenT.src = "iDoc-Drawing/images/drusenT.png";
		imgDrusenTSmartTag = new Image();
		//imgDrusenTSmartTag.src = "iDoc-Drawing/images/drusenTLink.png";
		
		imgDryDegeneration = new Image();
		//imgDryDegeneration.src = "iDoc-Drawing/images/DryDegeneration.png";
		imgDryDegenerationT = new Image();
		//imgDryDegenerationT.src = "iDoc-Drawing/images/dryT.png";
		
		imgRetinalTear = new Image();
		//imgRetinalTear.src = "iDoc-Drawing/images/RetinalTear.png";
		imgRetinalTearT = new Image();
		//imgRetinalTearT.src = "iDoc-Drawing/images/retinalT.png";
		
		imgLatticeDegeneration = new Image();
		//imgLatticeDegeneration.src = "iDoc-Drawing/images/LatticeDegeneration.png";
		imgLatticeDegenerationT = new Image();
		//imgLatticeDegenerationT.src = "iDoc-Drawing/images/latticeT.png";
		
		imgChoroidalNevus = new Image();
		//imgChoroidalNevus.src = "iDoc-Drawing/images/ChoroidalNevus.png";
		imgChoroidalNevusT = new Image();
		//imgChoroidalNevusT.src = "iDoc-Drawing/images/chorodialT.png";
				
		imgHorseShoeTear = new Image();
		//imgHorseShoeTear.src = "iDoc-Drawing/images/HorseShoeTear.png";
		imgHorseShoeTearT = new Image();
		//imgHorseShoeTearT.src = "iDoc-Drawing/images/horseT.png";
		
		imgRetinalHemorrhage = new Image();
		//imgRetinalHemorrhage.src = "iDoc-Drawing/images/RetinalHemorrhage.png";
		imgRetinalHemorrhageT = new Image();
		//imgRetinalHemorrhageT.src = "iDoc-Drawing/images/hemorrhage.png";
				
		imgPallor = new Image();
		//imgPallor.src = "iDoc-Drawing/images/Pallor.png";
		imgPallorT = new Image();
		//imgPallorT.src = "iDoc-Drawing/images/pallorT.png";
		imgPallorTSmartTag = new Image();
		//imgPallorTSmartTag.src = "iDoc-Drawing/images/pallorTLink.png";
				
		imgSPKMild = new Image();
		//imgSPKMild.src = "iDoc-Drawing/images/SPKMild.png";
		imgSPKMildT = new Image();
		//imgSPKMildT.src = "iDoc-Drawing/images/SPKMildT.png";
		
		imgDisciformScar = new Image();
		//imgDisciformScar.src = "iDoc-Drawing/images/disciformScar.png";
		imgDisciformScarT = new Image();
		//imgDisciformScarT.src = "iDoc-Drawing/images/disciformScarT.png";
		
		imgExudates = new Image();
		//imgExudates.src = "iDoc-Drawing/images/exudates.png";
		imgExudatesT = new Image();
		//imgExudatesT.src = "iDoc-Drawing/images/exudatesT.png";
		
		imgHemorrhageRed = new Image();
		//imgHemorrhageRed.src = "iDoc-Drawing/images/hemorrhageRed.png";
		imgHemorrhageRedT = new Image();
		//imgHemorrhageRedT.src = "iDoc-Drawing/images/hemorrhageRedT.png";
		
		imgNeovascularization = new Image();
		//imgNeovascularization.src = "iDoc-Drawing/images/neovascularization.png";
		imgNeovascularizationT = new Image();
		//imgNeovascularizationT.src = "iDoc-Drawing/images/neovascularizationT.png";
		
		imgPannus = new Image();
		//imgPannus.src = "iDoc-Drawing/images/pannus.png";
		imgPannusT = new Image();
		//imgPannusT.src = "iDoc-Drawing/images/pannusT.png";
		
		imgPingnelcula = new Image();
		//imgPingnelcula.src = "iDoc-Drawing/images/pingnelcula.png";
		imgPingnelculaT = new Image();
		//imgPingnelculaT.src = "iDoc-Drawing/images/pingnelculaT.png";
		
		imgPRPTreatment = new Image();
		//imgPRPTreatment.src = "iDoc-Drawing/images/prpTreatment.png";
		imgPRPTreatmentT = new Image();
		//imgPRPTreatmentT.src = "iDoc-Drawing/images/prpTreatmentT.png";
		
		imgPterygium = new Image();
		//imgPterygium.src = "iDoc-Drawing/images/pterygium.png";
		imgPterygiumT = new Image();
		//imgPterygiumT.src = "iDoc-Drawing/images/pterygiumT.png";
		
		imgRedDot = new Image();
		//imgRedDot.src = "iDoc-Drawing/images/redDot.png";
		imgRedDotT = new Image();
		//imgRedDotT.src = "iDoc-Drawing/images/redDotT.png";
		
		imgRetinalHemorrhageDot = new Image();
		//imgRetinalHemorrhageDot.src = "iDoc-Drawing/images/retina_hemorrhage_dot.png";
		imgRetinalHemorrhageDotT = new Image();
		//imgRetinalHemorrhageDotT.src = "iDoc-Drawing/images/retina_hemorrhage_dotT.png";
		
		imgUlcer = new Image();
		//imgUlcer.src = "iDoc-Drawing/images/ulcer.png";
		imgUlcerT = new Image();
		//imgUlcerT.src = "iDoc-Drawing/images/ulcerT.png";
		
		imgRetinalIrregularHemorrhage = new Image();
		//imgRetinalIrregularHemorrhage.src = "iDoc-Drawing/images/retinalIrregularityHemorrhage.png";
		imgRetinalIrregularHemorrhageT = new Image();
		//imgRetinalIrregularHemorrhageT.src = "iDoc-Drawing/images/retinalIrregularityHemorrhageT.png";	

		//New Icons
		imgCME = new Image();
		//imgCME.src = "iDoc-Drawing/images/Blue_dots.png";
		imgCMET = new Image();
		//imgCMET.src = "iDoc-Drawing/images/Blue_dots_text.png";
		
		imgDruMild = new Image();
		//imgDruMild.src = "iDoc-Drawing/images/drusen_mild.png";
		imgDruMildT = new Image();
		//imgDruMildT.src = "iDoc-Drawing/images/drusen_mild_text.png";
		
		imgDruMod = new Image();
		//imgDruMod.src = "iDoc-Drawing/images/drusen_moderate.png";
		imgDruModT = new Image();
		//imgDruModT.src = "iDoc-Drawing/images/drusen_moderate_text.png";
		
		imgFloaters = new Image();
		//imgFloaters.src = "iDoc-Drawing/images/floaters.png";
		imgFloatersT = new Image();
		//imgFloatersT.src = "iDoc-Drawing/images/floaters_text.png";
		
		imgFocalTreatment = new Image();
		//imgFocalTreatment.src = "iDoc-Drawing/images/Green_Dots.png";
		imgFocalTreatmentT = new Image();
		//imgFocalTreatmentT.src = "iDoc-Drawing/images/Green_Dots_text.png";
		
		/*
		imgLattice = new Image();
		imgLattice.src = "iDoc-Drawing/images/lattice.png";
		imgLatticeT = new Image();
		imgLatticeT.src = "iDoc-Drawing/images/lattice_text.png";
		*/
		
		imgMA = new Image();
		//imgMA.src = "iDoc-Drawing/images/ma.png";
		imgMAT = new Image();
		//imgMAT.src = "iDoc-Drawing/images/ma_text.png";
		
		imgPVD = new Image();
		//imgPVD.src = "iDoc-Drawing/images/pvd.png";
		imgPVDT = new Image();
		//imgPVDT.src = "iDoc-Drawing/images/pvd_text.png";		
		
		imgRPEChanges = new Image();
		//imgRPEChanges.src = "iDoc-Drawing/images/RPEChanges.png";
		imgRPEChangesT = new Image();
		//imgRPEChangesT.src = "iDoc-Drawing/images/RPEChanges_text.png";		
		
		imgERM = new Image();
		//imgERM.src = "iDoc-Drawing/images/ERM.png";		
		
		imgERMT = new Image();
		//imgERMT.src = "iDoc-Drawing/images/ERM_text.png";		
		
		strDataDefLength = this.strImgLen();
		
		//Test --
		var strPrevD=document.getElementById('hidImgDataFileName'+me.classId).value;
		if(strPrevD != ""){
			imgPrevD = new Image();
			//imgPrevD.crossOrigin = 'anonymous';
			//imgPrevD.setAttribute('crossOrigin','anonymous');			
			//alert(strPrevD);
			imgPrevD.src =""+strPrevD;
		}	
		
		//alert($("canvas").parent("div").length);
		$(canvasObj,canvasTempObj).bind('contextmenu', function(){ return false });				
		
		this.setCanvasDataDB();
		if(document.getElementById('hidImageCss'+me.classId).value == "imgLaCanvas"){
			this.setCDRation();
		}		
		
		//make pencil selected		
		this.setEvent('funPencil');			
		
	};
	
	this.strImgLen = function (){	
		var strData = canvasObj.toDataURL("image/png");
		return strData.length;
	};
	
	this.setCanvasDataDB = function(){		
		//document.getElementById('divCanvas'+me.classId).className = document.getElementById('hidImageCss'+me.classId).value;
		var blNewControlHaveData = false;
		var strRed = null, strGreen = null, strBlue = null, strAlpha = null, strFileName = null;
		var strRedWNL = null, strGreenWNL = null, strBlueWNL = null, strAlphaWNL = null;
		var arrRed = new Array();
		var arrGreen = new Array();
		var arrBlue = new Array();
		var arrAlpha = new Array();
		/*
		strRed = document.getElementById('hidRedPixel'+me.classId).value;
		strGreen = document.getElementById('hidGreenPixel'+me.classId).value;
		strBlue = document.getElementById('hidBluePixel'+me.classId).value;
		strAlpha = document.getElementById('hidAlphaPixel'+me.classId).value;
		*/
		
		strAlpha =strBlue =strGreen =strRed ="";
		strRedWNL=strGreenWNL=strBlueWNL=strAlphaWNL="";		
		
		
		strFileName = document.getElementById('hidDrawingTestImageP'+me.classId).value;		
		if((strRed != "") && (typeof(strRed) != "undefined") && (strGreen != "") && (typeof(strGreen) != "undefined") && (strBlue != "") && (typeof(strBlue) != "undefined") && (strAlpha!= "") && (typeof(strAlpha) != "undefined")){
			strRedWNL = strRed.replace(/0,/gi, "");
			strRedWNL = strRedWNL.replace(/0/gi, "");
			
			strGreenWNL = strGreen.replace(/0,/gi, "");
			strGreenWNL = strGreen.replace(/0/gi, "");
			
			strBlueWNL = strBlue.replace(/0,/gi, "");
			strBlueWNL = strBlue.replace(/0/gi, "");
			
			strAlphaWNL = strAlpha.replace(/0,/gi, "");
			strAlphaWNL = strAlpha.replace(/0/gi, "");
			
			arrRed = strRed.split(',');
			arrGreen = strGreen.split(',');
			arrBlue = strBlue.split(',');
			arrAlpha = strAlpha.split(',');			
			var myImageData = ctx.createImageData(canvasObj.width, canvasObj.height);
			var len = arrRed.length;
			for (var i = 0; i < len; i++) {
				var r = arrRed[i];			  
				var g = arrGreen[i];
				var b = arrBlue[i];
				var a = arrAlpha[i];
				myImageData.data[i * 4 + 0] = r; // Red value				
				myImageData.data[i * 4 + 1] = g; // Green value
				myImageData.data[i * 4 + 2] = b; // Blue value	
				myImageData.data[i * 4 + 3] = a; // Alpha value			
			}							
			ctx.putImageData(myImageData, 0, 0);
			/*
			document.getElementById('hidRedPixel'+me.classId).value = "";
			document.getElementById('hidGreenPixel'+me.classId).value = "";
			document.getElementById('hidBluePixel'+me.classId).value = "";
			document.getElementById('hidAlphaPixel'+me.classId).value = "";			
			*/
			blNewControlHaveData = true;
			me.arrBlCanvasHaveDrwaing[0] = false;
			if((strRedWNL != "") && (strGreenWNL != "") && (strBlueWNL != "") && (strAlphaWNL != "")){
				me.arrBlCanvasHaveDrwaing[1] = true;
			}
			else{
				me.arrBlCanvasHaveDrwaing[1] = false;
			}
		}
		
		if((strFileName != "") && (typeof(strFileName) != "undefined") && (strFileName != null) && (document.getElementById('hidImageCss'+me.classId).value == "imgDB")){
			document.getElementById('divCanvas'+me.classId).className = "imgDB imgLoad";
			blNewControlHaveData = true;
			me.arrBlCanvasHaveDrwaing[0] = false;
			me.arrBlCanvasHaveDrwaing[1] = true;
		}
		
		
		/*
		var arrImageDataDB = new Array;
		var arrDataDB = new Array;
		var arrTextDataDB = new Array;
		var strDataDB = document.getElementById("hidImagesData"+me.classId).value;		
		if((strDataDB != "") && (strDataDB != "$~$")){
			arrDataDB = strDataDB.split("$~$");
			arrImageDataDB = arrDataDB[0].split("~");
			arrTextDataDB = arrDataDB[1].split("~");
			if((this.JSIsArray(arrImageDataDB) == true) && (arrImageDataDB.length > 0) && (strDataDB != "")){
				this.drawDBImagesData(arrImageDataDB);
				blNewControlHaveData = true;
				me.arrBlCanvasHaveDrwaing[0] = false;
				me.arrBlCanvasHaveDrwaing[1] = true;
			}
			if((this.JSIsArray(arrTextDataDB) == true) && (arrTextDataDB.length > 0) && (strDataDB != "")){
				this.drawDBTextData(arrTextDataDB);
				blNewControlHaveData = true;
				me.arrBlCanvasHaveDrwaing[0] = false;
				me.arrBlCanvasHaveDrwaing[1] = true;
			}
		}
		*/
		
		
		
		var strImageData = (document.getElementById("hidOldAppletImaData"+me.classId)) ? document.getElementById("hidOldAppletImaData"+me.classId).value : "" ;
		//strImageData = "";
		
		
		
		if( (strImageData != "")){
			strImageData += '?' + (new Date()).getTime();

			//alert(strImageData);
			
			var tempInt = 0;
			var imgD = new Image();
			imgD.src = strImageData;
			//var imgOriD = new Image();
			//imgOriD.src = "iDoc-Drawing/images/oldAppletOptical.png";
			//alert(imgOriD.src);
			tempInt = setInterval(function() {
					if(imgD.complete == true){
						document.getElementById('divCanvas'+me.classId).className = "imgNoImage";
						document.getElementById('hidImageCss'+me.classId).value = "imgNoImage";
						/*imgOriD.width = canvasObj.width;
						imgOriD.height = canvasObj.height;
						imgD.width = canvasObj.width;
						imgD.height = canvasObj.height;
						*/
						ctx.drawImage(imgD, 0, 0, canvasObj.width, canvasObj.height);
						me.arrBlCanvasHaveDrwaing[0] = false;
						me.arrBlCanvasHaveDrwaing[1] = true;
						//alert(imgOriD.width);
						me.getSetOldImage(ctx, canvasObj, imgD);											
						clearInterval(tempInt);
					}
				}
			,zTimeoutMM);
		}
		else{
			document.getElementById('divCanvas'+me.classId).className = document.getElementById('hidImageCss'+me.classId).value;
			
			//Add Image --
			//*
			var strImg = document.getElementById('hidImgDataFileName'+me.classId).value;
			if(strImg!=""){				
				//document.writeln("<img src='"+imgD.src+"'>");
				imgPrevD.onload = function()
				{
					if(ctx){						
						ctx.drawImage(imgPrevD, 0,0);	
						me.arrBlCanvasHaveDrwaing[0] = false;
						me.arrBlCanvasHaveDrwaing[1] = true;	
					}
				}				
			}
			//*/
			//Add Image --
			
		}
	};
	
	this.getSetOldImage = function(target, obj, imgD){
		/*ctx.drawImage(imgOriD, 0, 0, obj.width, obj.height);
		ctxTemp.drawImage(imgD, 0, 0, obj.width, obj.height);		
		var arrRedPixel = new Array();
		var arrGreenPixel = new Array();
		var arrBluePixel = new Array();
		var arrAlphaPixel = new Array();		
		var frameOri = ctx.getImageData(0, 0, obj.width, obj.height);
		var frameTempImg = ctxTemp.getImageData(0, 0, obj.width, obj.height);
		var l = frameOri.data.length / 4;
		//alert(frameOri.data.length);
		for (var i = 0; i < l; i++) {
			var rOri = frameOri.data[i * 4 + 0];			  
			var gOri = frameOri.data[i * 4 + 1];
			var bOri = frameOri.data[i * 4 + 2];
			var aOri = frameOri.data[i * 4 + 3];
			
			var rTempImg = frameTempImg.data[i * 4 + 0];			  
			var gTempImg = frameTempImg.data[i * 4 + 1];
			var bTempImg = frameTempImg.data[i * 4 + 2];
			var aTempImg = frameTempImg.data[i * 4 + 3];
			
			if((rOri == rTempImg) && (gOri == gTempImg) && (bOri == bTempImg)){
				arrRedPixel[i] = 0;
				arrGreenPixel[i] = 0;
				arrBluePixel[i] = 0;
				arrAlphaPixel[i] = 0;
			}
			else{			
				arrRedPixel[i] = rTempImg;
				arrGreenPixel[i] = gTempImg;
				arrBluePixel[i] = bTempImg;
				arrAlphaPixel[i] = aTempImg;
			}
		}
		this.clearMainCanvas();
		this.clearTempCanvas("none");
		var myImageData = target.createImageData(obj.width, obj.height);
		var len = arrRedPixel.length;
		for (var i = 0; i < len; i++) {
			var r = arrRedPixel[i];			  
			var g = arrGreenPixel[i];
			var b = arrBluePixel[i];
			var a = arrAlphaPixel[i];
			myImageData.data[i * 4 + 0] = r;			
			myImageData.data[i * 4 + 1] = g;
			myImageData.data[i * 4 + 2] = b;
			myImageData.data[i * 4 + 3] = a;
		}							
		target.putImageData(myImageData, 0, 0);			
		return;
		*/
		//////////////////////////////
		var arrRedPixel = new Array();
		var arrGreenPixel = new Array();
		var arrBluePixel = new Array();
		var arrAlphaPixel = new Array();		
		var frame = target.getImageData(0, 0, obj.width, obj.height);
		var l = frame.data.length / 4;
		//alert(l);
		//console.log(l);
		//return false;
		for (var i = 0; i < l; i++) {
			var r = frame.data[i * 4 + 0];			  
			var g = frame.data[i * 4 + 1];
			var b = frame.data[i * 4 + 2];
			var a = frame.data[i * 4 + 3];	
			if(((r == 255) && (g == 255) && (b == 255) && (a > 0))){
				//console.log("yes");
				r = 0;
				g = 0;
				b = 0;
				a = 0;
			}			
			arrRedPixel[i] = r;
			arrGreenPixel[i] = g;
			arrBluePixel[i] = b;
			arrAlphaPixel[i] = a;
		}						
		this.clearMainCanvas();
		var myImageData = target.createImageData(obj.width, obj.height);
		var len = arrRedPixel.length;
		for (var i = 0; i < len; i++) {
			var r = arrRedPixel[i];			  
			var g = arrGreenPixel[i];
			var b = arrBluePixel[i];
			var a = arrAlphaPixel[i];
			myImageData.data[i * 4 + 0] = r;			
			myImageData.data[i * 4 + 1] = g;
			myImageData.data[i * 4 + 2] = b;
			myImageData.data[i * 4 + 3] = a;
			//if((r == 0) && (g == 0) && (b == 0)){				
				//console.log("yes-put");				
			//}		
		}							
		target.putImageData(myImageData, 0, 0);
	};
	
	this.drawDBImagesData = function(arrImageDataDB){
		//alert(arrImageDataDB);
		var arrTemp = new Array();
		for(var a = 0; a < arrImageDataDB.length; a++){
			//alert(arrImageDataDB[a]);
			if(arrImageDataDB[a]){
				var arrImageDataDBTemp = new Array();
				arrImageDataDBTemp = arrImageDataDB[a].split(",");
				//alert(arrTemp[6]);								
				arrTemp[a] = new Array();
				arrTemp[a][0] = arrImageDataDBTemp[0];//path;
				arrTemp[a][1] = arrImageDataDBTemp[1];//x;
				arrTemp[a][2] = arrImageDataDBTemp[2];//y;
				arrTemp[a][3] = arrImageDataDBTemp[3];//w;
				arrTemp[a][4] = arrImageDataDBTemp[4];//h;
				arrTemp[a][5] = arrImageDataDBTemp[5];//moveToX;
				arrTemp[a][6] = arrImageDataDBTemp[6];//moveToY;
				arrTemp[a][7] = arrImageDataDBTemp[7];//lineToX;
				arrTemp[a][8] = arrImageDataDBTemp[8];//lineToY;
				arrTemp[a][9] = arrImageDataDBTemp[9];//textX;
				arrTemp[a][10] = arrImageDataDBTemp[10];//textY;
				arrTemp[a][11] = arrImageDataDBTemp[11];//textW;
				arrTemp[a][12] = arrImageDataDBTemp[12];//textH;				
				arrTemp[a][13] = arrImageDataDBTemp[13];//path;
				arrTemp[a][14] = arrImageDataDBTemp[14];//pathT;
				
				arrTemp[a][15] = arrImageDataDBTemp[15];//strSmartTag;
				arrTemp[a][16] = arrImageDataDBTemp[16];//intSmartTagXPos;
				arrTemp[a][17] = arrImageDataDBTemp[17];//intSmartTagYPos;
				arrTemp[a][18] = arrImageDataDBTemp[18];//strSmartTagID;				
				arrTemp[a][19] = arrImageDataDBTemp[19];//Smart Tag Master ID
				arrTemp[a][20] = arrImageDataDBTemp[20];//Smart Tag Child ID
				arrTemp[a][21] = arrImageDataDBTemp[21];//Clock Wise Angle
				arrTemp[a][22] = arrImageDataDBTemp[22];//Anti Clock Wise Angle
			}
		}
		//alert(arrTemp);
		if(arrTemp.length > 0){
			this.drwaImagesOnMain(arrTemp, "DB");	
		}
	};
	
	this.drawDBTextData = function(arrTextDataDB){
		//alert(arrTextDataDB);
		var arrTemp = new Array();
		for(var a = 0; a < arrTextDataDB.length; a++){
			//alert(arrTextDataDB[a]);
			if(arrTextDataDB[a]){
				var arrTextDataDBTemp = new Array();
				arrTextDataDBTemp = arrTextDataDB[a].split(",");
				//alert(arrTemp[1]);								
				arrTemp[a] = new Array();
				arrTemp[a][0] = arrTextDataDBTemp[0];//x;
				arrTemp[a][1] = arrTextDataDBTemp[1];//y;
				arrTemp[a][2] = arrTextDataDBTemp[2];//text;
			}			
		}
		//alert(arrTemp);
		if(arrTemp.length > 0){
			this.writeTextOnMain(arrTemp, "DB");	
		}
	}
	
	this.setCDRation = function(){
		if((document.getElementById("hidCDRationOD")) && (document.getElementById("hidCDRationOS"))){
			this.strCDRatioOD = document.getElementById("hidCDRationOD").value;
			this.strCDRatioOS = document.getElementById("hidCDRationOS").value;
		}
		if((this.strCDRatioOD != null) && (this.strCDRatioOD != "")){
			ctx.beginPath();
			ctx.font = "14px Arial";
			ctx.fillStyle = '#171717';
			
			var text = "C:D: " + this.strCDRatioOD
			var textLength = ctx.measureText(text).width;
			var x = 150;
			var y = 420;
			me.clear(x, y - 10, textLength, 14);
			
			ctx.fillText(text, x, y);
			ctx.closePath();
			//me.arrBlCanvasHaveDrwaing[0] = true;
		}
		if((this.strCDRatioOS != null) && (this.strCDRatioOS != "")){
			ctx.beginPath();
			ctx.font = "14px Arial";
			ctx.fillStyle = '#171717';
			
			var text = "C:D: " + this.strCDRatioOS
			var textLength = ctx.measureText(text).width;
			var x = 500;
			var y = 420;
			me.clear(x, y - 10, textLength, 14);
			
			ctx.fillText(text, x, y);
			ctx.closePath();
			//me.arrBlCanvasHaveDrwaing[0] = true;
		}
	};
	
	this.drawSelect = function(e){
		this.getEvt(e, "block");
		intSelectStartX = mouseX;
		intSelectStartY = mouseY;
	};
	this.drawWithPencil = function(e){
		this.getEvt(e, "none");
		if(mousePressed){
			ctx.beginPath();
			var a = parseFloat(me.lineType);
			ctx.strokeStyle = currentColor;
			ctx.fillStyle = currentColor;
			
			//var x = mouseX - Math.abs((a / 2));
			//ctx.fillRect(x, mouseY, a, a);
			ctx.lineCap = "round";			
			
			ctx.stroke();
			ctx.closePath();
			
			//window.status=mouseX+", "+mouseY;
			
			//me.arrBlCanvasHaveDrwaing[0] = true;
			me.arrBlCanvasHaveDrwaing[0] = false;
			me.arrBlCanvasHaveDrwaing[1] = false;
			
			me.makeCanvasActive();
		}
		ctx.beginPath();//alert('mouseX='+mouseX)
		ctx.moveTo( mouseX, mouseY );
	};
	this.drawWithBrush = function(e){					
		this.getEvt(e, "none");
		ctx.lineCap = "square";
		ctx.beginPath();
		ctx.moveTo( mouseX, mouseY );
	};
	this.drawFillColor = function(e){
		me.arrBlCanvasHaveDrwaing[0] = true;
		document.getElementById("cCanvas"+me.classId).className = "cCanvas";
		me.makeCanvasActive();
		me.arrBlCanvasHaveDrwaing[1] = true;
		this.getEvt(e, "none");
		var left = parseInt(intSelectStartX);					
		var right = parseInt(intSelectStartX) + parseInt(intSelectWidth);						
		var top = parseInt(intSelectStartY);
		var bottom = parseInt(intSelectStartY) + parseInt(intSelectHieght);						
		if ((parseInt(mouseX) <= right) && (parseInt(mouseX) >= left) && (parseInt(mouseY) <= bottom) && (parseInt(mouseY) >= top)){
			this.drawRectangle(intSelectStartX, intSelectStartY, intSelectWidth, intSelectHieght, ctx, "ctx");
			ctx.fillStyle = currentColor;
			ctx.fillRect(intSelectStartX, intSelectStartY, intSelectWidth, intSelectHieght);
		}
		else if(arrEmtRect.length > 0){						
			for(var i in arrEmtRect){
				var left = parseInt(arrEmtRect[i][0]);					
				var right = parseInt(arrEmtRect[i][0]) + parseInt(arrEmtRect[i][2]);						
				var top = parseInt(arrEmtRect[i][1]);
				var bottom = parseInt(arrEmtRect[i][1]) + parseInt(arrEmtRect[i][3]);												
				var floodFillMouseX = parseInt(mouseX);
				var floodFillMouseY = parseInt(mouseY);
				if ((parseInt(floodFillMouseX) <= right) && (parseInt(floodFillMouseX) >= left) && (parseInt(floodFillMouseY) <= bottom) && (parseInt(floodFillMouseY) >= top)){				
					var floodFillStartX = parseInt(arrEmtRect[i][0]);
					var floodFillStartY = parseInt(arrEmtRect[i][1]);
					var floodFillWidth = parseInt(arrEmtRect[i][2]);
					var floodFillHieght = parseInt(arrEmtRect[i][3]);
					var arrRGBReturn = new Array();
					arrRGBReturn = this.hexToRGB(currentColor);
					var nR = 0, nG = 0, nB = 0;
					nR = arrRGBReturn[0];
					nG = arrRGBReturn[1];
					nB = arrRGBReturn[2];
					this.flood(floodFillStartX, floodFillStartY, floodFillWidth, floodFillHieght, nR, nG, nB, floodFillMouseX, floodFillMouseY);	
					break;						
				}
				else{
					var floodFillMouseX = parseInt(mouseX);
					var floodFillMouseY = parseInt(mouseY);
					var arrRGBReturn = new Array();
					arrRGBReturn = this.hexToRGB(currentColor);
					var nR = 0, nG = 0, nB = 0;
					nR = arrRGBReturn[0];
					nG = arrRGBReturn[1];
					nB = arrRGBReturn[2];
					this.flood(0, 0, canvasObj.width, canvasObj.height, nR, nG, nB, floodFillMouseX, floodFillMouseY);
				}
				break;
			}					
		}
		else{
			var floodFillMouseX = parseInt(mouseX);
			var floodFillMouseY = parseInt(mouseY);
			var arrRGBReturn = new Array();
			arrRGBReturn = this.hexToRGB(currentColor);
			var nR = 0, nG = 0, nB = 0;
			nR = arrRGBReturn[0];
			nG = arrRGBReturn[1];
			nB = arrRGBReturn[2];
			//console.log("OUT" + ", x:" + floodFillMouseX + ", y:" + floodFillMouseY);
			this.flood(0, 0, canvasObj.width, canvasObj.height, nR, nG, nB, floodFillMouseX, floodFillMouseY);
		}
	};			
	this.flood = function(startX, startY, drawingAreaWidth, drawingAreaHeight, nR, nG, nB, floodFillMouseX, floodFillMouseY){
		//console.log("SX: " + startX +",SY: "+ startY +",W: "+ drawingAreaWidth +",H: "+ drawingAreaHeight);
		colorLayerData = ctx.getImageData(0, 0, canvasObj.width, canvasObj.height);
		//var pixelPos = ( (floodFillMouseY * drawingAreaWidth) + floodFillMouseX ) * 4;
		var data = ctx.getImageData(floodFillMouseX, floodFillMouseY, 1, 1).data; 
		//var pixelPos= (floodFillMouseX + floodFillMouseY * drawingAreaWidth) * 4;
		//console.log("x: " + floodFillMouseX +" y: "+ floodFillMouseY);
		//console.log("outline: " + outlineLayerData.data[pixelPos + 0] +","+ outlineLayerData.data[pixelPos + 1] +","+ outlineLayerData.data[pixelPos + 2] +","+ outlineLayerData.data[pixelPos + 3]);				
		var r = data[0];
		var g = data[1];
		var b = data[2];
		var a = data[3];
		//console.log("clicked color:   " + r +","+ g +","+ b +","+ a);				
		clickedColorR = r;
		clickedColorG = g;
		clickedColorB = b;
		newColorR = nR;
		newColorG = nG;
		newColorB = nB;
		/*arrPreClickCol = new Array();
		arrPreClickCol[0] = clickedColorR;
		arrPreClickCol[1] = clickedColorG;
		arrPreClickCol[2] = clickedColorB;
		*/
		//console.log("clickedColor:   " + clickedColorR +","+ clickedColorG +","+ clickedColorB);								
		//console.log("new color:   " + newColorR +","+ newColorG +","+ newColorB);				
		if(startX != 0 && startY != 0){
			if(clickedColorR == newColorR && clickedColorG == newColorG && clickedColorB == newColorB){
				//console.log("Return because trying to fill with the same color");
				return;
			}
		}
		
		//if(outlineLayerData.data[pixelPos] + outlineLayerData.data[pixelPos+1] + outlineLayerData.data[pixelPos+2] == 0 && outlineLayerData.data[pixelPos+ 3] == 255){
		if(r + g + b == 0 && a == 255){	
			//console.log("Return because clicked outline: " + outlineLayerData.data[pixelPos+4]);
			return;
		}
		//console.log("pixelStack PUSH: " + (startX) + "," + (startY));
		//pixelStack = [[startX, startY]];
		pixelStack = [[floodFillMouseX, floodFillMouseY]];
		//console.log("pixelStack: " + pixelStack);
		this.floodFill(startX, startY, drawingAreaWidth, drawingAreaHeight, canvasObj.width, canvasObj.height);
	}; 			
	this.floodFill = function(drawingAreaX, drawingAreaY, drawingAreaWidth, drawingAreaHeight, canvasWidth, canvasHeight){
		var newPos, x, y, pixelPos, blReachLeft, blReachRight;
		var drawingBoundLeft = drawingAreaX;
		var drawingBoundTop = drawingAreaY;
		var drawingBoundRight = drawingAreaX + drawingAreaWidth - 1;
		var drawingBoundBottom = drawingAreaY + drawingAreaHeight - 1;
		while(pixelStack.length > 0){
			newPos = pixelStack.pop();
			x = newPos[0];
			y = newPos[1];					
			//pixelPos = (y * canvasWidth + x) * 4;
			pixelPos= (x + y * canvasWidth) * 4;
			// Go up as long as the color matches and are inside the canvas					
			//console.log("UP1: " + this.seeBoundary(pixelPos) + "," + y);
			while(y-- >= drawingBoundTop && this.seeBoundary(pixelPos)){
				//console.log("UP: " + seeBoundary(x, y) + "," + y);
				pixelPos -= canvasWidth * 4;
			}
			pixelPos += canvasWidth * 4;
			++y;
			blReachLeft = false;
			blReachRight = false;
			// Go down as long as the color matches and in inside the canvas
			while(y++ < drawingBoundBottom && this.seeBoundary(pixelPos)){				
				//console.log("DOWN: " + seeBoundary(x, y) + "," + y + "," + x + "," + arrResultReturn[0]+ "," + arrResultReturn[1]+ "," + arrResultReturn[2]+ "," + arrResultReturn[3]);
				//console.log("test");
				this.colorPixel(pixelPos);
				if(x > drawingBoundLeft){
					if(this.matchClickedColor(pixelPos - 4)){
						if(blReachLeft == false){
							pixelStack.push([x - 1, y]);
							//console.log("PUSH: " + ((x-1) - drawingAreaX - 2) + "," + (y - drawingAreaY - 2));
							blReachLeft = true;
						}
					}
					else if(blReachLeft == true){
						blReachLeft = false;
					}
				}
				if(x < drawingBoundRight){
					if(this.matchClickedColor(pixelPos + 4)){
						if(blReachRight == false){
							pixelStack.push([x + 1, y]);
							//console.log("PUSH: " + ((x+1) - drawingAreaX - 2) + "," + (y - drawingAreaY - 2));
							blReachRight = true;
						}
					}
					else if(blReachRight == true){
						blReachRight = false;
					}
				}				
				pixelPos += canvasWidth * 4;
			}
		}
		this.redraw(ctx);
	};
	this.redraw = function(target){
		pixelStack = new Array();				
		newColorR = null, newColorG = null, newColorB = null, newColorA = null, clickedColorR = null, clickedColorG = null, clickedColorB = null, clickedColorA = null;
		target.clearRect(0, 0, canvasObj.width, canvasObj.height);
		if(colorLayerData){
			target.putImageData(colorLayerData, 0, 0);
			colorLayerData = target.getImageData(0, 0, canvasObj.width, canvasObj.height);
		}
	};
	this.colorPixel = function(pixelPos){
		colorLayerData.data[pixelPos] = newColorR;
		colorLayerData.data[pixelPos+1] = newColorG;
		colorLayerData.data[pixelPos+2] = newColorB;
		colorLayerData.data[pixelPos+3] = 255;
	};
	this.seeBoundary = function(pixelPos){								
		var r = colorLayerData.data[pixelPos];	
		var g = colorLayerData.data[pixelPos+1];	
		var b = colorLayerData.data[pixelPos+2];
		//console.log("seeBoundary: " + r + "," + g + "," + b + "clickedColor:" + clickedColorR + "," + clickedColorG + "," + clickedColorG + "newColor:" + newColorR + "," + newColorG + "," + newColorB);
		//if(clickedColorR == newColorR && clickedColorG == newColorG && clickedColorG == newColorB){
		//console.log(((r == 0) && (g == 0) && (b == 0)));
		//console.log(((r != newColorR) && (g != newColorG) && (b != newColorB)));
		//console.log((clickedColorR + clickedColorG + clickedColorB) != (newColorR + newColorG + newColorB));
		//var preColor = parseInt(arrPreClickCol[0]) + parseInt(arrPreClickCol[1]) + parseInt(arrPreClickCol[2]);
		//var clickedColorNew = parseInt(clickedColorR) + parseInt(clickedColorG) + parseInt(clickedColorB);
		//var newColorR = parseInt(clickedColorR) + parseInt(clickedColorG) + parseInt(clickedColorB);
		if(((r == 0) && (g == 0) && (b == 0)) || ((clickedColorR + clickedColorG + clickedColorB) != (newColorR + newColorG + newColorB))){
			if(((clickedColorR + clickedColorG + clickedColorB) != (newColorR + newColorG + newColorB)) && ((clickedColorR + clickedColorG + clickedColorB) > 0)){
				if(((r == 0) && (g == 0) && (b == 0))){
					//console.log("seeBoundary Result2: FALSE");
					return false;
				}
				else{
					return true;
				}
			}
			else if(((r == 0) && (g == 0) && (b == 0))){
				//console.log("seeBoundary Result1: TRUE");
				return true;
			}
			else{
				//console.log("seeBoundary Result3: FALSE");
				return false;
			}
		}		
		else{
			//console.log("seeBoundary Result3: FALSE");
			return false;
		}
	};			
	this.matchClickedColor = function(pixelPos){
		var r = colorLayerData.data[pixelPos];	
		var g = colorLayerData.data[pixelPos+1];	
		var b = colorLayerData.data[pixelPos+2];				
		// If the current pixel matches the clicked color
		if(r == clickedColorR && g == clickedColorG && b == clickedColorB){
			return true;
		}
		// If current pixel matches the new color
		if(r == newColorR && g == newColorG && b == newColorB){
			return false;
		}
		return true;
	};
	this.hexToRGB = function(color){
		if(color == "#000000"){
			var arrRGB = new Array(17, 17, 17);
		}
		else{
			function HexToR(color){
				return parseInt((cutHex(color)).substring(0,2),16)
			}
			function HexToG(color){
				return parseInt((cutHex(color)).substring(2,4),16)
			}
			function HexToB(color){
				return parseInt((cutHex(color)).substring(4,6),16)
			}
			function cutHex(color){
				return (color.charAt(0)=="#") ? color.substring(1,7) : color
			}
			var arrRGB = new Array(HexToR(color), HexToG(color), HexToB(color));
		}
		return arrRGB;
	};
	this.drawSparyColor = function(e){
		this.getEvt(e, "none");
		if(mousePressed){
			ctx.beginPath();
			ctx.strokeStyle = currentColor;
			sprayWidth = parseFloat(me.lineType);
			for(var i = sprayWidth*25; i>0; i--) {
				var rndx = mouseX + Math.round(Math.random() * ((sprayWidth * 16) - (sprayWidth * 8)));
				var rndy = mouseY + Math.round(Math.random() * ((sprayWidth * 16) - (sprayWidth * 8)));
				//alert(rndx+"--"+rndy);
				me.drawDot(rndx, rndy, 1, ctx.strokeStyle);
				//me.drawDot(mouseX, mouseY, 1, ctx.strokeStyle);
			}
			ctx.closePath();
			me.arrBlCanvasHaveDrwaing[0] = true;
			me.arrBlCanvasHaveDrwaing[1] = true;
			me.makeCanvasActive();
		}
		ctx.beginPath();
		me.arrBlCanvasHaveDrwaing[0] = true;
		document.getElementById("cCanvas"+me.classId).className = "cCanvas";
		me.makeCanvasActive();
		me.arrBlCanvasHaveDrwaing[1] = true;
	};
	this.drawEraser = function(e){
		this.getEvt(e, "none");
	};
	this.writeTAValueCanvas = function(obj, e){		
		//alert(e.keyCode);
		if(e.keyCode == 13){
			var TAx = parseInt(gbArrCurrentTAXY[0]);
			var TAy = parseInt(gbArrCurrentTAXY[1]);
			if((TAx > 0) && (TAy > 0)){
				for (var i in gbArrTextMain){
					var x = parseInt(gbArrTextMain[i][0]);
					var y = parseInt(gbArrTextMain[i][1]);
					var strOriText = gbArrTextMain[i][2];
					var intOriTextWidth = parseInt(ctx.measureText(strOriText).width);
					var strTAName = gbArrTextMain[i][3];
					if((TAx == x) && (TAy == y) && (obj.id == strTAName)){
						var strTAValEdit = obj.value;
						var textWidth = parseInt(ctx.measureText(strTAValEdit).width);
						if(textWidth < 1){
							textWidth = 100;
						}
						gbArrTextMain[i][2] = strTAValEdit;						
						gbArrTextDB[i][2] = strTAValEdit;						
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.makeCanvasActive();
						me.arrBlCanvasHaveDrwaing[1] = true;
						var objTA = document.getElementById(strTAName);
						objTA.style.display = "none";
						objTA.value = strTAValEdit;
						objTA.style.width = textWidth;
						me.clear(x, y - 10, intOriTextWidth, 14);
						ctx.beginPath();
						ctx.font = "14px Arial";
						ctx.fillStyle = '#171717';
						ctx.fillText(strTAValEdit, x, y);
						ctx.closePath();
					}
				}
			}
			gbArrCurrentTAXY[0] = 0;
			gbArrCurrentTAXY[1] = 0;
			gbArrCurrentTAXY = new Array();			
		}
		else if(e.keyCode == 27){
			me.hideTATempCanvas();			
			gbArrCurrentTAXY[0] = 0;
			gbArrCurrentTAXY[1] = 0;
			gbArrCurrentTAXY = new Array();
		}		
	};
	
	this.hideTATempCanvas = function(){		
		for (var i in gbArrTextMain){
			var strTAName = gbArrTextMain[i][3];
			var objTA = document.getElementById(strTAName);
			objTA.value = gbArrTextMain[i][2];			
			objTA.style.display = "none";
		}
		me.clearTempCanvas("none");
	};
	
	this.createDOMTA = function(text, strTAName, target){
		var textWidth = parseInt(target.measureText(text).width);
		if(textWidth < 100){
			textWidth = 150;
		}
		textWidth = textWidth+"px";
		var drawingTATemp = document.createElement("textarea");
		drawingTATemp.setAttribute("name", strTAName);
		drawingTATemp.setAttribute("id", strTAName);				
		drawingTATemp.setAttribute("wrap", "PHYSICAL");				
		drawingTATemp.onkeydown = function(){
			me.writeTAValueCanvas(this, event);
		};
		drawingTATemp.onblur = function(){
			me.hideTATempCanvas();
		};
		document.body.appendChild(drawingTATemp);
		var objTA = document.getElementById(strTAName);
		objTA.style.display = "none";
		objTA.value = text;
		objTA.style.width = textWidth;
	};
	
	this.drawText = function(e, x, y){
		x = x || 0;
		y = y || 0;
		var defText = "Please Enter Your Text on Drawing.......";
		if(typeof(e) == "object"){
			if(isIPad == false){
				this.onMouseDown(e);
			}			
			else{
				this.onTouchStart(e);
			}				
			var writeAtX = mouseX;
			var writeAtY = mouseY;
			gbEventTypeTemp = "funText";
			var reply = prompt("Please Enter Your Text", defText);
			reply = ""+reply.replace(defText,"");	
			if (reply != null && reply != ""){
				ctx.beginPath();
				ctx.font = "14px Arial";
				ctx.fillStyle = '#171717';
				ctx.fillText(reply, writeAtX, writeAtY);
				ctx.closePath();
				this.eventType = "funText";
				gbEventType = "funText";
				gbEventTypeTemp = "";
				me.arrBlCanvasHaveDrwaing[0] = true;
				me.makeCanvasActive();
				me.arrBlCanvasHaveDrwaing[1] = true;
				
				var strTAName = "drawingTA"+intTextCounterMain;
				me.createDOMTA(reply, strTAName, ctx);
				/*var textWidth = parseInt(ctx.measureText(reply).width);
				if(textWidth < 100){
					textWidth = 150;
				}
				textWidth = textWidth+"px";
				//alert(textWidth);
				var strTAName = "drawingTA"+intTextCounterMain;
				var drawingTATemp = document.createElement("textarea");
				drawingTATemp.setAttribute("name", strTAName);
				drawingTATemp.setAttribute("id", strTAName);				
				drawingTATemp.setAttribute("wrap", "PHYSICAL");				
				drawingTATemp.onkeydown = function(){
					me.writeTAValueCanvas(this, event);
				};
				drawingTATemp.onblur = function(){
					me.hideTATempCanvas();
				};
				document.body.appendChild(drawingTATemp);
				var objTA = document.getElementById(strTAName);
				objTA.style.display = "none";
				objTA.value = reply;
				objTA.style.width = textWidth;
				*/
				gbArrTextMain[intTextCounterMain] = new Array();
				gbArrTextMain[intTextCounterMain][0] = writeAtX;
				gbArrTextMain[intTextCounterMain][1] = writeAtY;
				gbArrTextMain[intTextCounterMain][2] = reply;
				gbArrTextMain[intTextCounterMain][3] = strTAName;
				
				gbArrTextDB[intTextCounterMain] = new Array();
				gbArrTextDB[intTextCounterMain][0] = writeAtX;
				gbArrTextDB[intTextCounterMain][1] = writeAtY;
				gbArrTextDB[intTextCounterMain][2] = reply;
				
				intTextCounterMain++;
			}
		}
		else if((x > 0) && (y > 0)){	
			var writeAtX = x;
			var writeAtY = y;
			gbEventTypeTemp = "funDrwaArrowText";
			this.eventType = null;
			gbEventType = null;			
			var reply = prompt("Please Enter Your Text", defText);
			reply = ""+reply.replace(defText,"");	
			if (reply != null && reply != ""){
				ctx.beginPath();
				ctx.font = "14px Arial";						
				ctx.fillStyle = '#171717';
				ctx.fillText(reply, writeAtX, writeAtY);
				ctx.closePath();				
			}
			this.eventType = "funDrwaArrowText";
			gbEventType = "funDrwaArrowText";
			gbEventTypeTemp = "";
			me.arrBlCanvasHaveDrwaing[0] = true;
			me.makeCanvasActive();
			me.arrBlCanvasHaveDrwaing[1] = true;
		}
		mousePressed = false;
	};
	
	this.drawNE = function(wh,ne){		
		//var angle = Math.PI * 0.58;
		var centerY = canvasObj.height - 300;
		
		if(wh=="OD"){
			//var writeAtX = 80;
			//var writeAtY = canvasObj.height/2;
			
			var writeAtX = 80;
			var writeAtY = canvasObj.height-10;
			
			//alert(writeAtX+" - "+writeAtY);
			
			var stPoint=stPoint2=0;
			var endPoint=canvasObj.width/2;
			var centerX = canvasObj.width / 5;
			
			ctx.clearRect(80,430,220,30);
			
		}else{
			//var writeAtX = 440;
			//var writeAtY = canvasObj.height/2;			
			
			var writeAtX = 440;
			var writeAtY = canvasObj.height-10;
			
			var stPoint=canvasObj.width/2;
			var endPoint=canvasObj.width;
			var stPoint2=0;
			var centerX = canvasObj.width / 1.43;
			
			ctx.clearRect(440,430,220,30);
			
		}		
		
		
		if(ne==true){
			//me.drawStraightLine( stPoint, 0, endPoint, canvasObj.height, "ctx", false);		
			//me.drawStraightLine( endPoint,0 , stPoint, canvasObj.height, "ctx", false);		
			var reply = "Not Examined";
			ctx.beginPath();
			ctx.font = "34px Arial";						
			ctx.fillStyle = 'red';			
			
			ctx.fillText(reply, writeAtX, writeAtY);//org
			
			
			ctx.closePath();				
		//}	
		}		
		me.arrBlCanvasHaveDrwaing[0] = true;
		me.makeCanvasActive();
		me.arrBlCanvasHaveDrwaing[1] = true;
	};
	//<!--Menu-->
	this.showMenu = function(){
		
		var strX="";
		
		//Making Menu Options default --
		
		if(arrMenu.length>0){
			for(var x in arrMenu){				
				strX+="<li onclick=\"drw_addtxt(this,"+me.classId+")\">"+arrMenu[x]+"</li>";
			}
		}
		
		//Making Menu Options default--	
		
		if(strX!=""){
			var str = "<div class=\"drw_menu\">"+
					"<ul>"+
						strX+
						//"<li onclick=\"drw_addtxt(this,"+me.classId+")\">Allergy</li>"+
			
					"</ul>"+				
					"</div>";		
			
			$("body").append(str);
		
			//var tmpx = (mouseX1)	
			///window.status=mouseX1+" - "+mouseY1;
			//var tmp = (mouseY1>400) ? mouseY1-400 : mouseY1;
			var tmp = mouseY1;
			$(".drw_menu").css({"display":"block", "left":mouseX1+"px", "top":tmp+"px", "z-index":2000});
			gbEventType="";
			//clear past selection
			$("#divDrawing"+me.classId+" span").removeClass("drwactive");
		}	
			this.setEvent("releaseEvent");			
				
	}
	this.addtxt = function(obj){
		
		if(obj.innerText != ""){		
			
			reply = obj.innerText;
			ctx.beginPath();
			ctx.font = "14px Arial";						
			ctx.fillStyle = '#171717';
			ctx.fillText(reply, mouseX, mouseY);			
			ctx.closePath();	
			
			var i = gbArrTextMain.length;
			gbArrTextMain[i]=[];
			gbArrTextMain[i][0]=mouseX;
			gbArrTextMain[i][1]=mouseY;
			gbArrTextMain[i][2]=reply;
			intTextCounterMain=gbArrTextMain.length;			
		
		}
		
		me.hideMenu();
		
	}
	this.addArrMenu = function(str){
		if(arrMenu.indexOf(""+str)==-1){arrMenu[arrMenu.length]=""+str;}
	};
	this.hideMenu = function(){
		if(gbEventType=="evrightclick"){gbEventType="";}
		$(".drw_menu").remove();		
		
	};
	//COI = ClickOnImage
	this.checkCOI =function(e){
		var mX = e.offsetX;
		var mY = e.offsetY;

		for(var i = 0; i < gbArrDrawingImages.length; i++){

			var imageX = parseInt(gbArrDrawingImages[i][0]);
			var imageY = parseInt(gbArrDrawingImages[i][1]);
			var imageW = parseInt(gbArrDrawingImages[i][2]);
			var imageH = parseInt(gbArrDrawingImages[i][3]);

			var imageLeft = imageX;
			var imageRight = imageX + imageW;
			var imageTop = imageY;
			var imageBottom = imageY + imageH;

			if((mX < imageRight) && (mX > imageLeft) && 
				(mY < imageBottom) && (mY > imageTop)){
				return 1;
				break;	
			}

		}	
		return 0;
	}
	//<!--Menu-->
	this.drwaArrowText = function(e){
		this.getEvt(e, "block");
		intArrowStartX = mouseX;
		intArrowStartY = mouseY;
	};
	this.drawLine = function(e){
		this.getEvt(e, "block");
		intLineStartX = mouseX;
		intLineStartY = mouseY;				
	};
	this.drawArc = function(e){
		this.getEvt(e, "block");
		intArcStartX = mouseX;
		intArcStartY = mouseY;				
	};
	this.drawEmptyRect = function(e){
		this.getEvt(e, "block");				
		intEmtRectStartX = mouseX;
		intEmtRectStartY = mouseY;
	};
	this.drawEmptyRoundRect = function(e){
		this.getEvt(e, "block");
		intEmtRoundRectStartX = mouseX;
		intEmtRoundRectStartY = mouseY;
	};
	this.drawEmptyEllipse = function(e){
		this.getEvt(e, "block");				
		intEmtElpsStartX = mouseX;
		intEmtElpsStartY = mouseY;
	};
	this.drawEmptyCircle = function(e){
		this.getEvt(e, "block");				
		intEmtCirStartX = mouseX;
		intEmtCirStartY = mouseY;
	};
	this.drawFilledRect = function(e){
		this.getEvt(e, "block");				
		intFilledRectStartX = mouseX;
		intFilledRectStartY = mouseY;				
	};
	this.drawFilledRoundRect = function(e){
		this.getEvt(e, "block");				
		intFilledRoundRectStartX = mouseX;
		intFilledRoundRectStartY = mouseY;
	};
	this.drawFilledEllipse = function(e){
		this.getEvt(e, "block");				
		intFilledElpsStartX = mouseX;
		intFilledElpsStartY = mouseY;
	};
	this.drawFilledCircle = function(e){
		this.getEvt(e, "block");				
		intFilledCirStartX = mouseX;
		intFilledCirStartY = mouseY;
	};
	this.startSelectProcess = function(e, startX, startY, width, hieght, target, blSelectCopy){
		if(isIPad == false){
			this.onMouseDown(e);
		}			
		else{
			this.onTouchStart(e);
		}
		this.getCanvasDataForSelectedArea(startX, startY, width, hieght, target, blSelectCopy);
		this.drawOnTempCanvas(arrSelRed, arrSelGreen, arrSelBlue, arrSelAlpha, startX, startY, width, hieght, ctxTemp);
	};	
	this.getCanvasDataForSelectedArea = function(startX, startY, width, height, target, blSelectCopy){
		arrSelRed = new Array();
		arrSelGreen = new Array();
		arrSelBlue = new Array();
		arrSelAlpha = new Array();              			
		var frame = target.getImageData(startX, startY, width, height);
		var l = frame.data.length / 4;
		for (var i = 0; i < l; i++) {
			var r = frame.data[i * 4 + 0];
			var g = frame.data[i * 4 + 1];
			var b = frame.data[i * 4 + 2];
			var a = frame.data[i * 4 + 3];
			arrSelRed[i] = r;
			arrSelGreen[i] = g;
			arrSelBlue[i] = b;
			arrSelAlpha[i] = a;
		}
		if(blSelectCopy == false){
			target.clearRect(startX, startY, width, height);
		}
	};
	this.drawOnTempCanvas = function(r, g, b, a, startX, startY, width, hieght, target){
		var arrRed = r;
		var arrGreen = g;
		var arrBlue = b;
		var arrAlpha = a;
		var myImageData = target.createImageData(width, hieght);
		var len = arrRed.length;
		for (var i = 0; i < len; i++) {
			var red = arrRed[i];			  
			var green = arrGreen[i];
			var blue = arrBlue[i];
			var alpha = arrAlpha[i];
			myImageData.data[i * 4 + 0] = red; // Red value				
			myImageData.data[i * 4 + 1] = green; // Green value
			myImageData.data[i * 4 + 2] = blue; // Blue value	
			myImageData.data[i * 4 + 3] = alpha; // Alpha value			
		}
		this.clearTempCanvas();
		target.putImageData(myImageData, startX, startY);
		if((widthMXSX == 0) && (hieghtMYSY == 0) && (widthSelect == 0) && (hieghtSelect == 0)){
			intSelectStartXTempCanvas = parseInt(startX);
			intSelectStartYTempCanvas = parseInt(startY);
			widthMXSX = mouseX - intSelectStartXTempCanvas;
			hieghtMYSY = mouseY - intSelectStartYTempCanvas;
			widthSelect = intSelectWidth;
			hieghtSelect = intSelectHieght;
		}	
		gbBlDragStartSelectProcess = true;			
	};
	this.drawDrwaingPoints = function(e){
		if(gbEventTypeTemp == "funSelect" || gbEventTypeTemp == "funSelectCopy" || gbEventTypeTemp == "funDrwaArrowText" || gbEventTypeTemp == "funClearCanvas"){
			if(gbEventTypeTemp == "funSelect"){
				var left = parseInt(intSelectStartX);					
				var right = parseInt(intSelectStartX) + parseInt(intSelectWidth);						
				var top = parseInt(intSelectStartY);
				var bottom = parseInt(intSelectStartY) + parseInt(intSelectHieght);						
				if ((mouseX <= right) && (mouseX >= left) && (mouseY <= bottom) && (mouseY >= top)){
					me.startSelectProcess(e, intSelectStartX, intSelectStartY, intSelectWidth, intSelectHieght, ctx, false);
				}
				else{
					gbEventTypeTemp = null;
				}
			}
			else if(gbEventTypeTemp == "funSelectCopy"){
				var left = parseInt(intSelectStartX);					
				var right = parseInt(intSelectStartX) + parseInt(intSelectWidth);
				var top = parseInt(intSelectStartY);
				var bottom = parseInt(intSelectStartY) + parseInt(intSelectHieght);						
				if ((mouseX <= right) && (mouseX >= left) && (mouseY <= bottom) && (mouseY >= top)){
					me.startSelectProcess(e, intSelectStartX, intSelectStartY, intSelectWidth, intSelectHieght, ctx, true);
				}
				else{
					gbEventTypeTemp = null;
				}
			}				
		}
		else{
			gbEventTypeTemp = null;
		}
		//alert(gbEventTypeTemp);
		if(gbEventTypeTemp == null){
			//alert(eventType);
			/*if(gbEventType == null){
				alert("Please Select Drawing Event!");
				return false;
			}*/						
			//alert(gbEventType);
			switch(gbEventType){						
				case "funSelect":
					me.drawSelect(e);					
				break;
				case "funSelectCopy":
					me.drawSelect(e);
				break;
				case "funPencil":
					me.drawWithPencil(e);
				break;
				case "funBrush":
					me.drawWithBrush(e);
				break;
				case "funFillColor":
					me.drawFillColor(e);
				break;				
				case "funSparyColor":
					me.drawSparyColor(e);
				break;
				case "funEraser":
					me.drawEraser(e);
				break;			
				case "funText":
					me.drawText(e);
				break;	
				case "funDrwaArrowText":
					me.drwaArrowText(e);
				break;
				case "funDrawLine":
					me.drawLine(e);
				break;
				case "funDrawArc":
					me.drawArc(e);
				break;
				case "funDrawRect":
					me.drawEmptyRect(e);
				break;
				case "funDrawRoundRect":
					me.drawEmptyRoundRect(e);
				break;
				case "funDrawEllipse":
					me.drawEmptyEllipse(e);
				break;
				case "funDrawCircle":
					me.drawEmptyCircle(e);
				break;
				case "funDrawFilledRectangle":
					me.drawFilledRect(e);
				break;
				case "funDrawFilledRoundRect":
					me.drawFilledRoundRect(e);
				break;						
				case "funDrawFilledEllipse":
					me.drawFilledEllipse(e);
				break;
				case "funDrawFilledCircle":
					me.drawFilledCircle(e);
				break;
				case "funDrag":	
					me.getEvt(e, "block");				
				break;				
				default:
					me.getEvt(e, "none");					
				break;
			}
		}
		gbEventTypeTemp = null;
	}
	this.getEvt = function(e, tempCanDisp){
		canvasTempObj.style.display = tempCanDisp;		
		
		if(isIPad == false){			
			this.onMouseDown(e);
		}			
		else{
			this.onTouchStart(e);
		}		
	};
	
	this.funUndoText = function(e){		
		if(gbArrTextMain.length>0){
			var i = gbArrTextMain.length - 1; //Last one
			var x = gbArrTextMain[i][0];
			var y = gbArrTextMain[i][1];
			var text = gbArrTextMain[i][2];
			//var textLength = ((text.length * 14)/2)  + 2;
			var textLength = ctx.measureText(text).width;
			me.clear(x, y - 10, textLength, 14);
			gbArrTextMain.length=i;
			intTextCounterMain = i;
		}
	}
	
	this.onMouseDown = function(e){
		
		//alert('onMouseDown');							
		mousePressed = true;
		me.hideMenu();	
		//*
		if(e.button==2){ //Check right click
			//Show Menu for Text --
			//get event values here and stop it here
			gbEventType="evrightclick";
			//alet(1);
			//e.preventDefault();
			//me.showMenu(0,0);
		}
		//*/
		
	};
	this.onMouseUp = function(e){	
		if(mousePressed == true){
		//alert(gbEventType);					
			switch(gbEventType){
				case "evrightclick":
					
					mouseX1 = e.pageX+1; //1 is add to prevent mouse out event on right click
					mouseY1 = e.pageY+1; //1 is add to prevent mouse out event on right click				
					
					me.showMenu();
					
					e.preventDefault();
					e.cancelBubble = true;
					if (e.stopPropagation){ e.stopPropagation();}
					
				break;	
					
				case "funSelect":
				case "funSelectCopy":
					if(gbBlDragStartSelectProcess == true){
						var stX = mouseX - widthMXSX;
						var stY = mouseY - hieghtMYSY;
						me.drawSelectedCanvas(arrSelRed, arrSelGreen, arrSelBlue, arrSelAlpha, stX, stY, widthSelect, hieghtSelect, ctx);
						me.clearTempCanvas("none");
						intSelectStartXTempCanvas = 0, intSelectStartYTempCanvas = 0;
						widthMXSX = 0, hieghtMYSY = 0;
						widthSelect = 0, hieghtSelect = 0;
						arrSelRed = new Array();
						arrSelGreen = new Array();
						arrSelBlue = new Array();
						arrSelAlpha = new Array();
						gbBlDragStartSelectProcess = false;
					}
				break;
				case "funDrwaArrowText":
					if(intArrowStartX > 0 && intArrowStartY > 0){
						me.drawStraight( intArrowStartX, intArrowStartY, mouseX, mouseY, "ctx", true);
						intArrowStartX = 0;
						intArrowStartY = 0;
					}
				break;
				case "funDrawLine":
					if(intLineStartX > 0 && intLineStartY > 0){
						me.drawStraightLine( intLineStartX, intLineStartY, mouseX, mouseY, "ctx", false);
						intLineStartX = 0;
						intLineStartY = 0;
					}
				break;
				case "funDrawArc":
					me.clearTempCanvas("none");
					if(intArcStartX > 0 && intArcStartY > 0){
						me.drawArcMain( intArcStartX, intArcStartY, mouseX, mouseY, ctx );
						intArcStartX = 0;
						intArcStartY = 0;
					}
				break;
				case "funDrawRect":
					me.clearTempCanvas("none");
					//alert(intEmtRectStartX +"--"+ intEmtRectStartY +"--"+ intRectWidth +"--"+ intRectHieght);
					if(intEmtRectStartX > 0 && intEmtRectStartY > 0 && intRectWidth > 0 && intRectHieght > 0){
						ctx.lineWidth = parseFloat(me.lineType);
						ctx.strokeStyle = currentColor;
						ctx.strokeRect( intEmtRectStartX , intEmtRectStartY, intRectWidth, intRectHieght );
						var arrTemp = new Array(intEmtRectStartX , intEmtRectStartY, intRectWidth, intRectHieght);
						arrEmtRect.push(arrTemp);
						intEmtRectStartX = 0;
						intEmtRectStartY = 0;
						intRectWidth = 0;
						intRectHieght = 0;
					}
				break;
				case "funDrawRoundRect":
					me.clearTempCanvas("none");
					if(intEmtRoundRectStartX > 0 && intEmtRoundRectStartY > 0 && intEmtRoundRectEndX > 0 && intEmtRoundRectEndY > 0){
						me.drawRoundRectangle(intEmtRoundRectStartX, intEmtRoundRectStartY, intEmtRoundRectEndX, intEmtRoundRectEndY, ctx);
						intEmtRoundRectStartX = 0;
						intEmtRoundRectStartY = 0;
						intEmtRoundRectEndX = 0;
						intEmtRoundRectEndY = 0;
					}
				break;
				case "funDrawEllipse":
					me.clearTempCanvas("none");
					if(intEmtElpsStartX > 0 && intEmtElpsStartY > 0){
						me.drawEllipse(intEmtElpsStartX, intEmtElpsStartY, mouseX, mouseY, ctx);
						intEmtElpsStartX = 0;
						intEmtElpsStartY = 0;
					}
				break;
				case "funDrawCircle":
					me.clearTempCanvas("none");
					if(intEmtCirStartX > 0 && intEmtCirStartY > 0){
						me.drawCircle(intEmtCirStartX, intEmtCirStartY, mouseX, mouseY, ctx);
						intEmtCirStartX = 0;
						intEmtCirStartY = 0;
					}
				break;
				case "funDrawFilledRectangle":
					me.clearTempCanvas("none");
					if(intFilledRectStartX > 0 && intFilledRectStartY > 0 && intFilledRectWidth > 0 && intFilledRectHieght > 0){
						me.drawRectangle(intFilledRectStartX, intFilledRectStartY, intFilledRectWidth, intFilledRectHieght, ctx, "ctx", true);
						intFilledRectStartX = 0;
						intFilledRectStartY = 0;
						intFilledRectWidth = 0;
						intFilledRectHieght = 0;
					}
				break;
				case "funDrawFilledRoundRect":
					me.clearTempCanvas("none");
					if(intFilledRoundRectStartX > 0 && intFilledRoundRectStartY > 0 && intFilledRoundRectEndX > 0 && intFilledRoundRectEndY > 0){
						me.drawRoundRectangle(intFilledRoundRectStartX, intFilledRoundRectStartY, intFilledRoundRectEndX, intFilledRoundRectEndY, ctx, true);
						intFilledRoundRectStartX = 0;
						intFilledRoundRectStartY = 0;
						intFilledRoundRectEndX = 0;
						intFilledRoundRectEndY = 0;
					}
				break;						
				case "funDrawFilledEllipse":
					me.clearTempCanvas("none");
					if(intFilledElpsStartX > 0 && intFilledElpsStartY > 0){
						me.drawEllipse(intFilledElpsStartX, intFilledElpsStartY, mouseX, mouseY, ctx, true);
						intFilledElpsStartX = 0;
						intFilledElpsStartY = 0;
					}
				break;
				case "funDrawFilledCircle":
					me.clearTempCanvas("none");
					if(intFilledCirStartX > 0 && intFilledCirStartY > 0){
						me.drawCircle(intFilledCirStartX, intFilledCirStartY, mouseX, mouseY, ctx, true);
						intFilledCirStartX = 0;
						intFilledCirStartY = 0;
					}
				break;
				case "funDrawImage":					
					//alert("22");
					me.clearTempCanvas("none");						
					if(!me.checkCOI(e)){ //checked on image
						
						me.drawImages(fwi_img, fwi_intSmartTagDivId);											
					}					
					
				break;					
				/*	
				case "funDrawPuker":
					//me.clearTempCanvas("none");
					//me.drawPuker(mouseX, mouseY);
				break;
				*/	
			}
		}
		//mouseX = canvasObj.width;
		//mouseY = canvasObj.height;
		//mouseX = null;
		//mouseY = null;
		mousePressed = false;
	};
	this.onMouseMove = function(e){					
		mouseX = e.offsetX;
		
		mouseY = e.offsetY;
		//var temp = "X="+mouseX+" Y="+mouseY;
		//document.getElementById("od_desc").value = temp;
		if(mousePressed == true){
			//window.status = ""+gbEventType;
			switch(gbEventType){
				case "funSelect":
					if(gbBlDragStartSelectProcess == false){
						me.drawRectangle(intSelectStartX, intSelectStartY, mouseX, mouseY, ctxTemp, "ctxTemp");
						gbEventTypeTemp = "funSelect";
						
						/*
						//Enable scale options
						var t1 = document.getElementById("divScaleOpt"+me.classId);
						t1.style.display="block";
						*/
						
					}
					else if(gbBlDragStartSelectProcess == true){
						
						var stX = mouseX - widthMXSX;
						var stY = mouseY - hieghtMYSY;						
						me.clearTempCanvas();
						me.drawSelectedCanvas(arrSelRed, arrSelGreen, arrSelBlue, arrSelAlpha, stX, stY, widthSelect, hieghtSelect, ctxTemp);
						ctxTemp.beginPath();
						ctxTemp.moveTo(stX, stY);
						ctxTemp.strokeStyle = "rgb(17, 17, 17)";
						ctxTemp.strokeRect( stX, stY, widthSelect, hieghtSelect );
						ctxTemp.beginPath();
						
					}
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funSelectCopy":
					if(gbBlDragStartSelectProcess == false){
						me.drawRectangle(intSelectStartX, intSelectStartY, mouseX, mouseY, ctxTemp, "ctxTemp");
						gbEventTypeTemp = "funSelectCopy";
					}
					else if(gbBlDragStartSelectProcess == true){
						var stX = mouseX - widthMXSX;
						var stY = mouseY - hieghtMYSY;
						me.clearTempCanvas();
						me.drawSelectedCanvas(arrSelRed, arrSelGreen, arrSelBlue, arrSelAlpha, stX, stY, widthSelect, hieghtSelect, ctxTemp);
						ctxTemp.beginPath();
						ctxTemp.moveTo(stX, stY);
						ctxTemp.strokeStyle = "rgb(0, 0, 0)";
						ctxTemp.strokeRect( stX, stY, widthSelect, hieghtSelect );
						ctxTemp.beginPath();
					}
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funPencil":					
					if(mousePressed){
						ctx.lineCap = "round";
						ctx.strokeStyle = currentColor;
						ctx.lineWidth = parseFloat(me.lineType);
						ctx.lineTo( mouseX, mouseY );
						ctx.stroke();
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
					
				break;
				case "funBrush":
					ctx.strokeStyle = currentColor;
					ctx.lineWidth = parseFloat(me.lineType);
					ctx.lineTo( mouseX, mouseY );
					ctx.stroke();	
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funSparyColor":						
					ctx.strokeStyle = currentColor;	
					//ctx.fillStyle = '#FFF';
					//sprayWidth = parseInt(me.lineType);
					sprayWidth = parseFloat(me.lineType);
					for(var i = sprayWidth*5; i>0; i--) {
						//var rndx = mouseX + Math.round(Math.random() * ((sprayWidth * 8) - (sprayWidth * 4)));
						//var rndy = mouseY + Math.round(Math.random() * ((sprayWidth * 8) - (sprayWidth * 4)));
						var rndx = mouseX + Math.round(Math.random() * ((sprayWidth * 16) - (sprayWidth * 8)));
						var rndy = mouseY + Math.round(Math.random() * ((sprayWidth * 16) - (sprayWidth * 8)));
						//alert(rndx+"--"+rndy);
						me.drawDot(rndx, rndy, 1, ctx.strokeStyle);
						//me.drawDot(mouseX, mouseY, 1, ctx.strokeStyle);
					}
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;    
				case "funEraser":
					var arrTemp = me.currentEraser.split("-");						
					ctx.clearRect(mouseX, mouseY, parseInt(arrTemp[0]), parseInt(arrTemp[1]));
					/*ctx.fillStyle = '#ffffff';
					ctx.fillRect(mouseX, mouseY, parseInt(arrTemp[0]), parseInt(arrTemp[1]));
					*/
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;          
				case "funDrwaArrowText":
					me.drawStraight( intArrowStartX, intArrowStartY, mouseX, mouseY, "ctxTemp");
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawLine":
					if((intLineStartX > 0) && (intLineStartY > 0)){
						me.drawStraightLine( intLineStartX, intLineStartY, mouseX, mouseY, "ctxTemp" );
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
				case "funDrawArc":
					if((intArcStartX > 0) && (intArcStartY > 0)){
						me.clearTempCanvas();
						me.drawArcMain( intArcStartX, intArcStartY, mouseX, mouseY, ctxTemp );
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
				case "funDrawRect":
					if((intEmtRectStartX > 0) && (intEmtRectStartY > 0)){
						me.drawRectangle(intEmtRectStartX, intEmtRectStartY, mouseX, mouseY, ctxTemp, "ctxTemp");
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
				case "funDrawRoundRect":
					if((intEmtRoundRectStartX > 0) && (intEmtRoundRectStartY > 0)){
						me.clearTempCanvas();
						me.drawRoundRectangle(intEmtRoundRectStartX, intEmtRoundRectStartY, mouseX, mouseY, ctxTemp);
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
				case "funDrawEllipse":
					if((intEmtElpsStartX > 0) && (intEmtElpsStartY > 0)){
						me.clearTempCanvas();
						me.drawEllipse(intEmtElpsStartX, intEmtElpsStartY, mouseX, mouseY, ctxTemp);
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
				case "funDrawCircle":
					if((intEmtCirStartX > 0) && (intEmtCirStartY > 0)){
						me.clearTempCanvas();
						me.drawCircle(intEmtCirStartX, intEmtCirStartY, mouseX, mouseY, ctxTemp);
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
				case "funDrawFilledRectangle":
					if((intFilledRectStartX > 0) && (intFilledRectStartY > 0)){
						me.drawRectangle(intFilledRectStartX, intFilledRectStartY, mouseX, mouseY, ctxTemp, "ctxTemp", true);							
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
				case "funDrawFilledRoundRect":
					if((intFilledRoundRectStartX > 0) && (intFilledRoundRectStartY > 0)){
						me.clearTempCanvas();
						me.drawRoundRectangle(intFilledRoundRectStartX, intFilledRoundRectStartY, mouseX, mouseY, ctxTemp, true);
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;						
				case "funDrawFilledEllipse":					
					if((intFilledElpsStartX > 0) && (intFilledElpsStartY > 0)){
						me.clearTempCanvas();
						me.drawEllipse(intFilledElpsStartX, intFilledElpsStartY, mouseX, mouseY, ctxTemp, true);
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
				case "funDrawFilledCircle":
					if((intFilledCirStartX > 0) && (intFilledCirStartY > 0)){
						me.clearTempCanvas();
						me.drawCircle(intFilledCirStartX, intFilledCirStartY, mouseX, mouseY, ctxTemp, true);
						me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
					}
				break;
			}
		}
	};
	this.onMouseClick = function(e){
		//alert(gbEventType);
		if((gbEventType == null) || (gbEventType == "funDrawImage")){
			mouseX = e.offsetX;
			mouseY = e.offsetY;
			var pageX = e.pageX;
		    var pageY = e.pageY;
			var intCanvasPageLeft = canvasObj.offsetLeft;
			var intCanvasPageTop = canvasObj.offsetTop;
			for(var i = 0; i < gbArrDrawingImages.length; i++){
				var imageX = parseInt(gbArrDrawingImages[i][0]);
				var imageY = parseInt(gbArrDrawingImages[i][1]);
				var imageW = parseInt(gbArrDrawingImages[i][2]);
				var imageH = parseInt(gbArrDrawingImages[i][3]);
				
				var textX = parseInt(gbArrDrawingImages[i][11]);
				var textY = parseInt(gbArrDrawingImages[i][12]);
				var textW = parseInt(gbArrDrawingImages[i][13]);
				var textH = parseInt(gbArrDrawingImages[i][14]);
				
				var textLeft = textX;
				var textRight = textX + textW;
				var textTop = textY;
				var textBottom = textY + textH;
				
				var imageLeft = imageX;
				var imageRight = imageX + imageW;
				var imageTop = imageY;
				var imageBottom = imageY + imageH;
				//alert(imageLeft+"--"+imageRight+"--"+imageTop+"--"+imageBottom);
				if ((mouseX < textRight) && (mouseX > textLeft) && (mouseY < textBottom) && (mouseY > textTop)){
					//alert(gbArrDrawingImages[i]);	
					//alert(textX+"--"+textY+"--"+pageX+"--"+pageY);
					//alert(e.type + ' in between');
					var strPathT = gbArrDrawingImages[i][16];
					var arrTextImageName = strPathT.split("/");
					var strTextImageName = arrTextImageName[arrTextImageName.length - 1];
					switch(strTextImageName){
						case "puckerTLink.png":
							var divName = "div_smart_tags_options"+me.gbArrSmartTagDivID[0];
							document.getElementById(divName).style.display = "block";
							intSmartTagX = textLeft + 3;
							intSmartTagY = textBottom + 10;
							if((gbArrDrawingImages[i][20] != "") && (typeof(gbArrDrawingImages[i][20]) != "undefined")){
								var arrSmartTagID = gbArrDrawingImages[i][20].split("#-$-#");
								for(var a = 0; a < arrSmartTagID.length; a++){
									if(document.getElementById(arrSmartTagID[a])){
										document.getElementById(arrSmartTagID[a]).checked = true;
									}
								}
							}
							gbIntCurrentArrDrawingImagesIndex = i;
							i = gbArrDrawingImages.length;
						break;
						case "drusenTLink.png":
							var divName = "div_smart_tags_options"+me.gbArrSmartTagDivID[1];
							document.getElementById(divName).style.display = "block";
							intSmartTagX = textLeft + 3;
							intSmartTagY = textBottom + 10;
							if((gbArrDrawingImages[i][20] != "") && (typeof(gbArrDrawingImages[i][20]) != "undefined")){
								var arrSmartTagID = gbArrDrawingImages[i][20].split("#-$-#");
								for(var a = 0; a < arrSmartTagID.length; a++){
									if(document.getElementById(arrSmartTagID[a])){
										document.getElementById(arrSmartTagID[a]).checked = true;
									}
								}
							}
							gbIntCurrentArrDrawingImagesIndex = i;
							i = gbArrDrawingImages.length;
						break;		
						case "pallorTLink.png":
							var divName = "div_smart_tags_options"+me.gbArrSmartTagDivID[2];
							document.getElementById(divName).style.display = "block";
							intSmartTagX = textLeft + 3;
							intSmartTagY = textBottom + 10;
							if((gbArrDrawingImages[i][20] != "") && (typeof(gbArrDrawingImages[i][20]) != "undefined")){
								var arrSmartTagID = gbArrDrawingImages[i][20].split("#-$-#");
								for(var a = 0; a < arrSmartTagID.length; a++){
									if(document.getElementById(arrSmartTagID[a])){
										document.getElementById(arrSmartTagID[a]).checked = true;
									}
								}
							}
							gbIntCurrentArrDrawingImagesIndex = i;
							i = gbArrDrawingImages.length;
						break;		
					}
				}
				else if((mouseX < imageRight) && (mouseX > imageLeft) && (mouseY < imageBottom) && (mouseY > imageTop)){
					//alert("Image Click - Local");
					/* Stoped rotation
					document.getElementById("divRotateAngle"+me.classId).style.display = "block";
					gbIntCurrentArrDrawingImagesIndex = null;
					gbIntCurrentArrDrawingImagesIndex = i;
					i = gbArrDrawingImages.length;
					document.getElementById("spRCCW"+me.classId).className = "toolIcon16 rotateCounterClockWise16";
					document.getElementById("spRCW"+me.classId).className = "toolIcon16 rotateClockWise16";
					if((typeof(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][23]) != "undefined") && (gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][23] != "")){
						document.getElementById("txtRotateAngle"+me.classId).value = gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][23];	
						document.getElementById("spRCW"+me.classId).className = "toolIcon16 rotateClockWiseDB16";
						document.getElementById("spRCCW"+me.classId).className = "toolIcon16 rotateCounterClockWise16";
					}
					else if((typeof(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][24]) != "undefined") && (gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][24] != "")){
						document.getElementById("txtRotateAngle"+me.classId).value = gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][24];	
						document.getElementById("spRCCW"+me.classId).className = "toolIcon16 rotateCounterClockWiseDB16";
						document.getElementById("spRCW"+me.classId).className = "toolIcon16 rotateClockWise16";
					}
					else{
						document.getElementById("txtRotateAngle"+me.classId).value = "";
					}
					*/
				}
				else{
					//alert("Image Click out");
					gbIntCurrentArrDrawingImagesIndex = null;
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
				}
			}
			//alert(gbArrDrawingImages);	
			//alert(gbArrImagesDB);
			for(var i = 0; i < gbArrImagesDB.length; i++){
				var imageX = parseInt(gbArrImagesDB[i][1]);
				var imageY = parseInt(gbArrImagesDB[i][2]);
				var imageW = parseInt(gbArrImagesDB[i][3]);
				var imageH = parseInt(gbArrImagesDB[i][4]);
				
				var textX = parseInt(gbArrImagesDB[i][9]);
				var textY = parseInt(gbArrImagesDB[i][10]);
				var textW = parseInt(gbArrImagesDB[i][11]);
				var textH = parseInt(gbArrImagesDB[i][12]);
			
				var textLeft = textX;
				var textRight = textX + textY;
				var textTop = textY;
				var textBottom = textY + textH;
				
				var imageLeft = imageX;
				var imageRight = imageX + imageW;
				var imageTop = imageY;
				var imageBottom = imageY + imageH;
				//alert(imageLeft+"--"+imageRight+"--"+imageTop+"--"+imageBottom);
				
				if ((mouseX < textRight) && (mouseX > textLeft) && (mouseY < textBottom) && (mouseY > textTop)){
					//alert(textX+"--"+textY+"--"+pageX+"--"+pageY);
					//alert(e.type + ' in between');
					var strPathT = gbArrImagesDB[i][14];
					var arrTextImageName = strPathT.split("/");
					var strTextImageName = arrTextImageName[arrTextImageName.length - 1];
					switch(strTextImageName){
						case "puckerTLink.png":							
							gbIntCurrentArrDrawingImagesDBIndex = i;
							i = gbArrImagesDB.length;
						break;
						case "drusenTLink.png":							
							gbIntCurrentArrDrawingImagesDBIndex = i;
							i = gbArrImagesDB.length;
						break;
						case "pallorTLink.png":							
							gbIntCurrentArrDrawingImagesDBIndex = i;
							i = gbArrImagesDB.length;
						break;
					}
				}
				else if((mouseX < imageRight) && (mouseX > imageLeft) && (mouseY < imageBottom) && (mouseY > imageTop)){
					//alert("Image Click - DB");
					gbIntCurrentArrDrawingImagesDBIndex = null;
					gbIntCurrentArrDrawingImagesDBIndex = i;
					i = gbArrImagesDB.length;
				}
			}	
			
			if((gbIntCurrentArrDrawingImagesIndex != null) && (gbIntCurrentArrDrawingImagesDBIndex != null)){
				var intImageX = parseInt(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][0]);
				var intImageY = parseInt(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][1]);
				var intImageW = parseInt(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][2]);
				var intImageH = parseInt(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][3]);
					
				var intImageDBX = parseInt(gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][1]);
				var intImageDBY = parseInt(gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][2]);
				var intImageDBW = parseInt(gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][3]);
				var intImageDBH = parseInt(gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][4]);
				
				var intTextX = parseInt(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][11]);
				var intTextY = parseInt(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][12]);
				var intTextW = parseInt(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][13]);
				var intTextH = parseInt(gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][14]);
					
				var intTextDBX = parseInt(gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][9]);
				var intTextDBY = parseInt(gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][10]);
				var intTextDBW = parseInt(gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][11]);
				var intTextDBH = parseInt(gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][12]);
				if((intTextX != intTextDBX) && (intTextY != intTextDBY) && (intTextW != intTextDBW) && (intTextH != intTextDBH)){
					//alert(gbIntCurrentArrDrawingImagesIndex+"--"+gbIntCurrentArrDrawingImagesDBIndex);
					gbIntCurrentArrDrawingImagesIndex = null;
					gbIntCurrentArrDrawingImagesDBIndex = null;
				}
				else if((intImageX != intImageDBX) && (intImageY != intImageDBY) && (intImageW != intImageDBW) && (intImageH != intImageDBH)){
					//alert(gbIntCurrentArrDrawingImagesIndex+"--"+gbIntCurrentArrDrawingImagesDBIndex);
					gbIntCurrentArrDrawingImagesIndex = null;
					gbIntCurrentArrDrawingImagesDBIndex = null;
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
				}
			}
					
		}else if(gbEventType=="evrightclick"){			
			me.hideMenu();
		}
		
	};
	this.insertSmartTag = function(arrSmartTagOp, arrSmartTagOpID){
		//alert(parseInt(intSmartTagX)+"--"+parseInt(intSmartTagY)+"--"+gbIntCurrentArrDrawingImagesIndex+"--"+gbIntCurrentArrDrawingImagesDBIndex);
		if((parseInt(intSmartTagX) > 0) && (parseInt(intSmartTagY) > 0) && (gbIntCurrentArrDrawingImagesIndex != null) && (gbIntCurrentArrDrawingImagesDBIndex != null)){
			var text = gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][17];
			if(text != ""){
				var textLength = parseInt(ctx.measureText(text).width);
				me.clear(intSmartTagX, intSmartTagY - 10, textLength, 14)
			}
			var strSmartTagOp = "";
			var arrSmartTagValue = new Array();
			var arrSmartTagMasterId = new Array();
			var arrSmartTagChildId = new Array();
			if(arrSmartTagOp.length > 0){
				for(var a = 0; a < arrSmartTagOp.length; a++){
					var strSTVal = arrSmartTagOp[a];
					var arrSTVal = strSTVal.split("`!#`");
					arrSmartTagValue.push(arrSTVal[0]);
					arrSmartTagMasterId.push(arrSTVal[1]);
					arrSmartTagChildId.push(arrSTVal[2]);
				}
				if(arrSmartTagValue.length > 0){
					strSmartTagOp = "(" + arrSmartTagValue.join("-") + ")";			
				}
			}
			this.writeSmartTag(ctx, strSmartTagOp, intSmartTagX, intSmartTagY);
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][17] = strSmartTagOp;
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][18] = intSmartTagX;
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][19] = intSmartTagY;
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][20] = arrSmartTagOpID.join("#-$-#");
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][21] = arrSmartTagMasterId.join("#-STMID-#");//Smart Tag Master ID
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][22] = arrSmartTagChildId.join("#-STCID-#");//Smart Tag Child ID
						
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][15] = strSmartTagOp;
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][16] = intSmartTagX;
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][17] = intSmartTagY;
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][18] = arrSmartTagOpID.join("#-$-#");
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][19] = arrSmartTagMasterId.join("#-STMID-#");//Smart Tag Master ID
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][20] = arrSmartTagChildId.join("#-STCID-#");//Smart Tag Child ID
			
			me.arrBlCanvasHaveDrwaing[0] = true;
			me.arrBlCanvasHaveDrwaing[1] = true;
			intSmartTagX = null;
			intSmartTagY = null;
			gbIntCurrentArrDrawingImagesIndex = null;
		}
		else{
			//alert("Test");
		}
	}
	this.writeSmartTag = function(trget, strSmartTagOp, intSmartTagX, intSmartTagY){
		trget.beginPath();
		trget.font = "14px Arial";
		trget.fillStyle = '#171717';
		trget.fillText(strSmartTagOp, intSmartTagX, intSmartTagY);
		trget.closePath();
	};
	this.onMouseDblClick = function(e){
		//alert('onMouseDblClick'+"----"+gbEventType);
		me.clearTempCanvas("none");
		if(gbEventType == null){
			mouseX = e.offsetX;
			mouseY = e.offsetY;
			var pageX = e.pageX
		    var pageY = e.pageY
			var intCanvasPageLeft = canvasObj.offsetLeft;
			var intCanvasPageTop = canvasObj.offsetTop;
			var blTAAppear = false;
			//alert(mouseX+"--"+mouseY+"--"+pageX+"--"+pageY+"--"+canvasObj.offsetLeft+"--"+canvasObj.offsetTop);
			for (var i in gbArrTextMain){
				var x = parseInt(gbArrTextMain[i][0]);
				var y = parseInt(gbArrTextMain[i][1]);
				var text = gbArrTextMain[i][2];
				var strTAName = gbArrTextMain[i][3];
				var obj = document.getElementById(strTAName)
				var textLength = ctx.measureText(text).width;				
				var left = x;
				var right = x + textLength;
				var top = y - 10;
				var bottom = y;
				//alert(x+"--"+y+"--"+pageX+"--"+pageY);
				/*alert(mouseX+"--"+mouseY+"--"+left+"--"+right+"--"+top+"--"+bottom);
				alert(mouseX < right);
				alert(mouseX > left);
				alert(mouseY < bottom);
				alert(mouseY > top);
				*/
				if ((mouseX < right) && (mouseX > left) && (mouseY < bottom) && (mouseY > top) && (blTAAppear == false)){
					//alert(x+"--"+y+"--"+pageX+"--"+pageY);
					//alert('in between');
					canvasTempObj.style.display = "block";
					//alert(x+"--"+y);
					/*var distanceLeft = pageX - intCanvasPageLeft;
					var distanceTop = pageY - intCanvasPageTop;
					//alert(distanceLeft+"--"+distanceTop);
					var pxLeftL = distanceLeft - mouseX;
					var pxLeftT = distanceTop - mouseY;
					//alert(pxLeftL+"--"+pxLeftT);
					var pxDiffX = pxLeftL - x;
					var pxDiffY = pxLeftT - y;
					var corX = pageX - pxDiffX;
					var corY = pageY - pxDiffY;
					*/
					//alert(x+"--"+y+"--"+intCanvasPageLeft+"--"+intCanvasPageTop)
					gbArrCurrentTAXY = new Array();
					gbArrCurrentTAXY[0] = x;
					gbArrCurrentTAXY[1] = y;
					var corX = intCanvasPageLeft + x;
					var corY = pageY - 10;
					//alert(corX+"--"+corY);
					obj.style.position = "absolute";
					obj.style.left = corX+"px";					
					obj.style.top = corY+"px";
					//alert(document.getElementById(strTAName).style.left);
					obj.style.display = "block";
					obj.focus();
					blTAAppear = true;					
				}
				else{
					obj.style.display = "none";
				}
			}
			if(blTAAppear == false){
				if(gbEventType == null){
					alert("Please Select Drawing Event!");
				}
			}		
		}
	};
	
	this.getTouchEvent = function() {						
		return(isIPad ? window.event.targetTouches[ 0 ] : event);
	}
	this.getCanvasLocalCoordinates = function(pageX, pageY ) {
		var tempX = pageX - canvasObj.offsetLeft;
		var tempY = pageY - canvasObj.offsetTop;
		return({					
			x: (tempX - 90),
			y: (tempY - 50)
		});
	}	
	this.onTouchStart = function(e){
		//alert('onTouchStart');				
		var touch = this.getTouchEvent( event );			
		var localPosition = this.getCanvasLocalCoordinates(touch.pageX,touch.pageY);			
		var lastPenPoint = {x: localPosition.x, y: localPosition.y};
		mouseX = lastPenPoint.x;
		mouseY = lastPenPoint.y;
		mousePressed = true;			
	};
	this.onTouchEnd = function(e){
		if(mousePressed == true){
		//alert(gbEventType);					
			switch(gbEventType){
				case "funSelect":
				case "funSelectCopy":
					if(gbBlDragStartSelectProcess == true){
						var stX = mouseX - widthMXSX;
						var stY = mouseY - hieghtMYSY;
						me.drawSelectedCanvas(arrSelRed, arrSelGreen, arrSelBlue, arrSelAlpha, stX, stY, widthSelect, hieghtSelect, ctx);
						me.clearTempCanvas("none");
						intSelectStartXTempCanvas = 0, intSelectStartYTempCanvas = 0;
						widthMXSX = 0, hieghtMYSY = 0;
						widthSelect = 0, hieghtSelect = 0;
						arrSelRed = new Array();
						arrSelGreen = new Array();
						arrSelBlue = new Array();
						arrSelAlpha = new Array();
						gbBlDragStartSelectProcess = false;
					}
				break;
				case "funDrwaArrowText":
					if(intArrowStartX > 0 && intArrowStartY > 0){
						me.drawStraight( intArrowStartX, intArrowStartY, mouseX, mouseY, "ctx", true);
						intArrowStartX = 0;
						intArrowStartY = 0;
					}
				break;
				case "funDrawLine":
					if(intLineStartX > 0 && intLineStartY > 0){
						me.drawStraightLine( intLineStartX, intLineStartY, mouseX, mouseY, "ctx", false);
						intLineStartX = 0;
						intLineStartY = 0;
					}
				break;
				case "funDrawArc":
					me.clearTempCanvas("none");
					if(intArcStartX > 0 && intArcStartY > 0){
						me.drawArcMain( intArcStartX, intArcStartY, mouseX, mouseY, ctx );
						intArcStartX = 0;
						intArcStartY = 0;
					}
				break;
				case "funDrawRect":
					me.clearTempCanvas("none");
					if(intEmtRectStartX > 0 && intEmtRectStartY > 0 && intRectWidth > 0 && intRectHieght > 0){
						ctx.lineWidth = parseFloat(me.lineType);
						ctx.strokeStyle = currentColor;
						ctx.strokeRect( intEmtRectStartX , intEmtRectStartY, intRectWidth, intRectHieght );
						var arrTemp = new Array(intEmtRectStartX , intEmtRectStartY, intRectWidth, intRectHieght);
						arrEmtRect.push(arrTemp);
						intEmtRectStartX = 0;
						intEmtRectStartY = 0;
						intRectWidth = 0;
						intRectHieght = 0;
					}
				break;
				case "funDrawRoundRect":
					me.clearTempCanvas("none");
					if(intEmtRoundRectStartX > 0 && intEmtRoundRectStartY > 0 && intEmtRoundRectEndX > 0 && intEmtRoundRectEndY > 0){
						me.drawRoundRectangle(intEmtRoundRectStartX, intEmtRoundRectStartY, intEmtRoundRectEndX, intEmtRoundRectEndY, ctx);
						intEmtRoundRectStartX = 0;
						intEmtRoundRectStartY = 0;
						intEmtRoundRectEndX = 0;
						intEmtRoundRectEndY = 0;
					}
				break;
				case "funDrawEllipse":
					me.clearTempCanvas("none");
					if(intEmtElpsStartX > 0 && intEmtElpsStartY > 0){
						me.drawEllipse(intEmtElpsStartX, intEmtElpsStartY, mouseX, mouseY, ctx);
						intEmtElpsStartX = 0;
						intEmtElpsStartY = 0;
					}
				break;
				case "funDrawCircle":
					me.clearTempCanvas("none");
					if(intEmtCirStartX > 0 && intEmtCirStartY > 0){
						me.drawCircle(intEmtCirStartX, intEmtCirStartY, mouseX, mouseY, ctx);
						intEmtCirStartX = 0;
						intEmtCirStartY = 0;
					}
				break;
				case "funDrawFilledRectangle":
					me.clearTempCanvas("none");
					if(intFilledRectStartX > 0 && intFilledRectStartY > 0 && intFilledRectWidth > 0 && intFilledRectHieght > 0){
						me.drawRectangle(intFilledRectStartX, intFilledRectStartY, intFilledRectWidth, intFilledRectHieght, ctx, "ctx", true);
						intFilledRectStartX = 0;
						intFilledRectStartY = 0;
						intFilledRectWidth = 0;
						intFilledRectHieght = 0;
					}
				break;
				case "funDrawFilledRoundRect":
					me.clearTempCanvas("none");
					if(intFilledRoundRectStartX > 0 && intFilledRoundRectStartY > 0 && intFilledRoundRectEndX > 0 && intFilledRoundRectEndY > 0){
						me.drawRoundRectangle(intFilledRoundRectStartX, intFilledRoundRectStartY, intFilledRoundRectEndX, intFilledRoundRectEndY, ctx, true);
						intFilledRoundRectStartX = 0;
						intFilledRoundRectStartY = 0;
						intFilledRoundRectEndX = 0;
						intFilledRoundRectEndY = 0;
					}
				break;						
				case "funDrawFilledEllipse":
					me.clearTempCanvas("none");
					if(intFilledElpsStartX > 0 && intFilledElpsStartY > 0){
						me.drawEllipse(intFilledElpsStartX, intFilledElpsStartY, mouseX, mouseY, ctx, true);
						intFilledElpsStartX = 0;
						intFilledElpsStartY = 0;
					}
				break;
				case "funDrawFilledCircle":
					me.clearTempCanvas("none");
					if(intFilledCirStartX > 0 && intFilledCirStartY > 0){
						me.drawCircle(intFilledCirStartX, intFilledCirStartY, mouseX, mouseY, ctx, true);
						intFilledCirStartX = 0;
						intFilledCirStartY = 0;
					}
				break;
			}
		}
		mouseX = canvasObj.width;
		mouseY = canvasObj.height;
		mousePressed = false;
	};
	this.onTouchMove = function(e){alert('onTouchMove')
		//alert('onTouchMove');						
		var touch = me.getTouchEvent( event );			
		var localPosition = me.getCanvasLocalCoordinates(touch.pageX, touch.pageY);			
		var lastPenPoint = {x: localPosition.x, y: localPosition.y};
		mouseX = lastPenPoint.x;
		mouseY = lastPenPoint.y;
		if(mousePressed == true){
			//alert(gbEventType);
			switch(gbEventType){
				case "funSelect":
					if(gbBlDragStartSelectProcess == false){
						me.drawRectangle(intSelectStartX, intSelectStartY, mouseX, mouseY, ctxTemp, "ctxTemp");
						gbEventTypeTemp = "funSelect";
					}
					else if(gbBlDragStartSelectProcess == true){
						var stX = mouseX - widthMXSX;
						var stY = mouseY - hieghtMYSY;
						me.clearTempCanvas();
						me.drawSelectedCanvas(arrSelRed, arrSelGreen, arrSelBlue, arrSelAlpha, stX, stY, widthSelect, hieghtSelect, ctxTemp);
						ctxTemp.beginPath();
						ctxTemp.moveTo(stX, stY);
						ctxTemp.strokeStyle = "rgb(17, 17, 17)";
						ctxTemp.strokeRect( stX, stY, widthSelect, hieghtSelect );
						ctxTemp.beginPath();
					}
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funSelectCopy":
					if(gbBlDragStartSelectProcess == false){
						me.drawRectangle(intSelectStartX, intSelectStartY, mouseX, mouseY, ctxTemp, "ctxTemp");
						gbEventTypeTemp = "funSelectCopy";
					}
					else if(gbBlDragStartSelectProcess == true){
						var stX = mouseX - widthMXSX;
						var stY = mouseY - hieghtMYSY;
						me.clearTempCanvas();
						me.drawSelectedCanvas(arrSelRed, arrSelGreen, arrSelBlue, arrSelAlpha, stX, stY, widthSelect, hieghtSelect, ctxTemp);
						ctxTemp.beginPath();
						ctxTemp.moveTo(stX, stY);
						ctxTemp.strokeStyle = "rgb(0, 0, 0)";
						ctxTemp.strokeRect( stX, stY, widthSelect, hieghtSelect );
						ctxTemp.beginPath();
					}
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funPencil":
					ctx.strokeStyle = currentColor;
					ctx.lineWidth = parseFloat(me.lineType);
					ctx.lineTo( mouseX, mouseY );
					ctx.stroke();
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funBrush":
					ctx.strokeStyle = currentColor;
					ctx.lineWidth = parseFloat(me.lineType);
					ctx.lineTo( mouseX, mouseY );
					ctx.stroke();	
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funSparyColor":						
					ctx.strokeStyle = currentColor;	
					ctx.fillStyle = '#FFF';
					sprayWidth = parseFloat(me.lineType);
					for(var i = sprayWidth*15; i>0; i--) {
						var rndx = mouseX + Math.round(Math.random() * ((sprayWidth * 8) - (sprayWidth * 4)));
						var rndy = mouseY + Math.round(Math.random() * ((sprayWidth * 8) - (sprayWidth * 4)));
						me.drawDot(rndx, rndy, 1, ctx.strokeStyle);
					}
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;    
				case "funEraser":
					var arrTemp = me.currentEraser.split("-");						
					ctx.clearRect(mouseX, mouseY, parseInt(arrTemp[0]), parseInt(arrTemp[1]));
					/*ctx.fillStyle = '#ffffff';
					ctx.fillRect(mouseX, mouseY, parseInt(arrTemp[0]), parseInt(arrTemp[1]));
					*/
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;          
				case "funDrwaArrowText":
					me.drawStraight( intArrowStartX, intArrowStartY, mouseX, mouseY, "ctxTemp");
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawLine":
					me.drawStraightLine( intLineStartX, intLineStartY, mouseX, mouseY, "ctxTemp" );
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawArc":
					me.clearTempCanvas();
					me.drawArcMain( intArcStartX, intArcStartY, mouseX, mouseY, ctxTemp );
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawRect":
					me.drawRectangle(intEmtRectStartX, intEmtRectStartY, mouseX, mouseY, ctxTemp, "ctxTemp");
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawRoundRect":
					me.clearTempCanvas();
					me.drawRoundRectangle(intEmtRoundRectStartX, intEmtRoundRectStartY, mouseX, mouseY, ctxTemp);
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawEllipse":
					me.clearTempCanvas();
					me.drawEllipse(intEmtElpsStartX, intEmtElpsStartY, mouseX, mouseY, ctxTemp);
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawCircle":
					me.clearTempCanvas();
					me.drawCircle(intEmtCirStartX, intEmtCirStartY, mouseX, mouseY, ctxTemp);
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawFilledRectangle":
					me.drawRectangle(intFilledRectStartX, intFilledRectStartY, mouseX, mouseY, ctxTemp, "ctxTemp", true);							
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawFilledRoundRect":
					me.clearTempCanvas();
					me.drawRoundRectangle(intFilledRoundRectStartX, intFilledRoundRectStartY, mouseX, mouseY, ctxTemp, true);
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;						
				case "funDrawFilledEllipse":
					me.clearTempCanvas();
					me.drawEllipse(intFilledElpsStartX, intFilledElpsStartY, mouseX, mouseY, ctxTemp, true);
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
				case "funDrawFilledCircle":
					me.clearTempCanvas();
					me.drawCircle(intFilledCirStartX, intFilledCirStartY, mouseX, mouseY, ctxTemp, true);
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				break;
			}
		}
	};
	this.drawSelectedCanvas = function(r, g, b, a, startX, startY, width, hieght, target){
		var arrRed = r;
		var arrGreen = g;
		var arrBlue = b;
		var arrAlpha = a;		
		
		var myImageData = target.createImageData(width, hieght);
		var len = arrRed.length;
		for (var i = 0; i < len; i++) {
			var red = arrRed[i];			  
			var green = arrGreen[i];
			var blue = arrBlue[i];
			var alpha = arrAlpha[i];
			myImageData.data[i * 4 + 0] = red; // Red value				
			myImageData.data[i * 4 + 1] = green; // Green value
			myImageData.data[i * 4 + 2] = blue; // Blue value	
			myImageData.data[i * 4 + 3] = alpha; // Alpha value			
		}
		target.putImageData(myImageData, startX, startY);
	};
	
	this.funScaleMe = function(val){	
		
		/*
		ctx.strokeRect(5,5,25,15);
		ctx.scale(2,2);
		ctx.strokeRect(5,5,25,15); 
		*/
	};
	
	this.drawStraightLine = function(x1, y1, x2, y2, target, blDrwaText) {
		blDrwaText = blDrwaText || false;
		if(target == "ctxTemp"){
			this.clearTempCanvas();
			ctxTemp.strokeStyle = currentColor;			
			ctxTemp.beginPath();
			ctxTemp.moveTo(x1, y1);
			ctxTemp.lineTo(x2, y2);
			ctxTemp.stroke();
			ctxTemp.beginPath();
		}
		else if(target == "ctx"){
			canvasTempObj.style.display = "none";
			//if((x2 > x1) || (y2 > y1)){ //04-04-2013: Drawing - When you draw a line or arrow upward towards a  it does not draw 
				ctx.strokeStyle = currentColor;			
				ctx.lineWidth = 1;
				ctx.beginPath();
				ctx.moveTo(x1, y1);
				ctx.lineTo(x2, y2);
				ctx.stroke();
				ctx.closePath();					
				/*if(blDrwaText == true){
					//this.drawArrow(x2, y2, ctx);
					this.drawArrow(ctx, x1, y1, x2, y2);
					this.drawText("", x2, y2);
				}*/				
			//}
		}
	};
	this.drawStraight = function(x1, y1, x2, y2, target, blDrwaText) {
		blDrwaText = blDrwaText || false;
		if(target == "ctxTemp"){
			this.clearTempCanvas();
			ctxTemp.strokeStyle = currentColor;			
			//ctxTemp.beginPath();
			//ctxTemp.moveTo(x1, y1);
			//ctxTemp.lineTo(x2, y2);
			//ctxTemp.stroke();
			//ctxTemp.beginPath();
			this.drawArrow(ctxTemp, x1, y1, x2, y2);
		}
		else if(target == "ctx"){
			canvasTempObj.style.display = "none";
			//if((x2 > x1) || (y2 > y1)){ //04-04-2013: Drawing - When you draw a line or arrow upward towards a  it does not draw 
				ctx.strokeStyle = currentColor;			
				//ctx.lineWidth = 1;
				//ctx.beginPath();
				//ctx.moveTo(x1, y1);
				//ctx.lineTo(x2, y2);
				//ctx.stroke();
				//ctx.closePath();					
				if(blDrwaText == true){
					//this.drawArrow(x2, y2, ctx);
					this.drawArrow(ctx, x1, y1, x2, y2);					
					//this.drawText("", x1, y1);  //simi: Remove text from Arrow
				}				
			//}
		}
	};
	this.drawCircleArrow = function(x, y, target){
		target.beginPath();
		target.strokeStyle = "rgb(17, 17, 17)";
		target.arc(x, y, 2, 0, Math.PI*2, false);
		target.stroke();
		target.closePath();		
	};
	
	this.drawArrow = function(target, fromx, fromy, tox, toy){
		target.lineWidth = 1;
		target.beginPath();
		var headlen = 10;	// length of head in pixels
		var dx = tox-fromx;
		var dy = toy-fromy;
		var angle = Math.atan2(dy,dx);
		target.moveTo(fromx, fromy);
		target.lineTo(tox, toy);
		target.lineTo(tox-headlen*Math.cos(angle-Math.PI/6),toy-headlen*Math.sin(angle-Math.PI/6));
		target.moveTo(tox, toy);
		target.lineTo(tox-headlen*Math.cos(angle+Math.PI/6),toy-headlen*Math.sin(angle+Math.PI/6));
		target.stroke();
		target.closePath();
	};
	
	this.drawArcMain = function(x1, y1, x2, y2, target){		
		target.beginPath();				
		target.strokeStyle = "black"; 
		target.lineWidth = "1";
		var tempY = (y2 - y1)/ 2;
		var radius = Math.round(Math.abs((tempY)));
		var blAntiClockCir = false;
		if(x1 < x2){
			blAntiClockCir = true;
		}
		ctx.strokeStyle = currentColor;	
		target.arc(x1, y1, radius, ((Math.PI) * 3)/2, (Math.PI)/2, blAntiClockCir);
		target.stroke();
	};
	this.drawRoundRectangle = function(x1, y1, x2, y2, target, blFilled) {
		blFilled = blFilled || false;
		target.beginPath();
		target.strokeStyle = currentColor;
		target.lineWidth = parseFloat(this.lineType);
		target.beginPath();
		target.moveTo(x1, y1);
		var cpX = 0, cpY = 0, x = 0, y = 0, lX = 0, lY = 0;
		lX = x2;
		lY = y1;
		x = lX + 10;
		y = lY + 10;
		cpX = x;
		cpY = lY;
		target.lineTo(lX, lY);
		target.quadraticCurveTo(cpX , cpY, x, y);
		var cpX = 0, cpY = 0, x = 0, y = 0, lX = 0, lY = 0;
		lX = x2 + 10;
		lY = y2;
		x = lX - 10;
		y = y2 + 10;
		cpX = lX;
		cpY = y2 + 10;				
		target.lineTo(lX, lY);
		target.quadraticCurveTo(cpX, cpY, x, y);
		var cpX = 0, cpY = 0, x = 0, y = 0, lX = 0, lY = 0;
		lX = x1;
		lY = y2 + 10;
		x = lX - 10;
		y = lY - 10;
		cpX = x;
		cpY = lY;
		target.lineTo(lX, lY);
		target.quadraticCurveTo(cpX, cpY, x, y);								
		var cpX = 0, cpY = 0, x = 0, y = 0, lX = 0, lY = 0;
		lX = x1 - 10;
		lY = y1 + 10;
		x = x1;
		y = y1;
		cpX = x1 - 10;
		cpY = y1;
		target.lineTo(lX, lY);
		target.quadraticCurveTo(cpX, cpY, x, y);
		if(blFilled == true){
			target.fillStyle = currentColor;	
			target.fill();
		}
		target.stroke();
		target.closePath();
		switch(gbEventType){
			case "funDrawRoundRect":
				intEmtRoundRectEndX = x2;
				intEmtRoundRectEndY = y2;
			break;
			case "funDrawFilledRoundRect":
				intFilledRoundRectEndX = x2;
				intFilledRoundRectEndY = y2;
			break;	
		}
	};
	this.drawEllipse = function(x1, y1, x2, y2, target, blFilled){
		blFilled = blFilled || false;
		//KAPPA = Subtract one from square root of two, divide the result by three, and multiply by four. ("As simple as 1-2-3-4")
		var KAPPA = 4 * ((Math.sqrt(2) - 1) / 3);
		var rx = (x2 - x1) / 2;
		var ry = (y2 - y1) / 2;	
		var cx = x1 + rx;
		var cy = y1 + ry;
		target.beginPath();
		target.moveTo(cx, cy - ry);
		target.strokeStyle = currentColor;
		target.lineWidth = parseFloat(this.lineType);
		/*
		Quadrant 1:
		cubic bezier control point 1, start height and kappa * radius to the right
		BX1 = cx + (kappa * radiusX);
		BY1 = cy - ry;
		cubic bezier control point 2, end and kappa * radius above
		BX2 = cx + rx;
		BY2 = cy + (kappa * radiusY);
		draw cubic bezier from current point to Q1X/Q1Y with BX1/BY1 and BX2/BY2 as bezier control points
		*/
		var BX1 = 0, BY1 = 0, BX2 = 0, BY2 = 0, Q1X = 0, Q1Y = 0;
		BX1 = cx + (KAPPA * rx);
		BY1 = cy - ry;
		BX2 = cx + rx;
		BY2 = cy - (KAPPA * ry);
		Q1X = cx + rx;
		Q1Y = cy;				
		target.bezierCurveTo(BX1, BY1,  BX2, BY2, Q1X, Q1Y);
		/*
		Quadrant 2:
		BX1 = cx + radiusX;  cubic bezier point 1 
		BY1 = cy + (KAPPA * radiusY); 
		BX2 = cx + (KAPPA * radiusX)  cubic bezier point 2 
		BY2 = cy + radiusY; 
		*/
		var BX1 = 0, BY1 = 0, BX2 = 0, BY2 = 0, Q2X = 0, Q2Y = 0;
		BX1 = cx + rx;
		BY1 = cy + (KAPPA * ry);
		BX2 = cx + (KAPPA * rx);
		BY2 = cy + ry;
		Q2X = cx;
		Q2Y = cy + ry;								
		target.bezierCurveTo(BX1, BY1,  BX2, BY2, Q2X, Q2Y);				
		/*
		Quadrant 3:
		BX1 = cx - (KAPPA * radiusX);  cubic bezier point 1 
		BY1 = cy + radiusY; 
		BX2 = cx - radiusX  cubic bezier point 2 
		BY2 = cy + (KAPPA * radiusY); 
		*/
		var BX1 = 0, BY1 = 0, BX2 = 0, BY2 = 0, Q3X = 0, Q3Y = 0;
		BX1 = cx - (KAPPA * rx);
		BY1 = cy + ry;
		BX2 = cx - rx;
		BY2 = cy + (KAPPA * ry);
		Q3X = cx - rx;
		Q3Y = cy;												
		target.bezierCurveTo(BX1, BY1,  BX2, BY2, Q3X, Q3Y);				
		/*
		Quadrant 4:
		BX1 = cx - rx  cubic bezier point 1 
		BY1 = cy - (KAPPA * ry); 
		BX2 = cx - radiusX  cubic bezier point 2 
		BY2 = cy + (KAPPA * radiusY); 
		*/
		var BX1 = 0, BY1 = 0, BX2 = 0, BY2 = 0, Q4X = 0, Q4Y = 0;
		BX1 = cx - rx;
		BY1 = cy - (KAPPA * ry);
		BX2 = cx - (KAPPA * rx);
		BY2 = cy - ry;
		Q4X = cx;
		Q4Y = cy - ry;
		target.bezierCurveTo(BX1, BY1,  BX2, BY2, Q4X, Q4Y);
		if(blFilled == true){
		target.fillStyle = currentColor;	
		target.fill();
		}				
		target.stroke();
	};
	this.drawCircle = function(x1, y1, x2, y2, target, blFilled){
		blFilled = blFilled || false;
		var radiusX = (x2 - x1) / 2;
		//var radiusY = (y2 - y1) / 2;	
		var radius = radiusX;
		target.beginPath();
		target.lineWidth = parseFloat(this.lineType);
		target.strokeStyle = currentColor;				
		target.arc(x1, y1, radius, 0, Math.PI * 2, false);
		target.restore();
		if(blFilled == true){
			target.fillStyle = currentColor;	
			target.fill();
		}	
		target.stroke();
		target.closePath();
	};
	this.drawRectangle = function(x1, y1, x2, y2, target, forWhat, blFilled){
		blFilled = blFilled || false;
		if(forWhat == "ctxTemp"){
			this.clearTempCanvas();
			if((gbEventType == "funSelect") || (gbEventType == "funSelectCopy")){
				target.lastStrokeStyle = target.strokeStyle;
				target.strokeStyle = target.createPattern(dashed, 'repeat');
				target.strokeFill = 1;
				target.lineWidth = 1;
			}
			else{
				target.strokeStyle = currentColor;
				target.lineWidth = parseFloat(this.lineType);
			}
		}
		else{
			this.clearTempCanvas("none");
			target.strokeStyle = currentColor;
			target.lineWidth = parseFloat(this.lineType);
		}				
		target.beginPath();
		target.moveTo(x1, y1);
		if(forWhat == "ctxTemp"){
			var w = x2 - x1;
			var h = y2 - y1;
		}
		else{
			var w = x2;
			var h = y2;
		}
		if(blFilled == true){
			target.fillStyle = currentColor;
			target.fillRect(x1, y1, w, h);
		}
		else{
			target.strokeRect( x1, y1, w, h );					
		}
		target.beginPath();	
		switch(gbEventType){
			case "funSelect":
			case "funSelectCopy":
				intSelectWidth = w;
				intSelectHieght = h;
				arrTempSel=[x1, y1, w, h];
			break;
			case "funDrawRect":
				intRectWidth = w;
				intRectHieght = h;
			break;
			case "funDrawFilledRectangle":
				intFilledRectWidth = w;
				intFilledRectHieght = h;
			break;
		}
	};
	this.drawDot = function(x, y, size, col, trg) {		
		x = Math.floor(x) + 1; //prevent antialiasing of 1px dots
		y = Math.floor(y) + 1;
		if(x > 0 && y > 0) {		
			if(!trg) { 
				trg = ctx; 
			}			
			if(col || size) {
				var lastcol = trg.fillStyle; 
				var lastsize = sprayWidth; 
			}
			if(col)  { 
				trg.fillStyle = col;  
			}
			if(size) {
				sprayWidth = size; 
			}
			var dotoffset = (sprayWidth > 1) ? sprayWidth/2 : sprayWidth;
			trg.fillRect((x-dotoffset), (y-dotoffset), sprayWidth, sprayWidth);
			if(col || size) { 
				trg.fillStyle = lastcol; 
				sprayWidth = lastsize; 
			}
		}
	};
	
	this.makeButtonActive = function(){
		//clear past selection
		$("#divDrawing"+me.classId+" span").removeClass("drwactive");	
		
		var e = window.event;
		if(e){
			var targ;
			if (e.target) targ = e.target;
			else if (e.srcElement) targ = e.srcElement;
			if(targ && targ.tagName && targ.tagName.toUpperCase()=="SPAN"){
				$(targ).addClass("drwactive");
			}else if(gbEventType=="funPencil"){				
				$("#divDrawing"+me.classId+" span[class*='toolPencil']").addClass("drwactive");
			};			
		}
	};
	
	this.setEvent = function (eventType, img, intSmartTagDivId){
		
		img = img || "";
		intSmartTagDivId = intSmartTagDivId || "0";		
		this.eventType = eventType;
		gbEventType = eventType;
		if((gbEventTypeTemp == "funSelect") && (gbEventType != "funFillColor")){
			this.clearTempCanvas("none");
		}	
		gbEventTypeTemp = null;		
		
		//make icon active--
		me.makeButtonActive();		
		//make icon active--
		
		
		switch(gbEventType){				                   
			case "funSelect":
				if(gbDragStart == false){
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolSelect";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funSelect');
					//this.makeAlert("Select");
					//alert('Drag event is selected to select Select please click on Drag to unselect Drag event.');
				}
			break;
			 case "funSelectCopy":
				if(gbDragStart == false){				
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolSelectCopy";					
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funSelectCopy');
					//this.makeAlert("Select Copy->Paste");
				}	
			break;
			case "funPencil":
				if(gbDragStart == false){
					
					if(document.getElementById("spanImageEvent"+me.classId)){document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolPencil";}
					if(document.getElementById("divEraserType"+me.classId)){document.getElementById("divEraserType"+me.classId).style.display = "none";}
					if(document.getElementById("divRotateAngle"+me.classId)){document.getElementById("divRotateAngle"+me.classId).style.display = "none";}
					if(document.getElementById("divPBType"+me.classId)){document.getElementById("divPBType"+me.classId).style.display = "block";}					
					this.lineType = this.setLineType('1', "spanPB1", "funPencil");					
				}
				else{
					
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funPencil');
					//this.makeAlert("Pencil");
				}
			break;
			case "funBrush":
				if(gbDragStart == false){					
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolBrush";					
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "block";
					this.lineType = this.setLineType('1', "spanPB1", "funBrush");
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funBrush');
					//this.makeAlert("Brush");
				}
			break;
			case "funFillColor":
				if(gbDragStart == false){
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolFillColor";	
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funFillColor');
					//this.makeAlert("Fill Color");
				}					
			break;				
			case "funSparyColor":
				if(gbDragStart == false){
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolSparyColor";
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "block";
					this.lineType = this.setLineType('1', "spanPB1", "funSparyColor");					
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funSparyColor');
					//this.makeAlert("Spary Color");
				}	
			break;
			case "funEraser":
				if(gbDragStart == false){
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolEraserMain";
					document.getElementById("divPBType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("divEraserType"+me.classId).style.display = "block";			
					this.currentEraser = this.setErase('16-16','divEraser16');
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funEraser');
					//this.makeAlert("Eraser");
				}	
			break;			
			case "funText":
				if(gbDragStart == false){
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolText";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funText');
					//this.makeAlert("Text");
				}		
			break;
			case "funClearCanvas":
				if(gbDragStart == false){
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolSmallClear";
					this.clearCanvas();
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funClearCanvas');
					//this.makeAlert("Clear Drawing");
				}		
			break;
			case "funDrwaArrowText":
				if(gbDragStart == false){
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolArrow";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrwaArrowText');
					//this.makeAlert("Arrow Text");
				}		
			break;
			case "funDrawLine":
				if(gbDragStart == false){
					this.makeDivNone();
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolLine";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawLine');
					//this.makeAlert("Line");
				}				
			break;	
			case "funDrawArc":
				if(gbDragStart == false){
					this.makeDivNone();
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolArc";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawArc');
					//this.makeAlert("Arc");
				}		
			break;						
			case "funDrawRect":
				if(gbDragStart == false){
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolRect";
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "block";
					this.lineType = this.setLineType('1', "spanPB1", "funDrawRect");
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawRect');
					//this.makeAlert("Rectangle");
				}		
			break;
			case "funDrawRoundRect":
				if(gbDragStart == false){
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolRoundRect";
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "block";
					this.lineType = this.setLineType('1', "spanPB1", "funDrawRoundRect");
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawRoundRect');
					//this.makeAlert("Rounded Rectangle");
				}	
			break;
			case "funDrawEllipse":
				if(gbDragStart == false){
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolEllipse";
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "block";
					this.lineType = this.setLineType('1', "spanPB1", "funDrawEllipse");
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawEllipse');
					//this.makeAlert("Ellipse");
				}		
			break;
			case "funDrawCircle":
				if(gbDragStart == false){
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolCircle";
					document.getElementById("divEraserType"+me.classId).style.display = "none";
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					document.getElementById("divPBType"+me.classId).style.display = "block";
					this.lineType = this.setLineType('1', "spanPB1", "funDrawCircle");
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawCircle');
					//this.makeAlert("Circle");
				}		
			break;
			case "funDrawFilledRectangle":
				if(gbDragStart == false){
					this.makeDivNone();
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolFilledRect";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawFilledRectangle');
					//this.makeAlert("Filled Rectangle");
				}		
			break;
			case "funDrawFilledRoundRect":
				if(gbDragStart == false){
					this.makeDivNone();
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolFilledRoundRect";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawFilledRoundRect');
					//this.makeAlert("Filled Rounded Rectangle");
				}		
			break;
			case "funDrawFilledEllipse":
				if(gbDragStart == false){
					this.makeDivNone();
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolFilledEllipse";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawFilledEllipse');
					//this.makeAlert("Filled Ellipse");
				}		
			break;
			case "funDrawFilledCircle":
				if(gbDragStart == false){
					this.makeDivNone();
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolFilledCircle";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawFilledCircle');
					//this.makeAlert("Filled Circle");
				}	
			break;
			case "funDrawImage":
				/*
				if(gbDragStart == false){
					this.drawImages(img, intSmartTagDivId);
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('funDrawImage', img, intSmartTagDivId);
					//this.makeAlert(img+" Image");
					//alert('Drag event is selected to select '+img+' Image please click on Drag to unselect Drag event.');
				}
				*/
				
				if(gbDragStart == false){		
					
					//this.setEvent('funDrawImage', img, intSmartTagDivId);
					//this.drawImages(img, intSmartTagDivId);
					//document.getElementById("divRotateAngle"+me.classId).style.display = "none";
						
					document.getElementById("divRotateAngle"+me.classId).style.display = "none";
					fwi_img = img;
					fwi_intSmartTagDivId = intSmartTagDivId;	
					/*
					if(img!=""){
						$("#divDrawing"+me.classId+" #divToolsRight span[title="+img+"], #moreSymp"+me.classId+" span[title="+img+"]").addClass("drwactive");
						alert("#divDrawing"+me.classId+" #divToolsRight span[title="+img+"]\n\n"+$("#divDrawing"+me.classId+" #divToolsRight span[title="+img+"]").length);
					}
					*/				
					//$("#divDrawing"+me.classId+" span").removeClass("drwactive");
					//this.setEvent("releaseEvent");
					
					//gbEventType = null;
					this.makeDivNone();
					document.getElementById("spanImageEvent"+me.classId).className = "";
					//--
					/*
					var e = window.event;
					var targ;
					if (e.target) targ = e.target;
					else if (e.srcElement) targ = e.srcElement;					
					if(targ.tagName.toUpperCase()=="SPAN"){$(targ).addClass("drwactive");};					
					*/
				}
				
			break;
			case "funDrag":
				this.makeDivNone();
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolDrag";
				if(gbDragStart == false){
					gbDragStart = true;
					document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolDrag";
				}
				else if(gbDragStart == true){
					gbEventType = null;
					gbDragStart = false;
					document.getElementById("spanImageEvent"+me.classId).className = "";
				}
				//*
				if(gbDragStart == true){
					//Symptoms Drag Logic
					this.clearImagesFromCanvas(gbArrDrawingImages, "all");
					gbArrDrawingImagesTemp = new Array();
					intImagesCounterTemp = 0;
					this.drwaImagesOnTemp(gbArrDrawingImages);
					gbArrDrawingImages = new Array();
					intDrawingImagesCounter = 0;
					
					/*
					//Text Drag Logic
					gbArrTextTemp = new Array();					
					this.clearTextFromCanvas(gbArrTextMain);					
					this.writeTextOnTemp(gbArrTextMain);
					gbArrTextMain = new Array();
					intTextCounterMain = 0;					
					*/
				}
				else if(gbDragStart == false){
					//alert(gbArrDrawingImagesTemp);
					this.drwaImagesOnMain(gbArrDrawingImagesTemp, "LOCAL");
					//alert(gbArrImagesDB);
					/*
					this.writeTextOnMain(gbArrTextTemp, "LOCAL");
					gbArrTextTemp = new Array();
					*/
					me.arrBlCanvasHaveDrwaing[0] = true;
					me.arrBlCanvasHaveDrwaing[1] = true;
					me.makeCanvasActive();
				}
				//*/
			break;
			case "releaseEvent":
				if(gbDragStart == false){
					gbEventType = null;
					this.makeDivNone();
					document.getElementById("spanImageEvent"+me.classId).className = "";
				}
				else{
					gbEventType = "funDrag";
					this.setEvent('funDrag');
					this.setEvent('releaseEvent');
				}
			break;
			default:				
				this.makeDivNone();
				document.getElementById("spanImageEvent"+me.classId).className = "";
				
			break;
		}
	};
	
	this.makeAlert = function(msg){
		alert('Drag event is selected to select '+msg+' please click on Drag to unselect Drag event.');
	};
	
	this.clearTextFromCanvas = function(arrText){
		for (var i in arrText){
			var x = arrText[i][0];
			var y = arrText[i][1];
			var text = arrText[i][2];
			//var textLength = ((text.length * 14)/2)  + 2;
			var textLength = ctx.measureText(text).width;
			me.clear(x, y - 10, textLength, 14);
		}
	};
	
	this.writeTextOnTemp = function (arrText){
		canvasTempObj.style.display = "block";		
		for (var i in arrText){
			var x = arrText[i][0];
			var y = arrText[i][1];
			var text = arrText[i][2];
			this.drawTextOnTemp(text, x, y);
		}
	};
	
	this.drawTextOnTemp = function(text, x, y){
		var intCounter = intTextCounterTemp;		
		gbArrTextTemp[intCounter] = new Array();
		gbArrTextTemp[intCounter][0] = new DragText(text, x, y, ctxTemp);		
		gbArrTextTemp[intCounter][1] = setInterval(function(){
			//alert(intCounter+"--"+x+"--"+y+"--"+text);
			gbArrTextTemp[intCounter][2] = gbArrTextTemp[intCounter][0].x;
			gbArrTextTemp[intCounter][3] = gbArrTextTemp[intCounter][0].y;
			gbArrTextTemp[intCounter][4] = gbArrTextTemp[intCounter][0].text;
			gbArrTextTemp[intCounter][0].update();
			//alert(gbArrTextTemp);
		}, zTimeoutMM);	
		intTextCounterTemp++;
	};
	
	this.writeTextOnMain = function (arrText, dataFrom){
		//alert(arrText);
		gbArrTextMain = new Array();
		intTextCounterMain = 0;
		this.clearTempCanvas("none");
		gbArrTextDB = new Array();
		for (var i in arrText){
			var strTAName = "drawingTA"+intTextCounterMain;
			if(dataFrom == "LOCAL"){			
				var obj = arrText[i][0];
				var intNumber = parseInt(arrText[i][1]);
				var x = arrText[i][2];
				var y = arrText[i][3];
				var text = arrText[i][4];				
				clearInterval(intNumber);				
			}
			else if(dataFrom == "DB"){
				var x = arrText[i][0];
				var y = arrText[i][1];
				var text = arrText[i][2];
				me.createDOMTA(text, strTAName, ctx);				
			}			
			gbArrTextMain[intTextCounterMain] = new Array();
			gbArrTextMain[intTextCounterMain][0] = x;
			gbArrTextMain[intTextCounterMain][1] = y;
			gbArrTextMain[intTextCounterMain][2] = text;
			gbArrTextMain[intTextCounterMain][3] = strTAName;
			
			gbArrTextDB[intTextCounterMain] = new Array();
			gbArrTextDB[intTextCounterMain][0] = x;
			gbArrTextDB[intTextCounterMain][1] = y;
			gbArrTextDB[intTextCounterMain][2] = text;
			intTextCounterMain++;
			ctx.beginPath();
			ctx.font = "14px Arial";
			ctx.fillStyle = '#171717';
			ctx.fillText(text, x, y);
			ctx.closePath();
		}
		//alert(gbArrTextMain);
		//alert(gbArrTextDB);
		//alert(arrText);
	};
	
	this.clearImagesFromCanvas = function(arrImages, op, floatAngle){
		floatAngle = floatAngle || 0;
		if(op == "all"){
			for (var i in arrImages){
				//alert(arrImages[i]);
				var left = parseInt(arrImages[i][0]);					
				var right = parseInt(arrImages[i][0]) + parseInt(arrImages[i][2]);
				var top = parseInt(arrImages[i][1]);
				var bottom = parseInt(arrImages[i][1]) + parseInt(arrImages[i][3]);
				me.clear(left, top, arrImages[i][2], arrImages[i][3]);
				if((arrImages[i][23] != "") || (arrImages[i][24] != "")){
					//alert(arrImages[i][0]+"--"+arrImages[i][1]+"--"+arrImages[i][2]+"--"+arrImages[i][3]);
					//alert(left+"--"+right+"--"+top+"--"+bottom);
					var y2 = arrImages[i][3]
					if(parseInt(arrImages[i][2]) > parseInt(arrImages[i][3])){
						y2 = (parseInt(arrImages[i][2]) + 15); 
					}
					//alert(left+"--"+top+"--"+arrImages[i][2]+"--"+y2);
					me.clearImage(left, top, arrImages[i][2], y2, ctx);
				}
				var moveToX = parseInt(arrImages[i][7]);
				var moveToY = parseInt(arrImages[i][8]);
				var lineToX = parseInt(arrImages[i][9]);
				var lineToY = parseInt(arrImages[i][10]);
				me.clearLine(moveToX, moveToY, lineToX, lineToY, ctx);
				me.clear(lineToX - 4, lineToY - 4, 8, 8);
				me.clear(arrImages[i][11], arrImages[i][12], arrImages[i][13], arrImages[i][14]);
				if(arrImages[i][6]){
					clearInterval(arrImages[i][6]);
				}
				if(arrImages[i][17]){
					var strSmartTag = arrImages[i][17];
					var intSmartTagXPos = parseInt(arrImages[i][18]);
					var intSmartTagYPos = parseInt(arrImages[i][19]);
					if(strSmartTag != ""){
						var textLength = parseInt(ctx.measureText(strSmartTag).width);
						me.clear(intSmartTagXPos, intSmartTagYPos - 10, textLength, 14)
					}
				}
			}
		}
		else if(op != "all"){
			var i = parseInt(op);
			
			if(typeof(arrImages[i])=="undefined"){return;}
			/*
			//alert(arrImages[i]+" \n "+typeof(arrImages[i][0]));
			str="";
			for(var x in arrImages[i]){
				str+=x+" - "+arrImages[i][x]+", ";
			}
			alert(str);
			*/
			
			var left = parseInt(arrImages[i][0]);
			var right = parseInt(arrImages[i][0]) + parseInt(arrImages[i][2]);
			var top = parseInt(arrImages[i][1]);
			var bottom = parseInt(arrImages[i][1]) + parseInt(arrImages[i][3]);
			//me.clear(left, top, arrImages[i][2], arrImages[i][3]);
			//alert(arrImages[i][0]+"--"+arrImages[i][1]+"--"+arrImages[i][2]+"--"+arrImages[i][3]);
			//alert(left+"--"+right+"--"+top+"--"+bottom);
			var y2 = arrImages[i][3]
			if(parseInt(arrImages[i][2]) > parseInt(arrImages[i][3])){
				y2 = (parseInt(arrImages[i][2]) + 15); 
			}
			//me.clear(left - 5, top - 10, arrImages[i][2] + 10, y2);
			//alert(left+"--"+top+"--"+arrImages[i][2]+"--"+y2);
			me.clearImage(left, top, arrImages[i][2], y2, ctx);
			
			/*var c = 2 * 3.14 * right;
			var facAng = floatAngle/360;
			var arcLen = facAng * c;
			var d = Math.sqrt((right*right) + (bottom*bottom));
			
			var stXTemp = left + arcLen;
			var stYTemp = top - (arcLen/2);
			var stX = d - stXTemp;
			var stX2 = stX + d;
			var stY2 = stYTemp + d;			
			me.clear(stX, stYTemp, stX2, stY2);
			*/
			var moveToX = parseInt(arrImages[i][7]);
			var moveToY = parseInt(arrImages[i][8]);
			var lineToX = parseInt(arrImages[i][9]);
			var lineToY = parseInt(arrImages[i][10]);
			me.clearLine(moveToX, moveToY, lineToX, lineToY, ctx);
			me.clear(lineToX - 4, lineToY - 4, 8, 8);
			me.clear(arrImages[i][11], arrImages[i][12], arrImages[i][13], arrImages[i][14]);
			if(arrImages[i][6]){
				clearInterval(arrImages[i][6]);
			}
			
			if(arrImages[i][17]){
				var strSmartTag = arrImages[i][17];
				var intSmartTagXPos = parseInt(arrImages[i][18]);
				var intSmartTagYPos = parseInt(arrImages[i][19]);
				if(strSmartTag != ""){
					var textLength = parseInt(ctx.measureText(strSmartTag).width);
					me.clear(intSmartTagXPos, intSmartTagYPos - 10, textLength, 14)
				}
			}
		}
	};
	
	this.clearImage = function (x1, y1, x2, y2, target){
		//alert(x1+"--"+y1+"--"+x2+"--"+y2);
		var w = x2;
		var h = y2;
		w = w + 15;
		//target.fillStyle = "red";
		//target.fillRect(x1 - 4, y1 - 2, w, h + 2);	
		//alert(x1+"--"+y1+"--"+x2+"--"+y2+"--"+w+"--"+h);		
		var x = Math.abs(x1 - 4);
		var y = Math.abs(y1 - 10);
		//alert(x+"--"+y+"--"+w+"--"+h);
		//target.fillStyle = "red";
		//target.fillRect(x, y, w, h);
		var frame = target.getImageData(x, y, w, h);
		var l = frame.data.length / 4;		
		var arrRedPixel = new Array();
		var arrGreenPixel = new Array();
		var arrBluePixel = new Array();
		var arrAlphaPixel = new Array();
		//alert(l);
		//console.log(l);
		for (var i = 0; i < l; i++) {
			var r = frame.data[i * 4 + 0];			  
			var g = frame.data[i * 4 + 1];
			var b = frame.data[i * 4 + 2];
			var a = frame.data[i * 4 + 3];				
			//console.log(r+"--"+g+"--"+b+"--"+a+"--"+i);
			//console.log(((r >= 112) && (r <= 122)) && ((g >= 70) && (g <= 80)) && ((b >= 30) && (b <= 40)));
			//console.log(((r >= 112) && (r <= 122)));
			//((g >= 70) && (g <= 80))
			//((b >= 30) && (b <= 40))
			if(((r > 1) && (g > 1) && (b > 1) && (a > 1))){
				//console.log("yes"+"--"+i);
				//alert("yes");
				r = 0;
				g = 0;
				b = 0;
				a = 0;
			}
			arrRedPixel[i] = r;
			arrGreenPixel[i] = g;
			arrBluePixel[i] = b;
			arrAlphaPixel[i] = a;
		}
		//return;
		var myImageData = target.createImageData(w, h);
		var len = arrRedPixel.length;
		for (var i = 0; i < len; i++) {
			var r = arrRedPixel[i];			  
			var g = arrGreenPixel[i];
			var b = arrBluePixel[i];
			var a = arrAlphaPixel[i];
			myImageData.data[i * 4 + 0] = r;			
			myImageData.data[i * 4 + 1] = g;
			myImageData.data[i * 4 + 2] = b;
			myImageData.data[i * 4 + 3] = a;
			/*if((r == 0) && (g == 0) && (b == 0)){				
				console.log("yes-put");				
			}*/		
		}							
		target.putImageData(myImageData, x, y);
	};
	
	this.drwaImagesOnTemp = function(arrImages){
		canvasTempObj.style.display = "block";
		for (var i in arrImages){			
			var x = parseInt(arrImages[i][0]);
			var y = parseInt(arrImages[i][1]);
			var w = parseInt(arrImages[i][2]);
			var h = parseInt(arrImages[i][3]);
			var intNumber = parseInt(arrImages[i][4]);
			var obj = arrImages[i][5];
			var interval = parseInt(arrImages[i][6]);
			var moveToX = parseInt(arrImages[i][7]);
			var moveToY = parseInt(arrImages[i][8]);
			var lineToX = parseInt(arrImages[i][9]);
			var lineToY = parseInt(arrImages[i][10]);
			var textX = parseInt(arrImages[i][11]);
			var textY = parseInt(arrImages[i][12]);
			var textW = parseInt(arrImages[i][13]);
			var textH = parseInt(arrImages[i][14]);
			var path = arrImages[i][15];
			var pathT = arrImages[i][16];
			var strSmartTag = intSmartTagXPos = intSmartTagYPos = strSmartTagID = strSmartTagMasterId = strSmartTagChildId = null;
			if(arrImages[i][17]){
				strSmartTag = arrImages[i][17];
				intSmartTagXPos = parseInt(arrImages[i][18]);
				intSmartTagYPos = parseInt(arrImages[i][19]);
				strSmartTagID = arrImages[i][20];
				strSmartTagMasterId = arrImages[i][21];
				strSmartTagChildId = arrImages[i][22];
			}
			
			var flotRotateAngleClockWise = arrImages[i][23];
			var floatRotateAngleAntiClockWise = arrImages[i][24];
			//alert(flotRotateAngleClockWise+"--"+floatRotateAngleAntiClockWise);
			me.drawOnTemp2(path, x, y, w, h, moveToX, moveToY, lineToX, lineToY, textX, textY, textW, textH, ctxTemp, pathT, strSmartTag, intSmartTagXPos, intSmartTagYPos, strSmartTagID, strSmartTagMasterId, strSmartTagChildId, flotRotateAngleClockWise, floatRotateAngleAntiClockWise);
		}
	};
	
	this.drwaImagesOnMain = function(arrImages, dataFrom){
		//alert(arrImages);
		this.clearTempCanvas("none");
		gbArrDrawingImages = new Array();		
		gbArrImagesDB = new Array();
		intDrawingImagesCounter = 0;
		for (var i in arrImages){
			if(dataFrom == "LOCAL"){
				var x = parseInt(arrImages[i][0]);
				var y = parseInt(arrImages[i][1]);
				var w = parseInt(arrImages[i][2]);
				var h = parseInt(arrImages[i][3]);
				var intNumber = parseInt(arrImages[i][4]);
				var obj = arrImages[i][5];
				var interval = parseInt(arrImages[i][6]);
				var moveToX = parseInt(arrImages[i][7]);
				var moveToY = parseInt(arrImages[i][8]);
				var lineToX = parseInt(arrImages[i][9]);
				var lineToY = parseInt(arrImages[i][10]);
				var textX = parseInt(arrImages[i][11]);
				var textY = parseInt(arrImages[i][12]);
				var textW = parseInt(arrImages[i][13]);
				var textH = parseInt(arrImages[i][14]);
				var path = arrImages[i][15];
				var pathT = arrImages[i][16];
				var strSmartTag = intSmartTagXPos = intSmartTagYPos = strSmartTagID = strSmartTagMasterId = strSmartTagChildId = null;
				if(arrImages[i][17]){
					strSmartTag = arrImages[i][17];
					intSmartTagXPos = parseInt(arrImages[i][18]);
					intSmartTagYPos = parseInt(arrImages[i][19]);
					strSmartTagID = arrImages[i][20];
					strSmartTagMasterId = arrImages[i][21];
					strSmartTagChildId = arrImages[i][22];
				}
				
				var flotRotateAngleClockWise = arrImages[i][23];
				var floatRotateAngleAntiClockWise = arrImages[i][24];
				//alert(flotRotateAngleClockWise+"--"+floatRotateAngleAntiClockWise);
				//alert(path+"--"+pathT);
				clearInterval(interval);
			}
			if(dataFrom == "DB"){
				//path = arrImages[i][0];
				//alert(arrImages[i]);
				var x = parseInt(arrImages[i][1]);
				var y = parseInt(arrImages[i][2]);
				var w = parseInt(arrImages[i][3]);
				var h = parseInt(arrImages[i][4]);
				var moveToX = parseInt(arrImages[i][5]);
				var moveToY = parseInt(arrImages[i][6]);
				var lineToX = parseInt(arrImages[i][7]);
				var lineToY = parseInt(arrImages[i][8]);
				var textX = parseInt(arrImages[i][9]);
				var textY = parseInt(arrImages[i][10]);
				var textW = parseInt(arrImages[i][11]);
				var textH = parseInt(arrImages[i][12]);
				var mainImageName = arrImages[i][13];
				var textImageName = arrImages[i][14];
				
				var strSmartTag = intSmartTagXPos = intSmartTagYPos = strSmartTagID = strSmartTagMasterId = strSmartTagChildId = null;	
				if(typeof(arrImages[i][15]) != "undefined"){
					strSmartTag = arrImages[i][15];
					intSmartTagXPos = arrImages[i][16];
					intSmartTagYPos = arrImages[i][17];
					strSmartTagID = arrImages[i][18];
					strSmartTagMasterId = arrImages[i][19];
					strSmartTagChildId = arrImages[i][20];
				}
				var flotRotateAngleClockWise = "", floatRotateAngleAntiClockWise = "";
				if(typeof(arrImages[i][21]) != "undefined"){
					flotRotateAngleClockWise = arrImages[i][21];
					floatRotateAngleAntiClockWise = arrImages[i][22];
				}
				switch(mainImageName){
					case "Pucker.png":					
						path = imgPuker.src;
						//alert(strSmartTagID);
						if((strSmartTag != "") && (intSmartTagXPos > 0) && (intSmartTagYPos > 0) && (strSmartTagID != "")){
							//alert("asddfg");
							pathT = imgPukerTSmartTag.src;
						}
						else{
							//alert("asd");
							pathT = imgPukerT.src;
						}
						me.addArrMenu("Pucker");
					break;
					case "Drusen.png":					
						path = imgDrusen.src;
						if((strSmartTag != "") && (intSmartTagXPos > 0) && (intSmartTagYPos > 0) && (strSmartTagID != "")){
							//alert("asddfg");
							pathT = imgDrusenTSmartTag.src;
						}
						else{
							//alert("asd");
							pathT = imgDrusenT.src;
						}
						me.addArrMenu("Drusen");
					break;
					case "Pallor.png":					
						path = imgPallor.src;
						if((strSmartTag != "") && (intSmartTagXPos > 0) && (intSmartTagYPos > 0) && (strSmartTagID != "")){
							//alert("asddfg");
							pathT = imgPallorTSmartTag.src;
						}
						else{
							//alert("asd");
							pathT = imgPallorT.src;
						}
						me.addArrMenu("Pallor");
					break;
					case "RetinalHemorrhage.png":					
						path = imgRetinalHemorrhage.src;
						pathT = imgRetinalHemorrhageT.src;
						me.addArrMenu("Retinal Hemorrhage");
					break;
					case "LatticeDegeneration.png":					
						path = imgLatticeDegeneration.src;
						pathT = imgLatticeDegenerationT.src;
						me.addArrMenu("Lattice Degeneration");
					break;
					case "RetinalTear.png":					
						path = imgRetinalTear.src;
						pathT = imgRetinalTearT.src;
						me.addArrMenu("Retinal Tear");
					break;
					case "HorseShoeTear.png":					
						path = imgHorseShoeTear.src;
						pathT = imgHorseShoeTearT.src;
						me.addArrMenu("Horse Shoe Tear");
					break;
					case "DryDegeneration.png":					
						path = imgDryDegeneration.src;
						pathT = imgDryDegenerationT.src;
						me.addArrMenu("Dry Degeneration");
					break;
					case "ChoroidalNevus.png":					
						path = imgChoroidalNevus.src;
						pathT = imgChoroidalNevusT.src;
						me.addArrMenu("Choroidal Nevus");
					break;
					case "SPKMild.png":					
						path = imgSPKMild.src;
						pathT = imgSPKMildT.src;
						me.addArrMenu("SPK Mild");
					break;
					case "disciformScar.png":					
						path = imgDisciformScar.src;
						pathT = imgDisciformScarT.src;
						me.addArrMenu("Disciform Scar");
					break;
					case "exudates.png":					
						path = imgExudates.src;
						pathT = imgExudatesT.src;
						me.addArrMenu("Exudates");
					break;
					case "hemorrhageRed.png":					
						path = imgHemorrhageRed.src;
						pathT = imgHemorrhageRedT.src;
						me.addArrMenu("Hemorrhage");
					break;
					case "neovascularization.png":					
						path = imgNeovascularization.src;
						pathT = imgNeovascularizationT.src;
						me.addArrMenu("Neovascularization");
					break;
					case "pannus.png":					
						path = imgPannus.src;
						pathT = imgPannusT.src;
						me.addArrMenu("Pannus");
					break;
					case "pingnelcula.png":					
						path = imgPingnelcula.src;
						pathT = imgPingnelculaT.src;
						me.addArrMenu("Pinguecula");
					break;
					case "prpTreatment.png":					
						path = imgPRPTreatment.src;
						pathT = imgPRPTreatmentT.src;
						me.addArrMenu("PRP Treatment(Laser)");
					break;
					case "pterygium.png":					
						path = imgPterygium.src;
						pathT = imgPterygiumT.src;
						me.addArrMenu("Pterygium");
					break;
					case "redDot.png":					
						path = imgRedDot.src;
						pathT = imgRedDotT.src;
						me.addArrMenu("Red Dot");
					break;
					case "retina_hemorrhage_dot.png":					
						path = imgRetinalHemorrhageDot.src;
						pathT = imgRetinalHemorrhageDotT.src;
						me.addArrMenu("Retinal Hemorrhage Dot");
					break;
					case "ulcer.png":					
						path = imgUlcer.src;
						pathT = imgUlcerT.src;
						me.addArrMenu("Ulcer");
					break;					
					case "retinalIrregularityHemorrhage.png":					
						path = imgRetinalIrregularHemorrhage.src;
						pathT = imgRetinalIrregularHemorrhageT.src;
						me.addArrMenu("Retinal Irregular Hemorrhage");	
					break;
					
					/*new icons*/
					case "Blue_dots.png":					
						path = imgCME.src;
						pathT = imgCMET.src;
						me.addArrMenu("Blue Dots");
					break;
					
					case "drusen_mild.png":					
						path = imgDruMild.src;
						pathT = imgDruMildT.src;
						me.addArrMenu("Drusen Mild");
					break;
					
					case "drusen_moderate.png":					
						path = imgDruMod.src;
						pathT = imgDruModT.src;
						me.addArrMenu("Drusen Moderate");
					break;
					
					case "floaters.png":					
						path = imgFloaters.src;
						pathT = imgFloatersT.src;
						me.addArrMenu("Floaters");
					break;
					
					case "Green_Dots.png":					
						path = imgFocalTreatment.src;
						pathT = imgFocalTreatmentT.src;
						me.addArrMenu("Green Dots");
					break;
					
					/*
					case "lattice.png":					
						path = imgLattice.src;
						pathT = imgLatticeT.src;
						me.addArrMenu("Lattice");
					break;
					*/
					
					case "ma.png":					
						path = imgMA.src;
						pathT = imgMAT.src;
						me.addArrMenu("MA");
					break;
					
					case "pvd.png":					
						path = imgPVD.src;
						pathT = imgPVDT.src;
						me.addArrMenu("PVD");
					break;
					
					case "RPEChanges.png":					
						path = imgRPEChanges.src;
						pathT = imgRPEChangesT.src;
						me.addArrMenu("RPE Changes");
					break;	
					
					case "ERM.png":					
						path = imgERM.src;
						pathT = imgERMT.src;
						me.addArrMenu("ERM");
					break;	
					
					
				/*	case "xx.png":					
						path = imgx.src;
						pathT = imgxT.src;
						me.addArrMenu("x");
					break;					
					*/
					
					/*new icons*/					
				}
			}
			
			//alert(textX+"--"+textY+"--"+textW+"--"+textH+"--"+path+"--"+pathT+"--"+strSmartTag+"--"+intSmartTagXPos+"--"+intSmartTagYPos+"--"+strSmartTagID);
			me.drawOnMain(path, x, y, w, h, moveToX, moveToY, lineToX, lineToY, textX, textY, textW, textH, ctx, pathT, strSmartTag, intSmartTagXPos, intSmartTagYPos, strSmartTagID, strSmartTagMasterId, strSmartTagChildId, flotRotateAngleClockWise, floatRotateAngleAntiClockWise);
			var aarMainImage = path.split("/");
			gbArrImagesDB[i] = new Array();
			gbArrImagesDB[i][0] = aarMainImage[aarMainImage.length - 1];
			gbArrImagesDB[i][1] = x;
			gbArrImagesDB[i][2] = y;
			gbArrImagesDB[i][3] = w;
			gbArrImagesDB[i][4] = h;
			gbArrImagesDB[i][5] = moveToX;
			gbArrImagesDB[i][6] = moveToY;
			gbArrImagesDB[i][7] = lineToX;
			gbArrImagesDB[i][8] = lineToY;
			gbArrImagesDB[i][9] = textX;
			gbArrImagesDB[i][10] = textY;
			gbArrImagesDB[i][11] = textW;
			gbArrImagesDB[i][12] = textH;			
			gbArrImagesDB[i][13] = aarMainImage[aarMainImage.length - 1];
			var aarTextImage = pathT.split("/");
			gbArrImagesDB[i][14] = aarTextImage[aarTextImage.length - 1];
			
			gbArrImagesDB[i][15] = strSmartTag;
			gbArrImagesDB[i][16] = intSmartTagXPos;
			gbArrImagesDB[i][17] = intSmartTagYPos;
			gbArrImagesDB[i][18] = strSmartTagID;
			gbArrImagesDB[i][19] = strSmartTagMasterId;
			gbArrImagesDB[i][20] = strSmartTagChildId;			
			gbArrImagesDB[i][21] = flotRotateAngleClockWise;//Clock Wise Angle
			gbArrImagesDB[i][22] = floatRotateAngleAntiClockWise;//Anti Clock Wise Angle
		}
		//gbArrImagesDB = gbArrImagesDB.join("~");
		//alert(gbArrImagesDB);				
		gbArrDrawingImagesTemp = new Array();
		intImagesCounterTemp = 0
		gbEventType = null;
	};
	
	this.clearLine = function (x1, y1, x2, y2, target) {        
		//alert(x1+"--"+y1+"--"+x2+"--"+y2);
		target.strokeStyle = "rgb(1, 1, 1)";
		target.lineWidth = 2;
		target.beginPath();
		target.moveTo(x1, y1);
		target.lineTo(x2, y2);
		target.stroke();
		target.closePath();
		var w = Math.abs(x2 - x1);
		var h = 0;
		if(w == 0){
			w = 6;
		}
		if(x1 < x2){
			x1 = x1;
		}
		else if(x1 > x2){
			x1 = x2;			
		}
		
		if(y1 < y2){
			h = Math.abs((y2 - y1) + 5);
			y1 = y1;
		}
		else if(y1 > y2){
			h = Math.abs((y2 - y1) - 5);
			y1 = y2;			
		}
		else{
			h = 5;
		}
		w = w + 15;
		//ctx.fillStyle = "red";
		//ctx.fillRect(x1 - 4, y1 - 2, w, h + 2);	
		//alert(x1+"--"+y1+"--"+x2+"--"+y2+"--"+w+"--"+h);		
		var x = Math.abs(x1 - 4);
		var y = Math.abs(y1 - 4);
		h = h;
		//alert(x+"--"+y+"--"+w+"--"+h);
		//ctx.fillStyle = "red";
		//ctx.fillRect(x, y, w, h);
		var frame = target.getImageData(x, y, w, h);
		var l = frame.data.length / 4;		
		var arrRedPixel = new Array();
		var arrGreenPixel = new Array();
		var arrBluePixel = new Array();
		var arrAlphaPixel = new Array();
		//alert(l);
		for (var i = 0; i < l; i++) {
			var r = frame.data[i * 4 + 0];			  
			var g = frame.data[i * 4 + 1];
			var b = frame.data[i * 4 + 2];
			var a = frame.data[i * 4 + 3];				
			//console.log(r+"--"+g+"--"+b+"--"+a);
			//console.log(((r >= 112) && (r <= 122)) && ((g >= 70) && (g <= 80)) && ((b >= 30) && (b <= 40)));
			//console.log(((r >= 112) && (r <= 122)));
			//((g >= 70) && (g <= 80))
			//((b >= 30) && (b <= 40))
			if(((r == 1) && (g == 1) && (b == 1)) || ((r == 0) && (g == 0) && (b == 0) && (a > 0)) || (((r > 1) && (r <=5)) && ((g > 1) && (g <= 5)) && ((b > 1) && (b <=5)))){
			//if(((r >= 112) && (r <= 122)) && ((g >= 70) && (g <= 80)) && ((b >= 30) && (b <= 40))){			
				//console.log("yes");
				r = 0;
				g = 0;
				b = 0;
				a = 0;
			}
			arrRedPixel[i] = r;
			arrGreenPixel[i] = g;
			arrBluePixel[i] = b;
			arrAlphaPixel[i] = a;
		}
		
		var myImageData = target.createImageData(w, h);
		var len = arrRedPixel.length;
		for (var i = 0; i < len; i++) {
			var r = arrRedPixel[i];			  
			var g = arrGreenPixel[i];
			var b = arrBluePixel[i];
			var a = arrAlphaPixel[i];
			myImageData.data[i * 4 + 0] = r;			
			myImageData.data[i * 4 + 1] = g;
			myImageData.data[i * 4 + 2] = b;
			myImageData.data[i * 4 + 3] = a;
			if((r == 0) && (g == 0) && (b == 0)){				
				//console.log("yes-put");				
			}		
		}							
		target.putImageData(myImageData, x, y);
	};
	
	this.drawOnTemp2 = function(path, x, y, w, h, moveToX, moveToY, lineToX, lineToY, textX, textY, textW, textH, target, pathT, strSmartTag, intSmartTagXPos, intSmartTagYPos, strSmartTagID, strSmartTagMasterId, strSmartTagChildId, flotRotateAngleClockWise, floatRotateAngleAntiClockWise){
		//var textX = x;
		//var textY = y + 205;
		var NewX = new Array();
		var NewY = new Array();
		var NewArrowX = new Array();
		var NewArrowY = new Array();
		var imagePosition = new Array();
		var intCounter = intImagesCounterTemp;	
		//alert(intCounter+"-"+strSmartTag+"-"+intSmartTagXPos+"-"+intSmartTagYPos);
		gbArrDrawingImagesTemp[intCounter] = new Array();
		
		gbArrDrawingImagesTemp[intCounter][0] = x;
		gbArrDrawingImagesTemp[intCounter][1] = y;
		gbArrDrawingImagesTemp[intCounter][2] = w;
		gbArrDrawingImagesTemp[intCounter][3] = h;
		gbArrDrawingImagesTemp[intCounter][4] = intCounter;
			
		gbArrDrawingImagesTemp[intCounter][5] = new DragImage(path, x, y, target, true, textX, textY, pathT, flotRotateAngleClockWise, floatRotateAngleAntiClockWise);
		gbArrDrawingImagesTemp[intCounter][6] = setInterval(function() {
			var arrowX = lineToX;
			var arrowY = lineToY;			
			imagePosition[intCounter] = gbArrDrawingImagesTemp[intCounter][5].update();			
			if(imagePosition[intCounter].from == "upImage"){
				gbArrDrawingImagesTemp[intCounter][0] = imagePosition[intCounter].x;
				gbArrDrawingImagesTemp[intCounter][1] = imagePosition[intCounter].y;
				gbArrDrawingImagesTemp[intCounter][2] = imagePosition[intCounter].w;
				gbArrDrawingImagesTemp[intCounter][3] = imagePosition[intCounter].h;
				
				var lineX = imagePosition[intCounter].x + (imagePosition[intCounter].w/2);
				var lineY = imagePosition[intCounter].y + imagePosition[intCounter].h;
				//alert(imagePosition[intCounter].x+"^^"+imagePosition[intCounter].y);
				//console.log(typeof(NewArrowX[intCounter]));				
				if(typeof(NewArrowX[intCounter]) == "undefined" && typeof(NewArrowY[intCounter]) == "undefined"){
					var writeAtX = arrowX;
					var writeAtY = arrowY;				
				}
				else if(typeof(NewArrowX[intCounter]) == "number" && typeof(NewArrowY[intCounter]) == "number"){
					var writeAtX = parseInt(NewArrowX[intCounter]);
					var writeAtY = parseInt(NewArrowY[intCounter]);				
				}
				/*
				target.strokeStyle = "rgb(17, 17, 17)";
				target.lineWidth = 1;
				target.beginPath();
				NewX[intCounter] = lineX;
				NewY[intCounter] = lineY;
				target.moveTo(lineX, lineY);
				*/
				gbArrDrawingImagesTemp[intCounter][7] = lineX;
				gbArrDrawingImagesTemp[intCounter][8] = lineY;
				/*
				target.lineTo(writeAtX, writeAtY);
				*/
				gbArrDrawingImagesTemp[intCounter][9] = writeAtX;
				gbArrDrawingImagesTemp[intCounter][10] = writeAtY;
				/*
				target.stroke();
				target.closePath();
				me.drawCircleArrow(writeAtX, writeAtY, target);
				*/
				gbArrDrawingImagesTemp[intCounter][11] = imagePosition[intCounter].textX;
				gbArrDrawingImagesTemp[intCounter][12] = imagePosition[intCounter].textY;
				gbArrDrawingImagesTemp[intCounter][13] = imagePosition[intCounter].textW;
				gbArrDrawingImagesTemp[intCounter][14] = imagePosition[intCounter].textH;
				if(strSmartTag != null){
					me.writeSmartTag(target, strSmartTag, intSmartTagXPos, intSmartTagYPos);
					gbArrDrawingImagesTemp[intCounter][17] = strSmartTag;
					gbArrDrawingImagesTemp[intCounter][18] = intSmartTagXPos;
					gbArrDrawingImagesTemp[intCounter][19] = intSmartTagYPos;
					gbArrDrawingImagesTemp[intCounter][20] = strSmartTagID;
					gbArrDrawingImagesTemp[intCounter][21] = strSmartTagMasterId;
					gbArrDrawingImagesTemp[intCounter][22] = strSmartTagChildId;
				}
			}
			else if(imagePosition[intCounter].from == "downImage"){ //Text image no need
				gbArrDrawingImagesTemp[intCounter][11] = imagePosition[intCounter].x;
				gbArrDrawingImagesTemp[intCounter][12] = imagePosition[intCounter].y;
				gbArrDrawingImagesTemp[intCounter][13] = imagePosition[intCounter].w;
				gbArrDrawingImagesTemp[intCounter][14] = imagePosition[intCounter].h;
				
				var lineX = imagePosition[intCounter].x + (imagePosition[intCounter].w/2);
				var lineY = imagePosition[intCounter].y;
				NewArrowX[intCounter] = lineX;
				NewArrowY[intCounter] = lineY;
				//alert(imagePosition[intCounter].x+"^^"+imagePosition[intCounter].y);
				var writeAtX = arrowX;
				var writeAtY = y + h;				
				target.strokeStyle = "rgb(17, 17, 17)";
				target.lineWidth = 1;
				target.beginPath();
				target.moveTo(lineX, lineY - 2);
				
				gbArrDrawingImagesTemp[intCounter][7] = lineX;
				gbArrDrawingImagesTemp[intCounter][8] = lineY - 2;
				
				if(typeof(NewX[intCounter]) == "undefined" && typeof(NewY[intCounter]) == "undefined"){
					target.lineTo(writeAtX, writeAtY);
					gbArrDrawingImagesTemp[intCounter][9] = writeAtX;
					gbArrDrawingImagesTemp[intCounter][10] = writeAtY;
				}
				else if(typeof(NewX[intCounter]) == "number" && typeof(NewY[intCounter]) == "number"){
					target.lineTo(NewX[intCounter], NewY[intCounter]);
					gbArrDrawingImagesTemp[intCounter][9] = writeAtX;
					gbArrDrawingImagesTemp[intCounter][10] = writeAtY;
				}
				target.stroke();
				target.closePath();
				me.drawCircleArrow(lineX, lineY - 2, target);
				
				if(strSmartTag != null){
					intSmartTagXPos = imagePosition[intCounter].x + 3;
					intSmartTagYPos = imagePosition[intCounter].y + imagePosition[intCounter].h + 10;				
					me.writeSmartTag(target, strSmartTag, intSmartTagXPos, intSmartTagYPos);
					gbArrDrawingImagesTemp[intCounter][17] = strSmartTag;
					gbArrDrawingImagesTemp[intCounter][18] = intSmartTagXPos;
					gbArrDrawingImagesTemp[intCounter][19] = intSmartTagYPos;
					gbArrDrawingImagesTemp[intCounter][20] = strSmartTagID;
					gbArrDrawingImagesTemp[intCounter][21] = strSmartTagMasterId;
					gbArrDrawingImagesTemp[intCounter][22] = strSmartTagChildId;
				}
			}
			gbArrDrawingImagesTemp[intCounter][23] = flotRotateAngleClockWise;
			gbArrDrawingImagesTemp[intCounter][24] = floatRotateAngleAntiClockWise;			
		}, zTimeoutMM);
		gbArrDrawingImagesTemp[intCounter][15] = path;
		gbArrDrawingImagesTemp[intCounter][16] = pathT;
		
		intImagesCounterTemp++;
	};
	
	this.drawOnMain = function(path, x, y, w, h, moveToX, moveToY, lineToX, lineToY, textX, textY, textW, textH, target, pathT, strSmartTag, intSmartTagXPos, intSmartTagYPos, strSmartTagID, strSmartTagMasterId, strSmartTagChildId, flotRotateAngleClockWise, floatRotateAngleAntiClockWise){
		var intCounter = intDrawingImagesCounter;
		var imagePosition = new Array();
		gbArrDrawingImages[intCounter] = new Array();		
		gbArrDrawingImages[intCounter][0] = x;
		gbArrDrawingImages[intCounter][1] = y;
		gbArrDrawingImages[intCounter][2] = w;
		gbArrDrawingImages[intCounter][3] = h;
		gbArrDrawingImages[intCounter][4] = intCounter;
		//alert(gbArrDrawingImages[intCounter][0]+" outer");
		gbArrDrawingImages[intCounter][5] = new DragImage(path, x, y, target, false, textX, textY, pathT, flotRotateAngleClockWise, floatRotateAngleAntiClockWise);
		gbArrDrawingImages[intCounter][6] = setInterval(function() {
			imagePosition[intCounter] = gbArrDrawingImages[intCounter][5].update();
			if((imagePosition[intCounter].mainImageLoad == true) && (imagePosition[intCounter].textImageLoad == true)){
				clearInterval(gbArrDrawingImages[intCounter][6]);
			}
			/*gbArrDrawingImages[intCounter][0] = imagePosition[intCounter].x;
			gbArrDrawingImages[intCounter][1] = imagePosition[intCounter].y;
			gbArrDrawingImages[intCounter][2] = imagePosition[intCounter].w;
			gbArrDrawingImages[intCounter][3] = imagePosition[intCounter].h;
			*/
			//alert(gbArrDrawingImages[intCounter][0]+" inner");
			//alert(imagePosition[intCounter].x+"^^"+imagePosition[intCounter].y);
			//console.log(typeof(NewArrowX[intCounter]));
			/***T
			target.strokeStyle = "rgb(17, 17, 17)";
			target.lineWidth = 1;
			target.beginPath();			
			target.moveTo(moveToX, moveToY);	
			
			//gbArrDrawingImages[intCounter][7] = moveToX;
			//gbArrDrawingImages[intCounter][8] = moveToY;
			target.lineTo(lineToX, lineToY);
			//gbArrDrawingImages[intCounter][9] = lineToX;
			//gbArrDrawingImages[intCounter][10] = lineToY;
			target.stroke();
			target.closePath();
			me.drawCircleArrow(lineToX, lineToY, target);
			***/
			//gbArrDrawingImages[intCounter][11] = textX;
			//gbArrDrawingImages[intCounter][12] = textY;
			//gbArrDrawingImages[intCounter][13] = textW;
			//gbArrDrawingImages[intCounter][14] = textH;
		}, zTimeoutMM);
		gbArrDrawingImages[intCounter][7] = moveToX;
		gbArrDrawingImages[intCounter][8] = moveToY;
		gbArrDrawingImages[intCounter][9] = lineToX;
		gbArrDrawingImages[intCounter][10] = lineToY;
		gbArrDrawingImages[intCounter][11] = textX;
		gbArrDrawingImages[intCounter][12] = textY;
		gbArrDrawingImages[intCounter][13] = textW;
		gbArrDrawingImages[intCounter][14] = textH;
		gbArrDrawingImages[intCounter][15] = path;
		gbArrDrawingImages[intCounter][16] = pathT;
		if(strSmartTag != null){
			me.writeSmartTag(target, strSmartTag, intSmartTagXPos, intSmartTagYPos);
			gbArrDrawingImages[intCounter][17] = strSmartTag;
			gbArrDrawingImages[intCounter][18] = intSmartTagXPos;
			gbArrDrawingImages[intCounter][19] = intSmartTagYPos;
			gbArrDrawingImages[intCounter][20] = strSmartTagID;
			gbArrDrawingImages[intCounter][21] = strSmartTagMasterId;
			gbArrDrawingImages[intCounter][22] = strSmartTagChildId;
		}
		gbArrDrawingImages[intCounter][23] = flotRotateAngleClockWise;
		gbArrDrawingImages[intCounter][24] = floatRotateAngleAntiClockWise;
		intDrawingImagesCounter++;
	};
	
	//not in use
	this.drawOnTemp = function(path, x, y, w, h, target){
		var pathT = imgPukerT.src;
		var textX = x;
		var textY = y + 205;
		var NewX = new Array();
		var NewY = new Array();
		var NewArrowX = new Array();
		var NewArrowY = new Array();
		var imagePosition = new Array();
		var intCounter = intImagesCounterTemp;		
		gbArrDrawingImagesTemp[intCounter] = new Array();
		
		gbArrDrawingImagesTemp[intCounter][0] = x;
		gbArrDrawingImagesTemp[intCounter][1] = y;
		gbArrDrawingImagesTemp[intCounter][2] = w;
		gbArrDrawingImagesTemp[intCounter][3] = h;
		gbArrDrawingImagesTemp[intCounter][4] = intCounter;
		
		gbArrDrawingImagesTemp[intCounter][5] = new DragImage(path, x, y, target, true, textX, textY, pathT);
		gbArrDrawingImagesTemp[intCounter][6] = setInterval(function() {
			var arrowX = x + (w/2);
			var arrowY = y + 200;
			imagePosition[intCounter] = gbArrDrawingImagesTemp[intCounter][5].update();			
			if(imagePosition[intCounter].from == "upImage"){
				
				gbArrDrawingImagesTemp[intCounter][0] = imagePosition[intCounter].x;
				gbArrDrawingImagesTemp[intCounter][1] = imagePosition[intCounter].y;
				gbArrDrawingImagesTemp[intCounter][2] = imagePosition[intCounter].w;
				gbArrDrawingImagesTemp[intCounter][3] = imagePosition[intCounter].h;
				
				var lineX = imagePosition[intCounter].x + (imagePosition[intCounter].w/2);
				var lineY = imagePosition[intCounter].y + imagePosition[intCounter].h;
				//alert(imagePosition[intCounter].x+"^^"+imagePosition[intCounter].y);
				//console.log(typeof(NewArrowX[intCounter]));				
				if(typeof(NewArrowX[intCounter]) == "undefined" && typeof(NewArrowY[intCounter]) == "undefined"){
					var writeAtX = arrowX;
					var writeAtY = arrowY;				
				}
				else if(typeof(NewArrowX[intCounter]) == "number" && typeof(NewArrowY[intCounter]) == "number"){
					var writeAtX = parseInt(NewArrowX[intCounter]);
					var writeAtY = parseInt(NewArrowY[intCounter]);				
				}
				target.strokeStyle = "rgb(17, 17, 17)";
				target.lineWidth = 1;
				target.beginPath();
				NewX[intCounter] = lineX;
				NewY[intCounter] = lineY;
				target.moveTo(lineX, lineY);
				
				gbArrDrawingImagesTemp[intCounter][7] = lineX;
				gbArrDrawingImagesTemp[intCounter][8] = lineY;
				
				target.lineTo(writeAtX, writeAtY);
				
				gbArrDrawingImagesTemp[intCounter][9] = writeAtX;
				gbArrDrawingImagesTemp[intCounter][10] = writeAtY;
				
				target.stroke();
				target.closePath();
				me.drawCircleArrow(writeAtX, writeAtY, target);
				
				gbArrDrawingImagesTemp[intCounter][11] = imagePosition[intCounter].textX;
				gbArrDrawingImagesTemp[intCounter][12] = imagePosition[intCounter].textY;
				gbArrDrawingImagesTemp[intCounter][13] = imagePosition[intCounter].textW;
				gbArrDrawingImagesTemp[intCounter][14] = imagePosition[intCounter].textH;
			}
			else if(imagePosition[intCounter].from == "downImage"){
				gbArrDrawingImagesTemp[intCounter][11] = imagePosition[intCounter].x;
				gbArrDrawingImagesTemp[intCounter][12] = imagePosition[intCounter].y;
				gbArrDrawingImagesTemp[intCounter][13] = imagePosition[intCounter].w;
				gbArrDrawingImagesTemp[intCounter][14] = imagePosition[intCounter].h;
				
				var lineX = imagePosition[intCounter].x + (imagePosition[intCounter].w/2);
				var lineY = imagePosition[intCounter].y;
				NewArrowX[intCounter] = lineX;
				NewArrowY[intCounter] = lineY;
				//alert(imagePosition[intCounter].x+"^^"+imagePosition[intCounter].y);
				var writeAtX = arrowX;
				var writeAtY = y + h;				
				target.strokeStyle = "rgb(17, 17, 17)";
				target.lineWidth = 1;
				target.beginPath();
				target.moveTo(lineX, lineY - 2);
				
				gbArrDrawingImagesTemp[intCounter][7] = lineX;
				gbArrDrawingImagesTemp[intCounter][8] = lineY - 2;
				
				if(typeof(NewX[intCounter]) == "undefined" && typeof(NewY[intCounter]) == "undefined"){
					target.lineTo(writeAtX, writeAtY);
					gbArrDrawingImagesTemp[intCounter][9] = writeAtX;
					gbArrDrawingImagesTemp[intCounter][10] = writeAtY;
				}
				else if(typeof(NewX[intCounter]) == "number" && typeof(NewY[intCounter]) == "number"){
					target.lineTo(NewX[intCounter], NewY[intCounter]);
					gbArrDrawingImagesTemp[intCounter][9] = writeAtX;
					gbArrDrawingImagesTemp[intCounter][10] = writeAtY;
				}
				target.stroke();
				target.closePath();
				me.drawCircleArrow(lineX, lineY - 2, target);
			}
			/*var textX = arrowX;
			var textY = arrowY;
			target.beginPath();
			target.font = "14px Arial";
			target.fillStyle = '#171717';
			target.fillText("Pucker", textX - 15, textY + 10);
			target.closePath();
			*/
		}, zTimeoutMM);						
		intImagesCounterTemp++;
	};
	
	this.makeDivNone = function(){
		document.getElementById("divEraserType"+me.classId).style.display = "none";
		document.getElementById("divPBType"+me.classId).style.display = "none";
		document.getElementById("divRotateAngle"+me.classId).style.display = "none";	
	};
	this.setLineType = function(type,obj, drawingEventType){
		var id = null;
		var lineType = 0.5;
		if(typeof(obj) == 'object'){
			id = obj.id;
		}
		else{
			id = obj+me.classId;
			obj = document.getElementById(id);
		}
		if(document.getElementById("spanPB1"+me.classId)){document.getElementById("spanPB1"+me.classId).className = "toolPBType toolPBType1";}	
		if(document.getElementById("spanPB2"+me.classId)){document.getElementById("spanPB2"+me.classId).className = "toolPBType toolPBType2";}	
		if(document.getElementById("spanPB3"+me.classId)){document.getElementById("spanPB3"+me.classId).className = "toolPBType toolPBType3";}	
		if(document.getElementById("spanPB4"+me.classId)){document.getElementById("spanPB4"+me.classId).className = "toolPBType toolPBType4";}
		if(obj && document.getElementById(obj.id)){document.getElementById(obj.id).className = obj.className + " eraserDivBorder";}
		if(drawingEventType == "funPencil"){
			lineType = parseFloat(type);	
		}
		else{
			lineType = 0.5;		
		}
		if(drawingEventType == "funBrush"){
			
			switch(type){
				case "0.15":
					lineType = 7;
				break;
				case '1.0':
					lineType = 15;
				break;
				case '2.5':
					lineType = 20;
				break;
				case '5.0':
					lineType = 25;
				break;
				default:
					lineType = 7;
				break;
			}
				
		}
		/*else if(drawingEventType == "funSparyColor"){
			switch(type){
				case "1":
					lineType = "1";
				break;
				case "5":
					lineType = "3";
				break;
				case "10":
					lineType = "6";
				break;
				case "15":
					lineType = "9";
				break;
				default:
					lineType = "12";
				break;
			}					
		}*/
		else if(drawingEventType == "funSparyColor"){
			switch(type){
				//case "1":
				case "0.15":
					lineType = 0.5;
				break;
				case '1.0':
					lineType = 0.8;
				break;
				case '2.5':
					lineType = 2;
				break;
				case '5.0':
					lineType = 2.5;
				break;
				default:
					lineType = 1;
				break;
			}					
		}
		else if((drawingEventType == "funDrawRect")){
		   lineType = parseFloat(type);
		}
		else if(drawingEventType == "funDrawRoundRect"){
		   lineType = parseFloat(type);
		}
		else if(drawingEventType == "funDrawEllipse"){
		   lineType = parseFloat(type);
		}
		else if(drawingEventType == "funDrawCircle"){
			lineType = parseFloat(type);
		}
		return lineType;
	};
	this.setErase = function (type,obj){
		var id;
		if(typeof(obj) == 'object'){
			id = obj.id;
		}
		else{
			id = obj+me.classId;
			obj = document.getElementById(id);
		}
		document.getElementById("divEraser16"+me.classId).className = "toolIcon16 toolEraser16";	
		document.getElementById("divEraser24"+me.classId).className = "toolIcon toolEraserMain";	
		document.getElementById("divEraser48"+me.classId).className = "toolIcon48 toolEraser48";	
		document.getElementById(obj.id).className = obj.className + " eraserDivBorder";
		return type;
	};	
	this.clearCanvas = function(){
		gbEventTypeTemp = "funClearCanvas";
		if(confirm("Are you sure to Erase/Clear whole Drawing!")){
			me.arrBlCanvasHaveDrwaing[0] = true;
			me.makeCanvasActive();
			me.arrBlCanvasHaveDrwaing[1] = false;
			this.eventType = null;
			this.lineType = 0.5;
			gbEventType = null;
			canvasObj = null, ctx = null;
			canvasTempObj = null, ctxTemp = null, lastPenPoint = null;
			currentColor = "#171717";
			touch = "";
			gbEventTypeTemp = null;
			mouseX = 0, mouseY = 0;
			intSelectStartX = 0, intSelectStartY = 0, intSelectWidth = 0, intSelectHieght = 0;
			intArrowStartX = 0, intArrowStartY = 0;
			mousePressed = false, gbBlDragStart = false, gbBlDragStartSelectProcess = false;	
			this.currentEraser = "16-16";	
			sprayWidth = 1;
			intSelectStartXTempCanvas = 0, intSelectStartYTempCanvas = 0;
			widthMXSX = 0, hieghtMYSY = 0;
			widthSelect = 0, hieghtSelect = 0;
			intLineStartX = 0, intLineStartY = 0;
			intArcStartX = 0, intArcStartY = 0;
			intEmtRectStartX = 0, intEmtRectStartY = 0;
			intRectWidth = 0, intRectHieght = 0;
			intEmtRoundRectStartX = 0, intEmtRoundRectStartY = 0, intEmtRoundRectEndX = 0, intEmtRoundRectEndY = 0;
			intRoundRectWidth = 0, intRoundRectHieght = 0;
			intFilledRectStartX = 0, intFilledRectStartY = 0, intFilledRectWidth = 0, intFilledRectHieght = 0;
			intFilledRoundRectStartX = 0, intFilledRoundRectStartY = 0, intFilledRoundRectEndX = 0, intFilledRoundRectEndY = 0;
			intEmtElpsStartX = 0, intEmtElpsStartY = 0;
			intEmtCirStartX = 0, intEmtCirStartY = 0;
			intFilledElpsStartX = 0, intFilledElpsStartY = 0;
			intFilledCirStartX = 0,	intFilledCirStartY = 0;
			arrSelRed = new Array();
			arrSelGreen = new Array();
			arrSelBlue = new Array();
			arrSelAlpha = new Array();
			arrEmtRect = new Array();
			outlineLayerData, colorLayerData;			
			pixelStack = new Array();
			newColorR = null, newColorG = null, newColorB = null, newColorA = null, clickedColorR = null, clickedColorG = null, clickedColorB = null, clickedColorA = null;
			for(var i in gbArrDrawingImages){			
				if(gbArrDrawingImages[i][6]){
					clearInterval(gbArrDrawingImages[i][6]);
				}
			}
			
			gbArrDrawingImages = new Array();
			intDrawingImagesCounter = 0;			
			gbArrDrawingImagesTemp = new Array();
			gbArrImagesDB = new Array();
			intImagesCounterTemp = 0;
			gbDragStart = false;
			
			for(var i in gbArrTextTemp){			
				if(gbArrTextTemp[i][1]){
					clearInterval(gbArrTextTemp[i][1]);
				}
			}
			
			gbArrTextMain = new Array();
			intTextCounterMain = 0;
			gbArrTextTemp = new Array();
			intTextCounterTemp = 0;
			gbArrTextDB = new Array();	
			
			canvasTempObj = document.getElementById("cCanvasTemp"+me.classId);
			ctxTemp = canvasTempObj.getContext("2d");alert(ctxTemp)
			this.clearTempCanvas("none");
			canvasObj = document.getElementById("cCanvas"+me.classId);
			ctx = canvasObj.getContext("2d");	                    
			this.clearMainCanvas();
			document.getElementById("spanColorCur"+me.classId).style.backgroundColor = currentColor;
			document.getElementById('divCanvas'+me.classId).className = "";
			document.getElementById('hidImageCss'+me.classId).value = "imgNoImage";	
			
			/*
			document.getElementById('hidRedPixel'+me.classId).value = "";
			document.getElementById('hidGreenPixel'+me.classId).value = "";
			document.getElementById('hidBluePixel'+me.classId).value = "";
			document.getElementById('hidAlphaPixel'+me.classId).value = "";
			*/
			
			document.getElementById('hidDrawingTestName'+me.classId).value = "";
			document.getElementById('hidDrawingTestId'+me.classId).value = "";
			document.getElementById('hidDrawingTestImageP'+me.classId).value = "";
			document.getElementById('hidImagesData'+me.classId).value = "";
			//checkCanvasWNL();
		}
	};
	this.clearTempCanvas = function(disp){// Clears Temp canvas
		disp = disp || "block";
		canvasTempObj.width = canvasTempObj.width;
		canvasTempObj.height = canvasTempObj.height;
		ctxTemp.clearRect(0,0,canvasTempObj.width,canvasTempObj.height);
		canvasTempObj.style.display = disp;
	};
	this.setCurrentColor = function(color){
		document.getElementById("spanColorCur"+me.classId).style.backgroundColor = color;
		currentColor = color;
	};
	this.setCanvasImage = function(type){
		var ask;
		//if(me.arrBlCanvasHaveDrwaing[0] == true){			
		if(warning_flg==0){
			ask = confirm("Drawing will clear. Would you like to precede chosen Drawing Image?");
			warning_flg=1;
		}
		else{
			ask = true
		}
		if(ask == true){			
			this.clearImagesFromCanvas(gbArrDrawingImages, "all");
			//alert(gbArrDrawingImages);
			gbArrDrawingImagesTemp = new Array();
			intImagesCounterTemp = 0;
			this.clearMainCanvas();
			gbArrDrawingImages = new Array();
			intDrawingImagesCounter = 0;			
			gbArrDrawingImagesTemp = new Array();
			gbArrImagesDB = new Array();
			intImagesCounterTemp = 0;
			gbDragStart = false;
			this.clearTextFromCanvas(gbArrTextMain);
			gbArrTextMain = new Array();
			intTextCounterMain = 0;
			gbArrTextTemp = new Array();
			intTextCounterTemp = 0;
			gbArrTextDB = new Array();
			
			me.arrBlCanvasHaveDrwaing[0] = false;
			me.arrBlCanvasHaveDrwaing[1] = false;
			
			
			
			
			var img = '';			
			switch(type){
				case "NoImage":
					img = "imgNoImage";							
				break;
				case "Face":
					img = "imgFaceCanvas";										
				break;
				case "Optical":
					img = "imgOpticalCanvas";					
				break;
				case "La":
					img = "imgLaCanvas";
				break;
				case "Ophtha":
					img = "imgOphthaCanvas";
				break;
				case "Pic-Con":
					img = "imgPicConCanvas";
				break;
				case "Gonio":
					img = "imgGonioCanvas";
				break;
				case "EOM":
					img = "imgEOMCanvas";
				break;
				case "Cornea R L":
				case "Cornea":
					img = "imgCorneaCanvas";
				break;
				case "Cornea Eye":
				case "CorneaEye":
					img = "imgCorneaEyeCanvas";
				break;
				case "EOM 2":
				case "Eom_2":
					img = "imgEOM2Canvas";
				break;
				case "Lids and Lacrimal":
				case "Lids_and_Lacrimal":
					img = "imgLidsAndLacrimalCanvas";
				break;
			}
			document.getElementById('divCanvas'+me.classId).className = "";
			document.getElementById('hidImageCss'+me.classId).value = "";
			
			document.getElementById('hidDrawingChangeYesNo'+me.classId).value = "yes";
			
			/*
			document.getElementById('hidRedPixel'+me.classId).value = "";
			document.getElementById('hidGreenPixel'+me.classId).value = "";
			document.getElementById('hidBluePixel'+me.classId).value = "";
			document.getElementById('hidAlphaPixel'+me.classId).value = "";
			*/
			
			document.getElementById('hidDrawingTestName'+me.classId).value = "";
			document.getElementById('hidDrawingTestId'+me.classId).value = "";
			document.getElementById('hidDrawingTestImageP'+me.classId).value = "";
			document.getElementById('hidImagesData'+me.classId).value = "";
			//document.getElementById('hidFileName').value = "";
			document.getElementById("divCanvas"+me.classId).style.backgroundImage = "";			
			document.getElementById('hidImageCss'+me.classId).value = img;
			document.getElementById('divCanvas'+me.classId).className = img;
			
			//imgLength
			strDataDefLength = this.strImgLen();
			//alert(strDataDefLength);
			//window.status=strDataDefLength;
			
			if(document.getElementById('hidImageCss'+me.classId).value == "imgLaCanvas"){
				this.setCDRation();
			}
		}
	};				
	
	this.doSaveCanvas = function(frmName, extraFun){
		//alert(typeof(canvasObj));
		var strData = canvasObj.toDataURL("image/png");
		
		//window.status=	strData.length+">"+strDataDefLength;		
		var strTestPath = document.getElementById("hidDrawingTestImageP"+me.classId).value;
		document.getElementById("hidCanvasImgData"+me.classId).value = (strData.length>strDataDefLength||strTestPath!="") ? strData : "" ;
		
		this.clearImagesFromCanvas(gbArrDrawingImages, "all");
		this.clearTextFromCanvas(gbArrTextDB);
		var strImageDB = gbArrImagesDB.join("~");
		//alert(strImageDB);
		//return;
		for(var i in gbArrTextDB){
			if(gbArrTextDB[i][2] == ""){
				delete gbArrTextDB[i];
				//gbArrTextDB.splice(i, 1);
			}
		}
		var arrTempTextDB = new Array();
		var a = 0;
		for(var i in gbArrTextDB){
			if(typeof(gbArrTextDB[i]) == "object"){					
				arrTempTextDB[a] = gbArrTextDB[i];
				a++;
			}
		}
		//alert("2");
		//alert(gbArrTextDB);
		//alert(arrTempTextDB);
		//return;
		
		var strTestDB = arrTempTextDB.join("~");
		var dragData = strImageDB+"$~$"+strTestDB;
		//alert(gbArrImagesDB.join("~"));
		var objFrm = document.getElementById(frmName);
		//alert(me.arrBlCanvasHaveDrwaing);
		//return;
		document.getElementById("hidDrawingChangeYesNo"+me.classId).value = "yes";
		if(me.arrBlCanvasHaveDrwaing[0] == true){			
			//alert(gbArrImagesDB.join("~"));
			//alert(document.getElementById("hidImagesData").value);
			//document.getElementById("img_load").style.display = "block";
			//document.getElementById("divTestImages").style.display = "block";	
			
			if(me.arrBlCanvasHaveDrwaing[1] == true){	
				document.getElementById("hidImagesData"+me.classId).value = dragData;
				
				var frame = ctx.getImageData(0, 0, canvasObj.width, canvasObj.height);
				var l = frame.data.length / 4;
				var arrRed = new Array();
				var arrGreen = new Array();
				var arrBlue = new Array();
				var arrAlpha = new Array();
				for (var i = 0; i < l; i++) {
					var r = frame.data[i * 4 + 0];
					var g = frame.data[i * 4 + 1];
					var b = frame.data[i * 4 + 2];
					var a = frame.data[i * 4 + 3];
					arrRed[i] = r;
					arrGreen[i] = g;
					arrBlue[i] = b;
					arrAlpha[i] = a;
				}
				//var strData = canvasObj.toDataURL("image/jpeg");
				
				/*
				document.getElementById("hidRedPixel"+me.classId).value = arrRed.join();
				document.getElementById("hidGreenPixel"+me.classId).value = arrGreen.join();
				document.getElementById("hidBluePixel"+me.classId).value = arrBlue.join();
				document.getElementById("hidAlphaPixel"+me.classId).value = arrAlpha.join();
				*/
				
				document.getElementById("hidDone"+me.classId).value = "DONE";
				objFrm.submit();
			}
			/*if(extraFun != ""){
				extraFun = extraFun.replace(/@/gi, "'");
				eval(extraFun);
			}
			if(""+typeof(objFrm.onsubmit)=="function"){objFrm.onsubmit();}
			objFrm.submit();*/
		}
		else{
			/*document.getElementById("hidDrawingChangeYesNo"+me.classId).value = "no";
			if(""+typeof(objFrm.onsubmit)=="function"){objFrm.onsubmit();}
			objFrm.submit();*/
		}
	};
	
	this.chkImageLoadCanvas = function(){
		var arrBlImage = new Array();
		for (var i in gbArrDrawingImages){
			var intCounter = 0;
			intCounter = gbArrDrawingImages[i][4];
			var imagePosition = new Array();
			imagePosition[intCounter] = gbArrDrawingImages[i][5].update();			
			if((imagePosition[intCounter].mainImageLoad == true) && (imagePosition[intCounter].textImageLoad == true)){
				arrBlImage[i] = true;
			}
		}
		
		if(arrBlImage.length == gbArrDrawingImages.length){
			for (var i in arrBlImage){
				if(arrBlImage[i] == false){
					return false;
				}
			}
			return true;			
		}
		return false;
	};
	
	this.saveCanvas = function(frmName, extraFun){
		if(me.arrBlCanvasHaveDrwaing[0] == true){
			extraFun = extraFun || "";
			//if(gbDragStart == true){
				/////////
				//alert(gbArrDrawingImagesTemp);
				if(gbArrDrawingImagesTemp.length > 0){
					this.drwaImagesOnMain(gbArrDrawingImagesTemp, "LOCAL");
				}
				//alert(gbArrImagesDB);
				if(gbArrTextTemp.length > 0){
					this.writeTextOnMain(gbArrTextTemp, "LOCAL");
				}
				/////////
				//return;
				//var tempInterval = setInterval(function() {
				(function() {	
					var ans = me.chkImageLoadCanvas();
					//alert(ans);
					if(ans == true){
						//clearInterval(tempInterval);
						gbArrTextTemp = new Array();
						//me.arrBlCanvasHaveDrwaing[0] = true;
						me.arrBlCanvasHaveDrwaing[1] = true;
						me.makeCanvasActive();
						gbDragStart = false;
						me.doSaveCanvas(frmName, extraFun);
					}
				//}, zTimeoutMM);
				})();	
			//}
		}
	};
	
	this.saveCanvasold = function(frmName, extraFun){
		extraFun = extraFun || "";
		
		if(gbDragStart == true){
			//alert(gbArrDrawingImagesTemp);
			this.drwaImagesOnMain(gbArrDrawingImagesTemp, "LOCAL");
			//alert(gbArrImagesDB);
			this.writeTextOnMain(gbArrTextTemp, "LOCAL");
			gbArrTextTemp = new Array();
			me.arrBlCanvasHaveDrwaing[0] = true;
			me.arrBlCanvasHaveDrwaing[1] = true;
			me.makeCanvasActive();
			gbDragStart = false;
		}
		
		var strData = canvasObj.toDataURL("image/png");
		var strTestPath = document.getElementById("hidDrawingTestImageP"+me.classId).value;	
		document.getElementById("hidCanvasImgData"+me.classId).value =  (strData.length>strDataDefLength||strTestPath!="") ? strData : "" ;
		
		this.clearImagesFromCanvas(gbArrDrawingImages, "all");
		this.clearTextFromCanvas(gbArrTextDB);
		var strImageDB = gbArrImagesDB.join("~");
		for(var i in gbArrTextDB){
			if(gbArrTextDB[i][2] == ""){
				delete gbArrTextDB[i];
				//gbArrTextDB.splice(i, 1);
			}
		}
		var arrTempTextDB = new Array();
		var a = 0;
		for(var i in gbArrTextDB){
			if(typeof(gbArrTextDB[i]) == "object"){					
				arrTempTextDB[a] = gbArrTextDB[i];
				a++;
			}
		}
		//alert(arrTempTextDB)
		//return;
		//alert(gbArrTextDB)
		var strTestDB = arrTempTextDB.join("~");
		var dragData = strImageDB+"$~$"+strTestDB;
		//alert(gbArrImagesDB.join("~"));
		var objFrm = document.getElementById(frmName);
		document.getElementById("hidDrawingChangeYesNo"+me.classId).value = "yes";
		if(me.arrBlCanvasHaveDrwaing[0] == true){				
			//alert(gbArrImagesDB.join("~"));
			//alert(document.getElementById("hidImagesData").value);
			document.getElementById("img_load").style.display = "block";
			document.getElementById("divTestImages").style.display = "block";	
			if(me.arrBlCanvasHaveDrwaing[1] == true){	
				document.getElementById("hidImagesData"+me.classId).value = dragData;
				var frame = ctx.getImageData(0, 0, canvasObj.width, canvasObj.height);
				var l = frame.data.length / 4;
				var arrRed = new Array();
				var arrGreen = new Array();
				var arrBlue = new Array();
				var arrAlpha = new Array();
				for (var i = 0; i < l; i++) {
					var r = frame.data[i * 4 + 0];
					var g = frame.data[i * 4 + 1];
					var b = frame.data[i * 4 + 2];
					var a = frame.data[i * 4 + 3];
					arrRed[i] = r;
					arrGreen[i] = g;
					arrBlue[i] = b;
					arrAlpha[i] = a;
				}
				//var strData = canvasObj.toDataURL("image/jpeg");
				/*
				document.getElementById("hidRedPixel"+me.classId).value = arrRed.join();
				document.getElementById("hidGreenPixel"+me.classId).value = arrGreen.join();
				document.getElementById("hidBluePixel"+me.classId).value = arrBlue.join();
				document.getElementById("hidAlphaPixel"+me.classId).value = arrAlpha.join();
				*/
			}
			if(extraFun != ""){
				extraFun = extraFun.replace(/@/gi, "'");
				eval(extraFun);
			}
			if(""+typeof(objFrm.onsubmit)=="function"){objFrm.onsubmit();}
			objFrm.submit();
		}
		else{
			document.getElementById("hidDrawingChangeYesNo"+me.classId).value = "no";
			if(""+typeof(objFrm.onsubmit)=="function"){objFrm.onsubmit();}
			objFrm.submit();
		}		
	};
	
	this.loadTestImage = function(imgPath, performedTestImagePath, img_id){
		var newImage = "url("+imgPath+")";
		var img = new Image();
  		img.src = imgPath;
		new_width = img.width;
		new_height = img.height;
		this.clearImagesFromCanvas(gbArrDrawingImages, "all");
		gbArrDrawingImagesTemp = new Array();
		intImagesCounterTemp = 0;
		this.clearMainCanvas();
		gbArrDrawingImages = new Array();
		intDrawingImagesCounter = 0;			
		gbArrDrawingImagesTemp = new Array();
		gbArrImagesDB = new Array();
		intImagesCounterTemp = 0;
		gbDragStart = false;
		gbArrTextMain = new Array();
		intTextCounterMain = 0;
		gbArrTextTemp = new Array();
		intTextCounterTemp = 0;
		gbArrTextDB = new Array();
		me.arrBlCanvasHaveDrwaing[0] = true;
		me.makeCanvasActive();
		me.arrBlCanvasHaveDrwaing[1] = true;
		document.getElementById("hidDrawingTestImageP"+me.classId).value = performedTestImagePath;
		document.getElementById("hidDrawingTestImageID"+me.classId).value = img_id;
		document.getElementById('hidImageCss'+me.classId).value = "imgDB";
		document.getElementById("divCanvas"+me.classId).style.backgroundImage = "";
		canvasObj.width = new_width;
		canvasObj.height = new_height;
		//document.getElementById("divCanvas"+me.classId).style.width =new_width+"px";
		//document.getElementById("divCanvas"+me.classId).style.height =new_height+"px";
		
		//document.getElementById("cCanvasTemp"+me.classId).style.width =new_width+"px";
		//document.getElementById("cCanvasTemp"+me.classId).style.height =new_height+"px";
		
		//document.getElementById("cCanvas"+me.classId).style.width =new_width+"px";
		//document.getElementById("cCanvas"+me.classId).style.height =new_height+"px";
		//document.getElementById("divCanvas").className = "imgLoad";
		document.getElementById("divCanvas"+me.classId).style.backgroundImage = newImage;
		document.getElementById("divCanvas"+me.classId).style.backgroundRepeat = "no-repeat";
		
		
		if( document.getElementById("divTestImages")) 
			document.getElementById("divTestImages").style.display = "none";
		
		//imgLength
		strDataDefLength = this.strImgLen();
		//alert(strDataDefLength);
		//window.status=strDataDefLength;
		
	}
	this.clearMainCanvas = function(){
		canvasObj.width = canvasObj.width;
		canvasObj.height = canvasObj.height;
		ctx.setTransform(1, 0, 0, 1, 0, 0);
		ctx.clearRect(0, 0, canvasObj.width, canvasObj.height);		
	}
	
	this.resetDrawing = function(resetFor){		
		me.arrBlCanvasHaveDrwaing[0] = true;
		me.makeCanvasActive();
		me.arrBlCanvasHaveDrwaing[1] = false;
		this.eventType = null, this.lineType = 0.5;
		this.currentEraser = "16-16";	
		gbEventType = null, gbLineType = 0.5;
		lastPenPoint = null;
		currentColor = "#171717";
		touch = "";
		gbEventTypeTemp = null;
		mouseX = 0, mouseY = 0;
		intSelectStartX = 0, intSelectStartY = 0, intSelectWidth = 0, intSelectHieght = 0;
		intArrowStartX = 0, intArrowStartY = 0;
		mousePressed = false, gbBlDragStart = false, gbBlDragStartSelectProcess = false;		
		//me.arrBlCanvasHaveDrwaing[0] = false
		//me.arrBlCanvasHaveDrwaing[1] = false
		sprayWidth = 1;
		intSelectStartXTempCanvas = 0, intSelectStartYTempCanvas = 0;
		widthMXSX = 0, hieghtMYSY = 0;
		widthSelect = 0, hieghtSelect = 0;
		intLineStartX = 0, intLineStartY = 0;
		intArcStartX = 0, intArcStartY = 0;
		intEmtRectStartX = 0, intEmtRectStartY = 0;
		intRectWidth = 0, intRectHieght = 0;
		intEmtRoundRectStartX = 0, intEmtRoundRectStartY = 0, intEmtRoundRectEndX = 0, intEmtRoundRectEndY = 0;
		intRoundRectWidth = 0, intRoundRectHieght = 0;
		intFilledRectStartX = 0, intFilledRectStartY = 0, intFilledRectWidth = 0, intFilledRectHieght = 0;
		intFilledRoundRectStartX = 0, intFilledRoundRectStartY = 0, intFilledRoundRectEndX = 0, intFilledRoundRectEndY = 0;
		intEmtElpsStartX = 0, intEmtElpsStartY = 0;
		intEmtCirStartX = 0, intEmtCirStartY = 0;
		intFilledElpsStartX = 0, intFilledElpsStartY = 0;
		intFilledCirStartX = 0,	intFilledCirStartY = 0;
		arrSelRed = new Array();
		arrSelGreen = new Array();
		arrSelBlue = new Array();
		arrSelAlpha = new Array();
		arrEmtRect = new Array();
		outlineLayerData = null, colorLayerData = null;
		pixelStack = new Array();
		newColorR = null, newColorG = null, newColorB = null, newColorA = null, clickedColorR = null, clickedColorG = null, clickedColorB = null, clickedColorA = null;
		for(var i in gbArrDrawingImages){
			if(gbArrDrawingImages[i][6]){
				clearInterval(gbArrDrawingImages[i][6]);
			}
		}
		
		gbArrDrawingImages = new Array();
		intDrawingImagesCounter = 0;			
		gbArrDrawingImagesTemp = new Array();
		gbArrImagesDB = new Array();
		intImagesCounterTemp = 0;
		gbDragStart = false;
		
		for(var i in gbArrTextTemp){			
			if(gbArrTextTemp[i][1]){
				clearInterval(gbArrTextTemp[i][1]);
			}
		}
		gbArrTextMain = new Array();
		intTextCounterMain = 0;
		gbArrTextTemp = new Array();
		intTextCounterTemp = 0;
		gbArrTextDB = new Array();
		this.clearTempCanvas("none");
		this.makeDivNone();
		this.clearMainCanvas();
		document.getElementById("spanImageEvent"+me.classId).className = "";
		document.getElementById("hidImageCss"+me.classId).value = "";
		/*
		document.getElementById("hidRedPixel"+me.classId).value = "";
		document.getElementById("hidGreenPixel"+me.classId).value = "";
		document.getElementById("hidBluePixel"+me.classId).value = "";
		document.getElementById("hidAlphaPixel"+me.classId).value = "";
		*/
		document.getElementById("hidDrawingTestName"+me.classId).value = "";
		document.getElementById("hidDrawingTestId"+me.classId).value = "";
		document.getElementById("hidDrawingTestImageP"+me.classId).value = "";
		document.getElementById("hidCanvasImgData"+me.classId).value = "";
		document.getElementById("hidImgDataFileName"+me.classId).value = "";
		document.getElementById('hidImagesData'+me.classId).value = "";
		var img = "";
		switch(resetFor){
			case "NoImage":
				img = "imgNoImage";							
			break;
			case "Face":
				img = "imgFaceCanvas";										
			break;
			case "Optical":
				img = "imgOpticalCanvas";					
			break;
			case "La":
				img = "imgLaCanvas";
			break;
			case "Ophtha":
				img = "imgOphthaCanvas";
			break;
			case "Pic-Con":
				img = "imgPicConCanvas";
			break;
			case "Gonio":
				img = "imgGonioCanvas";
			break;
			case "EOM":
				img = "imgEOMCanvas";
			break;
			case "Cornea":
				img = "imgCorneaCanvas";
			break;
			case "CorneaEye":
				img = "imgCorneaEyeCanvas";
			break;
			case "Eom_2":
				img = "imgEOM2Canvas";
			break;
			case "Lids_and_Lacrimal":
				img = "imgLidsAndLacrimalCanvas";
			break;
		}
		document.getElementById('hidImageCss'+me.classId).value = img;
		document.getElementById('divCanvas'+me.classId).className = img;
		//checkCanvasWNL();
	};
	this.chkDrawingExits = function(){
		if(me.arrBlCanvasHaveDrwaing[0] == true){
			return true;
		}
		else{
			return false;
		}
	};
		
	this.drawImages = function(imgType, intSmartTagDivId){		
		intSmartTagDivId = intSmartTagDivId || "0";		
		var x = mouseX-15 || 10;	//15 less : images drop right to pointer
		var y = mouseY-5 || 10;	//5 less : images drop right to pointer
		var w = 0, h = 0;
		var path = "", pathT = "";		
		this.makeDivNone();
		switch(imgType){
			case "Pucker":				
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolPucker";
				w = imgPukerT.width;
				h = imgPukerT.height;
				path = imgPuker.src;
				if(parseInt(intSmartTagDivId) > 0){
					pathT = imgPukerTSmartTag.src;
				}
				else{
					pathT = imgPukerT.src;
				}				
				me.addArrMenu("Pucker");
			break;
			case "Drusen":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolDrusen";
				w = imgDrusen.width;
				h = imgDrusen.height;
				path = imgDrusen.src;
				if(parseInt(intSmartTagDivId) > 0){
					pathT = imgDrusenTSmartTag.src;
				}
				else{
					pathT = imgDrusenT.src;
				}
				me.addArrMenu("Drusen");				
			break;
			case "Pallor":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolPollar";
				w = imgPallor.width;
				h = imgPallor.height;
				path = imgPallor.src;
				if(parseInt(intSmartTagDivId) > 0){
					pathT = imgPallorTSmartTag.src;
				}
				else{
					pathT = imgPallorT.src;
				}				
				me.addArrMenu("Pallor");
			break;
			case "RetinalHemorrhage":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolRetinal";
				w = imgRetinalHemorrhage.width;
				h = imgRetinalHemorrhage.height;
				path = imgRetinalHemorrhage.src;
				pathT = imgRetinalHemorrhageT.src;
				me.addArrMenu("Retinal Hemorrhage");
			break;
			case "LatticeDegeneration":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolLattice";
				w = imgLatticeDegeneration.width;
				h = imgLatticeDegeneration.height;
				path = imgLatticeDegeneration.src;
				pathT = imgLatticeDegenerationT.src;				
				me.addArrMenu("Lattice Degeneration");
			break;
			case "RetinalTear":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolRetinalTear";
				w = imgRetinalTear.width;
				h = imgRetinalTear.height;
				path = imgRetinalTear.src;
				pathT = imgRetinalTearT.src;				
				me.addArrMenu("Retinal Tear");
			break;
			case "HorseShoeTear":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolHorse";
				w = imgHorseShoeTear.width;
				h = imgHorseShoeTear.height;
				path = imgHorseShoeTear.src;
				pathT = imgHorseShoeTearT.src;				
				me.addArrMenu("Horse Shoe Tear");
			break;	
			case "DryDegeneration":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolDry";
				w = imgDryDegeneration.width;
				h = imgDryDegeneration.height;
				path = imgDryDegeneration.src;
				pathT = imgDryDegenerationT.src;				
				me.addArrMenu("Dry Degeneration");
			break;			
			case "ChoroidalNevus":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolChoroidal";
				w = imgChoroidalNevus.width;
				h = imgChoroidalNevus.height;
				path = imgChoroidalNevus.src;
				pathT = imgChoroidalNevusT.src;				
				me.addArrMenu("Choroidal Nevus");
			break;
			case "SPKMild":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolSPKMild";
				w = imgSPKMild.width;
				h = imgSPKMild.height;
				path = imgSPKMild.src;
				pathT = imgSPKMildT.src;				
				me.addArrMenu("SPK Mild");
			break;
			case "disciformScar":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolDisciformScar";
				w = imgDisciformScar.width;
				h = imgDisciformScar.height;
				path = imgDisciformScar.src;
				pathT = imgDisciformScarT.src;				
				me.addArrMenu("Disciform Scar");
			break;
			case "exudates":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolExudates";
				w = imgExudates.width;
				h = imgExudates.height;
				path = imgExudates.src;
				pathT = imgExudatesT.src;				
				me.addArrMenu("Exudates");
			break;
			case "hemorrhageRed":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolHemorrhage";
				w = imgHemorrhageRed.width;
				h = imgHemorrhageRed.height;
				path = imgHemorrhageRed.src;
				pathT = imgHemorrhageRedT.src;				
				me.addArrMenu("Hemorrhage");
			break;
			case "Neovascularization":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolNeovascularization";
				w = imgNeovascularization.width;
				h = imgNeovascularization.height;
				path = imgNeovascularization.src;
				pathT = imgNeovascularizationT.src;				
				me.addArrMenu("Neovascularization");
			break;
			case "Pannus":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolPannus";
				w = imgPannus.width;
				h = imgPannus.height;
				path = imgPannus.src;
				pathT = imgPannusT.src;				
				me.addArrMenu("Pannus");
			break;
			case "Pinguecula":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolPingnelcula";
				w = imgPingnelcula.width;
				h = imgPingnelcula.height;
				path = imgPingnelcula.src;
				pathT = imgPingnelculaT.src;				
				me.addArrMenu("Pinguecula");
			break;
			case "PRPTreatment":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolPRPTreatment";
				w = imgPRPTreatment.width;
				h = imgPRPTreatment.height;
				path = imgPRPTreatment.src;
				pathT = imgPRPTreatmentT.src;				
				me.addArrMenu("PRP Treatment(Laser)");
			break;
			case "Pterygium":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolPterygium";
				w = imgPterygium.width;
				h = imgPterygium.height;
				path = imgPterygium.src;
				pathT = imgPterygiumT.src;				
				me.addArrMenu("Pterygium");
			break;
			case "RedDot":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolRedDot";
				w = imgRedDot.width;
				h = imgRedDot.height;
				path = imgRedDot.src;
				pathT = imgRedDotT.src;				
				me.addArrMenu("Red Dot");
			break;
			case "RetinalHemorrhageDot":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolRetinalHemorrhageDot";
				w = imgRetinalHemorrhageDot.width;
				h = imgRetinalHemorrhageDot.height;
				path = imgRetinalHemorrhageDot.src;
				pathT = imgRetinalHemorrhageDotT.src;				
				me.addArrMenu("Retinal Hemorrhage Dot");
			break;
			case "Ulcer":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolUlcer";
				w = imgUlcer.width;
				h = imgUlcer.height;
				path = imgUlcer.src;
				pathT = imgUlcerT.src;				
				me.addArrMenu("Ulcer");
			break;
			case "RetinalIrregularHemorrhage":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolRetinalIrregularHemorrhage";
				w = imgRetinalIrregularHemorrhage.width;
				h = imgRetinalIrregularHemorrhage.height;
				path = imgRetinalIrregularHemorrhage.src;
				pathT = imgRetinalIrregularHemorrhageT.src;				
				me.addArrMenu("Retinal Irregular Hemorrhage");
			break;
			
			/*new icons*/
			case "CME":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolCME";
				w = imgCME.width;
				h = imgCME.height;
				path = imgCME.src;
				pathT = imgCMET.src;
				me.addArrMenu("CME");
			break;		
			
			case "DrusenMild":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolDruMild";
				w = imgDruMild.width;
				h = imgDruMild.height;
				path = imgDruMild.src;
				pathT = imgDruMildT.src;
				me.addArrMenu("Drusen Mild");
			break;		
			
			case "DrusenModerate":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolDruMod";
				w = imgDruMod.width;
				h = imgDruMod.height;
				path = imgDruMod.src;
				pathT = imgDruModT.src;
				me.addArrMenu("Drusen Moderate");
			break;		
			
			case "Floaters":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolFloaters";
				w = imgFloaters.width;
				h = imgFloaters.height;
				path = imgFloaters.src;
				pathT = imgFloatersT.src;
				me.addArrMenu("Floaters");
			break;		
			
			case "FocalTreatment":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolFocalTreatment";
				w = imgFocalTreatment.width;
				h = imgFocalTreatment.height;
				path = imgFocalTreatment.src;
				pathT = imgFocalTreatmentT.src;
				me.addArrMenu("Focal Treatment");
			break;		
			
			/*case "Lattice":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolLattice";
				w = imgLattice.width;
				h = imgLattice.height;
				path = imgLattice.src;
				pathT = imgLatticeT.src;
				me.addArrMenu("Lattice");
			break;		
			*/
			
			case "MA":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolMA";
				w = imgMA.width;
				h = imgMA.height;
				path = imgMA.src;
				pathT = imgMAT.src;
				me.addArrMenu("MA");
			break;		
			
			case "PVD":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolPVD";
				w = imgPVD.width;
				h = imgPVD.height;
				path = imgPVD.src;
				pathT = imgPVDT.src;
				me.addArrMenu("PVD");
			break;
			
			case "RPEChanges":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolRPEChanges";
				w = imgRPEChanges.width;
				h = imgRPEChanges.height;
				path = imgRPEChanges.src;
				pathT = imgRPEChangesT.src;
				me.addArrMenu("RPE Changes");
			break;
			
			case "ERM":
				document.getElementById("spanImageEvent"+me.classId).className = "toolIcon toolERM";
				w = imgERM.width;
				h = imgERM.height;
				path = imgERM.src;
				pathT = imgERMT.src;
				me.addArrMenu("ERM");
			break;
			
			
			
			/*new icons*/	
			
		}
		
		
		for(var i in gbArrDrawingImages){		
			var left = gbArrDrawingImages[i][0];
			var right = gbArrDrawingImages[i][0] + gbArrDrawingImages[i][2];
			var top = gbArrDrawingImages[i][1];
			var bottom = gbArrDrawingImages[i][1] + gbArrDrawingImages[i][3];
			if ((x <= right) && (x >= left) && (y <= bottom) && (y >= top)){
				x = x + w;
				y = y + h;
			}
		}
		//alert(x+"--"+y);
		//intDrawingImagesCounter++;
		var intCounter = intDrawingImagesCounter;	
		gbArrDrawingImages[intCounter] = new Array();
		gbArrDrawingImages[intCounter][0] = x;
		gbArrDrawingImages[intCounter][1] = y;
		gbArrDrawingImages[intCounter][2] = w;
		gbArrDrawingImages[intCounter][3] = h;
		gbArrDrawingImages[intCounter][4] = intDrawingImagesCounter;				
		
		var aarMainImage = path.split("/");
		var arrImagesDBChild = new Array();
		arrImagesDBChild[0] = aarMainImage[aarMainImage.length - 1];
		arrImagesDBChild[1] = x;
		arrImagesDBChild[2] = y;
		arrImagesDBChild[3] = w;
		arrImagesDBChild[4] = h;
		
		var textX = x;
		var textY = y + 105;
		gbArrDrawingImages[intCounter][5] = new DragImage(path, x, y, ctx, false, textX, textY, pathT);
		
		gbArrDrawingImages[intCounter][6] = setTimeout(function() {
			var arrowX = x + (w/2);
			var arrowY = y + 100;			
			var imagePosition = gbArrDrawingImages[intCounter][5].update();			
			gbArrDrawingImages[intCounter][0] = imagePosition.x;
			gbArrDrawingImages[intCounter][1] = imagePosition.y;
			gbArrDrawingImages[intCounter][2] = imagePosition.w;
			gbArrDrawingImages[intCounter][3] = imagePosition.h;
			
			var lineX = imagePosition.x + (imagePosition.w/2);
			var lineY = imagePosition.y + imagePosition.h;
			var writeAtX = arrowX;
			var writeAtY = arrowY;
			
			
			
			/*//t
			ctx.strokeStyle = "rgb(17, 17, 17)";
			ctx.lineWidth = 1;
			ctx.beginPath();
			ctx.moveTo(lineX, lineY);
			*/
			gbArrDrawingImages[intCounter][7] = lineX;
			gbArrDrawingImages[intCounter][8] = lineY;

			//tctx.lineTo(writeAtX, writeAtY);
			gbArrDrawingImages[intCounter][9] = writeAtX;
			gbArrDrawingImages[intCounter][10] = writeAtY;
			//tctx.stroke();
			//tctx.closePath();
			//tme.drawCircleArrow(arrowX, arrowY, ctx);
			/*gbArrDrawingImages[intCounter][11] = textX;
			gbArrDrawingImages[intCounter][12] = textY;
			gbArrDrawingImages[intCounter][13] = imgPukerT.width;
			gbArrDrawingImages[intCounter][14] = imgPukerT.height;
			*/
			
			gbArrDrawingImages[intCounter][11] = imagePosition.textX;
			gbArrDrawingImages[intCounter][12] = imagePosition.textY;
			gbArrDrawingImages[intCounter][13] = imagePosition.textW;
			gbArrDrawingImages[intCounter][14] = imagePosition.textH;
			
			arrImagesDBChild[5] = lineX;
			arrImagesDBChild[6] = lineY;
			arrImagesDBChild[7] = writeAtX;
			arrImagesDBChild[8] = writeAtY;
			arrImagesDBChild[9] = imagePosition.textX;
			arrImagesDBChild[10] = imagePosition.textY;
			arrImagesDBChild[11] = imagePosition.textW;
			arrImagesDBChild[12] = imagePosition.textH;
			//alert(arrImagesDBChild[5]);
			
			/*var textX = arrowX;
			var textY = arrowY;
			ctx.beginPath();
			ctx.font = "14px Arial";
			ctx.fillStyle = '#171717';
			ctx.fillText("Pucker", textX - 15, textY + 10);
			ctx.closePath();
			*/			
		}, zTimeoutMM);	
		
		
		gbArrDrawingImages[intCounter][15] = path;
		gbArrDrawingImages[intCounter][16] = pathT;
		gbArrDrawingImages[intCounter][17] = "";//SmartTag string Index
		gbArrDrawingImages[intCounter][18] = "";//SmartTag X Index
		gbArrDrawingImages[intCounter][19] = "";//SmartTag Y Index
		gbArrDrawingImages[intCounter][20] = "";//SmartTag Check box id
		gbArrDrawingImages[intCounter][21] = "";//SmartTag Master Id
		gbArrDrawingImages[intCounter][22] = "";//SmartTag Child Id
		gbArrDrawingImages[intCounter][23] = "";//Clock Wise Angle
		gbArrDrawingImages[intCounter][24] = "";//Anti Clock Wise Angle
		
		
		////////////////
		//alert(gbArrImagesDB);
		arrImagesDBChild[13] = aarMainImage[aarMainImage.length - 1];
		var aarTextImage = pathT.split("/");
		arrImagesDBChild[14] = aarTextImage[aarTextImage.length - 1];
		arrImagesDBChild[15] = "";//SmartTag string Index
		arrImagesDBChild[16] = "";//SmartTag X Index
		arrImagesDBChild[17] = "";//SmartTag Y Index
		arrImagesDBChild[18] = "";//SmartTag Check box id
		arrImagesDBChild[19] = "";//Smart Tag Master ID
		arrImagesDBChild[20] = "";//Smart Tag Child ID
		arrImagesDBChild[21] = "";//Clock Wise Angle
		arrImagesDBChild[22] = "";//Anti Clock Wise Angle
		gbArrImagesDB.push(arrImagesDBChild);
		//alert(gbArrImagesDB);
		////////////////		
		
		intDrawingImagesCounter++;
		me.arrBlCanvasHaveDrwaing[0] = true;
		me.makeCanvasActive();
		me.arrBlCanvasHaveDrwaing[1] = true;
	};
	
	function RotateImage(src, x, y, target, blOnTemp, textX, textY, pathT, rotateType, floatAngle) {
		//alert(x+"~~"+y);			
		var that = this;
		var startX = 0, startY = 0;
		var startXT = 0, startYT = 0;
		var drag = false;
		var dragT = false;
		this.x = x;
		this.y = y;
		this.textX = textX;
		this.textY = textY;
		//alert(that.x+"``"+that.y);			
		var img = new Image();			
		img.src = src;
		var imgT = new Image();			
		imgT.src = pathT;
		var TO_RADIANS = Math.PI/180;
		this.update = function() {
			//alert(that.x+"``"+that.y+"``"+'this.update');
			//alert('this.update');			
			if(blOnTemp == true){
				me.clearTempCanvas();
			}
			//alert(imgT.src+"--"+that.textX+"--"+that.textY+"--"+imgT.width+"--"+imgT.height+"=="+target);
			if((img.complete == true) && (imgT.complete == true)){
				if(rotateType == 1){
					var angle = floatAngle;
				}
				else if(rotateType == 2){
					var angle = -floatAngle;
				}
				target.save();
				target.setTransform(1,0,0,1,0,0);
				target.translate(that.x, that.y);
				target.translate(img.width/2, img.height/2);
				target.rotate(angle * TO_RADIANS);
				target.drawImage(img, -(img.width/2), -(img.height/2), img.width, img.height);
				target.restore();
				/***
				target.drawImage(imgT, that.textX, that.textY, imgT.width, imgT.height);
				***/
			}
			return({x: that.x, y: that.y, w: img.width, h: img.height, textX: that.textX, textY: that.textY, textW: imgT.width, textH: imgT.height, mainImageLoad: img.complete , textImageLoad: imgT.complete});
		}
	}
	
	this.drwaRotateImageMain = function(path, x, y, moveToX, moveToY, lineToX, lineToY, textX, textY, target, pathT, strSmartTag, intSmartTagXPos, intSmartTagYPos, rotateType, floatAngle){
		var imagePosition = new Object();
		var objDragImage = new RotateImage(path, x, y, target, false, textX, textY, pathT, rotateType, floatAngle);
		var intIntreval = setInterval(function() {
			imagePosition = objDragImage.update();
			if((imagePosition.mainImageLoad == true) && (imagePosition.textImageLoad == true)){
				clearInterval(intIntreval);
			}
			target.strokeStyle = "rgb(17, 17, 17)";
			target.lineWidth = 1;
			target.beginPath();			
			target.moveTo(moveToX, moveToY);
			target.lineTo(lineToX, lineToY);
			target.stroke();
			target.closePath();
			me.drawCircleArrow(lineToX, lineToY, target);
			if(strSmartTag != null){
				me.writeSmartTag(target, strSmartTag, intSmartTagXPos, intSmartTagYPos);
			}
		}, zTimeoutMM);
	};
	this.drwaImagesMainRotate = function(arrImages, counter, rotateType, floatAngle){
		//canvasTempObj.style.display = "block";
		var i = counter;
		if(typeof(arrImages[i])=="undefined"){return;}
		var x = parseInt(arrImages[i][0]);
		var y = parseInt(arrImages[i][1]);
		var w = parseInt(arrImages[i][2]);
		var h = parseInt(arrImages[i][3]);
		var intNumber = parseInt(arrImages[i][4]);
		var obj = arrImages[i][5];
		var interval = parseInt(arrImages[i][6]);
		var moveToX = parseInt(arrImages[i][7]);
		var moveToY = parseInt(arrImages[i][8]);
		var lineToX = parseInt(arrImages[i][9]);
		var lineToY = parseInt(arrImages[i][10]);
		var textX = parseInt(arrImages[i][11]);
		var textY = parseInt(arrImages[i][12]);
		var textW = parseInt(arrImages[i][13]);
		var textH = parseInt(arrImages[i][14]);
		var path = arrImages[i][15];
		var pathT = arrImages[i][16];
		var strSmartTag = intSmartTagXPos = intSmartTagYPos = strSmartTagID = strSmartTagMasterId = strSmartTagChildId = null;
		if(arrImages[i][17]){
			strSmartTag = arrImages[i][17];
			intSmartTagXPos = parseInt(arrImages[i][18]);
			intSmartTagYPos = parseInt(arrImages[i][19]);
			strSmartTagID = arrImages[i][20];
			strSmartTagMasterId = arrImages[i][21];
			strSmartTagChildId = arrImages[i][22];
		}
		//me.drwaRotateImageMain(path, x, y, moveToX, moveToY, lineToX, lineToY, textX, textY, ctx, pathT, strSmartTag, intSmartTagXPos, intSmartTagYPos, rotateType, floatAngle);
		var target = ctx;
		var imagePosition = new Object();
		var objDragImage = new RotateImage(path, x, y, target, false, textX, textY, pathT, rotateType, floatAngle);
		var intIntreval = setInterval(function() {
			imagePosition = objDragImage.update();
			if((imagePosition.mainImageLoad == true) && (imagePosition.textImageLoad == true)){
				clearInterval(intIntreval);
			}
			/***
			target.strokeStyle = "rgb(17, 17, 17)";
			target.lineWidth = 1;
			target.beginPath();			
			target.moveTo(moveToX, moveToY);
			target.lineTo(lineToX, lineToY);
			target.stroke();
			target.closePath();
			me.drawCircleArrow(lineToX, lineToY, target);			
			if(strSmartTag != null){
				me.writeSmartTag(target, strSmartTag, intSmartTagXPos, intSmartTagYPos);
			}
			***/
		}, zTimeoutMM);
	};
	this.drawRotatedImage = function(rotateType, floatAngle){
		
		
		
		
		
		/*
		var TO_RADIANS = Math.PI/180;
		
		//alert(gbIntCurrentArrDrawingImagesIndex+ " \n\n " +gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex]);
		if(gbIntCurrentArrDrawingImagesIndex==null)return;
		
		this.clearImagesFromCanvas(gbArrDrawingImages, gbIntCurrentArrDrawingImagesIndex, floatAngle);		
		this.drwaImagesMainRotate(gbArrDrawingImages, gbIntCurrentArrDrawingImagesIndex, rotateType, floatAngle);
		if(rotateType == 1){
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][23] = floatAngle;
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][24] = "";
			
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][21] = floatAngle;
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][22] = "";
			me.arrBlCanvasHaveDrwaing[0] = true;
			me.arrBlCanvasHaveDrwaing[1] = true;
			
			document.getElementById("spRCW"+me.classId).className = "toolIcon16 rotateClockWiseDB16";
			document.getElementById("spRCCW"+me.classId).className = "toolIcon16 rotateCounterClockWise16";
		}
		else if(rotateType == 2){
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][23] = "";
			gbArrDrawingImages[gbIntCurrentArrDrawingImagesIndex][24] = floatAngle;
			
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][21] = "";
			gbArrImagesDB[gbIntCurrentArrDrawingImagesDBIndex][22] = floatAngle;
			me.arrBlCanvasHaveDrwaing[0] = true;
			me.arrBlCanvasHaveDrwaing[1] = true;
			
			document.getElementById("spRCCW"+me.classId).className = "toolIcon16 rotateCounterClockWiseDB16";
			document.getElementById("spRCW"+me.classId).className = "toolIcon16 rotateClockWise16";
		}
		*/
	};
	
	this.clear = function(left, top, width, height) {
		ctx.setTransform(1, 0, 0, 1, 0, 0);
		ctx.clearRect(left, top, width, height);				
	};
	this.clearTemp = function(left, top, width, height) {
		//ctxTemp.clearRect(left, top, width, height);
		canvasTempObj.width = canvasTempObj.width;
		canvasTempObj.height = canvasTempObj.height;			
	};
	
	this.disableTools = function(op){
		/*var arrElementToll = document.getElementsByTagName ('span');		
		for (var i = 0; i < arrElementToll.length; i++){
			var strClass = arrElementToll[i].className;
			if(strClass.search("toolIcon") > 0){
				if(op == "D"){				
					arrElementToll[i].disabled = true;
					//alert(arrElementToll[i].disabled);
				}
				else if(op == "E"){
					arrElementToll[i].disabled = false;
				}
			}
		}*/
		document.getElementById("tool1"+me.classId).disabled = true;
	};
	
	this.checkCanvasWNL = function(cntr){	
		
		if(typeof(cntr)!="undefined"&&cntr==0){
			document.getElementById("hidCanvasWNL").value = "";	
		}
		
		//if Changed, alert
		if(typeof(newET_drawingChanged) != "undefined" && me.arrBlCanvasHaveDrwaing[0] == true){
			newET_drawingChanged();
		}
		
		if(me.arrBlCanvasHaveDrwaing[1] == true){
			document.getElementById("hidCanvasWNL").value += "no";
		}
		else{
			document.getElementById("hidCanvasWNL").value += "yes";
		}
		//alert(document.getElementById("hidCanvasWNL").value)
	};
	
	
	
	this.next_tool = function(){
		var arrTemp = new Array();
		arrTemp = strCurrentTool.split("_");
		var intNextTool = parseInt(arrTemp[1]) + 1;
		var strNextTool = "tool_" + intNextTool + "_" + me.classId;
		
		
		//alert(strCurrentTool+" - "+strNextTool);
		
		
		if(document.getElementById(strNextTool)){
			document.getElementById("toolTop"+me.classId).innerHTML = "";
			document.getElementById("toolTop"+me.classId).innerHTML = document.getElementById(strNextTool).innerHTML;
			strCurrentTool = strNextTool;
		}
		else{
			document.getElementById("toolTop"+me.classId).innerHTML = "";
			document.getElementById("toolTop"+me.classId).innerHTML = document.getElementById("tool_1" + "_" + me.classId).innerHTML;
			strCurrentTool = arrToolDiv[0];
		}
	};
	
	this.previous_tool = function(){
		var arrTemp = new Array();
		arrTemp = strCurrentTool.split("_");
		var intPreTool = parseInt(arrTemp[1]) - 1;
		var strPreTool = "tool_" + intPreTool + "_" + me.classId;
		if(document.getElementById(strPreTool)){
			document.getElementById("toolTop"+me.classId).innerHTML = "";
			document.getElementById("toolTop"+me.classId).innerHTML = document.getElementById(strPreTool).innerHTML;
			strCurrentTool = strPreTool;
		}
		else{
			var lastEl = arrToolDiv[arrToolDiv.length - 1];
			document.getElementById("toolTop"+me.classId).innerHTML = "";
			document.getElementById("toolTop"+me.classId).innerHTML = document.getElementById(lastEl).innerHTML;
			strCurrentTool = lastEl;
		}
	}
	
	function DragText(text, x, y, trg){
		var that = this;
		var startX = 0, startY = 0;
		var drag = false;
		this.text = text;
		this.x = x;
		this.y = y;
		//alert('DragText');
		this.update = function() {
			//alert('this.update');
			if (mousePressed){
				//alert('mousePressed');				
				var textLength = trg.measureText(that.text).width;
				//var textLength = (that.text.length * 14)/2;
				if(isIPad == true){
					var left = that.x - textLength;
					var right = that.x + textLength;
					var top = that.y - 14;
					var bottom = that.y + 14;
					if (!drag){
						startX = mouseX - that.x;
						startY = mouseY - that.y;					  
					}					
					if ((mouseX < right) && (mouseX > left) && (mouseY < bottom) && (mouseY > top)){					
						drag = true;
					}						
				}
				else{
					var left = parseInt(that.x);					
					var right = parseInt(that.x) + parseInt(textLength);
					var top = parseInt(that.y - 10);
					var bottom = parseInt(that.y) + 14;
					if (!drag){
						startX = mouseX - that.x;
						startY = mouseY - that.y;					  
					}
					//document.getElementById("el_la_od").value = mouseX+"<"+right+"--"+mouseX+">"+left+"--"+mouseY+"<"+bottom+"--"+mouseY+">"+top;
					if ((mouseX < right) && (mouseX > left) && (mouseY < bottom) && (mouseY > top)){						
						drag = true;						
					}					
				}
			}
			else{
			   drag = false;
			}
			if (drag){
				that.x = mouseX - startX;
				that.y = mouseY - startY;					
			}
			if(drag){
				me.clearTempCanvas();				
			}
			trg.beginPath();
			trg.font = "14px Arial";
			trg.fillStyle = '#171717';
			//alert(text+"--"+that.x+"--"+that.y)
			trg.fillText(text, that.x, that.y);
			trg.closePath();
			return({x: that.x, y: that.y, text: text});	
		}
	}
	
	function DragImage(src, x, y, target, blOnTemp, textX, textY, pathT, flotRotateAngleClockWise, floatRotateAngleAntiClockWise) {
		textX = textX || 0;
		textY = textY || 0;
		
		pathT = pathT || "";
		blOnTemp = blOnTemp || false;
		flotRotateAngleClockWise = flotRotateAngleClockWise || "";
		floatRotateAngleAntiClockWise = floatRotateAngleAntiClockWise || "";
		//alert(x+"~~"+y);			
		var that = this;
		var startX = 0, startY = 0;
		var startXT = 0, startYT = 0;
		var drag = false;
		var dragT = false;
		this.x = x;
		this.y = y;
		this.textX = textX;
		this.textY = textY;
		//alert(that.x+"``"+that.y);			
		var img = new Image();			
		img.src = src;
		
		var imgT = new Image();			
		imgT.src = pathT;		
		
		//alert("2");
		var TO_RADIANS = Math.PI/180;
		
		
		this.update = function() {
			//alert(that.x+"``"+that.y+"``"+'this.update');
			//alert('this.update');
			if (mousePressed){
				//alert('mousePressed');
				if(isIPad == true){
					var left = that.x - img.width;					
					var right = that.x + img.width;
					var top = that.y - img.height;
					var bottom = that.y + img.height;
					if (!drag){
						startX = mouseX - that.x;
						startY = mouseY - that.y;					  
					}					
					if ((mouseX < right) && (mouseX > left) && (mouseY < bottom) && (mouseY > top)){					
						drag = true;
					}	
					
					var leftT = that.textX - imgT.width;				
					var rightT = that.textX + imgT.width;
					var topT = that.textY - imgT.height;
					var bottomT = that.textY + imgT.height;
					if (!dragT){
						startXT = mouseX - that.textX;
						startYT = mouseY - that.textY;					  
					}
					if ((mouseX < rightT) && (mouseX > leftT) && (mouseY < bottomT) && (mouseY > topT)){												
						dragT = true;						
					}	
				}
				else{
					var left = parseInt(that.x);					
					var right = parseInt(that.x) + parseInt(img.width);
					var top = parseInt(that.y);
					var bottom = parseInt(that.y) + parseInt(img.height);
					if (!drag){
						startX = mouseX - that.x;
						startY = mouseY - that.y;					  
					}
					if ((mouseX < right) && (mouseX > left) && (mouseY < bottom) && (mouseY > top)){						
						drag = true;						
					}
					
					var leftT = parseInt(that.textX);					
					var rightT = parseInt(that.textX) + parseInt(imgT.width);
					var topT = parseInt(that.textY);
					var bottomT = parseInt(that.textY) + parseInt(imgT.height);
					if (!dragT){
						startXT = mouseX - that.textX;
						startYT = mouseY - that.textY;					  
					}
					if ((mouseX < rightT) && (mouseX > leftT) && (mouseY < bottomT) && (mouseY > topT)){												
						dragT = true;						
					}
				}
				if(isIPad == true){
					//me.clear(left, top, right, bottom);
					//me.clear(left, top, img.width, img.height);				
				}
				else{
					if(blOnTemp == false){
						me.clear(left, top, img.width, img.height);
						me.clear(leftT, topT, imgT.width, imgT.height);
					}
					/*var lineX = (left + (img.width/2)) - 1;
					var lineY = top + img.height;
					var writeAtX = lineX;
					var writeAtY = top + 200;
					me.clear(lineX, lineY, 2, writeAtY);					
					me.clear(lineX - 4, writeAtY - 4, 8, 8);
					me.clear(left, writeAtY, 6*12, 12);
					*/
				}
			}
			else{
				drag = false;
				dragT = false;
			}
			if (drag){
				that.x = mouseX - startX;
				that.y = mouseY - startY;					
			}
			else if (dragT){
				that.textX = mouseX - startXT;
				that.textY = mouseY - startYT;	
				//console.log(imgT.src+"--"+that.textX+"--"+that.textY+"--"+imgT.width+"--"+imgT.height+"=="+target);				
			}
			if((blOnTemp == true) && ((drag) || (dragT))){
				me.clearTempCanvas();
				/*me.clearTemp(left, top, img.width, img.height);
				var lineX = left;
				var lineY = top;
				var writeAtX = lineX;
				var writeAtY = top + 250;
				me.clearTemp(lineX, lineY, img.width, writeAtY);					
				me.clearTemp(lineX - 4, writeAtY - 4, 8, 8);
				me.clearTemp(left, writeAtY, 6*12, 12);				
				*/
			}
			//console.log(img.src+"--"+that.x+"--"+that.y+"--"+img.width+"--"+img.height+"=="+target+imgT.src+"--"+that.textX+"--"+that.textY+"--"+imgT.width+"--"+imgT.height+"=="+target);
			//alert(imgT.src+"--"+that.textX+"--"+that.textY+"--"+imgT.width+"--"+imgT.height+"=="+target);
			if((img.complete == true) && (imgT.complete == true)){
				if(flotRotateAngleClockWise != "" || floatRotateAngleAntiClockWise != ""){
					if(flotRotateAngleClockWise != ""){
						var angle = flotRotateAngleClockWise;
					}
					else if(floatRotateAngleAntiClockWise != ""){
						var angle = "-" + floatRotateAngleAntiClockWise;
					}
					target.save();
					target.translate(that.x, that.y);
					target.translate(img.width/2, img.height/2);
					target.rotate(angle * TO_RADIANS);
					target.drawImage(img, -(img.width/2), -(img.height/2), img.width, img.height);
					target.restore();
				}
				else{					
					target.drawImage(img, that.x, that.y, img.width, img.height);
				}
				//text
				//target.drawImage(imgT, that.textX, that.textY, imgT.width, imgT.height);
				//
			}
			if(drag || dragT){
				if(drag){
					return({x: that.x, y: that.y, w: img.width, h: img.height, from: "upImage", textX: that.textX, textY: that.textY, textW: imgT.width, textH: imgT.height});
				}
				else if(dragT){
					return({x: that.textX, y: that.textY, w: imgT.width, h: imgT.height, from: "downImage"});
				}	
			}
			else{
				return({x: that.x, y: that.y, w: img.width, h: img.height, from: "upImage", textX: that.textX, textY: that.textY, textW: imgT.width, textH: imgT.height, mainImageLoad: img.complete , textImageLoad: imgT.complete});
			}
		}
		
	}
}
var objCLSDrawing = new Array();
//var arrToolDiv = new Array();
//var strCurrentTool = "tool_1";
function setEvent(eventType, img, intSmartTagDivId, canvasId){
	img = img || "";
	intSmartTagDivId = intSmartTagDivId || "0";
	canvasId = canvasId || "";
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	//objCLSDrawing[0].setEvent(eventType, img, intSmartTagDivId);
	//objCLSDrawing[1].setEvent(eventType, img, intSmartTagDivId);
	if(canvasId == ""){
		for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
			objCLSDrawing[intCounter].setEvent(eventType, img, intSmartTagDivId);
		}
	}
	else{
		
		objCLSDrawing[canvasId].setEvent(eventType, img, intSmartTagDivId);
		if(typeof(checkCanvasWNL)!="undefined"){checkCanvasWNL();}
		if(typeof(checkwnls)!="undefined"){checkwnls();}		
	}
}
function drawingInit(){
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}	
	//arrToolDiv = Array("tool_1", "tool_2");
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
		objCLSDrawing[intCounter] = new CLSDrawing(intCounter);
		objCLSDrawing[intCounter].drawingInit();
	}
	//objCLSDrawing[0] = new CLSDrawing(0);
	//objCLSDrawing[1] = new CLSDrawing(1);
	//setCD();
	//objCLSDrawing[0].drawingInit();
	//objCLSDrawing[1].drawingInit();
	
	setSmartTagDivID();
	var w = parseInt(window.innerWidth);
    var h = parseInt(window.innerHeight);
	if(document.getElementById("divTestImages")){document.getElementById("divTestImages").style.width = w+"px";}
	if(document.getElementById("divTestImages")){document.getElementById("divTestImages").style.height = h+"px";}
	if(document.getElementById("divTestImagesMain")){
	document.getElementById("divTestImagesMain").style.width = (w - 50)+"px";
	document.getElementById("divTestImagesMain").style.height = (h - 100)+"px";
	}
	//document.getElementById("divTestImages").style.display = "block";
	//document.getElementById("divTestImagesMain").style.display = "block";	
}
function setCD(){
	if(typeof(objCLSDrawing) == "undefined"){
		drawingInit();
	}
	if((document.getElementById("hidCDRationOD")) && (document.getElementById("hidCDRationOS"))){
		//objCLSDrawing.strCDRatioOD = document.getElementById("hidCDRationOD").value;
		//objCLSDrawing.strCDRatioOS = document.getElementById("hidCDRationOS").value;
		//if(document.getElementById('hidImageCss').value == "imgLaCanvas"){
			//objCLSDrawing[0].setCDRation();
			//objCLSDrawing[1].setCDRation();
			if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
			for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
				if(document.getElementById('hidImageCss'+intCounter).value == "imgLaCanvas"){
					objCLSDrawing[intCounter].setCDRation();
				}
			}
		//}
	}
}
function saveCanvas(frmName, extraFun,flgStopSubmit){
	
	//TEST Save 2 --
	
	$("#img_load").show();
	var blHaveDrawing = false;
	extraFun = extraFun || "";	
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	//objCLSDrawing[0].saveCanvas(frmName, extraFun);
	//objCLSDrawing[1].saveCanvas(frmName, extraFun);
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
		//alert(objCLSDrawing[intCounter].arrBlCanvasHaveDrwaing[0]+"-"+intCounter);
		
		if(objCLSDrawing && objCLSDrawing[intCounter] && objCLSDrawing[intCounter].arrBlCanvasHaveDrwaing[0] == true){
			blHaveDrawing = true;
			objCLSDrawing[intCounter].saveCanvas(frmName, extraFun);
		}
		
	}
	if(typeof(flgStopSubmit)=="undefined" || flgStopSubmit!=1){
		
		/*
		if(blHaveDrawing == true){
			saveDone(frmName, extraFun);
		}
		else{
		*/	
			
			var objFrm = document.getElementById(frmName);
			if(""+typeof(objFrm.onsubmit)=="function"){objFrm.onsubmit();}
			
			objFrm.submit();
			/*frm_data = $(objFrm).serialize();alert(frm_data)
			$.ajax({
				type: "POST",
				url: "../drawing.php",
				data: frm_data,
				success: function(r) {alert(r)
					var myEditor = CKEDITOR.instances.FCKeditor1;
					 myEditor.insertHtml(r);
					 CKEDITOR.dialog.getCurrent().hide()
				}
			});*/
			
		//}	
	}	
}
/*
function saveDone(frmName, extraFun){
	//alert(frmName+"--"+extraFun);
	var tempInterval = setInterval(function() {				
		var ans = chkSaveDone();
		//alert(ans);
		if(ans == true){
			clearInterval(tempInterval);
			var objFrm = document.getElementById(frmName);
			if(extraFun != ""){
				extraFun = extraFun.replace(/@/gi, "'");
				eval(extraFun);
			}
			if(""+typeof(objFrm.onsubmit)=="function"){objFrm.onsubmit();}
			objFrm.submit();
		}
	}, zTimeoutMM);
}
*/
function chkSaveDone(){
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}	
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
		//alert(objCLSDrawing[intCounter].arrBlCanvasHaveDrwaing[0]+"-"+intCounter);
		if(objCLSDrawing[intCounter].arrBlCanvasHaveDrwaing[0] == true){
			if(document.getElementById("hidDone"+intCounter)){
				if(document.getElementById("hidDone"+intCounter).value != "DONE"){
					return false;
				}
			}
			else{
				return false;
			}
		}
	}
	return true;
}
function setCanvasImage(type, index){
	//objCLSDrawing[0].setCanvasImage(type);
	//objCLSDrawing[1].setCanvasImage(type);
	/*for(var intCounter = 0; intCounter < 25; intCounter++){
		objCLSDrawing[intCounter].setCanvasImage(type);
	}*/
	objCLSDrawing[index].setCanvasImage(type);
}
function setCurrentColor(color){
	//objCLSDrawing[0].setCurrentColor(color);
	//objCLSDrawing[1].setCurrentColor(color);
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
		objCLSDrawing[intCounter].setCurrentColor(color);
	}
}
function setLineType(type,obj){
	//objCLSDrawing[0].lineType = objCLSDrawing[0].setLineType(type, obj, objCLSDrawing[0].eventType);
	//objCLSDrawing[1].lineType = objCLSDrawing[1].setLineType(type, obj, objCLSDrawing[1].eventType);
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
		objCLSDrawing[intCounter].lineType = objCLSDrawing[intCounter].setLineType(type, obj, objCLSDrawing[intCounter].eventType);
	}
}
function setErase(type,obj){
	//objCLSDrawing[0].currentEraser = objCLSDrawing[0].setErase(type, obj);
	//objCLSDrawing[1].currentEraser = objCLSDrawing[1].setErase(type, obj);
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
		objCLSDrawing[intCounter].currentEraser = objCLSDrawing[intCounter].setErase(type, obj);
	}
}
if(typeof(blEnableHTMLDrawing)!="undefined" && blEnableHTMLDrawing=="1"){
	$(document).ready(function () {	
		//window.addEventListener("load", drawingInit, false);
		drawingInit();
	});
}
var gbImages;
function GetXmlHttpObject(){            
	var objXMLHttp = null;
	if(window.XMLHttpRequest){
		objXMLHttp = new XMLHttpRequest();
	}else if(window.ActiveXObject){
		objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return objXMLHttp;
}
function getTestImages(patId, canvasId){
	document.getElementById("img_load").style.display = "block";
	document.getElementById("divTestImages").style.display = "block";
	var xmlHttpObj = GetXmlHttpObject();
	if(xmlHttpObj==null){
		alert ("Browser does not support HTTP Request");	
		return;
	}	
	var url = 'iDoc-Drawing/CLSAJAXTestDrawing.php?patId='+patId+'&canvasId='+canvasId+'&mod=get';
	xmlHttpObj.onreadystatechange = function(){
		if(xmlHttpObj.readyState == 4){
			if(xmlHttpObj.responseText){				
				var strResponseVal = xmlHttpObj.responseText;				
				if(strResponseVal != "NOIMAGES"){
					var arrResponseVal = strResponseVal.split("~~");
					var htmlDiv = arrResponseVal[0];
					gbImages = arrResponseVal[1];				
					document.getElementById("divTestImagesMain").style.display = "block";				
					document.getElementById("divTestImagesMain").innerHTML = htmlDiv;
					document.getElementById("img_load").style.display = "none";
					//document.getElementById("cornea_od_desc_1").value = htmlDiv;
				}
				else{
					document.getElementById("img_load").style.display = "none";
					document.getElementById("divTestImagesMain").style.display = "none";
					document.getElementById("divTestImages").style.display = "none";
					alert("Sorry, No Test image(s) found at server!");
				}
			}else{
				document.getElementById("img_load").style.display = "none";
				document.getElementById("divTestImagesMain").style.display = "none";
				document.getElementById("divTestImages").style.display = "none";
				alert("Sorry, No Test image(s) found at server!");	
			}
		}
	}
	xmlHttpObj.open("GET",url,true);
	xmlHttpObj.send(null);
}
function delImages(images){
	var xmlHttpObjDel = GetXmlHttpObject();
	if(xmlHttpObjDel == null){
		alert ("Browser does not support HTTP Request");	
		return;
	}	
	images = typeof images === 'undefined' ? '' : images;
	if(images != ""){
		var urlDel = 'iDoc-Drawing/CLSAJAXTestDrawing.php?images='+images+'&mod=del';
		//alert(urlDel);
		xmlHttpObjDel.onreadystatechange = function(){
			if(xmlHttpObjDel.readyState == 4){
				if(xmlHttpObjDel.responseText){				
					var strResponseVal = xmlHttpObjDel.responseText;	
					gbImages = "";					
					//alert(strResponseVal);
				}
			}
		}
		xmlHttpObjDel.open("POST",urlDel,true);
		xmlHttpObjDel.send(null);
	}
}
function loadTestImage(imgPath,performedTestImagePath, canvasId, img_id){
	canvasId = canvasId || "";
	img_id = img_id || "";
	//alert(imgPath+"--"+testName+"--"+testId+"--"+performedTestImagePath+"--"+canvasId);
	//objCLSDrawing[0].loadTestImage(imgPath, testName, testId, performedTestImagePath);
	//objCLSDrawing[1].loadTestImage(imgPath, testName, testId, performedTestImagePath);
	if(canvasId == ""){
		if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
		for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
			if(objCLSDrawing && objCLSDrawing[intCounter]){
			objCLSDrawing[intCounter].loadTestImage(imgPath,performedTestImagePath,img_id);
			}
		}
	}
	else{
		objCLSDrawing[canvasId].loadTestImage(imgPath,performedTestImagePath,img_id);
	}
	delImages(gbImages);
}
function closeTestImages(){	
	document.getElementById("divTestImagesMain").style.display = "none";
	document.getElementById("divTestImages").style.display = "none";
	delImages(gbImages);
}
function resetDrawing(resetFor){	
	//objCLSDrawing[0].resetDrawing(resetFor);
	//objCLSDrawing[1].resetDrawing(resetFor);
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){		
		var a = document.getElementById("divDrawing"+intCounter);		
		if(a && a.style.display=="block"){
			objCLSDrawing[intCounter].resetDrawing(resetFor);		
		}
	}
	checkCanvasWNL();
}
function chkDrawingExits(){
	return objCLSDrawing[0].chkDrawingExits();
}
function drawImages(imgType){
	//objCLSDrawing[0].drawImages(imgType);
	//objCLSDrawing[1].drawImages(imgType);
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
		objCLSDrawing[intCounter].drawImages(imgType);
	}
}
function checkCanvasWNL(){
	//objCLSDrawing[0].checkCanvasWNL();
	//objCLSDrawing[1].checkCanvasWNL();
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){		
		if(objCLSDrawing[intCounter]){	objCLSDrawing[intCounter].checkCanvasWNL(intCounter);}
	}
}
function showHideMore(strShowHideFor, num , strDispOp){
	strDispOp = strDispOp || "";
	if((strShowHideFor == "moreSymp") && (strDispOp == "")){
		if(document.getElementById("aSympMore"+num).innerHTML == "More"){
			document.getElementById("moreSymp"+num).style.display = "block";
			document.getElementById("aSympMore"+num).innerHTML = "Hide";
		}
		else if(document.getElementById("aSympMore"+num).innerHTML == "Hide"){
			document.getElementById("moreSymp"+num).style.display = "none";
			document.getElementById("aSympMore"+num).innerHTML = "More";
		}
	}
	if(strShowHideFor == "moreSymp"){
		if(strDispOp == "hide"){
			document.getElementById("moreSymp"+num).style.display = "none";
			document.getElementById("aSympMore"+num).innerHTML = "More";
		}
	}
}
function setMoveCursor(ele, op) {
	op = op || 0;
	if(op == 1){
		ele.style.cursor = 'move';
	}
	else if(op == 0){
		ele.style.cursor = 'auto';
	}
}

function drag_move_div(ele, ev) {
	var deltaX = ev.clientX - parseInt(ele.style.left);
	var deltaY = ev.clientY - parseInt(ele.style.top);	
	
	ele.addEventListener('mousemove', moveHandler, false ); //Register handler
	ele.addEventListener('mouseup', upHandler, false ); 	//Register handler
	
	ev.cancelBubble = true;                             //Prevent bubbling
	ev.returnValue = false;                             //Prevent action
	
	//moveHandler: ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	//carries out the move when the mouse is dragged
	function moveHandler() {		
		e = window.event;                                 // IE event model
		ele.style.left = (e.clientX - deltaX) + "px";
		ele.style.top  = (e.clientY - deltaY) + "px";
		e.cancelBubble = true;                            //Prevent bubbling
	}
	//upHandler: ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	//Terminates the move and unregisters the handlers when the mouse is up
	function upHandler() {
		e = window.event; // IE event model
		ele.removeEventListener('mousemove', moveHandler, false ); 
		ele.removeEventListener('mouseup', upHandler, false ); 
		e.cancelBubble = true;//Prevent bubbling
	}
}
function doScanUpload(examId, formId, processFor, scanUploadfor, canvasId){
	canvasId = canvasId || "";
	
	if(processFor=="upload-WEBCAM"){
		
		var features = "toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=630,left=150,top=60";
		var url = 'iDoc-Drawing/webcam/flash.php?examId='+examId+'&formId='+formId+'&scanOrUpload='+processFor+'&scanUploadfor='+scanUploadfor+'&canvasId='+canvasId;
		var wname = "drwImg";
		window.open(url,wname,features);
		
	}else{	
		window.open('iDoc-Drawing/scan_upload_drawing.php?examId='+examId+'&formId='+formId+'&scanOrUpload='+processFor+'&scanUploadfor='+scanUploadfor+'&canvasId='+canvasId,'scan_upload_drawing','toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=1,width=650,height=470,left=290,top=100');	
	}
}
function getPreview(examId, formId, processFor, scanUploadfor, canvasId){
	canvasId = canvasId || "";
	var h = document.getElementById("hidSessionH").value;
	window.open('iDoc-Drawing/get_scan_upload_preview.php?examId='+examId+'&formId='+formId+'&scanOrUpload='+processFor+'&scanUploadfor='+scanUploadfor+'&canvasId='+canvasId,'get_preview','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}

function previous_tool(index){
	objCLSDrawing[index].previous_tool();
}

function next_tool(index){	
	objCLSDrawing[index].next_tool();
}

function callAJAXLoadDarwingData(id, a, callAJAX){	
	var that = this;
	this.id = id;
	this.a = a;
	if(callAJAX == false){
		var xmlHttpObjLoadDrawing = GetXmlHttpObject();
		if(xmlHttpObjLoadDrawing == null){
			alert ("Browser does not support HTTP Request");	
			return;
		}
		if(parseInt(that.id) > 0){
			var urlGetDrawing = 'iDoc-Drawing/load_drawing_data.php?id='+that.id;	
			//alert(urlGetDrawing);
			xmlHttpObjLoadDrawing.onreadystatechange = function(){
				if(xmlHttpObjLoadDrawing.readyState == 4){
					if(xmlHttpObjLoadDrawing.responseText){
						var strResponseVal = xmlHttpObjLoadDrawing.responseText;
						//document.writeln(strResponseVal);
						//return;
						var arrResponseVal = new Array();
						var dbRedPixel = dbGreenPixel = dbBluePixel = dbAlphaPixel = dbTollImage = dbPatTestName = dbPatTestId = dbTestImg = canvasDataFileNameDB = dbCanvasImageDataPoint = imgDB = strCanvasWNL = "";
						arrResponseVal = strResponseVal.split("`~`!@`~`");
						dbRedPixel = arrResponseVal[0];
						dbGreenPixel = arrResponseVal[1];
						dbBluePixel = arrResponseVal[2];
						dbAlphaPixel = arrResponseVal[3];
						dbTollImage = arrResponseVal[4];
						dbPatTestName = arrResponseVal[5];
						dbPatTestId = arrResponseVal[6];
						dbTestImg = arrResponseVal[7];
						canvasDataFileNameDB = arrResponseVal[8];
						dbCanvasImageDataPoint = arrResponseVal[9];
						imgDB = arrResponseVal[10];
						strCanvasWNL = arrResponseVal[11];
						//alert(imgDB);
						document.getElementById("hidImageCss"+that.a).value = dbTollImage;
						
						/*
						document.getElementById("hidRedPixel"+that.a).value = dbRedPixel;
						document.getElementById("hidGreenPixel"+that.a).value = dbGreenPixel;
						document.getElementById("hidBluePixel"+that.a).value = dbBluePixel;
						document.getElementById("hidAlphaPixel"+that.a).value = dbAlphaPixel;
						*/
						
						//document.getElementById("hidDrawingTestName").value = dbPatTestName;
						//document.getElementById("hidDrawingTestId").value = dbPatTestId;
						//document.getElementById("hidDrawingTestImageP").value = dbTestImg;
						document.getElementById("hidImgDataFileName"+that.a).value = canvasDataFileNameDB;
						document.getElementById("hidImagesData"+that.a).value = dbCanvasImageDataPoint;	
						document.getElementById("hidCanvasWNL").value += strCanvasWNL;
						objCLSDrawing[that.a].drawingInit();
						document.getElementById("divDrawing"+that.a).style.display = "block";
						document.getElementById("hidLoad"+that.id).value = "DONE";
						//
						checkwnls();//Set Flag						
					}
				}
			}
			xmlHttpObjLoadDrawing.open("GET",urlGetDrawing,true);
			xmlHttpObjLoadDrawing.send(null);
		}
	};
	
	this.chkCall = function() {
		//alert("chkCall"+that.id);
		if(document.getElementById("hidLoad"+that.id)){
			if(document.getElementById("hidLoad"+that.id).value == "DONE"){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	};
}

//Test --

function AJAXLoadDarwingData(id, strMasterDiv,fid,pid,eid,enm){
	
	
	alert('AJAXLoadDarwingData')
	if(typeof(id)=="undefined"||id==""){ id=0; }
	if(typeof(fid)=="undefined"||fid==""){ fid=0; }
	if(typeof(eid)=="undefined"||eid==""){ eid=0; }
	
	//alert(id+", "+strMasterDiv+", "+fid+", "+pid+", "+eid+", "+enm);
	
	document.getElementById("divTestImages").style.display = "block";
	document.getElementById("ajax_load_drawing").style.display = "block";
	
	//if(id != ""){	
		
		var urlGetDrawing = "iDoc-Drawing/load_images.php"; //?id='+id
		
		$.post(urlGetDrawing,{"id":id,"fid":fid, "pid":pid, "eid":eid, "enm":enm},function(data){
			
			//alert(data);
			
			var z;
			var data0 = data["drw"];
			var strCanvasWNLAll="";
			if(data0){
				for(z in data0){
					var strResponseVal = data0[z];
					var arrResponseVal = new Array();
					var dbRedPixel = dbGreenPixel = dbBluePixel = dbAlphaPixel = dbTollImage = dbPatTestName = dbPatTestId = dbTestImg = canvasDataFileNameDB = dbCanvasImageDataPoint = imgDB = strCanvasWNL = loadId=drwNE="";
					
					arrResponseVal = strResponseVal.split("`~`!@`~`");
					dbRedPixel = arrResponseVal[0];
					dbGreenPixel = arrResponseVal[1];
					dbBluePixel = arrResponseVal[2];
					dbAlphaPixel = arrResponseVal[3];
					dbTollImage = arrResponseVal[4];
					dbPatTestName = arrResponseVal[5];
					dbPatTestId = arrResponseVal[6];
					dbTestImg = arrResponseVal[7];
					canvasDataFileNameDB = arrResponseVal[8];
					dbCanvasImageDataPoint = arrResponseVal[9];
					imgDB = arrResponseVal[10];
					strCanvasWNL = arrResponseVal[11];
					loadId = arrResponseVal[12];
					drwNE = arrResponseVal[13];	
					
					//document.write("<pre>"+arrResponseVal[14]+" - "+arrResponseVal[15]);
					//alert("<pre>"+arrResponseVal[14]+" - "+arrResponseVal[15]);
					
					//alert(drwNE);
					//alert(imgDB);
					/*
					document.getElementById("hidImageCss"+z).value = dbTollImage;
					document.getElementById("hidRedPixel"+z).value = dbRedPixel;
					document.getElementById("hidGreenPixel"+z).value = dbGreenPixel;
					document.getElementById("hidBluePixel"+z).value = dbBluePixel;
					document.getElementById("hidAlphaPixel"+z).value = dbAlphaPixel;
					*/
					
					//document.getElementById("hidDrawingTestName").value = dbPatTestName;
					//document.getElementById("hidDrawingTestId").value = dbPatTestId;
					//document.getElementById("hidDrawingTestImageP").value = dbTestImg;	
					if(drwNE=="OD"){ document.getElementById("elem_drwNEOD"+z).checked=true;  }
					else if(drwNE=="OS"){ document.getElementById("elem_drwNEOS"+z).checked=true;  } 
					
					document.getElementById("hidImgDataFileName"+z).value = canvasDataFileNameDB;
					document.getElementById("hidImagesData"+z).value = dbCanvasImageDataPoint;	
					strCanvasWNLAll += strCanvasWNL;
					objCLSDrawing[z].drawingInit();
					document.getElementById("divDrawing"+z).style.display = "block";				
					document.getElementById("hidLoad"+loadId).value = "DONE";
					//				
				}
			}
			
			
			document.getElementById("hidCanvasWNL").value=""+strCanvasWNLAll;
			document.getElementById("totLoad").style.display = "none";
			document.getElementById("hidDrawingLoadAJAX").value = "1";
			document.getElementById("divTestImages").style.display = "none";
			document.getElementById("ajax_load_drawing").style.display = "none";
			if(z>0){
				$("#"+strMasterDiv).animate({scrollTop: 1},1000);
			}
			
			//test titlebar drawing
			if(data["drwtemp"]){
				var str="";
				var drwtemp=data["drwtemp"];
				var ln = drwtemp.length;
				for(var x in drwtemp){
					//alert(drwtemp[x][0]+" \n "+drwtemp[x][1]);
					if(drwtemp[x][1]!=""){
						str+="<span  class=\"toolImg scanImg\" style=\"background-image:url("+drwtemp[x][1]+");\" title=\""+drwtemp[x][2]+"\" onClick=\"setCanvasImage_v2(this,'"+drwtemp[x][0]+"');\">"+
							//"<div title=\"Click to delete template\" class=\" tooldel\" onclick=\"drwiconTempDel('"+drwtemp[x][3]+"',1,this)\"></div>"+
							"</span>"; 
					}else if(drwtemp[x][2]!=""){
						var css=drwtemp[x][2];	
						if(drwtemp[x][2]=="Cornea R L"){
							css="Cornea";
						}else if(drwtemp[x][2]=="Cornea Eye"){
							css="CorneaEye";
						}else if(drwtemp[x][2]=="EOM 2"){
							css="EOM2";
						}else if(drwtemp[x][2]=="Lids and Lacrimal"){
							css="LidsAndLacrimal";
						}else if(drwtemp[x][2]=="Pic-Con"){
							css="PicCon";
						}
						
						str+="<span class=\"toolImg toolImg"+css+"\"  title=\""+drwtemp[x][2]+"\" onClick=\"setCanvasImage_v2(this);\">"+
								//"<div title=\"Click to delete template\"  class=\" tooldel\" onclick=\"drwiconTempDel('"+drwtemp[x][3]+"',2,this)\"></div>"+
								"</span>"; 
						
					}
				}				
				
				$(".flltToolTop").html(str);	
			}
			
			
			//if(id>=0){					
				checkwnls();//Set Flag				
			///}	
			
		},"json");
	//}	
}

//Test --
/*
function AJAXLoadDarwingData(id, strMasterDiv){
	///alert(id);
	document.getElementById("divTestImages").style.display = "block";
	document.getElementById("ajax_load_drawing").style.display = "block";
	if(id != ""){
		var a;
		a = 0;
		var intLoading = 1;
		var arrId = id.split(",");
		document.getElementById("totLoad").innerHTML = intLoading+"/"+arrId.length;
		var callAJAX = false;
		var nextDivId;
		//var timerID = setInterval(function(){ 
		(function(){ 	
			var call = new callAJAXLoadDarwingData(arrId[a], a, callAJAX);
			callAJAX = true;
			//var tempInterval = setInterval(function() {
			(function() {	
				//alert("in");			
				var ans = call.chkCall();
				alert("ans"+ans);
				if(ans == true){
					callAJAX = false;
					//clearInterval(tempInterval);
					if(a < (arrId.length - 1)){
						document.getElementById("plus"+a).style.display = "none";
					}
					nextDivId = "divDrawing" + a;
					if(a > 0){
						$("#"+strMasterDiv).animate({scrollTop: document.getElementById(nextDivId).offsetTop},1000);
					}
					a++;
					intLoading++;
					document.getElementById("totLoad").innerHTML = intLoading+"/"+arrId.length;
					alert("HELLO01");
					if(a == arrId.length){
						document.getElementById("totLoad").innerHTML = "100%";
						//clearInterval(timerID);
						//var hideInterval = setInterval(function() {
						alert("HELLO0");
						(function() {		
							//clearInterval(hideInterval);
							alert("HELLO");
							document.getElementById("totLoad").style.display = "none";
							document.getElementById("hidDrawingLoadAJAX").value = "1";
							document.getElementById("divTestImages").style.display = "none";
							document.getElementById("ajax_load_drawing").style.display = "none";
							alert("HELLO 2");
							$("#"+strMasterDiv).animate({scrollTop: 1},1000);
						//}, 1000);
						})();	
					}					
				}
			//},100);
			})();	
		})();	
		//}, 10000);			
	}
}
*/
/*function AJAXLoadDarwingData(id){
	alert(id);
	var xmlHttpObjLoadDrawing = GetXmlHttpObject();
	if(xmlHttpObjLoadDrawing == null){
		alert ("Browser does not support HTTP Request");	
		return;
	}
	document.getElementById("divTestImages").style.display = "block";
	document.getElementById("ajax_load_drawing").style.display = "block";
	if(id != ""){
		var urlDel = 'iDoc-Drawing/load_drawing_data.php?id='+id;
		xmlHttpObjLoadDrawing.onreadystatechange = function(){
			if(xmlHttpObjLoadDrawing.readyState == 4){
				if(xmlHttpObjLoadDrawing.responseText){				
					var strResponseValue = xmlHttpObjLoadDrawing.responseText;
					//alert(strResponseValue);
					var arrResponseValue = new Array();
					arrResponseValue = strResponseValue.split("-!@#-::-!@#-");
					for(var a = 0; a < arrResponseValue.length; a++){
						var strResponseVal = arrResponseValue[a];
						var arrResponseVal = new Array();
						var dbRedPixel = dbGreenPixel = dbBluePixel = dbAlphaPixel = dbTollImage = dbPatTestName = dbPatTestId = dbTestImg = canvasDataFileNameDB = dbCanvasImageDataPoint = imgDB = strCanvasWNL = "";
						arrResponseVal = strResponseVal.split("`~`!@`~`");
						dbRedPixel = arrResponseVal[0];
						dbGreenPixel = arrResponseVal[1];
						dbBluePixel = arrResponseVal[2];
						dbAlphaPixel = arrResponseVal[3];
						dbTollImage = arrResponseVal[4];
						dbPatTestName = arrResponseVal[5];
						dbPatTestId = arrResponseVal[6];
						dbTestImg = arrResponseVal[7];
						canvasDataFileNameDB = arrResponseVal[8];
						dbCanvasImageDataPoint = arrResponseVal[9];
						imgDB = arrResponseVal[10];
						strCanvasWNL = arrResponseVal[11];
						//alert(imgDB);
						//document.getElementById("hidImageCss").value = dbTollImage;
						document.getElementById("hidRedPixel"+a).value = dbRedPixel;
						document.getElementById("hidGreenPixel"+a).value = dbGreenPixel;
						document.getElementById("hidBluePixel"+a).value = dbBluePixel;
						document.getElementById("hidAlphaPixel"+a).value = dbAlphaPixel;
						//document.getElementById("hidDrawingTestName").value = dbPatTestName;
						//document.getElementById("hidDrawingTestId").value = dbPatTestId;
						//document.getElementById("hidDrawingTestImageP").value = dbTestImg;
						document.getElementById("hidImgDataFileName"+a).value = canvasDataFileNameDB;
						document.getElementById("hidImagesData"+a).value = dbCanvasImageDataPoint;	
						document.getElementById("hidCanvasWNL").value = strCanvasWNL;
						objCLSDrawing[a].drawingInit();
						//objCLSDrawing[0].drawingInit();
						//objCLSDrawing[1].drawingInit();
						document.getElementById("hidDrawingLoadAJAX").value = "1";
						document.getElementById("divTestImages").style.display = "none";
						document.getElementById("ajax_load_drawing").style.display = "none";
						document.getElementById("divDrawing"+a).style.display = "block";
					}
				}
			}
		}
		xmlHttpObjLoadDrawing.open("get",urlDel,true);
		xmlHttpObjLoadDrawing.send(null);
	}
}
*/
function setSmartTagDivID(){
	if(typeof(arrSmartTagDivID)!="undefined" && arrSmartTagDivID.length>0){
	for(var a = 0; a < arrSmartTagDivID.length; a++){		
		//objCLSDrawing[0].gbArrSmartTagDivID[a] = arrSmartTagDivID[a];
		//objCLSDrawing[1].gbArrSmartTagDivID[a] = arrSmartTagDivID[a];
		if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
		for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
			objCLSDrawing[intCounter].gbArrSmartTagDivID[a] = arrSmartTagDivID[a];
		}
		//alert(objCLSDrawing.gbArrSmartTagDivID[0]);
	}
	}
}
function setSmartTag(op){
	var arrSmartTagOp = new Array();
	var arrSmartTagOpID = new Array();
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	if(document.getElementsByName("chkSmartTag")){
		var objChkSmartTag =  document.getElementsByName("chkSmartTag")
		for(var a = 0; a < objChkSmartTag.length; a++){
			var objChkSmartTagItem = objChkSmartTag.item(a);
			if(objChkSmartTagItem.checked == true){
				objChkSmartTagItem.checked = false;
				//alert(objChkSmartTagItem.value);			
				arrSmartTagOp.push(objChkSmartTagItem.value);
				arrSmartTagOpID.push(objChkSmartTagItem.id);
			}
		}
	}
	if((arrSmartTagOp.length > 0) && (op == "done")){
		//objCLSDrawing[0].insertSmartTag(arrSmartTagOp, arrSmartTagOpID);
		//objCLSDrawing[1].insertSmartTag(arrSmartTagOp, arrSmartTagOpID);		
		for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
			objCLSDrawing[intCounter].insertSmartTag(arrSmartTagOp, arrSmartTagOpID);
		}
	}
	else{
		if(op == "done"){
			//objCLSDrawing[0].insertSmartTag(arrSmartTagOp, arrSmartTagOpID);
			//objCLSDrawing[1].insertSmartTag(arrSmartTagOp, arrSmartTagOpID);
			for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
				objCLSDrawing[intCounter].insertSmartTag(arrSmartTagOp, arrSmartTagOpID);
			}
			alert("Please select smart tag option to proceed Done!")
		}
	}
}
function drawRotatedImage(op,id){	
	var flotRotateAngle = parseFloat(document.getElementById("txtRotateAngle"+id).value);
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	if(flotRotateAngle > 0 && flotRotateAngle <= 360){
		if(op == 1){
			if(flotRotateAngle > 0){
				//objCLSDrawing[0].drawRotatedImage(op, flotRotateAngle);
				//objCLSDrawing[1].drawRotatedImage(op, flotRotateAngle);
				for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
					objCLSDrawing[intCounter].drawRotatedImage(op, flotRotateAngle);
				}
			}
		}
		else if(op == 2){
			if(flotRotateAngle > 0){
				//objCLSDrawing[0].drawRotatedImage(op, flotRotateAngle);
				//objCLSDrawing[1].drawRotatedImage(op, flotRotateAngle);
				for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
					objCLSDrawing[intCounter].drawRotatedImage(op, flotRotateAngle);
				}
			}		
		}
	}
	else{
		alert("Please entre rotation angle degree in between 1 and 360!")
	}
}

function idoc_getNextDrawNumber(){
	var tmpIndx = "-1";
	/*
	$(".canvasPrevBorder, .cCanvas").each(function(){ 
				if(typeof(this.id)!="undefined" && this.id!=""){
					var indx = this.id.replace("cCanvas", "");
					indx = parseInt(indx);
					if(indx > tmpIndx){
						tmpIndx = indx;
					}
				}  
		});
	*/
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	for(var i=0;i<drawCntlNum;i++){		
		if($("#divCanvas"+i).length<=0){
			tmpIndx = i;
			break;	
		}	
	}
	
	return tmpIndx;	
}


var flg_showNext_inprocess=0;
function showNext(intCounter, strMasterDiv){
	intCounter = parseInt(intCounter) + 1
	var nextDivId = "divDrawing" + intCounter;
	if(document.getElementById(nextDivId)){
		document.getElementById(nextDivId).style.display = "block";
		//document.getElementById(strMasterDiv).scrollTop = document.getElementById(nextDivId).offsetTop;
		$("#"+strMasterDiv).animate({scrollTop: document.getElementById(nextDivId).offsetTop,"opacity": "0.5"},'slow', function (){$("#"+strMasterDiv).css({"opacity":""})});

	}
	else{
		
		if(flg_showNext_inprocess==1){ return; }
		if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
		
		var intCounter = idoc_getNextDrawNumber();
		if(intCounter=="-1"){ alert("Multi drawings limit is exceeding. Maximum allowed is "+drawCntlNum+"!"); return; }		
		var nextDivId = "divDrawing" + intCounter;
		flg_showNext_inprocess=1;	
		//getdrawing from server --
		$.get("common/requestHandler.php",{"elem_formAction":"AddNewDrawing","intTempDrawCount":intCounter, "examName":examName},function(data){
				flg_showNext_inprocess=0;
				//alert(data);	
				//document.write(data);
				data = $.trim(data);
				//$("#td_la_drawing").append(data);
				$(data).insertAfter($("div[id*=divDrawing]").last());
				objCLSDrawing[intCounter] = new CLSDrawing(intCounter);
				objCLSDrawing[intCounter].drawingInit();
				//$("#"+strMasterDiv).animate({scrollTop: document.getElementById(nextDivId).offsetTop,"opacity": "0.5"},'slow', function (){$("#"+strMasterDiv).css({"opacity":""})});
			
				$("#"+strMasterDiv).animate({scrollTop: document.getElementById(nextDivId).offsetTop,"opacity": "0.5"},'slow', function (){$("#"+strMasterDiv).css({"opacity":""})});			
			
			});
		
		//if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
		//alert("Multi drawings limit is exceeding. Maximum allowed is "+drawCntlNum+"!");
	}
}

//Delete Drawing
function idocdraw_delNext(intCounter, strMasterDiv){	
	//if(intCounter>0){		
		var flg = confirm("Are you sure to delete this drawing? \n\nDrawing can not be recovered!");	
		if(flg==true){	
		var nextDivId = "divDrawing" + intCounter;		
		//document.getElementById("hidDrawingChangeYesNo" + intCounter).value = "yes";
		//document.getElementById("hidRedPixel" + intCounter).value = "DELETE";
		//document.getElementById(nextDivId).style.display = "none";
			
			//Save
			if(examName == "LA"){
				testId="hidLADrawingId";
			}else if(examName == "Gonio"){
				testId="hidIOPDrawingId";
			}else if(examName == "Fundus"){
				testId="hidFundusDrawingId";
			}else if(examName == "SLE"){
				testId="hidSLEDrawingId";
			}else if(examName == "External"){
				testId="hidExternalDrawingId";
			}else if(examName == "EOM"){
				testId="hidEOMDrawingId";
			}else{
				testId="";	
			}			
			
			if(testId!=""){
				var id = $(":hidden[name='"+testId+intCounter+"']").val();				
				if(id!="" && id!=0){					
					$.post("saveCharts.php",{"elem_saveForm":"DeleteDrawing","hidDrawingId":id},function(data){});
					$(":hidden[name='"+testId+intCounter+"']").val(0);	
				}
			}
			
		//remove
		//document.write($("#"+nextDivId).html());	
		//alert($("#"+nextDivId).html());	
		//var myWindow = window.open("","MsgWindow","width=1000,height=700, scrollbars=yes, resizable=yes,");
		//myWindow.document.write(""+$("#"+nextDivId).html());
		if($("div[id*=divDrawing]").length>1){
			$("#"+nextDivId).remove();	
		}else{	
			setEvent('funClearCanvas', '', '0', intCounter);
		}			
			
		}	
	//}else{
	//	setEvent('funClearCanvas', '', '0', intCounter);
	//}
}

function setAllDrawingToSave(){	
	/*
	$("div[id*=divDrawing]").each(function(inx){
			if($(this).css("display")=="block"){
				//$(this).children(":input[name*=hidDrawingChangeYesNo]").val("yes");
				
			}
		});		
	*/
	
	if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
	for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){		
		if($("#divDrawing"+intCounter).css("display")=="block"){
			objCLSDrawing[intCounter].arrBlCanvasHaveDrwaing[0]=true;
			objCLSDrawing[intCounter].arrBlCanvasHaveDrwaing[1]=true;		
		}
	}	
}

function funUndoText(intCounter){
	intCounter=intCounter||0;
	objCLSDrawing[intCounter].funUndoText();
}

function drw_addtxt(objM,intD){
	
	//alert(objM+" -- "+intD);
	objCLSDrawing[intD].addtxt(objM);
	
}

function funScaleMe(intCounter){
	var val = document.getElementById("elem_scalesize"+intCounter);
	intCounter=intCounter||0;
	objCLSDrawing[intCounter].funScaleMe(val);
}

var warning_flg=0; //worning once
function setCanvasImage_v2(obj, pth, img_id){
	img_id = img_id || '';
	//remove
	$("#dv_temp_showIcons").remove();
	
	var strindx=$(obj).parents(".flltToolTop").attr("id");
	
	
	if(typeof(strindx)=="undefined"){ 
		strindx=$(obj).parent("div").attr("data-indx"); 
	}else{	
		strindx=strindx.replace(/toolTop/, "");		
	}
	
	//if(typeof(strindx)=="undefined"||strindx==""){return 0;}
	
	if(typeof(pth)=="undefined"){
		//default
		var name = $(obj).attr("title");
		setCanvasImage(name, strindx);
	}else{
		//scan
			warning_flg=1;
	
		var testName=testId=performedTestImagePath="";
		performedTestImagePath=pth.replace(zPath+"/main/uploaddir","");
		
		//alert("F: "+pth+"\n"+testName+"\n"+testId+"\n"+performedTestImagePath+"\n"+strindx+"\n"+zPath+"main/uploaddir");		
		loadTestImage(pth, performedTestImagePath, strindx, img_id);
	}	
}

function drw_showAllTools(val,indx){
	//window.status=val;
	
	if(val==0){
		$("#dv_temp_showIcons").remove();		
	}else if(val==1){
		
		//alert($(obj).children("#dv_temp_showIcons").length);
		
		if($("#dv_temp_showIcons").length<=0){
			var html=$("#toolTop"+indx).html();	
			var str="<div id=\"dv_temp_showIcons\" data-indx=\""+indx+"\"  class=\"fllt\" style=\"position:absolute;border:1px solid red;border:1px dashed #999; width:550px; text-align:left;top:0px;left:0px;background-color:white; \" >"+					
					html+
					"</div>";
			$("body").append(str);
			
			var o = $("#toolTop"+indx).position();
			var o1 =$("#dv_temp_showIcons");
			//window.status=""+o1[0].offsetWidth+" - "+o1[0].offsetHeight;
			//alert("left: "+o.left+"px, top:"+o.top+"px");
			
			$("#dv_temp_showIcons").css({"left":o.left+"px","top":(o.top+o1[0].offsetHeight)+"px"});		
			
		}else{
			$("#dv_temp_showIcons").remove();			
		}		
	}	
}

function drwiconTempDel(id,flg,obj){
	
	//alert(id+","+flg+","+obj);
	
	stopClickBubble();	
	if(id!=""){
		
		var flgCnfm = confirm("Are you sure to delete it?");
		if(!flgCnfm){return 0;}
		//alert(id+" - "+flg);
		$.post("common/requestHandler.php",{"elem_formAction":"deleteDrawTemplate", "id":id,"type":flg},function(data){
			
			$(obj).parent().remove();
			
			});
		
	}
	
	
}

//-----

function drw_scanWebImage(){
	var features = "toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=630,left=150,top=60";
	var url = "iDoc-Drawing/webcam/flash.php";
	var wname = "drwImg";
	window.open(url,wname,features);
//	window.open("webcam/flash.php",'lic','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=630,left=150,top=60');		
}

//-----

function drawNE(obj){
	//check---	
	var wh = obj.id.indexOf("OD")!=-1 ? "OD" : "OS";
	
	if(obj.checked){
		if(wh=="OD"){
			var chk = obj.id.replace("OD","OS");	
		}else{
			var chk = obj.id.replace("OS","OD");	
		}
		
		if($("#"+chk).prop("checked")){
			alert("Invalid operation.  Both eyes cannot be selected as Not Examined.");
			obj.checked=false;
			return;
		}		
	}
	
	var intCounter =obj.id.replace("elem_drwNEOD","");
	intCounter = intCounter.replace("elem_drwNEOS","");
	objCLSDrawing[intCounter].drawNE(wh,obj.checked);
}