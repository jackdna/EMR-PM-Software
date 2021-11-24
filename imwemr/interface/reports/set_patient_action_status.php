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
FILE : set_pateint_action_status.php
PURPOSE : Set patient status requested by Collection report.
ACCESS TYPE : Indirect
*/

//Global File
$without_pat = "yes";
require_once("reports_header.php");
$saved=0;
$patId_str = $_REQUEST['patId_str'];
$actionCode = $_REQUEST['actionCode'];

// SET VALUES
if($patId_str!=''){
	$qry="Update patient_data SET next_action_status ='".$actionCode."' WHERE id IN(".$patId_str.")";
	$rs=imw_query($qry);
	$saved=1;
	
	// ADD RECORD IN PATIENT NEXT HISTORY ACTION TABLE
	$selRs=imw_query("Select id, next_action_status FROM patient_data WHERE id IN(".$patId_str.")");
	while($selRes = imw_fetch_array($selRs)){
		$rs = imw_query("Insert into patient_next_action_history SET 
		patient_id ='".$selRes['id']."',
		user_id ='".$_SESSION['authId']."',
		action_code  ='".$actionCode."',
		change_date ='".date('Y-m-d')."',
		change_time ='".date('H:i:s')."'
		");
	}
}
//--------------
echo $saved;
?>
