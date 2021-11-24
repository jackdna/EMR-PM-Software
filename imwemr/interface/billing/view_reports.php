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
File: view_reports.php
Purpose: To get reports form clearing house
Access Type: Direct Access
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
$objEBilling 	= new ElectronicBilling();
$report_id		= stripslashes(trim($_GET['id']));

//--- CHANGE REPORTS STATUS AS READ ----
$objEBilling->MarkReportStatus('comm',$id,'1');

$reportDetails = $objEBilling->getEmdeonReport($id);
$report_file_name = trim($reportDetails['ws_file_name']);
$valid_response = true;
/******DOWNLOADING REPORT DATA IF EMPTY/ CLEARING HOUSE SPECIFIC*****/
if(trim($reportDetails['report_data'])==''){
	$ClearingHouse	= $objEBilling->ClearingHouse();
	$CL		 		= $ClearingHouse[0]['abbr'];
	$group_id		= $reportDetails['group_id'];
	
	if($CL=='PI'){
		//GROUP DETAILS
		$group_rs 			= $objEBilling->get_groups_detail($group_id);
		
		$CL_mode 		= $ClearingHouse[0]['connect_mode'];
		$CL_url			= ($CL_mode=='T') ? $ClearingHouse[0]['test_url'] : $ClearingHouse[0]['prod_url'];
		
		//-------SKIPPING LOOP IF ANY OF THE REQUIRED DATA IS MISSING------
		if(trim($group_rs['user_id'])!='' && trim($group_rs['user_pwd'])!=''){
			$cur = curl_init($CL_url."transfer/download.php?file=".$report_file_name);
			curl_setopt($cur, CURLOPT_USERPWD, $group_rs['user_id'].":".$group_rs['user_pwd']);
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
			$output	=  curl_exec($cur);
			$error	= curl_error($cur);
			curl_close($cur);
			//echo $output.'<hr>'.$error.'<hr><hr><hr>';
			if(strtolower(substr($output,0,5))=='error'){//if text started with ERROR...
				$report_data = $output.'<br><br>This report is either already downloaded from clearing house or not prepared yet.';
				$valid_response = false;
			}else if(!empty($error)){//If any curl error found..
				$report_data = $error;
				$valid_response = false;
			}else{
				if($output!='' && $error==''){
					$reportDetails = $objEBilling->SaveCommercialReport($output,$report_id,$CL);
				}
			}
		}
		
	}
	
}
$report_data_db = stripslashes(html_entity_decode(trim($reportDetails['report_data'])));
if(!empty($report_data_db)) $report_data = $report_data_db;

$is277 = strpos($report_data, "ST*277*");
$doprocess = (isset($_REQUEST['process']) && trim($_REQUEST['process'])!='') ? trim($_REQUEST['process']) : '';

//IF THE REPORT DATA SAYS, IT IS 277.
if($is277 !== false &&  $doprocess=='277' && $valid_response){
	require_once(dirname(__FILE__).'/../../library/classes/CLSVSDemo.php');	
	$objVsDemo = new CLSVSDemo;
	$sortOrder = intval($_GET['sortOrder']);
	$report_data_277 = array();
	if(substr_count($report_data,'ISA*00*')>1){
		$temp_rpt_data = explode('ISA*00*',$report_data);
		foreach($temp_rpt_data as $str_rpt_data){
			$str_rpt_data = 'ISA*00*'.$str_rpt_data;
			$report_data_arr = $objVsDemo->process277CA($str_rpt_data,$sortOrder);
			$report_data_277[] = $report_data_arr['0'];	
		}
	}else{
		$report_data_arr = $objVsDemo->process277CA($report_data,$sortOrder);
		$report_data_277[] = $report_data_arr['0'];
	}
}


if(substr(trim($report_data),0,1) == 'R' && $is277===false && $valid_response){
	$report_data_arr = array();
	$report_data_arr = explode("\n",$report_data);
	$report_data = "";
	
	for($i=0;$i<=count($report_data_arr);$i++){
		
		if(substr(trim($report_data_arr[$i]),0,1) == 'R'){
			$space_place = strpos($report_data_arr[$i]," ");
			if($space_place <= 50 && $space_place > 25){
				$replace_character = substr($report_data_arr[$i],0,$space_place);	
				$report_data .= str_replace($replace_character," ",$report_data_arr[$i]);
			}
		} 
	}
}

$report_recieve_date = preg_replace('/(-)|(:)|( )/','_',$reportDetails[0]['report_recieve_date']);

//VISION SHARE CASE-----------------------------------
$HDRreportFlag = $HTMLreportFlag = false;
if(empty($report_data) == false && $is277===false && $valid_response){
	$report_file_extension	= strtoupper(substr($report_file_name,-4));
	if(in_array($report_file_extension,array('.INS','.RSP'))){
		$HDRreport = $objEBilling->parseHDRreport(strtoupper($report_data),$report_file_extension);
		if($HDRreport)$HDRreportFlag = true;
	}
	if(in_array($report_file_extension,array('.HTM','HTML'))){
		if($report_file_extension=='HTML'){
			$report_file_extension	= strtoupper(substr($report_file_name,-5));			
		}
		if(in_array($report_file_extension,array('.HTM','.HTML'))){
			$HTMLreportFlag = true;
		}
	}
	
	
	if(!$HDRreportFlag && !$HTMLreportFlag){
		//--- CREATE LIVE FILE FOR DOWNLOADING ---
		$fileData = preg_replace('/[^0-9A-Za-z*~:]/','',$report_data);	
		$fileDataArr = preg_split('/ISA/',$fileData);
		
		for($i=0;$i<count($fileDataArr);$i++){
			$fileData = 'ISA'.$fileDataArr[$i];	
			
			//--- GET 997 FILE DATE ---
			$dateDataArr = preg_split('/(GS\*FA\*)/',$fileData);
			$dateDataArr = preg_split('/(~)/',$dateDataArr[1]);
			$dateDataArr = preg_split('/(\*)/',$dateDataArr[0]);
			$fileDateVal = NULL;
			
			if(empty($dateDataArr[2]) === false){
				$fileDateVal = substr($dateDataArr[2],0,4).'-';
				$fileDateVal .= substr($dateDataArr[2],4,2).'-';
				$fileDateVal .= substr($dateDataArr[2],-2);
			}
			
			$dataArr = preg_split('/(AK1\*HC\*)|(AK5\*)|(AK9\*)/',$fileData);
			$trans_data_arr = preg_split('/~/',$dataArr[1]);
			$setNumberData = (int)preg_replace("/\s/","",$trans_data_arr[0]);
			$status = 0;
			if(ucfirst($dataArr[2][0]) == 'A' || ucfirst($dataArr[3][0]) == 'A'){
				$status = 1;
			}else if(ucfirst($dataArr[2][0]) == 'E' || ucfirst($dataArr[3][0]) == 'E'){
				$status = 2;
			}
			
			//---- CHANGE 837 FILE STATUS -----------
			if($setNumberData > 0){
				//--- FILE ALREADY UPLOADED EXISTS CHECK ----
				$q1	 = "select count(*) as rowCount from batch_file_submitte
						where status = '1' and Transaction_set_unique_control = '$setNumberData'";
				$countQryRs = imw_query($q1);
				$countQryRes = array();
				while($countQryRs1 = imw_fetch_assoc($countQryRs)){
					$countQryRes[] = $countQryRs1;
				}
				
				//--getting file name via unique control number---
				$txn_control_num_qry = "select file_name from batch_file_submitte where Transaction_set_unique_control = '$setNumberData' LIMIT 0,1";
				$txn_control_num_res = imw_query($txn_control_num_qry);
				if($txn_control_num_res && imw_num_rows($txn_control_num_res)>0){
					$txn_control_num_rs = imw_fetch_assoc($txn_control_num_res);
				}
				
				//--- CHANGE CLAIM FILE STATUS AS ACCEPTED/REJECTED ----
				$qry = "update batch_file_submitte set status = '$status' where 
						Transaction_set_unique_control = '$setNumberData'";
				$qryId = imw_query($qry);
	
				if($countQryRes[0]['rowCount'] == 0){
					if($status == 1 || $status == 2){								
						$getEncounter = getRecords('batch_file_submitte','Transaction_set_unique_control',$setNumberData);
						if(empty($getEncounter) == false){
							$allEncounter = explode(',',$getEncounter->encounter_id);
							foreach($allEncounter as $encounter){
								$patientListData = getRecords('patient_charge_list',"del_status='0' and encounter_id",$encounter);								
								if($patientListData->encounter_id > 0){
									$submitedRec['encounter_id'] = $encounter;
									$submitedRec['patient_id'] = $patientListData->patient_id;
									$submitedRec['Ins_type'] = $getEncounter->ins_comp;
									if($getEncounter->ins_comp == 'primary'){
										$insCompId = $patientListData->primaryInsuranceCoId;
									}
									else{
										$insCompId = $patientListData->secondaryInsuranceCoId;
									}
									$submitedRec['Ins_company_id'] = $insCompId;
									$submitedRec['posted_amount'] = $patientListData->totalBalance;
									$submitedRec['operator_id'] = $_SESSION['authUserID'];
									$submitedRec['submited_date'] = $fileDateVal;
									$insertIds = AddRecords($submitedRec,'submited_record');
								}
							}
						}
					}else if($status==2){
						$file_status = 'Accpeted with Errors.';	
					}else{
						$file_status = 'Rejected.';
					}
					$file_check = preg_split('/ISA\*00\*/',$fileData);
					
					if(count($file_check)>1){
						$dataRec = array();
						$dataRec['File_name'] = $dirRoot;
						$dataRec['set_number_id'] = $setNumberData;
						$dataRec['file_data'] = addslashes($fileData);
						$dataRec['file_upload_date'] = $fileDateVal;
						$dataRec['operator_id'] = $_SESSION['authId'];
						$dataRec['file_status'] = $status;
						$insertId = AddRecords($dataRec,'electronic_997_file');
						
						if(empty($msg) === true){	
							if($insertId > 0){
								$msg = 'File Successfully Uploaded. File Status is '.$file_status;
							}
							else{
								$msg = 'Error In Connection Please Try Agains.';
							}
						}
					}
				}
				else{
					$msg = 'File already uploaded.';
				}
			}
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>View Report</title>
<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" media="screen">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/billinginfo.css" rel="stylesheet" media="screen">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/html5shiv.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/css/css/respond.min.js"></script>
<![endif]-->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
    setTimeout(function(){init_disp_billing_rpt();},200);
});

function init_disp_billing_rpt(obj_id){
	obj_id = typeof(obj_id)=='string' ? '' : obj_id;
	if(obj_id != ''){
		$('#div_emd_reports').show();
	}
	h 		= pageHeight();
	pbh		= $('.bilnghead').height()*2;
	pbf		= $('#bottom_buttons').height()*2;
	h		= parseInt(h-pbh-pbf-0);
	$('#div_emd_reports pre, #div_emd_reports #HDRrpt, div#print_data').css({'height':h,'overflow-y':'auto','overflow-x':'hidden'});
}

window.opener.loadResult();		
if(typeof(window.opener.loadResultVS)!='undefined') window.opener.loadResultVS();

function saveFile(){
	document.saveFileFrm.submit();
}

function goBack(){
	document.view_reports_frm.submit();
}
function print_frm(){
	window.focus();
	window.print();
}
</script>
<style type="text/css">
#table_hdr tr td{vertical-align:top !important;}
</style>
<style type="text/css" media="print">
#div_emd_reports pre,#print_data,#div_emd_reports #HDRrpt{height:auto !important; overflow:hidden;}
#bottom_buttons,.sort_link{display:none;}
div.bilnghead{display:none !important;}
#div_emd_reports pre{font-size:10px;}
</style>
</head>
<body>
<div class="billreport">
    <div class="bilnghead">
        <div class="row">
        	<div class="col-sm-12">
            	<h2>View Electronic Billing Report</h2>
            </div>
        </div>
    </div>
	<div class="clearfix"></div>
    <div id="div_emd_reports">
        <?php if($HDRreportFlag){
			echo '<div id="HDRrpt">';
			foreach($HDRreport as $segType=>$segData){
			 	if($segType=='HDR'){
					echo '<div class="row">';
						if(count($segData)==4){$header_colwidth = 3;}
						else if(count($segData)==3){$header_colwidth = 4;}
						foreach($segData as $title=>$data){
							echo '<div class="col-sm-'.$header_colwidth.'"><b>'.$title.':</b> '.$data.'</div>';
						}
					echo '</div>';
				}else if($segType=='ACK' || $segType=='CST'){
						foreach($segData as $row){
							//$row['Patient ID'] = 23472;
							$PatientDataArr	= $objEBilling->get_patient_details(array('Patients'=>$row['Patient ID']));
							$PatientData	= $PatientDataArr[$row['Patient ID']];	
							?>
                            <table class="table table-striped table-bordered table-responsive" id="table_hdr">
							<tr>
                            	<td class="col-sm-4">
                                	<div class="bg-info"><b>PATIENT</b></div>
                                	<?php echo $PatientData['lname'].', '.$PatientData['fname'].' '.$PatientData['mname'].' - '.$PatientData['id'];?><br>DOB: <?php $patDOB	= date_create($PatientData['DOB']); echo date_format($patDOB,"m-d-Y");?>, Gender: <?php echo $PatientData['sex'];?>
                                </td>
                                <td class="col-sm-4">
                                	<div class="bg-info"><span class="pull-right"><?php if(!empty($row['Encounter ID'])){echo 'Encounter ID: '.$row['Encounter ID'];}?></span><b>CLAIM</b></div>
                                    Clearing House ID: <?php echo $row['PI Claim ID'];?><br>
                                    Payer Claim ID: <?php echo $row['Payer Claim ID'];?>
                                </td>
                                <td class="col-sm-4">
                                	<div class="bg-info">
                                    	<span class="pull-right"><?php echo $row['Response Date'];?>&nbsp;</span>
                                        <b>RESPONSE</b>
                                    </div>
                                    <?php echo $row['Response Type'];?><br>
                                    <?php echo $row['Response'];?>
                                </td>
                            </tr>
                            </table>
						<?php
                        }
				}else if($segType=='TRL'){
					echo '<div class="row">';
						foreach($segData as $title=>$data){
							if($title=='Count'){ echo '<div class="col-sm-12"><b>Encounter Count:</b> '.$data.'</div>';}
						}
					echo '</div>';
				}
			}
			echo '</div>';
			//pre($HDRreport);
			//echo '<pre>'.$report_data.'</pre>';
			
		}else if ($HTMLreportFlag){
			/*echo '<script type="text/javascript">window.location.href="html_rpt_viewer.php?report_id='.$report_id.'";</script>';*/
			echo '<iframe id="HDRrpt" name="HDRrpt" frameborder="0" style="width:99%" src="html_rpt_viewer.php?report_id='.$report_id.'"></iframe>';
		}else if ($report_data != '' && $report_data_277 == ''){
        	echo '<pre>'.$report_data.'</pre>';
		}else if(is_array($report_data_277)){
			echo '<div id="print_data">';
			foreach($report_data_277 as $k=>$v){echo $v;}
			echo '</div>';
		}
		?>
    </div>
</div>

<div class="clearfix"></div>

<div class="text-center" id="bottom_buttons">
	<?php if ($HTMLreportFlag){?>
    <input type="button" class="btn btn-info" value="Print" onClick="$('#HDRrpt').prop('src','html_rpt_viewer.php?report_id=<?php echo $report_id;?>&print=yes');"> &nbsp; &nbsp; &nbsp; &nbsp;
    <?php }else{?>
    <input type="button" class="btn btn-info" value="Print" onClick="print_frm();"> &nbsp; &nbsp; &nbsp; &nbsp;
    <?php }?>
    <input type="button" class="btn btn-danger" value="Close" onClick="window.close();">
</div>
<div class="clearfix"></div>

<script type="text/javascript">
if(typeof(window.opener.opener.top.innerDim)=='function'){
	var innerDim = window.opener.opener.top.innerDim();
	if(innerDim['w'] > 1200) innerDim['w'] = 1200; else innerDim['w'] = innerDim['w']-50;
	if(innerDim['h'] > 1024) innerDim['h'] = 900; else innerDim['h'] = innerDim['h']-100;
	window.resizeTo(innerDim['w'],innerDim['h']);
}
</script>
</body>
</html>