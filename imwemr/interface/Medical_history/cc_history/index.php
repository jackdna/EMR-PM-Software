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
include_once($GLOBALS['srcdir']."/classes/medical_hx/cc_history.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");

$cc_hx = new CC_Hx($medical->current_tab);
$pid = $cc_hx->patient_id;

//--- POLICY STATUS FOR AUDIT TRAIL ----
$policyStatus = $cc_hx->policy_status;

//--- SET CC HISTORY DATA ----
$cc_data = $cc_hx->get_cc_data();


//patient id Audit Trail array 
$pkIdAuditTrailArr = $cc_hx->pat_id_audit_trail;

//--- SET DIV HEIGHT ----
$divHeight= $GLOBALS['gl_browser_name']=='ipad' ? $_SESSION["wn_height"] - 62 : $_SESSION['wn_height']-270;

if(is_array($_SESSION['Patient_Viewed']) && $policyStatus == 1 and count($pkIdAuditTrailArr) > 0 && isset($_SESSION['Patient_Viewed']) === true){
	$opreaterId = $_SESSION["authId"];												 
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$arrAuditTrailView_CCHX = array();
	$arrAuditTrailView_CCHX[0]['Pk_Id'] = $pkIdAuditTrailArr[0];
	$arrAuditTrailView_CCHX[0]['Table_Name'] = 'chart_left_cc_history';
	$arrAuditTrailView_CCHX[0]['Action'] = 'view';
	$arrAuditTrailView_CCHX[0]['Operater_Id'] = $opreaterId;
	$arrAuditTrailView_CCHX[0]['Operater_Type'] = getOperaterType($opreaterId);
	$arrAuditTrailView_CCHX[0]['IP'] = $ip;
	$arrAuditTrailView_CCHX[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_CCHX[0]['URL'] = $URL;
	$arrAuditTrailView_CCHX[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_CCHX[0]['OS'] = $os;
	$arrAuditTrailView_CCHX[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_CCHX[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_CCHX[0]['Filed_Label'] = 'Patient CC & HX Data';
	$arrAuditTrailView_CCHX[0]['Category_Desc'] = 'CC_HX';
	$arrAuditTrailView_CCHX[0]['Old_Value'] = join('-',$pkIdAuditTrailArr).'-';
	$arrAuditTrailView_CCHX[0]['pid'] = $pid;
								
	$patientViewed = $_SESSION['Patient_Viewed'];
	if(is_array($patientViewed) && $patientViewed["Medical History"]["CC_HX"] == 0){
		auditTrail($arrAuditTrailView_CCHX,$mergedArray,0,0,0);
		$patientViewed["Medical History"]["CC_HX"] = 1;
		$_SESSION['Patient_Viewed'] = $patientViewed;
	}
}

//--- ALERTS BY RAVI MANTRA ---
if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
	echo $cc_hx->set_cls_alerts();
}
?>
<div class="ml10">
	<div class="table-responsive">
		<table class="table table-striped table-bordered">
			 <tr class="grythead">
				<th class="text-nowrap">DOS</th>
				<th class="text-nowrap">Chief Complaint</th>
				<th>History</th>
			</tr>
			<?php
				if(count($cc_data) > 0){
					foreach($cc_data as $obj){
					?>
						<tr>
							<td style="width:16%; vertical-align:top!important;"><?php echo $obj['DOS']; ?></td>
							<td style="width:42%; vertical-align:top!important;"><?php echo $obj['CC']; ?></td>
							<td style="width:42%; vertical-align:top!important;"><?php echo $obj['History']; ?></td>
						</tr>
					<?php	
					}
				}else{
				?>
				<tr>
					<td colspan="3" class="text-center"> 	
						No record found
					</td>
				</tr>
				<?php
				}
			?>	
		</table>	
	</div>	
</div>
<script>
	self.focus();		
	top.show_loading_image("hide");
</script>
</body>
</html>