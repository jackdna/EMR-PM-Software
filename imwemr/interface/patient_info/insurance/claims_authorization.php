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
/*	
File: claims_authorization.php
Purpose: Authorization claims
Access Type: Direct 
*/
require_once("../globals.php");
require_once("../billing/billing_globals.php");
require_once("../billing/electronic_billing_functions.php");
$objEBilling = new ElectronicBilling();

$soapUser = 'MWI_ER8353B9';
$soapPass = 's7uUpV4F';
$overwriteTID = '17186547';

//--GETTING GROUP DETIALS----
$arr_groups		= $objEBilling->get_groups_detail();

//--GETTING HQ FACILITY DETAILS
$arr_HQFac		= $objEBilling->get_facility_detail();
$default_group	= $arr_HQFac['default_group'];
/*
$soapUser 		= $arr_groups[$default_group]['user_id'];
$soapPass 		= $arr_groups[$default_group]['user_pwd'];
*/

//--GETTING USER ARRAY--
$arr_usres		= $objEBilling->getUserArr();


$patient_id				= $_SESSION['patient'];
$patient_id				= (isset($_POST['patient']) && trim($_POST['patient'])!='') ? trim($_POST['patient']) : $patient_id;

$patient_details		= $objEBilling->get_patient_details(array('Patients'=>$patient_id));
$patientDetails			= $patient_details[$patient_id];
$patientName 			= core_name_format($patientDetails['lname'], $patientDetails['fname'], $patientDetails['mname']).' - '.$patient_id;

//PATIENT'S PRIMARY PHY
$ptPriPhy				= $patientDetails['providerID'];
$ptPriPhy				= (isset($_POST['ptPriPhy']) && trim($_POST['ptPriPhy'])!='') ? trim($_POST['ptPriPhy']) : $ptPriPhy;

//PATIENT'S GROUP (if <empty>, HQ Facility Group will be used)
$ptGroupId				= (isset($_POST['ptGroupId']) && trim($_POST['ptGroupId'])!='') ? trim($_POST['ptGroupId']) : $default_group;

//PATIENT'S PRIMARY PAYER ID (required; need to post/get)
$ptPriPayer				= (isset($_POST['ptPriPayer']) && trim($_POST['ptPriPayer'])!='') ? trim($_POST['ptPriPayer']) : '';//if empty, overwritten from ptInsDataId
$ptInsDataId			= (isset($_REQUEST['ptInsDataId']) && trim($_REQUEST['ptInsDataId'])!='') ? trim($_REQUEST['ptInsDataId']) : '';

//CURRENT DIAGNOSIS ONSET DATE
//$onsetDate				= (isset($_POST['diag_date']) && trim($_POST['diag_date'])!='') ? trim($_POST['diag_date']) : date('m-d-Y');

$onSetDateFromat        =   date(''.phpDateFormat().'', strtotime($_POST['diag_date']));

$onsetDate				= (isset($_POST['diag_date']) && trim($_POST['diag_date'])!='') ? $onSetDateFromat : date(''.phpDateFormat().'');

//GETTING POSTED TEMPLATE ID
$template_id			= (isset($_POST['preauth_template_id']) && trim($_POST['preauth_template_id'])!='') ? intval($_POST['preauth_template_id']) : 0;

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Claim Authorization Request/Response</title>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src=""></script>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/interface/themes/default/css_core.php" type="text/css">
<style type="text/css">
.resp_summary, .resp_details{line-height:1.2;}
.dff_button_sm{width:80px;}
</style>
<script type="text/javascript">
$(document).ready(function() {
	
	var date_global_format = window.opener.top.jquery_date_format;

    $( ".date-pick" ).datepicker({changeMonth: true,changeYear: true,dateFormat:date_global_format});
   //	$(".date-pick").val('<?php echo $onsetDate;?>');
});
function working(){
	$('#btn_check').val('Checking Status....').attr('disabled','disabled');
	$('#plz_wait').show();
}
function valid_preauth(){
	g   = $('#ptGroupId').val();
	ph	= $('#ptPriPhy').val();
	dd	= $('#diag_date').val();
	tm	= $('#preauth_template_id').val();
	if(g==''){fancyAlert('Please select Group.'); return false;}
	else if(ph==''){fancyAlert('Please select Rendering Provider.'); return false;}
	else if(dd==''){fancyAlert('Please enter Diagnosis Onset Date.'); return false;}
	else if(tm==''){fancyAlert('Please select Pre-Auth. Temlate.'); return false;}
	else{
		working();
		document.pa.submit();
	}
}
</script>
</head>
<body scroll="no">
<div id="plz_wait" style="position:absolute;top:150px; left:250px; width:auto;" class="hide white border2 padd10">Please wait, contacting clearing house....</div>
<div class="section_header alignCenter text_big" style="height:25px; padding:5px 0px 0px 5px;"> 
    <span class="fr" style="margin-right:20px;"><?php echo $patientName; ?></span>
	<span class="fl">Claim Pre-Authorization Request</span>
</div>
<form style="margin:0px;" method="post" name="pa">
<input type="hidden" id="patient" name="patient" value="<?php echo xss_rem($patient_id);?>">
<input type="hidden" id="ptPriPayer" name="ptPriPayer" value="<?php echo xss_rem($ptPriPayer);?>">
<input type="hidden" id="ptInsDataId" name="ptInsDataId" value="<?php echo xss_rem($ptInsDataId);?>">
<input type="hidden" id="do_check" name="do_check" value="yes">
<div class="section" id="div_add_new_notes">
	<table class="table_collapse m5">
    	<tr>
        	<td><select name="ptGroupId" id="ptGroupId" style="width:200px;">
            		<option value="">-SELECT-</option>
					<?php foreach($arr_groups as $val){
						if($default_group==$val['gro_id']){$selected_grp = ' selected';}else{$selected_grp = '';}
						?>
                    <option value="<?php echo $val['gro_id'];?>"<?php echo $selected_grp;?>><?php echo $val['name'];?></option>
                    <?php }?>
                </select>
                <div class="label">Group</div>
			</td>
        	<td><select name="ptPriPhy" id="ptPriPhy" style="width:150px;">
            		<option value="">-SELECT-</option>
					<?php foreach($arr_usres as $uid=>$val){
						if($ptPriPhy==$uid){$selected_pripro = ' selected';}else{$selected_pripro = '';}
						?>
                    <option value="<?php echo $uid;?>"<?php echo $selected_pripro;?>><?php echo $val['long'];?></option>
                    <?php }?>
                </select>
                <div class="label">Rendering Provider</div>
			</td>
            <td>
            	<input type="text" style="width:110px;" class="date-pick" name="diag_date" id="diag_date" value="<?php echo $onsetDate;?>">
                <div class="label">Diagnosis Onset Date</div>
            </td>
            <td><select name="preauth_template_id" id="preauth_template_id" style="width:200px;">
            		<option value="">-SELECT-</option>
                    <?php $q_template = "SELECT id,template_name FROM pre_auth_templates WHERE del_status='0'";
                    $res_template = mysql_query($q_template);
                    while($rs_template = mysql_fetch_assoc($res_template)){
						if($template_id==$rs_template['id']){$selected_template = ' selected';}else{$selected_template = '';}
                        echo '<option value="'.$rs_template['id'].'"'.$selected_template.'>'.$rs_template['template_name'].'</option>';
                    }
                    ?>
                </select>
                <div class="label">Pre Auth Templates</div>
            </td>
            <td class="valignTop">
            	<input type="submit" class="dff_button" value="Check Pre-Auth." id="btn_check" onClick="valid_preauth();">
            </td>
		</tr>
	</table>
    </div>
</form>
<div class="m5 subsection padd5" style="height:365px; overflow:auto; overflow-x:hidden;"><?php
    if(!empty($_POST['do_check'])==true){
        $template_q				= "SELECT medical_type, dx_codes FROM pre_auth_templates WHERE id = '$template_id' LIMIT 0,1";
        $template_res			= mysql_query($template_q);
        $template_rs			= mysql_fetch_assoc($template_res);
        $db_service_type		= trim($template_rs['medical_type']) != '' ? trim($template_rs['medical_type']) : 1;
		if(strpos($db_service_type,' - ')!= -1){
			$db_serv_temp_arr	= explode(' - ',$db_service_type);
			$db_service_type	= trim($db_serv_temp_arr[0]);
		}
        $db_diagnosis			= $template_rs['dx_codes'];
        
        //DIAGNOSIS ARRAY. (from posted values, or exploded from template record)
        $ptDiagnosis_arr		= array(); //array('366.53','375.15','362.51','367.4');
        if($db_diagnosis != '')	{$ptDiagnosis_arr = unserialize(html_entity_decode($db_diagnosis));}
        $ptDiagnosis_arr		= $objEBilling->remove_empty($ptDiagnosis_arr);
		$ptDiagnosis_arr		= $objEBilling->set_int_keys($ptDiagnosis_arr);

	    //Line Item (SV1, SV2) details (from posted values, or exploded from template record)
        $cpt_q					= "SELECT * FROM pre_auth_templates_details WHERE pre_auth_id = '$template_id'";
        $cpt_res				= mysql_query($cpt_q);
        $LineItem				= array();
        $LineItemCnt			= 1;
        if($cpt_res && mysql_num_rows($cpt_res) > 0){
            while($cpt_rs = mysql_fetch_assoc($cpt_res)){
                $LineItem[$LineItemCnt]['cpt']		= $cpt_rs['procedure_name'];
                $LineItem[$LineItemCnt]['rev']		= $cpt_rs['proc_code'];
                $LineItem[$LineItemCnt]['mod']		= $cpt_rs['mod1'];
                $LineItem[$LineItemCnt]['unit']		= $cpt_rs['unit'];
                $LineItem[$LineItemCnt]['amount']	= $cpt_rs['charges'];
                $LineItem[$LineItemCnt]['dx']		= $cpt_rs['diagnosis'];
                $LineItemCnt++;
            }
        }
        
        //ASSIGNING DEFAULT VALUES FOR FUNCTION TO CREATE EDI. 
        $objEBilling->patientId				= $patient_id;
        $objEBilling->groupId				= $ptGroupId;
        $objEBilling->priPhysician			= $ptPriPhy;
        $objEBilling->priPayer				= $ptPriPayer;//this will be over written by INs.Data ID payer.
        $objEBilling->InsDataId				= $ptInsDataId; 
        $objEBilling->reviewServiceType		= $db_service_type;//1 //demo value, may code from template.
        $objEBilling->onSetDate				= getDateFormatDB($onsetDate);
        //$objEBilling->onSetDate				= $objEBilling->date_for_db($onsetDate);
        $objEBilling->ptDiagArr				= $ptDiagnosis_arr;
        $objEBilling->LineItemArr			= $LineItem;
		
        
        $now		= date('Y-m-d H:i:s');
        $EDI278Data = $objEBilling->make278EDI();
        //pre($EDI278Data); die('<br>----end----');
        //$EDI278Data['error'] 	= '';
        //$EDI278Data['response']	= 'ISA*00*          *00*          *ZZ*17186547       *ZZ*EMDEON         *160414*0129*^*00501*000000044*0*P*:~GS*HI*17186547*EMDEON*20160414*0129*44*X*005010X217~ST*278*0044*005010X217~BHT*0007*13*4400044*20160414*0129*RU~HL*1**20*1~NM1*X3*2*COVENTRY HEALTHCARE*****PI*00250~HL*2*1*21*1~NM1*1P*1*WNOROWSKI*BRIAN****XX*1780669101~HL*3*2*22*1~NM1*IL*1*STINSON*DANIEL****MI*80122494903~DMG*D8*19890514*M~HL*4*3*EV*0~UM*IN*I*1*11:B~DTP*431*D8*20160414~HI*ABK:E11359*ABF:E10329*ABF:H4322*ABF:E11321~HSD*VS*1~NM1*SJ*1*WNOROWSKI*BRIAN****XX*1780669101~SE*16*0044~GE*1*44~IEA*1*000000044~';
        
        if($EDI278Data['error']==''){
            //TEST
            $soapClient = new SoapClient("../../library/wsdl/changehealthcare_test.wsdl");
            $param = array("sUserID" => $soapUser,"sPassword" => $soapPass);
            
            //PRODUCTION
         //   $soapClient = new SoapClient("../../library/wsdl/changehealthcare_production.wsdl");
       //     $param = array("sUserID" => $soapUser,"sPassword" => $soapPass);
            
            //CHECKING AUTHORIZATION
            $authResult = $soapClient->Authenticate($param);
           // pre($authResult);
            
            $param = array("sUserID" => $soapUser,
                            "sPassword" => $soapPass,
                            "sMessageType" => "X12",
                            "sEncodedRequest" => base64_encode($EDI278Data['response'])
                          );
           //pre($param,1);
            $sendRq = $soapClient->SendRequest($param);
           // pre($sendRq);
        
            $arrSendRq = get_object_vars($sendRq);
            $arrSendRequestResult = get_object_vars($arrSendRq["SendRequestResult"]);
           // pre($arrSendRequestResult);		
            $ErrorCode =  $arrSendRequestResult["ErrorCode"];
            if(intval($ErrorCode)>0 && is_numeric($ErrorCode)){
                $errMsg[] = $ErrorCode.': '.$arrSendRequestResult["Response"];
            }else{
                $ErrorCode='';
                $now2			= date('Y-m-d H:i:s');
                $edi278Response	= base64_decode($arrSendRequestResult["Response"]);
				//echo $edi278Response;
                $response		= $objEBilling->read278EDI($edi278Response);
                $responsetext	= $objEBilling->responseArray2Text($response);
                
                $insert_q = "INSERT INTO claim_pre_auth 
                        SET patient_id 			= '$patient_id', 
                        ins_data_id				= '$ptInsDataId',
                        ins_comp_id				= '$ptPriPayer',
                        provider_id				= '$ptPriPhy',
                        group_id				= '$ptGroupId',
                        template_id				= '".$template_id."', 
                        pt_sub_rel				= '',
                        diagnosis				= '".implode(',',$ptDiagnosis_arr)."',
                        procedures				= '',
                        request_datetime		= '".$now."',
                        request_data			= '".addslashes($EDI278Data['response'])."',
                        request_by				= '".$_SESSION['authId']."',
                        request_from			= '2',
                        response_data			= '".addslashes($edi278Response)."',
                        response_datetime		= '".$now2."',
                        response_status			= '".addslashes($responsetext)."',
                        response_details		= '".addslashes(htmlentities(serialize($response)))."',
                        delete_status			= '0',
                        authorization_number 	= ''
                        ";
                $insert_res = mysql_query($insert_q);
                //echo '<hr>'.$insert_q.'<hr>';
                if(!$insert_res){echo mysql_error();$errMsg[] = 'Unable to save response.';}
            }
			
			if(count($errMsg)>0){$erMsg = implode('<li>', $errMsg); echo '<div class="warning"><b>Error: </b>'. $erMsg.'</div><br><br>';}
        }else{
            echo '<div class="warning"><b>Error: </b>'. $EDI278Data['error'].'</div><br><br>';
        }
    }


    $q = "SELECT cpa.id as cpa_id, cpa.patient_id, DATE_FORMAT(cpa.request_datetime,'".getSqlDateFormat()." %H:%i:%s %p') as request_datetime, cpa.request_by, cpa.request_from, cpa.request_data, cpa.response_data, cpa.response_details, 
                 pat.template_name 
                 FROM claim_pre_auth cpa 
                 LEFT JOIN pre_auth_templates pat ON (pat.id = cpa.template_id) 
                 WHERE delete_status = '0' AND patient_id = '".$patient_id."' 
                 ORDER BY cpa.id DESC";
    $res = mysql_query($q);echo mysql_error();
    if($res && mysql_num_rows($res)>0){?>
        <table class="table_collapse cellBorder4">
            <tr class="section_header">
                <td style="width:20px;">#</td>
                <td style="width:250px;">Template Name</td>
                <td style="width:180px;">Request Date/Time</td>
                <td style="width:90px;">Request From</td>
                <td style="width:auto;">Response</td>
                <td style="width:95px" align="center">Download</td>
            </tr>
        <?php
        $record_cnt = 1;
        while($rs = mysql_fetch_assoc($res)){
			$responsetext	= '';
            $request_from_val	 = $objEBilling->x12RequestFrom(intval($rs['request_from']));
            $response	= $objEBilling->read278EDI($rs['response_data']);
            $responsetext	= $objEBilling->responseArray2Text($response);
            ?>
            <tr>
                <td class="valignTop"><?php echo $record_cnt;?></td>
                <td class="valignTop"><?php echo $rs['template_name'];?></td>
                <td class="valignTop"><?php echo $rs['request_datetime'];?></td>
                <td class="valignTop"><?php echo $request_from_val;?></td>
                <td class="valignTop"><?php echo $responsetext;?></td>
                <td align="center" class="valignTop">
                    <form method="post" action="eligibility/download.php" target="_blank" style="margin:0px;" name="requ">
                    <input type="hidden" name="content" value="<?php echo $rs['request_data'];?>">
                    <input type="hidden" name="file" value="278_request_<?php echo $patient_id;?>_<?php echo $rs['cpa_id'];?>.txt">
                    <input type="hidden" name="from" value="cpa">
                    <input type="submit" name="requ_sub" class="dff_button_sm" value="278 Request">
                    </form>
                    <form method="post" action="eligibility/download.php" target="_blank" style="margin-top:2px;" name="resp">
                    <input type="hidden" name="content" value="<?php echo $rs['response_data'];?>">
                    <input type="hidden" name="file" value="278_response_<?php echo $patient_id;?>_<?php echo $rs['cpa_id'];?>.txt">
                    <input type="hidden" name="from" value="cpa">
                    <input type="submit" name="resp_sub" class="dff_button_sm" value="278 Response">
                    </form>
                </td>
            </tr>
            
            <?php
            $record_cnt++;
        }?>
        </table>
        <?php
    }else{
        echo 'Pre-Authorization not checked for this patient.';
    }
?>
</div>    

<div class="section">
     <table class="table_collapse" style="height:30px; background:#93B9DC;">
        <tr>
            <td style="text-align:center;">
                <input type="button" title="Close" class="dff_button"  id="close"   value="Close" name="close" onClick="window.close();">
            </td>
        </tr>
    </table>
</div>
</body>
</html>