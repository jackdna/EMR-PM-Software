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
?>
<?php
include_once(dirname(__FILE__) . "/../../config/globals.php");

//-----  Get data from remote server -------------------

$zRemotePageName = "patient_order_set_docs";
//require(dirname(__FILE__)."/get_chart_from_remote_server.inc.php");

//-----  Get data from remote server -------------------

require_once($GLOBALS['srcdir'] . "/classes/cls_common_function.php");
require_once($GLOBALS['srcdir'] . "/classes/Functions.php");
$clsCommon = new CLSCommonFunction;
$objManageData = new ManageData;

ob_start();
//--- GET DEFAULT GROUP NAME ---
$groupQry = "select groups_new.name, groups_new.group_Address1, groups_new.group_Address2, groups_new.group_Zip,
			groups_new.group_City, groups_new.group_State, groups_new.group_Telephone, groups_new.gro_id,
			groups_new.group_Fax from groups_new join facility on facility.default_group = groups_new.gro_id 
			and facility.facility_type = '1' where groups_new.del_status='0'";
$groupQryRes = $clsCommon->mysqlifetchdata($groupQry);
$group_name = $groupQryRes[0]['name'];
$group_id = $groupQryRes[0]['gro_id'];
$group_Address = $groupQryRes[0]['group_Address1'];
if(empty($groupQryRes[0]['group_Address2']) === false){
	$group_Address .= ' '.$groupQryRes[0]['group_Address2'];
}
$group_Address .= ' '.$groupQryRes[0]['group_City'];
$group_Address .= ', '.$groupQryRes[0]['group_State'];
$group_Address .= ' '.$groupQryRes[0]['group_Zip'];

$date_of_order = get_date_format(date('Y-m-d'));
$group_Telephone = core_phone_format($groupQryRes[0]['group_Telephone']);
$group_Fax = core_phone_format($groupQryRes[0]['group_Fax']);
//$objManageData->Smarty->assign("date_of_order", getDateFormat(date('Y-m-d')));
//$objManageData->Smarty->assign("group_name", $group_name);
//$objManageData->Smarty->assign("group_Address", $group_Address);
//$objManageData->Smarty->assign("group_Telephone", core_phone_format($groupQryRes[0]['group_Telephone']));
//$objManageData->Smarty->assign("group_Fax", core_phone_format($groupQryRes[0]['group_Fax']));

//--- PATIENT DETAILS ----
$patient_id = $_SESSION['patient'];
$pat_qry = "select * from patient_data where id = '$patient_id'";
$patQryRes = $clsCommon->mysqlifetchdata($pat_qry);
$patient_name_arr = array();
$patient_name_arr["LAST_NAME"] = $patQryRes[0]['lname'];
$patient_name_arr["FIRST_NAME"] = $patQryRes[0]['fname'];
$patient_name_arr["MIDDLE_NAME"] = $patQryRes[0]['mname'];
$patient_name = $objManageData->__changeNameFormat($patient_name_arr);
$patient_name .= ' - '.$patient_id;
//$objManageData->Smarty->assign("patient_name", $patient_name);

$patient_address = $patQryRes[0]['street'];
if(empty($patQryRes[0]['street2']) === false){
	$patient_address .= ' '.$patQryRes[0]['street2'];
}
$patient_address .= ' '.$patQryRes[0]['city'];
$patient_address .= ', '.$patQryRes[0]['state'];
$patient_address .= ' '.$patQryRes[0]['postal_code'];

//$objManageData->Smarty->assign("patient_address", $patient_address);
//$objManageData->Smarty->assign("gender_info", $patQryRes[0]['sex']);
$gender_info = $patQryRes[0]['sex'];
$patient_dob = $patQryRes[0]['DOB'];
$patient_dob = get_date_format($patient_dob);
//$objManageData->Smarty->assign("patient_dob", $patient_dob);

$patient_home_ph = core_phone_format($patQryRes[0]['phone_home']);
$patient_work_ph = core_phone_format($patQryRes[0]['phone_biz']);
$patient_cell_ph = core_phone_format($patQryRes[0]['phone_cell']);
//$objManageData->Smarty->assign("patient_home_ph", core_phone_format($patQryRes[0]['phone_home']));
//$objManageData->Smarty->assign("patient_work_ph", core_phone_format($patQryRes[0]['phone_biz']));
//$objManageData->Smarty->assign("patient_cell_ph", core_phone_format($patQryRes[0]['phone_cell']));

//--- GET ALL ORDER SET DETAILS ----
$sql = "select id,orderset_name,order_id,order_set_option from order_sets";
$orderSetDetails = $clsCommon->mysqlifetchdata($sql);
$orderSetOption = '';
$orderSetNameArr = array();
$selectedOrderIdArr1 = array();
$order_id_arr = array();
$orderIdArr = array();
$order_set_option_arr = array();
$ordersArr = array();
for($i=0;$i<count($orderSetDetails);$i++){
	$id = $orderSetDetails[$i]['id'];
	$orderset_name = $orderSetDetails[$i]['orderset_name'];
	$orderSetNameArr[$id] = $orderset_name;
	$order_id = $orderSetDetails[$i]['order_id'];
	$order_set_option_arr[$id]=$orderSetDetails[$i]['order_set_option'];
	$ordersArr[$id] = preg_split('/,/',$order_id);
}

//--- GET ALL ORDERS DETAILS ----
$sql1 = "select * from order_details";
$ordersQryRes = $clsCommon->mysqlifetchdata($sql1);
$ordersDetailsArr = array();
$inf_order_arr = array();
for($o=0;$o<count($ordersQryRes);$o++){
	$id = $ordersQryRes[$o]['id'];
	$ordersDetailsArr[$id] = $ordersQryRes[$o];
	$o_type = $ordersQryRes[$o]['o_type'];
	preg_match('/Information/',$o_type,$inf_check);
	if(count($inf_check)>0){
		$inf_order_arr[] = $id;
	}
}

//---- GET ORDER SET DETAILS ----
$formId = $_SESSION['form_id'];

//--- FORM ID IF CHART NOTE FINALIZED -----
if(empty($formId) == true){
	$formId = $_SESSION['finalize_id'];
}
require_once(dirname(__FILE__).'/print_order_sets.php');

$patient_orders = $page_data;
//$objManageData->Smarty->assign("patient_orders", $page_data);

$orderQry = "select * from order_set_associate_chart_notes where patient_id = '$patient_id'
			and form_id = '$formId' and plan_num = '$plan_num' and delete_status = '0'";
$orderQryRes = $clsCommon->mysqlifetchdata($orderQry);

//--- GET PROVIDER ID ---
$pro_qry = "select doctorId, cosigner_id, assess_plan from chart_assessment_plans 
		where patient_id = '$patient_id' and form_id = '$formId'";
$proQryRes = $clsCommon->mysqlifetchdata($pro_qry);
$providerId = $proQryRes[0]['doctorId'];
if(empty($providerId) === true){
	$providerId = $proQryRes[0]['cosigner_id'];
}
//--- PATIENT DEFAULT PROVIDER ID ---
if(empty($providerId) === true){
	$providerId = $patQryRes[0]['providerID'];
}
$assess_plan = $proQryRes[0]['assess_plan'];
if(empty($assess_plan) === false){
	file_put_contents(dirname(__FILE__).'/xml/assess_plan.xml', $assess_plan);
	$assess_plan_obj = simplexml_load_file(dirname(__FILE__).'/xml/assess_plan.xml');
	$assess_plan_arr = (array)$assess_plan_obj->data;
	$plan_data_obj = $assess_plan_arr['ap'][$plan_num - 1];

	$assess_data = $plan_data_obj->assessment;
	$plan_data = $plan_data_obj->plan;

	//$objManageData->Smarty->assign("assess_data", $assess_data);
	//$objManageData->Smarty->assign("plan_data", $plan_data);
}

//---- PROVIDER NAME ----
$proQry = "select lname, fname, mname from users where id = '$providerId'";
$proQryRes = $clsCommon->mysqlifetchdata($proQry);
$pro_name_arr = array();
$pro_name_arr["LAST_NAME"] = $proQryRes[0]['lname'];
$pro_name_arr["FIRST_NAME"] = $proQryRes[0]['fname'];
$pro_name_arr["MIDDLE_NAME"] = $proQryRes[0]['mname'];
$physician_name = $objManageData->__changeNameFormat($pro_name_arr);

//$objManageData->Smarty->assign("physician_name", $physician_name);

//--- SAVE PROVIDER SIGNATURE IMAGE ----
$id = $providerId;
$tblName = "users";
$pixelFieldName = "sign";
$idFieldName = "id";
$imgPath = "";
$saveImg = dirname(__FILE__)."/../../library/html_to_pdf/user_id_".$id.".jpg";
$imgNme = "user_id_".$id.".jpg";
//require(dirname(__FILE__)."/imgGd.php");

/**********imgGD.php Starts*********/
require_once(dirname(__FILE__)."/../../library/classes/imgGdFun.php");
// id,tbl,pixelField,idField,imgName

//Set values
$pixels = "";		
//Get Values
if(!empty($_GET["id"]))
{
    $id = $_GET["id"];
    $tbl = $_GET["tbl"];
    $pixelField = $_GET["pixelField"];
    $idField = $_GET["idField"];
    $imgName = $_GET["imgName"];
    $saveImg = $_GET["saveImg"];
}else{	
    $id = $id;
    $tbl = $tblName;
    $pixelField = $pixelFieldName;
    $idField = $idFieldName;
    $imgName = $imgPath;
    $saveImg = $saveImg;
}

if(!empty($id)){
    $qry = "SELECT $pixelField FROM $tbl WHERE $idField = $id";		
    $row = sqlQuery($qry);	
    $pixels = $row[$pixelField];
}
//Get Image	
drawOnImage_new($pixels,$imgName,$saveImg);
/**********imgGD.php Ends*********/

//$objManageData->Smarty->assign("imgNme", $imgNme);

//$objManageData->Smarty->display(dirname(__FILE__)."/patient_order_set_docs.tpl");
include(dirname(__FILE__)."/patient_order_set_docs_print.php");

$file_content = ob_get_contents();
ob_end_clean();

//--- SAVE PRINT ORDERS DATA FOR PDF PRINTING ----
$data_arr = array();
$data_arr['patient_id'] = $patient_id;
$data_arr['providerId'] = $providerId;
$data_arr['group_id'] = $group_id;
$data_arr['formId'] = $formId;
$data_arr['order_file_content'] = $file_content;
$data_arr['created_date'] = date('Y-m-d');
$data_arr['created_by'] = $_SESSION['authId'];

AddRecords($data_arr, 'print_orders_data');

//--- CREATE HTML FILE FOR PDF PRINTING ---
$html_file_name = 'print_order_details_'.$_SESSION['authId'].'.html';
$file_location = data_path().'UserId_'.$_SESSION['authId'].'/tmp/'.$html_file_name;

if(constant("REMOTE_SYNC") == 1 && $zOnParentServer==1){
	
	file_put_contents($file_location, $file_content);	
	$urlpdf = checkUrl4Remote($GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?file_name=$file_location");	
	$zRemoteServerData["header"] = $urlpdf;

}else{

file_put_contents($file_location, $file_content);

header("Location: ".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?file_location=".$file_location);

}

?>