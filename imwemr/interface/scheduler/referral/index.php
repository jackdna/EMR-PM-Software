<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
require_once(dirname(__FILE__).'/../../../config/globals.php');
$library_path = $GLOBALS['webroot'].'/library';

?>
<!DOCTYPE html>
<html>
<head>
	<title>imwemr</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
	<?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
  	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
 	<?php } ?>
 	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
	<!--<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>-->
	<script>top.show_loading_image('show')</script>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
	<![endif]-->	
	<style type="text/css">
		.span { display: block; font-weight:599; font-size:14px; font-style:italic; font-family:robotobold; }
		.adminnw thead th { position: sticky; top: 0; background: #fff; }
	</style>
</head>
<body>
	<input type="hidden" name="ord_by_field" id="ord_by_field" value="" autocomplete="off">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC" autocomplete="off">
<div class="whtboxs" style="overflow-y: auto; height: 100vh;">  
	<table class="table table-hover table-striped table-bordered adminnw" style="border-collapse: separate;">
		<thead>
			<tr class = "link_cursor">
				<th width="8%">Pt. Name-ID</th>
				<th width="8%">Pri.&nbsp;Ins.</th>
				<th width="8%" onClick="load_listing('','proc',this);" id="proc">Procedure<span></span></th>
				<th width="9%" onClick="load_listing('','sa_app_start_date',this);" id="sa_app_start_date">Appt.&nbsp;Date&nbsp;&&nbsp;Time<span></span></th>
				<th width="7%" onClick="load_listing('','physician',this);" id="physician">Physician<span></span></th>
				<th width="8%"><span style="display: inline-block; margin-top: 12px; float:left;">PCP</span> <select class="select minimal pull-right" style="width:70px;" id="dropx" onChange="load_listing(this.value);"><option value="" selected>Active</option><option value="reschedule">Reschedule</option></select></th>
				<th width="6%">PCP&nbsp;Phone#</th>
				<th width="8%">Referring&nbsp;Phy</th>
				<th width="6%" onClick="load_listing('','phone',this);" id="phone">Ref&nbsp;Phy&nbsp;Phone#<span></span></th>
				<th width="6%">Referring#</th>
				<th width="5%">#of&nbsp;Referrals</th>
				<th width="6%" onClick="load_listing('','effective_date',this);" id="effective_date">Dates<span></span></th>
				<th width="7%">Authorization</th>
				<th width="6%">Auth&nbsp;Date </th>
				<th></th>
			</tr>
		</thead>
		<tbody id="results"></tbody>			
	</table>
	
	<div id="referral_modal" class="modal" >
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h4 class="modal-title" id="modal_title">Edit Referral - <span id="pt_name_id"></span> </h4>
				</div>

				<div class="modal-body" style="min-height:300px;max-height:450px; overflow:hidden; overflow-y:auto;">
					<form id="edit_referral_form">
						<input type="hidden" name="action" id="action" />
						<input type="hidden" name="appt_id" id="appt_id" />
						<input type="hidden" name="patient_id" id="patient_id" />
						<input type="hidden" name="ins_data_id" id="ins_data_id" />
						<input type="hidden" name="ins_case_id" id="ins_case_id" />
						<input type="hidden" name="ins_prov_id" id="ins_prov_id" />
						<input type="hidden" name="pt_ref_id" id="pt_ref_id" />
						<input type="hidden" name="pt_pcp_id" id="pt_pcp_id" />
						<input type="hidden" name="reff_id" id="reff_id" />
						<input type="hidden" name="auth_id" id="auth_id" />
						<div class="row">
							
							<div class="col-xs-3">
								<label>Primary Insurance Provider</label>
								<span id="pri_ins_prov" class="span"></span>
							</div>
							
							<div class="col-xs-3">
								<label>Appt. Date & Time</label>
								<span id="appt_date_time" class="span"></span>
							</div>
							
							<div class="col-xs-3">
								<label>Physician</label>
								<span id="physician" class="span"></span>
							</div>

							<div class="col-xs-3">
								<label>Procedure</label>	
								<select name="pri_proc" id="pri_proc" class="form-control select minimal"></select>
							</div>

						</div>
			
						<div class="clearfix">&nbsp;</div>
			
						<div class="row">
							<div class="col-xs-3">
								<label>Primary Care Physician</label>	
								<input type="text" name="pcp_phy" id="pcp_phy" class="form-control" onkeyup="top.loadPhysicians(this,'pt_pcp_id');" placeholder="Last name, First name" onChange="get_phy_phone(this,'pt_pcp_id','pcp_phone');" />
							</div>

							<div class="col-xs-3">
								<label>PCP Phone</label>	
								<input type="text" name="pcp_phone" id="pcp_phone" class="form-control" onchange="top.set_phone_format(this,'###-###-####','','','form-control');" placeholder="PCP Phone" />
							</div>

							<div class="col-xs-3">
								<label>Ref. Physician</label>	
								<input type="text" name="ref_phy" id="ref_phy" class="form-control" onkeyup="top.loadPhysicians(this,'pt_ref_id');" placeholder="Last name, First name" onChange="get_phy_phone(this,'pt_ref_id','ref_phone');" />
							</div>

							<div class="col-xs-3">
								<label>Ref. Phy. Phone</label>	
								<input type="text" name="ref_phone" id="ref_phone" class="form-control" onchange="top.set_phone_format(this,'###-###-####','','','form-control');" placeholder="Ref. Phy. Phone" />
							</div>
						</div>
			
						<div class="clearfix">&nbsp;</div>
			
						<div class="row">
							<div class="col-xs-3">
								<div class="row">
									<div class="col-xs-6">
										<label>Referral#</label>
										<input type="text" name="refferal_no" id="refferal_no" class="form-control" placeholder="Referral#" />		
									</div>
									<div class="col-xs-6">
										<label>#Of Referrals</label>
										<input type="text" name="no_of_reffs" id="no_of_reffs" class="form-control" placeholder="#Of Referrals" />
									</div>	
								</div>

							</div>

							<div class="col-xs-3">
								<label>Start Date</label>
								<div class="input-group">
									<input type="text" name="reff_effective_date" id="reff_effective_date" class="form-control datepicker" placeholder="Start Date" />	
									<label class="input-group-addon btn" for="reff_effective_date">
										<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
									</label>
								</div>	

							</div>

							<div class="col-xs-3">
								<label>Authorization#</label>
								<input type="text" name="auth_name" id="auth_name" class="form-control" placeholder="Authorization#"/>	
							</div>

							<div class="col-xs-3">
								<label>Auth Date</label>
								<div class="input-group">
									<input type="text" name="auth_date" id="auth_date" class="form-control datepicker" placeholder="Auth Date" />	
									<label class="input-group-addon btn" for="auth_date">
										<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
									</label>
								</div>	
							</div>
						</div>
						
						<div class="clearfix">&nbsp;</div>

						<div class="row">
							<div class="col-xs-12">
								<label>Referral Notes</label>
								<textarea name="reff_notes" id="reff_notes" class="form-control" style="min-height:70px;" placeholder="Referral Notes"></textarea>		
							</div>
						</div>

						<div class="clearfix">&nbsp;</div>
						
					</form>	
				</div>

				<div class="modal-footer text-center">
					<button type="button" class="btn btn-success" onClick="submit_form();">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>

			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript">
	var result_data = [];
	var raw_data = [];
	
	function bind_datepicker(e)
	{
		$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
	}
	
	function submit_form(){
		var a = $("#appt_id").val();
		var d = result_data[a]
		var dataArr = {};
		var modified = false;
		
		$("#edit_referral_form").find('input[type=text],textarea,select').each(function(){
			var n = $(this).attr('name');
			if( n== 'pri_proc') n='procedureid';
			if( d[n] != $(this).val() ){
				modified = true;
				dataArr[n] = $(this).val();
			}
		});
		
		if( modified ){
			//dataArr['action']	= 'update';
			if(validate(dataArr)) {
				$("#action").val('update');
				save().done(function(r){after_save(r);});
			}
		}
		else{
			top.fAlert('No change detected');
		}
		return;
	}
	
	function validate(data){
		
		var msg = '';
		if( data['pcp_phone'] && typeof data['pcp_phone'] !== 'undefined' )	{
			if( $("#pt_pcp_id").val() <= 0 && $("#pcp_phy").val() == '' )
				msg += 'Primary care physician.<br>';
		}
		if( data['ref_phone'] && typeof data['ref_phone'] !== 'undefined' )	{
			if( $("#pt_ref_id").val() <= 0 && $("#ref_phy").val() == ''  )
				msg += 'Referring physician.<br>';
		}
		
		if( msg) { 
			top.fAlert('Please fill the following:<br> '+msg);
			return false;
		}
		
		return true;
	}
	
	function save() {
		var data = $("#edit_referral_form").serializeArray();
		return $.post(top.JS_WEB_ROOT_PATH+'/interface/scheduler/referral/ajax.php',data);	
	}
	
	function after_save(r){
		r = $.parseJSON(r);
		if(r.error.length > 0)
		{
			var m = '';
			$.each(r.error,function(i,v){
				m += v + '<br>';
			})
			top.fAlert(m)		
		}
		else {
			top.fAlert("Record saved successfully");
			load_listing();
		}
		
	}
	
	function edit(_this){ 
		$("#referral_modal").modal('show');
		fill_form_data($(_this).data('appt-id'));
		bind_datepicker();
	}
	
	function fill_form_data(appt_id){
		var d = result_data[appt_id];
		var v = {}, h = {}, i = {};
		
		h['appt_id'] = appt_id;
		h['patient_id'] = d.patient_id;
		h['pt_pcp_id'] = d.pt_pcp_id;
		h['pt_ref_id'] = d.pt_ref_id;
		h['reff_id'] = d.reff_id;
		h['auth_id'] = d.auth_id;
		h['ins_data_id'] = d.ins_data_id;
		h['ins_case_id'] = d.ins_case_id;
		h['ins_prov_id'] = d.pri_ins_prov_id;
		
		i['pri_ins_prov'] = d.pri_ins_prov;
		i['pt_name_id'] = d.pt_name_id;
		i['appt_date_time'] = d.appt_date_time;
		i['physician'] = d.physician;
		
		v['pri_proc'] = d.procedureid;
		v['pcp_phy'] = d.pcp_phy;
		v['pcp_phone'] = d.pcp_phone;
		v['ref_phy'] = d.ref_phy;
		v['ref_phone'] = d.ref_phone;
		
		v['refferal_no'] = d.refferal_no;
		v['no_of_reffs'] = d.no_of_reffs;
		v['reff_effective_date'] = d.reff_effective_date;
		v['reff_notes'] = d.reff_notes;
		
		v['auth_name'] = d.auth_name;
		v['auth_date'] = d.auth_date;
			
		$.each(v,function(i,v){ $("#"+i).val(v); });
		$.each(h,function(i,v){ $("input[type=hidden]#"+i).val(v); });
		$.each(i,function(i,v){ $("span#"+i).html(v); });
		
	}
	
	//load listing
	function load_listing(type,so,currLink) {
		if( typeof type === 'undefined') type = '';
		if(typeof(so)=='undefined') so = '';
		if(typeof(currLink)=='undefined') currLink = '';
		
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Loading Referral Data...');

		oso		= $('#ord_by_field').val(); //old_so
		soAD	= $('#ord_by_ascdesc').val();
		if(so == ''){
			so = $('#ord_by_field').val();
		}else{
			$('#ord_by_field').val(so);
			if(oso==so){
				if(soAD=='ASC') soAD = 'DESC';
				else  soAD = 'ASC';
			}else{
				soAD = 'ASC';
			}
			$('#ord_by_ascdesc').val(soAD);
		};

		$('.link_cursor span').removeAttr('class');
		var obj = currLink ? $(currLink) : (so?$("th#"+so):'');
		if( obj )
		{
			if(soAD=='ASC')	obj.find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
			else obj.find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
		}

		var so_url= so ? '?so='+so+'&soAD='+soAD : '';
		if( !type ) {
			var d = $("#dropx").val();
			if(d == 'reschedule' )	type = 'reschedule';
		}
		var data = {}; 
		if( type) { data['appt_type'] = type; }
		$.get(top.JS_WEB_ROOT_PATH+'/interface/scheduler/referral/ajax.php'+so_url, data)
					.done(function(r){ after_load(r); top.show_loading_image('hide'); });	
	}
	
	var u = '';
	var get_phy_phone = function (_this,f,u){
		var data = {};
		data['action'] = 'ref_phone';
		data['phy_id'] = parseInt($("#"+f).val());
		$.post(top.JS_WEB_ROOT_PATH+'/interface/scheduler/referral/ajax.php',data)
			.done(function(r){ r = $.parseJSON(r);
				$("#"+u).val(r.phone);
			});;
	}
	
	function after_load(r) {
		r = $.parseJSON(r);var html = '';result_data = r;
		var e = false;
		$.each(r,function(i,x){ e = true;
			var icon_css = x.reff_notes ? 'style="padding:7px 5px;background-color:#158d83; display:block; color:white; border-radius:5px; "' : '';
				html += '<tr>';
				html += '<td>'+x.pt_name_id+'</td>';
				html += '<td>'+x.pri_ins_prov+'</td>';
				html += '<td>'+x.proc_type+'</td>';
				html += '<td>'+x.appt_date_time+'</td>';
				html += '<td>'+x.physician+'</td>';
				html += '<td>'+x.pcp_phy+'</td>';
				html += '<td>'+x.pcp_phone+'</td>';
				html += '<td>'+x.ref_phy+'</td>';
				html += '<td>'+x.ref_phone+'</td>';
				html += '<td>'+x.refferal_no+'</td>';
				html += '<td>'+x.no_of_reffs+'</td>';
				html += '<td>'+x.reff_effective_date+'</td>';
				html += '<td>'+x.auth_name+'</td>';
				html += '<td>'+x.auth_date+'</td>';
				html += '<td><span '+icon_css+'><span style="background-color:white;display:block"><img src="'+top.JS_WEB_ROOT_PATH+'/library/images/editrec.png" style="min-width:25px!important;" class="pointer" title="Edit" data-appt-id="'+i+'" onClick="edit(this);" /></span></span></td>';
				html += '</tr>';
		});
		
		if(!e) {
			html = '<tr><td colspan="15" class="text-center bg-warning f-bold">No record found</td></tr>'
		}
		
		$("tbody#results").html(html);
	};
		
	function load_raw_data(){
		return $.get(top.JS_WEB_ROOT_PATH+'/interface/scheduler/referral/ajax.php?action=raw_data');	
	}
	
	//Setting table height
	function set_container_height(){
		var header_position = $('#first_toolbar',top.document).position();
		var window_height = parseInt(window.innerHeight - $('footer',top.document).outerHeight());

		//If pagination is there on the page i.e --> .pgn_prnt class
		if($('.pgn_prnt:first').length){
			window_height = window_height - $('.pgn_prnt').outerHeight();
			$('.pgn_prnt').css('overflow-x','hidden');
		}else{
			window_height = parseInt(window_height + 30);
		}

		$('.whtbox').css({
			'height':window_height,
			'max-height':window_height,
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
	} 
	
	$(document).ready(function(){
		set_container_height();
		top.set_header_title('Referral - Work List');
		load_listing();
		top.show_loading_image('hide');
		load_raw_data().done(function(r){
			r = $.parseJSON(r);
			raw_data = r;
			//$('[id=pri_ins_prov]').typeahead({source:r.typeahead,items:8,scrollBar:true});
			$("#pri_proc").html('<option value="" selected>--Reason--</option>'+r.slot_proc);
		});
		
		
	});
	
	$(window).load(function(){
		setTimeout(function(){
			$(top.document).contents().find('#first_toolbar').addClass('Admin').show();
		}, 1000);
	});
		
	$(window).resize(function(){ set_container_height();});
	
</script>
</body>
</html>