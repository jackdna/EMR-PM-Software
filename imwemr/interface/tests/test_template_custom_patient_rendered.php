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
$callFromInterface		= strtolower(trim(strip_tags($_GET['callFromInterface'])));
$doNotShowRightSide 	= strtolower(trim($_REQUEST['doNotShowRightSide']));
require_once("../../config/globals.php");
if($callFromInterface != 'admin'){
	require_once("../../library/patient_must_loaded.php");
}
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");
require_once("../../library/classes/ChartTestPrev.php");

$library_path 			= $GLOBALS['webroot'].'/library';
$patient_id				= $_SESSION['patient'];
$objTests				= new Tests;
//$objTests->patient_id 	= $patient_id;

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

$test_master_id			= intval(trim($_GET['test_master_id']));
if($test_master_id==0) 	  $test_master_id = $objTests->get_template_test_id($_GET['tId'],'custom');
$this_test_properties	= $objTests->get_table_cols_by_test_table_name($test_master_id,'id');
$test_table_name		= $this_test_properties['test_table'];
$this_test_screen_name	= $this_test_properties['temp_name'];

//User and  User_type
$logged_user 	= $objTests->logged_user;
$userType 		= $objTests->logged_user_type;

//GET VERSION HTML DETAILS
$this_version_data					= $objTests->get_template_test_version_data($this_test_properties['id'],$this_test_properties['version']);
$elem_version_id					= $this_test_properties['version'];
$elem_test_main_options 			= $this_version_data['test_main_options'];
$elem_test_results 					= $this_version_data['test_results'];
$elem_test_treatment 				= $this_version_data['test_treatment'];

if($callFromInterface != 'admin'){
    //================= GETTING PATIENT DATA
	$getPatientDataStr = "SELECT * FROM patient_data WHERE id = '$patient_id'";
	$getPatientDataQry = imw_query($getPatientDataStr);
	$getPatientDataRow = imw_fetch_array($getPatientDataQry);
	$patFname = $getPatientDataRow['fname'];
	$patMname = $getPatientDataRow['mname'];
	$patLname = $getPatientDataRow['lname'];
	$patientName = $patFname." ".$patMname." ".$patLname;
    
	$elem_per_vo			= $objTests->get_tests_VO_access_status();

	//iMedicMonitor status.
	$objTests->patient_whc_room();

	//----GET ALL ACTIVE TESTS FROM ADMIN------
	$ActiveTests			= $objTests->get_active_tests();


	// FILE NAME for PRINT
	/*$date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/other_test_print_'.$_SESSION['authId']."_".$rand;
	mk_print_folder($test_name,$date_f,'common');*/

	//Retain QueryString
	$qstr4js="";
	if(isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])){
		$qstr4js .= "&".$_SERVER["QUERY_STRING"];
	}
	//Retain QueryString
	
	$elem_examDate = get_date_format(date('Y-m-d'));
	$elem_examTime = date('Y-m-d H:i:s'); //time();
	$elem_opidTestOrderedDate = ""; // $elem_examDate;

	//Assign Chart Notes specific user Type by checking the list
	if(in_array($userType,$GLOBALS['arrValidCNPhy'])){
		$userType = 1;
	}else if(in_array($userType,$GLOBALS['arrValidCNTech'])){
		$userType = 3;
	}
	
	//-----ORDER BY USERS------------
	$order_by_users									= $objTests->get_order_by_users('cn');

	//--------OPERATOR NAME----
	$elem_operatorId = (($userType == 1 || $userType == 12) || ($userType == 3)) ? $_SESSION["authId"] : "";
	$elem_operatorName = (($userType == 1 || $userType == 12) || ($userType == 3)) ? $objTests->getPersonnal3($elem_operatorId) : "";
	//GETTING FORM ID AND FINALIZED STATUS
	list($form_id,$finalize_flag)		= $objTests->get_chart_form_id($patient_id);
	
	//Previous Test --
	$oChartTestPrev = new ChartTestPrev($patient_id,"CUSTOM");
	
	//No Pop
	$noP = 1;
	if(isset($_GET["noP"]) && !empty($_GET["noP"])){
		$noP = $_GET["noP"];
	}

	//Test id
	$tId = 0;
	if(isset($_GET["tId"])){
		$tId = $_GET["tId"];
		//if you come directly without chart
		//Set Form Id zero
		$form_id = 0;
	}
	if(!empty($form_id)){
		//Get Form id based on patient id
		$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '".$patient_id."' AND test_template_id = '$test_master_id' AND formId = '$form_id' AND purged='0' AND del_status='0'";
		$res = imw_query($q);
		$row = imw_fetch_assoc($res);
	}else if(isset($tId) && !empty($tId)){//Get record based on patient id and test id
			$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '".$patient_id."' AND test_id = '".$tId."' AND test_template_id = '$test_master_id'";
			$res = imw_query($q);
			$row = imw_fetch_assoc($res);
	}else{
		$row = false; // open new test for patient
	}

	if($row == false){	//&& ($finalize_flag == 0)
		//SET MODE
		$test_mode = "new";
		$test_edid = "0";
	}else{
		$test_mode = "update";
		$test_edid = $row["test_id"];
	}

	if($row != false){
		$test_pkid = $row["test_id"];
		$test_form_id = $row["formId"];
		$elem_examDate = ($test_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate;
		$elem_examTime = ($test_mode != "new") ? $row["examTime"] : $elem_examTime ;
		$elem_testOtherName = $row["test_other"];
		$elem_topoMeterEye = $row["test_other_eye"];
		$elem_performedBy = ($test_mode != "new") ? $row["performedBy"] : "";
		$elem_ptUnderstanding = $row["ptUnderstanding"];
		$elem_diagnosis = $row["diagnosis"];
		$elem_diagnosisOther = $row["diagnosisOther"];
		$elem_reliabilityOd = $row["reliabilityOd"];
		$elem_reliabilityOs = $row["reliabilityOs"];

		$elem_physician = ( $test_mode == "update" ) ? $row["phyName"] : "" ;
		$elem_techComments = stripslashes($row["techComments"]);
		$encounterId = $row["encounter_id"];
		$elem_opidTestOrdered = $row["ordrby"];
        $orderedBy=$order_by_users[$elem_opidTestOrdered];
		if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){
			$elem_opidTestOrderedDate = get_date_format($row["ordrdt"]);
		}
		$purged=$row["purged"];
		$sign_path = $row["sign_path"];
		$sign_path_date_time = $row["sign_path_date_time"];
		$sign_path_date = $sign_path_time = "";
		if($sign_path && $sign_path_date_time!="0000-00-00 00:00:00" || $sign_path_date_time!=0) {
			$sign_path_date = date("m-d-Y",strtotime($sign_path_date_time));
			$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
		}
	
		//GET VERSION HTML DETAILS
		$this_version_data			= $objTests->get_template_test_version_data($this_test_properties['id'],$row['version']);
		$elem_version_id			= $this_version_data['id'];
		$elem_test_main_options 	= $this_version_data['test_main_options'];
		$elem_test_results 			= $this_version_data['test_results'];
		$elem_test_treatment 		= $this_version_data['test_treatment'];
		
		//this version data values
		$test_main_options 			= $row['test_main_options'];
		$test_result				= $row['test_result'];
		$test_treatment				= $row['test_treatment'];
	}


	//Performed Id
	if(empty($elem_performedBy) && (($userType == 1 || $userType == 12) || ($userType == 3))){
		$elem_performedBy = $logged_user;
	}

	//Interpreted By
	if(empty($elem_physician)){
		if($userType == '1'){
			$elem_phyName_order = $logged_user;
		}
	}

	//Current Performed by logged in
	$elem_performedByCurr = "";
	if(($userType == 1 || $userType == 12) || ($userType == 3)){
		$elem_performedByCurr = (empty($elem_performedBy) || (($userType == 3))) ? $logged_user : $elem_performedBy;
	}	
}

//---------------------------------PRINT HTML START BELOW------------------------------

ob_start();
$yes = ' (y)'; 	$no = ' (n)';	
$trHeight = "15";
$td1Width = "56";
if($finalize_flag == 1){
	$finalize = "&nbsp;&nbsp;&nbsp;&nbsp;(Finalized)";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<script type="text/javascript" src="../../library/js/jquery.min.1.12.1.js"></script>
</head>
<body id="body_main_content">
<style>
table{ font-size:12px; font-weight:normal;}
.tb_heading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#BCD5E1;
}
.text_b{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000;
	background-color:#BCD5E1;
}
.text{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
.txt_11b { font-size:12px; font-weight:bold; }
.alignLeft{ text-align:left; }
.alignCenter{ text-align:center; }
.alignRight{ text-align:right; }
.valignTop { vertical-align:top; }
.alignMiddle { vertical-align:middle; }
.table_collapse { padding:0px; border-collapse:collapse; margin:0px; }
.drak_purple_color {  color: #990099; font-weight:bold; }
.blue_color { color: #0000FE; font-weight:bold;}
.green_color { color: #008000; font-weight:bold;}
</style>
<page backtop="9mm" backbottom="0.5mm">		
    <page_footer>
        <table style="width:100%;">
            <tr>
                <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
            </tr>
        </table>
    </page_footer>	
    <page_header>
        <table border="0" cellpadding="0" cellspacing="0">				
                <tr>
                    <td colspan="3" align="center" style="width:1080px" class="text_b"><b><?php echo $this_test_screen_name.$finalize;?></b>
                    </td>
                </tr>
                <tr>
                    <td class="text_b" align="left" style="width:350px">Ordered By <?php echo $orderedBy;?> on <?php echo $elem_opidTestOrderedDate;?></td>
                    <td style="width:450px;" class="text_b" align="center">Patient Name:&nbsp;&nbsp;<?php echo $patientName.' - ('.$patient_id.')';?></td>
                    <td class="text_b" align="right" style="width:280px">DOS:&nbsp;&nbsp;<?php echo FormatDate_show($elem_examDate);?></td>
                </tr>
        </table>
    </page_header>
    <table class="alignLeft" style="width:1080px;" border="1">
        <tr class="alignLeft alignMiddle">
            <td colspan="2" class="txt_11b alignLeft">
                <table style="width:100%" border="0" >
                    <tr style="height:20px">
                        <td style="width:55%" id="test_main_options">&nbsp;                 </td>
                        <td style="width:45%" align="left">
                                <strong><?php echo $elem_testOtherName;?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="drak_purple_color">OU</span>
                                <?php echo ($elem_topoMeterEye == "OU") ? $yes : $no ;?>&nbsp;&nbsp;
                                <span class="blue_color">OD</span>
                                <?php echo ($elem_topoMeterEye == "OD") ? $yes : $no ;?>&nbsp;&nbsp;
                                <span class="green_color ">OS</span>
                                <?php echo ($elem_topoMeterEye == "OS") ? $yes : $no ;?>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;" class="alignLeft" colspan="2">
                          <div style="width:100%;"><strong>Technician Comments</strong>&nbsp;:&nbsp;<?php echo $elem_techComments; ?></div>
                        </td>
                    </tr>                	
                </table>
            </td>
        </tr>
        <tr class="alignLeft alignMiddle">
            <td style="height:25px" colspan="2" >
                <strong>Performed By</strong>&nbsp;:&nbsp;<?php echo $objTests->getPersonnal3($elem_performedBy);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <strong>Patient Understanding & Cooperation</strong> &nbsp;:&nbsp;
                Good<?php echo ($elem_ptUnderstanding == '' || $elem_ptUnderstanding == "Good") ? " (yes)" : "" ;?>&nbsp;&nbsp;
                Fair<?php echo ($elem_ptUnderstanding == "Fair") ? " (yes)" : "" ;?>&nbsp;&nbsp;
                Poor<?php echo ($elem_ptUnderstanding == "Poor") ? " (yes)" : "" ;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <strong>Diagnosis</strong>&nbsp;:&nbsp;<?php 
                 if($elem_diagnosis=='Other') { echo $elem_diagnosisOther; }else { echo $elem_diagnosis;} ?>
            </td>
        </tr>
        <tr class="alignLeft alignMiddle" >
            <td style="height:20px;" colspan="2"><strong>Physician Interpretation</strong>: -</td>
        </tr>
        <tr class="alignLeft alignMiddle">
            <td style="height:20px; width:55%" >
                <strong>Reliability</strong> &nbsp;:&nbsp;
                <span class="blue_color txt_11b">OD</span>&nbsp;&nbsp;
                Good<?php echo (!$elem_reliabilityOd || $elem_reliabilityOd == "Good") ? " (yes)" : "";?>&nbsp;&nbsp;
                Fair<?php echo ($elem_reliabilityOd == "Fair") ? " (yes)" : "";?>&nbsp;&nbsp;
                Poor<?php echo ($elem_reliabilityOd == "Poor") ? " (yes)" : "";?>&nbsp;&nbsp;
            </td>
            <td style="width:45%">
                <span class="green_color ">OS</span>&nbsp;&nbsp;
                Good<?php echo (!$elem_reliabilityOs || $elem_reliabilityOs == "Good") ? " (yes)" : "";?>&nbsp;&nbsp;
                Fair<?php echo ($elem_reliabilityOs == "Fair") ? " (yes)" : "";?>&nbsp;&nbsp;
                Poor<?php echo ($elem_reliabilityOs == "Poor") ? " (yes)" : "";?>&nbsp;&nbsp;
            </td>
        </tr>
        <tr class="alignLeft alignMiddle" >
            <td style="height:20px;" colspan="2" ><strong>Test Results</strong>: -</td>
        </tr>
        <tr class="alignLeft alignMiddle">
            <td style="height:20px;" colspan="2" class="txt_11b alignLeft">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="width:120px;" class="drak_purple_color alignCenter alignMiddle">&nbsp;</td>
                        <td style="width:470px;" class="alignCenter blue_color" >OD</td>
                        <td style="width:470px;" class="alignCenter green_color">OS</td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" border="0" style="width:100%;"> 
                    <tbody id="tbl_test_results"></tbody>
                </table>
            </td>
        </tr>
        <tr class="alignLeft alignMiddle">
            <td style="height:20px;" colspan="2" class="txt_11b">Treatment/Prognosis : -</td>
        </tr>
        <tr>
            <td colspan="2" id="test_treatment_options"></td>
        </tr>       
        <tr><td colspan="2" style="height:10px"></td></tr>
        <tr>  
           <td  class="alignLeft" ><strong>Interpreted By:</strong> &nbsp;&nbsp;
                <?php
                    if($elem_physician){
                        $getPersonnal3 = $objTests->getPersonnal3($elem_physician);
                    }else{
                        $getPersonnal3 = '';
                    }
                    echo $getPersonnal3;
                ?>
            </td>
            <td><strong>Future Appointments:</strong>&nbsp;&nbsp;<?php $data = $objTests->getFutureApp($patient_id);echo $data;?></td>
        </tr>
        <tr><td colspan="2" id="insert_sign_html_here"></td></tr>
    </table>
</page>
</body>
</html>