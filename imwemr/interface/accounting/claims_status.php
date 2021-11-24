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
include_once(dirname(__FILE__)."/../../config/globals.php");
$without_pat = 'true';
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
include_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');

$objEBilling = new ElectronicBilling();

$soapUser = 'MWI_IMEDIC1';
$soapPass = '9Hc6J4Nj';

if(intval($_REQUEST['enc_id'])>0){
	$encounter_id		= intval($_REQUEST['enc_id']);
	$encDetials			= $objEBilling->getEncounterDetails($encounter_id);
	$patient_id			= $encDetials['patient_id'];
	$patient_details	= $objEBilling->get_patient_details(array('Patients'=>$patient_id));
	$patientDetails		= $patient_details[$patient_id];
	$patientName 		= core_name_format($patientDetails['lname'], $patientDetails['fname'], $patientDetails['mname']).' - '.$patient_id;
}


$EDI276Data = "";
$errMsg = array();
if($encounter_id > 0 && $_REQUEST['do_check']=='yes'){//if encounter id provided.
	$chk_submitted_qry = "SELECT * FROM (SELECT submited_id,Ins_type,sr.posted_amount,sr.submited_date,sr.patient_id,pcl.gro_id, pcl.primaryInsuranceCoId, 
						  pcl.secondaryInsuranceCoId, pcl.primaryProviderId, pcl.case_type_id, pcl.date_of_service FROM submited_record sr 
						  JOIN patient_charge_list pcl ON (sr.encounter_id=pcl.encounter_id AND pcl.submitted='true') 
						  WHERE sr.encounter_id='".$encounter_id."' ORDER BY submited_date DESC)
						  AS submited_record GROUP BY Ins_type ORDER BY Ins_type LIMIT 0,1";
	$chk_submitted_res = imw_query($chk_submitted_qry);
	if($chk_submitted_res && imw_num_rows($chk_submitted_res)>0){//if encounter is submitted and not altered after that.
		$chk_submitted_rs = imw_fetch_assoc($chk_submitted_res);
		$patient_id 		= $chk_submitted_rs['patient_id'];
		
		$group_id			= $chk_submitted_rs['gro_id'];
		$groupDetails		= $objEBilling->get_groups_detail($group_id);
		$soapUser 			= $groupDetails['user_id'];
		$soapPass 			= $groupDetails['user_pwd'];

		$InsuranceCoId		= $chk_submitted_rs['primaryInsuranceCoId'];
		if($chk_submitted_rs['Ins_type']!='primary'){$InsuranceCoId	= $chk_submitted_rs['secondaryInsuranceCoId'];}
		$insuranceDetails	= $objEBilling->getInsCompDetails($InsuranceCoId);
		$insPayerID			= trim($insuranceDetails['emdeon_payer_eligibility']);
	//	if($groupDetails['group_institution']!='1'){$insPayerID	= trim($insuranceDetails['Payer_id_pro']);}
		
		$provider_id		= $chk_submitted_rs['primaryProviderId'];
		$providerDetails	= getUserDetails($provider_id);
		
		$subscriberDetails	= $objEBilling->get_patient_insurance($chk_submitted_rs['case_type_id'],$patient_id,$chk_submitted_rs['Ins_type'],$chk_submitted_rs['date_of_service']);
		$pat_sub_relation	= $subscriberDetails['subscriber_relationship'];
		$prod_TID			= trim($groupDetails['prod_tid']);//16515462
		$submitter_id		= $objEBilling->padd_string($prod_TID,15,' ','suffix');//'16515462       '; //Production:16515462, TEST: 16488793
		$receiver_id		= 'EMDEON         ';
		$ICNumberARR		= $objEBilling->get_unique_headers(276); //Interchange Control Number.
		$GSGE_ID			= $ICNumberARR['new_id_num'];
		$ICNumber			= $ICNumberARR['interchange_control_num'];
		$TSCNumber			= $ICNumberARR['transaction_set_unique_control'];	//Transaction Set Control Number.
		$ProductionCode		= 'P'; //P or T
		$VersionIdentifier	= '005010X212';
		$TransactionId		= $ICNumberARR['header_control_identifier']; //Submitter Transaction Identifier
		
		//HEADER //ISA*00*          *00*          *ZZ*16488793       *ZZ*EMDEON         *130627*1113*^*00501*000000001*0*T*:~
		$ISA	= 'ISA*00*          *00*          *ZZ*'.$submitter_id.'*ZZ*'.$receiver_id.'*'.date('ymd').'*'.date('hi').'*^*00501*'.$ICNumber.'*0*'.$ProductionCode.'*:~';

		//FUCNTIONAL GROUP HEADER //GS*HR*15929599*EMDEON*20130606*0650*000000001*X*005010X212A1~
		$GS		= 'GS*HR*'.trim($submitter_id).'*'.trim($receiver_id).'*'.date('Ymd').'*'.date('hi').'*'.$GSGE_ID.'*X*'.$VersionIdentifier.'~';
		
		//TRANSACTION SET HEADER //ST*276*0001*005010X212A1~
		$ST		= 'ST*276*'.$TSCNumber.'*'.$VersionIdentifier.'~';
		$Line_counter = 1;
		
		//BEGINNING OF HIERARCHICAL TRANSACTION //BHT*0010*13*ALL*20130606*0650~
		$BHT	= 'BHT*0010*13*'.$TransactionId.'*'.date('Ymd').'*'.date('hi').'~';
		$Line_counter++;
		
		//HIERARCHICAL LEVEL 1 //PAYER NAME LOOP (2100A)
		$HL1	= 'HL*1**20*1~';
		$Line_counter++;		
		
		//NM1*PR*2*AARP HEALTH PLAN*****PI*36273~
		$payer_name = preg_replace('/[^a-zA-Z0-9_\- .]/','',trim(substr($insuranceDetails['name'],0,60)));
		if($payer_name=='')					{$errMsg[] = 'Invalid payer name.';}
		else if(trim($insPayerID)=='')		{$errMsg[] = 'Payer ID not found for claim status request (RTE Payer ID).';}
		$NM1_PAYER		= 'NM1*PR*2*'.$payer_name.'*****PI*'.$insPayerID.'~';
		$Line_counter++;
		
		//HIERARCHICAL LEVEL 2 //INFORMATION RECEIVER NAME (Loop 2100B)
		$HL2	= 'HL*2*1*21*1~';	
		$Line_counter++;
		
		//NM1*41*2*SHORE EYE ASSOCIATES PA*****46*1093737017~
		$NM1_RECEIVER	= 'NM1*41*2*'.trim(substr($groupDetails['name'],0,60)).'*****46*'.trim($submitter_id).'~';
		$Line_counter++;
		
		//HIERARCHICAL LEVEL 3 //PROVIDER NAME (Loop 2100C)
		$HL3	= 'HL*3*2*19*1~';
		$Line_counter++;
		
		//NM1*IP*1*BARBER*KEVIN*M***XX*1184612269~
		$NM1_PROVIDER	= 'NM1*1P*1*'.$providerDetails['lname'].'*'.$providerDetails['fname'].'*'.trim(substr($providerDetails['mname'],0,1)).'***XX*'.$providerDetails['user_npi'].'~';
		$Line_counter++;
		
		//HIERARCHICAL LEVEL 4 //SUBSCRIBER NAME (Loop 2100D)
		if(strtolower($pat_sub_relation)=='self'){$hl4_end = 0;}else{$hl4_end = 1;}
		$HL4	= 'HL*4*3*22*'.$hl4_end.'~';
		$Line_counter++;
		
		//DMG*D8*19191029*M~
		if($patientDetails['DOB']=='00000000' || $patientDetails['DOB']=='')	{$errMsg[] = 'Subscriber\'s DOB is invlaid.';}
		if($patientDetails['sex']=='')	{$errMsg[] = 'Subscriber\'s Gender is invalid.';}
		$DMG_SUBSCRIBER = 'DMG*D8*'.preg_replace("/-/","",$subscriberDetails['subscriber_DOB']).'*'.strtoupper(substr($subscriberDetails['subscriber_sex'],0,1)).'~';
		$Line_counter++;
		
		//NM1*QC*1*DOE*JOHN****MI*R11056841~
		$NM1_SUBSCRIBER = '';
		if(strtolower($pat_sub_relation)=='self'){
			$subscriber_RelCode = 'IL';
		}else{
			$subscriber_RelCode = 'QC';
			$NM1_SUBSCRIBER = 'NM1*IL*1*'.$subscriberDetails['subscriber_lname'].'*'.$subscriberDetails['subscriber_fname'].'*'.trim(substr($subscriberDetails['subscriber_mname'],0,1)).'***MI*'.preg_replace("/-/","",$subscriberDetails['policy_number']).'~';
			$Line_counter++;
		}
		
		$HL5 = '';
		if($subscriber_RelCode == 'QC'){
			//HIERARCHICAL LEVEL 5 //DEPENDENT/PATIENT NAME (Loop 2100D)
			$HL5	= 'HL*5*4*23~';						
			$Line_counter++;
			$DMG_DEPENDENT = $DMG_SUBSCRIBER;
			$DMG_SUBSCRIBER = '';
		}
		
		$NM1_DEPENDENT = 'NM1*'.$subscriber_RelCode.'*1*'.$patientDetails['lname'].'*'.$patientDetails['fname'].'*'.trim(substr($patientDetails['mname'],0,1));
		if($subscriber_RelCode == 'IL'){
			$NM1_DEPENDENT .=	'***MI*'.preg_replace("/-/","",$subscriberDetails['policy_number']);
		}
		$NM1_DEPENDENT .= '~';
		$Line_counter++;
		
		//CLAIM IDENTIFICATION (LOOP 2200D)
		//CLAIM CONTROL NUMBER (REF*1K*4124396333104)
		$TRN_SEGMENT 	= 'TRN*1*'.$encounter_id.'~';
		$Line_counter++;
		/*
		$REF_IK			= 'REF*1K*4121476181852~';
		$Line_counter++;
		*/
		$claim_amount	= $chk_submitted_rs['posted_amount'];
		$AMT_CLMAMONT	= 'AMT*T3*'.$claim_amount.'~';
		$Line_counter++;
		
		$dos			= $chk_submitted_rs['date_of_service'];
		$DTP_DOS		= 'DTP*472*RD8*'.preg_replace("/-/","",$dos).'-'.preg_replace("/-/","",$dos).'~';
		$Line_counter++;
		
		//TRANSACTION SET END
		$Line_counter++;
		$SE_SEGMENT		= 'SE*'.$Line_counter.'*'.$TSCNumber.'~';
		$GE_SEGMENT		= 'GE*1*'.$GSGE_ID.'~';
		$IEA_SEGMENT	= 'IEA*1*'.$ICNumber.'~';
		
		$EDI276Data = strtoupper($ISA.$GS.$ST.$BHT.$HL1.$NM1_PAYER.$HL2.$NM1_RECEIVER.$HL3.$NM1_PROVIDER.$HL4.$DMG_SUBSCRIBER.$NM1_SUBSCRIBER.$HL5.$DMG_DEPENDENT.$NM1_DEPENDENT.$TRN_SEGMENT.$REF_IK.$AMT_CLMAMONT.$DTP_DOS.$SE_SEGMENT.$GE_SEGMENT.$IEA_SEGMENT);
		//$EDI276Data = 'ISA*00*          *00*          *ZZ*16488793       *ZZ*EMDEON         *130627*1113*^*00501*000000001*0*T*:~GS*HR*16488793*EMDEON*20130627*1113*000000001*X*005010X212~ST*276*0001*005010X212~BHT*0010*13*27712345*20130627*1113~HL*1**20*1~NM1*PR*2*CERTL*****PI*C5010~HL*2*1*21*1~NM1*41*2*CLINIC ONE*****46*5299999990~HL*3*2*19*1~NM1*1P*1*PROVII*DOCII****XX*5299999990~HL*4*3*22*0~DMG*D8*19741030*M~NM1*IL*1*LASTI*FIRSTI****MI*AAA05805801~TRN*1*1722634842~REF*1K*4121476181852~AMT*T3*172~DTP*472*RD8*20070223-20070228~SE*16*0001~GE*1*123456789~IEA*1*000000001~';
		
		if(count($errMsg)==0){
			//TEST
			//$soapClient = new SoapClient("../../tmp/emdeon276demo/emdeon_wsdl_test.wsdl");
			//$param = array("sUserID" => "ITSTCS_IMEDIC3","sPassword" => "6943u3ze");
			
			//PRODUCTION
			$soapClient = new SoapClient(dirname(__FILE__)."/../../library/classes/emdeon_wsdl_production.wsdl");
			$param = array("sUserID" => $soapUser,"sPassword" => $soapPass);
			
			//CHECKING AUTHORIZATION
			$authResult = $soapClient->Authenticate($param);
			//pre($authResult);
			
			//SETTING PARAMETERS AND SENDING
			//TEST
			//$param = array("sUserID" => "ITSTCS_IMEDIC3","sPassword" => "6943u3ze", "sMessageType" => "X12", "sEncodedRequest" => base64_encode($EDI276Data));
			
			//PRODUCTION
			$param = array("sUserID" => $soapUser,"sPassword" => $soapPass, "sMessageType" => "X12", "sEncodedRequest" => base64_encode($EDI276Data));
			//pre($param);
			
			$sendRq = $soapClient->SendRequest($param);
			//pre($sendRq);
			
			$arrSendRq = get_object_vars($sendRq);
			$arrSendRequestResult = get_object_vars($arrSendRq["SendRequestResult"]);
			//pre($arrSendRequestResult);		
			$ErrorCode =  $arrSendRequestResult["ErrorCode"];
			if(intval($ErrorCode)>0 && is_numeric($ErrorCode)){
				$errMsg[] = $ErrorCode.': '.$arrSendRequestResult["Response"];
			}else{
				$ErrorCode='';
				$edi277 		= base64_decode($arrSendRequestResult["Response"]);
				$claimStatus	= $objEBilling->render277CA($edi277);
				$resp_decode	= $claimStatus['decoded'];
				$request_status	= $claimStatus['main_status'];
				$now			= date('Y-m-d H:i:s');
				
				$insert_q = "INSERT INTO claim_status_enquiry (edi_format, patient_id, encounter_id, inscomp_id, group_id, provider_id, pat_sub_rel, claim_amount, dos, clm_control_num, send_data, sent_on, response, resp_decode, request_status, operator_id, del_status) VALUES('277','$patient_id','$encounter_id','$InsuranceCoId', '$group_id', '$provider_id', '$pat_sub_relation', '$claim_amount', '$dos', '', '".addslashes($EDI276Data)."','".$now."', '".addslashes($edi277)."', '".addslashes(json_encode($resp_decode))."', '".$request_status."', '".$_SESSION['authId']."',0)";
				$insert_res = imw_query($insert_q);
				if(!$insert_res){$errMsg[] = 'Unable to save response.';}
			}
		}
	}else{
		$errMsg[] = 'Encounter is edited after submission.';
	}
}else if(intval($_REQUEST['enc_id'])>0){
	$encounterDetails = $objEBilling->getEncounterDetails(intval($_REQUEST['enc_id']));
	//pre($encounterDetails);
	$encounter_id 	= $encounterDetails['encounter_id'];
	$patient_id		= $encounterDetails['patient_id'];
}
else{
	$errMsg[] = 'Encounter ID not provided.';
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Claim Status Request/Response</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/interface/themes/default/css_core.php" type="text/css">
<style type="text/css">
.resp_summary, .resp_details{line-height:1.2;}
</style>
<script type="text/javascript" src="../../library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.resultset tbody .link_cursor').click(function(){
		if($(this).find('td div.resp_details').css('display')=='none'){
			$('div.resp_summary').show();
			$('div.resp_details').hide();
			//$(this).find('td div.resp_summary').hide();
			$(this).find('td div.resp_details').show();
		}else{
			//$(this).find('td div.resp_summary').show();
			$(this).find('td div.resp_details').hide();			
		}
	});
	var errmsg = "<?php echo addslashes(''.implode('<br>',$errMsg)); ?>";
	if(errmsg != '') alert(errmsg);
	
});

function working(){
	$('#btn_check').val('Checking Status....').attr('disabled','disabled');
	$('#plz_wait').show();
}
</script>
</head>
<body scroll="no">
<div id="plz_wait" style="position:absolute;top:150px; left:250px; width:auto;" class="hide white border2 padd10">Please wait, contacting clearing house....</div>
<div class="section_header alignCenter text_big" style="height:25px; padding:5px 0px 0px 5px;"> 
    <?php ?><span class="fr">Encounter ID: <?php echo $encounter_id; ?>&nbsp;</span><?php ?>
    <span class="fl">Claim Status Request / Response</span>
    <span><?php echo $patientName; ?></span>
</div>
<div class="section" id="div_add_new_notes" style="height:385px; overflow:auto; overflow-x:hidden;">
    <?php $q_old_enquiries = "SELECT DATE_FORMAT(sent_on,'%m-%d-%Y %H:%i:%s %p') AS sent_on, response, request_status, operator_id FROM claim_status_enquiry WHERE patient_id='$patient_id' AND encounter_id='$encounter_id' AND del_status='0' ORDER BY sent_on DESC";
		$res_old_enquiries = imw_query($q_old_enquiries);echo imw_error();
		if($res_old_enquiries && imw_num_rows($res_old_enquiries)>0){?>
         	<table class="table_collapse cellBorder4">
            <thead>
            <tr class="bg4">
            	<th style="width:20px">#</th>
                <th style="width:170px" class="leftborder">DATE</th>
                <th style="width:180px" class="leftborder">OPERATOR</th>
                <th style="width:auto" class="leftborder">RESPONSE</th>
            </tr>
            </thead>
            </table>
        	<table class="table_collapse cellBorder4 resultset">
            <thead>
            <tr>
            	<th style="width:20px"></th>
                <th style="width:170px"></th>
                <th style="width:180px"></th>
                <th style="width:auto"></th>
            </tr>
            </thead>
            <tbody>
        <?php
			$cnt = 1;
			$altClass = 'bg1';
			while($rs_enquiries = imw_fetch_assoc($res_old_enquiries)){
				$op_details 	= getUserDetails($rs_enquiries['operator_id'],'lname, fname, mname');
				$operator_name	= core_name_format($op_details['lname'], $op_details['fname'], $op_details['mname']);
				if($altClass==''){$altClass = 'bg1';} else{$altClass = '';}
				$claimStatus	= $objEBilling->render277CA($rs_enquiries['response']);
				$resp_decode	= $claimStatus['decoded'];
				$request_status	= $claimStatus['main_status'];
				?>
            <tr class="link_cursor <?php echo $altClass;?>">
            	<td class="pl2"><?php echo $cnt;?></td>
            	<td class="pl2"><?php echo $rs_enquiries['sent_on'];?></td>
            	<td class="pl2"><?php echo $operator_name;?></td>
            	<td><div class="resp_summary padd2"><?php $st = explode(':',$request_status); echo $objEBilling->getEDImessage('EDI277',$st[0]);?></div>
                    <div class="resp_details hide"><?php
						$stc_Details = $resp_decode;
                    	if(isset($stc_Details['STC1']) && is_array($stc_Details['STC1'])){
							echo '<b>Claim Status:</b> ';
							foreach($stc_Details['STC1'] as $code=>$val){
								echo $val.' ';
							}
							echo '<br>';
						}
                    	if(isset($stc_Details['STC2']) && $stc_Details['STC2'] != ''){
							echo '<b>Claim Status Report Date:</b> '.$stc_Details['STC2'];
						}
						if(isset($stc_Details['STC3']) && $stc_Details['STC3'] != ''){
							echo '<br><b>Action Performed:</b> '.$stc_Details['STC3'];
						}
						if(isset($stc_Details['STC4']) && $stc_Details['STC4'] != ''){
							echo '<br><b>Claim Amount:</b> '.$stc_Details['STC4'];
						}
						if(isset($stc_Details['STC5']) && $stc_Details['STC5'] != ''){
							echo '<br><b>Paid Amount:</b> '.$stc_Details['STC5'];
						}
						if(isset($stc_Details['STC6']) && $stc_Details['STC6'] != ''){
							echo '<br><b>Payment Date:</b> '.$stc_Details['STC6'];
						}
						if(isset($stc_Details['STC7']) && $stc_Details['STC7'] != ''){
							echo '<br><b>Payment Method:</b> '.$stc_Details['STC7'];
						}
						if(isset($stc_Details['STC8']) && $stc_Details['STC8'] != ''){
							echo '<br><b>Payment Issue Date:</b> '.$stc_Details['STC8'];
						}
						if(isset($stc_Details['STC9']) && $stc_Details['STC9'] != ''){
							echo '<br><b>Payment# (Check3, EFT# etc.):</b> '.$stc_Details['STC9'];
							if(isset($stc_Details['STC10']) && $stc_Details['STC10'] != ''){
								echo ', '.$stc_Details['STC10'];
							}
							if(isset($stc_Details['STC11']) && $stc_Details['STC11'] != ''){
								echo ', '.$stc_Details['STC11'];
							}
						}						
					?></div>
                </td>
            </tr>			
            <?php
				$cnt++;
			}?>
            </tbody>
            </table>
			<?php
		}else{
			echo '<div class="alignCenter warning text12b">Claim status never checked.</div>';
		}
	
	?>
</div>
    <div class="section">
         <table class="table_collapse" style="height:30px;">
            <tr>
                <td style="text-align:center;">
                	<form style="margin:0px;" onSubmit="return working()">
                    	<input type="hidden" name="enc_id" id="enc_id" value="<?php echo $encounter_id;?>">
                    	<input type="hidden" name="do_check" id="do_check" value="yes">
                    	<input type="submit" title="Check Claim Status" class="dff_button"  id="btn_check" value="Check Claim Status" name="btn_check">
	                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	                <input type="button" title="Close" class="dff_button"  id="close"   value="Close" name="close" onClick="window.close();">
                    </form>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
