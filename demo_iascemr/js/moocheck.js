
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*
* FancyForm 0.91
* By Vacuous Virtuoso, lipidity.com
* ---
* Checkbox and radio input replacement script.
* Toggles defined class when input is selected.
*/

var FancyForm = {
	start: function(elements, options){ 
		
		FancyForm.runningInit = 1;
		if($type(elements)!='array') elements = $$('input');
		if(!options) options = [];
		FancyForm.onclasses = ($type(options['onClasses']) == 'object') ? options['onClasses'] : {
			checkbox: 'checked',
			radio: 'selected'
			
		}
		FancyForm.offclasses = ($type(options['offClasses']) == 'object') ? options['offClasses'] : {
			checkbox: 'unchecked',
			radio: 'unselected'
		}
		if($type(options['extraClasses']) == 'object'){
			FancyForm.extra = options['extraClasses'];
		} else if(options['extraClasses']){
			FancyForm.extra = {
				checkbox: 'f_checkbox',
				radio: 'f_radio',
				on: 'f_on',
				off: 'f_off',
				all: 'fancy'
			}
		} else {
			FancyForm.extra = {};
		}
		FancyForm.onSelect = $pick(options['onSelect'],function(el){});
		FancyForm.onDeselect = $pick(options['onDeselect'], function(el){});
		var keeps = [];
		FancyForm.chks = elements.filter(function(chk){
			if( $type(chk) != 'element' ) return false;
			if( chk.getTag() == 'input' && (FancyForm.onclasses[chk.getProperty('type')]) ){
				var el = chk.getParent();
				if(el.getElement('input')==chk){
					el.type = chk.getProperty('type');
					el.inputElement = chk;
					this.push(el);
				} else {
					chk.addEvent('click',function(ev){ev.stopPropagation();})
				//	el = (new Element('div',{class:'hi'}).adopt(chk)).injectInside(el); 
				}
			} else if( (chk.inputElement = chk.getElement('input')) && (FancyForm.onclasses[(chk.type = chk.inputElement.getProperty('type'))]) ){
				return true;
			}
			return false;
		}.bind(keeps));
		FancyForm.chks = FancyForm.chks.merge(keeps);
		keeps = null;
		FancyForm.chks.each(function(chk){
			chk.inputElement.setStyle('position', 'absolute');
			chk.inputElement.setStyle('left', '-9999px');
			chk.addEvent('selectStart', function(){})
			chk.name = chk.inputElement.getProperty('name');
			if(chk.inputElement.checked) FancyForm.select(chk);
			else FancyForm.deselect(chk);
			chk.addEvent('click', function(e){
				var e = new Event(e);
				//alert(chk);
				if(chk.inputElement.type=='radio') {
					//alert(chk.inputElement.name);
					if(chk.inputElement.value=="No"){
						//alert(chk.inputElement.className);
					}
					if(chk.inputElement.value=="Yes"){
						//alert(chk.inputElement.className);
					}
					
					
				}
				if(chk.inputElement.getProperty('disabled')) return;
				if ($type(e.preventDefault) == 'function')
					e.preventDefault(true);
				else if ($type(e.returnValue) == 'function')
					e.returnValue(true);
				if (!chk.hasClass(FancyForm.onclasses[chk.type]))
						FancyForm.select(chk);
				else if(chk.type != 'radio')
					FancyForm.deselect(chk);
				FancyForm.focusing = 1;
				chk.inputElement.focus();
				FancyForm.focusing = 0;
			});
			chk.addEvent('mousedown', function(e){
				if ($type(e.preventDefault) == 'function')
					e.preventDefault(true);
				else if ($type(e.returnValue) == 'function')
					e.returnValue(true);
			});
			chk.inputElement.addEvent('focus', function(e){
				if(!FancyForm.focusing) chk.setStyle('outline', '1px dotted');
			});
			chk.inputElement.addEvent('blur', function(e){chk.setStyle('outline', '0')});
			if(extraclass = FancyForm.extra[chk.type])
				chk.addClass(extraclass);
			if(extraclass = FancyForm.extra['all'])
				chk.addClass(extraclass);
		});
		FancyForm.runningInit = 0;
	},
	select: function(chk){
		//alert(chk.inputElement.name);
		//disp_none(document.frm_health_ques.chkbx_diab2,'diab_yes2','ramMain');
		chk.inputElement.checked = 'checked';
		chk.removeClass(FancyForm.offclasses[chk.type]);
		chk.addClass(FancyForm.onclasses[chk.type]);
		if (chk.type == 'radio'){
			FancyForm.chks.each(function(other){
				if (other.name != chk.name || other == chk) return;
				FancyForm.deselect(other);
			});
			//var temp2= eval(chk.name);
			//var temp2=chk.name;
			
		}
		
		if (chk.type == 'checkbox'){
			//alert(chk.inputElement.name);
			
			if(chk.inputElement.name=='chbx_no') {
				//alert(chk.inputElement.name.value);
			}
			
		}
		
		if(extraclass = FancyForm.extra['on'])
			chk.addClass(extraclass);
		if(extraclass = FancyForm.extra['off'])
			chk.removeClass(extraclass);
		if(!FancyForm.runningInit)
			FancyForm.onSelect(chk);
	}, 
	deselect: function(chk){
		
		/*if(chk.inputElement.type=="radio"){
		alert(chk.inputElement.checked +"DE");
	     }*/
		chk.inputElement.checked = false;
		chk.removeClass(FancyForm.onclasses[chk.type]);
		chk.addClass(FancyForm.offclasses[chk.type]);
		if(extraclass = FancyForm.extra['off'])
			chk.addClass(extraclass);
		if(extraclass = FancyForm.extra['on'])
			chk.removeClass(extraclass);
		if(!FancyForm.runningInit)
			FancyForm.onDeselect(chk);
			
	},
	all: function(){
		FancyForm.chks.each(function(chk){
			FancyForm.select(chk);
		});
	},
	none: function(){
		FancyForm.chks.each(function(chk){
			FancyForm.deselect(chk);
		});
	}
};

/*
window.addEvent('domready', function(){
	FancyForm.start();
});
*/



/****new code**/

function GetXmlHttpObject()
{ 
			
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
	objXMLHttp=new XMLHttpRequest()
	}
	else if (window.ActiveXObject)
	{
	objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}
	return objXMLHttp
}			


var preDefineCloseOut;
function preDefineOpenCloseFun() {
	document.getElementById("hiddPreDefineId").value = "preDefineOpenYes";
}
function preCloseFun(Id) {
	
	if(document.getElementById("hiddPreDefineId")) {
		if(document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
			if(document.getElementById(Id)) {
				if(document.getElementById(Id).style.display != "none"){
					document.getElementById(Id).style.display = "none"; 
					//document.getElementById("hiddPreDefineId").value = "";
				}
			}
			if(top.document.getElementById(Id)) {
				if(top.document.getElementById(Id).style.display != "none"){
					top.document.getElementById(Id).style.display = "none"; 
					//document.getElementById("hiddPreDefineId").value = "";
				}
				
			}
		}
		
	}
	
}


function disp(field_name,elem_id) {
	
	document.getElementById(elem_id).style.display="inline-block";

	$('#spn_'+elem_id).addClass('fa-angle-double-up');
	$('#spn_'+elem_id).removeClass('fa-angle-double-down');
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function disp_new(field_name,elem_id) {
	
	if ($(field_name).is(":checked")) {
		$('#'+elem_id).collapse('show');
		$("a[data-target='#"+elem_id+"'] > span").removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
	}
	else{
		$('#'+elem_id).collapse('hide');
		$("a[data-target='#"+elem_id+"'] > span").removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
	}
}

var elem_idt2="";
function disp_none(field_name,elem_id) {
	
	document.getElementById(elem_id).style.display="none";
	
	$('#spn_'+elem_id).addClass('fa-angle-double-down');
	$('#spn_'+elem_id).removeClass('fa-angle-double-up');
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
}
function disp_none_new(field_name,elem_id) {
	
	if($('#'+elem_id).hasClass('in')){
		$('#'+elem_id).collapse('hide');
		$("a[data-target='#"+elem_id+"'] > span").removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
	}
}

function disp_rev(field_name,elem_id,t_id) {
	
	if(document.getElementById(elem_id).style.display=="none") {
		document.getElementById(elem_id).style.display="inline-block";
		$('#spn_'+elem_id).addClass('fa-angle-double-up');
		$('#spn_'+elem_id).removeClass('fa-angle-double-down');
		
		//document.getElementById(t_id).innerHTML='<img  src="images/none.gif" border="0" style="cursor:pointer; " />';
		
	} else {
		document.getElementById(elem_id).style.display="none";
		$('#spn_'+elem_id).addClass('fa-angle-double-down');
		$('#spn_'+elem_id).removeClass('fa-angle-double-up');
		
		//document.getElementById(t_id).innerHTML='<img  src="images/block.gif" border="0" style="cursor:pointer; " />';
	}
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function disp_rowcolor(elem_id) {
	//alert(document.getElementById(elem_id));
	document.getElementById(elem_id).style.background="#FFF7C0";
}

function txt_disable(txt_id1,txt_id2,txt_id3,txt_id4,txt_id5,txt_id6,txt_id7,txt_id8) {
	//alert(document.getElementById(txt_id).disabled);
	document.getElementById(txt_id1).disabled=true;
	document.getElementById(txt_id2).disabled=true;
	document.getElementById(txt_id3).disabled=true;
	document.getElementById(txt_id4).disabled=true;
	document.getElementById(txt_id5).disabled=true;
	document.getElementById(txt_id6).disabled=true;
	document.getElementById(txt_id7).disabled=true;
	document.getElementById(txt_id8).disabled=true;
	
}

function txt_enable(txt_id1,txt_id2,txt_id3,txt_id4,txt_id5,txt_id6,txt_id7,txt_id8) {
	document.getElementById(txt_id1).disabled=false;
	document.getElementById(txt_id2).disabled=false;
	document.getElementById(txt_id3).disabled=false;
	document.getElementById(txt_id4).disabled=false;
	document.getElementById(txt_id5).disabled=false;
	document.getElementById(txt_id6).disabled=false;
	document.getElementById(txt_id7).disabled=false;
	document.getElementById(txt_id8).disabled=false;
	
	
}
/*
function txt_disable_frame(framename,txt_id1,txt_id2,txt_id3,txt_id4,txt_id5,txt_id6,txt_id7,txt_id8) {
	//alert(document.getElementById(txt_id).disabled);
	var objframename = eval(framename);
	//alert(objframename.document.getElementById(txt_id1).name);
	
	objframename.document.getElementById(txt_id1).disabled=true;
	objframename.document.getElementById(txt_id2).disabled=true;
	objframename.document.getElementById(txt_id3).disabled=true;
	objframename.document.getElementById(txt_id4).disabled=true;
	objframename.document.getElementById(txt_id5).disabled=true;
	objframename.document.getElementById(txt_id6).disabled=true;
	objframename.document.getElementById(txt_id7).disabled=true;
	objframename.document.getElementById(txt_id8).disabled=true;
	
	
}
*/
function txt_disable_frame(framename,
						   	left_txt_id1,right_txt_id1,left_txt_id2,right_txt_id2,
							left_txt_id3,right_txt_id3,left_txt_id4,right_txt_id4,
							left_txt_id5,right_txt_id5,left_txt_id6,right_txt_id6,
							left_txt_id7,right_txt_id7,left_txt_id8,right_txt_id8,
							left_txt_id9,right_txt_id9,left_txt_id10,right_txt_id10
							) {
	//alert(document.getElementById(left_txt_id1).disabled);
	var objframename = eval(framename);
	
	objframename.document.getElementById(left_txt_id1).disabled=true;
	objframename.document.getElementById(right_txt_id1).disabled=true;
	objframename.document.getElementById(left_txt_id2).disabled=true;
	objframename.document.getElementById(right_txt_id2).disabled=true;
	objframename.document.getElementById(left_txt_id3).disabled=true;
	objframename.document.getElementById(right_txt_id3).disabled=true;
	objframename.document.getElementById(left_txt_id4).disabled=true;
	objframename.document.getElementById(right_txt_id4).disabled=true;
	objframename.document.getElementById(left_txt_id5).disabled=true;
	objframename.document.getElementById(right_txt_id5).disabled=true;
	objframename.document.getElementById(left_txt_id6).disabled=true;
	objframename.document.getElementById(right_txt_id6).disabled=true;
	objframename.document.getElementById(left_txt_id7).disabled=true;
	objframename.document.getElementById(right_txt_id7).disabled=true;
	objframename.document.getElementById(left_txt_id8).disabled=true;
	objframename.document.getElementById(right_txt_id8).disabled=true;
	objframename.document.getElementById(left_txt_id9).disabled=true;
	objframename.document.getElementById(right_txt_id9).disabled=true;
	objframename.document.getElementById(left_txt_id10).disabled=true;
	objframename.document.getElementById(right_txt_id10).disabled=true;
	
	
}

function txt_disable_frame1(framename,left_txt_id,right_txt_id,len) {
	//alert(document.getElementById(left_txt_id1).disabled);
	var objframename = eval(framename);
	for(var i=1;i<=len;i++) {
		var incr_left_text_id = left_txt_id+i;
		var incr_right_text_id = right_txt_id+i;
		objframename.document.getElementById(incr_left_text_id).disabled=true;
		objframename.document.getElementById(incr_right_text_id).disabled=true;
	}
}
function txt_enable_frame1(framename,left_txt_id,right_txt_id,len) {
	//alert(document.getElementById(left_txt_id1).disabled);
	var objframename = eval(framename);
	for(var i=1;i<=len;i++) {
		var incr_left_text_id = left_txt_id+i;
		var incr_right_text_id = right_txt_id+i;
		objframename.document.getElementById(incr_left_text_id).disabled=false;
		objframename.document.getElementById(incr_right_text_id).disabled=false;
	}
}
//CLICK ON YES ON CHECKING THE SUB OPTION DONE BY MAMTA
function checkyes(id,id2,id3)
{
  if(document.getElementById(id2).checked==true)
  {
  document.getElementById(id).checked=true;
  document.getElementById(id3).checked=false;
  }
  else if(document.getElementById(id2).checked==false)
  {
  document.getElementById(id).checked=false;
  document.getElementById(id3).checked=true;
  }
}//END CLICK ON YES ON CHECKING THE SUB OPTION DONE BY MAMTA
function txt_enable_disable_frame1(framename,field_id,left_txt_id,right_txt_id,len) {
								
	//alert(document.getElementById(field_id).checked);
	/*if(typeof(arguments[4])=="object")
	{
		var table_name = arguments[4].table_name;
		var table_obj = document.getElementById(table_name);
		var total_rows = table_obj.rows.length;
/*		//# Debugging
		var str="";
		for(i=0;i<total_rows;i++)
		{
			str+="\n"+table_obj.rows[i].children(0).children(0).id;
		}
		alert(str);
		//return;
		//# debugging * /
		len = total_rows;
	}*/
	var hidden_field = "hidd_count_rows_in_table";
	switch(framename)
	{
		case "iframe_medication_pre_op_gen_anes": hidden_field="hidd_count_rows_in_medication_table";break;
	}
	var rows = document.getElementById(hidden_field).value;
	len = rows;
	var objframename = eval(framename);
	if(document.getElementById(field_id).checked==true) {
		for(var i=1;i<=len;i++) {
			var incr_left_text_id = left_txt_id+i;
			var incr_right_text_id = right_txt_id+i;
			document.getElementById(incr_left_text_id).disabled=true;
			document.getElementById(incr_left_text_id).value='';
			document.getElementById(incr_right_text_id).disabled=true;
			document.getElementById(incr_right_text_id).value='';
		}
	}else if(document.getElementById(field_id).checked==false) {
		for(var i=1;i<=len;i++) {
			var incr_left_text_id = left_txt_id+i;
			var incr_right_text_id = right_txt_id+i;
			document.getElementById(incr_left_text_id).disabled=false;
			document.getElementById(incr_right_text_id).disabled=false;
		}
	}

}

/*
function txt_enable_frame(framename,txt_id1,txt_id2,txt_id3,txt_id4,txt_id5,txt_id6,txt_id7,txt_id8) {
	var objframename = eval(framename);
	objframename.document.getElementById(txt_id1).disabled=false;
	objframename.document.getElementById(txt_id2).disabled=false;
	objframename.document.getElementById(txt_id3).disabled=false;
	objframename.document.getElementById(txt_id4).disabled=false;
	objframename.document.getElementById(txt_id5).disabled=false;
	objframename.document.getElementById(txt_id6).disabled=false;
	objframename.document.getElementById(txt_id7).disabled=false;
	objframename.document.getElementById(txt_id8).disabled=false;
	
	
}
*/

function txt_enable_frame(framename,
						   	left_txt_id1,right_txt_id1,left_txt_id2,right_txt_id2,
							left_txt_id3,right_txt_id3,left_txt_id4,right_txt_id4,
							left_txt_id5,right_txt_id5,left_txt_id6,right_txt_id6,
							left_txt_id7,right_txt_id7,left_txt_id8,right_txt_id8,
							left_txt_id9,right_txt_id9,left_txt_id10,right_txt_id10
							) {
	var objframename = eval(framename);
	
	objframename.document.getElementById(left_txt_id1).disabled=false;
	objframename.document.getElementById(right_txt_id1).disabled=false;
	objframename.document.getElementById(left_txt_id2).disabled=false;
	objframename.document.getElementById(right_txt_id2).disabled=false;
	objframename.document.getElementById(left_txt_id3).disabled=false;
	objframename.document.getElementById(right_txt_id3).disabled=false;
	objframename.document.getElementById(left_txt_id4).disabled=false;
	objframename.document.getElementById(right_txt_id4).disabled=false;
	objframename.document.getElementById(left_txt_id5).disabled=false;
	objframename.document.getElementById(right_txt_id5).disabled=false;
	objframename.document.getElementById(left_txt_id6).disabled=false;
	objframename.document.getElementById(right_txt_id6).disabled=false;
	objframename.document.getElementById(left_txt_id7).disabled=false;
	objframename.document.getElementById(right_txt_id7).disabled=false;
	objframename.document.getElementById(left_txt_id8).disabled=false;
	objframename.document.getElementById(right_txt_id8).disabled=false;
	objframename.document.getElementById(left_txt_id9).disabled=false;
	objframename.document.getElementById(right_txt_id9).disabled=false;
	objframename.document.getElementById(left_txt_id10).disabled=false;
	objframename.document.getElementById(right_txt_id10).disabled=false;
	
	
}

function txt_enable_disable(field_id,txt_id1,txt_id2,txt_id3,txt_id4,txt_id5,txt_id6,txt_id7,txt_id8) {
	//alert(document.getElementById(field_id).checked);
	if(document.getElementById(field_id).checked==true) {
		document.getElementById(txt_id1).disabled=true;
		document.getElementById(txt_id2).disabled=true;
		document.getElementById(txt_id3).disabled=true;
		document.getElementById(txt_id4).disabled=true;
		document.getElementById(txt_id5).disabled=true;
		document.getElementById(txt_id6).disabled=true;
		document.getElementById(txt_id7).disabled=true;
		document.getElementById(txt_id8).disabled=true;
	
	}else if(document.getElementById(field_id).checked==false) {
		document.getElementById(txt_id1).disabled=false;
		document.getElementById(txt_id2).disabled=false;
		document.getElementById(txt_id3).disabled=false;
		document.getElementById(txt_id4).disabled=false;
		document.getElementById(txt_id5).disabled=false;
		document.getElementById(txt_id6).disabled=false;
		document.getElementById(txt_id7).disabled=false;
		document.getElementById(txt_id8).disabled=false;
	}
	//alert(document.getElementById(field_id).checked);
	
}

//function txt_enable_disable_frame(framename,txt_id1,txt_id2,txt_id3,txt_id4,txt_id5,txt_id6,txt_id7,txt_id8) {

function txt_enable_disable_frame(framename,field_id,
								left_txt_id1,right_txt_id1,left_txt_id2,right_txt_id2,
								left_txt_id3,right_txt_id3,left_txt_id4,right_txt_id4,
								left_txt_id5,right_txt_id5,left_txt_id6,right_txt_id6,
								left_txt_id7,right_txt_id7,left_txt_id8,right_txt_id8,
								left_txt_id9,right_txt_id9,left_txt_id10,right_txt_id10
								) {


//alert(document.getElementById(field_id).checked);
	var objframename = eval(framename);
	if(document.getElementById(field_id).checked==true) {
		
		
		objframename.document.getElementById(left_txt_id1).disabled=true;
		objframename.document.getElementById(right_txt_id1).disabled=true;
		objframename.document.getElementById(left_txt_id2).disabled=true;
		objframename.document.getElementById(right_txt_id2).disabled=true;
		objframename.document.getElementById(left_txt_id3).disabled=true;
		objframename.document.getElementById(right_txt_id3).disabled=true;
		objframename.document.getElementById(left_txt_id4).disabled=true;
		objframename.document.getElementById(right_txt_id4).disabled=true;
		objframename.document.getElementById(left_txt_id5).disabled=true;
		objframename.document.getElementById(right_txt_id5).disabled=true;
		objframename.document.getElementById(left_txt_id6).disabled=true;
		objframename.document.getElementById(right_txt_id6).disabled=true;
		objframename.document.getElementById(left_txt_id7).disabled=true;
		objframename.document.getElementById(right_txt_id7).disabled=true;
		objframename.document.getElementById(left_txt_id8).disabled=true;
		objframename.document.getElementById(right_txt_id8).disabled=true;
		objframename.document.getElementById(left_txt_id9).disabled=true;
		objframename.document.getElementById(right_txt_id9).disabled=true;
		objframename.document.getElementById(left_txt_id10).disabled=true;
		objframename.document.getElementById(right_txt_id10).disabled=true;
		
	}else if(document.getElementById(field_id).checked==false) {
		
		objframename.document.getElementById(left_txt_id1).disabled=false;
		objframename.document.getElementById(right_txt_id1).disabled=false;
		objframename.document.getElementById(left_txt_id2).disabled=false;
		objframename.document.getElementById(right_txt_id2).disabled=false;
		objframename.document.getElementById(left_txt_id3).disabled=false;
		objframename.document.getElementById(right_txt_id3).disabled=false;
		objframename.document.getElementById(left_txt_id4).disabled=false;
		objframename.document.getElementById(right_txt_id4).disabled=false;
		objframename.document.getElementById(left_txt_id5).disabled=false;
		objframename.document.getElementById(right_txt_id5).disabled=false;
		objframename.document.getElementById(left_txt_id6).disabled=false;
		objframename.document.getElementById(right_txt_id6).disabled=false;
		objframename.document.getElementById(left_txt_id7).disabled=false;
		objframename.document.getElementById(right_txt_id7).disabled=false;
		objframename.document.getElementById(left_txt_id8).disabled=false;
		objframename.document.getElementById(right_txt_id8).disabled=false;
		objframename.document.getElementById(left_txt_id9).disabled=false;
		objframename.document.getElementById(right_txt_id9).disabled=false;
		objframename.document.getElementById(left_txt_id10).disabled=false;
		objframename.document.getElementById(right_txt_id10).disabled=false;
		}
	//alert(document.getElementById(field_id).checked);
	
}

function txt_rev(field_id,txt_id1,txt_id2,txt_id3,txt_id4,txt_id5,txt_id6,txt_id7,txt_id8) {
	
	if(document.getElementById(txt_id1).disabled==false) {
		
		document.getElementById(field_id).checked=true;
		
		document.getElementById(txt_id1).disabled=true;
		document.getElementById(txt_id2).disabled=true;
		document.getElementById(txt_id3).disabled=true;
		document.getElementById(txt_id4).disabled=true;
		document.getElementById(txt_id5).disabled=true;
		document.getElementById(txt_id6).disabled=true;
		document.getElementById(txt_id7).disabled=true;
		document.getElementById(txt_id8).disabled=true;
	
	}else if(document.getElementById(txt_id1).disabled==true) {
		
		document.getElementById(field_id).checked=false;
		
		document.getElementById(txt_id1).disabled=false;
		document.getElementById(txt_id2).disabled=false;
		document.getElementById(txt_id3).disabled=false;
		document.getElementById(txt_id4).disabled=false;
		document.getElementById(txt_id5).disabled=false;
		document.getElementById(txt_id6).disabled=false;
		document.getElementById(txt_id7).disabled=false;
		document.getElementById(txt_id8).disabled=false;
	}
	//alert(document.getElementById(field_id).checked);
	
}
function chk_unchk_prevAnes(main_id,id1,id2,id3) {
	
	if(document.getElementById(main_id).checked==true) {
		document.getElementById(id1).disabled=false;
		document.getElementById(id2).disabled=false;
		document.getElementById(id3).disabled=false;
	}else {
		document.getElementById(id1).disabled=true;
		document.getElementById(id2).disabled=true;
		document.getElementById(id3).disabled=true;
	}
}

function chk_unchk_family_hist(main_id,id1) {
	
	if(document.getElementById(main_id).checked==true) {
		document.getElementById(id1).disabled=false;
	}else {
		document.getElementById(id1).disabled=true;
	}
}

function chk_unchk_select(main_id,id1) {
	
	if(document.getElementById(main_id).checked==true) {
		
		$("select#"+id1+"").removeAttr('disabled').selectpicker('refresh');
	}else {
		$("select#"+id1+"").attr('disabled',true).selectpicker('refresh');
	}
}

function chk_unchk_smoke(main_id,id1,id2,id3,id4,id5,id6) {
	
	if(document.getElementById(main_id).checked==true) {
		document.getElementById(id1).disabled=true;
		document.getElementById(id2).disabled=true;
		document.getElementById(id3).disabled=true;
		document.getElementById(id4).disabled=true;
		document.getElementById(id5).disabled=true;
		document.getElementById(id6).disabled=true;
	}else if(document.getElementById(main_id).checked==false) {
		document.getElementById(id1).disabled=false;
		document.getElementById(id2).disabled=false;
		document.getElementById(id3).disabled=false;
		document.getElementById(id4).disabled=false;
		document.getElementById(id5).disabled=false;
		document.getElementById(id6).disabled=false;
	}
}

function chk_unchk_alcohol(main_id,id1,id2,id3,id4,id5) {
	
	if(document.getElementById(main_id).checked==false) {
		
		document.getElementById(id1).checked='unchecked';
		document.getElementById(id1).disabled=true;
		document.getElementById(id2).disabled=true;
		document.getElementById(id3).disabled=true;
		document.getElementById(id4).disabled=true;
		document.getElementById(id5).disabled=true;
		
	}else if(document.getElementById(main_id).checked==true) {
		
		document.getElementById(id1).disabled=false;
		document.getElementById(id2).disabled=false;
		document.getElementById(id3).disabled=false;
		document.getElementById(id4).disabled=false;
		document.getElementById(id5).disabled=false;
		
	}
}

function chk_unchk_rdo(rdo1,rdo2) {
	if(document.getElementById(rdo1).checked==false) {
		document.getElementById(rdo1).checked=true;
	}else {
		document.getElementById(rdo1).checked=false;
	}
	//alert(document.getElementById(rdo1).checked);
}
/*
function checkSingle(elemId,grpName)
{
	var objGrp = document.getElementsByName(grpName);
	var objElem = document.getElementById(elemId);
	var len = objGrp.length;		
	if(objElem.checked == false)
	{		
		for(var i=0;i<len;i++)
		{
			if((objGrp[i].id != objElem.id) && (objGrp[i].checked == true) )
			{
				objGrp[i].click();
				objGrp[i].checked=false;
			}
		}	
	}
}
*/
/*function checkSingle(elemId,grpName)
{
	var objGrp = document.getElementsByName(grpName);
	var objElem = document.getElementById(elemId);
	var len = objGrp.length;		
	if(objElem.checked == true)
	{		
		for(var i=0;i<len;i++)
		{
			if((objGrp[i].id != objElem.id) && (objGrp[i].checked == true) )
			{
				objGrp[i].click();
				objGrp[i].checked=false;
			}
		}	
	}
}*/
function checkSingle(elemId,grpName)
{
	var obgrp = document.getElementsByName(grpName);
	var objele = document.getElementById(elemId);
	var len = obgrp.length;		
	if(objele.checked == true)
	{		
		for(var i=0;i<len;i++)
		{
			if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) )
			{
				//obgrp[i].click();
				obgrp[i].checked=false;
			}
		}	
	}
}

/*
function checkSingleByName(elemId,grpName)
{
	var obgrp = document.getElementsByName(grpName);
	var objele = document.getElementById(elemId);
	
	var len = obgrp.length;		
	if(objele.checked == true)
	{		
		//document.getElementsByName(elemId).value='Yes';
		//alert(document.getElementsByName(elemId)+'\n'+document.getElementsByName(elemId).value);
		for(var i=0;i<len;i++)
		{
			if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) )
			{
				//obgrp[i].click();
				obgrp[i].checked=false;
			}
		}	
	}
}
*/ 
function enable_id(list_id,textarea_id) {
		if(document.getElementById(list_id).value=='other') {
			document.getElementById(textarea_id).disabled=false;
		} else {
			document.getElementById(textarea_id).disabled=true;
		}
}

function disp_hide_id(list_id,textarea_id) {
		//alert(document.getElementById(list_id).value);
		if(document.getElementById(list_id).value=='other') {
			document.getElementById(textarea_id).style.display="inline-block";
		} else {
			document.getElementById(textarea_id).style.display="none";
		}
}

function list_disp(chbx_id,main_id, sub_id) {
		//alert(document.getElementById(chbx_id).checked);
		if(document.getElementById(chbx_id).checked==true) {
			//document.getElementById(chbx_id).checked=true;
			document.getElementById(main_id).style.display="inline-block";
			document.getElementById(sub_id).style.display="inline-block";
		}else if(document.getElementById(chbx_id).checked==false) {
			//document.getElementById(chbx_id).checked==false;
			document.getElementById(main_id).style.display="none";
			document.getElementById(sub_id).style.display="none";
		}
}

function list_disp_txtarea(chbx_id,main_id, sub_id) {
		
		if(document.getElementById(chbx_id).checked==true) {
			document.getElementById(main_id).style.display="inline-block";
			//document.getElementById(sub_id).style.display="inline-block";
		}else if(document.getElementById(chbx_id).checked==false) {
			document.getElementById(main_id).style.display="none";
			if(document.getElementById(sub_id)) {
				document.getElementById(sub_id).style.display="none";
			}
		}
}

function disp_hide_checked_row_id(chbx_id,row_id) {
		//alert(document.getElementById(chbx_id).checked);
		if(document.getElementById(chbx_id).checked==true) {
			//document.getElementById(chbx_id).checked=true;
			document.getElementById(row_id).style.display="inline-block";
		}else if(document.getElementById(chbx_id).checked==false) {
			//document.getElementById(chbx_id).checked==false;
			document.getElementById(row_id).style.display="none";
		}
}

function disp_hide_row_id(row_id) {
	/* Safari Compatible */
	var disp_row = "inline-block";
	if(arguments[1]){
		disp_row = arguments[1];
	}

	if(document.getElementById(row_id).style.display=="none") {
			document.getElementById(row_id).style.display=disp_row;
	}else if(document.getElementById(row_id).style.display==disp_row) {
			document.getElementById(row_id).style.display="none";
	}
	/* Safari Compatible */
}

function toggle_date_range(obj, id){
	
	if($(obj).is(":checked")) {
		$("#"+id).slideDown('slow');
	}
	else{
		$("#"+id).slideUp('slow');
	}
}

function disp_row_id(row_id) {
		document.getElementById(row_id).style.display="inline-block";
		
}
function disp_row_2(row_id,top) {
	//alert(top)
		document.getElementById(row_id).style.display="inline-block";
		document.getElementById(row_id).style.top=top;
}

function save_hide_row_id(row_id) {
	document.getElementById(row_id).style.display="none";
}


function disp_one_hide_other(one_id,other_id) {
	document.getElementById(one_id).style.display="inline-block";
	document.getElementById(other_id).style.display="none";
}
function disp_one_hide_other_onchange(drop_down_id,one_id,other_id) {
		//alert(document.getElementById(drop_down_id).value);
		if(document.getElementById(drop_down_id).value=='other') {
			document.getElementById(one_id).style.display="none";
			document.getElementById(other_id).style.display="inline-block";
		} else if(document.getElementById(drop_down_id).value!=''  && document.getElementById(drop_down_id).value!='other') {
			document.getElementById(one_id).style.display="inline-block";
			document.getElementById(other_id).style.display="none";
		} else {
			document.getElementById(one_id).style.display="none";
			document.getElementById(other_id).style.display="none";
			
		}
}

function disp_one_hide_other_onchangeNew(drop_down_id,one_id,other_id,chbx_heparin_lockStart,chbx_iv,iv_sub_id) {
		
		//if(document.getElementById(chbx_heparin_lockStart).checked==true) {
			if(document.getElementById(drop_down_id).value=='other') {
				document.getElementById(one_id).style.display="none";
				document.getElementById(other_id).style.display="inline-block";
			} else if(document.getElementById(drop_down_id).value!=''  && document.getElementById(drop_down_id).value!='other') {
				if(document.getElementById(chbx_heparin_lockStart).checked==true || document.getElementById(chbx_iv).checked==true) {
					document.getElementById(one_id).style.display="inline-block";
				}else {
					document.getElementById(one_id).style.display="none";
				}
				if(document.getElementById(chbx_iv).checked==true) {
					if(document.getElementById(iv_sub_id)) {
						document.getElementById(iv_sub_id).style.display="inline-block";
					}
				}else {
					if(document.getElementById(iv_sub_id)) {
						document.getElementById(iv_sub_id).style.display="none";
					}
				}
				document.getElementById(other_id).style.display="none";
			} else {
				document.getElementById(one_id).style.display="none";
				document.getElementById(other_id).style.display="none";
			}
		/*
		}else {
			document.getElementById(one_id).style.display="none";
			document.getElementById(other_id).style.display="none";
		}*/
}
function all_hide(id1,id2) {
	document.getElementById(id1).style.display="none";
	document.getElementById(id2).style.display="none";
}

function all_disp(id1,id2) {
	document.getElementById(id1).style.display="inline-block";
	document.getElementById(id2).style.display="inline-block";

}

function get_today_date(dt_id)
{
	//temp_dt_id=dt_id;
	var today=new Date();
	var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	var d=today.getDate();
	var mt=today.getMonth();
	mt=mt+1;
	var y=today.getYear();
	
	var dn="PM"
			if (h<12)
				dn="AM"
			if (h>12)
				h=h-12
			if (h==0)
				h=12
			if (mt<=9)
				mt="0"+mt
			if (d<=9)
				d="0"+d
// add a zero in front of numbers<10
		m=today_checkTime(m);
		s=today_checkTime(s);
		//document.getElementById(dt_id).innerHTML=mt+"/"+d+"/"+y;
		document.getElementById(dt_id).innerHTML=mt+"/"+d+"/"+y+" "+h+":"+m+":"+s+" "+dn;
		
		//t=setTimeout("get_today_date('dt_time_id')",1000);
}


function get_today_date_pat_confirm(dt_id)
{
	//temp_dt_id=dt_id;
	var today=new Date();
	var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	var d=today.getDate();
	var mt=today.getMonth();
	mt=mt+1;
	var y=today.getYear();
	
	var dn="PM"
			if (h<12)
				dn="AM"
			if (h>12)
				h=h-12
			if (h==0)
				h=12
			if (mt<=9)
				mt="0"+mt
			if (d<=9)
				d="0"+d
// add a zero in front of numbers<10
		m=today_checkTime(m);
		s=today_checkTime(s);
		//document.getElementById(dt_id).innerHTML=mt+"/"+d+"/"+y;
		document.getElementById(dt_id).innerHTML=mt+"/"+d+"/"+y+" "+h+":"+m+" "+dn;
		
		t=setTimeout("get_today_date_pat_confirm('pat_conf_currdt_id')",1000);
}


function today_checkTime(i)
{
	if (i<10)
	  {
	  i="0" + i;
	  }
	return i;
}
function get_only_today_date(dt_id)
{
	//temp_dt_id=dt_id;
	var today=new Date();
	var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	var d=today.getDate();
	var mt=today.getMonth();
	mt=mt+1;
	var y=today.getYear();
	
	var dn="PM"
			if (h<12)
				dn="AM"
			if (h>12)
				h=h-12
			if (h==0)
				h=12
			if (mt<=9)
				mt="0"+mt
			if (d<=9)
				d="0"+d
// add a zero in front of numbers<10
		m=today_checkTime(m);
		s=today_checkTime(s);
		//document.getElementById(dt_id).innerHTML=mt+"/"+d+"/"+y;
		document.getElementById(dt_id).innerHTML=mt+"/"+d+"/"+y;
		
		//t=setTimeout("get_today_date('dt_time_id')",1000);
}

////CODE EDIT BY SURINDER
function footer_bgcolor(bgcolor_code) {
	//alert(document.getElementById(elem_id));
	top.footer_button_id.style.display="inline-block";
	top.footer_button_id.style.background=bgcolor_code;
}


///END CODE EDIT BY SURINDER
////////////////////////// PRE DEFIENED POP UPS
function showPreDefineFn(name1, name2, c, posLeft, posTop){	
	var rows = document.getElementById("hidd_count_rows_in_medication_table").value;
	c = rows;
	document.getElementById("evaluationPreDefineDiv").style.display = 'inline-block';
	document.getElementById("evaluationPreDefineDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPreDefineDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
		if(top.frames[0]){
			top.frames[0].setPNotesHeight();
		}
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showPreDefineFnNew(name1, name2, c, posLeft, posTop){	
	//alert(top.document.getElementById("evaluationPreDefineDiv"));
	/*if(typeof(arguments[2])=="object")
	{
		var table_name = arguments[2].table_name;
		var table_obj = document.getElementById(table_name);
		var total_rows = table_obj.rows.length;
		c = total_rows;
	}*/
	
	var rows = document.getElementById("hidd_count_rows_in_table").value;
	c = rows;
	top.document.getElementById("evaluationPreDefineDiv").style.display = 'inline-block';
	
	top.document.getElementById("evaluationPreDefineDiv").style.left = posLeft+'px';
	top.document.getElementById("evaluationPreDefineDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
		if(top.frames[0]){
			top.frames[0].setPNotesHeight();
		}
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}


function changeColorFn(obj, r, obj2){
	
	for(i=1;i<=r;i++){
		if(document.getElementById(obj2+''+i)) {
			var obj1 = document.getElementById(obj2+''+i);		
			//obj1.bgColor = "#E0E0E0";
			//obj.style.background = "#E0E0E0";
		}
	}
	if(obj) {
		//obj.bgColor = "#ECF1EA";
		obj.style.background = "#ECF1EA";
	} 
}

/*
function getInnerHTMLFn(obj, c){
	var objectName = document.getElementById("divId").value;
	var objectCounter = document.getElementById("counter").value;
	var objSecondary = document.getElementById("secondaryValues").value;
	if(!isNaN(objectCounter)){
		for(i=1;i<=objectCounter; i++){
			if(document.getElementById(objectName+i)){
				if(document.getElementById(objectName+i).value==''){
					var str = obj.innerHTML;
					if(str.indexOf(" - ")!=-1){
							posStr1 = str.indexOf(" - ");
							var str1 = str.substr(0, posStr1);							
							var posStr2 = posStr1+3;
							var str2 = str.substr(posStr2);
					}
					if(str1!=''){ var str = str1; }
					document.getElementById(objectName+i).value = str;					
					if((objSecondary!='') || (objSecondary!=0)){
						document.getElementById(objSecondary+i).value = str2+' '+i;
					}
					break;					
				}
			}
		}	
	}else{
		document.getElementById(objectName).value = obj.innerHTML;
	}
	//closePreDefineDiv();
}
*/
var inner_framename="";
/*
function getInnerHTMLFn(obj, c,inner_framename){	
	//alert(document.getElementById("divId").value);
	var objectName = document.getElementById("divId").value;
	var objectCounter = document.getElementById("counter").value;
	var objSecondary = document.getElementById("secondaryValues").value;
	if(inner_framename) {
		var inner_framename = eval(inner_framename);
		if(!isNaN(objectCounter)){
			for(i=1;i<=objectCounter; i++){
				//alert(objectName);
				if(inner_framename.document.getElementById(objectName+i)){
					if(inner_framename.document.getElementById(objectName+i).value==''){
						var str = obj.innerHTML;

						if(str.indexOf(" - ")!=-1){
								posStr1 = str.indexOf(" - ");
								var str1 = str.substr(0, posStr1);							
								var posStr2 = posStr1+3;
								var str2 = str.substr(posStr2);
						}
						if(str1!=''){ var str = str1; }
						inner_framename.document.getElementById(objectName+i).value = str;					
						if((objSecondary!='') || (objSecondary!=0)){
							inner_framename.document.getElementById(objSecondary+i).value = str2+' '+i;
						}
						break;					
					}
				}
			}	
		}else{
			
			inner_framename.document.getElementById(objectName).value = obj.innerHTML;
		}
		
	}else {
		if(!isNaN(objectCounter)){
			for(i=1;i<=objectCounter; i++){
				//alert(objectName);
				if(document.getElementById(objectName+i)){
					if(document.getElementById(objectName+i).value==''){
						var str = obj.innerHTML;
						
						if(str.indexOf(" - ")!=-1){
								posStr1 = str.indexOf(" - ");
								var str1 = str.substr(0, posStr1);							
								var posStr2 = posStr1+3;
								var str2 = str.substr(posStr2);
						}
						if(str1!=''){ var str = str1; }
						document.getElementById(objectName+i).value = str;					
						if((objSecondary!='') || (objSecondary!=0)){
							document.getElementById(objSecondary+i).value = str2+' '+i;
						}
						break;					
					}
				}
			}	
		}else{
			
			document.getElementById(objectName).value = obj.innerHTML;
		}
	}
	//closePreDefineDiv();
}
*/

function getInnerHTMLFn(obj, c,inner_framename,objfrmNumber){
	//alert(inner_framename);
	var objectName = top.frames[0].frames[0].document.getElementById("divId").value;
	var objectCounter = top.frames[0].frames[0].document.getElementById("counter").value;
	var objSecondary = top.frames[0].frames[0].document.getElementById("secondaryValues").value;
	
	var objHiddTertiary = "";
	if(top.frames[0].frames[0].document.getElementById("hiddTertiaryValue")) {
		if(top.frames[0].frames[0].document.getElementById("hiddTertiaryValue").value != "") {
			objHiddTertiary = top.frames[0].frames[0].document.getElementById("hiddTertiaryValue").value;
		}
	}
	
	
	if(inner_framename) {
		//alert(inner_framename);
		var inner_framename = top.frames[0].frames[0];
		/*
		if(inner_framename=='iframe_allergies_oproom_rec') {
			objfrmNumber=1;	//SECOND FRAME
		}else if(inner_framename=='iframe_medication_op_room_record_id') {
			objfrmNumber=2;  //THIRD FRAME
		}
		if(objfrmNumber) {
			var inner_framename = top.frames[0].frames[0].frames[objfrmNumber];
		}else {
			var inner_framename = top.frames[0].frames[0].frames[0];
			
		}
		*/
		
		if(!isNaN(objectCounter)){
			for(i=1;i<=objectCounter; i++){
				if(inner_framename.document.getElementById(objectName+i)){
					if(inner_framename.document.getElementById(objectName+i).value==''){
						
						//var str = obj.;
						var str = obj;//
						if(str.indexOf(" - ")!=-1){
								/*
								posStr1 = str.indexOf(" - ");
								var str1 = str.substr(0, posStr1);							
								var posStr2 = posStr1+3;
								var str2 = str.substr(posStr2);
								*/
								var strArr = new Array();
								strArr = str.split(" - ");
								str1 = strArr[0]; 
								str2 = strArr[1]; 
								str3 = strArr[2];
								
						}else{
							posStr1 = str.indexOf("  ");
								var str1 = str.substr(0, posStr1);							
								var posStr2 = "";
								var str2 = "";
								var str3 = "";
						}
						if(str1!=''){ var str = str1; }
						inner_framename.document.getElementById(objectName+i).value = str;					
						if((objSecondary!='') || (objSecondary!=0)){
							inner_framename.document.getElementById(objSecondary+i).value = str2;
						}
						if((objHiddTertiary!='') || (objHiddTertiary!=0)){
							inner_framename.document.getElementById(objHiddTertiary+i).value = str3;
						}
						break;					
					}
				}
			}
			
			/*if(objectCounter==i)
			{
				var frames = top.frames[0].frames[0];
				if(top.frames[0].frames[0].document.getElementById("table1"))
				{
					frames.document.getElementById("counter").value = parseInt(i)+1;
					frames.add_new_rows();
				}
			}*/
		}else{
			
			//inner_framename.document.getElementById(objectName).value = obj.innerHTML;
			inner_framename.document.getElementById(objectName).value = obj.value;
			
		}
		
	}else {
		if(!isNaN(objectCounter)){
			for(i=1;i<=objectCounter; i++){
				//alert(objectName);
				if(document.getElementById(objectName+i)){
					if(document.getElementById(objectName+i).value==''){
						//var str = obj.innerHTML;
						var str = obj.innerText;
						
						if(str.indexOf(" - ")!=-1){
								posStr1 = str.indexOf(" - ");
								var str1 = str.substr(0, posStr1);							
								var posStr2 = posStr1+3;
								var str2 = str.substr(posStr2);
						}else{
							posStr1 = str.indexOf("  ");
								var str1 = str.substr(0, posStr1);							
								var posStr2 = "";
								var str2 = "";
						}
						if(str1!=''){ var str = str1; }
						document.getElementById(objectName+i).value = str;					
						if((objSecondary!='') || (objSecondary!=0)){
							document.getElementById(objSecondary+i).value = str2;
						}
						break;					
					}
				}
			}	
		}else{
			
			//document.getElementById(objectName).value = obj.innerHTML;
			document.getElementById(objectName).value = obj.value;
		}
	}
	//closePreDefineDiv();
}
//code
function showPreDefineMedFn(name1, name2, c, posLeft, posTop){	
	if(document.getElementById("hidd_count_rows_in_medication_table"))
	{
		var table_obj = document.getElementById("hidd_count_rows_in_medication_table");
		var table_rows = table_obj.value;
		c = table_rows;
	}
	document.getElementById("evaluationPreDefineMedDiv").style.display = 'inline-block';
	document.getElementById("evaluationPreDefineMedDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPreDefineMedDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPreDefineMedFnNew(name1, name2, c, posLeft, posTop){
	
	if(document.getElementById("hidd_count_rows_in_medication_table"))
	{
		var table_obj = document.getElementById("hidd_count_rows_in_medication_table");
		var table_rows = table_obj.value;
		c = table_rows;
	}
	top.document.getElementById("evaluationPreDefineMedDiv").style.display = 'inline-block';
	top.document.getElementById("evaluationPreDefineMedDiv").style.left = posLeft+'px';
	top.document.getElementById("evaluationPreDefineMedDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function preDefineSavedHealthQuestMedFn(name1, name2, c, posLeft, posTop){
	
	if(document.getElementById("hidd_count_rows_in_medication_table"))
	{
		var table_obj = document.getElementById("hidd_count_rows_in_medication_table");
		var table_rows = table_obj.value;
		c = table_rows;
	}
	top.document.getElementById("preDefineSavedHealthQuestMedDiv").style.display = 'inline-block';
	top.document.getElementById("preDefineSavedHealthQuestMedDiv").style.left = posLeft+'px';
	top.document.getElementById("preDefineSavedHealthQuestMedDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPreDefineMedNurseFn(name1, name2, c, posLeft, posTop){	
	
	document.getElementById("evaluationPreDefineMedNurseDiv").style.display = 'inline-block';
	document.getElementById("evaluationPreDefineMedNurseDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPreDefineMedNurseDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPreDefineDiagnosisFn(name1, name2, c, posLeft, posTop){	
	document.getElementById("evaluationPreDiagnosisDiv").style.display = 'inline-block';
	document.getElementById("evaluationPreDiagnosisDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPreDiagnosisDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPreDefineProcNotes(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationProcedureNotesDiv").style.display = 'inline-block';
	document.getElementById("evaluationProcedureNotesDiv").style.left = posLeft+'px';
	document.getElementById("evaluationProcedureNotesDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPreDefineModelFn(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationPreDefineModelDiv").style.display = 'block';
	document.getElementById("evaluationPreDefineModelDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPreDefineModelDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

//START
function showPreDefineSuppliesUsedFn(name1, name2, c, posLeft, posTop){	

	if(document.getElementById("evaluationPreDefineSuppliesUsedDiv"))
	{	
		document.getElementById("evaluationPreDefineSuppliesUsedDiv").style.display = 'inline-block';
		document.getElementById("evaluationPreDefineSuppliesUsedDiv").style.left = posLeft+'px';
		document.getElementById("evaluationPreDefineSuppliesUsedDiv").style.top = posTop+'px';
		document.getElementById("divId").value = name1;
		document.getElementById("counter").value = c;
		document.getElementById("secondaryValues").value = name2;	
		//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
			if(document.getElementById("hiddPreDefineId")) {
				document.getElementById("hiddPreDefineId").value = "";
				if(document.getElementById("hiddPreDefineId").value == "") {
					preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
				}
			}
		//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		
		//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
			top.frames[0].setPNotesHeight();
		//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
	}
}

//END
function showProceduresFn(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationProceduresDiv").style.display = 'inline-block';
	document.getElementById("evaluationProceduresDiv").style.left = posLeft+'px';
	document.getElementById("evaluationProceduresDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function closePreOpMedicationDiv() {
	if(document.getElementById('PreOpMedicationDiv')) {
		document.getElementById('PreOpMedicationDiv').style.display='none';
	}
}
function closeevaluationPreOpMedDiv() {
	if(document.getElementById("evaluationPreOpMedDiv")) {
		document.getElementById("evaluationPreOpMedDiv").style.display = 'none';
	}
}
//function for the pre medication order table
function showPreMedsFn(name1, name2, c, posLeft, posTop){	
	document.getElementById("evaluationPreOpMedDiv").style.display = 'inline-block';
	document.getElementById("evaluationPreOpMedDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPreOpMedDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
//
function showPreOpMedicationFn(name1,name2,c,posLeft,posTop){
	document.getElementById('selected_frame_name_id').value='';
	//alert(document.getElementById("PreOpMedDivss"));
	document.getElementById("PreOpMedDiv").style.display = 'inline-block';
	document.getElementById("PreOpMedDiv").style.left = posLeft+'px';
	document.getElementById("PreOpMedDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
}
//

function showPatientTakeHome(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationTakeHomeDiv").style.display = 'inline-block';
	document.getElementById("evaluationTakeHomeDiv").style.left = posLeft+'px';
	document.getElementById("evaluationTakeHomeDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPatientTakeHomeNew(name1, name2, c, posLeft, posTop){	
	top.document.getElementById("evaluationTakeHomeDiv").style.display = 'inline-block';
	top.document.getElementById("evaluationTakeHomeDiv").style.left = posLeft+'px';
	top.document.getElementById("evaluationTakeHomeDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showFoodListFn(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationFoodListDiv").style.display = 'inline-block';
	document.getElementById("evaluationFoodListDiv").style.left = posLeft+'px';
	document.getElementById("evaluationFoodListDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPreCommentsFn(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationPreCommentsDiv").style.display = 'inline-block';
	document.getElementById("evaluationPreCommentsDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPreCommentsDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPreCommentsFnNew(name1, name2, c, posLeft, posTop){	
	var t;
	if(document.getElementById("rowK")) {
		t=document.getElementById("rowK").value;
	}else {
		t=0;
	}
	if(t!=0) {
		t=t*20;
	}
	//alert(t);
	document.getElementById("evaluationPreCommentsDiv").style.display = 'inline-block';
	document.getElementById("evaluationPreCommentsDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPreCommentsDiv").style.top = t*1+parseInt(posTop)+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)

	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPostSiteFn(name1, name2, c, posLeft, posTop){	

	var t;
	if(document.getElementById("rowK")) {
		t=document.getElementById("rowK").value;
	}else {
		t=0;
	}
	if(t!=0) {
		t=t*21;
	}
	
	document.getElementById("evaluationPostSiteDiv").style.display = 'inline-block';
	document.getElementById("evaluationPostSiteDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPostSiteDiv").style.top = t+parseInt(posTop)+'px';
	//alert(t+parseInt(posTop));
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showNourishmentKind(name1, name2, c, posLeft, posTop){	

	var t;
	if(document.getElementById("rowK")) {
		t=document.getElementById("rowK").value;
	}else {
		t=0;
	}
	if(t!=0) {
		t=t*21;
	}
	document.getElementById("evaluationNKindDiv").style.display = 'inline-block';
	document.getElementById("evaluationNKindDiv").style.left = posLeft+'px';
	document.getElementById("evaluationNKindDiv").style.top = t*1+parseInt(posTop)+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showRecoveryCommentsFn(name1, name2, c, posLeft, posTop){	

	var t;
	if(document.getElementById("rowK")) {
		t=document.getElementById("rowK").value;
	}else {
		t=0;
	}
	if(t!=0) {
		t=t*20;
	}
	document.getElementById("evaluationRecoveryDiv").style.display = 'inline-block';
	document.getElementById("evaluationRecoveryDiv").style.left = posLeft+'px';
	document.getElementById("evaluationRecoveryDiv").style.top = t*1+parseInt(posTop)+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showComplicationsFn(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationComplicationsDiv").style.display = 'inline-block';
	document.getElementById("evaluationComplicationsDiv").style.left = posLeft+'px';
	document.getElementById("evaluationComplicationsDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showIntraOpPostOpOrderFn(name1, name2, c, posLeft, posTop){	

	document.getElementById("intraOpPostOpDiv").style.display = 'inline-block';
	document.getElementById("intraOpPostOpDiv").style.left = posLeft+'px';
	document.getElementById("intraOpPostOpDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showNurseNotesFn(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationNurseNotesDiv").style.display = 'inline-block';
	document.getElementById("evaluationNurseNotesDiv").style.left = posLeft+'px';
	document.getElementById("evaluationNurseNotesDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPostDefineDiagnosisFn(name1, name2, c, posLeft, posTop){	
	document.getElementById("evaluationPostDiagnosisDiv").style.display = 'inline-block';
	document.getElementById("evaluationPostDiagnosisDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPostDiagnosisDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPostOpDropsFn(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationPostOpDropsDiv").style.display = 'inline-block';
	document.getElementById("evaluationPostOpDropsDiv").style.left = posLeft+'px';
	document.getElementById("evaluationPostOpDropsDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showEvaluationFn(name1, name2, c, posLeft, posTop){	
	document.getElementById("evaluationEvaluationDiv").style.display = 'inline-block';
	document.getElementById("evaluationEvaluationDiv").style.left = posLeft+'px';
	document.getElementById("evaluationEvaluationDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showEvaluationLocalAnesFn(name1, name2, c, posLeft, posTop){	
	document.getElementById("evaluationLocalAnesEvaluationDiv").style.display = 'inline-block';
	document.getElementById("evaluationLocalAnesEvaluationDiv").style.left = posLeft+'px';
	document.getElementById("evaluationLocalAnesEvaluationDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
		
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
		
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showDentationLocalAnesFn(name1, name2, c, posLeft, posTop){	
	document.getElementById("dentationLocalAnesDentationDiv").style.display = 'inline-block';
	document.getElementById("dentationLocalAnesDentationDiv").style.left = posLeft+'px';
	document.getElementById("dentationLocalAnesDentationDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
		
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
		
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showEkgBigRowDiv(name1, name2, c, posLeft, posTop){	
	document.getElementById("ekgLocalAnesDiv").style.display = 'inline-block';
	document.getElementById("ekgLocalAnesDiv").style.left = posLeft+'px';
	document.getElementById("ekgLocalAnesDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showPostopEvaluationFn(name1, name2, c, posLeft, posTop){	
	document.getElementById("postop_evaluationEvaluationDiv").style.display = 'inline-block';
	document.getElementById("postop_evaluationEvaluationDiv").style.left = posLeft+'px';
	document.getElementById("postop_evaluationEvaluationDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

//End by


function getInnerHTMLTextareaFn(obj, obj2){
	document.getElementById("evaluationPreDefineDiv").style.display = 'none';
	document.getElementById(obj2).value = obj.innerHTML;	
}
function closePreDefineDiv(){
	//var divs=document.getElementsByTagName("DIV");
	var divs=top.document.getElementsByTagName("DIV");
	for(i=0, x=divs.length; i<x; i++){
		(divs[i].id.match("PreDefineDiv")) ? divs[i].style.display = 'none' : null;
	}
}


function closePreDefineDiv1(){
	//var divs=document.getElementsByTagName("DIV");
	var divs=document.getElementsByTagName("DIV");
	for(i=0, x=divs.length; i<x; i++){
		(divs[i].id.match("PreDefineDiv")) ? divs[i].style.display = 'none' : null;
	}
}

////////////////////////// PRE DEFIENED POP UPS

// FUNCTION TO SAVE FORMS AND ALLERGIES AND MEDICATIONS
function getFormToSave(frmName){
	var frmObj = top.frames[0].document.forms[0];	
	//alert(frmObj.name);
	if(frmName=='healthQues'){
		// ALLERGIES FRAME FORM
			var allergiesFrm = top.frames[0].frames[0].document.forms[0];
			allergiesFrm.submit();
		// ALLERGIES FRAME FORM
		
		// MEDICATION FRAME FORM
			var medicationFrm = top.frames[0].frames[1].document.forms[0];
			medicationFrm.submit();
		// MEDICATION FRAME FORM
	}	
	frmObj.submit();
}
// FUNCTION TO SAVE FORMS AND ALLERGIES AND MEDICATIONS

////////////////////////// PRE DEFIENED POP UPS

//FUNCTION TO EXPAND / COLLASPE SLIDER
function exp_collapse_slider(id) {
	//alert(document.getElementById(id).name);
	//alert(document.getElementById(id).style.display);
	if(document.getElementById(id).style.display=="inline-block") {
		document.getElementById(id).style.display="none";
	}else if(document.getElementById(id).style.display=="none") {
		document.getElementById(id).style.display="inline-block";
	}
}
function exp_collapse_category(id) {
	
		//alert(document.getElementById(id).style.display);

	if(document.getElementById(id).style.display=="inline-block") {
		document.getElementById(id).style.display="none";
	}else if(document.getElementById(id).style.display=="none") {
		document.getElementById(id).style.display="inline-block";
	}
}

//END FUNCTION TO EXPAND / COLLASPE SLIDER

//START CODE TO RESET THE FORM(LIKE HEALTH-QUESTIONNAIRE)
function getFormReset(){	
	var frmObj = top.mainFrame.main_frmInner.document.forms[0];
	if(!confirm('Reset Record! Are you sure ?')) { 
		//DO NOT RESET RECORD
	}else {
		if(frmObj) {
			if(frmObj.hiddResetStatusId){
				//alert(frmObj.hiddResetStatusId);
				frmObj.hiddResetStatusId.value='Yes';
			}
			rSetAll();
			getFormSave();
			
		}
	}
}

//END CODE TO RESET THE FORM

//START CODE TO PURGE AND RESET THE FORM(LIKE INSTRUCTION SHEET)
function getFormPurgeReset(pConfId, form_status){	
	var frmObj = top.mainFrame.main_frmInner.document.forms[0];
	top.document.getElementById("PurgeBtn").onclick = function fun() {
		if(form_status==""){
			alert("Please save this chart before purge");
		}else if(!confirm('Purge Record! Are you sure ?')) {
			
		}else {
			xmlHttpFinal=GetXmlHttpObject();
			if (xmlHttpFinal==null){
				alert ("Browser does not support HTTP Request");
				return;
			} 
			url="instructionsheet_printpop.php?pConfId="+pConfId+"&hiddPurgeResetStatusId=Yes";
			xmlHttpFinal.onreadystatechange= function () {
				if (xmlHttpFinal.readyState==4 || xmlHttpFinal.readyState=="complete") {
					//alert(xmlHttpFinal.responseText);
					if(frmObj) {
						if(frmObj.hiddPurgeResetStatusId){
							frmObj.hiddPurgeResetStatusId.value='Yes';	
						}
						rSetAll();
						getFormSave();
					}
					
				}
			}
			xmlHttpFinal.open("GET",url,true)
			xmlHttpFinal.send(null)
		}
	}
}
//END CODE TO PURGE AND RESET THE FORM(LIKE INSTRUCTION SHEET)

//START SET ALL VALUES TO BLANK
function rSetAll() {
	//var frmObj = top.mainFrame.main_frmInner.document.forms[0];
	var frmObj = top.mainFrame.main_frmInner.document;
	var getTags = frmObj.getElementsByTagName('input');
	var getArea = frmObj.getElementsByTagName('textarea');
	var getSelect = frmObj.getElementsByTagName('select');
	if(getTags) {
		for(var i=0;i<getTags.length;i++) {
			if(getTags[i].type == 'checkbox') {
				getTags[i].checked=false;
			}
			if(getTags[i].type == 'text') {
				getTags[i].value='';
			}
		}
	}
	if(getArea) {
		for(var l=0;l<getArea.length;l++) {
			getArea[l].value='';
		}
	}
	if(getSelect) {
		for(var t=0;t<getSelect.length;t++) {
			getSelect[t].value='';
		}
	}
}
//END SET ALL VALUES TO BLANK

// FUNCTION TO SAVE FORMS EDIT BY SURINDER
function getFormSave(){	
	var frmObj = top.mainFrame.main_frmInner.document.forms[0];
	var flag = true;
	if(frmObj) {
		if(frmObj.formIdentity){
			var frmNameAction = frmObj.frmAction.value;
			if(frmObj.name=='frm_health_ques' && frmNameAction=='pre_op_health_quest.php'){
				//CHECK SIGN OF PATIENT AND WITNESS
				top.frames[0].frames[0].SetSigPreHlthPtSign();
			}else if(frmObj.name=='frm_op_room' && frmNameAction=='op_room_record.php'){
				var validSn = top.frames[0].frames[0].validate_sn();
				if(!validSn) {
					flag=false;
				}
			}	
		}
		
	
		if(typeof(top.frames[0].frames[0].getAppValue) != "undefined"){
			top.frames[0].frames[0].getAppValue();
		}
		//CODE FOR AMENDMENT NOTES
			if(frmObj.signOnFileAmendment) {
				if(frmObj.signOnFileAmendment.value!='userSignDone') {
					alert('Please make signature before save');
					flag = false;
				}
			}
		//CODE FOR AMENDMENT NOTES
	
		//SET SIGNATURE VALUE OF CONSENT FORM
			if(frmObj.name=='frm_consent_multiple' || frmObj.name=='instructionSheetForm') {
				/*
				if(top.frames[0].frames[0].document.SigPlus1) {
					var objSigPlus1 = top.frames[0].frames[0].document.SigPlus1;
					if(objSigPlus1.NumberOfTabletPoints!=0) {
						  objSigPlus1.TabletState=0;
						  objSigPlus1.EncryptionMode=1;
						  objSigPlus1.SigCompressionMode=2;
						  
						  frmObj.consentSurgery_patient_sign.value=objSigPlus1.SigString;
						
					}
				}
				*/
				if(typeof(top.document.getElementById('browserPlatform')) != "undefined") {
					if(top.document.getElementById('browserPlatform').value != "iPad") {
						var newfun  = top.frames[0].frames[0].SetSig();
						if(!newfun) {
							flag=false;
						}		
					}
				}
			}
			
			//START 
				if(top.document.getElementById('hidd_SaveOnSingAllAnes')) { //(FROM mainpage.php)
					if(top.document.getElementById('hidd_SaveOnSingAllAnes').value=='true') {
						if(frmObj.name=='frm_gen_anes_rec') {
							frmObj.SaveRecordForm.value='';
						}
					}
				}	
			//END 
		
		//SET SIGNATURE VALUE OF CONSENT FORM
	}else {
		flag=false;
		//START IF SURGEON CLICK ON 'SIGN ALL BUTTON' WITHOUT OPENING ANY CHARTNOTE THEN RUN THIS CODE
		if(top.document.getElementById('hidd_SaveOnSingAllSurgeon')) {
			if(top.document.getElementById('hidd_SaveOnSingAllSurgeon').value=='true') {
				top.document.getElementById('hidd_SaveOnSingAllSurgeon').value='';
				top.myTimerSurgeonSignAll();
			}
		}
		//END IF SURGEON CLICK ON 'SIGN ALL BUTTON' WITHOUT OPENING ANY CHARTNOTE THEN RUN THIS CODE
	
		//START IF ANESTHESIOLOGIST CLICK ON 'SIGN ALL BUTTON' BY OPENING ANY CHARTNOTE THEN RUN THIS CODE	
			if(top.document.getElementById('hidd_SaveOnSingAllAnes')) { //(FROM mainpage.php)
				if(top.document.getElementById('hidd_SaveOnSingAllAnes').value=='true') {
					top.document.getElementById('hidd_SaveOnSingAllAnes').value='';
					top.myTimerAnesSignAll(); //(FROM mainpage.php)
				}
			}	
		//END IF ANESTHESIOLOGIST CLICK ON 'SIGN ALL BUTTON' BY OPENING ANY CHARTNOTE THEN RUN THIS CODE	
			
	}
	
	//START CODE TO SET SCROLL TOP
	var scrlTop = top.mainFrame.main_frmInner.document.body.scrollTop;
	if(top.document.getElementById('hiddScrollTop')) { //hidden field at mainpage.php
		top.document.getElementById('hiddScrollTop').value=scrlTop;
	}
	//END CODE TO SET SCROLL TOP
	if(flag==true) {
		if(frmObj.name == "frm_local_anes_rec"){	
			if(frmObj.hidEnableHTMLGrid.value == "1"){
				top.mainFrame.main_frmInner.saveAnesthesiaGrid();
			}
		}
		if(frmObj.name == "frm_laser_procedure"){	
			if(frmObj.hiddchk_laser_procedure_image.value == "on") {
				if(frmObj.hidd_isHTML5OK) {
					if(frmObj.hidd_isHTML5OK.value == "1") {
						top.mainFrame.main_frmInner.saveCanvas(frmObj.name);
					}
				}
			}
		}
		if(!top.document.getElementById('saveBtn').hasAttribute('disabled')){
			top.document.getElementById('saveBtn').setAttribute('disabled','disabled');
			
		}
		frmObj.submit(); 
		
		//top.mainFrame.main_frmInner.document.body.scrollTop=scrlTop;
	}	
}
// END FUNCTION TO SAVE FORMS AND ALLERGIES AND MEDICATIONS

// FUNCTION TO CANCEL FORMS EDIT BY SURINDER
	
	function getFormCancel() {
		
		
		//var frmObj = top.mainFrame.main_frmInner.document.forms[1].submit();
		//top.document.getElementById("footer_button_id").style.display = "none";
		var frmObj = top.mainFrame.main_frmInner.document.frm_return_BlankMainForm.submit();
		/*top.document.getElementById('SliderHeadConsent').innerHTML = '';
		top.document.getElementById('SliderHeadTitle').innerHTML = '';
		top.document.getElementById('SliderHeadEpost').innerHTML = '';*/
		top.$('#SliderHeadConsent,#SliderHeadTitle,#SliderHeadEpost').html('').fadeOut(500);
		//parent.top.$('#footer_button_id').hide();
		
		
		
		/*
		var frmObj = top.frames[0].document.forms[1]; 	
		frmObj.submit();
		*/
	}
// END FUNCTION TO CANCEL FORMS EDIT BY SURINDER



// FUNCTION TO SAVE & PRINT FORMS 
/////////////
function FormSave_n_Print(){	
	var frmObj = top.frames[0].document.forms[0];
	var flag = true;
	if(frmObj.formIdentity){
		var frmName = frmObj.formIdentity.value;
		if(frmName=='healthQues'){
			// ALLERGIES FRAME FORM			
			if(top.frames[0].frames[0]){
				var allergiesFrm = top.frames[0].frames[0].document.forms[0];
				if(allergiesFrm){
					allergiesFrm.submit();
				}
			}
			// ALLERGIES FRAME FORM

			// MEDICATION FRAME FORM
			if(top.frames[0].frames[1]){	
				var medicationFrm = top.frames[0].frames[1].document.forms[0];
				medicationFrm.submit();
			}
			// MEDICATION FRAME FORM
			
			// PREVIOUS OPERATION FRAME FORM
			if(top.frames[0].frames[2]){
				if(top.frames[0].frames[2].document.forms[0]){
					var prevOperationFrm = top.frames[0].frames[2].document.forms[0];
					if(prevOperationFrm){
						prevOperationFrm.submit();
					}
				}
			}
			// PREVIOUS OPERATION FRAME FORM
		}	
	}
	
	
	if(typeof(top.frames[0].getAppValue) != "undefined"){
		top.frames[0].getAppValue();
	}
	//CODE FOR AMENDMENT NOTES
		if(frmObj.signOnFileAmendment) {
			if(frmObj.signOnFileAmendment.value!='userSignDone') {
				alert('Please make signature before save');
				flag = false;
			}
		}
	//CODE FOR AMENDMENT NOTES
	
	if(flag==true) {
		frmObj.submit();
		//window.open('op_room_record_pdf.php','','width=650,height=600,top=100,left=100,resizable=yes,scrollbar=1');

		
	}
}




/////////////





/*function FormSavePrint(){	
	var frmObj = top.frames[0].document.forms[0];	
	if(frmObj.formIdentity){
		var frmName = frmObj.formIdentity.value;
		
		//alert(frmObj.name);
		if(frmName=='healthQues'){
			// ALLERGIES FRAME FORM
			
			if(top.frames[0].frames[0]){
				
				var allergiesFrm = top.frames[0].frames[0].document.forms[0];
				allergiesFrm.submit();
			}			
			// ALLERGIES FRAME FORM
			
			// MEDICATION FRAME FORM
			
			if(top.frames[0].frames[1]){	
				var medicationFrm = top.frames[0].frames[1].document.forms[0];
				medicationFrm.submit();
			}
			
			// MEDICATION FRAME FORM
		
			// PREVIOUS OPERATION FRAME FORM
			
			if(top.frames[0].frames[2]){
				if(top.frames[0].frames[2].document.forms[0]){
					var prevOperationFrm = top.frames[0].frames[2].document.forms[0];
					prevOperationFrm.submit();
				}
			}
			// PREVIOUS OPERATION FRAME FORM
		
		}	
	}
	
	frmObj.submit();
	//window.print();
	window.open('operative_record_pdf.php','','width=650,height=600,top=100,left=100,resizable=yes,scrollbar=1');
}
// END FUNCTION TO SAVE FORMS AND ALLERGIES AND MEDICATIONS
*/

//DISPLAY DROP DOWN WHEN CHECKBOX IS CHECKED (DONE BY SURINDER)
function list_text_disp(chbx_id,main_id, sub_id,txt_id) {
		if(document.getElementById(chbx_id).checked==true) {
			document.getElementById(main_id).style.display="inline-block";
			document.getElementById(sub_id).style.display="inline-block";
		}else if(document.getElementById(chbx_id).checked==false) {
			document.getElementById(main_id).style.display="none";
			document.getElementById(sub_id).style.display="none";
			document.getElementById(txt_id).style.display="none";
		}
}

//END DISPLAY DROP DOWN WHEN CHECKBOX IS CHECKED (DONE BY SURINDER)

//FUNCTION TO DISABLE & ENABLE CHECKBOXES AND DROPDOWN IN PREOPGENERAL ANESTHESIA
function alcohol_chk_unchk(chbx_acl_yes_id,chbx_acl_no_id,weeksList_id) {
	if(document.getElementById(chbx_acl_no_id).checked==true) {
		
		document.getElementById(weeksList_id).disabled=true;
		//document.getElementById(acl_number_id).disabled=true;
		
	}else if(document.getElementById(chbx_acl_yes_id).checked==true) {
		
		document.getElementById(weeksList_id).disabled=false;
		//document.getElementById(acl_number_id).disabled=false;
		
	}else {
		
		document.getElementById(weeksList_id).disabled=true;
		//document.getElementById(acl_number_id).disabled=true;

	}
}
//END FUNCTION TO DISABLE & ENABLE CHECKBOXES AND DROPDOWN IN PREOPGENERAL ANESTHESIA

//FUNCTION TO DISABLE & ENABLE textarea IN Pre-op Nursing
function enable_chk_unchk(chbx_food_yes_id,chbx_food_no_id,txtarea_id) {
	if(document.getElementById(chbx_food_no_id).checked==true) {
		
		document.getElementById(txtarea_id).disabled=true;
		//document.getElementById(acl_number_id).disabled=true;
		document.getElementById(txtarea_id).value = '';
		
	}else if(document.getElementById(chbx_food_yes_id).checked==true) {
		
		document.getElementById(txtarea_id).disabled=false;
		//document.getElementById(acl_number_id).disabled=false;
		if(document.getElementById('defaultListFoodTake'))
		{
			var D = document.getElementById('defaultListFoodTake').value;
			if(document.getElementById(txtarea_id).value == '')
			{
				document.getElementById(txtarea_id).value = D;
			}
		}
		
	}else {
		
		document.getElementById(txtarea_id).disabled=true;
		document.getElementById(txtarea_id).value = '';
		//document.getElementById(acl_number_id).disabled=true;

	}
}
//END FUNCTION TO DISABLE & ENABLE textarea IN Pre-op Nursing

// 
function showProgressFn( posLeft, posTop){	
	document.getElementById("evaluationProgressDiv").style.display = 'inline-block';
	document.getElementById("evaluationProgressDiv").style.left = posLeft+'px';
	document.getElementById("evaluationProgressDiv").style.top = posTop+'px';
}
// 

// FINALIZE CURR DOS OF THE PATIENT
function finalize(){
	
	var frmObj = top.frames[0].frames[0].document.forms[2];
	//alert(frmObj.name);
	//frmObj.submit(); return false;
	var msg="Following ChartNotes must complete before finalize:- \n";
	var flag = 0;
	var anesChartInUse="";
	var chkDateExist=frmObj.chkListDateExist.value;
	var showCheckList = frmObj.showCheckList.value;
	
	var f0=frmObj.checkListFinalizeFormStatus.value;
	var f1=frmObj.surgeryConsentFinalizeFormStatus.value;
	var f2=frmObj.HippaConsentFinalizeFormStatus.value;
	var f3=frmObj.BenefitConsentFinalizeFormStatus.value;
	var f4=frmObj.InsuranceConsentFinalizeFormStatus.value;
	var f5=frmObj.preopHealthQuestFinalizeFormStatus.value;
	var f6=frmObj.preopNursingFinalizeFormStatus.value;
	var f7=frmObj.postopNursingFinalizeFormStatus.value;
	var f8=frmObj.preopPhysicianFinalizeFormStatus.value;
	var f9=frmObj.postopPhysicianFinalizeFormStatus.value;
	var f10=frmObj.macRegionalAnesthesiaFinalizeFormStatus.value;
	var f11=frmObj.preopGenralAnesthesiaFinalizeFormStatus.value;
	var f12=frmObj.GenralAnesthesiaFinalizeFormStatus.value;
	var f13=frmObj.genralAnesthesiaNursesNotesFinalizeFormStatus.value;
	var f14=frmObj.OpRoomRecordFinalizeFormStatus.value;
	var f15=frmObj.surgicalOperativeRecordFinalizeFormStatus.value;
	var f16=frmObj.dischargeSummaryFinalizeFormStatus.value;
	var f17=frmObj.InstructionSheetFinalizeFormStatus.value;
	
	//if(f1!='completed'){ msg = msg+"\t• Surgery\n"; ++flag; }
	//if(f2!='completed'){ msg = msg+"\t• HIPAA\n"; ++flag; }
	//if(f3!='completed'){ msg = msg+"\t• Assign Benefits\n"; ++flag; }
	//if(f4!='completed'){ msg = msg+"\t• Insurance Card\n"; ++flag; }
	//if(f5!='completed'){ msg = msg+"\t• Health Questionnaire\n"; ++flag; }
	if(chkDateExist=="yes" && showCheckList == 'true'){if(f0!='completed'){ msg = msg+"\u2003\u2022 Check List\n"; ++flag; }}
	if(f6!='completed'){ msg = msg+"\u2003\u2022 Pre-Op Nursing\n"; ++flag; }
	if(f7!='completed'){ msg = msg+"\u2003\u2022 Post-Op Nursing\n"; ++flag; }
	if(f8!='completed'){ msg = msg+"\u2003\u2022 Pre-Op Physician\n"; ++flag; }
	if(f9!='completed'){ msg = msg+"\u2003\u2022 Post-Op Physician\n"; ++flag; }
	if(f10=='' && f11=='' && f12=='' && f13==''){
		msg = msg+"\u2003\u2022 Use any one of charts"; ++flag;
		msg = msg+"\u2003\u2022 MAC/Regional\n"; ++flag;
		msg = msg+"\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2022 Pre-Op General\n"; ++flag;
		msg = msg+"\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2022 General\n"; ++flag;
		msg = msg+"\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2022 General Nurse Notes\n"; ++flag;
	}else {
		if(f10=='not completed'){ msg = msg+"\u2003\u2022 MAC/Regional\n"; ++flag; }
		if(f11=='not completed'){ msg = msg+"\u2003\u2022 Pre-Op General\n"; ++flag; }
		if(f12=='not completed'){ msg = msg+"\u2003\u2022 General\n"; ++flag; }
		if(f13=='not completed'){ msg = msg+"\u2003\u2022 General Nurse Notes\n"; ++flag; }
	}
	/*
	if(f10!='completed'){ msg = msg+"\u2003\u2022 MAC/Regional"; ++flag; }
	if(f10=='completed'){
		anesChartInUse="yes";
	}else {
		if(f11=='not completed'){ msg = msg+"\u2003\u2022 Pre-Op General\n"; ++flag; anesChartInUse="yes"; }
		if(f12=='not completed'){
			if(f11=='completed'){ 
				msg = msg+"\u2003\u2003\u2022 General\n"; ++flag;
			}else {
				msg = msg+"\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2022 General\n"; ++flag;
			}
			anesChartInUse="yes";
		}
		if(f13=='not completed'){ 
			if(f11=='completed' && f12=='completed'){
				msg = msg+"\u2003\u2022 General Nurse Notes\n"; ++flag;
			}else {
				msg = msg+"\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2003\u2022 General Nurse Notes\n"; ++flag; 
			}
			anesChartInUse="yes";
		}
	}
	if(anesChartInUse=="") { msg = msg+"\n"; }*/
	if(f14!='completed'){ msg = msg+"\u2003\u2022 Intra-Op Record\n"; ++flag; }
	//if(f15!='completed'){ msg = msg+"\t• Operative Report\n"; ++flag; }
	if(f16!='completed'){ msg = msg+"\u2003\u2022 Discharge Summary\n"; ++flag; }
	//if(f17!='completed'){ msg = msg+"\t• Instruction Sheet\n"; ++flag; }
	
	//START CODE TO CHECK IF GLOBAL CONFIGURATION IS "YES" THEN DO NOT CHECK ANY MANDATORY CHART TO FINALIZE
	if(top.document.getElementById("finalize_without_mandatory_charts")) {
		if(top.document.getElementById("finalize_without_mandatory_charts").value=="YES") {
			flag = 0;
		}
	}
	//END CODE TO CHECK IF GLOBAL CONFIGURATION IS "YES" THEN DO NOT CHECK ANY MANDATORY CHART TO FINALIZE
	
	if(flag > 0){
			alert(msg);
			return false;	
	}else {
		if(!confirm('Finalize Chart Notes! Are you sure ?')) { 
			return false;
		}else{
			frmObj.submit();
			return true;
		}
	}
	
	//frmObj.submit();
}
// FINALIZE CURR DOS OF THE PATIENT

// Finalize Injection Procedure
function finalize_injection_procedure() {
	
	var frmObj	= top.frames[0].frames[0].document.forms[2];
	
	if(frmObj)
	{
		var procedurestatus	=	frmObj.InjectionMiscFinalizeFormStatus.value;
		if(procedurestatus=='completed')
		{
			if(!confirm('Finalize Chart Notes! Are you sure ?'))
			{ 
				return false;
			}
			else
			{
				frmObj.submit();
				return true;	
			}
		}
		else
		{
			alert("Injection/Miscellaneous Procedure Form Incomplete");	
		}
	}
}
// End Finalize Injection Procedure


// FINALIZE Amendment Notes OF THE PATIENT
function finalizeAmendment() {
	//alert('hello');
	var frmAmendmentObj = top.frames[0].frames[0].document.forms[2];
	//alert(frmAmendmentObj.name);
	if(!confirm('Finalize Amendment Notes! Are you sure ?')) { 
		return false;
	}else{
		frmAmendmentObj.submit();
	}
	
}
// FINALIZE Amendment Notes OF THE PATIENT

//GET CURRENT TIME IN GEN ANES NURSE NOTES
function showTime_nurseNotes(sTimeID,newTimeID,newTimeTempID,hidd_timeID)
{
	var today=new Date();
    var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	var hid_tm = h+":"+m+":"+s;
	  var dn="PM"
			if (h<12)
				dn="AM"
			if (h>12)
				h=h-12
			if (h==0)
				h=126
				
			if(h<10) h='0'+h
			
// add a zero in front of numbers<10
		m=checkTime_nurseNotes(m);
		
		t=h+":"+m+" "+dn;
	   
	   document.getElementById(sTimeID).style.display="inline-block";
	   document.getElementById(newTimeID).innerHTML=t;
	   document.getElementById(newTimeTempID).style.display="none";
	   document.getElementById(hidd_timeID).value=hid_tm;
	   
	   
	   
		//alert(document.getElementById('newTime').value);
		
		//alert(t);
		//t=setTimeout('startTime()',1000);
}

function checkTime_nurseNotes(i)
{
	if (i<10)
	  {
	  i="0" + i;
	  }
	return i;
}

//END GET CURRENT TIME IN GEN ANES NURSE NOTES

//FUNCTION TO CLOSE MEDICATION DIV
function medi_close(id) {
	//alert(id);
	if(id=='evaluationPreDefineMedDiv') {
		top.document.getElementById(id).style.display = 'none';
	}else {
		document.getElementById(id).style.display = 'none';
	}
}
function medi_close1(id) {
	//alert(id);
		document.getElementById(id).style.display = 'none';
}
function medi_closeNew(id) {
	//alert(id);
		top.document.getElementById(id).style.display = 'none';
}

//ED FUNCTION TO CLOSE MEDICATION DIV
function showAllergiesPopUpFn(pConfId)
{	var winHe=parseInt($(document).height()/3);
	window.open('allergies_header_pop_up.php?pConfId='+pConfId,'allergies_pop_up_win', 'width=450,height=200,top='+winHe+',left=430,scrollbars=1');
	
}


// CODE TO DISPLAY ALLERGIES POPUP AT HEADER
function showAllergiesPopFn( posLeft, posTop){	
	document.getElementById("AllergiesHeaderPopDiv").style.display = 'inline-block';
	document.getElementById("AllergiesHeaderPopDiv").style.left = posLeft+'px';
	document.getElementById("AllergiesHeaderPopDiv").style.top = posTop+'px';
}
// END CODE TO DISPLAY ALLERGIES POPUP AT HEADER

// CODE TO CLOSE ALLERGIES POPUP AT HEADER
function closeAllergiesPopFn(){	
	document.getElementById("AllergiesHeaderPopDiv").style.display = 'none';
}
// END CODE TO CLOSE ALLERGIES POPUP AT HEADER

function showTime()
{
	var today=new Date();
    var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	var hid_tm = h+":"+m+":"+s;
	  var dn="PM"
			if (h<12)
				dn="AM"
			if (h>12)
				h=h-12
			if (h==0)
				h=12
				
			if(h<10) h='0'+h
			
// add a zero in front of numbers<10
		m=checkTime1(m);
		
		t=h+":"+m+" "+dn;
	   document.getElementById('newTime').innerText=t;
	   //document.getElementById('hidd_timeId').value=hid_tm;
	   //document.getElementById('sTime').style.display="inline-block";
	   //document.getElementById('newTimeTemp').style.display="none";
	   
	   
	   
		//alert(document.getElementById('newTime').value);
		
		//alert(t); 
		//t=setTimeout('startTime()',1000);
}

function checkTime1(i)
{
	if (i<10)
	  {
	  i="0" + i;
	  }
	return i;
}

function show1Time()
{
	var today=new Date();
    var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	var hid_tm = h+":"+m+":"+s;
	  var dn="PM"
			if (h<12)
				dn="AM"
			if (h>12)
				h=h-12
			if (h==0)
				h=12
				
			if(h<10) h='0'+h	
			
// add a zero in front of numbers<10
		m=checkTime2(m);
		
		t=h+":"+m+" "+dn;
	 
	   document.getElementById('newTime1').innerText=t;
	   //document.getElementById('hidd_time1Id').value=hid_tm;
	   //document.getElementById('s1Time').style.display="inline-block";
	   //document.getElementById('postopnurse_heparinLockOutTime_id').style.display="none";
		//alert(document.getElementById('newTime').value);
		
		//alert(t);
		//t=setTimeout('startTime()',1000);
}
function checkTime2(i)
{
	if (i<10)
	  {
	  i="0" + i;
	  }
	return i;
}
//end by munisha

//FUNCTION TO DISPLAY TIME IN TEXT BOX BY TAKING ARGUMENT (IN A,P) 

	function displayTime(text_id)
	{
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
			alert ("Browser does not support HTTP Request")
			return
		}
		var url="user_agent.php"
		url=url+"?jsServerTimeRequest=yes&pste="+Math.random();
		xmlHttp.onreadystatechange=function() {
			if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
			   var tmValTemp= xmlHttp.responseText;
			   tmValTemp 	= tmValTemp.replace(/\s/g,'');
			   var tmVal 	= tmValTemp.replace('M','');
			   if(document.getElementById(text_id)) {
					document.getElementById(text_id).value=tmVal;
			   }
			} 
		};
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	
	function checkDisplayTime(i)
	{
		if (i<10)
		  {
		  i="0" + i;
		  }
		return i;
	}
	
// END FUNCTION TO DISPLAY TIME IN TEXT BOX BY TAKING ARGUMENT (IN A,P)

//FUNCTION TO DISPLAY TIME IN TEXT BOX BY TAKING ARGUMENT (IN AM,PM)
	function displayTimeAmPm(text_id)
	{
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
			alert ("Browser does not support HTTP Request")
			return
		}
		var url="user_agent.php"
		url=url+"?jsServerTimeRequest=yes&pste="+Math.random();
		xmlHttp.onreadystatechange=function() {
			if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
			   if(document.getElementById(text_id)) {
					document.getElementById(text_id).value=xmlHttp.responseText;
			   }
			} 
		};
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	
	function checkDisplayTimeAmPm(i)
	{
		if (i<10)
		  {
		  i="0" + i;
		  }
		return i;
	}
	
// END FUNCTION TO DISPLAY TIME IN TEXT BOX BY TAKING ARGUMENT (IN AM,PM) 

//SHOW TIME INTERVAL
	function showIntervalMain(objvalue,intervalId_td) {
		var startTime = objvalue;
		if(startTime.substr(2,1)==":") {
			//if(startTime.length==6) {
				startTimeSplit = startTime.split(":");
				//if(startTimeSplit[0].length==2 && startTimeSplit[1].length==3) {
					var startHours = startTime.substr(0,2);
					var startMins = startTime.substr(3,2);
					var startAmPm = startTime.substr(5,1);
					
					var startHoursPlusOne = parseInt(startHours)+1;
					var startHoursPlusTwo = parseInt(startHours)+2;
					var startHoursPlusThree = parseInt(startHours)+3;
					if(startHoursPlusOne >= 24) {
						startHoursPlusOne = startHoursPlusOne-24;
					}
					if(startHoursPlusTwo >= 24) {
						startHoursPlusTwo = startHoursPlusTwo-24;
					}
					if(startHoursPlusThree >= 24) {
						startHoursPlusThree = startHoursPlusThree-24;
					}
					if(startHours == 8) {
						startHoursPlusOne = 9;
						startHoursPlusTwo = 10;
						startHoursPlusThree = 11;
					}
					if(startHours == 9) {
						startHoursPlusOne = 10;
						startHoursPlusTwo = 11;
						startHoursPlusThree = 12;
					}
					if(startHoursPlusOne > 12) {
						startHoursPlusOne = startHoursPlusOne-12;
						if(top.document.getElementById("show_military_time")) {
							if(top.document.getElementById("show_military_time").value=="YES") {
								startHoursPlusOne = parseFloat(startHoursPlusOne+12);
							}
						}
					}
					if(startHoursPlusTwo > 12) {
						startHoursPlusTwo = startHoursPlusTwo-12;
						if(top.document.getElementById("show_military_time")) {
							if(top.document.getElementById("show_military_time").value=="YES") {
								startHoursPlusTwo = parseFloat(startHoursPlusTwo+12);
							}
						}
					}
					if(startHoursPlusThree > 12) {
						startHoursPlusThree = startHoursPlusThree-12;
						if(top.document.getElementById("show_military_time")) {
							if(top.document.getElementById("show_military_time").value=="YES") {
								startHoursPlusThree = parseFloat(startHoursPlusThree+12);
							}
						}
					}
					
					if(startHoursPlusOne<10) {
						startHoursPlusOne = "0"+startHoursPlusOne;
					}
					if(startHoursPlusTwo<10) {
						startHoursPlusTwo = "0"+startHoursPlusTwo;
					}
					if(startHoursPlusThree<10) {
						startHoursPlusThree = "0"+startHoursPlusThree;
					}								
					var intervalTimeMin1;
					var intervalTimeMin2;
					var intervalTimeMin3;
					var intervalTimeMin4;
					var intervalTimeMin5;
					var intervalTimeMin6;
					var intervalTimeMin7;
					var intervalTimeMin8;
					var intervalTimeMin9;
					var intervalTimeMin10;
					
				
					if(startMins< 15) {
						intervalTimeMin1 = startHours+":00";
						intervalTimeMin2 = startHours+":15";
						intervalTimeMin3 = startHours+":30";
						intervalTimeMin4 = startHours+":45";
						intervalTimeMin5 = startHoursPlusOne+":00";
						intervalTimeMin6 = startHoursPlusOne+":15";
						intervalTimeMin7 = startHoursPlusOne+":30";
						intervalTimeMin8 = startHoursPlusOne+":45";
						intervalTimeMin9 = startHoursPlusTwo+":00";
						intervalTimeMin10 = startHoursPlusTwo+":15";
						
					}else if(startMins>=15 && startMins< 30) {
						intervalTimeMin1 = startHours+":15";
						intervalTimeMin2 = startHours+":30";
						intervalTimeMin3 = startHours+":45";
						intervalTimeMin4 = startHoursPlusOne+":00";
						intervalTimeMin5 = startHoursPlusOne+":15";
						intervalTimeMin6 = startHoursPlusOne+":30";
						intervalTimeMin7 = startHoursPlusOne+":45";
						intervalTimeMin8 = startHoursPlusTwo+":00";
						intervalTimeMin9 = startHoursPlusTwo+":15";
						intervalTimeMin10 = startHoursPlusTwo+":30";
						
					}else if(startMins>=30 && startMins< 45) {
						intervalTimeMin1 = startHours+":30";
						intervalTimeMin2 = startHours+":45";
						intervalTimeMin3 = startHoursPlusOne+":00";
						intervalTimeMin4 = startHoursPlusOne+":15";
						intervalTimeMin5 = startHoursPlusOne+":30";
						intervalTimeMin6 = startHoursPlusOne+":45";
						intervalTimeMin7 = startHoursPlusTwo+":00";
						intervalTimeMin8 = startHoursPlusTwo+":15";
						intervalTimeMin9 = startHoursPlusTwo+":30";
						intervalTimeMin10 = startHoursPlusTwo+":45";
						
					}else if(startMins>=45) {
						intervalTimeMin1 = startHours+":45";
						intervalTimeMin2 = startHoursPlusOne+":00";
						intervalTimeMin3 = startHoursPlusOne+":15";
						intervalTimeMin4 = startHoursPlusOne+":30";
						intervalTimeMin5 = startHoursPlusOne+":45";
						intervalTimeMin6 = startHoursPlusTwo+":00";
						intervalTimeMin7 = startHoursPlusTwo+":15";
						intervalTimeMin8 = startHoursPlusTwo+":30";
						intervalTimeMin9 = startHoursPlusTwo+":45";
						intervalTimeMin10 = startHoursPlusThree+":00";
					}			
				
					var showStartIntervalTime;
						showStartIntervalTime='<img src="images/tpixel.gif" width="231" height="1" />';
						showStartIntervalTime+= intervalTimeMin1;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin2;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin3;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin4;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin5;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin6;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin7;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin8;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin9;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin10;
						
						//alert(showStartIntervalTime + document.getElementById('intervalId').innerHTML);
						if(document.getElementById(intervalId_td).style.display=='none') {
							document.getElementById(intervalId_td).style.display='inline-block';
							document.getElementById(intervalId_td).innerHTML = showStartIntervalTime;
						}else {
							document.getElementById(intervalId_td).style.display='none';
						}
				//}
				
			//}
		}		
		
		
		//var startTimeSplit = split(':',startTime);
		//alert(startTimeSplit[0]);
	}
//SHOW TIME INTERVAL

/********************** New ADD function (RAVINDER)  **********************/
function emr_print(){
	
	var win = $('[name^=testiframe]',window.parent.frames[0].frames[0].document)[0];
	if(win){
	try {
		win.contentWindow.document.execCommand('print', false, null);
	} catch(e) {
		win.contentWindow.print();
	}
	}else{
		alert("This functionality is under construction for this chart");	
	}
	//OLD CODE FROM WHICH FOCUS() FUNCTION IS NOT WORKING ON IE11 WIN 10 LATEST VERSION
	/*var win=window.parent.frames[0].frames[0].frames['testiframe'];
	if(win) {
		win.focus();
		win.print();
	}else {
		alert("This functionality is under construction for this chart");	
	}*/
}
function print_meds(val,path) {
	var url='print_meds.php?pConfId='+val+'&get_http_path='+path;
	window.open(url,'print_meds_win','width=650,height=600,top=100,left=100,resizable=yes,scrollbars=yes');
		
}
var pageval='';
function sav_print_pdf(val,path,pageval){
		var  val_url='';
		var  frmObj='';
		frmObj = top.frames[0].frames[0].document.forms[0];
		if(pageval!='' && typeof(pageval)!="undefined") {
			val_url=pageval;
		}else {
			if(frmObj.go_pageval) {
				val_url=frmObj.go_pageval.value;
			}
		}
		var url;
		if(val_url!=""){
			/*
			if(val_url=="surgery_consent_form"){
				url='consent_surgery_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=="hippa_consent_form"){
				url='consent_hippa_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=="benefit_consent_form"){
				url='consent_assign_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=="insurance_consent_form"){
				url='consent_insurance_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			*/
			if(val_url=="surgical_check_list"){
				url='check_list_printpop.php?pConfId='+val+'&get_http_path='+path;
				
			}
			if(val_url=="consent_multiple_form"){
				var consentMultipleId=frmObj.consentMultipleId.value;
				var consentMultipleAutoIncrId='';
				var consentMultipleAutoIncrId=frmObj.consentMultipleAutoIncrId.value;
				var autoMultipleLink='';
				if(consentMultipleAutoIncrId) {
					autoMultipleLink='&consentMultipleAutoIncrId='+consentMultipleAutoIncrId;
				}
				url='consent_multiple_printpop.php?pConfId='+val+'&consentMultipleId='+consentMultipleId+autoMultipleLink+'&get_http_path='+path;
			}
			if(val_url=="preophealthquestionnaire"){
				url='pre_op_health_quest_printpop.php?pConfId='+val+'&get_http_path='+path;
				
			}
			if(val_url=="history_physicial_clearance"){
				url='history_physicial_clearance_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='operativereport')
			{
			   	url='operative_recordPdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=="operatingroomrecords") {
			    url='op_room_record_pdf.php?pConfId='+val+'&get_http_path='+path;
				
			}
			if(val_url=="preopnursingrecord")
			{
			  	url= 'pre_op_nursing_record_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=="postopnursingrecord")
			{
			  	url= 'post_op_nursing_record_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=="postopnursingrecord")
			{
			  	url= 'post_op_nursing_record_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='preopphysicianorders')
			{
			    url='pre_op_physician_orders_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='postopphysicianorders')
			{
			    url='post_op_physician_orders_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='dischargesummarysheet')
			{
			    url='discharge_summary_sheet_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='localanesthesiarecord')
			{
				url='local_anesthesia_record_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='preopgenanesthesiarecord')
			{
				url='pre_op_general_anes_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='genanesthesiarecord')
			{
			    url='gen_anes_record_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='genanesthesianursesnotes')
			{
			    url='gen_anes_nurse_note_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='patient_instruction_sheet')
			{
			    url='instructionsheet_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='amendment')
			{
			    url='amendments_notes_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='laser_procedure_patient_table')
			{
			    url='laser_procedure_printpop.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='pre_nurse_alderate')
			{
			    url='pre_nurse_alderate_record_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='post_nurse_alderate')
			{
			    url='post_nurse_alderate_record_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='transfer_followups')
			{
			    url='transfer_followups_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='injection')
			{
			    url='injection_misc_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			if(val_url=='patient_medication_reconciliation_sheet')
			{
			    url='medication_reconciliation_sheet_pdf.php?pConfId='+val+'&get_http_path='+path;
			}
			
						
			window.open(url,'','width=650,height=600,top=100,left=100,resizable=yes,scrollbars=yes');
		}
		
		
	//window.open('../surgerycenter/html2pdf/public_html/demo/html2ps.php','','width=650,height=600,top=100,left=100,resizable=yes,scrollbar=1');	
}
/**********************End of New ADD function (RAVINDER)  **********************/

/**********************************/
var txtId5='';
function save_hide_row_idTemp(row_id,txtId1,txtId2,txtId3,txtId4,txtId5) {
	document.getElementById(row_id).style.display="none";
	document.getElementById(txtId1).value="";
	document.getElementById(txtId2).value="";
	document.getElementById(txtId3).value="";
	if(document.getElementById(txtId4)) {
		document.getElementById(txtId4).value="";
	}
	if(document.getElementById(txtId5)) {
		document.getElementById(txtId5).value="";
	}
	
}

/***********************************/

//FUNCTION FOR LOCAL ANESTHESIA RECORD	(IV Catheter )
function chk_unchk_Catheter(main_id,id1,id2,id3,id4,id5,id6,id7,id8,id9,id10) {
	
	if(document.getElementById(main_id).checked==true) {
		document.getElementById(id1).disabled=true;
		document.getElementById(id2).disabled=true;
		document.getElementById(id3).disabled=true;
		document.getElementById(id4).disabled=true;
		document.getElementById(id5).disabled=true;
		document.getElementById(id6).disabled=true;
		document.getElementById(id7).disabled=true;
		document.getElementById(id8).disabled=true;
		document.getElementById(id9).disabled=true;
		document.getElementById(id10).disabled=true;
	
	}else if(document.getElementById(main_id).checked==false) {
		document.getElementById(id1).disabled=false;
		document.getElementById(id2).disabled=false;
		document.getElementById(id3).disabled=false;
		document.getElementById(id4).disabled=false;
		document.getElementById(id5).disabled=false;
		document.getElementById(id6).disabled=false;
		document.getElementById(id7).disabled=false;
		document.getElementById(id8).disabled=false;
		document.getElementById(id9).disabled=false;
		document.getElementById(id10).disabled=false;
	
	}
}
//END FUNCTION FOR LOCAL ANESTHESIA RECORD	(IV Catheter )

//COMMON FUNCTION FOR NO AUTHORITY
	function noAuthorityFunCommon(userName) {
		alert("NOT AUTHORIZED TO SIGN THE RECORD!");
		return false;
	}
//COMMON FUNCTION FOR NO AUTHORITY 

//COMMON FUNCTION FOR ACCESS DENIED
	function accessDeniedFn() {
		alert("Access denied to Edit/Save");
		return false;
	}
	function accessSaveDeniedFn() {
		if(top.frames[0].frames[0]) {
			if(top.frames[0].frames[0].document.forms[0].frmAction.value=='discharge_summary_sheet.php') {
				return getFormSave();
			}else {
				alert("Access denied to Edit/Save");
				return false;
			}
		}
	}
//COMMON FUNCTION FOR ACCESS DENIED

//FUNCTION TO CLOSE PREDEFINE POPUP
	var tEkgOut; 
	function closeEkg2New(objCloseId){
		
		if(document.getElementById(objCloseId)) {
			if(document.getElementById(objCloseId).style.display != "none"){
				document.getElementById(objCloseId).style.display = "none"; 
			}
		}
		if(top.document.getElementById(objCloseId)) {
			if(top.document.getElementById(objCloseId).style.display != "none"){
				top.document.getElementById(objCloseId).style.display = "none"; 
			}
		}
	}
	function closeEkg2(objCloseId){
		if(document.getElementById(objCloseId)) {
			if(document.getElementById(objCloseId).style.display != "none"){
				document.getElementById(objCloseId).style.display = "none"; 
			}
		} 
		if(top.document.getElementById(objCloseId)) {
			if(top.document.getElementById(objCloseId).style.display != "none"){
				top.document.getElementById(objCloseId).style.display = "none"; 
			}
		}
		
	}
	function closeEkg(objEkgClose){
		if(objEkgClose=='ekgLocalAnesDiv') { //LOCAL ANES RECORD
			tEkgOut = setTimeout("closeEkg2('ekgLocalAnesDiv')",500);
		
		}else if(objEkgClose=='evaluationPreDiagnosisDiv') {  //OP ROOM RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationPreDiagnosisDiv')",500);
		}else if(objEkgClose=='evaluationPostDiagnosisDiv') {  //OP ROOM RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationPostDiagnosisDiv')",500);
		}else if(objEkgClose=='evaluationProceduresDiv') {  //OP ROOM RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationProceduresDiv')",500);
		}else if(objEkgClose=='evaluationPostOpDropsDiv') {  //OP ROOM RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationPostOpDropsDiv')",500);
		
		}else if(objEkgClose=='evaluationPreDefineModelDiv') {  //OP ROOM RECORD 
			tEkgOut = setTimeout("closeEkg2('evaluationPreDefineModelDiv')",500);
		}else if(objEkgClose=='evaluationPreDefineSuppliesUsedDiv') {  //OP ROOM RECORD 
			tEkgOut = setTimeout("closeEkg2('evaluationPreDefineSuppliesUsedDiv')",500);
		
		}else if(objEkgClose=='evaluationComplicationsDiv') {  //OP ROOM RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationComplicationsDiv')",500);
		}else if(objEkgClose=='intraOpPostOpDiv') {  //OP ROOM RECORD
			tEkgOut = setTimeout("closeEkg2('intraOpPostOpDiv')",500);
		}else if(objEkgClose=='evaluationNurseNotesDiv') {  //OP ROOM RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationNurseNotesDiv')",500);
		}else if(objEkgClose=='postop_evaluationEvaluationDiv') {  //LOCAL ANES RECORD
			tEkgOut = setTimeout("closeEkg2('postop_evaluationEvaluationDiv')",500);
		
		}else if(objEkgClose=='evaluationLocalAnesEvaluationDiv') {  //LOCAL ANES RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationLocalAnesEvaluationDiv')",500);
		}else if(objEkgClose=='evaluationEvaluationDiv') {  //GEN ANES RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationEvaluationDiv')",500);
		
		}else if(objEkgClose=='dentationLocalAnesDentationDiv') {  //LOCAL ANES RECORD
			tEkgOut = setTimeout("closeEkg2('dentationLocalAnesDentationDiv')",500);
		}else if(objEkgClose=='evaluationFoodListDiv') {  //PRE OP  NURSING RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationFoodListDiv')",500);
		}else if(objEkgClose=='evaluationPreCommentsDiv') {  //PRE OP  NURSING RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationPreCommentsDiv')",500);
		}else if(objEkgClose=='evaluationPostSiteDiv') {  //POST OP  NURSING RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationPostSiteDiv')",500);
		}else if(objEkgClose=='evaluationNKindDiv') {  //POST OP  NURSING RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationNKindDiv')",500);
		}else if(objEkgClose=='evaluationRecoveryDiv') {  //POST OP  NURSING RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationRecoveryDiv')",500);
		}else if(objEkgClose=='PreOpMedicationDiv') {  //PRE OP  PHYSICICAN RECORD
			tEkgOut = setTimeout("closeEkg2('PreOpMedicationDiv')",500);
		}else if(objEkgClose=='evaluationPreOpMedDiv') {  //PRE OP  PHYSICICAN RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationPreOpMedDiv')",500);
		}else if(objEkgClose=='evaluationTakeHomeDiv') {  //POST OP PHYSICIAN RECORD
			tEkgOut = setTimeout("closeEkg2('evaluationTakeHomeDiv')",500);
		}else if(objEkgClose=='evaluationPreDefineDiv') {  //ALLERGIES (NURSING)
			tEkgOut = setTimeout("closeEkg2('evaluationPreDefineDiv')",500);
		}else if(objEkgClose=='evaluationPreDefineMedDiv') {  //MEDICATION (NURSING)
			tEkgOut = setTimeout("closeEkg2('evaluationPreDefineMedDiv')",500);
		}else if(objEkgClose=='evaluationChiefComplaint') {  //laser procedure(cheif Complaint)
			tEkgOut = setTimeout("closeEkg2('evaluationChiefComplaint')",500);
		}else if(objEkgClose=='evaluationpast_medicalHX') {  //laser procedure(past medical hx)
			tEkgOut = setTimeout("closeEkg2('evaluationpast_medicalHX')",500);
		}else if(objEkgClose=='evaluationpresent_illness_hx') {  //laser procedure(pre illness HX)
			tEkgOut = setTimeout("closeEkg2('evaluationpresent_illness_hx')",500);
		}else if(objEkgClose=='evaluationmedication') {  //laser procedure(MEdication)
			tEkgOut = setTimeout("closeEkg2('evaluationmedication')",500);
		}else if(objEkgClose=='evaluationmental_state') {  //laser procedure(Mental state)
			tEkgOut = setTimeout("closeEkg2('evaluationmental_state')",500);
		}else if(objEkgClose=='evaluationfundus_exam') {  //laser procedure(Fundus Exam)
			tEkgOut = setTimeout("closeEkg2('evaluationfundus_exam')",500);
		}else if(objEkgClose=='evaluationspot_size') {  //laser procedure(Spot Size)
			tEkgOut = setTimeout("closeEkg2('evaluationspot_size')",500);
		}else if(objEkgClose=='evaluationpower') {  //laser procedure(Power)
			tEkgOut = setTimeout("closeEkg2('evaluationpower')",500);
		}else if(objEkgClose=='evaluationcount') {  //laser procedure(count)
			tEkgOut = setTimeout("closeEkg2('evaluationcount')",500);
		}else if(objEkgClose=='evaluationanesthesia') {  //laser procedure(anesthesia)
			tEkgOut = setTimeout("closeEkg2('evaluationanesthesia')",500);
		}else if(objEkgClose=='evaluationPost_ProgressNote') {  //laser procedure(Post_ProgressNote
			tEkgOut = setTimeout("closeEkg2('evaluationPost_ProgressNote')",500);
		}else if(objEkgClose=='evaluationpost_operative_status') {  //laser procedure(operative_status)
			tEkgOut = setTimeout("closeEkg2('evaluationpost_operative_status')",500);
		}else if(objEkgClose=='evaluationProcedureNotesDiv') {  //laser procedure(operative_status)
			tEkgOut = setTimeout("closeEkg2('evaluationProcedureNotesDiv')",500);
		}else if(objEkgClose=='preDefineSavedHealthQuestMedDiv') {  //MEDICATION (NURSING)
			tEkgOut = setTimeout("closeEkg2('preDefineSavedHealthQuestMedDiv')",500);
		}
		
	}
	function stopCloseEkg()
	{
		clearTimeout(tEkgOut);
	}
//END FUNCTION TO CLODE PREDEFINE POPUP

//FUNCTION TO OPEN NEW WINDOW
function MM_openBrWindow(theURL,winName,features) 
{ //v2.0
  //window.open(theURL,winName,features+",resizable=yes,scrollbars=yes");
  window.open(theURL,winName,features+",resizable=yes,scrollbars=no");
}
//END FUNCTION TO OPEN NEW WINDOW

//START FUNCTIONS BY GURLEEN

function showChiefCompliant(name1, name2, c, posLeft, posTop){	

	top.document.getElementById("evaluationChiefComplaint").style.display = 'inline-block';
	top.document.getElementById("evaluationChiefComplaint").style.left = posLeft+'px';
	top.document.getElementById("evaluationChiefComplaint").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showpast_medicalHX(name1, name2, c, posLeft, posTop){	
//	alert(name1);
	//alert(posLeft);
	top.document.getElementById("evaluationpast_medicalHX").style.display = 'inline-block';
	top.document.getElementById("evaluationpast_medicalHX").style.left = posLeft+'px';
	top.document.getElementById("evaluationpast_medicalHX").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showpresent_illness_hx(name1, name2, c, posLeft, posTop){	
//	alert(name1);
	//alert(posLeft);
	top.document.getElementById("evaluationpresent_illness_hx").style.display = 'inline-block';
	top.document.getElementById("evaluationpresent_illness_hx").style.left = posLeft+'px';
	top.document.getElementById("evaluationpresent_illness_hx").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showSLE(name1, name2, c, posLeft, posTop){	

	//alert(posLeft);
	document.getElementById("evaluationSLE").style.display = 'inline-block';
	document.getElementById("evaluationSLE").style.left = posLeft+'px';
	document.getElementById("evaluationSLE").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showexposure(name1, name2, c, posLeft, posTop){	

	//alert(posLeft);
	document.getElementById("evaluationexposure").style.display = 'inline-block';
	document.getElementById("evaluationexposure").style.left = posLeft+'px';
	document.getElementById("evaluationexposure").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showmental_state(name1, name2, c, posLeft, posTop){	

	//alert(posLeft);
	document.getElementById("evaluationmental_state").style.display = 'inline-block';
	document.getElementById("evaluationmental_state").style.left = posLeft+'px';
	document.getElementById("evaluationmental_state").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showFundus_Exam(name1, name2, c, posLeft, posTop){	

	//alert(posLeft);
	document.getElementById("evaluationfundus_exam").style.display = 'inline-block';
	document.getElementById("evaluationfundus_exam").style.left = posLeft+'px';
	document.getElementById("evaluationfundus_exam").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showmedication(name1, name2, c, posLeft, posTop){	

	//alert(posLeft);
	top.document.getElementById("evaluationmedication").style.display = 'inline-block';
	top.document.getElementById("evaluationmedication").style.left = posLeft+'px';
	top.document.getElementById("evaluationmedication").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showspot_size(name1, name2, c, posLeft, posTop){	

	//alert("rty");
	document.getElementById("evaluationspot_size").style.display = 'inline-block';
	document.getElementById("evaluationspot_size").style.left = posLeft+'px';
	document.getElementById("evaluationspot_size").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showpower(name1, name2, c, posLeft, posTop){	

	//alert("rty");
	document.getElementById("evaluationpower").style.display = 'inline-block';
	document.getElementById("evaluationpower").style.left = posLeft+'px';
	document.getElementById("evaluationpower").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showcount(name1, name2, c, posLeft, posTop){	

	//alert("rty");
	document.getElementById("evaluationcount").style.display = 'inline-block';
	document.getElementById("evaluationcount").style.left = posLeft+'px';
	document.getElementById("evaluationcount").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showanesthesia(name1, name2, c, posLeft, posTop){	

	//alert("rty");
	document.getElementById("evaluationanesthesia").style.display = 'inline-block';
	document.getElementById("evaluationanesthesia").style.left = posLeft+'px';
	document.getElementById("evaluationanesthesia").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS	
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showPost_ProgressNote(name1, name2, c, posLeft, posTop){	

	//alert(posLeft);
	document.getElementById("evaluationPost_ProgressNote").style.display = 'inline-block';
	document.getElementById("evaluationPost_ProgressNote").style.left = posLeft+'px';
	document.getElementById("evaluationPost_ProgressNote").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}
function showpost_operative_status(name1, name2, c, posLeft, posTop){	

	//alert(posLeft);
	document.getElementById("evaluationpost_operative_status").style.display = 'inline-block';
	document.getElementById("evaluationpost_operative_status").style.left = posLeft+'px';
	document.getElementById("evaluationpost_operative_status").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

// FINALIZE laser procedure OF THE PATIENT
function finalize_laser_procedure() {
	//alert('hello');
	var frmObjlaser = top.frames[0].frames[0].document.forms[2];
	if(frmObjlaser) {
		var laserprocedurestatus=frmObjlaser.LaserProcedureFinalizeFormStatus.value;
		//alert(laserprocedurestatus);
		if(laserprocedurestatus=='completed')
		{
			if(!confirm('Finalize Chart Notes! Are you sure ?')) { 
				return false;
			}else{
				frmObjlaser.submit();
				return true;	
			}
		}
		else
		{
			alert("Laser Procedure Form Incomplete");	
		}
	}
}
// FINALIZE laser procedure OF THE PATIENT
function exp_collapse_laser_procedure(id) {
	if(document.getElementById(id).style.display=="inline-block" || document.getElementById(id).style.display=="inline-block" || document.getElementById(id).style.display=="table-row-group") {
		document.getElementById(id).style.display="none";
	}else if(document.getElementById(id).style.display=="none") {
		document.getElementById(id).style.display="table-row-group";
	}
}

function showspot_duration(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationspot_duration").style.display = 'inline-block';
	document.getElementById("evaluationspot_duration").style.left = posLeft+'px';
	document.getElementById("evaluationspot_duration").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showshots(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationshots").style.display = 'inline-block';
	document.getElementById("evaluationshots").style.left = posLeft+'px';
	document.getElementById("evaluationshots").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showtotal_energy(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationtotal_energy").style.display = 'inline-block';
	document.getElementById("evaluationtotal_energy").style.left = posLeft+'px';
	document.getElementById("evaluationtotal_energy").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function showdegree_of_opening(name1, name2, c, posLeft, posTop){	

	document.getElementById("evaluationdegree_of_opening").style.display = 'inline-block';
	document.getElementById("evaluationdegree_of_opening").style.left = posLeft+'px';
	document.getElementById("evaluationdegree_of_opening").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	
	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}

function getFormPurge(frmName){
	
	var frmObj = top.mainFrame.main_frmInner.document.forms[0];
	var flag = true;
	var consentFormStatus = frmObj.hiddform_status.value;
	if((consentFormStatus=='completed')||(consentFormStatus=='not completed')){
			if(frmObj.hiddConsentPurgeStatus){
				frmObj.hiddConsentPurgeStatus.value="yes";
			}
			
			if(flag==true) {
				frmObj.submit();
			}
	}else{
		alert("Please verify/save this form before purge");	
	}
}

//END FUNCTION BY GURLEEN

//START FUNCTION TO CHANGE THE COLOR OF CHECKBOX
function changeChbxColor(grpNameChbx, chkflag){
	
	chkflag=typeof chkflag !== 'undefined' ? chkflag : false;
	var obgrpChbx = document.getElementsByName(grpNameChbx);
	var lenChbx = obgrpChbx.length;		
	for(var i=0;i<lenChbx;i++) {
		if(obgrpChbx[i].checked == true ) {
			chkflag=true;
		}
	}
	if(chkflag==true) {
		for(var i=0;i<lenChbx;i++) {
			obgrpChbx[i].parentNode.style.backgroundColor='#FFFFFF';
		}
	}
	if(chkflag==false) {
		for(var i=0;i<lenChbx;i++) {
			obgrpChbx[i].parentNode.style.backgroundColor='#F6C67A';
		}
	}		
	

}	
//END FUNCTION TO CHANGE THE COLOR OF CHECKBOX

function setFlag(fieldObj)
{
	if(fieldObj.type == 'checkbox') { //FOR CHECKBOX
		if(fieldObj.checked==true) {
			return true;
		}
	}else if(fieldObj.type=='select-one') { //FOR DROPDOWN
		if(fieldObj.value) {
			return true;
		}
	}else if(fieldObj.type=='text') { //FOR TEXTBOX
		if(fieldObj.value) {
			return true;
		}
	}else if(fieldObj.type=='textarea') { //FOR TEXTAREA
		if(fieldObj.value) {
			return true;
		}
	}else if(fieldObj.type=='file') { //FOR FILE
		if(fieldObj.value) {
			return true;
		}
	}
	return false;	
}

function setColor(fieldObj,flag)
{
	if(fieldObj.type=='checkbox') { //FOR CHECKBOX 
		if(flag==true) {
			fieldObj.parentNode.style.backgroundColor='#FFFFFF';
		}else {
			fieldObj.parentNode.style.backgroundColor='#F6C67A';
		}
	}else if(fieldObj.type=='select-one') { //FOR DROPDOWN
		if(flag==true) {
			fieldObj.style.backgroundColor='#FFFFFF';
		}else {
			fieldObj.style.backgroundColor='#F6C67A';
		}
	}else if(fieldObj.type=='text') { //FOR TEXTBOX
		if(flag==true) {
			fieldObj.style.backgroundColor='#FFFFFF';
		}else {
			fieldObj.style.backgroundColor='#F6C67A';
		}
	}else if(fieldObj.type=='textarea') { //FOR TEXTBOX
		if(flag==true) {
			fieldObj.style.backgroundColor='#FFFFFF';
		}else {
			fieldObj.style.backgroundColor='#F6C67A';
		}
	}else if(fieldObj.type=='file') { //FOR BROWSE
		if(flag==true) {
			fieldObj.style.backgroundColor='#FFFFFF';
		}else {
			fieldObj.style.backgroundColor='#F6C67A';
		}
	}else {
		if(fieldObj) {
			if(flag==true) {
				fieldObj.style.backgroundColor='#FFFFFF';
			}else {
				fieldObj.style.backgroundColor='#F6C67A';
			}
		}
	}
		
	
}

//specialCheck


//START FUNCTION TO CHANGE THE COLOR OF CHECKBOX
function changeDiffChbxColorNew(grpCnt,chbx1,chbx2,chbx3,chbx4,chbx5,chbx6,chbx7,chbx8,chbx9,chbx10,chbx11,chbx12,chbx13,chbx14,chbx15,chbx16,chbx17,chbx18,chbx19,chbx20){
	var chkChbxFlag=false;
	var i,m;
	var obgrpChbxNum='';
	var grpNameChbxId='';
	var isGroup	=	false;
	var frmObj = top.mainFrame.main_frmInner.document.forms[0];
	
	for(i=1;i<=grpCnt;i++) {
		grpNameChbxId=eval("chbx"+i);
		isGroup	=	(grpNameChbxId.indexOf('.') != '-1') ?	true : false;
		
		if(isGroup)
			obgrpChbxNum =document.getElementsByClassName(grpNameChbxId.substr(1));
		else
			obgrpChbxNum =document.getElementById(grpNameChbxId);
			
		if( obgrpChbxNum ) 
		{ 
			if(isGroup  ) 
			{
				if( obgrpChbxNum.length > 0 ) {
					for(m=0; m < obgrpChbxNum.length; m++)
					{ 
						if(setFlag(obgrpChbxNum[m])) chkChbxFlag = true;	
					}
				}
			}
			else
			{
				if(setFlag(obgrpChbxNum)) chkChbxFlag = true;	
			}
			//IF CHARNOTE IS INTRA-OP AND IF IOL IMAGE EXIST THEN SET FLAG TO TRUE FOR IOL BLOCK
			if(frmObj.name=='frm_op_room' && grpNameChbxId=='iol_na_id') {
				if(frmObj.hidd_anyOneImageExist.value) {
					chkChbxFlag=true;
				}
			}
			//IF CHARNOTE IS INTRA-OP AND IF IOL IMAGE EXIST THEN SET FLAG TO TRUE FOR IOL BLOCK
				
		}
		
	}
	
	for(i=1;i<=grpCnt;i++) {
		grpNameChbxId=eval("chbx"+i);
		isGroup	=	(grpNameChbxId.indexOf('.') != '-1') ?	true : false;
		
		if(isGroup)
			obgrpChbxNum =document.getElementsByClassName(grpNameChbxId.substr(1));
		else
			obgrpChbxNum =document.getElementById(grpNameChbxId);
			
		if( obgrpChbxNum )
		{	
			if( isGroup )
			{ 	if(obgrpChbxNum.length > 0 ) {
					for(m=0; m < obgrpChbxNum.length; m++)
					{
						setColor(obgrpChbxNum[m],chkChbxFlag);		
					} 
				}
			}
			else
			{ 	
				setColor(obgrpChbxNum,chkChbxFlag);
			}
		}
			
	}
	
}	
//END FUNCTION TO CHANGE THE COLOR OF CHECKBOX


//START FUNCTION TO CHANGE THE COLOR OF CHECKBOX
function changeDiffChbxColor(grpCnt,chbx1,chbx2,chbx3,chbx4,chbx5,chbx6,chbx7,chbx8,chbx9,chbx10,chbx11,chbx12,chbx13,chbx14,chbx15,chbx16,chbx17,chbx18,chbx19,chbx20){
	var chkChbxFlag=false;
	var i;
	var obgrpChbxNum='';
	var grpNameChbxId='';
	var frmObj = top.mainFrame.main_frmInner.document.forms[0];
	
	for(i=1;i<=grpCnt;i++) {
		grpNameChbxId=eval("chbx"+i);
		obgrpChbxNum =document.getElementById(grpNameChbxId);
		if(obgrpChbxNum) {
			//alert(obgrpChbxNum.type);
			if(obgrpChbxNum.type=='checkbox') { //FOR CHECKBOX
				if(obgrpChbxNum.checked==true) {
					chkChbxFlag=true;
				}
			}else if(obgrpChbxNum.type=='select-one') { //FOR DROPDOWN
				if(obgrpChbxNum.value) {
					chkChbxFlag=true;
				}
			}else if(obgrpChbxNum.type=='text') { //FOR TEXTBOX
				if(obgrpChbxNum.value) {
					chkChbxFlag=true;
				}
			}else if(obgrpChbxNum.type=='textarea') { //FOR TEXTAREA
				if(obgrpChbxNum.value) {
					chkChbxFlag=true;
				}
			}else if(obgrpChbxNum.type=='file') { //FOR FILE
				if(obgrpChbxNum.value) {
					chkChbxFlag=true;
				}
			}
			
			//IF CHARNOTE IS INTRA-OP AND IF IOL IMAGE EXIST THEN SET FLAG TO TRUE FOR IOL BLOCK
			if(frmObj.name=='frm_op_room' && grpNameChbxId=='iol_na_id') {
				if(frmObj.hidd_anyOneImageExist.value) {
					chkChbxFlag=true;
				}
			}
			//IF CHARNOTE IS INTRA-OP AND IF IOL IMAGE EXIST THEN SET FLAG TO TRUE FOR IOL BLOCK
		
		}
	}
	for(i=1;i<=grpCnt;i++) {
		grpNameChbxId=eval("chbx"+i);
		obgrpChbxNum =document.getElementById(grpNameChbxId);
		if(obgrpChbxNum) {
			if(obgrpChbxNum.type=='checkbox') { //FOR CHECKBOX
				if(chkChbxFlag==true) {
					obgrpChbxNum.parentNode.style.backgroundColor='#FFFFFF';
				}else {
					obgrpChbxNum.parentNode.style.backgroundColor='#F6C67A';
				}
			}else if(obgrpChbxNum.type=='select-one') { //FOR DROPDOWN
				if(chkChbxFlag==true) {
					obgrpChbxNum.style.backgroundColor='#FFFFFF';
				}else {
					obgrpChbxNum.style.backgroundColor='#F6C67A';
				}
			}else if(obgrpChbxNum.type=='text') { //FOR TEXTBOX
				if(chkChbxFlag==true) {
					obgrpChbxNum.style.backgroundColor='#FFFFFF';
				}else {
					obgrpChbxNum.style.backgroundColor='#F6C67A';
				}
			}else if(obgrpChbxNum.type=='textarea') { //FOR TEXTBOX
				if(chkChbxFlag==true) {
					obgrpChbxNum.style.backgroundColor='#FFFFFF';
				}else {
					obgrpChbxNum.style.backgroundColor='#F6C67A';
				}
			}else if(obgrpChbxNum.type=='file') { //FOR BROWSE
				if(chkChbxFlag==true) {
					obgrpChbxNum.style.backgroundColor='#FFFFFF';
				}else {
					obgrpChbxNum.style.backgroundColor='#F6C67A';
				}
			}else {
				if(obgrpChbxNum) {
					if(chkChbxFlag==true) {
						obgrpChbxNum.style.backgroundColor='#FFFFFF';
					}else {
						obgrpChbxNum.style.backgroundColor='#F6C67A';
					}
				}
			}

		}
	}
}	
//END FUNCTION TO CHANGE THE COLOR OF CHECKBOX

//START FUNCTION TO CHANGE THE COLOR OF TEXTBOX
var Txt17 = '';
function changeTxtGroupColor(grpCount,Txt1,Txt2,Txt3,Txt4,Txt5,Txt6,Txt7,Txt8,Txt9,Txt10,Txt11,Txt12,Txt13,Txt14,Txt15,Txt16,Txt17){
	var chkTxtflag=false;
	var i;
	var obgrpTxtNum='';
	var grpNameTxtId='';
	//alert(document.getElementById(Txt1).value);
	for(i=1;i<=grpCount;i++) {
		var grpNameTxtId=eval("Txt"+i);
		obgrpTxtNum =document.getElementById(grpNameTxtId);
		if(obgrpTxtNum) {
			if(obgrpTxtNum.value) {
				chkTxtflag=true;
			}
		}
	}
	
	for(i=1;i<=grpCount;i++) {
		grpNameTxtId=eval("Txt"+i);
		obgrpTxtNum =document.getElementById(grpNameTxtId);
		if(obgrpTxtNum) {
			if(chkTxtflag==true) {
				obgrpTxtNum.style.backgroundColor='#FFFFFF';
			}else {
				obgrpTxtNum.style.backgroundColor='#F6C67A';
			}	
		}
	}
	
}	
//END FUNCTION TO CHANGE THE COLOR OF TEXTBOX


//START FUNCTION TO CHANGE THE COLOR OF 90 TEXTBOX IN LOCAL ANES RECORD
function changeMultiTxtGroupColor(){
	var chkMultiTxtflag=false;
	var i;
	var obgrpMultiTxtNum='';
	var grpNameMultiTxtId='';
	//alert(document.getElementById(MultiTxt1).value);
	for(i=8;i<=237;i++) { 
		if(i<=97 || i>=108) { //DO NOT CHANGE COLOR OF IN-BETWEEN RECORDS
			obgrpMultiTxtNum =document.getElementById("bp_temp"+i);
			if(obgrpMultiTxtNum) {
				if(obgrpMultiTxtNum.value) {
					chkMultiTxtflag=true;
				}
			}
		}
	}
	for(i=8;i<=237;i++) {
		if(i<=97 || i>=108) { //DO NOT CHANGE COLOR OF IN-BETWEEN RECORDS
			obgrpMultiTxtNum =document.getElementById("bp_temp"+i);
			if(obgrpMultiTxtNum) {
				if(chkMultiTxtflag==true) {
					obgrpMultiTxtNum.style.backgroundColor='#FFFFFF';
				}else {
					obgrpMultiTxtNum.style.backgroundColor='#F6C67A';
				}	
			}
		}
	}
	
}	
//END FUNCTION TO CHANGE THE COLOR OF 90 TEXTBOX IN LOCAL ANES RECORD

//START FUNCTION TO FIND POSITION FROM LEFT
function findPos_X(id){
	var obj = document.getElementById(id);
	var posX = obj.offsetLeft;
	while(obj.offsetParent){
		posX=posX+obj.offsetParent.offsetLeft;
		if(obj==document.getElementsByTagName('body')[0]){break}
		else{obj=obj.offsetParent;}
	}
	return(posX);
}
//END FUNCTION TO FIND POSITION FROM LEFT

//START FUNCTION TO FIND POSITION FROM TOP
function findPos_Y(id){
	var obj = document.getElementById(id);
	var posY = obj.offsetTop;
	while(obj.offsetParent){
		posY=posY+obj.offsetParent.offsetTop;
		if(obj==document.getElementsByTagName('body')[0]){break}
		else{obj=obj.offsetParent;}
	}
	return(posY);
}
//END FUNCTION TO FIND POSITION FROM TOP

//START FUNCTION TO CHANGE THE COLOR OF CHECKBOX AND TEXTBOX FOR DISCHARGE SUMMARY
function changeTxtChkGroupColorDischarge(chkId,Txt1,Txt2,Txt3,Txt4,FileId){
	var chkTxtChkflag=false;
	var i;
	var j;
	var obgrpMultiChkNum='';
	var grpNameTxtChkId='';
	var grpNameTxtId='';
	var obgrpTxtNum='';
	chkId	=	top.frames[0].frames[0].document.getElementById('procedureIdImplode').value;
	var chk_procedureId_arr = new Array();
	chk_procedureId_arr  = chkId.split(',');
	var	chk_procedureId_Count = chk_procedureId_arr.length;
	//alert(chkTxtChkflag);
	
	for(i=0;i<chk_procedureId_Count;i++) {
		var getprocedureId = chk_procedureId_arr[i];
		obgrpMultiChkNum =document.getElementById("chbx_fdt_no"+getprocedureId);
		//alert(obgrpMultiChkNum);
			if(obgrpMultiChkNum) {
				if(obgrpMultiChkNum.type=='checkbox') { //FOR CHECKBOX
					if(obgrpMultiChkNum.checked==true) {
						chkTxtChkflag=true;
					}
				}
			}
	}
	
	if(chkTxtChkflag==false){
		for(j=1;j<=4;j++) {
			var grpNameTxtId=eval("Txt"+j);
			obgrpTxtNum =document.getElementById(grpNameTxtId);
			if(obgrpTxtNum) {
				if(obgrpTxtNum.value) {
					chkTxtChkflag=true;
				}
			}
		}
	}
	
	if(document.getElementById('chbx_disAttachedId')) {
		if(document.getElementById('chbx_disAttachedId').checked==true) {
			if(chkTxtChkflag==false){
				if(document.getElementById(FileId)) {
					if(document.getElementById(FileId).type=='file') { //FOR BROWSE
						if(document.getElementById(FileId).value) {
							chkTxtChkflag=true;
						}
					}
				}
			}
		}
	}
	
	for(i=0;i<chk_procedureId_Count;i++) {
		var getprocedureId = chk_procedureId_arr[i];
		//alert(getprocedureId);
		obgrpMultiChkNum =document.getElementById("chbx_fdt_no"+getprocedureId);
			if(obgrpMultiChkNum) {
				if(obgrpMultiChkNum.type=='checkbox') { //FOR CHECKBOX
					if(chkTxtChkflag==true) {
						obgrpMultiChkNum.parentNode.style.backgroundColor='#FFFFFF';
					}else {
						obgrpMultiChkNum.parentNode.style.backgroundColor='#F6C67A';
					}
				}
			}
	}
	
	for(var k=1;k<=4;k++) {
		var grpNameTxtId=eval("Txt"+k);
		obgrpTxtNum =document.getElementById(grpNameTxtId);
		if(obgrpTxtNum) {
			if(chkTxtChkflag==true) {
				obgrpTxtNum.style.backgroundColor='#FFFFFF';
			}else {
				obgrpTxtNum.style.backgroundColor='#F6C67A';
			}	
		}
	}
	
	if(document.getElementById('chbx_disAttachedId')) {
		if(document.getElementById('chbx_disAttachedId').checked==true) {
			var objFileId = document.getElementById(FileId);
			if(objFileId) {	
				if(objFileId.type=='file') { //FOR BROWSE
					if(chkTxtChkflag==true) {
						objFileId.style.backgroundColor='#FFFFFF';
					}else {
						objFileId.style.backgroundColor='#F6C67A';
						
					}
				}
			}
		}
	}

}	
//END FUNCTION TO CHANGE THE COLOR OF CHECKBOX AND TEXTBOX FOR DISCHARGE SUMMARY

//START FUNCTION TO CONVERT STRING VALUE IN TO  TIME FORMAT 
function cnvrtStrToTime(obj,strval) {
	
	var dn="PM";
	if(strval && (strval.length=='3' || strval.length=='4')) {
		if(strval.length=='3') {
			var cnvrtHrs=strval.substr(0,1);
			var cnvrtMin=strval.substr(1,2);
		}
		if(strval.length=='4') {
			var cnvrtHrs=strval.substr(0,2);
			var cnvrtMin=strval.substr(2,2);
		}	
		if(cnvrtHrs==6) {
			if(cnvrtMin>=30) {
				dn='AM';
			}
		}else if(cnvrtHrs>6 && cnvrtHrs<12) {
			dn='AM';
		}
		if(cnvrtHrs!=0 && cnvrtHrs<=12 && cnvrtMin<=59) {
			if(cnvrtHrs && cnvrtHrs.length==1) {  
				cnvrtHrs='0'+cnvrtHrs;
			}
		
			var cnvrtTime = cnvrtHrs+':'+cnvrtMin+' '+dn;
			if(document.getElementById(obj)) {
				
				document.getElementById(obj).value=cnvrtTime;
			}
		}else{
			alert('Please enter correct time');
			document.getElementById(obj).value='';
		}
	}else if(strval && strval.length<'3') {
		alert('Please enter correct time');
		document.getElementById(obj).value='';
	}
}	
//END FUNCTION TO CONVERT STRING VALUE INTO TIME FORMAT

function GetWidth()
{
        var x = 0;
        if (self.innerHeight)
        {
                x = self.innerWidth;
        }
        else if (document.documentElement && document.documentElement.clientHeight)
        {
                x = document.documentElement.clientWidth;
        }
       else if (document.body)
        {
                x = document.body.clientWidth;
        }
        return x;
}
function GetHeight()
{
	var y = 0;
	if (self.innerHeight)
	{
			y = self.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight)
	{
			y = document.documentElement.clientHeight;
	}
	else if (document.body)
	{
			y = document.body.clientHeight;
	}
	return y;
}

function trimNew(val){ 
	return val.replace(/^\s+|\s+$/, ''); 
}

//ipad functions 
function OnSignIpadPhyAdmin(user_id,pConfId,sigFor,idInnerHTML,signSeqNum){
	window.open("../chartNoteSignatureIPad.php?user_id="+user_id+"&pConfId="+pConfId+"&sigFor="+sigFor+"&idInnerHTML="+idInnerHTML+"&signSeqNum="+signSeqNum,"chartNoteSignature");
}
function OnSignIpadPhy(patient_id,pConfId,sigFor,idInnerHTML,signSeqNum){
	
	var win_width = 550;
	var win_height = 450;
	
	var x = event.clientX;// - parseInt(win_width/2);     // Get the horizontal coordinate
	var y = event.clientY - parseInt(win_height/2); 
	
	var features = "width="+win_width+",height="+win_height+",left="+x+",top="+y;
	var url = "chartNoteSignatureIPad.php?patient_id="+patient_id+"&pConfId="+pConfId+"&sigFor="+sigFor+"&idInnerHTML="+idInnerHTML+"&signSeqNum="+signSeqNum;
	
	window.open(url,"chartNoteSignature",features);
}
function image_DIV(imageSrc,div,id,seqNum){
	if(imageSrc){
		var imageSrcDir = "SigPlus_images/"+imageSrc;
		var imageSrcNew = "SigPlus_images/"+imageSrc;
		if(div == 'phy') {//ADMIN SECTION
			imageSrcNew = "../SigPlus_images/"+imageSrc;					
		}
		//alert(imageSrcNew);
		if(trimNew(imageSrc) != "") {
			if(div == "ptHealth"){				
				if(typeof(document.getElementById('hidden_patient_sign_image_path'))!="undefined") {
					document.getElementById("hidden_patient_sign_image_path").value=imageSrcNew;	
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					document.getElementById(id).innerHTML = "<tr><td style='width:2px;'>&nbsp;</td><td style='width:160px;height:90px;'><img src="+imageSrcNew+" style='width:150px; height:83px;'></td></tr>";		
				}
			}else if(div == "ptConsent" || div == "ptInstruction"){
				if(typeof(document.getElementById('hiddSigIpadId'+seqNum)) != "undefined") {
					document.getElementById('hiddSigIpadId'+seqNum).value=imageSrcNew;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					document.getElementById(id).innerHTML = "<tr><td style='width:160px;height:90px;'><img src="+imageSrcNew+" style='width:150px; height:83px;'></td></tr>";		
				}
			}else if(div == "phy"){
				if(typeof(document.getElementById('hiddSigIpadIdAdminPhy')) != "undefined") {
					document.getElementById('hiddSigIpadIdAdminPhy').value=imageSrcDir;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					document.getElementById(id).innerHTML = "<img src="+imageSrcNew+" style='width:230px; height:50px;'>";		
				}
				
			}
		}
	}
}
//ipad functions

//Check List Set Nurse Name By Section//

function setNurseNameBySection(getSectionName,currentNurseName){
	if(getSectionName!="" && currentNurseName!=""){
		var currentNurseId="";	
		currentNurseId=document.getElementById("check_list_nurse_id").value;
		if(getSectionName=="procedure_check_in"){
			if(typeof(document.getElementById("procedure_check_list_nurse_name"))!='undefined' && typeof(document.getElementById("procedure_check_in_nurse_id")!='undefined')){
				document.getElementById("procedure_check_list_nurse_name").innerHTML=currentNurseName;
				document.getElementById("procedure_check_in_nurse_id").value=currentNurseId;
			}
		}else if(getSectionName=="sign_in"){
			if(typeof(document.getElementById("sign_in_nurse_name"))!='undefined' && typeof(document.getElementById("sign_in_nurse_id")!='undefined')){
				document.getElementById("sign_in_nurse_name").innerHTML=currentNurseName;
				document.getElementById("sign_in_nurse_id").value=currentNurseId;
			}
		}else if(getSectionName=="time_out"){
			if(typeof(document.getElementById("time_out_nurse_name"))!='undefined' && typeof(document.getElementById("time_out_nurse_id")!='undefined')){
				document.getElementById("time_out_nurse_name").innerHTML=currentNurseName;
				document.getElementById("time_out_nurse_id").value=currentNurseId;
			}
		}else if(getSectionName=="sign_out"){
			if(typeof(document.getElementById("sign_out_nurse_name"))!='undefined' && typeof(document.getElementById("sign_out_nurse_id")!='undefined')){
				document.getElementById("sign_out_nurse_name").innerHTML=currentNurseName;
				document.getElementById("sign_out_nurse_id").value=currentNurseId;
			}
		}
	}
}

//


function popup(divid,selid,c_div)
{
	if(c_div){	
		cl_div=c_div.split('__');
		for(i in cl_div){
			if(document.getElementById(cl_div[i])) {
				document.getElementById(cl_div[i]).style.display='none';
			}
		}
	}
	document.getElementById(divid).style.display='inline-block';
	var selid1=selid+'1';
	var strToSendAttrib = "";
	var selectedArray=new Array(); 
	var availableList = document.getElementById(selid);
	var addIndex = availableList.selectedIndex;
	j=0;
	for(i = availableList.length-1; i >= 0 ; i--)
	{            		  
		if(availableList.options.item(i).selected == true)
		{
			strToSendAttrib = availableList.options.item(i).value;
			selectedArray[j]=strToSendAttrib;
			j++;		
		}											 
	}
	var availableListOpener=document.getElementById(selid1);
	n=0;
	for(k = availableListOpener.length-1; k >= 0 ;k--)
	{  				         		  
		if(availableListOpener.options.item(k).value == selectedArray[n])
		{
			availableListOpener.options.item(k).selected = true;
			n++;	
		}
		else
		{
			availableListOpener.options.item(k).selected = false;
		}													 
	}
	
}
function selected(divid,selid)
{
	var selid1=selid+'1';
	var strToSendAttrib = "";
	var selectedArray=new Array(); 
	var availableList = document.getElementById(selid1);
	var addIndex = availableList.selectedIndex;
/*	if(addIndex < 0)
	{
		alert("Please select to continue.");
		return;
	}*/
	j=0;
	for(i = availableList.length-1; i >= 0 ; i--)
	{            		  
		if(availableList.options.item(i).selected == true)
		{
			strToSendAttrib = availableList.options.item(i).value;
			selectedArray[j]=strToSendAttrib;
			j++;		
		}											 
	}
	var availableListOpener=document.getElementById(selid);
	n=0;
	for(k = availableListOpener.length-1; k >= 0 ;k--)
	{  				         		  
		if(availableListOpener.options.item(k).value == selectedArray[n])
		{
			availableListOpener.options.item(k).selected = true;
			n++;	
		}
		else
		{
			availableListOpener.options.item(k).selected = false;
		}													 
	}
	document.getElementById(divid).style.display='none';
}
function close_popup(divid)
{
	if(document.getElementById(divid)) {
		document.getElementById(divid).style.display='none';
	}
}
function check_chart_log(){
	var login_pass=$.trim($("#login_password").val());
	var login_lock_user=$.trim($("#login_lock_user_id").val());
	var chart_lock_id=$.trim($("#chart_lock_id").val());
	var patient_id=$("#patient_id").val();
	var confirmation_id=$("#pConfId").val();
	var form_name=$("#chart_form_name").val();
	var login_user=$("#login_user").val();						
	var ajaxURL="chart_pt_lock_ajax.php?task=login&chart_login_lock_user="+login_lock_user+"&login_pass="+login_pass+"&chart_lock_id="+chart_lock_id+"&patient_id="+patient_id+"&conf_id="+confirmation_id+"&form_name="+form_name+"&login_user="+login_user;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) { //alert(r);$("#resp").html(r);
		if($.trim(r)==1){
			top.frames[0].displayMainFooter();
			$("#div_chrt_lock").hide();
		}else{
			alert("Enter correct password");
			$("#login_password").val("");	
		}
	  }
	});
}
function chart_log_del(){
	var frmObj = top.mainFrame.main_frmInner;
	var patient_id		= '';
	var confirmation_id	= '';
	var form_name		= '';
	var login_user		= '';
	if(frmObj.document.getElementById("patient_id")) {
		patient_id=frmObj.document.getElementById("patient_id").value;
	}
	if(frmObj.document.getElementById("pConfId")) {
		confirmation_id=frmObj.document.getElementById("pConfId").value;
	}
	if(frmObj.document.getElementById("chart_form_name")) {
		form_name=frmObj.document.getElementById("chart_form_name").value;
	}
	if(frmObj.document.getElementById("login_user")) {
		login_user=frmObj.document.getElementById("login_user").value;
	}
	if(patient_id && confirmation_id && form_name && login_user) {
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
			alert ("Browser does not support HTTP Request")
			return
		}
		var url="chart_pt_lock_ajax.php";
		url=url+"?task=delete_log";
		url=url+"&patient_id="+patient_id;
		url=url+"&conf_id="+confirmation_id;
		url=url+"&form_name="+form_name;
		url=url+"&login_user="+login_user;
		xmlHttp.onreadystatechange=function() {
			if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
			} 
		};
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
}

function downloadCSV()
{
		var url = window.location.href;    
		if (url.indexOf('?') > -1){
			 url += '&action=csv'
		}else{
			 url += '?action=csv'
		}
		window.location.href = url;
}

function changeSelectpickerColor(classIdName)
{
	var isGroup	=	(classIdName.indexOf('.') != '-1') ?	true : false;
	
	if(isGroup)
		var objGroup =	document.getElementsByClassName(classIdName.substr(1));
	else
		var objGroup =	document.getElementById(classIdName);
	
	if( objGroup ) 
	{
		var _bgColor	=	'#F6C67A';
		for(m=0; m < objGroup.length; m++)
		{ 
			var v	=	objGroup[m].value;
			if(typeof v == 'undefined') v = '';
			
			if(v !== '') _bgColor = '#FFF';	
		}
		
		for(m=0; m < objGroup.length; m++)
		{ 
			objGroup[m].parentNode.style.backgroundColor = _bgColor;
		}
	}
		
}

function clearMedVal(obj)
{
	if(obj.checked==true)
	{
		//clear	all values
		$('.noMedOn').val('');
		$('.noMedOn').attr('disabled',true);
		$('.noMedOn').css("background-color", "#F0F0F0");
		
		$('#no_medication_comments').attr('disabled',false);
		$('#no_medication_comments').css("background-color", "");
	}	
	else 
	{
		$('.noMedOn').attr('disabled',false);
		$('.noMedOn').css("background-color", "");
		
		$('#no_medication_comments').val('');
		$('#no_medication_comments').attr('disabled',true);
		$('#no_medication_comments').css("background-color", "#F0F0F0");
		
	}
}

function changeBgColor(_this,id)
{
	var v = document.getElementById(id).value;
	var c = '#FFFFFF';
	c = (v != '' || _this.checked == true ) ? c : '#F6C67A';
	document.getElementById(id).style.backgroundColor = c
}

function textAreaAdjust(o) {
	var scrollLeft = window.pageXOffset || (document.documentElement || document.body.parentNode || document.body).scrollLeft;
	var scrollTop  = window.pageYOffset || (document.documentElement || document.body.parentNode || document.body).scrollTop;
	
	o.style.minHeight = 0 ;
	var minHght = o.scrollHeight;
	//if(o.scrollHeight > 200) { minHght  = 100; }
 	o.style.minHeight = (1+minHght)+"px";

	//START CODE TO RESET ALIGNMENT OF SCAN/UPLOAD IOL IN OPERATING ROOM
	var p = $("#below_summary_dummy");
	var offset = p.offset();
	if($("#below_summary_dummy").length >0) {
		$("#below_summary_dummy").html("");	
	}
	if($("#below_summary").length >0) {
		$("#below_summary").css({"left":+offset.left,"top":+offset.top});
	}
	//END CODE TO RESET ALIGNMENT OF SCAN/UPLOAD IOL IN OPERATING ROOM
	
	window.scrollTo(scrollLeft, scrollTop);
	
}
function chkTmFormat(obj) {
	timeValue = obj.value;
	var flag=false;
	if(timeValue.length==8 || !timeValue) { 
		if(!timeValue) {return; }
		HH = timeValue.split(':')[0].substr(0,2);
		MM = parseFloat(timeValue.split(':')[1].substr(0,2));
		if(HH>24) {flag=true;}	
		if(MM>59) {flag=true;}	
	}
	var MM = '00';
	if(timeValue.length >=1){
		HH = timeValue.substr(0,2);
		if(timeValue.search(':')>=0) {
			HH = timeValue.split(':')[0].substr(0,2);
			MM = parseFloat(timeValue.split(':')[1].substr(0,2));
		}
		if(HH>24 || timeValue.length<=3) { 
			HH = timeValue.substr(0,1); 
			MM = parseFloat(timeValue.substr(1,2));

			if(parseFloat(timeValue.substr(0,2)) <= 24) {
				HH = timeValue.substr(0,2);
				MM = parseFloat((timeValue.substr(2,1)));
			}
			
			if(timeValue.search(':')>=0) {
				HH = timeValue.split(':')[0].substr(0,2);
				if(HH>24) { HH = timeValue.split(':')[0].substr(0,1); }
				MM = parseFloat(timeValue.split(':')[1].substr(0,2));
			}
			if(HH==0) {
				HH = timeValue.substr(0,2); 
				MM = parseFloat(timeValue.substr(2,2)); 
			}
			if(MM <= 9 && MM!=0) {MM = '0'+MM; }
			if(MM=='' || MM==0) {MM = '00';}
		}
		if(HH=='') { HH = '00';}
		if(HH <= 9 && HH.length==1) {HH = '0'+parseFloat(HH);}
	}else{
		HH = '00';
	}
	if(timeValue.length >3){
		if(MM=='00') {MM = parseFloat(timeValue.substr(2,2));}
		if(timeValue.search(':')>=0) {
			MM = parseFloat(timeValue.split(':')[1].substr(0,2));
		}
		if(MM <= 9 && MM.length==2) { MM = parseFloat(MM.substr(1,1)); }
		if((MM <= 9 && MM!=0) || MM.length==1){ MM = '0'+MM; }
		if(MM=='' || MM==0) {MM = '00'; }
	}else{
		//MM = '00';
	}
	var flagPM=true;
	if(HH>12){
		flagPM=false;
		HH = HH- 12;
		if(HH <= 9 && HH!=0){
			HH = '0'+HH;
		}
	}
	//if((HH >= 7 && HH <= 11 && flagPM==true))
	if((HH <= 11 && flagPM==true)){
		Suffix = 'AM';
	}else{
		Suffix = 'PM';
	}
	if(timeValue.search('a')>=0 || timeValue.search('A')>=0) {Suffix = 'AM'; }
	if(timeValue.search('p')>=0 || timeValue.search('P')>=0) {Suffix = 'PM'; }
	if(isNaN(HH))  { HH = '00';}
	if(isNaN(MM))  {MM = '00'; }
	
	if(MM>59 || HH=='00' || flag==true) {  alert('Please enter correct time format'); obj.value='';return;}		
	timeValue = HH+':'+MM+' '+Suffix;
	
	//START CODE FOR MILITARY TIME
	if(top.document.getElementById('show_military_time')) { //(FROM mainpage.php)
		if(top.document.getElementById('show_military_time').value=='YES') {
			timeValue = HH+':'+MM;
			if(Suffix=='PM' && HH !='12') {
				HH = parseFloat((parseFloat(HH)+12));
			}else if(Suffix=='AM' && HH =='12') {
				HH = '00';
			}
			timeValue = HH+':'+MM;
		}
	}
	//END CODE FOR MILITARY TIME
	
	obj.value = timeValue;
} 
function capitalizeStr(obj) {
    if(obj) {
		if(trimNew(obj.value)) {
			obj.value = trimNew(obj.value);
			obj.style.textTransform = "capitalize";
		}
	}
}