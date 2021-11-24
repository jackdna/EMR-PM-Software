//js more

fabric.Image.filters.Redify = fabric.util.createClass({

  type: 'Redify',

  applyTo: function(canvasEl) {
    var context = canvasEl.getContext('2d'),
        imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
        data = imageData.data;

    for (var i = 0, len = data.length; i < len; i += 4) {
      
      data[i + 0] = 0;	    
      data[i + 1] = 0;
      data[i + 2] = 0;
      data[i + 3] = 0;	    
    }

    context.putImageData(imageData, 0, 0);
  }
});

fabric.Image.filters.Redify.fromObject = function(object) {
  return new fabric.Image.filters.Redify(object);
};
//-------------------

fabric.Object.prototype._applyEraseExe = function(ctx){
	
	//---- 
	var this_scl_w=this_scl_h=1; 
		
	var ar_er = this.erasers || [];
	//var ar_er = [[-30,-30,10]]; 
	var ar_er_ln=ar_er.length;	
	for (var j=0;j<ar_er_ln;j++){
	
	var row="", col="", size="";
	row=parseInt(ar_er[j][0]*this_scl_w); 
	col=parseInt(ar_er[j][1]*this_scl_h); 
	size=ar_er[j][2]||10;
	if(row===""||col===""||size===""){ continue; }	
	//console.log("IN",row, col, size, " - ", this_scl_w, this_scl_h);
	ctx.clearRect(row, col, size, size);
	}//	
	//----
		
}

fabric.Object.prototype.applyErase_v2 =function(callback, row, col, size){ 
	
	//console.log(this.canvas);	
	//console.log(callback);
	
	var _this=this;
	
	size=10;	
	var scale_x=scale_y=1;
	scale_x=this.scaleX||1;	
	scale_y=this.scaleY||1;
	var flip_x=this.flipX||0;
	//console.log(this);
	size = size/scale_x;
	//filters = filters || this.filters;
	//change color
	var ar_er = this.get('erasers') || [];		
	if(typeof(row)!="undefined"&&row>0){	
		var w = (this.width/2)*scale_x;
		var h = (this.height/2)*scale_y;
		
		row = parseInt(row)-parseInt(w)-parseInt(size*scale_x/2);
		col=parseInt(col)-parseInt(h)-parseInt(size*scale_y/2);
		
		row=parseInt(row/scale_x);  
		col=parseInt(col/scale_y);
		if(flip_x==1){row+=9; row=(row>0)?0-row:Math.abs(row);  } //9? by ht		
		
		ar_er.push([row, col, size]); 
	}
	//ar_er = [[-25,-25,10]];
	var ar_er_ln=ar_er.length;		
	if(ar_er_ln<=0){return;}
	
	this.set('erasers', ar_er);	
	this.canvas.renderAll();
	
	return this;
	
};

fabric.Object.prototype.applyErase =function(callback, row, col, size){  
	
		if(this.type!="c-image"){
			var ret = this.applyErase_v2(callback, row, col, size);
			return ret;
		}
	
		size=size||75;			
		if(this.width==730){  size=20; }//lesser size for previous drawing images
		
		
		
		//filters = filters || this.filters;
		//change color
		var ar_er = this.get('erasers') || [];		
		if(typeof(row)!="undefined"&&row>0){				
			ar_er.push([row, col, size]); 
		}
		//ar_er = [[0,0,30], [10,10,30], [40,40,30], [140,140,30],[260,140,30]];
		var ar_er_ln=ar_er.length;		
		if(ar_er_ln<=0){return;}
		
		//console.log(ar_er);
		
		//
		if(this.type=="c-image"){
			var imgElement = this._originalElement;
			if (!imgElement) {	return;     }
			var imgEl = imgElement;			
		}else{
			return;	
		}
		
		//--		
		  var canvasEl = fabric.util.createCanvasElement(),
		  replacement = fabric.util.createImage(),
		  _this = this;

		canvasEl.width = imgEl.width;
		canvasEl.height = imgEl.height;
		
		if(this.type=="c-image"){
			canvasEl.getContext('2d').drawImage(imgEl, 0, 0, imgEl.width, imgEl.height);	
		}					
	
		//console.log("this", this);
		var this_scaleX=parseFloat(this.scaleX)||1, this_scaleY=parseFloat(this.scaleY)||1;
		
		//if(this_scaleX==2){ this_scaleX=1; }
		//if(this_scaleY==2){ this_scaleY=1; }
		
		var this_w = parseInt(this.width), this_h = parseInt(this.height);
		var this_scl_w = parseFloat((imgEl.width/this_w)/this_scaleX), this_scl_h = parseFloat((imgEl.height/this_h)/this_scaleY);		
		//console.log(imgEl.width, this.width, this_scaleX, this_scl_w, this_w);
		
		//change
		var context = canvasEl.getContext('2d');
		
		for (var j=0;j<ar_er_ln;j++){
		
		var row="", col="", size="";
		size=parseInt(ar_er[j][2]/this_scaleX);
		row=parseInt(ar_er[j][0]*this_scl_w) - parseInt(size/2); 
		col=parseInt(ar_er[j][1]*this_scl_h) - parseInt(size/2); 
		
		if(row===""||col===""||size===""){ continue; }	
		
		//console.log("IN",ar_er[j][0],ar_er[j][1],row, col, size, " - ", imgEl.width, imgEl.height, this_scl_w, this_scl_h, this_scaleX, this_scaleY);
		context.clearRect(row, col, size, size);
		/*
		var imageData = context.getImageData(row, col, size, size),
		data = imageData.data;
		
		//
		for (var i = 0, len = data.length; i < len; i += 4) {			
			data[i + 0] = 0;	    
			data[i + 1] = 0;
			data[i + 2] = 0;
			data[i + 3] = 0;
		}		
		
		context.putImageData(imageData, row, col);	
		*/
		
		}//
		
		
		this.set('erasers', ar_er);
		
		
		//--
		if (fabric.isLikelyNode) {
		replacement.src = canvasEl.toBuffer(undefined, fabric.Image.pngCompression);
		// onload doesn't fire in some node versions, so we invoke callback manually
		_this._element = replacement;
		//!forResizing && (_this._filteredEl = replacement);
		callback && callback();
		}
		else {
		replacement.onload = function() {
		_this._element = replacement;
		//!forResizing && (_this._filteredEl = replacement);
		callback && callback();
		replacement.onload = canvasEl = imgEl = null;
		};
		replacement.src = canvasEl.toDataURL('image/png');
		}
		//--		
		
		//
		return canvasEl;	

	};
	
//---------------------

fabric.CImage = fabric.util.createClass(fabric.Image, {

  type: 'c-image',

  initialize: function(element, options) {
    this.callSuper('initialize', element, options);
    options && this.set('erasers', options.erasers);
  },

  toObject: function() {
    return fabric.util.object.extend(this.callSuper('toObject'), { erasers: this.erasers });
  }
});

fabric.CImage.fromObject = function(object, callback) {
  fabric.util.loadImage(object.src, function(img) {
    callback && callback(new fabric.CImage(img, object));
  });
};

fabric.CImage.async = true;

//
fabric.CImage.fromURL = function(url, callback, imgOptions) {
    fabric.util.loadImage(url, function(img) {
      callback && callback(new fabric.CImage(img, imgOptions));
    }, null, imgOptions && imgOptions.crossOrigin);
  };	

//----------------------------


//---------------------
fabric.Citext = fabric.util.createClass(fabric.IText, {

  type: 'citext',

  initialize: function(element, options) {
	options || (options = { });  
    this.callSuper('initialize', element, options);
    options && this.set('erasers', options.erasers);
  },

  toObject: function() {
    return fabric.util.object.extend(this.callSuper('toObject'), { erasers: this.erasers });
  },
  
  _render: function(ctx) {
	this.callSuper('_render', ctx);
	this._applyEraseExe(ctx);	  
  }
  
});

fabric.Citext.fromObject = function(object) {	
	return new fabric.Citext(object.text, object);  
};

fabric.Citext.async = false;
//----------------------------

//---------------------
fabric.Citextb = fabric.util.createClass(fabric.IText, {

  type: 'citextb',

  initialize: function(element, options) {
	options || (options = { });  
    this.callSuper('initialize', element, options);
    options && this.set('unit', options.unit);
  },

  toObject: function() {
    return fabric.util.object.extend(this.callSuper('toObject'), { unit: this.unit });
  },
  
  _render: function(ctx) {
	this.callSuper('_render', ctx);	
  }
  
});

fabric.Citextb.fromObject = function(object) {	
	return new fabric.Citextb(object.text, object);  
};

fabric.Citextb.async = false;
//----------------------------


//---------------------
fabric.CRect = fabric.util.createClass(fabric.Rect, {

  type: 'cRect',

  initialize: function(options) {
    options || (options = { });

    this.callSuper('initialize', options);
    options && this.set('erasers', options.erasers);
  },

  toObject: function() {
    return fabric.util.object.extend(this.callSuper('toObject'), { erasers: this.erasers   });
  },

  _render: function(ctx) {
    this.callSuper('_render', ctx);
    this._applyEraseExe(ctx);	
  }
});

fabric.CRect.fromObject = function(object) {
  return new fabric.CRect(object);  
};

fabric.CRect.async = false;

//----------------------------


//---------------------
fabric.CCircle = fabric.util.createClass(fabric.Circle, {

  type: 'cCircle',

  initialize: function(options) {
    options || (options = { });

    this.callSuper('initialize', options);
    options && this.set('erasers', options.erasers);
  },

  toObject: function() {
    return fabric.util.object.extend(this.callSuper('toObject'), { erasers: this.erasers   });
  },

  _render: function(ctx) {
    this.callSuper('_render', ctx);
    this._applyEraseExe(ctx);	
  }
});

fabric.CCircle.fromObject = function(object) {
  return new fabric.CCircle(object);  
};

fabric.CCircle.async = false;

//----------------------------


//---------------------
fabric.CLine = fabric.util.createClass(fabric.Line, {

  type: 'cLine',

  initialize: function(points, options) {
    options || (options = { });

    this.callSuper('initialize', points, options);
    options && this.set('erasers', options.erasers);
  },

  toObject: function() {
    return fabric.util.object.extend(this.callSuper('toObject'), { erasers: this.erasers   });
  },

  _render: function(ctx) {
    this.callSuper('_render', ctx);
    this._applyEraseExe(ctx);	
  }
});

fabric.CLine.fromObject = function(object) {
  var points = [object.x1, object.y1, object.x2, object.y2];
  return new fabric.CLine(points, object);  
};

fabric.CLine.async = false;
//----------------------------

//---------------------
fabric.CPath = fabric.util.createClass(fabric.Path, {

  type: 'cPath',

  initialize: function(path, options) {
    options || (options = { });

    this.callSuper('initialize', path, options);
    options && this.set('erasers', options.erasers);
  },

  toObject: function() {
    return fabric.util.object.extend(this.callSuper('toObject'), { erasers: this.erasers   });
  },

  _render: function(ctx) {
    this.callSuper('_render', ctx);
    this._applyEraseExe(ctx);	
  }
});

fabric.CPath.fromObject = function(object, callback) { 
      callback(new fabric.CPath(object.path, object));    
};

fabric.CPath.async = true;
//----------------------------


//-----------------------------

fabric.CPencilBrush = fabric.util.createClass(fabric.PencilBrush,{
		initialize: function(canvas) {
		      this.callSuper('initialize', canvas);
		    },
		    
		createPath: function(pathData) {
		      var path = new fabric.CPath(pathData, {
				   fill: null,
				   stroke: this.color,
				   strokeWidth: this.width,
				   strokeLineCap: this.strokeLineCap,
				   strokeLineJoin: this.strokeLineJoin,
				   strokeDashArray: this.strokeDashArray,
				   originX: 'center',
				   originY: 'center'
				 });

		      if (this.shadow) {
			this.shadow.affectStroke = true;
			path.setShadow(this.shadow);
		      }

		      return path;
		    }
	});
	
//-----------------------------

//-----------------------------

fabric.CGroup =	fabric.util.createClass(fabric.Group,{
	
		type: 'cGroup',

		initialize: function(path, options) {
		    options || (options = { });

		    this.callSuper('initialize', path, options);
		    options && this.set('erasers', options.erasers);
		},
		toObject: function() {
			return fabric.util.object.extend(this.callSuper('toObject'), { erasers: this.erasers   });
		},  
		_render: function(ctx){
			this.callSuper('_render', ctx);
			this._applyEraseExe(ctx);	
		}  
	});
	
fabric.CGroup.fromObject = function(object, callback) { 
      fabric.util.enlivenObjects(object.objects, function(enlivenedObjects) {
      delete object.objects;
      callback && callback(new fabric.CGroup(enlivenedObjects, object));
    });
};

fabric.CGroup.async = true;

//-----------------------------

//-----------------------------	
	
fabric.CSprayBrush =	fabric.util.createClass(fabric.SprayBrush,{
		initialize: function(canvas) {
		      this.callSuper('initialize', canvas);
		    },
		onMouseUp: function() {
		    var originalRenderOnAddRemove = this.canvas.renderOnAddRemove;
		    this.canvas.renderOnAddRemove = false;

		    var rects = [ ];
			var cg_fill_clr = "#000000";
		    for (var i = 0, ilen = this.sprayChunks.length; i < ilen; i++) {
		      var sprayChunk = this.sprayChunks[i];

		      for (var j = 0, jlen = sprayChunk.length; j < jlen; j++) {
			cg_fill_clr = this.color;
			var rect = new fabric.CRect({
			  width: sprayChunk[j].width,
			  height: sprayChunk[j].width,
			  left: sprayChunk[j].x + 1,
			  top: sprayChunk[j].y + 1,
			  originX: 'center',
			  originY: 'center',
			  fill: this.color
			});

			this.shadow && rect.setShadow(this.shadow);
			rects.push(rect);
		      }
		    }

		    if (this.optimizeOverlapping) {
		      rects = this._getOptimizedRects(rects);
		    }
			cg_fill_clr = (typeof(cg_fill_clr)!="undefined"&&cg_fill_clr!="") ? cg_fill_clr : "#000000";
		    var group = new fabric.CGroup(rects, { originX: 'center', originY: 'center', fill:cg_fill_clr });
		    group.canvas = this.canvas;

		    this.canvas.add(group);
		    this.canvas.fire('path:created', { path: group });

		    this.canvas.clearContext(this.canvas.contextTop);
		    this._resetShadow();
		    this.canvas.renderOnAddRemove = originalRenderOnAddRemove;
		    this.canvas.renderAll();
		}  
	});
	
//---------------------------

