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

include("../../../config/globals.php");
require_once(dirname(__FILE__) . "/../../../library/classes/audit_common_function.php");
$id = $_REQUEST['id'];
$auth_id = $_SESSION['authId'];
######Audit For Patient Export PHI###############
$plPHI = 0;
$plPHI = (int) $_SESSION['AUDIT_POLICIES']['PHI_Export'];


if ($plPHI == 1) {
	$arrAuditTrailPHI = array();
	$opreaterId = $_SESSION['authId'];
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" . $browserInfoArr['version'];
	$browserName = str_replace(";", "", $browserInfo);
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);

	$sel = imw_query("select p_id,id,name from document_patient_rel where id=$id");
	while ($row = imw_fetch_array($sel)) {
		$p_id = $row['p_id'];
		$rid = $row['id'];
		$rName = $row['name'];
		$arrAuditTrailPHI [] = array(
					"Pk_Id" => $rid,
					"Table_Name" => "document_patient_rel",
					"Action" => "phi_export",
					"Operater_Id" => $opreaterId,
					"Operater_Type" => getOperaterType($opreaterId),
					"IP" => $ip,
					"MAC_Address" => $_REQUEST['macaddrs'],
					"URL" => $URL,
					"Browser_Type" => $browserName,
					"OS" => $os,
					"Machine_Name" => $machineName,
					"pid" => $_SESSION['patient'],
					"Category" => "chart_notes",
					"Category_Desc" => "pt_instructions",
					"Old_Value" => $p_id,
					"Depend_Select" => "select CONCAT(CONCAT_WS(', ',lname,fname),'(',id,')') as patientName",
					"Depend_Table" => "patient_data",
					"Depend_Search" => "id",
					"New_Value" => $rName
		);
	}


	$table = array("audit_policies");
	$error = array($phiError);
	$mergedArray = merging_array($table, $error);
	auditTrail($arrAuditTrailPHI, $mergedArray, 0, 0, 0);
}
##############################################
?>