// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.


$(function(){
	
	$("body").on("mouseenter", "#vitalSignGridPop", function(event){
		$("#hiddCalPopId").val("vitalSignPopYes");	
	});
	
	$("body").on("mouseleave","#vitalSignGridPop", function(){
		//if($("#hiddCalPopId").val()  === "vitalSignPopYes" )
		//{
			$(this).fadeOut(100);	
			$("#hiddCalPopId").val('');
		//}
	});
	
	$('body').on('click','#vitalSignGridPop input[type="button"]',function(){
		
		var param = $(this).val() ;
		var id		=	$("#vitalSignGridHolder").val();
		var obj		=	$("#" + id + "") ;
		
		if(param === 'C' ) 
		{
			clearVitalGridPopVal(obj) ;
		}
		else
		{
			getVitalGridPopVal(obj,param);
		}
	});
	
	$(".timeStamp").on("click focus",function(){ 
		var $_this	=	$(this);
		var $_id		=	$_this.attr('id');
		var $_left	=	$_this.offset().left + ($(this).outerWidth(true) - $("#vitalSignGridPop").outerWidth(true) + 40) ;
		var $_top		=	$_this.offset().top - ($("#vitalSignGridPop").outerHeight(true) - $(this).outerHeight(true)  -90 ); 
		
		$("#vitalSignGridPop").css({'top' : $_top+'px', 'left' : $_left +'px'}).fadeIn(100);
		$("#vitalSignGridHolder").val($_id);
		if( !$_this.val()) {
			getAjaxTime('',$_this);
		}
		
	});
	$("body").on("click",".vitalSignGrid",function(){ 
		var $_this	=	$(this);
		var $_id		=	$_this.attr('id');
		var $_left	=	$_this.offset().left ;
	//	var $_top		=	$_this.offset().top - ($("#vitalSignGridPop").outerHeight(true) +10); 
		
		var $_top		=	setVerticalPosition($_this,$(document),$("#vitalSignGridPop"));
		$("#vitalSignGridPop").css({'top' : $_top+'px', 'left' : $_left +'px'}).fadeIn(100);
		
		$("#vitalSignGridHolder").val($_id);
		
		if($_this.val() === '' && $_this.hasClass('timeBox'))
		{
			timeBox($_this);	
		}
		
	});
		
	function timeBox($this){ 
			var Dsr			=	$this.attr('data-row-id');
			var Dpr, DprObj, DprVal = ''; 
			var	DprArr = [] ;
			
			if(Dsr > 1 )
			{
				Dpr		=	parseInt(Dsr - 1) ; 
				DprObj	=	$("#vitalSignGrid_"+ Dpr + "_1");
				DprVal	=	DprObj.val();
				if(DprVal)
				{
					DprArr	=	DprVal.split(':');
					DprArr[0]		=	(DprArr[1].indexOf('P') !== -1) ? (DprArr[0] !== '12' ? parseInt(DprArr[0]) + 12 : DprArr[0])  : ((DprArr[0] === '12' && DprArr[1].indexOf('A') !== -1) ? parseInt(DprArr[0]) -12 : DprArr[0])  ;
					DprArr[1]		=	$.trim(DprArr[1].replace('PM','').replace('AM','').replace('P','').replace('A',''));
					DprVal			=	DprArr[0] + ':' + DprArr[1];
				}
			}
			else if( $("#frmAction").val() === 'local_anes_record.php' )
			{
				DprVal=	$("#bp_temp6").val() ;
				if(DprVal)
				{
					DprArr			=	DprVal.split(':');
					DprArr[0]		=	(DprArr[1].indexOf('P') !== -1) ? (DprArr[0] !== '12' ? parseInt(DprArr[0]) + 12 : DprArr[0]) : ((DprArr[0] === '12' && DprArr[1].indexOf('A') !== -1) ? parseInt(DprArr[0]) -12 : DprArr[0])   ;
					DprArr[1]		=	$.trim(DprArr[1].replace('PM','').replace('AM','').replace('P','').replace('A',''));
					DprVal	=	DprArr[0] + ':' + DprArr[1];
					
				}
			}
			
			getAjaxTime(DprVal,$this);
			
	}
	function getAjaxTime(cn,obj)
	{	
		$.ajax({
				url : 'getAjaxTime.php',
				type:'POST',
				dataType:"json",
				data:{ 'v' : encodeURI(cn) },
				success:function(data)
				{
					obj.val(data);
				}
			});
		
	}

});


function getVitalGridPopVal(obj,n)
{
	var cVal	=	obj.val();
	if(n == 'A' || n == 'P' ) 	n = ' ' + n +'M';
	var nVal	=	cVal	+ n ;
	obj.val(nVal);
	obj.focus();
}

function clearVitalGridPopVal(obj)
{
		obj.val('');
}

$(document).ready(function() {
	//change date and time text to update form
	$(document).on('click','.dynamic_sig_dt .fa-edit', function(){
		//check if another popup for edit is opened then close it
		if($(document).find('#editSignature'))
		{
			$(document).find('#editSignature').find("#cancelDateTimeBtn").trigger('click');
			//close date time popup
			$("#vitalSignGridPop").trigger('mouseenter').trigger('mouseleave');
		}
		$(this).parent().parent().find('b').html('');
		$(this).parent().parent().find('b').css('float','left'); 
		$(this).parent().parent().find('b').css('margin-right','5px'); 
		var dateTimeStr=$(this).parent().html();
		dateTimeStr=dateTimeStr.trim();	
		dateTimeStr = dateTimeStr.replace(/\s\s+/g, ' ');
		var dtArr=dateTimeStr.split(' ');
		var dtVal = dtArr[1]+' '+dtArr[2];
		if(dtArr[2] != 'AM' && dtArr[2] != 'PM' && dtArr[2] != 'am' && dtArr[2] != 'pm') {
			dtVal = dtArr[1];	
		}
		var string="<span id='editSignature'>";
		string+="<div class='input-group datepickertxt' style='float:left;width:130px;'><input type='text' aria-describedby='basic-addon1' placeholder='MM-DD-YYYY' class='form-control  datepickertxt required' name='sigNewDate' id='sigNewDate' value='"+dtArr[0]+"'><div class='input-group-addon datepicker'><a href='javascript:void(0)'><span class='glyphicon glyphicon-calendar'></span></a></div></div>";
		string+="<input type='text' class='form-control vitalSignGrid timeBox' style='float:left;width:80px; margin-left:5px' name='sigNewTime' id='sigNewTime' value='"+dtVal+"'>";
		
		string+="&nbsp;<a id='updateDateTimeBtn' class='btn btn-success' href='javascript:void(0)'><b class='fa fa-save'></b></a>";
		string+="&nbsp;<a id='cancelDateTimeBtn' class='btn btn-danger' href='javascript:void(0)'><b class='fa fa-close'></b></a>";
		
		string+="</span>";
		$(this).parent().html(string);
	});
	
	$(document).on('click','#cancelDateTimeBtn', function(){
			if(document.getElementById('sigNewTime')) {
				chkTmFormat(document.getElementById('sigNewTime'));
			}
			//remove time popup
			$("#vitalSignGridPop").trigger('mouseenter').trigger('mouseleave');
			//get date time
			var newDate=$('#sigNewDate').val();
			var newTime=$('#sigNewTime').val();
			var obj=$(this).parent().parent();
			
			obj.parent().find('b').html('<b> Signature Date</b>');
			//assign date time
			obj.html(newDate+' '+newTime+' <span class="fa fa-edit"></span>');
	});
	//function to update date and time
	$(document).on('click','#updateDateTimeBtn', function(){
		if(document.getElementById('sigNewTime')) {
			chkTmFormat(document.getElementById('sigNewTime'));
		}
		$("#vitalSignGridPop").trigger('mouseenter').trigger('mouseleave');
		var newDate=$('#sigNewDate').val();
		var newTime=$('#sigNewTime').val();
		var obj=$(this).parent().parent();
		obj.parent().find('b').html('<b> Signature Date</b>');
		var table=obj.attr('data-table-name');
		var field=obj.attr('data-field-name');
		var id_field_name=obj.attr('data-id-name');
		var id=obj.attr('data-id-value');
		
		obj.html(newDate+' '+newTime+' <span class="fa fa-edit"></span>');
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return;
		}
			
		var url='update_sig_date_time_ajax.php?table='+table+'&field='+field+'&id_field_name='+id_field_name+'&id='+id+'&newDate='+newDate+'&newTime='+newTime;
		//alert(url);
		//xmlHttp.onreadystatechange=AjaxChangeBaseLine
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	});
});


function setHorizontalPosition(eventObj,documentObj,preDefineObj)
{
	var Left = eventObj.offset().left;
	var exDiff	=	15	;
	if( documentObj.width() < ( Left + preDefineObj.outerWidth(true) + exDiff ) )
	{
		Left = Left + ( documentObj.width() - (Left + preDefineObj.outerWidth(true) + exDiff ))	;
	}
	
	return Left	;	
}

function setVerticalPosition(eventObj,documentObj,preDefineObj)
{
	var Top = eventObj.offset().top;
	var exDiff	=	15	;
	
	if(Top - documentObj.scrollTop() < preDefineObj.outerHeight(true) )
	{
		Top = Top + (eventObj.outerHeight(true) ) ;
	}
	else
	{
		Top	= Top - (preDefineObj.outerHeight(true) ) ;	
	}
				
	return Top	;	
}
function outClick(callback) {
	 var subject = this;
	 $(document).click(function(event) {
		if(!$(event.target).closest(subject).length) {
			callback.call(subject);
		}
	});
	return this;
}

function bsValueUnchk(obj,frm){
	var frmObj = document.forms[frm];
	if(obj.value!=""){
		frmObj.chkBoxNS.checked = false; 
	}
	else{
		frmObj.chkBoxNS.checked = true; 
		frmObj.chkBoxNS.value = '1';
	}
	changeDiffChbxColor(2,'chkBoxNS','bsvalue');
}