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

//inserting / updateing new note
if(isset($_REQUEST["act_id"]) && $_REQUEST["act_id"] > 0){
	$qry_init = "UPDATE provider_notes ";
	$qry_cond = " WHERE provider_notes_id = '".$_REQUEST["act_id"]."' ";
}else{
	//deleting cache for this day
	$file_name=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml/".$_REQUEST["note_date"]."-".$_REQUEST["prov_id"].".sch";
	if(file_exists($file_name)){
		unlink($file_name);
	}
	$qry_init = "INSERT INTO provider_notes ";
	$qry_cond = "";
}
$qry = $qry_init." SET provider_notes = '".addslashes($_REQUEST["notes"])."', provider_id = '".$_REQUEST["prov_id"]."', notes_date = '".$_REQUEST["note_date"]."', operator_id  = '".$_SESSION["authId"]."', enter_date  = '".date("Y-m-d H:i:s")."' ".$qry_cond;
imw_query($qry);

//getting lates count
$disp_cnt = 0;
$qry0 = "SELECT count(*) as rowCnt FROM provider_notes pn WHERE pn.provider_id = '".$_REQUEST["prov_id"]."' AND pn.notes_date = '".$_REQUEST["note_date"]."' AND pn.delete_status = 0";
$res0 = imw_query($qry0);
if(imw_num_rows($res0) > 0){
	$arr0 = imw_fetch_assoc($res0);
	$disp_cnt = $arr0["rowCnt"];
}
echo $disp_cnt;
?>