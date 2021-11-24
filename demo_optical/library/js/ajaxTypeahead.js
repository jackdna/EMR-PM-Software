/*
 *	File: ajaxTypehead.js
 *	Coded in PHP7
 *	Purpose: Ajax thyepehead for Optical program.
 *	Access Type: Direct Access
 *  Dependency: '../css/ajaxTypeahead.css'
*/
(function($){
	
	"use strict";
	function AjaxTypeahead(elem, options){
		
		/*selector element object*/
		this.elem = elem;
		
		/*Id of Element to which class function is bind*/
		this.elemId = $(this.elem).attr('id');
		
		/*Record missing parameters*/
		this.missing = [];
		
		/*Log error if required options not Set*/
		if(typeof(options.url)==="undefined"){this.missing.push("url");}
		if(typeof(options.type)==="undefined"){this.missing.push("type");}
		if(typeof(options.showAjaxVals)==="undefined"){this.missing.push("showAjaxVals");}
		/*if(typeof(options.hidIDelem)==="undefined"){this.missing.push("hidIDelem");}*/
		
		if(this.missing.length > 0){
			console.error("Ajax Autocomplete Missing Required paramerters for #"+this.elemId+": "+this.missing);
			return;
		}
		/*End Log error if required options not Set*/
		
		/*Type of data to be returned (used in ajax action script)*/
		this.type = options.type;
		
		/*URL of action script*/
		this.url = options.url;
		
		/*Minimum Length of textfieldValue*/
		this.minLength = (typeof(options.minLength)!=="undefined")?options.minLength:1;
		
		/*Hidden Elemnt to Hold  id value of return Data*/
		this.hidIDelem = (typeof(options.hidIDelem)!=="undefined")?options.hidIDelem:false;
		
		/*Set backgroundColor of the autocomplete List - Optional*/
		this.bgColor = (typeof(options.bgColor)!=="undefined")?options.bgColor:"#888888";
		
		/*Set Hover backgroundColor - Optional*/
		this.hoverBgColor = (typeof(options.hoverBgColor)!=="undefined")?options.hoverBgColor:"#000000";
		
		/*Set Font Color  - Optional*/
		this.textColor = (typeof(options.textColor)!=="undefined")?options.textColor:"#FFFFFF";
		
		/*Set Font Size - Optional*/
		this.fontSize = (typeof(options.fontSize)!=="undefined")?options.fontSize:"11px";
		
		/*Maximum List Length in typehead*/
		this.maxVals = (typeof(options.maxVals)==="number")?options.maxVals:10;
		
		/*Trigger onChange event Flag*/
		this.changeTrigger = true;
		
		/*No of options in typehead list returned from Ajax call*/
		this.listLength = 0;
		
		/*Highlighted option index in typehead*/
		this.selectedOption = 1;
		
		/*Navaigation Record by arrow key*/
		this.lastMove = "down";
		
		/*Flag if selector hold multiple values seperated by ";"*/
		this.multiple = (typeof(options.multiple)!=="undefined")?true:false;
		
		/*Return data key to show in typehead list*/
		this.showAjaxVals = (typeof(options.showAjaxVals)!=="undefined")?options.showAjaxVals:'upc';
			/*Prevent resetting value of selector on enter key*/
		$(this.elem).on("keydown", function(event){if(event.keyCode===13){return false;}});
		$(this.elem).on("keyup", $.proxy(this.keyUp, this));
		$(this.elem).on("focus", $.proxy(this.onFocus, this));
		//$(this.elem).on("blur", $.proxy(this.onBlur, this));
		
		/*Ajax Request State Indicator*/
		this.ajaxState = false; 
		
		/*Record Carriage Return - pressed in Typeahead field*/
		this.enterPressed = false;
		
		/*Flag to enable triggering of change event of hidden field*/
		this.tiggerHidChange = (typeof(options.tiggerHidChange)!=="undefined")?options.tiggerHidChange:false;
		
		/*Container for additional prameters to be sent in request*/
		this.extraElements = (typeof(options.extraElements)!=="undefined")?options.extraElements:{};
	}
	
	
	AjaxTypeahead.prototype = {
		
		/*Event handler for keyUp event*/
		keyUp: function(event){
			/*if(typeof(top.main_iframe)!=="undefined" && typeof(top.main_iframe.admin_iframe)!=="undefined" && top.main_iframe.admin_iframe.remakeFlag){
				top.falert("You are going to change the item in Remake Order.<br />It will not be treated as Remake.");
			}*/
            var currentVal = $(event.currentTarget).val();
			if(this.multiple){
				currentVal = currentVal.split(';');
				currentVal = $.trim(currentVal[currentVal.length-1]);
			}
			/*Match input length with minimum input length configured*/
            if(currentVal.length>=this.minLength){
				/*Get keycode to navigation and value select*/
				var currentKey = event.keyCode;
				switch(currentKey){
					
					/*Handle Enter key press*/
					case 13:
						this.enterPressed = true;
						/*Get current active element in Typehead*/
						var activeOption = $(document.body).find("div.ajaxTypeahead>ul>li.activeOption");
						
						/*Trigger Click event of Typehead option to set value of selector and hidden field*/
						$(activeOption).trigger('click');
					break;
					
					/*Handle Up arrow navigation*/
					case 38:
						this.moveUp();
					break;
					
					/*Handle Down arrow navigation*/
					case 40:
						this.moveDown();
					break;
					
					/*Capture input and make ajax Call*/
					default:
						/*Parameters to be passed to action script in ajax call*/
						var ajaxParameters = {
											  "type":this.type,
											  "maxVals":this.maxVals,
											  "queryElemType":this.showAjaxVals,
											  "upcMatch":currentVal,
											  "extraParams":{},
											 };
						$.each(this.extraElements, function(key, elemObj){
							ajaxParameters.extraParams[$(elemObj).attr('name')] = $(elemObj).val();
						});
						
						/*Fix for Contact Lens Stock Data from iDoc*/
						if(this.type==="contactLensDataStock"){
							var manufactuers_cl_name = $.trim($("#manufacturer :selected").text());
							var manufactuers_cl_id = $.trim($("#manufacturer").val());
							if(manufactuers_cl_name!=="Please Select" && manufactuers_cl_name!==""){
								ajaxParameters.manufacturer_name = manufactuers_cl_name;
								ajaxParameters.manufacturer_id = manufactuers_cl_id;
							}
						}
						/*End Fix for Contact Lens Stock Data from iDoc*/
						
						/*Call ajax requrest function*/
						this.ajaxRequest(ajaxParameters);
						
						/*Flag to fire change Event on selector object*/
						this.changeTrigger = true;
					break;
				}
            }
			else{
				/*Hide typehead list if not matched with minimum input length*/
				this.hideList();
			}
        },
		
		/*function to handle ajax Call*/
		ajaxRequest: function(params){
			
			/*Pointer to Calling object*/
			var obj = this;
			
			/*Cancel Previous incompleted request*/
			if(this.ajaxState && this.ajaxState!==4){
				this.ajaxState.abort();
			}
			
			this.ajaxState = $.ajax({
				url: this.url,
				data: params,
				method: 'POST',
				async: true,
				success: function(data){
					
					/*Chek for blank value returned from Action Script*/
					data = (data.trim()!=="")?jQuery.parseJSON(data.trim()):{};
					if(typeof(data.error)==="undefined"){
						obj.showList(data);
					}
					else{
						obj.hideList();
					}
				}
			});
		},
		
		/*Trigger keyup if selector in in focus*/
		onFocus: function(event){
			$(event.currentTarget).trigger("keyup");
		},
		
		/*Trigger change event of selector on focusout*/
		onBlur: function(event){
			//$(event.currentTarget).trigger("change");
		},
		
		/*show typehead dropdown list*/
		showList: function(data){
			
			/*current top position of selector*/
			var topPosition = $(this.elem).offset().top+$(this.elem).outerHeight(true);
			
			/*current left position of selector*/
			var leftPosition = $(this.elem).offset().left;
			
			/*Typehead list to be shown*/
			var list = "";
			
			/*Hide List if already opened to populate new one*/
			this.hideList();
			
			/*No. of elements/option in typehead list returned from action script*/
			this.listLength = data.length;
			
			/*Container for typehead options*/
			list = '<div class="ajaxTypeahead" style="background-color:'+this.bgColor+';top:'+topPosition+'px;left:'+leftPosition+'px;">';
			list += '<ul>';
			
			/*Pointer to Calling object*/
			var obj = this;
			
			$(data).each(function(){
				
				/*Data to be displayed in typehead options*/
				var dispData = "";
				var make_id = "";
				
				/*Values to be shown in typehead list matched with selector's configurations*/
				if(obj.showAjaxVals==="upc"){
					dispData = this.upc+':'+this.name;
				}
				else if(obj.showAjaxVals==="name"){
					dispData = this.name;
				}
				else{
					dispData = this[obj.showAjaxVals];	
				}
				
				/*Fix for Contact Lens Stock Data from iDoc*/
				if(obj.type==="contactLensDataStock" && this.id===""){
					make_id = ' make_id="'+this.make_id+'"';
				}
				/*End Fix for Contact Lens Stock Data from iDoc*/
				
				/*Typehead options, theme as per configurations for selector*/
				list += '<li style="color:'+obj.textColor+';font-size:'+obj.fontSize+';" onMouseOver="$(this).css(\'background-color\', \''+obj.hoverBgColor+'\');" onMouseOut="$(this).css(\'background-color\', \'inherit\');" ElemID="'+this.id+'"'+make_id+'>'+dispData+'</li>';
			});
			
			list += '</ul>';
			list += '</div>';
			
			/*Append Typehead list prepared to document*/
			$(document.body).append(list);
			
			/*Trigger down keypress to highlist first element of Typehead list*/
			this.moveDown();
			
			/*Bind click event to typehead options and chage value of selector and hidden element if typehead option selected/clicked*/
			$(document.body).find("div.ajaxTypeahead>ul>li").on({
				click: function(){
					
					/*change value of selector*/
					if(obj.multiple){
						/*Multiple values in Selector*/
						var prev = $.trim($(obj.elem).val());
						prev = prev.split(";");
						$(prev).each(function(i, v){
							prev[i] = $.trim(v);
						});
						
						if(prev[prev.length-1]===""){
							prev.pop();
							prev = prev.join(";");
						}
						else{
							if(prev.length===1){
								prev = "";
							}
							else{
								prev.pop();
								prev =  prev.join(";")+";";
							}
						}
						
						var nVal = $(this).html();						
						if(prev!=="" && prev.indexOf(";") !== -1){
							nVal = prev+$(this).html()+";";
						}
						else{
							nVal = nVal+";";
						}
						
						nVal = $("<div/>").html(nVal).text();
						$(obj.elem).val(nVal);
					}
					else{
						/*Single Value in Selector*/
						var nVal1 = $("<div/>").html($(this).html()).text();
						$(obj.elem).val(nVal1);
					}
					
					/*Chaneg value of hidden element*/
					if(obj.hidIDelem){
						/*Fix for Contact Lens Stock Data from iDoc*/
						if(obj.type==="contactLensDataStock"){
							if($(this).attr('ElemID')===""){
								$("#stock_form")[0].reset();	/*Reset Stock Form if New CL is Loaded*/
								$("#idoc_cl_id").val($(this).attr('make_id'));
								$(obj.hidIDelem).val('');
							}
							else{
								$("#idoc_cl_id").val('');
								$(obj.hidIDelem).val($(this).attr('ElemID'));
							}
						}/*End Fix for Contact Lens Stock Data from iDoc*/
						else{
							$(obj.hidIDelem).val($(this).attr('ElemID'));
						}
						
						if(obj.tiggerHidChange){
							$(obj.hidIDelem).trigger('change');
						}
					}
					
					/*Trigger change event of selector*/
					$(obj.elem).trigger('change');
					$(obj.elem).trigger('change1');
					
					/*Hide typehead on option selected*/
					obj.hideList();
				}
			});
			
			if(this.listLength===1 && this.enterPressed===true){
				this.enterPressed = true;
				/*Get current active element in Typehead*/
				var activeOption = $(document.body).find("div.ajaxTypeahead>ul>li.activeOption");

				/*Trigger Click event of Typehead option to set value of selector and hidden field*/
				$(activeOption).trigger('click');
				this.enterPressed = false;
			}
		},
		
		
		/*Down arrow key Navigation*/
		moveDown: function(){
			
			/*Check Last move by navigation keys*/
			if(this.lastMove==="up"){
				/*current option pointer by arrow key navigation -- adjust if last navigation down*/
				this.selectedOption = this.selectedOption+1;
			}
			
			/*Revoe hover/highlight from all Typehead options*/
			this.removeHover($(document.body).find("div.ajaxTypeahead>ul>li"));
			
			/*Highlight option -- arrow key navigations*/
			this.hoverBakcground($(document.body).find("div.ajaxTypeahead>ul>li:nth-of-type("+this.selectedOption+")"));
			
			/*Current highlighted option counter*/
			this.selectedOption++;
			
			/*Current highlighted option counter -- adjust if reached to last option in list*/
			if(this.selectedOption>this.listLength){this.selectedOption=this.listLength;}
			
			/*Record Last navigation throught arrow keys*/
			this.lastMove="down";
		},
		
		/*Up arrow key Navigation*/
		moveUp: function(){
			if(this.lastMove==="down"){
				this.selectedOption = (this.selectedOption===this.listLength)?this.selectedOption-1:this.selectedOption-2;
			}
			else{
				this.selectedOption--;
			}
			
			if(this.selectedOption<1){this.selectedOption=1;}
			
			this.removeHover($(document.body).find("div.ajaxTypeahead>ul>li"));
			this.hoverBakcground($(document.body).find("div.ajaxTypeahead>ul>li:nth-of-type("+this.selectedOption+")"));
			this.lastMove="up";
		},
		
		/*Highlist option on mouseover or by arrow key navigation*/
		hoverBakcground: function(obj){
			
			/*Make option active*/
			$(obj).addClass("activeOption");
			
			/*Change background color of active option*/
			$(obj).css("background-color", this.hoverBgColor);
		},
		
		/*Deselect highloghted option in Typehead*/
		removeHover: function(obj){
			
			/*Set background color to default*/
			$(obj).css("background-color", "inherit");
			
			/*Unhighlight option(s)*/
			$(obj).removeClass("activeOption");
		},
		
		/*Hide Typehead list*/
		hideList: function(){
			
			/*Reset selected option counter for arrow key navigation*/
			this.selectedOption = 1;
			
			/*Remove Typehead from document*/
			$(document.body).children("div.ajaxTypeahead").remove();
		},
		
	};
	
	
	$.fn.ajaxTypeahead = function(options) {
		return this.each(function(){
			var typeahead = '';
			typeahead = new AjaxTypeahead(this, options);
		});
    };
	
})(jQuery);