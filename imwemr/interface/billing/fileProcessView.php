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

?><?php
/*
File: electronic_billing.php
Purpose: Electronic Billing Main interface.
Access Type: Direct Access (in frame) 
*/

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/common_function.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
$objEBilling = new ElectronicBilling();

if($errId == 'undefined') $errId = '';

//----SQL DATE FORMAT---
$getSqlDateFormat= get_sql_date_format();

//--- GET BATCH FILE DETAILS ---
$fileDetails 			= $objEBilling->getBatchFileDetails($fileId);
$ins_company_id			= $fileDetails['ins_company_id'];
$encounterId 			= $fileDetails['encounter_id'];
$pcld_id 				= $fileDetails['pcld_id'];
$clearing_house 		= $fileDetails['clearing_house'];
$fileStatus 			= $fileDetails['status'];
	$res_vs_check = imw_query("SELECT * FROM vision_share_cert_config");
	if(imw_num_rows($res_vs_check)==0){
		$ClearingHouse			= $objEBilling->ClearingHouse();
		$clearing_house = $ClearingHouse[0]['house_name'];
	}

$ins_comp_chk 			= $fileDetails['ins_comp'];
$group_id 				= $fileDetails['group_id'];
$Interchange_control	= $fileDetails['Interchange_control'];

//--- GET ALL USERS DETAILS ------
$usersnameArr = $objEBilling->getUserArr();

//--- GET ALL MODIFIERS ----
$modifierCodeArr = $objEBilling->get_all_modifiers();

//-- GETTING GROUP DETAIL -------
$group_details = $objEBilling->get_groups_detail($group_id);

//---- GET CLAIM BATCH FILE RECORDS --------
$qry = "SELECT pcl.encounter_id, pcl.patient_id, DATE_FORMAT(pcl.date_of_service,'$getSqlDateFormat') as date_of_service, 
			pcl.primaryProviderId, SUM(pcld.totalAmount) AS totalAmt, COUNT(pcld.charge_list_detail_id) AS totalCPT, 
			pcl.billing_type,COUNT(pcld.units) AS totalUnits, 
			pcl.gro_id,pcl.primary_paid,pcl.secondary_paid, 
			pcl.tertiary_paid, pcl.primaryInsuranceCoId, pcl.secondaryInsuranceCoId, pcl.tertiaryInsuranceCoId, 
			pcl.operator_id, pd.lname, pd.fname, pcl.charge_list_id, pd.mname, 
			ic1.in_house_code AS PriInsName, ic2.in_house_code AS SecInsName,
			ic1.institutional_type AS PriInstiType, ic2.institutional_type AS SecInstiType 
		FROM patient_charge_list pcl 
		JOIN patient_data pd ON (pd.id = pcl.patient_id) 
		JOIN patient_charge_list_details pcld ON (pcld.charge_list_id = pcl.charge_list_id) 
		LEFT JOIN insurance_companies ic1 ON (ic1.id = pcl.primaryInsuranceCoId) 
		LEFT JOIN insurance_companies ic2 ON (ic2.id = pcl.secondaryInsuranceCoId) 
		WHERE pcl.encounter_id IN ($encounterId) AND pcld.charge_list_detail_id IN ($pcld_id)";
if(empty($errId) === false){ 
	$qry .= " AND pcl.charge_list_id not IN ($errId)"; 
}
$qry .= " group by pcl.encounter_id
		order by pd.lname,pd.fname";
$chargeListRes = imw_query($qry);

//---- GETTING CHARGE LIST DETAILS ACCORING TO PCLD_IDs----
$pcld_query = "SELECT pcld.charge_list_detail_id, pcld.charge_list_id, pcld.diagnosis_id1, pcld.diagnosis_id2, pcld.diagnosis_id3, 
				pcld.diagnosis_id4, pcld.diagnosis_id5, pcld.diagnosis_id6, pcld.diagnosis_id7, pcld.diagnosis_id8, 
				pcld.diagnosis_id9, pcld.diagnosis_id10, pcld.diagnosis_id11, pcld.diagnosis_id12,pcld.procCode, 
				pcld.modifier_id1, pcld.modifier_id2, pcld.modifier_id3, pcld.modifier_id4, 
				cft.cpt4_code FROM patient_charge_list_details pcld 
				LEFT JOIN cpt_fee_tbl cft ON (cft.cpt_fee_id = pcld.procCode AND cft.not_covered='0') 
				JOIN patient_charge_list pcl ON (pcl.charge_list_id = pcld.charge_list_id) 
				WHERE (pcld.del_status='0' OR (pcld.del_status='1' OR pcl.void_notify='1')) AND pcld.proc_selfpay='0' AND pcld.charge_list_detail_id IN ($pcld_id) 
				ORDER BY pcld.charge_list_id";
$pcld_res = imw_query($pcld_query);
$chargeDetails = array();
if($pcld_res && imw_num_rows($pcld_res)>0){
	while($pcld_rs = imw_fetch_assoc($pcld_res)){
		$pcl_id = $pcld_rs['charge_list_id'];
		
		//Charge List Detail IDs
		$chargeDetails[$pcl_id]['pcld'][] = $pcld_rs['charge_list_detail_id'];
		
		//CPT
		$chargeDetails[$pcl_id]['cpt'][] = $pcld_rs['cpt4_code'];
		
		//DX
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id1'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id2'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id3'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id4'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id5'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id6'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id7'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id8'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id9'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id10'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id11'];
		$chargeDetails[$pcl_id]['dx'][] = $pcld_rs['diagnosis_id12'];
		
		//MODs
		if(empty($pcld_rs['modifier_id1']) === false){$chargeDetails[$pcl_id]['mods'][] = $modifierCodeArr[$pcld_rs['modifier_id1']];}
		if(empty($pcld_rs['modifier_id2']) === false){$chargeDetails[$pcl_id]['mods'][] = $modifierCodeArr[$pcld_rs['modifier_id2']];}
		if(empty($pcld_rs['modifier_id3']) === false){$chargeDetails[$pcl_id]['mods'][] = $modifierCodeArr[$pcld_rs['modifier_id3']];}
		if(empty($pcld_rs['modifier_id4']) === false){$chargeDetails[$pcl_id]['mods'][] = $modifierCodeArr[$pcld_rs['modifier_id4']];}
	}
}

$totalAmtArr 	= array();
$totalAmount 	= 0;
$num_of_claims 	= 0;

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Electronic Billing</title>
<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/billinginfo.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/html5shiv.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/respond.min.js"></script>
<![endif]-->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/billing_electronic.js"></script>
</head>
<script type="text/javascript">

	function changeTab(id,eid){
		var eid="";
		var filename = "../accounting/accountingTabs.php";
		var send_url = "billing_session.php?patient="+id+"&rd2="+filename+"&front=yes&encounter="+eid;
		opener.top.core_redirect_to("Accounting", send_url);
	}
	
	function sendFile(){
		top.fAlert('Processing.... please wait.');
		document.downloadFrm.action = 'curl.php';
		document.downloadFrm.submit();
	}
	
	function send_vision_share(op, insCompId){
		document.downloadFrm.action = 'vision_share_837_batch_submit.php?gpId='+op+'&insCompId='+insCompId;
		document.downloadFrm.submit();
	}
	
	function toggle_batch_hx(){
		$('#batch_hx').toggle();
	}
	
	$(document).ready(function(){
		$.ajax({type: "GET",
				url: 'electronic_billing_ajax.php?eb_task=get_batch_file_log&showbatchhx=yes&batchfileId=<?php echo $fileId;?>',
				success: function(r){
					$('#batch_hx').html(r);
					$('#batch_hx table tr:odd').addClass('alt');
			}
		});			
	});
</script>
<style type="text/css" media="print">
.resultset_div{height:auto !important;}
#footer_buttons_div{display:none;}
.resultset_div table{width:100% !important;}
.resultset_div table tr td{width:auto !important; white-space:normal !important;}
</style>
<body>
<div class="container-fluid mtb10">
    <div class="whitebox">
    	<div class="row">
			<?php if(imw_num_rows($chargeListRes)>0){?>
        	<div class="col-sm-12">
				<div class="purple_bar">
                <span class="glyphicon glyphicon-list pull-right link_cursor" onClick="$('#myModal').modal('show');"></span>
                <big><b>ELECTRONIC CLAIM FILE - INTERCHANGE NUMBER: <?php echo $objEBilling->padd_string($Interchange_control,4,'0').' ('.ucfirst($ins_comp_chk).')';?></b></big></div>
            	<div class="clearfix"></div>
                <div class="table-responsive col-sm-12 resultset_div">
                    <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr class="grythead">
                            <th>Patient Name - ID</th>
                            <th>Encounter ID</th>
                            <th>DOS</th>
                            <th>Claim for Payer</th>
                            <th>Physician</th>
                            <th>CPT</th>
                            <th>Units</th>
                            <th>DX Codes</th>
                            <th>Charges</th>
                            <th>Modifiers</th>
                            <th>Operator</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($rs = imw_fetch_assoc($chargeListRes)){
                        $ins_prof_chk	= "";
                        $INST_TYPE 		= '';
                        
                        $num_of_claims++;
                        $pcl_id						= $rs['charge_list_id'];
                        $patientId 					= $rs['patient_id'];
                        $patientNameArr 			= array();
                        $patientNameArr["LAST_NAME"] 	= $rs['lname'];
                        $patientNameArr["FIRST_NAME"] 	= $rs['fname'];
                        $patientNameArr["MIDDLE_NAME"] 	= $rs['mname'];
                        $patientName 				= changeNameFormat($patientNameArr);
                        $encounter_id 				= $rs['encounter_id'];	
                        $dateOfService 				= $rs['date_of_service'];
                        $patientDueAmount 			= numberformat($rs['totalAmt'],2);
                        $totalAmount 				+= preg_replace('/,/','',$rs['totalAmt']); 
                        $totalCPT 					= $rs['totalCPT'];
                        $totalUnits 				= count(array_unique($chargeDetails[$pcl_id]['cpt']));//$rs['totalUnits'];
                        $phyName 					= $usersnameArr[$rs['primaryProviderId']];
                        
                        $primaryInsuranceCoId = $rs['primaryInsuranceCoId'];
                        $secondaryInsuranceCoId = $rs['secondaryInsuranceCoId'];
                        
                        $insCompName	= $rs['PriInsName'];
                        if($ins_comp_chk=="secondary"){
                            $insCompName	= $rs['SecInsName'];
                        }
						
						/*******NEW CODE TO FETCH CORRECT PAYER AT THE TIME OF BATCH CREATION*******/
						$final_payer = $objEBilling->get_payer_when_batch_created($encounter_id,$patientId,$fileId,$ins_comp_chk);
						if($final_payer && !empty($final_payer)) $insCompName = $final_payer;
						/*******END OF NEW CODE****************************************************/
						                        
                        $operatorName 	= $usersnameArr[$rs['operator_id']];
    
                        $cpt_string		= implode(', ',array_unique($chargeDetails[$pcl_id]['cpt']));
                        $dx_string		= implode(', ',array_unique($chargeDetails[$pcl_id]['dx']));
                        $mod_string		= implode(', ',array_unique($chargeDetails[$pcl_id]['mods']));
                        
                        $ClaimAmount 	= numberformat($rs['totalAmt'],2);
                        
                        $pcld_IDs		= implode(',',$chargeDetails[$pcl_id]['pcld']);
						
						if($ins_comp_chk=="primary"){
							$ins_comp="1";	if($rs['PriInstiType']=='INST_PROF') $ins_prof_chk = '1';
						}else if($ins_comp_chk=="secondary"){
							$ins_comp="2";	if($rs['SecInstiType']=='INST_PROF') $ins_prof_chk = '1';
						}
						
                        if($rs['billing_type']==3 || $rs['billing_type']==1){
							$printHcfa="1";	
						}else if($rs['billing_type']==2){
							$printHcfa="2";	
						}else{
                        	$printHcfa="1";
							if($group_details['group_institution']=='1' && $ins_prof_chk==""){
								$printHcfa="2";
							}
						}
						?>
                        <tr>
                            <td class="text-nowrap"> <a href="javascript:void(0);" onClick="print_hcfa_ub('<?php echo $pcl_id;?>','<?php echo $pcld_IDs;?>','<?php echo $ins_comp;?>','<?php echo $printHcfa;?>');"><?php echo $patientName;?> - <?php echo $patientId;?></td>
                            <td class="text-center"><?php echo $encounter_id;?></td>
                            <td class="text-nowrap text-center"><?php echo $dateOfService;?></td>
                            <td class="text-center"><?php echo $insCompName;?></td>
                            <td class="text-nowrap"><?php echo $phyName['short'];?></td>
                            <td><?php echo $cpt_string;?></td>
                            <td class="text-center"><?php echo $totalUnits;?></td>
                            <td><?php echo $dx_string;?></td>
                            <td class="text-right"><?php echo $ClaimAmount;?></td>
                            <td class="text-center"><?php echo $mod_string;?></td>
                            <td class="text-nowrap"><?php echo $operatorName['short'];?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                    </table>
            	</div>
			</div>
       	</div>
        <div class="clearfix"></div>
        <div class="row">
        	<div class="col-sm-12">
	            <div class="purple_bar col-sm-6 text-center"><b><?php echo $num_of_claims;?> Claims</b></div>
    	        <div class="purple_bar col-sm-6 text-center"><b>Total Amount: <?php echo numberformat($totalAmount,2);?></b></div>
        	</div>
        </div>

        <?php }else{?>    
        <div class="row"><div class="col-sm-12"><?php echo imw_msg('no_rec');?></div></div>
        <?php }?>
    </div>
    <div class="clearfix"></div>
    <div class="row">
    	<div class="col-sm-12 text-center" id="footer_buttons_div">
            <form name="downloadFrm" action="downloadFile.php" method="post">
                <input type="hidden" name="batch_file_submitte_id" id="batch_file_submitte_id" value="<?php echo $fileId;?>" >
                <?php if($fileStatus==0 && $clearing_house!=''){?>
                <input type="button" id="Commercial" name="Commercial" value="Send To Clearing" class="btn btn-primary" onClick="sendFile();" title="<?php echo $clearing_house;?>">
                <?php }else if($fileStatus==0 && $clearing_house=='visionshare'){?>
                <input type="button" id="btSendVS" name="btSendVS" value="Send To Vision Share" class="btn btn-primary" onClick="send_vision_share('<?php echo $group_id;?>', '<?php echo $ins_company_id;?>');">
                <?php }?>
                <input type="button" id="btn" name="btn" value="Print" class="btn btn-info mlr10" onClick="javascipt:window.print();">
                <input type="submit" id="Save" name="Save" value="Save" class="btn btn-success">
                <input type="button" id="btn2" name="btn2" class="btn btn-info mlr10" value="Close Window" onClick="javascript:window.close()" >
            </form>
        </div>
	</div>
    <div class="clearfix"></div>
</div>

<div id="myModal" class="modal" role="dialog">
    <div class="modal-dialog modal-md"> 
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal_title">Claim Batch  - Action Log</h4>
            </div>
            <div class="modal-body" id="batch_hx">
                <img src="../../library/images/loading.gif"> ...loading, please wait..                
            </div>
            <div id="module_buttons" class="ad_modal_footer modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript">
if(typeof(window.opener.top.innerDim)=='function'){
	var innerDim = window.opener.top.innerDim();
	if(innerDim['w'] > 1600) innerDim['w'] = 1600;
	if(innerDim['h'] > 900) innerDim['h'] = 900;
	window.resizeTo(innerDim['w'],innerDim['h']);
	brows	= get_browser();
	if(brows!='ie') innerDim['h'] = innerDim['h']-35;
	var result_div_height = innerDim['h']-210;
	$('.resultset_div').height(result_div_height+'px');
}
</script>
</body>
</head>