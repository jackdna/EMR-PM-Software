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
 * Purpose: Add/Modify procedure templates
 * Access Type: Direct
 */
//------FILE INCLUSION------
include_once("../../admin_header.php");
include_once('../../../../library/classes/admin/scheduler_admin_func.php');

$pro_id = $_REQUEST['pro_id'];
$total_labels=array();

//------GET PROCEDURES------
$res = imw_query("
				SELECT
					id,
					proc,acronym
				FROM
					`slot_procedures`
				WHERE
					proc!=''
				AND
					(procedureId=id || procedureId=0)
				AND
					active_status='yes'
				AND
					source=''
				GROUP BY
					proc
				ORDER BY
					acronym ASC
				");

	$arrDefaultName = array();
    $arrDefaultId = array();

	while($row = imw_fetch_array($res))
	{
        $arrDefaultName[] = $row['proc']."~~".$row['acronym'];
        $arrDefaultId[] = $row['id'];
    }
    $arrResult = array_combine($arrDefaultId, $arrDefaultName);

	if(count($arrResult) > 0)
	{
		foreach($arrResult as $variable => $value)
		{
            $sel="";
            if($variable==$procedureid)
            {
                $sel='selected';
                $blSelected = true;
            }
            $vall=explode("~~",$value);
            $showProc = $vall[1];
            $total_labels[$showProc]=$showProc;
        }
    }

//------GET LABELS------
$qry1= "SELECT
			DISTINCT stl.template_label
		FROM
			`schedule_label_tbl` as stl
		WHERE
			stl.label_type != ''
		AND
			stl.template_label != ''
		ORDER BY
			template_label ASC
		";
$result_str1=imw_query($qry1);

while($label_row = imw_fetch_array($result_str1))
{
	if(strstr($label_row['template_label'],';'))
	{
		$lbl=array();
		$lbl=explode(';',$label_row['template_label']);
		foreach($lbl as $label)
		{
			$label=trim($label);
			$total_labels[$label]=$label;
		}
	}
	else
	{
		$total_labels[$label_row['template_label']]=$label_row['template_label'];
	}
}

//------SAVING PROVIDER SPECIFIC TIMINGS------
if(isset($_POST['action']) && $_POST['action'] == "save_timings")
{
    $doctor_id = $_REQUEST['doctor_id'];

    $deleteQ = "DELETE
				FROM
					`slot_procedures_timings`
				WHERE
					(
						doctor_id='".$doctor_id."'
						AND
						procedureId='".$pro_id."'
					)
				";
    $resQ = imw_query($deleteQ);

    for($counter=1;$counter<=4;$counter++)
	{
        $ap1_aft=$_POST['ap1_aftPHY'.$counter];
        $ap2_aft=$_POST['ap2_aftPHY'.$counter];

		$afterAvail = $ap1_aft.','.$ap2_aft;

		$hr=$_POST['time_aft_from_hourPHY'.$counter];
        $min=$_POST['time_aft_from_minsPHY'.$counter];
        $hr2=$_POST['time_aft_to_hourPHY'.$counter];
        $min2=$_POST['time_aft_to_minsPHY'.$counter];

        if($hr!="" && $hr2!="" && $min != "" && $min2 != "")
		{

            if($ap1_aft == "PM")
			{
                if($hr < 12)
				{
                    $hr += 12;
                }
            }
            if($ap2_aft == "PM")
			{
                if($hr2 < 12)
				{
                    $hr2 += 12;
                }
            }
            if($ap1_aft == "AM")
			{
                if($hr == 12)
				{
                    $hr = "00";
                }
            }

            if($ap2_aft == "AM")
			{
                if($hr2 == 12)
				{
                    $hr2 = "00";
                }
            }

            $hr = (strlen($hr) == 1) ? "0".$hr : $hr;
            $hr2 = (strlen($hr2) == 1) ? "0".$hr2 : $hr2;

            $ts1 = mktime($hr,$min,0);
            $ts2 = mktime($hr2,$min2,0);

            $etm_from_aft = $hr.":".$min.":00";
            $etm_to_aft = $hr2.":".$min2.":00";

            if($ts2 > $ts1)
			{
                $query="INSERT INTO
							`slot_procedures_timings`
						SET
							after_start_time='".$etm_from_aft."',
							after_end_time='".$etm_to_aft."',
							doctor_id='".$doctor_id."',
							after_availiability='".$afterAvail."',
							procedureId='".$pro_id."',
							timeCount='".$counter."'
						";
                imw_query($query) or die(imw_error());
            }
        }
    }
    header("location:open.php?pro_id=".$pro_id."&refreshOpener=1&save=succ");
}

//------DELETING PROVIDER SETTINGS------
if(isset($_REQUEST['del_id']) && !empty($_REQUEST['del_id'])){
    $strDelQry = "update slot_procedures set active_status='del' WHERE id = '".$_REQUEST['del_id']."'";
    imw_query($strDelQry);
    header("location:open.php?pro_id=".$pro_id."&del=succ");
}

//------SAVING RECORDS------
if(isset($_POST['action']) && $_POST['action'] == "save")
{
	$qry="SELECT
			id
		FROM
			`slot_procedures`
		WHERE
			proc='".$_REQUEST['proc_name']."'
		AND
			sp1.doctor_id = '0'
		AND
			active_status!='del'
		";
	$result=imw_query($qry);
	$num_proc=imw_num_rows($result);
	if($pro_id == "")
	{
		if($num_proc>0)
		{
			echo "<SCRIPT>window.location='open.php?alrdy_exist=yes';</SCRIPT>";
			die();
		}
		else
		{
        	$insQry = "INSERT INTO slot_procedures SET ";
		}
    }
	else
	{
        $insQry = "UPDATE slot_procedures SET ";
    }

	$labels='';
	$labels=implode('~:~',$_POST['labels']);

	$user_group='';
	$user_group=implode(',',$_POST['user_group']);

	$insQry .= "proc = '".addslashes($_REQUEST['proc_name'])."',
				acronym = '".addslashes($_REQUEST['proc_acro'])."',
				proc_type='".addslashes($_REQUEST['proc_type'])."',
				proc_color = '".addslashes($_REQUEST['proc_color'])."',
				labels='$labels',user_group='".$user_group."',
				ref_management = ".($_REQUEST['ref_management']?1:0).",
				verification_req = ".($_REQUEST['verification_req']?1:0).",
				non_billable = ".($_REQUEST['non_billable']?1:0)."
				";

	$default_proc_time = "";

	$tm_qry = "SELECT id FROM slot_procedures WHERE times = '10' LIMIT 1";
	$tm_res = imw_query($tm_qry) or $msg_info[] = imw_error();
	if($tm_res)
	{
		if(imw_num_rows($tm_res) > 0)
		{
			$tm_arr = imw_fetch_array($tm_res);
			$default_proc_time = $tm_arr["id"];
		}
		else
		{
			$ins_qry1 = "INSERT INTO
							`slot_procedures`
						SET
							proc = '',
							acronym = '',
							proc_color = '',
							proc_time = '',
							after_start_time  = '',
							after_end_time = '',
							doctor_id = '0',
							after_availiability  = '',
							procedureId = '0',
							times = '10',
							active_status = 'yes'";
			imw_query($ins_qry1) or $msg_info[] = imw_error();
			$default_proc_time = imw_insert_id();
		}
	}
	$insQry .= ", proc_time = '".$default_proc_time."'";


	if($pro_id != "")
	{
        $insQry .= "WHERE
						id = '".$pro_id."'
					OR
						procedureId = '".$pro_id."'
					";
    }
    imw_query($insQry);
	if($pro_id == "")
	{
		//echo "here";
       	$proInsertId = imw_insert_id();
       	$updQry="
				UPDATE
					`slot_procedures`
				SET
					procedure_id = '".$proInsertId."'
				WHERE
					id = '".$proInsertId."'
				";
       	imw_query($updQry);
    }
    if($pro_id != "")
	{
		//echo "there";
		$proInsertId = $pro_id;

		//------UPDATING EXISTING RECORDS------
		if(isset($_REQUEST['proc_spec_id']) && !empty($_REQUEST['proc_spec_id']))
		{
             if(is_array($_REQUEST['proc_spec_id']) && count($_REQUEST['proc_spec_id']) > 0)
			 {
				$k = 0;
				foreach($_REQUEST['proc_spec_id'] as $intRecId)
				{
					$updQry = "UPDATE slot_procedures SET ";
					$updQry .= " doctor_id = '".$_REQUEST['doctors_id'][$k]."', ";

						//------GETTING & SETTING PROCEDURE TIME------
                      	$proc_time = $_REQUEST['proc_time'][$k];
                     	if($_REQUEST['proc_time'][$k] == "other")
						{
                            	$new_proc_time_var = "other_time".$intRecId;
                            	$new_proc_time = (trim($_REQUEST[$new_proc_time_var]) != "") ? intval($_REQUEST[$new_proc_time_var]) : "";

                            	if($new_proc_time != "")
								{
                                	$chkQry="SELECT
												id
											FROM
												`slot_procedures`
											WHERE
												times = '".$new_proc_time."'
											ORDER BY
												id LIMIT 1";
									$rsChk = imw_query($chkQry);
                                	if($rsChk)
									{
                                    	if(imw_num_rows($rsChk) > 0)
										{
                                        	$arrChk = imw_fetch_array($rsChk);
                                        	$proc_time = $arrChk['id'];
                                    	}
										else
										{
                                        	$insQry4 = "INSERT INTO
															`slot_procedures`
														SET
															times = '".$new_proc_time."',
															active_status = 'yes' ";
                                        	imw_query($insQry4);
                                        	$proc_time = imw_insert_id();
                                    	}
                                	}
                            	}
								else
								{
                                	$proc_time = "";
								}
                      	}
                      	$updQry .= " proc_time = '".$proc_time."', ";

                     	 //------GETTING & SETTING GAP TIME------
                      	$gap_time = $_REQUEST['inter'][$k];
                      	if($_REQUEST['inter'][$k] == "other")
						{
                            $new_gap_time_var = "other_time_inter".$intRecId;
                            $new_gap_time = (trim($_REQUEST[$new_gap_time_var]) != "") ? intval($_REQUEST[$new_gap_time_var]) : "";

                            if($new_gap_time != "")
							{
                                $gap_time = $new_gap_time;

                                $chkQry="SELECT
											id
										FROM
											`slot_procedures`
										WHERE
											times = '".$new_gap_time."'
										ORDER BY
											id LIMIT 1";
                                $rsChk = imw_query($chkQry);
                                if($rsChk)
								{
                                    if(imw_num_rows($rsChk) > 0)
									{

									}
									else
									{
                                        $insQry3 = "INSERT INTO
														`slot_procedures`
													SET
														times = '".$new_gap_time."',
														active_status = 'yes' ";
                                        imw_query($insQry3);
                                    }
                                }
                            }
							else
							{
                                $gap_time = "";
                            }
                      }
                      $updQry .= " intervals = '".$gap_time."', ";
                      $max_allowed = ($_REQUEST['max_allow'][$k] != "") ? intval($_REQUEST['max_allow'][$k]) : "";
                      $exp_arrival_time = ($_REQUEST['exp_arrival_time'][$k] != "") ? intval($_REQUEST['exp_arrival_time'][$k]) : "";

					  $updQry .= " max_allowed = '".$max_allowed."', ";
                      $updQry .= " proc_mess = '".addslashes(nl2br($_REQUEST['txt_comments'][$k]))."', ";
					  $updQry .= " exp_arrival_time = '".$exp_arrival_time."' ";
                      $updQry .= " WHERE id = '".$intRecId."' ";

					  imw_query($updQry);
                      $k++;
                  }
             }
		}


          //------ADDING NEW PROVIDER------
          if(isset($_REQUEST['new_phy_name']) && !empty($_REQUEST['new_phy_name'])){
              $insQry = "INSERT INTO slot_procedures SET
                            proc = '".addslashes($_REQUEST['proc_name'])."',
                            times = '',
                            acronym = '".addslashes($_REQUEST['proc_acro'])."',
                            proc_color = '".addslashes($_REQUEST['proc_color'])."',
							proc_type = '".addslashes($_REQUEST['proc_type'])."', ";

              //------GETTING & SETTING PROCEDURE TIME------
              $proc_time = $_REQUEST['fac_slot'];
              if($_REQUEST['fac_slot'] == "other")
			  {
                    $new_proc_time = (trim($_REQUEST['other_time']) != "") ? intval($_REQUEST['other_time']) : "";

                    if($new_proc_time != "")
					{
                        $chkQry="SELECT
									id
								FROM
									`slot_procedures`
								WHERE
									times = '".$new_proc_time."'
								ORDER BY
									id LIMIT 1
								";
                        $rsChk = imw_query($chkQry);
                        if($rsChk)
						{
                            if(imw_num_rows($rsChk) > 0)
							{
                                $arrChk = imw_fetch_array($rsChk,imw_ASSOC);
                                $proc_time = $arrChk['id'];
                            }
							else
							{
                                $insQry3 = "INSERT INTO
												`slot_procedures`
											SET
												times = '".$new_proc_time."',
												active_status = 'yes' ";
                                imw_query($insQry3);
                                $proc_time = imw_insert_id();
                            }
                        }
                    }
					else
					{
                        $proc_time = "";
                    }
              }
              $insQry .= "  proc_time = '".$proc_time."', ";
              $max_allowed = ($_REQUEST['new_max_allow'] != "") ? intval($_REQUEST['new_max_allow']) : "";
			  $exp_arrival_time = ($_REQUEST['new_exp_arrival_time'] != "") ? intval($_REQUEST['new_exp_arrival_time']) : "";

			  $insQry .= "  max_allowed = '".$max_allowed."', ";
              $insQry .= "  exp_arrival_time = '".$exp_arrival_time."', ";
              //------GETTING & SETTING GAP TIME------
              $gap_time = $_REQUEST['inter_new'];
              if($_REQUEST['inter_new'] == "other")
			  {
                    $new_gap_time = (trim($_REQUEST['other_time_inter']) != "") ? intval($_REQUEST['other_time_inter']) : "";

                    if($new_gap_time != "")
					{
                        $gap_time = $new_gap_time;

                        $chkQry="SELECT
									id
								FROM
									`slot_procedures`
								WHERE
									times = '".$new_gap_time."'
								ORDER BY
									id LIMIT 1";
                        $rsChk = imw_query($chkQry);
                        if($rsChk)
						{

                            if(imw_num_rows($rsChk) > 0)
							{

							}
							else
							{
                                $insQry2="INSERT INTO
											`slot_procedures`
										SET
											times = '".$new_gap_time."',
											active_status = 'yes'
										";
                                imw_query($insQry2);
                            }
                        }
                    }
					else
					{
                        $gap_time = "";
                    }
              }
              $insQry .= "  intervals = '".$gap_time."', ";

              $insQry .= "  doctor_id = '".$_REQUEST['new_phy_name']."',
                            proc_mess = '".addslashes(nl2br($_REQUEST['new_txt_comments']))."',
                            procedureId = '".$pro_id."',
                            active_status = 'yes' ";
              imw_query($insQry) or die(imw_error());
          }
    }

		//---
		/*
		 * ERP PATIENT PORTAL CLINICAL SUMMARY API WORK STARTS HERE
		 * PATIENT_SUMMARY IS API MAIN FILE
		 * isERPPortalEnabled IS FUNCTION CALLED FROM common_functions.php TO CHECK ERP ACCOUNT ENABLE OR DISABLE
		 */
		$erp_error=array();
		if(isERPPortalEnabled())
		{
			try {
				include_once($GLOBALS['srcdir'].'/erp_portal/appointments.php');
				$oAppointments = new Appointments();
				$oAppointments->addUpdateAppointmentRequestReasons($proInsertId);
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
		}
		//--

	@header("location:open.php?pro_id=".$proInsertId."&refreshOpener=1&save=succ");
}



//------ARRAYS TO SHOW TIMINGS DROP DOWNS------
$tm_array = array('01','02','03','04','05','06','07','08','09','10','11','12');
$timeDiff = '00';

$timeSlot = DEFAULT_TIME_SLOT;

for($i = 1;$i <=(60/$timeSlot);$i++)
{
    $tm_min_array[] = $timeDiff;
    $timeDiff += $timeSlot;
}

//------LOADING DEFAULT TEMPLATE DETAILS------
if($pro_id != "")
{
    $strQry = "SELECT * FROM slot_procedures WHERE id = '".$pro_id."'";
    $rsData = imw_query($strQry);
    $arrData = imw_fetch_array($rsData);
}
$arr_user_type=array();

$qry_usertype="SELECT id, name FROM `user_groups` WHERE `status` = '1' ORDER BY `name`";
$res_usertype=imw_query($qry_usertype);

while($row_usertype=imw_fetch_assoc($res_usertype))
{
	$usertype_id=$row_usertype["id"];;
	$usertype_name=$row_usertype["name"];;
	$arr_user_type[$usertype_id]=$usertype_name;
}
?>
<style>
.sp-preview {width: 80% !important }
</style>
	<script type="text/javascript">
	//------PHP VAR------
	var Request_sav = '<?php echo $_REQUEST['save']; ?>';
	var Request_del = '<?php echo $_REQUEST['del']; ?> ';

	$(document).ready( function() {
		if(Request_sav == 'succ'){
			top.fAlert('Records saved successfully');
		}

		if(Request_del == 'succ'){
			top.fAlert('Records deleted successfully');
		}
	});

	function getProcedureTemplatesOnDML(){
		var url_dt="load.php";
		$.ajax({
			type: "GET",
			url: url_dt,
			data: '',
			success:function(response){
				window.opener.top.fmain.document.getElementById("divLoadProTmp").innerHTML = response;
			}
		});
	}

	function save_action(){
		if(document.frmlabel.proc_name.value==""){
			top.fancyAlert("Please Enter Template Name\n","imwemr","",top.document.getElementById("divCommonAlertMsgProcTemplate"),'','','','',true,10, 300, '');
			document.frmlabel.proc_name.className = 'mandatory form-control';
			document.frmlabel.proc_name.focus();
			return false;
		}else if(document.frmlabel.proc_acro.value==""){;
			top.fancyAlert("Please Enter Template Acronym\n","imwemr","",top.document.getElementById("divCommonAlertMsgProcTemplate"),'','','','',true,10, 300, '');
			document.frmlabel.proc_acro.className = 'mandatory form-control';
			document.frmlabel.proc_acro.focus();
			return false;
		}else{
			proc_val=document.frmlabel.proc_name.value;
			proc_acro_val=document.frmlabel.proc_acro.value;
			var object_proc=window.opener.top.fmain.document.getElementById("json_procedure");
			var obj_proc_ali=window.opener.top.fmain.document.getElementById("json_acronym");
			var ret=true;
			if(object_proc || obj_proc_ali){
				var ret=check_proc_acro(proc_val,proc_acro_val);
			}
			if(ret==true){
				document.getElementById("frmlabel").submit();
			}
		}
	}
	function view_tr(){
		if($("#trdisp").css('display')=="none"){
			$("#trdisp").css('display','table-row');
			$('#new').attr('disabled','disabled');
			$('#new_record').val(1)
		}
	}
	function remove(){
		if($("#trdisp").css('display')=="table-row"){
			$("#trdisp").css('display','none');
			if($("#trdisp2")){
				$("#trdisp2").css('display','none');
			}
			$("#new_record").val(0);
			$('#new').removeAttr('disabled');
		}
	}
	function show_other(ivals,objName){
		if(ivals=='other'){
			$("#"+objName+"").css('display','block');
		}else{
			$("#"+objName+"").css('display','none');
		}
	}
	function confirm_del(del_id,cnfrm){
		document.frmDelete.del_id.value = del_id;
		top.fancyConfirm('Are you sure to delete this provider setting?', 'Please confirm','delete_p_temp()');
	}

	function delete_p_temp()
	{
		document.frmDelete.submit();
	}
	function blank_function(){
		return false;
	}

	function save_timings(objForm){
		objForm.doctor_id.value = $("#div_doctor_id").val();
		objForm.pro_id.value = $("#pro_id").val();
		objForm.submit();
	}
	function change_input_class(obj){
		if(obj.value == ""){
			obj.className = "mandatory form-control";
		}else{
			obj.className = "form-control";
		}
	}
	function check_proc_acro(obj_proc_val,obj_proc_acr_val){
		var ret=true;
		var object_proc=window.opener.top.fmain.document.getElementById("json_procedure");
		var obj_proc_ali=window.opener.top.fmain.document.getElementById("json_acronym");
		if(object_proc){
			var json_procedure_div = JSON.parse(object_proc.innerHTML);
		}
		if(obj_proc_ali){
			var json_acryn_div = JSON.parse(obj_proc_ali.innerHTML);
		}
		var edid_id=document.frmlabel.pro_id.value;
		if(obj_proc_val){
			if(json_procedure_div[obj_proc_val.toLowerCase()] && json_procedure_div[obj_proc_val.toLowerCase()]!=edid_id){
				msg='Procedure name already exist';
				top.fAlert(msg);
				return false;
				ret=false;
			}
		}
		if(obj_proc_acr_val){
			if(json_acryn_div[obj_proc_acr_val.toLowerCase()] && json_acryn_div[obj_proc_acr_val.toLowerCase()]!=edid_id){
				msg='Practice Code already exist';
				top.fAlert(msg);
				return false;
				ret=false;
			}
		}
		return ret;
	}

	function check_val(id)
	{
		var x,obj;

		x = document.getElementById(id).value;
		obj = document.getElementById(id);

		if(isNaN(x))
		{
			fAlert('Please enter numeric values only');
			obj.value='';
			return false;
		}
		else if (x == '')
		{
			top.fAlert('Please enter numeric values only');
			obj.value='';
			return false;
		}
	}
	</script>
	<style>
		.bfh-colorpicker-popover{right:0!important;left:inherit;}
	</style>
	<div id="selectpicker_parent" style="position:absolute;"></div>
	<div class="container-fluid">
		<div class="mainwhtbox">
			<div id="divCommonAlertMsgProcTemplate"></div>
			<form name="frmDelete" action="" method="get">
				<input type="hidden" id="pro_id" name="pro_id" value="<?php echo $pro_id;?>">
				<input type="hidden" id="del_id" name="del_id" value="">
			</form>
			<form name="frmlabel" id="frmlabel" action="" method="post">
				<input type="hidden" name="pro_id" value="<?php echo $pro_id;?>">
				<input type="hidden" id="action" name="action" value="save">
				<input type="hidden" id="div_doctor_id" name="div_doctor_id" value="">
			<div class="row">
				<div class="col-sm-12">
					<div class="admpophead"><div class="row">
						<div class="col-sm-8"><h2>Procedure Template</h2></div>
						<div class="col-sm-4 text-right adminclos"><span onClick="window.close();">X</span></div>
					</div></div>
					<div class="row">
						<div class="col-sm-3 ">
						<div class="procbox"><div >
							<label>Procedure Name</label>
							<input type="text" id="proc_name" class="form-control" name="proc_name" value="<?php echo stripslashes($arrData['proc']);?>" onBlur="change_input_class(this);" />
						</div>

						<div>
							<label>Labels</label>
							<select name="labels[]" id="labels" class="selectpicker" data- multiple data-width="100%" data-done-button="true">
								<?php
								$thisLblArr=explode('~:~',$arrData['labels']);
								sort($total_labels);

								foreach($total_labels as $label)
								{
									echo '<option value="'.$label.'"';
									echo(in_array($label,$thisLblArr))?' selected':'';
									echo'>'.$label.'</option>';
								}?>
							</select>
						</div>

						<div>
							<label>Practice Code</label>
							<input name="proc_acro" type="text" class="form-control" id="proc_acro" value="<?php echo stripslashes($arrData['acronym']);?>" onBlur="change_input_class(this);" />
						</div>

						<div>
							<label>Provider Group</label>
							<?php
								 $all_user_selected=$thisUserArr="";
								if($pro_id=="" || !$pro_id){
									$all_user_selected=' SELECTED ';
								}else{
									$thisUserArr=explode(',',$arrData['user_group']);
								}
							?>
							<select name="user_group[]" id="user_group" class="selectpicker minimal selecicon" multiple data-width="100%">
								<?php
									foreach($arr_user_type as $usertype_id => $usertpye_name){
										$single_selected="";
										if(in_array($usertype_id,$thisUserArr)){$single_selected=' SELECTED ';}
										echo '<option value="'.$usertype_id.'" '.$single_selected.' '.$all_user_selected.' >'.$usertpye_name.'</option>';
									}
								?>
							</select>
						</div>
						<div class="row">
							<div class="col-sm-6 ">
								<label>Color</label>
								<input type="text" class="grid_color_picker" name="proc_color" id="proc_color" value="<?php echo stripslashes($arrData['proc_color']);?>">
								<?php $grid_color_picker_js= stripslashes($arrData['proc_color']);?>
							</div>

							<div class="col-sm-6 "><label>Type</label>
								<select name="proc_type" id="proc_type" class="selectpicker" data-width="100%">
									<?php
									$proc_type_arr=array(""=>"None","Clinical"=>"Clinical","Surgical"=>"Surgical","Telemedicine"=>"Telemedicine");
									foreach($proc_type_arr as $val=>$txt)
									{
										$selected=($arrData['proc_type']==$val)?"selected":"";
										echo "<option value=\"$val\" $selected>$txt</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div>
							<div class="clearfix">&nbsp;</div>
							<div class="checkbox">
								<input type="checkbox" name="ref_management" id="ref_management" <?php echo($arrData['ref_management']?'checked':'');?> value="1" />
								<label for="ref_management">Referral Required</label>
							</div>
						</div>
						<div>
							<div class="checkbox">
								<input type="checkbox" name="verification_req" id="verification_req" <?php echo($arrData['verification_req']?'checked':'');?> value="1" />
								<label for="verification_req">Auth/Verify Required</label>
							</div>
						</div>
                        <div>
							<div class="checkbox">
								<input type="checkbox" name="non_billable" id="non_billable" <?php echo($arrData['non_billable']?'checked':'');?> value="1" />
								<label for="non_billable">Non-Billable</label>
							</div>
						</div>

						</div>
						</div>
						<div class="col-sm-9">

								<div style="width:100%; height:273px; overflow:auto;">
									<?php
									if($pro_id != "")
									{
										$strProTimingsQry = "SELECT
																id,
																times
															FROM
																`slot_procedures`
															WHERE
																times != ''
															ORDER BY
																CONVERT(times,SIGNED) ASC";
										$rsProTimingsData = imw_query($strProTimingsQry);
										$arrProcTimings = array();
										$arrAddedTimings = array();
										if($rsProTimingsData)
										{
											$k = 0;
											while($arrTempProTimings = imw_fetch_array($rsProTimingsData))
											{
												if(!in_array($arrTempProTimings['times'], $arrAddedTimings))
												{
												  $arrProcTimings[$k]['id'] = $arrTempProTimings['id'];
												  $arrProcTimings[$k]['times'] = $arrTempProTimings['times'];
												  $arrAddedTimings[] = $arrTempProTimings['times'];
												  $k++;
												}
											}
										}
										$intProTimingsCnt = count($arrProcTimings);
										?>
									<table class="table table-bordered table-condensed adminnw">
										<thead>
										<tr>
											<th class="text-nowrap col-sm-2">Provider</th>
											<th class="col-sm-1"> Appt. Duration</th>
											<th class="col-sm-1">Expected Arrival (In mins.)</th>
											<th class="col-sm-1">Max. Allowed</th>
											<th class="col-sm-3">Procedure Message</th>
											<th style="width:5%">&nbsp;</th>
										</tr>
</thead>
										<?php
										$strDetQry ="SELECT
														sp1.*,
														sp2.times as procTime,
														u.fname,
														u.lname
													FROM
														slot_procedures sp1
														LEFT JOIN slot_procedures sp2 ON sp2.id = sp1.proc_time
														LEFT JOIN users u ON u.id = sp1.doctor_id
													WHERE
														(sp1.procedureId='$pro_id' || sp1.id=$pro_id)
													AND
														sp1.active_status!='del'
													ORDER BY
														doctor_id";
										$rsDetData = imw_query($strDetQry);

										$arrCarryForward = array();
										if($rsDetData)
										{
											if(imw_num_rows($rsDetData)>0)
											{
												while($arrDetData = imw_fetch_array($rsDetData))
												{
													$intProcTimeId = $arrDetData['procTime'];
													$intDetId = $arrDetData['id'];
													$strProcTimingsQry = "SELECT id,times FROM slot_procedures WHERE proc = '' ORDER BY id";
													$rsProcTimingsData = imw_query($strProcTimingsQry);

													if($arrDetData['doctor_id'] == "0")
													{
														$arrCarryForward["PROC_TIME"] = $arrDetData['proc_time'];
														$arrCarryForward["APPT_GAP"] = $arrDetData['intervals'];
														$arrCarryForward["MAX_ALLOWED"] = $arrDetData['max_allowed'];
														$arrCarryForward["PROC_MSG"] = $arrDetData['proc_mess'];
														$arrCarryForward["EXP_ARRIVAL_TIME"] = $arrDetData['exp_arrival_time'];
													}
													?>
											<input type="hidden" name="proc_spec_id[]" value="<?php echo $intDetId;?>" />
											<tr>
												<td class="text-left text-nowrap"><span>&nbsp;&nbsp;<?php if($arrDetData['doctor_id'] != "0"){ echo "<b>".stripslashes($arrDetData['lname'])." ".stripslashes($arrDetData['fname'])."</b>";}else{ echo "<b>All </b>";}?></span><input type="hidden" name="doctors_id[]" value="<?php echo $arrDetData['doctor_id'];?>"></td>
												<td class="text-left text-nowrap">
													<select  name="proc_time[]" id="<?php echo "times".$intDetId;?>" class="selectpicker" onChange="show_other(this.value, 'idtime<?php echo $intDetId;?>');" data-container="#selectpicker_parent" data-width="100%">
														<option value=""><?php echo imw_msg('drop_sel'); ?></option>
														<?php
														for($i = 0 ; $i < $intProTimingsCnt; $i++)
														{
															echo "<option value='".$arrProcTimings[$i]['id']."'";
															if($arrDetData['proc_time'] == $arrProcTimings[$i]['id']){ echo 'selected';}
															echo ">".$arrProcTimings[$i]['times']." Min</option>";
														}
														?>
														<option value="other">Other</option>
													</select>
													<span id="idtime<?php echo $intDetId;?>" style="display:none;width:40%;padding-top:1%" class=""><input type="text" class=" form-control pull-left" size="5" name="other_time<?php echo $intDetId;?>" value="">&nbsp;<b style="vertical-align:baseline">Min</b></span>
												</td>
												<td class="text-center">
													<input id="exp<?php echo $intDetId;?>" name="exp_arrival_time[]" onblur="javascript:check_val('exp<?php echo $intDetId;?>');"  type="text" class="form-control" value="<?php echo $arrDetData['exp_arrival_time'];?>" size="5">
												</td>
												<td class="text-center">
													<input id="maxs<?php echo $intDetId;?>" name="max_allow[]" type="text" class="form-control" value="<?php echo $arrDetData['max_allowed'];?>" size="5">
												</td>
												<td class="text-left text-nowrap">
													<textarea id="messes<?php echo $intDetId;?>" name="txt_comments[]" class="form-control" rows="2" cols="78"><?php echo stripslashes(str_replace("<br />","",$arrDetData['proc_mess'])); ?></textarea>
												</td>
												<td class="text-center">
													<?php
													if($arrDetData['doctor_id'] != "0"){
														?>
													<a href="#" style="<?php echo $disp;?>"  onclick="javascript:confirm_del('<?php echo $intDetId;?>')" ><img src="../../../../library/images/del.png" title="Delete" border="0"></a>
														<?php
													}
													?>
												</td>
											</tr>
													<?php
												}
											}
										}
										?>
										<tr style="display:none;" id="trdisp" >
											<input type="hidden" name="new_record" id="new_record" value="0">
											<td>&nbsp;&nbsp;<select name="new_phy_name" class="selectpicker" id="phy_name" data-container="#selectpicker_parent" data-width="90%" data-live-search="true">
													<option value=""><?php echo imw_msg('drop_sel'); ?></option>
													<?php
													$t_provider="SELECT
																	id,
																	fname,
																	mname,
																	lname
																FROM
																	`users`
																WHERE
																	Enable_Scheduler = 1
																AND
																	delete_status = 0
																AND
																	id NOT IN (
																				SELECT
																					doctor_id
																				FROM
																					`slot_procedures`
																				WHERE
																					procedureId = '".$pro_id."'
																				AND
																					active_status!='del'
																			   )
																ORDER BY lname";
													$sqlt_provider = @imw_query($t_provider);
													$numt_provider=@imw_num_rows($sqlt_provider);
													$str_ids="";
													while($vrst_provider=@imw_fetch_array($sqlt_provider))
													{
														$idss=$vrst_provider['id'];
														$provider = $vrst_provider['lname']." ".$vrst_provider['fname'];
														$pro=$procedure_specDetails->doctor_id;
														$select1 = (($pro == $idss)) ? "SELECTED" : "";
														echo "<option value=$idss $select1>$provider</option>";
													}
													?>
												</select>
											</td>
											<td class="text-left text-nowrap">
												<select name="fac_slot" id="fac_slots" class="selectpicker" onChange="show_other(this.value, 'idtime');" data-container="#selectpicker_parent" data-width="100%">
													<option value=""><?php echo imw_msg('drop_sel'); ?></option>
													<?php
													for($i = 0 ; $i < $intProTimingsCnt; $i++)
													{
														echo "<option value='".$arrProcTimings[$i]['id']."'";
														if($arrCarryForward['PROC_TIME'] == $arrProcTimings[$i]['id']){ echo 'selected';}
														echo ">".$arrProcTimings[$i]['times']." Min</option>";
													}
													?>
													<option value="other">Other</option>
												</select>
												<span id="idtime" style="display:none;width:40%" class=""><input  type="text" class=" form-control pull-left" size="5" name="other_time" value="">&nbsp;<b>Min</b></span>
											</td>
											<td class="text-center text-nowrap"><input name="new_exp_arrival_time" id="exp" type="text" class=" form-control" value="<?php echo $arrCarryForward["EXP_ARRIVAL_TIME"];?>" onblur="javascript:check_val('exp');" size="5"></td>
											<td class="text-center text-nowrap"><input name="new_max_allow" type="text" class=" form-control" value="<?php echo $arrCarryForward["MAX_ALLOWED"];?>" size="5"></td>
											<td class="text-left text-nowrap"><textarea name="new_txt_comments" class=" form-control" rows="2" cols="78"></textarea></td>
											<td class="text-center">
												<a href="#" style="<?php echo $disp;?>"  onclick="javascript:remove()" ><img src="../../../../library/images/del.png" title="Delete" border="0"></a>
											</td>
										 </tr>
									  </table>
										<?php
									  }
									  ?>


								</div>
					 <div class="pt10 text-center">
										 <?php
										if($pro_id != "" && $numt_provider > 0){
											?>
											<input type="button" id="new"  style="<?php echo $disp2;?>" class="btn btn-success" value="Add Provider" onClick="view_tr()" >
											<?php
										}
										?>
										<input type="button" value="Save" id="save" style="<?php echo $disp2;?>"  class="btn btn-success" onClick="javascript:save_action();">
										<input type="button" id="close" class="btn btn-danger" value="Close"  onClick="window.close();">
									</div>
						</div>
					</div>

				</div>




			</div>

			</form>
			<?php
			if($_REQUEST['alrdy_exist']=='yes')
			{
			?>
			<script>
				top.fancyAlert("Procedure Already Exist\n","imwemr","",top.document.getElementById("divCommonAlertMsgProcTemplate"),'','','','',true,10, 300, '');
			</script>
			<?php
			}
			if(isset($_REQUEST['refreshOpener']) && $_REQUEST['refreshOpener'] == 1)
			{
				?>
					<script>
						getProcedureTemplatesOnDML();
					</script>
				<?php
			}
			?>
		</div>
	</div>
<?php
	include('../../admin_footer.php');
 ?>
