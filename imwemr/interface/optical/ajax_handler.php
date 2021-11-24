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

require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once('../../library/classes/common_function.php');

require_once($GLOBALS['srcdir'].'/classes/cls_common_function.php');
$cls_object = New CLSCommonFunction;

require_once('../../library/classes/optical_class.php');

$pid = $_SESSION['patient'];
$auth_id = $_SESSION['authId'];
$optical_obj = New Optical($pid,$auth_id);

if(isset($_REQUEST['ajax_request'])){
	if(isset($_REQUEST['update_order_id']) && isset($_REQUEST['update_order_status'])){
		$return_val = 0;
		//Update records
		$orderUpQuery= "update clprintorder_master set order_status='".trim($_REQUEST["update_order_status"])."' WHERE patient_id='".$optical_obj->patient_id."' and print_order_id='".$_REQUEST["update_order_id"]."' ";
		$orderHxRes = imw_query($orderUpQuery) or die(imw_error());
		if($orderHxRes){
			$return_val = '1';
		}
		echo $return_val;
	}
	
	//Get CPT Cost
	if(isset($_REQUEST['get_cpt_cost'])){
		$return_val = $optical_obj->get_cpt_cost($_REQUEST);
		echo $return_val;	
	}
	
	//Get Frames data
	if(isset($_REQUEST['vendor_name_val'])){
		$frame_data = $optical_obj->get_frame_data($_REQUEST);
		echo $frame_data;
	}
	
	//Get Frames dropdown
	if(isset($_REQUEST['manf_name']) && isset($_REQUEST['cptCode'])){
		$dropdown = $optical_obj->get_frame_dropdown($_REQUEST);
		echo $dropdown;
	}
	
	exit();
}
?>