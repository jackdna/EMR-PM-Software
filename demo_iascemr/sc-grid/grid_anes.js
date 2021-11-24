var arrDrawIcon_main = ['sc-grid/images/CDr.png','sc-grid/images/CFill.bak.png','sc-grid/images/TDn.png','sc-grid/images/TUp.png'];
function initDraw(cnm){
	//
	var c="div_"+cnm;
	var ocan_all = $('#'+c+' canvas');
	var ocan =  ocan_all[0];
	var ar_redo = [];
	
	//load drawing icons first
	var arrDrawIcon=[];
	if(typeof(arrDrawIcon_main)!="undefined" && arrDrawIcon_main){
		for(var exm in arrDrawIcon_main){
			if(arrDrawIcon_main[exm] && typeof(arrDrawIcon_main[exm])!="undefined" && arrDrawIcon_main[exm]!=""){
				arrDrawIcon[exm] = new Image();				
				arrDrawIcon[exm].src = arrDrawIcon_main[exm];
			}
		}
	}

	var canvas = this.__canvas = new fabric.Canvas(ocan,{ perPixelTargetFind: true,targetFindTolerance: 4});
	setBottoxDrw_canvas = canvas;
	fabric.Object.prototype.transparentCorners = false;
	canvas.selection = false;
	var otext;
	
	var addExamNmOnCanvas = function(o){  
		//$(".drw_menu").remove(); 
		var otgt = o.target;
		var e = o.e;
		var pointer = canvas.getPointer(e);	
		
		if(parseInt(pointer.x) <= 135 ){ return ; }
		
		var imgType = $("#"+c+" :hidden[name=elem_drw_icon_sel]").val();
		if(typeof(imgType) == "undefined" || imgType==""){ imgType=0; } 
		var tlen = canvas.getObjects().length;
		if(typeof(tlen)=="undefined"){ tlen=0; }	
		
		var uidx="ox"+tlen, uiddsg="odsg"+tlen;
		
		var fi=0;
		
		//--
		if(fi==0){
		if(isNaN(imgType) && imgType.toLowerCase() == "text"){			
			otext = new fabric.Citext('', {zIndex:100000000000000, left: parseInt(pointer.x),  top: parseInt(pointer.y),  padding: 7, fontSize:'15',fontFamily: 'Times New Roman', fill: '#000000', selectable:false  });
			canvas.add(otext).setActiveObject(otext);
			otext.enterEditing();
		}else{
			var oImg = new fabric.CImage(arrDrawIcon[imgType]);
			var w = parseInt(oImg.width);
			var h = parseInt(oImg.height);	
			oImg.set({ left: parseInt(pointer.x)-2,   top: parseInt(pointer.y)-7,  transparentCorners:true, cornerSize:5, width:w,height:h, hasControls:false, hasBorders:false, selectable:true  });		
			canvas.add(oImg);
		}
		
		}
		//--	
	};

	canvas.on('mouse:over', function(e) {  /*if(e.target.)  canvas.selection=true;*/	 });

	canvas.on('mouse:down', function(e) {
			if(e.target && e.target.type == "c-image"){ return; }
			addExamNmOnCanvas(e); 
	});	

	canvas.on('mouse:out', function(e) {			  });
	
	$("#"+c).on("mouseout", function(){  $("#elem_"+cnm+"_drw").val(ocan.toDataURL("image/png"));  var s = ""+JSON.stringify(canvas); /*s=s.replace(/\\n/g,"lshl");*/  $("#elem_"+cnm+"_drw_coords").val(s);   } );
	
	var xjson = $("#elem_"+cnm+"_drw_coords").val();
	if($.trim(xjson)!=""){xjson = JSON.parse(xjson); canvas.loadFromJSON(xjson, canvas.renderAll.bind(canvas), function(o, object){ object.set({hasControls:false, hasBorders:false, selectable:true});  }); $("#"+c).trigger("mouseout"); }	
	
	this.undo = function(){
		
		var objects = canvas.getObjects();
		var len = objects.length;
		
		if(len==0){return;}
		
		var pop_o = objects[len-1];	
		ar_redo[ar_redo.length] =  pop_o;
		
		objects.length = len-1;
		canvas.renderAll();
	};
	
	this.redo = function(){
		
		var len = ar_redo.length;
		if(len==0){return;}
		
		var pop_o = ar_redo[len-1];
		ar_redo.length = len-1;
		
		var objects = canvas.getObjects();
		objects[objects.length] =  pop_o;
		canvas.renderAll();
	}
	
	this.clear = function(){
		// remove all objects and re-render
		canvas.clear().renderAll();
		
	}
}

function setEvent(d){
	if(d=="undo"){	xdrx.undo();	}
	else if(d=="redo"){ xdrx.redo(); }
	else if(d=="erase"){ xdrx.clear(); }
	else{	
	$("#elem_drw_icon_sel").val(d);
	}
}

var xdrx;
$(document).ready(function () {

//Bottox check
xdrx = new initDraw('cnvs_anes');
	
});
//--