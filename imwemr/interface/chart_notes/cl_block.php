<?php

include_once("../../config/globals.php");
//include_once("menu_data.php");

$dateFormat= get_sql_date_format();

function getSimpleMenu(){
    
}

function getColorsFromDB(){
	$clColorResult = imw_query("select color_name from contactlensecolor");
	$clColorArray = array();
	while($clColorRow = imw_fetch_assoc($clColorResult)){
		$clColorArray[] = $clColorRow['color_name'];
	}
	return $clColorArray;
}

function getCLMenuArray($menu){
    if($menu == 'dva'){
        return array('20/15', '20/20', '20/25', '20/30', '20/40', '20/50', '20/60', '20/70', '20/80', '20/100', '20/150', '20/200', '20/300', '20/400', '20/600', '20/800', 'CF', 'CF 1ft', 'CF 2ft', 'CF 3ft', 'CF 4ft', 'CF 5ft', 'CF 6ft', 'HM', 'LP', 'LP c p', 'LP s p', 'NLP', 'F&F', 'F/(F)', '2/200', 'CSM', 'Enucleation', 'Prosthetic', 'Pt Uncoopera', 'Unable', '5/200');
    }else if($menu == 'nva'){
        return array('20/20(J1+)', '20/25(J1)', '20/30(J2)', '20/40(J3)', '20/32(J4)', '20/50(J5)', '20/60(J6)', '20/70(J7)', '20/63(J8)', '20/80', '20/100(J10)', '20/200(J16)', '20/400', '20/800', 'APC 20/30', 'APC 20/40', 'APC 20/60', 'APC 20/80', 'APC 20/100', 'APC 20/160', 'APC 20/200', 'CSM', '(C)SM', 'C(S)M', 'CS(M)', 'C(S)(M)', '(C)(S)M', '(C)S(M)', '(C)(S)(M)', 'F&F', 'Unable');
    }else if($menu == 'color'){
        return getColorsFromDB();
    }else if($menu == 'blend'){
        return array('Light', 'Medium', 'Heavy');
	}else if($menu == 'comfort'){
        return array('Comfortable', 'Uncomfortable', 'Dry', 'Itchy', 'Feel Edges');
	}else if($menu == 'movement'){
        return array('>1/2 mm', '=1/2 mm', '<1/2 mm', 'Loose', 'Tight', 'Good');
	}else if($menu == 'condition'){
        return array('Clean', 'Deposits', 'Tear', 'Other');
	}else if($menu == 'position'){
        return array('Centered', 'Superior', 'Inferior', 'Nasal', 'Temporal', 'Clear Limbus', 'Over Limbus');
	}else if($menu == 'fluoresceinpatter'){
        return array('Pooling', 'Baring', 'Parallel');
	}else if($menu == 'invertedlids'){
        return array('Clean', 'Papillae', 'Mucous');
	}
	
    return array('');
}

function getMenus($menu, $element)
{
    $menuString = "";
    $menuString .= '<div id="div_'.$menu.'" class="input-group-btn menu menu_acuitiesMrDis">';
    $menuString .= '<button type="button" class="btn dropdown-toggle" style="z-index:0;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-trgt-id="'.$element.'" tabindex="-1"><span class="caret"></span></button>';
    $menuString .= '<ul class="dropdown-menu  dropdown-menu-right">';
    $menuArray = getCLMenuArray($menu);
    for($i = 0; $i < sizeof($menuArray);$i++){
		//$menuString .= '<li><a href="javascript:void(0);" onclick="selectItemFromMenu(\''.$menuArray[$i].'\',\''.$element.'\');">'.$menuArray[$i].'</a></li>';
		$menuString .= '<li onclick="selectItemFromMenu(\''.$menuArray[$i].'\',\''.$element.'\');"><a href="javascript:void(0);" >'.$menuArray[$i].'</a></li>';
    }
    $menuString .= '</ul>';
    $menuString .= '</div>';
    return $menuString;
}

function searchManufacturer($trm){
	$arrManufac = array();
	//$lensListQry= "SELECT clmk.* FROM contactlensemake clmk WHERE manufacturer like '".$trm."%' OR style like '".$trm."%' OR (style='' AND type like '".$trm."%')				
	//			order by clmk.make_id";
	$lensListQry= "SELECT clmk.make_id, clmk.base_curve, clmk.diameter, clmk.manufacturer, clmk.style, clmk.type FROM contactlensemake clmk WHERE (manufacturer like '".$trm."%' OR style like '".$trm."%' OR (style='' AND type like '".$trm."%')) AND clmk.del_status = '0' order by clmk.make_id";
	$lensListRes = imw_query($lensListQry) or die(imw_error());				
	$lensListNumRow = imw_num_rows($lensListRes);
	if($lensListNumRow>0) {
		$i=0;
		while($lensListRow=imw_fetch_array($lensListRes)) {			
			//ARRAY FOR TYPEAHEAD
			//if($lensListRow['del_status']=='0' && ($lensListRow['source']=='0' || $lensListRow['source']=='')){
				$styleType = ''; $sep='';
				$styleType=addslashes($styleType);
				if($lensListRow['manufacturer']!=''){ $styleType = $lensListRow['manufacturer']; $sep='-';}
				if($lensListRow['style']!=''){ $styleType.=$sep.$lensListRow['style']; $sep='-';}
				if($lensListRow['type']!=''){ $styleType.=$sep.$lensListRow['type']; }
				
				/*
				$arrManufac[]=$styleType;
				$arrManufacId[]=$lensListRow['make_id'];
				$arrManufacInfo[$lensListRow['make_id']]=$lensListRow['base_curve'].'~'.$lensListRow['diameter'];
				*/
				$arrManufac[]=array("value"=>$styleType,"id"=>$lensListRow['make_id'],"info"=>$lensListRow['base_curve'].'~'.$lensListRow['diameter']);
			//}
		}
	}
	return $arrManufac;	
}
if(isset($_GET["elem_formAction"]) && $_GET["elem_formAction"]=="TypeAhead"){
	$term = $_GET["term"];
	$ar_tmp = searchManufacturer($term);	
	echo json_encode($ar_tmp);
	exit();
}
echo "<!-- SCL DRWAWING --> ";
$clws_id ='';
$readonly='';
$chgBehave = 'onfocus="javascript:checkDOS(this.id);"';
$visClass='';
$latestTrialNo=$trialNo=0;
$orderExists=0;

$cylSign=$GLOBALS["def_cylinder_sign_cl"];

//$disTrialNo='disabled="disabled"';
$sessFormID = $_SESSION['finalize_id'];
if($_SESSION['form_id'] != ''){
    $sessFormID = $_SESSION['form_id'];
}

$res_chart_master= imw_query("SELECT cl_order,date_of_service from chart_master_table where patient_id='".$_SESSION["patient"]."' and id = '".$sessFormID."'");
$arr_chart_master = imw_fetch_assoc($res_chart_master);
$saveTypeList = array('Evaluation','Fit','Refit','CL Check','Update Trial','Take Home CL','Current CL','Current Trial','Final');

$elem_statusElements='';
if($_SESSION["patient"]>0 && $sessFormID>0){
	$sql = "SELECT status_elements FROM chart_vis_master WHERE patient_id = '".$_SESSION["patient"]."' AND form_id = '".$sessFormID."' ";
	$rs = imw_query($sql);
	$res=imw_fetch_array($rs);
	$elem_statusElements=$res['status_elements'];
	unset($rs);
}

function vis_getStatus1($nm) {
	global $elem_statusElements;
	return (strpos($elem_statusElements,$nm."=1,")!==false) ? " active " : "inact";
}

function getCLCPTPracticeCode(){
	$arrCPTIds=array();
	$rs=imw_query("Select cpt_cat_id FROM cpt_category_tbl WHERE LOWER(cpt_category)='contact lens'");
	$res=imw_fetch_array($rs);
	$cl_cat_id=$res['cpt_cat_id'];
	if($cl_cat_id>0){
		$qryCPT = imw_query("Select cpt_fee_id, cpt4_code, cpt_prac_code from cpt_fee_tbl WHERE cpt_cat_id='".$cl_cat_id."' ORDER BY cpt_prac_code");
		while($qryCPTRes = imw_fetch_array($qryCPT)){
			//$arrCPTCodes[$qryCPTRes['cpt_fee_id']] = $qryCPTRes['cpt_prac_code'];
			$arrCPTIds[$qryCPTRes['cpt_fee_id']] = $qryCPTRes['cpt_fee_id'];
		}
	}
	return $arrCPTIds;
}

function getCPTDefaultCharges($cptFeeId=''){
	
	$arrCPTIds = getCLCPTPracticeCode();
	$strCPTIds = implode(',', $arrCPTIds);
	
	//GET ID OF DEFAULT FEE COLUMN
	$rs=imw_query("Select fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)='default'");
	$res=imw_fetch_array($rs);
	$defaultFeeId = $res['fee_table_column_id'];
	
	//GET DEFAULT CPT CHARGES	
	if(sizeof($arrCPTIds)>0){
		$cptFeeTableQry = "Select cpt_fee_id,cpt_fee FROM cpt_fee_table WHERE fee_table_column_id='".$defaultFeeId."' AND cpt_fee_id IN(".$strCPTIds.")";
		$cptFeeTableRes = imw_query($cptFeeTableQry);
		while($cptFeeTableRow = imw_fetch_array($cptFeeTableRes)){
			$arrDefaultCPTFee[$cptFeeTableRow["cpt_fee_id"]] = $cptFeeTableRow["cpt_fee"];
		}
	}
	return $arrDefaultCPTFee;
}

function getLensManufacturer(){
	$arrLensManuf= array();
	$arrManufac = $arrManufacId = $arrManufacInfo = array();
	//$lensListQry= "SELECT clmk.*, cpttbl.cpt_fee_id, cpttbl.cpt4_code, cpttbl.cpt_prac_code, cpttbl.cpt_prac_code FROM contactlensemake clmk 
//LEFT JOIN cpt_fee_tbl cpttbl ON cpttbl.cpt_fee_id = clmk.cpt_fee_id order by clmk.make_id";

	$lensListQry = "SELECT clmk.make_id, clmk.cpt_fee_id, clmk.manufacturer, clmk.style, clmk.type, clmk.base_curve, clmk.diameter, cpttbl.cpt_fee_id, cpttbl.cpt4_code, cpttbl.cpt_prac_code, cpttbl.cpt_prac_code FROM contactlensemake clmk LEFT JOIN cpt_fee_tbl cpttbl ON cpttbl.cpt_fee_id = clmk.cpt_fee_id where clmk.del_status = '0' order by clmk.make_id";

	$lensListRes = imw_query($lensListQry) or die(imw_error());				
	$lensListNumRow = imw_num_rows($lensListRes);
	if($lensListNumRow>0) {
		$i=0;
		while($lensListRow=imw_fetch_array($lensListRes)) {
			$arrLensManuf[$lensListRow['make_id']]['cpt_fee_id'] = $lensListRow['cpt_fee_id'];
			$arrLensManuf[$lensListRow['make_id']]['cpt4Code'] = $lensListRow['cpt4_code'];
			$arrLensManuf[$lensListRow['make_id']]['cpt_prac_code'] = $lensListRow['cpt_prac_code'];
			if($lensListRow['type']==''){
				$lensManuf = $lensListRow['manufacturer'].'-'.$lensListRow['style'];
			}else{
				$lensManuf = $lensListRow['manufacturer'].'-'.$lensListRow['style'].'-'.$lensListRow['type'];
			}
			
			if($lensManuf[strlen($lensManuf)-1] == "-"){
				$lensManuf = substr($lensManuf, 0, (strlen($lensManuf) - 1));
			}
			$arrLensManuf[$lensListRow['make_id']]['det'] = $lensManuf;

			if($lensListRow['price']<=0 && !empty($lensListRow['cpt_fee_id'])){
				$cptQry 	= "SELECT cpt_fee FROM cpt_fee_table WHERE cpt_fee_id ='".$lensListRow['cpt_fee_id']."' AND fee_table_column_id=1";
				$cptRes 	= imw_query($cptQry) or die(imw_error());				
				$cptNumRow	= imw_num_rows($cptRes);
				if($cptNumRow>0) {
					$cptRow	=imw_fetch_array($cptRes);
					$arrLensManuf[$lensListRow['make_id']]['price'] = $cptRow['cpt_fee'];
				}
			}else{
				$arrLensManuf[$lensListRow['make_id']]['price'] = $lensListRow['price'];
			}
			
			//ARRAY FOR TYPEAHEAD
			//if(($lensListRow['source']=='0' || $lensListRow['source']=='')){
				$styleType = ''; $sep='';
				if($lensListRow['manufacturer']!=''){ $styleType = $lensListRow['manufacturer']; $sep='-';}
				if($lensListRow['style']!=''){ $styleType.=$sep.$lensListRow['style']; $sep='-';}
				if($lensListRow['type']!=''){ $styleType.=$sep.$lensListRow['type']; }
	
				$arrManufac[]=$styleType;
				$arrManufacId[]=$lensListRow['make_id'];
				$arrManufacInfo[$lensListRow['make_id']]=$lensListRow['base_curve'].'~'.$lensListRow['diameter'];
			//}
		}
	}
	return array('arrLensManuf'=>$arrLensManuf, 'arrManufac'=>$arrManufac, 'arrManufacId'=>$arrManufacId, 'arrManufacInfo'=>$arrManufacInfo);
}

//GET MANUF VALUES FOR B/C AND DIAMETER
function getLensManufVals(){
	$arrLensManufValues= array();
	$lensListQry= "SELECT clmk.make_id,clmk.base_curve, clmk.diameter FROM contactlensemake clmk 
LEFT JOIN cpt_fee_tbl cpttbl ON cpttbl.cpt_fee_id = clmk.cpt_fee_id WHERE clmk.base_curve<>'' OR clmk.diameter<>'' order by clmk.make_id";
	$lensListRes = imw_query($lensListQry) or die(imw_error());				
	$lensListNumRow = imw_num_rows($lensListRes);
	if($lensListNumRow>0) {
		while($lensListRow=imw_fetch_array($lensListRes)) {
			$arrLensManufValues[$lensListRow['make_id']] = $lensListRow['make_id'].'$'.$lensListRow['base_curve'].'$'.$lensListRow['diameter'];
		}
	}
	return $arrLensManufValues;
}
$arrLensManufValues = getLensManufVals();
$strLensManufValues = implode(',',$arrLensManufValues);

function setTitleRows($clType){
//	alert($clType);
	if($clType == 'scl' || $clType == 'prosthesis' || $clType == 'no-cl') {	
		$titleRow = '<tr>';
		$titleRow.= '<td class="txt_10b valignBottom" style="width:3%;"></td>';
		$titleRow.= '<td class="txt_10b valignBottom" style="width:8%;">B. Curve</td>';
		$titleRow.= '<td class="txt_11b alignLeft" style="width:8%;">Diameter</td>';
		$titleRow.= '<td class="txt_11b alignLeft" style="width:8%;">Sphere</td>';
		$titleRow.= '<td class="txt_11b nowrap alignLeft" style="width:8%;">Cylinder</td>';
		$titleRow.= '<td class="txt_11b alignLeft" style="width:8%;">Axis</td>';
		$titleRow.= '<td class="txt_11b alignLeft" style="width:8%;">Color</td>';
		$titleRow.= '<td class="txt_11b alignLeft" style="width:8%;">ADD</td>';
		$titleRow.= '<td class="txt_11b alignLeft nowrap" style="width:8%;">DVA</td>';
		$titleRow.= '<td class="txt_11b alignLeft nowrap" style="width:8%;">NVA</td>';
		$titleRow.= '<td class="txt_11b alignLeft nowrap" style="width:22%;">Type</td>';
		$titleRow.= '<td class="txt_11b alignLeft nowrap" style="width:2%;"></td>';
		$titleRow.= '</tr>';
	}
	if($clType == 'rgp' || $clType == 'rgp_soft' || $clType == 'rgp_hard')
	{
		$titleRow = '<tr>';
		$titleRow.= '<td style="width:3%;"></td>';
		$titleRow.= '<td class="txt_11b nowrap" style="width:7%;">BC</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">Diameter</td>';						
		$titleRow.= '<td class="txt_11b" style="width:7%;">Power</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">Cylinder</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">Axis</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">OZ</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">CT</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">Color</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">Add</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">DVA</td>';
		$titleRow.= '<td class="txt_11b" style="width:7%;">NVA</td>';
		$titleRow.= '<td class="txt_11b" style="width:17%;">Type</td>'; 
		$titleRow.= '<td class="txt_11b" style="width:2%;"></td>';
		$titleRow.= '</tr>';
	}
	if($clType == 'cust_rgp')
	{
		$titleRow= '<tr class="txt_11b">';
		$titleRow.= '<td style="width:3%;"></td>';
		$titleRow.= '<td style="width:5%;">BC</td>';							
		$titleRow.= '<td style="width:5%;">Diameter</td>';
		$titleRow.= '<td style="width:5%;">Power</td>';	
		$titleRow.= '<td style="width:5%;">Cylinder</td>';
		$titleRow.= '<td style="width:5%;">Axis</td>';
		$titleRow.= '<td style="width:5%;">2&deg;/W</td>';
		$titleRow.= '<td style="width:5%;">3&deg;/W</td>';
		$titleRow.= '<td style="width:5%;">PC/W</td>';
		$titleRow.= '<td style="width:5%;">OZ</td>';
		$titleRow.= '<td style="width:5%;">CT</td>';
		$titleRow.= '<td style="width:5%;">Color</td>';
		$titleRow.= '<td style="width:5%;">Blend</td>';
		$titleRow.= '<td style="width:5%;">Edge</td>';
		$titleRow.= '<td style="width:5%;">Add</td>';
		$titleRow.= '<td style="width:5%;">DVA</td>';
		$titleRow.= '<td style="width:5%;">NVA</td>';
		$titleRow.= '<td style="width:14%;">Type</td>';
		$titleRow.= '<td style="width:2%;"></td>';
		$titleRow.= '</tr>';
	}
	return $titleRow;
}

//--------------------------------

//GET ALL LENS MANUFACTURER IN ARRAY
$arr = getLensManufacturer();
//print_r($arr['arrLensManuf']);
$arrLensManuf=$arr['arrLensManuf'];
$arrManufac=$arr['arrManufac'];
$arrManufacId=$arr['arrManufacId'];
$arrManufacInfo=$arr['arrManufacInfo'];
json_encode($arrManufac);
json_encode($arrManufacId);
json_encode($arrManufacInfo);

echo "<script>\n";
echo "var arrManufac=[];\n";
echo "var arrManufacId=[];\n";
echo "var arrManufacInfo=[];\n";
echo "arrManufac = ".json_encode($arrManufac).";\n";
echo "arrManufacId = ".json_encode($arrManufacId).";\n";
echo "arrManufacInfo = ".json_encode($arrManufacInfo).";\n";
echo "var lensNameArray = [];\n";
echo "var lensDetailsArray = [];\n";
$lensNamesArray = array_keys($arrManufac);
for($i = 0;$i < sizeof($lensNamesArray);$i++){
    echo "lensNameArray['".$lensNamesArray[$i]."'] = '".$arrManufacId[$lensNamesArray[$i]]."';\n";
}

$lensDetArray = array_keys($arrManufacInfo);
for($i = 0;$i < sizeof($lensDetArray);$i++){
    echo "lensDetailsArray['".$lensDetArray[$i]."'] = '".$arrManufacInfo[$lensDetArray[$i]]."';\n";
}
// Get dva array
$dvaArray = getCLMenuArray('dva');
echo "var dvaJSArray = new Array();\n";
for($i = 0;$i < sizeof($dvaArray);$i++){
    echo "dvaJSArray[".$i."] = '".$dvaArray[$i]."';\n";
}

// Get nva array
$nvaArray = getCLMenuArray('nva');
echo "var nvaJSArray = new Array();\n";
for($i = 0;$i < sizeof($nvaArray);$i++){
    echo "nvaJSArray[".$i."] = '".$nvaArray[$i]."';\n";
}

echo "</script>\n";
//$arrLensManuf = $arr[];

//die("349");

//GET FEE OF DEFAULT CL CHARGES
$arrDefaultCPTFee = getCPTDefaultCharges();

//	GET LATEST TRIAL NO 
$trialRs= imw_query("SELECT clws_type, clws_trial_number  from contactlensmaster where patient_id='".$_SESSION["patient"]."' ORDER BY clws_id");
while($trialRes = imw_fetch_array($trialRs)){
	$clws_type_arr = explode(',', $trialRes["clws_type"]);
	if($trialRes["clws_trial_number"] > $trialNo){ $trialNo = $trialRes["clws_trial_number"]; }
	if(in_array('Final', $clws_type_arr)){ $trialNo=0; }
}
$latestTrialNo=(is_array($clws_type_arr) && in_array('Current Trial', $clws_type_arr)) ? (($trialNo==0) ? 1 : $trialNo) : $trialNo+1;

// Get latest contact lens worksheet by clws id
$qry="Select clws_id from contactlensmaster WHERE patient_id = '".$_SESSION['patient']."' AND form_id = '".$sessFormID."' and del_status=0 ORDER BY clws_id DESC LIMIT 0,1";
$rs= imw_query($qry) or die(imw_error());
$res = imw_fetch_array($rs);
$clws_id = $res['clws_id'];

if($clws_id==''){
	//$qry="Select clws_id from contactlensmaster WHERE patient_id = '".$_SESSION['patient']."' AND form_id < '".$sessFormID."' ORDER BY clws_id DESC LIMIT 0,1";
	$qry = "SELECT c2.clws_id FROM chart_master_table c1 
			INNER JOIN contactlensmaster c2 ON c2.form_id = c1.id   
			WHERE c1.patient_id = '".$_SESSION['patient']."' AND c1.id < '".$sessFormID."' 
			AND c1.purge_status='0' AND c1.delete_status='0' and c2.del_status=0 
			ORDER BY dos DESC, clws_id DESC LIMIT 0,1"; 
	$rs= imw_query($qry) or die(imw_error());
	$res = imw_fetch_array($rs);
	$clws_id = $res['clws_id'];
}
if(isset($_REQUEST['copySheetId']) && $_REQUEST['copySheetId']>0){
	$clws_id = $_REQUEST['copySheetId'];
}
// CHECK IF PRINT ORDER EXIST FOR WORKSHEET
if($clws_id>0){
	$rs=imw_query("Select print_order_id from clprintorder_master WHERE clws_id='".$clws_id."'");
	if(imw_num_rows($rs) > 0){ $orderExists=1; }
}
// SET ARRAY VALUES
	$cl_records_yes = 'no';
	$trial_no_old='0';
	$evalutaionFieldSettings=array();
	$cl_query_lastsheet = "SELECT clws_id,currentWorksheetid,dos, clGrp, form_id FROM contactlensmaster WHERE clws_id='".$clws_id."'";
	$cl_result_lastsheet = imw_query($cl_query_lastsheet);
	if($cl_result_lastsheet && imw_num_rows($cl_result_lastsheet)==1){
		$cl_records_yes = 'yes';
		$cl_rs_lastsheet = imw_fetch_array($cl_result_lastsheet);
		$cl_id_lastsheet = $cl_rs_lastsheet['clws_id'];
		$cl_dos = $cl_rs_lastsheet['dos'];
		$cl_form_id = $cl_rs_lastsheet['form_id'];
		$clGrp = $cl_rs_lastsheet['clGrp'];
		$currentWorksheetid = $cl_rs_lastsheet['currentWorksheetid'];

		$GetDataQuery= "SELECT contactlensmaster.*, DATE_FORMAT( contactlensmaster.clws_savedatetime , '%m-%d-%Y' ) AS worksheetdate , contactlensworksheet_det.* FROM contactlensmaster LEFT JOIN contactlensworksheet_det	ON contactlensworksheet_det.clws_id = contactlensmaster.clws_id where contactlensmaster.clws_id='".$clws_id."' ORDER BY contactlensworksheet_det.clEye, contactlensworksheet_det.id ASC";
		$CLRsData=imw_query($GetDataQuery);
		$i=0;
		while($CLRes= imw_fetch_assoc($CLRsData)){
			$CLResData[$i]= $CLRes;
			
			//FIELD SETTINGS FOR EVALUATION THAT WHCIH FIELDS(SCL/RGP) WILL BE FETCHED FOR OD AND OS.
			$eye= strtoupper($CLRes['clEye']);	
			if(!$evalutaionFieldSettings[$eye]){
				$evalutaionFieldSettings[$eye]=$CLRes['clType'];
			}

			$i++;
		}
		
		$cl_comment = $CLResData[0]['cl_comment'];
		$LabelsTrial_current = $clws_type_old = $clws_type_sheet= $CLResData[0]['clws_type'];
		$clws_types_arr = explode(',', $clws_type_old);
		$temp_clws_type_arr = $clws_types_arr;
		$arr_charges_id = explode(',',$CLResData[0]['charges_id']);
		$cpt_fee = $CLResData[0]['cpt_evaluation_fit_refit'];
		$latestTrialNo = $CLResData[0]['clws_trial_number'];
		
		if(in_array('Current Trial', $clws_types_arr)){ 
			$LabelsTrial_current.= " #".$CLResData[0]['clws_trial_number'];
			$trial_no_old=$CLResData[0]['clws_trial_number'];
		}
		//display contact lens
		$ctmpCL = " in "; 
	}

	$replenishment = "";
	$wearScheduler = "";
	$disinfecting = "";
	
	//IF COPY FROM CALLED THEN DOENS NOT CHANGE SOME OF VALUES OF EXISTING SHEET.
	if($_REQUEST['copySheetId']!='' && $_REQUEST['copied_to_clws_id']!='' && $_REQUEST['copied_to_clws_id']>0){

		$qry = "SELECT cl_comment, clws_type, clws_trial_number, form_id, charges_id, cpt_evaluation_fit_refit, clws_trial_number
		FROM contactlensmaster WHERE clws_id='".$_REQUEST['copied_to_clws_id']."'";
		$rs=imw_query($qry);

		if(imw_num_rows($rs)>0){
			$res=imw_fetch_assoc($rs);

			$cl_comment = $res['cl_comment'];
			$LabelsTrial_current = $clws_type_old =$clws_type_sheet= $res['clws_type'];
			$clws_types_arr = explode(',', $clws_type_old);
			$temp_clws_type_arr = $clws_types_arr;
			$arr_charges_id = explode(',',$res['charges_id']);
			$cpt_fee = $res['cpt_evaluation_fit_refit'];
			$latestTrialNo = $res['clws_trial_number'];
	
			if(in_array('Current Trial', $clws_types_arr)){ 
				$LabelsTrial_current.= " #".$res['clws_trial_number'];
				$trial_no_old=$res['clws_trial_number'];
			}

			$cl_form_id=$res['form_id'];
		}

		//VALUES GET FROM COPY FROM SHEET
		$replenishmentResult = imw_query("select wear_scheduler, replenishment, disinfecting from contactlensmaster where clws_id='".$_REQUEST['copySheetId']."'");
		$replenishRow = imw_fetch_array($replenishmentResult);
		$replenishment = $replenishRow['replenishment'];
		$wearScheduler = $replenishRow['wear_scheduler'];
		$disinfecting = $replenishRow['disinfecting'];
	}

	//	SET OTHER SAVE SAVED VALUE
	if($LabelsTrial_current!=''){
		$tempArr = explode(',', $clws_type_old);
		$otherSaveVal='';
		for($i=0;$i<sizeof($tempArr); $i++){
			if(!in_array($tempArr[$i], $saveTypeList)){
				$otherSaveArr[]=$tempArr[$i];
			}
		}
		$otherSaveVal= is_array($otherSaveArr) ? implode(',', $otherSaveArr) : "" ;
	}

	
	if($sessFormID > $cl_form_id){
		//$readonly='readonly="readonly"';
		//$chgBehave='onfocus="javascript:checkDOS(this.id);"';
	}

	//if($sessFormID == $cl_form_id){
	//	$visClass ='active';
	//}
	?>
<script>
function selectItemFromMenu(value, element){
	//$("#" + element).val(value);
	var inputElem = $("input[name=" + element+"]");
	if(inputElem[0] === undefined){
	}else{
		inputElem[0].value = value;
		$(inputElem[0]).focus();
	}
}
function checkLensMakeType(element){
	var typeField = $(element);
	var fld = typeField.attr('id');
	if(typeField.val() == 0){
		$("#" + fld + "ID").val("");
	}
	
	var fld1 = fld.replace("OD", "OS");
	console.log(fld1, fld, $("#"+fld1).length);
	$("#"+fld1).trigger("click");
	
}
</script>
<div  id="ContactLens" class="collapse mb5 <?php echo $ctmpCL;?>" >

	<div class="examsectbox">
        <div class="header">
            <div class="head-icons form-inline">
                <ul>
                    <!--
                    <li class="form-inline">
                        <div class="kvalue img_acuity"><img src="/imedicwarer8-dev/library/images/ar_icon.png" alt="" onClick="callPopup('popup_mr.php','popMR','1345','685');" /></div>
                    </li>
                    -->						
                    <li>
                        <h2 class="clickable">Contact Lens</h2>
                    </li>
                    <li style="margin-right:20px; margin-left:20px">
						<button type="button" class="btn btn-primary btn-sm" style="margin-bottom:5px;" data-toggle="collapse" onclick="top.icon_popups('contact_lens_worksheet');">CL Worksheet</button>                    
                    </li>
                    <li style="padding-right:150px;">
						<?php if($LabelsTrial_current!=''){
                                $dispLabels=$LabelsTrial_current;
                                if(strlen($dispLabels)>30){ $dispLabels=substr($dispLabels,0,30).'..';}
                            ?>
                            <div style="color:#000; margin-top:1px;">&nbsp;<?php echo $dispLabels;?>&nbsp;</div>
                        <?php } ?>                    
                    </li>
					<li>
					   <?php 
                       $AllMenuesqry="SELECT currentWorksheetid,clws_trial_number,clws_id,DATE_FORMAT( `dos`,'".$dateFormat."') as PreviousDOS, DATE_FORMAT( `clws_savedatetime`,'".$dateFormat."') as savedDate, clws_type, form_id, del_status  from contactlensmaster where patient_id='".$_SESSION["patient"]."' ORDER BY form_id DESC, clws_id DESC";
                       $resAllMenues=imw_query($AllMenuesqry);
                       $strCopyFromOptions ='';
                        if($resAllMenues){
                            $numRows=imw_num_rows($resAllMenues);
                            if($numRows>0){
                                while($resRowALL=imw_fetch_assoc($resAllMenues)){
                                    $sel=''; $selCopy='';
                                    $LabelsTrial=$resRowALL["clws_type"];
                                    $clws_types_arr1 = explode(",", $resRowALL["clws_type"]);
                                                    
                                    if(in_array('Current Trial', $clws_types_arr1)){
                                        $LabelsTrial.= ' #'.$resRowALL["clws_trial_number"];
                                    }
                                    if($resRowALL['clws_id']==$_GET['copySheetId']){ $selCopy="selected";}
                                        $strCopyFromOptions.= '<option value="'.$resRowALL["clws_id"].'" '.$selCopy.' '.$colorStyle.' >'.$resRowALL["savedDate"].' ('.$LabelsTrial.')&nbsp;</option>';
                                    $oldFormId= $resRowALL['form_id'];
                                }
                            }
                        }
                        ?>                    
                    </li>
                    <li class="copyfrom">Copy from:&nbsp;
                        <select id="copyFromId" name="copyFromId" onChange="loadCopyFromWorkSheet(this.value, '','<?php echo ($_REQUEST['copied_to_clws_id']>0)? $_REQUEST['copied_to_clws_id']:$clws_id;?>');" class="form-control minimal" style="margin-bottom:3px;width:150px;max-width:150px;">
                        <?php print($strCopyFromOptions);?>
                        </select>
                    </li>
                    <li>
						<input type="button"  class="btn btn-success btn-sm"  onClick="stopClickBubble();showSave();hideAppletsDiv('OD');" name="clSave" id="clSave" value="Save">                    
                    </li>
                    <li>
		            <?php 
						//$clws_types_arr = array();
						if($LabelsTrial_current == ''){
							$clws_type_sheet = 'Evaluation';
							$clws_types_arr = explode(',',$clws_type_sheet);
						}
						?>
						<input type="hidden" size="300" name="clws_types" id="clws_types" value="<?php echo $clws_type_sheet; ?>">
                    </li>
                    <li style="float:right">
                        <button type="button" class="btn btn-default" style="margin-top:4px;margin-right:3px;" onclick="printRx('1','<?php echo $clws_id;?>');" data-toggle="tooltip" title="" data-original-title="Print CL Rx">
	                        <span class="glyphicon glyphicon-print"></span>
                        </button>                        
                    </li>
                    
                    <li class="cl_drawing_od" style="background:none; border:none; float:right">
                        <img src="../../library/images/space.gif" id="DrawingPngImg_od"  onClick="stopClickBubble();showAppletsDiv('od','',dgi('idoc_drawing_id_od').value,dgi('description_A_od').value,dgi('description_B_od').value);" style="cursor:hand;" title="Show/Hide Drawing" >
                                                     
                    </li>
                    <li class="ml10 cl_drawing_os" style="visibility:hidden; float:right">
						OS:
                        <img src="../../library/images/space.gif" id="DrawingPngImg_os"   onClick="stopClickBubble();showAppletsDiv('os','',dgi('idoc_drawing_id_os').value,dgi('description_A_os').value,dgi('description_B_os').value);" style="cursor:hand;" title="Show/Hide OS Drawing" >
                                                       
                    </li>
                    
                    <li style="float:right">
						<input type="checkbox" name="cl_order" id="cl_order" value="1" <?php if($arr_chart_master['cl_order']) echo "checked";?> onClick="stopClickBubble();" class="<?php if(($arr_chart_master['cl_order'])){ echo vis_getStatus1("cl_order"); } ?>" style="margin-top:12px;">
						<label for="cl_order"> CL-Req </label>                                        
                    </li>
					<li>	
						<?php
                         if($orderExists==1){?>
                            <div style="float:left; background:#CCC; color:#000; margin-top:1px; margin-left:15px;">
                                &nbsp;<?php echo 'Order Done';?>&nbsp;
                            </div>
                        <?php }?>
					</li>
                </ul>
            </div>
        </div>
        <!-- END HEADER ROW -->
        
        <!-- SAVE POPUP OLD -->
		<!--<div id="buttonSavediv" style="left:330px; top:190px; position:absolute; background-color:#ecdFFc; border:solid 1px #81A2C9; z-index:1005px; display:none; z-index:100; color:black" onclick="stopClickBubble();">
		<?php 
		if($elem_per_vo != "1"){ ?>
		<table style="border:0px; width:220px; padding:2px; border-collapse:separate; border-spacing:2px;" class="table borderless">
		  <tr>
			<td class="txt_11b" colspan="2">Save CL worksheet to :</td>
		</tr>
	<?php	
	/*//this is in contecct lens.css
        <style>
		.la_bg{
			background-image:url(images/la.jpg);
			/*background-color:#996699;* /
			background-attachment: scroll;
			background-repeat: repeat-x;
			background-position: left;
		}
		</style>
	*/	
	?>	
        <tr class="la_bg white_color txt_11b"><td colspan="2" style=" background-color:#7C0B86; background-image:url(images/la.jpg);background-attachment: scroll;background-repeat: repeat-x;background-position: left;">
        <div style="width:300px; padding-top:2px; background-color:#9D6B9E" class="fl white_color">
                        <?php 
						$chargeRS=imw_query("Select * from cl_charges WHERE del_status=0 ORDER BY cl_charge_id");
						while($chargeRES = imw_fetch_array($chargeRS)){
                            $cptPrice = $arrDefaultCPTFee[$chargeRES['cpt_fee_id']];
							$cptPrice = ($cptPrice<=0) ? '0' : $cptPrice; 
							$arrCLCharges[$chargeRES['name']] = $cptPrice.'~'.$chargeRES['cl_charge_id'];
						}
							$s=1;
						    foreach($arrCLCharges as $name => $data){
								list($price,$charges_id) = explode('~', $data);
						?>	
                            <div class="txt_11b fl white_color active" style="margin-top:1px; color:white; background-color:#9D6B9E" ><input type="checkbox" id="chargesChk<?php echo $s;?>" clPrice="<?php echo $price;?>" name="clws_charges[]" onClick="javascript:checkSingle('chargesChk<?php echo $s;?>', '<?php echo $price;?>','clws_charges','sheet');"  value="<?php echo $charges_id;?>" <?php if(is_array($arr_charges_id) && in_array($charges_id,$arr_charges_id)){echo("checked");}?>></div>
                            <div class="alignRight fl valignMiddle white_color" style="padding-top:3px;color:white; background-color:#9D6B9E"><?php echo $name;?>&nbsp;</div>
						<?php $s++; } ?>
        </div>
        </td></tr>
		  <tr>
			<td class="txt_11b" colspan="2" style="border-bottom:1px solid #81A2C9">
            &nbsp;&nbsp;&nbsp;Charges:&nbsp;$<input type="text" class="txt_10" id="cpt_evaluation_fit_refit" name="cpt_evaluation_fit_refit"  value="<?php echo $cpt_fee;?>" style="text-align:right; width:60px;" onKeyDown="javasript:checkNumber(this.value);" >
            </td>
		</tr>        
		<?php	
            $s=1;
			$chargeRS=imw_query("Select * from cl_charges WHERE del_status=0 ORDER BY cl_charge_id");
			while($chargeRES = imw_fetch_array($chargeRS)){
				$arrCLCharges[$chargeRES['name']] = $chargeRES['price'].'~'.$chargeRES['cl_charge_id'];
			}
            foreach($arrCLCharges as $name => $price){
        ?>	
            <tr class="txt_11b">
                <td class="txt_11b nowrap"><?php echo $name;?></td>
                <td class="txt_11b"><input type="checkbox" id="EvaluationChk<?php echo $s;?>" name="clws_type"  value="<?php echo $name;?>" onClick="javascript:stopClickBubble();checkSingle('EvaluationChk<?php echo $s;?>', '<?php echo $price;?>', 'clws_type','popup');" <?php if(in_array($name, $clws_types_arr)){echo("checked");}?>> </td>
            </tr>
        <?php $s++; } ?>

		<?php if($_GET["copySheetId"]!=""){ ?>
		<tr>
			<td class="txt_11b nowrap">Update Trial</td>
			<td class="txt_11b nowrap"><input type="checkbox" id="CurrentTrialChk" name="clws_type" onClick="javascript:stopClickBubble();checkSingle('CurrentTrialChk','0','clws_type','popup');"  value="Update Trial" <?php if(in_array("Update Trial", $clws_types_arr)){echo("checked");}?>> </td>
		</tr>
		
		<?php } ?>
		<tr>
			<td class="txt_11b nowrap">Take Home CL</td>
			<td class="txt_11b nowrap"><input type="checkbox" id="takeHomeCLChk" name="clws_type" onClick="javascript:stopClickBubble();checkSingle('takeHomeCLChk','0','clws_type','popup');"  value="Take Home CL" <?php if(in_array("Take Home CL", $clws_types_arr)){echo("checked");}?>> </td>
		</tr>

		<tr>
			<td class="txt_11b nowrap">Current CL</td>
			<td class="txt_11b nowrap"><input type="checkbox" id="CurrentCLChk" name="clws_type" onClick="javascript:stopClickBubble();checkSingle('CurrentCLChk','0','clws_type','popup');"  value="Current CL" <?php if(in_array("Current CL", $clws_types_arr)){echo("checked");}?>> </td>
		</tr>

		<tr>
			<td class="txt_11b">Trial</td>
			<td class="txt_11b"><input type="checkbox" id="NewTrialChk" name="clws_type" onClick="javascript:stopClickBubble();enbDisTrialNo1(this.id);checkSingle('NewTrialChk','0','clws_type','popup');"  value="Current Trial" <?php if(in_array("Current Trial", $clws_types_arr)){echo("checked");}?>> 
            <input type="text" name="clws_trial_number" id="clws_trial_number" onClick="javascript:stopClickBubble();" value="<?php if($latestTrialNo>0){echo($latestTrialNo);}?>" size="2" class="txt_10" <?php if(!in_array("Current Trial", $clws_types_arr) && count($clws_types_arr)>0) echo 'disabled'; ?>></td>
		</tr>
		<tr>
			<td class="txt_11b">Final</td>
			<td class="txt_11b"><input type="checkbox" id="FinalChk" name="clws_type" onClick="javascript:stopClickBubble();checkSingle('FinalChk','0','clws_type','popup');"  value="Final" <?php if(in_array("Final", $clws_types_arr)){echo("checked");}?>> </td>
	 	 </tr>
		 <?php // } ?>
		  <tr>
            <td class="txt_11b">Other</td>
			<td class="txt_11b"><input type="text" name="otherSave" id="otherSave" style="width:110px" value="<?php echo $otherSaveVal;?>" onBlur="javascript:stopClickBubble();checkSingle('otherSave','0','clws_type','popup');"></td>
		 </tr> 
		  <tr>
			<td class="txt_11b" colspan="2" style="border-bottom:1px solid #81A2C9"></td>
		</tr> 
		<tr>
			<td class="alignCenter" colspan="2" style="padding-top:5px;">
            <input type="button" name="done_button"  class="dff_button" id="DoneBtn"  value="Done" onClick="stopClickBubble();return Contactlensformsubmit();">&nbsp;&nbsp;
			<input type="button"  class="dff_button"  id="CloseSave" value="Cancel"  onClick="stopClickBubble();showSave('close');">
            </td>
		</tr>
		<?php } ?>
	</table>
	</div>-->        
    	<!-- END SAVE POPUP OLD -->
    	
    	<!-- SAVE POPUP NEW -->
		<div id="buttonSavediv" class="modal contact_lens_save_box" onclick="stopClickBubble();" style="height:auto;overflow-y:scroll;display:none;z-index:500;border:0px;">
		<?php 
		if($elem_per_vo != "1"){ ?>
		<table style="border:solid 1px #81A2C9; width:220px; padding:2px; border-collapse:separate; border-spacing:2px; height:auto;overflow-y:scroll;background:#FFFFFF;" class="table">
    		<tr>
    			<td class="txt_11b" style="width:100%;background:#1b9e95;height:30px;color:#ffffff;" colspan="2">&nbsp;Save CL worksheet to:</td>
    		</tr>
            <tr class="la_bg white_color txt_11b">
            	<td colspan="2">
                    <div style="width:300px; padding-top:2px;" class="fl white_color">
                        <?php 
            			$chargeRS=imw_query("Select * from cl_charges WHERE del_status=0 ORDER BY cl_charge_id");
            			while($chargeRES = imw_fetch_array($chargeRS)){
            				$cptPrice = $arrDefaultCPTFee[$chargeRES['cpt_fee_id']];
            				$cptPrice = ($cptPrice<=0) ? '0' : $cptPrice; 
            				$arrCLCharges[$chargeRES['name']] = $cptPrice.'~'.$chargeRES['cl_charge_id'];
            			}
            				$s=1;
            			    foreach($arrCLCharges as $name => $data)
            			    {
            					list($price,$charges_id) = explode('~', $data);
            			?>	
                            <div class="txt_11b fl white_color active" style="margin-top:1px; color:white; background-color:white;">
                            	<input type="checkbox" id="chargesChk<?php echo $s;?>" clPrice="<?php echo $price;?>" name="clws_charges[]" onClick="javascript:checkSingle('chargesChk<?php echo $s;?>', '<?php echo $price;?>','clws_charges','sheet');"  value="<?php echo $charges_id;?>" <?php if(is_array($arr_charges_id) && in_array($charges_id,$arr_charges_id)){echo("checked");}?>>
                            	<span style="color:black;"><?php echo $name;?></span>
                           	</div>
            			<?php $s++; } ?>
                    </div>
            	</td>
            </tr>
		  <tr>
			<td class="txt_11b" colspan="2">
			<?php
                if(is_nan(floatval($cpt_fee))){
                    $cpt_fee = "0.00";
                }
			?>
            Charges:&nbsp;$&nbsp;<input type="text" class="txt_10" id="cpt_evaluation_fit_refit" name="cpt_evaluation_fit_refit"  value="<?php echo $cpt_fee;?>" style="text-align:right;width:60px;border:solid 1px gray;" onKeyDown="javasript:checkNumber(this.value);" />
            </td>
		</tr>
		<tr><td colspan="2" style="height:1px;border-bottom:1px solid #81A2C9;padding-bottom:5px;"></td></tr>
		<?php	
            $s=1;
			$chargeRS=imw_query("Select * from cl_charges WHERE del_status=0 ORDER BY cl_charge_id");
			while($chargeRES = imw_fetch_array($chargeRS)){
				$arrCLCharges[$chargeRES['name']] = $chargeRES['price'].'~'.$chargeRES['cl_charge_id'];
			}
            foreach($arrCLCharges as $name => $price){
        ?>	
            <tr class="txt_11b">
                <td class="txt_11b nowrap"><?php echo $name;?></td>
                <td class="txt_11b"><input type="checkbox" id="EvaluationChk<?php echo $s;?>" name="clws_type"  value="<?php echo $name;?>" onClick="javascript:stopClickBubble();checkSingle('EvaluationChk<?php echo $s;?>', '<?php echo $price;?>', 'clws_type','popup');" <?php if(in_array($name, $clws_types_arr)){echo("checked");}?>> </td>
            </tr>
        <?php $s++; } ?>

		<?php if($_GET["copySheetId"]!=""){ ?>
		<tr>
			<td class="txt_11b nowrap">Update Trial</td>
			<td class="txt_11b nowrap"><input type="checkbox" id="CurrentTrialChk" name="clws_type" onClick="javascript:stopClickBubble();checkSingle('CurrentTrialChk','0','clws_type','popup');"  value="Update Trial" <?php if(in_array("Update Trial", $clws_types_arr)){echo("checked");}?> ></td>
		</tr>
		<?php } ?>
		<tr>
			<td class="txt_11b nowrap">Take Home CL</td>
			<td class="txt_11b nowrap"><input type="checkbox" id="takeHomeCLChk" name="clws_type" onClick="javascript:stopClickBubble();checkSingle('takeHomeCLChk','0','clws_type','popup');"  value="Take Home CL" <?php if(in_array("Take Home CL", $clws_types_arr)){echo("checked");}?>> </td>
		</tr>

		<tr>
			<td class="txt_11b nowrap">Current CL</td>
			<td class="txt_11b nowrap"><input type="checkbox" id="CurrentCLChk" name="clws_type" onClick="javascript:stopClickBubble();checkSingle('CurrentCLChk','0','clws_type','popup');"  value="Current CL" <?php if(in_array("Current CL", $clws_types_arr)){echo("checked");}?>> </td>
		</tr>

		<tr>
			<td class="txt_11b">Trial</td>
			<td class="txt_11b">
			
			<input type="checkbox" id="NewTrialChk" name="clws_type" onClick="javascript:stopClickBubble();enbDisTrialNo1(this.id);checkSingle('NewTrialChk','0','clws_type','popup');checkTrial(this, 'clws_trial_number');"  value="Current Trial" <?php if(in_array("Current Trial", $clws_types_arr)){echo("checked");}?>> 
            
			<input type="text" name="clws_trial_number" id="clws_trial_number" onClick="javascript:stopClickBubble();" value="<?php if($latestTrialNo>0){echo($latestTrialNo);}?>" size="5" class="txt_10" style="border:solid 1px gray;" <?php if(!in_array("Current Trial", $clws_types_arr) && count($clws_types_arr)>0) echo 'disabled'; ?>></td>
		</tr>
		<tr>
			<td class="txt_11b">Final</td>
			<td class="txt_11b"><input type="checkbox" id="FinalChk" name="clws_type" onClick="javascript:stopClickBubble();checkSingle('FinalChk','0','clws_type','popup');"  value="Final" <?php if(in_array("Final", $clws_types_arr)){echo("checked");}?> /></td>
	 	 </tr>
		 <?php // } ?>
		  <tr>
            <td class="txt_11b">Other</td>
			<td class="txt_11b"><input type="text" name="otherSave" id="otherSave" style="width:110px;border:solid 1px gray;" value="<?php echo $otherSaveVal;?>" onBlur="javascript:stopClickBubble();checkSingle('otherSave','0','clws_type','popup');"></td>
		 </tr> 
		  <tr>
			<td class="txt_11b" colspan="2" style="border-bottom:1px solid #81A2C9"></td>
		</tr> 
		<tr>
			<td class="alignCenter" style="padding-top:5px; margin-top:10px; text-align:right; padding-right:10px;">
            	<input type="button" name="done_button" class="btn btn-success" id="DoneBtn" value="Done" onClick="stopClickBubble();showSave('close');return Contactlensformsubmit();">&nbsp;&nbsp;
            </td>
            <td class="alignCenter" style="padding-top:5px; padding-left:10px; margin-top:10px;">
            	<input type="button" class="btn btn-danger" id="CloseSave" value="Cancel" onClick="stopClickBubble();showSave('close');">
            </td>
		</tr>
		<?php } ?>
	</table>
	</div>        
    <!-- END SAVE POPUP NEW -->
        
        <div class="clearfix"></div>
        <div class="default">
        
            <!-- ctrlListVals IS JUST FOR MENU B/C AND DIAMETER VALUES -->
            <input type="hidden" name="ctrlListVals" id="ctrlListVals" value="<?php echo $strLensManufValues;?>">
            <input type="hidden" name="txtTotOD" id="txtTotOD" value="1">
            <input type="hidden" name="txtTotOS" id="txtTotOS" value="1">
            <input type="hidden" name="newSave" id="newSave" value="">
            <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $_SESSION['patient'];?>">    
            <input type="hidden" name="provider_id" id="provider_id" value="<?php echo $_SESSION['authId'];?>">
            
            <?php
                $sessFormID = $_SESSION['finalize_id'];
    			if($_SESSION['form_id'] != ''){
    				$sessFormID = $_SESSION['form_id'];
				}
				
				if($_REQUEST['copySheetId']!='' && $_REQUEST['copied_to_clws_id']!='' && $_REQUEST['copied_to_clws_id']>0){
					if($sessFormID==$cl_form_id){
						$clws_id_hidden=$_REQUEST['copied_to_clws_id'];
					}else{
						$clws_id_hidden='';
					}
				}else{
					if($sessFormID==$cl_form_id){
						$clws_id_hidden=$cl_id_lastsheet;
					}					
				}
            ?>
            <input type="hidden" name="clws_id" id="clws_id" value="<?php echo $clws_id_hidden; ?>">

            <input type="hidden" name="clws_type_old" id="clws_type_old" value="<?php echo $clws_type_old;?>">
            <input type="hidden" name="trial_no_old" id="trial_no_old" value="<?php echo $trial_no_old;?>">
            <input type="hidden" name="currentWorksheetid" id="currentWorksheetid" value="<?php echo($currentWorksheetid);?>">
            <!--<input type="hidden" name="newSheetMode" id="newSheetMode" value="<?php echo($_GET["mode"]);?>">-->
            <input type="hidden" name="prvdos" id="prvdos" value="<?php echo strtotime($cl_dos);?>">
            <input type="hidden" name="clGrp" id="clGrp" value="<?php echo $clGrp;?>">
            <input type="hidden" name="otherSaveVal" id="otherSaveVal" value="<?php echo $otherSaveVal;?>">
            
           	<input type="hidden" name="chartNoteDOS" id="chartNoteDOS" value="<?php echo $arr_chart_master['date_of_service'];?>">
            <input type="hidden" name="worksheetDOS" id="worksheetDOS" value="<?php echo $cl_dos;?>">
            <input type="hidden" name="chartNoteFORMID" id="chartNoteFORMID" value="<?php echo $sessFormID; ?>">
			<input type="hidden" name="worksheetFORMID" id="worksheetFORMID" value="<?php echo $cl_form_id; ?>">
			<input type="hidden" name="recordSave" id="recordSave" value="saveTrue">
            <?php
            $txtTotOD = $txtTotOS =$txtTotOU = 0; $dispSCLEval = $dispRGPEva =0;
            $realTotOD = $realTotOS =$realTotOU = 0;
            $dispEvalOD = $dispEvalOS =$dispEvalOU =0;
            $mainArrSize = is_array($CLResData) ? sizeof($CLResData) : 0 ;
            //echo 'mainArrSize='.$mainArrSize;
            for($i=0; $i<$mainArrSize; $i++)
            {
                if(strtolower($CLResData[$i]['clEye'])=='od') {
                    $txtTotOD+=1;	$realTotOD+=1; 
                }else if(strtolower($CLResData[$i]['clEye'])=='os') {
                    $txtTotOS+=1;	$realTotOS+=1;
                }
                if($CLResData[$i]['clType']=='scl' || $CLResData[$i]['clType']=='prosthesis' || $CLResData[$i]['clType']=='no-cl') { $dispSCLEval =1; }
                if($CLResData[$i]['clType'] == 'rgp' || $CLResData[$i]['clType'] == 'rgp_soft' || $CLResData[$i]['clType'] == 'rgp_hard') { $dispRGPEval =1; }
            }
    
            if($txtTotOD ==0){
                $txtTotOD =1;
            }else { $dispEvalOD =1; }
            
            if($txtTotOS ==0){
                $txtTotOS=1;
            }else { $dispEvalOS=1; }
    
    
            ?>
            <!--</div>-->  
            
            <div id="cl_block">   
		<?php
		$sqlManufact = "Select distinct(manufacturer) from contactlensemake order by 
				`contactlensemake`.`manufacturer` ASC";
		$resManuf = imw_query($sqlManufact);
		$arrSubOptions = array();	$strLen =0;	$DDMenuWidth = 120;
		if(imw_num_rows($resManuf) > 0){
			while($rowManuf = imw_fetch_array($resManuf)){
				$manufacturer = $rowManuf['manufacturer'];
				$sqlStyle = "select distinct(style),type from contactlensemake where manufacturer = '$manufacturer' 
							order by `contactlensemake`.`style` ASC";
				$resStyle = imw_query($sqlStyle);
				$manLen = strlen($manufacturer);
				while($rowStyle = imw_fetch_array($resStyle)){
					$arrSubOptions[] = array($rowStyle["style"]."-".$rowStyle["type"],$xyz,$rowStyle["style"]."-".$rowStyle["type"]);
					$arrSubOptions1 = $rowStyle["style"];
					$stringAllManufact.="'".str_replace("'","",$arrSubOptions1)."',";
					$strL= strlen($rowStyle["style"]."-".$rowStyle["type"]);
					if($manLen > $strL) { $strL = $manLen; }
					if($strL > $strLen) { $strLen = $strL; }
				}
				$arrMainValues[] = array($manufacturer,$arrSubOptions);
				$manufacturer = '';unset($arrSubOptions);
			}
		}	
	
		$cl_query_lastsheet = "SELECT clws_id FROM contactlensmaster WHERE clws_id='".$clws_id."'";
		$cl_result_lastsheet = imw_query($cl_query_lastsheet);
		$cl_rs_lastsheet = imw_fetch_array($cl_result_lastsheet);
		$cl_id_lastsheet = $cl_rs_lastsheet['clws_id'];

		$GetDataQuery = "SELECT contactlensmaster.*, DATE_FORMAT( contactlensmaster.clws_savedatetime , '%m-%d-%Y' ) AS worksheetdate , contactlensworksheet_det.* FROM contactlensmaster LEFT JOIN contactlensworksheet_det ON contactlensworksheet_det.clws_id = contactlensmaster.clws_id where contactlensmaster.clws_id='".$clws_id."'";
		$CLRsData = imw_query($GetDataQuery);
		$i=0;
		$arrSavedRowIds=array();
		while($CLRes=imw_fetch_assoc($CLRsData)){
			$replenishment = $CLRes['replenishment'];
			$wearScheduler = $CLRes['wear_scheduler'];
			$disinfecting = $CLRes['disinfecting'];
			$CLResData[$i] = $CLRes;

			//$arrSavedRowIds[$CLRes['clEye']][$CLRes['id']]=$CLRes['front_end_row_Id'];
			$i++;
		}
		$clComments = "";
		$clCommentId = 0;
		$qry="Select id, comment from cl_comments where delete_status='0'";

		if($_REQUEST['copySheetId']!='' && $_REQUEST['copied_to_clws_id']!=''){
			$qry.=" AND cl_sheet_id='".$_REQUEST['copied_to_clws_id']."'";
		}else{
			$qry.=" AND cl_sheet_id='".$clws_id."'";
		}
		$qry.=" ORDER BY id asc limit 1";
        $clCommentsResult = imw_query($qry);
        $clCommentsRow = imw_fetch_assoc($clCommentsResult);
        $clComments = $clCommentsRow['comment'];
        $clCommentId = $clCommentsRow['id'] ;
	if($sessFormID!=$cl_form_id){
		$clCommentId = 0;
	}
	
		$mainArrSize = 0 ;
		$clEvalString = "";
		if(is_array($CLResData))
		{
			array_unshift($CLResData, "0");
			//'Size='.$mainArrSize = sizeof($CLResData);
			$mainArrSize = sizeof($CLResData);
				
			//GETTING & SETTING DRAWING ARRAY FOR IMAGES
			//$arrDrawing=array();
			foreach($CLResData as $drawingData){
				if(isset($drawingData['clEye'])){
					if($drawingData['clEye']=='OD'){
						if($drawingData['elem_SCLOdDrawingPath']!='' || $drawingData['idoc_drawing_id']>0){
							//ADDING IN SCAN DATABASE TABLE IS NOT EXIST THERE
							if($drawingData['elem_SCLOdDrawingPath']!='' && $drawingData['idoc_drawing_id']<=0){
								$qry="Insert INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing SET 
								toll_image='imgCorneaCanvas',
								drawing_for='DrawCL',
								drawing_image_path='".$drawingData['elem_SCLOdDrawingPath']."',
								row_created_by='1',
								row_created_date_time='".date('Y-m-d H:i:s')."',
								patient_id='".$_SESSION['patient']."',
								patient_form_id='".$drawingData['form_id']."',
								row_visit_dos='".$drawingData['dos']."'";
								imw_query($qry);
								$drawingData['idoc_drawing_id']=imw_insert_id();
								
								$qry="Update contactlensworksheet_det SET idoc_drawing_id='".$drawingData['idoc_drawing_id']."' WHERE id='".$drawingData['id']."'";
								imw_query($qry);
							}
						}
					}
					if($drawingData['clEye']=='OS'){
						if($drawingData['elem_SCLOsDrawingPath']!='' || $drawingData['idoc_drawing_id']>0){
							//ADDING IN SCAN DATABASE TABLE IS NOT EXIST THERE
							if($drawingData['elem_SCLOsDrawingPath']!='' && $drawingData['idoc_drawing_id']<=0){
								$qry="Insert INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing SET 
								toll_image='imgCorneaCanvas',
								drawing_for='DrawCL',
								drawing_image_path='".$drawingData['elem_SCLOsDrawingPath']."',
								row_created_by='1',
								row_created_date_time='".date('Y-m-d H:i:s')."',
								patient_id='".$_SESSION['patient']."',
								patient_form_id='".$drawingData['form_id']."',
								row_visit_dos='".$drawingData['dos']."'";
								imw_query($qry);
								$drawingData['idoc_drawing_id']=imw_insert_id();
								
								$qry="Update contactlensworksheet_det SET idoc_drawing_id='".$drawingData['idoc_drawing_id']."' WHERE id='".$drawingData['id']."'";
								imw_query($qry);
							}
						}
					}
				}
			}
		}
				?>
                <div id="odRows">
				<?php 
				//PROTECTING JAVASCRIPT ERROR
				$arrDrawingData['od']['idoc_drawing_id']='';
				$arrDrawingData['od']['desc_od']='';
				$arrDrawingData['od']['desc_os']='';
				$arrDrawingData['os']['idoc_drawing_id']='';
				$arrDrawingData['os']['desc_od']='';
				$arrDrawingData['os']['desc_os']='';

				//pre($CLResData);
				if($mainArrSize > 1)
				{
					$oldClType = '';	$j=1;

					for($i=1; $i <= $mainArrSize; $i++)
					{
						$dispTitle =0;	$topPad='';	
						$odos = strtolower($CLResData[$i]['clEye']);
						if($odos == 'od')
						{
							/*$minVal= min(array_values($arrSavedRowIds['OD']));
							if($minVal>0){
								$j=$arrSavedRowIds['OD'][$CLResData[$i]['id']];
							}*/

							$clType = $CLResData[$i]['clType'];
							if($clType != $oldClType) { 
								$dispTitle=1;	$topPad="padding-top:22px"; 
								$divHeight = "45px"; $rgpHeight = "21px";
							}else {
								$divHeight = "30px"; $rgpHeight = "30px";
							}
							$dispTitle=1;	$topPad="padding-top:22px"; 
							$divHeight = "45px"; $rgpHeight = "21px";
						
							//CHEKING IF DRAWING DATA EXIST OR NOT
							if($CLResData[$i]['idoc_drawing_id']>0){
/*								$qry1="Select id FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing WHERE id='".$CLResData[$i]['idoc_drawing_id']."' AND drawing_image_path!=''";
								$rs1=imw_query($qry1);
								if(imw_num_rows($rs1)){
*/									$arrDrawingData['od']['idoc_drawing_id']=$CLResData[$i]['idoc_drawing_id'];
									$arrDrawingData['od']['desc_od']=$CLResData[$i]['corneaSCL_od_desc'];
									$arrDrawingData['od']['desc_os']=$CLResData[$i]['corneaSCL_os_desc'];
								//}
							}//unset($rs1);

						if($clType =='scl' || $clType =='prosthesis' || $clType =='no-cl'){
							$CLResData[$i]['SclTypeOD'] = $arrLensManuf[$CLResData[$i]['SclTypeOD_ID']]['det'];
						?>

                        <input type="hidden" name="clDetIdOD<?php echo $j; ?>" value="<?php echo $CLResData[$i]['id'];?>">                 
                          <div id="CLRowOD<?php echo $j;?>" style="display:block; clear:both; height:<?php echo $divHeight;?>;" class="scl_bg">
                           <div style="float:left;width:7%;<?php echo $topPad; ?>;">
                              <select name="clTypeOD<?php echo $j;?>" class="form-control minimal <?php echo vis_getStatus1("clTypeOD".$j);?>" id="clTypeOD<?php echo $j;?>" onChange="changeCLType(this);changeRow(this.value, 'od', 'CLRowOD', document.getElementById('txtTotOD').value, '<?php echo $j;?>',arrManufac, arrManufacId, arrManufacInfo);"  <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;">
                                <option value="scl" <?php if($clType=='scl')echo 'selected';?>>SCL</option>
								<option value="rgp_soft">RGP Soft</option>
								<option value="rgp_hard">RGP Hard</option>
                                <option value="cust_rgp">Custom RGP</option>
                                <option value="prosthesis" <?php if($clType=='prosthesis')echo 'selected';?>>Prosthesis</option>
                                <option value="no-cl" <?php if($clType=='no-cl')echo 'selected';?>>No-CL</option>
                              </select>
                           </div>
                           <div style="float:left;width:91%;">
								<table class="table borderless" id="sclTable" style="width:99%;">
                                <tbody>
							  	<?php echo setTitleRows('scl'); ?>
							  	<tr class="alignLeft">
								<td class="odcol">OD</td>
								<td>
								  <input class="form-control <?php echo vis_getStatus1("SclBcurveOD".$j);?>" type="text" id="SclBcurveOD<?php echo $j;?>" name="SclBcurveOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclBcurveOD'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' ); copyValuesODToOS('SclBcurveOD<?php echo $j;?>','SclBcurveOS<?php echo $j;?>');"></td> 
								<td class="alignLeft">
								  <input id="SclDiameterOD<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("SclDiameterOD".$j);?>" name="SclDiameterOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclDiameterOD'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' ); copyValuesODToOS('SclDiameterOD<?php echo $j;?>','SclDiameterOS<?php echo $j;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');">
								</td>
								<td class="alignLeft">
								 <input type="text" name="SclsphereOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclsphereOD'];?>" class="form-control  <?php echo vis_getStatus1("SclsphereOD".$j);?>"  id="SclsphereOD<?php echo $j;?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');" >
								</td>  
								<td class="nowrap alignLeft" >
								 <input id="SclCylinderOD<?php echo $j;?>" type="text" name="SclCylinderOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclCylinderOD'];?>"  class="form-control  <?php echo vis_getStatus1("SclCylinderOD".$j);?>"  style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');">
								</td> 
								<td class="alignLeft">
								 <input type="text" name="SclaxisOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclaxisOD'];?>" class="form-control  <?php echo vis_getStatus1("SclaxisOD".$j);?>"  id="SclaxisOD<?php echo $j;?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>', 'noDecimal');;" onKeyUp="check2BlurCL(this,'A','','CLRowOD<?php echo $j;?>');">
								</td>
								<td class="alignLeft nowrap">
									<div class="input-group" style="width:100%;">
										

										<input id="SclColorOD<?php echo $j; ?>" type="text" name="SclColorOD<?php echo $j;?>" value="<?php if($CLResData[$i]['SclColorOD']) echo $CLResData[$i]['SclColorOD']; ?>" class="form-control acuity inact <?php echo vis_getStatus1("SclColorOD".$j); ?>" <?php echo $chgBehave; ?> <?php echo $readonly; ?> onblur="this.click(); justify2DecimalCL(this, '<?php echo $cylSign; ?>' );"  onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j; ?>');" onfocus="setCursorAtEnd(this);"   autocomplete="off">

										<?php echo getMenus("color", "SclColorOD".$j);
								        //echo getSimpleMenu($ColorOptionsArray,"menu_clcolorOpts","RgpColorOD".$j,0,0,array("pdiv"=>"divWorkView"));
                                        ?>
                                	</div>
								</td> 
								<td class="alignLeft" >
									<input id="SclAddOD<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("SclAddOD".$j);?>" name="SclAddOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclAddOD'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' ); copyValuesODToOS('SclAddOD<?php echo $j;?>','SclAddOS<?php echo $j;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');">
								</td>
								
                                <td class="alignLeft nowrap">
                                	<div class="input-group" style="width:100%;">
                                		<input id="SclDvaOD<?php echo $j; ?>" type="text" name="SclDvaOD<?php echo $j;?>" value="<?php if($CLResData[$i]['SclDvaOD']) echo $CLResData[$i]['SclDvaOD']; else echo '20/'; ?>" class="form-control acuity inact <?php echo vis_getStatus1("SclDvaOD".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');this.click();" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');" onfocus="setCursorAtEnd(this);" autocomplete="off">
                                		<?php echo getMenus("dva", "SclDvaOD".$j); ?>
                                	</div>
                                <?php //echo getSimpleMenu($arrAcuitiesMrDis,"menu_acuitiesMrDis","SclDvaOD".$j,0,0,array("pdiv"=>"divWorkView")); ?>
                                </td>
                                
								<td class="alignLeft nowrap">
									<div class="input-group" style="width:100%;">
										<input type="text" name="SclNvaOD<?php echo $j;?>" id="SclNvaOD<?php echo $j;?>" value="<?php if($CLResData[$i]['SclNvaOD']) echo $CLResData[$i]['SclNvaOD']; else echo '20/'; ?>" class="form-control  <?php echo vis_getStatus1("SclNvaOD".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
										<?php //echo getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","SclNvaOD".$j,0,0,array("pdiv"=>"divWorkView")); ?>
										<?php echo getMenus("nva", "SclNvaOD".$j); ?>
									</div>
								</td> 
								<td class="alignLeft nowrap">
                                    <div  style="width:100%; float:left;" class="txt_11b alignLeft nowrap">
                                        
										<input type="text" name="SclTypeOD<?php echo $j;?>" id="SclTypeOD<?php echo $j;?>" class="typeAhead form-control <?php echo vis_getStatus1("SclTypeOD".$j);?>" style="width:100%; background-image:none;" value="<?php echo $CLResData[$i]['SclTypeOD'];?>" <?php echo $chgBehave;?> onKeyUp="checkLensMakeType(this);" onKeyDown="clearLensMakeId(this);" />
                                        
										<input type="hidden" name="SclTypeOD<?php echo $j;?>ID" id="SclTypeOD<?php echo $j;?>ID" value="<?php echo $CLResData[$i]['SclTypeOD_ID'];?>" funVars="worksheet~SclTypeOD<?php echo $j;?>~SclTypeOS<?php echo $j;?>~SclBcurveOD<?php echo $j;?>~SclDiameterOD<?php echo $j;?>">
                                   </div>
								</td>
							<?php if($readonly==''){ 
								?>                                
  								<td class="alignLeft nowrap">
                                        <?php if($j < $txtTotOD) {  ?>
                                        <figure><span id="imgOD<?php echo $j;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('CLRowOD<?php echo $j;?>');" title="Delete Row"></span></figure>
                                        <?php }else { ?>
                                        <figure><span id="imgOD<?php echo $j;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('clTypeOD<?php echo $j;?>').value, 'od', 'CLRowOD', 'imgOD', '<?php echo $j; ?>','10','', arrManufac, arrManufacId, arrManufacInfo);" title="Add More"></span></figure>                                    
                                        <?php } ?>
                                    <?php }?>
								</td>
							  </tr> 
                              </tbody>                   
							</table>                     
	            		   </div>	
						   </div>
						<?php } ?>

						<!-- RGP OD ROWS -->
						<?php if($clType =='rgp'|| $clType =='rgp_soft' || $clType =='rgp_hard'){
						  $CLResData[$i]['RgpTypeOD'] = $arrLensManuf[$CLResData[$i]['RgpTypeOD_ID']]['det'];
						?>
                        <input type="hidden" name="clDetIdOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['id'];?>">
						<div id="CLRowOD<?php echo $j;?>" class="rgp_bg" style="display:block; clear:both; height:<?php echo $rgpHeight;?>;" >
							<div style="float:left;width:7%; <?php echo $topPad; ?>">
							  <select name="clTypeOD<?php echo $j;?>" class="form-control minimal <?php echo vis_getStatus1("clTypeOD".$j);?>" id="clTypeOD<?php echo $j;?>" onChange="changeCLType(this);changeRow(this.value, 'od', 'CLRowOD', document.getElementById('txtTotOD').value, '<?php echo $j;?>',arrManufac, arrManufacId, arrManufacInfo);" form-control minimal <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;">
								<option value="scl">SCL</option>
								<?php if($clType =='rgp'){
									echo "<option value=\"rgp\" selected=\"selected\">RGP</option>";
								} ?>
								<option value="rgp_soft" <?php if($clType =='rgp_soft') { echo " selected=\"selected\""; } ?> >RGP Soft</option>
								<option value="rgp_hard" <?php if($clType =='rgp_hard') { echo " selected=\"selected\""; } ?> >RGP Hard</option>
								<option value="cust_rgp">Custom RGP</option>
                                <option value="prosthesis">Prosthesis</option>
                                <option value="no-cl">No-CL</option>
							  </select>
						   </div>
						   <div style="float:left;width:91%;">
							<table class="table borderless" id="rgp_table1" style="display:block;width:99%;">
							  <?php echo setTitleRows('rgp');?>  
							<tr class="alignLeft">
								<td class="odcol" style="width:3%;">OD</td>
								<td>
								
								<input class="form-control  <?php echo vis_getStatus1("RgpBCOD".$j);?>"  type="text" name="RgpBCOD<?php echo $j;?>" id="RgpBCOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpBCOD'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' ); copyValuesODToOS('RgpBCOD<?php echo $j;?>','RgpBCOS<?php echo $j;?>');"></td>
								
								
								<td><input type="text" name="RgpDiameterOD<?php echo $j;?>" id="RgpDiameterOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpDiameterOD'];?>" class="form-control  <?php echo vis_getStatus1("RgpDiameterOD".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' ); copyValuesODToOS('RgpDiameterOD<?php echo $j;?>','RgpDiameterOS<?php echo $j;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');"></td>												
								<td><input class="form-control  <?php echo vis_getStatus1("RgpPowerOD".$j);?>"  type="text" name="RgpPowerOD<?php echo $j;?>" id="RgpPowerOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpPowerOD'];?> " style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');"></td> 
                                <td><input class="form-control  <?php echo vis_getStatus1("RgpCylinderOD".$j);?>"  type="text" name="RgpCylinderOD<?php echo $j;?>" id="RgpCylinderOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCylinderOD'];?> " style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');"></td> 
                                <td><input class="form-control  <?php echo vis_getStatus1("RgpAxisOD".$j);?>"  type="text" name="RgpAxisOD<?php echo $j;?>" id="RgpAxisOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpAxisOD'];?> " style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>  onKeyUp="check2BlurCL(this,'A','','CLRowOD<?php echo $j;?>');"></td> 
								<td><input type="text" name="RgpOZOD<?php echo $j;?>" id="RgpOZOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpOZOD'];?>" class="form-control  <?php echo vis_getStatus1("RgpOZOD".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>></td> 
                                <td><input type="text" name="RgpCTOD<?php echo $j;?>" id="RgpCTOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCTOD'];?>" class="form-control  <?php echo vis_getStatus1("RgpCTOD".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>></td> 
								<td class="alignLeft nowrap">
									<div class="input-group" style="width:100%;">
										<input type="text" name="RgpColorOD<?php echo $j;?>" id="RgpColorOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpColorOD']; ?>" class="form-control  <?php echo vis_getStatus1("RgpColorOD".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;" onblur="this.click();">
										<?php echo getMenus("color", "RgpColorOD".$j);
								        //echo getSimpleMenu($ColorOptionsArray,"menu_clcolorOpts","RgpColorOD".$j,0,0,array("pdiv"=>"divWorkView"));
                                        ?>
                                	</div>
								</td> 
								<td><input id="RgpAddOD<?php echo $j;?>" type="text" class="form-control <?php echo vis_getStatus1("RgpAddOD".$j);?>" name="RgpAddOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpAddOD'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this,'<?php echo $cylSign;?>'); copyValuesODToOS('RgpAddOD<?php echo $j;?>','RgpAddOS<?php echo $j;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');"></td> 
								<td class="nowrap">
									<div class="input-group" style="width:100%;">
										<input id="RgpDvaOD<?php echo $j; ?>" type="text" name="RgpDvaOD<?php echo $j;?>" value="<?php if($CLResData[$i]['RgpDvaOD']!='') { echo $CLResData[$i]['RgpDvaOD']; }else{ echo '20/'; } ?>" class="form-control  <?php echo vis_getStatus1("RgpDvaOD".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();" >
										<?php echo getMenus("dva", "RgpDvaOD".$j);
		          						    //echo getSimpleMenu($arrAcuitiesMrDis,"menu_acuitiesMrDis","RgpDvaOD".$j,0,0,array("pdiv"=>"divWorkView"));
					           			?>
									</div>
								<!-- Acuities-->
								</td>
								<td class="nowrap">
									<div class="input-group" style="width:100%;">
										<input type="text" name="RgpNvaOD<?php echo $j; ?>" id="RgpNvaOD<?php echo $j;?>" value="<?php if($CLResData[$i]['RgpNvaOD']) echo $CLResData[$i]['RgpNvaOD']; else echo '20/'; ?>" class="form-control <?php echo vis_getStatus1("RgpNvaOD".$j); ?>" <?php echo $chgBehave; ?> <?php echo $readonly; ?> onblur="this.click();">
										<?php
                                            echo getMenus("nva", "RgpNvaOD".$j);
                                            //echo getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","RgpNvaOD".$j,0,0,array("pdiv"=>"divWorkView"));
                                        ?>
									</div>
								</td>
								<td class="nowrap">
                                <div style="width:100%; float:left;" class="txt_11b alignLeft nowrap" >
                                    <input type="text" name="RgpTypeOD<?php echo $j;?>" id="RgpTypeOD<?php echo $j;?>" class="typeAhead form-control <?php echo vis_getStatus1("RgpTypeOD".$j);?>" style="width:100%; background-image:none;" value="<?php echo $CLResData[$i]['RgpTypeOD'];?>" <?php echo $chgBehave;?> onKeyUp="checkLensMakeType(this);" onKeyDown="clearLensMakeId(this);" />
                                    
									<input type="hidden" name="RgpTypeOD<?php echo $j;?>ID" id="RgpTypeOD<?php echo $j;?>ID" value="<?php echo $CLResData[$i]['RgpTypeOD_ID'];?>" funVars="worksheet~RgpTypeOD<?php echo $j;?>~RgpTypeOS<?php echo $j;?>~RgpBCOD<?php echo $j;?>~RgpDiameterOD<?php echo $j;?>">
                                </div>
								</td> 
									
								<td class="nowrap"> 
								<?php if($readonly==''){?>
                                    <?php if($j < $txtTotOD) {  ?>
										<figure><span id="imgOD<?php echo $j;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('CLRowOD<?php echo $j;?>');" title="Delete Row"></span></figure>                                    
                                    <?php }else { ?>
										<figure><span id="imgOD<?php echo $j;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('clTypeOD<?php echo $j;?>').value, 'od', 'CLRowOD', 'imgOD', '<?php echo $j;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" title="Add More"></span></figure>                                                                        
                                    <?php } ?>
                                <?php } ?>
								</td> 
							</tr>
						</table>
						   </div>
						  </div>
						<?php } ?>  
						<!-- END RGP OD -->

						 <!--  START CUSTOM OD -->
						<?php if($clType == 'cust_rgp') { 
						$CLResData[$i]['RgpCustomTypeOD'] = $arrLensManuf[$CLResData[$i]['RgpCustomTypeOD_ID']]['det'];
						?>
                        <input type="hidden" name="clDetIdOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['id'];?>">
						<div id="CLRowOD<?php echo $j;?>" class="custRgp_bg" style="display:block; clear:both; height:<?php echo $rgpHeight;?>;" >
							<div style=" float:left;width:7%; <?php echo $topPad;?>">
							  <select name="clTypeOD<?php echo $j;?>" class="form-control minimal <?php echo vis_getStatus1("clTypeOD".$j);?>" id="clTypeOD<?php echo $j;?>" onChange="changeCLType(this);changeRow(this.value, 'od', 'CLRowOD', document.getElementById('txtTotOD').value, '<?php echo $j;?>',arrManufac, arrManufacId, arrManufacInfo);"  <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;">
								<option value="scl">SCL</option>
								<option value="rgp_soft">RGP Soft</option>
								<option value="rgp_hard">RGP Hard</option>
								<option value="cust_rgp" selected="selected">Custom RGP</option>
                                <option value="prosthesis">Prosthesis</option>
                                <option value="no-cl">No-CL</option>
							  </select>
						   </div>
						   <div style=" float:left;width:91%;">
							<table class="table borderless" id="rgp_custom_table1" style="width:99%;">
							  <?php echo setTitleRows('cust_rgp'); ?> 
									<tr class="alignLeft">
										<td class="odcol">OD</td>
										<td><input class="form-control  <?php echo vis_getStatus1("RgpCustomBCOD".$j);?>"  type="text" name="RgpCustomBCOD<?php echo $j;?>" id="RgpCustomBCOD<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomBCOD']);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustomBCOD<?php echo $j;?>','RgpCustomBCOS<?php echo $j;?>');"></td> 
										<td><input id="RgpCustomDiameterOD<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpCustomDiameterOD".$j);?>" name="RgpCustomDiameterOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomDiameterOD'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustomDiameterOD<?php echo $j;?>','RgpCustomDiameterOS<?php echo $j;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');" ></td> 
										<td><input class="form-control  <?php echo vis_getStatus1("RgpCustomPowerOD".$j);?>"  type="text" name="RgpCustomPowerOD<?php echo $j;?>" id="RgpCustomPowerOD<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomPowerOD']);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');" ></td> 
                                        <td><input class="form-control  <?php echo vis_getStatus1("RgpCustomCylinderOD".$j);?>"  type="text" name="RgpCustomCylinderOD<?php echo $j;?>" id="RgpCustomCylinderOD<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomCylinderOD']);?>" style="width:100%;"  <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');" ></td> 
                                        <td><input class="form-control  <?php echo vis_getStatus1("RgpCustomAxisOD".$j);?>"  type="text" name="RgpCustomAxisOD<?php echo $j;?>" id="RgpCustomAxisOD<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomAxisOD']);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onKeyUp="check2BlurCL(this,'A','','CLRowOD<?php echo $j;?>');"></td> 
										<td><input type="text" name="RgpCustom2degreeOD<?php echo $j;?>" id="RgpCustom2degreeOD<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustom2degreeOD']);?>" style="width:100%;" class="form-control  <?php echo vis_getStatus1("RgpCustom2degreeOD".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustom2degreeOD<?php echo $j;?>','RgpCustom2degreeOS<?php echo $j;?>');"></td>
										<td><input class="form-control  <?php echo vis_getStatus1("RgpCustom3degreeOD".$j);?>"  type="text" name="RgpCustom3degreeOD<?php echo $j;?>" id="RgpCustom3degreeOD<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustom3degreeOD']);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustom3degreeOD<?php echo $j;?>','RgpCustom3degreeOS<?php echo $j;?>');"></td>
										<td><input class="form-control  <?php echo vis_getStatus1("RgpCustomPCWOD".$j);?>"  type="text" name="RgpCustomPCWOD<?php echo $j;?>" id="RgpCustomPCWOD<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomPCWOD']);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>'); copyValuesODToOS('RgpCustomPCWOD<?php echo $j;?>','RgpCustomPCWOS<?php echo $j;?>');"></td>	
										<td><input id="RgpCustomOZOD<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpCustomOZOD".$j);?>" name="RgpCustomOZOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomOZOD'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td> 
                                        <td><input id="RgpCustomCTOD<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpCustomCTOD".$j);?>" name="RgpCustomCTOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomCTOD'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>');"></td> 
										<td class="nowrap"> <!-- Acuities -->
											<div class="input-group" style="width:100%;">
												<input type="text" name="RgpCustomColorOD<?php echo $j;?>"  id="RgpCustomColorOD<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomColorOD']; ?>" class="form-control  <?php echo vis_getStatus1("RgpCustomColorOD".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;" onblur="this.click();">
												<?php // echo getSimpleMenu($ColorOptionsArray,"menu_clcolorOpts","RgpCustomColorOD".$j,0,0,array("pdiv"=>"divWorkView")); ?>
												<?php echo getMenus("color", "RgpCustomColorOD".$j); ?>
											</div>
										</td> 
										<td class="nowrap">
											<div class="input-group" style="width:100%;">
												<input type="text" name="RgpCustomBlendOD<?php echo $j;?>"  id="RgpCustomBlendOD<?php echo $j;?>" value="<?php if($CLResData[$i]['RgpCustomBlendOD']) echo $CLResData[$i]['RgpCustomBlendOD']; ?>" class="form-control  <?php echo vis_getStatus1("RgpCustomBlendOD".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;" onblur="this.click();">
												<?php //echo getSimpleMenu($BlendOptionsArray,"menu_clblendOpts","RgpCustomBlendOD".$j,0,0,array("pdiv"=>"divWorkView")); ?>
												<?php echo getMenus("blend", "RgpCustomBlendOD".$j); ?>
											</div>
										</td> 
										
										<td><input type="text" name="RgpCustomEdgeOD<?php echo $j;?>" id="RgpCustomEdgeOD<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomEdgeOD']);?>"  class="form-control  <?php echo vis_getStatus1("RgpCustomEdgeOD".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' ); copyValuesODToOS('RgpCustomEdgeOD<?php echo $j;?>','RgpCustomEdgeOS<?php echo $j;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');"></td> 
										
										<td><input id="RgpCustomAddOD<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpCustomAddOD".$j);?>" name="RgpCustomAddOD<?php echo $j;?>" style="width:100%;" value="<?php echo $CLResData[$i]['RgpCustomAddOD'];?>"  <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' ); copyValuesODToOS('RgpCustomAddOD<?php echo $j;?>','RgpCustomAddOS<?php echo $j;?>');" onKeyUp="check2BlurCL(this,'s','','CLRowOD<?php echo $j;?>');" ></td> 
						
										<td class="nowrap">
											<div class="input-group" style="width:100%;">
												<input id="RgpCustomDvaOD<?php echo $j;?>" type="text" name="RgpCustomDvaOD<?php echo $j;?>" value="<?php if($CLResData[$i]['RgpCustomDvaOD']) echo $CLResData[$i]['RgpCustomDvaOD']; else echo '20/'; ?>" class="form-control  <?php echo vis_getStatus1("RgpCustomDvaOD".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
												<?php
												    echo getMenus("dva", "RgpCustomDvaOD".$j);
                                                    //echo getSimpleMenu($arrAcuitiesMrDis,"menu_acuitiesMrDis","RgpCustomDvaOD".$j,0,0,array("pdiv"=>"divWorkView"));
                                                ?>
											</div>
										</td> 
										<td class="nowrap">
											<div class="input-group" style="width:100%;">
												<input id="RgpCustomNvaOD<?php echo $j;?>" type="text" name="RgpCustomNvaOD<?php echo $j;?>" value="<?php if($CLResData[$i]['RgpCustomNvaOD']) echo $CLResData[$i]['RgpCustomNvaOD']; else echo '20/'; ?>" class="form-control  <?php echo vis_getStatus1("RgpCustomNvaOD".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
												<?php
												    echo getMenus("nva", "RgpCustomNvaOD".$j);
											         //echo getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","RgpCustomNvaOD".$j,0,0,array("pdiv"=>"divWorkView"));
											     ?>
											</div>
										</td> 
										<td class="nowrap">
                                            <div style="float:left;width:100%;">
                                                <input type="text" name="RgpCustomTypeOD<?php echo $j;?>" id="RgpCustomTypeOD<?php echo $j;?>" class="typeAhead form-control <?php echo vis_getStatus1("RgpCustomTypeOD".$j);?>" style="width:100%; background-image:none;" value="<?php echo $CLResData[$i]['RgpCustomTypeOD'];?>" <?php echo $chgBehave;?> onKeyUp="checkLensMakeType(this);" onKeyDown="clearLensMakeId(this);" />
                                                <input type="hidden" name="RgpCustomTypeOD<?php echo $j;?>ID" id="RgpCustomTypeOD<?php echo $j;?>ID" value="<?php echo $CLResData[$i]['RgpCustomTypeOD_ID'];?>" funVars="worksheet~RgpCustomTypeOD<?php echo $j;?>~RgpCustomTypeOS<?php echo $j;?>~RgpCustomBCOD<?php echo $j;?>~RgpCustomDiameterOD<?php echo $j;?>">
                                            </div>
										</td> 
										<td class="nowrap"> 
										<?php if($readonly==''){?>
											<?php if($j < $txtTotOD) {  ?>
											<figure><span id="imgOD<?php echo $j;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('CLRowOD<?php echo $j;?>');" title="Delete Row"></span></figure>                                    											
											<?php }else { ?>
											<figure><span id="imgOD<?php echo $j;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('clTypeOD<?php echo $j;?>').value, 'od', 'CLRowOD', 'imgOD', '<?php echo $j;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" title="Add More"></span></figure>                                                                                                                    
											<?php } ?>
                                        <?php } ?>
										</td> 
									</tr>
							  </table>
							</div>
						</div>             
						<!-- END CUSTOM RGP -->
					 <?php
						}
						$oldClType = $clType; 
						$j++;
						 } 
					}					
				}
			?>                                                            							
			<!-- drawings -->
			<input type="hidden" name="idoc_drawing_id_od" id="idoc_drawing_id_od" value="<?php echo $arrDrawingData['od']['idoc_drawing_id'];?>">
				<input type="hidden" name="description_A_od" id="description_A_od" value="<?php echo $arrDrawingData['od']['desc_od'];?>">                                
				<input type="hidden" name="description_B_od" id="description_B_od" value="<?php echo $arrDrawingData['od']['desc_os'];?>">   

				<input type="hidden" name="idoc_drawing_id_os" id="idoc_drawing_id_os" value="<?php echo $arrDrawingData['os']['idoc_drawing_id'];?>">
				<input type="hidden" name="description_A_os" id="description_A_os" value="<?php echo $arrDrawingData['os']['desc_od'];?>">                                
				<input type="hidden" name="description_B_os" id="description_B_os" value="<?php echo $arrDrawingData['os']['desc_os'];?>"> 
			<!-- drawings -->	
			
			<?php 
            if($mainArrSize <= 1 || $realTotOD==0){
                
    	        echo makeSCLRowWV('od', '1', 'CLRowOD', '1', 'padding-top:20px', $strAcuitiesMrDisString,$strAcuitiesNearString,$arrMainValues,$chgBehave);
			}
            ?>
            
            	</div>

                <!-- START OS ROWS -->
                <div id="osRows">  
                
                <?php      
                if($mainArrSize > 1)
                {
                    $oldClType = '';	$j=1;
                    for($i=1; $i <= $mainArrSize; $i++)
                    {
                        $dispTitle =0;	$topPad='';	
                        $odos = strtolower($CLResData[$i]['clEye']);
                        if($odos == 'os')
                        {
							/*$minVal= min(array_values($arrSavedRowIds['OS']));
							if($minVal>0){							
								$j=$arrSavedRowIds['OS'][$CLResData[$i]['id']];
							}*/

							$clType = $CLResData[$i]['clType'];

                            $dispTitle=1;	$topPad="padding-top:22px"; 
                            $divHeight = "45px"; $rgpHeight = "21px";
    
                        //CHEKING IF DRAWING DATA EXIST OR NOT
                        if($CLResData[$i]['idoc_drawing_id']>0){
								$arrDrawingData['os']['idoc_drawing_id']=$CLResData[$i]['idoc_drawing_id'];
                                $arrDrawingData['os']['desc_od']=$CLResData[$i]['corneaSCL_od_desc'];
                                $arrDrawingData['os']['desc_os']=$CLResData[$i]['corneaSCL_os_desc'];
                        }//unset($rs1);
    
                        if($clType =='scl' || $clType =='prosthesis' || $clType =='no-cl') { 
                        $CLResData[$i]['SclTypeOS'] = $arrLensManuf[$CLResData[$i]['SclTypeOS_ID']]['det'];					
    
                        ?> 
                        <input type="hidden" name="clDetIdOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['id'];?>">
                        <div id="CLRowOS<?php echo $j;?>" style="display:block; clear:both;" class="scl_bg">
                           <div style=" float:left;width:7%; <?php echo $topPad;?>">
                              <select name="clTypeOS<?php echo $j;?>" class="form-control minimal <?php echo vis_getStatus1("clTypeOS".$j);?>" id="clTypeOS<?php echo $j;?>" onChange="changeCLType(this);changeRow(this.value, 'os', 'CLRowOS', document.getElementById('txtTotOS').value, '<?php echo $j;?>',arrManufac, arrManufacId, arrManufacInfo);" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;">
                                <option value="scl" <?php if($clType=='scl')echo 'selected';?>>SCL</option>
								<option value="rgp_soft">RGP Soft</option>
								<option value="rgp_hard">RGP Hard</option>
                                <option value="cust_rgp">Custom RGP</option>
                                <option value="prosthesis" <?php if($clType=='prosthesis')echo 'selected';?>>Prosthesis</option>
                                <option value="no-cl" <?php if($clType=='no-cl')echo 'selected';?>>No-CL</option>
                              </select>
                           </div>
                           <div style="float:left;width:91%;">
                                <table class="table borderless" id="sclTable" style="width:99%;">
                                  <?php echo  setTitleRows('scl');?>  
                                  <tr class="alignLeft">
                                  <td class="oscol txt_10b" style="width:3%;">OS</td>
                                  <td><input type="text" name="SclBcurveOS<?php echo $j;?>" id="SclBcurveOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclBcurveOS'];?>" class="form-control  <?php echo vis_getStatus1("SclBcurveOS".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );"></td> 
                                  <td><input id="SclDiameterOS<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("SclDiameterOS".$j);?>" name="SclDiameterOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclDiameterOS'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                                  <td><input type="text" name="SclsphereOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclsphereOS'];?>" class="form-control  <?php echo vis_getStatus1("SclsphereOS".$j);?>" id="SclsphereOS<?php echo $j;?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                                  <td><input class="form-control <?php echo vis_getStatus1("SclCylinderOS".$j);?>" id="SclCylinderOS<?php echo $j;?>" type="text" name="SclCylinderOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclCylinderOS'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                                  <td class="alignLeft"><input type="text" name="SclaxisOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclaxisOS'];?>"  class="form-control  <?php echo vis_getStatus1("SclaxisOS".$j);?>" id="SclaxisOS<?php echo $j;?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>', 'noDecimal');;" onKeyUp="check2BlurCL(this,'A','','CLRowOS<?php echo $j;?>');"></td>
                                  <td class="alignLeft">
                                  	<div class="input-group" style="width:100%;">
										<input type="text" name="SclColorOS<?php echo $j;?>" id="SclColorOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['SclColorOS']; ?>" class="form-control  <?php echo vis_getStatus1("SclColorOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;" onblur="this.click();">
										<?php echo getMenus("color", "SclColorOS".$j);
								        //echo getSimpleMenu($ColorOptionsArray,"menu_clcolorOpts","RgpColorOD".$j,0,0,array("pdiv"=>"divWorkView"));
                                        ?>
                                	</div>
                                  </td> 
                                  <td><input id="SclAddOS<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("SclAddOS".$j);?>" name="SclAddOS<?php echo $j;?>"  value="<?php echo $CLResData[$i]['SclAddOS'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                                  <td class="valignTop nowrap" style="width:9%;">
                                  	<div class="input-group" style="width:100%;">
                                  		<input id="SclDvaOS<?php echo $j;?>" type="text" name="SclDvaOS<?php echo $j;?>" value="<?php if($CLResData[$i]['SclDvaOS']) echo $CLResData[$i]['SclDvaOS']; else echo '20/'; ?>" style="width:100%;" class="form-control  <?php echo vis_getStatus1("SclDvaOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?>   onblur="this.click();">
										<?php echo getMenus("dva", "SclDvaOS".$j); ?>
                                        <?php //echo getSimpleMenu($arrAcuitiesMrDis,"menu_acuitiesMrDis","SclDvaOS".$j,0,0,array("pdiv"=>"divWorkView")); ?>
                                    </div>
                                  </td> 
                                  <td class="nowrap">
                                  	<div class="input-group" style="width:100%;">
                                    	<input type="text" name="SclNvaOS<?php echo $j;?>"  id="SclNvaOS<?php echo $j;?>" value="<?php if($CLResData[$i]['SclNvaOS']) echo $CLResData[$i]['SclNvaOS']; else echo '20/'; ?>" style="width:100%;" class="form-control  <?php echo vis_getStatus1("SclNvaOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="this.click();">
                                    	<?php echo getMenus("nva", "SclNvaOS".$j); ?>
                                    	<?php //echo getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","SclNvaOS".$j,0,0,array("pdiv"=>"divWorkView")); ?> 
                                    </div>
                                  </td>
                                  <td class="nowrap">
                                    <div style="width:100%;float:left;">
                                        <input type="text" name="SclTypeOS<?php echo $j;?>" id="SclTypeOS<?php echo $j;?>" class="typeAhead form-control <?php echo vis_getStatus1("SclTypeOS".$j);?>" style="width:100%;background-image:none;" value="<?php echo $CLResData[$i]['SclTypeOS'];?>" <?php echo $chgBehave;?> onKeyUp="checkLensMakeType(this);" onKeyDown="clearLensMakeId(this);" />
                                       
									   <input type="hidden" name="SclTypeOS<?php echo $j;?>ID" id="SclTypeOS<?php echo $j;?>ID" value="<?php echo $CLResData[$i]['SclTypeOS_ID'];?>" funVars="halfFun~garbage1~garbage2~SclBcurveOS<?php echo $j;?>~SclDiameterOS<?php echo $j;?>">
                                    </div>
                                  </td>
                                	<?php if($readonly==''){
                                    ?>
                                 <td class="nowrap">
                                        <?php if($j < $txtTotOS) {  ?>
											<figure><span id="imgOS<?php echo $j;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('CLRowOS<?php echo $j;?>');" title="Delete Row"></span></figure>                                    											                                        
                                        <?php }else { ?>
											<figure><span id="imgOS<?php echo $j;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('clTypeOS<?php echo $j;?>').value, 'os', 'CLRowOS', 'imgOS', '<?php echo $j;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" title="Add More"></span></figure>                                                                                                                                                            
                                        <?php } ?>
                                    <?php } ?>
                                 </td>			
                                </tr>                    
                               </table>
                           </div>
                        </div> 
                        <?php } ?>
                        
                        <!-- RGP OS ROW -->
                        <?php if($clType == 'rgp' || $clType == 'rgp_hard' || $clType == 'rgp_soft') {
                            $CLResData[$i]['RgpTypeOS'] = $arrLensManuf[$CLResData[$i]['RgpTypeOS_ID']]['det'];
                             ?>
                        <input type="hidden" name="clDetIdOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['id'];?>">     
                        <div id="CLRowOS<?php echo $j;?>" class="rgp_bg" style="display:block; clear:both; height:<?php echo $rgpHeight;?>;" >
                            <div class="rgp_bg" style="float:left;width:7%; <?php echo $topPad;?>">
                              <select name="clTypeOS<?php echo $j;?>" id="clTypeOS<?php echo $j;?>" class="form-control minimal <?php echo vis_getStatus1("clTypeOS".$j);?>" onChange="changeCLType(this);changeRow(this.value, 'os', 'CLRowOS', document.getElementById('txtTotOS').value, '<?php echo $j;?>',arrManufac, arrManufacId, arrManufacInfo);" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;">
                                <option value="scl">SCL</option>
                                <?php if($clType =='rgp'){
									echo "<option value=\"rgp\" selected=\"selected\">RGP</option>";
								} ?>
								<option value="rgp_soft" <?php if($clType == 'rgp_soft'){ echo "selected=\"selected\""; } ?>>RGP Soft</option>
								<option value="rgp_hard" <?php if($clType == 'rgp_hard'){ echo "selected=\"selected\""; } ?>>RGP Hard</option>
                                <option value="cust_rgp">Custom RGP</option>
                                <option value="prosthesis">Prosthesis</option>
                                <option value="no-cl">No-CL</option>
                              </select>
                           </div>
                           <div class="rgp_bg" style=" float:left;width:91%;">
                            <table class="table borderless" id="rgp_table1" style="display:block;width:99%;">
                              <?php 
                                echo setTitleRows('rgp');
                               ?>
                            <tr class="alignLeft">
                              <td class="oscol txt_10b" style="width:3%;">OS</td>
                              <td>
							  
							  <input class="form-control  <?php echo vis_getStatus1("RgpBCOS".$j);?>"  type="text" name="RgpBCOS<?php echo $j;?>" id="RgpBCOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpBCOS'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );"></td>
                              <td>
							  
							  <input type="text" name="RgpDiameterOS<?php echo $j;?>" id="RgpDiameterOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpDiameterOS'];?>" class="form-control  <?php echo vis_getStatus1("RgpDiameterOS".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td>												
                              <td>
							  
							  <input class="form-control  <?php echo vis_getStatus1("RgpPowerOS".$j);?>"  type="text" name="RgpPowerOS<?php echo $j;?>" id="RgpPowerOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpPowerOS'];?> " style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                              <td>
							  
							  <input class="form-control  <?php echo vis_getStatus1("RgpCylinderOS".$j);?>"  type="text" name="RgpCylinderOS<?php echo $j;?>" id="RgpCylinderOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCylinderOS'];?> " style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                              <td>
							  
							  <input class="form-control  <?php echo vis_getStatus1("RgpAxisOS".$j);?>"  type="text" name="RgpAxisOS<?php echo $j;?>" id="RgpAxisOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpAxisOS'];?> " style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onKeyUp="check2BlurCL(this,'A','','CLRowOS<?php echo $j;?>');"></td> 
                              <td>
							  
							  <input type="text" name="RgpOZOS<?php echo $j;?>" id="RgpOZOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpOZOS'];?>" class="form-control  <?php echo vis_getStatus1("RgpOZOS".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>></td> 
                              <td>
							  
							  <input type="text" name="RgpCTOS<?php echo $j;?>" id="RgpCTOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCTOS'];?>" class="form-control  <?php echo vis_getStatus1("RgpCTOS".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>></td> 
                              <td class="alignLeft nowrap" style="width:7%;">
                              	<div class="input-group" style="width:100%;">
                                	<input type="text" name="RgpColorOS<?php echo $j;?>"  id="RgpColorOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpColorOS']; ?>" class="form-control  <?php echo vis_getStatus1("RgpColorOS".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
                                	<?php echo getMenus("color", "RgpColorOS".$j); ?>
                                	<?php //echo getSimpleMenu($ColorOptionsArray,"menu_clcolorOpts","RgpColorOS".$j,0,0,array("pdiv"=>"divWorkView")); ?>
                                    <!-- Acuities -->
                                </div>
                              </td>
                              <td><input  id="RgpAddOS<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpAddOS".$j);?>" name="RgpAddOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpAddOS'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                              <td class="nowrap">
                              	<div class="input-group" style="width:100%;">
                                	<input type="text" name="RgpDvaOS<?php echo $j; ?>"  id="RgpDvaOS<?php echo $j;?>" value="<?php if($CLResData[$i]['RgpDvaOS']) echo $CLResData[$i]['RgpDvaOS']; else echo '20/'; ?>" style="width:100%;" class="form-control <?php echo vis_getStatus1("RgpDvaOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
                                	<?php echo getMenus("dva", "RgpDvaOS".$j); ?>
                                	<?php // echo getSimpleMenu($arrAcuitiesMrDis,"menu_acuitiesMrDis","RgpDvaOS".$j,0,0,array("pdiv"=>"divWorkView")); ?>
                                    <!-- Acuities -->
                                </div>
                                </td> 
                              <td class="nowrap">
                              	<div class="input-group" style="width:100%;">
                                	<input type="text" name="RgpNvaOS<?php echo $j;?>"  id="RgpNvaOS<?php echo $j;?>" style="width:100%;" value="<?php if($CLResData[$i]['RgpNvaOS']) echo $CLResData[$i]['RgpNvaOS']; else echo '20/'; ?>" class="form-control <?php echo vis_getStatus1("RgpNvaOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
                                	<?php echo getMenus("nva", "RgpNvaOS".$j); ?>
                                	<?php //echo getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","RgpNvaOS".$j,0,0,array("pdiv"=>"divWorkView")); ?>
                                    <!-- Acuities -->
                                </div>
                              </td> 
                              <td class="nowrap">
                            <div class="alignLeft" style="width:100%;float:left;">
                                <input type="text" name="RgpTypeOS<?php echo $j;?>" id="RgpTypeOS<?php echo $j;?>" class="typeAhead form-control <?php echo vis_getStatus1("RgpTypeOS".$j);?>" style="width:100%; background-image:none;" value="<?php echo $CLResData[$i]['RgpTypeOS'];?>" <?php echo $chgBehave;?> onKeyUp="checkLensMakeType(this);" onKeyDown="clearLensMakeId(this);" />
                                
								<input type="hidden" name="RgpTypeOS<?php echo $j;?>ID" id="RgpTypeOS<?php echo $j;?>ID" value="<?php echo $CLResData[$i]['RgpTypeOS_ID'];?>" funVars="halfFun~garbage1~garbage2~RgpBCOS<?php echo $j;?>~RgpDiameterOS<?php echo $j;?>" >
                            </div>
                                </td>
                              
                              <td class="nowrap">
                                <?php if($readonly==''){?>
                                  <?php if($j < $txtTotOS) {  ?>
								  <figure><span id="imgOS<?php echo $j;?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('CLRowOS<?php echo $j;?>');" data-toggle="tooltip" title="Delete" data-original-title="Delete"></span></figure>                                    											                                                                          
                                  <?php }else { ?>
									<figure><span id="imgOS<?php echo $j;?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('clTypeOS<?php echo $j;?>').value, 'os', 'CLRowOS', 'imgOS', '<?php echo $j;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" data-original-title="Add More"></span></figure>
                                  <?php } ?>
                                  <?php } ?>
                                </td> 
                              </tr>
                        </table>
                           </div>
                          </div>
                          <?php } ?>
                          <!-- END RGP OS ROW -->
                        
                        <!-- START CUSTOM RGP OS ROW -->
                        <?php if($clType == 'cust_rgp') { 
                        $CLResData[$i]['RgpCustomTypeOS'] = $arrLensManuf[$CLResData[$i]['RgpCustomTypeOS_ID']]['det'];
                        ?> 
                        <input type="hidden" name="clDetIdOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['id'];?>">           
                        <div id="CLRowOS<?php echo $j;?>" class="custRgp_bg" style="display:block; clear:both; height:<?php echo $rgpHeight;?>;" >
                            <div style=" float:left;width:7%; <?php echo $topPad;?>">
                              <select name="clTypeOS<?php echo $j;?>" id="clTypeOS<?php echo $j;?>" class="form-control minimal <?php echo vis_getStatus1("clTypeOS".$j);?>" onChange="changeCLType(this);changeRow(this.value, 'os', 'CLRowOS', document.getElementById('txtTotOS').value, '<?php echo $j;?>',arrManufac, arrManufacId, arrManufacInfo);" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;">
                                <option value="scl">SCL</option>
								<option value="rgp_soft">RGP Soft</option>
								<option value="rgp_hard">RGP Hard</option>
                                <option value="cust_rgp" selected="selected">Custom RGP</option>
                                <option value="prosthesis">Prosthesis</option>
                                <option value="no-cl">No-CL</option>
                              </select>
                           </div>
                           <div style=" float:left;width:91%;">
                            <table class="table borderless" id="rgp_custom_table1" style="width:99%;">
                                  <?php echo setTitleRows('cust_rgp');?> 
                                    <tr class="alignLeft">
                                        <td class="oscol txt_10b" style="width:3%;">OS</td>  
                                        <td>
										
										<input class="form-control <?php echo vis_getStatus1("RgpCustomBCOS".$j);?>"  type="text" name="RgpCustomBCOS<?php echo $j;?>" id="RgpCustomBCOS<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomBCOS']);?>" style="width:100%;"  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );"></td>
                                        <td>
										
										<input id="RgpCustomDiameterOS<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpCustomDiameterOS".$j);?>" name="RgpCustomDiameterOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomDiameterOS'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                                        <td>
										
										<input class="form-control <?php echo vis_getStatus1("RgpCustomPowerOS".$j);?>"  type="text" name="RgpCustomPowerOS<?php echo $j;?>" id="RgpCustomPowerOS<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomPowerOS']);?>" style="width:100%;"  <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
                                        <td>
										
										<input class="form-control <?php echo vis_getStatus1("RgpCustomCylinderOS".$j);?>"  type="text" name="RgpCustomCylinderOS<?php echo $j;?>" id="RgpCustomCylinderOS<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomCylinderOS']);?>" style="width:100%;"  <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td>                                         
                                        <td><input class="form-control <?php echo vis_getStatus1("RgpCustomAxisOS".$j);?>"  type="text" name="RgpCustomAxisOS<?php echo $j;?>" id="RgpCustomAxisOS<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomAxisOS']);?>" style="width:100%;"  <?php echo $chgBehave;?> <?php echo $readonly;?> onKeyUp="check2BlurCL(this,'A','','CLRowOS<?php echo $j;?>');"></td> 
                                        <td>
										
										<input type="text" name="RgpCustom2degreeOS<?php echo $j;?>" id="RgpCustom2degreeOS<?php echo $j;?>"  value="<?php echo($CLResData[$i]['RgpCustom2degreeOS']);?>" style="width:100%;" class="form-control  <?php echo vis_getStatus1("RgpCustom2degreeOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );"></td> 
                                        <td>
										
										<input class="form-control <?php echo vis_getStatus1("RgpCustom3degreeOS".$j);?>"  type="text" name="RgpCustom3degreeOS<?php echo $j;?>" id="RgpCustom3degreeOS<?php echo $j;?>"  value="<?php echo($CLResData[$i]['RgpCustom3degreeOS']);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> SclsphereOS<?php echo $j;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );"></td>	
                                        <td><input class="form-control <?php echo vis_getStatus1("RgpCustomPCWOS".$j);?>"  type="text" name="RgpCustomPCWOS<?php echo $j;?>" id="RgpCustomPCWOS<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomPCWOS']);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );"></td>	
                                        <td><input id="RgpCustomOZOS<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpCustomOZOS".$j);?>" name="RgpCustomOZOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomOZOS'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> ></td> 
                                        <td><input id="RgpCustomCTOS<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpCustomCTOS".$j);?>" name="RgpCustomCTOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomCTOS'];?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?> ></td> 
                                        <td class="nowrap">
                                        	<div class="input-group" style="width:100%;">
                                            	<input type="text" name="RgpCustomColorOS<?php echo $j;?>"  id="RgpCustomColorOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomColorOS']; ?>" class="form-control  <?php echo vis_getStatus1("RgpCustomColorOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
                                                <?php
                                                // echo getSimpleMenu($ColorOptionsArray,"menu_clcolorOpts","RgpCustomColorOS".$j,0,0,array("pdiv"=>"divWorkView"));
                                                /*
                                                if($readonly==''){?>
                                                    <script>
                                                    $(document).ready(function () {
                                                    $('#RgpCustomColorOS'+<?php echo $j;?>).combobox({
                                                        data: [<?php echo $ColorOptionsString;?>],
                                                        autoShow: false,
                                                        listHTML: function(val, index) {
                                                            return $.ui.combobox.defaults.listHTML(
                                                                  val, index);
                                                        }
                                                    });
                                                    $('#RgpCustomColorOS'+<?php echo $j;?>).css('width', '50px');
                                                    });
                                                </script>
                                                <?php } 
                                                */
                                                ?>
                                                <?php echo getMenus("color", "RgpCustomColorOS".$j); ?>
                                            </div>
                                        </td> 
                                        <td class="nowrap">
                                        	<div class="input-group" style="width:100%;">
                                        		<input type="text" name="RgpCustomBlendOS<?php echo $j;?>"  id="RgpCustomBlendOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomBlendOS']; ?>" class="form-control  <?php echo vis_getStatus1("RgpCustomBlendOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;" onblur="this.click();">
                                            	<?php //echo getSimpleMenu($BlendOptionsArray,"menu_clblendOpts","RgpCustomBlendOS".$j,0,0,array("pdiv"=>"divWorkView")); 
                                            /*
                                            if($readonly==''){?>
                                            <script>
                                            $(document).ready(function () {
                                                $('#RgpCustomBlendOS'+<?php echo $j;?>).combobox({
                                                    data: [<?php echo $BlendOptionsString;?>],
                                                    autoShow: false,
                                                    listHTML: function(val, index) {
                                                        return $.ui.combobox.defaults.listHTML(
                                                              val, index);
                                                    }
                                                });
                                                $('#RgpCustomBlendOS'+<?php echo $j;?>).css('width', '50px');
                                            });	
                                            </script>
                                            <?php }
                                            */
                                            ?>
                                            <?php echo getMenus("blend", "RgpCustomBlendOS".$j); ?>
                                            </div>
                                        </td> 
                                        <td><input type="text" name="RgpCustomEdgeOS<?php echo $j;?>" id="RgpCustomEdgeOS<?php echo $j;?>" value="<?php echo($CLResData[$i]['RgpCustomEdgeOS']);?>" class="form-control  <?php echo vis_getStatus1("RgpCustomEdgeOS".$j);?>" style="width:100%;" <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');"></td> 
										
                                        <td><input  id="RgpCustomAddOS<?php echo $j;?>" type="text" class="form-control  <?php echo vis_getStatus1("RgpCustomAddOS".$j);?>" name="RgpCustomAddOS<?php echo $j;?>" value="<?php echo $CLResData[$i]['RgpCustomAddOS'];?>" style="width:100%;"  <?php echo $chgBehave;?> <?php echo $readonly;?>  onblur="justify2DecimalCL(this, '<?php echo $cylSign;?>' );" onKeyUp="check2BlurCL(this,'s','','CLRowOS<?php echo $j;?>');" ></td> 
										
                                        <td class="nowrap">
                                        	<div class="input-group" style="width:100%;">
                                        		<input id="RgpCustomDvaOS<?php echo $j;?>" type="text" name="RgpCustomDvaOS<?php echo $j;?>" value="<?php if($CLResData[$i]['RgpCustomDvaOS']) echo $CLResData[$i]['RgpCustomDvaOS']; else echo '20/'; ?>" size="5" class="form-control  <?php echo vis_getStatus1("RgpCustomDvaOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
                                        <?php 
                                        // echo getSimpleMenu($arrAcuitiesMrDis,"menu_acuitiesMrDis","RgpCustomDvaOS".$j,0,0,array("pdiv"=>"divWorkView"));
                                        /*
                                        if($readonly==''){?>
                                        <script>
                                        $(document).ready(function () {
                                                $('#RgpCustomDvaOS'+<?php echo $j;?>).combobox({
                                                    data: [<?php echo $strAcuitiesMrDisString;?>],
                                                    autoShow: false,
                                                    listHTML: function(val, index) {
                                                        return $.ui.combobox.defaults.listHTML(
                                                              val, index);
                                                    }
                                                });
                                                $('#RgpCustomDvaOS'+<?php echo $j;?>).focus(function() {
                                                  setCursorAtEnd(this);
                                                });
                                                $('#RgpCustomDvaOS'+<?php echo $j;?>).css('width', '60px')													
                                        });		
                                            </script>
                                        <?php }
                                        */
                                        ?>
                                        	<?php echo getMenus("dva", "RgpCustomDvaOS".$j); ?>
                                        	</div>
                                        </td> 
                                        <td class="nowrap">
                                        	<div class="input-group" style="width:100%;">
                                        	<input id="RgpCustomNvaOS<?php echo $j;?>" type="text" name="RgpCustomNvaOS<?php echo $j;?>" value="<?php if($CLResData[$i]['RgpCustomNvaOS']) echo $CLResData[$i]['RgpCustomNvaOS']; else echo '20/'; ?>" size="5" class="form-control <?php echo vis_getStatus1("RgpCustomNvaOS".$j);?>" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();">
                                            <?php 											
                                            // echo getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","RgpCustomNvaOS".$j,0,0,array("pdiv"=>"divWorkView"));
                                            /*
                                            if($readonly==''){?>
                                            <script>
                                            $(document).ready(function () {
                                                $('#RgpCustomNvaOS'+<?php echo $j;?>).combobox({
                                                    data: [<?php echo $strAcuitiesNearString;?>],
                                                    autoShow: false,
                                                    listHTML: function(val, index) {
                                                        return $.ui.combobox.defaults.listHTML(
                                                              val, index);
                                                    }
                                                });
                                                $('#RgpCustomNvaOS'+<?php echo $j;?>).focus(function() {
                                                  setCursorAtEnd(this);
                                                });		
                                                $('#RgpCustomNvaOS'+<?php echo $j;?>).css('width', '80px');											
                                            });	
                                            </script>
                                            <?php } 
                                            */
                                            ?>
                                            <?php echo getMenus("nva", "RgpCustomNvaOS".$j); ?>
                                            </div>
                                        </td> 
                                        <td class="nowrap">
                                            <div style="width:100%;float:left;" >
                                                <input type="text" name="RgpCustomTypeOS<?php echo $j;?>" id="RgpCustomTypeOS<?php echo $j;?>" class="typeAhead form-control <?php echo vis_getStatus1("RgpCustomTypeOS".$j);?>" style="width:100%;background-image:none;" value="<?php echo $CLResData[$i]['RgpCustomTypeOS'];?>" <?php echo $chgBehave;?> onKeyUp="checkLensMakeType(this);" onKeyDown="clearLensMakeId(this);" />
                                                <input type="hidden" name="RgpCustomTypeOS<?php echo $j;?>ID" id="RgpCustomTypeOS<?php echo $j;?>ID" value="<?php echo $CLResData[$i]['RgpCustomTypeOS_ID'];?>" funVars="halfFun~garbage1~garbage2~RgpCustomBCOS<?php echo $j;?>~RgpCustomDiameterOS<?php echo $j;?>" >
                                            </div>
                                        </td>
                                        <td> 
                                        <?php if($readonly==''){ ?>
                                            <?php if($j < $txtTotOS){ ?>
											<figure><span id="imgOS<?php echo $j; ?>" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow('CLRowOS<?php echo $j;?>');" data-toggle="tooltip" title="Delete" data-original-title="Delete"></span></figure>                                            
											<?php }else { ?>
											<figure><span id="imgOS<?php echo $j; ?>" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi('clTypeOS<?php echo $j;?>').value, 'os', 'CLRowOS', 'imgOS', '<?php echo $j;?>','10','', arrManufac, arrManufacId, arrManufacInfo);" data-original-title="Add More"></span></figure>                                          
                                            <?php } ?>
                                        <?php } ?>
                                        </td>
                                    </tr>
                              </table>
                            </div>
                        </div>
                         <!-- END CUSTOM RGP OS -->
                        <?php
                        }
                          $oldClType = $clType;
                          $j++;
                        }
                    }
            
                }// END outermost IF
            ?>
            
        <?php
            if($mainArrSize <= 1 || $realTotOS==0){
                echo makeSCLRowWV('os', '1', 'CLRowOS', 1, 'padding-top:20px', $strAcuitiesMrDisString,$strAcuitiesNearString,$arrMainValues,$chgBehave);
            ?>
            <!--
            <script type="text/javascript">
            $(function(){
                $.get('simpleMenuContent.php', function(data){ 
                    $('#SclTypeOS1').menu({ content: data, flyOut: true });
                });
            });
            </script>			
            -->
        <?php }		?>
    </div> 
    
	<div style="clear:both;width:96%;">
		<table class="table borderless" id="sclTable" style="width:99%;">
			<tr>
				<td></td>
				<td>Comfort</td>
				<td>Movement</td>
				<td>Rotation</td>
				<td>Condition</td>
				<td>Position</td>
				<td>Other</td>
				<td>Position B/Blink</td>
				<td>Other</td>
				<td>Position A/Blink</td>
				<td>Other</td>
				<td>Fluorescein Patter</td>
				<td>Inverted Lids</td>
			</tr>
			<?php
				$clEvalArray = array();
				$clEvalArray[] = "OD";
				$clEvalArray[] = "OS";
				
				$clEvaluationQuery = "select cle.*, cwd.clEye, cwd.clType from contactlens_evaluations cle join contactlensworksheet_det cwd on cle.clws_id=cwd.clws_id where cle.clws_id='".$clws_id."'";
				//pre($clEvaluationQuery);
				$clEvaluationResult = imw_query($clEvaluationQuery) or die("Failed to get cl evaluation: ".imw_error());
				$clEvaluationRow = imw_fetch_assoc($clEvaluationResult);
				//echo "lens type: ".$clEvaluationRow['clType'];
//				pre($clEvaluationRow);
				//echo count($clEvaluationRow."<br />");

				//$clTypeArray = array();
				//if($clws_id > 0){
			    //	$clResult = imw_query("select clEye, clType from contactlensworksheet_det where clws_id='".$clws_id."'");
				//	while($clRow = imw_fetch_assoc($clResult)){
				//		$clTypeArray[$clRow['clEye']] = $clRow['clType'];
				//	}
				//}
				//pre($clTypeArray);
			?>
			<?php
				for($k=0;$k<count($clEvalArray);$k++){
					$contactLensType = "";
					$eye = "";
					$eye = $clEvalArray[$k];
					if($clws_id <= 0 || $clws_id == ""){
						$contactLensType = "CLSLC";
					}elseif($clws_id > 0){
						$contactLensType=$evalutaionFieldSettings[$eye];

						if($contactLensType == "scl"){
							$contactLensType = "CLSLC";
						}else{
							$contactLensType = "CLRGP";
						}
					}
			?>
			<tr>
				<input type="hidden" name="hdnCLTypeOD" id="hdnCLTypeOD" />
				<input type="hidden" name="hdnCLTypeOS" id="hdnCLTypeOS" />
				<td class="<?php echo strtolower($eye); ?>col txt_10b alignLeft" style="width:2%;"><?php echo strtoupper($eye); ?></td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo $contactLensType."EvaluationComfort".strtoupper($eye); ?>" id="<?php echo $contactLensType."EvaluationComfort".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow[$contactLensType."EvaluationComfort".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave;?> <?php echo $readonly; ?> onblur="this.click();" style="width:100%;">
						<?php echo getMenus("comfort", $contactLensType."EvaluationComfort".strtoupper($eye)); ?>
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo $contactLensType."EvaluationMovement".strtoupper($eye); ?>" id="<?php echo $contactLensType."EvaluationMovement".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow[$contactLensType."EvaluationMovement".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave;?> <?php echo $readonly; ?> onblur="this.click();" style="width:100%;">
						<?php echo getMenus("movement", $contactLensType."EvaluationMovement".strtoupper($eye)); ?>
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="EvaluationRotation<?php echo strtoupper($eye); ?>" id="EvaluationRotation<?php echo strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["EvaluationRotation".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave; ?> <?php echo $readonly; ?> style="width:100%;">
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo "CLSLCEvaluationCondtion".strtoupper($eye); ?>" id="<?php echo "CLSLCEvaluationCondtion".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["CLSLCEvaluationCondtion".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave; ?> <?php echo $readonly; ?> onblur="this.click();" style="width:100%;">
						<?php echo getMenus("condition", "CLSLCEvaluationCondtion".strtoupper($eye)); ?>
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo "CLSLCEvaluationPosition".strtoupper($eye); ?>" id="<?php echo "CLSLCEvaluationPosition".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["CLSLCEvaluationPosition".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave; ?> <?php echo $readonly; ?> onblur="this.click();"  onblur="this.click();" style="width:100%;">
						<?php echo getMenus("position", "CLSLCEvaluationPosition".strtoupper($eye)); ?>
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo $contactLensType."EvaluationPositionOther".strtoupper($eye); ?>" id="<?php echo $contactLensType."EvaluationPositionOther".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow[$contactLensType."EvaluationPositionOther".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave; ?> <?php echo $readonly; ?> style="width:100%;">
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo "CLRGPEvaluationPosBefore".strtoupper($eye); ?>" id="<?php echo "CLRGPEvaluationPosBefore".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["CLRGPEvaluationPosBefore".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave;?> <?php echo $readonly; ?> onblur="this.click();" style="width:100%;">
						<?php echo getMenus("position", "CLRGPEvaluationPosBefore".strtoupper($eye)); ?>
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo "CLRGPEvaluationPosBeforeOther".strtoupper($eye); ?>" id="<?php echo "CLRGPEvaluationPosBeforeOther".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["CLRGPEvaluationPosBeforeOther".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave;?> <?php echo $readonly;?> style="width:100%;">
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo "CLRGPEvaluationPosAfter".strtoupper($eye); ?>" id="<?php echo "CLRGPEvaluationPosAfter".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["CLRGPEvaluationPosAfter".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave;?> <?php echo $readonly;?> onblur="this.click();" style="width:100%;">
						<?php echo getMenus("position", "CLRGPEvaluationPosAfter".strtoupper($eye)); ?>
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo "CLRGPEvaluationPosAfterOther".strtoupper($eye); ?>" id="<?php echo "CLRGPEvaluationPosAfterOther".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["CLRGPEvaluationPosAfterOther".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave; ?> <?php echo $readonly; ?> style="width:100%;">
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo "CLRGPEvaluationFluoresceinPattern".strtoupper($eye); ?>" id="<?php echo "CLRGPEvaluationFluoresceinPattern".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["CLRGPEvaluationFluoresceinPattern".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave; ?> <?php echo $readonly; ?> onblur="this.click();" style="width:100%;">
						<?php echo getMenus("fluoresceinpatter", "CLRGPEvaluationFluoresceinPattern".strtoupper($eye)); ?>
					</div>
				</td>
				<td class="alignLeft">
					<div class="input-group" style="width:100%;">
						<input type="text" name="<?php echo "CLRGPEvaluationInverted".strtoupper($eye); ?>" id="<?php echo "CLRGPEvaluationInverted".strtoupper($eye); ?>" value="<?php echo $clEvaluationRow["CLRGPEvaluationInverted".strtoupper($eye)]; ?>" class="form-control" <?php echo $chgBehave; ?> <?php echo $readonly; ?> onblur="this.click();" style="width:100%;">
						<?php echo getMenus("invertedlids", "CLRGPEvaluationInverted".strtoupper($eye)); ?>
					</div>
				</td>
			</tr>
			<?php } ?>
		</table>
		<table class="table borderless" style="width:99%;">
			<tr>
				<td>Replenishment</td>
				<td>Wear Scheduler</td>
				<td>Disinfecting</td>
			</tr>
			<tr>
				<td style="width:10%">
					<select name="replenishment1" id="replenishment1" class="form-control minimal">
						<option value=""></option>
						<option value="2 Weeks">2 Weeks</option>
						<option value="3 Months">3 Months</option>
						<option value="Annual">Annual</option>                            
						<option value="As Needed">As Needed</option>
						<option value="Daily">Daily</option>
						<option value="Monthly">Monthly</option>
						<option value="Quarterly">Quarterly</option>
						<option value="Semi-Annual">Semi-Annual</option>
						<option value="Weekly">Weekly</option>
					</select>
				</td>
				<td style="width:10%">
					<select name="wear_scheduler1" id="wear_scheduler1" class="form-control minimal">
						<option value=""></option>
						<option value="As Needed">As Needed</option>
						<option value="Bi-weekly">Bi-weekly</option>
						<option value="Daily">Daily</option>
						<option value="Monthly">Monthly</option>
						<option value="Weekly">Weekly</option>
					</select>
				</td>
				<td style="width:10%">
					<select name="disinfecting1" id="disinfecting1" class="form-control minimal">
						<option value=""></option>
						<option value="None">None</option>                            
						<option value="Bio True">Bio True</option>
						<option value="Boston">Boston</option>
						<option value="Chemical">Chemical</option>
						<option value="ClearCare">ClearCare</option>
						<option value="Complete">Complete</option>
						<option value="Generic">Generic</option>
						<option value="Optifree Express">Optifree Express</option>
						<option value="Optifre PurMoist">Optifre PurMoist</option>
						<option value="Optifree Replenish">Optifree Replenish</option>
						<option value="PeroxiClear">PeroxiClear</option>
						<option value="Renu">Renu</option>
						<option value="Revitalens">Revitalens</option>
					</select>
				</td>
			</tr>
		</table>
	</div>

    <div style="clear:both;width:96%;">
		<input type="hidden" id="hdnCommentId" name="hdnCommentId" value="<?php echo $clCommentId; ?>" />
    	<textarea class="form-control" rows="1" name="cl_comment" id="cl_comment"><?php echo $clComments; ?></textarea>
    </div>
                   
            </div>
        </div>
   </div>
    <!-- main -->
	<!--<input type="hidden" name="replenishment1" id="replenishment1" value="<?php echo $replenishment; ?>" />
	<input type="hidden" name="wear_scheduler1" id="wear_scheduler1" value="<?php echo $wearScheduler; ?>" />
	<input type="hidden" name="disinfecting1" id="disinfecting1" value="<?php echo $disinfecting; ?>" />-->
</div> 

<?php
//functions --
function makeSCLRowWV($odos, $i, $rowName, $txtTot,$topPad, $strAcuitiesMrDisString,$strAcuitiesNearString,$arrMainValues,$chgBehave)
{
		$cylSign=$GLOBALS["def_cylinder_sign_cl"];
		switch($odos){
			case 'od':
				$DDName = 'clTypeOD';
				break;
			case 'os':
				$DDName = 'clTypeOS';
				break;
		}		
		
		$rowData='<div id="'.$rowName.$i.'" style="display:block; clear:both; height:48px" class="scl_bg">

				<div style=" float:left;width:7%; '.$topPad.'">
                  <select name="'.$DDName.$i.'" id="'.$DDName.$i.'" class="form-control minimal" onChange="changeCLType(this);changeRow(this.value, \''.$odos.'\', \''.$rowName.'\', \''.$txtTot.'\', \''.$i.'\',arrManufac, arrManufacId, arrManufacInfo);" '.$chgBehave.' style="padding-top:20px;width:100%;">
                    <option value="scl" selected="selected">SCL</option>
					<option value="rgp_soft">RGP Soft</option>
					<option value="rgp_hard">RGP Hard</option>
                    <option value="cust_rgp">Custom RGP</option>
					<option value="prosthesis">Prosthesis</option>
					<option value="no-cl">No-CL</option>
                  </select>
               </div>
               <div style="float:left;width:91%">
				<table class="table borderless" id="sclTable" style="width:99%;">'.
                  	/*'<script type="text/javascript">document.write(setTitleRows(\'scl\'));</script>';'.*/
			setTitleRows('scl');
					
			if($odos =='od') { 	
				  $rowData.='<tr>
    			    <td class="odcol">OD</td>
                    <td>
					  <input class="form-control" type="text" id="SclBcurveOD'.$i.'" name="SclBcurveOD'.$i.'" value="" style="width:100%;" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\'); copyValuesODToOS(\'SclBcurveOD'.$i.'\',\'SclBcurveOS'.$i.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');" ></td> 
                    <td class="txt_11b alignLeft">
					  <input  id="SclDiameterOD'.$i.'" type="text" class="form-control " name="SclDiameterOD'.$i.'" value="" style="width:100%;" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\'); copyValuesODToOS(\'SclDiameterOD'.$i.'\',\'SclDiameterOS'.$i.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');">
                    </td>
                    <td class="txt_11b alignLeft">
					  <input type="text" name="SclsphereOD'.$i.'" value="" class="form-control" style="width:100%;" id="SclsphereOD'.$i.'" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');">
                    </td>  
                    <td class="txt_11b nowrap alignLeft">
					  <input  id="SclCylinderOD'.$i.'" type="text" name="SclCylinderOD'.$i.'" value="" class="form-control" style="width:100%;" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');">                    
                    </td> 
                    <td class="txt_11b alignLeft">
					  <input type="text" name="SclaxisOD'.$i.'" value="" class="form-control" style="width:100%;" id="SclaxisOD'.$i.'" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\', \'noDecimal\');this.click();" onKeyUp="check2BlurCL(this,\'A\',\'\',\''.$rowName.$i.'\');" />                    
                    </td>
                    
                    <td class="txt_11b alignLeft">'.
                    '<div class="input-group" style="width:100%;">'.
			         '<input type="text" name="SclColorOD'.$i.'"  id="SclColorOD'.$i.'" value="" style="width:100%;" class="form-control" '.$chgBehave.' onfocus="setCursorAtEnd(this);" onblur="this.click();">'.
			           //getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","SclNvaOD".$i,0,0,array("pdiv"=>"divWorkView")).
			             getMenus("color", "SclColorOD".$i).
			         '</div>'.
                    '</td>
                    

                    <td class="txt_11b alignLeft">
                    <input  id="SclAddOD'.$i.'" type="text" class="form-control" name="SclAddOD'.$i.'" value="" style="width:100%;" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\'); copyValuesODToOS(\'SclAddOD'.$i.'\',\'SclAddOS'.$i.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');">
                    </td>
                    <td class="txt_11b alignLeft nowrap">'.
		    
		    //cl_makeCombo("SclDvaOD'.$i.'","$chgBehave")
				  '<div class="input-group" style="width:100%;">'.
                    '<input id="SclDvaOD'.$i.'" type="text" name="SclDvaOD'.$i.'" value="20/" style="width:100%;" class="form-control" '.$chgBehave.' onfocus="setCursorAtEnd(this);" onblur="this.click();">'.
		              //getSimpleMenu($arrAcuitiesMrDis,"menu_acuitiesMrDis","SclDvaOD".$i,0,0,array("pdiv"=>"divWorkView")).
		              getMenus("dva", "SclDvaOD".$i).
		          '</div>'.
                    '</td> 
                    <td class="txt_11b alignLeft nowrap" style="width:90px;">'.
                    '<div class="input-group" style="width:100%;">'.
			         '<input type="text" name="SclNvaOD'.$i.'"  id="SclNvaOD'.$i.'" value="20/" style="width:100%;" class="form-control" '.$chgBehave.' onfocus="setCursorAtEnd(this);" onblur="this.click();">'.
			           //getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","SclNvaOD".$i,0,0,array("pdiv"=>"divWorkView")).
			             getMenus("nva", "SclNvaOD".$i).
			         '</div>'.
                    '</td> 
                    <td  class="nowrap" >
                    <div class="od_typeahead_div alignLeft" style="float:left; width:100%;">
                        <input type="text" name="SclTypeOD'.$i.'"  id="SclTypeOD'.$i.'" class="typeAhead form-control" style="width:100%; background-image:none;" value=""/ '.$chgBehave.'>
						<input type="hidden" name="SclTypeOD'.$i.'ID" id="SclTypeOD'.$i.'ID" value="" funVars="worksheet~SclTypeOD'.$i.'~SclTypeOS'.$i.'~SclBcurveOD'.$i.'~SclDiameterOD'.$i.'">
                    </div> 
					</td>           
					<td class="nowrap" >
                    <div class="addicon" style="float:left">&nbsp;';
					if($i < $txtTot) {  
					$rowData.=	'<figure><span id="imgOD'.$i.'" class="glyphicon glyphicon-remove" style="cursor:pointer;" onClick="removeTableRow(\''.$rowName.$i.'\');" data-toggle="tooltip" title="Delete" data-original-title="Delete"></span></figure>';                                            
                     }else { 
					$rowData.=	'<figure><span id="imgOD'.$i.'" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi(\''.$DDName.$i.'\').value, \'od\', \''.$rowName.'\', \'imgOD\', \''.$i.'\',\'10\',\'\', arrManufac, arrManufacId, arrManufacInfo);" data-original-title="Add More"></span></figure>';
                     }
                    $rowData.='</div>';
                    $rowData.='
					</td>
                    </tr>';
			}
			if($odos =='os'){
				$rowData.='
					<tr>
					  <td class="oscol">OS</td>
                        <td><input type="text" name="SclBcurveOS'.$i.'" id="SclBcurveOS'.$i.'" value="" style="width:100%;" class="form-control" onblur="justify2DecimalCL(this, \''.$cylSign.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"> </td> 
                        <td><input id="SclDiameterOS'.$i.'" type="text" class="form-control " name="SclDiameterOS'.$i.'" value="" style="width:100%;" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"></td> 
                        <td><input type="text" name="SclsphereOS'.$i.'" value="" style="width:100%;" class="form-control" id="SclsphereOS'.$i.'" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"></td> 
                        <td><input class="form-control" id="SclCylinderOS'.$i.'" type="text" name="SclCylinderOS'.$i.'" value="" style="width:100%;" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"></td> 
                        <td class="alignLeft" ><input type="text" name="SclaxisOS'.$i.'" value="" style="width:100%;" class="form-control" id="SclaxisOS'.$i.'" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\', \'noDecimal\');" onKeyUp="check2BlurCL(this,\'A\',\'\',\''.$rowName.$i.'\');"></td>
                        <td class="txt_11b alignLeft nowrap" style="width:90px;">'.
                            '<div class="input-group" style="width:100%;">'.
        			             '<input type="text" name="SclColorOS'.$i.'"  id="SclColorOS'.$i.'" value="" style="width:100%;" class="form-control" '.$chgBehave.' onfocus="setCursorAtEnd(this);" onblur="this.click();">'.
                                    //getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","SclNvaOD".$i,0,0,array("pdiv"=>"divWorkView")).
                                getMenus("color", "SclColorOS".$i).
                            '</div>'.
                        '</td>
                        <td><input id="SclAddOS'.$i.'" type="text" class="form-control " name="SclAddOS'.$i.'"  value="" style="width:100%;" '.$chgBehave.' onblur="justify2DecimalCL(this, \''.$cylSign.'\');" onKeyUp="check2BlurCL(this,\'s\',\'\',\''.$rowName.$i.'\');"></td> 
                        <td class="valignTop nowrap">'.
                            '<div class="input-group" style="width:100%;">'.
			                     '<input id="SclDvaOS'.$i.'" type="text" name="SclDvaOS'.$i.'" value="20/" class="form-control" '.$chgBehave.' onfocus="setCursorAtEnd(this);" style="width:100%;z-index:0;" onblur="this.click();">'.
			                     //getSimpleMenu($arrAcuitiesMrDis,"menu_acuitiesMrDis","SclDvaOS".$i,0,0,array("pdiv"=>"divWorkView")).
			                     getMenus("dva", "SclDvaOS".$i).
			                 '</div>'.
                        '</td> 
                        <td class="nowrap">'.
                            '<div class="input-group" style="width:100%;">'.
			                     '<input type="text" name="SclNvaOS'.$i.'"  id="SclNvaOS'.$i.'" value="20/" class="form-control" '.$chgBehave.' onfocus="setCursorAtEnd(this);" style="width:100%;z-index:0;" onblur="this.click();"> '.
			                     //getSimpleMenu($arrAcuitiesNear,"menu_acuitiesNear","SclNvaOS".$i,0,0,array("pdiv"=>"divWorkView")).
			                     getMenus("nva", "SclNvaOS".$i).
			                 '</div>'.
                        '</td> 
                        <td class="nowrap">
						<div class=" alignLeft" style="float:left; width:100%;">
							<input type="text" name="SclTypeOS'.$i.'" id="SclTypeOS'.$i.'" class="typeAhead form-control" style="width:100%; background-image:none;" value="" '.$chgBehave.' />
							<input type="hidden" name="SclTypeOS'.$i.'ID" id="SclTypeOS'.$i.'ID" value="" funVars="halfFun~garbage1~garbage2~SclBcurveOS'.$i.'~SclDiameterOS'.$i.'">
						</div>
                        </td>
						<td class="nowrap">
						<div class="addicon" style="float:left;">&nbsp;';
						if($i < $txtTot) {  
							$rowData.=	'<figure><span id="imgOS'.$i.'" class="glyphicon glyphicon-remove style="cursor:pointer;" "onClick="removeTableRow(\''.$rowName.$i.'\');" data-toggle="tooltip" title="Delete" data-original-title="Delete"></span></figure>';                                            							
                        }else { 
							$rowData.='<figure><span id="imgOS'.$i.'" class="glyphicon glyphicon-plus" style="cursor:pointer;" onClick="addNewRow(dgi(\''.$DDName.$i.'\').value, \'os\', \''.$rowName.'\', \'imgOS\', \''.$i.'\',\'10\',\'\', arrManufac, arrManufacId, arrManufacInfo);" data-original-title="Add More"></span></figure>';							
						}
						$rowData.='</div>';
						$rowData.='									
                                    </td>
                                </tr>';
			}
                  $rowData.='</table>
               </div>
             </div>';  
	return $rowData;
}

function colorMenu($drawingDiv)
{
$colorMenu ='<div style="width:100px;">
	<div style="cursor:hand; height:11px; width:100%; padding:3px 0px 4px 0px;" onClick="changeColor(0,0,255,\''.$drawingDiv.'\')">
		<div class="fl" style="background-color:#0000FF; width:8px; height:10px;"></div>
		<div class="fl txt_10 black_color">Flat</div>
	</div>
	<div style="cursor:hand; height:11px; width:100%; padding:3px 0px 4px 0px" onClick="changeColor(255,255,0,\''.$drawingDiv.'\')">
		<div class="fl" style="background-color:#FFFF00; width:8px; height:10px;"></div>
		<div class="fl txt_10 black_color">Alignment</div>
	</div>                                
	<div style="cursor:hand; height:11px; width:100%; padding:3px 0px 4px 0px" onClick="changeColor(255,0,0,\''.$drawingDiv.'\')">
		<div class="fl" style="background-color:#FF0000; width:8px; height:10px;"></div>
		<div class="txt_10 black_color fl">Steep</div>
	</div> 
	<div style="cursor:hand; height:11px; width:100%; padding:3px 0px 3px 0px" onClick="changeColor(0,0,0,\''.$drawingDiv.'\')">
		<div class="fl" style="background-color:#000000; width:8px; height:10px;"></div>
		<div class="txt_10 black_color fl">Draw</div>
	</div>
	<div style="height:11px; clear:both" class="alignLeft"><img src="../../images/eraser.gif"  onClick="getclear(\''.$drawingDiv.'\');" style="cursor:hand;" alt="Eraser"></div>                                                                
 </div>';
 return $colorMenu;
}
?>
<script type="text/javascript">
	<?php if($clws_id == '' || $clws_id <= 0)
	{
	?>
		$("#copyFromId").val("0");
	<?php
	}else{
	?>
	    $("#copyFromId").val("<?php echo $clws_id; ?>");
	<?php
	}
	?>
	$('#txtTotOD').val('<?php echo $txtTotOD; ?>');
	$('#txtTotOS').val('<?php echo $txtTotOS; ?>');
	//alert("Line 1730");
	//applyTypeAhead();

	function checkTrial(checkBox, textBoxId){
		var checkBoxId = $(checkBox).attr('id');
		if($('#' + checkBoxId).is(":checked")){
			if($("#" + textBoxId).val() == "" || $("#" + textBoxId).val() == 0){
				$("#" + textBoxId).val("1");
			}
		}else{
			$("#" + textBoxId).val("");
		}
	}	

	function changeCLType(element){
		var clTypeElement = $(element);
		var id = clTypeElement.attr("id");
		id = id.replace("clType", "");
		if(id == "OD1"){
			id = "OD";
		}else{
			id = "OS";
		}
		if(clTypeElement.val() == "scl")
		{
			/*$("#RGPEvaluationPosBefore" + id).val("");
			$("#RGPEvaluationPosBeforeOther" + id).val("");
			$("#RGPEvaluationPosAfter" + id).val("");
			$("#RGPEvaluationPosAfterOther" + id).val("");
			$("#RGPEvaluationFluoresceinPattern" + id).val("");
			$("#RGPEvaluationInverted" + id).val("");*/
		}
		else if(clTypeElement.val() == "rgp" || clTypeElement.val() == "cust_rgp" || clTypeElement.val() == "rgp_soft" || clTypeElement.val() == "rgp_hard")
		{
			$("#CLSLCEvaluationComfort" + id).attr("name", "CLRGPEvaluationComfort" + id);
			$("#CLSLCEvaluationMovement" + id).attr("name", "CLRGPEvaluationMovement" + id);
		}
	}
	
	function clearLensMakeId(element){
		var field = $(element);
		$('#' + field.attr("id") + 'ID').val('0');
	}
	$("#replenishment1").val("<?php echo $replenishment; ?>");
	$("#wear_scheduler1").val("<?php echo $wearScheduler; ?>");
	$("#disinfecting1").val("<?php echo $disinfecting; ?>");
</script>