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
set_time_limit(0);
include("../../../config/globals.php");
if($_SERVER['REQUEST_METHOD'] == "POST"){
$strFacility = implode(",",$_REQUEST['facility']);
//$strProvider = implode(",",$_REQUEST['provider']);
$strElem_chkpid = implode(",",$_REQUEST['elem_chkpid']);
$strElem_formid_tmp = implode(",",array_filter($_REQUEST['elem_formid_tmp']));

$strFacility = $_REQUEST['facility'];
$strProvider = $_REQUEST['provider'];

$startDate = $_REQUEST['Start_date'];
$endDate = $_REQUEST['End_date'];
$enc_key = $_REQUEST['enc_key'];
$zip_encrypt = $_REQUEST['zip_encrypt'];


$queryA = $queryJ = $rqInsType = "";
$rqArrInsType = $_REQUEST['ins_type'];
if(empty($rqArrInsType) == false){
	$rqInsType = str_ireplace(",","','",$rqArrInsType);
	$rqInsType = "'".$rqInsType."'";
	$queryA .= " AND insd.type IN ($rqInsType) ";
}	
$rqInsProvider = $_REQUEST['insId'];	
if(empty($rqInsProvider) == false){
	$queryA .= " AND insd.provider IN ($rqInsProvider) ";
}
if(empty($rqInsType) == false || empty($rqInsProvider) == false){
	$queryJ .= " JOIN insurance_data insd ON insd.pid = pd.id AND insd.actInsComp = '1' ".$queryA;	
}

$query = "SELECT pd.id as pat_id,CONCAT(pd.lname,', ',pd.fname) as pat_name,
								cmt.id as form_id, DATE_FORMAT(cmt.date_of_service, '".get_sql_date_format('','Y','-')."') as dos
								FROM patient_data pd
								JOIN chart_master_table cmt ON cmt.patient_id = pd.id 
								".$queryJ."
								WHERE 1=1 ";
if($strFacility != "")		
$query .=  " AND pd.default_facility in($strFacility) ";
if($strProvider != "")
$query .=  " AND pd.providerID in($strProvider) ";
if($startDate != "" && $endDate!="")
$query .=  " AND (cmt.date_of_service BETWEEN '".getDateFormatDB($startDate)."' AND '".getDateFormatDB($endDate)."') ";
if($strElem_chkpid != "")
$query .=  " AND pd.id in($strElem_chkpid) ";
if($strElem_formid_tmp != "")
$query .=  " AND cmt.id in($strElem_formid_tmp) ";

if($_REQUEST['patientId']) {
	$query .=  " AND pd.id in(".$_REQUEST['patientId'].") ";	
}
$query .=  " ORDER BY cmt.date_of_service DESC,pd.lname ";	
//$query .= implode(" AND ",$arrQry);
//echo($query);pre($_REQUEST);
//$queryRes = imw_query($query);
$arrData = get_array_records_query($query);
	/*
	if($_REQUEST['patientId']!=""){
		$arrData = array();
		$arrData[0]['pat_id'] = $_REQUEST['patientId'];
		$arrData[0]['form_id'] = $_REQUEST['cmbxElectronicDOS'];
		$arrData[0]['pat_name'] = $_REQUEST['patient'];
	}
	*/
}
$library_path = $GLOBALS['webroot'].'/library';
?>
<html>
	<title>imwemr</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery-ui.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-colorpicker.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/messi/messi.css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/admin.css" type="text/css">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
	<![endif]-->
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery-ui.min.1.11.2.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.dragToSelect.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-formhelpers-colorpicker.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/simple_drawing.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/Driving_License_Scanning.js"></script>
	<head>
		<script>
			function chkall(v){
			var ar = document.getElementsByName("elem_chkpid[]");
			var ln = ar.length;
				for(var o=0;o<ln;o++ ){
					//alert(o+"  - "+ar[o]);
					ar[o].checked = v;
					
				}
			}
			function refreshMedAllergyProb(obj) {
					var pid="";
					var form_id = "";
					var objChkid = top.fmain.ccda_export_report.document.getElementsByName("elem_chkpid[]");
					var objChkFormid = top.fmain.ccda_export_report.document.getElementsByName("elem_formid[]");
					var objFormidTmp = top.fmain.ccda_export_report.document.getElementsByName("elem_formid_tmp[]");				
					
					var aCnt = 0;
					for(var ze=0;ze<objChkid.length;ze++){
						objFormidTmp[ze].value = "";
						if(objChkid[ze].checked){
							aCnt++;
							objFormidTmp[ze].value = objChkFormid[ze].value;		
						}
					}
					if(top.fmain.ccda_export_report.document.getElementById("cnt_rec_id")) {
						top.fmain.ccda_export_report.document.getElementById("cnt_rec_id").innerHTML = "("+aCnt+")";
					}
					for(fe=0;fe<objChkid.length;fe++){					
						if(objChkid[fe].checked){						
							pid = objChkid[fe].value;
							form_id = objChkFormid[fe].value;
							break;
						}					
					}
				
					var objMed = top.fmain.document.getElementById("mu_data_set_medications");
					var objAllergy = top.fmain.document.getElementById("mu_data_set_allergies");
					var objProbList = top.fmain.document.getElementById("mu_data_set_problem_list");
					var showalert = 'no';
					if(objMed) {
						top.fmain.get_medications('medications',objMed,showalert);	
					}
					if(objAllergy) {
						top.fmain.get_medications('allergies',objAllergy,showalert);	
					}
					if(objProbList) {
						top.fmain.get_medications('problem_list',objProbList,showalert);	
					}
					
					
					if(pid == "" ){					
						top.fAlert("Please select patient to proceed");
					}
				//}
			}
		</script>
	</head>
<body class="whtbox">
<form id="ccda_report" name="ccda_report" method="post">
<input type="hidden" name="facility" id="facility">
<input type="hidden" name="provider" id="provider">
<input type="hidden" name="ins_type" id="ins_type">
<input type="hidden" name="insId" id="insId">
<input type="hidden" name="Start_date" id="Start_date">
<input type="hidden" name="End_date" id="End_date">
<input type="hidden" name="patientId" id="patientId">
<input type="hidden" name="cmbxElectronicDOS" id="cmbxElectronicDOS">
<input type="hidden" name="patient" id="patient">
<input type="hidden" name="ccdDocumentOptions" id="ccdDocumentOptions">
<input type="hidden" name="enc_key" id="enc_key">
<input type="hidden" name="medications" id="medications">
<input type="hidden" name="allergies" id="allergies">
<input type="hidden" name="problem_list" id="problem_list">
<input type="hidden" name="zip_encrypt" id="zip_encrypt">
	<?php $col_height_frame = (int) ($_SESSION['wn_height'] - 500);?>
    <table class=" table table-bordered adminnw" style="width:100%; margin-bottom:2px;">
        <thead>
            <tr>    
            <?php
            if(!empty($_REQUEST["enc_key"])){ //
                    ?>
                    
                    <th align="left" width="2%"></th>
                    <th align="left" width="5%">S.No.</th>
                    <th align="left" width="45%">CCDA</th>
                    <th align="left" width="45%">SHA2-Key</th>
                    <?php }else{ ?>
                    <th style="width:2%;">
                        <div class="checkbox">
                            <input type="checkbox" id="elem_flg_all_chk" value="1" onClick="chkall(this.checked);refreshMedAllergyProb(this);" >
                            <label for="elem_flg_all_chk"></label>
                        </div>
                    </th>
                    <th align="left" width="5%">S.No.</th>
                    <th align="left" width="44%">Patient<span id="cnt_rec_id"></span></th>
                    <th align="left" width="45%">DOS</th>   
                    <?php } ?>
                    
            </tr>
        </thead>
    </table>
    <div style=" width:100%;height:82%; overflow:scroll; overflow-x:hidden; ">
		<table class=" table table-bordered adminnw" style="width:100%;">
			<?php 
			if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST["enc_key"])){
			$set_sequence_ccda_report = true;	
			require_once("create_ccda_r2_xml.php");
			}else{
			
				if(count($arrData)<=0){
						echo "<tr><td colspan='7' align='center' height='50px'>No records found</td></tr>";
				}else{
					
					$count = 1;
					//*
					foreach($arrData as $key=>$arr){
					$pat_id = $arr['pat_id'];
					$form_id = $arr['form_id'];
					$form_id_new = $arr['form_id'];
					$pat_name = $arr['pat_name'];
					$dos = $arr['dos'];
			
					$_REQUEST['pid'] = $pat_id;
					$_REQUEST['electronicDOSCCD'] = $form_id;
					$_REQUEST['option'] = $_REQUEST['ccdDocumentOptions'];	
			
					if($_REQUEST['pid'] != ""){
					$pid = $_REQUEST['pid'];
					$form_id = $_REQUEST['electronicDOSCCD'];
					}
					$currentDate = date("YmdHis");
					$qry = "select patient_data.*,users.fname as ptProviderFName,users.mname as ptProviderMName,users.lname as ptProviderLName,users.user_npi as ptProviderNPI,
						refferphysician.Title as ptRefferPhyTitle,refferphysician.FirstName as ptRefferPhyFName,refferphysician.MiddleName as ptRefferPhyMName,
						refferphysician.LastName as ptRefferPhyLName,refferphysician.physician_phone as ptRefferPhyPhone
						from patient_data LEFT JOIN users on users.id = patient_data.providerID
						LEFT JOIN refferphysician ON refferphysician.physician_Reffer_id = patient_data.primary_care_id 
						where patient_data.id = '".$pid."'";
					echo "<tr>
								<td class='text_12' bgcolor='#FFFFFF' width='2%'>
								<div class=\"checkbox\">
										<input type=\"checkbox\" name=\"elem_chkpid[]\" id=\"elem_flg_all_chk_$count\" value=\"".$pat_id."\" onclick=\"refreshMedAllergyProb(this)\">
										<label for=\"elem_flg_all_chk_$count\"></label>
										<input type=\"hidden\" name=\"elem_formid[]\" id=\"elem_flg_all_$count\" value=\"".$form_id_new."\">
										<input type=\"hidden\" name=\"elem_formid_tmp[]\" id=\"elem_flg_all_cnt_$count\" value=\"\">
									</div>
								</td>
								<td class='text_12' bgcolor='#FFFFFF' width='5%'>$count</td>
								<td class='text_12' bgcolor='#FFFFFF' width='45%'>$pat_name - $pat_id</td>
								<td class='text_12' bgcolor='#FFFFFF' width='45%'>$dos</td>
								</tr>";
								$count++;
					}
				}
				
			}
?>
		</table>
	</div>
</form>
</body>
</html>
<script>
top.show_loading_image("hide");
parent.document.getElementById('enc_key').value='';
</script>