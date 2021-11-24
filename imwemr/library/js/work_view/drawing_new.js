var idocdraw_defname="divDrawing";
var idocdraw_warning_flg=0; //worning once
var idocdraw_arr=[]; //array of all drawing
var gbImages;
var console = window.console || { log: function() {} };
var idocdraw = function(c) {

  var me = this; 		
  var strDataDefLength=0, strDataEmptyLength=0;
  var iTextNE;
  var arrMenu=[];
  me.arrSympDone_showSymptomPopUP=[];
  me.forceSave=0;
  var img_denominator=8;	
	
  //var $ = function(id){return document.getElementById(id)};
  var mc =  $('#'+c);
  if(mc.length<=0){ return; }
  var ocan_all = $('#'+c+" canvas");	
  var ocan =  ocan_all[0];
  var cnvsIndx = ""+c.replace(idocdraw_defname,"");
   	
	
  //load drawing icons first
	var arrDrawIcon=[];
	if(typeof(arrDrawIcon_main)!="undefined" && arrDrawIcon_main){
		arrDrawIcon = arrDrawIcon_main;
		
		for(var exm in arrDrawIcon){
			if(arrDrawIcon[exm]["path"] && typeof(arrDrawIcon[exm]["path"])!="undefined" && arrDrawIcon[exm]["path"]!=""){
				arrDrawIcon[exm]["img"] = new Image();				
				arrDrawIcon[exm]["img"].src = arrDrawIcon[exm]["path"];
				var src2 = arrDrawIcon[exm]["path"].replace('drawicon','drawicon/L');
				arrDrawIcon[exm]["L_img"] = new Image();				
				arrDrawIcon[exm]["L_img"].src = src2;
			}				
		}	
	}	
	

  var canvas = this.__canvas = new fabric.Canvas(ocan, {
	isDrawingMode: true,
	perPixelTargetFind: true,
	targetFindTolerance: 4
  });
  
  //disable right click on canvas;;
   //Disable context menu        
      $('#'+c+" .upper-canvas").bind('contextmenu', function(e) {
	showCanvasMenu(e);
	e.preventDefault();    
	return false;
    });
  
  //slider line width--
  $( '#'+c+" #slider-range-min" ).slider({
      range: "min",
      value: 1,
      min: 1,
      max: 40,
      slide: function( event, ui ) {
        $( '#'+c+" #drawing-line-width" ).val( "" + ui.value ).triggerHandler("change");
      }
    });
 $( '#'+c+" #drawing-line-width" ).val( "" + $( '#'+c+" #slider-range-min" ).slider( "value" ) ).triggerHandler("change");
  //slider line width--  
  
  //slider line width--
  $( '#'+c+" #slider-range-font-size" ).slider({
      range: "min",
      value: 13,
      min: 10,
      max: 60,
      slide: function( event, ui ) {
        $( '#'+c+" #text-font-size" ).val( "" + ui.value ).triggerHandler("change");
      }
    });
  $( '#'+c+" #text-font-size" ).val( "" + $( '#'+c+" #slider-range-font-size" ).slider( "value" ) ).triggerHandler("change");
  //slider line width--      
    
    
  //color-picker --   
  /*
  $('#'+c+" #drawing-color").spectrum({
    color: "black",
    showInput: true,
    className: "full-spectrum",
    showInitial: true,
    showPalette: true,
    showSelectionPalette: true,
    maxSelectionSize: 10,
    preferredFormat: "hex",
    localStorageKey: "spectrum.demo",
    hideAfterPaletteSelect: true,	  
    move: function (color) {
        
    },
    show: function () {
    
    },
    beforeShow: function () {
    
    },
    hide: function () {
    
    },
    change: function() {        
    },
    palette: [
        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"], 
        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", 
        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", 
        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", 
        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", 
        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", 
        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", 
        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
    ]
});
*/
  //color-picker --



  //var canvas = new fabric.Canvas('c');
 
  //var info = document.getElementById('info'); 
  
  fabric.Object.prototype.transparentCorners = false;
 
  var global = [];
  global["mode"]=global["selection"]=global["Filled"]=global["Oval"]="";
  
  var line, isDown, arrow=[], circle,  group, group_arrow;
  
  var startx,starty;

  canvas.on({
  
  'mouse:down': function(ev) {
    //var text = document.createTextNode(' MouseDown ');
    //info.insertBefore(text, info.firstChild);
	isDown = true;
	var pointer = canvas.getPointer(ev.e);
	var points = [ pointer.x, pointer.y, pointer.x, pointer.y ];
	startx = pointer.x;
	starty = pointer.y;	
	  
	if(global["mode"]=="Eraser"){ 
		//*
		canvas.selection = false;
		var flg_1=0;
		canvas.forEachObject(function(o) {		  
		   		
		  if(flg_1==0&&o.containsPoint(pointer)){  //make selection for eraser
				//o.set({padding:10, borderColor: 'red',cornerColor: 'green',cornerSize: 6,hasControls: false, hasRotatingPoint:false });
				//canvas.setActiveObject(o);
				
				var o_glp= o.getLocalPointer(ev, pointer); 
				//console.log("type: ",o.type);
				if(o.type=="cGroup"){				
					remove_group_obj(o,pointer.x,pointer.y);
				}else{				
					erase_obj_pixel(o,0,o_glp.x,o_glp.y);						
				}	
				flg_1=1;
			  }	
		});
		//*/
	}
	
	if(global["mode"]=="Line" || global["mode"]=="Arrow"){
		var strk = canvas.freeDrawingBrush.width || 5;
		line = new fabric.CLine(points, {
		    strokeWidth: strk,
		    fill: drawingColorEl.val(),
		    stroke: drawingColorEl.val(),
		    originX: 'center',
		    originY: 'center',
		    selectable:true	
		    
		  });
		
		//--
		if(global["mode"]=="Arrow"){
			
		var headlen = 10;	// length of head in pixels
		var angle = Math.atan2(pointer.y-pointer.y,pointer.x-pointer.x);
		arrow[0] = new fabric.CLine([ pointer.x, pointer.y, pointer.x-headlen*Math.cos(angle-Math.PI/6),pointer.y-headlen*Math.sin(angle-Math.PI/6) ] , {
		    strokeWidth: strk,
		    fill: drawingColorEl.val(),
		    stroke: drawingColorEl.val(),
		    originX: 'center',
		    originY: 'center',
		    selectable:true	
		    
		  });	
		arrow[1] = new fabric.CLine([ pointer.x, pointer.y, pointer.x-headlen*Math.cos(angle+Math.PI/6),pointer.y-headlen*Math.sin(angle+Math.PI/6) ] , {
		    strokeWidth: strk,
		    fill: drawingColorEl.val(),
		    stroke: drawingColorEl.val(),
		    originX: 'center',
		    originY: 'center',
		    selectable:true
		  });	 		
		  
		line.on('selected', function() {
				var lid = canvas.getObjects().indexOf(this);
				canvas.setActiveGroup(new fabric.Group([ this, canvas.item(parseInt(lid)+1), canvas.item(parseInt(lid)+2) ])).renderAll();				
			}); //select all arrow  
		//testing
		canvas.add(line, arrow[0],arrow[1]);  //or
		  
		  /* Group works but Selection do not//*/
		 //group_arrow = new fabric.Group([ line, arrow[0], arrow[1] ], {left:0, top:0, selectable:true });		 
		 
		  //canvas.selection = false;
		  //canvas.add(group_arrow);	  
		 
		 
		 // 
		  
		}else{			
			line.on('selected', function() { canvas.setActiveGroup(new fabric.Group([ this ])).renderAll(); }); //select all arrow  
			canvas.add(line);
		}
		//--
		
		//stop selection
		canvas.selection = false;
		
	}	
	
	if(!canvas.isDrawingMode&&global["selection"]!=1){	 
		if(global["mode"]=="Circle"){
				//canvas.selection = false;
				//alert("circle create start");
				//var circle = new fabric.Circle({   radius: 20, left:parseInt(pointer.x)-10, top:parseInt(pointer.y)-10, fill: drawingColorEl.value  });
				var dred=10; //give value divisible by 2
				var ocfeat={   radius: dred, left:parseInt(pointer.x)-dred/2, top:parseInt(pointer.y)-dred/2, transparentCorners:true, cornerSize:5  };
				if(global["Filled"]==1){ ocfeat.fill=drawingColorEl.val();  }else{ ocfeat.fill='transparent'; ocfeat.stroke='black';  }
				if(global["Oval"]==1){ ocfeat.flipX=true; ocfeat.scaleY=0.5; }
				
				var circle = new fabric.CCircle(ocfeat);				
				canvas.add(circle);
		}
		
		if(global["mode"]=="Arc"){
				//canvas.selection = false;
				//alert("circle create start");
				//var circle = new fabric.CCircle({   radius: 20, left:parseInt(pointer.x)-10, top:parseInt(pointer.y)-10, fill: drawingColorEl.value  });
				var dred=10; //give value divisible by 2
				var ocfeat={   radius: dred, left:parseInt(pointer.x)-dred/2, top:parseInt(pointer.y)-dred/2, stroke:drawingColorEl.val(), fill: '', startAngle: 0, endAngle: Math.PI, transparentCorners:true, cornerSize:5  };
				var circle = new fabric.CCircle(ocfeat);				
				canvas.add(circle);
		}
		
		if(global["mode"]=="Rect"){
			var dred=25;
			var ocfeat={ left:parseInt(pointer.x)-dred/2, top:parseInt(pointer.y)-dred/2, width: dred, height: dred, transparentCorners:true, cornerSize:5 };
			if(global["Filled"]==1){ ocfeat.fill=drawingColorEl.val();  }else{ ocfeat.fill='transparent'; ocfeat.stroke='black';  }
			if(global["RoundEdge"]=="1"){ ocfeat.rx=5; ocfeat.ry=5; }  
			
			var rect = new fabric.CRect(ocfeat); //fill: drawingColorEl.value			
			canvas.add(rect);
		}
		
		if(global["mode"]=="Text"){
			var otext = new fabric.Citext('', {zIndex:100000000000000, left: parseInt(pointer.x),  top: parseInt(pointer.y),  padding: 7, fontSize:textFontSizeEl.val(),fontFamily: fontFamilyEl.val(), fill: drawingColorEl.val()  });
			canvas.add(otext).setActiveObject(otext);
			otext.enterEditing();
			otext.on("editing:exited", function(){ $("#div_tmp_tpahd").remove();  var txt = $.trim(""+otext.getText()); if(txt!=""){  me.showSymptomPopUP(txt); /*console.log(otext.toJSON());*/  }});
			otext.on("changed", function(e){
					var txt = $.trim(""+otext.getText());					
					if(txt!=""){
					
					//console.log($( "#tmp_tpahd" ));
					//$( "#tmp_tpahd" ).
							
					var tp = parseInt(otext.getTop())+40;	
					var lft = parseInt(otext.getLeft())+60;
							
						
					if($("#div_tmp_tpahd").length<=0){	
						$("#div_tmp_tpahd").remove();							
						$(mc).append("<div id=\"div_tmp_tpahd\"  style=\"position:absolute; width:100px; height:100px;top:100px; left:200px;\"><input id=\"tmp_tpahd\" style=\"visibility:hidden;height:1px;\"></div>");
						}
						$( "#tmp_tpahd" ).autocomplete({
							source: function( request, response ) {
									    $.getJSON( zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead", {
										term: request.term
									    }, response );
									},
							select: function(event, ui){ var uv = ""+ui.item.value; otext.setText(uv); otext.exitEditing(); canvas.renderAll(); return false; $("#div_tmp_tpahd").remove();  }
						});							
					}
					//console.log(otext);
					$("#div_tmp_tpahd").css({"top":tp,"left":lft});
					//
					$("#tmp_tpahd").autocomplete( "search", ""+txt );
				});
				
				//if prv drawing img is set				
				if(setImageDataCanvas_flg == 1){canvas.selection = false;}
		}
		
		if(global["mode"]=="Image"){
			var imgType = global["img_title"];
			drawFinding(imgType, pointer.x,  pointer.y);			
		}		
	}    
  },
  
  'mouse:move': function(ev) {
    //var text = document.createTextNode(' MouseMove ');
    //info.insertBefore(text, info.firstChild);
	if (!isDown||global["selection"]==1) return;	
	var pointer = canvas.getPointer(ev.e);
	if(global["mode"]=="Line" || global["mode"]=="Arrow"){
		
		
		if(global["mode"]=="Arrow"){
		var strk = canvas.freeDrawingBrush.width || 5;
		var headlen = 5*strk;	// length of head in pixels
		var angle = Math.atan2(pointer.y-starty,pointer.x-startx);
			
			line.set({ x2: pointer.x, y2: pointer.y });	
			arrow[0].set({ x1: pointer.x, y1: pointer.y, x2: pointer.x-headlen*Math.cos(angle-Math.PI/6), y2: pointer.y-headlen*Math.sin(angle-Math.PI/6) });
			arrow[1].set({ x1: pointer.x, y1: pointer.y, x2: pointer.x-headlen*Math.cos(angle+Math.PI/6), y2: pointer.y-headlen*Math.sin(angle+Math.PI/6) });			
			
			
			//group_arrow.item(0).set({ x2: pointer.x, y2: pointer.y });	
			//group_arrow.item(1).set({ x1: pointer.x, y1: pointer.y, x2: pointer.x-headlen*Math.cos(angle-Math.PI/6), y2: pointer.y-headlen*Math.sin(angle-Math.PI/6) });
			//group_arrow.item(2).set({ x1: pointer.x, y1: pointer.y, x2: pointer.x-headlen*Math.cos(angle+Math.PI/6), y2: pointer.y-headlen*Math.sin(angle+Math.PI/6) });				
			
		}else{
			line.set({ x2: pointer.x, y2: pointer.y });	
		}
		
		canvas.renderAll();
	}else if(global["mode"]=="Eraser"){
		//console.log(pointer.x, pointer.y);
		canvas.selection = false;
		//select a object		
		var flg_1=0;
		canvas.forEachObject(function(o) {		  
		  if(flg_1==0&&o.containsPoint(pointer)){  //make selection for eraser
				//o.set({padding:10, borderColor: 'red',cornerColor: 'green',cornerSize: 6,hasControls: false, hasRotatingPoint:false });
				//canvas.setActiveObject(o);								
				var o_glp= o.getLocalPointer(ev, pointer); 
				if(o.type=="cGroup"){				
					remove_group_obj(o,pointer.x,pointer.y);
				}else{
				//console.log("local",o_glp);
				erase_obj_pixel(o,0,o_glp.x,o_glp.y);
				}	
				flg_1=1;					
			  }	
		});
		
		
	}	
  },
  'mouse:up': function(ev) {
  
    isDown = false;
    canvas.selection = true;    
    var pointer = canvas.getPointer(ev.e); 

    if(global["mode"]=="Image"){
		//clearSelectionEl.onclick();
		//canvas.deactivateAll().renderAll();
		//global["mode"]="Image";
	}

	if(global["mode"]=="Eraser"){ 
		/*	
		//start selection
		canvas.selection = true;		
		canvas.forEachObject(function(o) {
		  o.selectable = true;		  
		  canvas.renderAll();	
		});	
		*/
	}	
    
    //selection: no working
    
    //var text = document.createTextNode(' MouseUp '+pointer.x+" - "+pointer.y);
    //info.insertBefore(text, info.firstChild);  
    
  },
'object:selected': function(ev) {
    //var text = document.createTextNode(' MouseMove ');
    //info.insertBefore(text, info.firstChild);
    global["selection"]=1;	
	//alert("object selected"); 
	
  },
'selection:cleared': function(ev) {
    //var text = document.createTextNode(' MouseMove ');
    //info.insertBefore(text, info.firstChild);
    global["selection"]=0;    
    //alert("object selection cleared.");
  }  
});

//--

// some functions --

this.resetDrawing= function(){	
	clearEl.trigger("click");
};	

var remove_group_obj = function(o,x,y){	
	var size=10;	
   	var x1 = parseInt(x)+size, x2=parseInt(x)-size;
	var y1 = parseInt(y)+size, y2=parseInt(y)-size;	
	o.forEachObject(function(o1) {		
		if(o1.originalLeft<=x1&&o1.originalLeft>=x2 && o1.originalTop<=y1&&o1.originalTop>=y2){			
			o.remove(o1);
		}else{			
			var xl = o1.group.left+o1.left;
			var yt = o1.group.top+o1.top;				
			if(xl<=x1&&xl>=x2 && yt<=y1&&yt>=y2){
				o.remove(o1);
			}
		}
	});
};

var erase_obj_pixel = function(oImg,flg,px,py){	
	
	//oImg.filters.push(new fabric.Image.filters.Redify(10,10));
	//oImg.applyFilters(canvas.renderAll.bind(canvas));
	//fabric.Image.filters.Redify.fromObject(oImg);
	
	if(typeof(flg)!="undefined"&&flg==1){		
		oImg.applyErase(canvas.renderAll.bind(canvas));	
	}else{
		/*
		var o_br = oImg.getBoundingRect();
		var tx = parseInt(px - o_br.left);
		var ty = parseInt(py - o_br.top);
		*/
		//console.log(px,py,o_br.left,o_br.top,o_br.width,o_br.height,tx,ty);
		
		oImg.applyErase(canvas.renderAll.bind(canvas), px, py);
	}	
};

var drawFinding = function(imgTitle, px, py, dnse){	
	var imgType = imgTitle; //global["img_title"];
	
	if(!arrDrawIcon || !imgType || typeof(arrDrawIcon[imgType])=="undefined"){return 0;}
	
	
	var src = arrDrawIcon[imgType]["img"].src; //'ERM.png';
	var w = arrDrawIcon[imgType]["img"].width;
	var h = arrDrawIcon[imgType]["img"].height;
	var src2 = src.replace('drawicon','drawicon/L');			
	//fabric.util.loadImage(src2, function(img) {
	var oImg = new fabric.CImage(arrDrawIcon[imgType]["L_img"]);
	var w = parseInt(oImg.width/img_denominator);
	var h = parseInt(oImg.height/img_denominator);	
	oImg.set({ left: parseInt(px),   top: parseInt(py),  transparentCorners:true, cornerSize:5, width:w,height:h  });			  
	
	//test--
	//erase_obj_pixel(oImg,px, py);
	//test--
	
	canvas.add(oImg);
	//});
	
	//add names in menu
	if(arrDrawIcon[imgType]["name"]!=""){
		var strlen = parseInt(arrDrawIcon[imgType]["name"].length);
		arrMenu.push(arrDrawIcon[imgType]["name"]);				
		
		///add right click option here.--
		if(idoc_nolabelwicon!="1"){
		//Adaptive and dynamic drawing.  Once they click on the element on the pallet it should automatically label the element on the drawing and then pop-up the Smart Chart associated with the label
		var px = parseInt(px);				
		var py = parseInt(py)+50;
		if(py>parseInt(canvas.height)){ py = parseInt(py)-100; }
		if(px>(parseInt(canvas.width)-40)){ px = parseInt(px)-((strlen*10)); }
		addExamNmOnCanvas(arrDrawIcon[imgType]["name"],px,py,dnse);
		}
		///add right click option here.--
	}
	return 1;
};

this.autodraw_wh = [{"t":30,"l":10}, {"t":30,"l":parseInt(canvas.width/2)}];
this.autodraw = function(aod, aos){		
	
	var str = $("#hidDrwDataJson"+cnvsIndx).val();
	var ostr_json = (str!="") ? $.parseJSON( str ) : "" ;
	var arrSrc = [], arrTxt=[];
	if(ostr_json){
		//console.log(ostr_json["objects"]);
		$.each(ostr_json["objects"], function(i,v){
				if(v.type=="image"){
					arrSrc[arrSrc.length] =""+v.src;
				}
				if(v.type=="citext"){
					arrTxt[arrTxt.length] =""+v.text;						
				}
			});
	}
	
	var flag_added=0;
	var arr = [aod, aos];
	for(var z in arr){		
		var aod = arr[z];  
		if(aod.length>0){			
			for(var x in aod){
				//var find="ERM";
				var find = aod[x];
				find= exam_map(find,2);
				
				//check if already exists
				
				var tt = me.autodraw_wh[z].t;
				var tl = me.autodraw_wh[z].l;
				
				if(arrTxt.length>0 && arrTxt.indexOf(find)!=-1){continue;}				
				
				var res = drawFinding(find, tl,tt, 1);
				
				//console.log(find+" - "+tt+" - "+tl);
				
				if(res){					
					//console.log(find+" - "+tt+" - "+tl);
					me.autodraw_wh[z].t = me.autodraw_wh[z].t+100;
					if(me.autodraw_wh[z].t>400){ me.autodraw_wh[z].t=20; me.autodraw_wh[z].l=me.autodraw_wh[z].l+95; }
					flag_added=1;	
				}
			}
		}
	}
	
	//remove intersection
	//if(canvas.isTargetTransparent(canvas, tl, tt)){ console.log("true"); }else{}
	if(flag_added==1){
	canvas.forEachObject(function(obj) {
		canvas.forEachObject(function(obj2) {
			if (obj === obj2) return;
			if(obj.intersectsWithObject(obj2)){
				obj.animate('top', '-=30', { onChange: canvas.renderAll.bind(canvas) });	
				obj2.animate('top', '+=30', { onChange: canvas.renderAll.bind(canvas) });	
			}
		});
	});
	}
	
	me.save();
	
	//
	//var str = $("#hidDrwDataJson"+cnvsIndx).val();
	//console.log(str);
	
};

//Highlight toolicons
this.autoHighlight = function(aod, aos){
	var arr = [aod, aos];
	for(var z in arr){		
		var aod = arr[z];  
		if(aod.length>0){			
			for(var x in aod){
				//var find="ERM";
				var find = aod[x];
				find= exam_map(find,2);
				$(mc).find("span[title='"+escape(find)+"']").addClass("drwhighlight");				
			}
		}
	}
};

//
var exam_map = function(ndl, mode){
	var mval_1 = mval_2 = ""+ndl;
	switch(ndl){
		case "Peripheral Retinal Hemorrhage":	
		case "Retinal Hemorrhage":
			mval_1="PERIPHERAL RETINAL HEMORRHAGE";
			mval_2 = "Retinal Hemorrhage";
		break;
		case "Lattice Degeneration":
			mval_1="PERIPHERAL DEGENERATION/LATTICE DEGENERATION";			
		break;
		case "SPK Mild":
		case "Conjunctiva SPK":
			mval_1="CONJUNCTIVA SPK";			
			mval_2 = "SPK Mild";
		break;
		case "hemorrhageRed":
			mval_1="VITREOUS/HEMORRHAGE";			
		break;
		case "Pannus":
			//mval_1="";
			//mval_2 = "";
		break;
		case "Ulcer":
			mval_1="INFECTION/INFLAMMATION/ULCER";				
		break;
		case "RPE Changes":
			mval_1="AMD/RPE CHANGES";				
		break;
		case "Pucker":
			//mval_1="";
			//mval_2 = "";
		break;
		case "Horse Shoe Tear":
			//mval_1="";
			//mval_2 = "";
		break;
		case "Dry Degeneration":
			//mval_1="";
			//mval_2 = "";
		break;
		case "Nevus":
		case "Choroidal Nevus":
			mval_1="NEVUS";
			mval_2 = "Choroidal Nevus";
		break;
		case "Scar":
		case "Disciform Scar":
			mval_1="CORNEA/SCAR";			
			mval_2 = "Disciform Scar";
		break;
		case "Hard Exudate":
		case "Exudates":
			mval_1="DR/HARD EXUDATE";
			mval_2 = "Exudates";
		break;
		case "PRP":
		case "PRP Treatment(Laser)":
			mval_1="DR/PRP";
			mval_2 = "PRP Treatment(Laser)";
		break;
		case "Red Dot":
			//mval_1="";
			//mval_2 = "";
		break;
		case "Retinal Hemorrhage Dot":
			mval_1="PERIPHERAL RETINAL HEMORRHAGE";			
		break;
		case "Retinal Irregular Hemorrhage":
			//mval_1="";
			//mval_2 = "";
		break;
		case "CME":
			//mval_1="";
			//mval_2 = "";
		break;
		case "Drusen Mild":
			//mval_1="";
			//mval_2 = "";
		break;
		case "Drusen Moderate":
			//mval_1="";
			//mval_2 = "";
		break;
		case "Focal Treatment":
			//mval_1="";
			//mval_2 = "";
		break;
		case "MA":
			//mval_1="";
			//mval_2 = "";
		break;
	}
	
	if(mode == 1){return mval_1;}
	else if(mode == 2){return mval_2;}
	return "";
}

var change_elem_ids_draw_pu = function(){
	
	$("#div_sc_con_detail .symOpt1").each(function(){
			var str = $(this).html(); 
			var ar = str.match(/for=\"[a-zA-Z_0-9]+\"/g);
			if(typeof(ar)!="undefined" && ar.length>0){
				for(var x in ar){
					if(typeof(ar[x])!="undefined" && ar[x]!="link_BL" && ar[x]!=""){
						var tmp = ar[x].replace(/for=\"|\"/g, "");
						str = str.replace("for=\""+tmp+"\"", "for=\""+tmp+"_drwpusx\"");
						str = str.replace("id=\""+tmp+"\"", "id=\""+tmp+"_drwpusx\"");	
					}
				}
			}
			
			$(this).html(str);
		});
	
};

this.showSymptomPopUP = function(mval){	
		//
		if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){ return;}
		
		var symp = todoId = searchVal = "";
		
		if(typeof(mval)=="undefined" || mval==""){return;}
		
		//reset symptom --		
		if(typeof(arrDrawIcon[mval])!="undefined" && typeof(arrDrawIcon[mval]["symptom"])!="undefined" && arrDrawIcon[mval]["symptom"]!=""){
			mval= ""+arrDrawIcon[mval]["symptom"];				
		}else{		
			mval= exam_map(mval,1);
		}
		//reset symptom --
		
		
		searchVal = symp = encodeURI(mval);
		
		//alert(""+examName);
		
		//icd 1 or 9
		var vicd10 = $("#hid_icd10").val();
		if(typeof(vicd10)=="undefined"){			
			if(window.opener && window.opener.top.fmain){
			vicd10 = window.opener.top.fmain.$("#hid_icd10").val();	
			}			
		}		
		
		var url = zPath+"/chart_notes/requestHandler.php";
		var params = "elem_formAction=SmartChartDetail&symp="+symp+"&searchVal="+searchVal+"&drawingSymp=1&examName="+examName+"&icd10="+vicd10;
		
		$("#div_sc_con_detail").hide();
		$("#div_sc_con_detail, .modal-backdrop").remove();
		
		//alert(params);
		
		//
		//if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","divCanvas"+me.classId);		
		
		$.get(url, params,
			function(data){
				
				//
				//if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","divCanvas"+me.classId);
				
				//var  dw = window.open("", "ss", "width=200,height=200");
				//dw.document.write(data);
				
				if(data != ""){
					
					//close typeahead if any				
					//					
					//$("body").append("<div id=\"div_sc_con_detail\" "+ ">"+data+"</div>");					
					//$("#div_sc_con_detail").draggable({handle:"th"});
					$("body").append(data);
					
					//reset drawing elements ID
					change_elem_ids_draw_pu();
					
					$("#div_sc_con_detail").modal({'backdrop': "static", 'show': 'true'});
					//$("#idMultiPlanSCPop").draggable({handle:"#hdrMultiPlanSCPop"});
					//date				
					$( ".dacry input[type=text], .lacsci input[type=text], .ctmri input[type=text]" ).datepicker({dateFormat:"mm-dd-yy", showOn: "button"});

					// typeahead --
					if(typeof(cn_ta1_no_assess)!="undefined"){
					$( "#div_sc_con_detail textarea, #div_sc_con_detail input[type=text]" ).bind("focus", function(){ if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, '');};  }); 	
					}					
					// --	
					if(typeof(newET_setGray_v3)!="undefined"){	newET_setGray_v3(1);	}
					
				}else{
					///alert("Symptom '"+mval+"' not defined.");
					alertDrw("prmt_sym_not_def", "Symptom '"+mval+"' not defined.");					
				}
			}
		);		
	};
	
var alertDrw=function(did, dmsg){
	$("#"+did).remove();
	$("body").append("<div id=\""+did+"\" style=\"position:absolute;background-color:red;color:white;padding:2px;border:1px solid black;top:10%;left:40%;font-size:12px;font-weight:bold;\">"+dmsg+"</div>");
	$("#"+did).bind("click", function(){$(this).remove();});
	setTimeout(function(){ $("#"+did).remove(); }, 2000);
};	

this.setForceSave = function(flg){ if($('#divDrawing'+cnvsIndx).css("display")=="block"){me.forceSave=flg;}};

this.save = function(flg){	
	
	//remove prev image
	if(typeof(flg)!="undefined" && flg==1){	
		var objArray = canvas.getObjects();
		var tmpObject;
		for (var j = 0; j < objArray.length; j++) {
			if(objArray[j].id =="prev_back_img"){
			    canvas.remove(objArray[j]);
			    break;
			}
		}
	}
	
	clearSelectionEl.trigger("click");
	mc.trigger("mouseout");
	
};

this.setCanvasDataDB = function(){			
	strFileName = document.getElementById('hidDrawingTestImageP'+cnvsIndx).value;		
	if((strFileName != "") && (typeof(strFileName) != "undefined") && (strFileName != null) && (document.getElementById('hidImageCss'+cnvsIndx).value == "imgDB")){
				document.getElementById('divCanvas'+cnvsIndx).className = "imgDB imgLoad";
				//blNewControlHaveData = true;
				//me.arrBlCanvasHaveDrwaing[0] = false;
				//me.arrBlCanvasHaveDrwaing[1] = true;
			}

	var strImageData = (document.getElementById("hidOldAppletImaData"+cnvsIndx)) ? document.getElementById("hidOldAppletImaData"+cnvsIndx).value : "" ;
	
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
					document.getElementById('divCanvas'+cnvsIndx).className = "imgNoImage";
					document.getElementById('hidImageCss'+cnvsIndx).value = "imgNoImage";
					/*imgOriD.width = canvasObj.width;
					imgOriD.height = canvasObj.height;
					imgD.width = canvasObj.width;
					imgD.height = canvasObj.height;
					*/
					//ctx.drawImage(imgD, 0, 0, canvasObj.width, canvasObj.height);
					//fabric.util.loadImage(src2, function(img) {
					  var oImg = new fabric.CImage(imgD);
					  var w = parseInt(oImg.width);
					  var h = parseInt(oImg.height);	
					  oImg.set({ left: 0,   top: 0,  transparentCorners:true, cornerSize:5, width:w,height:h  });
					  canvas.add(oImg);
					//});
					
					//me.arrBlCanvasHaveDrwaing[0] = false;
					//me.arrBlCanvasHaveDrwaing[1] = true;
					//alert(imgOriD.width);
					//me.getSetOldImage(ctx, canvasObj, imgD);											
					clearInterval(tempInt);
				}
			}
		,1);
	}
	else{
		
		//alert("3");
		
		document.getElementById('divCanvas'+cnvsIndx).className = document.getElementById('hidImageCss'+cnvsIndx).value;
		
		//Add Image --
		//*
		var strImg = document.getElementById('hidImgDataFileName'+cnvsIndx).value;
		if(strImg!=""){				
			//document.writeln("<img src='"+imgD.src+"'>");
			imgPrevD.onload = function()
			{
				//if(ctx){						
					//ctx.drawImage(imgPrevD, 0,0);	
					var oImg = new fabric.CImage(imgPrevD);
					var w = parseInt(oImg.width);
					var h = parseInt(oImg.height);	
					oImg.set({ left: 0,   top: 0,  transparentCorners:true, cornerSize:5, width:w,height:h  });
					canvas.add(oImg);
					//me.arrBlCanvasHaveDrwaing[0] = false;
					//me.arrBlCanvasHaveDrwaing[1] = true;	
				//}
			}				
		}
		//*/
		//Add Image --
		
	}
};

this.drwInit = function(){	
	//check if json data exists then load it on canvas
	var str = $("#hidDrwDataJson"+cnvsIndx).val();
	if(typeof(str)!="undefined" && str!=""){		
		setJsonDataCanvas(str);
	}else{
		var strPrevD=$('#hidImgDataFileName'+cnvsIndx).val();		
		setImageDataCanvas(strPrevD);
		//alertDrw("prmt_prev_img_ref", "Please redraw all objects of this image!");
	}	
};

this.setStrImgLen = function(){	
	strDataDefLength = this.strImgLen();
};

this.refresh_bg_img_toolbar = function(){
	backgroundImgEl = $('#'+c+' #toolTop span.toolImg');	
	backgroundImgEl.bind("click", function(){
		me.setbgimg(this);
	});
}

this.loadTestImage = function(imgPath, testName, testId, performedTestImagePath){
		var newImage = "url("+imgPath+")";
		clearEl.trigger("click");		
		//me.arrBlCanvasHaveDrwaing[0] = true;
		//me.makeCanvasActive();
		//me.arrBlCanvasHaveDrwaing[1] = true;
		document.getElementById("hidDrawingTestName"+cnvsIndx).value = testName;
		document.getElementById("hidDrawingTestId"+cnvsIndx).value = testId;
		document.getElementById("hidDrawingTestImageP"+cnvsIndx).value = performedTestImagePath;
		document.getElementById('hidImageCss'+cnvsIndx).value = "imgDB";
		document.getElementById("divCanvas"+cnvsIndx).style.backgroundImage = "";		
		document.getElementById("divCanvas"+cnvsIndx).style.backgroundImage = newImage;
		
		//document.getElementById("divTestImagesMain").style.display = "none";
		//document.getElementById("divTestImages").style.display = "none";
		$("#testImgModal").modal("hide");
		$("#testImgModal, .modal-backdrop").remove();
		
		//imgLength
		strDataDefLength = this.strImgLen();
		//alert(strDataDefLength);
		//window.status=strDataDefLength;
		
	}

var makeButtonActive = function(targ){	
	//clear past selection
	canvas.defaultCursor = canvas.moveCursor = 'default';
	$(mc).find("span.drwactive").removeClass("drwactive");	
	$(targ).addClass("drwactive").removeClass("drwhighlight");
	drawingLineWidthEl.val(1).trigger("change");
	if(global["mode"]=="Eraser"){
		//stop selection
		canvas.selection = true;		
		canvas.forEachObject(function(o) {
		  o.selectable = true;		  
		  canvas.renderAll();	
		});	
	}	
};

var getJsonDataCanvas = function() {
	//var myWindow = window.open("","MsgWindow","width=200,height=100, resizable=1");
	//myWindow.document.write(""+JSON.stringify(canvas));
	var ret = ""+JSON.stringify(canvas);
	var rep = escapeRegExp(window.location.protocol+"//"+window.location.host);
	var re = new RegExp(rep,"g");
	ret = ret.replace(re,"");	
	return ""+ret;
   };

var setImageDataCanvas_flg = 0;   
var setJsonDataCanvas = function(json) {  canvas.loadFromJSON(json, canvas.renderAll.bind(canvas), function(o, object){    if(object.type=="c-image"){setTimeout(function(){ strDataDefLength = me.strImgLen(); erase_obj_pixel(object,1); },100);}else{ strDataDefLength = me.strImgLen(); /*erase_obj_pixel(object,1);*/ }});  if($('#hidImageCss'+cnvsIndx).val() == "imgLaCanvas"){	setTimeout(function(){me.setCDRation(); strDataDefLength = me.strImgLen();},1000);};   };
var setImageDataCanvas = function(str){ str=$.trim(str);  if(str!=""){ fabric.CImage.fromURL(""+str, function(oImg) {  oImg.set({lockMovementX:true,lockMovementY:true,lockRotation:true,lockScalingX:true,lockScalingY:true,lockUniScaling:true });  /*oImg.id="prev_back_img";*/  canvas.add(oImg);  if($('#hidImageCss'+cnvsIndx).val() == "imgLaCanvas"){ setTimeout(function(){me.setCDRation(oImg);},1000);};  setImageDataCanvas_flg = 1;strDataDefLength = me.strImgLen();  });}else{ if($('#hidImageCss'+cnvsIndx).val() == "imgLaCanvas"){ setTimeout(function(){me.setCDRation();strDataDefLength = me.strImgLen();},1000);}; }};
	
var hideCanvasMenu = function(){ $(".drw_menu").remove(); };
var addExamNmOnCanvas = function(txt,px,py,doshowexm){ 
	if(txt!=""){
		var iTextM = new fabric.Citext(txt, {
								      left: parseInt(px),
								      top: parseInt(py),
								      fontSize:textFontSizeEl.val(),fontFamily: fontFamilyEl.val(), fill: drawingColorEl.val()
								    });
		canvas.add(iTextM);
		
		if(typeof(doshowexm)!="undefined" && doshowexm=="1"){ return; }
		//Show Smart PoP UP		
		if((me.arrSympDone_showSymptomPopUP).indexOf(txt)==-1){
			me.arrSympDone_showSymptomPopUP[me.arrSympDone_showSymptomPopUP.length] = ""+txt;
			me.showSymptomPopUP(txt);
		}
	}
};

var showCanvasMenu = function(e){ 
		var pointer = canvas.getPointer(e);	
		var str="";
		if(arrMenu.length>0){		
			arrMenu = jQuery.unique( arrMenu );
			arrMenu.sort();
			for(var x in arrMenu){
				if(typeof(arrMenu[x])!="undefined" && arrMenu[x]!=""){
					str+="<li>"+arrMenu[x]+"</li>";
				}
			}
			
			if(str!=""){
				str="<ul>"+str+"</ul>";	
			}
			
			$("body").append("<div id=\"cmenu\" class=\"drw_menu\" >"+str+"</div>");
			var sx=parseInt(pointer.x)+90,sy=parseInt(pointer.y)+90;
			$(".drw_menu").css({"top":sy+"px","left":sx+"px","display":"block"});
			$(".drw_menu ul li").bind("click", function(){ var txt = $(this).html();    hideCanvasMenu();  					
					addExamNmOnCanvas(txt,pointer.x,pointer.y);						
				});
		}
	};

this.testme = function(){
		alert("Test");
	};
/*	
this.clearDrawCanvas = function(){	
	//makeButtonActive(this); canvas.clear();  me.save();
};
*/	

this.setCDRation = function(oPrvImg){
		var strCDRatioOD = strCDRatioOS= "";
		if((document.getElementById("hidCDRationOD")) && (document.getElementById("hidCDRationOS"))){
			strCDRatioOD = document.getElementById("hidCDRationOD").value;
			strCDRatioOS = document.getElementById("hidCDRationOS").value;
		}
		
		//
		if(strCDRatioOD == "" && strCDRatioOS == ""){	return;	}
		
		//clear previous CD text		
		canvas.forEachObject(function(o) {			
			if(o.type == "citext" && o.text.indexOf("C:D:")!=-1){				
			    canvas.remove(o);
			}
		});		
		
		//--
		//Add new
		if((strCDRatioOD != null) && (strCDRatioOD != "")){
			//clear prv cd
			if(oPrvImg && typeof(oPrvImg)!="undefined"){			
				erase_obj_pixel(oPrvImg,0,160, 410);
				erase_obj_pixel(oPrvImg,0,180, 410);
				erase_obj_pixel(oPrvImg,0,200, 410);
			}
			//add new
			var text = "C:D: "+ strCDRatioOD;			
			var x = 150;
			var y = 420;			
			var tmp = new fabric.Citext(text, {
						      left: x,
						      top: y,
						      fontFamily: 'Arial',
						      fill: '#171717',
						      fontSize: '14'	
						    });	
			canvas.add(tmp);
		}
		if((strCDRatioOS != null) && (strCDRatioOS != "")){
			//clear prv cd
			if(oPrvImg && typeof(oPrvImg)!="undefined"){			
				erase_obj_pixel(oPrvImg,0,510, 420);
				erase_obj_pixel(oPrvImg,0,530, 420);
				erase_obj_pixel(oPrvImg,0,550, 420);
			}
			//add new
			var text = "C:D: "+ strCDRatioOS;
			var x = 500;
			var y = 420;			
			var tmp = new fabric.Citext(text, {
						      left: x,
						      top: y,
						      fontFamily: 'Arial',
						      fill: '#171717',
						      fontSize: '14'	
						    });	
			canvas.add(tmp);
		}	
	};
	
this.strImgLen = function (){	
		var strData = ocan.toDataURL("image/png");
		return strData.length;
	};
	
this.strImgEmptyLen = function (){	return (navigator.userAgent.indexOf("Chrome")!=-1) ? 9358 : 8066; };
	
var setActiveProp = function(name, value) {	
  var object = canvas.getActiveObject();
  if (!object) return;

  object.set(name, value).setCoords();
  canvas.renderAll();
}

var setActiveStyle = function(styleName, value, object) {
  object = object || canvas.getActiveObject();
  if (!object) return;

  if (object.setSelectionStyles && object.isEditing) {
    var style = { };
    style[styleName] = value;
    object.setSelectionStyles(style);
    object.setCoords();
  }
  else {
    object[styleName] = value;
  }

  object.setCoords();
  canvas.renderAll();
};

var getActiveStyle = function(styleName, object) {
  object = object || canvas.getActiveObject();
  if (!object) return '';

  return (object.getSelectionStyles && object.isEditing)
    ? (object.getSelectionStyles()[styleName] || '')
    : (object[styleName] || '');
};

this.setbgimg = function(obj, frc, drw_tp) {		
		var type = obj.title;
		var ask;		
		if(idocdraw_warning_flg==0|| (typeof(frc)!="undefined" && frc==1) ){			
			var strData = ocan.toDataURL("image/png");			
			if(strData.length!=strDataEmptyLength){			
				
				//ask =  confirm("Drawing will clear. Would you like to precede chosen Drawing Image?");
				fancyConfirm("Drawing will clear. Would you like to precede chosen Drawing Image?", "", "top.setbgimg_confirm('1', '"+cnvsIndx+"', '"+type+"', '"+frc+"', '"+drw_tp+"');", "top.setbgimg_confirm('0', '"+cnvsIndx+"', '"+type+"', '"+frc+"', '"+drw_tp+"');");
				idocdraw_warning_flg=1;
				return -1;
				
			}else{ask = true;}	
			idocdraw_warning_flg=1;
		}
		else{
			ask = true;
		}
		if(ask == true){
			cnfrm_draw_type=1;
			clearEl.trigger("click");
			//--
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
				case "ON_MA_Fundus":
					img = "imgOnMaCanvas";
				break;				
				default:
					
					var t_bgimg = $(obj).data("bgimg");
					if(typeof(t_bgimg)!="undefined" && t_bgimg!=""){
						var testName=testId=performedTestImagePath="";
						performedTestImagePath=t_bgimg.replace(zPath+"/main/uploaddir","");
						if(examName == "LA"){
							testName	= "LA_DSU";
							testId=$(":hidden[name=elem_laId]").val();
						}else if(examName == "Gonio"){
							testName	= "IOP_GON_DSU";
							testId=$(":hidden[name=elem_gonioId]").val();
						}else if(examName == "Fundus"){
							testName	= "FUNDUS_DSU";
							testId=$(":hidden[name=elem_rvId]").val();
						}else if(examName == "SLE"){
							testName	= "SLE_DSU";
							testId=$(":hidden[name=elem_sleId]").val();
						}else if(examName == "External"){
							testName	= "EXTERNAL_DSU";
							testId=$(":hidden[name=elem_eeId]").val();
						}else if(examName == "EOM"){
							testName	= "EOM_DSU";
							testId=$(":hidden[name=elem_eomId]").val();
						}	
						
						//alert("F: "+t_bgimg+"\n"+testName+"\n"+testId+"\n"+performedTestImagePath+"\n"+zPath+"main/uploaddir");		
						me.loadTestImage(t_bgimg, testName, testId, performedTestImagePath);
						return 0;
					}			
					
				break;
			}
			//---
			document.getElementById('divCanvas'+cnvsIndx).className = "";
			document.getElementById('hidImageCss'+cnvsIndx).value = "";			
			document.getElementById('hidDrawingChangeYesNo'+cnvsIndx).value = "yes";
			document.getElementById('hidDrawingTestName'+cnvsIndx).value = "";
			document.getElementById('hidDrawingTestId'+cnvsIndx).value = "";
			document.getElementById('hidDrawingTestImageP'+cnvsIndx).value = "";
			document.getElementById('hidImagesData'+cnvsIndx).value = "";			
			document.getElementById("divCanvas"+cnvsIndx).style.backgroundImage = "";			
			document.getElementById('hidImageCss'+cnvsIndx).value = img;
			document.getElementById('divCanvas'+cnvsIndx).className = img;			
			//--
			//
			if(document.getElementById('hidImageCss'+cnvsIndx).value == "imgLaCanvas"){
				this.setCDRation();
			}
		}
		return 0;
	  };
	  
var upload_scan_cam = function(type){
		var examId = $("#idoc_intDrawingExamId").val(); //set in main exam
		var formId = $("#idoc_intDrawingFormId").val(); //set in main exam
		var scanUploadfor = $("#idoc_strScanUploadfor").val(); //set in main exam	
		doScanUpload(examId, formId, type, scanUploadfor, cnvsIndx);
	};	  

//--
  
  var clearEl = $('#'+c+'  span[title="Clear Drawing"]'), //#clear-canvas
	penEl = $('#'+c+'  span[title=Pencil]'),//#pencil-canvas
	brushEl = $('#'+c+'  span[title=Brush]'),//#pencil-canvas
	spreyEl = $('#'+c+'  span[title="Spray Color"]'), //#spray-canvas
	//stopdrawEl = $('#'+c+'  #stop-drawing-canvas'),
	drawingColorEl = $('#'+c+'  #drawing-color'),
	circleCanvasEl = $('#'+c+'  span[title="Draw Circle"]'), //#circle-canvas
	remselCanvasEl = $('#'+c+'  span[title="Remove Selected"]'),//#remsel-canvas //Eraser
        eraserCanvasEl = $('#'+c+'  span[title="Eraser"]'), //Eraser
	clearSelectionEl = $('#'+c+'  span[title="Clear Selection"]'),	//#clear-select-canvas
	circleFillCanvasEl = $('#'+c+'  span[title="Draw Filled Circle"]'),//#circle-fill-canvas
	ovalCanvasEl = $('#'+c+'  span[title="Draw Ellipse"]'),//#oval-canvas
	ovalFillCanvasEl = $('#'+c+'  span[title="Draw Filled Ellipse"]'),//#oval-fill-canvas
	lineCanvasEl = $('#'+c+'  span[title="Draw Line"]'),//#line-canvas
	arrowCanvasEl = $('#'+c+'  span[title="Draw Arrow"]'),//#arrow-canvas
	drawingLineWidthEl = $('#'+c+'  #drawing-line-width'),
	selectionCanvasEl = $('#'+c+'  span[title=Select]'),//#selection-canvas
	arcCanvasEl = $('#'+c+'  span[title="Draw Arc"]'),//#arc-canvas
	rectCanvasEl = $('#'+c+'  span[title="Draw Rectangle"]'),//#rect-canvas
	rectRoundCanvasEl = $('#'+c+'  span[title="Draw Rounded Rectangle"]'),//#rect-round-canvas
	rectFilledCanvasEl = $('#'+c+'  span[title="Draw Filled Rectangle"]'),//#rect-fill-canvas
	rectRoundFilledCanvasEl = $('#'+c+'  span[title="Draw Filled Rounded Rectangle"]'), //#rect-fill-round-canvas
	textCanvasEl = $('#'+c+'  span[title="Write Your Text"]'),//#text-canvas
	imgCanvasEl = $('#'+c+' #divToolsRight span.toolIcon'),
	backgroundImgEl = $('#'+c+' #toolTop span.toolImg'),
	fontFamilyEl = $('#'+c+' #textOptions_font #font-family'),
	textFontSizeEl = $('#'+c+' #textOptions_font_size #text-font-size'),
	colorToolsEl = $('#'+c+' .idoc-colors span.colorSpanBorder'),
	textFontBoldEl = $('#'+c+' #textOptions_font_deco #text-font-b'),
	textFontItelicEl = $('#'+c+' #textOptions_font_deco #text-font-i'),
	textFontLlineEl = $('#'+c+' #textOptions_font_deco #text-font-l'),
	objSendBackEl = $('#'+c+' #divSendBackForth #obj-send-b'),
	objSendFrontEl = $('#'+c+' #divSendBackForth #obj-send-f'),
	chkDrwNE = $('#'+c+' input[id*=elem_drwNE]'),
	plusDrwEl = $('#'+c+' #plusDrw'),
	DelDrwEl = $('#'+c+' #DelDrw'),
	drwBckGrndMoreEl = $('#'+c+' .nextButton'),
	btChooseTextImage = $('#'+c+' #btChooseTextImage'),
	scan_drw_El = $('#'+c+'  span[title="Scan Drawing Image"]'),//#Scan Drawing Image
	upload_drw_El = $('#'+c+'  span[title="Upload Drawing Image"]'),//#Upload Drawing Image
	upload_drw_cam_El = $('#'+c+'  span[title="Upload Drawing Image - Camera"]')//#Upload Drawing Image - Camera	
	//testCanvasEl = $('#'+c+'  #test-canvas'),
	//getJsonCanvasEl = $('#'+c+'  #get-json-canvas'),
	//setJsonCanvasEl = $('#'+c+'  #set-json-canvas')
	;	
	
	upload_drw_cam_El.bind("click", function(){	upload_scan_cam("upload-WEBCAM"); });
	
	scan_drw_El.bind("click", function(){upload_scan_cam("scan");	});
	
	upload_drw_El.bind("click", function(){upload_scan_cam("upload");});
	
	btChooseTextImage.bind("click", function(){
			$("#img_load, #divTestImages").show();			
			var patId = $(":hidden[name=elem_patientId]").val();			
			if(typeof(patId)=="undefined"||patId==""){ patId = sess_pt;}
			if(typeof(patId)=="undefined"||patId==""){console.log("Err: elem_patientId is undfined.");}
			//var url = 'iDoc-Drawing/CLSAJAXTestDrawing.php?patId='+patId+'&canvasId='+cnvsIndx+'&mod=get';	
			var url = zPath+'/chart_notes/requestHandler.php?elem_formAction=CLSAJAXTestDrawing&patId='+patId+'&canvasId='+cnvsIndx+'&mod=get';		
			$.get(url,function(data){
					var strResponseVal = data;
					if(strResponseVal != "NOIMAGES"){
						var arrResponseVal = strResponseVal.split("~~");
						var htmlDiv = arrResponseVal[0];
						gbImages = arrResponseVal[1];				
						//document.getElementById("divTestImagesMain").style.display = "block";				
						//document.getElementById("divTestImagesMain").innerHTML = htmlDiv;
						document.getElementById("img_load").style.display = "none";
						//document.getElementById("cornea_od_desc_1").value = htmlDiv;	
						//$("#divImages  span.closeBtn").bind("click", function(){ $("#divImages").remove();});
						$("#testImgModal").modal("hide");
						$("#testImgModal, .modal-backdrop").remove();
						$("body").append(htmlDiv);
						$("#testImgModal").modal("show");	
					}else{
						document.getElementById("img_load").style.display = "none";
						document.getElementById("divTestImagesMain").style.display = "none";
						document.getElementById("divTestImages").style.display = "none";
						//top.fAlert("Sorry, No Test image found at server!");
						alertDrw("prmt_no_test_img", "Sorry, No Test image found at server!");
						//console.log(strResponseVal);
					}
				});
		});
	
	drwBckGrndMoreEl.bind("click", function(){  //will add more template through ajex later: pending now: also add events
			if($("#dv_temp_showIcons").length<=0){
				var html=$("#"+c+" #toolTop").html();
				var str="<div id=\"dv_temp_showIcons\" data-indx=\""+cnvsIndx+"\"  class=\"fllt\" style=\"position:absolute;border:1px solid red;border:1px dashed #999; width:550px; text-align:left;top:0px;left:0px;background-color:white; \" >"+					
						html+
						"</div>";
				$("body").append(str);
				
				$("#dv_temp_showIcons  span.toolImg").bind("click", function(){						
						me.setbgimg(this);
					});	
				
				//backgroundImgEl
				
				var o = $("#"+c+" #toolTop").position();
				var o1 =$("#dv_temp_showIcons");
				//window.status=""+o1[0].offsetWidth+" - "+o1[0].offsetHeight;
				//alert("left: "+o.left+"px, top:"+o.top+"px");
				
				$("#dv_temp_showIcons").css({"left":o.left+"px","top":(o.top+o1[0].offsetHeight)+"px"});		
				
			}else{
				$("#dv_temp_showIcons").remove();			
			}	
		});
	
	plusDrwEl.bind("click", function(){ 
			var cnvsIndx_Nxt = parseInt(cnvsIndx) + 1;
			var nextDivId = "divDrawing" + cnvsIndx_Nxt;
			if($("#"+nextDivId).length>0){
				//$("#"+nextDivId).show();
				//
				$("#"+nextDivId).removeClass("hidden");	
				/*
				var strMasterDiv= $("#strMasterDiv").val();
				if(typeof(strMasterDiv)!="undefined"){
						$("#"+strMasterDiv).animate({scrollTop: document.getElementById(nextDivId).offsetTop,"opacity": "0.5"},'slow', function (){$("#"+strMasterDiv).css({"opacity":""})});
				}*/
			}
			else{ top.fAlert("Multi drawings limit is exceeding. Maximum allowed is "+drawCntlNum+"!"); 	}
		
		});
		fAlertCnfrm = false;
		DelDrwEl.bind("click", function(){
			thisId = $(this).prop('id');
			//console.log("P", nextDivId, mc, cnvsIndx);
			flg = false;
			if(fAlertCnfrm==false){
				top.fancyConfirm('Are you sure to delete it?', '', 'fAlertCnfrm=true;$("#'+c+'  #'+thisId+'").trigger("click");');
				return;
			}
			else{
				fAlertCnfrm=false;
				flg = true;
			}
			if(flg==true){	
				var nextDivId = "divDrawing" + cnvsIndx;
					
				//Save
				var testId="";	
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
				}			
				
				if(testId!=""){
					var id = $(":hidden[name='"+testId+cnvsIndx+"']").val();				
					if(id!="" && id!=0){					
						$.post("saveCharts.php",{"elem_saveForm":"DeleteDrawing","hidDrawingId":id},function(data){});
						$(":hidden[name='"+testId+cnvsIndx+"']").val(0);
						strDataDefLength = strDataEmptyLength;//make default value empty	
					}
				}
				
				//if($("div[id*=divDrawing]").length>1){
				if(cnvsIndx>0){
					//$("#"+nextDivId).remove();
					clearEl.trigger("click");
					//$("#"+nextDivId).hide();
					$("#"+nextDivId).addClass("hidden");	
				}else{	
					clearEl.trigger("click");
				}
			}
		}); 		
	
	
	chkDrwNE.bind("click", function(){   
			var reply = "Not Examined";
			var wh = this.id.indexOf("OD")!=-1 ? "OD" : "OS";	
			if(this.checked){
				if(wh=="OD"){
					var chk = this.id.replace("OD","OS");	
				}else{
					var chk = this.id.replace("OS","OD");	
				}
				
				if($("#"+chk).prop("checked")){
					top.fAlert("Invalid operation.  Both eyes cannot be selected as Not Examined.");
					this.checked=false;
					return;
				}		
			}else{
				if(typeof(iTextNE)!="undefined"){ canvas.remove(iTextNE);  }else{
					var objArray = canvas.getObjects();
					var tmpObject;
					for (var j = 0; j < objArray.length; j++) {						
						if((objArray[j].type =="citext" || objArray[j].type == "i-text") && objArray[j].text==reply){
						    canvas.remove(objArray[j]);
						    break;
						}
					}	
				}
			}
			
			//--
			var centerY = ocan.height - 300;
			if(wh=="OD"){
				var writeAtX = 80;
				var writeAtY = ocan.height-40;				
				var stPoint=stPoint2=0;
				var endPoint=ocan.width/2;
				var centerX = ocan.width / 5;
			}else{				
				
				var writeAtX = 440;
				var writeAtY = ocan.height-40;
				
				var stPoint=ocan.width/2;
				var endPoint=ocan.width;
				var stPoint2=0;
				var centerX = ocan.width / 1.43;
			}
			
			//if prv drawing img is set				
			//if(setImageDataCanvas_flg == 1){
				
				canvas.forEachObject(function(o) {
					if(o.lockMovementX == true&&o.lockMovementY == true&&o.lockRotation == true&&
						o.lockScalingX == true&&o.lockScalingY == true&&o.lockUniScaling == true){							
						for(var inx=0;inx<12;inx++){	
							erase_obj_pixel(o,0,writeAtX+20*inx, ocan.height-25);
							erase_obj_pixel(o,0,writeAtX+20*inx, ocan.height-20);
						}
					}				  
				});
				
			//}
			
			if(this.checked==true){
				
				iTextNE = new fabric.Citext(reply, {
						      left: writeAtX,
						      top: writeAtY,
						      fontFamily: 'Arial',
						      fill: 'red',
						      size: '34'	
						    });	
				canvas.add(iTextNE);
			//}	
			}
			/*	
			me.arrBlCanvasHaveDrwaing[0] = true;
			me.makeCanvasActive();
			me.arrBlCanvasHaveDrwaing[1] = true;
			$("#divCanvas"+me.classId).triggerHandler("mouseout");			
			*/
			
		});
  
  //alert(colorToolsEl.length);
  colorToolsEl.bind("click", function(){ var r = ""+$(this).attr("style").replace(/(background-color\:|\;)/g,""); if(r!=""){  $('#'+c+" #drawing-color").val(""+r);  $('#'+c+" #drawing-color").triggerHandler("change");} });  
  
  clearEl.bind("click", function() { /*me.clearDrawCanvas();*/ makeButtonActive(this); canvas.clear();  me.save(); }); 
  
  var brushTool = function(obj, lw){
	clearSelectionEl.trigger("click");  
	makeButtonActive(obj);  
	canvas.isDrawingMode = true;
	global["mode"]="";
	canvas.freeDrawingBrush = new fabric["C"+"Pencil" + 'Brush'](canvas);  
	if (canvas.freeDrawingBrush) {	
	      drawingLineWidthEl.val(lw).triggerHandler("change");
	      canvas.freeDrawingBrush.color = drawingColorEl.val();
	      canvas.freeDrawingBrush.width = parseInt(drawingLineWidthEl.val(), 10) || 1;
	      //canvas.freeDrawingBrush.shadowBlur = parseInt(drawingShadowWidth.value, 10) || 0;      
	}  
   };
  
  penEl.bind("click", function() { brushTool(this,1); }); 
  brushEl.bind("click", function() { brushTool(this,3); }); 
  spreyEl.bind("click", function() { 
	makeButtonActive(this);  
	canvas.isDrawingMode = true;
	global["mode"]="";
	canvas.freeDrawingBrush = new fabric["C"+"Spray" + 'Brush'](canvas);  
	if (canvas.freeDrawingBrush) {	
	      drawingLineWidthEl.val(15).triggerHandler("change");	
	      canvas.freeDrawingBrush.color = drawingColorEl.val();
	      canvas.freeDrawingBrush.width = parseInt(drawingLineWidthEl.val(), 10) || 1;
	      //canvas.freeDrawingBrush.shadowBlur = parseInt(drawingShadowWidth.value, 10) || 0;		
	}
  }); 
  eraserCanvasEl.bind("click", function() { 
		clearSelectionEl.trigger("click");
		makeButtonActive(this);
		canvas.isDrawingMode = false;
		global["mode"]="Eraser";
	  
		/*
		canvas.freeDrawingBrush = new fabric["Pencil" + 'Brush'](canvas);  
		if (canvas.freeDrawingBrush) {
		canvas.freeDrawingBrush.color = 'transparent';
		canvas.freeDrawingBrush.width = parseInt(drawingLineWidthEl.val(), 10) || 1;
		//canvas.freeDrawingBrush.shadowBlur = parseInt(drawingShadowWidth.value, 10) || 0;
		}
		*/	  
		//
		//console.log(, navigator.userAgent);
		canvas.defaultCursor = canvas.moveCursor = (navigator.userAgent.match(/(MSIE|rv\:11)/i)!=null) ? 'not-allowed' : "url(iDoc-Drawing/images/red.png) 11 11,not-allowed";
		 //(navigator.userAgent.match(/(MSIE)/i) != null) ? 'not-allowed' : " url('iDoc-Drawing/images/red.png')";
		//stop selection
		canvas.selection = false;		
		canvas.renderAll();
		canvas.forEachObject(function(o) {
		  o.selectable = false;		  
		  canvas.renderAll();	
		});	  
	  
	  });  
  
  //stopdrawEl.onclick = function() { canvas.isDrawingMode = !canvas.isDrawingMode; stopdrawEl.innerHTML = (canvas.isDrawingMode) ? 'Stop Drawing' : 'Start Drawing' ; };
  drawingLineWidthEl.bind("change", function() { canvas.freeDrawingBrush.width = parseInt(this.value, 10) || 1; /*this.previousSibling.innerHTML = this.value; */   });
  drawingColorEl.bind("change", function() {   canvas.freeDrawingBrush.color = this.value;  setActiveStyle('fill', this.value);  });
  circleCanvasEl.bind("click", function() { makeButtonActive(this);  canvas.isDrawingMode = false; global["mode"]="Circle"; global["Filled"]="0"; global["Oval"]="0";  });
  circleFillCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Circle"; global["Filled"]="1"; global["Oval"]="0";  });
  ovalCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Circle"; global["Filled"]="0"; global["Oval"]="1";  });
  ovalFillCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Circle"; global["Filled"]="1"; global["Oval"]="1";  });
  rectCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Rect"; global["RoundEdge"]="0"; global["Filled"]="0";  });
  rectFilledCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Rect"; global["RoundEdge"]="0"; global["Filled"]="1";  });
  rectRoundCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Rect"; global["RoundEdge"]="1"; global["Filled"]="0";  });
  rectRoundFilledCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Rect"; global["RoundEdge"]="1"; global["Filled"]="1";  });
  textCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Text"; });
  imgCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Image"; global["img_title"]=""+this.title; });
  remselCanvasEl.bind("click", function() {  
		var activeObject = canvas.getActiveObject(),
		activeGroup = canvas.getActiveGroup();

		if (activeGroup) {
		var objectsInGroup = activeGroup.getObjects();
		canvas.discardActiveGroup();
		objectsInGroup.forEach(function(object) {
		canvas.remove(object);
		});
		}
		else if (activeObject) {
		canvas.remove(activeObject);
		}
    });  
  
  clearSelectionEl.bind("click", function() { makeButtonActive(this); canvas.deactivateAll().renderAll(); global["selection"]=0;  });
  lineCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Line";  });
  arrowCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Arrow";  });
  selectionCanvasEl.bind("click", function() { clearSelectionEl.trigger("click"); makeButtonActive(this); global["mode"]=""; canvas.selection = true; canvas.isDrawingMode = false;  }); 
  arcCanvasEl.bind("click", function() { makeButtonActive(this); canvas.isDrawingMode = false; global["mode"]="Arc";  });
  backgroundImgEl.bind("click", function(){
	  me.setbgimg(this);
	});	
  
	//font-family  
	fontFamilyEl.bind("change", function() {  setActiveProp('fontFamily', this.value.toLowerCase()); });  
	textFontSizeEl.bind("change", function() {  setActiveProp('fontSize', parseInt(this.value, 10)); });
	textFontBoldEl.bind("click", function(){ setActiveProp('fontWeight',getActiveStyle('fontWeight') === 'bold' ? '' : 'bold'); });
	textFontItelicEl.bind("click", function(){ setActiveProp('fontStyle',getActiveStyle('fontStyle') === 'italic' ? '' : 'italic');  });
	textFontLlineEl.bind("click", function(){ var value = (getActiveStyle('textDecoration').indexOf('line-through') > -1) ? getActiveStyle('textDecoration').replace('line-through', '') : (getActiveStyle('textDecoration') + ' line-through');    setActiveProp('textDecoration', value); });

	///selection
	objSendBackEl.bind("click", function(){ var activeObject = canvas.getActiveObject();   if (activeObject) {     canvas.sendToBack(activeObject);    }  });
	objSendFrontEl.bind("click", function(){ var activeObject = canvas.getActiveObject();  if (activeObject) {     canvas.bringToFront(activeObject);   } });
	
	//div mouse out
	mc.bind("mouseout", function(){ 
			
			//check for not active drawing, deleted drawing
			if(strDataDefLength==0||this.tmp_del_procs_flag==1){this.tmp_del_procs_flag=0; return;}
			
			//clearSelectionEl.trigger("click");		
			var strData = ocan.toDataURL("image/png");		
			//console.log(strData.length+" == "+strDataDefLength);
			var strTestPath = $("#hidDrawingTestImageP"+cnvsIndx).val();
			var tmp = (strData.length!=strDataDefLength||strTestPath!=""||me.forceSave==1) ? strData : "" ;
			$("#hidCanvasImgData"+cnvsIndx).val(tmp);
			$("#hidDrwDataJson"+cnvsIndx).val(getJsonDataCanvas());
			var strjsndt = $("#hidDrwDataJson"+cnvsIndx).val();
			var ojsndt = JSON.parse(strjsndt);
			
			//
			//console.log($("#hidDrwDataJson"+cnvsIndx).val());
			
			//--		
			var arrBlCanvasHaveDrwaing=[];
			arrBlCanvasHaveDrwaing[0]=(strData.length!=strDataDefLength||me.forceSave==1) ? true : false; //changed
			//arrBlCanvasHaveDrwaing[1]=(strData.length>9358) ? true : false; //has drawing
			arrBlCanvasHaveDrwaing[1]=((typeof(ojsndt)!="undefined" && typeof(ojsndt.objects)!="undefined" && typeof(ojsndt.objects.length)!="undefined" && ojsndt.objects.length>0)) ? true : false; //has drawing
			if(arrBlCanvasHaveDrwaing[1]==false){ $("#hidCanvasImgData"+cnvsIndx).val(""); }
			//console.log("Changed:"+arrBlCanvasHaveDrwaing[0]+", Has Drawing:"+arrBlCanvasHaveDrwaing[1]+", Cur length:"+strData.length+", Def Length:"+strDataDefLength);
		
			//if Changed, alert
			if(arrBlCanvasHaveDrwaing[0] == true || me.forceSave==1){
				newET_drawingChanged();
				$("#hidDrawingChangeYesNo"+cnvsIndx).val("yes");
				//$("#hidRedPixel"+cnvsIndx).val(strData.length);
				//$("#hidGreenPixel"+cnvsIndx).val(strDataDefLength);
			}else{
				$("#hidDrawingChangeYesNo"+cnvsIndx).val("");	
			}
			
			//fix for wrong data--
			/*
			console.log("---Start--");
			console.log(cnvsIndx);
			console.log(ojsndt.objects.length);
			console.log(me.forceSave);			
			console.log(strData.length);
			console.log(strDataDefLength);
			console.log($("#hidDrawingChangeYesNo"+cnvsIndx).val());
			console.log($("#hidDrwDataJson"+cnvsIndx).val());
			console.log($("#hidCanvasImgData"+cnvsIndx).val());
			//*/
			/*
			if($("#hidDrawingChangeYesNo"+cnvsIndx).val() == "yes" && 
				$.trim($("#hidCanvasImgData"+cnvsIndx).val())=="" &&
				$.trim($("#hidDrwDataJson"+cnvsIndx).val())!='{"objects":[],"background":""}'){
					
					console.log()
					
					//$("#hidDrawingChangeYesNo"+cnvsIndx).val("");
					
				}
			*/
			//console.log($("#hidDrwDataJson"+cnvsIndx).val());
			
			//fix for wrong data--
			
			
			if(!$("#divDrawing"+cnvsIndx).hasClass("hidden")){/*KnownIssue: if empty canvas is found it will not show positive*/
				if(arrBlCanvasHaveDrwaing[1] == true){
					document.getElementById("hidCanvasWNL").value += "no";
				}
				else{
					document.getElementById("hidCanvasWNL").value = "yes";
				}
				//--
				
				//set yellow flag for exam
				//to be enabled later: pending
				if(typeof(checkwnls)=="function"){
					checkwnls();
				}
			}	
		});
  
 // testCanvasEl.bind("click", function() {   });
  /*
   getJsonCanvasEl.bind("click", function() {   	
	var myWindow = window.open("","MsgWindow","width=200,height=100, resizable=1");
	myWindow.document.write(""+JSON.stringify(canvas));
   });
   setJsonCanvasEl.bind("click", function() {  var json=$('ta1').value; alert(json); canvas.loadFromJSON(json, canvas.renderAll.bind(canvas));   });  
   */

  if (canvas.freeDrawingBrush) {
    canvas.freeDrawingBrush.color = drawingColorEl.val();
    canvas.freeDrawingBrush.width = parseInt(drawingLineWidthEl.val(), 10) || 1;
    canvas.freeDrawingBrush.shadowBlur = 0;
  }
  
  this.setCanvasDataDB();
  strDataDefLength = this.strImgLen();
  strDataEmptyLength = this.strImgEmptyLen();
  penEl.trigger("click");
  
//*/

  
  
};

//functions--
function drawingInit(){
	for(var f=0;f<2;f++){
		var a1 = new idocdraw(''+idocdraw_defname+f);
		idocdraw_arr[f] = a1;
	}
	//var a1 = new idocdraw();
}
function loadTestImage(imgPath, testName, testId, performedTestImagePath, canvasId){
	canvasId = canvasId || "";	
	if(canvasId == ""){
		if(typeof(drawCntlNum)=='undefined'){drawCntlNum=25;}
		for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
			idocdraw_arr[intCounter].loadTestImage(imgPath, testName, testId, performedTestImagePath);
		}
	}
	else{
		idocdraw_arr[canvasId].loadTestImage(imgPath, testName, testId, performedTestImagePath);
	}
	delImages(gbImages);
}
function delImages(images){	
	if(typeof(images) != "undefined" && images != ""){
		var urlDel = 'iDoc-Drawing/CLSAJAXTestDrawing.php';
		var param='images='+images+'&mod=del';
		$.post(urlDel, param,function(data){
				var strResponseVal = data;
				gbImages = "";
			});
	}
}

function setAllDrawingToSave(){
	var l = idocdraw_arr.length;	
	for(var a=0; a<l;a++){		
		idocdraw_arr[a].setForceSave(1); 
	}
}

function idoc_savedrawing(){
	var l = idocdraw_arr.length;	
	for(var a=0; a<l;a++){		
		if(typeof(idocdraw_arr[a].save)!="undefined"){
		idocdraw_arr[a].save(1); //flg will remove background
		}	
	}
}

function idoc_refresh_bg_img_toolbar(){
	var l = idocdraw_arr.length;	
	for(var a=0; a<l;a++){
		idocdraw_arr[a].refresh_bg_img_toolbar();
	}
}

function idoc_autoDraw(str_od, str_os){
	var arr_od = (typeof(str_od)!="undefined" && str_od!="") ? str_od.split("\n") : [] ;	
	var arr_os = (typeof(str_os)!="undefined" && str_os!="") ? str_os.split("\n") : [] ;	
	//idocdraw_arr[0].autodraw(arr_od,arr_os);
	if(idocdraw_arr && idocdraw_arr[0] && typeof(idocdraw_arr[0].autoHighlight)!="undefined"){
	idocdraw_arr[0].autoHighlight(arr_od,arr_os);
	}	
	//console.log(arr_find.length);
}

function idoc_getFindings4Drw(){
	
	if(typeof(examName)=="undefined"||examName==""){return;}
	
	var str_od = str_os = "";
	var otab = getWcId("draw");
	var tod= $("#"+otab.div).find("textarea[name*=_od],textarea[name*=od_]").eq(0);
	var str_od = tod.val();
	var tos= $("#"+otab.div).find("textarea[name*=_os],textarea[name*=os_]").eq(0);
	var str_os = tos.val();
	
	if(typeof(examName)!="undefined" && examName!="" && examName != "EOM"){
		$(".tab-pane").each(function(index_pn){
				var fnm = "";
				$(this).find(".table-responsive .table-striped tr").each(function(index_tr){
						if(index_tr==0){ return 0; }
						//console.log(this.id, index_tr);
						
						var t = $(this).find("td:first-child").html();
						if(typeof(t)!="undefined" && t!=""){ fnm = ""+t; }
						else if(fnm!=""){ t=fnm;  }
						
						if(typeof(t)!="undefined" && t!="" && t!="Comments"){
							if(t.indexOf("span")!=-1||t.indexOf("<br>")!=-1||t.indexOf("label")!=-1){	t = t.replace(/(\<span\>.\<\/span\>)|(\<br\>)|(<span class=\"glyphicon glyphicon-menu-(up|down)\"><\/span>)|(<label onclick\=\"openSubGrp\(\'(.*)\'\)\">)|(<\/label>)/g,""); }	
							t = $.trim(t);
							
							if(t!=""){
							var wh="",flg=0;
							$(this).find(":input[name][type!=hidden]").each(function(indx){
									if((this.type=="checkbox"||this.type=="radio")){
										if(this.checked){flg=1;}
									}else if(this.value!=""){
										flg=1;
									}
									
									if(flg==1){								
										if(this.name.toLowerCase().indexOf("_od")!=-1||this.name.toLowerCase().indexOf("od_")!=-1){
											wh="_od";	
										}else if(this.name.toLowerCase().indexOf("_os")!=-1||this.name.toLowerCase().indexOf("os_")!=-1){
											wh="_os";	
										}
										if(wh!=""){
											return false;
										}
									}
								});
							if(flg==1){	
								if(wh=="_os"){
									if(str_os.indexOf(t)==-1){ if(str_os!=""){str_os+="\n";} str_os += ""+t+"";}
								}else if(wh=="_od"){
									if(str_od.indexOf(t)==-1){if(str_od!=""){str_od+="\n";} str_od += ""+t+"";}
								}
							}	
							}						
						}					
					});					
			});
			
		/*
		$(".tab-pane .table-responsive table-striped tr").each(function(indextr){
				
				$(this).find("td:first-child").value;
				console.log(indextr);
				
			
				
				//console.log(this.id);
				var t = ""+$(this).prev().html();
				t=$.trim(t);
				//console.log(this.id+" - "+t);
			
				if(typeof(t)!="undefined" && t!="" && t!="Comments"){
					
					if(t.indexOf("span")!=-1||t.indexOf("<br>")!=-1){	t = t.replace(/(\<span\>.\<\/span\>)|(\<br\>)/g,""); }
					
					var wh="",flg=0;
					$(this).find(":input[name][type!=hidden]").each(function(indx){
							if((this.type=="checkbox"||this.type=="radio")){
								if(this.checked){flg=1;}
							}else if(this.value!=""){
								flg=1;
							}
							
							if(flg==1){								
								if(this.name.toLowerCase().indexOf("_od")!=-1||this.name.toLowerCase().indexOf("od_")!=-1){
									wh="_od";	
								}else if(this.name.toLowerCase().indexOf("_os")!=-1||this.name.toLowerCase().indexOf("os_")!=-1){
									wh="_os";	
								}
								if(wh!=""){
									return false;
								}
							}
						});				
					
					if(flg==1){	
						if(this.id.indexOf("_os")!=-1||wh=="_os"){
							if(str_os.indexOf(t)==-1){ if(str_os!=""){str_os+="\n";} str_os += ""+t+"";}
						}else if(this.id.indexOf("_od")!=-1||wh=="_od"){
							if(str_od.indexOf(t)==-1){if(str_od!=""){str_od+="\n";} str_od += ""+t+"";}
						}
					}
				}
				
			});
			*/
			
		//console.log(str_od);
		//console.log(str_os);		
		
	}else{
		//for EOM
		var arr = [];
		arr["NPC"] = ":input[name*=elem_npc], :input[name*=ortho_desc]";

		arr["NPA"]	= ":input[name*=elem_npa], :input[name*=npa_desc]";

		arr["EOM"] = ":input[name*=elem_eom], :input[name*=full_desc]";

		arr["Abnormal"] = ":input[name*=elem_eomAbn]";

		arr["Horizontal"] = ":input[name*=elem_eomHori]";

		arr["Vertical"] = ":input[name*=elem_eomVerti]";

		arr["AV Patterns"] = ":input[name*=elem_eomAvp], :input[name*=elem_eomControl]";

		arr["Randot Stereo Test"] = ":input[name*=elem_ranSt], :input[name*=elem_stereo_SecondsArc]";

		arr["Color Vision Test"] = ":input[name*=elem_color], :input[name*=elem_comm_colorVis]";

		arr["Worth 4 Dot Test"] = ":input[name*=elem_w4dot], :input[name*=elem_comm_w4Dot]";

		arr["Anomalous Head Position"] = ":input[name*=elem_comments_AnoHead]";

		arr["Nystagmus"] = ":input[name*=elem_comments_Nystag]";

		arr["Ductions"] = "#divDuction :input[name]";

		for(var x in arr){			
			var ptrn = arr[x];
			var t = x;
			$(ptrn).each(function(indx){					
					if(this.type=="checkbox"){
						if(this.checked){							
							if(str_od.indexOf(t)==-1){ if(str_od!=""){str_od+="\n";} str_od += ""+t+"";}
						}
					}else if(this.value!=""){						
						if(str_od.indexOf(t)==-1){ if(str_od!=""){str_od+="\n";} str_od += ""+t+"";}
					}
				});
		}
		
		//
		str_os=str_od;
	}
	
	/*
	//R7 Feedback – 12/27/18
	//Please do not automatically add finding to Text boxes of the OD/OS in drawing
	// Insert Findings in text areas
	if(str_od!=""){				
		tod.val(""+str_od).trigger("click");
	}
	//
	if(str_os!=""){
		tos.val(""+str_os).trigger("click");
	}
	*/

	//auto draw
	idoc_autoDraw(str_od, str_os);
	
}

//Test --
function AJAXLoadDarwingData(id, strMasterDiv,fid,pid,eid,enm){	
	
	if(typeof(id)=="undefined"||id==""){ id=0; }
	if(typeof(fid)=="undefined"||fid==""){ fid=0; }
	if(typeof(eid)=="undefined"||eid==""){ eid=0; }
	
	//alert(id+", "+strMasterDiv+", "+fid+", "+pid+", "+eid+", "+enm);
	
	document.getElementById("divTestImages").style.display = "block";
	//document.getElementById("ajax_load_drawing").style.display = "block";
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1");
	$(":button").hide();
	
	//if(id != ""){	
		
		//var urlGetDrawing = "iDoc-Drawing/load_drawing_data.php"; //?id='+id
		var urlGetDrawing = zPath+"/chart_notes/requestHandler.php";
		
		$.post(urlGetDrawing,{"id":id,"fid":fid, "pid":pid, "eid":eid, "enm":enm, "elem_formAction":"loadDraw"},function(data){
			
			//alert(data);
			
			var z;
			var data0 = data["drw"];
			var strCanvasWNLAll="";
			if(data0 && data0.length>0){
				for(z in data0){
					var strResponseVal = data0[z];
					var arrResponseVal = new Array();
					var dbRedPixel = dbGreenPixel = dbBluePixel = dbAlphaPixel = dbTollImage = dbPatTestName = dbPatTestId = dbTestImg = canvasDataFileNameDB = dbCanvasImageDataPoint = imgDB = strCanvasWNL = loadId=drwNE=drw_data_json="";
					
					
					
					arrResponseVal = strResponseVal.split("`~`!@`~`");
					
					//alert(arrResponseVal.length);
					
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
					drw_data_json = arrResponseVal[15];					
					
					
					//*validate json : 
					if(typeof(drw_data_json)!="undefined" && drw_data_json!=""){
						var drw_data_json_tmp=0;	
						try{    drw_data_json_tmp = $.parseJSON(drw_data_json);  }catch(e){ drw_data_json_tmp=0; }
						if(drw_data_json_tmp==0){ drw_data_json = ""; }						
					}
					//*/
					
					
					
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
					document.getElementById("hidDrwDataJson"+z).value = drw_data_json;
					//document.getElementById("divDrawing"+z).style.display = "block";
					$("#divDrawing"+z).removeClass("hidden");	
					document.getElementById("hidLoad"+loadId).value = "DONE";
					strCanvasWNLAll += strCanvasWNL;
					idocdraw_arr[z].drwInit();
					idocdraw_arr[z].setStrImgLen();
					//				
				}
			}else{ idocdraw_arr[0].drwInit();	idocdraw_arr[0].setStrImgLen();  }
			
			
			document.getElementById("hidCanvasWNL").value=""+strCanvasWNLAll;
			//document.getElementById("totLoad").style.display = "none";
			document.getElementById("hidDrawingLoadAJAX").value = "1";
			document.getElementById("divTestImages").style.display = "none";
			
			if(z>0){
				$("#"+strMasterDiv).animate({scrollTop: 1},1000);
			}
			
			//if(id>=0){					
				///checkwnls();//Set Flag// pending
			///}	
			
			//* Pending
			//test titlebar drawing
			if(data["drwtemp"]){
				var str="";
				var drwtemp=data["drwtemp"];
				var ln = drwtemp.length;
				for(var x in drwtemp){
					//alert(drwtemp[x][0]+" \n "+drwtemp[x][1]);
					if(drwtemp[x][1]!=""){
						str+="<span  class=\"toolImg scanImg\" style=\"background-image:url("+drwtemp[x][1]+");\" title=\""+drwtemp[x][2]+"\" data-bgimg=\""+drwtemp[x][0]+"\" >"+
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
						
						str+="<span class=\"toolImg toolImg"+css+"\"  title=\""+drwtemp[x][2]+"\" >"+
								//"<div title=\"Click to delete template\"  class=\" tooldel\" onclick=\"drwiconTempDel('"+drwtemp[x][3]+"',2,this)\"></div>"+
								"</span>"; 
						
					}
				}				
				
				//--
				//alert($(".flltToolTop").html());
				//alert(str);
				//--
				
				$(".flltToolTop").html(str);					
				idoc_refresh_bg_img_toolbar();	
				//backgroundImgEl.bind("click", function(){setbgimg(this);});	
				
			}
			//*/
			
			//- get summary and insert it textareas --
			setTimeout(function(){idoc_getFindings4Drw(); chng_draw_type(); $(":button").show(); $("#ajax_load_drawing").hide();if(typeof(setProcessImg) != 'undefined' )setProcessImg("0");},1000);
			//- get summary and insert it textareas --
			
		},"json");
	//}	
}

function doScanUpload(examId, formId, processFor, scanUploadfor, canvasId){
	canvasId = canvasId || "";
	
	if(processFor=="upload-WEBCAM"){
		
		var features = "toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=630,left=150,top=60";
		var url = zPath+'/chart_notes/onload_wv.php?elem_action=DrawSUC&examId='+examId+'&formId='+formId+'&scanOrUpload='+processFor+'&scanUploadfor='+scanUploadfor+'&canvasId='+canvasId;
		//'iDoc-Drawing/webcam/flash.php
		var wname = "drwImg";
		//window.open(url,wname,features);
		
	}else{	
		var url = zPath+'/chart_notes/onload_wv.php?elem_action=DrawSUC&examId='+examId+'&formId='+formId+'&scanOrUpload='+processFor+'&scanUploadfor='+scanUploadfor+'&canvasId='+canvasId;	
		//window.open(url,'scan_upload_drawing','toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=1,width=650,height=470,left=290,top=100');	
	}
	
	$("#div_suc_doc").modal('hide');
	$("#div_suc_doc").remove();
	//
	if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"");
	$.get(url,function(data){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"");
			$("body").append(data);
			$("#div_suc_doc").modal('show');
			
			if(processFor=="scan"){
				scn_cntrl_main();
				if( typeof csxiacquire === 'function' )
				csxiacquire(Button, ShiftState, X, Y);
			}
		});	
}

function saveCanvas(frmName, extraFun,flgStopSubmit){
	idoc_savedrawing();
	/*	
	//TEST Save 2 --	
	if($("#ajax_load_drawing").css("display")=="block"){return;}
	
	$("#img_load").show();
	var blHaveDrawing = false;
	extraFun = extraFun || "";
	idoc_savedrawing();
	if(typeof(flgStopSubmit)=="undefined" || flgStopSubmit!=1){			
		var objFrm = document.getElementById(frmName);
		if(""+typeof(objFrm.onsubmit)=="function"){objFrm.onsubmit();}
		objFrm.submit();
	}
	*/
}

function resetDrawing(){
	var l = idocdraw_arr.length;	
	for(var a=0; a<l;a++){
		idocdraw_arr[a].resetDrawing();
	}
}

function setCD(){
	var l = idocdraw_arr.length;
	if(l <= 0){drawingInit();}
	if((document.getElementById("hidCDRationOD")) && (document.getElementById("hidCDRationOS"))){		
		for(var intCounter = 0; intCounter < l; intCounter++){
			if(document.getElementById('hidImageCss'+intCounter).value == "imgLaCanvas"){
				idocdraw_arr[intCounter].setCDRation();
				break;
			}
		}
	}
}

function AJAXLoadDarwingData_exe(hid_drw_id, dv_con, exm_id, exm_nm){
	if(hid_drw_id!=""){
		var intEOMDrawingId = document.getElementById(hid_drw_id+"0").value;
		var intDrawingLoadAJAX = document.getElementById("hidDrawingLoadAJAX").value
		var arrEOMDrawingId = new Array();var strEOMDrawingId="";
		if((parseInt(intEOMDrawingId) > 0) && (parseInt(intDrawingLoadAJAX) == 0)){
			for(var intCounter = 0; intCounter < drawCntlNum; intCounter++){
				var otmp = document.getElementById(hid_drw_id+intCounter);
				if(otmp==null){break;}
				var hidEOMDrawingId = otmp.value;
				if(parseInt(hidEOMDrawingId) > 0){
					arrEOMDrawingId[arrEOMDrawingId.length] = hidEOMDrawingId;
				}
			}
			strEOMDrawingId = arrEOMDrawingId.join(",");
		}
		//function is in iDoc-Drawing->drawing.js File
		AJAXLoadDarwingData(strEOMDrawingId, dv_con,$(":hidden[name=elem_formId]").val(),$(":hidden[name=elem_patientId]").val(),$(":hidden[name="+exm_id+"]").val(),""+exm_nm);
	}
}

function chng_draw_type(){			
	if(flg_chng_draw_type!=0){
		$("#tab"+flg_chng_draw_type).trigger("click");
		flg_chng_draw_type=0;
	}
}


function chngDfDrwType(cr_tp, cnfrm){
	var cnfrm_draw_type=0;

	var exdttp = $("#elem_drawType").val();
	if( (cr_tp ==5 && (exdttp=="" || exdttp=="0")) || exdttp==cr_tp){ 
		$("#elem_drawType").val(cr_tp);
		return; 
	}
	
	//
	var isloaded = $("#hidDrawingLoadAJAX").val();	
	if(isloaded!=1 && exdttp!=""){
		$("#tab"+exdttp).trigger("click");
		flg_chng_draw_type=cr_tp;
		return;
	}
	
	if(typeof(cnfrm)!="undefined" && cnfrm==1){
		var fc = 0 ;
	}else{
		var fc = 1 ;
	}
	
	drw = (cr_tp==8||cr_tp==9) ? "ON_MA_Fundus" : "La" ;
	var l = idocdraw_arr.length;	
	for(var a=0; a<l;a++){		
		cnfrm_draw_type = idocdraw_arr[a].setbgimg({title:drw},fc,cr_tp);
		if(cnfrm_draw_type==-1){break;}
	}
	
	if((typeof(cnfrm)!="undefined" && cnfrm==1)||cnfrm_draw_type!=-1){
		$("#elem_drawType").val(cr_tp);
	}
}

function setbgimg_confirm(a, b, c, d, e){
	if(a==1){chngDfDrwType(e, 1);}
	else{ 
		var exdttp = $("#elem_drawType").val();		
		if(exdttp!="" && exdttp!="0"){
			$("#tab"+exdttp).trigger("click");
		}
	}
}

//start
$(document).ready(function () {
		drawingInit();
		//if(idocdraw_arr[0]){ alert("Hello"); idocdraw_arr[0].testme();  }
	
	});