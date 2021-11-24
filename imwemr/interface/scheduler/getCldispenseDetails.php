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
$EncounterIDForCLRecieved=0;

$clQry = "Select print_order_id FROM clprintorder_master WHERE patient_id = '".$_SESSION['patient']."' ORDER BY print_order_id DESC LIMIT 0,1";
	$clRs=imw_query($clQry) or die(imw_error());
	$clRes = imw_fetch_array($clRs);
	$print_order_id = $clRes['print_order_id'];

if($print_order_id>0){
$EncounterForCLSupply='select encounter_id from patient_charge_list where del_status="0" and patient_id="'.$_SESSION['patient'].'" and cl_print_ord_id="'.$print_order_id.'"';
$EncounterForCLSupplyRes=imw_query($EncounterForCLSupply);
	if($EncounterForCLSupplyRes){
			$numRowsEnc=imw_num_rows($EncounterForCLSupplyRes);
			if($numRowsEnc>0){
					$EncounterForCLSupplyResRow=imw_fetch_assoc($EncounterForCLSupplyRes);
					$EncounterIDForCLRecieved=$EncounterForCLSupplyResRow["encounter_id"];
					$_SESSION["encounterIdFromCL"]=$EncounterIDForCLRecieved;
				}
	   }
}
if($EncounterIDForCLRecieved>0){
	echo $print_order_id;
}
?>