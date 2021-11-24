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

File: procedure_flow_sheet.php 
Purpose: This file provides Procedural Flow Sheet section in work view.
Access Type : Direct
*/

include_once(dirname(__FILE__)."/../../config/globals.php");
//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

// -- Deleteing records
if($_REQUEST['delProc_id'] && $_REQUEST['del_from']=="PFS"){
	$sql = "UPDATE chart_procedures SET deleted_by = '".$_SESSION["authId"]."', exam_date= NOW() WHERE id IN(".$_REQUEST['delProc_id'].")  ";
	$res=imw_query($sql);
	if($res){
		echo "done";
	}else{echo imw_error();}
	die();
}
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
$qryGetAllProc="Select procedure_id,procedure_name,ret_gl from operative_procedures where del_status!='1'  and ret_gl!=1"; //and ret_gl!=2:: AK:> I know it is tagged as GL but, IK think they should show at PFS i.e. at one place they can see all Procedures done on the Pt.
$resGetAllProc=imw_query($qryGetAllProc)or die(imw_error());
$arrProc=array();
$arrProc_gro=array();
while($rowProc=imw_fetch_assoc($resGetAllProc)){
	$arrProc[$rowProc['procedure_id']]=$rowProc['procedure_name'];
}
//=======================================================================================================//

$patient_id=$_SESSION['patient'];
$form_id=$_SESSION['form_id'];

//
$oSaveFile = new SaveFile($patient_id);

if($patient_id){
	$qryPatientData="SELECT concat(lname,', ',fname, ' ',UPPER(SUBSTRING(mname,1,1))) as patient_name,Date_Format(DOB ,'".get_sql_date_format('','y','-')."') as patient_dob,DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS pat_age, facilityPracCode from patient_data 
					LEFT JOIN pos_facilityies_tbl  ON pos_facility_id = default_facility 
					where id='".$patient_id."'";					
	$resPatientData=imw_query($qryPatientData);
	$rowPatientData=imw_fetch_assoc($resPatientData);
	$patientNameID=$rowPatientData['patient_name']." - ".$patient_id;
	
	if(!empty($rowPatientData["facilityPracCode"])){
		$patientNameID .= " (".$rowPatientData["facilityPracCode"].")";
	}
	
	
	$patient_dob_age="DOB: ".$rowPatientData['patient_dob'];//." (".$rowPatientData['pat_age'].")";
}

$arrPatientOD=array();
$arrPatientOS=array();
$arrPatientDOS=array();
$arrPatientDOS_botox=$arrPatientOD_botox=$arrPatientOS_botox=array();
$arr_IL=array();
$ct=$f_val=$s_val=0;
$dt=0;
$patientProcData="";
$qryPatientProc="Select cp.id,cp.patient_id,cp.form_id,cp.complication,cp.cmt,cp.proc_id,cp.site,cp.dx_code,cp.iop_type,cp.iop_od,
				cp.iop_os,cp.intravit_meds,cp.user_id,date_format(cmt.date_of_service,'".get_sql_date_format('','Y')."') as dos, 
				cpb.btx_usd, DATE_FORMAT(cp.exam_date,'".get_sql_date_format()."')  AS exam_date, cpb.drw_path, cp.comments , cp.lids_opts
				from chart_procedures as cp 
				LEFT JOIN chart_procedures_botox as cpb ON cp.id = cpb.chart_proc_id
				inner join chart_master_table as cmt on (cmt.id=cp.form_id and cmt.patient_id=cp.patient_id) 
				WHERE cp.patient_id='".$patient_id."' and (cp.site IN ('OU','OD','OS') OR cp.lids_opts!='' OR cpb.chart_proc_id IS NOT NULL) and cp.proc_note_masterId='0' 
				AND cmt.finalize='1' and cp.deleted_by='0' order by cp.exam_date DESC, cmt.date_of_service DESC"; 

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
			$sql="SELECT * FROM chart_procedures c1 
					LEFT JOIN chart_procedures_botox c2 ON c1.id = c2.chart_proc_id 
					WHERE proc_note_masterId = '".$rowPatientProc['id']."' ";
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
			$tmp_dt = !empty($rowPatientProc['exam_date']) ? $rowPatientProc['exam_date']:$rowPatientProc['dos'];
			$tmp_dt_a = !empty($rowPatientProc[$key+1]['exam_date']) ? $rowPatientProc[$key+1]['exam_date']:$rowPatientProc[$key+1]['dos'];
			list($mm1,$dd1,$yy1)=explode("-",$tmp_dt);
			list($mm2,$dd2,$yy2)=explode("-",$tmp_dt_a);
			$date_f = getDateFormatDB($tmp_dt);
			$date_s = getDateFormatDB($tmp_dt_a);
			$week_diff=get_week($date_f,$date_s);if($week_diff==0){$week_diff="";}
			$arrPatientDOS[$rowPatientProc['id']]='<td class="text-center"><div class="checkbox checkbox-inline"><input type="checkbox" id="chk_box_'.$rowPatientProc['id'].'" value="'.$rowPatientProc['id'].'" class="chk_box"><label for="chk_box_'.$rowPatientProc['id'].'"></label></div></td><td style="'.$fontcolor.'">'.$tmp_dt.'</td>';		
			
			//Bottox flg--
			$flgBottoxOn=0;
			if((strpos(strtolower($arrProc[$rowPatientProc['proc_id']]),"botox")!==false)&&!empty($rowPatientProc['btx_usd'])){ $flgBottoxOn=1; }
			//Bottox flg--

			if($rowPatientProc['site']=="OD" || $rowPatientProc['site']=="OU" || $flgBottoxOn==1 || strpos($rowPatientProc['lids_opts'],"RUL") !== false || strpos($rowPatientProc['lids_opts'],"RLL") !== false){
				
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
				<td style="'.$fontcolor.'" class="text-center'.$css_col_od3.'">'.($comp).'</td>
				<td style="'.$fontcolor.'" class="text-center'.$css_col_od4.'">'.$rowPatientProc['dx_code'].'</td>
				<td style="'.$fontcolor.'" class="text-center'.$css_col_od5.'">'.getPatentOct($patient_id,$current_form_id).'</td>
				<td style="'.$fontcolor.'" class="text-center'.$css_col_od6.'">'.$rowPatientProc['cmt'].'</td>
				<td style="'.$fontcolor.'" class="text-center'.$css_col_od7.'">'.$iopod.'</td>
				<td style="'.$fontcolor.'" class=" text-center'.$css_col_od8.'">'.$arrUserName[$rowPatientProc['user_id']].'</td>	
				';
			}
			if($rowPatientProc['site']=="OS" || $rowPatientProc['site']=="OU" || $flgBottoxOn==1 || strpos($rowPatientProc['lids_opts'],"LUL") !== false || strpos($rowPatientProc['lids_opts'],"LLL") !== false){
				
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
				<td style="'.$fontcolor.'" class=" '.$css_col_os2.' ">'.str_ireplace("|~|","<br>",$rowPatientProc['intravit_meds']).'</td>
				<td style="'.$fontcolor.'" class=" text-center '.$css_col_os3.' ">'.($comp).'</td>
				<td style="'.$fontcolor.'" class=" text-center '.$css_col_os4.' ">'.$rowPatientProc['dx_code'].'</td>
				<td style="'.$fontcolor.'" class=" text-center '.$css_col_os5.' ">'.getPatentOct($patient_id,$current_form_id).'</td>
				<td style="'.$fontcolor.'" class="text-center  '.$css_col_os6.' ">'.$rowPatientProc['cmt'].'</td>
				<td style="'.$fontcolor.'" class="text-center  '.$css_col_os7.' ">'.$iopos.'</td>
				<td style="'.$fontcolor.'" class=" text-center  '.$css_col_os8.' ">'.$arrUserName[$rowPatientProc['user_id']].'</td>	
				';
			}
			
			// Botox ---			
			if($flgBottoxOn==1){
				
				$strBotox=$strBotoxOD=$strBotoxOS=$strBtxThumb="";
				$strBtxDos="DOS - ".$rowPatientProc['exam_date'].", ";
				if($rowPatientProc['drw_path']!=""){
					$tmp_pth=$oSaveFile->getFilePath($rowPatientProc['drw_path'],'i');
					if(file_exists($tmp_pth)){
						$tmp_pth_s=str_replace(".png", "_s.png", $tmp_pth);
						$tmp_pth = $oSaveFile->getFilePath($rowPatientProc['drw_path'],'w');
						if(file_exists($tmp_pth_s)){  $tmp_pth_s=str_replace(".png", "_s.png", $tmp_pth); }else{ $tmp_pth_s=$tmp_pth; }
						$strBtxThumb = "<img src='".$tmp_pth_s."' width=\"75\" alt=\"botox\" onclick='showBotoxImg(\"".$tmp_pth."\")' style='cursor:pointer;'>";
					}
				}
				$strBotox.=$strBtxDos;				
				$strBotox.= "Botox - ".$rowPatientProc['btx_usd'];
				if(!empty($rowPatientProc['comments'])){  $strBotox.= ", Comments - ".$rowPatientProc['comments'];  }
				
				$css_col_od9="";
				if(isset($row_prev)){		
					if($rowPatientProc['btx_usd'] != $row_prev['btx_usd']){ $css_col_od9=proc_note_getEditCss_p2($rowPatientProc['btx_usd'], $row_prev['btx_usd']);  }
				}
				
					$strBotoxOD=$strBotox;			
				
				$arrPatientDOS_botox[$rowPatientProc['id']]='<td class="text-center"></td><td style="'.$fontcolor.'">'.$strBtxThumb.'</td>';
				$arrPatientOD_botox[$rowPatientProc['id']]["OD"]='<td style="'.$fontcolor.'" class=" '.$css_col_od9.'" colspan="16" >'.$strBotoxOD.'</td>';
			}
			// Botox ---	
		}
	}
}else{
	$patientProcData="<tr><td colspan='19' class='text-center'>No record found</td></tr>";
}


$cnt=0;
foreach($arrPatientDOS as $form_id => $procVal){
	$patientProcData.='<tr >'.$arrPatientDOS[$form_id];
	if($arrPatientOD[$form_id]['OD']){
		$patientProcData.=$arrPatientOD[$form_id]['OD'];
	}else{
		$patientProcData.='
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>';
	}
	
	if($arrPatientOS[$form_id]['OS']){
		$patientProcData.=$arrPatientOS[$form_id]['OS'];
	}else{
		$patientProcData.='
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>';
	}
	
	$patientProcData.="</tr>";	
	
	//Botox line --
	if(isset($arrPatientDOS_botox[$form_id])&&!empty($arrPatientDOS_botox[$form_id])){
		$patientProcData.="<tr valign=\"top\">";
		$patientProcData.=$arrPatientDOS_botox[$form_id];
		$patientProcData.=$arrPatientOD_botox[$form_id]['OD'];
		$patientProcData.="</tr>";
	}
	//Botox line --
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Procedure Flow Sheet</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		
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
		</style>
	</head>   
	<body>
		<div class="mainwhtbox pd10">
			<div class="row">
				<form name="retinal_form" action="retinal_flow_sheet.php" method="post">
					<div class="col-sm-12 purple_bar">
						<div class="row">
							<div class="col-sm-3">
								<label>Procedure Flow Sheet</label>	
							</div>
							<div class="col-sm-6 text-center">
								<label><?php echo $patientNameID; ?></label>	
							</div>
							<div class="col-sm-3 text-right">
								<label><?php echo $patient_dob_age; ?></label>	
							</div>
						</div>
					</div>

					<div class="col-sm-12" id="result_div"> 
						<div class="row">
							<table class="table table-striped table-bordered">
              	<thead>
                <tr class="grythead vlign-top">
                	<td class="text-center" rowspan="2">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" class="main_chk_box" id="main_chk_box">		
											<label for="main_chk_box"></label>
										</div>	
									</td>
									<td rowspan="2" class="text-center">Date</td>
                  <td colspan="8" class="text-center">OD</td>
                  <td colspan="8" class="text-center">OS</td>
               	</tr>
                
								<tr class="grythead vlign-top">
									<td class="text-center">Procedure</td>
									<td class="text-center">Med</td>
									<td class="text-center">Comp</td>
									<td class="text-center">Dx.</td>
									<td class="text-center">OCT</td>
									<td class="text-center">CMT</td>
									<td class="text-center">IOP</td>
									<td class="text-center">Physician</td>
									
									<td class="text-center">Procedure</td>
									<td class="text-center">Med</td>
									<td class="text-center">Comp</td>
									<td class="text-center">Dx.</td>
									<td class="text-center">OCT</td>
									<td class="text-center">CMT</td>
									<td class="text-center">IOP</td>
									<td class="text-center">Physician</td>
								</tr>
								</thead>
                <tbody>
								<?php 
									$rec=1;
									if($patientProcData){
										echo $patientProcData;
									}else{
										$rec=0;
										echo "<tr><td colspan='19' class='text-center'>No record found</td></tr>";
									}
								?>
                </tbody> 	
							</table>
						</div>	
					</div>		
				</form>
			</div>
  	</div>
    
    <?php if($rec != 0){ ?>
    	<div class="mainwhtbox pd10">
        <div class="row">
					<div class="col-sm-12 text-center ad_modal_footer" id="module_buttons">
						<input type="button" value="Delete" class="btn btn-danger" id="del_button" />	
					</div>
       	</div>
    	</div>
  	<?php } ?>	
		
	</body>    
</html>
<script type="text/javascript">
	$(document).ready(function(){
		$("#main_chk_box").on('click', function(){
			var main_chkbox = $(this).prop("checked");
			$(".chk_box").each(function(index, element){$(element).prop("checked",main_chkbox);});
		});
		
		$("#del_button").click(function(){var checked_id="";
			$(".chk_box").each(function(index, element){
				if($(this).is(":checked")){
					checked_id+=$(this).val()+", ";
				}
			});
			if(checked_id!=''){
				checked_id = checked_id.substr(0,checked_id.length-2);
				fancyConfirm("Are you sure you want to delete?",'',"window.deleteModifiers('"+checked_id+"')");
			}else{fAlert("No Record Selected.");}
		});
		
		
		
		
	});
	
	function init()
	{
			var h = window.innerHeight;
			var headerH = $(".purple_bar").outerHeight(true);
			var footerH = 0;
			if( $("#module_buttons").length > 0 ){
				footerH = $("#module_buttons").parent().parent().outerHeight(true);
			}
			
			var hh = h - (headerH + footerH +50);
			$("#result_div").css({'min-height':hh+'px', 'max-height':hh+'px', 'overflow':'auto'});
			
	}
	
	$(window).load(function(){
			popup_resize(1600,900,0.9);
			init();
	});	
	$(window).resize(function(e) {
  	init();  
  });
	
	
	function deleteModifiers(checked_id){
		if(checked_id){
			$.get("procedure_flow_sheet.php?del_from=PFS&delProc_id="+checked_id, function(data){
				if(data=="done"){
					window.location.reload();
				}else{fAlert("SQL ERROR: "+data);}
			});
		}
	}
	
	
	function showBotoxImg(src){
		if(typeof(src)!="undefined" && src!=""){
			$('#divBtxImg').remove();
			$("body").append("<div id=\"divBtxImg\"><div class=\"drghnd\"><label onclick=\"$('#divBtxImg').remove();\">close</label></div><image src=\""+src+"\" width=\"400\" ></div>");
			$('#divBtxImg').draggable({handle:".drghnd"}); //
		}
	}			
</script>