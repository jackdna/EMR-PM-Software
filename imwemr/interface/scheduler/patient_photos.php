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
//require_once($GLOBALS['fileroot'].'/library/classes/billing_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

//scheduler object
$obj_scheduler = new appt_scheduler();

$imagePath = '';

if(isset($_REQUEST["path"]) && !empty($_REQUEST["path"])){
	$imagePath = $_REQUEST["path"];
	$path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$imagePath;
}else{
	$qry = "SELECT p_imagename FROM `patient_data` where id = '".$_REQUEST["pat_id"]."'";
	$res = imw_query($qry);

	if(imw_num_rows($res) > 0){
		$arr = imw_fetch_assoc($res);
		$imagePath = $arr["p_imagename"];
		$path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$imagePath;
	}
}

if(file_exists($path)){
	print $obj_scheduler->show_image_thumb($imagePath, 116, 116);
}
?>