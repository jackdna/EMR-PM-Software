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

File: procedure_glucoma_flow_sheet.php
Purpose: This file provide Glucoma Flow Sheet procedure note in GFS.
Access Type : Direct
*/

require_once('../../config/globals.php');
extract($_REQUEST);
//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';
include_once $GLOBALS['srcdir']."/classes/common_function.php";
include_once $GLOBALS['srcdir']."/classes/work_view/ChartGlucoma.php";
include_once $GLOBALS['srcdir']."/classes/SaveFile.php";

$pid = $_SESSION['patient'];
$auth_id = $_SESSION['authId'];

$glaucoma_obj = New ChartGlucoma($pid);

function proc_note_getEditCss_p2($elem_BP, $elem_BP_prev){
	$ret = "";
	if(!empty($elem_BP) && !empty($elem_BP_prev)){
		$ret = "prev_edit";
	}else if(!empty($elem_BP) && empty($elem_BP_prev)){
		$ret = "prev_add";
	}else if(empty($elem_BP) && !empty($elem_BP_prev)){
		$ret = "prv_del";
	}
	return $ret;
}


function proc_note_getEditCss($str_elm){
	$ret = "";	
	
	$var_prev_elm = $str_elm."_prev";	
	global $$str_elm, $$var_prev_elm;	
	$elem_BP = $$str_elm;
	if(isset($$var_prev_elm)) { $elem_BP_prev = $$var_prev_elm;}
	
	//echo " <br/> ".$elem_BP." - ".$elem_BP_prev;
	
	//*
	if(isset($elem_BP_prev) && $elem_BP!=$elem_BP_prev){	
	
		$ret = proc_note_getEditCss_p2($elem_BP, $elem_BP_prev);
	
	}
	return $ret;
}

function get_week($date1,$date2){
	$weeks="";
	if($date2 && $date1 && $date2!='--' && $date1!='--'){
		$daylen = 60*60*24;
		$date1=trim($date1);
		$date2=trim($date2);
		$date1." ".$date2."<br>";;
   		$days=ceil((strtotime($date1)-strtotime($date2))/$daylen);
		$weeks=floor($days/7);
		if($weeks==0 && $days!=0){
			$weeks=$days." <span style='font-size:10px;font-weight:;'>Day</span>";
		}
	}
	return $weeks;
}

function getPatentOct($patientID,$formID){
	$ret="N";
	if($patientID && $formID){
		$qryOctTest="SELECT oct_id from oct where form_id='".$formID."' AND patient_id='".$patientID."'";
		$resOctTest=imw_query($qryOctTest);
		if(imw_num_rows($resOctTest)>0){
			$ret="Y";	
		}
	}
	return $ret;
}
$arrUserName=array();
$qryUser="Select id,fname,lname from users order by id";
$resUser=imw_query($qryUser);
if(imw_num_rows($resUser)>0){
	while($rowUser=imw_fetch_assoc($resUser)){
		$ufname=substr($rowUser['fname'],0,1);
		$ulname=substr($rowUser['lname'],0,1);
		$uname=$ufname.$ulname;
		$arrUserName[$rowUser['id']]=$uname;
	}
}
//=====================Get All Procedures name in Array=================================================// 
$qryGetAllProc="Select procedure_id,procedure_name,ret_gl from operative_procedures where del_status!='1' and ret_gl=2";
$resGetAllProc=imw_query($qryGetAllProc)or die(imw_error());
$arrProc=array();
$arrProc_gro=array();
while($rowProc=imw_fetch_assoc($resGetAllProc)){
	$arrProc[$rowProc['procedure_id']]=$rowProc['procedure_name'];
}
//=======================================================================================================//

$patient_id = $glaucoma_obj->pid;
$form_id=$_SESSION['form_id'];

if($patient_id){
	$qryPatientData="SELECT concat(lname,', ',fname, ' ',UPPER(SUBSTRING(mname,1,1))) as patient_name,Date_Format(DOB ,'%m-%d-%y') as patient_dob,DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS pat_age, facilityPracCode from patient_data 
					LEFT JOIN pos_facilityies_tbl  ON pos_facility_id = default_facility 
					where id='".$patient_id."'";					
	$resPatientData=imw_query($qryPatientData);
	$rowPatientData=imw_fetch_assoc($resPatientData);
	$patientNameID=$rowPatientData['patient_name']." - ".$patient_id;
	
	if(!empty($rowPatientData["facilityPracCode"])){
		$patientNameID .= " (".$rowPatientData["facilityPracCode"].")";
	}
	
	
	$patient_dob_age="DOB: ".get_date_format($rowPatientData['patient_dob'],'mm-dd-yyyy')." (".$rowPatientData['pat_age'].")";
}

$arrPatientOD=array();
$arrPatientOS=array();
$arrPatientDOS=array();
$arr_IL=array();
$ct=$f_val=$s_val=0;
$dt=0;
$patientProcData="";
$qryPatientProc="Select cp.id,cp.patient_id,cp.form_id,cp.complication,cp.cmt,cp.proc_id,cp.site,cp.dx_code,cp.iop_type,cp.iop_od,cp.iop_os,cp.intravit_meds,cp.user_id,date_format(cmt.date_of_service,'".get_sql_date_format()."') as dos, cp.lids_opts from chart_procedures as cp inner join chart_master_table as cmt on (cmt.id=cp.form_id and cmt.patient_id=cp.patient_id) WHERE cp.patient_id='".$patient_id."' and (cp.site IN ('OU','OD','OS') OR cp.lids_opts!='') and cp.proc_note_masterId='0' AND cmt.finalize='1' order by cmt.date_of_service DESC";
$resPatientProc=imw_query($qryPatientProc);
if(imw_num_rows($resPatientProc)>0){
	while($rowPatientProc=imw_fetch_assoc($resPatientProc)){
		$rowPatientProcs[]=$rowPatientProc;
	}
	foreach($rowPatientProcs as $key => $rowPatientProc){
		$dt++;
		$comp="";
		$fontcolor="";
		
		//Check edited proc values -- 
		
		if(!empty($rowPatientProc['id'])){
			$sql="SELECT * FROM chart_procedures WHERE proc_note_masterId = '".$rowPatientProc['id']."' ";
			$row_prev=sqlQuery($sql);
			if($row_prev==false){
				unset($row_prev);
			}
		}
		
		//Check edited proc values -- 		
		
		$comp=($rowPatientProc['complication']=="1")?"Y":"N";
		if($rowPatientProc['complication']=="1"){
			$fontcolor="color:#CA4E56";
		}
		if($arrProc[$rowPatientProc['proc_id']]){
			$arr_IL[$rowPatientProc['id']]=$arrProc_gro[$rowPatientProc['proc_id']];
			$date_f=$date_s=$mm1=$dd1=$yy1=$mm2=$dd2=$yy2=$week_diff="";
			list($mm1,$dd1,$yy1)=explode("-",$rowPatientProc['dos']);
			list($mm2,$dd2,$yy2)=explode("-",$rowPatientProcs[$key+1]['dos']);
			$date_f=$yy1."-".$mm1."-".$dd1;
			$date_s=$yy2."-".$mm2."-".$dd2;
			$week_diff=get_week($date_f,$date_s);if($week_diff==0){$week_diff="";}
			$arrPatientDOS[$rowPatientProc['id']]='<td class="pl5 botborder" style="width:120px;'.$fontcolor.'">'.$rowPatientProc['dos'].'</td>';		
				
			if($rowPatientProc['site']=="OD" || $rowPatientProc['site']=="OU" || strpos($rowPatientProc['lids_opts'],"RUL") !== false || strpos($rowPatientProc['lids_opts'],"RLL") !== false){
				
				$iopod = (!empty($rowPatientProc['iop_od'])) ? $rowPatientProc['iop_type']." ".$rowPatientProc['iop_od'] : "";
				
				
				//--
				
				$css_col_od1 = $css_col_od2 = $css_col_od3 = $css_col_od4 = $css_col_od5 = $css_col_od6 = $css_col_od7 = $css_col_od8 = "";
				if(isset($row_prev)){
					
					//echo "<br/>".$rowPatientProc['intravit_meds']." ------ ".$row_prev['intravit_meds']."<br/>";
					
					if($rowPatientProc['proc_id'] != $row_prev['proc_id']){	$css_col_od1 = proc_note_getEditCss_p2($rowPatientProc['proc_id'], $row_prev['proc_id']); }
					if($rowPatientProc['intravit_meds'] != $row_prev['intravit_meds']){ $css_col_od2 = proc_note_getEditCss_p2($rowPatientProc['intravit_meds'], $row_prev['intravit_meds']); }
					if($rowPatientProc['complication']!=$row_prev['complication']){ $css_col_od3 = proc_note_getEditCss_p2($rowPatientProc['complication'], $row_prev['complication']); }
					if($rowPatientProc['dx_code']!=$row_prev['dx_code']){ $css_col_od4 = proc_note_getEditCss_p2($rowPatientProc['dx_code'], $row_prev['dx_code']); }
					if($rowPatientProc['cmt']!=$row_prev['cmt']){ $css_col_od6 = proc_note_getEditCss_p2($rowPatientProc['cmt'], $row_prev['cmt']); }
					
					$iopod_prev = (!empty($row_prev['iop_od'])) ? $row_prev['iop_type']." ".$row_prev['iop_od'] : "";
					if($iopod!=$iopod_prev){ $css_col_od7 = proc_note_getEditCss_p2($iopod, $iopod_prev); }	
					if($rowPatientProc['user_id']!=$row_prev['user_id']){ $css_col_od8 = proc_note_getEditCss_p2($rowPatientProc['user_id'], $row_prev['user_id']);  }
					
				}
				
				//--
				
				
				$arrPatientOD[$rowPatientProc['id']]["OD"]='
				 
				<td style="'.$fontcolor.'" class="'.$css_col_od1.'">'.$arrProc[$rowPatientProc['proc_id']].'</td>
				<td style="'.$fontcolor.'" class="'.$css_col_od2.'">'.str_ireplace("|~|","<br>",$rowPatientProc['intravit_meds']).'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_od3.'">'.($comp).'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_od4.'">'.$rowPatientProc['dx_code'].'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_od5.'">'.getPatentOct($patient_id,$current_form_id).'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_od6.'">'.$rowPatientProc['cmt'].'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_od7.'">'.$iopod.'</td>
				<td style="'.$fontcolor.'" class=" text-center '.$css_col_od8.'">'.$arrUserName[$rowPatientProc['user_id']].'</td>	
				';
			}
			if($rowPatientProc['site']=="OS" || $rowPatientProc['site']=="OU" || strpos($rowPatientProc['lids_opts'],"LUL") !== false || strpos($rowPatientProc['lids_opts'],"LLL") !== false){
				
				$iopos = (!empty($rowPatientProc['iop_os'])) ? $rowPatientProc['iop_type']." ".$rowPatientProc['iop_os'] : "";
				
				//--
				
				$css_col_os1 = $css_col_os2 = $css_col_os3 = $css_col_os4 = $css_col_os5 = $css_col_os6 = $css_col_os7 = $css_col_os8 = "";
				if(isset($row_prev)){
				
					if($rowPatientProc['proc_id'] != $row_prev['proc_id']){	$css_col_os1 = proc_note_getEditCss_p2($rowPatientProc['proc_id'], $row_prev['proc_id']); }
					if($rowPatientProc['intravit_meds'] != $row_prev['intravit_meds']){ $css_col_os2 = proc_note_getEditCss_p2($rowPatientProc['intravit_meds'], $row_prev['intravit_meds']); }
					if($rowPatientProc['complication']!=$row_prev['complication']){ $css_col_os3 = proc_note_getEditCss_p2($rowPatientProc['complication'], $row_prev['complication']); }
					if($rowPatientProc['dx_code']!=$row_prev['dx_code']){ $css_col_os4 = proc_note_getEditCss_p2($rowPatientProc['dx_code'], $row_prev['dx_code']); }
					if($rowPatientProc['cmt']!=$row_prev['cmt']){ $css_col_os6 = proc_note_getEditCss_p2($rowPatientProc['cmt'], $row_prev['cmt']); }
					
					$iopos_prev = (!empty($row_prev['iop_os'])) ? $row_prev['iop_type']." ".$row_prev['iop_os'] : "";
					if($iopos!=$iopos_prev){ $css_col_os7 = proc_note_getEditCss_p2($iopos, $iopos_prev); }	
					if($rowPatientProc['user_id']!=$row_prev['user_id']){ $css_col_os8 = proc_note_getEditCss_p2($rowPatientProc['user_id'], $row_prev['user_id']);  }
				
				}
				
				//--
				
				
				$arrPatientOS[$rowPatientProc['id']]["OS"]='
				<td  style="'.$fontcolor.'" class="'.$css_col_os1.' ">'.$arrProc[$rowPatientProc['proc_id']].'</td>
				<td style="'.$fontcolor.'" class="'.$css_col_os2.' ">'.str_ireplace("|~|","<br>",$rowPatientProc['intravit_meds']).'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_os3.' ">'.($comp).'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_os4.' ">'.$rowPatientProc['dx_code'].'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_os5.' ">'.getPatentOct($patient_id,$current_form_id).'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_os6.' ">'.$rowPatientProc['cmt'].'</td>
				<td style="'.$fontcolor.'" class="text-center '.$css_col_os7.' ">'.$iopos.'</td>
				<td style="'.$fontcolor.'" class=" text-center '.$css_col_os8.' ">'.$arrUserName[$rowPatientProc['user_id']].'</td>	
				';
			}
		}
	}
}else{
	$patientProcData="<tr><td colspan='17' class='text-center text-muted'>No record found</td></tr>";
}


$cnt=0;
foreach($arrPatientDOS as $form_id => $procVal){
	$patientProcData.='<tr>'.$arrPatientDOS[$form_id];
	if($arrPatientOD[$form_id]['OD']){
		$patientProcData.=$arrPatientOD[$form_id]['OD'];
	}else{
		$patientProcData.='
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>';
	}
	
	if($arrPatientOS[$form_id]['OS']){
		$patientProcData.=$arrPatientOS[$form_id]['OS'];
	}else{
		$patientProcData.='
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class="text-center">&nbsp;</td>
		<td class=" text-center">&nbsp;</td>';
	}
	
	$patientProcData.="</tr>";	
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Patient Refractive Sheet</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/gfs.css"/>
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]--> 
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/amcharts/amcharts.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/amcharts/serial.js" type="text/javascript"></script>
		<script>
			$(document).ready(function(){
				var innerDim = window.opener.top.innerDim();
				if(innerDim['w'] > 1600) innerDim['w'] = 1600;
				if(innerDim['h'] > 900) innerDim['h'] = 900;
				window.resizeTo(innerDim['w'],innerDim['h']);
				brows	= get_browser();
				if(brows!='ie') innerDim['h'] = innerDim['h']-35;
				var result_div_height = innerDim['h']-210;
			});
		
		</script>
		<style>
			.process_loader {
				border: 16px solid #f3f3f3;
				border-radius: 50%;
				border-top: 16px solid #3498db;
				width: 80px;
				height: 80px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
				display: inline-block;
			}
			.adminbox{min-height:inherit}
			.adminbox label{overflow:initial;}
			.adminbox .panel-body{padding:5px}
			.adminbox div:nth-child(odd) {padding-right: 1%;}
			.od{color:blue;}
			.os{color:green;}
			.ou{color:#9900cc;}
			.checkbox label::after{padding-top:0px}
			
		</style>
	</head>
	<body>
		<div class="mainwhtbox">
			<form name="retinal_form" action="retinal_flow_sheet.php" method="post">
				<div class="row">
					<div class="col-sm-12 purple_bar">
						<div class="row">
							<div class="col-sm-3">
								<label>Glaucoma Flow Sheet</label>
							</div>	
							<div class="col-sm-6 text-center">
								<label><?php echo $patientNameID; ?></label>
							</div>	
							<div class="col-sm-3 text-right">
								<label><?php echo $patient_dob_age; ?></label>
							</div>	
						</div>
					</div>
					<div class="col-sm-12 pt10">
						<div class="row">
							<table class="table table-bordered table-striped">
								<tr class="text-center">
									<td colspan="9" class="od">OD</td>
									<td colspan="9" class="os">OS</td>
								</tr>
								<tr class="grythead">
									<!-- OD -->
									<td>Date</td>
									<td>&nbsp;&nbsp;&nbsp;Procedure</td>
									<td>Med</td>
									<td class="text-center">Comp</td>
									<td class="text-center">Dx.</td>
									<td class="text-center">OCT</td>
									<td class="text-center">CMT</td>
									<td class="text-center">IOP</td>
									<td class="text-center">Physician</td>
									
									<!-- OS -->
									<td>Procedure</td>
									<td class="">Med</td>
									<td class="text-center">Comp</td>
									<td class="text-center">Dx.</td>
									<td class="text-center">OCT</td>
									<td class="text-center">CMT</td>
									<td class="text-center">IOP</td>
									<td class="text-center">Physician</td>	
								</tr>
								<?php 
								if($patientProcData){
									echo $patientProcData;
								}else{
									echo "<tr><td colspan='17' class='text-center text-muted'>No record found</td></tr>";
								}
								?> 	
							</table>
						</div>	
					</div>	
				</div>
			</form>	
		</div>
	</body>	
</html>  