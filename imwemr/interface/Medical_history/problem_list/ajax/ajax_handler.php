<?php
include_once('../../../../config/globals.php');
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/problem_list.class.php");
$medical = new MedicalHistory($_REQUEST['showpage']);
$problem_obj = new ProblemLst($medical->current_tab);       // Create new instance of ProblemList class
$patientName = $problem_obj->get_patient_name($_SESSION['patient']);        // Get patient name
$patientNameAndIdArray = explode('-', trim($patientName));      // Explode patient name and id string to name and id
//$patientId

///Modify 
if(isset($_REQUEST['edit_id']) && trim($_REQUEST["edit_id"])!= ""){
	$json_arr = array();
	$json_arr['oper_name'] = $operatorNameEDIT = $problem_obj->edit_record($_REQUEST["edit_id"]);
	$json_arr['problem_list_arr'] = $arrProblemListID = $problem_obj->arr_problem_list;
	echo json_encode($json_arr);
	exit();
}

//Checking UMLS_PL 
if(isset($_REQUEST['medName']) && isset($_REQUEST['index']) && $_REQUEST['medName'] != ''){
	$str_return = $problem_obj->check_umls_pl($_REQUEST['medName'],$_REQUEST['index']);
	echo $str_return;
	exit();
}


//Delete problem list 
if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "delete" && isset($_REQUEST['del_check'])){
	//pre($_REQUEST);
	$del_status = $problem_obj->delete_pro_list($_REQUEST);
	echo $del_status;
	exit();
}


//Get Diagnos codes
if(isset($_REQUEST['get_typeahead_arr']) && $_REQUEST['get_typeahead_arr'] != ''){
	list($strTHDesc,$strTHPracCode,$strTHDesc2,$strSnowmed_ct,$desc_prac_code) = $problem_obj->get_dx_ths();
	$return_arr['strTHDesc'] = explode('~~~',$strTHDesc);
	$return_arr['strTHPracCode'] = explode(',',$strTHPracCode);
	$return_arr['strTHDesc2'] = explode(',',$strTHDesc2);
	$return_arr['strSnowmed_ct'] = explode(',',$strSnowmed_ct);
	$return_arr['desc_prac_code'] = $desc_prac_code;
	echo json_encode($return_arr);
	exit();
}
if($_GET['ajax_req'] && $_GET['ajax_req'] == "problem_list"){       // If request is for showing problem list in problem list popup
    if($_GET['status'] && $_GET['status'] != ""){       // If status is not empty
        $status = $_GET['status'];      // Get status from request
    }
    echo $problem_obj->getPatientProblemsList($status);         // Echo problem list (according to status) to browser
    exit();
}
if($_POST['problems'] && $_POST['problems'] != ""){                 // If request is for saving, deleting or updating problems
    $problem_obj->save_problem_list_from_popup($_POST);         // Save problem list
    exit();
}
if($_GET['ajax_req'] && $_GET['ajax_req'] == "body"){ // If request is for getting body content
	// Echo content to browser
    echo "<div class='whtbox patprob'>";
    echo "<div class='patprobhead'>";
    echo "<div class='row'>";
    echo "<div class='col-sm-6'>";
    echo "<h2>";
    $patientNameArray = explode(' ', trim($patientNameAndIdArray[0]));      // Explode patient name to first, middle and last names
    $lastName = $patientNameArray[sizeof($patientNameArray) - 1];
    $firstAndMiddleNames = "";
    for($i = 0;$i<(sizeof($patientNameArray) - 1);$i++){
        $firstAndMiddleNames .= " ".$patientNameArray[$i];
    }
    echo "<span id='patientname'>".$lastName.", ".$firstAndMiddleNames." - ".$patientNameAndIdArray[1]."</span>";       // Show patient's name and id
    echo "</h2>";
    echo "</div>";
    echo "<div class='col-sm-6 text-right'>";
    echo "<div class='ptrht'>";
    echo "<div class='dropdown'>";
    echo "<select id='ddlStatus' name='ddlStatus' class='minimal form-control' style='width:100px;'>";
    echo "<option value='All'>All</option>";
    echo "<option value='Active' selected>Active</option>";
    echo "<option value='External'>External</option>";
    echo "<option value='Inactive'>Inactive</option>";
    echo "<option value='Resolved'>Resolved</option>";
    echo "<option value='Unobserved'>Unobserved</option>";
    echo "</select>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "<form id='problemListForm'>";
    echo "<div id='problemlist' class='ptlstdtl'>";
    echo "<div class='clearfix'></div>";
    echo "</div>";
    echo "</form>";
	echo "</div>";
    exit();
}
?>