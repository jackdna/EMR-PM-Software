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
require_once(dirname(__FILE__).'/../../config/globals.php');
//require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

$rqPhyId = $rqFacId = $rqDated = "";
$rqPhyId = $_REQUEST["phyId"];
$rqFacId = $_REQUEST["facId"];
$rqDated = $_REQUEST["dated"];
list($year, $month, $day) = explode("-", $rqDated);
$day_name = date("l", mktime(0, 0, 0, $month, $day, $year));
if(strlen($day) == 1){
	$day = "0".$day;
}
$rqDated = $year."-".$month."-".$day;

$arrPhyId = $arrFacId = array();
$arrPhyId = explode(",",$rqPhyId);
foreach($arrPhyId as $key => $val){
	if(empty($val) == true){
		unset($arrPhyId[$key]);
	}
}
$arrPhyId = array_values($arrPhyId);
$arrFacId = explode(",",$rqFacId);
foreach($arrFacId as $key => $val){
	if(empty($val) == true){
		unset($arrFacId[$key]);
	}
}
$arrFacId = array_values($arrFacId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css">
	<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css" rel="stylesheet">
    
    
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/messi/messi.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/common.js"></script>
    <!--
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/admin/menuIncludes_menu/js/disableBackspace.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/common/script_function.js"></script> 
    -->
    
    <!--
    ---------------
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/interface/themes/default/common.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $css_patient; ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/admin/menuIncludes_menu/js/disableBackspace.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/common/script_function.js"></script>
    -->  
    <script language="javascript">
        window.focus();
        function printEligibilityFile(phyId, facId, dated){
            var urlRTEFile = "print_realtime_eligibility_file.php?phyId="+phyId+"&facId="+facId+"&dated="+dated;
            var h = document.body.clientHeight;
            window.open(urlRTEFile,'allAPPRTEPrint','toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
        }
        function on_load(){
            document.getElementById("img_load").style.display = 'none';
        }
    </script>     
    </head>
    <body>
        <div id="img_load" style="display:block;position:absolute;top:440px;left:625px;">
        <img  src="<?php echo $GLOBALS['webroot'];?>/library/images/loading_image.gif" alt="Data Loading" ></div>
        
        <div class="container-fluid">
			<div class="row">
                <div class="col-sm-12 text-center">
                    <h4><strong>Eligibility Detail for: <?php echo get_date_format($rqDated,'mm-dd-yy'); ?></strong></h4>
                </div>
                
            </div>
			<div class="whtbox" style="height:<?php echo $_SESSION['wn_height'] - 300; ?>px; overflow:auto">
				<table class="table table-striped table-bordered table-hover adminnw" id="elData">
				<thead>
				<tr>
					<th>Appt. Time</th>
					<th>Appt. Proc</th>
					<th>RTA Check</th>
					<th>Opr</th>
					<th>Physician Name</th>
					<th>Location</th>
					<th>Patient Name - ID</th>
					<th>Ins Practice Name</th>
					<th><span style="vertical-align:super;">*</span>CoPay/Co-Ins/Ded</th>
					<th>Status</th>
				</tr>
				</thead>
				<tbody>
					<?php
					$intTotRec = 0;
					if((count($arrPhyId) > 0) && (count($arrFacId) > 0)){
						$intTempCounter = 0;
						$rqPhyId = $rqFacId = "";
						$rqPhyId = implode(",",$arrPhyId);
						$rqFacId = implode(",",$arrFacId);
						$qryGetPatEl = "select 
								sa.id as patAppIdSA,  sa.rte_id as patRTEId , sp.proc as patAppProc , 
								DATE_FORMAT(sa.sa_app_starttime, '%h:%i %p') as saTime,
								CONCAT(SUBSTRING(us.fname,1,1),SUBSTRING(us.lname,1,1),SUBSTRING(us.mname,1,1)) as elOpName,
								CONCAT_WS(', ',usSA.lname,usSA.fname) as saPhy,
								fac.name as saFac,
								CONCAT(pd.lname,', ',pd.fname) as patName, sa.sa_patient_id as patIdSA,
								insComp.name as insCompName, rtme.responce_pat_policy_no as elPatPolicyNo, 
								insComp1.name as insCompNamePat, insData1.policy_number as insPatPolicy,
								rtme.response_deductible as rteDCE, rtme.response_copay as rteCopay, rtme.response_co_insurance as rteCoins,
								rtme.EB_responce as elResponce, rtme.transection_error as tranError,
								rtme.eligibility_ask_from  as elAsk

								from schedule_appointments sa 
								LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid
								LEFT JOIN real_time_medicare_eligibility rtme ON sa.rte_id = rtme.id
								LEFT JOIN users us ON us.id = rtme.request_operator  
								LEFT JOIN users usSA ON usSA.id = sa.sa_doctor_id   
								LEFT JOIN facility fac ON fac.id = sa.sa_facility_id   
								LEFT JOIN patient_data pd on pd.id = sa.sa_patient_id 
								LEFT JOIN insurance_data insData ON (insData.id = rtme.ins_data_id AND insData.actInsComp='1') 
								LEFT JOIN insurance_data insData1 ON (insData1.pid = sa.sa_patient_id AND insData.actInsComp='1') 
								LEFT JOIN insurance_companies insComp ON insComp.id = insData.provider 
								LEFT JOIN insurance_companies insComp1 ON insComp1.id = insData1.provider 
								where sa.sa_doctor_id in (".$rqPhyId.") and sa.sa_facility_id in (".$rqFacId.") and sa.sa_app_start_date = '".$rqDated."'
								AND insData1.actInsComp = '1' AND insData1.type = 'primary'
								AND insData1.ins_caseid > 0 
								AND sa.sa_patient_app_status_id NOT IN(201,18,19,20,203)
								order by sa.sa_app_start_date, sa.sa_app_starttime
								";

						$rsGetPatEl = imw_query($qryGetPatEl);
						if($rsGetPatEl){
							$intTotRec = imw_num_rows($rsGetPatEl);
							if($intTotRec > 0){
								while($rowGetPatEl = imw_fetch_array($rsGetPatEl)){
									$dbElTime = $dbElOpName = $dbElPatPolicyNo = $dbElDDE = $dbElCopay = $dbElCoins = $dbElResponce = $dbElTranError = "";
									$dbSAPhy = $dbSAFac = $dbPatName = $dbPatID = $dbPatInsCompName = $strEBResponce = $dbInsPatCompName = $dbInsPatPolicyNo = "";
									$dbRTEID = $dbPatAppProc = "";
									$rteChk = "No";

									$dbRTEID = $rowGetPatEl["patRTEId"];
									if(empty($dbRTEID) == false){
										$rteChk = "Yes";
									}
									$dbPatAppProc = $rowGetPatEl["patAppProc"];
									$dbElTime = $rowGetPatEl["saTime"];
									$dbElOpName = $rowGetPatEl["elOpName"];
									$dbSAPhy = $rowGetPatEl["saPhy"];
									$dbSAFac = $rowGetPatEl["saFac"];
									$dbPatName = $rowGetPatEl["patName"];
									$dbPatID = $rowGetPatEl["patIdSA"];
									$dbPatInsCompName = $rowGetPatEl["insCompName"];
									$dbElPatPolicyNo = $rowGetPatEl["elPatPolicyNo"];
									$dbElDDE = $rowGetPatEl["rteDCE"];
									$dbElCopay = $rowGetPatEl["rteCopay"];
									$dbElCoins = $rowGetPatEl["rteCoins"];

									$dbInsPatCompName = $rowGetPatEl["insCompNamePat"];
									$dbInsPatPolicyNo = $rowGetPatEl["insPatPolicy"];

									$arrRespDDC = $arrRespCopay = $arrRespCoIns = array();
									$strDDCAmt = $strCopayAmt = $strCoInsAmt = "";

									$arrRespDDC = explode("-", $dbElDDE);
									$strDDCAmt = $arrRespDDC[4];
									if(empty($strDDCAmt) == false){
										$strDDCAmt = "$".$strDDCAmt;
									}
									elseif(empty($strDDCAmt) == true){
										$strDDCAmt = "N/A";
									}

									$arrRespCopay = explode("-", $dbElCopay);
									$strCopayAmt = (float)$arrRespCopay[4];
									if(empty($strCopayAmt) == false){
										$strCopayAmt = "$".$strCopayAmt;
									}
									elseif(empty($strCopayAmt) == true){
										$strCopayAmt = "N/A";
									}

									$arrRespCoIns = explode("-", $dbElCoins);
									$strCoInsAmt = $arrRespCoIns[6];
									if(empty($strCoInsAmt) == false){
										$strCoInsAmt = $strCoInsAmt."%";
									}
									elseif(empty($strCoInsAmt) == true){
										$strCoInsAmt = "N/A";
									}

									$dbElResponce = $rowGetPatEl["elResponce"];
									$dbElTranError = $rowGetPatEl["tranError"];
									$tdColor = $strTitle = "";
									if((($dbElResponce == "6") || ($dbElResponce == "7") || ($dbElResponce == "8") || ($dbElResponce == "V")) && (empty($dbElTranError) == true)){
										$strEBResponce = "Inactive";
										$tdColor = "style=\"color:red; font-weight:bold;\"";
									}
									elseif((empty($dbElResponce) == false) && (empty($dbElTranError) == true)){
										$strEBResponce = "Active";
										$tdColor = "style=\"color:green; font-weight:bold;\"";
									}
									elseif(empty($dbElTranError) == false){
										$strEBResponce = "Error";
										$tdColor = "style=\"color:black; font-weight:bold;\"";
										$strTitle = "title=\"".$dbElTranError."\"";
									}
									?>
									<tr>
										<td><?php echo $dbElTime; ?></td>
										<td><?php echo $dbPatAppProc; ?></td>
										<td><?php echo $rteChk; ?></td>
										<td><?php echo $dbElOpName; ?></td>
										<td><?php echo $dbSAPhy; ?></td>
										<td><?php echo $dbSAFac; ?></td>
										<td><?php echo $dbPatName."&nbsp;-&nbsp;".$dbPatID; ?></td>
										<td>
											<?php 
												if((empty($dbPatInsCompName) == false) && (empty($dbElPatPolicyNo) == false)){
													echo $dbPatInsCompName."<br>".$dbElPatPolicyNo; 
												}
												elseif((empty($dbInsPatCompName) == false) && (empty($dbInsPatPolicyNo) == false)){
													echo $dbInsPatCompName."<br>".$dbInsPatPolicyNo; 
												}
											?>
										</td>					
										<td><?php echo $strCopayAmt." / ".$strCoInsAmt." / ".$strDDCAmt; ?></td>
										<td <?php echo $strTitle; echo $tdColor; ?>><?php echo $strEBResponce; ?></td>
									</tr>
									<?php
									$intTempCounter++;										
								}
							}
							else{
								?>
								<tr>
									<td colspan="10">
										No Patient Eligibility History Exist!
									</td>
								</tr>
								<?php
							}
						}
					}
					?>
				</tbody>
				</table>
			</div>
			 
             <div class="row">   
                <div class="col-sm-8 text-left">
				<div class="bgcolor"><span style="vertical-align:super;">*</span>Indicating these are the default values which are gather in request and might not be set by the user!</div>	 
               </div>   	
                <div class="col-sm-4 text-right">
                    <?php 
				if($intTotRec > 0){
					?>
				    <button type="button" class="btn btn-default" name="btStPro" id="btStPro" onClick="printEligibilityFile('<?php echo $rqPhyId; ?>', '<?php echo $rqFacId; ?>', '<?php echo $rqDated; ?>');">Print</button><?php 
				}
				?>
                    <button type="button" class="btn btn-danger" name="btClose" id="btClose" onClick="window.close();">Close</button>
                </div>   	
             </div>
             </div>
    </body>
    <script language="javascript">
		on_load();
	</script>
</html>