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

File: retinal_flow_sheet.php
Purpose: This file provides Retinal Flow Sheet in work view.
Access Type : Direct

*/

include_once(dirname(__FILE__)."/../../config/globals.php");

//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

if(empty($_GET['fltr']) === true){
	$_GET['fltr'] = '2';
}

$library_path = $GLOBALS['webroot'].'/library';
include_once $GLOBALS['srcdir']."/classes/work_view/wv_functions.php";

//$operator_id=$_SESSION['logged_user_type'];
$operator_id = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
$form_finilize_id=$_SESSION['finalize_id'];
if(!$form_finilize_id){
	$form_finilize_id=$_SESSION['form_id'];
}

if($_REQUEST['delProc_id'] && $_REQUEST['del_from']=="RFS"){
	$sql = "UPDATE chart_procedures SET deleted_by = '".$_SESSION["authId"]."', exam_date= NOW() WHERE id IN(".$_REQUEST['delProc_id'].")  ";
	$res=imw_query($sql);
	if($res){
		echo "done";
	}else{echo imw_error();}
	die();
}


if($_POST["rfsop"] == "save_comm"){ //savecommentts
	$od_comm = $_POST["d"];
	$os_comm = $_POST["s"];

	$rfs_comm = serialize(array($od_comm,$os_comm));

	$sql = "SELECT count(*) as num FROM chart_pt_data WHERE patient_id = '".$_SESSION['patient']."' ";
	$row = sqlQuery($sql);
	if($row!=false && $row["num"]>0){
		$sql = "update chart_pt_data SET rfs_comm='".imw_real_escape_string($rfs_comm)."' WHERE patient_id = '".$_SESSION['patient']."'  ";
		$row = sqlQuery($sql);

	}else{
		$sql = "Insert Into chart_pt_data SET rfs_comm='".imw_real_escape_string($rfs_comm)."', patient_id = '".$_SESSION['patient']."'   ";
		$row = sqlQuery($sql);
	}

	exit("Done");
}


if($_REQUEST['inj_count']=='1' && $_REQUEST['inj']){
	$inj_val=$_REQUEST['inj'];
	$injections=explode("~||~",$inj_val);
	foreach($injections as $injectionNameVal){//echo $injectionNameVal;
		list($injName,$injCount,$injPrevCount,$site)=explode("~=~",$injectionNameVal);
		if($injName && $injCount && $_SESSION['patient']){
			$qryInj="INSERT INTO patient_procedure_injections set patient_id='".$_SESSION['patient']."',injection='".$injName."',count='".$injCount."',prev_count_val='".$injPrevCount."',form_id='".$form_finilize_id."',operator_id='".$operator_id."',site='".$site."',datatime=now()";
			$resInj=imw_query($qryInj);
		}
	}
	echo "Record Updated";
	die();
}
if($_REQUEST['show_med_history']=='y' && $_REQUEST['inj_name']){
	$injName=addslashes(trim($_REQUEST['inj_name']));
	$ret_val='
		<div class="row">
			<div class="col-sm-12 text-center">
				<div class="alert alert-info">No record</div>
			</div>
		</div>';
	$qryInj="SELECT * from patient_procedure_injections WHERE patient_id='".$_SESSION['patient']."' AND injection='".$injName."'  AND site='".$_REQUEST['site_value']."' order by id DESC ";
	$resInj=imw_query($qryInj);
	if(imw_num_rows($resInj)>0){$i=1;
		$ret_val_table='';
		$ret_val_table.='<div class="row"><div class="col-sm-12"><table class="table table-bordered table-striped" cellpadding="2" cellspacing="0">
				<tr class="grythead"><th >#</th><th>Injection Name</th><th >Count</th><th>Previous Count</th></tr>';
		while($rowResInj=imw_fetch_assoc($resInj)){
			$ret_val_table.='<tr><td >'.$i.'. </td>';
			$ret_val_table.='<td class="text-left">'.$rowResInj['injection'].'</td>';
			$ret_val_table.='<td class="text-center">'.$rowResInj['count'].'</td>';
			$ret_val_table.='<td class="text-center" style="color:#CA4E56;">'.$rowResInj['prev_count_val'].'</td>';
			$ret_val_table.='</tr>';
			$i++;
		}
		$ret_val_table.='</table></div></div>';
	}

	if(empty($ret_val_table) === false){
		$ret_val = $ret_val_table;
	}

	$ret .= $ret_val.'~~~~'.$injName.'';
	echo $ret;
	die();
}
function check_inj($form_id,$injection,$pt_id,$site){
	$retMedCount=$qry_form_id="";
	if($form_id){
		$qry_form_id=" AND form_id ='".$form_id."'";
	}
	$qryCheckInj="Select count from patient_procedure_injections where 1=1 ".$qry_form_id."  AND injection='".$injection."' AND patient_id='".$pt_id."' AND site='".$site."' Order BY id DESC LIMIT 1";
	$resCheckInj=imw_query($qryCheckInj);
	if(imw_num_rows($resCheckInj)>0){
		$rowCheckInj=imw_fetch_assoc($resCheckInj);
		$retMedCount=$rowCheckInj['count'];
	}
	return $retMedCount;
}
function getAddVal($pt_id,$site,$injection){
		$qryCheckInj="Select count from patient_procedure_injections WHERE injection='".$injection."' AND patient_id='".$pt_id."' AND site='".$site."' Order BY id DESC LIMIT 1";
	$resCheckInj=imw_query($qryCheckInj);
	if(imw_num_rows($resCheckInj)>0){
		$rowCheckInj=imw_fetch_assoc($resCheckInj);
		$retMedCount=$rowCheckInj['count'];
	}
	return $retMedCount;

}
function get_week($date1,$date2){
	//echo $date1." - ".$date2."<br>";
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

function getPatientOct($patientID,$formID){
	$ret="";$va_od=$va_os="";
	if($patientID && $formID){
		$qryOctTest="SELECT sel_od AS vis_dis_od_sel_1, sel_os AS vis_dis_os_sel_1, txt_od AS vis_dis_od_txt_1, txt_os AS vis_dis_os_txt_1
				from chart_vis_master c1
				INNER JOIN chart_acuity c2 ON c1.id = c2.id_chart_vis_master
				where c1.form_id='".$formID."' AND c1.patient_id='".$patientID."' AND c2.sec_name='Distance' AND c2.sec_indx='1' ";
		$resOctTest=imw_query($qryOctTest)or die(imw_error());
		if(imw_num_rows($resOctTest)>0){
			//$ret="Y";
			$rowOctTest=imw_fetch_assoc($resOctTest);
			$va_od=$rowOctTest['vis_dis_od_sel_1']." ".$rowOctTest['vis_dis_od_txt_1'];
			$va_os=$rowOctTest['vis_dis_os_sel_1']." ".$rowOctTest['vis_dis_os_txt_1'];
			$ret=$va_od."!!-!!".$va_os;
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

$qryGetAllProc="Select procedure_id,procedure_name,ret_gl from operative_procedures where del_status!='1' and ret_gl=1";
$resGetAllProc=imw_query($qryGetAllProc)or die(imw_error());
$arrProc=array();
$arrProc_gro=array();
while($rowProc=imw_fetch_assoc($resGetAllProc)){
	$arrProc[$rowProc['procedure_id']]=$rowProc['procedure_name'];
}

//=====================Get Medications name in Array which is in Ret. Injection ====================//
$qryMedcation="Select id,medicine_name,ret_injection from medicine_data where ret_injection='1' AND del_status = '0' ORDER BY ret_injection DESC";
$resMedcation=imw_query($qryMedcation) or die(imw_error());
$arrMedicationInj=array();
while($rowMedication=imw_fetch_assoc($resMedcation)){
	$arrMedicationInj[$rowMedication['medicine_name']]=$rowMedication['medicine_name'];
}
//pre($arrMedicationInj);
//=======================================================================================================//
$patient_id=$_SESSION['patient'];
$form_id=$_SESSION['form_id'];

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
$arr_IL=array();
$arrInjGroup=array();
$arrInjdateOD=array();
$arrInjdateOS=array();
$ct=$f_val=$s_val=0;
$arrCountMedInj=array();
$dtOD=0;
$dtOS=0;

$patientProcData="";
$qryPatientProc="Select cp.id,cp.patient_id,cp.form_id,cp.complication,cp.cmt,cp.proc_id,cp.site,cp.dx_code,cp.iop_type,cp.iop_od,cp.iop_os,cp.intravit_meds,cp.user_id,date_format(cmt.date_of_service,'".get_sql_date_format('','Y','-')."') as dos,cmt.id as form_id, cp.deleted_by, cp.lids_opts,cp.laser_procedure_note,cp.spot_duration,cp.spot_size,cp.power,cp.shots,cp.total_energy,cp.degree_of_opening,cp.exposure,cp.count,date_format(cp.exam_date,'".get_sql_date_format('','Y','-')."') as exmdt from chart_procedures as cp inner join chart_master_table as cmt on (cmt.id=cp.form_id and cmt.patient_id=cp.patient_id) WHERE cp.patient_id='".$patient_id."' and (cp.site IN ('OU','OD','OS') OR cp.lids_opts!='') AND cmt.finalize='1' order by cp.exam_date DESC, cmt.date_of_service DESC";
$resPatientProc=imw_query($qryPatientProc);
if(imw_num_rows($resPatientProc)>0){
	while($rowPatientProc=imw_fetch_assoc($resPatientProc)){
		if($arrProc[$rowPatientProc['proc_id']]){

			//filterDeleted_by --
				$css_del_row="";
				if(!empty($rowPatientProc["deleted_by"])){
					if(!isset($_GET["fltr"]) || $_GET["fltr"]!="1" ){
						continue;
					}
				}
			//filterDeleted_by --

			$rowPatientProcs[]=$rowPatientProc;
			$intravit_meds_val="";
			$intravit_meds_val=explode("|~|",$rowPatientProc['intravit_meds']);
			$inj=false;
			for($m=0;$m<count($intravit_meds_val);$m++){
				$intravit_meds_val[$m] = trim($intravit_meds_val[$m]);
				if(in_array($intravit_meds_val[$m],$arrMedicationInj) && $inj==false){
					$arrInjGroup[$rowPatientProc['id']]=$intravit_meds_val[$m];
					$tmp_dos = !empty($rowPatientProc['exmdt']) ? $rowPatientProc['exmdt'] : $rowPatientProc['dos'];
					if($rowPatientProc['site'] == "OD" || $rowPatientProc['site'] == "OU" || strpos($rowPatientProc['lids_opts'],"RUL") !== false || strpos($rowPatientProc['lids_opts'],"RLL") !== false){//cp.site + lids
						$arrInjdateOD[]=$tmp_dos;
					}
					if($rowPatientProc['site'] == "OS" || $rowPatientProc['site'] == "OU" || strpos($rowPatientProc['lids_opts'],"LUL") !== false || strpos($rowPatientProc['lids_opts'],"LLL") !== false){//cp.site + lids
						$arrInjdateOS[]=$tmp_dos;
					}
					$inj=true;
					//break;
				}
				if(in_array($intravit_meds_val[$m],$arrMedicationInj)){
					$arrCountMedInj[$intravit_meds_val[$m]][$rowPatientProc['form_id']][$rowPatientProc['site']]=$intravit_meds_val[$m];
					$arrFormId[]=$rowPatientProc['form_id'];
				}
			}
		}
	}//echo "<pre>";print_r($rowPatientProcs);
	foreach($rowPatientProcs as $key => $rowPatientProc){
		$comp="";
		$fontcolor="";
		$comp=($rowPatientProc['complication']=="1")?"Y":"N";
		if($rowPatientProc['complication']=="1"){
			$fontcolor="color:#CA4E56";
			//$fontcolor="color:red";
			//$fontcolor="color:#A9000B";
		}

		if(!empty($rowPatientProc['deleted_by'])){
			$fontcolor="color:red";
			$strEditJs="";
			$strEditCss="";
		}else{
			$strEditJs=' onclick="editProcNote(\''.$rowPatientProc['id'].'\')"  ';
			$strEditCss="cursor:pointer;text-decoration:underline;";
		}

		$proc_intravit_med="";
		$proc_intravit_med=str_ireplace("|~|","<br>",$rowPatientProc['intravit_meds']);
		//=======================Laser Procedure Notes====================================================//
		$laser_procedure_note=$rowPatientProc["laser_procedure_note"];
		$spot_duration=$spot_size=$power=$shots=$total_energy=$degree_of_opening=$exposure=$count="";
		$arr_laser_procedure_note=array();
		if($laser_procedure_note==1){
			$spot_duration=trim(addslashes($rowPatientProc["spot_duration"]));
			if($spot_duration){$arr_laser_procedure_note[]="SpotDuration:&nbsp;".$spot_duration;}
			$spot_size=trim(addslashes($rowPatientProc["spot_size"]));
			if($spot_size){$arr_laser_procedure_note[]="SpotSize: ".$spot_size;}
			$power=trim(addslashes($rowPatientProc["power"]));
			if($power){$arr_laser_procedure_note[]="Power: ".$power;}
			$shots=trim(addslashes($rowPatientProc["shots"]));
			if($shots){$arr_laser_procedure_note[]="Shots: ".$shots;}
			$total_energy=trim(addslashes($rowPatientProc["total_energy"]));
			if($total_energy){$arr_laser_procedure_note[]="TotalEnergy: ".$total_energy;}
			$degree_of_opening=trim(addslashes($rowPatientProc["degree_of_opening"]));
			if($degree_of_opening){$arr_laser_procedure_note[]="Degree: ".$degree_of_opening;}
			$exposure=trim(addslashes($rowPatientProc["exposure"]));
			if($exposure){$arr_laser_procedure_note[]="Exposure: ".$exposure;}
			$count=trim(addslashes($rowPatientProc["count"]));
			if($count){$arr_laser_procedure_note[]="Count: ".$count;}
		}
		if(count($arr_laser_procedure_note)>0){$proc_intravit_med=implode("<br />",$arr_laser_procedure_note);}

		//===========================================================================//
		$week_diffOD=$week_diffOS=0;
		$flgInjRow=0;
		$styleBlueLine = "border-left:1px solid #B1C0D6;";
		if($arrProc[$rowPatientProc['proc_id']]){

			if($arrInjGroup[$rowPatientProc['id']]){

				//OD ---------
				if($rowPatientProc['site'] == "OD" || $rowPatientProc['site'] == "OU" || strpos($rowPatientProc['lids_opts'],"RUL") !== false || strpos($rowPatientProc['lids_opts'],"RLL") !== false){//cp.site + lids
					$date_f=$date_s=$mm1=$dd1=$yy1=$mm2=$dd2=$yy2=$week_diffOD="";
					list($mm1,$dd1,$yy1)=explode("-",$arrInjdateOD[$dtOD]);
					list($mm2,$dd2,$yy2)=explode("-",$arrInjdateOD[$dtOD+1]);

					$date_f=$yy1."-".$mm1."-".$dd1;
					$date_s=$yy2."-".$mm2."-".$dd2;
					$date_f = getDateFormatDB($arrInjdateOD[$dtOD]);
					$date_s = getDateFormatDB($arrInjdateOD[$dtOD+1]);
					$week_diffOD=get_week($date_f,$date_s);
					$dtOD++;
					$flgInjRow=1;
				}
				//------------------

				//OS -------------
				if($rowPatientProc['site'] == "OS" || $rowPatientProc['site'] == "OU" || strpos($rowPatientProc['lids_opts'],"LUL") !== false || strpos($rowPatientProc['lids_opts'],"LLL") !== false){//cp.site + lids
					$date_f=$date_s=$mm1=$dd1=$yy1=$mm2=$dd2=$yy2=$week_diffOS="";
					list($mm1,$dd1,$yy1)=explode("-",$arrInjdateOS[$dtOS]);
					list($mm2,$dd2,$yy2)=explode("-",$arrInjdateOS[$dtOS+1]);

					$date_f=$yy1."-".$mm1."-".$dd1;
					$date_s=$yy2."-".$mm2."-".$dd2;

					$date_f=getDateFormatDB($arrInjdateOS[$dtOS]);
					$date_s=getDateFormatDB($arrInjdateOS[$dtOS+1]);
					$week_diffOS=get_week($date_f,$date_s);
					$dtOS++;
					$flgInjRow=1;
				}
				//------------------


			}
			if($week_diffOD==0){$week_diffOD="";}
			if($week_diffOS==0){$week_diffOS="";}


			if($flgInjRow==1){
				$strWeekRowOD = '<td style="'.$fontcolor.'" class="text-center">'.$week_diffOD.'</td>';
				$strWeekRowOS = '<td style="'.$styleBlueLine.$fontcolor.'" class="text-center">'.$week_diffOS.'</td>';
				$strColSpanRow = $styleBlueLine = "";
			}else{
				$strWeekRowOD = $strWeekRowOS =""; $strColSpanRow = ' colspan="2" ';
			}
			$tmp_dos = !empty($rowPatientProc['exmdt']) ? $rowPatientProc['exmdt'] : $rowPatientProc['dos'];
			$arrPatientDOS[$rowPatientProc['id']]='<td style="padding:4px; vertical-align:middle;"><div class="checkbox checkbox-inline"><input type="checkbox" class="chk_box" id="checkbox_'.$rowPatientProc['id'].'" value="'.$rowPatientProc['id'].'"><label for="checkbox_'.$rowPatientProc['id'].'"></label></div></td><td class="pl5 botborder" style="'.$fontcolor.$strEditCss.'  "   '.$strEditJs.'>'.$tmp_dos.'</td>';
			$vis_acuty=$vis_acuty_od=$vis_acuty_os="";
			$vis_acuty=getPatientOct($patient_id,$rowPatientProc['form_id']);
			list($vis_acuty_od,$vis_acuty_os)=explode("!!-!!",$vis_acuty);
			if($rowPatientProc['site']=="OD" || $rowPatientProc['site']=="OU" || strpos($rowPatientProc['lids_opts'],"RUL") !== false || strpos($rowPatientProc['lids_opts'],"RLL") !== false){

				$iopod = (!empty($rowPatientProc['iop_od'])) ? $rowPatientProc['iop_type']." ".$rowPatientProc['iop_od'] : "";

				$arrPatientOD[$rowPatientProc['id']]["OD"]=''.$strWeekRowOD.'
				<td style="'.$fontcolor.'" '.$strColSpanRow.' class="botborder">'.$arrProc[$rowPatientProc['proc_id']].'</td>
				<td style="'.$fontcolor.'" class="botborder">'.$proc_intravit_med.'</td>
				<td style="'.$fontcolor.'" class="botborder text-center">'.($comp).'</td>
				<td style="'.$fontcolor.'" class="botborder text-center">'.$rowPatientProc['dx_code'].'</td>
				<td style="'.$fontcolor.'" class="botborder text-center">'.$vis_acuty_od.'</td>
				<td style="'.$fontcolor.'" class="text-center botborder">'.$rowPatientProc['cmt'].'</td>
				<td style="'.$fontcolor.'" class="text-center botborder">'.$iopod.'</td>
				<td style="'.$fontcolor.'" class=" text-center botborder">'.$arrUserName[$rowPatientProc['user_id']].'</td>
				';
			}
			if($rowPatientProc['site']=="OS" || $rowPatientProc['site']=="OU" || strpos($rowPatientProc['lids_opts'],"LUL") !== false || strpos($rowPatientProc['lids_opts'],"LLL") !== false){

				$iopos = (!empty($rowPatientProc['iop_os'])) ? $rowPatientProc['iop_type']." ".$rowPatientProc['iop_os'] : "";

				$arrPatientOS[$rowPatientProc['id']]["OS"]=''.$strWeekRowOS.'
				<td style="'.$styleBlueLine.$fontcolor.'" '.$strColSpanRow.' class="botborder pl5">'.$arrProc[$rowPatientProc['proc_id']].'</td>
				<td style="'.$fontcolor.'" class="botborder">'.$proc_intravit_med.'</td>
				<td style="'.$fontcolor.'" class="botborder text-center">'.($comp).'</td>
				<td style="'.$fontcolor.'" class="botborder text-center">'.$rowPatientProc['dx_code'].'</td>
				<td style="'.$fontcolor.'" class="botborder text-center">'.$vis_acuty_os.'</td>
				<td style="'.$fontcolor.'" class="text-center botborder">'.$rowPatientProc['cmt'].'</td>
				<td style="'.$fontcolor.'" class="text-center botborder">'.$iopos.'</td>
				<td style="'.$fontcolor.'" class=" text-center botborder">'.$arrUserName[$rowPatientProc['user_id']].'</td>
				';
			}
		}
	}
}else{
	$patientProcData="<tr><td colspan='15' class='text-center'>No record found</td></tr>";
}

$cnt=0;
$patientProcDataInj=$patientProcDataLaser="";
foreach($arrPatientDOS as $form_id => $procVal){
	if($arrInjGroup[$form_id]){
		$patientProcDataInj.='<tr style="border-top:1px solid #CCC;">'.$arrPatientDOS[$form_id];
		if($arrPatientOD[$form_id]['OD']){
			$patientProcDataInj.=$arrPatientOD[$form_id]['OD'];
		}else{
			$patientProcDataInj.='
			<td class="text-center">&nbsp;</td>
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
			$patientProcDataInj.=$arrPatientOS[$form_id]['OS'];
		}else{
			$patientProcDataInj.='
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>';
		}

		$patientProcDataInj.="</tr>";
	}else{
		$patientProcDataLaser.='<tr>'.$arrPatientDOS[$form_id];
		if($arrPatientOD[$form_id]['OD']){
			$patientProcDataLaser.=$arrPatientOD[$form_id]['OD'];
		}else{
			$patientProcDataLaser.='
			<td colspan="2" class="botborder">&nbsp;</td>
			<td class="botborder">&nbsp;</td>
			<td class="botborder text-center">&nbsp;</td>
			<td class="botborder text-center">&nbsp;</td>
			<td class="botborder text-center">&nbsp;</td>
			<td class="botborder text-center">&nbsp;</td>
			<td class="text-center botborder">&nbsp;</td>
			<td class=" text-center botborder">&nbsp;</td>';
		}

		if($arrPatientOS[$form_id]['OS']){
			$patientProcDataLaser.=$arrPatientOS[$form_id]['OS'];
		}else{
			$patientProcDataLaser.='
			<td colspan="2" class="botborder pl5">&nbsp;</td>
			<td class="botborder">&nbsp;</td>
			<td class="botborder text-center">&nbsp;</td>
			<td class="botborder text-center">&nbsp;</td>
			<td class="botborder text-center">&nbsp;</td>
			<td class="botborder text-center">&nbsp;</td>
			<td class="text-center botborder">&nbsp;</td>
			<td class=" text-center botborder">&nbsp;</td>';
		}

		$patientProcDataLaser.="</tr>";
	}
}
$arrInjection=array();
$arrInjByForm=array();
$arrInjByFormCurForm=array();
$qryGetInjection="Select id,patient_id,form_id,injection,count,site from patient_procedure_injections WHERE patient_id='".$_SESSION['patient']."' ORDER BY id DESC";
$resGetInjection=imw_query($qryGetInjection);
if(imw_num_rows($resGetInjection)>0){
	while($rowGetInjection=imw_fetch_assoc($resGetInjection)){
		if(!in_array($rowGetInjection['injection'],$arrInjection)){
			$arrInjByForm[$rowGetInjection['injection']][$rowGetInjection['form_id']]=$rowGetInjection['count'];
			$arrInjByFormCurForm[$rowGetInjection['site']][$rowGetInjection['injection']]=$rowGetInjection['form_id'];
		}
		$arrInjection[]=$rowGetInjection['injection'];
	}
}
$spanInj="";

foreach($arrCountMedInj as $key=>$medInjVal){
	foreach($medInjVal as $keyMed=> $medName){
		if($arrInjByForm[$key][$keyMed]){
		}
	}
}
$arrOdCnt=array();
$arrOsCnt=array();$spanInj=$spanInjOS;
if(count($arrCountMedInj)>0){
	$arrrTemp=array();

	foreach($arrCountMedInj as $key=>$medInjVal){
		$f=0;

		$contVal="";
		foreach($arrInjByForm as $keyMedInj => $injCount){
			if(strtolower($key)==strtolower($keyMedInj)){
				foreach($injCount as $injCountVal){
					$contVal=$injCountVal;
				}
			}
		}
		$medCnt="";

		$y1=$y2=0;
		foreach($medInjVal as $keyInjMed=> $valMedInj){
			foreach($valMedInj as $keysite =>$medval){

				if($keysite=="OD" || $keysite=="OU"){
					$keysiteVal=$keysite;
					if($keysite=="OU"){$keysiteVal="OD";}
					$arrOdCnt[$medval][$y1]=$medval;
					if(count($arrInjByFormCurForm)>0){
						if($arrInjByFormCurForm["OD"][$medval] && $keyInjMed<=$arrInjByFormCurForm["OD"][$medval]){
							unset($arrOdCnt[$medval][$y1]);
						}
					}
					$y1++;
				}
				if($keysite=="OS" || $keysite=="OU"){
					$keysiteVal=$keysite;
					if($keysite=="OU"){$keysiteVal="OS";}
					$arrOsCnt[$medval][$y2]=$medval;
					if(count($arrInjByFormCurForm)>0){
						if($arrInjByFormCurForm["OS"][$medval] && $keyInjMed<=$arrInjByFormCurForm["OS"][$medval]){
							unset($arrOsCnt[$medval][$y2]);
						}
					}
					$y2++;
				}

			}

		}
		$i++;

	}
	//pre($arrOdCnt);
	foreach($arrOdCnt as $key=>$valmed){

		$medInj=check_inj($form_finilize_id,$key,$_SESSION['patient'],"OD");
		if($medInj){
			$medCnt=$medInj;
		}else{
			$medCnt=count($valmed);
			$contValInj=getAddVal($_SESSION['patient'],"OD",$key);
			if($contValInj){
				$medCnt=($medCnt+$contValInj);
			}
		}?>

		<?php
		$spanInj.='<li id="'.$key.'_od"  onClick="add_injection_count(\'view_inj_history\',\''.$key.'\',\'OD\',\''.$key.'_od\')">
				<label class="pointer">Count: &nbsp;'.$key.':</label>
			</li>

			<li>
				<input type="text" id="'.$key.'" name="injection[]"  value="'.$medCnt.'" class="form-control input-sm injcnt" >
				<input type="hidden" id="'.$key.'"  name="injection_change[]"  value="'.$medCnt.'">
				<input type="hidden" id="site"'.$key.'" name="injection_site[]" value="OD">
			</li>';
		$spanInj.="";
		if(count($arrOdCnt)>1){
			//$spanInj.="&nbsp;";
		}
	}
	//pre($arrOsCnt);
	foreach($arrOsCnt as $keyOs=>$valosmed){
		$medInj=check_inj($form_finilize_id,$keyOs,$_SESSION['patient'],"OS");
		if($medInj){
			$medCntos=$medInj;
		}else{
			$medCntos=count($valosmed);
			$contValInj=getAddVal($_SESSION['patient'],"OS",$keyOs);
			if($contValInj){
				$medCntos=($medCntos+$contValInj);
			}
		}

		$spanInjOS.='<li id="'.$keyOs.'_os" onClick="add_injection_count(\'view_inj_history\',\''.$keyOs.'\',\'OS\',\''.$key.'_os\')">
				<label class="pointer">Count: &nbsp;'.$keyOs.':</label>
			</li>

			<li>
				<input type="text" id="'.$keyOs.'" name="injection[]"  value="'.$medCntos.'" class="form-control input-sm injcnt" >
				<input type="hidden" id="'.$keyOs.'"  name="injection_change[]"  value="'.$medCntos.'">
				<input type="hidden" id="site"'.$key.'" name="injection_site[]" value="OS">
			</li>';
		if(count($arrCountMedInj)>1){
			//$spanInjOS.="&nbsp;";
		}
	}
	if($spanInjOS){
		$spanInjOS.='<li><a class="btn btn-md btn-primary" onClick="add_injection_count(\'\',\'\',\'OS\')" style="font-weight:bold; cursor:pointer;">Save</a></li>';
		$os_span_inj_data = '<ul class="list-unstyled list-inline">'.$spanInjOS.'</ul>';
		$spanInjOS = $os_span_inj_data;
	}
	if($spanInj){
		$spanInj.='<li><a class="btn btn-md btn-primary" onClick="add_injection_count(\'\',\'\',\'OD\')" style="font-weight:bold; cursor:pointer;">Save</a></li>';
		$od_span_inj_data = '<ul class="list-unstyled list-inline">'.$spanInj.'</ul>';
		$spanInj = $od_span_inj_data;
	}

}

//rfs comments
$sql_rfs_comm = "SELECT rfs_comm FROM chart_pt_data WHERE patient_id = '".$_SESSION['patient']."' ";
$row_rfs_comm=sqlQuery($sql_rfs_comm);
if($row_rfs_comm!=false){
	$arr_rfs_comm = unserialize($row_rfs_comm["rfs_comm"]);
	$elem_rfs_comments_od = $arr_rfs_comm[0];
	$elem_rfs_comments_os = $arr_rfs_comm[1];
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Retinal Flow Sheet</title>
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
			.injcnt{width:40px;}
			.col-sm-6-rfs{width: 55%;}
			.col-sm-4-rfs{width: 31%;}
			.col-sm-2-rfs{width: 14%;}
		</style>
		<script type="text/javascript">
			window.focus();
        	function add_injection_count(type_inj,inj,site_val,inj_div){
				if(type_inj=='view_inj_history'){//alert(inj);
					var objMsgDiv=document.getElementsByClassName('msg_div');
					for(var i=0;i<objMsgDiv.length;i++){
						objMsgDiv.item(i).style.display='none';
					}
					ptinj_ajax_url = 'retinal_flow_sheet.php?show_med_history=y&inj_name='+inj+'&site_value='+site_val;
					$.ajax({
						url: ptinj_ajax_url,
						success: function(respRes){
							if(respRes){
								var div_val = respRes.split('~~~~');
								show_modal('view_inj_histroy',div_val[1],div_val[0]);
								document.getElementById(inj_div).style.display='block';
							}
						}
					});

				}else{
					var obj_inj=document.getElementsByName('injection[]');
					var obj_change_inj=document.getElementsByName('injection_change[]');
					var obj_site=document.getElementsByName('injection_site[]');
					var url="";
					for(var i=0;i<obj_inj.length;i++){
						obj_val=obj_inj.item(i).value;
						obj_change_val=obj_change_inj.item(i).value;
						obj_change_site=obj_site.item(i).value;
						obj_val_id=obj_inj.item(i).id;
						if(obj_val!=obj_change_val){
							url+=obj_val_id+'~=~'+obj_val+'~=~'+obj_change_val+'~=~'+site_val+'~||~';;
							obj_change_inj.item(i).value=obj_val;
						}

					}
					if(url){
						ptinj_ajax_url = 'retinal_flow_sheet.php?inj_count=1&inj='+url;
						$.ajax({
								url: ptinj_ajax_url,
								success: function(respRes){
									if(respRes){
										fAlert(respRes);
									}
								}
							});
						}else{fAlert("Record Updated");}
					}
			}

			function editProcNote(id){
				window.opener.top.fmain.showOtherForms('onload_wv.php?elem_action=Procedures&chart_proc_id='+id,'Procedures','1275','850','0');
				window.close();
			}

			function funfilterBy(ob){
				if(ob != ""){
					window.location.replace("?fltr="+ob);
				}
			}

			function saverfscomments(){
				var d = $("#elem_rfs_comments_od").val();
				var s = $("#elem_rfs_comments_os").val();
				$("#divprocsing").remove();

				$("body").append("<div id=\"divprocsing\" style=\"position:absolute;top:50%;left:50%;background-color:red;color:white;font-weight:bold; text-align:center;border:1px solid black; padding:5px;\">Processing..</div>");

				$.post("retinal_flow_sheet.php", {"rfsop":"save_comm", "d":d, "s":s}, function(data){ $("#divprocsing").remove();});
			}
        </script>

	</head>
	<body>
		<div class="mainwhtbox pd10" id="result_div">
			<div class="row">
				<form name="retinal_form" id="retinal_form" action="retinal_flow_sheet.php" method="post">
					<div class="col-sm-12 purple_bar">
						<div class="row">
							<div class="col-sm-3">
								<label>Retinal Flow Sheet</label>
							</div>
							<div class="col-sm-6 text-center">
								<label><?php echo $patientNameID; ?></label>
							</div>
							<div class="col-sm-3 text-right">
								<label><?php echo $patient_dob_age; ?></label>
							</div>
						</div>
					</div>
					<?php  if($patientProcDataInj!="" || $patientProcDataLaser!=""){ ?>
					<div class="col-sm-12 pt10">
						<div class="row">
							<div class="col-sm-6 col-sm-6-rfs">
								<div class="row">
									<div class="col-sm-1">
										<label class="od">OD</label>
									</div>
									<div class="col-sm-11">
										<textarea id="elem_rfs_comments_od" class="form-control" placeholder="comments" rows="1" onchange="saverfscomments()"><?php echo $elem_rfs_comments_od;?></textarea>
									</div>
								</div>
							</div>

							<div class="col-sm-4  col-sm-4-rfs">
								<div class="row">
									<div class="col-sm-1 text-center">
										<label class="os">OS</label>
									</div>
									<div class="col-sm-11">
										<textarea id="elem_rfs_comments_os" class="form-control" placeholder="comments" rows="1" onchange="saverfscomments()"><?php echo $elem_rfs_comments_os;?></textarea>
									</div>
								</div>
							</div>

							<div class="col-sm-2  col-sm-2-rfs text-right">
								<div class="row">
									<div class="col-sm-4">
										<button onClick="saverfscomments()" class="btn btn-success" style="cursor:pointer;">Save</button>
									</div>

									<div class="col-sm-8">
										<select class="selectpicker" name="fltr" title="Filter by" onchange="funfilterBy(this.value)" data-title="Filter By" data-width="100%">
											<option value="1" <?php if(isset($_GET["fltr"]) && $_GET["fltr"]=="1" ){  echo "selected"; } ?> >Show All</option>
											<option value="2" <?php if(isset($_GET["fltr"]) && $_GET["fltr"]=="2" ){  echo "selected"; } ?> >Active</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php }
						$inj_ckbox=$laser_ckbox="";
						//Injection Data
						if($patientProcDataInj){
							$inj_ckbox=1;	?>
							<div class="col-sm-12 pt10" >
								<div class="row">
									<div class="col-sm-12 purple_bar">
										<div class="row">
											<div class="col-sm-1"><label class="">OD</label></div>
											<div class="col-sm-4">
												<?php if($spanInj){echo $spanInj; }?>
											</div>
											<div class="col-sm-2 text-center">
												Injections
												<label class="pull-right" >OS</label>
											</div>
											<div class="col-sm-1"></div>
											<div class="col-sm-4">
												<?php
													if($spanInjOS){
														echo $spanInjOS;
													}
												?>
											</div>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="row">
											<div class="table-responsive">
											<table class="table table-striped table-bordered">
												<tr class="grythead">
													<td>
														<div class="checkbox checkbox-inline">
															<input type="checkbox" class="main_chk_box" id="main_chk_box">
															<label for="main_chk_box"></label>
														</div>
													</td>
													<td>Date</td>
													<td class="text-center"># Weeks</td>
													<td>&nbsp;&nbsp;&nbsp;Procedure</td>
													<td>Med</td>
													<td class="text-center">Comp</td>
													<td class="text-center">Dx.</td>
													<td class="text-center">VA</td>
													<td class="text-center">CMT</td>
													<td class="text-center">IOP</td>
													<td class="text-center">Provider</td>
													<td class="text-center"># Weeks</td>
													<td>Procedure</td>
													<td>Med</td>
													<td class="text-center">Comp</td>
													<td class="text-center">Dx.</td>
													<td class="text-center">VA</td>
													<td class="text-center">CMT</td>
													<td class="text-center">IOP</td>
													<td class="text-center">Provider</td>
												</tr>
												<?php echo $patientProcDataInj; ?>
											</table>
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php 	}
						//Laser Data
						if($patientProcDataLaser){ ?>
						<div class="col-sm-12 pt10">
							<div class="row">
								<div class="col-sm-12 purple_bar">
									<div class="row">
										<div class="col-sm-5"><label class="">OD</label></div>
										<div class="col-sm-2 text-center">
											<label>Laser</label>
											<label class="pull-right">OS</label>
										</div>
										<div class="col-sm-5"></div>
									</div>
								</div>
								<div  class="col-sm-12">
									<div class="row">
										<div class="table-responsive">
										<table class="table table-bordered table-striped">
											<tr class="grythead">
												<td><?php if($inj_ckbox==""){ ?>
													<div class="checkbox checkbox-inline">
														<input id="laser_checkbox_0" type="checkbox" class="main_chk_box">
														<label for="laser_checkbox_0"></label>
													</div>
													 <?php
												} ?></td>
												<td>Date</td>

												<td colspan="2">&nbsp;&nbsp;Procedure&nbsp;</td>
												<td>Med / Laser</td>
												<td class="text-center">Comp</td>
												<td class="text-center">Dx.</td>
												<td class="text-center">VA</td>
												<td class="text-center">CMT</td>
												<td class="text-center">IOP</td>
												<td class="text-center">Provider</td>
												<td style="border-left:1px solid #B1C0D6;" colspan="2">Procedure&nbsp;</td>
												<td>Med / Laser</td>
												<td class="text-center">Comp</td>
												<td class="text-center">Dx.</td>
												<td class="text-center">VA</td>
												<td class="text-center">CMT</td>
												<td class="text-center">IOP</td>
												<td class="text-center">Provider</td>
											</tr>
											<?php echo $patientProcDataLaser; ?>
										</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php }

						if($patientProcDataInj=="" && $patientProcDataLaser==""){?>
							<div class="col-sm-12 pt10">
								<div class="alert alert-info text-center">
									<b>No record found</b>
								</div>
							</div> <?php
						}
					?>
      	</form>
     	</div>
  	</div>
    <?php if($patientProcDataInj!="" || $patientProcDataLaser!=""){ ?>
    <div class="mainwhtbox pd10">
    	<div class="row">
      	<div class="col-sm-12 text-center ad_modal_footer" id="module_buttons">
        	<input type="button" value="Delete" class="btn btn-danger" id="del_button"/>
       	</div>
     	</div>
   	</div>
    <?php	} ?>

	</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){
		$(".main_chk_box").click(function(){
			var main_chkbox = $(this).prop("checked");
			$(".chk_box").each(function(index, element){$(this).prop("checked",main_chkbox);});
		});

		$("#del_button").click(function(){var checked_id="";
			$(".chk_box").each(function(index, element){
				if($(element).is(":checked")){
					checked_id+=$(element).val()+", ";
				}
			});
			if(checked_id!=''){
				checked_id = checked_id.substr(0,checked_id.length-2);
				fancyConfirm("Are you sure you want to delete?",'',"window.deleteModifiers('"+checked_id+"')");
			}else{fAlert("No Record Selected.");}
		});

		//Setting popup dimensions based on the resolution
		if(typeof(window.opener.top.innerDim)=='function'){
			var innerDim = window.opener.top.innerDim();
			if(innerDim['w'] > 1600) innerDim['w'] = 1600;
			if(innerDim['h'] > 900) innerDim['h'] = 900;
			window.resizeTo(innerDim['w'],innerDim['h']);
			brows	= get_browser();
			if(brows!='ie') innerDim['h'] = innerDim['h']-35;
			var result_div_height = innerDim['h']-210;
			//$('.mainwhtbox').height(result_div_height+'px');
		}

	});

	function init()
	{
			var h = window.innerHeight;
			var footerH = 0;
			if( $("#module_buttons").length > 0 ){
				footerH = $("#module_buttons").parent().parent().outerHeight(true);
			}

			var hh = h - (footerH  + 30);
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
			$.get("retinal_flow_sheet.php?del_from=RFS&delProc_id="+checked_id, function(data){
				if(data=="done"){
					fAlert('Record deleted');
					window.location.reload();
				}else{fAlert("SQL ERROR: "+data);}
			});
		}
	}
</script>
