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

require_once("../../admin_header.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
require_once('../../../../library/classes/cls_common_function.php');
$OBJCommonFunction = new CLSCommonFunction;

$sys_temp_qry = "select id,schedule_name from schedule_templates WHERE parent_id = 0 and template_type = 'SYSTEM' order by schedule_name";
$sys_temp_qry_obj = imw_query($sys_temp_qry);	
$systemp_data = "";
while($sch_sys_val = imw_fetch_assoc($sys_temp_qry_obj))
{
	$id = $sch_sys_val['id'];
	$schedule_name = $sch_sys_val['schedule_name'];						
	$systemp_data .= '<option class="sch_sys_tmp" value="'.$id.'<>'.$schedule_name.'" >'.$schedule_name.'</option>';
}	


if($theyear){
	if($themonth==2 && date('d')>28){
		if($theyear%4==0){		
			$date =getdate(mktime(0,0,0,$themonth,29,$theyear));
			$thedate=29;
		}else{
			$thedate=28;
			$date =getdate(mktime(0,0,0,$themonth,28,$theyear));
		}
	}else{
		$tmp = date("d");
		if(($themonth=="4"||$themonth=="6"||$themonth=="9"||$themonth=="11")&&$tmp=="31"){
			$tmp = "30";
		}
		$date =getdate(mktime(0,0,0,$themonth,$tmp,$theyear));
	}
}
else{
	$date = getdate();
}
//pre($date);echo' -------------';
$eff_day=date("D");
$cur_date=date("j");
$month_number=$date["mon"]; // get month number
$year=$date["year"];        // get year
$month_name=$date["month"];
$df_pro = $sel_pro_month;
if(!$thedate) $thedate =  date('d');
$date_format_SET='m-d-Y';
$cur_date = date($date_format_SET,mktime(0,0,0,$date["mon"],$thedate,$date["year"]));
$get_cur_date = date($date_format_SET,mktime(0,0,0,$month_number,$thedate,$year));

$qry = "select * from facility order by name";
$sql_qry = imw_query($qry);
$fac_res = fetchArray($sql_qry);
$fac_option = "<option value=''>".imw_msg('drop_sel')."</option>";
for($i=0;$i<count($fac_res);$i++){
	$id = $fac_res[$i]['id'];
	$name = $fac_res[$i]['name'];						
	//$fac_sel = $id == $sel_facility ? 'selected' : '';
	$fac_option .= '<option value="'.$id.'" '.$fac_sel.'>'.$name.'</option>';
}
?>
<style>
	.uspos .icon {
    display: inline-block;
    background-color: #E6E5E5;
    border-radius: 3px;
    border-width: 1px 1px 2px;
    border-style: solid;
    border-color: rgb(216, 216, 216);
    border-image: initial;
    border-bottom: 2px solid rgb(216, 216, 216);
    padding: 2px 1px !important;
    margin: 0px !important;border-radius: 5px;
}
	.uspos .icon img{width: 32px}
div.jquery-drag-to-select {
	background: #def;
	display: none;
	opacity: .3;
	filter: alpha(opacity=30);
	z-index: 10;
	border: 1px solid #369;
}

div.jquery-drag-to-select.active {
	display: block;
}
.disabled{
background: #FFFFFF;
}
.selected{
background:#c0c0c0;
}

.classRed{
background-color:#FF0000;
color:#0000FF;
}
a.del_prov_sch 
{
	color:#fff;
	background-color:#F00;
	padding:3px;
	font-size:11px;
}

</style>
<script type="text/javascript">
	var sch_sys_temp = '<?php echo $systemp_data; ?>';
	var temp_start_time=temp_end_time=pro_sch_tmp_id="";
	
	function show_hd_em_temp()
	{
		show_em_temp_status = $('#show_em_temp').is(':checked');	
		if(show_em_temp_status == true)
		{
			$('#sel_schedule_name').append(sch_sys_temp);
		}
		else
		{
			$('.sch_sys_tmp').remove();	
		}
	}
	
	function y2k(number)
	{
		return (number < 1000)? number+1900 : number;
	}
	var today = new Date();
	var day = today.getDate();
	var month = today.getMonth()
	var year = y2k(today.getYear());
	
	function restart(obj)
	{
		document.getElementById(obj).value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
		mywindow.close();
	}
	function padout(number)
	{
		return (number < 10) ? '0' + number : number;
	}


	function schedule_label(obj){
		
		EnableDisable(0);
		document.getElementById("new_sch").checked = false;
		document.getElementById("template_label_tr").style.display = "none";
		if(obj.value!=""){		
		var val_arr = obj.value.split('<>');		
		document.getElementById("template_label").value = val_arr[1];
		}else{
		
			document.getElementById("template_label").value="";
		}
	}
	
	function schedule_childTemp(valueStr)
	{
		var val_arr = valueStr.split('<>');	
		$.ajax({
			url : 'schedule_child_templates.php?sch_tmp_id='+val_arr[0],
			type : 'POST',
			success : function(resultObj)
			{
				//resultData = resultObj.responseText;
				$('#sel_child_schedule_name').html(resultObj);
				schedule_TemplateTime(valueStr);	
				$(".selectpicker").selectpicker('refresh');								
			}
		});	
	}
		
	function schedule_TemplateTime(valueStr){
		if(valueStr!=""){		
			var val_arr = valueStr.split('<>');		
			var url = 'schedule_template_time.php?sch_tmp_id='+val_arr[0];
			$.ajax({
				url:url,
				type:'GET',
				success:function(response){	
					var returnVal = response;
					var allInfo=returnVal.split("---");
					var mstartTime=allInfo[0];
					var mEndTime=allInfo[1];
					var sel_sch=allInfo[2];
					var morTime = mstartTime.split(':');
					var eveTime = mEndTime.split(':');
					var sel_sch1 = sel_sch.split(',');
					
					//save start and end time for iportal popup
					temp_start_time=mstartTime;
					temp_end_time=mEndTime;
					if(morTime[0]>12){
						morTime[0] = morTime[0] -12;
						if(morTime[0] < 10)
							morTime[0] = '0'+morTime[0];
					}
					if(morTime[0] == 0)
						morTime[0] = 12;
					if(eveTime[0]>12){
						eveTime[0] = eveTime[0] -12;
						if(eveTime[0] < 10)
							eveTime[0] = '0'+eveTime[0];
					}
					if(eveTime[0] == 0)
						eveTime[0] = 12;
					$("#start_hour").val(morTime[0]);
					$("#start_min").val(morTime[1]);
					$("#end_hour").val(eveTime[0]);
					$("#end_min").val(eveTime[1]);
					$("#start_time").val(sel_sch1[0]);
					$("#end_time").val(sel_sch1[1]);
					$(".selectpicker").selectpicker('refresh');	
				}
			});
		}
	}	

	function setEnableDisable(objflag){
		if(objflag.checked){
			EnableDisable(1);
			if($('#template_label_tr').hasClass('hide')){
				$('#template_label_tr').removeClass('hide');
				$('#template_label_tr').addClass('show');
			}
		}else{
			EnableDisable(0);
			if($('#template_label_tr').hasClass('show')){
				$('#template_label_tr').removeClass('show');
				$('#template_label_tr').addClass('hide');
			}
		}		
	}
	function EnableDisable(flag){

		if(flag==0){
				$("#start_hour").prop('disabled', true);
				$("#start_min").prop('disabled', true);
				$("#end_hour").prop('disabled', true);
				$("#end_min").prop('disabled', true);
				$("#start_time").prop('disabled', true);
				$("#end_time").prop('disabled', true);
				
		}else{
				$("#start_hour").prop('disabled', false);
				$("#start_min").prop('disabled', false);
				$("#end_hour").prop('disabled', false);
				$("#end_min").prop('disabled', false);
				$("#start_time").prop('disabled', false);
				$("#end_time").prop('disabled', false);
		}	
		
		$(".selectpicker").selectpicker('refresh');

	}	
	
	function sel_sch(id, dated, total_appts, today_appts)
	{
		//reset all form fields
		formReset();
		
		$("#total_appts").val(total_appts);
		$("#today_appts").val(today_appts);
		$("#dated").val(dated);
		$("#tmp_record_id").val(id);
		
		var url = 'ajax.php?id='+id+"&dated="+dated;
		$.ajax({
			url:url,
			type:'GET',
			success:function(response){		
				var arr=response.split('~:~');
				var today_date=arr[0];
				var temp_expiry_date=arr[1];
				var provider=arr[2];
				var facility=arr[3];
				var parent_temp=arr[4];
				parent_temp=parent_temp.replace("'","");
				var child_temp=arr[5];
				var start_date=arr[6];
				var end_date=arr[7];
				var morning_start_time=arr[8];
				var morning_end_time=arr[9];
				var date_status=arr[10];
				//save start and end time for iportal popup
				temp_start_time=morning_start_time;
				temp_end_time=morning_end_time;
				pro_sch_tmp_id=id;
				
				var morTime = morning_start_time.split(':');
				var eveTime = morning_end_time.split(':');
				var sel_sch1 = date_status.split(',');
				
				
				if(morTime[0]>12){
					morTime[0] = morTime[0] -12;
					if(morTime[0] < 10)
						morTime[0] = '0'+morTime[0];
				}
				if(morTime[0] == 0)
					morTime[0] = 12;
				if(eveTime[0]>12){
					eveTime[0] = eveTime[0] -12;
					if(eveTime[0] < 10)
						eveTime[0] = '0'+eveTime[0];
				}
				if(eveTime[0] == 0)
				{eveTime[0] = 12;}

				var parent_temp_arr = parent_temp.split('<>');		
				//$("#sch_app_id").val(id);//disabling it to stop updation

				//$("#Start_date").val(today_date);
				if($.trim(temp_expiry_date) != "")
				{
					sel_temp_date_arr = temp_expiry_date.split('-');	
					result_expiry_dt = sel_temp_date_arr[1]+'-'+sel_temp_date_arr[2]+'-'+sel_temp_date_arr[0];
					$('#tmp_expiry_dt').val(result_expiry_dt);
				}

				$("#sel_facility").val(facility);
				$("#existing_fac").val(facility);

				$("#sel_schedule_name").val(parent_temp);
				if($("#sel_schedule_name option[value='"+parent_temp+"']").length==0 && parent_temp_arr[0] && parent_temp_arr[1]){
					$("#sel_schedule_name").append("<option selected value='"+parent_temp+"'>"+parent_temp_arr[1]+"</option>");
				}
				//enable previousely disabled options
				 $("#sel_schedule_name_replace option:selected").attr('disabled','disabled').siblings().removeAttr('disabled');
				//disable selected option to be changed
				$('#sel_schedule_name_replace').children('option[value="' + parent_temp + '"]').attr('disabled', true)

				if($.trim(child_temp) != "")
				{
					var child_temp_opts = decodeURIComponent(child_temp.replace(/\+/g, " "));
					var child_temp_arr = child_temp_opts.split('<>');	
					$("#sel_child_schedule_name").append("<option selected value='"+child_temp_opts+"'>"+child_temp_arr[1]+"</option>");
					$("#child_temp_id").val(child_temp_arr[0]);
					//enable child template delete button
					$("#remove_child").prop('disabled',false);
				}

				$("#child_from").val(start_date);
				$("#child_to").val(end_date);

				$("#start_hour").val(morTime[0]);
				$("#start_min").val(morTime[1]);
				$("#start_time").val(sel_sch1[0]);

				$("#end_hour").val(eveTime[0]);
				$("#end_min").val(eveTime[1]);
				$("#end_time").val(sel_sch1[1]);

				$("#template_label").val(parent_temp_arr[1]);


				$(".selectpicker").selectpicker('refresh');
			}
		});
		formDisable();
	}
	
	function formReset()
	{
		$("#tmp_expiry_dt").val('');
		$("#sel_facility").val('');
		$("#existing_fac").val('');
		$("#sel_schedule_name").val('');
		$("#sel_child_schedule_name").val('');
		$("#child_from").val('');
		$("#child_to").val('');

		$("#start_hour").val('');
		$("#start_min").val('');
		$("#start_time").val('');

		$("#end_hour").val('');
		$("#end_min").val('');
		$("#end_time").val('');
		
		//enable fields
		formEnable();
		$("#delete").prop('disabled',true);
		$("#remove_child").prop('disabled',true);
		
		$(".selectpicker").selectpicker('refresh');
	}
	function formEnable()
	{
		$("#tmp_expiry_dt").prop('disabled',false);
		$("#sel_facility").prop('disabled',false);
		$("#sel_schedule_name").prop('disabled',false);
		$("#show_em_temp").prop('disabled',false);
		$("#new_sch").prop('disabled',false);
		$("#sel_child_schedule_name").prop('disabled',false);
		$("#child_from").prop('disabled',false);
		$("#child_to").prop('disabled',false);

		$("#start_hour").prop('disabled',false);
		$("#start_min").prop('disabled',false);
		$("#start_time").prop('disabled',false);

		$("#end_hour").prop('disabled',false);
		$("#end_min").prop('disabled',false);
		$("#end_time").prop('disabled',false);
		
		$("#Submit").prop('disabled',false);
		$(".selectpicker").selectpicker('refresh');
	}
	function formDisable()
	{
		$("#tmp_expiry_dt").prop('disabled',true);
		$("#sel_facility").prop('disabled',true);
		$("#sel_schedule_name").prop('disabled',true);
		$("#show_em_temp").prop('disabled',true);
		$("#new_sch").prop('disabled',true);
		$("#sel_child_schedule_name").prop('disabled',true);
		$("#child_from").prop('disabled',true);
		$("#child_to").prop('disabled',true);

		$("#start_hour").prop('disabled',true);
		$("#start_min").prop('disabled',true);
		$("#start_time").prop('disabled',true);

		$("#end_hour").prop('disabled',true);
		$("#end_min").prop('disabled',true);
		$("#end_time").prop('disabled',true);
		
		$("#Submit").prop('disabled',true);
		$("#delete").prop('disabled',false);
		$(".selectpicker").selectpicker('refresh');
	}
	function sel_sch2(facId,schTmpId,mstartTime,mEndTime,schAppId,curDate,sel_sch,child_temp_opts_data,sel_temp_date,pro_tmp_id){
		schTmpId=schTmpId.replace("'","");
		if($.trim(child_temp_opts_data) != "")
		{
			child_temp_opts = decodeURIComponent(child_temp_opts_data.replace(/\+/g, " "));
			$('#sel_child_schedule_name').html(child_temp_opts);
		}
		//save start and end time for iportal popup
		temp_start_time=mstartTime;
		temp_end_time=mEndTime;
		pro_sch_tmp_id=pro_tmp_id;
		var morTime = mstartTime.split(':');
		var eveTime = mEndTime.split(':');
		var sel_sch1 = sel_sch.split(',');
		if(morTime[0]>12){
			morTime[0] = morTime[0] -12;
			if(morTime[0] < 10)
				morTime[0] = '0'+morTime[0];
		}
		if(morTime[0] == 0)
			morTime[0] = 12;
		if(eveTime[0]>12){
			eveTime[0] = eveTime[0] -12;
			if(eveTime[0] < 10)
				eveTime[0] = '0'+eveTime[0];
		}
		if(eveTime[0] == 0)
			eveTime[0] = 12;
		var facIdarr = schTmpId.split('<>');		
		document.getElementById("sch_app_id").value = schAppId;
		document.getElementById("sel_facility").value = facId;
		document.getElementById("existing_fac").value = facId;
		document.getElementById("sel_schedule_name").value = schTmpId;
		if($("#sel_schedule_name option[value='"+schTmpId+"']").length==0 && facIdarr[0] && facIdarr[1]){
			$("#sel_schedule_name").append("<option selected value='"+schTmpId+"'>"+facIdarr[1]+"</option>");
		}
		document.getElementById("start_hour").value = morTime[0];
		document.getElementById("start_min").value = morTime[1];
		document.getElementById("end_hour").value = eveTime[0];
		document.getElementById("end_min").value = eveTime[1];
		document.getElementById("template_label").value = facIdarr[1];
		document.getElementById("Start_date").value = curDate;
		document.getElementById("start_time").value = sel_sch1[0];
		document.getElementById("end_time").value = sel_sch1[1];
		if($.trim(sel_temp_date) != "")
		{
			sel_temp_date_arr = sel_temp_date.split('-');	
			result_expiry_dt = sel_temp_date_arr[1]+'-'+sel_temp_date_arr[2]+'-'+sel_temp_date_arr[0];
			document.getElementById('tmp_expiry_dt').value = result_expiry_dt;
		}
		$(".selectpicker").selectpicker('refresh');
	}
	
	function save_schedule(){
		var msg = '';
		var sel_pro = document.getElementById("sel_pro").value;
		var sel_facility = document.getElementById("sel_facility").value;
		var sel_schedule_name = document.getElementById("sel_schedule_name").value;
		var sel_child_schedule_name = document.getElementById("sel_child_schedule_name").value;
		var sel_child_from = document.getElementById("child_from").value;
		var sel_child_to = document.getElementById("child_to").value;
		var Start_date = document.getElementById("Start_date").value;
		var app_id = document.getElementById("sch_app_id").value;
		var cur_week = document.getElementById("cur_week").value;
		var Start_date_String="";
		var new_template_name = document.getElementById("template_label").value;

		if(document.getElementById("Start_date_String").value!=""){
			Start_date_String=document.getElementById("Start_date_String").value;
		}
		//if($.trim(sel_child_schedule_name) != "")
		//{
		//	sel_schedule_name = sel_child_schedule_name;	
		//}
		if(sel_child_schedule_name)
		{
			if(!sel_child_from || !sel_child_to)
			msg = 'Please Enter <strong>From</strong> and <strong>To</strong> date for Child Template.<br>';
		}
		if(sel_pro == ''){
			msg = 'Please Select Provider.<br>';
		}
		if(sel_facility == ''){
			msg += 'Please Select Facility.<br>';
		}
		if(sel_schedule_name == '' && document.getElementById("new_sch").checked == false){
			msg += 'Please Select Schedule template.<br>';
		}
		if(Start_date == ''){
			msg += 'Please Select Provider Schedule Date.<br>';
		}
		if(document.getElementById("new_sch").checked == true){
			if(new_template_name == ""){
				msg += 'Please Enter Label for New Template.<br>';
			}
		}
		if(msg){
			top.fAlert(msg);
		}
		else{
			var wrdata_params = $('#wrdata').val();
			var last_day_t = $('#last_day_t').val();
			var url = 'save_provider_schedule.php?sel_pro='+sel_pro;
			url += '&sel_facility='+sel_facility+'&sel_schedule_name='+sel_schedule_name+'&sel_child_schedule_name='+sel_child_schedule_name+'&last_day_t='+last_day_t+'&wrdata_params='+wrdata_params;
			url += '&child_from='+sel_child_from+'&child_to='+sel_child_to;
			if(document.getElementById("new_sch").checked == true){
				url += '&Start_date='+Start_date+'&app_id='+app_id+'&cur_week='+cur_week+"&Start_date_String="+Start_date_String+"&Template_Label="+new_template_name+"&newtemp=y";
			}else{
				url += '&Start_date='+Start_date+'&app_id='+app_id+'&cur_week='+cur_week+"&Start_date_String="+Start_date_String+"&Template_Label="+new_template_name+"&newtemp=n";
			}
			$.ajax({
				url:url,
				type:'GET',
				success:function(response){
					provider_status(response)
				}
			});
		}
	}
	
	function displayConfirmYesNo(title,msg,btn1,btn2,func,showCancel,showImage,misc)
	{
		text = '<div id="divConFuture" style="position:relative; z-index:1000;">';		  		 
		text += '<table align="center" width="200" border=0 cellpadding=2 cellspacing=0 class="confirmTable" style="position:absolute;top:0px;left:0px;z-index:10;">';
		text += '<tr><td class="confirmTitle" colspan="2" >';		  		  
		text += title;
		text += '</td></tr>';
		text += '<tr>';
		text += '<td class="confirmBackground" valign=\"top\">';
		if((typeof showImage == "undefined") || (showImage != 0))
		{
			text += '<img src="../../../../library/images/confirmYesNo.gif" alt="Confirm">';
		}
		text += '</td><td class="confirmBackground">';
		text += msg;
		text += '</td></tr>';
		text += '<tr><td class="confirmBackground" colspan="2"><center>';
		text += '<input type="button" value="'+btn1+'" onClick="window.'+func+'(1)" class="confirmButton btn btn-default">';
		text += '<input type="button" value="'+btn2+'" onClick="window.'+func+'(0)" class="confirmButton btn btn-default">';
		if((typeof misc != "undefined") && (misc == "Undo"))
		{
			text += '<input type="button" value="UndoChanges" onClick="window.'+func+'(2)" class="confirmButton btn btn-default">';
		}
		if((typeof showCancel == "undefined") || (showCancel != 0))
		{
			text += '<input type="button" value="Cancel" onClick="window.'+func+'(-1)" class="confirmButton btn btn-default">';		  		  
		}
		text += '</center></td></tr></table>';		 
		text += '</div>';
		if (document.getElementById) 
		{
			mDiv = document.getElementById('msgDiv');
			mDiv.innerHTML = text;
			mDiv.style.visibility = 'visible';
		}	
	}
	
	function getConfirmation(val)
	{
		hideConfirmYesNoLocal();
		document.getElementById("exception").value = val;
		document.frm_dump_month.action = 'provider_sch_save.php';
		document.frm_dump_month.submit();
	}
	
	function hideConfirmYesNoLocal()
	{
		var mDiv = document.getElementById('msgDiv');
		mDiv.style.visibility = 'hidden';
	}
	
	function delete_me(total_appt,today_appt,pro_tmp_id)
	{		
		//enable form to submit it
		formEnable();
		if(total_appt && typeof(total_appt)!='undefined'){
			var total=total_appt;
		}else{
			var total=$("#total_appts").val();
		}
		
		if(total_appt && typeof(total_appt)!='undefined'){
			var today=today_appt;
		}else{
			var today=$("#today_appts").val();
		}
		if(pro_tmp_id && typeof(pro_tmp_id)!='undefined'){
			$("#pro_sch_tmp").val(pro_tmp_id);
		}else{
			$("#pro_sch_tmp").val($("#tmp_record_id").val());
		}
		var sel_pro = $("#sel_pro").value;
		var fac_id = $("#sel_facility").value;
		var sch_id = $("#sel_schedule_name").value;
		
		if(sel_pro=="")
		{
			top.fAlert("Please select provider.");
			return false;
		}
		else if(fac_id=="")
		{
			top.fAlert("Please select facility.");
			return false;			
		}
		else if(sch_id=="")
		{
			top.fAlert("Please select schedule.");
			return false;			
		}else{			
			top.fancyConfirm('Remove from future months!','','window.top.fmain.askForAppts(1,'+total+')','window.top.fmain.askForAppts(0,'+today+')');
	
		}
	}
	
	function askForAppts(all, appts)
	{
		var sel_dt_ln=$('[name="dateSelect[]"]:checked').length
		if(sel_dt_ln<=1){
		var msgs="All the matching appointments ("+appts+" approx) will be sent to re-schedule list.<br>Delete provider schedule ?";
		}else{var msgs="All the matching appointments will be sent to re-schedule list.<br>Delete provider schedule ?";}
		if(all==1)
		{
			top.fancyConfirm(msgs,'','window.top.fmain.getConfirmation2(1)');//cal fucntion with param delete all
		}
		else{
			top.fancyConfirm(msgs,'','window.top.fmain.getConfirmation2(0)');//cal fucntion with param delete target date only
		}
	}
	
	/*function del_from_future_months_or_no()
	{		
		top.fancyConfirm('Remove from future months!','','window.top.fmain.getConfirmation2(1)','window.top.fmain.getConfirmation2(0)');	
	}*/
	
	function getConfirmation2(val)
	{
		hideConfirmYesNoLocal();
		top.show_loading_image('show');
		document.getElementById("exception").value = val;
		document.frm_dump_month.action = 'provider_sch_delete.php';
		document.frm_dump_month.submit();
	}
	
	function blank_function(){
		return false;
	}
	
	
	function save_app(){
		var msg = '';
		var sel_pro = document.getElementById("sel_pro").value;
		var sel_facility = document.getElementById("sel_facility1").value;
		var sel_schedule_name = document.getElementById("sel_schedule_name1").value;
		var Start_date = document.getElementById("get_date").value;
		var app_id = document.getElementById("sch_app_id").value;
		var cur_week = document.getElementById("cur_week").value;
		document.getElementById("sel_facility").value = sel_facility;
		document.getElementById("sel_schedule_name").value = sel_schedule_name;
		
		if(sel_pro == ''){
			msg = 'Please Select Provider.\n';
		}
		if(sel_facility == ''){
			msg += 'Please Select Facility.\n';
		}
		if(sel_schedule_name == ''){
			msg += 'Please Select Schedule template.\n';
		}
		if(Start_date == ''){
			msg += 'Please Select Provider Schedule Date.\n';
		}
		if(msg){
			top.fAlert(msg);
		}
		else{
			var url = 'save_provider_schedule.php?sel_pro='+sel_pro;
			url += '&sel_facility='+sel_facility+'&sel_schedule_name='+sel_schedule_name;
			url += '&Start_date='+Start_date+'&app_id='+app_id+'&cur_week='+cur_week;
			$.ajax({
				url:url,
				type:'GET',
				success:function(response){
					provider_status(response)
				}
			});	
		}
	}

function setValuesForForm(startTimestr,endTimestr){
var mstartTime=trim(startTimestr);
var mEndTime=trim(endTimestr);
var morTime = mstartTime.split(':');
var eveTime = mEndTime.split(':');
var sel_schE = endTimestr.substr(6,8);
var sel_schS = mstartTime.substr(6,8);

morTime[0]=trim(morTime[0]);
eveTime[0]=trim(eveTime[0]);
document.getElementById("start_hour").value =(morTime[0]=="00")?12:morTime[0];
document.getElementById("start_min").value = (parseInt(morTime[1])>0)?parseInt(morTime[1]):"00";
document.getElementById("end_hour").value = (eveTime[0]=="00")?12:eveTime[0];
document.getElementById("end_min").value =(parseInt(eveTime[1])>0)?parseInt(eveTime[1]):"00";
document.getElementById("start_time").value = trim(sel_schS);
document.getElementById("end_time").value = trim(sel_schE);
}

function replace_template_change()
{
	$("#time_tmp_data_replace").html("Loading");
	//$("#child_tmp_data_replace").html("Loading");
	var cur_tmp_id_val = $("#sel_schedule_name_replace").val();
	$.ajax({
		url: 'schedule_child_templates.php?sch_tmp_id='+cur_tmp_id_val+'&show_tmp_timings=1',
		type:'GET',
		complete: function(resp_data)
		{
			var result_data = resp_data.responseText;
			var result_data_arr = result_data.split("~~||~~");
			if(result_data_arr[0] != "")
			{
				$("#child_template_replace").html(result_data_arr[0]);
			}
			/*else
			{
				$("#child_tmp_data_replace").html("No child template");				
			}*/
			
			$("#time_tmp_data_replace").html(result_data_arr[1]);	
			$(".selectpicker").selectpicker('refresh');	
		}
	});	
}

function get_timing_child_template(ths)
{
	var cur_tmp_id_val = $(ths).val();
	if(cur_tmp_id_val == "")
	{
		replace_template_change();	
	}
	else
	{
		var cur_tmp_id_val_arr = cur_tmp_id_val.split('<>');
		var child_temp_id_replace = cur_tmp_id_val_arr[0];
		$("#time_tmp_data_replace").html("Loading");
		$.ajax({
			url : 'get_tmp_timings.php?sch_tmp_id='+child_temp_id_replace,
			complete: function(respData)
			{
				var resultData = respData.responseText;
				$("#time_tmp_data_replace").html(resultData);	
			}
		});		
	}	
}

function show_replace_cnt()
{
	$("#replace_cnt").modal('show');	
}

function printTemplate(id)
{
	var content=$("#"+id).html();
	if(content)
	{	
		$("#content").val(content);
		$("#print_helper").submit();
	}
}

function set_replace_temp_id_frm()
{
	formEnable();
	
	var action = $("input:radio[name ='replace']:checked").val();
	if(action=='template')
	{
		var from_date=$("#child_from_replace").val();
		var to_date=$("#child_to_replace").val();
		var child_temp = $("#child_template_replace").val();

		if(child_temp.length)
		{
			if(!from_date || !to_date)
			{
				msg = 'Please Enter <strong>From</strong> and <strong>To</strong> date for Child Template.<br>';
				top.fAlert(msg);
				return false;
			}
		}

		var parent_temp = 	$("#sel_schedule_name_replace").val();

		//assign values to hidden fields
		$("#replace_pid").val(parent_temp);
		$("#replace_cid").val(child_temp);
		if(child_temp.length)
		{
			$("#replace_c_from").val(from_date);
			$("#replace_c_to").val(to_date);
		}

		//$("#replace_temp_id").val(replace_temp_val);
		$("#replace_cnt").modal("hide");	

		var tmp_expiry_dt = document.getElementById("tmp_expiry_dt").value;
		if($.trim(tmp_expiry_dt)!="")
		{
			top.fmain.getConfirmation(1);
		}
		else
		{
			top.fancyConfirm('Apply to future months!','','window.top.fmain.getConfirmation(1)','window.top.fmain.getConfirmation(0)');
		}
	}else{
		var new_fac = $("#new_fac").val();
		var appt_act = $("input:radio[name ='appt_act']:checked").val();
		//keep_appt, move_appt
		$("#replace_action").val(action);
		$("#replace_facility").val(new_fac);
		$("#replace_appt_act").val(appt_act)
				
		top.fancyConfirm('Apply to future months!','','window.top.fmain.getConfirmation(1)','window.top.fmain.getConfirmation(0)');
	}
}

function provider_status(response){
		top.show_loading_image('hide');			
		var returnVal = response.split('__');
		if(returnVal[1]){
			top.fAlert("Cannot repeat the same schedule.");
		}else{
			var themonth = document.getElementById('themonth').value;
			var theyear = document.getElementById('theyear').value;
			document.getElementById('week').value = returnVal[0];
			var tmp_expiry_dt = document.getElementById("tmp_expiry_dt").value;
			if($.trim(tmp_expiry_dt)!="")
			{
				top.fmain.getConfirmation(1);
			}
			else
			{
				top.fancyConfirm('Apply to future months!','','window.top.fmain.getConfirmation(1)','window.top.fmain.getConfirmation(0)');
			}
		}
		return false;
	}

function win_opr_slots(){
	if(temp_start_time && temp_end_time){
		var pro_sch_id=pro_sch_tmp_id;
		window.open("slots.php?pro_sch_id="+pro_sch_id+"&start_time="+temp_start_time+"&end_time="+temp_end_time,'Schedule_Template_Slots','height=510,width=300,top=200,left=150');	
	}else{top.fAlert("Please Select Schedule Template");}
}
</script>
<form name="print_helper" id="print_helper" action="print_template.php" method="post" target="print_frame">
	<input type="hidden" name="content" id="content" value="">
</form>
<iframe name="print_frame" id="print_frame" src="" frameborder="0" allowtransparency="1" height="0" width="0"></iframe> 
	
	<!--modal wrapper class is being used to control modal design-->
	<div class="common_modal_wrapper">
	 <!-- Modal -->
	<div id="replace_cnt" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">
					<div class="radio radio-inline">
						<input type="radio" id="act_replace_template" value="template" name="replace" checked="checked" autocomplete="off" onChange="toggleOption('temp_div')">
						<label for="act_replace_template"> Replace Template </label>
					</div>
					<div class="radio radio-inline">
						<input type="radio" id="act_replace_facility" value="facility" name="replace" autocomplete="off" onChange="toggleOption('fac_div')">
						<label for="act_replace_facility"> Replace Facility </label>
					</div>
					</h4>
				</div>
				<div class="modal-body">
					<div id="temp_div" style="display: block">
					<div class="row">
					<div class="col-sm-4">
						<label>Template</label>	<br>
						<select name='sel_schedule_name_replace' id='sel_schedule_name_replace' class="selectpicker" data-width="100%" data-size="11" onchange="replace_template_change();">
							<option value=''><?php echo imw_msg('drop_sel') ?></option>
							<?php
								$qry = "select * from schedule_templates WHERE parent_id = 0 and template_type != 'SYSTEM' and del_status='' order by schedule_name";
								$sql_qry = imw_query($qry);
								$fac_res = fetchArray($sql_qry);
								$schedule_option = '';
								for($i=0;$i<count($fac_res);$i++){
									$id = $fac_res[$i]['id'];
									$template_type = $fac_res[$i]['template_type'];
									$schedule_name = str_ireplace("'","",$fac_res[$i]['schedule_name']);						
									$schedule_sel = $id.'<>'.$schedule_name == $sel_schedule_name ? 'selected' : '';
									$schedule_option .= '<option value="'.$id.'<>'.$schedule_name.'" '.$schedule_sel.'>'.$schedule_name.'</option>';
								}
								print $schedule_option;
							?>
						</select>
					</div>	
					<div class="col-sm-4">
						<label>Child Template</label><br/>
						<div id="child_tmp_data_replace">
							<select onchange='get_timing_child_template(this);' class='selectpicker' data-width="100%" name="child_template_replace" data-size="11" id='child_template_replace'>
							</select>
						</div>	
					</div>
					<div class="col-sm-4">
						<label>Time</label><br/>
						<div id="time_tmp_data_replace">
							--:--:-- To --:--:--
						</div>	
					</div>	
					</div>
					<div class="row pt10">
					<div class="col-sm-4">
					<label>Child Apply From</label>
					<div class="input-group">
						<input type="text" class="datepicker form-control" id="child_from_replace" name="child_from_replace" value="" onblur="checkdate(this);" autocomplete="off">
						<label class="input-group-addon pointer" for="child_from_replace">
						  <i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
						</label>
					  </div>

					</div>
					<div class="col-sm-4">
					<label>To</label>
					<div class="input-group">
						<input type="text" class="datepicker form-control" id="child_to_replace" name="child_to_replace" value="" onblur="checkdate(this);" autocomplete="off">
						<label class="input-group-addon pointer" for="child_to_replace">
						  <i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
						</label>
					  </div>
					</div>
					</div>
					</div>
					
					<div id="fac_div" style="display: none">
					<div class="row">
					<div class="col-sm-4">
						<label>From Facility</label><br>
						<select name="existing_fac" id="existing_fac" class="selectpicker" data-width="100%" data-size="11" disabled>
						<?php print $fac_option;?>
						</select>
					</div>	
					<div class="col-sm-4">
						<label>To Facility</label><br/>
						<select name="new_fac" id="new_fac" class="selectpicker" data-width="100%" data-size="11">
						<?php print $fac_option;?>
						</select>
					</div>
					<div class="col-sm-4">
						<div class="radio radio-inline">
							<input type="radio" id="move_appt" value="move_appt" name="appt_act" checked="checked" autocomplete="off">
							<label for="move_appt"> Move appointment to rescheduled list </label>
						</div><br>
						<div class="radio radio-inline">
							<input type="radio" id="keep_appt" value="keep_appt" name="appt_act" autocomplete="off">
							<label for="keep_appt"> Move all appointments to new location </label>
						</div>
					</div>
					</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" id="replace_template_act" onClick="set_replace_temp_id_frm();">Replace</buton>
					<button class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	</div>

<!--modal wrapper class is being used to control modal design-->
	<div class="common_modal_wrapper">
	 <!-- Modal -->
	<div id="viewLog" class="modal fade" role="dialog">
		<div class="modal-dialog modal_90">
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Provider Schedule Log</h4>
				</div>
				<div class="modal-body">
					<?php if($sel_pro_month){echo 'Loading...';} else echo 'Please select physician first.';?>
				</div> 
				<div class="modal-footer">
					<button class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	</div>
	
	<div style="position:absolute;top:250px;left:400px;visibility:hidden; z-index:1000;" id="msgDiv"></div>
	
<div class="mainwhtbox">
	<div style="height:<?php echo ($_SESSION["wn_height"] - 314); ?>px; overflow:hidden;">
		<div id="divCommonAlertMsgProvSchTemplate"></div> 
		<form name="frm_dump_month" method="post" action="" id="frm_dump_month" onSubmit="return false;">
			<input type="hidden" id="exception" name="exception" value="" >
			<input type="hidden" id="pro_sch_tmp" name="pro_sch_tmp" value="">
			<input type="hidden" id="total_appts" name="total_appts" value="">
			<input type="hidden" id="today_appts" name="today_appts" value="">
			<input type="hidden" id="tmp_record_id" name="tmp_record_id" value="">
			<input type="hidden" id="child_temp_id" name="child_temp_id" value="">
			<input type="hidden" id="dated" name="dated" value="">
			<!--hidden fields for replace template form-->
			<input type="hidden" name="replace_temp_id" id="replace_temp_id" value="" />
			<input type="hidden" name="replace_pid" id="replace_pid" value="" />
			<input type="hidden" name="replace_cid" id="replace_cid" value="" />
			<input type="hidden" name="replace_c_from" id="replace_c_from" value="" />
			<input type="hidden" name="replace_c_to" id="replace_c_to" value="" />
			<!--hidden fields for replace facility form-->
			<input type="hidden" name="replace_action" id="replace_action" value="" />
			<input type="hidden" name="replace_facility" id="replace_facility" value="" />
			<input type="hidden" name="replace_appt_act" id="replace_appt_act" value="" />

			
			<div class="row">
				<div class="col-sm-6">
					<table class="table table-condensed" id="monthtopimage">
					<tr class="text-center">
						<td class="ptl0 text-left text-nowrap">
							<label>Provider</label>
							<select name="sel_pro_month" id="sel_pro_month" class="selectpicker" data-size="11" onchange="top.show_loading_image('show');see_sel_month()">
								<option value=''>&nbsp;&nbsp;&nbsp;-- All Physician --</option>
								<?php
									echo $OBJCommonFunction->drop_down_providers($sel_pro_month,'1','');
								?>
							</select>
						</td>
						<td class="ptl0 text-left uspos">
							<?php if($sel_pro_month){?>
							<a href="javascript:void(0)" onClick="viewLog('<?php echo $sel_pro_month;?>', 'schedule',0);" class="icon" title="Provider Schedule Log"><img src="../../../../library/images/sc3.png" title="Provider Schedule Log"></a>&nbsp;
							<a href="javascript:void(0)" onClick="viewLog('<?php echo $sel_pro_month;?>','custom_label',0);" class="icon" title="Custom Label Log"><img src="../../../../library/images/label.png" title="Custom Label Log"></a>
							<?php }?>
						</td>
						<td>
						<td>
							<span class="glyphicon glyphicon-backward pointer" onclick="change_date('prev_year');"></span>
						</td>
						<td>
							<span class="glyphicon glyphicon-triangle-left pointer" onClick="change_date('prev_month');"></span>
						</td>
						<td>
							<select name="sel_month_year" id="sel_month_year" class="selectpicker" data-size="11" onchange="top.show_loading_image('show');setMonthValue(this.value); change_dateByDropDown();">
								<?php
									$month_numberFuture=getdate();
									$month_numberStart=$month_numberFuture["mon"];
									$endMonthLimit=$month_numberStart+12;
									$startYear=$month_numberFuture["year"];	
									$onceSelected=false;				
									for($i=$month_numberStart;$i<$endMonthLimit;$i++){

										$futureDate = getdate(mktime(0,0,0,$i,'03',$startYear));//day is not used so static
										$sel="";						
										if((int)$month_number.'-'.$year == (int)$futureDate["mon"]."-".$futureDate["year"] ){
											$sel=' selected = "selected" ';
											$onceSelected=true;
										}
										$month_data .= "<option value='".$futureDate["mon"]."-".$futureDate["year"]."' $sel >".trim(substr($futureDate["month"],0,3))."&nbsp;".$futureDate["year"]."</option>";										
									}

									if($onceSelected == false){
										$month_data .= "<option value='".$month_number."-".$year."'  selected = \"selected\"  >".trim(substr($month_name,0,3))."&nbsp;".$year."</option>";
									}	
									echo  $month_data;											
								?>
							</select><?php 

									//echo $month_name."&nbsp;".$year;
									?> 
						<td>
							<span class="glyphicon glyphicon-triangle-right pointer" onclick="change_date('next_month');"></span>
						</td>
						<td>
							<span class="glyphicon glyphicon-forward pointer" onclick="change_date('next_year');"></span>
						</td>
					</tr>
				</table>
				</div>
				<div class="col-sm-6">
					<div class="proscrht"><div class="row ">
					<div class="col-sm-5">
						<label>&nbsp;Provider Template</label>	
					</div>
					<div class="col-sm-4 text-right" id="get_cur_date">
						<label><?php
							print $get_cur_date;
						?></label>	
					</div>
					<div class="col-sm-3 text-right">
						<input type="button" id="deleteXML" name="deleteXML" value="Refresh Templates" onClick="Generate_TemplateXML();" class="btn btn-info"/>	
					</div>	
				</div></div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<?php $schDivHeight=($_SESSION["wn_height"]-400); ?>
					<div style="height:<?php echo $schDivHeight.'px' ?>; overflow-y:auto;overflow-x:hidden;">
						<?php
						require_once('schedule_calender.php');
						?>
						<div class="clearfix"></div>
						<?php
						require_once('provider_schedule_save.php');
						?>
						<script> EnableDisable(0);</script>
					</div>
				</div>
				<div class="col-sm-6">
					<div id="mainTemplatesDiv">
						<div id="template_div_id" style="height:486px;">
						<?php //------ Get Schedule Templates ------
							require_once('provider_schedule_template2.php');
						?>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>


<!-- XML delete notification block -->
<div id="divgenerateXML_content" class="box_background hide">
	<div class="row">
		<div class="col-sm-12">
			<span class="lead">Please do not hit the BACK, STOP or REFRESH or Any Tab Button until the "<strong>XML file generation completed</strong>" message appears. </span>
		</div>	
		<div id="tdgenerateXML" class="col-sm-12 lead pt10">
		</div>		
	</div>
</div>
	<script>	
		top.show_loading_image('hide');
		hide_submit_button();	
		var XML_delete_content = $('#divgenerateXML_content').html();
		function hide_submit_button(){
			top.document.getElementById("page_buttons").innerHTML="";
		}

		$(document).ready(function()
		{
			$('#tmp_expiry_dt').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
			//$('#tmp_expiry_dt').datetimepicker({dateFormat:"mm-dd-yy"});
		});
		
		///////////XML DELETE/////////////
		function Delete_TemplateXML(){
			top.show_loading_image('show');
			var url = 'deleteXML.php';
			$.ajax({
				url:url,
				type:'GET',
				success:function(){
					setMessageDeleted();
				}
			});
		}

		function setMessageDeleted(){
			top.show_loading_image('hide');
			Generate_TemplateXML('');
			var dateVal="";	
		}
		///////////XML DELETE/////////////

		///////////XML Generate Code dateVal=2010-08-31/////////////
		function Generate_TemplateXML(load_date){
			if(!load_date) load_date = "";
			if(load_date == ""){
				msgs = "Do you want to Refresh Scheduler Templates?";
				top.fancyConfirm(msgs,"",'window.top.fmain.Generate_TemplateXMLNew('+load_date+')');
			}else{
				top.fmain.Generate_TemplateXMLNew(load_date);
			}
		}

		function Generate_TemplateXMLNew(load_date){
		top.show_loading_image('show');
			if(!load_date) load_date = "";
			$.ajax({
				url: "generateXmlFinal.php?sel_date_val="+load_date,
				success: function(resp){
				var dateArr=resp.split('~');
				var modal_open = ($("#divgenerateXML").data('bs.modal') || {}).isShown;
				if(typeof(modal_open) == 'undefined'){
					show_modal('divgenerateXML','XML File Generation Started',XML_delete_content);
				}
					if(resp == "Completed"){			
						$("#divgenerateXML #tdgenerateXML").html("Scheduler Templates recreated successfully.");
						$('#divgenerateXML .modal-footer').show();
					}else{
						$("#divgenerateXML #tdgenerateXML").html("XML file generated for date:" + dateArr[1]);
						Generate_TemplateXML(dateArr[0]);
					}
					top.show_loading_image('hide');
				}
			});
			
		}
		///////////XML Generate Code/////////////
		
		function delete_child_temp(val)
		{
			//get total appts
			var total_appts=$("#total_appts").val();
			var msgs="All the matching appointments ("+total_appts+") will be sent to re-schedule list.<br>Delete provider schedule ?";
			top.fancyConfirm(msgs,'','window.top.fmain.removeChildConfirmed(1)');//cal fucntion with param delete all
			
		}
		
		function removeChildConfirmed(val)
		{
			hideConfirmYesNoLocal();
			top.show_loading_image('show');
			var child_temp_id=$("#child_temp_id").val();
			var tmp_record_id=$("#tmp_record_id").val();
			var dated=$("#dated").val();
			$.ajax({
				url: "delete_child_template.php?child_temp_id="+child_temp_id+"&tmp_record_id="+tmp_record_id+"&dated="+dated,
				success: function(resp){
					if(resp == "Done"){			
						top.fAlert("Child template removed successfully.");
					}
					top.show_loading_image('hide');
					see_sel_month();
				}
			});
		}
		function viewLog(sel_pro_month, logType, limit)
		{
			top.show_loading_image('show','', 'Loading...');
			if(logType=='schedule')
			$("#viewLog .modal-title").html('Provider Schedule Log');
			else
			$("#viewLog .modal-title").html('Custom/Front desk Label Log');
			
			display_message='';
			if(sel_pro_month>0){display_message='Loading...';} else {display_message='Please select physician first.';}
			if(limit==0){
				$("#viewLog .modal-body").html(display_message);
				$("#viewLog").modal('show');
			}
			if(sel_pro_month>0)
			{	
				$.ajax({
					url: "provider_sch_log.php?sel_pro_month="+sel_pro_month+"&logType="+logType+'&limit='+limit,
					success: function(resp){
						if(resp){	
							var resArr=resp.split('~::~');
							if(resArr[1]=='No Page')resArr[1]='';
							if(limit>0)
							{
								$("#viewLog .modal-body #divLoadSchTmp").append(resArr[0]);
								$("#paging").html(resArr[1]);
							}
							else
							$("#viewLog .modal-body").html(resArr[0]+resArr[1]);
						}
						top.show_loading_image('hide');
					}
				});
			}
		}
		function toggleOption(div)
		{
			$("#fac_div").hide();
			$("#temp_div").hide();
			
			$("#"+div).show();
		}
		set_header_title('Provider Schedule');
</script>
<?php require_once('../../admin_footer.php') ?>