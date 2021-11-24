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
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html
$rqPhyId = $_REQUEST['phyId'];
$rqFacId = $_REQUEST['facId'];
$rqDated = $_REQUEST['dated'];
list($year, $month, $day) = explode("-", $rqDated);
ob_start();
?>
<style>
	.text_b_w{
		font-size:11px;
		font-family:Arial, Helvetica, sans-serif;
		color:#FFFFFF;
		background-color:#4684ab;
	}
	.text_10b{
		font-size:11px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		background-color:#FFFFFF;
	}
	.text_10{
		font-size:11px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#FFFFFF;
	}
	.bgcolor{	
		background-color:#FFFFFF;
	}
</style>
<page backtop="10mm" backbottom="10mm">	
<page_header>
<table style="width:100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td style="width:100%" class="text_b_w">
        	<b>Eligibility Detail for: <?php echo $month."-".$day."-".$year; ?></b>
        </td>
    </tr>
    <tr><td style="width:100%; height:1px;"></td></tr>
</table>
<table style="width:100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td style="width:25px" class="text_b_w">#</td>
        <td style="width:65px" class="text_b_w">Appt. Time</td>
        <td style="width:85px" class="text_b_w">Appt. Proc</td>
        <td style="width:60px" class="text_b_w">RTA Check</td>
        <td style="width:40px" class="text_b_w">Opr</td>
        <td style="width:130px" class="text_b_w">Physician Name</td>
        <td style="width:100px" class="text_b_w">Location</td>
        <td style="width:180px" class="text_b_w">Patient Name ID</td>
        <td style="width:170px" class="text_b_w">Ins Practice Name Policy #</td>
        <td style="width:110px" class="text_b_w">*CoPay/Co-Ins/Ded</td>
        <td style="width:75px" class="text_b_w">Status</td>
    </tr>
</table>
</page_header>
<page_footer>
    <table style="width:100%;">
    	<tr>
            <td style="text-align:left;width:100%">
            	*Indicating these are the default values which are gather in request and might not be set by the user!
            </td>
        </tr>
        <tr>
            <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
        </tr>
    </table>
</page_footer>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
$intTempCounter = 1;
$qryGetPatEl = "select 
		sa.id as patAppIdSA,  sa.rte_id as patRTEId , sp.proc as patAppProc , 
		DATE_FORMAT(sa.sa_app_start_date, '%m-%d-%y') as saDate, DATE_FORMAT(sa.sa_app_starttime, '%h:%i %p') as saTime,
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
		LEFT JOIN insurance_data insData ON insData.id = rtme.ins_data_id													
		LEFT JOIN insurance_companies insComp ON insComp.id = insData.provider
		LEFT JOIN insurance_data insData1 ON insData1.pid = sa.sa_patient_id 
		LEFT JOIN insurance_companies insComp1 ON insComp1.id = insData1.provider
		
		where sa.sa_doctor_id in (".$rqPhyId.") and sa.sa_facility_id in (".$rqFacId.") and sa.sa_app_start_date = '".$rqDated."'
		AND insData1.actInsComp = '1' AND insData1.type = 'primary'
		AND insData1.ins_caseid > 0 
		AND sa.sa_patient_app_status_id NOT IN(201,18,19,20,203)
		order by sa.sa_app_start_date, sa.sa_app_starttime
		";
	$rsGetPatEl = imw_query($qryGetPatEl);
	if($rsGetPatEl){
		if(imw_num_rows($rsGetPatEl) > 0){
			if(imw_num_rows($rsGetPatEl) > 0){
				while($rowGetPatEl = imw_fetch_array($rsGetPatEl)){
					$classTr = ($intTempCounter % 2) == 0 ? "bgcolor" : "";
					$dbElDate = $dbElTime = $dbElOpName = $dbElPatPolicyNo = $dbElDDE = $dbElCopay = $dbElCoins = $dbElResponce = $dbElTranError = "";
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
					$tdColor = "";
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
					}
					?>
                    <tr class="<?php echo $classTr; ?>">
                        <td style="width:25px" ><?php echo $intTempCounter; ?></td>
                        <td style="width:65px"><?php echo $dbElTime; ?></td>
                        <td style="width:85px" ><?php echo $dbPatAppProc; ?></td>
                        <td style="width:60px"><?php echo $rteChk; ?></td>
                        <td style="width:40px"><?php echo $dbElOpName; ?></td>
                        <td style="width:130px"><?php echo $dbSAPhy; ?></td>
                        <td style="width:100px"><?php echo $dbSAFac; ?></td>
                        <td style="width:180px"><?php echo $dbPatName."&nbsp;-&nbsp;".$dbPatID; ?></td>
                        <td style="width:170px">
                            <?php 
                                if((empty($dbPatInsCompName) == false) && (empty($dbElPatPolicyNo) == false)){
                                    echo $dbPatInsCompName."<br>".$dbElPatPolicyNo; 
                                }
                                elseif((empty($dbInsPatCompName) == false) && (empty($dbInsPatPolicyNo) == false)){
                                    echo $dbInsPatCompName."<br>".$dbInsPatPolicyNo; 
                                }
                            ?>
                        </td>					
                        <td style="width:110px"><?php echo $strCopayAmt." / ".$strCoInsAmt." / ".$strDDCAmt; ?></td>
                        <td style="width:75px" <?php echo $tdColor; ?>><?php echo $strEBResponce; ?></td>
                    </tr>
                    <?php
					$intTempCounter++;
				}
			}
		}
		imw_free_result($rsGetPatEl);
	}
?>
</table>
</page>
<?php
$erFileData = ob_get_contents();
ob_end_clean();
$file_location = write_html($erFileData);
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	top.html_to_pdf('<?php echo $file_location; ?>','l','',true);
</script>
<!--
<form name="frmPrint" action="../common/new_html2pdf/createPdf.php" method="get">
	<input type="hidden" name="op" value="l"/>
    <input type="hidden" name="onePage" value="false"/>
</form>
<script>
document.frmPrint.submit();
</script>-->