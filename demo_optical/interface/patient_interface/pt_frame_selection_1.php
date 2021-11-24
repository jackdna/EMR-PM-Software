<?php
require_once(dirname(__FILE__)."/../../config/config.php"); 
require_once(dirname(__FILE__)."/../../library/classes/functions.php"); 	
require_once(dirname(__FILE__)."/../../library/classes/common_functions.php");
require_once(dirname(__FILE__)."/../../library/classes/drop_down.php");

$pageName = "frameLensSelection"; /*Variable for POS column Setting*/

$objDropDown = new dropDown();
if($_REQUEST['order_id']!="")
{
	$_SESSION['order_id']=$_REQUEST['order_id'];
}
$patient_id=$_SESSION['patient_session_id'];
$order_id=$_SESSION['order_id'];
$action=$_REQUEST['frm_method'];
$sel_pic=$_REQUEST['sel_pic'];
$pt_wear_pic=$_REQUEST['pt_wear_pic_1'];
$order_detail_id=$_REQUEST['order_detail_id'];

$log_loc_arr = array();
$log_loc_arr[$_SESSION['pro_fac_id']]=$_SESSION['pro_fac_id'];
$main_ord_qry=imw_query("select del_status, order_enc_id, re_make_id, order_status, loc_id, DATE_FORMAT(entered_date, '%m-%d-%Y') AS 'created_date' from in_order where id='".$_SESSION['order_id']."' and id>0");
$main_ord_row=imw_fetch_array($main_ord_qry);
$idoc_enc_id = (isset($main_ord_row['order_enc_id']))?(int)$main_ord_row['order_enc_id']:0;
$order_del_status=$main_ord_row['del_status'];
$lab_whr="";
if($GLOBALS['connect_visionweb']!=""){
	$vw_lab_ids="";
	if($main_ord_row['loc_id']>0){
		$log_loc_arr = array();
		$log_loc_arr[$main_ord_row['loc_id']]=$main_ord_row['loc_id'];
	}
	$log_loc_imp=implode("','",$log_loc_arr);
	$vw_usr_qry=imw_query("select lab_id from in_vision_web join in_vw_user_lab on in_vw_user_lab.vw_user_id=in_vision_web.id 
	where in_vision_web.vw_loc_id in('".$log_loc_imp."') group by lab_id");
	while($vw_usr_row=imw_fetch_array($vw_usr_qry)){
		if($vw_lab_ids!=""){
			$vw_lab_ids.="','";
		}
		$vw_lab_ids.=$vw_usr_row['lab_id'];
	}
	$lab_whr=" and id in('".$vw_lab_ids."')";
}
/*Lens Labs*/
$lens_labs = array();
$vw_lab_id_arr=array();
$labs_sql = "SELECT `id`, `lab_name`,vw_lab_id FROM `in_lens_lab` WHERE del_status=0 $lab_whr ORDER BY `lab_name` ASC";
$labs = imw_query($labs_sql);
if($labs && imw_num_rows($labs)>0){
	while($lab_row = imw_fetch_assoc($labs)){
		$lens_labs [$lab_row['id']] = $lab_row['lab_name'];
		if($lab_row['vw_lab_id']>0){
			$vw_lab_id_arr[$lab_row['vw_lab_id']] = $lab_row['id'];
		}
	}
}
/*Lens Labs*/

//----------------LAST DOS AND PRIMARY EYE CARE PHYSICIAN NAME-----------------------------//
$physicianName = $lastDos = "";
$sqldos = 'SELECT DATE_FORMAT(`cm`.`date_of_service`, "%m-%d-%Y") AS "dos" FROM `chart_master_table` `cm` WHERE `cm`.`patient_id`="'.$patient_id.'" ORDER BY `cm`.`date_of_service` DESC LIMIT 1';
$sqldos = imw_query($sqldos);
if($sqldos && imw_num_rows($sqldos)>0){
	$row = imw_fetch_assoc($sqldos);
	$lastDos = $row['dos'];
}
$sqlPrimaryPhy = 'SELECT IF(`u`.`fname`<>"" AND `u`.`lname`<>"", CONCAT(`u`.`lname`, ", ", `u`.`fname`), IF(`u`.`lname`<>"", `u`.`lname`, IF(`u`.`fname`<>"",`u`.`fname`,""))) AS "username" FROM `patient_data` `pd` LEFT JOIN `users` `u` ON `pd`.`providerID`= `u`.`id` WHERE `pd`.`id`="'.$patient_id.'" LIMIT 1';
$respPhy = imw_query($sqlPrimaryPhy);
if($respPhy && imw_num_rows($respPhy)>0){
	$row = imw_fetch_assoc($respPhy);
	$physicianName = $row['username'];
}

$physicianNameDisp = $physicianName;
if( mb_strlen($physicianName) > 20){
	$physicianNameDisp = mb_substr($physicianName, 0, 20);
	$physicianNameDisp = $physicianNameDisp.'...';
}
$physicianName = addslashes($physicianName);
$physicianNameDisp = addslashes($physicianNameDisp);
//-------------END LAST DOS AND PRIMARY EYE CARE PHYSICIAN NAME---------------------------//

//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$proc_code_desc_arr=array();
	$sql = "select * from cpt_category_tbl order by cpt_category ASC";
	$rez = imw_query($sql);	
	while($row=imw_fetch_array($rez)){
		$cat_id = $row["cpt_cat_id"];		
		$sql = "select * from cpt_fee_tbl WHERE cpt_cat_id='".$cat_id."' AND LOWER(status)='active' AND delete_status = '0' order by cpt_prac_code ASC";
		$rezCodes = imw_query($sql);
		$arrSubOptions = array();
		if(imw_num_rows($rezCodes) > 0){
			while($rowCodes=imw_fetch_array($rezCodes)){
				$arrSubOptions[] = array($rowCodes["cpt_prac_code"]."-".$rowCodes["cpt_desc"],$xyz, $rowCodes["cpt_prac_code"]);
				$arrCptCodesAndDesc[] = $rowCodes["cpt_fee_id"];
				$arrCptCodesAndDesc[] = $rowCodes["cpt_prac_code"];
				$arrCptCodesAndDesc[] = $rowCodes["cpt_desc"];
				
				$code = $rowCodes["cpt_prac_code"];
				$cpt_desc = $rowCodes["cpt_desc"];
				$stringAllProcedures.="'".str_replace("'","",$code)."',";	
				$stringAllProcedures.="'".str_replace("'","",$cpt_desc)."',";
				$proc_code_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_prac_code"];
				$proc_code_desc_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_desc"];
			}
		$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
		}		
	}

	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//

//------------------------	START GETTING DATA FOR MENUS TO DX Code -----------------------//
	$dx_code_arr=array();
	/*$sql_dx = "select * from diagnosis_category order by category ASC";
	$rez_dx = imw_query($sql_dx);	
	while($row_dx=imw_fetch_array($rez_dx)){
		$cat_id_dx = $row_dx["diag_cat_id"];		
		$sql_dx = "select * from diagnosis_code_tbl WHERE diag_cat_id='".$cat_id_dx."' AND delete_status = '0' order by d_prac_code ASC";
		$rezCodes_dx = imw_query($sql_dx);
		$arrSubOptions_dx = array();
		if(imw_num_rows($rezCodes_dx) > 0){
			while($rowCodes_dx=imw_fetch_array($rezCodes_dx)){
				$arrSubOptions_dx[] = array($rowCodes_dx["d_prac_code"]."-".$rowCodes_dx["diag_description"],$xyz, $rowCodes_dx["d_prac_code"]);
				$arrdxCodesAndDesc[] = $rowCodes_dx["diagnosis_id"];
				$arrdxCodesAndDesc[] = $rowCodes_dx["d_prac_code"];
				$arrdxCodesAndDesc[] = $rowCodes_dx["diag_description"];
				
				$code_dx = $rowCodes_dx["d_prac_code"];
				$dx_desc = $rowCodes_dx["diag_description"];
				$stringAllProcedures_dx.="'".str_replace("'","",$code_dx)."',";	
				$stringAllProcedures_dx.="'".str_replace("'","",$dx_desc)."',";
				$dx_code_arr[$rowCodes_dx["diagnosis_id"]]=$rowCodes_dx["d_prac_code"];
			}
		$arrdxCodes[] = array($row["category"],$arrSubOptions_dx);
		}		
	}

	$stringAllProcedures_dx = substr($stringAllProcedures_dx,0,-1);*/
	
	$icd10_sql_qry=imw_query("select id,icd10,icd10_desc from icd10_data where deleted='0'");
	while($icd10_sql_row=imw_fetch_array($icd10_sql_qry)){
		$icd10_dx=str_replace('-','',$icd10_sql_row['icd10']);
		$icd10_desc_arr[$icd10_dx]=$icd10_sql_row['icd10_desc'];
		$dx_code_arr[$icd10_sql_row["id"]]=$icd10_sql_row["icd10"];
	}
	
//-----------------  END GETTING DATA FOR MENUS TO CATEGORY OF DX Code	------------------------//
	


/************ Start Getting Data for CPT Codes ************/
$proc_code_arr=array();
$proc_code_desc_arr=array();
$sql = "SELECT `cpt_fee_id`, `cpt_prac_code`, `cpt_desc` FROM `cpt_fee_tbl` 
		WHERE `status`='active' AND `delete_status`='0'
		ORDER BY `cpt_prac_code` ASC";
$sql = imw_query($sql);
if($sql && imw_num_rows($sql)>0){
	while($row = imw_fetch_assoc($sql)){
		$proc_code_arr[$row['cpt_fee_id']] = $row['cpt_prac_code'];
		$proc_code_desc_arr[$row['cpt_fee_id']] = $row['cpt_desc'];
	}
}
imw_free_result($sql);
/************ End Getting Data for CPT Codes ************/


$getins_data="SELECT insct.case_name,insc.ins_caseid, insc.ins_case_type 
				FROM insurance_case_types insct 
				JOIN insurance_case insc ON (insc.ins_case_type=insct.case_id AND insc.case_status ='Open') 
				JOIN insurance_data insd ON (insd.ins_caseid=insc.ins_caseid AND insd.provider >0) 
				JOIN insurance_companies inscomp ON (inscomp.id=insd.provider AND inscomp.in_house_code !='n/a') 
				WHERE insc.patient_id='$patient_id'
				GROUP BY insc.ins_caseid 
				ORDER BY insc.ins_case_type";
$getins_data_qry = imw_query($getins_data);
while($getins_data_row = imw_fetch_array($getins_data_qry)){
	$insCasesArr = $getins_data_row['case_name'].'-'.$getins_data_row['ins_caseid'];
	$ins_case_arr[$getins_data_row['ins_caseid']]=$insCasesArr;
}

/*Desgin Options - Default*/
/*$design_options = array();
$design_resp = imw_query("SELECT 
	`id`, 
	`design_name` AS 'name'
FROM 
	`in_lens_design` 
WHERE 
	`del_status` = '0' 
ORDER BY 
	`design_name` ASC");
while($row = imw_fetch_array($design_resp)){
	array_push($design_options, $row);
}*/
/*Design Options*/

/******************************** Order Detail ***************************/
$action=$_REQUEST['frm_method'];
$order_detail_id=$_REQUEST['order_detail_id'];

/*Frame Colors*/
$sql = "SELECT id, color_name, color_code FROM in_frame_color WHERE del_status = 0 ORDER BY color_name ASC";
$res = imw_query($sql);
$frameColors = array();
while($row = imw_fetch_assoc($res)){
	$id = $row['id'];
	$frameColors[$id]['name'] = $row['color_name'];
	$frameColors[$id]['code'] = $row['color_code'];
}

/*Frame Shape Options*/
$frameShapes = $objDropDown->get_frame_shape_arr();

/*Seg Type Default Codes key*/
$default_vals = array('SV'=>'type_sv', 'PAL'=>'type_pr', 'BFF'=>'type_bf', 'TFF'=>'type_tf');

/*Lens Vision*/
$lens_vision_array = array('ou', 'od', 'os');

/*Order Edit operations*/
$order_edit_btn_status = true;
$order_post_btn_status = true;
$no_save = "'ordered', 'received', 'dispensed'"; /*Status for which save not allowed*/
if( $order_id != '' && $order_id > 0 ){
	$order_detail_status = imw_query('SELECT COUNT(\'id\') AS \'count\' FROM `in_order_details` WHERE `order_id`=\''.$order_id.'\' AND `order_status` IN('.$no_save.')');
	if($order_detail_status && imw_num_rows($order_detail_status)>0){
		$order_detail_status = imw_fetch_assoc($order_detail_status);
		$order_detail_status = (int)$order_detail_status['count'];
		if($order_detail_status > 0){
			$order_edit_btn_status = false;
		}
	}
	
	$no_post = "'received', 'dispensed'"; /*Status for Which Post not allowed*/
	$order_detail_status = imw_query('SELECT COUNT(\'id\') AS \'count\' FROM `in_order_details` WHERE `order_id`=\''.$order_id.'\' AND `order_status` IN('.$no_post.')');
	if($order_detail_status && imw_num_rows($order_detail_status)>0){
		
		$order_detail_status = imw_fetch_assoc($order_detail_status);
		$order_detail_status = (int)$order_detail_status['count'];
		if($order_detail_status > 0){
			$order_post_btn_status = false;
		}
	}
}
/*Vision WEb Condition for Frame type*/
$vw_type_whr="";if($GLOBALS['connect_visionweb']!=""){ $vw_type_whr=" and vw_code!=''";}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Optical</title>
<link href="tooltip.css?<?php echo constant("cache_version"); ?>" type="text/css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/interface/patient_interface/bootstrap.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.confirm.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/icd10dd.css?<?php echo constant("cache_version"); ?>" />

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.confirm.js?<?php echo constant("cache_version"); ?>"></script>

<!-- ICD 10 Files -->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/icd10/jquery-ui-1.10.4.custom.js?<?php echo constant("cache_version"); ?>" ype="text/javascript"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/icd10/icd10_autocomplete.js?<?php echo constant("cache_version"); ?>" type="text/javascript"></script>
<!-- END ICD 10 Files -->
<style>
.hideRow1{display:none;}
.listheading {
	height: 25px;
}
.label_style {
	margin: 0 5px;
}
.extra_margin {
	margin-top: 5px;
}
#all_data {
	border: 1px solid #CCC;
	margin-top: 5px;
	border-bottom: none;
}
.refData:nth-child(even) {
	background: rgba(0, 0, 0, 0.03);
}
.od_os_span span {
	/*float: left;*/
}
.pos_tab input {
	padding: 3px;
}
.span_head {
	font-weight: bold;
}
.input_upr input {
	float: left;
	margin: 0 5px 0 0;
}
select {
	margin: 0 5px 0 0;
}
.input_upr input[type=checkbox] {
	margin: 5px 2px 0px 0;
}
.span_data {
}
.span_data1 span {
	margin-top: 5px;
}
.od_os_span td {
	/*width: 40px;*/
	text-align: center;
	/*font-size:14px;*/
}
.od_os_span th {
	text-align: center;
	/*text-indent: 10px;*/
	/*font-size:14px;*/
}
.manufacturer_id, .brand_id, .shape_id {
	width: 50% !important;
	height: 23px;
}
/*.arrow_image {
	transform: rotate(180deg) !important;
	transition: .5s !important;
}*/
.searchIcon{text-decoration:none;}
.searchIcon>img{border:0px none;height:20px;width:20px;margin-left:2px;}
#confirmBox{left:28% !important;}
.rxBase{
	background-color:#C4C1C1;
	text-indent:0px !important;
}
th.rxBase{
	text-align:center;
}
.rxTable span.span_data{
	/*width:auto !important;*/
}
.multiDD>div.multiSelectOptions>label{font-family: Arial !important;font-size: 13.3px;clear:both;}
.multiDD>a[id*="_os_"]>span{width:200px !important;}
.multiDD>a[id*="_od_"]>span{width:191px !important;}
.multiDD>div.multiSelectOptions>label>input{margin:0px 2px 0px 0;vertical-align: top;float:none;}
.frameLabel{width:138px;float:left;margin-left:5px;}
.rxTable input{
	float:none;
	margin:0;
}
.icon_back{cursor:pointer;}
span.vision_od{color: blue;font-weight:bold;text-transform: uppercase;}
span.vision_os{color: green;font-weight:bold;text-transform: uppercase;}
.lens_label{width: 90px;float: left;}
.lens_vision{text-transform: uppercase;}
.lens_vision > option{text-transform: uppercase;}
.ouColor{color:#9900CC;}
.odColor{color: #0000FF;}
.osColor{color: #093;}
#loading{z-index:99999;}
#stock_large_image{
	background-color: rgba(0,0,0,0.2);
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
	display: none;
}
#stock_large_image>#stock_large{
	left:56px;position:relative;top:35px;border-radius:10px;
}
#stock_large_image>#stock_hide{
	position:absolute;top:45px;right:80px;z-index:1;cursor:pointer;
}
.stockDetails{
	background-color: rgba(0,0,0,0.75);
	color: #FFF;
	border-radius: 5px;
	padding: 2px;
	position: absolute;
	/*top: -73px;*/
	left: 3px;
	display: none;
	z-index: 10;
}
.stockDetails > table{
	width: 100%;
	border-collapse: collapse;
}
.stockDetails > table tr:not(:last-child) > td{border-bottom: 1px solid #FFF}
.stockDetails > table tr > td:last-child{padding-left:5px;}
.stockDetails > table tr > td:not(:last-child){padding-right:5px; border-right: 1px solid #FFF}
.traceFileExists{background-color:rgba(0,128,0,0.65);border-style:none;padding:3px 8px;border-radius:2px;border:1px solid rgba(0,128,0,0.9);color:#FFF;}
span[id^='traceFileName_']{font-family: sans-serif;}

.blackColor{
    color: #000000;
}

select > option{
	color: #000000;
}

select.disabledOpt{
	color: #BE0F0E;
}

select[id^="design_id_"]>option:disabled{
	color: #BE0F0E;
	background-color: #EEEEEE;
}
</style>
<script>
var PCP = '<?php echo $physicianName; ?>';
var PCP_DISP = '<?php echo $physicianNameDisp; ?>';
var order_edit_btn_status = '<?php echo (boolean)$order_edit_btn_status; ?>';
var order_post_btn_status = '<?php echo (boolean)$order_post_btn_status; ?>';
var order_del_status = '<?php echo ($order_del_status!='')?$order_del_status:0;?>';
var ADJACENT_QTY_DEDUCTION=<?php echo(defined('ADJACENT_QTY_DEDUCTION') && constant('ADJACENT_QTY_DEDUCTION')=='FALSE')?'false':'true';?>;

var dataChanged = false;
getDataFilePath = top.WRP+"/library/getICD10data.php"; /*ICD 10*/
function addNew_frame(){
	var LC = $("#last_cont").val();
	LC++;
	var newElem='<div class="row" id="all_data_'+LC+'" class="all_data refData">';
		newElem+='<div style="float:left;width:100%;border-bottom:1px solid #CCC;">';
	    	newElem+='<input type="hidden" name="module_type_id_'+LC+'" id="module_type_id_'+LC+'" value="1">';
			newElem+='<input type="hidden" name="order_detail_id_'+LC+'" id="order_detail_id_'+LC+'" value="">';
			newElem+='<input type="hidden" name="upc_id_'+LC+'" id="upc_id_'+LC+'" value="">';
			newElem+='<input type="hidden" name="item_prac_code_'+LC+'" id="item_prac_code_'+LC+'">';
			//newElem+='<input type="hidden" name="dx_code_'+LC+'" id="dx_code_'+LC+'" value="" onChange="get_dxcode(this);" style="width:100px;">';
			newElem+='<input type="hidden" name="item_id_'+LC+'" id="item_id_'+LC+'" value="">';
			newElem+='<input type="hidden" name="allowed_'+LC+'" id="allowed_'+LC+'" value="">';
			newElem+='<input name="discount_'+LC+'" type="hidden" class="s_tbx" id="discount_'+LC+'" onChange="chk_dis_fun();" value="">';
			newElem+='<input name="price_'+LC+'" id="price_'+LC+'"  type="hidden" class="s_tbx" onChange="this.value = parseFloat(this.value).toFixed(2); chk_dis_fun();" value="">';
			newElem+='<input name="price_hidden_'+LC+'" id="price_hidden_'+LC+'"  type="hidden" value="">';
			newElem+='<input name="discount_'+LC+'" type="hidden" class="s_tbx" id="discount_'+LC+'" onChange="chk_dis_fun();" value="">';
			newElem+='<input name="discount_hidden_'+LC+'" id="discount_hidden_'+LC+'"  type="hidden" value="">';
			newElem+='<input name="total_amount_'+LC+'" type="hidden" class="s_tbx" id="total_amount_'+LC+'" readonly value="">';
			newElem+='<input name="total_amount_hidden_'+LC+'" id="total_amount_hidden_'+LC+'"  type="hidden" value="">';
			newElem+='<input type="hidden" name="qty_'+LC+'" id="qty_'+LC+'" style="width:140px;" onChange="chk_dis_fun();" value="1">';
			newElem+='<input type="hidden" name="qty_hidden_'+LC+'" id="qty_hidden_'+LC+'"  value="1">';
			newElem+='<input type="hidden" name="item_comment_'+LC+'" id="item_comment_'+LC+'" style="width:140px;" value="">';
			
		/*Frame Section*/
		newElem+='<div class="col-md-4" style="padding-right:0;"><div class="row"><div class="col-md-12" style="padding-bottom:5px;width:98%;margin:0;padding-right:0;">';
			newElem+='<div class="row extra_margin">';
				newElem+='<div class="col-md-8 nopadd">';
					newElem+='<div class="row">';
						newElem+='<div class="col-md-12 nopadd-right">';
							newElem+='<label for="upc_name_'+LC+'" class="frameLabel">UPC</label>';
							newElem+='<input type="text" name="upc_name_'+LC+'" id="upc_name_'+LC+'" style="width:42%;" autocomplete="off" onChange="javascript:get_details_by_upc(document.getElementById(\'upc_id_'+LC+'\'), '+LC+')" value="" class="upc_1">';
						newElem+='</div>';
					newElem+='</div>';
					
					newElem+='<div class="row extra_margin">';
						newElem+='<div class="col-md-12 nopadd-right">';
							newElem+='<label for="item_name_'+LC+'"  class="frameLabel" id="item_name_'+LC+'_label">Item Name</label>';
							newElem+='<input type="text" name="item_name_'+LC+'" id="item_name_'+LC+'" style="width:42%;" autocomplete="off" onChange="javascript:get_details_by_upc(document.getElementById(\'upc_id_'+LC+'\'), '+LC+')" value=""><input type="text" name="item_name_'+LC+'_other" id="item_name_'+LC+'_other" style="width:42%;display:none" autocomplete="off" value="" title="type custom frame name" class="custom_item_name" data-row="'+LC+'">';
						newElem+='</div>';
					newElem+='</div>';
				newElem+='</div>';
				
				newElem+='<div class="col-md-4 nopadd">';
					newElem+='<img style="vertical-align:middle;cursor:pointer;" id="stock_image_'+LC+'" large="'+top.WRP+'/images/frame_stock/no_image_xl.jpg" src="'+top.WRP+'/images/frame_stock/no_image_thumb.jpg" onClick="show_stock_image('+LC+')" /> <a href="javascript:void(0);" onclick="javascript:stock_search(document.getElementById(\'module_type_id_'+LC+'\').value,\'\', '+LC+');" class="searchIcon"><img style="margin-left:0;" src="'+top.WRP+'/images/search.png" alt="Search Image" title="Search"></a>';
				newElem+='</div>';
			newElem+='</div>';
			
			newElem+='<div class="row extra_margin"><div class="col-md-12 nopadd">';
				newElem+='<label for="manufacturer_id_'+LC+'" class="frameLabel">Manufacturer</label>';
				newElem+='<input type="text" name="pof_manufacturer_id_'+LC+'" id="pof_manufacturer_id_'+LC+'" style="width:50%; display:none;" value="" />';
				newElem+='<select onChange="get_manufacture_brand(this.value,0,'+LC+');" id="manufacturer_id_'+LC+'" name="manufacturer_id_'+LC+'" style="width:50%" class="manufacturer_id"><option value="0">Select</option><?php
	$frameManufacturers = $objDropDown->get_frame_manu_arr();
	foreach($frameManufacturers as $key=>$val){
		echo '<option value="'.$key.'">'.addslashes(ucwords($val)).'</option>';
	}
	?></select>';
			newElem+='</div></div>';
			newElem+='<div class="row extra_margin"><div class="col-md-12 nopadd">';
				newElem+='<label for="brand_id_'+LC+'" class="frameLabel">Brand</label>';
				newElem+='<input type="text" name="pof_brand_id_'+LC+'" id="pof_brand_id_'+LC+'" style="width:50%; display:none;" value="" />';
				newElem+='<select onChange="get_brand_style(this.value,0, '+LC+');" id="brand_id_'+LC+'" name="brand_id_'+LC+'" style="width:50%" class="brand_id"><option value="0">Select</option></select>';
			newElem+='</div></div>';
			newElem+='<div class="row extra_margin"><div class="col-md-12 nopadd">';
				newElem+='<label for="color_id_'+LC+'" class="frameLabel">Color</label>';
				newElem+='<input type="text" name="pof_color_id_'+LC+'" id="pof_color_id_'+LC+'" style="width:24%; display:none;" value="" />';
				newElem+='<input type="text" id="color_id_'+LC+'" name="color_id_'+LC+'" value="" style="width:25%;height:23px;" autocomplete="off" />';
				newElem+="\n"+'<input type="text" name="color_code_'+LC+'" id="color_code_'+LC+'" style="width:23.5%;float:none;" />';
			newElem+='</div></div>';
			newElem+='<div class="row extra_margin"><div class="col-md-12 nopadd">';
				newElem+='<label for="shape_id_'+LC+'" class="frameLabel">Shape</label>';
				newElem+='<input type="text" name="pof_shape_id_'+LC+'" id="pof_shape_id_'+LC+'" style="width:50%; display:none;" value="" />';
				newElem+='<select id="shape_id_'+LC+'" name="shape_id_'+LC+'" style="width:50%" class="shape_id sel_dd"  onChange="other_option(this)">';
					newElem+='<option value="0">Select</option>';
<?php 
	foreach($frameShapes as $key=>$val){
		echo 'newElem+=\'<option value="'.$key.'">'.addslashes(ucwords($val)).'</option>\';';
	}
?>
				
					newElem+='<option disabled>--------------------------------------</option>';
					newElem+='<option value="other">Other</option>';
				newElem+='</select>';
				newElem+='<input type="text" class="other_val" name="shape_other_'+LC+'" id="shape_other_'+LC+'" style="width:50%;display:none;" value="" />';
				newElem+='<img style="display:none;" class="icon_back" src="'+top.WRP+'/images/icon_back.png" onClick="back_other(this)" />';
			newElem+='</div></div>';
			newElem+='<div class="row extra_margin"><div class="col-md-12nopadd">';
				newElem+='<label for="style_id_'+LC+'" class="frameLabel">Style</label>';
				newElem+='<input type="text" name="pof_style_id_'+LC+'" id="pof_style_id_'+LC+'" style="width:50%; display:none;" value="" />';
				newElem+='<select style="width:50%;height:23px;" name="style_id_'+LC+'" id="style_id_'+LC+'" onChange="other_option(this)" class="sel_dd">';
					newElem+='<option value="0">Select</option>';
					newElem+='<option disabled>--------------------------------------</option>';
					newElem+='<option value="other">Other</option>';
				newElem+='</select>';
				newElem+='<input type="text" class="other_val" name="style_other_'+LC+'" id="style_other_'+LC+'" style="width:50%; display:none;" value="" />';
				newElem+='<img style="display:none" class="icon_back" src="'+top.WRP+'/images/icon_back.png" onClick="back_other(this)" />';
			newElem+='</div></div>';
			newElem+='<div class="row extra_margin"><div class="col-md-12 nopadd">';
				newElem+='<label for="temple_'+LC+'" class="frameLabel">Temple</label>';
				newElem+='<input type="text" id="temple_'+LC+'" name="temple_'+LC+'" style="width:50%;" value="">';
			newElem+='</div></div>';
			
			newElem+='<div class="row extra_margin"><div class="col-md-12 nopadd">';
				newElem+='<label for="frame_type_id_'+LC+'" class="frameLabel">Type</label>';
				newElem+='<select name="type_id_'+LC+'" id="type_id_'+LC+'" style="width:50%;">';
					newElem+='<option value="0">Please Select</option>';
<?php
	$rows="";
	$rows = data("select * from in_frame_types where del_status='0' $vw_type_whr order by type_name asc");
	foreach($rows as $r){
		$selected = ($sel_order['type_id']==$r['id'])?"selected":"";
?>
						newElem+='<option value="<?php echo $r['id']; ?>" <?php echo $selected ?>><?php echo addslashes(ucwords($r['type_name'])); ?></option>';
<?php }	?> 
				newElem+='</select>';
			newElem+='</div></div>';
			
		/*Frame Attributes*/
		newElem+='<div class="row extra_margin" style="margin-top:5px;">';
			newElem+='<div class="col-md-5 nopadd">';
				newElem+='<label for="a_'+LC+'"  style="width:39px;float:left;margin-left:5px;">A</label>';
				newElem+='<input type="text" id="a_'+LC+'" name="a_'+LC+'" style="width:110px;margin-right:0px;" value="" onChange="pos_row_display('+LC+', \'2_'+LC+'_oversize_display\', \'\', \'in_lens_oversize\', \'\', false);">>';
			newElem+='</div>';
			newElem+='<div class="col-md-6 nopadd-right">';
				newElem+='<label for="b_'+LC+'" style="width:50px;float:left;">B</label>';
				newElem+='<input type="text" id="b_'+LC+'" name="b_'+LC+'" style="width:110px;" value="">';
			newElem+='</div>';
		newElem+='</div>';
		newElem+='<div class="row extra_margin" style="margin-top:5px;">';
			newElem+='<div class="col-md-5 nopadd">';
				newElem+='<label for="ed_'+LC+'" style="width:39px;float:left;margin-left:5px;">ED</label>';
				newElem+='<input type="text" id="ed_'+LC+'" name="ed_'+LC+'" style="width:110px;margin-right:0px;" value="">';
			newElem+='</div>';
			newElem+='<div class="col-md-6 nopadd-right">';
				newElem+='<label for="dbl_'+LC+'" style="width:50px;float:left;">DBL</label>';
				newElem+='<input type="text" id="dbl_'+LC+'" name="dbl_'+LC+'" style="width:110px;" value="">';
			newElem+='</div>';
		newElem+='</div>';
		newElem+='<div class="row extra_margin" style="margin-top:5px;">';
			newElem+='<div class="col-md-5 nopadd">';
				newElem+='<label for="fpd_'+LC+'" style="width:39px;float:left;margin-left:5px;m">FPD</label>';
				newElem+='<input type="text" id="fpd_'+LC+'" name="fpd_'+LC+'" style="width:110px;margin-right:0px" value="">';
			newElem+='</div>';
			newElem+='<div class="col-md-6 nopadd-right">';
				newElem+='<label for="bridge_'+LC+'" style="width:50px;float:left;">Bridge</label>';
				newElem+='<input type="text" id="bridge_'+LC+'" name="bridge_'+LC+'" style="width:110px;" value="">';
			newElem+='</div>';
		newElem+='</div>';
		/*End Frame Attributes*/
		
		/*Frame Options*/
		newElem+='<div class="row extra_margin" style="margin-top:8px;">';
			newElem+='<div class="col-md-2 nopadd" style="padding-left:5px;margin-right:15px;">';
				newElem+='<input class="chk_box_pof_'+LC+'" type="radio" name="order_chk_'+LC+'" value="1" id="order_chk_'+LC+'" style="margin-top:5px;" checked onClick="chk_pof_fun('+LC+', this);">';
				newElem+='<label for="order_chk_'+LC+'" style="float:left;">Order</label>';
			newElem+='</div>';
			newElem+='<div class="col-md-4 nopadd">';
				newElem+='<input class="chk_box_pof_'+LC+'" type="radio" name="use_on_hand_chk_'+LC+'" value="1" id="use_on_hand_chk_'+LC+'" style="margin-top:5px;" onClick="chk_pof_fun('+LC+', this);">';
				newElem+='<input type="hidden" name="qty_reduced_'+LC+'" id="qty_reduced_'+LC+'" value="0" />';
				newElem+='<input type="hidden" name="reduce_qty_'+LC+'" id="reduce_qty_'+LC+'" value="0" />';
				newElem+='<label for="use_on_hand_chk_'+LC+'" style="width:85px;float:left;margin-right:20px;">Use on hand</label>';
			newElem+='</div>';
			newElem+='<div class="col-md-4 nopadd">';
				newElem+='<input class="chk_box_pof_'+LC+' ptFrame" type="radio" name="in_add_'+LC+'" value="1" id="in_add_'+LC+'" style="margin-top:5px;" onClick="chk_pof_fun('+LC+', this);">';
				newElem+='<label for="in_add_'+LC+'" style="float:left;">Patient’s Frame</label>';
			newElem+='</div>';
		newElem+='</div>';
		
		newElem+='<div class="row extra_margin" style="margin-top:5px;">';
			newElem+='<div class="col-md-6" style="padding-left: 5px;">';
				newElem+='<input style="margin: 5px 5px 0 0;" type="checkbox" name="safety_glass_'+LC+'" id="safety_glass_'+LC+'" value="1" />';
				newElem+='<label for="safety_glass_'+LC+'">Safety Glasses</label>';
			newElem+='</div>';
			<?php if($GLOBALS['connect_visionweb']!=""){ ?>
			newElem+='<div class="col-md-6">';
				newElem+='<input name="trace_file_'+LC+'" id="trace_file_'+LC+'" type="file" style="display:none;width:1px;" />';
				newElem+='<input type="button" value="Add Trace File" onclick="$(\'#trace_file_'+LC+'\').click();" />';
			newElem+='</div>';
			<?php }?>
		newElem+='</div>';
		/*End Frame Options*/
		
		/*Frame Quantity*/
		newElem+='<div class="row extra_margin">';
			newElem+='<div class="col-md-5 nopadd-left">';
				newElem+='<span style="margin-left:5px;cursor:pointer;" onMouseOver="showStockDetails(this, '+LC+');" onMouseOut="hideStockDetails(this);">Qty on hand: </span>';
				newElem+='<span id="qoh_'+LC+'" onMouseOver="showStockDetails(this, '+LC+');" onMouseOut="hideStockDetails(this);" style="color:#FF1F1F;font-weight:bold;margin-right:25px;cursor:pointer;">0</span>';
				newElem+='<div class="stockDetails" id="stockDetails_'+LC+'">';
					newElem+='<table><tbody></tbody></table>';
				newElem+='</div>';
			newElem+='</div>';
			newElem+='<div class="col-md-6">';
				newElem+='<span>Qty at this facility: </span>';
				newElem+='<span id="fqoh_'+LC+'" style="color:#FF1F1F;font-weight:bold;">0</span>';
			newElem+='</div>';
		newElem+='</div>';
		newElem+='<div class="row extra_margin">';
			newElem+='<div class="col-md-12 nopadd-left">';
				newElem+='<label>Job Type</label> ';
				newElem+='<select name="job_type_'+LC+'" id="job_type_'+LC+'">';
					newElem+='<option value="FTC">Frame To Come</option>';
					newElem+='<option value="RED">Lens Only</option>';
					/*newElem+='<option value="FUN">Lens Only - No Trace</option>';*/
					newElem+='<option value="TBP">Supply Frame</option>';
					newElem+='<option value="UNC">Uncut</option>';
					/*newElem+='<option value="SPA">Safety Package</option>';
					newElem+='<option value="PAC">Frames Package</option>';*/
				newElem+='</select>';
			newElem+='</div>';
		newElem+='</div>';
	newElem+='</div></div></div>';
	/*End Frame Section*/
	
	/*Lens Frame Hidden Fields*/
	newElem+='<input type="hidden" name="discount_hidden_'+LC+'_lensD" id="discount_hidden_'+LC+'_lensD" value="0">';
	newElem+='<input type="hidden" name="module_type_id_'+LC+'_lensD" id="module_type_id_'+LC+'_lensD" value="2">';
	newElem+='<input type="hidden" class="order_lens_detail_id_cls" name="order_detail_id_'+LC+'_lensD" id="order_detail_id_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_frame_id_'+LC+'_lensD" id="lens_frame_id_'+LC+'_lensD" value="" />';
	newElem+='<input type="hidden" name="item_id_'+LC+'_lensD" id="item_id_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="upc_id_'+LC+'_lensD" id="upc_id_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="isRXLoaded_lensD_'+LC+'" id="isRXLoaded_lensD_'+LC+'" value="" />';
	newElem+='<input type="hidden" name="order_rx_lens_id_'+LC+'" id="order_rx_lens_id_'+LC+'" value="">';
	/*newElem+='<input type="hidden" name="qty_'+LC+'_lensD" value="2">';*/
	newElem+='<input type="hidden" name="overall_lens_discount_'+LC+'" value="" onBlur="apply_dis_all(this);">';
	newElem+='<input type="hidden" name="lens_item_count_'+LC+'" id="lens_item_count_lensD" value="">';
	/*End Lens Frame Hidden Fields*/
	
	/*Lens Section*/
	newElem+='<div class="col-md-8" style="border-left:1px solid #CCC;min-height:398px;float:left;padding-bottom:10px;padding-left:20px;">';
		newElem+='<div class="row extra_margin">';
			newElem+='<div class="col-md-6">';
				newElem+='<label for="upc_name_'+LC+'_lensD" style="width:90px;float:left;">UPC</label>';
				newElem+='<input type="text" name="upc_name_'+LC+'_lensD" id="upc_name_'+LC+'_lensD" autocomplete="off" style="width:65%;" onChange="javascript:get_details_by_upc_lensD(document.getElementById(\'upc_id_'+LC+'_lensD\'), '+LC+');" value="">';
			newElem+='</div>';
			
			newElem+='<div class="col-md-6" style="padding-right:0;">';
				newElem+='<label for="item_name_'+LC+'_lensD" style="width:90px;float:left;">Item Name</label>';
				/*onChange="javascript:get_details_by_upc_lensD(document.getElementById(\'upc_id_'+LC+'_lensD\'), '+LC+');"*/
				newElem+='<input type="text" name="item_name_'+LC+'_lensD" id="item_name_'+LC+'_lensD" autocomplete="off" style="width:65%;" value="">';
				newElem+='<a href="javascript:void(0);" onclick="javascript:stock_search_lensD(document.getElementById(\'module_type_id_'+LC+'_lensD\').value,\'\', '+LC+');" class="searchIcon"><img src="../../images/search.png" alt="Search Image" title="Search"></a>';
			newElem+='</div>';
		newElem+='</div>';

/*Lens Dual Vals*/
newElem+='<div class="row extra_margin" style="border:1px solid #ccc;padding: 10px 15px 10px 15px;">';
		
		/*Vision*/
		newElem+='<div class="row">';
			newElem+='<div class="col-md-2">';
				newElem+='<select class="ouColor lens_vision" name="lens_vision_'+LC+'_lensD" id="lens_vision_'+LC+'_lensD" onChange="changeVision('+LC+')">';
<?php
				foreach($lens_vision_array as $vals){
					echo 'newElem+=\'<option class="'.$vals.'Color" value="'.$vals.'">'.$vals.'</option>\';';
				}
?>
				newElem+='</select>';
			newElem+='</div>';
			newElem+='<div class="col-md-4 nopadd-left" style="text-align:center;padding-right:34px;">';
				newElem+='<span class="blueColor" style="font-weight:bold;">OD</span>';
			newElem+='</div>';
			
			newElem+='<div class="col-md-2"></div>';
			newElem+='<div class="col-md-4 nopadd-left" style="text-align:center;padding-right:34px;">';
				newElem+='<span class="greenColor" style="font-weight:bold;">OS</span>';
			newElem+='</div>';
		newElem+='</div>';
		
		/*Seg Type*/
		newElem+='<div class="row extra_margin">';
		
			newElem+='<div class="col-md-6">';
				newElem+='<label for="seg_type_id_'+LC+'_od_lensD" class="lens_label">Seg Type</label>';
				newElem+='<select name="seg_type_id_'+LC+'_od_lensD" id="seg_type_id_'+LC+'_od_lensD" style="width:65%;" onChange="pos_row_display('+LC+', \'2_'+LC+'_lens_display\', this.value, \'in_lens_type\', \'od\');">';
					newElem+='<option value="0" default_val="">Please Select</option>';
					<?php
						$qry = '';
						$qry = imw_query("SELECT `id`, `vw_code`, `type_name` FROM `in_lens_type` WHERE `del_status`='0' ORDER BY FIELD(`vw_code`, 'SV','PAL','BFF','TFF')");
						while($rows = imw_fetch_object($qry)){
					?>
						newElem+='<option value="<?php echo $rows->id; ?>" default_val="<?php echo $default_vals[$rows->vw_code]; ?>"><?php echo addslashes(ucwords($rows->type_name)); ?></option>';
					<?php }
						imw_free_result($qry);
					?>
				newElem+='</select>';
			newElem+='</div>';
			
			newElem+='<div class="col-md-6 nopadd-right">';
				newElem+='<label for="seg_type_id_'+LC+'_os_lensD" class="lens_label">Seg Type</label>';
				newElem+='<select name="seg_type_id_'+LC+'_os_lensD" id="seg_type_id_'+LC+'_os_lensD" style="width:65%;" onChange="pos_row_display('+LC+', \'2_'+LC+'_lens_display\', this.value, \'in_lens_type\', \'os\');">';
					newElem+='<option value="0" default_val="">Please Select</option>';
					<?php
						$qry = '';
						$qry = imw_query("SELECT `id`, `vw_code`, `type_name` FROM `in_lens_type` WHERE `del_status`='0' ORDER BY FIELD(`vw_code`, 'SV','PAL','BFF','TFF')");
						while($rows = imw_fetch_object($qry)){
					?>
						newElem+='<option value="<?php echo $rows->id; ?>" default_val="<?php echo $default_vals[$rows->vw_code]; ?>"><?php echo addslashes(ucwords($rows->type_name)); ?></option>';
					<?php }
						imw_free_result($qry);
					?>
				newElem+='</select>';
			newElem+='</div>';
			
		newElem+='</div>';
		
		/*Lens Design*/
		newElem+='<div class="row extra_margin">';
		
			newElem+='<div class="col-md-6">';
				newElem+='<label for="design_id_'+LC+'_od_lensD" class="lens_label">Design</label>';
				newElem+='<select name="design_id_'+LC+'_od" id="design_id_'+LC+'_od_lensD" style="width:65%;" onChange="pos_row_display('+LC+', \'2_'+LC+'_design_display\', this.value, \'in_lens_design\', \'od\');" disabled>';
					newElem+='<option value="0">Please Select</option>';
				newElem+='</select>';
				newElem+='<img style="float:right; margin-top: 6px;" src="'+top.WRP+'/images/icon_bl.png" alt="BL" onclick="blVals('+LC+')">';
			newElem+='</div>';
			
			newElem+='<div class="col-md-6 nopadd-right">';
				newElem+='<label for="design_id_'+LC+'_os_lensD" class="lens_label">Design</label>';
				newElem+='<select name="design_id_'+LC+'_os" id="design_id_'+LC+'_os_lensD" style="width:65%;" onChange="pos_row_display('+LC+', \'2_'+LC+'_design_display\', this.value, \'in_lens_design\', \'os\');" disabled>';
					newElem+='<option value="0">Please Select</option>';
				newElem+='</select>';
			newElem+='</div>';
			
		newElem+='</div>';
		
		/*Lens Material*/
		newElem+='<div class="row extra_margin">';
			
			newElem+='<div class="col-md-6">';
				newElem+='<label for="material_id_'+LC+'_od_lensD" class="lens_label">Material</label>';	
				newElem+='<select name="material_id_'+LC+'_od" id="material_id_'+LC+'_od_lensD" style="width:65%;" onChange="pos_row_display('+LC+', \'2_'+LC+'_material\', this.value, \'in_lens_material\', \'od\');" disabled>';
					newElem+='<option value="0">Please Select</option>';
				newElem+='</select>';
			newElem+='</div>';
			
			newElem+='<div class="col-md-6 nopadd-right">';
				newElem+='<label for="material_id_'+LC+'_os_lensD" class="lens_label">Material</label>';	
				newElem+='<select name="material_id_'+LC+'_os" id="material_id_'+LC+'_os_lensD" style="width:65%;" onChange="pos_row_display('+LC+', \'2_'+LC+'_material\', this.value, \'in_lens_material\', \'os\');" disabled>';
					newElem+='<option value="0">Please Select</option>';
				newElem+='</select>';
			newElem+='</div>';
			
		newElem+='</div>';
		
		/*Lens Tretment*/
		newElem+='<div class="row extra_margin">';
			
			newElem+='<div class="col-md-6">';
				newElem+='<label for="a_r_id_'+LC+'_od_lensD" class="lens_label">Treatment</label>';
				newElem+='<div class="multiDD" style="display:inline-block; width:240px;">';
					newElem+='<select name="a_r_id_'+LC+'_od" id="a_r_id_'+LC+'_od_lensD">';
					newElem+='</select>';
				newElem+='</div>';
			newElem+='</div>';
			
			newElem+='<div class="col-md-6 nopadd-right">';
				newElem+='<label for="a_r_id_'+LC+'_os_lensD" class="lens_label">Treatment</label>';
				newElem+='<div class="multiDD" style="display:inline-block; width:240px;">';
					newElem+='<select name="a_r_id_'+LC+'_os" id="a_r_id_'+LC+'_os_lensD">';
					newElem+='</select>';
				newElem+='</div>';
			newElem+='</div>';
			
		newElem+='</div>';

newElem+='</div>';
/*End Lens Dual Vals*/
		
		newElem+='<div class="row extra_margin">';
			newElem+='<div class="col-md-6">';
				newElem+='<label for="other_'+LC+'_lensD" style="width:90px;float:left;">Comments</label>';
				newElem+='<textarea name="other_'+LC+'" id="other_'+LC+'_lensD" style="width:65%;resize:none;border:1px solid #ccc;height:25px; float:left;"></textarea>';
			newElem+='</div>';
			
			newElem+='<div class="col-md-6" style="padding-right:0;">';
				newElem+='<div class="row">';
					newElem+='<div class="col-md-3 nopadd-right" style="display:none;">';
						newElem+='<input type="checkbox" name="uv400_'+LC+'" id="uv400_'+LC+'_lensD" style="vertical-align:middle;float:left;" <?php if($sel_order['uv400']==1){echo"checked";} ?>>';
						newElem+='<label for="uv400_'+LC+'_lensD" style="float:left;">UV400</label>';
					newElem+='</div>';
					
					newElem+='<div class="col-md-4" style="display:none;">';
						newElem+='<input type="checkbox" name="pgx_'+LC+'" id="pgx_'+LC+'_lensD" style="vertical-align:middle;float:left;">';
						newElem+='<label for="pgx_'+LC+'_lensD" style="float:left;margin-right:5px;">PGX</label>';
					newElem+='</div>';
					
					newElem+='<div class="col-md-3" title="Thin As Possible">';
						newElem+='<input type="checkbox" name="tap_'+LC+'" id="tap_'+LC+'_lensD" style="vertical-align:middle;float:left;">';
						newElem+='<label for="tap_'+LC+'_lensD" style="float:left;margin-right:5px;">TAP</label>';
					newElem+='</div>';
	
					newElem+='<div class="col-md-8 nopadd">';
						<?php if($GLOBALS['connect_visionweb']!=""){ ?>
						newElem+='<label for="lab_id_'+LC+'_lensD"><a href="javascript:void(0);" class="text_purpule" onClick="javascript:sel_lab_add('+LC+');" style="padding:0;" title="Lens Lab Address">Lab</a></label> ';
						newElem+=' <input type="hidden" name="lab_detail_id_'+LC+'_lensD" id="lab_detail_id_'+LC+'_lensD" value="">';
						newElem+=' <input type="hidden" name="lab_ship_detail_id_'+LC+'_lensD" id="lab_ship_detail_id_'+LC+'_lensD" value="">';
						<?php }else{?>
						newElem+='<label for="lab_id_'+LC+'_lensD">Lab</label> ';
						<?php }?>
						newElem+='<select name="lab_id_'+LC+'_lensD" id="lab_id_'+LC+'_lensD" style="width:83%;margin-right:0" onChange="sel_lab_add('+LC+');"><option value="0">Please Select</option><?php
                            foreach($lens_labs as $key=>$val){
	                            $class = ( in_array($key, $vw_lab_id_arr) )? 'blueColor' : 'blackColor';
                                echo '<option value="'.$key.'" class="'.$class.'">'.addslashes(ucwords($val)).'</option>';
                            }
                            ?></select>';
					newElem+='</div>';
			newElem+='</div>';
		newElem+='</div>';
	newElem+='</div>';
	<?php if($GLOBALS['connect_visionweb']==""){ ?>
	newElem+='<br>';
	<?php } ?>
	newElem+='<div class="row extra_margin od_os_span" style="clear:none !important;margin-bottom:5px;" id="rx_div_'+LC+'">';
	
	/*Lnes Rx Data*/
	/*----------------------------------- RX Hidden Fields For OD-----------------------------------*/
	newElem+='<input type="hidden" name="lens_sphere_od_'+LC+'" id="lens_sphere_od_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_cylinder_od_'+LC+'" id="lens_cylinder_od_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_axis_od_'+LC+'" id="lens_axis_od_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_axis_od_va_'+LC+'" id="lens_axis_od_va_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_add_od_'+LC+'" id="lens_add_od_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_add_od_va_'+LC+'" id="lens_add_od_va_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_mr_od_p_'+LC+'" id="lens_mr_od_p_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_mr_od_prism_'+LC+'" id="lens_mr_od_prism_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_mr_od_splash_'+LC+'" id="lens_mr_od_splash_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_mr_od_sel_'+LC+'" id="lens_mr_od_sel_'+LC+'_lensD" value="">';
	/*----------------------------------- RX Hidden Fields For Os-----------------------------------*/
	newElem+='<input type="hidden" name="lens_sphere_os_'+LC+'" id="lens_sphere_os_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_cylinder_os_'+LC+'" id="lens_cylinder_os_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_axis_os_'+LC+'" id="lens_axis_os_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_axis_os_va_'+LC+'" id="lens_axis_os_va_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_add_os_'+LC+'" id="lens_add_os_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_add_os_va_'+LC+'" id="lens_add_os_va_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_mr_os_p_'+LC+'" id="lens_mr_os_p_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_mr_os_prism_'+LC+'" id="lens_mr_os_prism_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_mr_os_splash_'+LC+'" id="lens_mr_os_splash_'+LC+'_lensD" value="">';
	newElem+='<input type="hidden" name="lens_mr_os_sel_'+LC+'" id="lens_mr_os_sel_'+LC+'_lensD" value="">';
	/*---------------------------------- Physician ID -----------------------------*/
    newElem+='<input type="hidden" name="lens_physician_id_'+LC+'" id="lens_physician_id_lensD_'+LC+'" value="">';
	
	/*Lens Rx Table*/
	newElem+='<span id="rx_Date_'+LC+'" style="float:right;font-weight:normal;display:none;"></span></div>';

/*Rx Table*/	
	newElem+='<div class="row extra_margin">';
		newElem+='<div class="col-md-12">';
			newElem+='<table style="width:100%;border-collapse:collapse;" class="rxTable">';
				newElem+='<tr>';
					newElem+='<th style="text-align:left;text-indent:0;width:32px;">';
						newElem+='<a href="javascript:void(0);" class="text_purpule" onClick="javascript:prescription_details('+LC+');" style="padding:0;" id="rx_label_'+LC+'" title="Lens Prescription">Rx</a>';
					newElem+='</th>';
					newElem+='<th style="width:146px;">SPH</th>';
					newElem+='<th style="width:110px;">CYL</th>';
					newElem+='<th style="width:110px;">AXIS</th>';
					newElem+='<th style="width:110px;">VA</th>';
					newElem+='<th style="width:110px;">ADD</th>';
					newElem+='<th style="width:110px;">VA</th>';
				newElem+='</tr>';
				
				newElem+='<tr>';
					newElem+='<td><span class="blueColor" style="font-weight:bold;">OD</span></td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="sph_text_od_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="cyl_text_od_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="axis_text_od_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="axis_text_od_va_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="add_text_od_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="add_text_od_va_'+LC+'"></span>';
					newElem+='</td>';
				newElem+='</tr>';
				
				newElem+='<tr>';
					newElem+='<td><span class="greenColor" style="font-weight:bold;">OS</span></td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="sph_text_os_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="cyl_text_os_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="axis_text_os_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="axis_text_os_va_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="add_text_os_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<span class="span_data" id="add_text_os_va_'+LC+'"></span>';
					newElem+='</td>';
				newElem+='</tr>';
				
				newElem+='<tr>';
					newElem+='<th></th>';
					newElem+='<th>Prism</th>';
					newElem+='<th>DPD</th>';
					newElem+='<th>NPD</th>';
					newElem+='<th title="Optical Center">OC</th>';
					newElem+='<th class="rxBase">Base Curve</th>';
					newElem+='<th class="rxBase">Min Seg Ht</th>';
				newElem+='</tr>';
				
				newElem+='<tr>';
					newElem+='<td><span class="blueColor" style="font-weight:bold;">OD</span></td>';
					newElem+='<td style="text-align:right;">';
						newElem+='<span id="prism_text_1_od_'+LC+'" class="span_data"></span>';
						newElem+='<span id="prism_text_od_seperator_'+LC+'"></span>';
						newElem+='<span class="span_data" id="prism_text_2_od_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<input type="text" name="lens_dpd_od_'+LC+'" id="lens_dpd_od_'+LC+'_lensD" value="" style="width:95%;" />';
					newElem+='</td>';
	
					newElem+='<td style="text-align:center;">';
						newElem+='<input type="text" name="lens_npd_od_'+LC+'" id="lens_npd_od_'+LC+'_lensD" value="" style="width:95%;" />';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<input type="text" name="lens_oc_od_'+LC+'" id="lens_oc_od_'+LC+'_lensD" value="" style="width:95%;" />';
					newElem+='</td>';
					newElem+='<td style="text-align:center;" class="rxBase">';
						newElem+='<input type="text" name="lens_base_od_'+LC+'" id="lens_base_od_'+LC+'_lensD" value="" style="width:95%;text-align:left;">';
					newElem+='</td>';
					newElem+='<td style="text-align:center;" class="rxBase">';
						newElem+='<input type="text" name="lens_seg_od_'+LC+'>" id="lens_seg_od_'+LC+'_lensD" value="" style="width:95%;text-align:left;">';
					newElem+='</td>';
				newElem+='</tr>';
				
				newElem+='<tr>';
					newElem+='<td><span class="greenColor" style="font-weight:bold;">OS</span></td>';
					newElem+='<td style="text-align:right">';
						newElem+='<span class="span_data" id="prism_text_1_os_'+LC+'"></span>';
						newElem+='<span id="prism_text_os_seperator_'+LC+'"></span>';
						newElem+='<span class="span_data" id="prism_text_2_os_'+LC+'"></span>';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<input type="text" name="lens_dpd_os_'+LC+'" id="lens_dpd_os_'+LC+'_lensD" value="" style="width:95%;" />';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<input type="text" name="lens_npd_os_'+LC+'" id="lens_npd_os_'+LC+'_lensD" value="" style="width:95%;" />';
					newElem+='</td>';
					newElem+='<td style="text-align:center;">';
						newElem+='<input type="text" name="lens_oc_os_'+LC+'" id="lens_oc_os_'+LC+'_lensD" value="" style="width:95%;" />';
					newElem+='</td>';
					newElem+='<td style="text-align:center;" class="rxBase">';
						newElem+='<input type="text" name="lens_base_os_'+LC+'" id="lens_base_os_'+LC+'_lensD" value="" style="width:95%;text-align:left;" />';
					newElem+='</td>';
					newElem+='<td style="text-align:center;" class="rxBase">';
						newElem+='<input type="text" name="lens_seg_os_'+LC+'" id="lens_seg_os_'+LC+'_lensD" value="" style="width:95%;text-align:left;"/>';
					newElem+='</td>';
				newElem+='</tr>';
			newElem+='</table>';
		newElem+='</div>';
	newElem+='</div>';
/*End Rx Table*/

	newElem+='<div class="row extra_margin" style="display:none;"><div class="col-md-6" style="padding-right:0;"><div class="row"><div class="col-md-5"><input type="hidden" name="lens_outside_rx_'+LC+'" id="lens_outside_rx_'+LC+'_lensD" value="" /><input type="hidden" name="lens_rx_dos_'+LC+'" id="lens_rx_dos_'+LC+'_lensD" value="" />';
          
newElem+='<label for="lens_outside_rx_'+LC+'_lensD" style="float:left;margin-right:4px;display:none;">Outside</label></div>';

newElem +='<input type="hidden" name="lens_last_exam_'+LC+'" id="lens_last_exam_'+LC+'_lensD" class="rx_cls" value=""><input type="hidden" name="lens_physician_name_'+LC+'" id="lens_physician_name_lensD_'+LC+'" value="" autocomplete="off"><input type="hidden" name="lens_telephone_'+LC+'" id="lens_telephone_lensD_'+LC+'" style="width:90px;" value="" onChange="set_phone_format(this,\'<?php echo $GLOBALS['phone_format'];?>\');">';

newElem +='</div></div></div>';

	/* End New Data */
	newElem+='<div class="row extra_margin">';
		newElem+='<div class="col-md-12">\n';
			newElem+='<label for="glasses_usage_'+LC+'">Usage</label>\n';
			newElem+='<select name="glasses_usage_'+LC+'" id="glasses_usage_'+LC+'" style="width:104px;">';
				newElem+='<option value="0">Please Select</option>';
<?php
	$sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='1' AND `module_id`='0' AND `opt_sub_type` IN(0,2) AND `del_status`='0' ORDER BY `opt_val` ASC";
	$resp = imw_query($sql);
	if($resp && imw_num_rows($resp)>0){
		while($row = imw_fetch_assoc($resp)){
			echo 'newElem+=\'<option value="'.$row['id'].'">'.$row['opt_val'].'</option>\';';
		}
	}
?>
			newElem+='</select>\n\n';
			
			newElem+='<label for="glasses_type_'+LC+'" style="margin-left:5px;">Type</label>\n';
            newElem+='<select name="glasses_type_'+LC+'" id="glasses_type_'+LC+'" style="width:104px;">';
            	newElem+='<option value="0">Please Select</option>';
<?php
$sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' AND `module_id`='0' AND `opt_sub_type` IN(0,2) AND `del_status`='0' ORDER BY `opt_val` ASC";
	$resp = imw_query($sql);
	if($resp && imw_num_rows($resp)>0){
		while($row = imw_fetch_assoc($resp)){
			echo 'newElem+=\'<option value="'.$row['id'].'">'.$row['opt_val'].'</option>\';';	
		}
	}
?>
            newElem+='</select>\n\n';
			
			newElem+='<label for="dx_code_'+LC+'_lensD">DX Code</label>\n';
<?php
	$all_dx_codes="";
	if($sel_order['dx_code']!=""){
		$dx_singl=array();
		$get_dxs = explode(",",$sel_order['dx_code']);
		$all_dx_codes = join('; ',$get_dxs);
	}
?>
            newElem+='<input type="text" name="dx_code_'+LC+'_lensD" id="dx_code_'+LC+'_lensD" style="width:104px;margin:0;float:none;" class="rx_cls" value="" autocomplete="off">\n\n';
			/*<!--onChange="get_dxcode(this);"-->*/
			
			newElem+='<input type="checkbox" name="lens_neutralize_rx_'+LC+'" id="lens_neutralize_rx_'+LC+'_lensD" value="1" style="float:none;" />\n';
            newElem+='<label for="lens_neutralize_rx_'+LC+'_lensD" style="float:none;">Neutralize</label>\n';
			newElem+='<label for="qty_'+LC+'_lensD" style="float:none;margin:0 12px 0 26px;">Qty</label>\n';
			newElem+='<input type="text" style="width:50px;margin:0;float:none;" name="qty_'+LC+'_lensD" id="qty_'+LC+'_lensD" value="1" onKeyUp="validate_qty(this);" onChange="changeQtyLens(this.value, '+LC+')">\n';
		newElem+='</div>\n';
	newElem+='</div>\n';
	
	<?php if($GLOBALS['connect_visionweb']!=""){ ?>
	newElem+='<div class="row extra_margin">';
		newElem+='<div class="col-md-12" style="text-align:right;">';
			newElem+='<span class="visionShowLink" elemKey="'+LC+'" style="font-weight:bold;color:#9900CC;cursor:pointer;margin-right:14px;">Show more details</span>';
		newElem+='</div>';
	newElem+='</div>';
	
	newElem+='<div id="visionweb_fields_'+LC+'" style="display:none;">';
    	newElem+='<div class="row extra_margin">';
            newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="he_coeff_'+LC+'_lensD" style="float:left;width:121px;">Head/Eye Coeff</label>';
            	newElem+='<select name="he_coeff_'+LC+'_lensD" id="he_coeff_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=0;$vw<100;$vw++){
						if($vw<10){
							$vw_v="0.0".$vw;
						}else{
							$vw_v="0.".$vw;
						}
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
            newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="st_coeff_'+LC+'_lensD" style="float:left;width:121px;">Stability Coeff</label>';
            	newElem+='<select name="st_coeff_'+LC+'_lensD" id="st_coeff_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=0;$vw<100;$vw++){
						if($vw<10){
							$vw_v="0.0".$vw;
						}else{
							$vw_v="0.".$vw;
						}
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
			newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="nhp_cape_'+LC+'_lensD" style="float:left;width:104px;">CAPE</label>';
            	newElem+='<select name="nhp_cape_'+LC+'_lensD" id="nhp_cape_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=-10;$vw<=10;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
        newElem+='</div>';
		
        newElem+='<div class="row extra_margin">';
			newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="progression_Len_'+LC+'_lensD" style="float:left;width:121px;">Progression Len.</label>';
            	newElem+='<select name="progression_Len_'+LC+'_lensD" id="progression_Len_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=5;$vw<=20;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
            newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="wrap_angle_'+LC+'_lensD" style="float:left;width:121px;">Wrap Angle</label>';
        		newElem+='<select name="wrap_angle_'+LC+'_lensD" id="wrap_angle_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=0;$vw<=30;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
        	newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="panto_angle_'+LC+'_lensD" style="float:left;width:104px;">Panto Angle</label>';
        		newElem+='<select name="panto_angle_'+LC+'_lensD" id="panto_angle_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=-10;$vw<=35;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
        newElem+='</div>';
		
		newElem+='<div class="row extra_margin">';
			newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="rv_distance_'+LC+'_lensD" style="float:left;width:121px;">R. Vertex Distance</label>';
            	newElem+='<select name="rv_distance_'+LC+'_lensD" id="rv_distance_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=5;$vw<=30;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
            newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="lv_distance_'+LC+'_lensD" style="float:left;width:121px;">L. Vertex Distance</label>';
            	newElem+='<select name="lv_distance_'+LC+'_lensD" id="lv_distance_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=5;$vw<=30;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
            newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="re_rotation_'+LC+'_lensD" style="float:left;width:104px;">R. Eye Rotation</label>';
            	newElem+='<select name="re_rotation_'+LC+'_lensD" id="re_rotation_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=16;$vw<=38;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
        newElem+='</div>';
		
		newElem+='<div class="row extra_margin">';
			newElem+='<div class="col-md-4 nopadd-right">';
                newElem+='<label for="le_rotation_'+LC+'_lensD" style="float:left;width:121px;">L. Eye Rotation</label>';
           		newElem+='<select name="le_rotation_'+LC+'_lensD" id="le_rotation_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=16;$vw<=38;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
           newElem+='</div>';
              newElem+='<div class="col-md-4 nopadd-right">';
			  	newElem+='<label for="reading_distance_'+LC+'_lensD" style="float:left;width:121px;">Reading Distance</label>';
            	newElem+='<select name="reading_distance_'+LC+'_lensD" id="reading_distance_'+LC+'_lensD" style="width:94px;">';
                	newElem+='<option value=""></option>';
                	<?php for($vw=20;$vw<=80;$vw++){
						$vw_v=$vw.".00";
					?>newElem+='<option value="<?php echo $vw_v ?>"><?php echo $vw_v ?></option>';<?php } ?>
                newElem+='</select>';
            newElem+='</div>';
			newElem+='<div class="col-md-4" style="text-align:right;">';
				newElem+='<span class="visionHideLink" elemKey="'+LC+'" style="font-weight:bold;color:#9900CC;cursor:pointer;margin-right:14px;">Hide details</span>';
			newElem+='</div>';
		newElem+='</div>';
	newElem+='</div>';
	<?php } ?>


newElem+='</div></div></div>';
  
	$('#firstform .input_upr').prepend(newElem);
	
	$("#upc_name_"+LC).ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'framesData',
			hidIDelem: document.getElementById('upc_id_'+LC),
			minLength:1,
			maxVals: 10,
			bgColor: "#888888",
			hoverBgColor: "#000000",
			textColor: "#FFFFFF",
			fontSize: "11px",
			showAjaxVals: 'upc'
		});
		
	$("#item_name_"+LC).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'framesData',
		hidIDelem: document.getElementById('upc_id_'+LC),
		showAjaxVals: 'name'
	});
	
	$("#upc_name_"+LC+"_lensD").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'lensData',
		hidIDelem: document.getElementById('upc_id_'+LC+'_lensD'),
		showAjaxVals: 'upc'
	});
	
	$("#color_id_"+LC).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'frameColors',
		hidIDelem: document.getElementById('color_code_'+LC),
		showAjaxVals: 'name'
	});
	
	
	/*ICD 10 typeahead*/
	bind_autocomp($("#dx_code_"+LC+"_lensD"),getDataFilePath);
	/*End ICD 10 typeahead*/
	
	$("#lens_physician_name_lensD_"+LC).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'physicianData',
		hidIDelem: document.getElementById('lens_physician_id_lensD_'+LC),
		showAjaxVals: 'name',
	});
	
	dd_pro = new Array();
	dd_pro["listHeight"] = 200;
	dd_pro["noneSelected"] = "Select All";
	dd_pro["rowId"] = "2_"+LC+"_a_r_";
	dd_pro["tbId"] = "in_lens_ar";
	dd_pro["vision"] = "od";
	$("#a_r_id_"+LC+"_od_lensD").multiSelect(dd_pro, posRowMulti);
	
	dd_pro = new Array();
	dd_pro["listHeight"] = 200;
	dd_pro["noneSelected"] = "Select All";
	dd_pro["rowId"] = "2_"+LC+"_a_r_";
	dd_pro["tbId"] = "in_lens_ar";
	dd_pro["vision"] = "os";
	$("#a_r_id_"+LC+"_os_lensD").multiSelect(dd_pro, posRowMulti);
	
	$("#last_cont").val(LC);
	$("#last_cont_lensD").val(LC);
	
	/*Bind Vision Web ShowHideControle*/
		visionHideShow();
	/*Bind Vision Web ShowHideControle*/
	//bindCustomName();
}

function patientFrame(count){
	
	if($("#in_add_"+count).is(':checked'))
	{
		$("#a_"+count+" , #b_"+count+", #ed_"+count+", #dbl_"+count+", #temple_"+count+", #bridge_"+count+", #fpd_"+count).removeAttr('readonly');
		$("#manufacturer_id_"+count+", #brand_id_"+count+", #style_id_"+count+", #shape_id_"+count+", #color_id_"+count).hide();
		$("#manufacturer_id_"+count).hide();
		$("#pof_manufacturer_id_"+count+", #pof_brand_id_"+count+", #pof_style_id_"+count+", #pof_shape_id_"+count+", #pof_color_id_"+count).show();
		fr_price = $("#price_"+count).val();
		fr_qty = $("#qty_"+count).val();
		fr_dis = $("#discount_"+count).val();
		fr_amt = $("#total_amount_"+count).val();
		fr_prac_code = $("#item_prac_code_"+count).val();
		
		$("#price_"+count+", #discount_"+count+", #total_amount_"+count+", #item_prac_code_"+count).val('0');
		$("#price_"+count+", #qty_"+count+", #discount_"+count+", #total_amount_"+count).attr('readonly','readonly');
		
		$("#item_name_"+count).val("<?php echo $GLOBALS['CUSTOM_FRAME']['name']; ?>");
		$("#upc_id_"+count).val("<?php echo $GLOBALS['CUSTOM_FRAME']['id']; ?>");
		$("#item_id_"+count).val("<?php echo $GLOBALS['CUSTOM_FRAME']['id']; ?>");
		$("#upc_name_"+count).val('<?php echo $GLOBALS['CUSTOM_FRAME']['upc']; ?>').change();
	}
	else
	{
		//$("#a_"+count+", #b_"+count+", #ed_"+count+", #dbl_"+count+", #temple_"+count+", #bridge_"+count+", #fpd_"+count).attr('readonly','readonly');
		$("#manufacturer_id_"+count+", #brand_id_"+count+", #style_id_"+count+", #shape_id_"+count+", #color_id_"+count).show();
		$("#pof_manufacturer_id_"+count+", #pof_brand_id_"+count+", #pof_style_id_"+count+", #pof_shape_id_"+count+", #pof_color_id_"+count).hide();
		if($("#price_hidden_"+count).val()!="")
		{
			$("#price_"+count).val($("#price_hidden_"+count).val());
			$("#qty_"+count).val($("#qty_hidden_"+count).val());
			$("#discount_"+count).val($("#discount_hidden_"+count).val());
			$("#total_amount_"+count).val($("#total_amount_hidden_"+count).val());
		}
		else
		{
			$("#price_"+count).val(fr_price);
			$("#qty_"+count).val(fr_qty);
			$("#discount_"+count).val(fr_dis);
			$("#total_amount_"+count).val(fr_amt);
			$("#item_prac_code_"+count).val(fr_prac_code);
		}
		$("#price_"+count+", #qty_"+count+", #discount_"+count+", #total_amount_"+count).removeAttr('readonly','readonly');
	}
}

$(document).ready(function(){
	
	var fr_price=fr_qty=fr_dis=fr_amt=0;
	var frameCount = $("#last_cont").val();
	for(i=1;i<=frameCount;i++){
		$("#pof_manufacturer_id_"+i+", #pof_brand_id_"+i+", #pof_style_id_"+i+", #pof_shape_id_"+i+", #pof_color_id_"+i).hide();
		
		if($("#in_add_"+i).is(':checked'))
		{
			$("#a_"+i+", #b_"+i+", #ed_"+i+",#dbl_"+i+", #temple_"+i+", #bridge_"+i+", #fpd_"+i).removeAttr('readonly');
			$("#manufacturer_id_"+i+", #brand_id_"+i+", #style_id_"+i+", #shape_id_"+i+", #color_id_"+i).hide();
			$("#pof_manufacturer_id_"+i+", #pof_brand_id_"+i+", #pof_style_id_"+i+", #pof_shape_id_"+i+", #pof_color_id_"+i).show();
			//$("#price_"+i+", #qty_"+i+", #discount_"+i+", #total_amount_"+i).val('0');
			$("#price_"+i+", #qty_"+i+", #discount_"+i+", #total_amount_"+i).attr('readonly','readonly');
		}
		else
		{
			//$("#a_"+i+", #b_"+i+", #ed_"+i+", #dbl_"+i+", #temple_"+i+", #bridge_"+i+", #fpd_"+i).attr('readonly','readonly')
			//#style_id_"+i+", #shape_id_"+i+", #color_id_"+i
			$("#manufacturer_id_"+i+", #brand_id_"+i).show();
			$("#pof_manufacturer_id_"+i+", #pof_brand_id_"+i+", #pof_style_id_"+i+", #pof_shape_id_"+i+", #pof_color_id_"+i).hide();
			$("#price_"+i+", #qty_"+i+", #discount_"+i+", #total_amount_"+i).removeAttr('readonly','readonly');
		}
	}
	
});

function prescription_details(count){
	/*var winTop='<?php echo $_SESSION['wn_height']-650;?>';*/
	var winTop=window.screen.availHeight;
	winTop = (winTop/2)-190;
	
	var winWidth = window.screen.availWidth;
	winWidth = (winWidth/2)-590;
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/lens_prescriptions.php?rxCount='+count,'lens_prescription_pop','width=1080,height=340,left='+winWidth+',scrollbars=no,top='+winTop);
	ptwin.focus();
}
function stock_search(type,fromVal,itemCounter){
	
	itemCounter = (typeof(itemCounter)=="undefined")?document.getElementById("last_cont").value:itemCounter;
	var win="";
	var pt_pic = "";
	
	var module_typePatval = document.getElementById('module_typePat').value;
	var manuf_id = document.getElementById('manufacturer_id_'+itemCounter).value;
	var brand = document.getElementById('brand_id_'+itemCounter).value;
	var color_id = document.getElementById('color_id_'+itemCounter).value;
	var shape_id = document.getElementById('shape_id_'+itemCounter).value;
	var style_id = document.getElementById('style_id_'+itemCounter).value;
	var pt_pic1 = '';
	var pt_pic2 = '';
	if(pt_pic1!=""){
		pt_pic = pt_pic1;
	}
	else if(pt_pic2!=""){
		pt_pic = pt_pic2;
	}
	
	if(document.getElementById('order_detail_id_1').value>0){
		var order_detail_id_1 = document.getElementById('order_detail_id_1').value;
	}else{
		var order_detail_id_1 = 'new_form';
	}
	
	if(fromVal=='pt_int'){
		/*win = window.open('../admin/stock_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&from=style&frm_dw='+fromVal+'&manuf_id='+manuf_id+'&brand='+brand+'&color='+color_id+'&shape='+shape_id+'&style='+style_id+'&picture='+pt_pic,'location_popup','width=1250,height=500,left=120,scrollbars=no,top=150');*/
		top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup','../admin/stock_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&from=style&frm_dw='+fromVal,'location_popup','width=1558,height=500,left=120,scrollbars=no,top=150');
		ptwin.focus();
	}
	if(fromVal==''){
		/*win = window.open('../admin/stock_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&from=style&manuf_id='+manuf_id+'&brand='+brand+'&picture='+pt_pic+'&itemCounter='+itemCounter,'location_popup','width=1250,height=500,left=120,scrollbars=no,top=150');*/
		top.WindowDialog.closeAll();
	var win3=top.WindowDialog.open('Add_new_popup','../admin/stock_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&from=style&itemCounter='+itemCounter,'location_popup','width=1420,height=520,left=120,scrollbars=no,top=150');
		win3.focus();
	}
}
function stock_search_lensD(type,fromVal,itemCounter){
	
	itemCounter = (typeof(itemCounter)=="undefined")?document.getElementById("last_cont_lensD").value:itemCounter;
	var module_typePatval = document.getElementById('module_typePat_lensD').value;
	if(document.getElementById('order_detail_id_'+itemCounter+'_lensD').value>0){
		var order_detail_id_1 = document.getElementById('order_detail_id_'+itemCounter+'_lensD').value;
	}
	else{
		var order_detail_id_1 = 'new_form';
	}
	/*var lens_type = document.getElementById('seg_type_id_'+itemCounter+'_'+visionVal+'_lensD').value;
	var lens_material = document.getElementById('material_id_'+itemCounter+'_'+visionVal+'_lensD').value;
	var lens_air = document.getElementById('a_r_id_'+itemCounter+'_'+visionVal+'_lensD').value;
	if(document.getElementById('transition_id_'+itemCounter+'_lensD')){
		var lens_transition = document.getElementById('transition_id_'+itemCounter+'_lensD').value;
	}
	if(document.getElementById('polarized_id_'+itemCounter+'_lensD')){
		var polarized_name = document.getElementById('polarized_id_'+itemCounter+'_lensD').value;
	}
	if(document.getElementById('tint_id_'+itemCounter+'_lensD')){
		var tint_type = document.getElementById('tint_id_'+itemCounter+'_lensD').value;
	}
	if(document.getElementById('color_id_'+itemCounter+'_lensD')){
		var color_name = document.getElementById('color_id_'+itemCounter+'_lensD').value;
	}*/
	
	var rx=0;
	if($("#isRXLoaded_lensD_"+itemCounter).val()!='')rx=1;
	/*var datastring = '&type='+lens_type+'&material='+lens_material+'&air='+lens_air+'&transition='+lens_transition+'&polarized='+polarized_name+'&tint='+tint_type+'&color='+color_name+'&itemCounter='+itemCounter;*/
	var datastring = '&itemCounter='+itemCounter;
	top.WindowDialog.closeAll();
	var win4=top.WindowDialog.open('Add_new_popup','lens_stock_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&from='+fromVal+datastring+'&rx='+rx,'location_popup','width=1050px,height=500,left=180,scrollbars=no,top=150');
	win4.focus();
}

$(function(){
	var LC = $("#last_cont").val();
	$("#lens_last_exam_"+LC+"_lensD").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
				
	last_exam_d=function()
	{
		if($("#isRXLoaded_lensD_"+LC).val()==1)
		{
			$(".ui-datepicker").hide();
			$("#lens_last_exam_"+LC+"_lensD").datepicker( "option", "readonly", "readonly");
		}
	}
});

function calculate_all(){
	calculate_all_Grand_POS();
}
function GDTChange(){
	var cost = (parseFloat($("#dispPriceF").val())+parseFloat($("#item_lens_grand_price_lensD").val()));
	if(!isNaN(cost)){
		$("#item_lens_grand_price_lensD").val(cost.toFixed(2));
	}
	
	var disc = (parseFloat($("#dispDiscF").val())+parseFloat($("#item_lens_grand_disc_lensD").val()));
	if(!isNaN(disc)){
		$("#item_lens_grand_disc_lensD").val(disc.toFixed(2));
	}
	
	var qty = (parseFloat($("#dispQtyF").val())+parseFloat($("#item_lens_grand_qty_lensD").val()));
	if(!isNaN(qty)){
		$("#item_lens_grand_qty_lensD").val(qty);
	}
	
	var gTotal = (parseFloat($("#dispTotalF").val())+parseFloat($("#item_lens_grand_total_lensD").val()));
	if(!isNaN(gTotal)){
		$("#item_lens_grand_total_lensD").val(gTotal.toFixed(2));
	}
}

function pos_row_display(counter, rowId, val, tb, vision, visionDD){
	
	var vision_selected = $( '#lens_vision_'+counter+'_lensD' ).val();
	
	visionDD = (typeof(visionDD)=='undefined') ? true : visionDD;
	
	
	if(visionDD && tb==='in_lens_type'){
		fetch_vision_dd(val, 'seg_type', counter, false, false, vision);
		
		if( get_prism_qty(counter, vision) > 0 )
		{
			rowId = rowId.replace(/((\d+_){2}).*/, '$1');
			pos_row_display(counter, rowId+'diopter_display', '', 'in_lens_diopter', vision, false);
		}
		return;
	}
	
	/*Delete Prism diopter charge row*/
	if( tb === 'in_lens_diopter' && get_prism_qty(counter, vision) < 1 )
	{
		delPosRow('2', counter, 'diopter', vision);
	}

	/*Delete Oversize Lens Charge Row*/
	if( tb === 'in_lens_oversize' && get_oversizelens_value(counter) < 59 )
	{
		delPosRow('2', counter, 'oversize', vision);
	}
	
	rowId = (typeof(rowId)==="undefined")?false:rowId;
	rowId = (typeof(rowId)==="undefined")?false:rowId
	
	var itemNames = {"in_lens_type":"lens", "in_lens_design":"design", "in_lens_material":"material", "in_lens_ar":"a_r", "in_lens_diopter":"diopter", "in_lens_oversize":"oversize"};
	
	var itemNamesPos = {"in_lens_type":"Seg Type", "in_lens_design":"Design", "in_lens_material":"Material", "in_lens_ar":"Treatment", "in_lens_diopter": "Prism Diopter Charges", "in_lens_oversize":"Oversized Lens Charges"};
	val = (typeof(val)==="undefined" || val==="0" || val==="")?false:val;
	
	var string = {};
	var uvFlag = false;
	<?php if(defined('TAX_CHECKBOX_CHECKED') && constant('TAX_CHECKBOX_CHECKED')=='FALSE'){ echo'var tax_applied = false;';}else{ echo'var tax_applied = true;';}?>
	var rowIdArray = [];
	
	var rowId_bk = rowId;
	
	if(
		(
			rowId && val && val!="uv400" && !uvFlag
		)
		||
		(rowId && uvFlag) 
		||
		(
			tb === 'in_lens_diopter' && get_prism_qty(counter, vision) > 0 
		)
		||
		(
			tb === 'in_lens_oversize' && get_oversizelens_value(counter) >= 59 
		)
	)
	{
		if(uvFlag===false){
			if(tb == "in_lens_material"){
				var seg_text = $('#seg_type_id_'+counter+'_'+vision+'_lensD>option:selected').text();
				string = {action:'get_price_from_praccode_material', sel_id:val, tb_name:tb, seg_val:seg_text};
				rowId_bk = rowId;
			}
			else if(tb == "in_lens_design"){
				var sphere_od = $('#lens_sphere_'+vision+'_'+counter+'_lensD').val();
				var cylinder_od = $('#lens_cylinder_'+vision+'_'+counter+'_lensD').val();
				
				var seg_val = $('#seg_type_id_'+counter+'_'+vision+'_lensD').val();
				string = {action:'get_price_from_praccode', sel_id:val, seg_val: seg_val,tb_name:tb, sph_od:sphere_od, cyl_od:cylinder_od};
			}
			else if(tb != "in_lens_type"){
				string = {action:'get_price_from_praccode', sel_id:val, tb_name:tb};
			}
		}
		
		if( Object.keys(string).length == 0)
			return true;
		
		var lens_discount = $("#discount_hidden_"+counter+"_lensD").val();
		
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response){
				
				var mystr1 = {};
				if(tb == "in_lens_material"){
					response = $.parseJSON(response);
					var counterMaterial = 0;
					$.each(response, function(key,obj){
						counterMaterial++;
						mystr1[key] = [];
						mystr1[key][0] = (obj.prac === null || obj.prac === '') ? '' : obj.prac;
						mystr1[key][1] = (obj.retail === null || obj.retail === '') ? '' : obj.retail;
						mystr1[key][2] = "";
						mystr1[key][3] = (obj.prac === null) ? '' : obj.prac;
						mystr1[key][4] = (obj.detail === null) ? '' : obj.detail;
						mystr1[key][5] = (obj.id === null || obj.id === '') ? 'temp_'+counterMaterial : obj.id;
					});
				}
				else{
					mystr1[0] = response.split('-:');
					mystr1[0][4] = "";
					mystr1[0][5] = "";
				}
				
				$.each(mystr1, function(key, obj){
					
					mystr = obj;
					rowClass = "";
					if(tb == "in_lens_material"){
						//console.log(rowId_bk);
						rowId = rowId_bk+((mystr[5]!="")?"_"+mystr[5]:"")+"_display_"+vision;
						rowIdArray.push(rowId);
						itemName = itemNames[tb]+((mystr[5]!="")?"_"+mystr[5]:"");
						rowClass = 'class="multiVals"';
					}
					else{
						rowId = rowId_bk+'_'+vision;
						itemName = itemNames[tb];
					}
									
					var row = $("#"+rowId);
					var rown = "";

					var lens_pos_row_qty = 1;
					if( tb === 'in_lens_diopter' )
						lens_pos_row_qty = get_prism_qty(counter, vision);
					else
						lens_pos_row_qty = $("#qty_"+counter+"_lensD").val();

					if(row.length>0){
						$("#"+rowId+" .pracodefield").val(mystr[0]);
						$("#"+rowId+" .pracodefield").attr("title",mystr[3]);
						$("#"+rowId+" .itemnameDisp").val(mystr[4]);
						
						$("#"+rowId+" .price_cls").val(mystr[1]);
						$("#"+rowId+" .allowed_cls").val(mystr[1]);
						
						$("#"+rowId+" .qty_cls").val(lens_pos_row_qty);
						
						$("#"+rowId+" .price_disc_per_proc").val(lens_discount);
						$("#"+rowId+" .price_disc").val('0.00');
						
						$("#"+rowId).find(".del_status").val("0"); /*Unset Del Status*/
						
						tax_p = parseFloat($("#"+rowId+" input.tax_p").val());
						if(tax_p>0)
							$("#"+rowId).find(".tax_applied").attr('checked', true);
						
						/*if(tb==='in_lens_type' && (mystr[1]=='' || mystr[1]=='0.00' || mystr[1]=='0')){
							$("#"+rowId).addClass('hideRow1');
						}
						else{*/
							$("#"+rowId).removeClass('hideRow');
							/*$("#"+rowId).removeClass('hideRow1');
						}*/
					}
					else{
						var lensItem = 0;
						var lensItemCounter = $("#lens_item_count_"+counter+"_lensD");
						
						if(lensItemCounter.length>0){
							lensItem = parseInt($(lensItemCounter).val());
							lensItem++;
						}
						else{
							lensItem++;
						}
						
						if(lensItem==1){
							$('#lens_item_count_'+counter+'_lensD').remove();
							
							var lensCounterRow ='<input type="hidden" name="lens_item_count_'+counter+'_lensD" id="lens_item_count_'+counter+'_lensD" value="'+lensItem+'">';
							/*Frame Row shoul be at top*/
							/*If Frame Row Exists*/
							var frameRow =  $("table.table_collapse.posTable > tbody > tr#1_"+counter);
							if( frameRow.length > 0 ){
								$( lensCounterRow ).insertAfter( frameRow[frameRow.length-1] );
							}
							else{
								$("table.table_collapse.posTable > tbody").prepend(lensCounterRow);
							}
						}
						
						/*Hide Seg Type Row if Price is 0* /
						if(tb==='in_lens_type' && (mystr[1]=='' || mystr[1]=='0.00' || mystr[1]=='0')){
							rowClass = 'class="hideRow1"';
						}*/
						
						rown +='<tr id="'+rowId+'" '+rowClass+'><!--td-->';
						if (lensItem==1){
							rown +='<input readonly="" style="width:100%;" type="hidden" name="pos_upc_name_'+counter+'_lensD" id="pos_upc_name_'+counter+'_lensD" value="<?php echo $GLOBALS['CUSTOM_LENS']['upc']; ?>">';
							$("#item_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['name']; ?>");
							$("#upc_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['upc']; ?>");
							$("#upc_id_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['id']; ?>");
							$("#item_id_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['id']; ?>");
						}
						else{
							if($("#upc_id_"+counter+"_lensD").val()==""){
								$("#pos_upc_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['upc']; ?>");
								$("#item_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['name']; ?>");
								$("#upc_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['upc']; ?>");
								$("#upc_id_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['id']; ?>");
								$("#item_id_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['id']; ?>");
							}
						}
						
						rown +='<input type="hidden" name="pos_order_chld_id_'+counter+'_lensD" value=""><input type="hidden" name="pos_order_detail_id_'+counter+'_lensD" value="" id="pos_order_detail_id_'+counter+'_lensD"><input type="hidden" name="lens_item_detail_id_'+counter+'_'+lensItem+'_lensD" id="lens_item_detail_id_'+counter+'_'+lensItem+'_lensD" value="'+lensItem+'">';
						rown +='<input type="hidden" name="lens_item_detail_name_'+counter+'_'+lensItem+'_lensD" id="lens_item_detail_name_'+counter+'_'+lensItem+'_lensD" value="'+itemName+'"><input type="hidden" name="lens_price_detail_id_'+counter+'_'+lensItem+'_lensD" id="lens_price_detail_id_'+counter+'_'+lensItem+'_lensD" value="">';
	
						rown +='<input type="hidden" name="pos_upc_id_1_lensD" id="pos_upc_id_1_lensD" value=""><!--/td-->';
						
						/*Vision*/
						rown +='<td>';
							if( tb !== 'in_lens_oversize' )
							{
								rown +='<span class="vis_type vision_'+vision+'">'+vision.toUpperCase()+'</span>';
								rown +='<input type="hidden" name="pos_lens_item_vision_'+counter+'_'+lensItem+'_lensD" id="pos_lens_item_vision_'+counter+'_'+lensItem+'_lensD" value="'+vision+'" class="row_vision_value" />';
							}
						rown +='</td>';

						if( tb === 'in_lens_diopter' || tb === 'in_lens_oversize' )
						{
							rown +='<td colspan="2">';
								rown +='<input readonly="" style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_'+counter+'_'+lensItem+'_lensD" id="pos_lens_item_name_'+counter+'_lensD" value="'+itemNamesPos[tb]+'">';
								rown +='<input readonly style="width:100%;" type="hidden" class="itemnameDisp" name="pos_lens_item_name_disp_'+counter+'_'+lensItem+'_lensD" id="pos_lens_item_name_disp_'+counter+'_'+lensItem+'_lensD" value="'+mystr[4]+'" />';
							rown +='</td>';
						}
						else
						{
							rown +='<td><input readonly="" style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_'+counter+'_'+lensItem+'_lensD" id="pos_lens_item_name_'+counter+'_lensD" value="'+itemNamesPos[tb]+'"></td>';
						
							rown +='<td><input readonly style="width:100%;" type="text" class="itemnameDisp" name="pos_lens_item_name_disp_'+counter+'_'+lensItem+'_lensD" id="pos_lens_item_name_disp_'+counter+'_'+lensItem+'_lensD" value="'+mystr[4]+'" /></td>';
						}
						
						rown +='<td><input style="width:100%;" type="text" class="pracodefield" name="item_prac_code_'+counter+'_'+lensItem+'_lensD" id="item_prac_code_'+counter+'_'+lensItem+'_lensD" value="'+mystr[0]+'" title="'+mystr[0]+'" calculate_all();"></td>';
						/*onchange="show_price_from_praccode(this,\'price_'+counter+'_'+lensItem+'_lensD\',\'pos\');*/
						
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_price_'+counter+'_'+lensItem+'_lensD" id="lens_item_price_'+counter+'_'+lensItem+'_lensD" value="'+mystr[1]+'" class="price_cls currency" onchange="calculate_all();"></td>';
	/*onChange="changeQty(\'2\', this.value, \''+counter+'\');"*/

						rown += '<td><input type="text" style="width:100%; text-align:right;" class="qty_cls" name="lens_qty_'+counter+'_'+lensItem+'_lensD" id="lens_qty_'+counter+'_'+lensItem+'_lensD_lensD" value="'+lens_pos_row_qty+'" autocomplete="off"  onChange="calculate_all();" onKeyUp="validate_qty(this);" /><input type="hidden" class="rqty_cls" name="pos_qty_right_1_lensD" value="0"><input type="hidden" name="pos_module_type_id_1_lensD" value="2"></td>';
	
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_allowed_'+counter+'_'+lensItem+'_lensD" id="lens_item_allowed_'+counter+'_'+lensItem+'_lensD" value="'+mystr[1]+'" class="allowed_cls currency" onchange="calculate_all();"></td>';
						
						
						rown +='<td style="display:none"><input readonly="" style="width:100%; text-align:right;" type="text" name="lens_item_total_'+counter+'_'+lensItem+'_lensD" id="pos_total_amount_'+counter+'_'+lensItem+'_lensD" value="0.00" class="price_total currency" onchange="calculate_all();">';
							/*Tax Calculations*/
							tax_applied = (tax_applied && facTax[2]>0);
							rown +='<input type="hidden" name="tax_p_'+counter+'_'+lensItem+'_lensD" id="tax_p_'+counter+'_'+lensItem+'_lensD" class="tax_p" value="'+facTax[2]+'" />';
							rown +='<input type="hidden" name="tax_v_'+counter+'_'+lensItem+'_lensD" id="tax_v_'+counter+'_'+lensItem+'_lensD" class="tax_v" value="0.00" />';
							/*End Tax Calculations*/
						rown +='</td>';
						
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="ins_amount_'+counter+'_'+lensItem+'_lensD" id="ins_amount_'+counter+'_'+lensItem+'_lensD" value="0.00" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"></td>';
						
						rown +='<td>';
							/*Line item's share in overall discount*/
							rown +='<input type="hidden" name="lens_item_overall_discount_'+counter+'_'+lensItem+'_lensD" id="lens_item_overall_discount_'+counter+'_'+lensItem+'_lensD" value="0.00" class="item_overall_disc" />';
							rown +='<input type="hidden" name="lens_item_discount_'+counter+'_'+lensItem+'_lensD" id="lens_item_discount_'+counter+'_'+lensItem+'_lensD" value="'+((lens_discount=="")?0:lens_discount)+'" onchange="calculate_all();" class="price_disc_per_proc">';
							rown +='<input style="width:100%; text-align:right;" type="text" name="read_lens_item_discount_'+counter+'_'+lensItem+'_lensD" id="pos_read_discount_'+counter+'_'+lensItem+'_lensD" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />';
						rown +='</td>';
						
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="pt_paid_'+counter+'_'+lensItem+'_lensD" id="pt_paid_'+counter+'_'+lensItem+'_lensD" value="0.00" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"></td>';
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="pt_resp_'+counter+'_'+lensItem+'_lensD" id="pt_resp_'+counter+'_'+lensItem+'_lensD" value="0.00" class="resp_cls currency" readonly=""></td>';
						
						rown +='<td><select name="discount_code_'+counter+'_'+lensItem+'_lensD" id="discount_code_1" class="text_10 disc_code dis_code_class" style="width:100%;"><option value="0">Please Select</option><?php $sel_rec=imw_query("select d_id,d_code,d_default from discount_code"); while($sel_write=imw_fetch_array($sel_rec)){ ?><option value="<?php echo $sel_write['d_id'];?>" ><?php echo $sel_write['d_code'];?></option><?php } ?></select></td>';
						
						rown +='<td><select name="ins_case_id_'+counter+'_'+lensItem+'_lensD" id="ins_case_id_'+counter+'_'+lensItem+'_lensD" class="ins_case_class" style="width:100%;" onchange="switch_pat_ins_resp(\''+counter+'_'+lensItem+'_lensD\');"><option value="0">Self Pay</option>';
		$.each(insCases, function(insVal, insKey){
			insSelected = (insKey==top.main_iframe.ptVisionPlanId)?' selected="selected"':'';
			rown +='<option value="'+insKey+'"'+insSelected+'>'+insVal+'</option>';
		});
		rown +='</select><input type="hidden" name="del_status_'+counter+'_'+lensItem+'_lensD" id="del_status_'+counter+'_'+lensItem+'_lensD" value="0" class="del_status"></td>';
							
						/*rown +='<input type="hidden" name="del_status_'+counter+'_'+lensItem+'_lensD" id="del_status_'+counter+'_'+lensItem+'_lens" class="del_status" value="0" />';*/
						
						rown +='<td><input type="checkbox" class="tax_applied" name="tax_applied_'+counter+'_'+lensItem+'_lensD" id="tax_applied_'+counter+'_'+lensItem+'_lensD" value="1" '+((tax_applied)?'checked="checked"':'')+' onChange="cal_overall_discount()" /></td>';
						
						rown += '<td><img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow(\'2\', \''+counter+'\', \''+itemNames[tb]+'\', \''+vision+'\');" /></td>';
						
						rown +='</tr>';
							
						if(lensItem==1){
							$('#lens_item_count_'+counter+'_lensD').remove();
							rown +='<input type="hidden" name="lens_item_count_'+counter+'_lensD" id="lens_item_count_'+counter+'_lensD" value="'+lensItem+'">';
							/*Frame Row should be at top*/
							/*If Frame Row Exists*/
							var frameRow =  $("table.table_collapse.posTable > tbody > tr#1_"+counter);
							if( frameRow.length > 0 ){
								$( rown ).insertAfter( frameRow[frameRow.length-1] );
							}
							else{
								$("table.table_collapse.posTable > tbody").prepend(rown);
							}
						}
						else{
							rowCount = $('tr[id^="2_'+counter+'"');
							lastRow = rowCount[rowCount.length-1];
							
							/*Group Rows on the basis of vision*/
							visRows = $("table.table_collapse.posTable > tbody > tr[id^='2_"+counter+"'][id$='"+vision+"']");
							
							diopterRow = $("table.table_collapse.posTable > tbody > tr[id^='2_"+counter+"_diopter_display_"+vision+"']");
							oversizeRow = $("table.table_collapse.posTable > tbody > tr[id^='2_"+counter+"_oversize_display_"+vision+"']");

							if( visRows.length > 0 ){
								lastRow = visRows[visRows.length-1];
							}
							
							if( diopterRow.length > 0 && tb !== 'in_lens_oversize' )
							{
								$(rown).insertBefore($(diopterRow[0]));
							}
							else if( oversizeRow.length > 0)
							{
								$(rown).insertBefore($(oversizeRow[0]));
							}
							else
							{
								/*OD at top always*/
								if( vision == 'od' && visRows.length == 0 ){
									lastRow = rowCount[0];
									$(rown).insertBefore($(lastRow));
								}
								else if( tb === 'in_lens_oversize' )
								{
									$(rown).insertAfter($(lastRow));
								}
								else
									$(rown).insertAfter($(lastRow));
							}
							$(lensItemCounter).val(lensItem);
						}
					}
				
				});
			},
			complete: function(){
				
				if(tb=="in_lens_material"){
					var posRows = $('.posTable tr[id^="'+rowId_bk+'"][id$="_'+vision+'"]');
						//var counter = rowId.charAt(2);
						$.each(posRows, function(i, rowObj){
							
							rowId = $(rowObj).attr('id');
							if($.inArray(rowId, rowIdArray)!="-1")
								return true;
							
							$(rowObj).find(".pracodefield").val("");
							$(rowObj).find(".pracodefield").attr("title","");
							$(rowObj).find(".price_cls").val('0.00');
							$(rowObj).find(".price_disc_per_proc").val('0.00');
							$(rowObj).find(".price_disc").val('0.00');
							$(rowObj).find(".qty_cls").val(0);
							$(rowObj).find(".price_total").val('0.00');
							$(rowObj).find(".allowed_cls").val('0.00');
							$(rowObj).addClass('hideRow');
							
							var delStatusField = $(rowObj).find(".del_status");
							if(delStatusField.length>0){
								$(delStatusField[0]).val("1"); /*Set Del Status*/
							}
							else{
								$(rowObj).remove();	
							}
						});
				}
				currencySymbols();
				prac_code_typeahead();
				changeLensPosLabel();
				calculate_all();
				
				var seg_vals = {in_lens_type: 'seg_type', in_lens_design: 'design', in_lens_material: 'material'};
				
				if(visionDD){
					fetch_vision_dd(val, seg_vals[tb], counter, false, false, vision);
				}
			}
		});
	}
	else if((!val && rowId) || (val=="uv400" && rowId)){
		if(tb=="in_lens_material" || tb=="in_lens_design"){
			var posRows = $('.posTable tr[id^="'+rowId+'"][id$="_'+vision+'"]');
			var counter = rowId.charAt(2);
			$.each(posRows, function(i, rowObj){
				
				$(rowObj).find(".pracodefield").val("");
				$(rowObj).find(".pracodefield").attr("title","");
				$(rowObj).find(".price_cls").val('0.00');
				$(rowObj).find(".price_disc_per_proc").val('0.00');
				$(rowObj).find(".price_disc").val('0.00');
				$(rowObj).find(".qty_cls").val(0);
				$(rowObj).find(".price_total").val('0.00');
				$(rowObj).find(".allowed_cls").val('0.00');
				$(rowObj).addClass('hideRow');
				
				var delStatusField = $(rowObj).find(".del_status");
				if(delStatusField.length>0){
					$(delStatusField[0]).val("1"); /*Set Del Status*/
				}
				else{
					$(rowObj).remove();	
				}
			});
		}
		else{
			$("#"+rowId+" .pracodefield").val("");
			$("#"+rowId+" .pracodefield").attr("title","");
			$("#"+rowId+" .price_cls").val('0.00');
			$("#"+rowId+" .price_disc").val('0.00');
			$("#"+rowId+" .qty_cls").val(0);
			$("#"+rowId+" .price_total").val('0.00');
			$("#"+rowId+" .allowed_cls").val('0.00');
			$("#"+rowId).addClass('hideRow');
			
			var delStatusField = $("#"+rowId).find(".del_status");
			if(delStatusField.length>0){
				$(delStatusField[0]).val("1"); /*Set Del Status*/
			}
			else{
				$("#"+rowId).remove();
			}
		}
		changeLensPosLabel();
		calculate_all();
	}
}

function get_prism_qty(counter, vision)
{
	var qty = 0;

	var prism = parseFloat($('input#lens_mr_'+vision+'_p_'+counter+'_lensD').val());
	var splash = parseFloat($('input#lens_mr_'+vision+'_splash_'+counter+'_lensD').val());
	
	qty += (isNaN(prism) === false)?prism:0;
	qty += (isNaN(splash) === false)?splash:0;
	
	if( qty > 0 && qty < 1 )
		qty = 1;

	return qty;
}

/*Frame oversize lens from Frame's atttribute - Abbrasive*/
function get_oversizelens_value(counter)
{
	var a_val = 0;
	
	if( typeof(counter) !== undefined )
	{	
		var frame_a = $('#a_'+counter);
		if( $(frame_a).length === 1 )
		{
			a_val = $(frame_a).val();
			a_val = a_val.replace(/[^\d\.]/g, '');
		}
	}

	a_val = parseFloat(a_val);
	return a_val;
}

function changeQtyLens(nVal, counter){
	
	if(typeof(nVal)=="undefined" || nVal=="" || nVal=="0")
	{
		nVal = 1;
		$("#qty_"+counter+"_lensD").val(nVal);
	}
	var pos_qty_rows = $('tr[id^="2_'+counter+'"]').not('[id^="2_'+counter+'_diopter_display"]');
	$(pos_qty_rows).each(function(i, row){
		$(row).find('.qty_cls').val(nVal);
	});
	calculate_all();
}
function sel_lab_add(id){
	var lab_id=$('#lab_id_'+id+'_lensD').val();
	var connect_visionweb='<?php echo $GLOBALS['connect_visionweb'];?>';
    var isVWLab = $('#lab_id_'+id+'_lensD>option:selected').is('.blueColor');

    if(lab_id>0 && connect_visionweb!="" && isVWLab === true){
		var lab_detail_id=$('#lab_detail_id_'+id+'_lensD').val();
		var lab_ship_detail_id=$('#lab_ship_detail_id_'+id+'_lensD').val();
		var winTop=window.screen.availHeight;
		winTop = (winTop/2)-190;
		
		var winWidth = window.screen.availWidth;
		winWidth = (winWidth/2)-390;
		top.WindowDialog.closeAll();
		var ptwin=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/lab_detail.php?lab_id='+lab_id+'&rxCount='+id+'&lab_detail_id='+lab_detail_id+'&lab_ship_detail_id='+lab_ship_detail_id+'&vw_loc_id='+<?php echo ($log_loc_imp!='')?$log_loc_imp:0; ?>,'lens_prescription_pop','width=800,height=440,left='+winWidth+',scrollbars=no,top='+winTop);
		ptwin.focus();

        $('#lab_id_'+id+'_lensD').removeClass('blackColor').addClass('blueColor');
	}
	else
    {
        $('#lab_detail_id_'+id+'_lensD').val('');
        $('#lab_ship_detail_id_'+id+'_lensD').val('');
        $('#lab_id_'+id+'_lensD').removeClass('blueColor').addClass('blackColor');
    }
}
</script>

<!-- Copyright 2000, 2001, 2002, 2003 Macromedia, Inc. All rights reserved. -->
</head>
<?php 
if(isset($_REQUEST['upc_name']) && $_REQUEST['upc_name'] !=""){
	echo "<script>
		$(document).ready(function(){
			get_details_by_upc('".$_REQUEST['upc_name']."')
		});
		</script>";
}

if(isset($_REQUEST['upc_name_lens']) && $_REQUEST['upc_name_lens'] !="")
{
	echo "<script>
		$(document).ready(function(){'
			get_details_by_upc_lensD('".$_REQUEST['upc_name_lens']."')
		});
		</script>";
}
?>
<body> 

<?php
	$main_order_status = $main_ord_row['order_status'];
	$remake = ($main_ord_row['re_make_id'] && $main_ord_row['re_make_id']!="0")?true:false;
	$remake_reason = "";
	$remake_reason_title = "";
	if($remake){
		$remake_data = imw_query("SELECT `remake_reason` FROM `in_order_remake_details` WHERE `order_id`='".$_SESSION['order_id']."'");
		if($remake_data && imw_num_rows($remake_data)>0){
			$remake_reason = imw_fetch_assoc($remake_data);
			$remake_reason = $remake_reason['remake_reason'];
			$remake_reason_title = $remake_reason;
			if(strlen($remake_reason)>7){
				$remake_reason = substr($remake_reason, 0, 7);
				$remake_reason .= "...";
			}
			
		}
		$remake_data = "";
	}
?>

  <form name="addframe" id="firstform" method="post" action="lensFrameAction.php" enctype="multipart/form-data">
<div class="container-fluid" style="padding:0;">
<div>
  <div class="listheading" <?php echo ($remake)?'style="background-color:rgba(0,153,17,0.49) !important;background-image:none;"':''; ?>>
	<div class="col-md-12 left_pad">
		<span style="width:134px;display:inline-block;">Frame Selection</span>
		<span style="display:inline-block; width: 100px">
		<?php
			$orderFalg = false;
			if($order_id!="" || $order_id>0){
                    echo "Order #".$order_id;
					$orderFalg = true;
	        }
        ?>
		</span>
		<span style="margin-right:15px;display:inline-block;width:100px;"><?php
			if( isset($main_ord_row['created_date']) && $main_ord_row['created_date']!='' ){
				echo $main_ord_row['created_date'];
			}else{
				?>
				<input type="text"  class="date-pick" name="order_date" id="order_date" style="height: 21px; background-size:17px 21px;width: 95px;" value="<?php echo date("m-d-Y"); ?>" autocomplete="off" />
				<?php
			}
		?></span>
		<span style="width:156px; display:inline-block;" title="<?php echo $remake_reason_title; ?>">
		<?php	if($remake){ echo "- Remake (".$remake_reason.")"; } ?>
		</span>
		<span style="width:108px;display:inline-block;">Lens Selection</span>
		<span style="width:168px;display:inline-block;">Last Exam: <?php echo $lastDos; ?></span>
		<span id="physicianDisp" title="">Physician: </span>
		<a href="javascript:void(0);" id="add_data_clone" style="text-decoration:none;float:right" onClick="addNew_frame();"><img src="../../images/add_btn.png" title="Add" alt="Add Another" style="border:0px none;"></a>
	</div>
  </div>
</div>
</div>

<div class="container-fluid input_upr" style="height:<?php echo $_SESSION['wn_height']-366; ?>px;overflow-y:scroll;overflow-x:hidden;" onChange="dataChanged=true;">
  <!--remake specific fields-->
<input type="hidden" name="remake_reason_id" id="remake_reason_id" value="0" />
<input type="hidden" name="remake_reason" id="remake_reason" value="" />
<input type="hidden" name="remake_prac_code" id="remake_prac_code" value="" />
<input type="hidden" name="remake_prac_code_id" id="remake_prac_code_id" value="" />
<input type="hidden" name="remake_price" id="remake_price" value="" />
<input type="hidden" name="remake_without_charges" id="remake_without_charges" value="0" />
<input type="hidden" name="remake_comments" id="remake_comments" value="" />
<input type="hidden" name="remake_doctor" id="remake_doctor" value="" />
<input type="hidden" name="remake_optician" id="remake_optician" value="" />
<input type="hidden" name="remake_lab" id="remake_lab" value="" />
<input type="hidden" name="remake_fac" id="remake_fac" value="" />

  <input type="hidden" name="frm_method" id="frm_method" value="<?php echo $action;?>" />
  <input type="hidden" name="frm_method_lensD" id="frm_method_lensD" value="<?php echo $action; ?>" />
  <input type="hidden" name="module_typePat" id="module_typePat" value="patient_interPage" />
  <input type="hidden" name="module_typePat_lensD" id="module_typePat_lensD" value="patient_interPage" />
  <input type="hidden" name="order_enc_id_chk" id="order_enc_id_chk" value="<?php echo $main_ord_row['order_enc_id']; ?>">
  <input type="hidden" name="vw_loc_id" id="vw_loc_id"  value="<?php echo $main_ord_row['loc_id']; ?>">


   <?php if($_SESSION['order_id']>0 && $action!="new_form"){
	if($action!="" && $action>0){
		$whr=" and id='$action'";
	}else{
		if($order_detail_id>0){
			$whr=" and id='$order_detail_id'";
		}
	}
	if($order_del_status==0)
	{
		$whr.=" and del_status='0'";
	}
	$sel_qry=imw_query("select * from in_order_details where order_id ='$order_id' $whr and patient_id='$patient_id' and module_type_id='1' order by id desc"); 
	}
	$LC=0;
	$sel_order=imw_fetch_array($sel_qry); 
	do{
	$LC++;
	if($sel_pic==""){
		$pt_wear_pic=$sel_order['pt_wear_pic'];
	}
	$order_detail_id=$sel_order['id'];
	$item_id=$sel_order['item_id'];
	$sel_qry2=imw_query("select in_item.qty_on_hand,in_item.threshold, in_item.stock_image, in_item.threshold, in_item_loc_total.stock from in_item LEFT JOIN in_item_loc_total on in_item.id=in_item_loc_total.item_id and in_item_loc_total.loc_id = '".$_SESSION['pro_fac_id']."' where in_item.id ='$item_id'");
	$sel_item=imw_fetch_array($sel_qry2);
	$stock_image=$sel_item['stock_image'];
	
	$itemStock = array();
	$itemStockSql = 'SELECT `loc`.`loc_name` AS \'name\', IF(ISNULL(`li`.`stock`), 0, `li`.`stock`) AS \'stock\'
						FROM `in_location` `loc`
						LEFT JOIN `in_item_loc_total` `li` ON(`loc`.`id` = `li`.`loc_id` AND `li`.`item_id` = '.((int)$item_id).')
						WHERE `loc`.`del_status` = 0
						ORDER BY `loc`.`loc_name` ASC';
	$itemStockResp = imw_query($itemStockSql);
	if($itemStockResp && imw_num_rows($itemStockResp)>0){
		while($stockRow = imw_fetch_object($itemStockResp)){
			$stockData = array();
			$stockData['name'] = $stockRow->name;
			$stockData['stock'] = $stockRow->stock;
			array_push($itemStock, $stockData);
		}
	}
?>
    <div id="all_data_<?php echo $LC; ?>" class="row all_data refData">
    <div style="float:left;width:100%;border-bottom:1px solid #CCC;"> 
      <!-----------------------------FRAME FORM HIDDEN FIELDS----------------------------->
      <input type="hidden" name="module_type_id_<?php echo $LC; ?>" id="module_type_id_<?php echo $LC; ?>" value="1">
      <input type="hidden" class="order_frm_detail_id_cls" name="order_detail_id_<?php echo $LC; ?>" id="order_detail_id_<?php echo $LC; ?>" value="<?php echo $sel_order['id']; ?>">
	  <input type="hidden" name="del_status_<?php echo $LC; ?>" id="del_status_<?php echo $LC; ?>" value="0" />
      <input type="hidden" name="upc_id_<?php echo $LC; ?>" id="upc_id_<?php echo $LC; ?>" value="<?php echo $sel_order['item_id']; ?>">
      <input type="hidden" name="item_prac_code_<?php echo $LC; ?>" id="item_prac_code_<?php echo $LC; ?>" value="<?php echo ($proc_code_arr[$sel_order['item_prac_code']]!="")?$proc_code_arr[$sel_order['item_prac_code']]:$sel_order['item_prac_code_default']; ?>" title="<?php echo ($proc_code_desc_arr[$sel_order['item_prac_code']])?$proc_code_desc_arr[$sel_order['item_prac_code']]:$sel_order['item_prac_code_default'];?>">
	  <!--  onChange="show_price_from_praccode(this,'price_<?php echo $LC; ?>','frm');" -->
     <!-- <input type="hidden" name="dx_code_<?php echo $LC; ?>" id="dx_code_<?php echo $LC; ?>" value="<?php echo $all_dx_codes; ?>" onChange="get_dxcode(this);" style="width:100px;">-->
      <input type="hidden" name="item_id_<?php echo $LC; ?>" id="item_id_<?php echo $LC; ?>" value="<?php echo $sel_order['item_id']; ?>">
      <input type="hidden" name="page_name" id="page_name" value="pt_frame_selection">    
      <input type="hidden" name="cur_date" id="cur_date" value="<?php echo date('Y-m-d'); ?>">
      <input type="hidden" name="allowed_<?php echo $LC; ?>" id="allowed_<?php echo $LC; ?>" value="<?php echo $sel_order['allowed']; ?>">
      <!---------------POS FRame Hidden Fields ---------------------------->
      <input type="hidden" name="dispPCodeF" id="dispPCodeF" value=""/>
      <input type="hidden" name="dispPriceF" id="dispPriceF" value="<?php echo $sel_order['price']; ?>"/>
      <input type="hidden" name="dispDiscF" id="dispDiscF" value="<?php echo $sel_order['discount']; ?>"/>
      <input type="hidden" name="dispQtyF" id="dispQtyF" value=""/>
      <input type="hidden" name="dispTotalF" id="dispTotalF" value="<?php echo $sel_order['total_amount']; ?>"/>
      <input type="hidden" name="dispCommentF" id="dispCommentF" onChange="$('#item_comment_<?php echo $LC; ?>').val($(this).val());">
      <input name="discount_<?php echo $LC; ?>" type="hidden" class="s_tbx" id="discount_<?php echo $LC; ?>" onChange="chk_dis_fun();" value="<?php echo $sel_order['discount']; ?>">
      
      <input name="price_<?php echo $LC; ?>" id="price_<?php echo $LC; ?>"  type="hidden" class="s_tbx" onChange="this.value = parseFloat(this.value).toFixed(2); chk_dis_fun();" value="<?php echo $sel_order['price']; ?>">
      <input name="price_hidden_<?php echo $LC; ?>" id="price_hidden_<?php echo $LC; ?>"  type="hidden" value="<?php echo $sel_order['price']; ?>">
      
      <input name="discount_hidden_<?php echo $LC; ?>" id="discount_hidden_<?php echo $LC; ?>"  type="hidden" value="<?php echo $sel_order['discount']; ?>">
      <input name="total_amount_<?php echo $LC; ?>" type="hidden" class="s_tbx" id="total_amount_<?php echo $LC; ?>" readonly value="<?php echo $sel_order['total_amount']; ?>">
      <input name="total_amount_hidden_<?php echo $LC; ?>" id="total_amount_hidden_<?php echo $LC; ?>"  type="hidden" value="<?php echo $sel_order['total_amount']; ?>">
      <input type="hidden" name="qty_<?php echo $LC; ?>" id="qty_<?php echo $LC; ?>" style="width:140px;" onChange="chk_dis_fun();" value="<?php echo (isset($sel_order['qty']))?$sel_order['qty']:"1"; ?>">
      <input type="hidden" name="qty_hidden_<?php echo $LC; ?>" id="qty_hidden_<?php echo $LC; ?>"  value="<?php echo (isset($sel_order['qty']))?$sel_order['qty']:"1"; ?>">
      <input type="hidden" name="item_comment_<?php echo $LC; ?>" id="item_comment_<?php echo $LC; ?>" style="width:140px;" value="<?php echo $sel_order['item_comment']; ?>">
	  <!---------------POS FRame Hidden Fields END---------------------------->
      <!-----------------------------FRAME FORM HIDDEN FIELDS END-----------------------------> 
      
      <div class="col-md-4" style="padding-right:0;">
        <div class="row">
          <div class="col-md-12" style="padding-bottom:5px;width:98%;margin:0;padding-right:0;">
            <div class="row extra_margin">
				<div class="col-md-8 nopadd">
					<div class="row">
						<div class="col-md-12 nopadd-right">
							<label for="upc_name_<?php echo $LC; ?>" class="frameLabel">UPC
							<?php if($sel_order['order_chld_id']>0){?><img src="../../images/flag_green.png" style="height: 14px;margin-left: 5px;" title="Posted" />
							<?php }?>  
							</label>
							<input type="text" name="upc_name_<?php echo $LC; ?>" id="upc_name_<?php echo $LC; ?>" style="width:42%;" autocomplete="off" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_<?php echo $LC; ?>'), <?php echo $LC; ?>)" value="<?php echo $sel_order['upc_code'];?>" class="upc_1">
						</div>
					</div>
				
					<div class="row extra_margin">
						<div class="col-md-12 nopadd-right">
						<?php 
							$label_text="Item Name";
							$displayItem=";display:block";
							$displayItemOther=";display:none";
							if($sel_order['upc_code']==$GLOBALS['CUSTOM_FRAME']['upc'])
							{
								$label_text="Custom Item Name";
								$displayItem=";display:none";
								$displayItemOther=";display:block";
							}
						?>
							<label for="item_name_<?php echo $LC; ?>" id="item_name_<?php echo $LC; ?>_label"  class="frameLabel"><?php echo $label_text;?></label>
							<input type="text" name="item_name_<?php echo $LC; ?>" id="item_name_<?php echo $LC; ?>" style="width:42%<?php echo $displayItem;?>" autocomplete="off" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_<?php echo $LC; ?>'), <?php echo $LC; ?>)" value="<?php echo $sel_order['item_name'];?>">
							<input type="text" name="item_name_<?php echo $LC; ?>_other" id="item_name_<?php echo $LC; ?>_other" style="width:42%;display:<?php echo $displayItemOther;?>" autocomplete="off"  value="<?php echo $sel_order['item_name_other'];?>" class="custom_item_name" data-row="<?php echo $LC; ?>">
						</div>
					</div>
				</div>
			  
				<div class="col-md-4 nopadd">
<?php
	$stock_image = 'no_image_thumb.jpg';
	$stock_image_l = 'no_image_xl.jpg';
	$image_base_path = $GLOBALS['DIR_PATH'].'/images/frame_stock';
	if( isset($sel_order['item_id']) && $sel_order['item_id']!='0' ){
		$item_image_name= trim($sel_order['upc_code']).'_'.trim($sel_order['item_id']).'.jpg';
		$stock_image	= 'thumb/'.$item_image_name;
		$stock_image_l	= 'xl/'.$item_image_name;
		
		/*Use Default Image if no stock Image exists*/
		if( !file_exists($image_base_path.'/'.$stock_image) ){
			$stock_image = 'no_image_thumb.jpg';
			$stock_image_l = 'no_image_xl.jpg';
		}
	}
?>
					<img style="vertical-align:middle;cursor:pointer;" id="stock_image_<?php echo $LC; ?>" large="<?php echo $GLOBALS['WEB_PATH'].'/images/frame_stock/'.$stock_image_l;?>" src="<?php echo $GLOBALS['WEB_PATH'].'/images/frame_stock/'.$stock_image;?>" onClick="show_stock_image(<?php echo $LC; ?>)" /> <a href="javascript:void(0);" onclick="javascript:stock_search(document.getElementById('module_type_id_<?php echo $LC; ?>').value,'', <?php echo $LC; ?>);" class="searchIcon"><img style="margin-left:0;" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/search.png" alt="Search Image" title="Search"></a>
				</div>
            </div>
			
			<div class="row extra_margin">
              <div class="col-md-12 nopadd">
              <?php 
			        $pofdetailqry = imw_query("select * from in_frame_pof where order_detail_id = '".$sel_order['id']."'");
                    $pofROW = imw_fetch_assoc($pofdetailqry);
              ?>
                <label for="manufacturer_id_<?php echo $LC; ?>"  class="frameLabel">Manufacturer</label>
                <input type="text" name="pof_manufacturer_id_<?php echo $LC; ?>" id="pof_manufacturer_id_<?php echo $LC; ?>" style="width:50%; display:none;" value="<?php echo $pofROW['manufacturer']; ?>" />
                <?php echo $objDropDown->drop_down('manufacturer_id',$sel_order['manufacturer_id'],"",$LC);?></div>
            </div>
            <div class="row extra_margin">
              <div class="col-md-12 nopadd">
                <label for="brand_id_<?php echo $LC; ?>" class="frameLabel">Brand</label>
                <input type="text" name="pof_brand_id_<?php echo $LC; ?>" id="pof_brand_id_<?php echo $LC; ?>" style="width:50%; display:none;" value="<?php echo $pofROW['brand']; ?>" />
                <?php echo $objDropDown->drop_down('brand_id',$sel_order['brand_id'],$sel_order['manufacturer_id'],$LC);?> </div>
            </div>
            <div class="row extra_margin">
              <div class="col-md-12 nopadd">
                <label for="color_id_<?php echo $LC; ?>" class="frameLabel">Color</label>
				
                <input type="text" name="pof_color_id_<?php echo $LC; ?>" id="pof_color_id_<?php echo $LC; ?>" style="width:24%; display:none;" value="<?php echo $pofROW['color']; ?>" />
<?php
$frameColorName = '';
if($sel_order['color_id']!=0){
	$sqlColorName = 'SELECT `color_name` FROM `in_frame_color` WHERE `id`='.(int)$sel_order['color_id'];
	$sqlColorName = imw_query($sqlColorName);
	if( $sqlColorName && imw_num_rows($sqlColorName)>0 ){
		$sqlColorName = imw_fetch_assoc($sqlColorName);
		$frameColorName = $sqlColorName['color_name'];
	}
}
else{
	$frameColorName = $sel_order['color_other'];
}
?>
				<input type="text" id="color_id_<?php echo $LC; ?>" name="color_id_<?php echo $LC; ?>" value="<?php echo $frameColorName; ?>" style="width:25%;height:23px;" autocomplete="off" />
				<input type="text" name="color_code_<?php echo $LC; ?>" id="color_code_<?php echo $LC; ?>" style="width:23.5%;float:none;vertical-align:top;" value="<?php echo ($sel_order['color_code'])?$sel_order['color_code']:''; ?>" />
              </div>
            </div>
            <div class="row extra_margin">
              <div class="col-md-12 nopadd">
                <label for="shape_id_<?php echo $LC; ?>" class="frameLabel">Shape</label>
                <input type="text" name="pof_shape_id_<?php echo $LC; ?>" id="pof_shape_id_<?php echo $LC; ?>" style="width:50%; display:none;" value="<?php echo $pofROW['shape']; ?>" />
				<?php $other_shape = ($sel_order['shape_id']=="0" && $sel_order['shape_other']!="")?true:false; ?>
				<select id="shape_id_<?php echo $LC; ?>" name="shape_id_<?php echo $LC; ?>" style="width:50%; <?php echo ($other_shape)?'display:none;':''?>" class="shape_id sel_dd" onChange="other_option(this)">
					<option value="0">Select</option>
		<?php	foreach($frameShapes as $key=>$val){
					$sel = ($key==$sel_order['shape_id'])?'selected':'';
					echo '<option value="'.$key.'" '.$sel.'>'.ucwords($val).'</option>';
				}
				?>
					<option disabled>--------------------------------------</option>
					<option value="other" <?php echo ($other_shape)?'selected':''?>>Other</option>
				</select>
				<input type="text" class="other_val" name="shape_other_<?php echo $LC; ?>" id="shape_other_<?php echo $LC; ?>" style="width:50%; <?php echo ($other_shape)?'':'display:none;'?>;" value="<?php echo ($sel_order['shape_other'])?$sel_order['shape_other']:''; ?>" />
				<img style="<?php echo ($other_shape)?'':'display:none;'?>" class="icon_back" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/icon_back.png" onClick="back_other(this)" />
              </div>
            </div>
            <div class="row extra_margin">
              <div class="col-md-12 nopadd">
                <label for="style_id_<?php echo $LC; ?>"class="frameLabel">Style</label>
                <input type="text" name="pof_style_id_<?php echo $LC; ?>" id="pof_style_id_<?php echo $LC; ?>" style="width:50%; display:none;" value="<?php echo $pofROW['style']; ?>" />
				<?php $other_style = ($sel_order['style_id']=="0" && $sel_order['style_other']!="")?true:false; ?>
                <select style="width:50%;height:23px;<?php echo ($other_style)?'display:none;':''?>" name="style_id_<?php echo $LC; ?>" id="style_id_<?php echo $LC; ?>" class="sel_dd" onChange="other_option(this)">
                  <option value="0">Select</option>
					<?php
                        $sql = "select id as style_id, style_name from in_frame_styles where del_status='0' order by style_name asc";
                        if($sel_order['brand_id']!="" && $sel_order['pof_check']==0){
                            $sty_whr = " AND bs.brand_id = '".$sel_order['brand_id']."'";
                            $sql = "SELECT fs.style_name, bs.style_id, bs.brand_id FROM in_style_brand as bs
                                    INNER JOIN in_frame_styles as fs ON fs.id=bs.style_id
                                    WHERE fs.del_status = '0' $sty_whr ORDER BY fs.style_name ASC";
                            $sel_style = imw_query($sql);
                            while($row_style = imw_fetch_array($sel_style)){
                                $selected = ($sel_order['style_id']==$row_style['style_id'])?"selected":"";
                                echo '<option value="'.$row_style['style_id'].'" '.$selected.'>'.addslashes($row_style['style_name']).'</option>';
                            }
                        }
                    ?>
					<option disabled>--------------------------------------</option>
					<option value="other" <?php echo ($other_style)?'selected':''?>>Other</option>
				</select>
				<input type="text" class="other_val" name="style_other_<?php echo $LC; ?>" id="style_other_<?php echo $LC; ?>" style="width:50%; <?php echo ($other_style)?'':'display:none;'?>;" value="<?php echo ($sel_order['style_other'])?$sel_order['style_other']:''; ?>" />
				<img style="<?php echo ($other_style)?'':'display:none;'?>" class="icon_back" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/icon_back.png" onClick="back_other(this)" />
              </div>
            </div>
            <div class="row extra_margin">
              <div class="col-md-12 nopadd">
                <label for="temple_<?php echo $LC; ?>" class="frameLabel">Temple</label>
                <input type="text" id="temple_<?php echo $LC; ?>" name="temple_<?php echo $LC; ?>" style="width:50%;" value="<?php echo $sel_order['temple']; ?>">
              </div>
            </div>
			<div class="row extra_margin">
              <div class="col-md-12 nopadd">
              	<label for="frame_type_id_<?php echo $LC; ?>" class="frameLabel">Type</label>
               	<select name="type_id_<?php echo $LC; ?>" id="type_id_<?php echo $LC; ?>" style="width:50%;">
				  <option value="0">Please Select</option>
				  <?php $rows="";
						$rows = data("select * from in_frame_types where del_status='0' $vw_type_whr order by type_name asc");
						foreach($rows as $r)
						{ 
						$selected = ($sel_order['type_id']==$r['id'])?"selected":"";
				?>
						<option value="<?php echo $r['id']; ?>" <?php echo $selected ?>><?php echo ucfirst($r['type_name']); ?></option>	
				<?php }	?>   
				</select>
				</div>
			</div>
			
			<div class="row extra_margin" style="margin-top:5px;">
				<div class="col-md-5 nopadd">
					<label for="a_<?php echo $LC; ?>"  style="width:39px;float:left;margin-left:5px;">A</label>
               		<input type="text" id="a_<?php echo $LC; ?>" name="a_<?php echo $LC; ?>" style="width:110px;margin-right:0px;" value="<?php echo $sel_order['a']; ?>" onChange="pos_row_display(<?php echo $LC; ?>, '2_<?php echo $LC; ?>_oversize_display', '', 'in_lens_oversize', '', false);">
				</div>
				<div class="col-md-6 nopadd-right">
					<label for="b_<?php echo $LC; ?>" style="width:50px;float:left;">B</label>
                	<input type="text" id="b_<?php echo $LC; ?>" name="b_<?php echo $LC; ?>" style="width:110px;" value="<?php echo $sel_order['b']; ?>">
				</div>
			</div>
			
			<div class="row extra_margin" style="margin-top:5px;">
				<div class="col-md-5 nopadd">
					<label for="ed_<?php echo $LC; ?>" style="width:39px;float:left;margin-left:5px;">ED</label>
                	<input type="text" id="ed_<?php echo $LC; ?>" name="ed_<?php echo $LC; ?>" style="width:110px;margin-right:0px;" value="<?php echo $sel_order['ed']; ?>">
				</div>
				<div class="col-md-6 nopadd-right">
					<label for="dbl_<?php echo $LC; ?>"  style="width:50px;float:left;">DBL</label>
                	<input type="text" id="dbl_<?php echo $LC; ?>" name="dbl_<?php echo $LC; ?>" style="width:110px;" value="<?php echo $sel_order['dbl']; ?>">
				</div>
			</div>
			
			<div class="row extra_margin" style="margin-top:5px;">
				<div class="col-md-5 nopadd">
					<label for="fpd_<?php echo $LC; ?>" style="width:39px;float:left;margin-left:5px;">FPD</label>
                	<input type="text" id="fpd_<?php echo $LC; ?>" name="fpd_<?php echo $LC; ?>" style="width:110px;margin-right:0px;" value="<?php echo $sel_order['fpd']; ?>">
				</div>
				<div class="col-md-6 nopadd-right">
					<label for="bridge_<?php echo $LC; ?>" style="width:50px;float:left;">Bridge</label>
                	<input type="text" id="bridge_<?php echo $LC; ?>" name="bridge_<?php echo $LC; ?>" style="width:110px;" value="<?php echo $sel_order['bridge']; ?>">
				</div>
			</div>
			
			<div class="row extra_margin" style="margin-top:8px;">
              <div class="col-md-2 nopadd" style="padding-left:5px;margin-right:15px;">
			  	<input class="chk_box_pof_<?php echo $LC; ?>" type="radio" name="order_chk_<?php echo $LC; ?>" value="1" id="order_chk_<?php echo $LC; ?>" style="margin-top:5px;" <?php if( $sel_order['order_chk']==1 || $order_id == NULL ){echo"checked";} ?> onClick="chk_pof_fun(<?php echo $LC; ?>, this);">
                <label for="order_chk_<?php echo $LC; ?>" style="float:left;">Order</label>
			  </div>
			  <div class="col-md-4 nopadd">
			  	<input class="chk_box_pof_<?php echo $LC; ?>" type="radio" name="use_on_hand_chk_<?php echo $LC; ?>" value="1" id="use_on_hand_chk_<?php echo $LC; ?>" style="margin-top:5px;" <?php if($sel_order['use_on_hand_chk']==1){echo"checked";} ?> onClick="chk_pof_fun(<?php echo $LC; ?>, this);">
				<input type="hidden" name="qty_reduced_<?php echo $LC; ?>" id="qty_reduced_<?php echo $LC; ?>" value="<?php echo $sel_order['qty_reduced']; ?>" />
				<input type="hidden" name="reduce_qty_<?php echo $LC; ?>" id="reduce_qty_<?php echo $LC; ?>" value="0" />
				
                <label for="use_on_hand_chk_<?php echo $LC; ?>" style="width:85px;float:left;margin-right:20px;">Use on hand</label>
			  </div>
			  <div class="col-md-4 nopadd">
			  	<input class="chk_box_pof_<?php echo $LC; ?> ptFrame" type="radio" name="in_add_<?php echo $LC; ?>" value="1" id="in_add_<?php echo $LC; ?>" style="margin-top:5px;" <?php if($sel_order['pof_check']=="1"){ echo "checked='checked'"; } ?> onClick="chk_pof_fun(<?php echo $LC; ?>, this);">
                <label for="in_add_<?php echo $LC; ?>" style="float:left;">Patient’s Frame<!--POF--></label>
			  </div>
			</div>
			
			<div class="row extra_margin" style="margin-top:5px;">
			  <div class="col-md-6" style="padding-left: 5px;">
			  	<input style="margin: 5px 5px 0 0;" type="checkbox" name="safety_glass_<?php echo $LC; ?>" id="safety_glass_<?php echo $LC; ?>" value="1" <?php if($sel_order['safety_glass']=="1"){ echo "checked='checked'"; } ?>/>
				<label for="safety_glass_<?php echo $LC; ?>">Safety Glasses</label>
			  </div>
			   <?php if($GLOBALS['connect_visionweb']!=""){
				   $trace_name="Add";
				   $filetitle = $fileName ='';
				   $trace_style='';
				   if($sel_order['trace_file']!=""){
						$trace_name="Update";
						$trace_class = "traceFileExists";
						
						$filetitle = $fileName = $sel_order['trace_file'];
						if( strlen($fileName) > 18 ){
							$fileName = substr($fileName, 0, 18).'..';
						}
					}
			   ?>
				   <div class="col-md-6">
					<input id="trace_file_<?php echo $LC; ?>" name="trace_file_<?php echo $LC; ?>" type="file" style=" display:none;width:1px;" class="traceFileField" orderItemKey="<?php echo $LC; ?>" />
					<input type="button" value="<?php echo $trace_name; ?> Trace File" onclick="$('#trace_file_<?php echo $LC; ?>').click();" class="<?php echo $trace_class; ?>" />
					<br />
					<span id="traceFileName_<?php echo $LC; ?>" title="<?php echo $filetitle; ?>"><?php echo $fileName; ?></span>
				  </div>
			  <?php } ?>
			</div>
			
            <?php
            //get colors
			if(isset($sel_item))
			{
				if($sel_item['qty_on_hand']<=$sel_item['threshold'])$color='#FF0000';
				else $color='#009900';
			}else $color='#FF1F1F'
			?>
            <div class="row extra_margin">
				<div class="col-md-5 nopadd-left">
					<span style="margin-left:5px;cursor:pointer;" onMouseOver="showStockDetails(this, <?php echo $LC; ?>);" onMouseOut="hideStockDetails(this);">Qty on hand: </span><span id="qoh_<?php echo $LC; ?>" style="color:<?php echo $color;?>;font-weight:bold;margin-right:25px;cursor:pointer;" onMouseOver="showStockDetails(this, <?php echo $LC; ?>);" onMouseOut="hideStockDetails(this);"><?php echo ($sel_item['qty_on_hand'])?$sel_item['qty_on_hand']:0;?></span>
					<div class="stockDetails" id="stockDetails_<?php echo $LC; ?>"><table><tbody><?php
	if(count($itemStock)>0):
		foreach($itemStock as $itmStock):
?><tr><td><?php echo $itmStock['name']; ?></td><td><?php echo $itmStock['stock']; ?></td></tr><?php
		endforeach;
	endif;
?></tbody></table></div>
				</div>
				<div class="col-md-6">
					<span>Qty at this facility: </span><span id="fqoh_<?php echo $LC; ?>" style="color:#FF1F1F;font-weight:bold;"><?php echo ($sel_item['stock'])?$sel_item['stock']:0;?></span>
				</div>
			</div>
			<div class="row extra_margin">
				<div class="col-md-12 nopadd-left">
					<label>Job Type</label>
					<select name="job_type_<?php echo $LC; ?>" id="job_type_<?php echo $LC; ?>">
						<option value="FTC">Frame To Come</option>
						<option value="RED"<?php echo ($sel_order['job_type']=='RED')?' selected':''; ?>>Lens Only</option>
						<option value="TBP"<?php echo ($sel_order['job_type']=='TBP')?' selected':''; ?>>Supply Frame</option>
						<option value="UNC"<?php echo ($sel_order['job_type']=='UNC')?' selected':''; ?>>Uncut</option>
					</select>
				</div>
			</div>
          </div>
        </div>
      </div>     
<?php
	$frameId = $sel_order['id'];
	$sel_order="";
	$whr="";
	$order_detail_id="";
	if($_SESSION['order_id']>0 && $action!="new_form")
	{
		$whr = "";
		if($action!="" && $action>0){
			$whr=" and id='$action'";
			$lens_pres_whr = " and det_order_id='$order_detail_id'";
			$price_whr = " and order_detail_id='$order_detail_id'";
		}else{
			if($order_detail_id>0){
				$whr=" and id='$order_detail_id'";
				$lens_pres_whr = " and det_order_id='$order_detail_id'";
				$price_whr = " and order_detail_id='$order_detail_id'";
			}
		}
		
		if($order_del_status==0)
		{
			$whr.=" and del_status='0'";
		}
		
		$sel_qry1=imw_query("select * from in_order_details where order_id ='$order_id' $whr and patient_id='$patient_id' and module_type_id='2' and lens_frame_id ='$frameId' order by show_default desc");
		$sel_order=imw_fetch_array($sel_qry1);
		$item_id=$sel_order['item_id'];
		$order_detail_id=$sel_order['id'];
		$lens_frame_id=$sel_order['lens_frame_id'];
		$lens_selection_id=$sel_order['lens_selection_id'];
		
		if($order_detail_id>0){
			$whr=" and id='$order_detail_id'";
			$lens_pres_whr = " and det_order_id='$order_detail_id'";
			$price_whr = " and order_detail_id='$order_detail_id'";
		}
		
		$frame_id=$_REQUEST['frame_id'];
		$frame_whr='';
		if($order_del_status==0)
		{
			$frame_whr.=" and del_status='0'";
			$price_whr.=" and del_status='0'";
		}
		if($lens_frame_id>0)
		{
			$frame_whr.= "and id='$lens_frame_id'";
		}
		elseif(isset($frame_id) && $frame_id>0)
		{
			$frame_whr.= "and id='$frame_id'";
		}
		
		$sel_frame_qry=imw_query("select id as frame_order_id,item_name as frame_name,item_id as frame_item_id,price as frame_price, qty as frame_qty,discount as frame_discount,total_amount as frame_total_amount,item_prac_code as frame_item_prac_code from in_order_details where order_id ='$order_id' $frame_whr and patient_id='$patient_id' and module_type_id='1'");
		$sel_frame_order=imw_fetch_array($sel_frame_qry);
		
		$sel_price_qry=imw_query("select * from in_order_item_price_details where order_id ='$order_id' $price_whr and patient_id='$patient_id'");
		$sel_price_order=imw_fetch_array($sel_price_qry);
		
		$price_order_id=$sel_price_order['id'];
		$lens_price=$sel_price_order['lens_wholesale'];
		$material_price=$sel_price_order['material_wholesale'];
		$a_r_price=$sel_price_order['a_r_wholesale'];
		$transition_price=$sel_price_order['transition_wholesale'];
		$polarization_price=$sel_price_order['polarization_wholesale'];
		$tint_price=$sel_price_order['tint_wholesale'];
		$uv400_price=$sel_price_order['uv400_wholesale'];
		$other_price=$sel_price_order['other_wholesale'];
		$lens_discount=$sel_price_order['lens_discount'];
		$material_discount=$sel_price_order['material_discount'];
		$a_r_discount=$sel_price_order['a_r_discount'];
		$transition_discount=$sel_price_order['transition_discount'];
		$polarization_discount=$sel_price_order['polarization_discount'];
		$tint_discount=$sel_price_order['tint_discount'];
		$uv400_discount=$sel_price_order['uv400_discount'];
		$other_discount=$sel_price_order['other_discount'];	
		
		if($order_detail_id>0)
		{
			$lp_whr='';
			if($order_del_status==0)
			{
				$lp_whr=" and del_status='0'";
			}
			$sel_lens_price_qry=imw_query("select orlp.id as lens_price_id, orlp.* from in_order_lens_price_detail as orlp  where order_id ='$order_id' and order_detail_id='$order_detail_id' and patient_id='$patient_id' $lp_whr");
		}
		else
		{
			$sel_lens_price_qry = imw_query("select id as itemized_id, lens_item_name as itemized_name from in_lens_items_detail");
		}
	}
	else
	{
		$sel_lens_price_qry = imw_query("select id as itemized_id, lens_item_name as itemized_name from in_lens_items_detail");
	}
	$lens_item_count = imw_num_rows($sel_lens_price_qry);
	$order_chld_id="";
	while($sel_lens_price_data=imw_fetch_array($sel_lens_price_qry))
	{
		if($sel_lens_price_data['order_chld_id']>0){
			$order_chld_id=$sel_lens_price_data['order_chld_id'];
		}
		$sqlUPC = "SELECT `upc_code` FROM `in_order_details` WHERE `id`='".$sel_lens_price_data['order_detail_id']."'";
		$UPCResp = imw_query($sqlUPC);
		if($UPCResp && imw_num_rows($UPCResp)>0){
			$UPCResp = imw_fetch_assoc($UPCResp);
			$sel_lens_price_data['upc_code']=$UPCResp['upc_code'];
		}
		else{
			$sel_lens_price_data['upc_code']="";
		}
		
		$lens_price_data[] = $sel_lens_price_data;
	}
	
	/*Rx Data*/
	/*Lens Data*/
	if($order_id!="" && $order_id!="0"){
		$lensRs=imw_query("Select in_optical_order_form.*, DATE_FORMAT(rx_dos, '%m-%d-%y') AS 'rx_dos_1', users.fname, users.lname FROM in_optical_order_form LEFT JOIN users ON users.id=in_optical_order_form.physician_id WHERE order_id='".$order_id."' $lens_pres_whr AND patient_id='$patient_id' AND del_status='0'");
		$lensRes=imw_fetch_array($lensRs);
		
		if($lensRes['physician_name']==""){
			if(($lensRes['fname']!='' || $lensRes['lname']!='') && $lensRes['physician_id']>0){
				$phyName=$lensRes['lname'].', '.$lensRes['fname'];
			}
		}
		else{
				$phyName=$lensRes['physician_name'];
		}
	}
	/*End Lens Data*/
?>
      <!-----------------------------LENS FORM HIDDEN FIELDS----------------------------->
      <input type="hidden" name="discount_hidden_<?php echo $LC; ?>_lensD" id="discount_hidden_<?php echo $LC; ?>_lensD" value="<?php echo $sel_order['discount']; ?>">
	  <input type="hidden" name="module_type_id_<?php echo $LC; ?>_lensD" id="module_type_id_<?php echo $LC; ?>_lensD" value="2">
      <input type="hidden" name="lens_frame_id_<?php echo $LC; ?>_lensD" id="lens_frame_id_<?php echo $LC; ?>_lensD" value="<?php echo ($sel_order['lens_frame_id']!="")?$sel_order['lens_frame_id']:$frameId; ?>">
      
      <input type="hidden" class="order_lens_detail_id_cls" name="order_detail_id_<?php echo $LC; ?>_lensD" id="order_detail_id_<?php echo $LC; ?>_lensD" value="<?php echo $sel_order['id']; ?>">
	  <input type="hidden" name="del_status_<?php echo $LC; ?>_lensD>" id="del_status_<?php echo $LC; ?>_lensD" value="0" />
      <input type="hidden" name="item_id_<?php echo $LC; ?>_lensD" id="item_id_<?php echo $LC; ?>_lensD" value="<?php echo $sel_order['item_id']; ?>">
      <?php $action=$_REQUEST['frm_method_lens']; ?>
      <input type="hidden" name="lens_prescription_count[<?php echo $LC; ?>]" >
      <input type="hidden" name="upc_id_<?php echo $LC; ?>_lensD" id="upc_id_<?php echo $LC; ?>_lensD" value="<?php echo $sel_order['item_id']; ?>">
      <input type="hidden" name="isRXLoaded_lensD_<?php echo $LC; ?>" id="isRXLoaded_lensD_<?php echo $LC; ?>" value="<?php echo $lensRes['id'];?>">
      <input type="hidden" name="page_name_lensD" value="lens_selection" />
      <input type="hidden" name="order_rx_lens_id_<?php echo $LC; ?>" id="order_rx_lens_id_<?php echo $LC; ?>" value="<?php echo $lensRes['id'];?>">
      <!------------------- POS Lens data fields ------------------------>


    <input type="hidden" name="frame_order_id" id="frame_order_id_lensD" value="<?php echo $sel_frame_order['frame_order_id']; ?>">
    <input type="hidden" name="dx_frame_order_id" id="dx_frame_order_id_lensD" value="<?php echo $sel_order['lens_frame_id']; ?>">
    <input type="hidden" name="price_order_id" id="price_order_id_lensD" value="<?php echo $price_order_id; ?>">
    <?php
    $final_price_arr=array($lens_price,$material_price,$a_r_price,$transition_price,$polarization_price,$tint_price,$uv400_price,$other_price,$framePrice);
    $final_discount_arr=array($lens_discount,$material_discount,$a_r_discount,$transition_discount,$polarization_discount,$tint_discount,$uv400_discount,$other_discount);
    ?>
    <input type="hidden" name="overall_lens_discount_<?php echo $LC; ?>" value="<?php echo $sel_order['overall_discount']; ?>" onBlur="apply_dis_all(this);">
      <!------------------- POS Field End ------------------------------->
     
      <!-----------------------------LENS FORM HIDDEN FIELDS END----------------------------->
      <div class="col-md-8" style="border-left:1px solid #CCC;min-height:398px;float:left;padding-bottom:10px; padding-left:20px;">
        <div class="row extra_margin">
          <div class="col-md-6">
            <label for="upc_name_<?php echo $LC; ?>_lensD" style="width:90px;float:left;">UPC
			<?php if($order_chld_id>0){?><img src="../../images/flag_green.png" style="height: 14px;margin-left: 5px;" title="Posted" />
			  <?php }?>
			</label>
            <input type="text" name="upc_name_<?php echo $LC; ?>_lensD" id="upc_name_<?php echo $LC; ?>_lensD" autocomplete="off" style="width:65%;" onChange="javascript:get_details_by_upc_lensD(document.getElementById('upc_id_<?php echo $LC; ?>_lensD'), <?php echo $LC; ?>);" value="<?php echo $sel_order['upc_code']; ?>">
          </div>
          <div class="col-md-6" style="padding-right:0;">
            <label for="item_name_<?php echo $LC; ?>_lensD" style="width:90px;float:left;">Item Name</label>
<!-- onChange="javascript:get_details_by_upc_lensD(document.getElementById('upc_id_<?php echo $LC; ?>_lensD'), <?php echo $LC; ?>);" -->
            <input type="text" name="item_name_<?php echo $LC; ?>_lensD" id="item_name_<?php echo $LC; ?>_lensD" autocomplete="off" style="width:65%;"  value="<?php echo $sel_order['item_name']; ?>"><a href="javascript:void(0);" onclick="javascript:stock_search_lensD(document.getElementById('module_type_id_<?php echo $LC; ?>_lensD').value,'', <?php echo $LC; ?>);" class="searchIcon">
			<img src="../../images/search.png" alt="Search Image" title="Search"></a>
          </div>
        </div>

<!-- Lens Dual Vals -->

<div class="row extra_margin" style="border:1px solid #ccc;padding: 10px 15px 10px 15px;">
		<div class="row">
			<div class="col-md-2">
				<select class="<?php echo isset($sel_order['lens_vision'])?$sel_order['lens_vision']:'ou'; ?>Color lens_vision" name="lens_vision_<?php echo $LC; ?>_lensD" id="lens_vision_<?php echo $LC; ?>_lensD" onChange="changeVision(<?php echo $LC; ?>)">
<?php			foreach($lens_vision_array as $val){
					$selected = ($sel_order['lens_vision'] === $val) ? 'selected' : '' ;
					echo '<option class="'.$val.'Color" value="'.$val.'" '.$selected.'>'.$val.'</option>';
				}
				$seg_od_status = '';
				$seg_os_status = '';
				if($sel_order['lens_vision']=='od'){
					$seg_os_status = 'disabled';
				}
				elseif($sel_order['lens_vision']=='os'){
					$seg_od_status = 'disabled';
				}
?>				</select>
			</div>
			<div class="col-md-4 nopadd-left" style="text-align:center;padding-right:34px;">
				<span class="blueColor" style="font-weight:bold;">OD</span>
			</div>
			
			<div class="col-md-2"></div>
			<div class="col-md-4 nopadd-left" style="text-align:center;padding-right:34px;">
				<span class="greenColor" style="font-weight:bold;">OS</span>
			</div>
		</div>
		
		<div class="row extra_margin">
			<div class="col-md-6">
				<label for="seg_type_id_<?php echo $LC; ?>_od_lensD" style="width:90px;float:left;">Seg Type</label>
				<select name="seg_type_id_<?php echo $LC; ?>_od_lensD" id="seg_type_id_<?php echo $LC; ?>_od_lensD" style="width:65%;" onChange="pos_row_display(<?php echo $LC; ?>, '2_<?php echo $LC; ?>_lens_display', this.value, 'in_lens_type', 'od');" <?php echo $seg_od_status; ?>>
					<option value="0" default_val="">Please Select</option>
				<?php  
					$qry="";
					$order_vw_code = 0;
					$qry = imw_query("SELECT `id`, `vw_code`, `type_name` FROM `in_lens_type` WHERE `del_status`='0' ORDER BY FIELD(`vw_code`, 'SV','PAL','BFF','TFF')");
					while($rows = imw_fetch_object($qry)){
						if($sel_order['seg_type_od'] == $rows->id){
							$selected = 'selected';
							$order_vw_code = $rows->vw_code;
						}
						else
							$selected = '';
				?>
						<option <?php echo $selected; ?> value="<?php echo $rows->id; ?>" default_val="<?php echo $default_vals[$rows->vw_code]; ?>"><?php echo $rows->type_name; ?></option>
				<?php }
					imw_free_result($qry);
				?>
				</select>
			</div>
			<div class="col-md-6" style="padding-right:0px;">
				<label for="seg_type_id_<?php echo $LC; ?>_os_lensD" style="width:90px;float:left;">Seg Type</label>
				<select name="seg_type_id_<?php echo $LC; ?>_os_lensD" id="seg_type_id_<?php echo $LC; ?>_os_lensD" style="width:65%;" onChange="pos_row_display(<?php echo $LC; ?>, '2_<?php echo $LC; ?>_lens_display', this.value, 'in_lens_type', 'os');" <?php echo $seg_os_status; ?>>
					<option value="0" default_val="">Please Select</option>
				<?php  
					$qry="";
					$order_vw_code_os = 0;
					$qry = imw_query("SELECT `id`, `vw_code`, `type_name` FROM `in_lens_type` WHERE `del_status`='0' ORDER BY FIELD(`vw_code`, 'SV','PAL','BFF','TFF')");
					while($rows = imw_fetch_object($qry)){
						if($sel_order['seg_type_os'] == $rows->id){
							$selected = 'selected';
							$order_vw_code_os = $rows->vw_code;
						}
						else
							$selected = '';
				?>
						<option <?php echo $selected; ?> value="<?php echo $rows->id; ?>" default_val="<?php echo $default_vals[$rows->vw_code]; ?>"><?php echo $rows->type_name; ?></option>
				<?php }
					imw_free_result($qry);
				?>
				</select>
			</div>
		</div>
				
        <div class="row extra_margin">
		 	<div class="col-md-6">
            <label for="design_id_<?php echo $LC; ?>_od_lensD" style="width:90px;float:left;">Design</label>
            <select name="design_id_<?php echo $LC; ?>_od" id="design_id_<?php echo $LC; ?>_od_lensD" style="width:65%;" onChange="pos_row_display(<?php echo $LC; ?>, '2_<?php echo $LC; ?>_design_display', this.value, 'in_lens_design', 'od');" <?php echo (!$orderFalg || $seg_od_status!='')?"disabled":""; ?>>
              <option value="0">Please Select</option>
<?php 
	if($orderFalg){
		$design_fetch = imw_query("SELECT 
										`id`, 
										`design_name`,
										`del_status`
									FROM 
										`in_lens_design` 
									WHERE 
										(`lens_vw_code`='".$order_vw_code."'
										AND del_status='0')
										OR id = '".$sel_order['design_id_od']."'
										ORDER BY `design_name` ASC");
			while($row = imw_fetch_object($design_fetch)){
				echo '<option '.(($sel_order['design_id_od']==$row->id)?"selected='selected'":"").' '.(($row->del_status=='0')?'':'disabled="disabled"').' value="'.$row->id.'">'.$row->design_name.'</option>';
			}
	}
?>
	        </select>
			<img style="float:right; margin-top: 6px;" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/icon_bl.png" alt="BL" onclick="blVals(<?php echo $LC; ?>)">
          </div>
		  <div class="col-md-6" style="padding-right:0px;">
            <label for="design_id_<?php echo $LC; ?>_os_lensD" style="width:90px;float:left;">Design</label>
            <select name="design_id_<?php echo $LC; ?>_os" id="design_id_<?php echo $LC; ?>_os_lensD" style="width:65%;" onChange="pos_row_display(<?php echo $LC; ?>, '2_<?php echo $LC; ?>_design_display', this.value, 'in_lens_design', 'os');" <?php echo (!$orderFalg || $seg_os_status!='')?"disabled":""; ?>>
              <option value="0">Please Select</option>
<?php 
	if($orderFalg){
		$design_fetch = imw_query("SELECT 
										`id`, 
										`design_name`,
										`del_status`
									FROM 
										`in_lens_design` 
									WHERE 
										(`lens_vw_code`='".$order_vw_code_os."'
										AND del_status='0')
										OR id = '".$sel_order['design_id_os']."'
										ORDER BY `design_name` ASC");
			while($row = imw_fetch_object($design_fetch)){
				echo '<option '.(($sel_order['design_id_os']==$row->id)?"selected='selected'":"").' '.(($row->del_status=='0')?'':'disabled="disabled"').' value="'.$row->id.'">'.$row->design_name.'</option>';
			}
	}
?>
	        </select>
          </div>
		</div>
		
		<div class="row extra_margin">
		 	<div class="col-md-6">
            <label for="material_id_<?php echo $LC; ?>_od_lensD" style="width:90px;float:left;">Material</label>
            <select name="material_id_<?php echo $LC; ?>_od" id="material_id_<?php echo $LC; ?>_od_lensD" style="width:65%;" onChange="pos_row_display(<?php echo $LC; ?>, '2_<?php echo $LC; ?>_material', this.value, 'in_lens_material', 'od');" <?php echo (!$orderFalg || $seg_od_status!='')?"disabled":""; ?>>
              <option value="0">Please Select</option>
<?php
	if($orderFalg){
		if(isset($sel_order['design_id_od']) && $sel_order['design_id_od'] > 0){
			$material_where_od = '`ld`.`id`=\''.$sel_order['design_id_od'].'\'';
		}
		else{
			$material_where_od = '`ld`.`lens_vw_code`=\''.$order_vw_code.'\'';
		}
		
		$material_fetch = imw_query('SELECT 
											`lm`.`id`, 
											`lm`.`material_name`,
											`lm`.`del_status`
										FROM 
											`in_lens_design` `ld` 
											LEFT JOIN `in_lens_material_design` `dm` ON(`ld`.`id` = `dm`.`design_id`) 
											LEFT JOIN `in_lens_material` `lm` ON(`dm`.`material_id` = `lm`.`id`) 
										WHERE 
											('.$material_where_od.'
											AND `lm`.`del_status` = 0)
											OR
											`lm`.`id` = \''.$sel_order['material_id_od'].'\'
										GROUP BY 
											`dm`.`material_id` ORDER BY `lm`.`material_name` ASC');
			while($row = imw_fetch_object($material_fetch)){
				echo '<option '.(($sel_order['material_id_od']==$row->id)?"selected='selected'":"").' '.(($row->del_status=='0')?'':'disabled="disabled"').' value="'.$row->id.'">'.$row->material_name.'</option>';
			}
	}
?>
            </select>
          </div>
		  <div class="col-md-6" style="padding-right:0px;">
            <label for="material_id_<?php echo $LC; ?>_os_lensD" style="width:90px;float:left;">Material</label>
            <select name="material_id_<?php echo $LC; ?>_os" id="material_id_<?php echo $LC; ?>_os_lensD" style="width:65%;" onChange="pos_row_display(<?php echo $LC; ?>, '2_<?php echo $LC; ?>_material', this.value, 'in_lens_material', 'os');" <?php echo (!$orderFalg || $seg_os_status!='')?"disabled":""; ?>>
              <option value="0">Please Select</option>
<?php
	if($orderFalg){
		if(isset($sel_order['design_id_os']) && $sel_order['design_id_os'] > 0){
			$material_where_os = '`ld`.`id`=\''.$sel_order['design_id_os'].'\'';
		}
		else{
			$material_where_os = '`ld`.`lens_vw_code`=\''.$order_vw_code_os.'\'';
		}
		
		$material_fetch = imw_query('SELECT 
											`lm`.`id`, 
											`lm`.`material_name`, 
											`lm`.`del_status` 
										FROM 
											`in_lens_design` `ld` 
											LEFT JOIN `in_lens_material_design` `dm` ON(`ld`.`id` = `dm`.`design_id`) 
											LEFT JOIN `in_lens_material` `lm` ON(`dm`.`material_id` = `lm`.`id`) 
										WHERE 
											('.$material_where_os.' 
											AND `lm`.`del_status` = 0)
											OR
											`lm`.`id` = \''.$sel_order['material_id_os'].'\'
										GROUP BY 
											`dm`.`material_id` ORDER BY `lm`.`material_name` ASC');
			while($row = imw_fetch_object($material_fetch)){
				echo '<option '.(($sel_order['material_id_os']==$row->id)?"selected='selected'":"").' '.(($row->del_status=='0')?'':'disabled="disabled"').' value="'.$row->id.'">'.$row->material_name.'</option>';
			}
	}
?>
            </select>
          </div>
		  </div>
		  
		<div class="row extra_margin"> 
		  <div class="col-md-6">
            <label for="a_r_id_<?php echo $LC; ?>_od_lensD" style="width:90px;float:left;">Treatment</label>
            <div class="multiDD" style="display:inline-block; width:240px;">
				<select name="a_r_id_<?php echo $LC; ?>_od" id="a_r_id_<?php echo $LC; ?>_od_lensD" style="width:65%;" <?php echo (!$orderFalg)?"disabled":""; ?>>
<?php
	if($orderFalg){
		$sel_a_r_vals = explode(";", $sel_order['a_r_id_od']);
		
		$tretment_list = array();
		/*Fetch List of Treatments connected to the selected material>design>seg_type*/
		
			/*List all materials ids*/
				$material_ids = array();
				if( isset($sel_order['material_id_od']) && $sel_order['material_id_od'] > 0 ){
					$material_ids[$sel_order['material_id_od']] = true;
				}
				else{
					$material_qry = imw_query('SELECT 
														DISTINCT(`dm`.`material_id`) AS `material_id` 
													FROM 
														`in_lens_design` `ld` 
														INNER JOIN `in_lens_material_design` `dm` ON(
															`ld`.`lens_vw_code` = \''.$order_vw_code.'\' 
															AND `ld`.`id` = `dm`.`design_id`
														)');
					while($row_t = imw_fetch_object($material_qry)){
						$material_ids[$row_t->material_id] = true;
					}
					imw_free_result($material_qry);
				}
			/*End material list ids*/
		
			/*List Treatment ids*/
				$treatment_ids = array();
				if( isset($sel_order['material_id_od']) && $sel_order['material_id_od'] > 0 ){
					$treatment_sql = 'SELECT 
											`material_id`, 
											`ar_id` 
										FROM 
											`in_lens_ar_material`
										WHERE `material_id`=\''.$sel_order['material_id_od'].'\'';
				}
				else{
					$treatment_sql = 'SELECT 
											DISTINCT(`material_id`) AS `material_id`, 
											`ar_id` 
										FROM 
											`in_lens_ar_material`';
				}
				
				$treatment_qry = imw_query($treatment_sql);
				
				while($row_t = imw_fetch_object($treatment_qry)){
					if($material_ids[$row_t->material_id]){
						$treatment_ids[$row_t->ar_id] = true;
					}
				}
				imw_free_result($treatment_qry);
				unset($material_ids);
			/*End list Treatment ids*/
		
			if(count($treatment_ids)>0){
				
				/*List all Treatment Values*/
				$treatments_qry = imw_query("SELECT 
													`id`, 
													`ar_name` 
												FROM 
													`in_lens_ar`
													WHERE `del_status`=0");
				while($row_t = imw_fetch_object($treatments_qry)){
					if($treatment_ids[$row_t->id]){
						$tretment_list[$row_t->id] = $row_t->ar_name;
					}
				}
				imw_free_result($treatments_qry);
				asort($tretment_list, SORT_NATURAL | SORT_FLAG_CASE);
				unset($treatment_ids);
				/*End list all Treatment Values*/
			}
		/*End List of Treatments connected to the selected material>design>seg_type*/
		
		foreach($tretment_list as $key=>$value){
			echo '<option '.((in_array($key, $sel_a_r_vals))?"selected='selected'":"").' value="'.$key.'">'.$value.'</option>';
		}
		unset($tretment_list, $row_treatment);
	}
?>
				</select>
			</div>
          </div>
		  
		  <div class="col-md-6">
            <label for="a_r_id_<?php echo $LC; ?>_os_lensD" style="width:90px;float:left;">Treatment</label>
            <div class="multiDD" style="display:inline-block; width:240px;">
				<select name="a_r_id_<?php echo $LC; ?>_os" id="a_r_id_<?php echo $LC; ?>_os_lensD" style="width:65%;" <?php echo (!$orderFalg)?"disabled":""; ?>>
<?php
	if($orderFalg){
		$sel_a_r_vals = explode(";", $sel_order['a_r_id_os']);
		
		$tretment_list = array();
		/*Fetch List of Treatments connected to the selected material>design>seg_type*/
		
			/*List all materials ids*/
				$material_ids = array();
				if( isset($sel_order['material_id_os']) && $sel_order['material_id_os'] > 0 ){
					$material_ids[$sel_order['material_id_os']] = true;
				}
				else{
					$material_qry = imw_query('SELECT 
														DISTINCT(`dm`.`material_id`) AS `material_id` 
													FROM 
														`in_lens_design` `ld` 
														INNER JOIN `in_lens_material_design` `dm` ON(
															`ld`.`lens_vw_code` = \''.$order_vw_code_os.'\' 
															AND `ld`.`id` = `dm`.`design_id`
														)');
					while($row_t = imw_fetch_object($material_qry)){
						$material_ids[$row_t->material_id] = true;
					}
					imw_free_result($material_qry);
				}
			/*End material list ids*/
		
			/*List Treatment ids*/
				$treatment_ids = array();
				
				if( isset($sel_order['material_id_os']) && $sel_order['material_id_os'] > 0 ){
					$treatment_sql = 'SELECT 
											`material_id`, 
											`ar_id` 
										FROM 
											`in_lens_ar_material`
										WHERE `material_id`=\''.$sel_order['material_id_os'].'\'';
				}
				else{
					$treatment_sql = 'SELECT 
											DISTINCT(`material_id`) AS `material_id`, 
											`ar_id` 
										FROM 
											`in_lens_ar_material`';
				}
				
				$treatment_qry = imw_query($treatment_sql);
				
				while($row_t = imw_fetch_object($treatment_qry)){
					if($material_ids[$row_t->material_id]){
						$treatment_ids[$row_t->ar_id] = true;
					}
				}
				imw_free_result($treatment_qry);
				unset($material_ids);
			/*End list Treatment ids*/
		
			if(count($treatment_ids)>0){
				
				/*List all Treatment Values*/
				$treatments_qry = imw_query("SELECT 
													`id`, 
													`ar_name` 
												FROM 
													`in_lens_ar`
													WHERE `del_status`=0");
				while($row_t = imw_fetch_object($treatments_qry)){
					if($treatment_ids[$row_t->id]){
						$tretment_list[$row_t->id] = $row_t->ar_name;
					}
				}
				imw_free_result($treatments_qry);
				asort($tretment_list, SORT_NATURAL | SORT_FLAG_CASE);
				unset($treatment_ids);
				/*End list all Treatment Values*/
			}
		/*End List of Treatments connected to the selected material>design>seg_type*/
		
		foreach($tretment_list as $key=>$value){
			echo '<option '.((in_array($key, $sel_a_r_vals))?"selected='selected'":"").' value="'.$key.'">'.$value.'</option>';
		}
		unset($tretment_list, $row_treatment);
	}
?>
				</select>
			</div>
          </div>
        </div>
</div>
<!-- End Lens Dual Vals -->

        <div class="row extra_margin">
          <div class="col-md-6">
             <label for="other_<?php echo $LC; ?>_lensD" style="width:90px;float:left;">Comments</label>
             <textarea name="other_<?php echo $LC; ?>" id="other_<?php echo $LC; ?>_lensD" style="width:65%;resize:none;border:1px solid #ccc;height:25px; float:left;"><?php echo stripslashes($sel_order['lens_other']); ?></textarea>
          </div>
          <div class="col-md-6" style="padding-right:0;">
                      	<div class="row">
              <div class="col-md-3 nopadd-right" style="display:none;">
                <input type="checkbox" name="uv400_<?php echo $LC; ?>" id="uv400_<?php echo $LC; ?>_lensD" style="vertical-align:middle;float:left;" <?php if($sel_order['uv400']==1){echo"checked";} ?> onClick="pos_row_display(<?php echo $LC; ?>, '2_<?php echo $LC; ?>_uv400_display', 'uv400', 'uv400');">
                <label for="uv400_<?php echo $LC; ?>_lensD" style="float:left;">UV400</label>
              </div>
              <div class="col-md-4" style="display:none;">
                <input type="checkbox" name="pgx_<?php echo $LC; ?>" id="pgx_<?php echo $LC; ?>_lensD" style="vertical-align:middle;float:left;"  onClick=""> <?php /*if($sel_order['pgx']==1){echo"checked";}*/ ?>
                <label for="pgx_<?php echo $LC; ?>_lensD" style="float:left;margin-right:5px;">PGX</label>
              </div>
               <div class="col-md-3" title="Thin As Possible">
                <input type="checkbox" name="tap_<?php echo $LC; ?>" id="tap_<?php echo $LC; ?>_lensD" style="vertical-align:middle;float:left;" <?php if($sel_order['tap']==1){echo"checked";} ?> onClick="">
                <label for="tap_<?php echo $LC; ?>_lensD" style="float:left;margin-right:5px;">TAP</label>
              </div>
			  <div class="col-md-8 nopadd">
			  	<label for="lab_id_<?php echo $LC; ?>_lensD">
                 	<?php if($GLOBALS['connect_visionweb']!=""){ ?>
                		<a href="javascript:void(0);" class="text_purpule" onClick="javascript:sel_lab_add(<?php echo $LC; ?>);" style="padding:0;" title="Lens Lab Address">Lab</a>
                	<?php }else{?>
                    Lab
                    <?php }
                    $labOptColor = ( in_array($sel_order['lab_id'], $vw_lab_id_arr) )? 'blueColor' : 'blackColor';
                    ?>
                </label>
			  	<select class="lab_id <?php echo $labOptColor; ?>" name="lab_id_<?php echo $LC; ?>_lensD" id="lab_id_<?php echo $LC; ?>_lensD" style="width:83%;margin-right:0" onChange="sel_lab_add(<?php echo $LC; ?>);">
					<option value="0" class="blackColor">Please Select</option>
					<?php
						foreach($lens_labs as $key=>$val){
							$selected = ($key==$sel_order['lab_id'])?'selected="selected"':'';
							$class = ( in_array($key, $vw_lab_id_arr) )? 'blueColor' : 'blackColor';
							echo '<option value="'.$key.'" class="'.$class.'" '.$selected.'>'.$val."</option>";
						}
					?>
				</select>
                <input type="hidden" name="lab_detail_id_<?php echo $LC; ?>_lensD" id="lab_detail_id_<?php echo $LC; ?>_lensD" value="<?php echo $sel_order['lab_detail_id'] ?>">
				<input type="hidden" name="lab_ship_detail_id_<?php echo $LC; ?>_lensD" id="lab_ship_detail_id_<?php echo $LC; ?>_lensD" value="<?php echo $sel_order['lab_ship_detail_id'] ?>">
			  </div>
            </div>
          </div>
        </div>
        <?php if($GLOBALS['connect_visionweb']==""){ ?>
        <br>
        <?php }?>
        <div class="row extra_margin od_os_span" style="clear:none !important;margin-bottom:5px;" id="rx_div_<?php echo $LC; ?>"> 
          <!----------------------------------- RX Hidden Fields For OD----------------------------------->
          <input type="hidden" name="lens_sphere_od_<?php echo $LC; ?>" id="lens_sphere_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['sphere_od'];?>">
          <input type="hidden" name="lens_cylinder_od_<?php echo $LC; ?>" id="lens_cylinder_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['cyl_od'];?>">
          <input type="hidden" name="lens_axis_od_<?php echo $LC; ?>" id="lens_axis_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['axis_od'];?>">
          <input type="hidden" name="lens_add_od_<?php echo $LC; ?>" id="lens_add_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['add_od'];?>">
          <input type="hidden" name="lens_mr_od_p_<?php echo $LC; ?>" id="lens_mr_od_p_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['mr_od_p'];?>">
          <input type="hidden" name="lens_mr_od_prism_<?php echo $LC; ?>" id="lens_mr_od_prism_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['mr_od_prism'];?>">
          <input type="hidden" name="lens_mr_od_splash_<?php echo $LC; ?>" id="lens_mr_od_splash_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['mr_od_splash'];?>">
          <input type="hidden" name="lens_mr_od_sel_<?php echo $LC; ?>" id="lens_mr_od_sel_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['mr_od_sel'];?>">
		  <input type="hidden" name="lens_axis_od_va_<?php echo $LC; ?>" id="lens_axis_od_va_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['axis_od_va'];?>">
		  <input type="hidden" name="lens_add_od_va_<?php echo $LC; ?>" id="lens_add_od_va_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['add_od_va'];?>">
          
          <!----------------------------------- RX Hidden Fields For Os----------------------------------->
          
          <input type="hidden" name="lens_sphere_os_<?php echo $LC; ?>" id="lens_sphere_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['sphere_os'];?>">
          <input type="hidden" name="lens_cylinder_os_<?php echo $LC; ?>" id="lens_cylinder_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['cyl_os'];?>">
          <input type="hidden" name="lens_axis_os_<?php echo $LC; ?>" id="lens_axis_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['axis_os'];?>">
          <input type="hidden" name="lens_add_os_<?php echo $LC; ?>" id="lens_add_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['add_os'];?>">
          <input type="hidden" name="lens_mr_os_p_<?php echo $LC; ?>" id="lens_mr_os_p_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['mr_os_p'];?>">
          <input type="hidden" name="lens_mr_os_prism_<?php echo $LC; ?>" id="lens_mr_os_prism_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['mr_os_prism'];?>">
          <input type="hidden" name="lens_mr_os_splash_<?php echo $LC; ?>" id="lens_mr_os_splash_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['mr_os_splash'];?>">
          <input type="hidden" name="lens_mr_os_sel_<?php echo $LC; ?>" id="lens_mr_os_sel_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['mr_os_sel'];?>">
		  <input type="hidden" name="lens_axis_os_va_<?php echo $LC; ?>" id="lens_axis_os_va_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['axis_os_va'];?>">
		  <input type="hidden" name="lens_add_os_va_<?php echo $LC; ?>" id="lens_add_os_va_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['add_os_va'];?>">
          
          <!---------------------------------- Physician ID ----------------------------->
          <input type="hidden" name="lens_physician_id_<?php echo $LC; ?>" id="lens_physician_id_lensD_<?php echo $LC; ?>" value="<?php echo $lensRes['physician_id'];?>">
		  <span id="rx_Date_<?php echo $LC; ?>" style="font-weight:normal;float:right;display:none;"><?php echo ($lensRes['rx_dos_1']=="00-00-00" || trim($lensRes['rx_dos_1'])=="")?"":$lensRes['rx_dos_1'];?></span>
		  <!-- Rx Table -->
          
        </div>
        
        <div class="row extra_margin" style="display:none;">
        	<div class="col-md-6" style="padding-right:0;">
            
            	<div class="row">
                  <div class="col-md-5">
                    <input type="hidden" name="lens_outside_rx_<?php echo $LC; ?>" id="lens_outside_rx_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['outside_rx']; ?>" />
                    
                    <input type="hidden" name="lens_rx_dos_<?php echo $LC; ?>" id="lens_rx_dos_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['rx_dos']; ?>" />
                    
                    <!--<label for="lens_outside_rx_<?php echo $LC; ?>_lensD" style="float:left;margin-right:4px;display:none;">Outside</label><a href="javascript:void(0);" class="text_purpule" id="rx_link_<?php echo $LC;?>" onClick="javascript:prescription_details('<?php echo $LC;?>');" style="float:left;margin-top:2px;margin-right:5px;padding-left:0px;" title="Lens Prescription">Rx</a>
                    <a href="javascript:void(0);" style="width:20px;float:left;" id="show_rx" onClick="show_rx(<?php echo $LC; ?>)"><img src="../../images/down_arrow.png" width="12px" title="Show Rx" style="transform:rotate(0deg);" id="arrow_image_<?php echo $LC; ?>"></a>
                    <span id="rxDate_<?php echo $LC; ?>" style="float:left;"><?php echo ($lensRes['rx_dos_1']=="00-00-0000" || trim($lensRes['rx_dos_1'])=="")?"":$lensRes['rx_dos_1'];?></span>-->
                  </div>

			<?php /*<div class="col-md-7" style="padding-left:0px;">
                                <label for="lens_last_exam_<?php echo $LC; ?>_lensD" style="float:left;margin-right:5px;">Last Exam</label>
                                <input type="text" name="lens_last_exam_<?php echo $LC; ?>" id="lens_last_exam_<?php echo $LC; ?>_lensD" style="width:95px;float:left;margin:0;" class="rx_cls" value="<?php if($lensRes['last_exam']!="0000-00-00" && $lensRes['last_exam']!="") { echo getDateFormat($lensRes['last_exam']); }?>">
                             </div>
            */
            ?>
             <input type="hidden" name="lens_last_exam_<?php echo $LC; ?>" id="lens_last_exam_<?php echo $LC; ?>_lensD" class="rx_cls" value="<?php if($lensRes['last_exam']!="00-00-00" && $lensRes['last_exam']!="") { echo getDateFormat($lensRes['last_exam']); }?>">
            <input type="hidden" name="lens_physician_name_<?php echo $LC; ?>" id="lens_physician_name_lensD_<?php echo $LC; ?>" value="<?php echo $phyName;?>" autocomplete="off">
            <input type="hidden" name="lens_telephone_<?php echo $LC; ?>" id="lens_telephone_lensD_<?php echo $LC; ?>" value="<?php echo  stripslashes(core_phone_format($lensRes['telephone']));?>" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');">
                            </div>
                        </div>
            <?php
            /** /
            ?>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12" style="padding-right:0px;">
                    	
                        <label for="lens_physician_name_lensD_<?php echo $LC; ?>" style="float:left;width:90px;">Eye Physician</label>
                <input type="text" name="lens_physician_name_<?php echo $LC; ?>" id="lens_physician_name_lensD_<?php echo $LC; ?>" style="width:66%;margin:0px;" value="<?php echo $phyName;?>" autocomplete="off">
                    </div>
                    <!--div class="col-md-6">
                        <label for="lens_telephone_lensD_<?php echo $LC; ?>" style="float:left;width:50px;">Tel.</label>
                        <input type="hidden" name="lens_telephone_<?php echo $LC; ?>" id="lens_telephone_lensD_<?php echo $LC; ?>" style="width:90px;" value="<?php echo  stripslashes(core_phone_format($lensRes['telephone']));?>" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');">
                    </div-->
                     <input type="hidden" name="lens_telephone_<?php echo $LC; ?>" id="lens_telephone_lensD_<?php echo $LC; ?>" style="width:90px;" value="<?php echo  stripslashes(core_phone_format($lensRes['telephone']));?>" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');">
                </div>
              </div>
*/
?>
        </div>
        <!-- End New Data -->

<!-- Rx div -->
	<div class="row extra_margin">
		<div class="col-md-12">
			<table style="width:100%;border-collapse:collapse;" class="rxTable">
				<tr>
					<th style="text-align:left;text-indent:0;width:32px;">
						<a href="javascript:void(0);" class="text_purpule" onClick="javascript:prescription_details(<?php echo $LC;?>);" style="padding:0;" id="rx_label_<?php echo $LC; ?>" title="Lens Prescription - <?php echo (($lensRes['rx_dos_1']=="00-00-00" || trim($lensRes['rx_dos_1'])=="")?"":$lensRes['rx_dos_1']).(($phyName!='')?', Prescribed By: '.$phyName: '');?>">Rx</a>
						<?php $tempPhyName=$phyName; ?>
					</th>
					<th style="width:146px;">SPH</th>
					<th style="width:110px;">CYL</th>
					<th style="width:110px;">AXIS</th>
					<th style="width:110px;">VA</th>
					<th style="width:110px;">ADD</th>
					<th style="width:110px;">VA</th>
				</tr>
				
				<tr>
					<td><span class="blueColor" style="font-weight:bold;">OD</span></td>
					<td style="text-align:center;">
						<span class="span_data" id="sph_text_od_<?php echo $LC; ?>"><?php echo $lensRes['sphere_od'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="cyl_text_od_<?php echo $LC; ?>"><?php echo $lensRes['cyl_od'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="axis_text_od_<?php echo $LC; ?>"><?php echo $lensRes['axis_od'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="axis_text_od_va_<?php echo $LC; ?>"><?php echo $lensRes['axis_od_va'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="add_text_od_<?php echo $LC; ?>"><?php echo $lensRes['add_od'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="add_text_od_va_<?php echo $LC; ?>"><?php echo $lensRes['add_od_va'];?></span>
					</td>
				</tr>
				
				<tr>
					<td><span class="greenColor" style="font-weight:bold;">OS</span></td>
					<td style="text-align:center;">
						<span class="span_data" id="sph_text_os_<?php echo $LC; ?>"><?php echo $lensRes['sphere_os'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="cyl_text_os_<?php echo $LC; ?>"><?php echo $lensRes['cyl_os'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="axis_text_os_<?php echo $LC; ?>"><?php echo $lensRes['axis_os'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="axis_text_os_va_<?php echo $LC; ?>"><?php echo $lensRes['axis_os_va'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="add_text_os_<?php echo $LC; ?>"><?php echo $lensRes['add_os'];?></span>
					</td>
					<td style="text-align:center;">
						<span class="span_data" id="add_text_os_va_<?php echo $LC; ?>"><?php echo $lensRes['add_os_va'];?></span>
					</td>
				</tr>
				
				<tr>
					<th></th>
					<th>Prism</th>
					<th>DPD</th>
					<th>NPD</th>
					<th title="Optical Center">OC</th>
					<th class="rxBase">Base Curve</th>
					<th class="rxBase">Min Seg Ht</th>
				</tr>
				
				<tr>
					<td><span class="blueColor" style="font-weight:bold;">OD</span></td>
					<td style="text-align:center;">
						<span id="prism_text_1_od_<?php echo $LC; ?>" class="span_data"><?php 
						echo ($lensRes['mr_od_p'] || $lensRes['mr_od_sel'])?$lensRes['mr_od_p']." ".$lensRes['mr_od_sel']:"";
						?></span>
						<span id="prism_text_od_seperator_<?php echo $LC; ?>"><?php 
						echo (
								(
									($lensRes['mr_od_p']!="" || $lensRes['mr_od_sel']!="")
										&&
									($lensRes['mr_od_splash']!="" || $lensRes['']!="mr_od_prism")
								)
								?
									"/"
								:
									""
							);
						?></span>
						<span class="span_data" id="prism_text_2_od_<?php echo $LC; ?>"><?php 
						echo ($lensRes['mr_od_splash'] || $lensRes['mr_od_prism'])?$lensRes['mr_od_splash']." ".$lensRes['mr_od_prism']:"";
						?></span>
					</td>
					<td style="text-align:center;">
						<input type="text" name="lens_dpd_od_<?php echo $LC; ?>" id="lens_dpd_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['dist_pd_od'];?>" style="width:95%;" />
					</td>
					<td style="text-align:center;">
						<input type="text" name="lens_npd_od_<?php echo $LC; ?>" id="lens_npd_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['near_pd_od'];?>" style="width:95%;" />
					</td>
					<td style="text-align:center;">
						<input type="text" name="lens_oc_od_<?php echo $LC; ?>" id="lens_oc_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['oc_od'];?>" style="width:95%;" />
					</td>
					<td style="text-align:center;" class="rxBase">
						<input type="text" name="lens_base_od_<?php echo $LC; ?>" id="lens_base_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['base_od'];?>" style="width:95%;text-align:left;">
					</td>
					<td style="text-align:center;" class="rxBase">
						<input type="text" name="lens_seg_od_<?php echo $LC; ?>" id="lens_seg_od_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['seg_od'];?>" style="width:95%;text-align:left;">
					</td>
				</tr>
				
				<tr>
					<td><span class="greenColor" style="font-weight:bold;">OS</span></td>
					<td style="text-align:center">
						<span class="span_data" id="prism_text_1_os_<?php echo $LC; ?>"><?php 
		echo ($lensRes['mr_os_p'] || $lensRes['mr_os_sel'])?$lensRes['mr_os_p']." ".$lensRes['mr_os_sel']:"";?></span>
						<span id="prism_text_os_seperator_<?php echo $LC; ?>"><?php
						echo (
								(
									($lensRes['mr_os_p'] || $lensRes['mr_os_sel'])
										&&
									($lensRes['mr_os_splash'] || $lensRes['mr_os_prism'])
								)
								?
									"/"
								:
									""
							);
						?></span>
						<span class="span_data" id="prism_text_2_os_<?php echo $LC; ?>"><?php 
		echo ($lensRes['mr_os_splash'] || $lensRes['mr_os_prism'])?$lensRes['mr_os_splash']." ".$lensRes['mr_os_prism']:"";?></span>
					</td>
					<td style="text-align:center;">
						<input type="text" name="lens_dpd_os_<?php echo $LC; ?>" id="lens_dpd_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['dist_pd_os'];?>" style="width:95%;" />
					</td>
					<td style="text-align:center;">
						<input type="text" name="lens_npd_os_<?php echo $LC; ?>" id="lens_npd_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['near_pd_os'];?>" style="width:95%;" />
					</td>
					<td style="text-align:center;">
						<input type="text" name="lens_oc_os_<?php echo $LC; ?>" id="lens_oc_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['oc_os'];?>" style="width:95%;" />
					</td>
					<td style="text-align:center;" class="rxBase">
						<input type="text" name="lens_base_os_<?php echo $LC; ?>" id="lens_base_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['base_os'];?>" style="width:95%;text-align:left;" />
					</td>
					<td style="text-align:center;" class="rxBase">
						<input type="text" name="lens_seg_os_<?php echo $LC; ?>" id="lens_seg_os_<?php echo $LC; ?>_lensD" value="<?php echo $lensRes['seg_os'];?>" style="width:95%;text-align:left;"/>
					</td>
				</tr>
			</table>
		</div>
	</div>
<!-- End Rx div -->

	<div class="row extra_margin">
		<div class="col-md-12">
			<label for="glasses_usage_<?php echo $LC; ?>">Usage</label>
			<select name="glasses_usage_<?php echo $LC; ?>" id="glasses_usage_<?php echo $LC; ?>" style="width:104px;">
			  <option value="0">Please Select</option>
			  <?php
				$sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='1' AND `module_id`='0' AND `opt_sub_type` IN(0,2) AND `del_status`='0' ORDER BY `opt_val` ASC";
				$resp = imw_query($sql);
				if($resp && imw_num_rows($resp)>0){
					while($row = imw_fetch_assoc($resp)){
						$selected = "";
						if($sel_order['contact_usage']==$row['id']){$selected='selected="selected"';}
						echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['opt_val'].'</option>';	
					}
				}
			?>
			</select>
			
			<label for="glasses_type_<?php echo $LC; ?>" style="margin-left:5px;">Type</label>
            <select name="glasses_type_<?php echo $LC; ?>" id="glasses_type_<?php echo $LC; ?>" style="width:104px;">
              <option value="0">Please Select</option>
              <?php
				$sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' AND `module_id`='0' AND `opt_sub_type` IN(0,2) AND `del_status`='0' ORDER BY `opt_val` ASC";
				$resp = imw_query($sql);
				if($resp && imw_num_rows($resp)>0){
					while($row = imw_fetch_assoc($resp)){
						$selected = "";
						if($sel_order['contact_type']==$row['id']){$selected='selected="selected"';}
						echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['opt_val'].'</option>';	
					}
				}
			?>
            </select>
			
			<label for="dx_code_<?php echo $LC; ?>_lensD">DX Code</label>
            <?php $all_dx_codes="";
            if($sel_order['dx_code']!=""){
                $dx_singl=array();
                $get_dxs = explode(",",$sel_order['dx_code']);
                /*for($fd=0;$fd<count($get_dxs);$fd++){
                    $dx_singl[] = $dx_code_arr[$get_dxs[$fd]];
                }
                $all_dx_codes = join('; ',$dx_singl);*/
				$all_dx_codes = join('; ',$get_dxs);
            }
            ?>
            <input type="text" name="dx_code_<?php echo $LC; ?>_lensD" id="dx_code_<?php echo $LC; ?>_lensD" style="width:104px;margin:0;float:none;" class="rx_cls" value="<?php echo $all_dx_codes; ?>" autocomplete="off">
			<!--onChange="get_dxcode(this);"-->
		
			<input type="checkbox" name="lens_neutralize_rx_<?php echo $LC; ?>" id="lens_neutralize_rx_<?php echo $LC; ?>_lensD" value="1" <?php if($lensRes['neutralize_rx']=="1"){echo"checked";} ?> style="float:none;"/>
            <label for="lens_neutralize_rx_<?php echo $LC; ?>_lensD" style="float:none;">Neutralize</label>
			<label for="qty_<?php echo $LC; ?>_lensD" style="float:none;margin:0 12px 0 26px;">Qty</label>
			<input type="text" style="width:50px;margin:0;float:none;" name="qty_<?php echo $LC; ?>_lensD" id="qty_<?php echo $LC; ?>_lensD" value="<?php if(isset($sel_order['qty'])){ echo $sel_order['qty']; } else { echo "1"; } ?>" onKeyUp="validate_qty(this);" onChange="changeQtyLens(this.value, <?php echo $LC; ?>)">
		</div>
	</div>
	
    <?php if($GLOBALS['connect_visionweb']!=""){ ?>
		<div class="row extra_margin">
			<div class="col-md-12" style="text-align:right;">
				<span class="visionShowLink" elemKey="<?php echo $LC; ?>" style="font-weight:bold;color:#9900CC;cursor:pointer;margin-right:14px;">Show more details</span>
			</div>
		</div>
        <div id="visionweb_fields_<?php echo $LC; ?>" style="display:none;">
            <div class="row extra_margin">
                <div class="col-md-4 nopadd-right">
                    <label for="he_coeff_<?php echo $LC; ?>_lensD" style="float:left;width:121px;">Head/Eye Coeff</label>
                    <select name="he_coeff_<?php echo $LC; ?>_lensD" id="he_coeff_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=0;$vw<100;$vw++){
                            if($vw<10){
                                $vw_v="0.0".$vw;
                            }else{
                                $vw_v="0.".$vw;
                            }
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['he_coeff']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['he_coeff']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
                <div class="col-md-4 nopadd-right">
                    <label for="st_coeff_<?php echo $LC; ?>_lensD" style="float:left;width:121px;">Stability Coeff</label>
                    <select name="st_coeff_<?php echo $LC; ?>_lensD" id="st_coeff_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=0;$vw<100;$vw++){
                            if($vw<10){
                                $vw_v="0.0".$vw;
                            }else{
                                $vw_v="0.".$vw;
                            }
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['st_coeff']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['st_coeff']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
                <div class="col-md-4 nopadd-right">
                    <label for="nhp_cape_<?php echo $LC; ?>_lensD" style="float:left;width:104px;">CAPE</label>
                    <select name="nhp_cape_<?php echo $LC; ?>_lensD" id="nhp_cape_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=-10;$vw<=10;$vw++){
                            $vw_v=$vw.".00";
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['nhp_cape']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['nhp_cape']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
            </div>
			
            <div class="row extra_margin">
                <div class="col-md-4 nopadd-right">
                    <label for="progression_Len_<?php echo $LC; ?>_lensD" style="float:left;width:121px;">Progression Len.</label>
                    <select name="progression_Len_<?php echo $LC; ?>_lensD" id="progression_Len_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=5;$vw<=20;$vw++){
                            $vw_v=$vw.".00";
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['progression_Len']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['progression_Len']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
				<div class="col-md-4 nopadd-right">
                    <label for="wrap_angle_<?php echo $LC; ?>_lensD" style="float:left;width:121px;">Wrap Angle</label>
                    <select name="wrap_angle_<?php echo $LC; ?>_lensD" id="wrap_angle_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=0;$vw<=30;$vw++){
                            $vw_v=$vw.".00";
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['wrap_angle']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['wrap_angle']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
                <div class="col-md-4 nopadd-right">
                    <label for="panto_angle_<?php echo $LC; ?>_lensD" style="float:left;width:104px;">Panto Angle</label>
                    <select name="panto_angle_<?php echo $LC; ?>_lensD" id="panto_angle_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=-10;$vw<=35;$vw++){
                            $vw_v=$vw.".00";
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['panto_angle']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['panto_angle']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
            </div>
			
            <div class="row extra_margin">
				<div class="col-md-4 nopadd-right">
                    <label for="rv_distance_<?php echo $LC; ?>_lensD" style="float:left;width:121px;">R. Vertex Distance</label>
                    <select name="rv_distance_<?php echo $LC; ?>_lensD" id="rv_distance_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=5;$vw<=30;$vw++){
                            $vw_v=$vw.".00";
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['rv_distance']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['rv_distance']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
                <div class="col-md-4 nopadd-right">
                    <label for="lv_distance_<?php echo $LC; ?>_lensD" style="float:left;width:121px;">L. Vertex Distance</label>
                    <select name="lv_distance_<?php echo $LC; ?>_lensD" id="lv_distance_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=5;$vw<=30;$vw++){
                            $vw_v=$vw.".00";
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['lv_distance']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['lv_distance']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
                <div class="col-md-4 nopadd-right">
                    <label for="re_rotation_<?php echo $LC; ?>_lensD" style="float:left;width:104px;">R. Eye Rotation</label>
                    <select name="re_rotation_<?php echo $LC; ?>_lensD" id="re_rotation_<?php echo $LC; ?>_lensD" style="width:94px;">
                        <option value=""></option>
                        <?php for($vw=16;$vw<=38;$vw++){
                            $vw_v=$vw.".00";
                        ?><option value="<?php echo $vw_v ?>" <?php if($sel_order['re_rotation']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['re_rotation']=="" && !$showVision[$LC])?false:true;
						?>
                    </select>
                </div>
            </div>
			
			<div class="row extra_margin">
				<div class="col-md-4 nopadd-right">
					<label for="le_rotation_<?php echo $LC; ?>_lensD" style="float:left;width:121px;">L. Eye Rotation</label>
					<select name="le_rotation_<?php echo $LC; ?>_lensD" id="le_rotation_<?php echo $LC; ?>_lensD" style="width:94px;">
							<option value=""></option>
							<?php for($vw=16;$vw<=38;$vw++){
								$vw_v=$vw.".00";
							?><option value="<?php echo $vw_v ?>" <?php if($sel_order['le_rotation']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
								$showVision[$LC] = ($sel_order['le_rotation']=="" && !$showVision[$LC])?false:true;
							?>
					</select>
				</div>
				<div class="col-md-4 nopadd-right">
					<label for="reading_distance_<?php echo $LC; ?>_lensD" style="float:left;width:121px;">Reading Distance</label>
					<select name="reading_distance_<?php echo $LC; ?>_lensD" id="reading_distance_<?php echo $LC; ?>_lensD" style="width:94px;">
						<option value=""></option>
						<?php for($vw=20;$vw<=80;$vw++){
							$vw_v=$vw.".00";
						?><option value="<?php echo $vw_v ?>" <?php if($sel_order['reading_distance']==$vw_v){echo"selected";} ?>><?php echo $vw_v ?></option><?php }
							$showVision[$LC] = ($sel_order['reading_distance']=="" && !$showVision[$LC])?false:true;
						?>
					</select>
				</div>
				<div class="col-md-4" style="text-align:right;">
					<span class="visionHideLink" elemKey="<?php echo $LC; ?>" style="font-weight:bold;color:#9900CC;cursor:pointer;margin-right:14px;">Hide details</span>
				</div>
			</div>
        </div>
        <?php } ?>
		
      </div>
    </div>
</div>
<?php }while($sel_order=imw_fetch_array($sel_qry));?>
<input type="hidden" name="last_cont" id="last_cont" value="<?php echo $LC; ?>" />
<input type="hidden" name="last_cont_lensD" id="last_cont_lensD" value="<?php echo $LC; ?>" />
<?php include("pt_pos.php"); ?>
<input type="hidden" name="pos_last_cont" id="pos_last_cont" value="<?php echo $LC; ?>" />
</div>
</div>
	</form>
<?php /* Lens Ends */
/*Autofill Latest Rx*/
$qry_proc=imw_query("select cpt_fee_id from cpt_fee_tbl where cpt_prac_code='92015' or cpt4_code='92015'");
while($row_proc=imw_fetch_array($qry_proc)){
	$enc_proc_arr[$row_proc['cpt_fee_id']]=$row_proc['cpt_fee_id'];
}
$enc_proc_imp=implode(',',$enc_proc_arr);

$limitRx = 1;
$noIncorrect = true;
include('lens_rx_list.php');
$rxBaseDet = $rxDetailsOD = $rxDetailsOS = $rxDosRaw = $rxDos = "";
$arrLensRX = array_shift($arrLensRX);	/*Get Values for Last Dos*/
if(count($arrLensRX)>0){
	reset($arrLensRX);
	$i = key($arrLensRX);
	
	$phyName = "";
	if($arrLensRX[$i]['OD']['physician_id']!='0' && $arrLensRX[$i]['OD']['physician_id']!=''){
		$rs=imw_query("Select id,fname,lname FROM users WHERE id='".$arrLensRX[$i]['OD']['physician_id']."'");
		if($rs){
			$res = imw_fetch_assoc($rs);
			if($res['lname']!='' || $res['fname']!=''){
				$phyName=$res['lname'].', '.$res['fname'];
			 }
		}
	}
	else{
		$phyName = (isset($arrLensRX[$i]['OD']['physician_name'])) ? $arrLensRX[$i]['OD']['physician_name'] : '';
	}
	
	$patPhone='';
	$patRs=imw_query("Select phone FROM facility WHERE facility_type='1'");
	$patRes=imw_fetch_array($patRs);
	if($patRes['phone']!=''){
		$patPhone=$patRes['phone'];
	}
	$vid = $arrLensRX[$i]['OD']['VIS_ID'];
	$rxBaseDet=$arrLensRX[$i]['OD']['physician_id'].'~'.$phyName.'~'.$patPhone.'~'.$lastExamDate;
	$rxDetailsOD=$arrLensRX[$i]['OD']['Sphere'].'~'.$arrLensRX[$i]['OD']['Cylinder'].'~'.$arrLensRX[$i]['OD']['Axis'].'~'.$arrLensPrism[$i]['OD']['mr_od_p'].'~'.$arrLensPrism[$i]['OD']['mr_od_prism'].'~'.$arrLensPrism[$i]['OD']['mr_od_splash'].'~'.$arrLensPrism[$i]['OD']['mr_od_sel'].'~'.$arrLensVision[$vid]['OD']['NPD'].'~'.$arrLensVision[$vid]['OD']['DPD'].'~'.$arrLensRX[$i]['OD']['Add'].'~'.$arrLensRX[$i]['OD']['Base'].'~'.$arrLensRX[$i]['OD']['Axis_VA'].'~'.$arrLensRX[$i]['OD']['Add_VA'].'~'.$arrLensVision[$vid]['OD']['MINSEG'].'~'.$arrLensVision[$vid]['OD']['OC'];

	$rxDetailsOS=$arrLensRX[$i]['OS']['Sphere'].'~'.$arrLensRX[$i]['OS']['Cylinder'].'~'.$arrLensRX[$i]['OS']['Axis'].'~'.$arrLensPrism[$i]['OS']['mr_os_p'].'~'.$arrLensPrism[$i]['OS']['mr_os_prism'].'~'.$arrLensPrism[$i]['OS']['mr_os_splash'].'~'.$arrLensPrism[$i]['OS']['mr_os_sel'].'~'.$arrLensVision[$vid]['OS']['NPD'].'~'.$arrLensVision[$vid]['OS']['DPD'].'~'.$arrLensRX[$i]['OS']['Add'].'~'.$arrLensRX[$i]['OS']['Base'].'~'.$arrLensRX[$i]['OS']['Axis_VA'].'~'.$arrLensRX[$i]['OS']['Add_VA'].'~'.$arrLensVision[$vid]['OS']['MINSEG'].'~'.$arrLensVision[$vid]['OS']['OC'];
}
$db_dos=explode('-',$arrLensRX[$i]['OD']['DOS']);
$db_dos_exp=$db_dos['2'].'-'.$db_dos['0'].'-'.$db_dos['1'];
$rxDosRaw = $db_dos_exp;
$rxDos = $arrLensRX[$i]['OD']['DOS1'];

$get_dx_qry=imw_query("select patient_charge_list_details.diagnosis_id1,  patient_charge_list_details.diagnosis_id2, patient_charge_list_details.diagnosis_id3, patient_charge_list_details.diagnosis_id4 from patient_charge_list join patient_charge_list_details 
on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id 
where patient_charge_list.patient_id='".$_SESSION['patient_session_id']."' and patient_charge_list_details.procCode in($enc_proc_imp) and patient_charge_list.date_of_service='$db_dos_exp'");
$lens_rxDetailsDX="";
if(imw_num_rows($get_dx_qry)>0){	
	$get_dx_row=imw_fetch_array($get_dx_qry);
	if($get_dx_row['diagnosis_id1']!=""){
		$lens_rxDetailsDX=$get_dx_row['diagnosis_id1'];
	}else if($get_dx_row['diagnosis_id2']!=""){
		$lens_rxDetailsDX=$get_dx_row['diagnosis_id2'];
	}else if($get_dx_row['diagnosis_id3']!=""){
		$lens_rxDetailsDX=$get_dx_row['diagnosis_id3'];
	}else if($get_dx_row['diagnosis_id4']!=""){
		$lens_rxDetailsDX=$get_dx_row['diagnosis_id4'];
	}
}
/*Autofill Latest Rx*/
?>
<link rel="stylesheet" type="text/css" href="../../library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="../../library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect_edited.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
var remakeFlag = <?php echo ($remake)?'true':'false'; ?>;
var hideloaderFlag = true;
var stockFramesItems = [];
var customFrame = <?php echo json_encode($GLOBALS['CUSTOM_FRAME']); ?>

/*Fill Color Code*/
function color_code(key){
	var code = $('#color_id_'+key+' option:selected').attr('color_code');
	$('#color_code_'+key).val(code);
}

/*Other Option for Frame Style or Shape*/
function other_option(obj){
	var sel = $(obj).val();
	if(sel=="other"){
		$(obj).hide();
		$(obj).siblings('.other_val, .icon_back').show();
	}
	else{
		$(obj).siblings('.other_val, .icon_back').hide();
		$(obj).show();
	}
}
function back_other(obj){
	$(obj).hide();
	$(obj).siblings('.other_val').val('').hide();
	$(obj).siblings('.sel_dd').val('0').show().trigger('change');
}

function reduce_qty_chk(act, alert_flag, action){
	
	var confirm_msg = "Do you want to reduce quantity from Inventory for:<br /><br />";
	var stop_alert="Unable to reduce qty for:<br /><br />";
	var use_on_hand = $('input[id^="use_on_hand_chk_"]');
	var valid_order=true;
	var confirmFlag = false;
	 
	$.each(use_on_hand, function(index, obj){
		id_index = $.trim($(obj).attr('id'));
		id_index = id_index.substr(id_index.lastIndexOf('_')+1);
		/*var fac_qty=($("#fqoh_"+id_index).text());
		var ordered_qty=($("#qty_"+id_index).val());*/
		
		qty_reduced = parseInt($("input#qty_reduced_"+id_index).val());
		
		item_id = $("input#item_id_"+id_index).val();
		
		flag = (item_id==="")?false:true;
		upc_code_frame = $("#upc_name_"+id_index).val();
		item_name_frame = $("#item_name_"+id_index).val();
		if($("#item_name_"+id_index+"_other").val())
		{
			item_name_frame = $("#item_name_"+id_index+"_other").val();
		}
		
		if($(obj).is(":checked")===true && qty_reduced!==1 && flag && !alert_flag){
			
			confirmFlag = true;
			upc_code_lens = item_name_lens = "";
			if($("#upc_id_"+id_index+"_lensD").val()!=""){
				upc_code_lens = $("#upc_name_"+id_index+"_lensD").val();
				item_name_lens = $("#item_name_"+id_index+"_lensD").val();
			}
			confirm_msg += '<label style="margin-bottom:5px;display:block;">';
			confirm_msg += '<input style="vertical-align:middle;margin:0;" type="checkbox" id="qty_reduce_confirm_'+id_index+'" value="'+id_index+'" />&nbsp;';
			confirm_msg += '<strong>'+upc_code_frame+'</strong> - '+item_name_frame;
			if(upc_code_lens!="" || item_name_lens!="")
			confirm_msg += '&nbsp;&nbsp;& <strong>'+upc_code_lens+'</strong> - '+item_name_lens;
			confirm_msg += '</label>';
			
		}
	});
	
	if(typeof(act)!=="undefined"){
		
		if(act===true){
			
			var modal_window = top.$('#modal-window');
			var item_sel_check = $(modal_window).find('input[id^="qty_reduce_confirm_"]');
			
			$.each(item_sel_check, function(index, obj){
				var index = $(obj).val();
				
				if($(obj).is(":checked")){
					$("#reduce_qty_"+index).val(true);
					var fac_qty=($("#fqoh_"+index).text());
					var ordered_qty=($("#qty_"+index).val());
					var upc_code_frame = $("#upc_name_"+index).val();
					var item_name_frame = $("#item_name_"+index).val();
					if(typeof(ADJACENT_QTY_DEDUCTION)!=="undefined" && ADJACENT_QTY_DEDUCTION==false){
						if(fac_qty<ordered_qty)
						{
							valid_order=false;
							stop_alert+='<strong>'+upc_code_frame+'</strong> - '+item_name_frame+' due to insufficent qty at this facility';
							top.falert(stop_alert);
							return false;
						}
					}
				}
				else
					$("#reduce_qty_"+index).val(false);
			});
		}
		frm_sub_fun(action, true);
		return; 
	}
	
	
	if(confirmFlag){
		top.fconfirm(confirm_msg, reduce_qty_chk, true, action);
		return false;
	}
	else
		return true;
}

function frm_sub_post_saved_callBack(result)
{
	if( result === undefined || result === false )
		return false;
	else
	{
		$("#frm_method").val('order_post_unchanged');
		document.addframe.submit();	
	}
}

function frm_sub_fun(action, confirm_qty_reduce){
	
/*Block Action if Order is locked for editing*/
<?php
	if(!$order_edit_btn_status){
		echo "if(action != 'reorder' && action != 'remake' && action != 'order_post'){";
		echo "frm_auto_sub_fun_callBack(false);";
		echo "return false;";
		echo "}";
echo <<<END
			else if(action==='order_post')
			{
				var alertMsg = 'Order is locked for modificatoins. So, any modification done will not be saved.<br /><br />';
				if($('#order_enc_id_chk').val()>0)
					alertMsg += 'Some Items already posted will not be updated. Only new items will go for billing.<br /><br />';
				
				alertMsg += 'Are you sure want to post the charges?';
				
				top.fconfirm(alertMsg,frm_sub_post_saved_callBack);
				return false;
			}
END;
	}
?>
/*End block action if Order is locked for editing*/
	
	var last_count_frame="";
	var last_count_lens="";
	var check="";
	last_count_frame=$('#last_cont').val();
	last_count_lens=$('#last_cont_lensD').val();
	for(i=1;i<=last_count_frame;i++)
	{
		if($('#upc_name_'+i).val()=="" && $('#upc_name_'+i+'_lensD').val()=="")
		{
			check=1;
		}
		else{
			check=0;
			break;
		}
	}
	var chk_sub=1;
	if(action=="cancel"){
		parent.parent.document.getElementById('main_iframe').src='<?php echo $GLOBALS['WEB_PATH'];?>/interface/patient_interface/index.php';
		return;
		/*var conf = confirm('Are you sure to cancel this Order ?');
		if(conf!=true){
			return false;
		}*/
	}
	else
	{
		var itemsAdded = $("#last_cont_lensD").val();
		var msg = "";
		var j = 1;
		for(var i = 1; i<=itemsAdded; i++){
			if($("#upc_name_"+i+"_lensD").val()!="" || $("#item_name_"+i+"_lensD").val()!=""){
				if($("#upc_name_"+i).val()=="" && $("#item_name_"+i).val()==""){
					msg += j+".   "+$("#upc_name_"+i+"_lensD").val()+" : "+$("#item_name_"+i+"_lensD").val()+"\n";
					j++;
				}
			}
	}
	if(msg!=""){
		msg = "Please Select Frame for the following Lens(es):\n"+msg;
		top.falert(msg, true);
		return false;
	}
	}
	
	if(action=="save" || action=="next" || action=="reorder" || action=='order_post'){
		var fac_qnt=$('#fqoh').text();
		var qnt=$('#dispQtyF').val();
		
		if( action=='order_post' ){
			var dis=0;
			
			var discCodemsg = $('<ol>').addClass('discCode_alert');
			
			$('.posTable>tbody tr[id]').each(function(index, element){
				
				dis = $(element).find('.price_disc').val();
				
				if(dis.slice(-1)=='%'){
					dis = dis.replace('%','');
				}
				if(dis[0]=="$"){
					dis = dis.replace(/^[$]+/,"");			
				}
				if(dis>0){
					var dis_code = $(element).find(".disc_code").val();
					if(dis_code=="" || dis_code=="0"){
						
						optLi = $('<li>');
						
						if( $(element).find('span.vis_type').length == 1){
							$(element).find('span.vis_type').clone().appendTo(optLi);
						}
						optText = $(element).find('.itemname').val()+' '+$(element).find('.itemnameDisp').val();
						$(optLi).append(optText);
						$(optLi).appendTo(discCodemsg);
						
						chk_sub=0;
					}
				}
			});
			
			/*Check Overall Discount Code*/
			if(chk_sub==1){
				var oadVal = $('#overall_discount').val();
				
				if( oadVal.slice(-1) == '%' ){
					oadVal = oadVal.slice(0, oadVal.length - 1);
					oadVal = parseFloat(oadVal);
				}
				
				if(oadVal!=0 && $('#overall_discount_code').val() == 0){
					optLi = $('<li>').text('Overall Discount');
					$(optLi).appendTo(discCodemsg);
					chk_sub=0;
				}
			}
		}
	}
	
	if(chk_sub==0){
		top.falert('Please Select Discount Code for the following:<br />'+$(discCodemsg)[0].outerHTML);
		return false;
	}
	
    //Starts here -( IM-3109 ) Reminder for to enter fields
    var mand_msg1="";
	for(i=1;i<=last_count_frame;i++)
	{
        var mand_msg="";
        
		if($.trim($('#upc_name_'+i).val())=="" && $.trim($('#upc_name_'+i+'_lensD').val())=="")
		{
            mand_msg+="Please enter UPC Code. <br/>";
		}
        
        var frame_name="";
        //required values for frames
        if( $.trim($('#upc_name_'+i).val())!="" ) {
            frame_name=$.trim($("#upc_name_"+i).val());
            if($.trim($('#a_'+i).val())=="")
            {
                mand_msg+="Please enter value for A. <br/>";
            }
            if($.trim($('#b_'+i).val())=="")
            {
                mand_msg+="Please enter value for B. <br/>";
            }
            if($.trim($('#dbl_'+i).val())=="" && $.trim($('#bridge_'+i).val())=="")
            {
                mand_msg+="Please enter value for DBL OR Bridge. <br/>";
            }
		}
        
        //Required values for leses
        if( ($.trim($('#upc_name_'+i).val())!="" && $.trim($('#upc_name_'+i+'_lensD').val())!="") ) {
            frame_name=((frame_name)? frame_name+' and ': frame_name) +' '+$.trim($("#upc_name_"+i+"_lensD").val())+" : "+$.trim($("#item_name_"+i+"_lensD").val())+"\n";
            //RX value (Sphere, Cylinder and Axis) is required
            if($.trim($('#sph_text_od_'+i).text())=="" || $.trim($('#cyl_text_od_'+i).text())=="" || $.trim($('#axis_text_od_'+i).text())=="")
            {
                mand_msg+="Please enter Rx value for OD (Sphere, Cylinder and Axis). <br/>";
            }
            if($.trim($('#sph_text_os_'+i).text())=="" || $.trim($('#cyl_text_os_'+i).text())=="" || $.trim($('#axis_text_os_'+i).text())=="")
            {
                mand_msg+="Please enter Rx value for OS (Sphere, Cylinder and Axis). <br/>";
            }
            //DPD and NPD for lenses is required
            if($.trim($('#lens_dpd_od_'+i+'_lensD').val())=="" && $.trim($('#lens_npd_od_'+i+'_lensD').val())=="")
            {
                mand_msg+="Please enter value for OD (DPD OR NPD). <br/>";
            }
            if($.trim($('#lens_dpd_os_'+i+'_lensD').val())=="" && $.trim($('#lens_npd_os_'+i+'_lensD').val())=="")
            {
                mand_msg+="Please enter value for OS (DPD OR NPD). <br/>";
            }
            
        }
        var brek=''; if(i>1)brek='<br/>';
        if(mand_msg!="") {
            mand_msg1+=brek+"<b>Please enter the following for Frame and Lens(es): "+frame_name+"</b> <br/>"+mand_msg;
        }
	}
    
    if(mand_msg1!="")
    {
        top.falert(mand_msg1, true);
        return false;
    }
    //Ends here -( IM-3109 ) Reminder for to enter fields

	$("#frm_method").val(action);
	if(frm_sub_fun_lensD(action)){
		if(action=="dispensed_post")
		{
			
			if(check==1)
			{
				top.falert("Please enter UPC Code");
			}
			else
			{
				$("#reduc_stock").val('yes');
				$("#frm_method").val(action);
				document.addframe.submit();
			}
		}else if(action=="reorder")
		{
			top.fconfirm('Reorder all the items in this order  <br> Please confirm',frm_sub_fun_callBack);
		}else if(action=="auto_save")
		{
			if(dataChanged && order_del_status==0){
				top.fconfirm('There is some change in order, Do you want to save?', frm_auto_sub_fun_callBack);
			}else{
				top.page_link(top.link_selected, false);
			}
			return false;
		}
		else if(action=="remake")
		{
			//top.fconfirm('Reorder all the items in this order  <br> Please confirm',frm_sub_fun_callBack);
			//open window for further working
			var winTop=window.screen.availHeight;
			winTop = (winTop/2)-190;
			var winWidth = window.screen.availWidth;
			winWidth = (winWidth/2)-390;
			order_id='<?php echo $_REQUEST['order_id'];?>';
			$("#frm_method").val('remake');
			top.WindowDialog.closeAll();
			var vv=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/remake_order.php?order_id='+order_id,'product_history_pop','width=752,height=325,left='+winWidth+',scrollbars=no,top='+winTop);
			vv.focus();
			
		}else if(action=="VisionWeb")
		{
			var alrt_msg="";
			var lab_alrt_msg="";
			
			var blocks = $(".order_frm_detail_id_cls");
			
			$.each(blocks, function(i, obj) {
				var chk_fram_id=$( obj ).attr('id');
				var chk_lens_id=chk_fram_id+'_lensD';
				var chk_id=chk_fram_id.replace("order_detail_id_","");
				var item_name=$('#item_name_'+chk_id+'_lensD').val(); 
				if($('#lab_id_'+chk_id+'_lensD').val()<=0){
					lab_alrt_msg += 'Please select lab for item "'+item_name+'"<br>';
				}
				var chk_cyl_od=parseFloat($('#lens_cylinder_od_'+chk_id+'_lensD').val());
				var chk_cyl_os=parseFloat($('#lens_cylinder_os_'+chk_id+'_lensD').val());
				
				var chk_axis_od=parseFloat($('#lens_axis_od_'+chk_id+'_lensD').val());
				var chk_axis_os=parseFloat($('#lens_axis_os_'+chk_id+'_lensD').val());
				
				var chk_add_od=parseFloat($('#lens_add_od_'+chk_id+'_lensD').val());
				var chk_add_os=parseFloat($('#lens_add_os_'+chk_id+'_lensD').val());
				
				var chk_seg_od=parseFloat($('#lens_seg_od_'+chk_id+'_lensD').val());
				var chk_seg_os=parseFloat($('#lens_seg_os_'+chk_id+'_lensD').val());
				var type_id_val=$('#seg_type_id_'+chk_id+'_od_lensD').val();
				var default_seg_od_key = $('#seg_type_id_'+chk_id+'_od_lensD > option[value="'+type_id_val+'"]').attr('default_val');
				var os_type_id_val=$('#seg_type_id_'+chk_id+'_os_lensD').val();
				var default_seg_os_key = $('#seg_type_id_'+chk_id+'_os_lensD > option[value="'+os_type_id_val+'"]').attr('default_val');
				var chk_visionVal = $('#lens_vision_'+chk_id+'_lensD').val();
					chk_visionVal = chk_visionVal.toLowerCase();
	
				if(chk_visionVal=='od' || chk_visionVal=='ou'){
					if((chk_cyl_od!=0 && !isNaN(chk_cyl_od)) && (chk_axis_od==0 || isNaN(chk_axis_od))){
						lab_alrt_msg += 'Please select OD AXIS for item "'+item_name+'"<br>';
					}
					if(default_seg_od_key!="type_sv"){
						if(chk_add_od==0 || isNaN(chk_add_od)){
							lab_alrt_msg += 'Please select OD ADD for item "'+item_name+'"<br>';
						}
						if((chk_add_od!=0 || !isNaN(chk_add_od)) && (chk_seg_od==0 || isNaN(chk_seg_od))){
							lab_alrt_msg += 'Please select OD Seg Height for item "'+item_name+'"<br>';
						}
					}
				}
				
				if(chk_visionVal=='os' || chk_visionVal=='ou'){
					if((chk_cyl_os!=0 && !isNaN(chk_cyl_os)) && (chk_axis_os==0 || isNaN(chk_axis_os))){
						lab_alrt_msg += 'Please select OS AXIS for item "'+item_name+'"<br>';
					}
					if(default_seg_os_key!="type_sv"){
						if(chk_add_os==0 || isNaN(chk_add_os)){
							lab_alrt_msg += 'Please select OS ADD for item "'+item_name+'"<br>';
						}	
						if((chk_add_os!=0 || !isNaN(chk_add_os)) && (chk_seg_os==0 || isNaN(chk_seg_os))){
							lab_alrt_msg += 'Please select OS Seg Height for item "'+item_name+'"<br>';
						}
					}
				}
			})
			if(lab_alrt_msg!=""){
				top.falert(lab_alrt_msg);
				return false;
			}
			
			$.each(blocks, function(i, obj) {
				var chk_fram_id=$( obj ).attr('id');
				var chk_lens_id=chk_fram_id+'_lensD';
				var chk_id=chk_fram_id.replace("order_detail_id_","");
				var item_name=$('#item_name_'+chk_id+'_lensD').val(); 
				if($('#lab_detail_id_'+chk_id+'_lensD').val()<=0){
					lab_alrt_msg += 'Please select lab billing address for item "'+item_name+'" and lab "'+$('#lab_id_'+chk_id+'_lensD option:selected').text()+'"<br>';
				}
				if($('#lab_ship_detail_id_'+chk_id+'_lensD').val()<=0){
					lab_alrt_msg += 'Please select lab shipping address for item "'+item_name+'" and lab "'+$('#lab_id_'+chk_id+'_lensD option:selected').text()+'"<br>';
				}
			})
			if(lab_alrt_msg!=""){
				//'<br>After making the changes, please <b>Save</b> the order and then <b>Send to VisionWeb</>.'
				top.falert(lab_alrt_msg);
				return false;
			}
			/*Use on Hand Check*/
			confirm_qty_reduce = (typeof(confirm_qty_reduce)==="undefined")?false:true;
			if(!confirm_qty_reduce && !reduce_qty_chk(undefined, false, action))
				return false;
			/*End Use on Hand Check*/
			document.addframe.submit();
			
		}else if(action=="VisionWebSend"){
			var alrt_msg="";
			var lab_alrt_msg="";
			var blocks = $(".order_frm_detail_id_cls");
			
			$.each(blocks, function(i, obj) {
				//alert($( this ).attr('id'));
				var chk_fram_id=$( obj ).attr('id');
				var chk_lens_id=chk_fram_id+'_lensD';
				var chk_id=chk_fram_id.replace("order_detail_id_","");
				var order_detail_ids=$('#'+chk_fram_id).val()+','+$('#'+chk_lens_id).val();
				var item_name=$('#item_name_'+chk_id+'_lensD').val(); 
				var frm_item_name=$('#item_name_'+chk_id).val(); 
				var vw_loc_id=$('#vw_loc_id').val(); 
				
				var order_id='<?php echo $order_id;?>';
				var patient_id='<?php echo $patient_id;?>';
				var dataString = 'method=UploadFile&patient_id='+patient_id+'&order_id='+order_id+'&order_detail_ids='+order_detail_ids+'&vw_loc_id='+vw_loc_id;
				//alert(dataString);
				$.ajax({
					type: "POST",
					url: "../admin/other/vw_refresh_data.php",
					data: dataString,
					cache: false,
					
					success: function(response)
					{	
						//alert(response);
						if(response!=""){
							
							//$(".success_msg").text(response);
							//console.log(response);
							//response=response.replace('.','\n');
							//top.falert(response);
							alrt_msg += '<b>'+chk_id+'. Frame: '+frm_item_name+' and Lens: '+item_name+'</b>';
							alrt_msg += '<p style="margin-left:17px;">'+response+'</p>';
						
						}
					},
					complete: function(){
						if(blocks.length > i+1){
							hideloaderFlag = false;
						}
						else{
							hideloaderFlag = true;
							if(alrt_msg!=""){
								top.falert(alrt_msg);
							}
						}
					}
				});
			})	
		}
		else{
			if(check==1)
			{
				top.falert("Please enter UPC Code");
			}
			else
			{
				/*Use on Hand Check*/
				confirm_qty_reduce = (typeof(confirm_qty_reduce)==="undefined")?false:true;
				if(!confirm_qty_reduce && !reduce_qty_chk(undefined, false, action))
					return false;
				/*End Use on Hand Check*/
				if(action=="order_post"){
					var prac_alrt_msg="";
					var posRow = $("table.posTable tr:not(.hideRow1, .hideRow)");
					$.each(posRow, function(i, obj){
						var pracField = $(obj).find(".pracodefield").val();
						var desc = "";
						var itemName = $(obj).find(".itemname").val();
						if(typeof($(obj).find(".itemnameDisp").val())!="undefined"){
							desc=" - "+$(obj).find(".itemnameDisp").val();
						}
						if(typeof(itemName)!="undefined" && (typeof(pracField)=="undefined" || pracField=="")){
							prac_alrt_msg += 'Please select Prac Code for item "'+itemName+desc+'"<br>';
						}
					});
					
					if(prac_alrt_msg!=""){
						top.falert(prac_alrt_msg);
						return false;
					}
				}
				if($('#order_enc_id_chk').val()>0 && action=="order_post"){
					top.falert("Some Items already posted will not be updated. Only new items will go for billing.");
				}
				document.addframe.submit();
			}
		}
	}
}
function frm_sub_fun_callBack(result)
{
	if(result==false)
	{
		return false;	
	}
	else
	{
		$("#frm_method").val('reorder');
		document.addframe.submit();	
	}
}
function frm_auto_sub_fun_callBack(result)
{
	if(result==false){
		top.page_link(top.link_selected, false);
		return false;
	}
	else{
		$("#frm_method").val('auto_save');
		document.addframe.submit();	
	}
}
function frm_sub_fun_lensD(action){
var isdis_code=0;
	if(action=="cancel")
	{
		parent.parent.document.getElementById('main_iframe').src='interface/patient_interface/index.php';
		return;
		/*var conf = confirm('Are you sure to cancel this Order ?');
		if(conf!=true)
		{
			return false;
		}*/
	}
	
	if(action=="save" || action=="next" || action=="dispensed_post")
	{
		var dis=dis_code=alldis=0;
		/*$('.disc_code').each(function(index, element) {
			dis = $('.disc_code').get(index).value;
			dis_code = $('.discount_code').get(index).value;
			if(dis.slice(-1)=='%'){
				dis = dis.replace('%','');
			}
			if(dis[0]=="$")
			{
				dis = dis.replace(/^[$]+/,"");			
			}
			if(dis>0)
			{
				if(dis_code=="")
				{
					top.falert("Please Select Discount Code");
					isdis_code=1;
					return false;
				}
			}
		});*/
	}
	if(isdis_code==0)
	{
		$("#frm_method_lensD").val(action);
	}
	return true;
}

$(document).ready(function(){
var dd_pro = "";
<?php
for($j=1;$j<=$LC;$j++)
{

if($order_id!=""):
?>
/*Frames Stock*/
stockFramesItems[<?php echo $j-1; ?>] = true;
<?php
endif;
/*VisionWeb ShowHide*/
if($showVision[$j]){
	echo '$("span.visionShowLink[elemkey=\''.$j.'\']").hide();';
	echo '$("#visionweb_fields_'.$j.'").show();';
}
?>
/*End VisionWeb ShowHide*/
	
	/*ICD 10 typeahead*/
	bind_autocomp($("#dx_code_<?php echo $j;?>_lensD"),getDataFilePath);
	/*End ICD 10 typeahead*/
	
	$("#upc_name_<?php echo $j;?>").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'framesData',
			hidIDelem: document.getElementById('upc_id_<?php echo $j;?>'),
			minLength:1,
			maxVals: 10,
			bgColor: "#888888",
			hoverBgColor: "#000000",
			textColor: "#FFFFFF",
			fontSize: "11px",
			showAjaxVals: 'upc'
	});
	
	
	$("#item_name_<?php echo $j;?>").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'framesData',
		hidIDelem: document.getElementById('upc_id_<?php echo $j;?>'),
		showAjaxVals: 'name'
	});
	
	$("#upc_name_<?php echo $j;?>_lensD").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'lensData',
		hidIDelem: document.getElementById('upc_id_<?php echo $j;?>_lensD'),
		showAjaxVals: 'upc'
	});
	
	$("#color_id_<?php echo $j;?>").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'frameColors',
		hidIDelem: document.getElementById('color_code_<?php echo $j;?>'),
		showAjaxVals: 'name'
	});
	
	/*$("#lens_physician_name_lensD_<?php echo $j;?>").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'physicianData',
		hidIDelem: document.getElementById('lens_physician_id_lensD_<?php echo $j;?>'),
		showAjaxVals: 'name',
	});*/
	
	dd_pro = new Array();
	dd_pro["listHeight"] = 200;
	dd_pro["noneSelected"] = "Select All";
	dd_pro["rowId"] = "2_<?php echo $j; ?>_a_r_";
	dd_pro["tbId"] = "in_lens_ar";
	dd_pro["vision"] = "od";
	$("#a_r_id_<?php echo $j;?>_od_lensD").multiSelect(dd_pro, posRowMulti);
	
	dd_pro = new Array();
	dd_pro["listHeight"] = 200;
	dd_pro["noneSelected"] = "Select All";
	dd_pro["rowId"] = "2_<?php echo $j; ?>_a_r_";
	dd_pro["vision"] = "os";
	dd_pro["tbId"] = "in_lens_ar";
	
	$("#a_r_id_<?php echo $j;?>_os_lensD").multiSelect(dd_pro, posRowMulti);
	
<?php }?>
itemdropdown = function(lens_sel_id){
		var ov = "";
		ov = "&type="+$("#seg_type_id_1_lensD").val()+"&progressive="+$("#progressive_id_1_lensD").val()+"&material="+$("#material_id_1_lensD").val()+"&transition="+$("#transition_id_1_lensD").val()+"&ar="+$("#a_r_id_1_lensD").val()+"&tint="+$("#tint_id_1_lensD").val()+"&polarized="+$("#polarized_id_1_lensD").val()+"&edge="+$("#edge_id_1_lensD").val()+"&color="+$("#color_id_1_lensD").val();
}
<?php if($order_id==""): ?>
	autoFillRx();
/*PreSelect VisionPlan*/
	$('#main_ins_case_id_1').val(top.main_iframe.ptVisionPlanId);
<?php endif; ?>
changeLensPosLabel();
calculate_all();
prac_code_typeahead();
});

/*Add POS Row MultiSelect Options*/
function posRowMulti(obj, rowID, tbId, selectObj, vision){
	
	var counter = rowID.substr(2,1);
	var vision_selected = $( '#lens_vision_'+counter+'_lensD' ).val();
	
	var itemNames = {"in_lens_type":"lens", "in_lens_design":"design", "in_lens_material":"material", "in_lens_ar":"a_r", "in_lens_diopter": "diopter"};
	
	var itemNamesPos = {"in_lens_type":"Seg Type", "in_lens_design":"Design", "in_lens_material":"Material", "in_lens_ar":"Treatment", "in_lens_diopter": "Prism Diopter Charges"};
	
	<?php if(defined('TAX_CHECKBOX_CHECKED') && constant('TAX_CHECKBOX_CHECKED')=='FALSE'){ echo'var tax_applied = false;';}else{ echo'var tax_applied = true;';}?>
	rowID = (typeof(rowID)==="undefined")?false:rowID;
	var selectedVals = $(selectObj).selectedValuesString();
	selectedVals = (selectedVals=="")?false:selectedVals;
	
	if( vision_selected=='ou' && vision=='od' ){
		$("#a_r_id_"+counter+"_os_lensD").changeSelected(selectedVals, true);
	}
	
	if(selectedVals && rowID){
		counter = rowID.substr(2,1);
		var tb = tbId;
		var lensItemCounter = $("#lens_item_count_"+counter+"_lensD");
		var mystr = "";
		
		pracCodes = prac_by_type_multi(selectedVals,'in_lens_ar');
		pracCodes = pracCodes.split(";");
		
		var lens_discount = $("#discount_hidden_"+counter+"_lensD").val();
		
		string = 'action=get_price_from_praccode_multi&sel_ids='+encodeURI(selectedVals)+'&tb_name='+tb;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response){
				selectedVals = (String(selectedVals)).split(",");
				response = $.parseJSON(response);
				$.each(response, function(repsKey, respVal){
					
					mystr = respVal.split('~~~');
					rowId = rowID+selectedVals[repsKey]+"_display_"+vision;
					
					var row = $("#"+rowId);
					var rown = "";
					if(row.length>0) {
						$("#"+rowId+" .pracodefield").val(mystr[0]);
						$("#"+rowId+" .pracodefield").attr("title",mystr[3]);
						
						$("#"+rowId+" .price_cls").val(mystr[1]);
						$("#"+rowId+" .allowed_cls").val(mystr[1]);
						
						$("#"+rowId+" .qty_cls").val($("#qty_"+counter+"_lensD").val());
						$("#"+rowId+" .price_disc_per_proc").val(lens_discount);
						$("#"+rowId+" .price_disc").val('0.00');
						
						if(top.main_iframe.admin_iframe.remakeFlag){
							
							tax_p = parseFloat($("#"+rowId+" input.tax_p").val());
							if(tax_p>0 && tax_applied === true){
								$("#"+rowId+" .tax_applied").attr('checked', true);
							}
						}
						
						$("#"+rowId).find(".del_status").val("0"); /*Unset Del Status*/
						$("#"+rowId).removeClass('hideRow');
					}
					else{
						
						var lensItem = 0;
						lensItemCounter = $("#lens_item_count_"+counter+"_lensD");
						if(lensItemCounter.length>0){
							lensItem = parseInt($(lensItemCounter).val());
							lensItem++;
						}
						else{
							lensItem++;
						}
						
					
						rown +='<tr id="'+rowId+'" class="multiVals"><!--td-->';
						if (lensItem==1){
							rown +='<input readonly="" style="width:100%;" type="hidden" name="pos_upc_name_'+counter+'_lensD" id="pos_upc_name_'+counter+'_lensD" value="<?php echo $GLOBALS['CUSTOM_LENS']['upc']; ?>">';
							$("#item_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['name']; ?>");
							$("#upc_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['upc']; ?>");
							$("#upc_id_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['id']; ?>");
							$("#item_id_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['id']; ?>");
						}
						else{
							if($("#upc_id_"+counter+"_lensD").val()==""){
								$("#pos_upc_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['upc']; ?>");
								$("#item_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['name']; ?>");
								$("#upc_name_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['upc']; ?>");
								$("#upc_id_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['id']; ?>");
								$("#item_id_"+counter+"_lensD").val("<?php echo $GLOBALS['CUSTOM_LENS']['id']; ?>");
							}
						}
						
						rown +='<input type="hidden" name="pos_order_chld_id_'+counter+'_lensD" value=""><input type="hidden" name="pos_order_detail_id_'+counter+'_lensD" value="" id="pos_order_detail_id_'+counter+'_lensD"><input type="hidden" name="lens_item_detail_id_'+counter+'_'+lensItem+'_lensD" id="lens_item_detail_id_'+counter+'_'+lensItem+'_lensD" value="'+lensItem+'">';
						rown +='<input type="hidden" name="lens_item_detail_name_'+counter+'_'+lensItem+'_lensD" id="lens_item_detail_name_'+counter+'_'+lensItem+'_lensD" value="'+itemNames[tb]+'_'+selectedVals[repsKey]+'"><input type="hidden" name="lens_price_detail_id_'+counter+'_'+lensItem+'_lensD" id="lens_price_detail_id_'+counter+'_'+lensItem+'_lensD" value="">';

						rown +='<input type="hidden" name="pos_upc_id_1_lensD" id="pos_upc_id_1_lensD" value=""><!--/td-->';
						
						rown +='<td>';
							rown +='<span class="vis_type vision_'+vision+'">'+vision.toUpperCase()+'</span>';
							rown +='<input type="hidden" name="pos_lens_item_vision_'+counter+'_'+lensItem+'_lensD" id="pos_lens_item_vision_'+counter+'_'+lensItem+'_lensD" value="'+vision+'" class="row_vision_value" />';
						rown +='</td>';	
						
						rown +='<td><input readonly="" style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_'+counter+'_'+lensItem+'_lensD" id="pos_lens_item_name_'+counter+'_lensD" value="'+itemNamesPos[tb]+'"></td>';
						
						rown +='<td><input readonly style="width:100%;" type="text" class="itemnameDisp" name="pos_lens_item_name_disp_'+counter+'_'+lensItem+'_lensD" id="pos_lens_item_name_disp_'+counter+'_'+lensItem+'_lensD" value="" /></td>';
						
						rown +='<td><input style="width:100%;" type="text" class="pracodefield" name="item_prac_code_'+counter+'_'+lensItem+'_lensD" id="item_prac_code_'+counter+'_'+lensItem+'_lensD" value="'+mystr[0]+'" title="'+mystr[0]+'" calculate_all();"></td>';
						/* onchange="show_price_from_praccode(this,\'price_'+counter+'_'+lensItem+'_lensD\',\'pos\'); */
						
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_price_'+counter+'_'+lensItem+'_lensD" id="lens_item_price_'+counter+'_'+lensItem+'_lensD" value="'+mystr[1]+'" class="price_cls currency" onchange="calculate_all();"></td>';

/*onChange="changeQty(\'2\', this.value, \''+counter+'\');" */
						rown += '<td><input type="text" style="width:100%; text-align:right;" class="qty_cls" name="lens_qty_'+counter+'_'+lensItem+'_lensD" id="lens_qty_'+counter+'_'+lensItem+'_lensD_lensD" value="'+$("#qty_"+counter+"_lensD").val()+'" autocomplete="off"  onChange="calculate_all();" onKeyUp="validate_qty(this);" /><input type="hidden" class="rqty_cls" name="pos_qty_right_1_lensD" value="0"><input type="hidden" name="pos_module_type_id_1_lensD" value="2"></td>';

						rown +='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_allowed_'+counter+'_'+lensItem+'_lensD" id="lens_item_allowed_'+counter+'_'+lensItem+'_lensD" value="'+mystr[1]+'" class="allowed_cls currency" onchange="calculate_all();"></td>';
						
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="ins_amount_'+counter+'_'+lensItem+'_lensD" id="ins_amount_'+counter+'_'+lensItem+'_lensD" value="0.00" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"></td>';
						
						rown +='<td>';
							/*Line item's share in overall discount*/
							rown +='<input type="hidden" name="lens_item_overall_discount_'+counter+'_'+lensItem+'_lensD" id="lens_item_overall_discount_'+counter+'_'+lensItem+'_lensD" value="0.00" class="item_overall_disc" />';
							rown +='<input type="hidden" name="lens_item_discount_'+counter+'_'+lensItem+'_lensD" id="lens_item_discount_'+counter+'_'+lensItem+'_lensD" value="'+((lens_discount=="")?0:lens_discount)+'" onchange="calculate_all();" class="price_disc_per_proc">';
							rown +='<input style="width:100%; text-align:right;" type="text" name="read_lens_item_discount_'+counter+'_'+lensItem+'_lensD" id="pos_read_discount_'+counter+'_'+lensItem+'_lensD" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />';
						rown +='</td>';
						
						rown +='<td style="display:none"><input readonly="" style="width:100%; text-align:right;" type="text" name="lens_item_total_'+counter+'_'+lensItem+'_lensD" id="pos_total_amount_'+counter+'_'+lensItem+'_lensD" value="0.00" class="price_total currency" onchange="calculate_all();">';
							/*Tax Calculations*/
							tax_applied = (tax_applied && facTax[2]>0);
							rown +='<input type="hidden" name="tax_p_'+counter+'_'+lensItem+'_lensD" id="tax_p_'+counter+'_'+lensItem+'_lensD" class="tax_p" value="'+facTax[2]+'" />';
							rown +='<input type="hidden" name="tax_v_'+counter+'_'+lensItem+'_lensD" id="tax_v_'+counter+'_'+lensItem+'_lensD" class="tax_v" value="0.00" />';
							/*End Tax Calculations*/
						rown +='</td>';
						
						
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="pt_paid_'+counter+'_'+lensItem+'_lensD" id="pt_paid_'+counter+'_'+lensItem+'_lensD" value="0.00" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"></td>';
						
						rown +='<td><input style="width:100%; text-align:right;" type="text" name="pt_resp_'+counter+'_'+lensItem+'_lensD" id="pt_resp_'+counter+'_'+lensItem+'_lensD" value="0.00" class="resp_cls currency" readonly=""></td>';
						
						rown +='<td><select name="discount_code_'+counter+'_'+lensItem+'_lensD" id="discount_code_1" class="text_10 disc_code dis_code_class" style="width:100%;"><option value="0">Please Select</option><?php $sel_rec=imw_query("select d_id,d_code,d_default from discount_code"); while($sel_write=imw_fetch_array($sel_rec)){ ?><option value="<?php echo $sel_write['d_id'];?>" ><?php echo $sel_write['d_code'];?></option><?php } ?></select></td>';
						
						rown +='<td><select name="ins_case_id_'+counter+'_'+lensItem+'_lensD" id="ins_case_id_'+counter+'_'+lensItem+'_lensD" class="ins_case_class" style="width:100%;" onchange="switch_pat_ins_resp(\''+counter+'_'+lensItem+'_lensD\');"><option value="0">Self Pay</option>';
		$.each(insCases, function(insVal, insKey){
			insSelected = (insKey==top.main_iframe.ptVisionPlanId)?' selected="selected"':'';
			rown +='<option value="'+insKey+'"'+insSelected+'>'+insVal+'</option>';
		});
		rown +='</select><input type="hidden" name="del_status_'+counter+'_'+lensItem+'_lensD" id="del_status_'+counter+'_'+lensItem+'_lensD" value="0" class="del_status"></td>';
						
						rown += '<td><input type="checkbox" class="tax_applied" name="tax_applied_'+counter+'_'+lensItem+'_lensD" id="tax_applied_'+counter+'_'+lensItem+'_lensD" value="1" '+((tax_applied)?'checked="checked"':'')+' onChange="cal_overall_discount()" /></td>';
						
						var delKey = "a_r_"+selectedVals[repsKey];
						rown += '<td><img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow(\'2\', \''+counter+'\', \''+delKey+'\', \''+vision+'\');" /></td>';
						
						rown +='</tr>';
													
						if(lensItem==1){
							$('#lens_item_count_'+counter+'_lensD').remove();
							rown +='<input type="hidden" name="lens_item_count_'+counter+'_lensD" id="lens_item_count_'+counter+'_lensD" value="'+lensItem+'">';
							$("table.table_collapse.posTable > tbody").prepend(rown);
						}
						else{
							rowCount = $('tr[id^="2_'+counter+'"');
							lastRow = rowCount[rowCount.length-1];
							
							
							/*Group Rows on the basis of vision*/
							visRows = $("table.table_collapse.posTable > tbody > tr[id^='2_"+counter+"'][id$='"+vision+"']");
							
							diopterRow = $("table.table_collapse.posTable > tbody > tr[id^='2_"+counter+"_diopter_display_"+vision+"']");
							
							if( visRows.length > 0 ){
								lastRow = visRows[visRows.length-1];
							}
							
							if( diopterRow.length > 0 )
							{
								$(rown).insertBefore($(diopterRow[0]));
							}
							else
							{
								/*OD at top always*/
								if( vision == 'od' && visRows.length == 0 ){
									lastRow = rowCount[0];
									$(rown).insertBefore($(lastRow));
								}
								else
									$(rown).insertAfter($(lastRow));
							}
							
							$(lensItemCounter).val(lensItem);
						}
					}
				});
			},
			complete: function(){
				var rowId1 = rowID.substring(0,rowID.length-4);
				var pro_cont = rowID.split('_');
				pro_cont = pro_cont[1]
				var lensRows = $(".posTable>tbody tr[id^='"+rowId1+"']");
				$("#lens_item_count_"+pro_cont+"_lensD").val(lensRows.length);
				$.each(lensRows, function(i, obj){
					k = i+1;
					$(obj).find("#lens_item_detail_id_"+pro_cont+"_"+k+"_lensD").val(k);
				});
				
				currencySymbols();
				changeLensPosLabel();
				calculate_all();
				
				//rowID1 = rowID.split('_');
				//fetch_vision_dd('tst1', 'treatment', rowID1[1]);
			}
		});
	}
	else if(!selectedVals && rowID){
		var posRows = $('.posTable tr[id^="'+rowID+'"][id$="_'+vision+'"]');
		var counter = rowID.charAt(2);
		$.each(posRows, function(i, rowObj){
			
			$(rowObj).find(".pracodefield").val("");
			$(rowObj).find(".pracodefield").attr("title","");
			$(rowObj).find(".price_cls").val('0.00');
			$(rowObj).find(".price_disc_per_proc").val('0.00');
			$(rowObj).find(".price_disc").val('0.00');
			$(rowObj).find(".qty_cls").val(0);
			$(rowObj).find(".price_total").val('0.00');
			$(rowObj).find(".allowed_cls").val('0.00');
			$(rowObj).addClass('hideRow');
			
			var delStatusField = $(rowObj).find(".del_status");
			if(delStatusField.length>0){
				$(delStatusField[0]).val("1"); /*Set Del Status*/
			}
			else{
				$(rowObj).remove();	
			}
		});
		var lensItemCounter1 = $("tr[id^='2_"+counter+"']").length;
		$("#lens_item_count_"+counter+"_lensD").val(lensItemCounter1);
		calculate_all();
	}
}
/*End Add POS Row MultiSelect Options*/
function autoFillRx(){
<?php if(count($arrLensRX)>0){ ?>
	var rxBaseDet="<?php echo $rxBaseDet;?>".split('~');
	var rxDetailsOD="<?php echo html_entity_decode($rxDetailsOD, ENT_QUOTES, 'UTF-8');?>".split('~');
	var rxDetailsOS="<?php echo html_entity_decode($rxDetailsOS, ENT_QUOTES, 'UTF-8');?>".split('~');
	var rxDetailsDX="<?php echo $lens_rxDetailsDX;?>".split('~');
	
	for(var kk=0;kk<=14;kk++){
		if($.trim(rxDetailsOD[kk])!=""){
			rxDetailsOD[kk]=$.trim(rxDetailsOD[kk]);
			if(rxDetailsOD[kk]=="20/"){
				rxDetailsOD[kk]="";
			}
		}
		if($.trim(rxDetailsOS[kk])!=""){
			rxDetailsOS[kk]=$.trim(rxDetailsOS[kk]);
			if(rxDetailsOS[kk]=="20/"){
				rxDetailsOS[kk]="";
			}
		}
	}
	
	var LC = $("#last_cont_lensD").val();
		
	// Base
	document.getElementById('lens_physician_id_lensD_'+LC).value=rxBaseDet[0];
	document.getElementById('lens_physician_name_lensD_'+LC).value=rxBaseDet[1];
	document.getElementById('lens_telephone_lensD_'+LC).value=rxBaseDet[2];
	document.getElementById('lens_last_exam_1_lensD').value=rxBaseDet[3];
	document.getElementById('isRXLoaded_lensD_'+LC).value=1;
	
	// OD VALUES
	document.getElementById('lens_sphere_od_1_lensD').value=rxDetailsOD[0];
	document.getElementById('lens_cylinder_od_1_lensD').value=rxDetailsOD[1];
	document.getElementById('lens_axis_od_1_lensD').value=rxDetailsOD[2];
	document.getElementById('lens_mr_od_p_1_lensD').value=rxDetailsOD[3];
	document.getElementById('lens_mr_od_prism_1_lensD').value=rxDetailsOD[4];
	document.getElementById('lens_mr_od_splash_1_lensD').value=rxDetailsOD[5];
	document.getElementById('lens_mr_od_sel_1_lensD').value=rxDetailsOD[6];
	document.getElementById('lens_npd_od_1_lensD').value=$.trim(rxDetailsOD[7]);
	document.getElementById('lens_dpd_od_1_lensD').value=$.trim(rxDetailsOD[8]);
	document.getElementById('lens_add_od_1_lensD').value=rxDetailsOD[9];
	document.getElementById('lens_base_od_1_lensD').value=rxDetailsOD[10];
	document.getElementById('lens_axis_od_va_1_lensD').value=rxDetailsOD[11];
	document.getElementById('lens_add_od_va_1_lensD').value=rxDetailsOD[12];
	document.getElementById('lens_seg_od_1_lensD').value=rxDetailsOD[13];
	document.getElementById('lens_oc_od_1_lensD').value=rxDetailsOD[14];
	
	document.getElementById('sph_text_od_'+LC).innerHTML=rxDetailsOD[0];
	document.getElementById('cyl_text_od_'+LC).innerHTML=rxDetailsOD[1];
	document.getElementById('axis_text_od_'+LC).innerHTML=rxDetailsOD[2];
	document.getElementById('add_text_od_'+LC).innerHTML=rxDetailsOD[9];
	
	var rx_1_od = false;
	var rx_2_od = false;
	if(rxDetailsOD[3] || rxDetailsOD[6]){
		document.getElementById('prism_text_1_od_'+LC).innerHTML=rxDetailsOD[3]+' '+rxDetailsOD[6];
		rx_1_od = true;
	}
	if(rxDetailsOD[5] || rxDetailsOD[4]){
		document.getElementById('prism_text_2_od_'+LC).innerHTML=rxDetailsOD[5]+' '+rxDetailsOD[4];
		rx_2_od = true;
	}
	
	if(rx_1_od && rx_2_od){
		document.getElementById('prism_text_od_seperator_'+LC).innerHTML="/";
	}
		
	//document.getElementById('dpd_text_od_'+LC).innerHTML=rxDetailsOD[7];
	//document.getElementById('npd_text_od_'+LC).innerHTML=rxDetailsOD[8];
	//document.getElementById('base_text_od_'+LC).innerHTML=rxDetailsOD[10];
	document.getElementById('axis_text_od_va_'+LC).innerHTML=rxDetailsOD[11];
	document.getElementById('add_text_od_va_'+LC).innerHTML=rxDetailsOD[12];
	
	// OS VALUES

	document.getElementById('lens_sphere_os_1_lensD').value=rxDetailsOS[0];
	document.getElementById('lens_cylinder_os_1_lensD').value=rxDetailsOS[1];
	document.getElementById('lens_axis_os_1_lensD').value=rxDetailsOS[2];
	document.getElementById('lens_mr_os_p_1_lensD').value=rxDetailsOS[3];
	document.getElementById('lens_mr_os_prism_1_lensD').value=rxDetailsOS[4];
	document.getElementById('lens_mr_os_splash_1_lensD').value=rxDetailsOS[5];
	document.getElementById('lens_mr_os_sel_1_lensD').value=rxDetailsOS[6];
	document.getElementById('lens_npd_os_1_lensD').value=$.trim(rxDetailsOS[7]);
	document.getElementById('lens_dpd_os_1_lensD').value=$.trim(rxDetailsOS[8]);
	document.getElementById('lens_add_os_1_lensD').value=rxDetailsOS[9];
	document.getElementById('lens_base_os_1_lensD').value=rxDetailsOS[10];
	document.getElementById('lens_axis_os_va_1_lensD').value=rxDetailsOS[11];
	document.getElementById('lens_add_os_va_1_lensD').value=rxDetailsOS[12];
	document.getElementById('lens_seg_os_1_lensD').value=rxDetailsOS[13];
	document.getElementById('lens_oc_os_1_lensD').value=rxDetailsOS[14];
	
	document.getElementById('sph_text_os_'+LC).innerHTML=rxDetailsOS[0];
	document.getElementById('cyl_text_os_'+LC).innerHTML=rxDetailsOS[1];
	document.getElementById('axis_text_os_'+LC).innerHTML=rxDetailsOS[2];
	document.getElementById('add_text_os_'+LC).innerHTML=rxDetailsOS[9];
	
	var rx_1_os = false;
	var rx_2_os = false;
	if(rxDetailsOS[3] || rxDetailsOS[6]){
		document.getElementById('prism_text_1_os_'+LC).innerHTML=rxDetailsOS[3]+' '+rxDetailsOS[6];
		rx_1_os = true;
	}
	if(rxDetailsOS[5] || rxDetailsOS[4]){
		document.getElementById('prism_text_2_os_'+LC).innerHTML=rxDetailsOS[5]+' '+rxDetailsOS[4];
		rx_2_os = true
	}
	
	if(rx_1_os && rx_2_os){
		document.getElementById('prism_text_os_seperator_'+LC).innerHTML="/";
	}
	
	//document.getElementById('dpd_text_os_'+LC).innerHTML=rxDetailsOD[7];
	//document.getElementById('npd_text_os_'+LC).innerHTML=rxDetailsOD[8];
	//document.getElementById('base_text_os_'+LC).innerHTML=rxDetailsOS[10];
	document.getElementById('axis_text_os_va_'+LC).innerHTML=rxDetailsOS[11];
	document.getElementById('add_text_os_va_'+LC).innerHTML=rxDetailsOS[12];
	
	//document.getElementById('dx_code_1_lensD').value=rxDetailsDX[0];
	//activeDeactiveFields();
	get_rx_dx_code(LC);
	set_phone_format(document.getElementById('lens_telephone_lensD_'+LC),'<?php echo $GLOBALS['phone_format']; ?>');
	document.getElementById('lens_rx_dos_'+LC+'_lensD').value = "<?php echo $rxDosRaw; ?>";
	//document.getElementById('rxDate_'+LC).innerHTML="<?php echo $rxDos; ?>";
	prescribedBy = '';
	if(rxBaseDet[1]!=''){
		prescribedBy = ', Prescribed By: '+rxBaseDet[1];
	}
	$('#rx_label_'+LC).attr("title","Lens Prescription - <?php echo $rxDos; ?>"+prescribedBy);
	if(LC==1){
		if(rxBaseDet[1]=='')
			$('#physicianDisp').text('Physician: '+PCP_DISP).attr('title', PCP);
		else
			$('#physicianDisp').text('Prescribed By: '+rxBaseDet[1]);
	}
	
	
	<?php } ?>
}

function get_rx_dx_code(id){
	var v_icd_10 = 10;
	var s_os=document.getElementById('lens_sphere_os_'+id+'_lensD').value;
	var s_od=document.getElementById('lens_sphere_od_'+id+'_lensD').value;
	var ad_os=document.getElementById('lens_add_os_'+id+'_lensD').value;
	var ad_od=document.getElementById('lens_add_od_'+id+'_lensD').value;
	var t_dx_2="";
	var t_dx="";
	
	var t_dx_arr = new Array();
	if((ad_od!="" && ad_od!="+")||(ad_os!="" && ad_os!="+")){
		t_as_2="Presbyopia"; t_dx_2=(v_icd_10=="9")? "367.4" : "H52.4; ";
	}
	if(s_od!="" && s_od!="-" && s_od!="+"){
		if(s_od.indexOf("-")!=-1){t_as="Myopia"; t_dx_arr.push((v_icd_10=="9")? "367.1" : "H52.11");}
		else{t_as="Hyperopia"; t_dx_arr.push((v_icd_10=="9")? "367.0" : "H52.01");}
	}
	if(s_os!="" && s_os!="-" && s_os!="+"){
		if(s_os.indexOf("-")!=-1){t_as="Myopia"; t_dx_arr.push((v_icd_10=="9")? "367.1" : "H52.12");}
		else{t_as="Hyperopia"; t_dx_arr.push((v_icd_10=="9")? "367.0" : "H52.02");}
	}
	
	t_dx_arr = $.unique(t_dx_arr);
	t_dx_arr = t_dx_arr.join("; ");
	t_dx_arr = t_dx_arr.trim();
	t_dx_arr += "; ";
	
	var final_dx_code=t_dx_2+t_dx_arr;
	document.getElementById('dx_code_'+id+'_lensD').value=final_dx_code;
}
/*function show_rx(count){
	$('#rx_div_'+count).slideToggle('slow');
	$('#arrow_image_'+count).toggle();
	$('#rx_link_'+count).toggle();
	$('#rxDate_'+count).toggle();
}*/
function chk_dis_fun(){
	var amt =0;
	var dis =0;
	var quantity =0;
	
	if(document.getElementById('discount_1').value!=""){
		var dis = document.getElementById('discount_1').value;
	}
	if(document.getElementById('qty_1').value>0){
		quantity = document.getElementById('qty_1').value;
	}
	if(document.getElementById('price_1').value!=""){
		var amt = document.getElementById('price_1').value;
	}
	
	var total = cal_discount(amt,dis);
	var final_price=parseFloat(total)*parseInt(quantity);
	document.getElementById('total_amount_1').value=final_price.toFixed(2);
	
	  var final_allowed=parseFloat(amt)*parseInt(quantity);
	document.getElementById('allowed_1').value=final_allowed.toFixed(2);
	
	$("#total_amount_1").trigger('change');
	$("#allowed_1").trigger('change');
}
function chk_pof_fun(count, obj){
	if(obj.checked==false){return;}
	//var obj_cls=$(obj).attr("class");
	
	var obj_cls=$('.chk_box_pof_'+count);
	$(obj_cls).each(function(index, element){
		$(element).attr("checked",false);
    });
	obj.checked=true;
	patientFrame(count);
	
	var pos_frame_description = $('#itemDescription_frame_'+count);
	if(pos_frame_description.length>0){
		if($('#in_add_'+count).is(':checked'))
			$(pos_frame_description).val('Pt Own Frame');
		else
			$(pos_frame_description).val('');
	}
}

$(document).ready(function(e) {
	//BUTTONS
	var mainBtnArr=[];
	var counter=0;
	<?php 
	if($order_del_status==0){
	if($_SESSION['order_id']==""){?>
	mainBtnArr[counter] = new Array("frame","Previous","top.main_iframe.admin_iframe.frm_sub_fun('previous');");
	counter++;
	<?php }?>
<?php if($order_edit_btn_status){ ?>
	mainBtnArr[counter] = new Array("frame","Cancel","top.main_iframe.admin_iframe.frm_sub_fun('cancel')");
	counter++;
	mainBtnArr[counter] = new Array("frame","Save","top.main_iframe.admin_iframe.frm_sub_fun('save')");
	counter++;
<?php } ?>
	mainBtnArr[counter] = new Array("frame","Print Order","top.main_iframe.admin_iframe.printpos('<?php echo $order_id;?>','Frame and `Lens Selection')");
	counter++;
	mainBtnArr[counter] = new Array("frame","Patient Receipt","top.main_iframe.admin_iframe.patientReceipt('<?php echo $order_id; ?>','Frame and Lens Selection')");
	counter++;
<?php if($order_post_btn_status){ ?>
	mainBtnArr[counter] = new Array("frame","Post","top.main_iframe.admin_iframe.frm_sub_fun('order_post')");	
	counter++
<?php } ?>
<?php if($_SESSION['order_id']!=""){?>
	mainBtnArr[counter] = new Array("frame","Reorder","top.main_iframe.admin_iframe.frm_sub_fun('reorder')");
	counter++;
	mainBtnArr[counter] = new Array("frame","Remake","top.main_iframe.admin_iframe.frm_sub_fun('remake')");
	<?php if($GLOBALS['connect_visionweb']!="" && ($main_order_status=="pending" or $main_order_status=="")){ ?>
			counter++;
			mainBtnArr[counter] = new Array("frame","Send to VisionWeb","top.main_iframe.admin_iframe.frm_sub_fun('VisionWeb')");	
	<?php } 
	} 
}//checking order del status end here
else{
	?>mainBtnArr[counter] = new Array("frame","Cancel","top.main_iframe.admin_iframe.frm_sub_fun('cancel')");
	counter++;
	<?php
}?>
	counter++
	top.btn_show("admin",mainBtnArr);		
	
/*VisionWEb*/
	visionHideShow();
/*End VisionWEb*/
	<?php if($action=="VisionWeb"){?>
		frm_sub_fun('VisionWebSend');
	<?php }?>
	<?php if($action=="order_post" || $action =="dispensed_post"){
		$order_enc_id=$main_ord_row['order_enc_id'];
	?>	
		var order_enc_id = '<?php echo $order_enc_id; ?>';
		var win1=top.WindowDialog.open('Add_new_popup','../../remoteConnect.php?encounter_id='+order_enc_id,'opt_med');
	<?php
	}?>
	
<?php if($_SESSION['order_id']!=''): ?>	
/*Upload Trace File*/
	$( '.traceFileField' ).on( 'change', function(event){
		
		var traceFile = event.target.files[0];
		
		var itemKey	= $(this).attr('orderItemKey');
		var orderDetailId = $.trim($('#order_detail_id_'+itemKey).val());
		
		if( orderDetailId=='' )
			return false;
		
		var data = new FormData();
		data.append('traceFile', traceFile, traceFile.name);
		data.append('type', 'uploadTraceFile');
		data.append('order_id', '<?php echo $_SESSION['order_id']; ?>');
		data.append('order_dtail_id', orderDetailId);
		
		var fieldObj = $(this);
		
		$.ajax({
			url: top.WRP+'/interface/patient_interface/ajax.php',
			method: 'POST',
			data: data,
			processData: false,
			contentType: false,
			success: function(resp){
				
				resp = $.parseJSON(resp);
				if(resp.status==1){
					$(fieldObj).next('input:button').val('Update Trce File').addClass('traceFileExists');
					$('#traceFileName_'+itemKey).text(resp.file);
					$('#traceFileName_'+itemKey).attr('title', resp.title);
					$(fieldObj).val('');
				}
			}
		});
	});
	
	/*Show Physician Name*/
	$('#physicianDisp').text('Prescribed By: <?php echo addslashes($tempPhyName); ?>');
/*End Upload Trace File*/	
<?php endif; ?>

	/*Changing Color of disabled selected selectLists*/
	$('select > option:selected:disabled').each(function(){
		
		var select = $(this).parent('select');
		addSelDisable(select);
		
		$(select).on('change', addSelDisable);
	});
	
	function addSelDisable(element){
		
		if( typeof(element.target) !== undefined && typeof(element.target) === 'object' )
			element = element.target;
		
		if( $('>option:selected', element).is(':disabled') )
			$(element).addClass('disabledOpt');
		else
			$(element).removeClass('disabledOpt');
	}
	
	/*End Changing Color of disabled selected selectLists*/
});

/*VisionWEb*/
function visionHideShow(){
	$(".visionShowLink").on('click', function(){
		var elemKey = $(this).attr("elemKey");
		$(this).hide();
		$("#visionweb_fields_"+elemKey).show();
		$('span.visionHideLink[elemKey="'+elemKey+'"]').show();
	});
	
	$(".visionHideLink").on('click', function(){
		var elemKey = $(this).attr("elemKey");
		$(this).hide();
		$("#visionweb_fields_"+elemKey).hide();
		$('span.visionShowLink[elemKey="'+elemKey+'"]').show();
	});
}
/*End VisionWEb*/

/*
 *Ajax Default Options - Loader
 */
$(document).ajaxSend(function(){
	$("#loading").show();
});
$(document).ajaxComplete(function(){
	if(hideloaderFlag){
		$("#loading").hide();
	}
});

function prac_code_typeahead(){
	
	$(".posTable .pracodefield:not(:has(+.hiddenPracId))").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeO'
	});
	
	//Bind Prac Code typeahead to Custom charge row
	$(".posTable .pracodefield:has(+.hiddenPracId)").each(function(){
		$(this).ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'praCode',
			showAjaxVals: 'defaultCodeO',
			hidIDelem: $(this).next('.hiddenPracId'),
			tiggerHidChange: true
		});
	});
	
	$(".posTable .pracodefield").attr('autocomplete', 'off');
}

/*Filter VisionWeb Values*/
/*Fetch dropdown values - VisionWeb*/
function fetch_vision_dd(value, element, key, call, itemVals, vision_val){
	
	var vision_selected = $( '#lens_vision_'+key+'_lensD' ).val();
	
	call = (typeof(call)!="undefined")?call:"ptInterface";
	
	sel_id = 0;
	
	if( vision_selected=='ou' && vision_val=='od' ){
		/*if( element=='seg_type' && $( '#seg_type_id_'+key+'_os_lensD' ).val()=='0' ){*/
		if( element=='seg_type' ){
			$( '#seg_type_id_'+key+'_os_lensD' ).val($( '#seg_type_id_'+key+'_od_lensD' ).val()).trigger('change');
		}
		/*else if( element=='design' && $( '#design_id_'+key+'_os_lensD' ).val()=='0' ){*/
		else if( element=='design' ){
			$( '#design_id_'+key+'_os_lensD' ).val($( '#design_id_'+key+'_od_lensD' ).val()).trigger('change');
		}
		/*else if( element=='material' && $( '#material_id_'+key+'_os_lensD' ).val()=='0' ){*/
		else if( element=='material' ){
			$( '#material_id_'+key+'_os_lensD' ).val($( '#material_id_'+key+'_od_lensD' ).val()).trigger('change');
		}
	}
	
	if(typeof(value)!="undefined" && typeof(element)!="undefined" && value!="" || value!="0" && element!="" || element!="0"){
		
		if(typeof(sel_id)=="undefined")
			sel_id = 0;
		if(typeof(key)=="undefined")
			key = 1;
		
		qdata = {};
		qdata.action = "getVisionDD";
		qdata.value = value;
		qdata.element = element;
		qdata.ptInterface = true;
		qdata.segTypeVal = (call=="admin")?value:$("#seg_type_id_"+key+"_"+vision_val+"_lensD").val();
		qdata.designVal = (call=="admin")?0:$("#design_id_"+key+"_"+vision_val+"_lensD").val();
		qdata.materialVal = (call=="admin")?0:$("#material_id_"+key+"_"+vision_val+"_lensD").val();
		qdata.treatmentVal = (call=="admin")?0:$("#a_r_id_"+key+"_"+vision_val+"_lensD").selectedValuesString();
		
		$.ajax({
			method: 'POST',
			data: qdata,
			url: top.WRP+'/interface/patient_interface/ajax.php',
			success: function(dt){
				dt = $.parseJSON(dt);
				
				var opt = "";
				if(Object.keys(dt).length>=0){
					var opts = "";
					$.each(dt, function(option, data){
						if(option=="design"){
							opts = '<option value="0">Please Select</option>';
							$.each(data, function(key1, data1){
								selected = "";
								if((call=="admin" && itemVals.design==data1) || (call=="ptInterface" && (qdata.designVal==data1 || itemVals.design==data1))){
									selected="selected";
								}
								opts += '<option value="'+data1+'" '+selected+'>'+key1+'</option>';
							});
							
							$("#design_id_"+key+"_"+vision_val+"_lensD").html(opts).removeAttr('disabled');
							if( vision_selected=='ou' && vision_val=='od' ){
								/*if($("#design_id_"+key+"_os_lensD").val()!='0')*/
									$("#design_id_"+key+"_os_lensD").html(opts).removeAttr('disabled');
							}
							
							if(qdata.designVal>0){
								
								$("#design_id_"+key+"_"+vision_val+"_lensD").trigger('change');
								if( vision_selected=='ou' && vision_val=='od' ){
									/*if($("#design_id_"+key+"_os_lensD").val()!='0')*/
										$("#design_id_"+key+"_os_lensD").trigger('change');
								}
							}
						}
						if(option=="material"){
							opts = '<option value="0">Please Select</option>';
							$.each(data, function(key1, data1){
								selected = "";
								if((call=="admin" && itemVals.material==data1) || (call=="ptInterface" && (qdata.materialVal==data1 || itemVals.material==data1))){
									selected="selected";
								}
								opts += '<option value="'+data1+'" '+selected+'>'+key1+'</option>';
							});
							
							$("#material_id_"+key+"_"+vision_val+"_lensD").html(opts).removeAttr('disabled');
							if( vision_selected=='ou' && vision_val=='od' ){
								/*if($("#material_id_"+key+"_os_lensD").val()!='0')*/
									$("#material_id_"+key+"_os_lensD").html(opts).removeAttr('disabled');
							}
							
							if(qdata.materialVal>0){
								
								$("#material_id_"+key+"_"+vision_val+"_lensD").trigger('change');
								if( vision_selected=='ou' && vision_val=='od' ){
									/*if($("#material_id_"+key+"_os_lensD").val()!='0')*/
										$("#material_id_"+key+"_os_lensD").trigger('change');
								}
							}
							else{
								qdata.treatmentVal = "";
							}
							
							if($("#material_id_"+key+"_"+vision_val+"_lensD").val()=="0"){
								qdata.treatmentVal = "";
							}
						}
						if(option=="treatment"){
							var options = [];
							var selectdVals = "";
							if(call=="admin"){
								//itemVals.treatment = itemVals.treatment.split(",");
								selectdVals = itemVals.treatment;
							}
							else if(call=="ptInterface" && !$.isArray(qdata.treatmentVal)){
								//qdata.treatmentVal =  qdata.treatmentVal.split(",");
								selectdVals = qdata.treatmentVal;
							}
							
							$.each(data, function(key1, data1){
								sel = false;
								/*if((call=="admin" && $.inArray(data1.id, itemVals.treatment) !== -1) || (call=="ptInterface" && $.inArray(data1.id, qdata.treatmentVal) !== -1)){
									sel = true;
								}*/
								options.push({ text: key1, value: data1, selected: sel});
							});
							
							$("#a_r_id_"+key+"_"+vision_val+"_lensD").multiSelectOptionsUpdate(options);
							$("#a_r_id_"+key+"_"+vision_val+"_lensD").changeSelected(selectdVals);
							if( vision_selected=='ou' && vision_val=='od' ){
								$("#a_r_id_"+key+"_os_lensD").multiSelectOptionsUpdate(options);
								$("#a_r_id_"+key+"_os_lensD").changeSelected(selectdVals);
							}
						}
					});
				}
			},
			complete: function(){
				changeLensPosLabel();
				calculate_all();
			}
		});
	}
}
/*End fetch dropdown values - VisionWeb*/
/*End Filter VisionWeb Values*/

/*Change selected vision value for the order*/
function changeVision(key){
	"use strict";
	
	var visionVal = $('#lens_vision_'+key+'_lensD').val();
	visionVal = visionVal.toLowerCase();
	
	var od_ids = new Array('seg_type_id_'+key+'_od_lensD', 'design_id_'+key+'_od_lensD', 'material_id_'+key+'_od_lensD');
	var os_ids = new Array('seg_type_id_'+key+'_os_lensD', 'design_id_'+key+'_os_lensD', 'material_id_'+key+'_os_lensD');//pos_lens_item_name_1_lensD
	
	var disable	= [];
	var enable	= [];
	
	if(visionVal === 'ou'){
		enable = od_ids.concat(os_ids);
		$( '#lens_vision_'+key+'_lensD' ).attr('class', 'ouColor lens_vision');
		/*$( '#qty_'+key+'_lensD' ).val(2);*/
	}
	else if( visionVal === 'od' ){
		disable	= os_ids;
		enable	= od_ids;
		$( '#lens_vision_'+key+'_lensD' ).attr('class', 'blueColor lens_vision');
		/*$( '#qty_'+key+'_lensD' ).val(1);*/
	}
	else if( visionVal === 'os' ){
		disable	= od_ids;
		enable	= os_ids;
		$( '#lens_vision_'+key+'_lensD' ).attr('class', 'greenColor lens_vision');
		/*$( '#qty_'+key+'_lensD' ).val(1);*/
	}
	
	$.each(disable, function(i, val){
		$('#'+val).val(0).prop('disabled', true).trigger('change');
	});
	
	$.each(enable, function(i, val){
		$('#'+val).prop('disabled', false);
	});
}

/*Copy values from OD to os - Lenses*/
function blVals(key){
	"use strict";
	
	$( '#loading' ).show();
	
	var visionVal = $('#lens_vision_'+key+'_lensD').val();
	visionVal = visionVal.toLowerCase();
	
	if(visionVal !== "ou"){
		top.falert('Please change vision to OU', true);
		return;
	}
	
	/*Seg Type*/
	var val1 = $('#seg_type_id_'+key+'_od_lensD').val();
	$('#seg_type_id_'+key+'_os_lensD').val(val1);
	if(val1 !== '' && val1 !== '0'){
		pos_row_display(key, '2_'+key+'_lens_display', val1, 'in_lens_type', 'os', false);
		//$('#seg_type_id_'+key+'_os_lensD').val(val1).trigger('change');
	}
	
	/*Design*/
	var content = $('#design_id_'+key+'_od_lensD').html();
	$('#design_id_'+key+'_os_lensD').html(content);
	
	val1 = $('#design_id_'+key+'_od_lensD').val();
	$('#design_id_'+key+'_os_lensD').val(val1).removeAttr('disabled');
	if(val1 !== '' && val1 !== '0'){
		pos_row_display(key, '2_'+key+'_design_display', val1, 'in_lens_design', 'os', false);
		//$('#design_id_'+key+'_os_lensD').trigger('change');
	}
	
	/*Material*/
	content = $('#material_id_'+key+'_od_lensD').html();
	$('#material_id_'+key+'_os_lensD').html(content);
	
	val1 = $('#material_id_'+key+'_od_lensD').val();
	$('#material_id_'+key+'_os_lensD').val(val1).removeAttr('disabled');
	if(val1 !== '' && val1 !== '0'){
		pos_row_display(key, '2_'+key+'_material', val1, 'in_lens_material', 'os', false);
		//$('#material_id_'+key+'_os_lensD').trigger('change');
	}
	
	/*Treatment*/
	var options = [];
	$.each($( '#a_r_id_'+key+'_od_lensD  + div.multiSelectOptions > label:not(.selectAll)' ), function(key, obj){
		var key = $(obj).children('input').val();
		var text = $(obj).text();
		options.push({ text: text, value: key});
	});
	$('#a_r_id_'+key+'_os_lensD').multiSelectOptionsUpdate(options);
	
	val1 = $( '#a_r_id_'+key+'_od_lensD' ).selectedValuesString();
	$( '#a_r_id_'+key+'_os_lensD' ).changeSelected(val1, true);
	
	$( '#loading' ).hide();
}
function show_stock_image(key){
	var large_path = $('#stock_image_'+key).attr('large');
	$('#stock_large_image > #stock_large').prop('src', large_path);
	$('#stock_large_image').fadeIn();
}
function showStockDetails(obj, key){
	if(typeof(stockFramesItems[key-1])!='undefined'){
		var elemtnt = $(obj).siblings('.stockDetails')[0];
		var height = $(elemtnt).height();
		$(elemtnt).css('top', '-'+height+'px');
		$(elemtnt).stop().fadeTo(500,1);
	}
}
function hideStockDetails(obj){
	$(obj).siblings('.stockDetails').stop().fadeTo(500,0).hide();
}
	
/*function bindCustomName(){
	$('.custom_item_name').change(function(){
		var row_num=$(this).attr('data-row');
		var custom_name=$('#item_name_'+row_num+'_other').val();
		var other_name=$(this).val();
		if(other_name)$('#itemDescription_frame_'+row_num).val(other_name);
		else $('#itemDescription_frame_'+row_num).val(custom_name);
	});
}
bindCustomName();*/
</script>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>
<div id="stock_large_image">
	<img id="stock_large" style="" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/frame_stock/no_image_xl.jpg" />
	<img id="stock_hide" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="$('#stock_large_image').hide();" />
</div>
<!-- container -->
<input type="hidden" id="delItemId" />
</body>
</html>