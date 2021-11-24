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

	File: pt_phss_info.php
	Purpose: HL7 implemented
	Access Type: Direct 
*/
set_include_path(dirname(__FILE__).'/../../../library');
require_once("../../../config/globals.php");
require_once "Net/HL7/Message.php";
require_once "Net/HL7/Segment.php";
require_once "Net/HL7/Segments/MSH.php";
require_once "Net/HL7.php";

$sel_mtype 		= (isset($_REQUEST['sel_mtype']) && trim($_REQUEST['sel_mtype'])!='') ? trim($_REQUEST['sel_mtype']) : '';
$sel_dos		= (isset($_REQUEST['sel_dos']) && trim($_REQUEST['sel_dos'])!='') ? trim($_REQUEST['sel_dos']) : '';
$patient_id		= $_SESSION['patient'];
$operator_id 	= $_SESSION["authId"];

if( isset($_POST['download_file']) && $_POST['download_file'] == 1 ) {
	$act_path = $_POST['act_path'];
	if($act_path){
		$fileData = file_get_contents($act_path);	
		downloadFiles($act_path,$fileData);
	}
}

if($sel_mtype!=''){
	$msg = new Net_HL7_Message();
	$pt_qry = "SELECT * FROM patient_data WHERE id = '".$patient_id."'";
	$pt_arr = get_array_records_query($pt_qry);
	$patient["id"] 				= $pt_arr[0]["id"];
	$respParty					= getRespParty($patient["id"]);
	$patient["facility"] 		= "imwemr";
	$patient["fname"] 			= stripslashes($pt_arr[0]["fname"]);
	$patient["lname"]			= stripslashes($pt_arr[0]["lname"]);
	$patient["mname"]			= stripslashes($pt_arr[0]["mname"]);
	$patient["dob"] 			= str_replace("-", "", $pt_arr[0]["DOB"]);
	$patient["sex"] 			= ($pt_arr[0]["sex"] == "Male") ? "M" : "F";
	$patient["address"] 		= $pt_arr[0]["street"]." ".$pt_arr[0]["street2"];
	$patient["city"] 			= $pt_arr[0]["city"];
	$patient["state"] 			= $pt_arr[0]["state"];
	$patient["country"] 		= $pt_arr[0]["country_code"]=='' ? 'USA' : $pt_arr[0]["country_code"];
	$patient["zip_code"] 		= $pt_arr[0]["postal_code"];
	if(trim($pt_arr[0]["phone_home"])==''){$pt_arr[0]["phone_home"]= $respParty['home_ph'];}
	$patient["home_phone"] 		= str_replace("-", "", $pt_arr[0]["phone_home"]);
	if(strtolower($pt_arr[0]["race"])=='other'){$pt_arr[0]["race"] = $pt_arr[0]["otherRace"];}
	$patient["race"] 			= get_extract_record('race', 'race_name', $pt_arr[0]["race"], 'cdc_code');
	$patient["ethnicity"] = get_extract_record('ethnicity', 'ethnicity_name', $pt_arr[0]["ethnicity"], 'cdc_code');
	$patient["maiden_lname"] 	= $pt_arr[0]["maiden_lname"];
	$patient["maiden_fname"] 	= $pt_arr[0]["maiden_fname"];
	$patient["maiden_mname"] 	= $pt_arr[0]["maiden_mname"];
	$patient['patientStatus']	= $pt_arr[0]['patientStatus'];
	if(strtolower($patient['patientStatus'])=='deceased'){
		$patient['patient_dod']		= trim($pt_arr[0]['dod_patient'])!='0000-00-00' ? trim($pt_arr[0]['dod_patient']) : '';
	}

	//getting pt facility
	$pt_fac = "iDoc";
	$pt_fac_npi = '9999999999';
	if(!empty($pt_arr[0]["default_facility"])){
		$vquery_t = "select 
						a.pos_facility_id,a.npiNumber, a.facilityPracCode, b.pos_prac_code 
					from 
						pos_facilityies_tbl a, pos_tbl b 
					where 
						a.pos_id = b.pos_id 
					and 
						a.pos_facility_id = '".$pt_arr[0]["default_facility"]."'";
		$vquery_a = get_array_records_query($vquery_t);
		$pt_fac = $vquery_a[0]["facilityPracCode"]."-".$vquery_a[0]["pos_prac_code"];
		$pt_fac_npi = $vquery_a[0]["npiNumber"]; 
	}else{
		$vquery_t = "select name,facility_npi from facility where facility_type = 1";
		$vquery_a = get_array_records_query($vquery_t);
		$pt_fac = $vquery_a[0]["name"];
		$pt_fac_npi = $vquery_a[0]["facility_npi"];
	}


	if($sel_mtype=='A04'){
		$sel_mtype_msh_code = 'ADT_A01';
	}else if($sel_mtype=='A03'){
		$sel_mtype_msh_code = 'ADT_A03';
	}
	$dttm = date("YmdHi");
	$msh = new Net_HL7_Segments_MSH();
	$msh->setField(1, "|");
	$msh->setField(2, "^~\&");
	$msh->setField(4, array($pt_fac,$pt_fac_npi,'NPI'));
	$msh->setField(7, $dttm);
	$msh->setField(9, array("ADT", $sel_mtype,$sel_mtype_msh_code));
	$msh->setField(10, "iDoc-PHMS-".$pt_arr[0]["id"]."-".date("YmdHis")); //$patient["id"].$operator_id.time()
	$msh->setField(11, array("P"));
	$msh->setField(12, array("2.5.1"));
	$msh->setField(21, array('PH_SS-NoAck','SS Sender','2.16.840.1.114222.4.10.3','ISO'));
	$msg->addSegment($msh);
	$segment_cnt = 1;

	//setting event information details
	$seg_EVN = new Net_HL7_Segment("EVN");
	//$seg_EVN->setField(1, $sel_mtype);
	$seg_EVN->setField(2, $dttm);
	$seg_EVN->setField(7, array($pt_fac,$pt_fac_npi,'NPI'));
	$msg->addSegment($seg_EVN);
	$segment_cnt++;	

	//setting patient information details
	$seg_PID = new Net_HL7_Segment("PID");
	$seg_PID->setField(1,1);
	$seg_PID->setField(3, array($patient["id"], '', '','','MR'));
	$seg_PID->setField(5, array('', '', '','','','','~','','','','','','S')); //in capital letters
	if($patient["dob"]!='' && $patient["dob"]!='00000000'){
	//	$seg_PID->setField(7, $patient["dob"]); //yyyymmdd
	}
	$seg_PID->setField(8, $patient["sex"]); //M or F
	if($patient["race"]!=''){
		$seg_PID->setField(10, array($patient["race"], '', "CDCREC"));
	}
	$seg_PID->setField(11, array('', '', '', '', $patient["zip_code"], '', '','',trim($patient["country"])));
	if($patient["ethnicity"]!=''){
		$seg_PID->setField(22, array($patient["ethnicity"], '', "CDCREC"));
	}
	if(isset($patient['patient_dod']) && $patient['patient_dod']!=''){
		$seg_PID->setField(29, $patient['patient_dod']);
	}
	$msg->addSegment($seg_PID);
	$segment_cnt++;	
	
	
	$OBX_counter = 1;
	//setting patient visit 1 information details
	$pv1_query = "SELECT encounterId, providerId, ptVisit, DATE_FORMAT(date_of_service,'%Y%m%d') AS admit_date, DATE_FORMAT(time_of_service,'%H%i') AS admit_time FROM chart_master_table WHERE patient_id='".$patient_id."' AND purge_status='0' AND delete_status='0' ORDER BY id DESC LIMIT 0,1";
	$pv1_res = imw_query($pv1_query);
	if($pv1_res && imw_num_rows($pv1_res)==1){
		$pv1_rs	 = imw_fetch_assoc($pv1_res);
		$seg_PV1 = new Net_HL7_Segment("PV1");
		$seg_PV1->setField(1, 1);
		$seg_PV1->setField(2, 'O');//for outpatient.
		$seg_PV1->setField(19, array($pv1_rs['encounterId'],'','','','VN'));
		$seg_PV1->setField(36, '01');//DISCHARGE DISPOSITION
		$seg_PV1->setField(44, array($pv1_rs['admit_date'].$pv1_rs['admit_time']));
		$msg->addSegment($seg_PV1);	
		$segment_cnt++;
		
		
		if($sel_mtype=='A03'){
		$imm_qry = "SELECT DATE_FORMAT(ppll.onset_date, '%Y%m%d') as administered_date, TIME_FORMAT(ppll.OnsetTime, '%H%m') as administered_time, ppll.problem_name 
						FROM pt_problem_list ppl JOIN (SELECT * FROM pt_problem_list_log ORDER BY id DESC) ppll ON (ppl.id = ppll.problem_id) 
					    WHERE ppl.pt_id  = '".$patient_id."' AND ppl.prob_type='Finding' AND ppl.status='Active' GROUP BY ppll.problem_id";
			$arr_imm = get_array_records_query($imm_qry);
			//setting patient diagnosis information 
			if(isset($arr_imm) && count($arr_imm) > 0){
				$cnt = 1;
				foreach($arr_imm as $arr_this_imm){
					$date_time = $arr_this_imm["administered_date"].$arr_this_imm["administered_time"];
					$arrProblemName1 = array();
					preg_match_all("/([0-9]+\.[0-9]+)/", $arr_this_imm["problem_name"], $arrProblemName1);
					$array_replace = array();
					if(count($arrProblemName1[0]) > 0){
						foreach($arrProblemName1[0] as $val){
							array_push($array_replace, $val);
						}
					}
					$arrProblemName2 = array();
					$str_replaced = str_replace($array_replace, "", $arr_this_imm["problem_name"]);
					preg_match_all("/([0-9]+)/", $str_replaced, $arrProblemName2);
					
					$int_arr1 = count($arrProblemName1[0]);
					$int_arr2 = count($arrProblemName2[0]);
					if($int_arr1 != "" && $int_arr2 != ""){
						$arrProblemName = array_merge($arrProblemName1, $arrProblemName2);
					}else if($int_arr1 != ""){
						$arrProblemName = $arrProblemName1;
					}else if($int_arr2 != ""){
						$arrProblemName = $arrProblemName2;
					}
					unset($arrProblemName1);
					unset($arrProblemName2);
					//print "<pre>";
					//print_r($arrProblemName);
					if(count($arrProblemName[0]) > 0){
						
						foreach($arrProblemName[0] as $val){
							if(empty($val) == false){
								$qryGetProblemName = "SELECT diag_description FROM diagnosis_code_tbl WHERE dx_code = '".$val."'";
								$arrrsGetProblemName = get_array_records_query($qryGetProblemName);
								if(is_array($arrrsGetProblemName) && count($arrrsGetProblemName) > 0){
									//print_r($arrrsGetProblemName);
									if($arrrsGetProblemName[0]["diag_description"] != ""){
										
										unset($seg_DG1);
										$seg_DG1 = new Net_HL7_Segment("DG1");
										
										$seg_DG1->setField(1, $cnt);
										$seg_DG1->setField(3, array($val,$arrrsGetProblemName[0]["diag_description"],'I9CDX'));
										$seg_DG1->setField(6, "F");
										$msg->addSegment($seg_DG1);
										$cnt++;
										$segment_cnt++;
									}
								}
							}
						}
					}
				}
			}
		}
		
		
		//OBX: Observation/Result Segment
		$visit_type = trim($pv1_rs['ptVisit']);
		if($visit_type!=''){
			$seg_OBX = new Net_HL7_Segment("OBX");
			$seg_OBX->setField(1, $OBX_counter);
			$seg_OBX->setField(2, 'CWE');
			$seg_OBX->setField(3, array('SS003','','PHINQUESTION'));
			if(in_array(strtolower($visit_type),array('emergency','emergency care','emergency visit'))){
				$visit_type = 'Emergency Visit';
				$seg_OBX->setField(5, array('261QE0002X',$visit_type,'NUCC'));
			}else if(in_array(strtolower($visit_type),array('urgent','urgent visit','urgent care'))){
				$visit_type = 'Urgent Care';
				$seg_OBX->setField(5, array('261QU0200X',$visit_type,'NUCC'));
			}else{
				$seg_OBX->setField(5, array($visit_type));
			}
			$seg_OBX->setField(11, 'F');
			$msg->addSegment($seg_OBX);	
			$segment_cnt++;
			$OBX_counter++;
		}
	}
	
	
	//PATIENT AGE OBSERVATION
	if($patient["dob"]=='' || $patient["dob"]=='0000-00-00'){
		$patient_age = 0;
	}else{
		$patient_age = floor( (strtotime(date('Y-m-d')) - strtotime($patient["dob"])) / 31556926);
	}
	
	$seg_OBX = new Net_HL7_Segment("OBX");
	$seg_OBX->setField(1, $OBX_counter);
	$seg_OBX->setField(2, 'NM');
	$seg_OBX->setField(3, array('21612-7','','LN'));
	$seg_OBX->setField(5, $patient_age);
	$seg_OBX->setField(6, array('a','','UCUM'));
	$seg_OBX->setField(11, 'F');
	$msg->addSegment($seg_OBX);
	$segment_cnt++;
	$OBX_counter++;
	
	
	$pbl_qry1 = "SELECT DATE_FORMAT(ppll.onset_date, '%Y%m%d') as administered_date, TIME_FORMAT(ppll.OnsetTime, '%H%m') as administered_time, ppll.problem_name 
						FROM pt_problem_list ppl JOIN (SELECT * FROM pt_problem_list_log ORDER BY id DESC) ppll ON (ppl.id = ppll.problem_id) 
					    WHERE ppl.pt_id  = '".$patient_id."' AND ppl.prob_type='Complaint' AND ppl.status='Active' GROUP BY ppll.problem_id";
	$pbl_res1 = imw_query($pbl_qry1);
	if($pbl_res1 && imw_num_rows($pbl_res1)>0){
		while($pbl_rs1 = imw_fetch_assoc($pbl_res1)){
			$seg_OBX = new Net_HL7_Segment("OBX");
			$seg_OBX->setField(1, $OBX_counter);
			$seg_OBX->setField(2,'CWE');
			$seg_OBX->setField(3,array('8661-1','','LN'));
			$seg_OBX->setField(5,array('','','','','','','','',trim($pbl_rs1['problem_name'])));
			$seg_OBX->setField(11,'F');
			$msg->addSegment($seg_OBX);
			$segment_cnt++;
			$OBX_counter++;
		}
	}

	
	if($sel_mtype=='A04'){
		$imm_qry = "SELECT DATE_FORMAT(ppll.onset_date, '%Y%m%d') as administered_date, TIME_FORMAT(ppll.OnsetTime, '%H%m') as administered_time, ppll.problem_name 
						FROM pt_problem_list ppl JOIN (SELECT * FROM pt_problem_list_log ORDER BY id) ppll ON (ppl.id = ppll.problem_id) 
					    WHERE ppl.pt_id  = '".$patient_id."' AND ppll.prob_type='Diagnosis' AND ppl.status RLIKE 'Active|Inactive|Resolved' GROUP BY ppll.problem_id";
						
		$arr_imm = get_array_records_query($imm_qry);
		//setting patient diagnosis information 
		if(isset($arr_imm) && count($arr_imm) > 0){
			$cnt = 1;
			foreach($arr_imm as $arr_this_imm){
				$date_time = $arr_this_imm["administered_date"].$arr_this_imm["administered_time"];
				$arrProblemName1 = array();
				preg_match_all("/([0-9]+\.[0-9]+)/", $arr_this_imm["problem_name"], $arrProblemName1);
				$array_replace = array();
				if(count($arrProblemName1[0]) > 0){
					foreach($arrProblemName1[0] as $val){
						array_push($array_replace, $val);
					}
				}
				$arrProblemName2 = array();
				$str_replaced = str_replace($array_replace, "", $arr_this_imm["problem_name"]);
				preg_match_all("/([0-9]+)/", $str_replaced, $arrProblemName2);
				
				$int_arr1 = count($arrProblemName1[0]);
				$int_arr2 = count($arrProblemName2[0]);
				if($int_arr1 != "" && $int_arr2 != ""){
					$arrProblemName = array_merge($arrProblemName1, $arrProblemName2);
				}else if($int_arr1 != ""){
					$arrProblemName = $arrProblemName1;
				}else if($int_arr2 != ""){
					$arrProblemName = $arrProblemName2;
				}
				unset($arrProblemName1);
				unset($arrProblemName2);
				//print "<pre>";
				//print_r($arrProblemName);
				if(count($arrProblemName[0]) > 0){
					
					foreach($arrProblemName[0] as $val){
						if(empty($val) == false){
							$qryGetProblemName = "SELECT diag_description FROM diagnosis_code_tbl WHERE dx_code = '".$val."'";
							$arrrsGetProblemName = get_array_records_query($qryGetProblemName);
							if(is_array($arrrsGetProblemName) && count($arrrsGetProblemName) > 0){
								//print "<pre>";
								//print_r($arrrsGetProblemName);
								if($arrrsGetProblemName[0]["diag_description"] != ""){
									
									unset($seg_DG1);
									$seg_DG1 = new Net_HL7_Segment("DG1");
									
									$seg_DG1->setField(1, $cnt);
									$seg_DG1->setField(3, array($val,$arrrsGetProblemName[0]["diag_description"],'I9CDX'));
									$seg_DG1->setField(6, "W");
									$msg->addSegment($seg_DG1);
									$cnt++;
									$segment_cnt++;
								}
							}
						}
					}
				}
			}
		}
	}
	//die;
	$str_download_file = "";
	for($kkk = 0; $kkk < $segment_cnt; $kkk++){
		$str_download_file .= substr($msg->getSegmentAsString($kkk), 0, -1)."\n";
	}

	$strPath = data_path()."hl7_health_survey.er7";
	file_put_contents($strPath, $str_download_file);
}
?>
<html>
	<head>
		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>HL7 Message for Public Health Surveillance</title>
    
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
   <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet" />
    <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
    <?php } ?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript">
			window.focus();
			
			$(document).ready(function(){
				var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
				window.resizeTo(parWidth,745);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
				
				$("select.selectpicker").selectpicker();
			});
		</script>
	</head>
	
	<body>
  	<div class="panel panel-primary">
      <div class="panel-heading">Standard: HL7 2.5.1 HL7 Message for Public Health Surveillance</div>
      <div class="panel-body popup-panel-body" style="max-height:580px; height:580px;">
      	<div class="col-xs-12">
        	<div class="row">
        		<form name="mtype_form" id="mtype_form">
       		
         			<div class="col-xs-3 col-md-2 col-lg-2">
								<label><b>DOS - Visit Type</b></label>
								<span class="clearfix"></span>
         				<select name="sel_dos" id="sel_dos" onChange="document.forms.mtype_form.submit();" class="selectpicker" title="Select" data-width="90%" data-size="10">
                	<?php 
										$q_dos = "SELECT id,date_of_service,ptVisit FROM chart_master_table WHERE patient_id='".$patient_id."' AND purge_status='0' AND delete_status='0'";
										$res_dos = imw_query($q_dos);
										$i = 0;
										while($rs_dos = imw_fetch_assoc($res_dos)) {
											$i++;
											$dos 	= $rs_dos['date_of_service'];
											$vType 	= trim($rs_dos['ptVisit']);
											if($vType != ''){$vType = $dos.' - '.$vType;}
											else{$vType = $dos;}
											$sel = $i == 1 ? 'selected' : '';
											echo '<option value="'.$dos.'" '.$sel.'>'.$vType.'</option>';
										}
									?>
								</select>
							</div>
             	<div class="col-xs-3 col-md-2 col-lg-2">
								<label><b>Message Type</b></label>
								<span class="clearfix"></span>
             		<select name="sel_mtype" id="sel_mtype" onChange="document.forms.mtype_form.submit();" class="selectpicker" title="Select" data-width="90%" >
             			<option value="A04" selected >Patient Registered</option>
									<option value="A03" <?php if($sel_mtype=='A03'){echo ' selected';}?>>Patient Discharged</option>
								</select>
							</div>
						
        		</form>
       		</div>
       		
     			<div class="row bg bg-warning mt20 ">
     				<div class="col-xs-12 bg-warning" style="height:300px; border:solid 1px #ddd; ">
     				<?php 
							if( $str_download_file ) 
								echo $str_download_file;
						?>
						</div>
					</div>
     		</div>	 		
   		</div>
			
			<footer class="panel-footer">
     		<form name="Hl7ExportFrm" action="pt_phss_info.php" method="post">
       		<input class="btn btn-success" type="submit" value="Save">
          <input class="btn btn-danger" type="button" value="Close" onClick="javascript:window.close();">
          <input type="hidden" name="download_file" value="1">
          <input type="hidden" name="act_path" value="<?php echo $strPath;?>">
      	</form>
    	</footer>
      
			
		</div>		
	</body>
</html>