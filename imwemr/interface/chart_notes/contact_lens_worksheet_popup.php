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
 *
 * File: contact_lens_worsheet_popup.php
 * Purpose: To view contact lens worksheets
 */


include_once("../../config/globals.php");
include_once("cl_functions.php");

if($_GET['delete_comment_id'] && $_GET['delete_comment_id'] != ''){
	imw_query("update cl_comments set delete_status=1 where id='".$_GET['delete_comment_id']."'");
	echo "SUCCESS";
	exit(0);
}

$browser = $GLOBALS['gl_browser_name'];
$clForm_id = $_SESSION['finalize_id'];
if($_SESSION['form_id'] != ''){
    $clForm_id = $_SESSION['form_id'];
}

$dos = $_SESSION['DOS_FOR_CL'];
$chart_dos=$dos;

//GETTING ALL INFO OF ALL CHART NOTES OF PATIENT
$arrAllChartNotes=array();
$rs=imw_query("Select id, DATE_FORMAT(date_of_service, '".get_sql_date_format()."') as 'date_of_service' FROM chart_master_table WHERE patient_id='".$_SESSION["patient"]."'");
while($res=imw_fetch_assoc($rs)){
	$arrAllChartNotes[$res['id']]=$res['date_of_service'];
}


if($clForm_id>0){
	//Get DOS from chart note table
	$cur_date= $arrAllChartNotes[$clForm_id];
}else{
	$cur_date=  date(phpDateFormat());
}


if($GLOBALS['currency']){
	$currency=$GLOBALS['currency'];
}else{
	$currency='$';
}


//GET ALL LENS MANUFACTURER IN ARRAY
$arrLensManuf = getLensManufacturer();
//GET MANUF VALUES FOR B/C AND DIAMETER
$arrLensManufValues = getLensManufVals();
$strLensManufValues = implode(',',$arrLensManufValues);
//GET FEE OF DEFAULT CL CHARGES
$arrDefaultCPTFee = getCPTDefaultCharges();
//pre($arrDefaultCPTFee);

//COLOR
$arrColor=getLensColorArr(false);

$contactLensMaxMakeIdQuery = "Select max(make_id) as max_make_id from contactlensemake";
$contactLensMaxMakeIdResult = imw_query($contactLensMaxMakeIdQuery);
$res1 = imw_fetch_array($contactLensMaxMakeIdResult);
$contactLensMaximumMakeId = $res1['max_make_id'];

//MAKE ARRAY FOR TYPEAHEAD
$arrManufac=$arrManufacId=$arrManufacInfo=array();
$lensMakeArray = array();
$arr_replace=array("\r","\n","\t");
$qry="Select make_id, manufacturer, style, type, base_curve, diameter FROM contactlensemake 
WHERE del_status=0 ORDER BY style, type ASC";
$rs=imw_query($qry) or die(imw_error());
if(imw_num_rows($rs)>0){
	while($res=imw_fetch_array($rs)){
		$styleType = ''; $sep='';
		if($res['manufacturer']!=''){ $styleType = $res['manufacturer']; $sep='-';}
		if($res['style']!=''){ $styleType.=$sep.$res['style']; $sep='-';}
		if($res['type']!=''){ $styleType.=$sep.$res['type']; }
		$makeId = $res['make_id'];
		$styleType=addslashes(str_replace($arr_replace," : ", $styleType));
		if($styleType[strlen($styleType)-1] == "-"){
			$styleType = substr($styleType, 0, (strlen($styleType) - 1));
		}
		$manufacturerNameArray[] = $styleType;
		$manufacturerIdArray[$styleType] = $makeId;
		$manufacturerInfoArray[$makeId] = $res['base_curve'].'~'.$res['diameter'];
		$lensMakeArray[$makeId] = $styleType;
	}
}unset($rs);
//pre($lensMakeArray);
//json_encode($arrManufac);
//json_encode($arrManufacId);
json_encode($manufacturerInfoArray);

// GET CL CHARGES
$arrCLChargesAdmin=array();
$chargeRS=imw_query("Select * from cl_charges WHERE del_status=0 ORDER BY cl_charge_id");
while($chargeRES = imw_fetch_array($chargeRS)){
	$chargeRES['cpt_fee_id'].'<br>';	
	$cptPrice = $arrDefaultCPTFee[$chargeRES['cpt_fee_id']];
	$cptPrice = ($cptPrice<=0) ? '0' : $cptPrice; 
	$arrCLCharges[$chargeRES['name']] = $cptPrice.'~'.$chargeRES['cl_charge_id'];
	$arrCLChargesAdmin[$chargeRES['cl_charge_id']]=$chargeRES['name'].'~'.$cptPrice;
}

$arrCLChargesJS=$arrCLCharges;
$arrCLChargesJS['Take Home CL']='Take Home CL';
$arrCLChargesJS['Current CL']='Current CL';
$arrCLChargesJS['Final']='Final';
$arrCLChargesJS['Current Trial']='Current Trial';
$arrCLChargesJS['Update Trial']='Update Trial';
json_encode($arrCLChargesJS);
json_encode($arrCLChargesAdmin);

?>
<!DOCTYPE html>
<html lang="en">
<div id="prevLoader" style="display:none;;z-index:9999; margin-left:48%; margin-top:20%; position:absolute;"><img src="../../library/images/loading_image.gif" /></div>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

<link href="../../library/css/jquery-ui.min.1.12.1.css" rel="stylesheet" type="text/css">
<link href="../../library/css/workview.css" rel="stylesheet" type="text/css">
<link href="../../library/css/font-awesome.css" rel="stylesheet" type="text/css">
<link href="../../library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="../../library/css/bootstrap-select.css" rel="stylesheet" type="text/css">
<link href="../../library/css/common.css" rel="stylesheet" type="text/css">
<link href="../../library/css/style.css" rel="stylesheet" type="text/css">
<link href="../../library/messi/messi.css" rel="stylesheet" type="text/css">
<style>
    ul{
        list-style:none;
    }
    div.eyesiteopt ul li{
        display:block;
        text-align:left;
        padding:0px;
        margin:0px;
        height:29px;
        vertical-align:top;
    }
    div.eyesiteopt ul, .btndiv ul{
        width:165px;
        display:inline-block;
        padding-left:inherit;
        margin-right:5px;
    }
    #hiddendvadiv ul li, .btndiv ul li{
        margin-bottom:2px;
    }
    div.eyesiteopt ul li input{
        background:#FFFFFF;
        height:31px;
    }
    ul.labels{
        float:left;
        padding-left:0px;
        margin-left:10px;
        width:120px;
    }
    li.position{
        display:inherit;
        width:80px;
    }
    .h31{
        height:31px important;
    }
    .disabled{
        background:#FFFFFF;
        border:0px;
    }
    .btndiv ul{
        width:165px;
    }
    .contactlens-popup-scrollable-row{
        white-space:nowrap;
        overflow-x:scroll;
        float:none;
    }
    .contactlens-popup-scrollable-row > div{
        float:none;
        display:inline-block;
    }
    .form-control[disabled]{
        background:#FFFFFF;
        border:#ffffff;
        cursor:default;
    }
    .multiselect .bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
        width: 160px;
    }
    /*.emptytext{
        background:#FFFFFF;
        border:solid 1px #FFFFFF;
        cursor:default;
        /*display:none;
        visibility:hidden;
    }*/
	
	.copyopt [class*="col-"] {
    	margin-bottom: 0px !important;
	}
	
	.dropdown {
	  display:block;
	}	
	#evalSCLDiv h2{
		font-size:15px;
		font-weight:bold;
		color:#4129A0;
	}
	#evalSCLDiv .checkbox{ padding-left:10px;}

	.form-control{
		width:250px;
	}

	.os_left_margin{
		margin-left:44px;
	}

	.cl_comment_image{
		cursor:pointer;
	}
	
	input[type=text]::-ms-clear{
		display:none;
	}
</style>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.12.1.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/work_view/contact_lens.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/work_view/typeahead.js"></script>

<script type="text/javascript">
	var arrCLChargesAdmin=[];
	var arrCLChargesAdmin=<?php echo json_encode($arrCLChargesAdmin); ?>;
	//var clCommentsData = 

	//var arrCLOrder=[];
	//var arrCLOrder= <?php echo json_encode($arrCLOrder); ?>;

	var arrManufac = <?php echo "['" . implode("','", $manufacturerNameArray) . "'];"; ?>	
/*	var arrManufac=[];
	var arrManufacId=[];
	var arrManufacInfo=[];
	arrManufac = <?php echo json_encode($arrManufac); ?>;
	arrManufacId = <?php echo json_encode($arrManufacId); ?>;
	arrManufacInfo = <?php echo json_encode($arrManufacInfo); ?>;
*/
	var lensNameArray = [];
	var lensDetailsArray = [];
	<?php
		$lensNamesArray = array_keys($manufacturerIdArray);
		for($i = 0;$i < sizeof($lensNamesArray);$i++){
			echo "lensNameArray['".$lensNamesArray[$i]."'] = '".$manufacturerIdArray[$lensNamesArray[$i]]."';\n";
		}

		$lensDetArray = array_keys($manufacturerInfoArray);
		for($i = 0;$i < sizeof($lensDetArray);$i++){
			echo "lensDetailsArray['".$lensDetArray[$i]."'] = '".$manufacturerInfoArray[$lensDetArray[$i]]."';\n";
		}
	?>	

	function dgi(obj){
		return document.getElementById(obj);
	}

	var copyFromSheetId = 0;
	
</script>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<?php

include 'simpleMenuContent.php';
$oldFormId =$oldClwsId='';	$oldWorksheets= $strCopyFromOptions ='';

$sql = "SELECT id, fname, mname, lname from patient_data where id = '".$_SESSION["patient"]."'";
$rs = imw_query($sql);
$res = imw_fetch_array($rs);
$patName=$res["fname"]."&nbsp;".$res["mname"]."&nbsp;".$res["lname"]."&nbsp;-&nbsp;".$res["id"];
unset($rs);

$AllMenuesqry="SELECT currentWorksheetid,clws_trial_number,clws_id,DATE_FORMAT( `dos`,'".get_sql_date_format()."') as PreviousDOS, 
DATE_FORMAT( `clws_savedatetime`,'".get_sql_date_format()."') as savedDate, clws_type, form_id, del_status  
FROM contactlensmaster where patient_id='".$_SESSION["patient"]."' ORDER BY form_id DESC, clws_id DESC";
//ORDER BY dos DESC, clws_savedatetime DESC
$resAllMenues=@imw_query($AllMenuesqry);
if($resAllMenues){
	$strOptionsALLMenues="";
	$numRows=imw_num_rows($resAllMenues);
	if($numRows>0){
		$temPcurrentWorksheetid=0;
		while($resRowALL=imw_fetch_assoc($resAllMenues)){
			$sel=''; $selCopy='';
			$colorStyle='';
			
			$LabelsTrial=$resRowALL["clws_type"];
			$LabelsTrial = addslashes($LabelsTrial);
			$clws_types_arr = explode(",", $LabelsTrial);
			
			if(in_array('Current Trial', $clws_types_arr)){
				$LabelsTrial= str_replace('Current Trial', 'Current Trial #'.$resRowALL["clws_trial_number"], $LabelsTrial);
			}
			
			$sheet_dos=($resRowALL['form_id']>0)? $arrAllChartNotes[$resRowALL['form_id']] : $resRowALL['PreviousDOS'];
			
			if($oldFormId!=$resRowALL['form_id']){
				$oldWorksheets.= '<optgroup label="Sheets of DOS '.$sheet_dos.'">Sheets of DOS '.$sheet_dos.'</optgroup>';
				$strCopyFromOptions.= '<optgroup label="Sheets of DOS '.$sheet_dos.'">Sheets of DOS '.$sheet_dos.'</optgroup>';
			}
			if($_REQUEST['clws_id']==$resRowALL["clws_id"]){ $sel="selected";}
			if($resRowALL['clws_id']==$_GET['copySheetId']){ $selCopy="selected";}
			if($resRowALL['del_status']==1) { $colorStyle='style="color:#F00";'; }
			
			$oldWorksheets.= '<option value="'.$resRowALL["clws_id"].'" '.$sel.' '.$colorStyle.' >'.$resRowALL["savedDate"].' ('.$LabelsTrial.')&nbsp;</option>';
			$strCopyFromOptions.= '<option value="'.$resRowALL["clws_id"].'" '.$selCopy.' '.$colorStyle.' >'.$resRowALL["savedDate"].' ('.$LabelsTrial.')&nbsp;</option>';
			$oldFormId= $resRowALL['form_id'];
			if(empty($oldClwsId)==true){
				$oldClwsId= $resRowALL['clws_id'];
			}
		}unset($resRowALL);
	}
}

//CHARGES OPTIONS
$charges_options='';
//$charges_options='<option value="">Select Option</option>';
$s=1;
foreach($arrCLCharges as $name => $data){
	list($price,$charges_id) = explode('~', $data);
	$price = addSlashes($price);
	$charges_id = addSlashes($charges_id);
	$name = addSlashes($name);
	$charges_options.="<option value=\"".$charges_id.'~'.$price."\">".$name."</option>";
	$s++;
}
?>                 

<div class=" container-fluid">
<form action="" method="post" name="frmContactlensNew" id="frmContactlensNew" onSubmit="formSubmit(); return false;">
<div class="whtbox exammain ">

<div class="clearfix"></div>
<div class="conthead">
	<figure><img src="../../library/images/contact_icon.png" alt=""/></figure>
	<h2 class="col-xs-5">Contact Lens</h2>
<?php
    $patientResult = imw_query("select fname, mname, lname from patient_data where id = '".$_SESSION['patient']."'");
    $patientRow = imw_fetch_array($patientResult);
	echo "<span>".$patientRow['fname']." ".$patientRow['mname']." ".$patientRow['lname']; ?> - <?php echo $_SESSION['patient']."</span>";
?>

</div>
<div class="clearfix"></div>

<div class="row">
    <div class="col-xs-3 ">
    	<button class=" btn-newworkst" type="button" id="btnNewSheet" onClick="openNewWS('newSheetBtn');">New CL Worksheet</button>
    	<button class=" btn-mr" type="button" id="btnMR1" onClick="dispHideMR(this.id)">MR</button>
		<button class=" btn-mr" type="button" id="btnFCL" onClick="dispHideCL(<?php echo $isFinalExists; ?>);">Final CL</button>
    </div>

    <div class='col-xs-7 text-left'>
        <select name="oldSheets" id="oldSheets" onChange="javascript:reloadWindow();">
            <option value=""> - Select WorkSheet - </option>
            <option value="undeleted" <?php if($_REQUEST['clws_id']=='undeleted')echo 'selected';?>>Latest 15 Undeleted Worksheets</option>
            <option value="deleted" <?php if($_REQUEST['clws_id']=='deleted')echo 'selected';?>>Latest 15 Deleted Worksheets</option>
			<option value="all" <?php if($_REQUEST['clws_id']=='all')echo 'selected';?>>Latest 15 Worksheets</option>
            <?php echo $oldWorksheets; ?>
        </select> 
    </div>
    <div class='col-xs-2 text-right'>
        <a onClick="window.open('contact_lens_order_history.php','OrderHistory','location=0,status=1,resizable=1,left=10,top=80,scrollbars=no,width=1255,height=550');" ><button class='btn-orderhx' type='button' data-toggle='modal' data-target='#cl_orderhx'>Order HX</button></a>&nbsp;
    </div>
</div>

<div class="clearfix"></div>

<div class="row" id="clsheets" style="overflow-x:scroll; white-space: nowrap; float:left; display:inline-block;">
<?PHP
//SAVING OLD DRAWING IN SCAN DATABASE TABLE IF NOT ALREADY EXIST

//pre($_REQUEST);

//FETCHING DATA
$arrCLWS_IDs=array();
$arrFormIds=array();
$arrCLData=array();
$arrCLEval=array();

/* $isNewSheet = true;
if($_REQUEST['clws_id'] && $_REQUEST['clws_id'] > 0){
	$isNewSheet = false;
} */

//pre($_SESSION);

$maxCLSheetId = 0;
$patientTotalCLSheets = 0;

//IN CASE OF SEARCHED BY DELETED/UNDELETED/ALL WORKSHEETS DISPLAY ONLY LATEST 15 SHEETS TO AVOID BROWSER HANGING
$latestSheetsString='';
if($_REQUEST['clws_id']=='deleted' || $_REQUEST['clws_id']=='undeleted' || $_REQUEST['clws_id']=='all'){
	$latestSheetArray = array();
	$latestSheetsQry = "Select clws_id from contactlensmaster where patient_id = '".$_SESSION['patient']."'";
	if($_REQUEST['clws_id']=='deleted'){
		$latestSheetsQry.=" AND del_status='1'";
	}
	if($_REQUEST['clws_id']=='undeleted'){
		$latestSheetsQry.=" AND del_status='0'";
	}	
	$latestSheetsQry.=" ORDER BY clws_id desc limit 15";
	$latestSheetsResult= imw_query($latestSheetsQry);

	while($latestSheetsRow = imw_fetch_assoc($latestSheetsResult)){
		$latestSheetArray[$latestSheetsRow['clws_id']] = $latestSheetsRow['clws_id'];
	}

	if(sizeof($latestSheetArray)>0){
		$latestSheetsString = implode(",", $latestSheetArray);
	}
}

// Get latest undeleted sheet
//echo "select max(clws_id) as max_sheet_id, count(clws_id) as sheetCount from contactlensmaster where patient_id='".$_SESSION['patient']."' and del_status=0";
$maxCLSheetResult = imw_query("select max(clws_id) as max_sheet_id, count(clws_id) as sheetCount from contactlensmaster where patient_id='".$_SESSION['patient']."' and del_status=0");
$maxCLSheetRow = imw_fetch_assoc($maxCLSheetResult);
$maxCLSheetId = $maxCLSheetRow['max_sheet_id'];
$patientTotalCLSheets = $maxCLSheetRow['sheetCount'];

$clCommentsWhereClause = " where clm.patient_id='".$_SESSION['patient']."' and clc.delete_status='0'";

$firstPageCLQuery = "";

$qry="Select cl.*, DATE_FORMAT(dos, '".get_sql_date_format()."') as 'dos', DATE_FORMAT(cl.clws_savedatetime , '".get_sql_date_format()."') AS worksheetdate, cl_det.* FROM contactlensmaster cl LEFT JOIN contactlensworksheet_det cl_det ON cl_det.clws_id = cl.clws_id WHERE cl.patient_id='".$_SESSION['patient']."'";
if(empty($_REQUEST['clws_id'])==false)
{
	if($_REQUEST['clws_id']=='deleted')
	{
		$qry.=" AND cl.del_status='1'";
		$clCommentsWhereClause .= " AND clm.del_status='1'";
		$qry .= " AND cl.clws_id in (".$latestSheetsString.")";
	}
	else if($_REQUEST['clws_id']=='undeleted')
	{
		$qry.=" AND cl.del_status='0'";
		$qry .= " AND cl.clws_id in (".$latestSheetsString.")";
	}
	else if($_REQUEST['clws_id']=='all')
	{
		$qry .= " AND cl.clws_id in (".$latestSheetsString.")";
	}
	else if($_REQUEST['clws_id']!='all') //IN THIS CASE SHEET ID EXISTS
	{
		if($_REQUEST['clws_id'] && $_REQUEST['clws_id']>0){
			$clwsIdStr = "";
			
			//GET ALL SHEETS OF SAME DOS
			$rs=imw_query("Select form_id FROM contactlensmaster WHERE clws_id='".$_REQUEST['clws_id']."'");
			$res=imw_fetch_assoc($rs);
			$cl_form_id=$res['form_id'];
			unset($rs);
			
			if($cl_form_id){
				$rs=imw_query("Select clws_id FROM contactlensmaster WHERE form_id='".$cl_form_id."' AND patient_id='".$_SESSION['patient']."'");
				while($res=imw_fetch_assoc($rs))
				{
					$arrCLWS[$res['clws_id']]=$res['clws_id'];
				}
				unset($rs);
				$clwsIdStr=implode(',', $arrCLWS);
				$maxCLSheetId = $clwsIdStr;
				unset($arrCLWS);
			}
			$qry.=" AND cl.clws_id IN(".$maxCLSheetId.")";
			$clCommentsWhereClause .= " AND clm.clws_id in (".$maxCLSheetId.")";
		}
	}
	$qry.=" ORDER BY cl.clws_id ASC, cl_det.clEye, cl_det.id ASC";
}
else{	// Page opened for first time
	if($patientTotalCLSheets > 0){
		$qry.=" AND cl.clws_id in(".$maxCLSheetId.") and cl.del_status=0";
		$clCommentsWhereClause .= " AND clm.clws_id in (".$maxCLSheetId.")";
	}else{
		$qry.=" and cl.del_status=0";
	}
}
//echo $qry;
//echo "patientTotalCLSheets: ".$patientTotalCLSheets;
if($patientTotalCLSheets > 0){
	// Get contact lens comments
	$clCommentArray = array();
	$clCommentsQuery = "select clc.id as comment_id, clc.cl_sheet_id as sheet_id, clc.comment as comment_desc from cl_comments clc 
	join contactlensmaster clm on clc.cl_sheet_id=clm.clws_id ".$clCommentsWhereClause." order by clc.id desc";
	$clCommentsResult = imw_query($clCommentsQuery) or die(imw_error()." - ".$clCommentsQuery);
	while($clRow = imw_fetch_assoc($clCommentsResult)){
		$sheetId = $clRow['sheet_id'];
		$commentId = $clRow['comment_id'];
		$commentDesc = $clRow['comment_desc'];
		$clCommentArray[$sheetId][$commentId] = $commentDesc;
	}
}


//echo "<br />".$qry ;
$rs = imw_query($qry);
$i=$s=$old_clws_id=0;
$arrTempOldArray=array();
while($res =imw_fetch_assoc($rs)){ 
	if($res['clEye']!='OU'){
		$clws_id=$res['clws_id'];
		$clEye=$res['clEye'];
		$eyeL=strtolower($clEye);
		$eye1Cap=ucfirst($eyeL);
		
		
		if($old_clws_id>0 && $old_clws_id!=$clws_id){ $s=$s+1; }
		if($arrTempOldArray[$clws_id][$clEye]){
			$i=$i+1;
		}else{
			$i=0;
		}
	
		if($clEye=='OD'){
			$arrCLData[$s][$clEye][$i]['OD_ID']=$res['id'];
		}else if($clEye=='OS'){
			$arrCLData[$s][$clEye][$i]['OS_ID']=$res['id'];
		}
			
		$arrCLData[$s][$clEye][$i]['clType'.$clEye]=$res['clType'];
		$arrCLData[$s][$clEye][$i]['clEye'.$clEye]=$res['clEye'];
	
		$arrCLData[$s][$clEye][$i]['clws_id']=$res['clws_id'];
		$arrCLData[$s][$clEye][$i]['id']=$res['id'];
	
		if(!$arrCLWS_IDs[$clws_id]){
			$dos= ($res['form_id']>0)? $arrAllChartNotes[$res['form_id']] : $res['dos'];
			$arrCLData[$s][$clEye][$i]['dos']=$dos;
			$arrCLData[$s][$clEye][$i]['clws_savedatetime']=$res['clws_savedatetime'];
			$arrCLData[$s][$clEye][$i]['clws_type']=$res['clws_type'];
			$arrCLData[$s][$clEye][$i]['clws_trial_number']=$res['clws_trial_number'];
			$arrCLData[$s][$clEye][$i]['currentWorksheetid']=$res['currentWorksheetid'];
			$arrCLData[$s][$clEye][$i]['AverageWearTime']=$res['AverageWearTime'];
			$arrCLData[$s][$clEye][$i]['Solutions']=$res['Solutions'];
			$arrCLData[$s][$clEye][$i]['Age']=$res['Age'];
			$arrCLData[$s][$clEye][$i]['DisposableSchedule']=$res['DisposableSchedule'];
			$arrCLData[$s][$clEye][$i]['form_id']=$res['form_id'];
			$arrCLData[$s][$clEye][$i]['del_status']=$res['del_status'];
			$arrCLData[$s][$clEye][$i]['charges_id']=$res['charges_id'];
			$arrCLData[$s][$clEye][$i]['worksheetdate']=$res['worksheetdate'];
			$arrCLData[$s][$clEye][$i]['cl_comment']=$res['cl_comment'];
			$arrCLData[$s][$clEye][$i]['cpt_evaluation_fit_refit']=$res['cpt_evaluation_fit_refit'];
			$arrCLData[$s][$clEye][$i]['usage_val']=$res['usage_val'];
			$arrCLData[$s][$clEye][$i]['allaround']=$res['allaround'];
			$arrCLData[$s][$clEye][$i]['wear_scheduler']=$res['wear_scheduler'];
			$arrCLData[$s][$clEye][$i]['replenishment']=$res['replenishment'];
			$arrCLData[$s][$clEye][$i]['disinfecting']=$res['disinfecting'];
			$arrCLData[$s][$clEye][$i]['prosthesis']=$res['prosthesis'];
			$arrCLData[$s][$clEye][$i]['prosthesis_val']=$res['prosthesis_val'];
			$arrCLData[$s][$clEye][$i]['no_cl']=$res['no_cl'];
			$arrCLData[$s][$clEye][$i]['no_cl_val']=$res['no_cl_val'];
			$arrCLWS_IDs[$clws_id]=$clws_id;
			$formId=$res['form_id'];
			$arrFormIds[$res['form_id']]=$formId;
			//GETTING OU VALUES SAVED IN FIRST ROW OF THIS SHEET
			$arrCLData[$s]['OU'][$i]['SclNvaOU']=$res['SclNvaOU'];
			$arrCLData[$s]['OU'][$i]['SclDvaOU']=$res['SclDvaOU'];
			$arrSheetInfo[$clws_id]['form_id']=$formId;
			$arrSheetInfo[$clws_id]['dos']=$dos;
		}
		if($res['clType']=='scl'){
			$arrCLData[$s][$clEye][$i]['SclBcurve'.$clEye]=$res['SclBcurve'.$clEye];
			$arrCLData[$s][$clEye][$i]['SclDiameter'.$clEye]=$res['SclDiameter'.$clEye];
			$arrCLData[$s][$clEye][$i]['Sclsphere'.$clEye]=$res['Sclsphere'.$clEye];
			$arrCLData[$s][$clEye][$i]['SclCylinder'.$clEye]=$res['SclCylinder'.$clEye];
			$arrCLData[$s][$clEye][$i]['SclAdd'.$clEye]=$res['SclAdd'.$clEye];
			$arrCLData[$s][$clEye][$i]['Sclaxis'.$clEye]=$res['Sclaxis'.$clEye];
			$arrCLData[$s][$clEye][$i]['SclColor'.$clEye]=$res['SclColor'.$clEye];
			$arrCLData[$s][$clEye][$i]['SclDva'.$clEye]=$res['SclDva'.$clEye];
			$arrCLData[$s][$clEye][$i]['SclNva'.$clEye]=$res['SclNva'.$clEye];
			$arrCLData[$s][$clEye][$i]['SclType'.$clEye]=$arrLensManuf[$res['SclType'.$clEye.'_ID']]['det'];
			$arrCLData[$s][$clEye][$i]['SclType'.$clEye.'_ID']=$res['SclType'.$clEye.'_ID'];
			
			//DRAWING
			//INSERT INTO SCAN DATABSE TABLE IF NOT EXIST THERE
			if($res['idoc_drawing_id']<=0 && $res['elem_SCL'.$eye1Cap.'DrawingPath']!=''){
				$qry="Insert INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing SET 
				toll_image='imgCorneaCanvas',
				drawing_for='DrawCL',
				drawing_image_path='".$res['elem_SCL'.$eye1Cap.'DrawingPath']."',
				row_created_by='1',
				row_created_date_time='".date('Y-m-d H:i:s')."',
				patient_id='".$_SESSION['patient']."',
				patient_form_id='".$formId."',
				row_visit_dos='".$dos."'";
				imw_query($qry);
				$res['idoc_drawing_id']=imw_insert_id();
				
				$qry="Update contactlensworksheet_det SET idoc_drawing_id='".$res['idoc_drawing_id']."' WHERE id='".$res['id']."'";
				imw_query($qry);
			}
			$arrCLData[$s][$clEye][$i]['elem_SCL'.$eye1Cap.'Drawing']=$res['elem_SCL'.$eye1Cap.'Drawing'];
			$arrCLData[$s][$clEye][$i]['hdSCL'.$eye1Cap.'DrawingOriginal']=$res['hdSCL'.$eye1Cap.'DrawingOriginal'];
			$arrCLData[$s][$clEye][$i]['elem_SCL'.$eye1Cap.'DrawingPath']=$res['elem_SCL'.$eye1Cap.'DrawingPath'];


			
			//CHEKING IF DRAWING DATA EXIST OR NOT
/*			if($res['idoc_drawing_id']>0){
				$qry1="Select id FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing WHERE id='".$res['idoc_drawing_id']."' AND drawing_image_path!=''";
				$rs1=imw_query($qry1);
				if(imw_num_rows($rs1)){$arrCLData[$s][$clEye][$i]['hasDrawing_'.$eyeL]='yes';}
			}unset($rs1);*/
						
		}else if($res['clType']=='rgp' || $res['clType']=='rgp_soft' || $res['clType']=='rgp_hard'){
			$arrCLData[$s][$clEye][$i]['RgpBC'.$clEye]=$res['RgpBC'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpDiameter'.$clEye]=$res['RgpDiameter'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCylinder'.$clEye]=$res['RgpCylinder'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpAxis'.$clEye]=$res['RgpAxis'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpOZ'.$clEye]=$res['RgpOZ'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCT'.$clEye]=$res['RgpCT'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpPower'.$clEye]=$res['RgpPower'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpColor'.$clEye]=$res['RgpColor'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpAdd'.$clEye]=$res['RgpAdd'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpDva'.$clEye]=$res['RgpDva'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpNva'.$clEye]=$res['RgpNva'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpType'.$clEye]=$arrLensManuf[$res['RgpType'.$clEye.'_ID']]['det'];
			$arrCLData[$s][$clEye][$i]['RgpType'.$clEye.'_ID']=$res['RgpType'.$clEye.'_ID'];
		}else if($res['clType']=='cust_rgp'){
			$arrCLData[$s][$clEye][$i]['RgpCustomBC'.$clEye]=$res['RgpCustomBC'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomDiameter'.$clEye]=$res['RgpCustomDiameter'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomCylinder'.$clEye]=$res['RgpCustomCylinder'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomAxis'.$clEye]=$res['RgpCustomAxis'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomOZ'.$clEye]=$res['RgpCustomOZ'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomCT'.$clEye]=$res['RgpCustomCT'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomPower'.$clEye]=$res['RgpCustomPower'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustom2degree'.$clEye]=$res['RgpCustom2degree'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustom3degree'.$clEye]=$res['RgpCustom3degree'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomPCW'.$clEye]=$res['RgpCustomPCW'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomColor'.$clEye]=$res['RgpCustomColor'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomBlend'.$clEye]=$res['RgpCustomBlend'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomEdge'.$clEye]=$res['RgpCustomEdge'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomAdd'.$clEye]=$res['RgpCustomAdd'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomDva'.$clEye]=$res['RgpCustomDva'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomNva'.$clEye]=$res['RgpCustomNva'.$clEye];
			$arrCLData[$s][$clEye][$i]['RgpCustomType'.$clEye]=$arrLensManuf[$res['RgpCustomType'.$clEye.'_ID']]['det'];
			$arrCLData[$s][$clEye][$i]['RgpCustomType'.$clEye.'_ID']=$res['RgpCustomType'.$clEye.'_ID'];
		}
		//pre($arrCLData);die;
		//DRAWING DATA
		$arrCLData[$s][$clEye][$i]['idoc_drawing_id_'.$eyeL]=$res['idoc_drawing_id'];
		$arrCLData[$s][$clEye][$i]['corneaSCL_od_desc']=$res['corneaSCL_od_desc'];
		$arrCLData[$s][$clEye][$i]['corneaSCL_os_desc']=$res['corneaSCL_os_desc'];		
		//CHEKING IF DRAWING DATA EXIST OR NOT
		$arrCLData[$s][$clEye][$i]['hasDrawing_'.$eyeL]='no';
		if($res['idoc_drawing_id']>0){
			$arrCLData[$s][$clEye][$i]['hasDrawing_'.$eyeL]='yes';
		}		

		$old_clws_id=$clws_id;
		$arrTempOldArray[$clws_id][$clEye]=$clEye;
	}
}

$arrCLEval=array();
if(sizeof($arrCLWS_IDs)>0){
	$strCLWS_IDs=implode(',', $arrCLWS_IDs);
	$qry="Select * FROM contactlens_evaluations cl_eval WHERE clws_id IN(".$strCLWS_IDs.")";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$clwsId=$res['clws_id'];
		$formId=$arrSheetInfo[$clws_id]['form_id'];
		$dos=$arrSheetInfo[$clws_id]['dos'];
		//SCL
		$arrCLEval[$clwsId]['CLSLCEvaluationSphereOD']=$res['CLSLCEvaluationSphereOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationCylinderOD']=$res['CLSLCEvaluationCylinderOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationPositionOD']=$res['CLSLCEvaluationPositionOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationPositionOtherOD']=$res['CLSLCEvaluationPositionOtherOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationAxisOD']=$res['CLSLCEvaluationAxisOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationDVAOD']=$res['CLSLCEvaluationDVAOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationSphereNVAOD']=$res['CLSLCEvaluationSphereNVAOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationCylinderNVAOD']=$res['CLSLCEvaluationCylinderNVAOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationAxisNVAOD']=$res['CLSLCEvaluationAxisNVAOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationNVAOD']=$res['CLSLCEvaluationNVAOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationComfortOD']=$res['CLSLCEvaluationComfortOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationMovementOD']=$res['CLSLCEvaluationMovementOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationCondtionOD']=$res['CLSLCEvaluationCondtionOD'];
		$arrCLEval[$clwsId]['CLSLCEvaluationSphereOS']=$res['CLSLCEvaluationSphereOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationCylinderOS']=$res['CLSLCEvaluationCylinderOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationPositionOS']=$res['CLSLCEvaluationPositionOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationPositionOtherOS']=$res['CLSLCEvaluationPositionOtherOS'];		
		$arrCLEval[$clwsId]['CLSLCEvaluationAxisOS']=$res['CLSLCEvaluationAxisOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationDVAOS']=$res['CLSLCEvaluationDVAOS'];		
		$arrCLEval[$clwsId]['CLSLCEvaluationSphereNVAOS']=$res['CLSLCEvaluationSphereNVAOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationCylinderNVAOS']=$res['CLSLCEvaluationCylinderNVAOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationAxisNVAOS']=$res['CLSLCEvaluationAxisNVAOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationNVAOS']=$res['CLSLCEvaluationNVAOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationComfortOS']=$res['CLSLCEvaluationComfortOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationMovementOS']=$res['CLSLCEvaluationMovementOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationCondtionOS']=$res['CLSLCEvaluationCondtionOS'];
		$arrCLEval[$clwsId]['CLSLCEvaluationDVAOU']=$res['CLSLCEvaluationDVAOU'];
		$arrCLEval[$clwsId]['CLSLCEvaluationNVAOU']=$res['CLSLCEvaluationNVAOU'];

		//RGP CUST-RGP
		$arrCLEval[$clwsId]['CLRGPEvaluationSphereOD']=$res['CLRGPEvaluationSphereOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationCylinderOD']=$res['CLRGPEvaluationCylinderOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationAxisOD']=$res['CLRGPEvaluationAxisOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationDVAOD']=$res['CLRGPEvaluationDVAOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationSphereNVAOD']=$res['CLRGPEvaluationSphereNVAOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationCylinderNVAOD']=$res['CLRGPEvaluationCylinderNVAOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationAxisNVAOD']=$res['CLRGPEvaluationAxisNVAOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationNVAOD']=$res['CLRGPEvaluationNVAOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationComfortOD']=$res['CLRGPEvaluationComfortOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationMovementOD']=$res['CLRGPEvaluationMovementOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationPosBeforeOD']=$res['CLRGPEvaluationPosBeforeOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationPosBeforeOtherOD']=$res['CLRGPEvaluationPosBeforeOtherOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationPosAfterOD']=$res['CLRGPEvaluationPosAfterOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationPosAfterOtherOD']=$res['CLRGPEvaluationPosAfterOtherOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationFluoresceinPatternOD']=$res['CLRGPEvaluationFluoresceinPatternOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationInvertedOD']=$res['CLRGPEvaluationInvertedOD'];
		$arrCLEval[$clwsId]['CLRGPEvaluationSphereOS']=$res['CLRGPEvaluationSphereOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationCylinderOS']=$res['CLRGPEvaluationCylinderOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationAxisOS']=$res['CLRGPEvaluationAxisOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationDVAOS']=$res['CLRGPEvaluationDVAOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationSphereNVAOS']=$res['CLRGPEvaluationSphereNVAOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationCylinderNVAOS']=$res['CLRGPEvaluationCylinderNVAOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationAxisNVAOS']=$res['CLRGPEvaluationAxisNVAOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationNVAOS']=$res['CLRGPEvaluationNVAOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationComfortOS']=$res['CLRGPEvaluationComfortOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationMovementOS']=$res['CLRGPEvaluationMovementOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationPosBeforeOS']=$res['CLRGPEvaluationPosBeforeOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationPosBeforeOtherOS']=$res['CLRGPEvaluationPosBeforeOtherOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationPosAfterOS']=$res['CLRGPEvaluationPosAfterOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationPosAfterOtherOS']=$res['CLRGPEvaluationPosAfterOtherOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationFluoresceinPatternOS']=$res['CLRGPEvaluationFluoresceinPatternOS'];
		$arrCLEval[$clwsId]['CLRGPEvaluationInvertedOS']=$res['CLRGPEvaluationInvertedOS'];
		
		$arrCLEval[$clwsId]['EvaluationRotationOD']=$res['EvaluationRotationOD'];
		$arrCLEval[$clwsId]['EvaluationRotationOS']=$res['EvaluationRotationOS'];
			
		//DRAWING
		//INSERT INTO SCAN DATABSE TABLE IF NOT EXIST THERE
		if($res['idoc_drawing_id_od']<=0 && $res['elem_conjunctivaOdDrawingPath']!=''){
			$qry="Insert INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing SET 
			toll_image='imgCorneaCanvas',
			drawing_for='DrawCL',
			drawing_image_path='".$res['elem_conjunctivaOdDrawingPath']."',
			row_created_by='1',
			row_created_date_time='".date('Y-m-d H:i:s')."',
			patient_id='".$_SESSION['patient']."',
			patient_form_id='".$formId."',
			row_visit_dos='".$dos."'";
			imw_query($qry);
			$res['idoc_drawing_id_od']=imw_insert_id();
			
			$qry="Update contactlens_evaluations SET idoc_drawing_id_od='".$res['idoc_drawing_id_od']."' WHERE id='".$res['id']."'";
			imw_query($qry);
		}
		if($res['idoc_drawing_id_os']<=0 && $res['elem_conjunctivaOsDrawingPath']!=''){
			$qry="Insert INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing SET 
			toll_image='imgCorneaCanvas',
			drawing_for='DrawCL',
			drawing_image_path='".$res['elem_conjunctivaOsDrawingPath']."',
			row_created_by='1',
			row_created_date_time='".date('Y-m-d H:i:s')."',
			patient_id='".$_SESSION['patient']."',
			patient_form_id='".$formId."',
			row_visit_dos='".$dos."'";
			imw_query($qry);
			$res['idoc_drawing_id_os']=imw_insert_id();
			
			$qry="Update contactlens_evaluations SET idoc_drawing_id_os='".$res['idoc_drawing_id_os']."' WHERE id='".$res['id']."'";
			imw_query($qry);
		}		
		$arrCLEval[$clwsId]['cornea_od_desc']=$res['cornea_od_desc'];
		$arrCLEval[$clwsId]['elem_conjunctivaOdDrawing']=$res['elem_conjunctivaOdDrawing'];
		$arrCLEval[$clwsId]['hdConjunctivaOdDrawingOriginal']=$res['hdConjunctivaOdDrawingOriginal'];
		$arrCLEval[$clwsId]['elem_conjunctivaOdDrawingPath']=$res['elem_conjunctivaOdDrawingPath'];
		$arrCLEval[$clwsId]['cornea_os_desc']=$res['cornea_os_desc'];
		$arrCLEval[$clwsId]['elem_conjunctivaOsDrawing']=$res['elem_conjunctivaOsDrawing'];
		$arrCLEval[$clwsId]['hdConjunctivaOsDrawingOriginal']=$res['hdConjunctivaOsDrawingOriginal'];
		$arrCLEval[$clwsId]['elem_conjunctivaOsDrawingPath']=$res['elem_conjunctivaOsDrawingPath'];
		
		$arrCLEval[$clwsId]['idoc_drawing_id_od']=$res['idoc_drawing_id_od'];
		$arrCLEval[$clwsId]['idoc_drawing_id_os']=$res['idoc_drawing_id_os'];

		//CHEKING IF DRAWING DATA EXIST OR NOT
		$arrCLEval[$clwsId]['hasDrawing_od']='no';
		$arrCLEval[$clwsId]['hasDrawing_os']='no';
		if($res['idoc_drawing_id_od']>0){
			$arrCLEval[$clwsId]['hasDrawing_od']='yes';
		}
		if($res['idoc_drawing_id_os']>0){
			$arrCLEval[$clwsId]['hasDrawing_os']='yes';
		}		
/*		if($res['idoc_drawing_id_od']>0){
			$qry1="Select id FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing WHERE id='".$res['idoc_drawing_id_od']."' AND drawing_image_path!=''";
			$rs1=imw_query($qry1);
			if(imw_num_rows($rs1)){$arrCLEval[$clwsId]['hasDrawing_od']='yes';}
		}
		unset($rs1);
*/		
/*		if($res['idoc_drawing_id_os']>0){
			$qry1="Select id FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing WHERE id='".$res['idoc_drawing_id_os']."' AND drawing_image_path!=''";
			$rs1=imw_query($qry1);
			if(imw_num_rows($rs1)){$arrCLEval[$clwsId]['hasDrawing_os']='yes';}
		}
		unset($rs1);
*/		
		//Set CL Eval Popup values in hidden fields for Reset Button
	/*	$clEvalOD = $clEvalOS ='';
		//SCL
		$clEvalOD= ($res['CLSLCEvaluationComfortOD']!='') ? $res['CLSLCEvaluationComfortOD'] : '';
		$clEvalOD.=($res['CLSLCEvaluationMovementOD']!='') ? '~'.$res['CLSLCEvaluationMovementOD'] : '~';
		$clEvalOD.=($res['CLSLCEvaluationPositionOD']!='') ? '~'.$res['CLSLCEvaluationPositionOD'] : '~';
		$clEvalOD.=($res['CLSLCEvaluationCondtionOD']!='') ? '~'.$res['CLSLCEvaluationCondtionOD'] : '~';
		
		$clEvalOS= ($res['CLSLCEvaluationComfortOS']!='') ? $res['CLSLCEvaluationComfortOS'] : '';
		$clEvalOS.=($res['CLSLCEvaluationMovementOS']!='') ? '~'.$res['CLSLCEvaluationMovementOS'] : '~';
		$clEvalOS.=($res['CLSLCEvaluationPositionOS']!='') ? '~'.$res['CLSLCEvaluationPositionOS'] : '~';
		$clEvalOS.=($res['CLSLCEvaluationCondtionOS']!='') ? '~'.$res['CLSLCEvaluationCondtionOS'] : '~';*/
		
		//--------------------------------------------
		
	}
	unset($rs);
}
json_encode($arrCLData);
json_encode($arrCLEval);

//pre($arrCLData);

//GET CL-REQ FOR CURRENT CHART NOTE
$arrFormIds[$clForm_id]=$clForm_id;
$arrCLOrder=array();
if(sizeof($arrFormIds)>0){
	$strFormIds=implode(',', $arrFormIds);
	$rs=imw_query("Select id, cl_order FROM chart_master_table WHERE id IN(".$strFormIds.")");
	while($res=imw_fetch_assoc($rs)){
		$arrCLOrder[$res['id']]=$res['cl_order'];
	}unset($rs);
	
}
json_encode($arrCLOrder);

//$clCommentsQuery = "select";

?>
<div id="mr1" style="width:100%;display:none;position:relative;">
    <?php @include("mrakvalues.php"); ?>
</div>

<div id="finalCLRx" style="width:100%;display:none;position:relative;">
	
	<?php
		$isCustRGP = false;
		$isRGP = false;
		$isScl = true;

		$maxFinalClwsIdResult = imw_query("select clws_id from contactlensmaster where clws_type like '%final%' order by clws_id desc limit 1");
		$isFinalExists = 1;
		if(imw_num_rows($maxFinalClwsIdResult) == 0){
			$isFinalExists = 0;
		}
		$maxFinalClwsIdRow = imw_fetch_array($maxFinalClwsIdResult);
		$maxClwsId = $maxFinalClwsIdRow['clws_id'];

		$imwResult = imw_query("select clm.dos as date_of_service, clm.clws_type as clws_type, clwsd.* from contactlensmaster clm left join contactlensworksheet_det clwsd on clm.clws_id = clwsd.clws_id where clm.patient_id = ".$_SESSION["patient"]." and clm.clws_id = ".$maxClwsId);
		$clTypeArray = array();
		while($clRow = imw_fetch_array($imwResult)){
			$clType = $clRow['clType'];
			if($clType == "scl"){
				$clTypeArray[] = "SCL";
			}
			else if($clType == "rgp" || $clType == "rgp_soft" || $clType == "rgp_hard"){
				$clTypeArray[] = "RGP";
			}
			else if($clType == "cust_rgp"){
				$clTypeArray[] = "CUSTOM_RGP";
			}
		}
		mysqli_data_seek($imwResult, 0);
		//echo "select clm.dos as date_of_service, clm.clws_type as clws_type, clwsd.* from contactlensmaster clm left join contactlensworksheet_det clwsd on clm.clws_id = clwsd.clws_id where clm.patient_id = ".$_SESSION["patient"]." and clm.clws_id = ".$maxClwsId;
		//pre($clTypeArray);
		$clArray = array();
		$count = 0;
		$sclString = "";
		$rgpString = "";
		$customRgpString = "";
		$finalString = "";
		while($clRow = imw_fetch_array($imwResult)){
			$dateOfService = $clRow['date_of_service'];
			$clEye = $clRow['clEye'];
			$clType = $clRow['clType'];
			$clArray[$dateOfService][$clEye]['CL_TYPE'] = $clType;
			$finalString .= "<tr>";
			if(in_array("CUSTOM_RGP", $clTypeArray)){
				//echo $clType."<br />";
				//echo "<br />".ucfirst($clType);//'CustomBC'.$clEye."<br />";
				if($clType == "cust_rgp"){
					$finalString .= "<td>".$clEye."</td>";
					$finalString .= "<td>Custom RGP</td>";
					$finalString .= "<td>".$lensMakeArray[$clRow['RgpCustomType'.$clEye.'_ID']]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomBC'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomDiameter'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomOZ'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomCT'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomPower'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomCylinder'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomAxis'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomColor'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustom2degree'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustom3degree'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomPCW'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomBlend'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomEdge'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomAdd'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomDva'.$clEye]."</td>";
					$finalString .= "<td>".$clRow['RgpCustomNva'.$clEye]."</td>";
				}else if($clType == "rgp" || $clType == "rgp_soft" || $clType == "rgp_hard"){
					$finalString .= "<td>".$clEye."</td>";
					$finalString .= "<td>RGP</td>";
					$finalString .= "<td>".$lensMakeArray[$clRow[ucfirst($clType).'Type'.$clEye.'_ID']]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'BC'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Diameter'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'OZ'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'CT'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Power'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Cylinder'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Axis'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Color'.$clEye]."</td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Add'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Dva'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Nva'.$clEye]."</td>";
				}else if($clType == "scl"){
					$finalString .= "<td>".$clEye."</td>";
					$finalString .= "<td>SCL</td>";
					$finalString .= "<td>".$lensMakeArray[$clRow[ucfirst($clType).'Type'.$clEye.'_ID']]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Bcurve'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Diameter'.$clEye]."</td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'sphere'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Cylinder'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'axis'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Color'.$clEye]."</td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Add'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Dva'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Nva'.$clEye]."</td>";
				}
			}else if(in_array("RGP", $clTypeArray)){
				if($clType == "rgp" || $clType == "rgp_soft" || $clType == "rgp_hard"){
					$finalString .= "<td>".$clEye."</td>";
					$finalString .= "<td>RGP</td>";
					$finalString .= "<td>".$lensMakeArray[$clRow[ucfirst($clType).'Type'.$clEye.'_ID']]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'BC'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Diameter'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'OZ'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'CT'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Power'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Cylinder'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Axis'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Color'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Add'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Dva'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Nva'.$clEye]."</td>";
				}else if($clType == "scl"){
					$finalString .= "<td>".$clEye."</td>";
					$finalString .= "<td>SCL</td>";
					$finalString .= "<td>".$lensMakeArray[$clRow[ucfirst($clType).'Type'.$clEye.'_ID']]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Bcurve'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Diameter'.$clEye]."</td>";
					$finalString .= "<td></td>";
					$finalString .= "<td></td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'sphere'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Cylinder'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'axis'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Color'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Add'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Dva'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Nva'.$clEye]."</td>";
				}
			}else if(in_array("SCL", $clTypeArray)){
				if($clType == "scl"){
					$finalString .= "<td>".$clEye."</td>";
					$finalString .= "<td>SCL</td>";
					$finalString .= "<td>".$lensMakeArray[$clRow[ucfirst($clType).'Type'.$clEye.'_ID']]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Bcurve'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Diameter'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'sphere'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Cylinder'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'axis'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Color'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Add'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Dva'.$clEye]."</td>";
					$finalString .= "<td>".$clRow[ucfirst($clType).'Nva'.$clEye]."</td>";
				}
			}
			$totalRows ++;
			$finalString .= "</tr>";
		}
		//echo "<br />".$isCustRGP."        ".$isRGP."<br />";
		//pre($clTypeArray);
		//pre($clArray);
	?>
	<table width="90%" cellpadding="5" cellspacing="5" border="0" style="margin-left:10px;margin-top:25px;">
		<tr><td colspan="20">Last contact lens final prescription</td></tr>
		<tr><td colspan="20" style="padding-bottom:5px;"></td></tr>
		<?php
		if(in_array("CUSTOM_RGP", $clTypeArray)){
			echo "<td></td>";
			echo "<td>Lens type</td>";
			echo "<td>Make</td>";
			echo "<td>Base curve</td>";
			echo "<td>Diameter</td>";
			echo "<td>OZ</td>";
			echo "<td>CT</td>";
			echo "<td>Sphere</td>";
			echo "<td>Cylinder</td>";
			echo "<td>Axis</td>";
			echo "<td>Color</td>";
			echo "<td>2°/W</td>";
			echo "<td>3°/W</td>";
			echo "<td>PC/W</td>";
			echo "<td>Blend</td>";
			echo "<td>Edge</td>";
			echo "<td>Add</td>";
			echo "<td>DVA</td>";
			echo "<td>NVA</td>";
		}else if(in_array("RGP", $clTypeArray)){
			echo "<td></td>";
			echo "<td>Lens type</td>";
			echo "<td>Make</td>";
			echo "<td>Base curve</td>";
			echo "<td>Diameter</td>";
			echo "<td>OZ</td>";
			echo "<td>CT</td>";
			echo "<td>Sphere</td>";
			echo "<td>Cylinder</td>";
			echo "<td>Axis</td>";
			echo "<td>Color</td>";
			echo "<td>Add</td>";
			echo "<td>DVA</td>";
			echo "<td>NVA</td>";
		}else{
			echo "<td></td>";
			echo "<td>Lens type</td>";
			echo "<td>Make</td>";
			echo "<td>Base curve</td>";
			echo "<td>Diameter</td>";
			echo "<td>Sphere</td>";
			echo "<td>Cylinder</td>";
			echo "<td>Axis</td>";
			echo "<td>Color</td>";
			echo "<td>Add</td>";
			echo "<td>DVA</td>";
			echo "<td>NVA</td>";
		}
		?>
		<?php $finalString; ?>
	</table>
</div>
        
<div id="column1" class="allaround" style="display:inline-block;position:relative;width:900px;min-width:900px;white-space:nowrap;vertical-align:top">
<div class="lensBlock">
<div class="contatbx">
<div class="head">
    <div class="row">
        <div class="col-xs-6 form-inline">
            <div class="col-xs-4">CL Visit Fee</div>
            <div id="clws_charges_div1" class="col-xs-5 form-group form-inline">
                <select class="selectpicker" name="clws_charges1[]" id="clws_charges1" multiple="multiple" onChange="checkSingle('1', 'test', this.name,'sheet');">
                    <?php echo $charges_options;?>
                </select>
			</div>
            <div class="col-xs-3 ">
	        	<button id="btnPrint1" type="button" class="btn-print" onClick="showCLTEACH(this.id,'printSheet',this);">print</button>
            </div>
        </div>
    
        <div class="col-xs-3 divUndelete"></div>
        <div class="col-xs-3">
        	<div class="col-xs-9 contdos dispDate">
	            DOS:&nbsp;<?php if($dos!="" && $dos!="0000-00-00") { print($dos); } else { print(date(phpDateFormat(date("Y-m-d")))); }?>
            </div>
            <div id="divAddRow1" class="col-xs-2">    
                <img id="imgNewColumn1" src="../../library/images/add_cont.png" alt="Add More" onClick="addNewColumn('1');"/>
                <img id="imgDeleteColumn1" src="../../library/images/delete_cont.png" alt="Delete Sheet" onClick="removeColumn(1);"/>
			</div>
        </div>
    </div>
</div>

<div class=" clearfix"></div>

<div id="clwsLabels1" class="contoption">
    <div class="row">
	<div class="col-sm-8">
    <div class="row">
     <?php
        $s=1; 
        $mClass='';
        $clws_types_arr = explode(',', $clws_type_sheet);
        foreach($arrCLCharges as $name => $price){
        ?>
		<div class="col-xs-<?php echo ceil(strlen($name)/4); ?>">
        	<label class="checkbox-inline">
          		<input type="checkbox" id="EvaluationChk<?php echo $s;?>" name="clws_type1"  value="<?php echo $name;?>" onClick="javascript:checkSingle('EvaluationChk<?php echo $s;?>', '<?php echo $price;?>',  this.name,'popup');" <?php if(in_array($name, $clws_types_arr)){echo("checked");}?>>
                <?php echo $name;?>
        	</label>
        </div>
		<?php $s++; } ?>        	
          
        <div class="col-xs-3">
        	<label class="checkbox-inline">
          		<input type="checkbox" id="CurrentCLChk" name="clws_type1" onClick="javascript:checkSingle('CurrentCLChk','0', this.name,'popup');"  value="Take Home CL" <?php if(in_array("Take Home CL", $clws_types_arr)){echo("checked");}?>>
                Take Home CL
        	</label>
        </div>
        
        <div class="col-xs-3">
        	<label class="checkbox-inline">
          		<input type="checkbox" id="takeHomeCLChk" name="clws_type1" onClick="javascript:checkSingle('takeHomeCLChk','0', this.name,'popup');"  value="Current CL" <?php if(in_array("Current CL", $clws_types_arr)){echo("checked");}?>>
                Current CL
        	</label>
        </div>        

        <div class="col-xs-3">
        	<label class="checkbox-inline">
          		<input type="checkbox" id="FinalChk" name="clws_type1" onClick="javascript:checkSingle('FinalChk','0', this.name,'popup');"  value="Final" <?php if(in_array("Final", $clws_types_arr)){echo("checked");}?>>
                Final
        	</label>
        </div>        

        <div class="col-xs-3 trial form-inline">
        	<label class="checkbox-inline">
          		<input type="checkbox" id="NewTrialChk" name="clws_type1" onClick="javascript:checkSingle('NewTrialChk','0', this.name,'popup'); enbDisTrialNo(this.name);"  value="Current Trial" <?php if(in_array("Current Trial", $clws_types_arr)){echo("checked");}?>>
                <input type="text" class="form-control" name="clws_trial_number1" id="clws_trial_number1" value="<?php if($Latestclws_trial_number>0){echo($Latestclws_trial_number);}?>" <?php if(!in_array("Current Trial", $clws_types_arr)) echo 'disabled'; ?>>
                Trial 
        	</label>
        </div>
    
        <div class="col-xs-3 trial form-inline">
        	<label >
         		<input type="text" class="form-control" name="otherSave1" id="otherSave1" value="<?php echo $otherSaveVal;?>" onBlur="javascript:checkSingle('otherSave','0', this.name,'popup');">
                Other 
        	</label>
        </div>
    
    </div>
    </div>
    <div class="col-sm-4"><div class="copticon">
	<img src="../../library/images/cont_icon1.png" title="Evaluation" alt="Evaluation" id="evaluation1" onClick="javascript: showEvaluationSCLOption(this.id,'block','');"/> 
    <img src="../../library/images/cont_icon2.png" title="CL Teach" alt="CL Teach" id="cl_teach1" onClick="javascript: showCLTEACH(this.id,'clteach',this);" /> 
    <img src="../../library/images/cont_icon3.png" title="Print Rx" alt="Print Rx" id="print_rx1" onClick="showCLTEACH(this.id,'printRxSheet',this);"/>
</div></div>
    
    
    </div>

</div>
<div class=" clearfix"></div>
<div class="copyfrm "><div class="row">
<div class="col-xs-6 form-inline">

<label for="">Copy From :</label> 
    <select class="form-control minimal" name="copyFromId1" id="copyFromId1" onChange="loadCopyFromWorkSheet(this.id, this.value, this);">
        <option value="0">Select Sheet</option>
        <?php print($strCopyFromOptions);?>
    </select>                
    
    <select class="form-control minimal" name="usage_val1" id="usage_val1">
        <option value="">-Usage-</option>
        <option value="Primary">Primary</option>
        <option value="Secondary">Secondary</option>
        <option value="Tertiary">Tertiary</option>
    </select>    

    <select class="form-control minimal" name="allaround1" id="allaround1">
        <option value="">-Select-</option>
        <option value="Computer">Computer</option>
        <option value="Distance">Distance</option>
        <option value="Final CL Refraction Rx">Final CL Refraction Rx</option>
        <option value="Final Refraction Rx">Final Refraction Rx</option>
        <option value="Monovision">Monovision</option>
        <option value="Monovision OD">Monovision OD</option>
        <option value="Monovision OS">Monovision OS</option>
        <option value="Multifocal">Multifocal</option>
        <option value="Near">Near</option>
        <option value="Occupational">Occupational</option>
        <option value="All Around">Primary</option>
        <option value="Sports">Sports</option>
    </select>    
</div>

<div class="col-xs-4">
	<input type="button" class="btn btn-success btn-bilateral" value="Copy to OS   >>" style="height:25px;border:solid 1px #FFFFFF;" onclick="copyAllValues(this, 'OD', 'OS');" style="display:inline;" />&nbsp;
	<input type="button" class="btn btn-success btn-bilateral" value="<<   Copy to OD" style="height:25px;border:solid 1px #FFFFFF;" onclick="copyAllValues(this, 'OS', 'OD');" style="display:inline;" />
</div>

<div class="col-xs-2 text-right">
	<label class="checkbox-inline">
  		<input type="checkbox" name="cl_order1" id="cl_order1" value="1"> CL-Req
	</label>
</div>

</div></div>

<div class=" clearfix"></div>
<div class="copyopt">
<div class="row">
	<!-- TITLES -->	
	<div style="float:left;width:216px;margin-right:5px">
        <div class="">
            <div class="divDrawing_od">
                <img src="../../library/images/eyeicon.png" id="DrawingPngImg_od1" title="Show/Hide Drawing" onClick="showAppletsDiv('od', this.id);"/>
                <input type="hidden" name="idoc_drawing_id_od1" id="idoc_drawing_id_od1" value="">
                <input type="hidden" name="description_A_od1" id="description_A_od1" value="">
                <input type="hidden" name="description_B_od1" id="description_B_od1" value="">
            </div>
            <div class="divDrawing_os" style="display:none"><div class="fl green_color">OS:</div>
<!--                <div class="fl">
                <img src="images/osDrawing.png" id="DrawingPngImg_os1" class="hand_cur" title="Show/Hide OS Drawing" onClick="showAppletsDiv('os', this.id);">
                <input type="hidden" name="idoc_drawing_id_os1" id="idoc_drawing_id_os1" value="">
                <input type="hidden" name="description_A_os1" id="description_A_os1" value="">
                <input type="hidden" name="description_B_os1" id="description_B_os1" value="">
                </div>-->
            </div>
        </div>
        <div class="conthght">Lens Type</div>
        <div class="conthght">Make</div>
        <div class="conthght">BC</div>
        <div class="conthght">Diameter</div>
        <div class="conthght rgpDivs" style="display:none">OZ (Optical Zone)</div>
        <div class="conthght rgpDivs" style="display:none">CT (Center Thickness)</div>                        
        <div class="conthght">Sphere/Power</div>
        <div class="conthght">Cylinder</div>
		<div class="conthght">Axis</div>
		<div class="conthght">Color</div>
        <div class="rgpCustDivs" style="display:none">
            <div class="conthght">2&deg;/W</div>
            <div class="conthght">3&deg;/W</div>
            <div class="conthght">PC/W</div>
        </div>
        <div class="rgpCustDivs" style="display:none">
            <div class="conthght">Blend</div>
            <div class="conthght">Edge</div>
        </div>
        <div class="conthght">Add</div>                        
        <div class="conthght">DVA</div>
        <div class="conthght">DVA OU</div>
        <div class="conthght">NVA</div>
        <div class="conthght">NVA OU</div>
        <div class="conthght">
			<button id="evalDva1" class="dvabut" type="button" onClick="javascript:dispHideDvaNva(this, 'evalDva');">Over Refraction (DVA)</button>            
        </div>
        <div class="evalDva" style="background-color:#aab3e6; display:none">
            <div class="conthght">Sphere</div>
            <div class="conthght">Cylinder</div>
            <div class="conthght">Axis</div>
            <div class="conthght">DVA</div>
            <div class="conthght">DVA OU</div>
        </div>
        <div class="conthght">
            <button id="evalNva1" class="nvabut" type="button" onClick="javascript:dispHideDvaNva(this, 'evalNva');">Over Refraction (NVA)</button>
        </div>
        <div class="evalNva" style="background-color:#c9e4a8; display:none">
            <div class="conthght">Sphere</div>
            <div class="conthght">Cylinder</div>
            <div class="conthght">Axis</div>
            <div class="conthght">NVA</div>
            <div class="conthght">NVA OU</div>
        </div>
        <div class="conthght">Comfort</div>
        <div class="conthght">Movement</div>
        <div class="conthght">Rotation</div>
        <div class="sclDivs conthght">Condition</div>
        <div class="sclDivs conthght">Position</div>
        <div class="rgpDivs conthght" style="display:none;">Position B/Blink</div>
        <div class="rgpDivs conthght" style="display:none;">Position A/Blink</div>
        <div class="rgpDivs conthght" style="display:none;">Fluorescein Patter</div>
        <div class="rgpDivs conthght" style="display:none;">Inverted Lids</div>
        <div style="height:54px; margin-bottom:7px"></div>
        <div class="conthght" style="border:none;margin-bottom:7px;">
            <div class="fl">Charges&nbsp;<?php echo $currency; ?></div>
		</div>
		<div style="height:42px;">Comments</div>
      </div>  

		<!-- OD -->
        <div id="OD1" style="float:left; width:250px">
            <div class="eyesiteopt">
            	<span class="odcol">OD</span> 
	            <figure id="imgDivOD1">
					<span id="imgNewSubColOD1" class="glyphicon glyphicon-plus" onClick="addNewSubColumn(this.id);" data-toggle="tooltip" title="Add More" data-original-title="Insert"></span>
	            </figure>
            </div>    
            <div class=" clearfix"></div>
            <div class="conthght">
                <select name="clTypeOD1" id="clTypeOD1" class="form-control minimal" onChange="javascript: dispHideRgp(this.id);">
                    <option value="scl">SCL</option>
					<option value="rgp_soft">RGP Soft</option>
					<option value="rgp_hard">RGP Hard</option>
                    <option value="cust_rgp">Custom RGP</option>
                    <option value="prosthesis">Prosthesis</option>
                    <option value="no-cl">No-CL</option>
					
                </select>
            </div>
            <div class="conthght">
                <input type="text" name="elemMakeOD1" id="elemMakeOD1" class="form-control" value="" onKeyDown="clearMakeId(this);" onBlur="clearMakeId(this);" data-sort="contain" autocomplete="off" />
                <input type="hidden" name="elemMakeOD1ID" id="elemMakeOD1ID" value="" >
            </div>
            <div class="sclBc dropdown conthght width-100">
            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemBcOD1" name="elemBcOD1" value="">
                <?php echo $sclBcValues;?>
            </div>
            <div class="rgpBc dropdown conthght width-100" style="display:none">
            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemBcOD1" name="elemBcOD1" value="" disabled>
                <?php echo $rgpBcValues;?>
            </div>
            <div class="sclDiameter dropdown conthght width-100">
            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemDiameterOD1" name="elemDiameterOD1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $sclDiameterValues;?>
            </div>
            <div class="rgpDiameter dropdown conthght width-100"  style="display:none">
				<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemDiameterOD1" name="elemDiameterOD1" value="" onblur="justify2DecimalCL(this);" disabled>
           		<?php echo $rgpDiameterValues;?>	
            </div>
            <div class="rgpDivs conthght" style="display:none">
            	<input class="form-control" type="text" id="elemOZOD1" name="elemOZOD1" value="">
            </div>
            <div class="rgpDivs conthght" style="display:none">
            	<input class="form-control" type="text" id="elemCTOD1" name="elemCTOD1" value="">
            </div>
            <div id="sclpowerdiv" class="sclSphere dropdown conthght width-100" data-scroll="true">
				<input class="dropdown-toggle form-control clpower" data-toggle="dropdown" type="text" id="elemSphereOD1" name="elemSphereOD1" value="" onblur="justify2DecimalCL(this);">
           		<?php echo $sphereValues;?>
           </div>
            <div id="rgppowerdiv" class="rgpSphere dropdown conthght width-100" style="display:none">
				<input class="dropdown-toggle form-control clpower" data-toggle="dropdown" type="text" id="elemSphereOD1" name="elemSphereOD1" value="" onblur="justify2DecimalCL(this);" disabled>
				<?php echo $powerValues;?>
            </div>
            <div class="dropdown conthght width-100">
				<input class="dropdown-toggle form-control clcylinder" data-toggle="dropdown" type="text" id="elemCylinderOD1" name="elemCylinderOD1" value="" onblur="justify2DecimalCL(this, '<?php echo $GLOBALS["def_cylinder_sign_cl"]; ?>');">
           		 <?php echo $cylinderValues;?>
            </div>
            <div class="dropdown conthght width-100">
				<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemAxisOD1" name="elemAxisOD1" value="" onblur="justify2DecimalCL(this);" style="display:inline;">
				<?php echo $axisValues;?>               	
			</div>
			<div class="SclColor dropdown conthght width-100">
				<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemColorOD1" name="elemColorOD1" value="" onblur="justify2DecimalCL(this);" style="display:inline;" />
            	<?php echo $colorValues;?>        
            </div>
            <div class="rgpCustDivs" style="display:none">
                <div class="conthght"><input class="txt_10 form-control" type="text" id="elemTwoDegreeOD1" name="elemTwoDegreeOD1" value="" onblur="justify2DecimalCL(this);"></div>
                <div class="conthght"><input class="txt_10 form-control" type="text" id="elemThreeDegreeOD1" name="elemThreeDegreeOD1" value="" onblur="justify2DecimalCL(this);"></div>
                <div class="conthght"><input class="txt_10 form-control" type="text" id="elemPCWOD1" name="elemPCWOD1" value="" onblur="justify2DecimalCL(this);"></div>
            </div>
            <div class="rgpCustDivs" style="display:none">
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemBlendOD1" name="elemBlendOD1" value="" onblur="justify2DecimalCL(this);">
               		<?php echo $blendValues;?>    	
               </div>
                <div class="conthght"><input class="form-control" data-toggle="dropdown" type="text" id="elemEdgeOD1" name="elemEdgeOD1" value="" onblur="justify2DecimalCL(this);"></div>
            </div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemAddOD1" name="elemAddOD1" value="" onblur="justify2DecimalCL(this);">
             	<?php echo $addValues;?> 
             </div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemDvaOD1" name="elemDvaOD1" value="" onblur="justify2DecimalCL(this);">
               	<?php echo $dvaValues;?> 
                </div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemDvaOU1" name="elemDvaOU1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $dvaValues;?> 
                </div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemNvaOD1" name="elemNvaOD1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $nvaValues;?> 
                </div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemNvaOU1" name="elemNvaOU1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $nvaValues;?> 
                </div>                        
            <div class="conthght">&nbsp;</div>
            <div class="evalDva" style="display:none">
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control clpower" data-toggle="dropdown" type="text" id="elemDvaSphereOD1" name="elemDvaSphereOD1" value="" onblur="justify2DecimalCL(this);">
                    <?php echo $sphereValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
					<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemDvaCylinderOD1" name="elemDvaCylinderOD1" value="" onblur="justify2DecimalCL(this, '<?php echo $GLOBALS["def_cylinder_sign_cl"]; ?>');">
                    <?php echo $cylinderRefValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
					<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemDvaAxisOD1" name="elemDvaAxisOD1" value="" onblur="justify2DecimalCL(this);" />
                    <?php echo $axisValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemEvalDvaOD1" name="elemEvalDvaOD1" value="" onblur="justify2DecimalCL(this);">
                    <?php echo $dvaValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemEvalDvaOU1" name="elemEvalDvaOU1" value="" onblur="justify2DecimalCL(this);">
                    <?php echo $dvaValues;?> 
                    </div>                            
            </div>
            <div class="conthght">&nbsp;</div>
            <div class="evalNva" style="display:none">
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control clpower" data-toggle="dropdown" type="text" id="elemNvaSphereOD1" name="elemNvaSphereOD1" value="" onblur="justify2DecimalCL(this);">
                    <?php echo $sphereValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
					<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemNvaCylinderOD1" name="elemNvaCylinderOD1" value="" onblur="justify2DecimalCL(this, '<?php echo $GLOBALS["def_cylinder_sign_cl"]; ?>');" style="display:inline;">
					
                    <?php echo $cylinderRefValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
					<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemNvaAxisOD1" name="elemNvaAxisOD1" value="" onblur="justify2DecimalCL(this);" />
                    <?php echo $axisValues;?> 
                </div>
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemEvalNvaOD1" name="elemEvalNvaOD1" value="" onblur="justify2DecimalCL(this);" />
                    <?php echo $nvaValues;?> 
                </div>
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemEvalNvaOU1" name="elemEvalNvaOU1" value="" onblur="justify2DecimalCL(this);" />
                    <?php echo $nvaValues;?> 
                    </div>                            
            </div>
            <div class="eval">
                <div class="evalSCL dropdown conthght width-100">
                	<input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemComfortOD1"  id="elemComfortOD1" value="" >
                    <?php echo $comfortValues;?> 
                </div>
                <div class="evalSCL dropdown conthght width-100">
					<input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemMovementOD1"  id="elemMovementOD1" value="" style="display:inline;">
                    <?php echo $movementValues;?> 
                </div>
                <div class="evalSCL conthght">
					<input type="text" class="txt_10 form-control" name="elemRotationOD1"  id="elemRotationOD1" value="">
                </div>
                <div class="evalSCL dropdown conthght width-100 sclDivs">
					<input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemConditionOD1"  id="elemConditionOD1" value="">
                    <?php echo $conditionValues;?> 
                </div>
                <div class="evalSCL sclDivs conthght">
                	<div class="row">
                    	<div class="col-xs-6 dropdown">
	                        <input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemPositionOD1" style="width:120px;" id="elemPositionOD1" value="">
                        	<?php echo $positionValues;?> 
                        </div>
                        <div class="col-xs-6">    
	                        <input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemPositionOtherOD1" id="elemPositionOtherOD1" style="width:120px;" value="Other" onClick="clearVal(this.id);" onBlur="setVal(this.id);">
                        </div>    
					</div>
                </div>
                <div class="evalRGP rgpDivs conthght" style="display:none;">
                	<div class="row">
                    	<div class="col-xs-6 dropdown">
		                    <input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemPositionBOD1" style="width:120px;" id="elemPositionBOD1" value="">
                        	<?php echo $positionValues;?> 
                        </div>    
                        <div class="col-xs-6">
		                    <input type="text" class="txt_10 otherClass form-control" name="elemPositionBOtherOD1"  id="elemPositionBOtherOD1" style="width:120px;" value="Other" onClick="clearVal(this.id);" onBlur="setVal(this.id);">
                        </div>
                    </div>        
                </div>
                <div class="evalRGP rgpDivs conthght" style="display:none;">
                	<div class="row">
                    	<div class="col-xs-6 dropdown">                
		                    <input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemPositionAOD1" style="width:120px;" id="elemPositionAOD1" value="">
	                        <?php echo $positionValues;?> 
                        </div>    
                        <div class="col-xs-6 ">                
		                    <input type="text" class="txt_10 otherClass form-control" name="elemPositionAOtherOD1" id="elemPositionAOtherOD1" style="width:120px;" value="Other" onClick="clearVal(this.id);" onBlur="setVal(this.id);">
                        
                        </div>
                    </div>         
                </div>
                <div class="evalRGP rgpDivs dropdown conthght" style="display:none;">
                	<input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemFLPatterOD1" id="elemFLPatterOD1" value="">
                    <?php echo $flpValues;?> 
                </div>
                <div class="evalRGP rgpDivs dropdown conthght width-100" style="display:none;">
                	<input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemInvertedOD1" id="elemInvertedOD1" value="">
                    <?php echo $ilValues;?> 
                </div>
            </div>

            <div class="commonSheetData" style="margin-top:1px; white-space:nowrap; position:relative;  overflow:visible; height:54px; margin-bottom:7px">
                <div class="row" style="display:inline-table; position:absolute; top:0px; left:0px">
                    <div class="col-xs-4" style="display:table-cell; float:none" >
                        <label>Replenishment</label>
                        <select name="replenishment1" id="replenishment1" class="form-control minimal" style="width:130px" >
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
                    </div>
                    <div class="col-xs-4" style="display:table-cell; float:none">
                        <label>Wear Scheduler</label>
                        <select name="wear_scheduler1" id="wear_scheduler1" class="form-control minimal"  style="width:130px">
                            <option value=""></option>
                            <option value="As Needed">As Needed</option>
                            <option value="Bi-weekly">Bi-weekly</option>
                            <option value="Daily">Daily</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Weekly">Weekly</option>
                        </select>                        
                    </div>    
                    <div class="col-xs-4" style="display:table-cell; float:none">
                        <label>Disinfecting</label>
                        <select name="disinfecting1" id="disinfecting1" class="form-control minimal"  style="width:130px">
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
                    </div>  
                </div>                      
			</div>
			<div class="row" style="width:610px;margin-bottom:7px">
				<div class="col-xs-12">
					<input class="form-control" type="text" id="cpt_evaluation_fit_refit1" name="cpt_evaluation_fit_refit1" value="" >
				</div>
			</div>
			<div id="commentsdiv" class="row commentsdiv" style="width:610px;margin-bottom:10px;">
				<div class="col-xs-11">
					<textarea id="txtcomments" name="comment_new_column1[]" rows="2" class="form-control newcomment" style="width:100%;height:42px;display:inline;"></textarea>
				</div>
				<div class="col-xs-1 figure">
					<figure id='commentplus' class='comment_figure cl_comment_image' style='display:inline;'>
						<span id="plusspan" class="glyphicon glyphicon-plus cl_comment_image" onClick="addCommentBox(this, '', '', 'new', 1, 0);" title="Add More"></span>
					</figure>
				</div>
			</div>
            <input type="hidden" name="detIdOD1" id="detIdOD1" value="">
		</div>
		 <!-- OS -->
        <div id="OS1" style="float:left; width:250px; margin-left:5px;">
            <div class="eyesiteopt"><span class="oscol os_left_margin">OS</span> 
	            <figure id="imgDivOS1">
					<span id="imgNewSubColOS1" class="glyphicon glyphicon-plus" onClick="addNewSubColumn(this.id);" data-toggle="tooltip" title="Add More" data-original-title="Insert"></span>
	            </figure>
            </div>
            <div class=" clearfix"></div>
            <div class="conthght">
                <select name="clTypeOS1" id="clTypeOS1" class="form-control os_left_margin" onChange="javascript: dispHideRgp(this.id);">
                    <option value="scl">SCL</option>
					<option value="rgp_soft">RGP Soft</option>
					<option value="rgp_hard">RGP Hard</option>
                    <option value="cust_rgp">Custom RGP</option>
                    <option value="prosthesis">Prosthesis</option>
                    <option value="no-cl">No-CL</option>
					
                </select>
            </div>
            <div class="conthght" >
                <input type="text" name="elemMakeOS1" id="elemMakeOS1" class="form-control os_left_margin" value="" onKeyDown="clearMakeId(this);" onBlur="clearMakeId(this);" data-sort="contain" autocomplete="off" />
                <input type="hidden" name="elemMakeOS1ID" id="elemMakeOS1ID" value="">                        
            </div>
            <div class="sclBc dropdown conthght width-100">
            	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemBcOS1" name="elemBcOS1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $sclBcValues;?> 
                </div>
            <div class="rgpBc dropdown conthght width-100" style="display:none">
            	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown"type="text" id="elemBcOS1" name="elemBcOS1" value="" onblur="justify2DecimalCL(this);" disabled>
                <?php echo $rgpBcValues;?> 
                </div>
            <div class="sclDiameter dropdown conthght width-100">
            	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemDiameterOS1" name="elemDiameterOS1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $sclDiameterValues;?>
                </div>
            <div class="rgpDiameter dropdown conthght width-100" style="display:none">
            	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemDiameterOS1" name="elemDiameterOS1" value="" onblur="justify2DecimalCL(this);" disabled>
                <?php echo $rgpDiameterValues;?> 
                </div>                        
            <div class="rgpDivs conthght" style="display:none">
            	<input class="txt_10 form-control os_left_margin" type="text" id="elemOZOS1" name="elemOZOS1" value="">
                </div>
            <div class="rgpDivs conthght" style="display:none">
            	<input class="txt_10 form-control os_left_margin" type="text" id="elemCTOS1" name="elemCTOS1" value="">
                </div>
            <div class="sclSphere dropdown conthght width-100" data-scroll="true">
            	<input class="dropdown-toggle form-control clpower os_left_margin" data-toggle="dropdown" type="text" id="elemSphereOS1" name="elemSphereOS1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $sphereValues;?> 
                </div>
            <div class="rgpSphere dropdown conthght width-100" style="display:none">
            	<input class="dropdown-toggle form-control clpower os_left_margin" data-toggle="dropdown" type="text" id="elemSphereOS1" name="elemSphereOS1" value="" onblur="justify2DecimalCL(this);" disabled>
                <?php echo $powerValues;?> 
                </div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control clcylinder os_left_margin" data-toggle="dropdown" type="text" id="elemCylinderOS1" name="elemCylinderOS1" value="" onblur="justify2DecimalCL(this, '<?php echo $GLOBALS["def_cylinder_sign_cl"]; ?>');">
                <?php echo $cylinderValues;?> 
                </div>
            <div class="dropdown conthght width-100">
				<!--<input type="button" class="btn btn-success btn-bilateral" value=">>" style="height:25px;" onclick="clickBilateral(this, 'OD', 'OS');" style="display:inline;" />-->
				<button type="button" class="btn btn-success btn-bilateral" onclick="clickBilateral(this, 'OD', 'OS');" style="display:inline;">>></button>

            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemAxisOS1" name="elemAxisOS1" value="" style="display:inline;" />
                <?php echo $axisValues;?> 
			</div>
			<div class="SclColor dropdown conthght width-100">
				<!--<input type="button" class="btn btn-success btn-bilateral" value="<<" style="height:25px;" onclick="clickBilateral(this, 'OS', 'OD');" style="display:inline;">-->
				<button type="button" class="btn btn-success btn-bilateral" style="height:25px;" onclick="clickBilateral(this, 'OS', 'OD');" style="display:inline;"><<</button>

            	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemColorOS1" name="elemColorOS1" value="" onblur="justify2DecimalCL(this);" style="display:inline;"/>
                <?php echo $colorValues;?> 
			</div>
            <div class="rgpCustDivs" style="display:none">
                <div class="conthght"><input class="txt_10 form-control os_left_margin" type="text" id="elemTwoDegreeOS1" name="elemTwoDegreeOS1" value="" onblur="justify2DecimalCL(this);"></div>
                <div class="conthght"><input class="txt_10 form-control os_left_margin" type="text" id="elemThreeDegreeOS1" name="elemThreeDegreeOS1" value="" onblur="justify2DecimalCL(this);"></div>
                <div class="conthght"><input class="txt_10 form-control os_left_margin" type="text" id="elemPCWOS1" name="elemPCWOS1" value="" onblur="justify2DecimalCL(this);"></div>
            </div>
            <div class="rgpCustDivs" style="display:none">
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemBlendOS1" name="elemBlendOS1" value="" onblur="justify2DecimalCL(this);">
                    <?php echo $blendValues;?> 
                    </div>
                <div class="conthght"><input class="txt_10 form-control os_left_margin" type="text" id="elemEdgeOS1" name="elemEdgeOS1" value="<?php echo $CLResData[$i]['elemEdgeOS'];?>" onblur="justify2DecimalCL(this);"></div>
            </div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemAddOS1" name="elemAddOS1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $addValues;?> 
                </div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemDvaOS1" name="elemDvaOS1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $dvaValues;?> 
                </div>
            <div class="conthght">&nbsp;</div>
            <div class="dropdown conthght width-100">
            	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemNvaOS1" name="elemNvaOS1" value="" onblur="justify2DecimalCL(this);">
                <?php echo $nvaValues;?> 
                </div>
            <div class="conthght">&nbsp;</div>
            <div class="conthght">&nbsp;</div>
            <div class="evalDva" style="display:none">
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control clpower os_left_margin" data-toggle="dropdown" type="text" id="elemDvaSphereOS1" name="elemDvaSphereOS1" value="" onblur="justify2DecimalCL(this);">
                    <?php echo $sphereValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
					<!--<input type="button" class="btn btn-success" value=">>" style="height:25px;" onclick="copyOverRefractionDva(this, 'OD', 'OS');" style="display:inline;" />-->
					<button type="button" class="btn btn-success" onclick="copyOverRefractionDva(this, 'OD', 'OS');" style="display:inline;">>></button>
					
					<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemDvaCylinderOS1" name="elemDvaCylinderOS1" value="" onblur="justify2DecimalCL(this, '<?php echo $GLOBALS["def_cylinder_sign_cl"]; ?>');" style="display:inline;">
                    <?php echo $cylinderRefValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
					<!--<input type="button" class="btn btn-success" value="<<" style="height:25px;" onclick="copyOverRefractionDva(this, 'OS', 'OD');" style="display:inline;" />-->
					<button type="button" class="btn btn-success" onclick="copyOverRefractionDva(this, 'OS', 'OD');" style="display:inline;"><<</button>
                	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemDvaAxisOS1" name="elemDvaAxisOS1" value="" onblur="justify2DecimalCL(this);" style="display:inline;"/>
                    <?php echo $axisValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemEvalDvaOS1" name="elemEvalDvaOS1" value="" onblur="justify2DecimalCL(this);">
                    <?php echo $dvaValues;?> 
                    </div>                            
                <div class="conthght">&nbsp;</div>                                                
            </div>
            <div class="conthght">&nbsp;</div>
            <div class="evalNva" style="display:none">
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control clpower os_left_margin" data-toggle="dropdown" type="text" id="elemNvaSphereOS1" name="elemNvaSphereOS1" value="" onblur="justify2DecimalCL(this);">
                    <?php echo $sphereValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
					<!--<input type="button" class="btn btn-success" value=">>" style="height:25px;" onclick="copyOverRefractionNva(this, 'OD', 'OS');" style="display:inline;" />-->
					<button type="button" class="btn btn-success" onclick="copyOverRefractionNva(this, 'OD', 'OS');" style="display:inline;">>></button>
                	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemNvaCylinderOS1" name="elemNvaCylinderOS1" value="" onblur="justify2DecimalCL(this, '<?php echo $GLOBALS["def_cylinder_sign_cl"]; ?>');" style="display:inline;" />
                    <?php echo $cylinderRefValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
					<!--<input type="button" class="btn btn-success" value="<<" style="height:25px;" onclick="copyOverRefractionNva(this, 'OS', 'OD');" style="display:inline;" />-->
					<button type="button" class="btn btn-success" onclick="copyOverRefractionNva(this, 'OS', 'OD');" style="display:inline;"><<</button>
                	<input class="dropdown-toggle form-control" data-toggle="dropdown" type="text" id="elemNvaAxisOS1" name="elemNvaAxisOS1" value="" onblur="justify2DecimalCL(this);" style="display:inline;" />
                    <?php echo $axisValues;?> 
                    </div>
                <div class="dropdown conthght width-100">
                	<input class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" type="text" id="elemEvalNvaOS1" name="elemEvalNvaOS1" value="" onblur="justify2DecimalCL(this);" />
                    <?php echo $dvaValues;?> 
                    </div>                            
                <div class="conthght width-100">&nbsp;</div>                                                  
            </div>
            <div class="eval">
                <div class="dropdown conthght width-100">
                	<input type="text" class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" name="elemComfortOS1"  id="elemComfortOS1" value="" >
                    <?php echo $comfortValues;?> 
				</div>
                <div class="dropdown conthght width-100">
					<!--<input type="button" class="btn btn-success" value=">>" style="height:25px;" onclick="copyCLFittings(this, 'OD', 'OS');" style="display:inline;" />-->
					<button type="button" class="btn btn-success" onclick="copyCLFittings(this, 'OD', 'OS');" style="display:inline;">>></button>
                	<input type="text" class="dropdown-toggle form-control" data-toggle="dropdown" name="elemMovementOS1"  id="elemMovementOS1" value="" style="display:inline;">
                    <?php echo $movementValues;?> 
                    </div>
                <div class="conthght">
					<!--<input type="button" class="btn btn-success" value="<<" style="height:25px;" onclick="copyCLFittings(this, 'OS', 'OD');" style="display:inline;" />-->
					<button type="button" class="btn btn-success" onclick="copyCLFittings(this, 'OS', 'OD');" style="display:inline;"><<</button>
					<input type="text" class="txt_10 form-control" name="elemRotationOS1"  id="elemRotationOS1" value="" style="display:inline;" />
				</div>
                <div class="sclDivs dropdown conthght width-100">
                	<input type="text" class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" name="elemConditionOS1"  id="elemConditionOS1" value="">
                    <?php echo $conditionValues;?> 
                    </div>
                <div class="evalSCL sclDivs conthght">
                	<div class="row">
                    	<div class="col-xs-6 dropdown">
		                    <input type="text" class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown"  name="elemPositionOS1" style="width:120px;" id="elemPositionOS1" value="">
                        <?php echo $positionValues;?> 
                        </div>    
                        <div class="col-xs-6">
		                    <input type="text" class="txt_10 otherClass form-control" name="elemPositionOtherOS1"  id="elemPositionOtherOS1" style="width:120px;" value="Other" onClick="clearVal(this.id);" onBlur="setVal(this.id);">                                
                        </div>
                    </div>        
                </div>
                <div class="rgpDivs conthght" style="display:none;">
                	<div class="row">
                    	<div class="col-xs-6  dropdown">
		                    <input type="text" class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" name="elemPositionBOS1"  id="elemPositionBOS1" style="width:120px;" value="">
                        	<?php echo $positionValues;?> 
                        </div>    
                        <div class="col-xs-6 ">
		                    <input type="text" class="txt_10 otherClass form-control" name="elemPositionBOtherOS1"  id="elemPositionBOtherOS1" style="width:120px;" value="Other" onClick="clearVal(this.id);" onBlur="setVal(this.id);">
                        </div>
                    </div>        
                </div>
                <div class="rgpDivs conthght" style="display:none;">
                	<div class="row">
                    	<div class="col-xs-6  dropdown">
		                    <input type="text" class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" name="elemPositionAOS1" style="width:120px;" id="elemPositionAOS1" value="">
                        	<?php echo $positionValues;?> 
                        </div>
                        <div class="col-xs-6 ">
		                    <input type="text" class="txt_10 otherClass form-control" name="elemPositionAOtherOS1"  id="elemPositionAOtherOS1" style="width:120px;" value="Other" onClick="clearVal(this.id);" onBlur="setVal(this.id);">
                        </div>
                    </div>        
                </div>
                <div class="rgpDivs dropdown conthght width-100" style="display:none;">
                	<input type="text" class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" name="elemFLPatterOS1"  id="elemFLPatterOS1" value="">
                    <?php echo $flpValues;?> 
                    </div>
                <div class="rgpDivs dropdown conthght width-100" style="display:none;">
                	<input type="text" class="dropdown-toggle form-control os_left_margin" data-toggle="dropdown" name="elemInvertedOS1"  id="elemInvertedOS1" value="">
                    <?php echo $ilValues;?> 
                    </div>
            </div>

            <div class=""></div>
            <div class=""></div>
            <div class="" style="border:none"></div>
            
            <input type="hidden" name="detIdOS1" id="detIdOS1" value="">
		</div>
</div>
</div>
<div class=" clearfix"></div>


</div>
<div class=" clearfix"></div>
</div>

<input type="hidden" name="dos1" id="dos1" value="<?php echo $cur_date;?>">
<input type="hidden" name="LatestSavedWorksheetId1" id="LatestSavedWorksheetId1"  value="">
<input type="hidden" name="LatestSavedworksheetdos1" id="LatestSavedworksheetdos1"  value="">
<input type="hidden" name="recordSave1" id="recordSave1" value="saveTrue">
<input type="hidden" name="clws_id1" id="clws_id1" value="">
<input type="hidden" name="currentWorksheetid1" id="currentWorksheetid1" value="">
<input type="hidden" name="newSheetMode1" id="newSheetMode1" value="">
<input type="hidden" name="prvdos1" id="prvdos1" value="">
<input type="hidden" name="clws_types1" id="clws_types1" value="">
<input type="hidden" name="charges_id1" id="charges_id1" value="">
<input type="hidden" name="clGrp1" id="clGrp1" value="OU">
<input type="hidden" name="AverageWearTime1" id="AverageWearTime1" value="">
<input type="hidden" name="Solutions1" id="Solutions1" value="">
<input type="hidden" name="Age1" id="Age1" value="">
<input type="hidden" name="DisposableSchedule1" id="DisposableSchedule1" value="">
<input type="hidden" name="subsheetsOD1" id="subsheetsOD1" value="1">
<input type="hidden" name="subsheetsOS1" id="subsheetsOS1" value="1">
<input type="hidden" name="clEvalOD1" id="clEvalOD1" value="">
<input type="hidden" name="clEvalOS1" id="clEvalOS1" value="">
<input type="hidden" name="odOSame" id="odOSame" value="" />
<input type="hidden" name="maxCLMakeId" id="maxCLMakeId" value="<?php echo $contactLensMaximumMakeId; ?>" />
<input type="hidden" name="contact_lens_worksheet_popup" id="contact_lens_worksheet_popup" value="" />

</div>



</div>
<div class="clearfix"></div>
<div class="text-center"><button id="submit" name="submit" class="btn btn-success" type="submit">Save</button>
<button class="btn btn-success" type="button" onclick="showCLTEACH(1,'print_order',this);">Order</button>
<button class="btn btn-danger" type="button" onClick="javascrpit:window.self.close();">Close</button></div>
<div class="clearfix"></div>

<div class="clearfix"></div>

</div>


<!-- Evaluatuion Values -->
<div id='EvaluationSCLSavediv' class='modal common_modal_wrapper' style='position:absolute;z-index:100000;display:none;'>
<div class='modal-dialog' style='width:1500px;'>
<div class='modal-content'>
<div class='modal-header bg-primary'><button type='button' class='close' data-dismiss='modal' onClick="javascript:dgi('EvaluationSCLSavediv').style.display='none';">x</button><h4 class='modal-title'>CL Evaluation/Fittings - SLC</h4></div>
<div class='modal-body' style='max-height:300px;overflow:auto;overflow-y:scroll'>
<div class='whtbox assignmen'>
<div class='clearfix'></div>
<div class='plr5 clarea row'>
    <div class="col-xs-2">
        <div id="EvaluationRefitSavediv" class='clleftpan'>
            <label for=''>Average Wear Time</label>
            <input type="text" class="form-control" id="AverageWearTime" name="AverageWearTime" value="" onBlur="fillEvaluation(this.id,'txtBoxes')" style="width:200px;">
            <label for=''>Solutions</label>
            <input class="form-control" type="text" id="Solutions" name="Solutions" value="" onBlur="fillEvaluation(this.id,'txtBoxes')" style="width:200px;">
            <label for=''>Age</label>
            <input class="form-control" type="text" id="Age" name="Age" value="" onBlur="fillEvaluation(this.id,'txtBoxes')" style="width:200px;">
            <label for=''>Disposable Schedule</label>
            <input class="form-control" type="text" id="DisposableSchedule" name="DisposableSchedule" value="" onBlur="fillEvaluation(this.id,'txtBoxes')" style="width:200px;">
        </div>
    </div>
    <div id="evalSCLDiv" class="col-xs-10">
        <div class='bilthead' onclick="check_bl_chkbox('comfort,movement,position,condition')">Bilateral</div>
        <div class='clearfix'></div>
        <div class='plr10'>
                <div style="float:left; width:49%">
                <div class='clsect'>
                <div class='topod text-center'>OD</div>
                <div class='clearfix'></div>
                <div id="evalpopupodid" class='plr10 clcolum EVAL_POPUP_OD'>
					<div class='row'>
						<div class='col-sm-3'>
							<h2>Comfort</h2>
							<ul class="nav">
								<li>
									<?php
										$i=0;
										foreach($comfort as $val) {
											$val =  str_replace('"','',$val);
											echo '
											<div class="checkbox pointer">
												<input type="checkbox" id="comfortOD'.$i.'" onclick="javascript:fillEvaluation(\'comfortOD\',\'elemComfortOD\');" value="'.$val.'">
												<label for="comfortOD'.$i.'">'.$val.'</label>
											</div>
											';
											$i++;
										}
									?>               
								</li>
							</ul>
						</div>
						<div class='col-sm-3'>
							<h2>Movement</h2>
							<ul  class="nav">
								<li>
									<?php
										$i=0;
										foreach($movement as $val) {
											$val =  str_replace('"','',$val);
											echo '<div class="checkbox pointer">
												<input type="checkbox" id="movementOD'.$i.'" onclick="javascript:fillEvaluation(\'movementOD\', \'elemMovementOD\');" value="'.$val.'">
												<label for="movementOD'.$i.'">'.$val.'</label>
											</div>';
											$i++;
										}
									?> 
								</li>
							</ul>
						</div>
						<div class='col-sm-3'>
							<h2>Position</h2>
							<ul class="nav">
								<?php
									$i=0;
									foreach($position as $val) {
										$val =  str_replace('"','',$val);
										echo '<div class="checkbox pointer">
											<input type="checkbox" id="positionOD'.$i.'" onclick="javascript:fillEvaluation(\'positionOD\',\'elemPositionOD\');" value="'.$val.'">
											<label for="positionOD'.$i.'">'.$val.'</label>
										</div>';					
										$i++;
									}
								?> 
							</ul>
						</div>
						<div class='col-sm-3'>
							<h2>Condition</h2>
							<ul class="nav">
								<li>
									<?php
										$i=0;
										foreach($condition as $val) {
											$val =  str_replace('"','',$val);
											echo '<div class="checkbox pointer">
												<input type="checkbox" id="conditionOD'.$i.'" onclick="javascript:fillEvaluation(\'conditionOD\',\'elemConditionOD\');" value="'.$val.'">
												<label for="conditionOD'.$i.'">'.$val.'</label>
											</div>';
											$i++;
										}
									?> 
								</li>
							</ul>
						</div>
					</div>
                </div>
                </div>
                </div>

				<div style="float:left !important; width:4% !important;font-weight:bold; color:#9900CC; margin-top:80px; text-align:left" onclick="check_bl_chkbox('comfort,movement,position,condition')">
					<input type="button" class="btn btn-success" value=">>" style="width:40px;height:25px;" onClick="evalPopupBilateral('OD', 'OS');" /><br />
					<input type="button" class="btn btn-success" value="<<" style="width:40px;height:25px;margin-top:10px;" onClick="evalPopupBilateral('OS', 'OD');" />
				</div>
                
                <div style="float:left; width:47%">
                <div class='clsect'>
                <div class='topos  text-center'>OS</div>
                <div class='clearfix'></div>
                <div id="evalpopuposid" class='plr10 clcolum EVAL_POPUP_OS'>
                <div class='row'>
					<div class='col-sm-3'>
						<h2>Comfort</h2>
						<ul class="nav">
							<li>
								<?php
									$i=0;
									foreach($comfort as $val) {
										$val =  str_replace('"','',$val);
										echo '<div class="checkbox pointer">
											<input type="checkbox" id="comfortOS'.$i.'" onclick="javascript:fillEvaluation(\'comfortOS\',\'elemComfortOS\');" value="'.$val.'">
											<label for="comfortOS'.$i.'">'.$val.'</label>
										</div>';
										$i++;
									}
								?> 
							</li>              
						</ul>
					</div>
					<div class='col-sm-3'>
						<h2>Movement</h2>
						<ul class="nav">
							<li>
								<?php
									$i=0;
									foreach($movement as $val) {
										$val =  str_replace('"','',$val);
										echo '<div class="checkbox pointer">
											<input type="checkbox" id="movementOS'.$i.'" onclick="javascript:fillEvaluation(\'movementOS\',\'elemMovementOS\');" value="'.$val.'">
											<label for="movementOS'.$i.'">'.$val.'</label>
										</div>';
										$i++;
									}
								?>
							</li>
						</ul>
					</div>
					<div class='col-sm-3'>
						<h2>Position</h2>
						<ul class="nav">
							<li>
								<?php
									$i=0;
									foreach($position as $val) {
										$val =  str_replace('"','',$val);
										echo '<div class="checkbox pointer">
											<input type="checkbox" id="positionOS'.$i.'" onclick="javascript:fillEvaluation(\'positionOS\',\'elemPositionOS\');" value="'.$val.'">
											<label for="positionOS'.$i.'">'.$val.'</label>
										</div>';
										$i++;
									}
								?>
							</li>
						</ul>
					</div>
					<div class='col-sm-3'>
						<h2>Condition</h2>
						<ul class="nav">
							<li>
								<?php
									$i=0;
									foreach($condition as $val) {
										$val =  str_replace('"','',$val);
										echo '<div class="checkbox pointer">
											<input type="checkbox" id="conditionOS'.$i.'" onclick="javascript:fillEvaluation(\'conditionOS\',\'elemConditionOS\');" value="'.$val.'">
											<label for="conditionOS'.$i.'">'.$val.'</label>
										</div>';
										$i++;
									}
								?> 
							</li> 
						</ul>
					</div>
                </div>
                </div>
                </div>
                </div>
        </div>
    </div>

</div>
<div class='clearfix'></div>
</div>
</div>
<div id="module_buttons" class="modal-footer ad_modal_footer" style="position:relative;">
    <button type="button" class="btn btn-success" data-dismiss="modal" onClick="javascript:closeEvalDiv();">Done</button>
    <input type="hidden" name="evaluationHidden" id="evaluationHidden" value="" >
</div>
</div>
</div>
</div>
    

<input type="hidden" name="sheetscount" id="sheetscount" value="1">
<input type="hidden" name="form_id" id="form_id" value="<?php echo $clForm_id;?>">
<input type="hidden" name="ctrlListVals" id="ctrlListVals" value="<?php echo $strLensManufValues;?>">
<input type="hidden" name="delSubSheets" id="delSubSheets" value="">
</form>
</div>


</body>
<script type="text/javascript">

var clCommentsArray = <?php echo json_encode($clCommentArray); ?>;
function checkSingle(elemId, elemPrice, grpName, callFor)
{
	var selTypes = '';
	var charges_id = '';
	var checked=0;
	var noOfChkd= totPrice = 0;
	
	if(callFor=='sheet'){
		var arrVals=[];
		grpName=grpName.replace('[]','');
		var num= grpName.substr((grpName.length)-1);
		var ctrlObj=$('#'+grpName);
		var vall = ctrlObj.val();

		if(!!vall){
			vals=vall.toString();
			arrVals=vals.split(',');
		}

		// GET CHARGE IDS
		for(x in arrVals){
			arrV=arrVals[x].split('~');
			charges_id+= arrV[0]+',';
			totPrice= parseFloat(totPrice) +  parseFloat(arrV[1]);
			checked=1;
			noOfChkd+=1;
		}

		$('#cpt_evaluation_fit_refit'+num).val('0');
		separator='';

		$('#charges_id'+num).val(charges_id.substr(0, (charges_id.length)-1).trim());

		if(noOfChkd>0){
			$('#cpt_evaluation_fit_refit'+num).val(totPrice.toFixed(2));
			//$('#cpt_evaluation_fit_refit'+num).attr('readonly', true);
		}else{
			$('#cpt_evaluation_fit_refit'+num).attr('readonly', false);
		}
	}
	if(callFor=='popup'){
		var num= grpName.substr((grpName.length)-1);
		grpName='clws_type';
		var obgrp = $('#clwsLabels'+num+' input:checkbox[name^="'+grpName+'"]');
		var len = obgrp.length;

		for(var i=0;i<len;i++)
		{
			if(obgrp[i].checked == true)
			{
				selTypes+= obgrp[i].value+',';
				checked=1;
			}
		}
		
		separator='';
		document.getElementById('clws_types'+num).value	 = selTypes.substr(0, (selTypes.length)-1).trim();

		if(checked==1){ separator=',';}
		if(document.getElementById('otherSave'+num).value!=''){
			document.getElementById('clws_types'+num).value+= separator+dgi('otherSave'+num).value;	
		}		
	}
}

function showEvaluationSCLOption(id, showHide, mode){
	var num='';
	if(isNaN(id)){
		num=id.substr(-1);
	}else{
		id=num;
	}
	
	$('#evaluationHidden').val(num);
	//ADJUST AGE, DISPOSABLE ETC...
	$('#AverageWearTime').val($('#AverageWearTime'+num).val());
	$('#Solutions').val($('#Solutions'+num).val());
	$('#Age').val($('#Age'+num).val());
	$('#DisposableSchedule').val($('#DisposableSchedule'+num).val());
	
	if(showHide!=""){
		dgi("EvaluationSCLSavediv").style.display = showHide;//table-row
	
		if(mode=='reset'){
			var evals = dgi('clEvalOD').value.split("~");
			$('#elemComfortOD'+num).val(evals[0]);
			$('#elemMovementOD'+num).val(evals[1]);
			//POSITION VAL IS SAME FOR SCL & RGP
			$('#elemPositionOD'+num).val(evals[2]);
			$('#elemPositionBOD'+num).val(evals[2]);
			$('#elemConditionOD'+num).val(evals[3]);
			evals='';
			var evals = dgi('clEvalOS').value.split("~");
			$('#elemComfortOS'+num).val(evals[0]);
			$('#elemMovementOS'+num).val(evals[1]);
			$('#elemPositionOS'+num).val(evals[2]);
			$('#elemPositionBOS'+num).val(evals[2]);
			$('#elemConditionOS'+num).val(evals[3]);
		}
		
		// - OD -------------
		var comfortLen = $('input:checkbox[id^="comfortOD"]').length;
		var movementLen = $('input:checkbox[id^="movementOD"]').length;
		var positionLen = $('input:checkbox[id^="positionOD"]').length;
		var conditionLen = $('input:checkbox[id^="conditionOD"]').length;

		var comfortOD = $('#elemComfortOD'+num).val();
		var movementOD = $('#elemMovementOD'+num).val();
		var positionOD = $('#elemPositionOD'+num).val();

		if($('#elemPositionBOD'+num).val()!=''){
			var positionOD = $('#elemPositionBOD'+num).val();
		}
		var conditionOD = $('#elemConditionOD'+num).val();
	
		// - OS -------------
		var comfortLenOS = $('input:checkbox[id^="comfortOS"]').length;
		var movementLenOS = $('input:checkbox[id^="movementOS"]').length;
		var positionLenOS = $('input:checkbox[id^="positionOS"]').length;
		var conditionLenOS = $('input:checkbox[id^="conditionOS"]').length;
	
		var comfortOS = $('#elemComfortOS'+num).val();
		var movementOS = $('#elemMovementOS'+num).val();
		if($('#elemPositionOS'+num).val()!=''){
			var positionOS = $('#elemPositionOS'+num).val();
		}else if($('#elemPositionBOS'+num).val()!=''){
			var positionOS = $('#elemPositionBOS'+num).val();
		}
		var conditionOS = $('#elemConditionOS'+num).val();
		
		// OD ---------------------
		for(i=0; i< comfortLen; i++)
		{
			if(comfortOD.indexOf($('#comfortOD'+i).val()) > -1) {
				$('#comfortOD'+i).attr('checked', true);
			}else {
				dgi('comfortOD'+i).checked =  false;
			}
		}
		for(i=0; i< movementLen; i++)
		{
			if(movementOD.indexOf($('#movementOD'+i).val()) > -1) {
				$('#movementOD'+i).attr('checked', true);
			}else {
				dgi('movementOD'+i).checked =  false;
			}
		}
		for(i=0; i< positionLen; i++)
		{
			if(positionOD.indexOf($('#positionOD'+i).val()) > -1) {
				$('#positionOD'+i).attr('checked', true);
			}else {
				dgi('positionOD'+i).checked =  false;
			}
		}
		for(i=0; i< conditionLen; i++)
		{
			if(conditionOD.indexOf($('#conditionOD'+i).val()) > -1) {
				$('#conditionOD'+i).attr('checked', true);
			}else {
				dgi('conditionOD'+i).checked =  false;
			}
		}				
		// OS ---------------------
		for(i=0; i< comfortLenOS; i++)
		{
			if(comfortOS.indexOf($('#comfortOS'+i).val()) > -1) {
				$('#comfortOS'+i).attr('checked', true);
			}else {
				dgi('comfortOS'+i).checked =  false;
			}
		}
		for(i=0; i< movementLenOS; i++)
		{
			if(movementOS.indexOf($('#movementOS'+i).val()) > -1) {
				$('#movementOS'+i).attr('checked', true);
			}else {
				dgi('movementOS'+i).checked =  false;
			}
		}

		for(i=0; i< positionLenOS; i++)
		{
			if(positionOS.indexOf($('#positionOS'+i).val()) > -1) {
				$('#positionOS'+i).attr('checked', true);
			}else {
				dgi('positionOS'+i).checked =  false;
			}
		}
		for(i=0; i< conditionLenOS; i++)
		{
			if(conditionOS.indexOf($('#conditionOS'+i).val()) > -1) {
				$('#conditionOS'+i).attr('checked', true);
			}else {
				dgi('conditionOS'+i).checked =  false;
			}
		}
	}
}

function fillEvaluation(colName, txtField)
{
	num=$('#evaluationHidden').val();	
	if(txtField=='txtBoxes'){
		$('#'+colName+num).val($('#'+colName).val());

	}else{
		
		var strVal= '';
		colLength = ($('input:checkbox[id^="'+colName+'"]').length);
		for(i=0; i< colLength; i++)
		{
			if($('#'+colName+i).is(':checked')) {
				strVal+= $('#'+colName+i).val()+','; 
			}
		}
		strVal =strVal.substr(0, strVal.length-1);
		$('#'+txtField+num).val(strVal);
		
		if(colName.substr(0 ,8)=='position'){
			var startV= txtField.substr(0 , parseInt(txtField.length)-2);
			var endV= txtField.substr(-2);
			$('#'+startV+'B'+endV+num).val(strVal);
		}
	}
}

function check_bl_chkbox(str){
	var chkNamesVSFields=[];
	chkNamesVSFields['comfort']='elemComfort';
	chkNamesVSFields['movement']='elemMovement';
	chkNamesVSFields['position']='elemPosition';
	chkNamesVSFields['condition']='elemCondition';

	var chkNames = str.split(",");
	var strLen = chkNames.length;
	for(k=0;k<strLen; k++)
	{
		boxName = chkNames[k];
		elemName=chkNamesVSFields[boxName];
		var boxLen = $('input:checkbox[id^="'+boxName+'OD"]').length;
		
		for(j=0; j<boxLen;j++){
			str = boxName.slice(0,1).toUpperCase() + boxName.slice(1);
			if(document.getElementById(boxName+'OD'+j).checked==true){
				$('#'+boxName+'OS'+j).attr("checked",true);
				fillEvaluation(boxName+'OS', elemName+'OS');
			}else{
				document.getElementById(boxName+'OS'+j).checked=false;
				fillEvaluation(boxName+'OS', elemName+'OS');
			}
		}
	}
}	

function closeEvalDiv(){
	dgi('EvaluationSCLSavediv').style.display='none';
}

function showCLTEACH(num,strOpt,buttonRef)
{
	var workSheetID=0;
	var dosNew='';
	if(strOpt=='clteach' || strOpt=='printRxSheet' || strOpt=='printSheet'){
		id=num;	
		if(strOpt=='clteach'){
			id=id.replace('cl_teach','');
		}else if(strOpt=='printRxSheet'){
			id=id.replace('print_rx','');
		}else if(strOpt=='printSheet'){
			id = $(buttonRef).closest("div[id^='column']").attr("id").replace("column", "");
		}		
		num= id.trim();
	}
	
	if(strOpt=='print_order'){
		//FOUND LATEST COLUMN WITH CLWS_ID
		var found_clws_id=false;
		$('input[name^="clws_id"').each(function(index, element) {
            if(found_clws_id==false){
				if($(this).val()>0){
					tId=$(this).attr('id');
					num=tId.replace('clws_id', '');
					found_clws_id=true;
				}
			}
        });
	}

	if(document.frmContactlensNew.dos) {
		dosNew = document.frmContactlensNew.dos.value;
	}
	if(dgi("clws_id"+num) || dgi("LatestSavedWorksheetId"+num))
	{
		if(dgi("clws_id"+num).value!="" && dgi("clws_id"+num).value>0)
		{
			workSheetID=dgi("clws_id"+num).value;
		}
		else if(dgi("LatestSavedWorksheetId"+num).value!="" && dgi("LatestSavedWorksheetId"+num).value>0)
		{
			workSheetID=dgi("LatestSavedWorksheetId"+num).value;
			if($('#LatestSavedworksheetdos'+num))
			{
				dosNew = $('#LatestSavedworksheetdos'+num).val();//mm-dd-YYYY
			}
		}
		else
		{
			top.fAlert("No data exists.");
			return false;
		}
	 }

	if(strOpt=="clteach"){
		window.open("cl_teach_popup.php?workSheetID="+workSheetID,"clteach",'location=0,status=1,resizable=0,left=10,top=80,scrollbars=no,width=1450,height=480');
	}	
	if(strOpt=="print_order"){
		if(copyFromSheetId <= 0){
			copyFromSheetId = workSheetID;
		}
		window.open("print_order.php?clws_id="+copyFromSheetId+"&dos="+dosNew+"&callFrom=order","printOrder",'location=0,status=1,resizable=1,left=10,top=80,scrollbars=yes,width=1090,height=690');
	}		
	if(strOpt=="printSheet"){
		printWorkSheet(1,workSheetID);
	}
	if(strOpt=="printRxSheet"){
		printRx(1,workSheetID);
	}		
	 
}

function printRx(method,workSheetId){
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	winPrintMr = window.open('print_patient_contact_lenses.php?printType=2&method='+method+'&workSheetId='+workSheetId,'printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
}
function printWorkSheet(method,workSheetId){
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	winPrintMr = window.open('print_contactlens_sheet.php?printType=2&method='+method+'&workSheetId='+workSheetId,'printPatientContactWorksheet','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
}

function showSave(command){
	dgi('buttonSavediv').style.display='table-row';
	if(command=="close"){
	dgi('buttonSavediv').style.display='none';
	}
}

function addNewColumn(num, callFrom){
	newNum=parseInt(num)+1;
	targetHTML='<div id="column'+newNum+'" class="allaround" style="position:relative;display:inline-block;width:900px;min-width:900px;white-space:nowrap;vertical-align:top">';
	targetHTML+=$('#column'+num).html();
	targetHTML+='</div>';
	targetHTML = targetHTML.replace(/ui-autocomplete-input/, '');
	$('#clsheets').prepend(targetHTML);

	//REMOVING EXTRA OD/OS 
	var i=0;
	$('#column'+newNum+' div[id^="OD"]').each(function(index, element) {
		if(i>0){ removeColumn($(this).attr('id'), 'subColumn', 'copying', newNum);	}
		i++;
    });
	i=0;
	$('#column'+newNum+' div[id^="OS"]').each(function(index, element) {
        if(i>0){ removeColumn($(this).attr('id'), 'subColumn', 'copying', newNum);	}
		i++;
    });

	$('#column' + newNum + ' #over_refraction_dva').css('display', 'none');
	$('#column' + newNum + ' #over_refraction_nva').css('display', 'none');

	//SET WIDTH OF INNER DIV
	//$('#column'+newNum+' .lensBlock').css('width', '450px');

	
 	//CLEARING HIDDEN FIELDS
	$('#column'+newNum+' input[type="hidden"]').val('');
	//CLEARING CLWS TYPE LABELS
	$('#column'+newNum+' input[name="clws_type'+newNum+'"]').removeAttr('checked');

	$('#column' + newNum + ' .lensBlock').css('width', '900px');

	try{
		$('#column' + newNum + ' input, #column' + newNum + ' select').each(function(){
			var ctrlName= $(this).attr('name');
			if(ctrlName == undefined){
				return true;
			}
			ctrlLike=ctrlName.substr(0, (ctrlName.length)-1);

			if(ctrlLike!='clws_type'){
				if($(this).attr('id')){
					id=$(this).attr('id');
					if(id.indexOf('degreeO')>0){
						newId=id.substr(0,id.length-1)+newNum;
					}else{
						newId= id.replace(num, newNum);
					}
					$(this).attr('id', newId);
				}
			}
			if($(this).attr('name')){
				name=$(this).attr('name');
				newName= name.replace(num, newNum);
				newName= newName.replace(num, newNum);
				$(this).attr('name', newName);
			}
		});
	}
	catch(e){
		//console.log(e.message);
	}
	
	//HIDE RGP/CUST RGP DIVS
	$('#column'+newNum+' .rgpDivs').removeAttr('style');
	$('#column'+newNum+' .rgpCustDivs').removeAttr('style');
	$('#column'+newNum+' .rgpDivs').hide();
	$('#column'+newNum+' .rgpCustDivs').hide();
	//HIDE EVAL DVA/NVA BLOCKS
	$('#column'+newNum+' .evalDva').removeAttr('style');
	$('#column'+newNum+' .evalNva').removeAttr('style');
	$('#column'+newNum+' .evalDva:first').css('background-color', '#aab3e6');
	$('#column'+newNum+' .evalNva:first').css('background-color', '#c9e4a8');
	$('#column'+newNum+' .evalDva').hide();
	$('#column'+newNum+' .evalNva').hide();
	
	//SET CHART NOTE DOS
	if(callFrom!='fillingdata'){
		$('#column'+newNum+' .dispDate').html('DOS:&nbsp;'+'<?php echo $cur_date;?>');
		$('#dos'+newNum).val('<?php echo $cur_date;?>');
	}
	
	$('#column'+newNum+' #evalDva'+num).attr('id', 'evalDva'+newNum);
	$('#column'+newNum+' #evalNva'+num).attr('id', 'evalNva'+newNum);
	$('#column'+newNum+' #mr'+num).attr('id', 'mr'+newNum);
	$('#column'+newNum+' #comments'+num).attr('id', 'comments'+newNum);
	$('#column'+newNum+' #comments'+newNum).attr('name', 'comments'+newNum);
	//$('#column'+newNum+' #scl_odDrawingPngImg'+num).attr('id', 'scl_odDrawingPngImg'+newNum);
	$('#column'+newNum+' #DrawingPngImg_od'+num).attr('id', 'DrawingPngImg_od'+newNum);
	$('#column'+newNum+' #DrawingPngImg_os'+num).attr('id', 'DrawingPngImg_os'+newNum);
	//$('#column'+newNum+' #scl_osDrawingPngImg'+num).attr('id', 'scl_osDrawingPngImg'+newNum);
	//$('#column'+newNum+' #rgp_odDrawingPngImg'+num).attr('id', 'rgp_odDrawingPngImg'+newNum);
	//$('#column'+newNum+' #rgp_osDrawingPngImg'+num).attr('id', 'rgp_osDrawingPngImg'+newNum);
	$('#column'+newNum+' #divOverRefOD'+num).attr('id', 'divOverRefOD'+newNum);
	$('#column'+newNum+' #divAddRow'+num).attr('id', 'divAddRow'+newNum);
	$('#column'+newNum+' #divAddRow'+newNum).html('<img id="imgNewColumn'+newNum+'" src="../../library/images/add_cont.png" alt="Add More" onClick="addNewColumn('+newNum+');"/>');	
	$('#column'+newNum+' #imgDivOD'+num).attr('id', 'imgDivOD'+newNum);
	$('#column'+newNum+' #imgDivOS'+num).attr('id', 'imgDivOS'+newNum);
	$('#column'+newNum+' #imgNewSubColOD'+num).attr('id', 'imgNewSubColOD'+newNum);
	$('#column'+newNum+' #imgNewSubColOS'+num).attr('id', 'imgNewSubColOS'+newNum);
	$('#column'+newNum+' #clwsLabels'+num).attr('id', 'clwsLabels'+newNum);
	$('#clwsLabels'+newNum+' #clws_trial_number'+newNum).attr('disabled', 'true');
	$('#column'+newNum+' #clws_charges_div'+num).attr('id', 'clws_charges_div'+newNum);
	$('#column'+newNum+' #OD'+num).attr('id', 'OD'+newNum);
	$('#column'+newNum+' #OS'+num).attr('id', 'OS'+newNum);
	$('#column'+newNum+' .divDrawing_os').css('display','none');
	$('#column'+newNum+' #DrawingPngImg_od'+newNum).attr('src', '../../library/images/eyeicon.png');
	$('#column'+newNum+' #DrawingPngImg_os'+newNum).attr('src', '../../library/images/eyeicon.png');

	//SETTING OF OTHER FIELDS
	$('#column'+newNum+' .otherClass').each(function(index, element) {
    	setVal($(this).attr('id'));
    });
	
	//APPLYING TYPEAHEAD
	$("#elemMakeOD"+newNum).typeahead({source:arrManufac,scrollBar:true});
	var autocomplete = $("#elemMakeOD"+newNum).typeahead();	
	//autocomplete.data('typeahead').source = arrManufac;
	autocomplete.data('typeahead').updater = function(item){
		$("#elemMakeOD"+newNum+"ID").val(lensNameArray[item]);
		$("#elemBcOD"+newNum).val(lensDetailsArray[lensNameArray[item]].split('~')[0]);
		$("#elemDiameterOD"+newNum).val(lensDetailsArray[lensNameArray[item]].split('~')[1]);
    	return item;
    }
	$("#elemMakeOD"+newNum).blur(function() { set_properties("elemMakeOD"+newNum);  });
	
	//OS
	$("#elemMakeOS"+newNum).typeahead({source:arrManufac,scrollBar:true});
	var autocomplete = $("#elemMakeOS"+newNum).typeahead();
	//autocomplete.data('typeahead').source = arrManufac;
	autocomplete.data('typeahead').updater = function(item){
		$("#elemMakeOS"+newNum+"ID").val(lensNameArray[item]);
		$("#elemBcOS"+newNum).val(lensDetailsArray[lensNameArray[item]].split('~')[0]);
		$("#elemDiameterOS"+newNum).val(lensDetailsArray[lensNameArray[item]].split('~')[1]);
    	return item;
	}

	//HIDDEN FIELDS
	var fieldNames=new Array('LatestSavedWorksheetId','LatestSavedworksheetdos','recordSave','clws_id','currentWorksheetid',
	'newSheetMode','prvdos','clws_types','charges_id','clGrp','clEvalOD','clEvalOS');
	for(x in fieldNames){
		fName=fieldNames[x];
		$('#column'+newNum+' #'+fName+num).attr('id', fName+newNum);
		$('#column'+newNum+' #'+fName+num).attr('name', fName+newNum);
	}
	$('#column'+newNum+' #subsheetsOD'+newNum).val(1);
	$('#column'+newNum+' #subsheetsOS'+newNum).val(1);
	
	//SET IMAGES
	$('#column'+newNum+' #evaluation'+num).attr('id', 'evaluation'+newNum);
	$('#column'+newNum+' #cl_teach'+num).attr('id', 'cl_teach'+newNum);
	$('#column'+newNum+' #print_rx'+num).attr('id', 'print_rx'+newNum);
	//$('#divAddRow'+num).html('');
	$('#column'+num+' #imgNewColumn'+num).remove(); ;


	//GIVE CANCEL BUTTON TO LAST COLUMN
	//if(callFrom!='fillingdata'){
		$('#divAddRow'+newNum).append('<img id="imgDeleteColumn'+newNum+'" src="../../library/images/delete_cont.png" alt="Delete Sheet" onClick="removeColumn('+newNum+');"/>');
	//}
	//if(callFrom=='fillingdata' && newNum<dataLength){
	//	$('#divAddRow'+newNum).html('');
	//}
	

	//MAKE MULTISELECT
	var chargesOpt='<select class="selectpicker" name="clws_charges'+newNum+'[]" id="clws_charges'+newNum+'" multiple="multiple">';
	chargesOpt+=charges_options;
	chargesOpt+='</select>';
	$('#clws_charges_div'+newNum).html(chargesOpt);
	$("#clws_charges"+newNum).selectpicker('refresh');
	if(callFrom!='fillingdata'){
		makeMultiSelect(newNum);
	}

	//SET DEFAULT VALUES FOR SPHERE/POWER
	$('#elemSphereOD'+newNum).val('');
	$('#elemSphereOS'+newNum).val('');

	
	//SET PROSTHESIS BASED ON WORKVIEW
/*	var obj=window.opener.fmain.document;
	var pros_val=obj.getElementById('is_od_os').value;
	$('#column'+newNum+' #clTypeOD'+newNum).trigger("change");
	$('#column'+newNum+' #clTypeOS'+newNum).trigger("change");
	if(pros_val!=''){
		if(pros_val=='OU'){
			$('#column'+newNum+' #clTypeOD'+newNum).val('prosthesis');
			$('#column'+newNum+' #clTypeOD'+newNum).trigger("change");
			$('#column'+newNum+' #clTypeOS'+newNum).val('prosthesis');
			$('#column'+newNum+' #clTypeOS'+newNum).trigger("change");
		}else{
			$('#column'+newNum+' #clType'+pros_val+newNum).val('prosthesis');
			$('#column'+newNum+' #clType'+pros_val+newNum).trigger("change");
		}
	}*/

	//CLEAR UN-DELETE BUTTON IF DISPLAYING
	$('#column'+newNum+' .divUndelete').html('');
	
	
	$('#sheetscount').val(newNum);
	activateMenuData(newNum);

	setCLPowerDefaultToZero();
	setCLCylinderDefaultToZero();
	
	epost_addTypeAhead('textarea[id*=comments]','<?php echo $GLOBALS['rootdir'];?>');

	$("#column" + newNum + " #clTypeOD" + newNum).val('scl');
	$("#column" + newNum + " #clTypeOS" + newNum).val('scl');

	$("#column" + newNum + " #clTypeOD" + newNum).trigger('change');
	$("#column" + newNum + " #clTypeOS" + newNum).trigger('change');

	$("#column" + newNum + " textarea[name^='comment_update_']").attr('name', 'comment_new_column' + newNum + '[]');
	$("#column" + newNum + " textarea[name^='comment_new_column']").attr('name', 'comment_new_column' + newNum + '[]');

	$('#column' + newNum + ' .comments_text_div').remove();

	$('#column' + newNum).find("textarea").each(function(){
		$(this).val('');
	});
	
	//---CL SHEET PRINT BUTTON DISABLE, IF TRIAL IS CHECKED---	
	if($('#clwsLabels'+newNum+' #NewTrialChk').is(':checked'))
	{
		$('#column' + newNum + ' #btnPrint1').attr("disabled", true);
	}
	else
	{ 
		$('#column' + newNum + ' #btnPrint1').attr("disabled", false); 
	}
	
}

function addNewSubColumn(id, callFrom){
	//alert(id);
	
	if(id.lastIndexOf("_") > -1){
	  newNum = id.substring(id.lastIndexOf("_")+1);
	  num='_'+newNum;
	  newNum=parseInt(newNum)+1;
	  newNumber=newNum;
	  newNum='_'+newNum;
	  sourceId=id.substring(0,id.lastIndexOf("_"));
	}else{
	  newNumber=1;
	  newNum = '_1';
	  num='';
	  sourceId=id;
	}
	
	var odos='';
	if(id.indexOf('OD')>-1){ odos='OD';}else if(id.indexOf('OS')>-1){odos='OS';}else{odos='OU';}
	var start=parseInt(id.indexOf(odos))+2;
	var colNum=sourceId.substr(start);
	var oldColName=odos+colNum+num;
	var newColName=odos+colNum+newNum;
	var newColName1=colNum+newNum;

	//SET COL WIDTH
	var col_width=$('#'+oldColName).width();
	var inc=col_width + 10;
	var colWidth=$('#column'+colNum).css('width');
	colWidth=(parseInt(colWidth.replace('px',''))+inc)+'px';
	var colMinWidth=$('#column'+colNum).css('min-width');
	colMinWidth=(parseInt(colMinWidth.replace('px',''))+inc)+'px';
	var blockWidth=$('#column'+colNum+' .lensBlock').css('width');
	blockWidth=(parseInt(blockWidth.replace('px',''))+inc)+'px';
	$('#column'+colNum).css('width', colWidth);
	$('#column'+colNum).css('min-width', colMinWidth);
	$('#column'+colNum+' .lensBlock').css('width', blockWidth);	

	//CREATE COLUMN
	targetHTML='<div id="'+newColName+'" onKeyDown="clearMakeId(this);" onBlur="clearMakeId(this);" style="float:left; width:'+col_width+'px; margin-left: 5px;">';
	targetHTML+=$('#'+oldColName).html();
	targetHTML+='</div>';
	$(targetHTML).insertAfter($('#'+oldColName));

	$('#'+newColName+' .rgpCustDivs').removeAttr('style');
	$('#'+newColName+' .rgpCustDivs').css('display', 'none');	
	$('#'+newColName+' .rgpDivs').removeAttr('style');
	$('#'+newColName+' .rgpDivs').css('display', 'none');

		var os_id = id.replace("imgNewSubCol", "");

	//CLEAR HIDDEN FIELD
	$('#'+newColName+' input[type="hidden"]').val('');

	//REMOVING
	$('#'+newColName+' textarea').remove();
	$('#'+newColName+' input[id^="cpt_evaluation_fit_refit"').remove();
	$('#imgDiv'+oldColName).html('');
	/*if(odos=='OD'){
		var obj=$('#'+newColName+' #replenishment'+colNum).parent();
		$(obj).html('&nbsp;');
		$(obj).css('height', '52px');
	}else if(odos=='OS'){	
		var obj=$('#'+newColName+' #disinfecting'+colNum).parent();
		$(obj).html('&nbsp;');
		$(obj).css('height', '52px');
	}*/

	if($('#'+newColName+' #elemDvaOU'+colNum)){ $('#'+newColName+' #elemDvaOU'+colNum).parent().html('&nbsp;');	}
	if($('#'+newColName+' #elemNvaOU'+colNum)){ $('#'+newColName+' #elemNvaOU'+colNum).parent().html('&nbsp;');	}
	if($('#'+newColName+' #elemEvalDvaOU'+colNum)){ $('#'+newColName+' #elemEvalDvaOU'+colNum).parent().html('&nbsp;');}
	if($('#'+newColName+' #elemEvalNvaOU'+colNum)){ $('#'+newColName+' #elemEvalNvaOU'+colNum).parent().html('&nbsp;');}		
	if(odos=='OD'){
		$('#'+newColName+' .commonSheetData').html('');
		$('#'+newColName+' .commonSheetData').css('height', '0px');
	}

	//REMOVING EVALUATIONS
	$('#'+newColName+' .evalDva div').html('&nbsp;');
	$('#'+newColName+' .evalNva div').html('&nbsp;');
	$('#'+newColName+' .eval div').html('&nbsp;');
	

	//NAME CHANGING
	$('#'+newColName+' #imgDiv'+oldColName).attr('id', 'imgDiv'+newColName);
	$('#'+newColName+' #imgNewSubCol'+oldColName).attr('id', 'imgNewSubCol'+newColName);
/*	if(odos=='OD'){
		$('#'+newColName+' #scl_odDrawingPngImg'+colNum+num).attr('id', 'scl_odDrawingPngImg'+colNum+newNum);
		$('#'+newColName+' #rgp_odDrawingPngImg'+colNum+num).attr('id', 'rgp_odDrawingPngImg'+colNum+newNum);
		dgi('scl_odDrawingPngImg'+colNum+newNum).src="images/odDrawing.png";
		dgi('rgp_odDrawingPngImg'+colNum+newNum).src="images/odDrawing.png";
	}else if(odos=='OS'){
		$('#'+newColName+' #scl_osDrawingPngImg'+colNum+num).attr('id', 'scl_osDrawingPngImg'+colNum+newNum);		
		$('#'+newColName+' #rgp_osDrawingPngImg'+colNum+num).attr('id', 'rgp_osDrawingPngImg'+colNum+newNum);
		dgi('scl_osDrawingPngImg'+colNum+newNum).src="images/odDrawing.png";
		dgi('rgp_osDrawingPngImg'+colNum+newNum).src="images/odDrawing.png";
	}
*/
	
	//ADDING
	if($('#imgDiv'+newColName+' #imgDelSubCol'+oldColName)){
		$('#imgDiv'+newColName+' #imgDelSubCol'+oldColName).remove();
	}
	$('#imgDiv'+newColName).append('<span id="imgDelSubCol'+newColName+'" class="glyphicon glyphicon-remove" onClick="removeColumn(this.id, \'subColumn\',\'\',\'\');" data-toggle="tooltip" title="Delete" data-original-title="Delete"></span>');

	var copyNum='';
	try
	{
		$('#'+newColName+' input, #'+newColName+' select').each(function(){

			id=$(this).attr('id');
			oldNum=charID='';

			if(id.lastIndexOf("_")>-1){
				oldNum = id.substring(id.lastIndexOf("_") + 1);
			}
			if(id.lastIndexOf("_")>-1 && !isNaN(oldNum)){
				if(id.substring((id.length)-2, 2)=='ID'){
					id=id.replace('ID','');
					charID='ID';
				}
				
				oldNum = id.substring(id.lastIndexOf("_")+1);			  
				copyNum=parseInt(oldNum)+1;
				oldNum='_'+oldNum;
				copyNum='_'+copyNum;
				sourceId=id.substring(0,id.lastIndexOf("_"));
			}else{
				if(id.substring((id.length)-2)=='ID'){
					id=id.replace('ID','');
					charID='ID';				
				}
				copyNum = '_1';
				sourceId=id;
			}
			//alert("2408");

			if($(this).attr('id')){
				if(id.indexOf('degreeO')>0){
					newId=id.substr(0,id.length-1)+newNum;
				}else{
					newId=sourceId+copyNum+charID;
				}
				$(this).attr('id', newId);
				$(this).attr('name', newId);
			}
		});
	}
	catch(e){
		//alert($(this).attr('id'));
	}
	
	//APPLYING TYPEAHEAD
	//$("#elemMake"+odos+colNum+newNum).typeahead({source:arrManufac,scrollBar:true});
	var autocomplete = $("#elemMake"+odos+colNum+newNum).typeahead();
	//alert(autocomplete);
	//autocomplete.data('typeahead').source = arrManufac;
	//alert(autocomplete.data('typeahead').source);
	//alert(autocomplete.data('typeahead'));
	/* autocomplete.data('typeahead').updater = function(item){
		$("#elemMake"+odos+colNum+newNum+"ID").val(lensNameArray[item]);
		$("#elemBc"+odos+colNum+newNum).val(lensDetailsArray[lensNameArray[item]].split('~')[0]);
		$("#elemDiameter"+odos+colNum+newNum).val(lensDetailsArray[lensNameArray[item]].split('~')[1]);
    	return item;
    } */
	
	//if(odos=='OD'){
		$('#elemMake'+odos+colNum+newNum).blur(function() { set_properties('elemMake'+odos+colNum+newNum);  });
	//}
	
	$('#subsheets'+odos+colNum).val(parseInt(newNumber)+1);
	activateMenuData(newColName1);
	//USED TO HIDE/DISP TEXTBOXES
	dispHideRgp('clTypeOD'+colNum);	
	
	
	
	if(os_id.lastIndexOf('OS') > -1){
		$("div[id^=" + os_id + "]").each(function(){
			var tempId = $(this).attr('id');
			
			if(tempId.lastIndexOf('_') > -1){
				$("#" + tempId + " .btn-bilateral").remove();
				$("#" + tempId + " :input").each(function(){
					$(this).addClass('os_left_margin');
				});
			}
		});
	}

	if(os_id.lastIndexOf('OD') > -1){
		$("div[id^=" + os_id + "]").each(function(){
			var tempId = $(this).attr('id');
			if(tempId.lastIndexOf('_') > -1){
				$("#" + tempId + " .cl_comment_image").remove();
				$("#" + tempId + " textarea").remove();
			}
		});
	}

	if(callFrom=='fillingdata'){
		return newColName;
	}

	$("#clType" + newColName).find('option[value=rgp]').remove();


	/* $("#OS" + newColName1).prevAll().each(function(){
		var id1 = $(this).attr('id'); 
		$("#" + id1 + " .btn-bilateral").hide();
		if(id1 != undefined){
			if(id1.indexOf('_') > -1){
				$(this).css('width', '250px');
			}
		}
	}); */
}

// DELETE COLUMN
function removeColumn(num, callFrom, callFrom1, callColNum, cnfrm)
{
	//alert("num: " + num);
	var mainColId = $("#" + num).attr('id');
	if(mainColId) mainColId = mainColId.replace("imgDelSubCol", "");
	var nextId = $("#" + mainColId).next().attr("id");
	$("#" + nextId).css('width', '250px');
	$("#" + nextId + " .btn-bilateral").show();

	//PERFORM ACTION IF DELETE FOR DATABASE
	if(callFrom!='subColumn'){
		if($('#clws_id'+num).val()>0){
			if(typeof(cnfrm)=="undefined"){
				fancyConfirm('Are you sure to delete worksheet?', '','removeColumn("'+num+'", "'+callFrom+'", "'+callFrom1+'", "'+callColNum+'", true)');
				return;
			}
			else{
				deleteWorksheet('delete', num);
				return false;
			}
		}
	}
	

	if(callFrom=='subColumn'){
		id=num;

		if(id.indexOf("_")>-1){
			var arrT=id.split('_');
			var odos= (arrT[0].indexOf('OD')>-1) ? 'OD' : 'OS';
			var start=parseInt(arrT[0].indexOf(odos))+2;	
			colNum=arrT[0].substring(start);
			num = arrT[1];
			curNum='_'+arrT[1];
			oldNumber=parseInt(num)-1;
			if(oldNumber>=1){
			  oldNum='_'+oldNumber;
			}else{
				oldNum='';
			}
		}	
				
		if(callFrom1=='copying' || callFrom1=='copyFrom'){
			$('#column'+callColNum+' #'+odos+colNum+curNum).remove();
			
			//GIVE ADD BUTTON TO PREVIOUS DIV
			htmlData='<span id="imgNewSubCol'+odos+callColNum+oldNum+'" class="glyphicon glyphicon-plus"onclick="addNewSubColumn(this.id);" data-toggle="tooltip" title="Add More" data-original-title="Insert"></span>';
			if(oldNumber>=1){
				htmlData+='<span id="imgDelSubCol'+odos+callColNum+oldNum+'" class="glyphicon glyphicon-remove" onClick="removeColumn(this.id, \'subColumn\',\'\',\'\');" data-toggle="tooltip" title="Delete" data-original-title="Delete"></span>';
			}
			$('#column'+callColNum+' #imgDiv'+odos+colNum+oldNum).html(htmlData);			
			
			//FOR REDUCING WIDTH OF COLUMN
			if(callFrom1=='copyFrom'){ callFrom1=''; }
			
		} else{
			
			if($('#detId'+odos+colNum+curNum).val()>0){
				delId=$('#detId'+odos+colNum+curNum).val();
				delSheetIds=$('#delSubSheets').val();
				$('#delSubSheets').val(delSheetIds+','+delId);
			}

			$('#'+odos+colNum+curNum).remove();
			curSheets=$('#subsheets'+odos+colNum).val();
			$('#subsheets'+odos+colNum).val(parseInt(curSheets)-1);
				
			//GIVE ADD BUTTON TO PREVIOUS DIV
			htmlData='<span id="imgNewSubCol'+odos+colNum+oldNum+'" class="glyphicon glyphicon-plus" onClick="addNewSubColumn(this.id);" data-toggle="tooltip" title="Add More" data-original-title="Insert"></span>';
			if(oldNumber>=1){
				htmlData+='<span id="imgDelSubCol'+odos+colNum+oldNum+'" class="glyphicon glyphicon-remove" onClick="removeColumn(this.id, \'subColumn\',\'\',\'\');" data-toggle="tooltip" title="Delete" data-original-title="Delete"></span>';
			}
			$('#imgDiv'+odos+colNum+oldNum).html(htmlData);			
			
		}

		//SET COL WIDTH
		if(callFrom1==''){
			var col_width= $('#'+odos+colNum).width();
			var inc= col_width+10;
			var colWidth=$('#column'+colNum).css('width');
			colWidth=(parseInt(colWidth.replace('px',''))-inc)+'px';
			var colMinWidth=$('#column'+colNum).css('min-width');
			colMinWidth=(parseInt(colMinWidth.replace('px',''))-inc)+'px';
			var blockWidth=$('#column'+colNum+' .lensBlock').css('width');
			blockWidth=(parseInt(blockWidth.replace('px',''))-inc)+'px';
			$('#column'+colNum).css('width', colWidth);
			$('#column'+colNum).css('min-width', colMinWidth);
			$('#column'+colNum+' .lensBlock').css('width', blockWidth);			
		}
	}else{
		var htmlData='';
		var oldNum=parseInt(num)-1;
		var availableCols=$('div[id^="column"]').size();
		if(availableCols>1){
			$('#column'+num).remove();
		}else{
			//IF ONLY ONE SHEET LEFT THEN FIRE NEW SHEET FUNCTION RATHER THAN DELETE IT.
			openNewWS();
		}
		
			
		availableCols=$('div[id^="column"]').size();
		//GET AVAILABLE COLUMN AFTER DELETED COLUMN
		//if(!document.getElementById('clws_id'+oldNum)){
		//	for(i=oldNum; i>=1; i--){
		//		if(document.getElementById('clws_id'+i)){ oldNum=i; break; }
		//	}
		//}
		
		//GIVE ADD BUTTON TO PREVIOUS DIV
		//if(callFrom1!='deleteDatabase'){
			//$('#sheetscount').val(oldNum);
			//htmlData='<img id="imgNewColumn'+oldNum+'" class="fl link_cursor" style="margin-bottom:10px" onclick="addNewColumn('+oldNum+');" alt="Add More" src="../../images/add_medical_history.gif">';
			//htmlData+='<div style="clear:both;"><img id="imgDeleteColumn'+oldNum+'" class="link_cursor" src="../../images/cancelled.gif" alt="Delete Row" onClick="removeColumn('+oldNum+');"></div>';
			//$('#divAddRow'+oldNum).html(htmlData);
		//}
		
		//IF CALL FROM DELETE DATABSE THEN GIVE BUTTONS TO LAST REMAIN SHEET
		var lastColumn='';
		$('div[id^="column"]').each(function(index, element){
		   lastColumn=$(this).attr('id'); 
		   return false;
		});

		if(lastColumn!=''){
			var num=0;
			var htmlData='';
			num=lastColumn.replace('column','');

			if(num>0){
				htmlData='<img id="imgNewColumn'+num+'" src="../../library/images/add_cont.png" alt="Add More" onClick="addNewColumn('+num+');"/>';
				if($('#column'+num+' .divUndelete').html()==''){ //CHECK IF SHEET DELETED OR NOT
					htmlData+='<img id="imgDeleteColumn'+num+'" src="../../library/images/delete_cont.png" alt="Delete Sheet" onClick="removeColumn('+num+');"/>';
				}
				$('#divAddRow'+num).html(htmlData);				
			}
			
		}
	}
}

function enbDisTrialNo(grpName){
	var num= grpName.substr((grpName.length)-1);
	if($('#clwsLabels'+num+' #NewTrialChk').is(':checked'))
	{
		document.getElementById('clws_trial_number'+num).disabled = false;
		//---CL SHEET PRINT BUTTON DISABLE, IF TRIAL IS CHECKED---
		$('#column'+num+' #btnPrint1').prop('disabled',true);
	}
	else
	{
		document.getElementById('clws_trial_number'+num).disabled = true;
		$('#column'+num+' #btnPrint1').prop('disabled',false);
	}
}

function activateMenuData(num){
	//WORKING OF OTHER DROP DOWNS
	$('.dropdown-toggle').dropdown();
	$(".dropdown-menu li").click(function(){
		var selVal=$(this).text();
		odElem=$(this).parent().parent().find('input[type="text"]:eq(0)');
		odElem.val(selVal);
	});
}

function fillData(arrCLData, arrCLEval, arrCLOrder, arrCLChargesAdmin, selColNum, callFrom, CommentsArr){
	//dgi("prevLoader").style.display='block';fillData
	var num=1;
	var arrEyes=['OD','OS'];
	var eye='';
	var masterData=[];
	var isDelExist= isUndelExist=false;

	for(c in arrCLData){
		var isRGP=isCustRGP=false;
		var masterData=[];
		var idoc_drawing_id_od=desc_od= idoc_drawing_id_os=desc_os= '';
		var dispEvalDva=dispEvalNva=false;
		
		if(selColNum>0){
			num=selColNum;
		}else{
			colNum=1;
			if(num>1){ colNum=parseInt(num)-1; addNewColumn(colNum, 'fillingdata');  }
		}
		
		for(e in arrEyes){
			eye=arrEyes[e];
			if(eye=='OD'){ eye1Cap='Od'; eyeL='od'; }
			else if(eye=='OS'){  eye1Cap='Os'; eyeL='os';}
			
			for(y in arrCLData[c][eye]){
				var subCol=newCol=subColCur='';
				newCol=eye+num;
				if(y>1){ subCol=parseInt(y)-1; subCol='_'+subCol; }
				
				if(y>0){ subColCur='_'+y; newCol=addNewSubColumn('imgNewSubCol'+eye+num+subCol, 'fillingdata'); }
				
				data=arrCLData[c][eye][y];
				if(masterData.length<=0){
					masterData=data;
				}

				var isRGPOnly = false;
				if(data['clType'+eye]=='scl'){
					$('#clType'+newCol).val(data['clType'+eye]);
					$('#'+newCol+' .sclBc #elemBc'+newCol).val(data['SclBcurve'+eye]);
					$('#'+newCol+' .sclDiameter #elemDiameter'+newCol).val(data['SclDiameter'+eye]);
					$('#'+newCol+' .sclSphere #elemSphere'+newCol).val(data['Sclsphere'+eye]);
					$('#elemCylinder'+newCol).val(data['SclCylinder'+eye]);
					$('#elemAxis'+newCol).val(data['Sclaxis'+eye]);
					$('#elemColor'+newCol).val(data['SclColor'+eye]);
					$('#elemAdd'+newCol).val(data['SclAdd'+eye]);
					$('#elemDva'+newCol).val(data['SclDva'+eye]);
					$('#elemNva'+newCol).val(data['SclNva'+eye]);
					$('#elemMake'+newCol).val(data['SclType'+eye]);
					$('#elemMake'+newCol+'ID').val(data['SclType'+eye+'_ID']);

/*					$('#elem_SCL'+eye1Cap+'Drawing'+num+subColCur).val(data['elem_SCL'+eye1Cap+'Drawing']);
					$('#hdSCL'+eye1Cap+'DrawingOriginal'+num+subColCur).val(data['hdSCL'+eye1Cap+'DrawingOriginal']);
					$('#elem_SCL'+eye1Cap+'DrawingPath'+num+subColCur).val(data['elem_SCL'+eye1Cap+'DrawingPath']);
					$('#scl_idoc_drawing_id_'+eyeL+num+subColCur).val(data['scl_idoc_drawing_id_'+eyeL]);
*/					//DESC OD AND OS FROM SAME ROW AS NEW DRAWING IMPLEMENTED
/*					if(eye=='OD'){
						$('#scl_description_od_A'+num+subColCur).val(data['corneaSCL_od_desc']);
						$('#scl_description_od_B'+num+subColCur).val(data['corneaSCL_os_desc']);
					}
*/					
					//DISP DRAWING ICON IMAGE
/*					if(eye=='OD' || (eye=='OS' && data['scl_idoc_drawing_id_'+eyeL]>0)){
						if(document.getElementById('scl_'+eyeL+'DrawingPngImg'+num+subColCur)){
							$('#scl_'+eyeL+'DrawingPngImg'+num+subColCur).css('display','block');
							if(data['hasDrawing_'+eyeL]=='yes'){
								$('#scl_'+eyeL+'DrawingPngImg'+num+subColCur).attr('src', 'images/odHasDrawing.png');
							}else{
								$('#scl_'+eyeL+'DrawingPngImg'+num+subColCur).attr('src', 'images/odDrawing.png');							
							}
						}
						if(document.getElementById('rgp_'+eyeL+'DrawingPngImg'+num+subColCur)){
							$('#rgp_'+eyeL+'DrawingPngImg'+num+subColCur).css('display','none');
						}
					}
*/					
				}else if(data['clType'+eye]=='rgp' || data['clType'+eye]=='rgp_soft' || data['clType'+eye]=='rgp_hard'){
					$('#clType'+newCol).val(data['clType'+eye]);
					$('#'+newCol+' .rgpBc #elemBc'+newCol).val(data['RgpBC'+eye]);
					$('#'+newCol+' .rgpDiameter #elemDiameter'+newCol).val(data['RgpDiameter'+eye]);
					$('#elemCylinder'+newCol).val(data['RgpCylinder'+eye]);
					$('#elemAxis'+newCol).val(data['RgpAxis'+eye]);
					$('#elemOZ'+newCol).val(data['RgpOZ'+eye]);
					$('#elemCT'+newCol).val(data['RgpCT'+eye]);
					$('#'+newCol+' .rgpSphere #elemSphere'+newCol).val(data['RgpPower'+eye]);
					$('#elemColor'+newCol).val(data['RgpColor'+eye]);
					$('#elemAdd'+newCol).val(data['RgpAdd'+eye]);
					$('#elemDva'+newCol).val(data['RgpDva'+eye]);
					$('#elemNva'+newCol).val(data['RgpNva'+eye]);
					$('#elemMake'+newCol).val(data['RgpType'+eye]);
					$('#elemMake'+newCol+'ID').val(data['RgpType'+eye+'_ID']);

					//DISP DRAWING ICON IMAGE
					//if(document.getElementById('rgp_'+eyeL+'DrawingPngImg'+num+subColCur)){
					//	$('#rgp_'+eyeL+'DrawingPngImg'+num+subColCur).css('display','block');
					//}
					//if(document.getElementById('scl_'+eyeL+'DrawingPngImg'+num+subColCur)){
					//	$('#scl_'+eyeL+'DrawingPngImg'+num+subColCur).css('display','none');
					//}
					
					//alert('clType' + newCol);
					isRGP=true;
					//alert("RGP value: " + data['clType' + eye]);
					if(data['clType' + eye]=='rgp'){
						isRGPOnly = true;
						//$('#clType' + newCol).append('<option value="rgp" selected="selected">RGP</option>');
						//$("#clType" + newCol + " option[value='rgp']").add();

						$("#clType" + newCol + " option").eq(1).before($("<option></option>").val("rgp").text("RGP"));
						$("#clType" + newCol).val("rgp");
						//alert('#clType' + newCol);
						//$('#clType' + newCol).eq(1).before($('', { value: 7, text: 'Dynamic Append' }));

					}
				}else if(data['clType'+eye]=='cust_rgp'){
					$('#clType'+newCol).val(data['clType'+eye]);
					$('#'+newCol+' .rgpBc #elemBc'+newCol).val(data['RgpCustomBC'+eye]);
					$('#'+newCol+' .rgpDiameter #elemDiameter'+newCol).val(data['RgpCustomDiameter'+eye]);
					$('#elemCylinder'+newCol).val(data['RgpCustomCylinder'+eye]);
					$('#elemAxis'+newCol).val(data['RgpCustomAxis'+eye]);
					$('#elemOZ'+newCol).val(data['RgpCustomOZ'+eye]);
					$('#elemCT'+newCol).val(data['RgpCustomCT'+eye]);
					$('#'+newCol+' .rgpSphere #elemSphere'+newCol).val(data['RgpCustomPower'+eye]);
					$('#elemTwoDegree'+newCol).val(data['RgpCustom2degree'+eye]);
					$('#elemThreeDegree'+newCol).val(data['RgpCustom3degree'+eye]);
					$('#elemPCW'+newCol).val(data['RgpCustomPCW'+eye]);
					$('#elemColor'+newCol).val(data['RgpCustomColor'+eye]);
					$('#elemBlend'+newCol).val(data['RgpCustomBlend'+eye]);
					$('#elemEdge'+newCol).val(data['RgpCustomEdge'+eye]);
					$('#elemAdd'+newCol).val(data['RgpCustomAdd'+eye]);
					$('#elemDva'+newCol).val(data['RgpCustomDva'+eye]);
					$('#elemNva'+newCol).val(data['RgpCustomNva'+eye]);
					$('#elemMake'+newCol).val(data['RgpCustomType'+eye]);
					$('#elemMake'+newCol+'ID').val(data['RgpCustomType'+eye+'_ID']);
					
					//DISP DRAWING ICON IMAGE
					//if(document.getElementById('rgp_'+eyeL+'DrawingPngImg'+num+subColCur)){
					//	$('#rgp_'+eyeL+'DrawingPngImg'+num+subColCur).css('display','block');
					//}
					//if(document.getElementById('scl_'+eyeL+'DrawingPngImg'+num+subColCur)){
					//	$('#scl_'+eyeL+'DrawingPngImg'+num+subColCur).css('display','none');
					//}
					isCustRGP=true;
					
				}else if(data['clType'+eye]=='prosthesis' || data['clType'+eye]=='no-cl'){
					$('#clType'+newCol).val(data['clType'+eye]);
					active_deactive_cols(data['clType'+eye], eye, num, subCol);
				}

				if(data['hasDrawing_'+eyeL]=='yes'){
					if(eye=='OD'){
						idoc_drawing_id_od= data['idoc_drawing_id_'+eyeL];
						desc_od=data['corneaSCL_od_desc']+'~~'+data['corneaSCL_os_desc'];
					}else{
						idoc_drawing_id_os= data['idoc_drawing_id_'+eyeL];
						desc_os=data['corneaSCL_od_desc']+'~~'+data['corneaSCL_os_desc'];
					}
				}
				
				if(!selColNum){
					$('#detId'+newCol).val(data['id']);
				}
				
			}
		}
	
	
		//USED TO HIDE/DISP TEXTBOXES
		dispHideRgp('clTypeOD'+num);
		dispHideRgp('clTypeOS'+num);

		var clws_id=masterData['clws_id'];
		
		//FILL EVALUATION VALUES
		var dataEval=arrCLEval[clws_id];
		var colNumEval=parseInt(c)+1;


		if(callFrom === true && parseInt(selColNum)) colNumEval = parseInt(selColNum);
		//if(parseInt(num)) colNumEval = num;
		var arrODOS=['OD','OS'];
		if(typeof dataEval !== "undefined") { 
		for(x in arrODOS){
			eye=arrODOS[x];
			if(eye=='OD'){
				eye1Cap='Od';
				eyeL='od';
			}
			else if(eye=='OS'){
				eye1Cap='Os';
				eyeL='os';
			}
			newCol=eye+colNumEval;
		
			if($('#clType'+newCol).val()=='scl'){
				$('#elemDvaSphere'+newCol).val(dataEval['CLSLCEvaluationSphere'+eye]);
				//$('#elemDvaSphere'+eye+selColNum).val(dataEval['CLSLCEvaluationSphere'+eye]);
				$('#elemDvaCylinder'+newCol).val(dataEval['CLSLCEvaluationCylinder'+eye]);
				//$('#elemDvaCylinder'+eye+selColNum).val(dataEval['CLSLCEvaluationCylinder'+eye]);
				$('#elemPosition'+newCol).val(dataEval['CLSLCEvaluationPosition'+eye]);
				if(dataEval['CLSLCEvaluationPositionOther'+eye]==''){
					setVal('elemPositionOther'+newCol); 
				}else{
					clearVal('elemPositionOther'+newCol); $('#elemPositionOther'+newCol).val(dataEval['CLSLCEvaluationPositionOther'+eye]);
				}
				$('#elemDvaAxis'+newCol).val(dataEval['CLSLCEvaluationAxis'+eye]);
				//$('#elemDvaAxis'+eye+selColNum).val(dataEval['CLSLCEvaluationAxis'+eye]);
				$('#elemEvalDva'+newCol).val(dataEval['CLSLCEvaluationDVA'+eye]);
				//$('#elemEvalDva'+eye+selColNum).val(dataEval['CLSLCEvaluationDVA'+eye]);
				$('#elemNvaSphere'+newCol).val(dataEval['CLSLCEvaluationSphereNVA'+eye]);
				//$('#elemNvaSphere'+eye+selColNum).val(dataEval['CLSLCEvaluationSphereNVA'+eye]);
				$('#elemNvaCylinder'+newCol).val(dataEval['CLSLCEvaluationCylinderNVA'+eye]);
				//$('#elemNvaCylinder'+eye+selColNum).val(dataEval['CLSLCEvaluationCylinderNVA'+eye]);
				$('#elemNvaAxis'+newCol).val(dataEval['CLSLCEvaluationAxisNVA'+eye]);
				//$('#elemNvaAxis'+eye+selColNum).val(dataEval['CLSLCEvaluationAxisNVA'+eye]);
				$('#elemEvalNva'+newCol).val(dataEval['CLSLCEvaluationNVA'+eye]);
				//$('#elemEvalNva'+eye+selColNum).val(dataEval['CLSLCEvaluationNVA'+eye]);
				$('#elemComfort'+newCol).val(dataEval['CLSLCEvaluationComfort'+eye]);
				$('#elemMovement'+newCol).val(dataEval['CLSLCEvaluationMovement'+eye]);
				$('#elemCondition'+newCol).val(dataEval['CLSLCEvaluationCondtion'+eye]);
				
				if(dataEval['CLSLCEvaluationSphere'+eye] || dataEval['CLSLCEvaluationCylinder'+eye]
				|| dataEval['CLSLCEvaluationAxis'+eye] || dataEval['CLSLCEvaluationDVA'+eye]){
					dispEvalDva=true;
				}

				if(dataEval['CLSLCEvaluationSphereNVA'+eye]	|| dataEval['CLSLCEvaluationCylinderNVA'+eye]
				|| dataEval['CLSLCEvaluationAxisNVA'+eye] || dataEval['CLSLCEvaluationNVA'+eye]){
					dispEvalNva=true;	
				}
			}else if($('#clType'+newCol).val()=='rgp' || $('#clType'+newCol).val()=='cust_rgp' || $('#clType'+newCol).val()=='rgp_soft' || $('#clType'+newCol).val()=='rgp_hard'){
				$('#elemDvaSphere'+newCol).val(dataEval['CLRGPEvaluationSphere'+eye]);
				//$('#elemDvaSphere'+eye+selColNum).val(dataEval['CLRGPEvaluationSphere'+eye]);
				$('#elemDvaCylinder'+newCol).val(dataEval['CLRGPEvaluationCylinder'+eye]);
				//$('#elemDvaCylinder'+eye+selColNum).val(dataEval['CLRGPEvaluationCylinder'+eye]);
				$('#elemDvaAxis'+newCol).val(dataEval['CLRGPEvaluationAxis'+eye]);
				//$('#elemDvaAxis'+eye+selColNum).val(dataEval['CLRGPEvaluationAxis'+eye]);
				$('#elemEvalDva'+newCol).val(dataEval['CLRGPEvaluationDVA'+eye]);
				//$('#elemEvalDva'+eye+selColNum).val(dataEval['CLRGPEvaluationDVA'+eye]);
				$('#elemNvaSphere'+newCol).val(dataEval['CLRGPEvaluationSphereNVA'+eye]);
				//$('#elemNvaSphere'+eye+selColNum).val(dataEval['CLRGPEvaluationSphereNVA'+eye]);
				$('#elemNvaCylinder'+newCol).val(dataEval['CLRGPEvaluationCylinderNVA'+eye]);
				//$('#elemNvaCylinder'+eye+selColNum).val(dataEval['CLRGPEvaluationCylinderNVA'+eye]);
				$('#elemNvaAxis'+newCol).val(dataEval['CLRGPEvaluationAxisNVA'+eye]);
				//$('#elemNvaAxis'+eye+selColNum).val(dataEval['CLRGPEvaluationAxisNVA'+eye]);
				$('#elemEvalNva'+newCol).val(dataEval['CLRGPEvaluationNVA'+eye]);
				//$('#elemEvalNva'+eye+selColNum).val(dataEval['CLRGPEvaluationNVA'+eye]);
				$('#elemComfort'+newCol).val(dataEval['CLRGPEvaluationComfort'+eye]);
				$('#elemMovement'+newCol).val(dataEval['CLRGPEvaluationMovement'+eye]);
				$('#elemPositionB'+newCol).val(dataEval['CLRGPEvaluationPosBefore'+eye]);
				if(dataEval['CLRGPEvaluationPosBeforeOther'+eye]==''){ setVal('elemPositionBOther'+newCol); 
				}else{ clearVal('elemPositionBOther'+newCol); $('#elemPositionBOther'+newCol).val(dataEval['CLRGPEvaluationPosBeforeOther'+eye]); }
				$('#elemPositionA'+newCol).val(dataEval['CLRGPEvaluationPosAfter'+eye]);
				if(dataEval['CLRGPEvaluationPosAfterOther'+eye]==''){ setVal('elemPositionAOther'+newCol); 
				}else{ clearVal('elemPositionAOther'+newCol); $('#elemPositionAOther'+newCol).val(dataEval['CLRGPEvaluationPosAfterOther'+eye]); }
				$('#elemFLPatter'+newCol).val(dataEval['CLRGPEvaluationFluoresceinPattern'+eye]);
				$('#elemInverted'+newCol).val(dataEval['CLRGPEvaluationInverted'+eye]);

				if((dataEval['CLRGPEvaluationSphere'+eye]!='' && dataEval['CLRGPEvaluationSphere'+eye]!='null') 
				|| (dataEval['CLRGPEvaluationCylinder'+eye]!='' && dataEval['CLRGPEvaluationCylinder'+eye]!='null')
				|| (dataEval['CLRGPEvaluationAxis'+eye]!='' && dataEval['CLRGPEvaluationAxis'+eye]!='null')
				|| (dataEval['CLRGPEvaluationDVA'+eye]!='' && dataEval['CLRGPEvaluationDVA'+eye]!='null')){
					dispEvalDva=true;
				}

				if((dataEval['CLRGPEvaluationSphereNVA'+eye]!='' && dataEval['CLRGPEvaluationSphereNVA'+eye]!='null') 
				|| (dataEval['CLRGPEvaluationCylinderNVA'+eye]!='' && dataEval['CLRGPEvaluationCylinderNVA'+eye]!='null')
				|| (dataEval['CLRGPEvaluationAxisNVA'+eye]!='' && dataEval['CLRGPEvaluationAxisNVA'+eye]!='null')
				|| (dataEval['CLRGPEvaluationNVA'+eye]!='' && dataEval['CLRGPEvaluationNVA'+eye]!='null')){
					dispEvalNva=true;
				}								

				if(dataEval['hasDrawing_'+eyeL]=='yes'){
					if(eye=='OD'){
						idoc_drawing_id_od= dataEval['idoc_drawing_id_'+eyeL];
						desc_od=dataEval['cornea_od_desc']+'~~'+dataEval['cornea_os_desc'];
					}else{
						idoc_drawing_id_os= dataEval['idoc_drawing_id_'+eyeL];
						desc_os=dataEval['cornea_od_desc']+'~~'+dataEval['cornea_os_desc'];
					}
				}				
				//DRAWING
//				if(eye=='OD' || eye=='OS'){
/*					$('#elem_conjunctiva'+eye1Cap+'Drawing'+colNumEval).val(dataEval['elem_conjunctiva'+eye1Cap+'Drawing']);
					$('#hdConjunctiva'+eye1Cap+'DrawingOriginal'+colNumEval).val(dataEval['hdConjunctiva'+eye1Cap+'DrawingOriginal']);
					$('#elem_conjunctiva'+eye1Cap+'DrawingPath'+colNumEval).val(dataEval['elem_conjunctiva'+eye1Cap+'DrawingPath']);
					
*/				
					//$('#rgp_description_'+eyeL+'_A'+colNumEval).val(dataEval['cornea_'+eyeL+'_desc']);
					//$('#rgp_description_'+eyeL+'_B'+colNumEval).val(dataEval['cornea_'+eyeL+'_desc']);
				
					//if(dataEval['hasDrawing_'+eyeL]=='yes'){
					//	$('#rgp_'+eyeL+'DrawingPngImg'+colNumEval).attr('src', 'images/odHasDrawing.png');
					//	$('#rgp_idoc_drawing_id_'+eyeL+colNumEval).val(dataEval['idoc_drawing_id_'+eyeL]);
					//}else{
					//	$('#rgp_'+eyeL+'DrawingPngImg'+colNumEval).attr('src', 'images/odDrawing.png');
					//}
					
			//	}
			}
			
			//COMMON FOR SCL & RGP
			$('#elemRotation'+newCol).val(dataEval['EvaluationRotation'+eye]);			
		}
	}
	
		//FILL DRAWING DATA
		if(idoc_drawing_id_od>0){
			var arr=[];
			$('#column'+num+' #idoc_drawing_id_od'+num).val(idoc_drawing_id_od);
			arr=desc_od.split('~~');
			if(arr[0]){ $('#description_A_od'+num).val(arr[0]);}
			if(arr[1]){ $('#description_B_od'+num).val(arr[1]);}
			$('#DrawingPngImg_od'+num).attr('src', 'images/odHasDrawing.png');
		}else{
			$('#column'+num+' #idoc_drawing_id_od'+num).val('');
			$('#description_A_od'+num).val('');
			$('#description_B_od'+num).val('');
			$('#DrawingPngImg_od'+num).attr('src', '../../library/images/eyeicon.png');
		}
		if(idoc_drawing_id_os>0){ //IF OLD DATA HAS SEPARATE OS VALUES
			$('#column'+num+' .divDrawing_os').css('display','block');
			var arr=[];
			$('#idoc_drawing_id_os'+num).val(idoc_drawing_id_os);
			arr=desc_os.split('~~');
			if(arr[0]){ $('#description_A_os'+num).val(arr[0]);}
			if(arr[1]){ $('#description_B_os'+num).val(arr[1]);}
			$('#DrawingPngImg_os'+num).attr('src', 'images/odHasDrawing.png');
		}else{
			$('#idoc_drawing_id_os'+num).val('');
			$('#description_A_os'+num).val('');
			$('#description_B_os'+num).val('');
			$('#DrawingPngImg_os'+num).attr('src', '../../library/images/eyeicon.png');
		}
		
		
		//FILL OU VALUES
		$('#elemDvaOU'+num).val(arrCLData[c]['OU'][0]['SclDvaOU']);
		$('#elemNvaOU'+num).val(arrCLData[c]['OU'][0]['SclNvaOU']);
		
		if(typeof dataEval !== "undefined")
		{
			$('#elemEvalDvaOU'+num).val(dataEval['CLSLCEvaluationDVAOU']);
			$('#elemEvalNvaOU'+num).val(dataEval['CLSLCEvaluationNVAOU']);
    		if(dataEval['CLSLCEvaluationDVAOU']){ dispEvalDva=true;	}
    		if(dataEval['CLSLCEvaluationNVAOU']){ dispEvalNva=true;	}
		}
		//DISPLAY/HIDE EVALDVA/NVA BLOCKS
		if(dispEvalDva==true){
			$('#column'+colNumEval+' .evalDva').show();
		}else{
			$('#column'+colNumEval+' .evalDva').hide();
		}
		if(dispEvalNva==true){
			$('#column'+colNumEval+' .evalNva').show();
		}else{
			$('#column'+colNumEval+' .evalNva').hide();
		}
		
		$('#comments'+num).val(masterData['cl_comment']);
		//if(callFrom!='newSheet'){
		if(!selColNum){
			$('#cpt_evaluation_fit_refit'+num).val(masterData['cpt_evaluation_fit_refit']);
		}
		
		//IF RGP/CUST RGP
		if(isRGP==true){
			$('#column'+num+' .rgpDivs').show();
		}
		if(isCustRGP==true){
			$('#column'+num+' .rgpDivs').show();
			$('#column'+num+' .rgpCustDivs').show();
		}		
	
		//FILLING HIDDEN FIELDS
		if(!selColNum){
			$('#clws_id'+num).val(clws_id);
		}
		//$('#detIdOD'+num).val(masterData['OD_ID']);
		//$('#detIdOS'+num).val(masterData['OS_ID']);
		//$('#detIdOU'+num).val(masterData['OU_ID']);
		if(!selColNum){
			$('#clws_types'+num).val(masterData['clws_type']);
			$('#clws_trial_number'+num).val(masterData['clws_trial_number']);
			$('#charges_id'+num).val(masterData['charges_id']);
		}
		$('#AverageWearTime'+num).val(masterData['AverageWearTime']);
		$('#Solutions'+num).val(masterData['Solutions']);
		$('#Age'+num).val(masterData['Age']);
		$('#DisposableSchedule'+num).val(masterData['DisposableSchedule']);
		
		//DATA FILLING
		if(!selColNum>0){
			$('#column'+num+' .dispDate').html('DOS:&nbsp;'+masterData['dos']);
			$('#column'+num+' #dos'+num).val(masterData['dos']);
		}
		$('#usage_val'+num).val(masterData['usage_val']);
		$('#allaround'+num).val(masterData['allaround']);
		$('#wear_scheduler'+num).val(masterData['wear_scheduler']);
		$('#replenishment'+num).val(masterData['replenishment']);
		$('#disinfecting'+num).val(masterData['disinfecting']);

		//CL ORDER
		var form_id=masterData['form_id'];
		if(form_id>0){
			var cl_order=arrCLOrder[form_id];
			if(cl_order==1){
				$('#cl_order'+num).attr('checked', true);
			}else{
				$('#cl_order'+num).attr('checked', false);
			}
		}
		
		//CHARGES OPTIONS		
		if(!selColNum){
			var arrChargesIds=masterData['charges_id'];
			var charges_options='';
			for(x in arrCLChargesAdmin){
				selected='';
				arrT=arrCLChargesAdmin[x].split('~');
	
				if(arrChargesIds != 'undefined' && arrChargesIds != null){
					if(arrChargesIds.search(x)>-1){ selected='selected';}
				}
				charges_options+='<option value="'+x+'~'+arrT[1]+'" '+selected+'>'+arrT[0]+'</option>';
			}
			var chargesOpt='<select class="selectpicker" name="clws_charges'+num+'[]" id="clws_charges'+num+'" multiple="multiple">';
			chargesOpt+=charges_options;
			chargesOpt+='</select>';
			$('#clws_charges_div'+num).html(chargesOpt);
			$("#clws_charges"+num).selectpicker('refresh');	
			
			makeMultiSelect(num);
			//$("#clws_charges"+num).selectpicker('refresh');				
		}
		//LABEL CHECKBOXES

		if(!selColNum){
			var otherVal='';
			var arrCLChargesJS=<?php echo json_encode($arrCLChargesJS);?>;
			var arrLabelNames = "";
			if(masterData['clws_type'] != 'undefined' && masterData['clws_type'] != null){
				arrLabelNames = masterData['clws_type'].split(',');
			}
			//UNCHECKED ALL IF COPYING
			if(selColNum>0){
				$("#clwsLabels"+num+" input[type='checkbox']").attr('checked', false);
			}
			for(x in arrLabelNames){
				lblName=arrLabelNames[x];
				if(arrCLChargesJS[lblName]){
					$("#clwsLabels"+num+" input:checkbox[value='"+lblName+"']").prop("checked", true);
					if(lblName=='Current Trial'){ $('#clws_trial_number'+num).attr('disabled', false); }
				}else{
					otherVal=lblName;
				}
			}		
			$('#otherSave'+num).val(otherVal);
		}
		
		//REMOVE DELETE BUTTON IF SHEET ALREADY DELETED
		if(masterData['del_status']=='1'){
			$('#column'+num+' #imgDeleteColumn'+num).remove();
			$('#column'+num+' .divUndelete').html('<input type="button" class="dff_button" value="Undelete" style="font-size:12px; margin-top:6px; border:1px solid #FFC12E" name="btnUndelete'+num+'"  id="btnUndelete'+num+'" onClick="deleteWorksheet(\'undelete\','+num+');">');
		}

		var textAreaRow = num;
		$("[id^='column"+textAreaRow+"'].allaround").each(function(){
			var colId = $(this).attr('id');
			
			//$("#" + colId + " #commentsdiv").nextAll().remove();
			$("#" + colId + " .comments_text_div").remove();
			//$(this).remove();
		});

		$("[id^='column"+textAreaRow+"'].allaround").each(function(){
			var columnId = $(this).attr('id');
			//console.log(columnId, textAreaRow);

			$("#" + columnId + " input[id^=clws_id"+textAreaRow+"]").each(function(){

				var clSheetId = $(this).val();

				//if(!$(this).val()){
					//console.log('Console Col,,,,',columnId, colNumEval);
					
				//}
				
				//if(Object.keys(arrCLEval).length > 0 && callFrom === true){
				//	clSheetId = Object.keys(arrCLEval).shift();
				//}
				
				var commentsExists = (CommentsArr && CommentsArr[clSheetId]) ? true : false;

				if(commentsExists){
					//console.log(CommentsArr[clSheetId]);
					var newCommentArr = [];
					
					var clCommentsTextareaCount = 1;
					$.each(CommentsArr[clSheetId], function(commentId, commentDesc){
						var newCommentDiv = "";
						//if(key == clSheetId){
							//console.log(commentId, commentDesc);
							//$.each(value, function(commentId, commentDesc){
								//(this, '', '', 'new', 1, 0)
								newCommentDiv = addCommentBox(columnId, clSheetId, commentDesc, null, clCommentsTextareaCount, commentId);
								if(newCommentDiv) newCommentArr.push(newCommentDiv);
								//console.log(newCommentDiv);
								clCommentsTextareaCount ++;
							//});
						//}
					});

					if(newCommentArr.length > 0) $("#" + columnId + " #commentsdiv").after(newCommentArr.join(''));

					$("#" + columnId + " textarea[name^='comment_update_']").attr('name', 'comment_new_' + columnId + '[]');
					$("#" + columnId + " textarea[name^='comment_new_column']").attr('name', 'comment_new_' + columnId + '[]');
				}
				
			});

			$("div[id^='OD']").each(function(){
				var tempId = $(this).attr('id');
				if(tempId.lastIndexOf('_') > -1){
					$("#" + tempId + " .cl_comment_image").remove();
					$("#" + tempId + " textarea").remove();
				}
			});

		});

		num++;
	}
	
}

function reloadWorkView(clws_id){
	var elemChanged=[];
	
	if(!window.opener.fmain.document.getElementById('clws_id')) return;
	var WV_clws_id=window.opener.fmain.document.getElementById('clws_id').value;

	//GET CHANGED ELEMENTS
	if(WV_clws_id=='' || WV_clws_id==clws_id){
		var arrElemMatch={
			scl_elemMake:'SclType',
			scl_elemBc:'SclBcurve',
			scl_elemDiameter:'SclDiameter',
			scl_elemSphere:'Sclsphere',
			scl_elemCylinder:'SclCylinder',
			scl_elemAxis:'Sclaxis',
			scl_elemColor:'SclColor',
			scl_elemAdd:'SclAdd',
			scl_elemDva:'SclDva',
			scl_elemNva:'SclNva',
			rgp_elemMake:'RgpType',
			rgp_elemBc:'RgpBC',
			rgp_elemCylinder:'RgpCylinder',
			rgp_elemDiameter:'RgpDiameter',	
			rgp_elemSphere:'RgpPower',
			rgp_elemAxis:'RgpAxis',
			rgp_elemColor:'RgpColor',
			rgp_elemOZ:'RgpOZ',
			rgp_elemCT:'RgpCT',
			rgp_elemAdd:'RgpAdd',
			rgp_elemDva:'RgpDva',
			rgp_elemNva:'RgpNva',
			cust_rgp_elemMake:'RgpCustomType',
			cust_rgp_elemBc:'RgpCustomBC',
			cust_rgp_elemDiameter:'RgpCustomDiameter',
			cust_rgp_elemPower:'RgpCustomPower',
			cust_rgp_elemCylinder:'RgpCustomCylinder',
			cust_rgp_elemAxis:'RgpCustomAxis',	
			cust_rgp_elemTwoDegree:'RgpCustom2degree',
			cust_rgp_elemThreeDegree:'RgpCustom3degree',
			cust_rgp_elemPCW:'RgpCustomPCW',
			cust_rgp_elemOZ:'RgpCustomOZ',
			cust_rgp_elemCT:'RgpCustomCT',
			cust_rgp_elemColor:'RgpCustomColor',
			cust_rgp_elemBlend:'RgpCustomBlend',
			cust_rgp_elemEdge:'RgpCustomEdge',
			cust_rgp_elemAdd:'RgpCustomAdd',
			cust_rgp_elemDva:'RgpCustomDva',
			cust_rgp_elemNva:'RgpCustomNva'
			};
		$('input[name^="clws_id"').each(function(index, element) {
            if($(this).val()==clws_id){
				elemId=$(this).attr('id');
				num=elemId.replace('clws_id','');
				
				if(document.getElementById('column'+num)){
					$('#column'+num+' input[type="text"]').each(function(index, element) {
                        elemId=$(this).attr('id');
						eye='OD';
						if(elemId.indexOf('OS')!='-1'){ eye='OS'; }
						var arrp=[];
						var n=0;
						arrp=elemId.split(eye);
						if(arrp[1]){
							arrv=[];
							if(arrp.indexOf('_')!='-1'){
								arrv=arrp.split('_');
								if(arrv[1]){
									n=arr[1];
								}
							}
						}
						n=parseInt(n)+1;
						
						type=$('#clType'+eye+arrp[1]).val();
						
						if(type=='cust_rgp' && arrp[0]=='elemSphere'){ arrp[0]='elemPower';}
						
						elemWV=arrElemMatch[type+"_"+arrp[0]];

						elemType='clType'+eye+n;
						if(typeof(arrp[1])!="undefined"){
							if(window.opener.fmain.document.getElementById(elemType)){
								if(type!=window.opener.fmain.document.getElementById(elemType).value){
									elemChanged[elemType]=elemType;
								}
							}else{
								elemChanged[elemWV]=elemWV;
							}
						}
						
						if(typeof(elemWV)!="undefined"){
							elemWV=elemWV+eye+n;
							if(window.opener.fmain.document.getElementById(elemWV)){
								if($('#'+elemId).val()!=window.opener.fmain.document.getElementById(elemWV).value){
									elemChanged[elemWV]=elemWV;
								}
							}else{
								elemChanged[elemWV]=elemWV;
							}
						}
					});
				}
			}
        });
	}
	
	var ta = $.trim($('#column'+num+' #txtcomments').val());
	if(ta!="" && ta!=$.trim(window.opener.fmain.document.getElementById('cl_comment').value)){
		elemChanged['cl_comment']='cl_comment';
	}
	
	if($("#column"+num+" textarea[id*=cltextarea_]")[0]){
		var ta = $.trim($("#column"+num+" textarea[id*=cltextarea_]")[0].value);
		if((ta!=$.trim(window.opener.fmain.document.getElementById('cl_comment').value))){
			elemChanged['cl_comment']='cl_comment';
		}
	}

	//	
	if(num && typeof(num)!="undefined" && $('#cl_order'+num).prop("checked")!=window.opener.fmain.$('#cl_order').prop("checked")){
		elemChanged['cl_order']='cl_order';
	}	
	
	//window.opener.fmain.myKeyUp(elemChanged);
	/*
	var elemString=curString='';
	hasElem=false;
	hasCurElem=false;
	oldVals=window.opener.fmain.document.getElementById('elem_utElems').value;
	curVals=window.opener.fmain.document.getElementById('elem_utElems_cur').value;	
	*/
	
	//for(x in elemChanged){
		/*
		if(oldVals.indexOf(x)=='-1'){
			elemString+=x+',';	hasElem=true;
		}
		if(curVals.indexOf(x)=='-1'){
			curString+=x+',';	hasCurElem=true;
		}*/
		
		//--
		//elem=x;							
		//if(elem!=""){window.opener.fmain.utElem_capture(window.opener.fmain.document.getElementById(elem));} //this will work for curval
		
		//--
	//}
	
	/*
	if(hasElem==true){ 
		window.opener.fmain.document.getElementById('elem_utElems').value=oldVals+'|1@'+elemString+'|';
	}
	if(hasCurElem==true){
		window.opener.fmain.document.getElementById('elem_utElems_cur').value=curVals+curString;		
	}
	*/
	
	if(typeof(opener.top.fmain)!='undefined' && typeof(opener.top.fmain.chkWVB4Move) != 'undefined'){
		opener.top.fmain.loadCopyFromWorkSheet('', elemChanged, this, 'popup');
	}else if(typeof(opener.top.fmain)!='undefined'){
		opener.top.fmain.loadCopyFromWorkSheet('', elemChanged, this, 'popup');
	}
	
}

function loadCopyFromWorkSheet(id, clws_id, selectDropDown){
	var copyFromDropDown = $(selectDropDown);
//	var allaroundId = (bilateral.closest('.allaround').attr('id'));
//	var columnCount = allaroundId.replace("column", "");
	//alert("sheetIdFromWhichToCopy: " + clws_id);
	copyFromSheetId = clws_id;
	if(clws_id>0){
		num=id.replace('copyFromId', '');
		var current_clws_id=$('#clws_id'+num).val();
		$.ajax({ 
			type: "POST",
			url: "cl_ajax.php",
			data: "mode=copyfrom&num="+num+"&clws_id="+clws_id+"&current_clws_id="+current_clws_id,
			success: function(data){
				r = jQuery.parseJSON(data);
				//console.log(r, 'copyfrom');
				arrSheetData	= r.arrCLData;
				arrEvalData 	= r.arrCLEval;
				arrCLComments 	= r.arrCLComments;
				//console.log("arrEvalData: ", arrCLComments);
				//alert(Object.keys(r.arrCLComments));
				//alert(Object.keys(r.arrCLComments).shift());


				// Fill evaluations
				// $('input[id^="clws_id"]').each(function(){
				// 	if($(this).val() == clws_id){
				// 		var colId = $(this).closest(".allaround").attr('id');
				// 		alert("colid: " + colId);
				// 		alert("num: " + num);
				// 		alert("columnCount: " + columnCount);
				// 		$("#" + colId + " elemComfortOD" + num).val($("#" + colId + " elemComfortOD" + columnCount).val());
				// 	}
				// });

				//REMOVING EXTRA OD/OS 
				var i=0;
				$('#column'+num+' div[id^="OD"]').each(function(index, element) {
					if(i>0){ removeColumn($(this).attr('id'), 'subColumn', 'copyFrom', num);	}
					i++;
				});
				i=0;
				$('#column'+num+' div[id^="OS"]').each(function(index, element) {
					if(i>0){ removeColumn($(this).attr('id'), 'subColumn', 'copyFrom', num);	}
					i++;
				});
				
				fillData(arrSheetData, arrEvalData, arrCLOrder, arrCLChargesAdmin, num, true, arrCLComments);
				$('div[id^="OS"]').each(function(){
					var parentId = $(this).attr('id');
					if(parentId.lastIndexOf('_') > -1){
						$('#' + parentId  + ' .btn-bilateral').nextAll().each(function(){
							$(this).addClass('os_left_margin');
						});
					}
				});
				$('div[id^="OS"]').each(function(){
					var parentId = $(this).attr('id');
					if(parentId.lastIndexOf('_') > -1){
						$('#' + parentId  + ' .btn-bilateral').hide();
					}
				});
				//$('div[id^="OS"]')
			}
		});
		
		$.ajax({ 
			url: "cl_ajax.php?clws_id="+clws_id+"&mode="+mode,
			success: function(updated){
				if(updated=='1'){
					if(mode=='undelete'){
						$('#column'+num+' .divUndelete').html('');
						top.fAlert('Worksheet undeleted successfully.');
					}else{
						$('#clws_id'+num).val('');
						removeColumn(num, '', 'deleteDatabase');
						top.fAlert('Worksheet deleted successfully.');
					}
				}else{
					//top.fAlert("No action is performed.");
				}
				resetDropDowns();
			}
		});
		//$("#copyFromId" + num).val(clws_id);
		// var sourceColumn = $("#clws_id" + (columnCount - 1)).closest(".allaround").attr('id').replace("column", "");
		// var destinationColumn = (parseInt(sourceColumn) + 1);

		// console.log(columnCount);

		// $("#column" + sourceColumn + " .comments_text_div").insertAfter("#column" + destinationColumn + " #commentsdiv");
		// $("#column" + destinationColumn + " textarea").each(function(){
		// 	$(this).attr("name", "comment_new_column" + destinationColumn + "[]");
		// });
		//return false;

		//alert("source: " + sourceColumn);
		//alert("destinationColumn: " + destinationColumn);
		//alert("cl_ajax.php?clws_id="+clws_id+"&mode=copycomments&sheetcount=" + destinationColumn);

		// To load contact lens comments
		// $.ajax({ 
		// 	url: "cl_ajax.php?clws_id="+clws_id+"&mode=copycomments&sheetcount=" + destinationColumn,
		// 	dataType: 'JSON',
		// 	success: function(data123){
		// 		console.log(data123);

		// 		return false;
		// 		//alert(data123);
		// 		//var dataArr = data123.split("##########");
		// 		$("#OD" + destinationColumn).append(data123);
		// 		$("#column" + destinationColumn + " div[id^='div_']").each(function(){
		// 			if($(this).attr('id') != "div_0"){
		// 				$(this).remove();
		// 			}
		// 		});
		// 		$("#column" + sourceColumn + " .comments_text_div").each(function(){
		// 			if($(this).attr('id') != "div_0"){
		// 				$(this).remove();
		// 			}
		// 		});
		// 		$("#OD" + sourceColumn).append(data123);
		// 	}
		// });
		//$("#OD" + destinationColumn + " #commentsdiv").nextAll().remove();
		//$("#OD" + sourceColumn + " #commentsdiv").nextAll().remove();
	}
}

function formSubmit(){
	dgi("prevLoader").style.display='block';
	var clws_id;
	var formSubmit=true;
	var trialNum=true;
	var abort=false;

	$('div[id^="column"]').each(function(index, element) {
		//alert($(this).attr('id'));
		var id = $(this).attr('id');
		id = id.replace("column", "");
		//$("#" + id + " .comments_text_div").attr('name', "comment_new_column" + id + "[]");
		//id = id.replace("column", "");
		//alert(id);
	});

	$('div[id^="column"]').each(function(index, element) {
		
		colId=$(this).attr('id');
		num=colId.replace('column','');
		if($('#clws_types'+num).val()==''){
			formSubmit=false;	   
		}else{
		   clws_types=$('#clws_types'+num).val();
		   if(clws_types.indexOf('Current Trial')>-1 && ($('#clws_trial_number'+num).val()=='' || $('#clws_trial_number'+num).val()==0)){
			   trialNum=false;
		   }
		}
		
		if(formSubmit==false){
			dgi("prevLoader").style.display='none';
			abort=true;
			alert('Visit type is not checked for CL sheet.');
			return false;
		}else if(trialNum==false){
			dgi("prevLoader").style.display='none';
			abort=true;
			alert('Trial number for CL sheet is not filled.');		
			return false;
		}else if($('#prosthesis'+num).attr('checked') && $('#prosthesis_val'+num).val()==''){
			dgi("prevLoader").style.display='none';
			abort=true;
			alert('Value for prosthesis is not selected.');		
			return false;
		}else if($('#no_cl'+num).attr('checked') && $('#no_cl_val'+num).val()==''){
			dgi("prevLoader").style.display='none';
			abort=true;
			alert('Value for "No-CL" is not selected.');		
			return false;
		}
    }); 
		
	if(abort==true){  return false;}	
	
	if(formSubmit==true && trialNum==true){
		
		//ONLY TO CHECK IF ONLY NEW SHEET CREATION
		var onlyNewSheet=0;
		$('input[name^="clws_id"').each(function(index, element) {
			if($(this).val()!=''){
				onlyNewSheet=0;
				return false;
			}else{
				onlyNewSheet=1;
			}
		});
		//-----------------------------------

		if($("#elemMakeOD1ID").val() == $("#elemMakeOS1ID").val()){
			if($("#elemMakeOD1").val() != $("#elemMakeOS1").val()){
				$("#odOSame").val("false");
			}else{
				$("#odOSame").val("true");
			}
		}
		$("#submit").attr("disabled", "disabled");
		var serialize=$("#frmContactlensNew").serializeArray();
		$.ajax({ 
			type: "POST",
			url: "save_contact_lens.php",
			data:serialize,
			dataType : "json",
			success: function(data){
				//a=window.open();a.document.write(data);
				//clws_id=data.trim();
				//data = jQuery.parseJSON(data);
				//console.log(data);
				var arrCLWS_IDVsCol = data.arrCLWS_IDVsCol;
				var latestClws_id = data.latestClws_id;
				var arrReturn = data.arrReturn;
				for(x in arrCLWS_IDVsCol){
					if(document.getElementById('clws_id'+x) && document.getElementById('clws_id'+x).value==''){
						$('#clws_id'+x).val(arrCLWS_IDVsCol[x]);
					}
				}
				//APPLIED IDS TO HIDDEN FIELDS THAT ARE NEWLY ADDED
				for(col in arrReturn){
					colF=parseInt(col)+1;
					for(eye in arrReturn[col]){
						for(subCol in arrReturn[col][eye]){
							subColTitle='';
							//subColF= parseInt(subCol)+1;
							if(subCol>0){ subColTitle='_'+subCol; }
							hiddenField='#detId'+eye+colF+subColTitle;
	
							if($('#column'+colF+' '+hiddenField)){
								if($('#column'+colF+' '+hiddenField).val()==""){
									$('#column'+colF+' '+hiddenField).val(arrReturn[col][eye][subCol]);
									//$('#column'+colF+' '+hiddenField).trigger("click");
								}
							}
						}
					}
				}
				reloadWorkView(latestClws_id);
				resetDropDowns();
				/* $("div[id^='column']").each(function(){
					$(this).find("textarea").each(function(){
						if($(this).attr('name').indexOf("_new") > -1){
							$(this).attr('name', $(this).attr('name').replace("_new", ""))
						}
					});
				}); */

				$("#prevLoader").fadeOut(500, function(){
					// Refresh CL data 
					//refreshClData();

					alert('Contact lens sheet(s) saved successfully!');

					if(onlyNewSheet==1){
						// IT WILL RELOAD WINDOW WITH LATEST ADDED SHEET. IT AVOID TO LOAD ALL SHEETS IF ALREADY LOADED.
						var url=window.location.href;
						arr=url.split('php');
						url=arr[0]+'php';
						location.replace(url);					
					}else{
						window.location.reload(url);
					}
				});
				//change_main_Selection("Work_View");
			}
		});	
	}
	
	return false;
}


function openNewWS(callFrom){
	copyFromSheetId = 0;
	sheetscount=$('#sheetscount').val();
	for(i=2; i<=sheetscount; i++){
		if(document.getElementById('column'+i)){
			var availableCols=$('div[id^="column"]').size();
			if(availableCols>1){
				$('#column'+i).remove();
			}
		}
	}
	
	colName=$('div[id^="column"]').attr('id');
	num=colName.replace('column','');
	
	if(document.getElementById('column'+num)){
		$('#column'+num+' input, #column'+num+' select, #column'+num+' textarea').each(function(){
			if($(this).attr('type') != 'button'){
				if($(this).attr('type') == 'checkbox'){
					$(this).attr('checked',false);
				}else{
					$(this).val('');
				}
			}
		});
		
		//SELECT SCL
		$("#clTypeOD"+num).val('scl');
		$("#clTypeOS"+num).val('scl');
		
		//CHARGES DROPDOWN		
		$("#clws_charges"+num).val('');
		makeMultiSelect(num);		
		$("#clws_charges"+num).selectpicker('refresh');	

		
		var cDate='<?php echo $cur_date?>';
		$('.dispDate').html('DOS:&nbsp;'+cDate);
		$('#dos'+num).val(cDate);
		//GIVE ADD IMAGE
		htmlData='<img id="imgNewColumn'+num+'" src="../../library/images/add_cont.png" alt="Add More" onclick="addNewColumn('+num+');">';
		$('#divAddRow'+num).html(htmlData);		
		
		
		//REMOVING EXTRA OD/OS 
		var num=1;
		var i=0;
		$('#column'+num+' div[id^="OD"]').each(function(index, element) {
			if(i>0){ removeColumn($(this).attr('id'), 'subColumn', 'copyFrom', num);	}
			i++;
		});
		i=0;
		$('#column'+num+' div[id^="OS"]').each(function(index, element) {
			if(i>0){ removeColumn($(this).attr('id'), 'subColumn', 'copyFrom', num);	}
			i++;
		});
		
		$('#oldSheets').val('');
		
		//USED TO HIDE/DISP TEXTBOXES
		dispHideRgp('clTypeOD'+num);
		dispHideRgp('clTypeOS'+num);
		dispHideRgp('clTypeOU'+num);
		//HIDE EVAL DVA/NVA BLOCKS
		$('#column'+num+' .evalDva').removeAttr('style');
		$('#column'+num+' .evalNva').removeAttr('style');
		$('#column'+num+' .evalDva:first').css('background-color', '#aab3e6');
		$('#column'+num+' .evalNva:first').css('background-color', '#c9e4a8');
		$('#column'+num+' .evalDva').hide();
		$('#column'+num+' .evalNva').hide();		
		
		//SET DRAWING IMAGES TO UNDRAWING
		if($('#DrawingPngImg_od'+num).length > 0){
			dgi('DrawingPngImg_od'+num).src="../../library/images/eyeicon.png";
		}
		if($('#DrawingPngImg_os'+num).length > 0){
			dgi('DrawingPngImg_os'+num).src="../../library/images/eyeicon.png";
		}
		
		//DEFAULT VALUE FOR SPHERE/POWER
		$('#elemSphereOD'+num).val('');
		$('#elemSphereOS'+num).val('');

		$(".comments_text_div").each(function(){
			$(this).remove();
		});

		//CLEAR UN-DELETE BUTTON IF DISPLAYING
		$('#column'+num+' .divUndelete').html('');		
		
		//IF FINAL IS EXIST THEN LOAD IT
/*		finalFound=0;
		if(callFrom!='delete'){
			$.ajax({
				type: "POST",
				url: "cl_ajax.php",
				data: "mode=getFinal",
				success: function(data){
					r = jQuery.parseJSON(data);
					finalFound 	= r.finalFound;
	
					if(finalFound=='1'){
						arrSheetData	= r.arrCLData;
						arrEvalData 	= r.arrCLEval;
	
						fillData(arrSheetData, arrEvalData, arrCLOrder, arrCLChargesAdmin, num, 'newSheet');
					}
				}
			});
		}*/
	}

	var lensTypeOD = $("#column1 #clTypeOD1");
	lensTypeOD.find('option[value=rgp]').remove();
	//lensTypeOD.find('option[value=rgp_soft]').add();
	//lensTypeOD.append($('<option>', { value : 'rgp_soft' }).text("Rgp hard"));
	//lensTypeOD.append($("<option></option>").attr("value", "rgp_soft").text("RGP Soft"));
	//lensTypeOD.append($("<option></option>").attr("value", "rgp_hard").text("RGP Hard"));

	var lensTypeOS = $("#column1 #clTypeOS1");
	lensTypeOS.find('option[value=rgp]').remove();
	//lensTypeOS.append($("<option></option>").attr("value", "rgp_soft").text("RGP Soft"));
	//lensTypeOS.append($("<option></option>").attr("value", "rgp_hard").text("RGP Hard"));
}

/*function copyValuesODToOS(odName){
	var osName=odName.replace('OD','OS');
	if(dgi(odName) && dgi(osName)){
		dgi(osName).value=dgi(odName).value;

		if(dgi(odName+'ID') && dgi(osName+'ID')){
			dgi(osName+'ID').value=dgi(odName+'ID').value;
		}
	}
}*/

function makeMultiSelect(num){
	//$('#clws_charges_options').val($('#clws_charges'+num).html());
	//$("#clws_charges"+num).multiSelect({noneSelected:'', selectAll:false});

	$('#clws_charges'+num).on('change',function(){
		checkSingle(num, 'test', this.name,'sheet');  			
		//console.log($('#clws_charges1').val();
	})

/*	$("input[name = 'clws_charges"+num+"[]']").click(function() {
		
	});		
*/}

function dispHideMR(id){
	if($('#mrVals').length >= 0){
		//if(document.getElementById('mrVals').value != ''){  
			num=id.substr((id.length)-1);
			$("#mr"+num).slideToggle('slow');
		//}
	}
	else{
		top.fAlert("No MR data is available.");
	}
	var maxBlock = $('#clsheets').find('div[id^=column]:first-child');
	if(maxBlock.length) $("#mr1").insertBefore(maxBlock);
}

function dispHideCL(isFinal){
	if(isFinal == 0){
		top.fAlert("No final CL sheet is available.");
	}else{
		$("#finalCLRx").slideToggle('slow');
	}
	var maxBlock = $('#clsheets').find('div[id^=column]:first-child');
	if(maxBlock.length) $("#finalCLRx").insertBefore(maxBlock);
}

function dispHideDvaNva(element, dvaNva){

	var bilateral = $(element);
	var allaroundId = (bilateral.closest('.allaround').attr('id'));
	var columnCount = allaroundId.replace("column", "");

	if(dvaNva=='evalDva'){ 
		//dvaNva='evalDva'; num=id.replace('evalDva','');
		$('#column' + columnCount + ' #over_refraction_dva').toggle('slow');
	}else{ 
		//dvaNva='evalNva'; num=id.replace('evalNva','');
		$('#column' + columnCount + ' #over_refraction_nva').toggle('slow');
	}

	$('#column'+columnCount+" ."+dvaNva).toggle('slow');
	if($('#column' + columnCount + ' #over_refraction_dva').css('display') != 'none'){
		$('#column' + columnCount + ' #over_refraction_nva').css('padding-top', '100px');
	}else{
		$('#column' + columnCount + ' #over_refraction_nva').css('padding-top', '100px');
	}
}

function dispHideRgp(id){
	//alert(id);
	var odos='';
	var subCol='';
	arrEyes=['OD', 'OS', 'OU'];
	selVal=$('#'+id).val();
	part=id.split('_');
	colNum = part[0].replace(/clTypeOD|clTypeOS|clTypeOU/g, '');
	if(part[1]){ subCol='_'+part[1];}
	if(id.indexOf("OD")>0){ odos='od'; }
	else if(id.indexOf("OS")>0){ odos='os'; }

	if($("#clTypeOD" + colNum).val() == "scl" && $("#clTypeOS" + colNum).val() == "scl"){
		$("#column" + colNum + " .bilateralDiv").css('height', '410px');
		$("#column" + colNum + " .over_refraction_dva").css('padding-top', '100px');
	}

	// Set bilateral button div height
	if($("#clTypeOD" + colNum).val() == "cust_rgp" || $("#clTypeOS" + colNum).val() == "cust_rgp"){
		$("#column" + colNum + " .bilateralDiv").css('height', '620px');
		$("#column" + colNum + " .bilateralDiv").css('padding-top', '270px');
	}
	else if($("#clTypeOD" + colNum).val() == "rgp" || $("#clTypeOS" + colNum).val() == "rgp" ||
	$("#clTypeOD" + colNum).val() == "rgp_soft" || $("#clTypeOS" + colNum).val() == "rgp_soft" ||
	$("#clTypeOD" + colNum).val() == "rgp_hard" || $("#clTypeOS" + colNum).val() == "rgp_hard"){
		$("#column" + colNum + " .bilateralDiv").css('height', '475px');
		$("#column" + colNum + " .bilateralDiv").css('padding-top', '210px');
	}
	

	// For bilateral buttons
	/* var topMarginBilateral = 0;
	var topMarginFitting = 0;
	if($("#clTypeOD" + colNum) .val() == "scl" && $("#clTypeOS" + colNum) .val() == "scl"){
		topMarginBilateral = "=200px";
		topMarginFitting = "=172px";
	}else{
		topMarginBilateral = "=300px";
		topMarginFitting = "=200px";
	}
	$("#column" + colNum + " .btn-bilateral").animate({
		marginTop: topMarginBilateral
		}, 500, function() {
	});
	$("#column" + colNum + " .btn-fitting").animate({
		marginTop: topMarginFitting
		}, 500, function() {
	}); */
	//FOR PROSTHESIS/NO-CL
	active_deactive_cols(selVal, odos.toUpperCase(), colNum, subCol);
	if(selVal=='prosthesis' || selVal=='no-cl'){
		return false;
	}

	if(colNum>=1){
		//CHECING ALL CL TYPES IN SAME COLUMN
		var isRGP=isCustRGP=isSCL=false;
		for(x in arrEyes){
			eye=arrEyes[x];

			$('[id^="clType'+eye+colNum+'"]').each(function(index, element) {
				subColCur='';
				curId=$(this).attr('id');
				curVal=$(this).val();
				part=curId.split('_');
				colNum = part[0].replace(/clTypeOD|clTypeOS|clTypeOU/g, '');
				if(part[1]){ subColCur='_'+part[1];}				

				if(curVal=='rgp' || curVal=='rgp_soft' || curVal=='rgp_hard'){
					isRGP=true;
				}
				if(curVal=='cust_rgp')isCustRGP=true;
				if(curVal=='scl' || curVal=='prosthesis' || curVal=='no-cl')isSCL=true;

				//DISP HIDE CONTROLS
				$('#'+eye+colNum+subColCur+' .sclDivs input').each(function(index, element) {
					if(curVal=='scl' || curVal=='prosthesis' || curVal=='no-cl'){
						if($(this)){
							$(this).css('visibility', 'visible');							
						}
					}else{
						if($(this)){
							$(this).css('visibility', 'hidden');
						}
					}
				});
				$('#'+eye+colNum+subColCur+' .rgpDivs input').each(function(index, element) {
					if(curVal=='scl' || curVal=='prosthesis' || curVal=='no-cl'){
						if($(this)){
							$(this).css('visibility', 'hidden');
						}
					}else{
						if($(this)){
							$(this).css('visibility', 'visible');
						}
					}
				});
				$('#'+eye+colNum+subColCur+' .rgpCustDivs input').each(function(index, element) {
					if(curVal=='cust_rgp'){
						if($(this)){
							$(this).css('visibility', 'visible');
						}
					}else{
						if($(this)){
							$(this).css('visibility', 'hidden');						
						}
					}
				});
/*				var cnt=1;
				$('#'+eye+colNum+subColCur+' .evalNva div input').each(function(index, element) {
					if(cnt<=3){
						if(curVal=='rgp' || curVal=='cust_rgp'){
							if($(this)){
								$(this).css('visibility', 'hidden');
							}
						}else{
							if($(this)){
								$(this).css('visibility', 'visible');	
							}
						}
						cnt++;
					}
				});	*/

				//SET BC,DIAMETER VALUES
				if(curVal=='scl'){
					$('#'+eye+colNum+subColCur+' .rgpBc').css('display','none');
					$('#'+eye+colNum+subColCur+' .rgpDiameter').css('display','none');
					$('#'+eye+colNum+subColCur+' .rgpSphere').css('display','none');
					$('#'+eye+colNum+subColCur+' .rgpBc #elemBc'+eye+colNum+subColCur).attr('disabled', true);
					$('#'+eye+colNum+subColCur+' .rgpDiameter #elemDiameter'+eye+colNum+subColCur).attr('disabled', true);
					$('#'+eye+colNum+subColCur+' .rgpSphere #elemSphere'+eye+colNum+subColCur).attr('disabled', true);
					$('#'+eye+colNum+subColCur+' .sclBc').css('display','block');
					$('#'+eye+colNum+subColCur+' .sclDiameter').css('display','block');
					$('#'+eye+colNum+subColCur+' .sclSphere').css('display','block');
					$('#'+eye+colNum+subColCur+' .sclColor').css('display','block');
					
					//sclSphere
					
					$('#'+eye+colNum+subColCur+' .sclBc #elemBc'+eye+colNum+subColCur).attr('disabled', false);
					$('#'+eye+colNum+subColCur+' .sclDiameter #elemDiameter'+eye+colNum+subColCur).attr('disabled', false);
					$('#'+eye+colNum+subColCur+' .sclSphere #elemSphere'+eye+colNum+subColCur).attr('disabled', false);
					
					
				}else if(curVal=='rgp' || curVal=='rgp_soft' || curVal=='rgp_hard' || curVal=='cust_rgp'){
					$('#'+eye+colNum+subColCur+' .sclBc').css('display','none');
					$('#'+eye+colNum+subColCur+' .sclDiameter').css('display','none');
					$('#'+eye+colNum+subColCur+' .sclSphere').css('display','none');
					$('#'+eye+colNum+subColCur+' .sclBc #elemBc'+eye+colNum+subColCur).attr('disabled', true);
					$('#'+eye+colNum+subColCur+' .sclDiameter #elemDiameter'+eye+colNum+subColCur).attr('disabled', true);
					$('#'+eye+colNum+subColCur+' .sclSphere #elemSphere'+eye+colNum+subColCur).attr('disabled', true);
					$('#'+eye+colNum+subColCur+' .rgpBc').css('display','block');
					$('#'+eye+colNum+subColCur+' .rgpDiameter').css('display','block');
					$('#'+eye+colNum+subColCur+' .rgpSphere').css('display','block');
					
					$('#'+eye+colNum+subColCur+' .rgpBc #elemBc'+eye+colNum+subColCur).attr('disabled', false);
					$('#'+eye+colNum+subColCur+' .rgpDiameter #elemDiameter'+eye+colNum+subColCur).attr('disabled', false);
					$('#'+eye+colNum+subColCur+' .rgpSphere #elemSphere'+eye+colNum+subColCur).attr('disabled', false);
				}
				
				//SET DRAWING IMAGES
/*				odos=eye.toLowerCase();
				if(odos=='od' || odos=='os'){
					var dispHide='block';
					if(subColCur!=''){
						dispHide='none';
					}
					if(curVal=='scl' ){
						if(odos=='od'){
							$('#column'+colNum+' #scl_odDrawingPngImg'+colNum+subColCur).css('display', 'block');
							$('#column'+colNum+' #rgp_odDrawingPngImg'+colNum+subColCur).css('display', 'none');
						}else if(odos=='os'){
							dispHide='none';
							if($('#scl_idoc_drawing_id_os'+colNum+subColCur).val()!='0' && $('#scl_idoc_drawing_id_os'+colNum+subColCur).val()!=''){ dispHide='block';}
							$('#column'+colNum+' #scl_osDrawingPngImg'+colNum+subColCur).css('display', dispHide);
							$('#column'+colNum+' #rgp_osDrawingPngImg'+colNum+subColCur).css('display', 'none');
						}
					}else{
						if(odos=='od'){
							$('#column'+colNum+' #scl_odDrawingPngImg'+colNum+subColCur).css('display', 'none');
							$('#column'+colNum+' #rgp_odDrawingPngImg'+colNum+subColCur).css('display', dispHide);
						}else if(odos=='os'){
							dispHide='none';
							if($('#rgp_idoc_drawing_id_os'+colNum+subColCur).val()!='0' && $('#rgp_idoc_drawing_id_os'+colNum+subColCur).val()!=''){ dispHide='block';}							
							$('#column'+colNum+' #scl_osDrawingPngImg'+colNum+subColCur).css('display', 'none');
							$('#column'+colNum+' #rgp_osDrawingPngImg'+colNum+subColCur).css('display', dispHide);
						}			
					}
				}*/				
			});	
		}
		
		if(isRGP==false && isCustRGP==false){
			$('#column'+colNum+' .rgpDivs').hide('slow');	
			$('#column'+colNum+' .rgpCustDivs').hide('slow');
		}else if(isCustRGP==true){
			$('#column'+colNum+' .rgpDivs').show('slow');

			$('#column'+colNum+' .rgpCustDivs').show('slow');	
		}else if(isRGP==true){
			$('#column'+colNum+' .rgpDivs').show('slow');	
			$('#column'+colNum+' .rgpCustDivs').hide('slow');	
		}
		if(isSCL==true){
			$('#column'+colNum+' .sclDivs').show('slow');	
		}else{
			$('#column'+colNum+' .sclDivs').hide('slow');	
		}
	}
}

function reloadWindow(){
	dgi("prevLoader").style.display='block';
	if($('#oldSheets').val()!=''){
		window.location.href="contact_lens_worksheet_popup.php?mode=oldSheets&clws_id="+$('#oldSheets').val();
	}
}

function copyValuesODToOS(odName,osName,callFrom){
	if(callFrom=='notMake'){
		odElem=$(odName).parent().parent().attr('aria-labelledby');
		if (typeof odElem !== "undefined"){
    		if(odElem.indexOf('OD')>-1){
    			osElem=odElem.replace('OD','OS');
    			$('#'+osElem).val($('#'+odElem).val());
    		}
		}
	}else{
		//alert(odName+' '+osName);
		if(dgi(odName) && dgi(osName)){
			dgi(osName).value=dgi(odName).value;

			if(dgi(odName+'ID') && dgi(osName+'ID')){
				dgi(osName+'ID').value=dgi(odName+'ID').value;
			}
		}	
	}
}

// function called when Contact Lens Order Submitted at Popup.
function redirectToEnterCharges(pid,eid){
	//var send_url ="../accounting/accountingTabs.php?flagSetPid=true&tab=enterCharges";
	//window.opener.parent.core_redirect_to("Accounting", send_url);
	window.opener.top.core_set_pt_session(top.fmain, pid, '../accounting/accounting_view.php?encounter_id='+eid+'&uniqueurl='+eid+'&del_charge_list_id=0&tabvalue=Enter_Charges&show_load=yes');
	self.close();
}


function deleteWorksheet(mode, num){
/*	var arrCLWS_ids=[];
	var arrColNums=[];
	var i=0;
	$('input:checkbox[id^="checkboxDel"]').each(function(index, element) {
        if($(this).is(":checked")){
			id=$(this).attr('id');
			num=id.replace('checkboxDel','');
			if($('#clws_id'+num)){
				if($('#clws_id'+num).val()>0){
					arrCLWS_ids[i]= $('#clws_id'+num).val();
					arrColNums[i]= num;
					i++;
				}
			}
		}
    });*/

	//SHEETS SHOULD DELETE IN DESCENDING ORDER SO THAT COLUMN1 ALWAYS REMAIN
	var clws_id=$('#clws_id'+num).val();
	$.ajax({ 
		url: "cl_ajax.php?clws_id="+clws_id+"&mode="+mode,
		success: function(updated){
			if(updated=='1'){
				if(mode=='undelete'){
					$('#column'+num+' .divUndelete').html('');
					top.fAlert('Worksheet undeleted successfully.');
				}else{
					$('#clws_id'+num).val('');
					removeColumn(num, '', 'deleteDatabase');
					top.fAlert('Worksheet deleted successfully.');
				}
			}else{
				top.fAlert("No action is performed.");
			}
			resetDropDowns();
		}
	});


/*	var isDelExist=isUnDelExist=false;
	$('div[id^="column"] .divDelUndel').each(function(index, element) {
        if($(this).text()=='Del'){ 	isDelExist=true;}
		else if($(this).text()=='Undel'){ isUnDelExist=true; }
    });
	if(isDelExist==true){ $('#btnUndelSheet').css('display', 'block'); }else{$('#btnUndelSheet').css('display', 'none');}
	if(isUnDelExist==true){ $('#btnDelSheet').css('display', 'block'); }else{$('#btnDelSheet').css('display', 'none');}*/
}

function resetDropDowns(){
	var arrOldSheets=[];
	var optionVals='';
	$.ajax({ 
		type: "POST",
		url: "cl_ajax.php",
		data: "mode=getDDOptions",
		success: function(data){
			if(data!=''){
				r = jQuery.parseJSON(data);
				oldWorksheets	= r.oldWorksheets;
				copyFromSheets	= r.copyFromSheets;
				
				//HISTORY DD
				var opt='<select name="oldSheets" id="oldSheets" onChange="javascript:reloadWindow();" style="width:180px;">';
				opt+=oldWorksheets;
				opt+='</select>';
				$('#oldSheets').parent().html(opt);

				//COPY FROM DD
				$('select[id^="copyFromId"]').each(function(index, element) {
                    id=$(this).attr('id');

					var opt='<select name="'+id+'" id="'+id+'" style="width: 90px;" onchange="loadCopyFromWorkSheet(this.id, this.value, this);">';
					opt+=copyFromSheets;
					opt+='</select>';

					$('#'+id).replaceWith(opt);
                });
			}
		}
	});		
}

function showAppletsDiv(eye, id){
	var divId=drawingData=drawingOriginalData=description=drawingDataPath='';
	var leftVal = 0;
	//var sclRgp=id.substr(0,3);
	var sno=id.replace('DrawingPngImg_'+eye, '');
	var idoc_drawing_id=parentCtrlId=parentImgId='';
	
	idoc_drawing_id=dgi('idoc_drawing_id_'+eye+sno).value;
	parentCtrlId='idoc_drawing_id_'+eye+sno;
	parentImgId='DrawingPngImg_'+eye+sno;
	od_desc=dgi('description_A_'+eye+sno).value;
	os_desc=dgi('description_B_'+eye+sno).value;

	var width=1400; var height=730;
	var br=navigator.userAgent;
	if(br.search("Chrome")>-1 || br.search("Firefox")>-1){
	  width=1550; height=740;	
	}else{
	  width=1400; height=730;
	}
	window.open('onload_wv.php?elem_action=Drawingpane&cl_draw=1&sno='+sno+'&eye='+eye+'&idoc_drawing_id='+idoc_drawing_id+'&parentCtrlId='+parentCtrlId+'&parentImgId='+parentImgId+'&od_desc='+od_desc+'&os_desc='+os_desc,'clDrawing'+eye+sno,'height='+height+'px, width='+width+'px, left=0, top=5px, resizable=yes');
  }


function clearVal(id){
	if($('#'+id).val()=='Other'){ 
		$('#'+id).val('');
		$('#'+id).css('color', '#000000');
		$('#'+id).css('font-style', 'normal');		
	}
}
function setVal(id){
	if($('#'+id).val()=='' || $('#'+id).val()=='Other'){ 
		$('#'+id).css('color', '#696969');
		$('#'+id).css('font-style', 'italic');		
		$('#'+id).val('Other');
	}
}
function clearMakeId(element){
	var field = $(element);
	/* if(id.indexOf('OD')!='-1'){
		otherElem=id.replace('OD','OS');
	}else{
		otherElem=id.replace('OS','OD');
	}
	
	$('#'+id+'ID').val('');
	if(document.getElementById(otherElem+'ID')){
		$('#'+otherElem+'ID').val('');
	} */
	//alert('#' + field.attr("id") + 'ID');
	//if(field.val() == ""){
		$('#' + field.attr("id") + 'ID').val('');
	//}
}


function active_deactive_cols(val, eye, col, subCol){
	if(col){
		var obj='';
		colNumT=col+subCol;
		otherEye=(eye=='OD') ? 'OS' : 'OD';
		
		path='#column'+col+' div[id="'+eye+colNumT+'"]';
		if(val=='prosthesis' || val=='no-cl'){
			var hasSclOrRgp=false;
			obj=$(path+' input[type="text"]:not(#cpt_evaluation_fit_refit'+col+'), #elemDvaOU'+col+', #elemNvaOU'+col+', #elemEvalDvaOU'+col+', #elemEvalNvaOU'+col);
			obj.val('');
			obj.css('background-color', '#CCC');
			obj.attr("disabled", true);

			//CHECK TO D-ACTIVE Common Fields
			$('#column'+col+' select[id="clType'+eye+colNumT+'"]').each(function(index, element) {
				if($(this).val()=='scl' || $(this).val()=='rgp' || $(this).val()=='cust_rgp'){
					hasSclOrRgp=true;
				}
            });
			if(hasSclOrRgp==false){
				if($('#column'+col+' select[name="clType'+otherEye+colNumT+'"]').val()=='prosthesis' || $('#column'+col+' select[name="clType'+otherEye+colNumT+'"]').val()=='no-cl'){
					obj=$('#column'+col+' .commonSheetData select');
					obj.css('background-color', '#ccc');
					obj.attr("disabled", true);
				}
			}			
		}else{
			var hasProsOrNoCl=false;
			obj=$(path+' input[type="text"]:not(#cpt_evaluation_fit_refit'+col+', #elemDvaOU'+col+', #elemNvaOU'+col+', #elemEvalDvaOU'+col+', #elemEvalNvaOU'+col+'), #column'+col+' .commonSheetData select');
			obj.css('background-color', '#fff');
			obj.attr("disabled", false);

			//CHECK TO ACTIVE OU VALUES
			$('#column'+col+' select[id^="clType'+eye+colNumT+'"]').each(function(index, element) {
				if($(this).val()=='prosthesis' || $(this).val()=='no-cl'){
					hasProsOrNoCl=true;
				}
            });
			if(hasProsOrNoCl==false){
				obj=$('#elemDvaOU'+col+', #elemNvaOU'+col+', #elemEvalDvaOU'+col+', #elemEvalNvaOU'+col);					
				obj.css('background-color', '#fff');
				obj.attr("disabled", false);
			}
		}
	}
}

function setCLPowerDefaultToZero(){
	$(".clpower").click(function(){
		var $parentDiv = $(this).parent();
		var $ul = $(this).parent().children('ul');
		$ul.animate({ scrollTop:1950 }, 1);
		$ul.children('#li0').css('background', '#CED2D2');
	});
}
function setCLCylinderDefaultToZero(){
	$(".clcylinder").click(function(){
		$(this).parent().children('ul').animate({ scrollTop:1500 }, 1);
		$(this).parent().children('ul').children('#cyl_0').css('background', '#CED2D2');
	});
}

var dataLength=0;
var arrCLOrder=[];
var charges_options='';

//var arrCLChargesAdmin=[];
$(document).ready(function(){
	<?php
	if(isset($GLOBALS['CL_POWER_RANGE'])){
	    echo "setCLPowerDefaultToZero();";
	    echo "setCLCylinderDefaultToZero();";
	}
	?>

	$("#OS1 ul").addClass('os_left_margin');
	var arrCLData=[];
	charges_options='<?php echo $charges_options;?>';
	//SET WINDOW HEIGHT WIDTH
	var winHeight=window.innerHeight;
	var winWidth=window.innerWidth;

//	if(winHeight>975)winHeight=975;

	//window.moveTo(50,0);
	//window.resizeTo("", winHeight);
//	window.height=winHeight;
	
	
	//SET INNER DIV HEIGHT

/*	var browser='<?php echo $browser;?>';
	if (browser=='ie') // If Internet Explorer, return version number
		winHeight=winHeight-118;	
	else  
		winHeight=winHeight-138;	*/
	//winHeight=winHeight-120;	
	//winHeight=winHeight;
	document.getElementById('clsheets').style.height=winHeight-159+'px';
	document.getElementById('clsheets').style.width=winWidth-15+'px';
	
	var subWidth=$('#OD1').width() * 2;
	$('#comments1').css('width', subWidth);
	$('#cpt_evaluation_fit_refit1').css('width', subWidth);

	epost_addTypeAhead('textarea[id*=comments]','<?php echo $GLOBALS['rootdir'];?>');
	//activateMenuData(1);
	//$( ".date-pick" ).datepicker({changeMonth: true,changeYear: true,dateFormat:opener.top.jquery_date_format});
	
	//MULTI SELECT
	//makeMultiSelect(1);

	//SETTING OF OTHER FIELDS
	$('.otherClass').each(function(index, element) {
    	setVal($(this).attr('id'));
    });

	//PARSING SAVED SHEETS
	arrCLData = <?php echo json_encode($arrCLData); ?>;
	var arrCLEval = <?php echo json_encode($arrCLEval); ?>;
	var arrCLOrder= <?php echo json_encode($arrCLOrder); ?>;
	//var arrCLChargesAdmin=<?php echo json_encode($arrCLChargesAdmin); ?>;
	var arrCL1=new Array();
	var arrCL1T=new Array();

	//$.each(clCommentsDataArray, function(key, value){
	//});

	//$.each(clCommentsDataArray, function(key, item)
	//{
	//	alert(key);
	//	alert(item);
	//});

	/* $('#clTypeOD1').on('change', function(){
		if(this.value == "scl"){
			$("#copytoos").css("height", "410px");
		}else if(this.value == "rgp"){
			$("#copytoos").css("height", "470px");
		}else if(this.value == "cust_rgp"){
			$("#copytoos").css("height", "620px");
		}
	}); */
	
	/*for(x in arrCL){
		arrCL1[x]=new Array();
		arrCL1T[x]=new Array();
		for(y in arrCL[x]){
			arrCL1[x][y]=new Array();
			for(z in arrCL[x][y]){
				arrCL1[x][y][z]=new Array();
				
				arrCL1[x][y][z]=arrCL[x][y][z];
				arrCL1T[x]=arrCL[x][y][z]['clws_id'];
			}
		}
	}

	arrCL1T.reverse();
	var arrCLData=new Array();
	for(x in arrCL1T){	//SOLVING THE ISSUE OF RANDOM KEY TYPE ARRAY
		clws_id=arrCL1T[x];
		arrCLData[clws_id]=new Array();
		for(y in arrCL1[clws_id]){
			arrCLData[clws_id][y]=new Array();
			for(z in arrCL1[clws_id][y]){		
				arrCLData[clws_id][y][z]=new Array();
				arrCLData[clws_id][y][z]=arrCL1[clws_id][y][z];
			}
		}
		dataLength++;
	}*/

	/*for(x in arrCLData){
		for(y in arrCLData[x]){
			for(z in arrCLData[x][y]){		
				alert(x+ ' '+y+' '+z+' '+arrCLData[x][y][z]['id']);
			}
		}
	}*/
	
	dataLength=arrCLData.length;

	mode='<?php echo $_REQUEST['mode'];?>';
	clws_id='<?php echo $_REQUEST['clws_id'];?>';
	if(mode=='oldSheets' && dataLength<=0 && (clws_id=='undeleted' || clws_id=='deleted')){
		top.fAlert('No '+clws_id+' sheet(s) exists.');
	}
	
	if(dataLength>0){
		fillData(arrCLData,arrCLEval,arrCLOrder,arrCLChargesAdmin,null, false, clCommentsArray);
	}
	
	
	//GET PROSTHESIS VALUE FROM WORKVIEW
/*	var colNum=0;
	var obj=window.opener.fmain.document;
	var pros_val=obj.getElementById('is_od_os').value;
	var clws_id_WV=obj.getElementById('clws_id').value;
	$('input[id^="clws_id"]').each(function(index, element) {
		if(colNum<=0){
			if(pros_val!=''){
				//FOR NEW SHEET
				//if((clws_id_WV>0 && this.value==clws_id_WV) || this.value==''){	
				if(this.value==''){	
					id=$(this).attr('id');
					colNum=id.replace('clws_id','');
					if(pros_val=='OU'){
						$('#column'+colNum+' #clTypeOD'+colNum+', #column'+colNum+' #clTypeOS'+colNum).val("prosthesis");
						$('#column'+colNum+' #clTypeOD'+colNum+', #column'+colNum+' #clTypeOS'+colNum).trigger("change");
					}else{
						$('#column'+colNum+' #clType'+pros_val+colNum).val("prosthesis");
						$('#column'+colNum+' #clType'+pros_val+colNum).trigger("change");
					}
				}
			}
		}
    });*/
	
	//ACTB 
	//OD
	$("#elemMakeOD1").typeahead({source:arrManufac,scrollBar:true});
	var autocomplete = $("#elemMakeOD1").typeahead();
	//autocomplete.data('typeahead').source = arrManufac;
	autocomplete.data('typeahead').updater = function(item){
		$("#elemMakeOD1ID").val(lensNameArray[item]);
		$("#elemBcOD1").val(lensDetailsArray[lensNameArray[item]].split('~')[0]);
		$("#elemDiameterOD1").val(lensDetailsArray[lensNameArray[item]].split('~')[1]);
    	return item;
    }
	$('#elemMakeOD1').blur(function() { set_properties('elemMakeOD1');  });
	
	//OS
	$("#elemMakeOS1").typeahead({source:arrManufac,scrollBar:true});
	var autocomplete = $("#elemMakeOS1").typeahead();
	//autocomplete.data('typeahead').source = arrManufac;
	autocomplete.data('typeahead').updater = function(item){
		$("#elemMakeOS1ID").val(lensNameArray[item]);
		$("#elemBcOS1").val(lensDetailsArray[lensNameArray[item]].split('~')[0]);
		$("#elemDiameterOS1").val(lensDetailsArray[lensNameArray[item]].split('~')[1]);
    	return item;
    }		

	//WORKING OF OTHER DROP DOWNS
	$('.dropdown-toggle').dropdown();
	$(".dropdown-menu li").click(function(){
		var selVal=$(this).text();
		odElem=$(this).parent().parent().find('input[type="text"]:eq(0)');
		odElem.val(selVal);
	});
	$("#OS1 .typeahead").css('margin-left', '44px');

	// To remove extra bilateral buttons
	$('div[id^="OS"]').each(function(){
		var id = $(this).attr('id');
		if(id.indexOf("_") !== -1){
			$("#" + id).find("input").each(function(){
				if($(this).hasClass("btn-bilateral")){
					$(this).css("opacity", 0);
					$(this).css("cursor", "default");
					$(this).attr("disabled", true);
				}
			});
		}
	});

	dgi("prevLoader").style.display='none';
	//---CL SHEET PRINT BUTTON DISABLE, IF TRIAL IS CHECKED---
	$("[id^='column']").each(function()	
	{
		str = $(this).attr('id');
		var num = str.replace("column", "");
		if($('#clwsLabels'+num+' #NewTrialChk').is(':checked'))
		{
			$('#column'+num+' #btnPrint1').prop('disabled',true);
		}
		else
		{
			$('#column'+num+' #btnPrint1').prop('disabled',false);
		}
	});
});


function fillMenuValue(ctrl){
	var selVal=$(ctrl).text();
	odElem=$(ctrl).parent().parent().parent().find('input[type="text"]:eq(0)');
	odElem.val(selVal);
}

function set_properties(id){
	var odos = (id.indexOf('OD')=='-1') ? 'OS' : 'OD';
	var arrN = id.split(odos);
	var n = arrN[1];
	/* if(n){
		if(odos == "OD"){
			//console.log(id, odos, $('#'+id).val());
		}
		if(odos=='OD'){
			osId=id.replace('OD','OS');
			if($('#'+osId)){
				//$('#'+osId).val($('#'+id).val());
			}
			if($('#'+osId+'ID')){
				//$('#'+osId+'ID').val($('#'+id+'ID').val());
			}
			if($('#elemBcOS'+n)){
				//$('#elemBcOS'+n).val($('#elemBcOD'+n).val());
			}
			if($('#elemDiameterOS'+n)){
				//$('#elemDiameterOS'+n).val($('#elemDiameterOD'+n).val());
			}
		}
	} */
}

$('.dropdown').on('shown.bs.dropdown', function () {
	var scrollEnabled = $(this).data('scroll');
	
	//Input value 
	if(scrollEnabled === true){
		var inputVal = '';
		if($(this).find('input:first-child').val()) inputVal = $(this).find('input:first-child').val();
		
		var ulElem = $(this).find('ul.dropdown-menu');
		if(ulElem.length){
			var scrollHeight = 0;
			var valueScroll = 0; 
			
			var optCount = ulElem.find('li').length;
			if(optCount > 0) optCount = optCount / 2.1;
			
			var valueFound = false;
			ulElem.find('li').each(function(id , optElement){
				//If list is there scroll to half of that
				if(id < optCount) scrollHeight += $(optElement).height();
				
				//If input has value scroll to that
				if(inputVal && inputVal !== null){
					var liVal = $(optElement).attr('id').replace('li', '');
					
					if(liVal == inputVal) valueFound = true;
					
					if(!valueFound) valueScroll += $(optElement).height();
				}
			});
			
			//If value scroll is there 
			if(valueScroll > 0) scrollHeight = valueScroll;
			
			ulElem.animate({ scrollTop: scrollHeight }, 1000);
		}
		
	}
	
  /* var ele = $(this).find("li>a:contains('+15.00')");
  var posi = ele.offset().top-ele.innerHeight();
  $('.dropdown-menu').animate({
        scrollTop: posi
    }, 1000); */
})

$("[id^='column']").css("display", "inline-block");

// Checks if both OD and OS eye contact lens type is same
function compareBothCLs(element){
    var bilateral = $(element);
    var allaroundId = (bilateral.closest('.allaround').attr('id'));
    var columnCount = allaroundId.replace("column", "");
    if($("#clTypeOD" + columnCount).val() !== $("#clTypeOS" + columnCount).val()){
        return false;
    }else{
        return columnCount;
    }
}

function clickBilateral(element, source, dest){
	var columnCount = compareBothCLs(element);
    if(columnCount === false){                                // If OD and OS lens types are not same
		alert("Lens types for OD and OS are not same.");
		return;
	}

	var clType = $("#clType" + source + columnCount).val();
	$("#elemMake" + dest + columnCount).val($("#elemMake" + source + columnCount).val());
	$("#elemMake" + dest + columnCount + 'ID').val($("#elemMake" + source + columnCount + 'ID').val());
	$("#elemCylinder" + dest + columnCount).val($("#elemCylinder" + source + columnCount).val());
	$("#elemAxis" + dest + columnCount).val($("#elemAxis" + source + columnCount).val());
	$("#elemColor" + dest + columnCount).val($("#elemColor" + source + columnCount).val());
	$("#elemAdd" + dest + columnCount).val($("#elemAdd" + source + columnCount).val());
	$("#elemColor" + dest + columnCount).val($("#elemColor" + source + columnCount).val());
	$("#elemDva" + dest + columnCount).val($("#elemDva" + source + columnCount).val());
	$("#elemNva" + dest + columnCount).val($("#elemNva" + source + columnCount).val());
	if(clType == "scl"){
		$(".sclSphere #elemSphere" + dest + columnCount).val($(".sclSphere #elemSphere" + source + columnCount).val());
		$(".sclDiameter #elemDiameter" + dest + columnCount).val($(".sclDiameter #elemDiameter" + source + columnCount).val());
		$(".sclBc #elemBc" + dest + columnCount).val($(".sclBc #elemBc" + source + columnCount).val());
	}
	if(clType == "rgp" || clType == "cust_rgp" || clType == "rgp_soft" || clType == "rgp_hard"){
		$("#elemOZ" + dest + columnCount).val($("#elemOZ" + source + columnCount).val());
		$("#elemCT" + dest + columnCount).val($("#elemCT" + source + columnCount).val());
		$(".rgpSphere #elemSphere" + dest + columnCount).val($(".rgpSphere #elemSphere" + source + columnCount).val());
		$(".rgpDiameter #elemDiameter" + dest + columnCount).val($(".rgpDiameter #elemDiameter" + source + columnCount).val());
		$(".rgpBc #elemBc" + dest + columnCount).val($(".rgpBc #elemBc" + source + columnCount).val());
	}
	if(clType == "cust_rgp"){
		$("#elemTwoDegree" + dest + columnCount).val($("#elemTwoDegree" + source + columnCount).val());
		$("#elemThreeDegree" + dest + columnCount).val($("#elemThreeDegree" + source + columnCount).val());
		$("#elemPCW" + dest + columnCount).val($("#elemPCW" + source + columnCount).val());
		$("#elemBlend" + dest + columnCount).val($("#elemBlend" + source + columnCount).val());
		$("#elemEdge" + dest + columnCount).val($("#elemEdge" + source + columnCount).val());
	}




	/* $("#" + source + columnCount).find("input").each(function(){
		var clType = $("#clType" + source + columnCount).val();
		var value = $(this).attr("id").replace(columnCount, "");
		if(clElementsArray[clType][$(this).attr("id").replace(columnCount, "")] !== 'undefined' &&
			clElementsArray[clType][$(this).attr("id").replace(columnCount, "")] !== null &&  
			clElementsArray[clType][$(this).attr("id").replace(columnCount, "")] !== "")
		{
			var copyFrom = $(this).attr("id");
			var copyTo = $(this).attr("id").replace(source, dest);
			//alert("copy to: " + copyTo);
			if($(this).attr("id") == ("elemBc" + source + columnCount)){
				if(clType == "scl"){
					$(".sclBc #" + copyTo).val($(".sclBc #" + copyFrom).val());
				}else{
					$(".rgpBc #" + copyTo).val($(".rgpBc #" + copyFrom).val());
				}
			}else if($(this).attr("id") == ("elemDiameter" + source + columnCount)){
				if(clType == "scl"){
					$(".sclDiameter #" + copyTo).val($(".sclDiameter #" + copyFrom).val());
				}else{
					$(".rgpDiameter #" + copyTo).val($(".rgpDiameter #" + copyFrom).val());
				}
			}else if($(this).attr("id") == ("elemSphere" + source + columnCount)){
				if(clType == "scl"){
					$(".sclSphere #" + copyTo).val($(".sclSphere #" + copyFrom).val());
				}else{
					$(".rgpSphere #" + copyTo).val($(".rgpSphere #" + copyFrom).val());
				}
			}else if($(this).attr("id") == ("elemColor" + source + columnCount)){
				if(clType == "scl"){
					$(".SclColor #elemColor" + dest + columnCount).val($(".SclColor #elemColor" + source + columnCount).val());
				}
			}else{
				$("#" + copyTo).val($("#" + copyFrom).val());
			}
		}
	}); */
}

function copyOverRefractionDva(element, source, dest){
	var columnCount = compareBothCLs(element);
    if(columnCount === false){                                // If OD and OS lens types are not same
        alert("Lens types for OD and OS are not same.");
        return;
    }
	$(".evalDva #elemDvaSphere" + dest + columnCount).val($("#elemDvaSphere" + source + columnCount).val());
	$(".evalDva #elemDvaCylinder" + dest + columnCount).val($("#elemDvaCylinder" + source + columnCount).val());
	$(".evalDva #elemDvaAxis" + dest + columnCount).val($("#elemDvaAxis" + source + columnCount).val());
	$(".evalDva #elemEvalDva" + dest + columnCount).val($("#elemEvalDva" + source + columnCount).val());
}

function copyOverRefractionNva(element, source, dest){
	var columnCount = compareBothCLs(element);
    if(columnCount === false){                                // If OD and OS lens types are not same
        alert("Lens types for OD and OS are not same.");
        return;
    }
	$("#elemNvaSphere" + dest + columnCount).val($("#elemNvaSphere" + source + columnCount).val());
	$("#elemNvaCylinder" + dest + columnCount).val($("#elemNvaCylinder" + source + columnCount).val());
	$("#elemNvaAxis" + dest + columnCount).val($("#elemNvaAxis" + source + columnCount).val());
	$("#elemEvalNva" + dest + columnCount).val($("#elemEvalNva" + source + columnCount).val());
}

function copyCLFittings(element, source, dest){
	var columnCount = compareBothCLs(element);
    if(columnCount === false){                                // If OD and OS lens types are not same
        alert("Lens types for OD and OS are not same.");
        return;
    }
	$("#elemComfort" + dest + columnCount).val($("#elemComfort" + source + columnCount).val());
	$("#elemMovement" + dest + columnCount).val($("#elemMovement" + source + columnCount).val());
	$("#elemRotation" + dest + columnCount).val($("#elemRotation" + source + columnCount).val());
	$("#elemCondition" + dest + columnCount).val($("#elemCondition" + source + columnCount).val());
	if($("#clType" + source + columnCount).val() == "scl"){
		$("#elemPosition" + dest + columnCount).val($("#elemPosition" + source + columnCount).val());
	}else if($("#clType" + source + columnCount).val() == "rgp" || $("#clType" + source + columnCount).val() == "rgp_soft" || $("#clType" + source + columnCount).val() == "rgp_hard"){
		$("#elemPosition" + dest + columnCount).val($("#elemPosition" + source + columnCount).val());
		$("#elemPositionB" + dest + columnCount).val($("#elemPositionB" + source + columnCount).val());
		$("#elemPositionA" + dest + columnCount).val($("#elemPositionA" + source + columnCount).val());
		$("#elemFLPatter" + dest + columnCount).val($("#elemFLPatter" + source + columnCount).val());
		$("#elemInverted" + dest + columnCount).val($("#elemInverted" + source + columnCount).val());
		$("#elemPositionBOther" + dest + columnCount).val($("#elemPositionBOther" + source + columnCount).val());
		$("#elemPositionAOther" + dest + columnCount).val($("#elemPositionAOther" + source + columnCount).val());
		//elemPositionBOtherOD1
	}else if($("#clType" + source + columnCount).val() == "cust_rgp"){
		$("#elemPosition" + dest + columnCount).val($("#elemPosition" + source + columnCount).val());
		$("#elemPositionB" + dest + columnCount).val($("#elemPositionB" + source + columnCount).val());
		$("#elemPositionA" + dest + columnCount).val($("#elemPositionA" + source + columnCount).val());
		$("#elemFLPatter" + dest + columnCount).val($("#elemFLPatter" + source + columnCount).val());
		$("#elemInverted" + dest + columnCount).val($("#elemInverted" + source + columnCount).val());
	}
}
/* var commentCount = 1;
function addCommentBox(element){
	var addCommentImage = $(element);
	var allaroundId = (addCommentImage.closest('.allaround').attr('id'));
	var columnCount = allaroundId.replace("column", "");
	//alert("colum count: " + columnCount);
	var latestDivId = addCommentImage.closest('.comments_text_div').attr('id');
	var newCommentDiv = "";
	newCommentDiv = "<div id='commentdiv_" + columnCount + "_" + (commentCount + 1) + "' class='row comments_text_div' style='width:610px;'>";
	newCommentDiv += "<div class='col-xs-10'>";
	newCommentDiv += "<textarea id='txtcomments_" + columnCount + "_" + (commentCount + 1) + "' name='comments' rows='2' class='form-control' style='width:100%;height:42px;display:inline;'>" + (commentCount + 1) + "</textarea>";
	newCommentDiv += "</div>";
	newCommentDiv += "<div class='col-xs-1'>";
	newCommentDiv += "<figure id='plus" + (commentCount + 1) + "' class='comment_figure' style='display:inline;'>";
	newCommentDiv += "<span class='glyphicon glyphicon-plus' onClick='addCommentBox(this);' title='Add comment'></span>";
	newCommentDiv += "</figure>";
	newCommentDiv += "</div>";
	newCommentDiv += "<div class='col-xs-1'>";
	newCommentDiv += "<figure id='delete" + (commentCount + 1) + "' class='comment_figure_delete' style='display:inline;'>";
	newCommentDiv += "<span class='glyphicon glyphicon-remove' onClick='addCommentBox(this);' title='Delete comment'></span>";
	newCommentDiv += "</figure>";
	newCommentDiv += "</div>";
	newCommentDiv += "</div>";
	$("#" + latestDivId).before(newCommentDiv);
	$(".comment_figure").each(function(){
		var figureCount = $(this).attr('id').replace('plus', '');
		if(figureCount <= commentCount){
			$(this).hide();
		}
	});
	commentCount = (commentCount + 1);
} */
function addCommentBox(column, sheetId, commentDesc, newComment, clCommentsTextareaCount, commentId){
	if(typeof column == 'object'){
		var element = $(column);
		var mainColumnId = $(column).closest(".allaround").attr('id');
	}else{
		var element = false;
		var mainColumnId = column.replace('column', '');
	}
	
	if(newComment == "new"){		// If new comment
		//sheetId = (element.closest("div[id^=column]")).children("input[id^=clws_id]").val() + "_new";
		sheetId = "comment_new_" + mainColumnId + "[]";
		commentId = 0;
	}else{
		sheetId = "comment_update_" + mainColumnId + "[]";
	}

	// To disable ajax delete for copied comments 
	commentId = (typeof commentDesc == 'string') ? commentId : 0;

	var newCommentDiv = "";
	newCommentDiv += "<div id='div_" + commentId + "' class='row comments_text_div' style='width:610px;height:auto;margin-bottom:10px;'>";
	newCommentDiv += "<div class='col-xs-11' style='height:auto;'>";
	newCommentDiv += "<textarea id='cltextarea_" + commentId + "' name='" + sheetId + "' class='cltextarea' rows='2' style='width:100%;display:inline;resize:none;'>" + commentDesc + "</textarea>";
	//newCommentDiv += commentDesc;
	newCommentDiv += "</div>";
	newCommentDiv += "<div class='col-xs-1 figure'>";
	newCommentDiv += "<figure id='plus" + sheetId + "' class='comment_figure' style='display:inline;'>";
	newCommentDiv += "<span class='glyphicon glyphicon-remove cl_comment_image' onClick=\"deleteCLComment(" + commentId + ", this);\" title='Delete comment'></span>";
	newCommentDiv += "</figure>";
	newCommentDiv += "</div>";
	newCommentDiv += "</div>";
	if(newComment == null){
		return newCommentDiv;
	}else{
		mainColumnId = $(column).closest("div[id^=column]").attr('id');
		var firstTextAreaVal = $("#" + mainColumnId + " #commentsdiv textarea").val();
		$("#" + mainColumnId + " #commentsdiv textarea").val('');
		$("#" + mainColumnId + " #commentsdiv").after(newCommentDiv);
		$("#" + mainColumnId + " #commentsdiv").next(".comments_text_div").find("textarea").val(firstTextAreaVal);
	}
	$("div[id^='OD']").each(function(){
		var tempId = $(this).attr('id');
		if(tempId.lastIndexOf('_') > -1){
			$("#" + tempId + " .cl_comment_image").remove();
			$("#" + tempId + " textarea").remove();
		}
	});
}

function deleteCLComment(commentId, element){
	var parentColumnId = $(element).closest("div[id^='column']").attr("id");
	if(commentId == 0){
		//$("#" + parentColumnId + " " + $(element)).closest("#div_" + commentId).fadeOut('slow');
		$(element).closest(".comments_text_div").fadeOut('slow').remove();
		//$("#" + parentColumnId + " " + commentId).fadeOut('slow');
	}else{
		$.ajax({
			type:'GET',
			data:'delete_comment_id=' + commentId,
			success:function(response){
				$("#div_" + commentId).fadeOut('slow').remove();
			}
		});
	}
}

function copyAllValues(element, source, dest){
	var columnCount = compareBothCLs(element);
    if(columnCount === false){                                // If OD and OS lens types are not same
        alert("Lens types for OD and OS are not same.");
        return;
    }
	clickBilateral(element, source, dest);
	copyOverRefractionDva(element, source, dest);
	copyOverRefractionNva(element, source, dest);
	copyCLFittings(element, source, dest);
}

function evalPopupBilateral(source, dest){
	$(".EVAL_POPUP_" + dest).find("input[type=checkbox]").each(function(){
		$(this).prop("checked", false);
	});

	$(".EVAL_POPUP_" + source).find("input[type=checkbox]").each(function(){
		if($(this).is(":checked")){
			var id = $(this).attr('id');
			id = id.replace(source, dest);
			$("#" + id).prop("checked", true);
		}
	});
	
	//var evalPopupBilateral = $(element);
	//var parentDiv = (evalPopupBilateral.closest('.EVAL_POPUP_' + source).attr('id'));
	//alert($(".EVAL_POPUP_" + source).attr("id"));
	//var sourceId = $(".EVAL_POPUP_" + source).attr("id");
	//$("#" + sourceId + )
	//alert("parent div id: " + parentDiv);
}

// Refresh CL data
/* function refreshClData(){
	// Get all the select drop downs
	$('select[id^="copyFromId"]').each(function(index, element) {
		var currentBlock = $(element).attr('id');
		var currentBlockId = currentBlock.replace("copyFromId", "");
		if(parseInt(currentBlockId)){
			// Block ID
			currentBlockId = parseInt(currentBlockId);

			// Current Sheet ID
			var clwsElem = $('input[id=clws_id'+currentBlockId+']');
			if(clwsElem && clwsElem.val()){
				var sheetID = clwsElem.val();

				// Remove previous comments block
				$('#column'+currentBlockId).find('#commentsdiv textarea').val('');
				console.log($('#column'+currentBlockId).find('#commentsdiv textarea'));
				$('#column'+currentBlockId).find('[id^="div_"].comments_text_div').each(function(id, elem){
					$(elem).remove();
				});

				// Refresh Data of current sheet block
				console.log(currentBlock, sheetID);
				
				loadCopyFromWorkSheet(currentBlock, sheetID);
			}
		}

	});

} */

</script>
</html>