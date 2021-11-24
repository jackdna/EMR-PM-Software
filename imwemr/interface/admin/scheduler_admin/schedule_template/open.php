<?php
/*
File: open.php
Purpose: Add/Modify Lunch Time
Access Type: Direct
*/
require_once("../../admin_header.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');

$pro_id = trim($_REQUEST['pro_id']);
$temp_parent_id = trim($temp_parent_id) != "" ? $temp_parent_id : 0;
if(isset($_REQUEST['firstTimeSaveChildElem']) && trim($_REQUEST['firstTimeSaveChildElem'])!= "")
{
	$firstTimeSaveChild = trim($_REQUEST['firstTimeSaveChildElem']);	
}
else
{
	$firstTimeSaveChild = 0;	
}


if($firstTimeSaveChild == 1 && $temp_parent_id != 0 && $pro_id == "")
{
	$child_creation_qry = "INSERT INTO schedule_templates(schedule_name,morning_start_time,morning_end_time,availability,date_status,check_true,fldLunchStTm,fldLunchEdTm,MaxAppointments,warning_percentage,template_type,parent_id) SELECT concat(schedule_name,' - child ', now()) as schedule_name,morning_start_time,morning_end_time,availability,date_status,check_true,fldLunchStTm,fldLunchEdTm,MaxAppointments,warning_percentage,template_type, ".$temp_parent_id." FROM schedule_templates WHERE id =".$temp_parent_id." and parent_id = 0";
	imw_query($child_creation_qry);
	$pro_id = $_REQUEST['pro_id'] = imw_insert_id();
	
	$req_qry = "SELECT id, fldLunchStTm,fldLunchEdTm FROM schedule_templates WHERE (fldLunchStTm not between morning_start_time and morning_end_time) and (fldLunchEdTm not between morning_start_time and morning_end_time) and (fldLunchStTm != morning_start_time && fldLunchEdTm != morning_end_time) and id=".$pro_id;
	$sch_req_data_obj = imw_query($req_qry);
	if(imw_num_rows($sch_req_data_obj) > 0)
	{
		$sch_req_data = imw_fetch_assoc($sch_req_data_obj);	
		tmp_log('Created', "Child template created", '', '',$pro_id);					
		if($sch_req_data['fldLunchStTm'] != "00:00:00" && $sch_req_data['fldLunchEdTm'] != "00:00:00")
		{
			$del_req_qry = "DELETE FROM schedule_label_tbl where sch_template_id = '".$pro_id."' AND template_label = 'Lunch'";
			imw_query($del_req_qry);

			$req_qry = "UPDATE schedule_templates SET fldLunchStTm='00:00:00',fldLunchEdTm='00:00:00' WHERE id=".$pro_id;
			$result_scLObj = imw_query($req_qry);							
		}				
	}
	
	$child_label_qry = "INSERT INTO schedule_label_tbl(sch_template_id,start_time,end_time,template_label,check_true,lunch,reserved,procedures,procedures_id,label_type,label_color,label_group) SELECT ".$pro_id.",start_time,end_time,template_label,check_true,lunch,reserved,procedures,procedures_id,label_type,label_color,label_group FROM schedule_label_tbl WHERE sch_template_id = ".$temp_parent_id." order by schedule_label_id";
	imw_query($child_label_qry);	
	
	$firstTimeSaveChild = 0;
}

if($temp_parent_id != 0 && $pro_id == "")
{
	$firstTimeSaveChild = 1;
}

$alrdyExist =0;
//saving records
if(isset($_POST['action']) && $_POST['action'] == "save"){
    
	// CHECK IF SAME NAME TEMPLATE ALREADY EXISTS
	$addition=1;
	$chkqry="select id, schedule_name from schedule_templates where schedule_name ='".addslashes($_POST['proc_name'])."'";
	$chkres=imw_query($chkqry);
	if(imw_num_rows($chkres)>0 )
	{
		$chkresult=imw_fetch_array($chkres);
		if($pro_id != $chkresult['id']) { $addition=0; }
	}
	
	if($addition==1){
	
		if($pro_id == ""){
			$insQry = "INSERT INTO schedule_templates SET ";
		}else{
			$insQry = "UPDATE schedule_templates SET ";
		}
	
		$intMrHr = intval($_POST['time_mor_from_hour']);
		$intEvHr = intval($_POST['time_mor_to_hour']);
	
		if($_POST['ap1_mor'] == "PM"){
			if($intMrHr < 12){
				$intMrHr += 12;
			}
		}    
		if($_POST['ap2_mor'] == "PM"){
			if($intEvHr < 12){
				$intEvHr += 12;
			}
		}
		if($_POST['ap1_mor'] == "AM"){
			if($intMrHr == 12){
				$intMrHr = "00";
			}
		}
		
		if($_POST['ap2_mor'] == "AM"){
			if($intEvHr == 12){
				$intEvHr = "00";
			}
		}
	
		$intMrHr = (strlen($intMrHr) == 1) ? "0".$intMrHr : $intMrHr;
		$intEvHr = (strlen($intEvHr) == 1) ? "0".$intEvHr : $intEvHr;
		$maxTime= strtotime($intEvHr.":".$_POST['time_mor_to_mins'].":00");
		$insQry .= "    schedule_name = '".addslashes($_POST['proc_name'])."', 
						label = '',
						morning_start_time = '".$intMrHr.":".$_POST['time_mor_from_mins'].":00',
						morning_end_time = '".$intEvHr.":".$_POST['time_mor_to_mins'].":00',
						after_start_time = '00:00:00',
						after_end_time = '00:00:00',
						availability = '2',
						date_status = '".$_POST['ap1_mor'].",".$_POST['ap2_mor']."',
						check_true = 'true',
						fldLunchStTm = '00:00:00',
						fldLunchEdTm = '00:00:00',
						MaxAppointments=".intval($_POST['MaxAppointments']).",
						MinAppointments=".intval($_POST['MinAppointments']).",
						warning_percentage=".intval($_POST['warning_percentage']).",
						parent_id = '".$temp_parent_id."'";
		if($pro_id != ""){
			$insQry .= " WHERE id = '".$pro_id."' ";
		}
		imw_query($insQry);
		$proInsertId = imw_insert_id();
	
		//create log
		if($pro_id != ""){
			tmp_log('Updated', "Template updated", '', '',$pro_id);
		}
		else
		{
			tmp_log('Created', "Template created", '', '',$proInsertId);	
		}
		if($pro_id != ""){
			
			$lblname11=$_REQUEST['label_name1'];
			$lbltype11=$_REQUEST['label_type1'];
			$tm_f11=$_REQUEST['tm_from1'];
			$tm_t11=$_REQUEST['tm_too1'];
			
			if($lblname11<>"" && $tm_f11<>"" && $tm_t11<>""){
				foreach($lblname11 as $k1=>$v1){
					foreach($tm_f11 as $k2=>$v2){
						foreach($tm_t11 as $k3=>$v3){
							if($k1==$k2  && $k2==$k3 && $k1==$k3){
								if($labl_check==1){
									$v1=$label;
									$check_true="true";
								}else{
									$v1=$v1;
									$check_true="false";
								}
								$lbl_typ='';
								$lbl_typ=($lbltype11[$k1])?$lbltype11[$k1]:'Information';
								
								$chkqry="select sch_template_id from schedule_label_tbl where sch_template_id ='$pro_id' 
										and start_time='$v2' and end_time='$v3'";
								$chkres=imw_query($chkqry);
								if(imw_num_rows($chkres)<=0){
								$qry121 = "insert into schedule_label_tbl set
										  sch_template_id ='$pro_id',
										  start_time='".trim($v2)."',
										  end_time='".trim($v3)."',
										  template_label='".trim($v1)."',
										  label_type='".trim($lbl_typ)."',
										  date_time='".date('Y-m-d H:i:s')."'";
								}else{
									$qry121 = "update schedule_label_tbl set
											  start_time='".trim($v2)."',
											  end_time='".trim($v3)."',
											  template_label='".trim($v1)."',
											  date_time='".date('Y-m-d H:i:s')."' where 
											  sch_template_id ='$pro_id' and start_time='$v2' and end_time='$v3'";
								}
								@imw_query($qry121);
								@imw_query("delete from schedule_label_tbl where sch_template_id ='$pro_id' and start_time='$v2' and   template_label=''   and end_time='$v3'");// or die(imw_error());
								$endTime=strtotime($v3.':00');
								if($maxTime<$endTime && $pro_id)
								{
									$qryFinal = "Select id from schedule_appointments
									where sa_app_start_date>='".date('Y-m-d')."' 
									AND sa_patient_app_status_id NOT IN (203,201,18,19,20)
									AND sch_template_id='$pro_id' 
									AND ('".$v3.':00'."' between sa_app_starttime AND sa_app_endtime)"; 
									$re = imw_query($qryFinal);
									$row=imw_fetch_array($re);
									if($row['id'])
									{
										//if we exceed max allowed time then move this slot appt to to_do_list (if any)
										logApptChangedStatus($row['id'], "", "", "", "201", "", "", $_SESSION['authUser'], "Schedule template timing changed.", "", false);
										//updating schedule appointments details
										updateScheduleApptDetails($row['id'], "", "", "", "201", "", "", $_SESSION['authUser'], "Schedule template timing changed.", "", false);
									}
	
								}
							}
						}
					}
				}
				//getting labels where label those are for lunch
				$strQry = "SELECT start_time, end_time, template_label FROM schedule_label_tbl WHERE label_type = 'Lunch' AND sch_template_id = '".$pro_id."' ORDER BY start_time";
				$rsLabel = imw_query($strQry);
				//capturing start and end time of lunch
				$tmStart = "00:00:00";
				$tmEnd = "00:00:00";
				$blStartTimeCaptured = false;
				while ($rowLbl = imw_fetch_array($rsLabel, imw_ASSOC)){
					if($blStartTimeCaptured == false){
						$tmStart = $rowLbl['start_time'].":00";
					}
					$blStartTimeCaptured = true;
					$tmEnd = $rowLbl['end_time'].":00";
				}
				//insert start and end timings of lunch in new columns
				$strQry = "UPDATE schedule_templates SET 
								fldLunchStTm = '".$tmStart."',
								fldLunchEdTm = '".$tmEnd."'
							WHERE id = '".$pro_id."'";
				imw_query($strQry); 
				
				$req_qry = "SELECT id, fldLunchStTm,fldLunchEdTm FROM schedule_templates WHERE (fldLunchStTm not between morning_start_time and morning_end_time) and (fldLunchEdTm not between morning_start_time and morning_end_time) and (fldLunchStTm != morning_start_time && fldLunchEdTm != morning_end_time) and id=".$pro_id;
				$sch_req_data_obj = imw_query($req_qry);
				if(imw_num_rows($sch_req_data_obj) > 0)
				{
					$sch_req_data = imw_fetch_assoc($sch_req_data_obj);						
					if($sch_req_data['fldLunchStTm'] != "00:00:00" && $sch_req_data['fldLunchEdTm'] != "00:00:00")
					{
						$del_req_qry = "DELETE FROM schedule_label_tbl where sch_template_id = '".$pro_id."' AND template_label = 'Lunch'";
						imw_query($del_req_qry);

						$req_qry = "UPDATE schedule_templates SET fldLunchStTm='00:00:00',fldLunchEdTm='00:00:00' WHERE id=".$pro_id;
						$result_scLObj = imw_query($req_qry);							
					}				
				}
			}
		}
		
		//clearing cache
		$strQryPSch = "SELECT today_date FROM provider_schedule_tmp WHERE sch_tmp_id = '".$pro_id."'";
		$resSet = imw_query($strQryPSch);
		if($resSet){
			if(imw_num_rows($resSet) > 0){
				while($arrCache = imw_fetch_array($resSet,imw_ASSOC)){
					$taskDate = $arrCache['today_date'];
					clear_cache("all",$taskDate,"","","sch");
				}
			}
		}
		
		if($pro_id != ""){
			header("location:open.php?pro_id=".$pro_id."&refreshOpener=1&temp_parent_id=".$temp_parent_id);
		}elseif($proInsertId){
			header("location:open.php?pro_id=".$proInsertId."&refreshOpener=1&temp_parent_id=".$temp_parent_id); 
		}else{
			header("location:open.php"); 
		}
	}else {
			$alrdyExist =1;
	}
	
}

//fetching schedule tempalte details
if($pro_id != ""){
    $vquery_cdel4 = "select DATE_FORMAT(morning_start_time,'%h:%i:%s %p') as morning_start_time2,DATE_FORMAT(morning_end_time,'%h:%i:%s %p') as morning_end_time2,morning_start_time,morning_end_time,schedule_name,MinAppointments,MaxAppointments, warning_percentage from schedule_templates where id=".$pro_id;                
    $vsql_cdel4 = imw_query($vquery_cdel4);    
    list($tm_frm,$tm_t,$org_tm_frm,$org_tm_t,$schedule_name,$MinAppointments,$MaxAppointments, $warning_percentage)=imw_fetch_array($vsql_cdel4);
    $arrTm_frm = explode(" ",$tm_frm);
    $arrTm_t = explode(" ",$tm_t);

    $arrTm_frm_tm = explode(":",$arrTm_frm[0]);
    $arrTm_t_tm = explode(":",$arrTm_t[0]);

    $arrOrgTm_frm = explode(":",$org_tm_frm);
    $arrOrgTm_t = explode(":",$org_tm_t);
}

//loading page
$timeDiff = '00';
$timeSlot = DEFAULT_TIME_SLOT;
for($i=1;$i<=(60/$timeSlot);$i++){                
        $tm_min_array[] = $timeDiff;
        $timeDiff += $timeSlot;
}
$day_slot=array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');    
$tm_array=array('01','02','03','04','05','06','07','08','09','10','11','12');
$time_array=array("12 AM","01 AM","02 AM","03 AM","04 AM","05 AM","06 AM","07 AM","08 AM","09 AM","10 AM","11 AM","12 PM","01 PM","02 PM","03 PM","04 PM","05 PM","06 PM","07 PM","08 PM","09 PM","10 PM","11 PM" );
// ARRAY USED TO QUERY TIME
$time_array_h=array("00:00","01:00","02:00","03:00","04:00","05:00","06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00" );
?>
		<script  type="text/javascript" language="javascript">
           
		    function getScheduleTemplatesOnDML(){
				window.opener.top.fmain.getScheduleTemplates();
				/*top.show_loading_image('show');
                var url_dt="load.php";                                
                $.ajax({
					url:url_dt,
					type:'GET',
					success:function(response){
						temp_parent_id = $('#temp_parent_id').val();
						window.opener.top.fmain.document.getElementById("divLoadSchTmp").innerHTML = response;
						top.show_loading_image('hide');
					}
				});*/
            }
            function blank_function(){
                return false;
            }
			var existing_label='';
            function pop_addProviderProcdiv(){
				if ((navigator.appName == 'Microsoft Internet Explorer' || navigator.appName == "Netscape") && event.button==2){
					var abst=[];
					$('#jquery-drag-to-select-Text').find('span[data-selected]').each(function(id, elem){
						var selStatus = $(elem).data('selected');
						if(selStatus == 1) abst.push($(elem)[0]);
					});
						var counter=0;
						var tempStringArray;
						var selVal = new Array;
						var selObj = new Array;
						var lblTyp;
						var	tempStr;
						var	obj;
						var label_color;
						var label_group=0;
						for(var a in abst){
							str= abst[counter].id;
							tempStringArray=str.split("_");
							tempStr="tr"+tempStringArray[1];
							if(document.getElementById(tempStr).style.backgroundColor=="lightblue" || document.getElementById(tempStr).style.backgroundColor=="rgb(173, 216, 230)"){
								
									selObj.push($(document.getElementById(tempStr)).attr('id').replace('tr',''));
									obj=document.getElementById(tempStr);
									var spanLblObj = $(document.getElementById(tempStr)).find('span[data-labeltype]');
									lblTyp=spanLblObj.data('labeltype');
									var spanClrObj = $(document.getElementById(tempStr)).find('span[data-labelcolor]');
									label_color=spanClrObj.data('labelcolor');
									var spanClrObj = $(document.getElementById(tempStr)).find('span[data-labelgroup]');
									label_group=spanClrObj.data('labelgroup');
								
									if($(obj).find("input").val())
									{
										var aa=$(obj).find("input").val();
										var bb=aa.split('; ');
										
										bb.forEach(function(element) {
										  selVal.push('0~~~'+element);
										});
									}
								}
							counter++;	
						}
						//availableOptions
                        document.oncontextmenu = blank_function; // return false for window left menus;
                        delAll();
						selVal = $.unique( selVal );
						$("#selTimeStr").html(selObj.join(', '));
						if(lblTyp=='Procedure'){$("#group").prop("disabled", false);}
						if(lblTyp=='Lunch'){$("#template_label").prop("readonly", true);}else{$("#template_label").prop("readonly", false);}
						$("#group").val(label_group);
						$("#group").selectpicker("refresh");	
						$("#label_type").val(lblTyp);
						$("#label_type").selectpicker("refresh");
						if(label_color){
							load_color_picker(label_color);
						}else load_color_picker('#FFF');
						existing_label=selVal.join('; ');
						//$("#template_label").val(selVal.join('; '));
                        document.getElementById("tempSelectedCache").value="";   
                        document.getElementById("addProviderProcdiv").style.display="block";
                        document.getElementById("addProviderProcdiv").style.width = 90;
                        document.getElementById("addProviderProcdiv").style.position = 'absolute';
                        document.getElementById("addProviderProcdiv").style.display = 'block';
						//alert(event.x);
                        document.getElementById("addProviderProcdiv").style.pixelLeft =60;// event.x;        
                        document.getElementById("addProviderProcdiv").style.pixelTop = 100;//event.y;
                        document.getElementById("addProviderProcdiv").style.pixelRight = 50;//event.y;
						
						//code added to correct popup position in chrome
						var bro_ver=navigator.userAgent.toLowerCase();
						if(bro_ver.search("chrome")>1){
							$("#addProviderProcdiv").css({"display":"inline-block",top: 60, left: 100,right: 50});
							$("#addProviderProcdiv table").css({"box-shadow":"0px 8px 15px #000"});
						}
						
                        IsOn = true;        
                        //event.returnValue = false; 
						//disable buttons
						$("#button_bottom").css({"display":"none"}); 
						var overlay = jQuery('<div id="overlay"> </div>');
						overlay.appendTo(document.body);
                }else{                
                    document.getElementById("addProviderProcdiv").style.display="none";
					//enable buttons
					$("#button_bottom").css({'display':'inline-block'});
					$('#overlay').remove();
                }
            }
            function hideProviderProcDiv(){
                document.getElementById("addProviderProcdiv").style.display="none";
				//enable buttons
				$("#button_bottom").css({'display':'inline-block'});
				$('#overlay').remove();
            }
            var dragresizedisable = true;
            function setToggleClick(intMode){                
                if(intMode == "0"){	
                    dragresizedisable = true;
                }
                if(intMode == "1"){
                    dragresizedisable = false;
                }
            }
            function save_action(){
				$("#save").attr('disabled',true);
				var minAppt=parseFloat($("#MinAppointments").val());
				var maxAppt=parseFloat($("#MaxAppointments").val());
				var maxApptPer=parseInt($("#warning_percentage").val());
				var procName=$.trim($("#proc_name").val());console.log(maxAppt);
                if(procName==""){;
                    top.fAlert("Please Enter Template Name");
                    document.frmlabel.proc_name.focus();
					$("#save").attr('disabled',false);
                    return false;
                }else if(document.frmlabel.time_mor_from_hour.value=="" || document.frmlabel.time_mor_from_mins.value=="" || document.frmlabel.ap1_mor.value=="" || document.frmlabel.time_mor_to_hour.value=="" || document.frmlabel.time_mor_to_mins.value=="" || document.frmlabel.ap2_mor.value==""){
                    top.fAlert("Please Enter Template Timings");
					$("#save").attr('disabled',false);
                    return false;
                }else if(typeof minAppt!=="undefined" && minAppt>0 && typeof maxAppt!=="undefined" && maxAppt>0 && maxAppt>0 && maxAppt<minAppt){
						top.fAlert("Min. Appointments value must be less than or equal to Max. Appointments value.");
						document.frmlabel.MinAppointments.focus();
						$("#save").attr('disabled',false);
						return false;
					
				}else if((!isNaN(maxAppt) && maxAppt>0 && maxAppt<1) || (!isNaN(minAppt) && minAppt>0 && minAppt<1))
				{
					top.fAlert(" Min. and Max. Appointments value must be a valid integer and greater than 1 .");
					document.frmlabel.MaxAppointments.focus();
					$("#save").attr('disabled',false);
					return false;
				}
				else if(typeof maxApptPer!=="undefined" && maxApptPer>0 && (typeof maxAppt=="undefined" || maxAppt<=0 ||  isNaN(maxAppt))){
					
						top.fAlert("Show warning at Max% will not work without 'Max' appointments value.");
						document.frmlabel.MaxAppointments.focus();
						$("#save").attr('disabled',false);
						return false;
					
					
				}else{
					top.show_loading_image('show');
					document.getElementById("frmlabel").submit();
                }
            }
            function closed(){
                window.close();
            }
		
		function open_child_temp(temp_parent_id)
		{
			url = "open.php?temp_parent_id="+temp_parent_id;
			window.open(url,'_blank','width=1100,height=620,scrollbars=0,titlebar=0,menubar=no,resizable=0,location=no,left=220,top=150');
		}

</script>
<style>
	div.jquery-drag-to-select {background: #def;display: none;opacity: .3;filter: alpha(opacity=30);z-index: 10;border: 1px solid #369;}
	div#jquery-drag-to-select-Text{overflow:auto; overflow-x:hidden; position:relative; height:355px;}
	div.jquery-drag-to-select.active {display: block;}
	div.master_tmp_info{font-size:13px;padding:5px;}
	.table{margin-bottom :0}
	#addProviderProcdiv{z-index:900; position:absolute; display:none;}
	#overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #000;
    filter:alpha(opacity=50);
    -moz-opacity:0.5;
    -khtml-opacity: 0.5;
    opacity: 0.5;
    z-index: 9;
}
	
	
</style>
    <div id="divCommonAlertMsgSchTemplate"></div>    
	<div class="mainwhtbox">
      <div class="admpophead"> <div class="row">
       		<div class="col-sm-8"><h2>Schedule Template</h2></div>
       		<div class="col-sm-4 text-right adminclos"><span onClick="closed();">X</span></div>
       </div></div>
        <form name="frmlabel" id="frmlabel" method="post" action="">
            <input type="hidden" name="pro_id" value="<?php echo $pro_id;?>">
            <input type="hidden" name="action" value="save">
			<?php
				if($temp_parent_id != 0)
				{
					$get_temp_qry = 'SELECT schedule_name FROM schedule_templates WHERE id = '.$temp_parent_id;
					$get_temp_robj = imw_query($get_temp_qry);
					$get_temp_result_data = imw_fetch_assoc($get_temp_robj);
					$get_temp_name = $get_temp_result_data['schedule_name'];
					echo '<div class="master_tmp_info"> <b>Master Template :</b> '.$get_temp_name.'</div>';
				}
				if($firstTimeSaveChild == 1 && trim($schedule_name) == "" && $temp_parent_id != 0)
				{
					$vquery_cdel4 = "select DATE_FORMAT(morning_start_time,'%h:%i:%s %p') as morning_start_time2,DATE_FORMAT(morning_end_time,'%h:%i:%s %p') as morning_end_time2,morning_start_time,morning_end_time,schedule_name,MaxAppointments, warning_percentage from schedule_templates where id=".$temp_parent_id;                
					$vsql_cdel4 = imw_query($vquery_cdel4);    
					list($tm_frm,$tm_t,$org_tm_frm,$org_tm_t,$schedule_name,$MaxAppointments, $warning_percentage)=imw_fetch_array($vsql_cdel4);
					$arrTm_frm = explode(" ",$tm_frm);
					$arrTm_t = explode(" ",$tm_t);
				
					$arrTm_frm_tm = explode(":",$arrTm_frm[0]);
					$arrTm_t_tm = explode(":",$arrTm_t[0]);
				
					$arrOrgTm_frm = explode(":",$org_tm_frm);
					$arrOrgTm_t = explode(":",$org_tm_t);					
					$schedule_name = $schedule_name.' - child '.time();
				}
			?>
            <div class="admschtem"><table class="table">
                <tr>
                    <th rowspan="3" class="text-center time"><img src="../../../../library/images/schclad.png" width="40" height="40" alt=""/>Time</th>
                    <th class="text-left text-nowrap">Template Name :</th>
                    <td class="text-left"><input type="text" name="proc_name" id="proc_name" value="<?php echo stripslashes($schedule_name);?>" class="form-control" ></td>
                    <th rowspan="3" class="text-left temtime">Template <br>
                    Timings</th>
                    <th class="text-left temtim col-sm-3">From</th>
                    <th class="text-left temtim col-sm-3">To</th>                                
                </tr>
                <tr>
                    <th class="text-left">Appts.:</th>
                    <td><div class="row">
							<div class="col-sm-2">Min.</div>	
							<div class="col-sm-4"><input type="text" name="MinAppointments" id="MinAppointments" onKeyUp="validate_num(this);" value="<?php echo (int)$MinAppointments;?>" class="form-control"></div>
							<div class="col-sm-2">Max.</div>	
							<div class="col-sm-4"><input type="text" name="MaxAppointments" id="MaxAppointments" onKeyUp="validate_num(this);" value="<?php echo (int)$MaxAppointments;?>" class="form-control" ></div>
						</div>
					</td>
                    <td class="temtim"><select name="time_mor_from_hour" class="selectpicker" >
                            <option value="">--</option>                                    
                                <?php
                                foreach ($tm_array as $tm){
                                    if ($tm == $arrTm_frm_tm[0]){                                                        
                                        $chk_sel="selected";
                                    }
                                    print "<option value=".$tm." ".$chk_sel.">".$tm."</option>";
                                    $chk_sel="";
                                }
                                ?>
                        </select>
                        <select name="time_mor_from_mins" class="selectpicker" >    
                            <option value="">--</option>                                        
                            <?php
                            foreach ($tm_min_array as $tmm){
                                if ($tmm == $arrTm_frm_tm[1]){
                                    $chk_sel_min="selected";
                                }
                                print "<option value=".$tmm." ".$chk_sel_min.">".$tmm."</option>";
                                $chk_sel_min="";
                            }
                            ?>
                        </select>
                        <select name='ap1_mor' class="selectpicker" >                                                    
                            <option value="AM" <?php if($arrTm_frm[1] == "AM"){ echo 'selected';}?>>AM</option>
                            <option value="PM"  <?php if($arrTm_frm[1] == "PM"){ echo 'selected';}?>>PM</option>
                        </select></td>
                    <td class="temtim">
                        <select name="time_mor_to_hour" class="selectpicker" >
                            <option value="">--</option>                                                
                            <?php
                            foreach ($tm_array as $tm){
                                if ($tm == $arrTm_t_tm[0]){                                                        
                                    $chk_sel="selected";
                                }
                                print "<option value=$tm $chk_sel>$tm</option>";
                                $chk_sel="";
                            }
                            ?>
                        </select>
                        <select name="time_mor_to_mins" class="selectpicker" >    
                            <option value="">--</option>                                            
                            <?php
                            foreach ($tm_min_array as $tmm){
                                if ($tmm == $arrTm_t_tm[1]){
                                    $chk_sel_min="selected";
                                }
                                print "<option value=$tmm $chk_sel_min>$tmm</option>";
                                $chk_sel_min="";
                            }
                            ?>
                        </select>
                        <select name='ap2_mor' class="selectpicker" >
                            <option value="AM" <?php if($arrTm_t[1] == "AM"){ echo 'selected';}?>>AM</option>
                            <option value="PM"  <?php if($arrTm_t[1] == "PM"){ echo 'selected';}?>>PM</option>
                        </select>    
                    </td>
                </tr>
				<tr>
                    <td class="text-left" colspan=2>
					<div style="float:left">
					Show warning at Max%</div>
					<div style="float:left; margin-left:14px" class="col-sm-3">
					<input type="text" name="warning_percentage" id="warning_percentage" onKeyUp="validate_num(this);" value="<?php echo (int)$warning_percentage;?>" class="form-control"  >
                    </div>
                    <td class="text-left temtim col-sm-3"></td>
                    <td class="text-left temtim col-sm-3"></td>                                
                </tr>
            </table></div>

            <div id="recs">
                <?php  
                //showing labels for timings
                if($pro_id<>""){
                    $timestamp2=strtotime("23:59:00");

                    $hr1 = intval($arrOrgTm_frm[0]);
                    $hr2 = intval($arrOrgTm_t[0]);
                    $min1_tmp = intval($arrTm_frm_tm[1]);
                    $min2_tmp = intval($arrTm_t_tm[1]);
                    ?>                                        
                    <table class="table table-striped table-bordered table-condensed"> 
                        <tr>
							<?php                                                                                                                        
                                $t_s = 1;
                                $bl = 0;                                                    
                                $min_interval = $timeSlot;
								$div_ht=390;
								if($temp_parent_id)$div_ht=362;
                            ?> 
                            <td valign="top" style="overflow:auto!important; position:relative;">
                                <div id="jquery-drag-to-select-Text" style="overflow:auto!important;overflow-x:hidden!important;height:<?php echo $div_ht;?>px;">
                                <table class="table table-hover table-condensed table-bordered">
							<?php 
                                    $times_from=$time_array_h[$hr1];
                                    $times_to=$time_array_h[$hr1];
                                    $hours = date('H:00');
                                    $min_interval=$min_interval;
                                    $loop_in=(60/$min_interval);
                                    $row_span=($loop_in);
                                    $hei=(12*($min_interval/5));
                                    $flag=false;
                                    $t_s = 1;
                                    for($j=$hr1;$j<=$hr2;$j++,$t_s++){
                                       // $min_interval=10;
                                        $loop_in=(60/$min_interval);
                                        $row_span=($loop_in);
                                        $hei=12*($min_interval/5);
    
                                        $min_inter_row=$min_interval;
                                        $iis=2;        
                                        for($ii=0;$ii<$loop_in;$ii++){
                                            $tod_cell_color = ($ii%2 == 0)? 'tblBg' : 'tblBg';
                                            $incre=$j."-".$ii;
                                            $template_start_label="template_start_label".$incre;
                                            $div_generatedd=$incre;
                                            $div_generatedds=$incre."-"."k";
                                            if($ii%2==0){
                                                $hei1=$hei-1; // FOR SECOND ROW
                                            }else{
                                                $hei1=$hei;
                                            }
    
                                            $time_hour_to=substr($times_to,0,2);
                                            $time_minute_to=substr($times_to,3,2);
                                            $times_to=date("H:i", mktime($time_hour_to,$time_minute_to+$min_interval));
                                            $times_ma=date("H:00", mktime($time_hour_to,$time_minute_to+$min_interval));
                                            $hours = date('H:00');
    
                                            $dat_start_time=strtotime($tm_frm);
                                            $dat_end_time=strtotime($tm_t);
                                            $loop_start_time=strtotime($times_from);
                                            $loop_end_time=strtotime($times_to);
                                            if($loop_start_time>=$dat_start_time && $loop_end_time<=$dat_end_time){
                                                $flag=true;
                                                $disable="enabled";
                                                $hidden="text";
                                            }else{
                                                $flag=false;
                                                $disable="disabled";
                                                $hidden="hidden";
                                            }
    
                                            if(($tm_frm=="00:00:00" && $tm_t=="00:00:00") || ($tm_frm=="12:00:00" && $tm_t=="12:00:00")){
                                                $flag=true;
                                                $disable="enabled";
                                                $hidden="text";
                                            }
                                            $tmp_label="";
											$label_color='';
											$label_group=0;
                                            if($flag){
                                                $labels=$label;
                                                $disptimes=$disptime;
                                                $chkqry11="select template_label, check_true, label_type, label_group, label_color from schedule_label_tbl where sch_template_id ='$pro_id' and start_time='$times_from' and end_time='$times_to' "; 
                                                $chkres11=@imw_query($chkqry11);
												$preFixLbl='';
                                                if(@imw_num_rows($chkres11)>0){
                                                    list($tmp_label,$check_trues,$label_type, $label_group, $lbl_color)=@imw_fetch_array($chkres11);
													if($label_type=='Procedure')
													{
														$label_color='background-color:#CDB599';
													}
													else if($label_type=='Information')
													{
														$label_color='background-color:#9CC5C9';
													}
													else
													{
														$label_color='';
													}
													if($label_group==1)
													{
														$preFixLbl='S';
														$preFixLblTbl="title='Single/Group Label'";
													}
													else
													{
														$preFixLbl='M';
														$preFixLblTbl="title='Multi/Split Labels'";
													}
                                                }
                                            }else{
                                                $labels="";												
                                                $disptimes="";
                                            }
                                            if($t_s == 1){
                                                if($time_minute_to < $min1_tmp){
                                                    $display = 'none';
                                                }else{
                                                    $display = 'table-row';
                                                }
                                            }
                                            if($j == $hr2 && $time_minute_to >= $min2_tmp){
                                                break;
                                            }                                                            
                                    ?>
                                    <tr id="tr<?php echo $times_from;?>"  style="display:<?php print $display; ?>"  class="<?php echo $tod_cell_color?>"  onMouseDown ="pop_addProviderProcdiv()">                                            
                                        <td style="width:10%">    
                                                <?php
                                                if($ii == 0){ 
                                                   /* if($j == $hr2 && $min2_tmp  == 0){
                                                        echo "</table></td></tr>";
                                                        break;
                                                    }*/
                                                    ?>
                                                <div class="time_pane title_pane">
                                                      <?php if($preFixLbl){echo "<span class='title_lbl' $preFixLblTbl>$preFixLbl</span> ";}
														echo $time_array[$j];
													  ?>
												</div>
                                                    <?php
                                                }else{
                                                    ?>
                                                <div class="time_pane">
                                                   <?php if($preFixLbl){echo"<span class='row_lbl' $preFixLblTbl>$preFixLbl</span> ";}
													echo $min_inter_row;
													?>
                                                </div>
                                                   
                                                    <?php
												   $min_inter_row = $min_inter_row + $min_interval;
                                                }
                                                ?>
                                      </td>
                                        <td id="<?php echo $div_generatedd;?>"  class="text-left text-nowrap">
                                        <span id="dragSelectSpan_<?php echo($times_from."_".$times_to);?>" class="col-sm-12 pull-left" data-selected="0" data-labeltype="<?php echo $label_type; ?>" data-labelcolor="<?php echo $lbl_color; ?>" data-labelgroup="<?php echo $label_group; ?>">
                                            <input type="text" <?php echo $disable;?> id="label_names1[]" style="<?php echo $label_color; ?>"  value="<?php if($tmp_label<>""){    echo $tmp_label;}?>"  name="label_name1[]" class="form-control" />
                                            <input type="hidden" <?php echo $disable;?> name="label_type1[]" value="<?php echo $label_type;?>" />
											<input type="hidden" <?php echo $disable;?> name="tm_from1[]" id="tm_from1" value="<?php echo $times_from;?>"  class="form-control" />
                                            <input type="hidden"  <?php echo $disable;?> name="tm_too1[]" value="<?php echo $times_to;?>"  class="form-control" />
                                        </span>                                                       
                                        </td>  
                                    </tr>
                                            <?php 
                                            $time_hour_from=substr($times_from,0,2);
                                            $time_minute_from=substr($times_from,3,2);
                                            $times_minute_to=substr($times_to,3,2);
                                            $times_from=date("H:i", mktime($time_hour_from,$time_minute_from+$min_interval));
                                            $timestamp=date("H:i:s",mktime($j,$time_minute_from+$min_interval,0));
                                            $timestamp3=strtotime($timestamp);
                                            if(($timestamp3<$timestamp2)){
                                                
                                            }else{
                                                break;
                                            }
                                        }
                                        $k++;
                                    } 
                                    ?>
                                </table>
                                </div>
                            </td>
                        </tr>
              </table>
                    <?php
                }
                ?>
          </div>
                   
            <table class="table">
				<tr>
					<td>
                    	<input type="hidden" id="temp_parent_id" name="temp_parent_id" value="<?php echo $temp_parent_id; ?>" />
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-3 form-inline" title="Slots highlighted with this color are Procedures" >
										<span class="pull-left" style="width:20px;height:20px;background-color:#CDB599;margin-right:7px;"></span>
										<span>Mandatory</span>
									</div>
									<div class="col-sm-3 form-inline" title="Slots highlighted with this color are Appt Type">
										<span class="pull-left" style="width:20px;height:20px;background-color:#9CC5C9;margin-right:7px;"></span>
										<span>Informative</span>
									</div>
									<div class="col-sm-6 text-right" id="button_bottom">
										<input type="hidden" name="firstTimeSaveChildElem" value="<?php echo $firstTimeSaveChild; ?>" />
										<?php
											if(trim($pro_id) !="" && $pro_id != 0 && $temp_parent_id == 0){
										?>
											<input type="button" class="btn btn-success" id="add_child_temp" value="Add Another Version" onClick="open_child_temp(<?php echo $pro_id; ?>);">						
										<?php	
											}
										?>
										<input type="button" class="btn btn-success" id="save" value="Save" onClick="save_action();">						
										<input type="button" class="btn btn-danger" id="close" value="Close" onClick="closed();">
									</div> 
								</div>
							</div>
							<div class="clearfix"></div>
						</div>						
					</td>
				</tr>
            </table>
        </form>
	</div>
		<div id="addProviderProcdiv" class="mainwhtbox">
          <div class="admpophead"> <div class="row" style="cursor: all-scroll">
           		<div class="col-sm-8"><h2>Add Schedule Template Labels</h2></div>
           		<div class="col-sm-4 text-right adminclos"><span onclick="hideProviderProcDiv();">X</span></div>
           </div>
           </div>
           <?php include("labels.php");?>
           		
            <!--<table class="table table-bordered table-striped table-condensed" cellpadding="0" cellspacing="0">
                <tr class="grythead">
                    <td class="lead"></td>
                    
                </tr>
                <tr class="mainwhtbox">
                    <td><div class=" add_provider_proc"></div></td>
                </tr>
            </table>-->
        </div>
        
        <?php
        if(isset($_REQUEST['refreshOpener']) && $_REQUEST['refreshOpener'] == 1){
            ?>
        <script>
            getScheduleTemplatesOnDML();    
        </script>
            <?php
        }
        ?>
<script>
<?php if($alrdyExist==1)
{ ?>
        top.fancyAlert("Error Saving Record! Template name already exists\n","imwemr","",top.document.getElementById("divCommonAlertMsgSchTemplate"),'','','','',true,10, 350, '');
<?php }?>

window["strHtmlTimings"]="";
window["strTimeRanges"]="";

$(document).ready(function()
{
	$("#addProviderProcdiv").draggable();
	if(document.getElementById("jquery-drag-to-select-Text")){
		//Stores the target element on click
		var span_obj = {};
		$('#jquery-drag-to-select-Text').dragToSelect({
			selectables: 'span', 
			onShow: function(e){
				var target_obj = e.target;
				//Target element
				span_obj.obj = $(target_obj).closest('span');
			},
			onHide: function (e) {
				//Adding selected class to target event on click event
				$(span_obj.obj).addClass('selected');
				var abst=$('#jquery-drag-to-select-Text span.selected').toArray();
				var obj = $('#jquery-drag-to-select-Text span.selected');
				var str="";
				var counter=0;
				var tempStringArray;		
				var	tempStr;
				for(var a in abst){
					str= abst[counter].id;
					tempStringArray=str.split("_");
					tempStr="tr"+tempStringArray[1];
					var spanObj = $(document.getElementById(tempStr)).find('span[data-selected]');
					if(document.getElementById(tempStr).style.backgroundColor=="lightblue" || document.getElementById(tempStr).style.backgroundColor=="rgb(173, 216, 230)"){
							var strToReplace='<div class="text_10b">Start Time: '+tempStringArray[1]+' END Time:'+tempStringArray[2]+'</div>';
							var strRangeToReplace=tempStringArray[1]+'---'+tempStringArray[2]+"~~~";
							window["strHtmlTimings"]=window["strHtmlTimings"].replace(strToReplace,"");
							window["strTimeRanges"]=window["strTimeRanges"].replace(strRangeToReplace,"");						
							document.getElementById(tempStr).style.backgroundColor="#FFFFFF";
							if(spanObj.length && spanObj.data('selected') == 1) spanObj.data('selected', 0); 

						}else{
							window["strHtmlTimings"]+='<div class="text_10b">Start Time: '+tempStringArray[1]+" END Time:"+tempStringArray[2]+"</div>";
							window["strTimeRanges"]+=tempStringArray[1]+'---'+tempStringArray[2]+"~~~";
							document.getElementById(tempStr).style.backgroundColor="lightblue";
							if(spanObj.length && spanObj.data('selected') == 0) spanObj.data('selected', 1);
						}
					counter++;	
				}
				if(window["strHtmlTimings"]!="" && window["strTimeRanges"]!=""){
					document.getElementById("defaultTimeSelectionTR").style.display="none";
					$('#hidTimeRangeFinalString').val(window["strTimeRanges"]);
					
					//show selected time string
					$("#defaultTimeSelectionTR2").show();
					
				}
				if(window["strHtmlTimings"]=="" && window["strTimeRanges"]==""){
					//show selected time string
					$("#defaultTimeSelectionTR2").hide();
					$("#selTimeStr").html('');
					$('#hidTimeRangeFinalString').val("");
					document.getElementById("defaultTimeSelectionTR").style.display="block";
				}
			}
		});	
	}
});
</script>
<?php include('../../admin_footer.php'); ?>