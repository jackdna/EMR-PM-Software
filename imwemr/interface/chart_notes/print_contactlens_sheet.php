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

//die('Print patient contact lenses');
?><?php

include_once('../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/class.language.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/cl_functions.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/Patient.php');
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
include_once($GLOBALS['fileroot']."/library/classes/functions.smart_tags.php");
include_once($GLOBALS['fileroot']."/library/classes/Functions.php");

$printType = $_REQUEST['printType'];
$printMethod = $_REQUEST['method'];
if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"]))	{
    $form_id = $_SESSION["form_id"];
}else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
    $form_id = $_SESSION["finalize_id"];
}
$today = get_date_format(date('Y-m-d'));

//GET ALL LENS MANUFACTURER IN ARRAY
$arrLensManuf = getLensManufacturer();

$pdf_css = '<style type="text/css">
		.text_b_w{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
			background-color:#BCD5E1;
			height:15px;
		}
		.text_10b{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			background-color:#FFFFFF;
		}
		.text_10{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#FFFFFF;
		}
		.gray_bg{
			background-color:#CCCCCC;
		}
		.heading{
			border-bottom:3px groove Gainsboro; 
			vertical-align:bottom;
		}
		.red_border{
			border:1px solid #F00;
		}
		.text_b{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
			background-color:#ffffff;
			border-style:solid;
			border-color:#FFFFFF;
			border-width: 1px; 
		}
		.text_10ab{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			color:#000000;
			background-color:#ffffff;
		}
		.text_10ab_white{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			color:#000000;
			background-color:#ffffff;
		}
	</style>
<style>
.tb_heading{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684AB;
}

.text{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
.textBold{
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
}
.text_9{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
}
.text_b_w{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#CCCCCC;
}
.text_blue_w{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	color:#0000FF;
} 
.text_green_w{
	font-size:11px;
	color:#006600;
	font-family:Arial, Helvetica, sans-serif;
}
.ou{color:#9900CC;}	
</style>';

$patientId = $_SESSION['patient'];
$qryGetpatientDetail = "select *,date_format(DOB,'".get_sql_date_format()."') as pat_dob,date_format(date,'".get_sql_date_format()."') as reg_date
						from patient_data where id = '".$patientId."'";
$rsGetpatientDetail	= imw_query($qryGetpatientDetail);
$numRowGetpatientDetail	= imw_num_rows($rsGetpatientDetail);
if($numRowGetpatientDetail){
    extract(imw_fetch_array($rsGetpatientDetail));
    $patientname = $fname.' '.$lname."&nbsp;-&nbsp;".$id;
    if($street){
        $patientAddressFull = $street;
    }
    if($street2){
        $patientAddressFull .= ' '.$street2.',';
    }
    if($city){
        if(!$street2){
            $patientAddressFull .= ',';
        }
        $patientAddressFull .= ' '.$city.', '.$state.' '.$postal_code;
    }
}

///get Work sheet master Data
//die("worksheet id: " . $_GET["workSheetId"]);
if($_GET["workSheetId"]!=""){
    $GetDataQuery= "SELECT clws_id, provider_id, clGrp, clws_type, clws_trial_number, DATE_FORMAT(clws_savedatetime,'".get_sql_date_format()."') AS worksheetdate,
				DATE_FORMAT(dos, '".get_sql_date_format()."') AS dos, AverageWearTime,Solutions,Age, DisposableSchedule FROM contactlensmaster
				WHERE patient_id='".$_SESSION['patient']."' AND clws_id='".trim($_GET["workSheetId"])."'";
    //die($GetDataQuery);
    $GetDataRes = imw_query($GetDataQuery) or die(imw_error());
    $GetDataNumRow = imw_num_rows($GetDataRes);
    if($GetDataNumRow>0){
        $resRow=imw_fetch_assoc($GetDataRes);
        // @extract($resRow);
    }
}

$clws_id = $resRow['clws_id'];
$clws_type = $resRow['clws_type'];
if($resRow['clws_type'] =="Evaluation"){$clws_type = "Evaluation";}
else if($resRow['clws_type'] =="Current Trial"){$clws_type = "Trial #".$resRow['clws_trial_number'];}

$htmlData = '
	<table border="0" cellspacing="0" cellpadding="0" style="width:90%;text-align:center;" align="center">
		<tr>
			<td width="200" style="background:#4684AB;padding-bottom:2px;padding-top:4px;padding-left:2px;padding-right:2px;">CL Worksheet</td>
			<td width="200" style="background:#4684AB;padding-bottom:2px;padding-top:4px;padding-left:2px;padding-right:2px;">'.$clws_type.'</td>
			<td width="270" style="background:#4684AB;padding-bottom:2px;padding-top:4px;padding-left:2px;padding-right:2px;">'.$patientname.'</td>
			<td width="200" style="background:#4684AB;padding-bottom:2px;padding-top:4px;padding-left:2px;padding-right:2px;">DOS: '.$resRow['dos'].'</td>
			<td width="170" align="center" style="background:#4684AB;padding-bottom:2px;padding-top:2px;padding-left:2px;padding-right:2px;">Date: '.$today.'</td>
		</tr>
	</table>
';

if(($clws_type =="Evaluation" || $clws_type =="Fit" || $clws_type =="Refit") && ($resRow['AverageWearTime']!="" ||$resRow['Solutions']!="" || $resRow['Age']!="" || $resRow['DisposableSchedule']!="" ))
{
    $htmlData .= '
	<table cellpadding="0" cellspacing="0" border="0" style="margin-top:5px;">
		<tr>
			<td width="100" class="text_b_h" align="left" width="130">Evaluation / Refit</td>';
    if($resRow['AverageWearTime']!=""){
        $htmlData .= '
				<td   nowrap class="text_b_h">Average Wear Time: </td>
				<td  nowrap class="text_9" width="90">'.$resRow['AverageWearTime'].'</td>';
    }
    if($resRow['Solutions']!=""){
        $htmlData .= '
				<td nowrap class="text_b_h">Solutions: </td>
				<td nowrap class="text_9" width="90">'.$resRow['Solutions'].'</td>
				';
    }
    if($resRow['Age']!=""){
        $htmlData .= '
				<td width="32" nowrap class="text_b_h">Age: </td>
				<td width="68" nowrap class="text_9" width="90">'.$resRow['Age'].'</td>';
    }
    if($resRow['DisposableSchedule']!=""){
        $htmlData .= '
				<td nowrap class="text_b_h">Disposable Schedule: </td>
				<td nowrap class="text_9" width="90">'.$resRow['DisposableSchedule'].'</td>';
    }
    $htmlData .= '</tr>
	</table>';
}

$htmlData .= '
	<br>
	<table cellpadding="0" cellspacing="0"><tr><td width="1080" class="tb_heading">Prescription Details</td></tr></table>
	';

$scl_header = '
	  <tr class="text_b_w textBold">
		<td width="100">&nbsp;</td>
		<td width="40">&nbsp;</td>
		<td width="60">B.Curve</td>
		<td width="60">Diameter </td>
		<td width="60">Sphere</td>
		<td width="60">Cylinder</td>
		<td width="60">Axis</td>
		<td width="60">Color</td>
		<td width="60">ADD</td>
		<td width="60">DVA</td>
		<td width="110">NVA</td>
		<td width="370">Type</td>
	   </tr>';
$rgp_header = '
	  <tr class="text_b_w textBold">
		<td width="100">&nbsp;</td>
		<td width="40">&nbsp;</td>
		<td width="60">BC</td>
		<td width="60">Diameter</td>
		<td width="60">Power</td>
		<td width="100">Description</td>
		<td width="60">Color</td>
		<td width="50">Add</td>
		<td width="60">DVA</td>
		<td width="60">NVA</td>
		<td width="245">Type</td>
	   </tr>';
$crgp_header = '
	  <tr class="text_b_w textBold">
		<td width="100">&nbsp;</td>
		<td width="40">&nbsp;</td>
		<td width="40">BC</td>
		<td width="70">Diameter</td>
		<td width="55">Power</td>
		<td width="55">2&#176;/W</td>
		<td width="40">3&#176;/W</td>
		<td width="55">PC/W</td>
		<td width="90">Description</td>
		<td width="45">Color</td>
		<td width="50">Blend</td>
		<td width="40">Edge</td>
		<td width="40">Add</td>
		<td width="60">DVA</td>
		<td width="80">NVA</td>
		<td width="160">Type</td>
	   </tr>';

//START CODE TO GET WORKSHEET DETAIL
$workSheetQuery= "SELECT clw.* FROM contactlensworksheet_det clw JOIN contactlensmaster clm ON (clw.clws_id = clm.clws_id)
					WHERE clw.clws_id='".$clws_id."' ORDER BY id";
$workSheetRes = imw_query($workSheetQuery) or die(imw_error());

$sclprintHeader = 0;	$rgpprintHeader = 0;	$crgpprintHeader = 0;	$clEye_flag = '';	$scl_flag = false; $rgp_flag = false;

if($workSheetRes && imw_num_rows($workSheetRes)>0){
    while($workSheetrs = imw_fetch_array($workSheetRes)){
        $clType=$workSheetrs['clType']=='scl'?'SCL':($workSheetrs['clType']=='rgp'?'RGP':($workSheetrs['clType']=='cust_rgp'?'Custom RGP':''));
        $clEye = $workSheetrs['clEye'];
        $cleyeClass = $clEye=='OD' ? 'text_blue_w' : ($clEye=='OS' ? 'text_green_w' : '');
        if($clEye_flag != $clEye){
            $clEye_flag = $clEye;
            $sclprintHeader = 1;			$rgpprintHeader = 1;			$crgpprintHeader = 1;
        }
        if($workSheetrs['clType']=='scl'){
            $pdf_page .= '
			<table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:5px;">';
            if($sclprintHeader == 1){
                $pdf_page .= $scl_header;
                $sclprintHeader = 0;
            }
            $scl_flag = true;
            $pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">'.$clType.'</td>
				<td width="40" class="'.$cleyeClass.'" valign="top">'.$clEye.'</td>
				<td width="60" valign="top">'.$workSheetrs['SclBcurve'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclDiameter'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['Sclsphere'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclCylinder'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['Sclaxis'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclColor'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclAdd'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['SclDva'.$clEye].'</td>
				<td width="110" valign="top">'.$workSheetrs['SclNva'.$clEye].'</td>
				<td width="370" valign="top">'.$arrLensManuf[$workSheetrs['SclType'.$clEye.'_ID']]['det'].'</td>
		   </tr>
		</table>';
        }//end of scl
        else if($workSheetrs['clType']=='rgp' || $workSheetrs['clType']=='rgp_soft' || $workSheetrs['clType']=='rgp_hard'){
			if($workSheetrs['clType']=='rgp'){
				$clType = "RGP";
			}else if($workSheetrs['clType']=='rgp_soft'){
				$clType = "RGP Soft";
			}else if($workSheetrs['clType']=='rgp_hard'){
				$clType = "RGP Hard";
			}
            $pdf_page .= '
			<table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:5px;">';
            if($rgpprintHeader == 1){
                $pdf_page .= $rgp_header;
                $rgpprintHeader = 0;
            }
            $rgp_flag = true;
            $pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">'.$clType.'</td>
				<td width="40" class="'.$cleyeClass.'" valign="top">'.$clEye.'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpBC'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpDiameter'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpPower'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpOZ'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpColor'.$clEye].'</td>
				<td width="50" valign="top">'.$workSheetrs['RgpAdd'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpDva'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpNva'.$clEye].'</td>
				<td width="245" valign="top">'.$arrLensManuf[$workSheetrs['RgpType'.$clEye.'_ID']]['det'].'</td>
		   </tr>
		</table>';
        }//end of rgp
        else if($workSheetrs['clType']=='cust_rgp'){
            $pdf_page .= '
			<table cellpadding="0" cellspacing="0" class="text_9" style="margin-top:5px;">';
            if($crgpprintHeader == 1){
                $pdf_page .= $crgp_header;
                $crgpprintHeader = 0;
            }
            $rgp_flag = true;
            $pdf_page .= '
			<tr>
				<td width="100" class="textBold" valign="top">'.$clType.'</td>
				<td width="40" class="'.$cleyeClass.'" valign="top">'.$clEye.'</td>
				<td width="40" valign="top">'.$workSheetrs['RgpCustomBC'.$clEye].'</td>
				<td width="25" valign="top">'.$workSheetrs['RgpCustomDiameter'.$clEye].'</td>
				<td width="35" valign="top">'.$workSheetrs['RgpCustomPower'.$clEye].'</td>
				<td width="35" valign="top">'.$workSheetrs['RgpCustom2degree'.$clEye].'</td>
				<td width="40" valign="top">'.$workSheetrs['RgpCustom3degree'.$clEye].'</td>
				<td width="55" valign="top">'.$workSheetrs['RgpCustomPCW'.$clEye].'</td>
				<td width="25" valign="top">'.$workSheetrs['RgpCustomOZ'.$clEye].'</td>
				<td width="45" valign="top">'.$workSheetrs['RgpCustomColor'.$clEye].'</td>
				<td width="50" valign="top">'.$workSheetrs['RgpCustomBlend'.$clEye].'</td>
				<td width="40" valign="top">'.$workSheetrs['RgpCustomEdge'.$clEye].'</td>
				<td width="40" valign="top">'.$workSheetrs['RgpCustomAdd'.$clEye].'</td>
				<td width="60" valign="top">'.$workSheetrs['RgpCustomDva'.$clEye].'</td>
				<td width="80" valign="top">'.$workSheetrs['RgpCustomNva'.$clEye].'</td>
				<td width="160" valign="top">'.$arrLensManuf[$workSheetrs['RgpCustomType'.$clEye.'_ID']]['det'].'</td>
		   </tr>
		</table>';
        }//end of custom rgp
    }
    $clEye_flag = $clEye;
    if($rgp_flag == true) { $scl_flag=false; }
}
$htmlData .= $pdf_page;

if($scl_flag==true || $rgp_flag==true){
    $scl_ovr_query = "SELECT * FROM contactlens_evaluations WHERE clws_id = '".$clws_id."' ORDER BY id";
    $scl_ovr_result = imw_query($scl_ovr_query);
    if($scl_ovr_result && imw_num_rows($scl_ovr_result)>0){
        $scl_ovr_rs = imw_fetch_array($scl_ovr_result);
        if($scl_flag==true){
            $pdf_data_scl_overef .= '
				<table id="scl_table1" cellpadding="0" cellspacing="2" style="margin-top:10px;">
				<tr>
					<td class="text_b_w" colspan="9">Over&nbsp;Refraction</td>
					<td class="text_b_w" colspan="5" nowrap="nowrap">CL Fittings-SLC</td>
				</tr>
				<tr >
					<td width="65" class="text_b_w">&nbsp;</td>
					<td width="70"  class="text_b_w">Sphere</td>
					<td width="75" class="text_b_w">Cylinder</td>
					<td width="55" class="text_b_w">Axis</td>
					<td width="65" nowrap class="text_b_w">DVA</td>
					<td width="70"  class="text_b_w">Sphere</td>
					<td width="75" class="text_b_w">Cylinder</td>
					<td width="55" class="text_b_w">Axis</td>
					<td width="105" nowrap class="text_b_w">NVA</td>
					<td width="120"  class="text_b_w">Comfort</td>
					<td width="75" class="text_b_w">Movement</td>
					<td width="90" class="text_b_w">Position</td>';
            if($resRow['clws_type'] =="Evaluation" || $resRow['clws_type'] =="Fit" || $resRow['clws_type'] =="Refit"){
                $pdf_data_scl_overef .= '
					<td width="80" class="text_b_w">Condition</td>';
            }
            $pdf_data_scl_overef .= '
				</tr>';
            
            if($resRow['clGrp']=="OD" || $resRow['clGrp']=="OU"){
                $pdf_data_scl_overef .= '
				<tr>
					<td width="18" class="text_blue_w">OD</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationSphereOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCylinderOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationAxisOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationDVAOD'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationSphereNVAOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCylinderNVAOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationAxisNVAOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationNVAOD'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationComfortOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationMovementOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationPositionOD'].'</td>';
                if($resRow['clws_type'] =="Evaluation" || $resRow['clws_type'] =="Fit" || $resRow['clws_type'] =="Refit"){
                    $pdf_data_scl_overef .= '
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCondtionOD'].'</td>';
                }
                $pdf_data_scl_overef .= '
				</tr>';
            }
            
            if($resRow['clGrp']=="OS" || $resRow['clGrp']=="OU"){
                $pdf_data_scl_overef .= '
				<tr>
					<td width="18" class="text_green_w">OS</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationSphereOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCylinderOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationAxisOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationDVAOS'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationSphereNVAOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCylinderNVAOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationAxisNVAOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationNVAOS'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationComfortOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationMovementOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationPositionOS'].'</td>';
                if($resRow['clws_type'] =="Evaluation" || $resRow['clws_type'] =="Fit" || $resRow['clws_type'] =="Refit"){
                    $pdf_data_scl_overef .= '
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCondtionOS'].'</td>';
                }
                $pdf_data_scl_overef .= '
				</tr>';
            }
            
            if($resRow['clGrp']=="OU"){
                $pdf_data_scl_overef .= '
				<tr>
					<td width="18" class="text_magenta_w">OU</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationSphereOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCylinderOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationAxisOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationDVAOU'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationSphereNVAOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCylinderNVAOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationAxisNVAOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationNVAOU'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationComfortOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationMovementOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationPositionOU'].'</td>';
                if($resRow['clws_type'] =="Evaluation" || $resRow['clws_type'] =="Fit" || $resRow['clws_type'] =="Refit"){
                    $pdf_data_scl_overef .= '
					<td class="text_9">'.$scl_ovr_rs['CLSLCEvaluationCondtionOU'].'</td>';
                }
                $pdf_data_scl_overef .= '
				</tr>';
            }
            
            if($scl_ovr_rs['CLSLCEvaluationCommentsOD']!="" &&($resRow['clGrp']=="OD" || $resRow['clGrp']=="OU")){
                $pdf_data_scl_overef .= '
				<tr>
					<td class="text_blue_w">OD</td>
					<td colspan="13" class="text_9"><b>Comments:</b> '.$scl_ovr_rs['CLSLCEvaluationCommentsOD'].'</td>
				</tr>';
            }
            if($scl_ovr_rs['CLSLCEvaluationCommentsOS']!="" && ($resRow['clGrp']=="OS" || $resRow['clGrp']=="OU")){
                $pdf_data_scl_overef .= '
				<tr>
					<td class="text_green_w">OS</td>
					<td colspan="13" class="text_9"><b>Comments:</b> '.$scl_ovr_rs['CLSLCEvaluationCommentsOS'].'</td>
				</tr>';
            }
            if($scl_ovr_rs['CLSLCEvaluationCommentsOU']!="" && ($resRow['clGrp']=="OU")){
                $pdf_data_scl_overef .= '
				<tr>
					<td class="text_magenta_w">OU</td>
					<td colspan="13" class="text_9"><b>Comments:</b> '.$scl_ovr_rs['CLSLCEvaluationCommentsOU'].'</td>
				</tr>';
            }
            $pdf_data_scl_overef .= '
			</table>';
        }//if(sclflag==true)
        
        if($rgp_flag==true){
            $pdf_data_rgp_overef .= '
				<table id="scl_table1" cellpadding="0" cellspacing="2" style="margin-top:10px;">
				<tr>
					<td class="text_b_w" colspan="7">CL Evaluation/Fittings '."-".' RGP (with or without Custom)</td>
					<td class="text_b_w" colspan="6" nowrap="nowrap">Over Refraction</td>
				</tr>
				<tr >
					<td width="65" class="text_b_w">&nbsp;</td>
					<td width="75"  class="text_b_w">Comfort</td>
					<td width="80" class="text_b_w">Movement</td>
					<td width="115" class="text_b_w">Pos. Before Blink</td>
					<td width="115" nowrap class="text_b_w">Pos. After Blink</td>
					<td width="115"  class="text_b_w">Fluorescein Pattern</td>
					<td width="90" class="text_b_w">Inverted Lids</td>
					<td width="70" class="text_b_w">Sphere</td>
					<td width="70" nowrap class="text_b_w">Cylinder</td>
					<td width="50"  class="text_b_w">Axis</td>
					<td width="70" class="text_b_w">DVA</td>
					<td width="90" class="text_b_w">NVA</td>
				</tr>';
            
            if($resRow['clGrp']=="OD" || $resRow['clGrp']=="OU"){
                $pdf_data_rgp_overef .= '
				<tr>
					<td width="18" class="text_blue_w">OD</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationComfortOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationMovementOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationPosBeforeOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationPosAfterOD'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationFluoresceinPatternOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationInvertedOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationSphereOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationCylinderOD'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationAxisOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationDVAOD'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationNVAOD'].'</td>
				</tr>';
            }
            
            if($resRow['clGrp']=="OS" || $resRow['clGrp']=="OU"){
                $pdf_data_rgp_overef .= '
				<tr>
					<td width="18" class="text_green_w">OS</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationComfortOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationMovementOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationPosBeforeOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationPosAfterOS'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationFluoresceinPatternOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationInvertedOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationSphereOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationCylinderOS'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationAxisOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationDVAOS'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationNVAOS'].'</td>
				</tr>';
            }
            
            if($resRow['clGrp']=="OU"){
                $pdf_data_rgp_overef .= '
				<tr>
					<td width="18" class="text_magenta_w">OU</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationComfortOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationMovementOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationPosBeforeOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationPosAfterOU'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationFluoresceinPatternOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationInvertedOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationSphereOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationCylinderOU'].'</td>
					        
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationAxisOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationDVAOU'].'</td>
					<td class="text_9">'.$scl_ovr_rs['CLRGPEvaluationNVAOU'].'</td>
				</tr>';
            }
            
            if($scl_ovr_rs['CLRGPEvaluationCommentsOD']!="" &&($resRow['clGrp']=="OD" || $resRow['clGrp']=="OU")){
                $pdf_data_rgp_overef .= '
				<tr>
					<td class="text_blue_w">OD</td>
					<td colspan="11" class="text_9"><b>Comments:</b> '.$scl_ovr_rs['CLRGPEvaluationCommentsOD'].'</td>
				</tr>';
            }
            if($scl_ovr_rs['CLRGPEvaluationCommentsOS']!="" && ($resRow['clGrp']=="OS" || $resRow['clGrp']=="OU")){
                $pdf_data_rgp_overef .= '
				<tr>
					<td class="text_green_w">OS</td>
					<td colspan="11" class="text_9"><b>Comments:</b> '.$scl_ovr_rs['CLRGPEvaluationCommentsOS'].'</td>
				</tr>';
            }
            if($scl_ovr_rs['CLRGPEvaluationCommentsOU']!="" && ($resRow['clGrp']=="OU")){
                $pdf_data_rgp_overef .= '
				<tr>
					<td class="text_magenta_w">OU</td>
					<td colspan="11" class="text_9"><b>Comments:</b> '.$scl_ovr_rs['CLRGPEvaluationCommentsOU'].'</td>
				</tr>';
            }
            $pdf_data_rgp_overef .= '
			</table>';
        }//if(sclflag==true)
        
    }//if result found
}//if(scl or rgp flag is true).

$htmlData .= $pdf_data_scl_overef.$pdf_data_rgp_overef;

//Get CL Teach Saved data//
if($_SESSION['patient'] && $_REQUEST["workSheetId"]!=""){
    $GetDataQuery= "SELECT * FROM clteach WHERE patient_id='".$_SESSION['patient']."' and workSheetID='".$_REQUEST["workSheetId"]."'";
    //echo $GetDataQuery;die;
    $GetDataRes = imw_query($GetDataQuery) or die(imw_error());
    $GetDataNumRow = imw_num_rows($GetDataRes);
    if($GetDataNumRow>0){
        $resRow=imw_fetch_assoc($GetDataRes);
        @extract($resRow);
        if($schedule_cltech_chk=="Yes"){$schedule_cltech_chk = "Schedule CL Teach";}
        if($clwearingtime_dw_cltech_chk=="dw"){$clwearingtime_dw_cltech_chk = "DW (No Overnight)";}
        if($clwearingtime_fw_cltech_chk=="fw"){$clwearingtime_fw_cltech_chk = "FW (Occasional Overnight)";}
        if($clwearingtime_ew_cltech_chk=="ew"){$clwearingtime_ew_cltech_chk = "EW (Overnight)";}
        if($clwearingtime_fwparttime_cltech_chk=="parttime"){$clwearingtime_fwparttime_cltech_chk="Part Time ".$clwearingtime_hrs_cltech_txt."hrs.";}
        if($contactsfortech_clbin_cltech_chk=="ClBin"){$contactsfortech_clbin_cltech_chk = "Cl Bin";}
        if($contactsfortech_trialdisplays_cltech_chk=="trial_displays"){$contactsfortech_trialdisplays_cltech_chk = "Trial Displays";}
        if($contactsfortech_other_cltech_chk=="Other_contactsfortec"){$contactsfortech_other_cltech_chk="Other:".$contactsfortech_other_cltech_txt;}
        $wearingtime_select = $anotherCLTech_cltech_txt."&nbsp;".$wearingtime_select;
        $provider_name = getUserFirstName($provider_id, 2);
        
        //echo "provider name: " . $provider_name;exit;
        
       // $provider_name = getPersonnal3($provider_id);
        
        
        
        
        $technician_name = getUserFirstName($technician_id,2);
        if($wearingtime_cltech_sel!="other"){$wearingtime_cltech_sel .= 'hrs.';}
        
        $pdf_data_cl_tech = '
		<table cellpadding="0" cellspacing="2" style="margin-top:10px;">
		<tr>
			<td align="left" width="265" class="text_b_w">CL Teach</td>
			<td class="text_b_w" width="265">'.$provider_name.'</td>
			<td width="265" class="text_b_w">'.$schedule_cltech_chk.'</td>
			<td width="260" class="text_b_w" align="right">Date: '.date("m/d/y").'</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="2" style="margin-top:-2px;">
		<tr>
			<td width="365" align="left" class="text_b_w">Doctor Recommends</td>
			<td width="365" align="left" class="text_b_w">CL Wearing Time</td>
			<td width="330" align="left" class="text_b_w">Contacts for Tech </td>
		</tr>
		<tr>
			<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%" align="left">
				<tr>
			        
					<td width="75" class="text_9" align="left"><b>Multipurpose:</b></td>
					<td width="162" align="left" class="text_9">'.$multipurpose_cltech_chk.'</td>
				</tr>
				<tr>
					<td width="75" class="text_9" align="left"><b>Peroxide:</b></td>
					<td width="162" align="left" class="text_9">'.$peroxide_cltech_chk.'</td>
					</tr>
				<tr>
					<td width="75" class="text_9" align="left"><b>Enzyme:</b></td>
					<td width="162" align="left" class="text_9">'.$enzyme_cltech_chk.'</td>
				</tr>
				</table>
			</td>
			<td>
				<table cellpadding="0" cellspacing="0" width="99%" align="left">
					<tr>
					<td align="left"  class="text_9">&nbsp;</td>
					<td  align="left" class="text_9">'.$clwearingtime_dw_cltech_chk.'</td>
					</tr>
				<tr>
					<td width="10" align="left"  class="text_9">&nbsp;</td>
					<td width="144" align="left" class="text_9">'.$clwearingtime_fw_cltech_chk.'</td>
				</tr>
				<tr>
					<td width="10" align="left"  class="text_9">&nbsp;</td>
					<td width="144" align="left" class="text_9">'.$clwearingtime_ew_cltech_chk.'</td>
				</tr>
				<tr>
					        
					<td width="10" align="left"  class="text_9">&nbsp;</td>
					<td width="144" align="left" class="text_9">'.$clwearingtime_fwparttime_cltech_chk.'</td>
				</tr>
					        
				</table>
			</td>
			<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%" align="left">
					<tr>
						<td width="10" align="left"  class="text_9">&nbsp;  </td>
						<td width="181" align="left" class="text_9">'.$contactsfortech_clbin_cltech_chk.'</td>
					</tr>
					<tr>
						<td width="10" align="left"  class="text_9">&nbsp;</td>
						<td width="181" align="left" class="text_9">'.$contactsfortech_trialdisplays_cltech_chk.'</td>
					</tr>
					<tr>
					<td width="10" align="left" class="text_9"></td>
						<td width="181" align="left" class="text_9">'.$contactsfortech_other_cltech_chk.'</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="622" align="left" class="text_9">Pt. instructed on I+Rtype :'.$contactsfortech_Pt_instructed_txt.' x each eye w/o incident</td>
				</tr>
				<tr>
				<td align="left" class="text_9"><b>Comments:&nbsp;</b>'.$contactsfortech_Pt_instructed_txtarea.'</td>
				</tr>
			</table>
			</td>
		</tr>
		</table>
					        
		<table cellpadding=0 cellspacing=0>
		<tr>
			<td width="100%" align="left" class="text_9"><b>Technician: '.$technician_name.'</b></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="2">
				<tr>
					<td width="168" align="left" class="text_9" nowrap><b>Pt. to Increase Wearing Time:</b></td>
					<td width="205" align="left" class="text_9">'.$wearingtime_cltech_sel.'</td>
					<td width="150" class="text_9" ><b>Date:</b>'.date("m-d-y").'</td>
				</tr>
				<tr>
					<td align="left" class="text_9" nowrap><b>Pt. has followup to see Doctor in:</b></td>
					<td align="left" class="text_9">'.$followup_cltech_txt.'&nbsp;Weeks</td>
					<td class="text_9" nowrap><b>Pt. needs another CL Teach:</b> '.$wearingtime_select.'</td>
				</tr>
			</table>
			</td>
		</tr>
		</table>';
	}
}

$pdf_cl_comments = "";
$clCommentArray = array();
$clCommentsQuery = "select clm.dos as date_of_service, clc.comment as comment_desc from cl_comments clc left join contactlensmaster clm on clc.cl_sheet_id=clm.clws_id where clm.patient_id='".$_SESSION['patient']."' and delete_status='0' and clm.clws_id = ".$clws_id." order by date_of_service desc";
//die($clCommentsQuery);
$clCommentsResult = imw_query($clCommentsQuery) or die(imw_error()." - ".$clCommentsQuery);
while($clRow = imw_fetch_assoc($clCommentsResult)){
	$commentDesc = $clRow['comment_desc'];
	$dateOfService = $clRow['date_of_service'];
	$clCommentArray[$dateOfService][] = $commentDesc;
}
$pdf_cl_comments .= '<table cellpadding="0" cellspacing="0" width="100%"><tr><td class="text_b_w" style="width:100%;">Contact lens comments</td></tr></table>';
$pdf_cl_comments .= '<table cellpadding="0" cellspacing="0" width="100%" border="0">';
$pdf_cl_comments .= '<tr><td style="width:30%;"><b>Date of service</b></td><td style="width:70%;"><b>Comment</b></td></tr>';
foreach($clCommentArray as $dos => $comments){
	foreach($comments as $comment){
		$pdf_cl_comments .= '<tr><td style="width:30%;">'.$dos.'</td><td style="width:70%;">'.$comment.'</td></tr>';
	}
	// foreach($clCommentArray as $dos2 => $comments2){
	// 	$pdf_cl_comments .= '<tr><td>'.$dos2.'</td><td>'.$comments2.'</td></tr>';
	// }
	
}
$pdf_cl_comments .= '</table>';
$htmlData .= $pdf_cl_comments;
//echo $pdf_cl_comments;die;
$file_path = write_html($htmlData);
if(isset($file_path)){ ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
 top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
 top.html_to_pdf('<?php echo $file_path; ?>','l','',true,false);
</script>
<?php } ?>