// JavaScript Document


//all_admin_content_agree_multi
/*
$(window).load(function() {
   
	$(document).ready(function() {*/
	
	/*	window.moveTo(0, 0);
		window.resizeTo(screen.availWidth, screen.availHeight);
		
		var hippa =	$('.all_admin_content_agree');
		//var hippa = top.document.getElementById('div_ovrflow');
		var hippa_win = $(window).height();
		var header_height = $('.header_full_wrap').outerHeight(true);
		var footer_height = $('.footer_wrap ').outerHeight(true);
		var bottom_btns_h = $('.btn-footer-slider').outerHeight(true);
		var height_custom_scroll = $('#iframeHome').contents().find('#patient_info');
		var height_custom = hippa_win - (header_height+footer_height+bottom_btns_h);
	//	var height_custom2 = hippa_win - (header_height+footer_height+bottom_btns_h+sub_head+140);
		var sub_head = $('#iframeHome').contents().find('.subtracting-head').outerHeight(true);
	//	sub_head=139;
		var height_custom2  = height_custom - (sub_head);
		hippa.css({ 'min-height' : height_custom , 'max-height': height_custom });
		height_custom_scroll.css({ 'min-height' : height_custom2 , 'max-height': height_custom2});
		
		alert(height_custom);
		alert(height_custom2);
		alert(sub_head);
		//top.iframeHome.document.getElementById('div_scroll_id').style.height=height_custom2+'px';*/
		
	
$(window).load(function() {
   
   	parent.top.$(".loader").fadeOut(1000).hide(1000);
	
	$(document).ready(function() {
	
		//alert(screen.availWidth);
		//alert(screen.availHeight);
		//window.moveTo(0, 0);
		//window.resizeTo(screen.availWidth, screen.availHeight);
	
		var hippa 		=	$('.all_admin_content_agree');
		var hippa_win 	=	$(window).height();
		
		var header_height	=	$('.header_full_wrap').outerHeight(true);
		var footer_height	=	$('.footer_wrap ').outerHeight(true);
		var bottom_btns_h	=	($('#div_innr_btn').outerHeight(true) +  $('#okCancelTr').outerHeight(true));
		var sub_head		=	$('.subtracting-head').outerHeight(true);
		var height_custom_scroll=	$('.scrollable_yes');
		var height_custom 	=	hippa_win - (header_height+footer_height+bottom_btns_h);
		var height_custom2	=	height_custom  - (sub_head);
		//var sub_head_j =  document.getElementsByClassName('subtracting-head-1');
		hippa.css({ 'min-height' : height_custom , 'max-height': height_custom });
		//hippa.find('#iframeHome').css('height',height_custom + 'px');
		
		height_custom_scroll.css({ 'min-height' : height_custom2 , 'max-height': height_custom2});
		//$('.datetimepicker,.datepickertxt').datetimepicker({format: 'MM-DD-YYYY'});
		
	});

	

	$(function(){
		
		
		$("body").on('click','ul#myTab li a ',function()
		{
			updateModalTitle($(this));
			
		});
		
		$("body").on('click','a.edit_epost',function()
		{	// Enter Code Here
			var EID		=	$(this).attr('data-epost-id');
			var CNote	=	$(this).attr('data-chart-note');
			var DTime	=	$(this).attr('data-date-time');
			var OpTxt	=	$(this).attr('data-operator-text');
			var Sibling	=	$(this).siblings('p.epost_content');
			var Cont	=	Sibling.html();
			var Tag		=	$('a.edit_epost[data-epost-id="'+EID+'"]');
			var Parent	=	Tag.closest('div.col-lg-12');
			var GParent	=	Parent.parent('div');
			
			var FormHtml =	'';
			FormHtml	+=	'<form id="editForm">';
			FormHtml	+=	$("form#addForm").html();
			FormHtml	+=	'</form>';
			
			$("#ePostModal").find('a.edit_epost').attr('disabled','disabled');
			Parent.html(FormHtml);
			Parent.find('label[for="select_Epost"]').html('Edit ePostIt');
			Parent.find('select#chart_notes').removeAttr('multiple');
			Parent.find('select#chart_notes').attr('name','chart_notes');
			Parent.find('select#chart_notes option[value="'+CNote+'"]').attr('selected','selected');
			Parent.find('select#chart_notes').siblings('div').remove();
			Parent.find('textarea#epostText').val(Cont);
			Parent.find('input[name="request"]').val('edit');
			Parent.find('input[name="request"]').after('<input type="hidden" readonly value="'+EID+'" name="ePostID" /> ');
			Parent.find('button[type="reset"]').html('Cancel');
			Parent.find('button[type="reset"]').removeAttr('id');
			Parent.find('button[type="reset"]').attr('data-ePostOperatorText',OpTxt);
			Parent.find('button[type="reset"]').attr('data-ePostDateTime',DTime);
			Parent.find('button[type="reset"]').attr('data-ePostContent',Cont);
			Parent.find('button[type="reset"]').attr('data-ePostID',EID);
			Parent.find('button[type="reset"]').attr('data-ePostChartNote',CNote);
			$(".selectpicker").selectpicker('refresh');
			
			GParent.animate({ scrollTop: GParent.scrollTop() + Parent.position().top }, 1000);
			
		});
		
		$("body").on('click','form#editForm button[type="reset"]',function()
		{
			var ePostDateTime			=	$(this).attr('data-ePostDateTime');
			var ePostOperatorText	=	$(this).attr('data-ePostOperatorText');
			var ePostContent			=	$(this).attr('data-ePostContent');
			var ePostID						=	$(this).attr('data-ePostID');
			var ePostChartNote		=	$(this).attr('data-ePostChartNote');
			
			var MOprTxt		=	(ePostOperatorText.indexOf('Modified') !== -1)	? ePostOperatorText.replace('Modified By ','').trim()	:	'';
			var COperTxt 	= (ePostOperatorText.indexOf('Created') !== -1)		? ePostOperatorText.replace('Created By ','').trim()	:	'';
			
			var Parent		=	$(this).closest('form#editForm').parent('div.col-lg-12');
			
			var	html			=	generateEpostRow('dataRow',ePostDateTime,ePostContent,ePostID,ePostChartNote,COperTxt,MOprTxt);
			
			Parent.replaceWith(html);
			
			$("#ePostModal").find('a.edit_epost').removeAttr('disabled');
			
		});
		
		$('form#addForm :reset').on('click', function(event) {
			event.preventDefault();
			
			var $form = $(event.target).closest('form');
			$form[0].reset();
			$form.find('select').selectpicker('render');
		});
		//$("#resetButton").click(function() { $('select#chart_notes').selectpicker('refresh'); });
		
		$("body").on('click','a.delete_epost',function()
		{	// Enter Code Here 
			if(confirm('Are you sure to delete ?'))
			{
				
				var EID		=	$(this).attr('data-epost-id');
				var Tag		=	$('a.delete_epost[data-epost-id="'+EID+'"]');
				var Parent	=	Tag.closest('div.col-lg-12');
				var GParent	=	Parent.parent('div'); 
				var tabPane	=	Parent.closest('div.tab-pane').attr('id');
				var SID		=	$("form#addForm").find('input[name="SID"]').val();
				
				$.ajax({
					url : 'common/epost_new_patient_popup.php',
					type:'POST',
					dataType:'json',
					data : {'request':'delete', 'ePostID':EID},
					beforeSend:function(){
						var txt	=	"<span class='fa fa-spinner fa-pulse '></span>&nbsp;Deleting...";
						Tag.html(txt);
					},
					complete:function(){
						var txt	=	"<span class='fa fa-trash'></span>&nbsp;Delete";
						Tag.html(txt);
						setTimeout(function(){ $("span#response").html(''); }, 3000);	
					},
					success:function(data)
					{ 	//alert(JSON.stringify(data));
						var msg	=	"";
						if(data.response === 1 ){
							msg		=	"Record Deleted Successfully";
							Parent.remove();
							updateCount(tabPane,'D');
							
							var CAlert	=	parseInt($("b#alertCount").html());
							var Visible	=	(CAlert > 0)  ? 'visible'	: 'hidden';
							$('#alert_'+SID+'').css('visibility',Visible);
							
							var CEpost	=	parseInt($("b#epostCount").html());
							
							if(CEpost > 0 ) 
							{
								if(!$("#epost_"+SID+"").hasClass('alert-epost'))	{	
										$("#epost_"+SID+"").addClass('alert-epost');
								}
							}
							else
							{	
								if($("#epost_"+SID+"").hasClass('alert-epost'))	 {
									$("#epost_"+SID+"").removeClass('alert-epost');
								}
								
							}
								
						}
						else
						{
							msg 	=	"Error while deleting record";	
						}
						//addClass(rType)
						$("span#response").css('background','transparent').html(msg);
						NoRecordUpdate(GParent);
					
					},
					
				
				});
			}
			
			return false;
		});
		
		$("body").on('click','a.disable_sx_alerts',function()
		{	// Enter Code Here 
			if(confirm('Disable Patient Sx Alerts - Are you sure ?'))
			{
				
				var Parent	=	$('div#sxALerts');
				var PID		=	$(this).attr('data-patient-id');
				var DCR		=	$(this).attr('data-decr');
				var GParent	=	Parent.parent('div'); 
				var tabPane	=	Parent.closest('div.tab-pane').attr('id');
				var SID		=	$("form#addForm").find('input[name="SID"]').val();
				
				$.ajax({
					url : 'common/epost_new_patient_popup.php',
					type:'POST',
					dataType:'json',
					data : {'request':'disableSxAlert', 'PID':PID},
					beforeSend:function(){ 
						var txt	=	"<span class='fa fa-spinner fa-pulse '></span>&nbsp;Please Wait...";
						$(this).html(txt);
					},
					complete:function(){
						var txt	=	"<span class='fa fa-close'></span>&nbsp;Disable";
						$(this).html(txt);
						setTimeout(function(){ $("span#response").html(''); }, 3000);	
					},
					success:function(data)
					{ 	//alert(JSON.stringify(data));
						var msg	=	"";
						if(data.response === 1 ){
							msg		=	"Patient Sx Alerts Disabled Successfully";
							Parent.remove();
							updateCount(tabPane,'DV' + DCR);
							
							var CAlert	=	parseInt($("b#alertCount").html());
							var Visible	=	(CAlert > 0)  ? 'visible'	: 'hidden';
							$('#alert_'+SID+'').css('visibility',Visible);
							
						}
						else
						{
							msg 	=	"Error While Disabling Patient Sx Alerts";	
						}
						//addClass(rType)
						$("span#response").css('background','transparent').html(msg);
						NoRecordUpdate(GParent);
					
					},
					
				
				});
			}
			
			return false;
		});
		
		$("body").on('submit','form#addForm,form#editForm',function()
		{	// Enter Code Here 
			
			var PSpan	=	$(this).find('span#processing');
			var $inputs	=	$(this).find('button,textarea,select');
			var content	=	$(this).find("#epostText").val().trim();
			var chartNote=	$(this).find("#chart_notes").val();
			if(chartNote === null){
				chartNote = '';
			}
			var formID	=	$(this).attr('id');
			var SID		=	$(this).find('input[name="SID"]').val();
			var ETemp	=	$(this).closest('div.active').attr('id').toLowerCase();
			var Parent	=	$("#"+formID+"").parent('div.col-lg-12');
			var GParent	=	Parent.parent('div'); 
			
			if( chartNote === '' )
			{
				alert('Select Chart Note for epost'); return false;
			}	
			else if(content === '' )
			{
				alert('Enter epost content'); return false;
			}
			
			else{
				var data	=	$(this).serialize();
				
				$.ajax({
					url : 'common/epost_new_patient_popup.php',
					type:'POST',
					dataType:'json',
					data : data,
					beforeSend:function(){
						$inputs.attr('disabled','disabled');
						var txt	=	"&nbsp;<b class='fa fa-spinner fa-pulse'></b>&nbsp;Processing request...Please Wait !!!";
						PSpan.html(txt);
					},
					complete:function(){
						PSpan.html('');
						setTimeout(function(){ $("span#response").html(''); }, 3000);	
					},
					success:function(data)
					{	
						var msg	=	"";
						var LTemp=	"";	
						
						if(data.response === 1 ){
								
							if(formID === 'addForm')
							{ // Enter Code Here
								msg = data.rMessage;
								$inputs.removeAttr('disabled').val('');
								$(".selectpicker").selectpicker('refresh');
								$.each(data.ePost,function(i,v)
								{
									LTemp = v.template;
									updateContent(LTemp,v.eDateTime,v.content,v.epost_id,v.consent_template_id,v.created_operator_name,v.modified_operator_name);
									updateCount(LTemp,'I');
								});
							}
							else if( formID === 'editForm')
							{ // Enter Code Here
								LTemp	=	(data.template).toLowerCase();
								msg		= "Record submitted successfully";
								Parent.remove();
								updateContent(data.template,data.ePost.eDateTime,data.ePost.content,data.ePost.epost_id,data.ePost.consent_template_id,data.ePost.created_operator_name,data.ePost.modified_operator_name);
								$("#ePostModal").find('a.edit_epost').removeAttr('disabled');
								if(ETemp !== LTemp) 	{
									updateCount(ETemp,'D');
									updateCount(LTemp,'I');
									NoRecordUpdate(GParent);
								}
							}
							
							var CAlert	=	$("b#alertCount").html();
							CAlert	=	parseInt(CAlert);
							var Visible	=	(CAlert > 0)  ? 'visible'	: 'hidden';
							
							var CEpost	=	$("b#epostCount").html();
							CEpost	=	parseInt(CEpost);
							
							if(CEpost > 0 ) 
							{
								if(!$("#epost_"+SID+"").hasClass('alert-epost'))	
								{
									$("#epost_"+SID+"").addClass('alert-epost');
								}
							}
							else{
								if($("#epost_"+SID+"").hasClass('alert-epost'))
								{
									$("#epost_"+SID+"").removeClass('alert-epost');
								}
								
							}
							$('#alert_'+SID+'').css('visibility',Visible);	
							
							//if( formID == 'editForm')	$('#'+LTemp +"_" + SID+'').trigger('click');
						
						}
						else
						{
							msg 	=	"Error while submitting record";	
						}
						//addClass(rType)
						$("span#response").css('background','transparent').html(msg);
						
					},
					
				
				});
			}
				
			return false;	
		});
		
		$('body').on('click', '[id^=alert_], [id^=epost_] ', function()
		{
			parent.top.$(".loader").fadeIn('fast').show('fast'); 
			
			$("b#epostCount,b#alertCount").html('0');
			$("span#response,span#processing").html('');
			
			var $this	=	$(this).find('b');
			var PR		=	$this.attr('data-privileges');				// Privileges
			var PRT		=	$this.attr('data-privileges-type');			// Privileges Type
			var AD		=	$this.attr('data-access-denied');			// Access Denied
			var SID		=	$this.attr('data-stub-id');					// Stub ID
			//var PS		=	$this.attr('data-patient-search');			// Patient Search
			var PID		=	$this.attr('data-patient-id');				// Patient ID
			var PCI		=	$this.attr('data-patient-confirmation-id');	// Patient Confirmation ID
			//var NID		=	$this.attr('data-imw-id');				// Imw ID
			//var AS		=	$this.attr('data-app-status');				// Application Status
			var EID		=	$this.attr('data-epost-id');				// Epost id
			var EF		=	$this.attr('data-epost-for');				// Epost For
			var ACD		=	$this.attr('data-action-denied');				// Save/Edit/Delete Action Denied
			
			if(!PID)
			{
				registerUser(SID,EID);
			}
			
			if(PR === 'Coordinator' && PRT !== 'Master')
			{
				alert(AD); return false;
			}
			else
			{
				$.ajax({
				
					url : 'common/epost_new_patient_popup.php',
					type:'POST',
					dataType:'json',
					data : {'SID':SID, 'PID':PID, 'PCI':PCI},
					beforeSend:function()
					{
						
						$('#ePostModal').modal({
							show: true,
							backdrop: true,
							keyboard: true
						});
					},
					complete:function()
					{
						parent.top.$(".loader").fadeOut('slow').hide('slow');
					},
					success:function(data)
					{
						//console.log(data);
						$('form#addForm input[name="PID"]').val(PID);
						$('form#addForm input[name="PCI"]').val(PCI);
						$('form#addForm input[name="SID"]').val(SID);
						
						$("#ePostModal .patientName").html('<b class="fa fa-user-plus "></b>&nbsp;'+data.PatientName);
						
						var html=	"";
						
						
							
						if(data.ePostData.length > 0 )
						{	
							$("b#epostCount").html(data.ePostData.length);
							$.each(data.ePostData,function(index,ePost){
								html	+=	generateEpostRow('dataRow',ePost.eDateTime,ePost.content,ePost.epost_id,ePost.consent_template_id, ePost.created_operator_name, ePost.modified_operator_name);
							});
						}
						else
						{	
							html	+=	generateEpostRow('noRecord');	
						}
						
						$("#ePostModal #Epost .form_inside_modal").html(html);
						
						html	=	"";
						var EAlertCnt		=	data.eAlertData.length;
						var IAlertCnt		=	data.iAlertData.length;
						if(EAlertCnt > 0 || IAlertCnt > 0 )
						{	
							
							var TotLen	=	parseInt(EAlertCnt +  IAlertCnt);
							$("b#alertCount").html( TotLen );
							
							$.each(data.eAlertData,function(index,eAlert){
								html	+=	generateEpostRow('dataRow',eAlert.eDateTime,eAlert.content,eAlert.epost_id, eAlert.consent_template_id, eAlert.created_operator_name, eAlert.modified_operator_name);
							});
						
							if( IAlertCnt > 0 )
							{
								html	+=	'<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="sxALerts" >';
								html	+=	'<div class="form_inner_m " style="border:solid 1px #DDD; border-radius:6px;  ">';
								
								
								html	+=	'<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 row " style="padding-top:3px; background-color: #5BC0DE; color:white; font-weight:600; margin-left:0; border-radius:6px 6px 0 0 ;" >';
								html	+=	'<div class="col-lg-3 col-md-3 sm-4 col-xs-4 " > Date Time</div>';
								html	+=	'<div class="col-lg-9 col-md-9 sm-8 col-xs-8 " >Patient Sx Alerts';
								html	+=	'<a href="javascript:void(0)" class="btn btn-danger disable_sx_alerts" style="float:right; padding:4px !important;" data-patient-id="'+PID+'" data-decr="'+IAlertCnt+'" >  <span class="fa fa-close"></span>   Disable    </a>';
								html	+=	'</div>';
								html	+=	'</div>';
									
								$.each(data.iAlertData,function(index,iAlert){
									//html	+=	generateEpostRow('SxAlertRow',iAlert.eDateTime,iAlert.content,iAlert.patient_alert_id);
									var css	=	((index+1) !==  IAlertCnt) ? 'border-bottom:1px solid #DDD;' : '' ; 
									html	+=	'<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 row" style="margin-left:0; padding:10px;' +css + '" >';
									html	+=	'<div class="col-lg-3 col-md-3 sm-4 col-xs-4 " > <i class="fa fa-clock-o"></i> '  + iAlert.eDateTime  + '</div>';
									html	+=	'<div class="col-lg-9 col-md-9 sm-8 col-xs-8 " >'  + iAlert.content +	'</div>';
									html	+=	'</div>';
								});
								
								
								html	+=	'</div>';
								html	+=	'</div>';
							}
							
							
							
						}
						else
						{
							html	+=	generateEpostRow('noRecord');
						}
						$("#ePostModal #Alert .form_inside_modal").html(html);
						
						
						if(ACD === 'yes')
						{
							$('#submitButton, .edit_epost, .delete_epost').hide();
						}
						else
						{
							$('#submitButton, .edit_epost, .delete_epost').show();
						}
						
						
						// Update Chart Notes options
						$("form#addForm select#chart_notes").find('option').remove();
						
						$.each(data.chartNotes,function(index,value){
							$("form#addForm select#chart_notes").append($('<option/>', { value: index, text : value }));
						});
						$(".selectpicker").selectpicker('refresh');
						// End Update Chart Notes options
						
						
						$('#ePostModal').find('.active').removeClass('active');
						if(EF === 'alert') { 
							$('#ePostModal #Alert').addClass('fadein active'); 
							$('ul#myTab a[href="#Alert"]').closest('li').addClass('active');
						}
						else { 
							$('#ePostModal #Epost').addClass('fadein active');
							$('ul#myTab a[href="#Epost"]').closest('li').addClass('active');
						}
						
						updateModalTitle();
						
					},
					/*error:function(response){
						//console.log(response);	
					}*/
				});
			
			}
		
			return false;
		});	
		
		
		// Helper Functions for epost
		var updateCount	=	function(eType,uType)
		{
			eType	=	eType.toLowerCase();
			var CV	=	$("b#"+eType+"Count").html();
			
			CV		=	parseInt(CV);
			
			if(uType === 'D')	
			{
				CV	=	CV - 1;
			}
			else if(uType.substr(0,2) === 'DV')	
			{
				CV	=	CV - parseInt(uType.substr(2));
			}
			else	
			{
				CV	=	CV + 1;
			}
			
			$("b#"+eType+"Count").html(CV);
			
		};
		
		var updateContent	=	function(eType,eDateTime,content,ePostID,ChartNote,created_operator_name,modified_operator_name)
		{
			var ET		=	eType.toLowerCase();
			var CV	=	$("b#"+ET+"Count").html();
			CV		=	parseInt(CV);
			
			var html	=	generateEpostRow('dataRow',eDateTime,content,ePostID,ChartNote,created_operator_name,modified_operator_name);
			
			if(CV === 0)	{
				$("#"+eType + " div.form_inside_modal ").html(html);
			}
			else{
				$("#"+eType + " div.form_inside_modal ").prepend(html);
			}
			
		};
		
		var generateEpostRow =	function(rowType,eDateTime,content,ePostID,ChartNote,created_operator_name,modified_operator_name)
		{
			var operator_value ='';
			if(modified_operator_name) {
				operator_value	=	'Modified By '+ modified_operator_name;
			}else if(created_operator_name) {
				operator_value	=	'Created By '+created_operator_name;
			}
			var html	 =	"";
			
			html	+=	'<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
			html	+=	'<div class="form_inner_m" >';
			html	+=	'<div class="well well-lg">';
			if(rowType === 'dataRow')
			{
				html	+=	'<p><i class="fa fa-clock-o"></i> ' + eDateTime + '<span style="padding-left:20px;">'+ operator_value+'</span></p>';
				html	+=	'<p class="epost_content">' + content + '</p>';
				
				html	+=	'<a id="edit" href="javascript:void(0)" class="btn btn-info edit_epost" data-epost-id="'+ePostID+'" data-chart-note="'+ChartNote+'" data-date-time="'+eDateTime+'" data-operator-text="'+operator_value+'"><span class="glyphicon glyphicon-edit"></span> Edit</a>&nbsp;';
				html	+=	'<a href="javascript:void(0)" class="btn btn-danger delete_epost" data-epost-id="'+ePostID+'">  <span class="fa fa-trash"></span>   Delete    </a>';
			}
			else{
				
				html	+=	'<p> No Records Found</p>';
				
			}
			html	+=	'</div>';
			html	+=	'</div>';
			html	+=	'</div>';
			
			
			return html;
		
		};
		
		var NoRecordUpdate	=	function(object)
		{
			var Records	=	object.find('div.col-lg-12').length;
			Records		=	(Records === 0 || Records === '') ? 0 : Records;	//(Records - isRemove);
						
			if( Records === 0 )
			{
				var html =	generateEpostRow('noRecord');
				object.html(html);
			}	
		};
		
		var registerUser	=	function(SID,EID)
		{
			var SDate	=	$("#ePostModal input#epost_selected_date").val();
			
			$.ajax({
					url : 'common/userRegistration.php',
					type:'POST',
					dataType:'json',
					data : {'request':'register', 'SID':SID, 'SDate':SDate },
					success:function(response)
					{
						$('.epost_trigger > b[data-epost-id="'+EID+'"]').attr('data-imw-id',response['NID']);
						$('.epost_trigger > b[data-epost-id="'+EID+'"]').attr('data-patient-id',response['PID']);
						
						$('.epost_trigger > b[data-epost-id="'+EID+'"]').trigger('click');
					},
					/*error:function()
					{
						console.log('Error in file');
					}*/
				
			});
				
						
			
				
		};	
		
		var updateModalTitle=	function($this)
		{
			var C;
			if($this)
				C	=	$this.attr('data-title');
			else
				C	=	$(" ul#myTab > li.active a ").attr('data-title');
			
			var T	=	(C === 'Epost' ? 'EpostIt' : ( C === 'Alert' ? 'Patient Alert' : 'Add New Epost' ));
			
			$("#ModalTitle").html(T);	
		};
		// End Helper Function for epost
		
	
	});

});


//START FUNCTION TO AUTO ADJUST TEXTAREA
$(function(){
	if(top.mainFrame) {
		if(top.mainFrame.main_frmInner) {
			if(top.mainFrame.main_frmInner.document) {
				var frmInputObj = top.mainFrame.main_frmInner.document;
				var frmInputGetArea = frmInputObj.getElementsByTagName('textarea');
				var frmFormObj = top.mainFrame.main_frmInner.document.forms[0];
				var frmName = frmFormObj.name;
				if(frmInputGetArea && (frmName == 'frm_pre_op_phy_order' || frmName == 'frm_post_op_phys' || frmName == 'frm_local_anes_rec' || frmName == 'frm_op_room')) {
					var idGetArea = "";
					for(var lGetArea=0;lGetArea<frmInputGetArea.length;lGetArea++) {
						idGetArea = frmInputGetArea[lGetArea].getAttribute('id');
						if($("#"+idGetArea).length >0) {
							textAreaAdjust($("#"+idGetArea)[0]);
						}
						/*
						$("#"+idGetArea).on('keyup',function(){
							textAreaAdjust($("#"+idGetArea)[0]);	
						});*/
						
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
						
					}
				}
				
			}
		}
	}
});
//END FUNCTION TO AUTO ADJUST TEXTAREA
