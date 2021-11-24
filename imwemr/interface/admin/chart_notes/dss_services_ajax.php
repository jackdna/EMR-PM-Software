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
require_once(dirname(__FILE__)."/../../../config/globals.php");
include_once(dirname(__FILE__).'/../../../library/classes/common_function.php');
$do 		= isset($_REQUEST['do']) ? trim($_REQUEST['do']) : '';

switch ($do) {
	case 'dssServiceSpeciality':
		try {
			include_once( $GLOBALS['srcdir'].'/dss_api/dss_enc_visit_notes.php' );
			$obj = new Dss_enc_visit_notes();
			$res = $obj->ConsultGetServiceSpecialtyList();
			$option = '';
			foreach ($res as $key => $service) {
				foreach ($service as $key => $opt) {
					if($key == 'svcIen') $svcIen = $opt;
					if($key == 'svcName') $svcName = $opt;
					if($key == 'orderableItem') $orderableItem = $opt;
				}
				$option .= '<option value="'.$svcIen.'" data-orderableItem="'.$orderableItem.'" data-svcName="'.$svcName.'">'.$svcName.'</option>';
			}
			echo $option;
			exit;
		} catch (Exception $e) {
			echo $e->getMessage();
		}	
	break;

	case 'dssSaveData':

		$id = $_REQUEST['id'];
		$test_id = $_REQUEST['test_id'];
		$service_ien = $_REQUEST['svcIen'];
		$service_name = $_REQUEST['svcName'];
		$service_orderable_item = $_REQUEST['orderableItem'];

		$checkId = '';
		if($id != '') {
			$checkId = " AND id != '$id'";
		}
		// die($checkId);

		$sql_check = "SELECT id FROM `dss_test_services` WHERE `status` = 0 AND (`test_id` = '$test_id' OR `service_ien` = '$service_ien')".$checkId;

		// pre($sql_check);

		$sqlResult = imw_query($sql_check);
		if(imw_num_rows($sqlResult) > 0) {
			echo json_encode(array('status' => 'error', 'msg' => 'Test is already linked with other Service.'));
			exit;
		}

		$sql = '';
		$where = '';
		$postData = "`test_id` = '$test_id', `service_ien` = '$service_ien', `service_name` = '$service_name', `service_orderable_item` = '$service_orderable_item'";

		if($id == '') {
	 		$sql = "INSERT INTO `dss_test_services` SET `created_at` = '".date('Y-m-d H:i:s')."', ";
	 		$where = '';
		} else {
			$sql = "UPDATE `dss_test_services` SET `modified_at` = '".date('Y-m-d H:i:s')."', ";
			$where = " WHERE `id` = ".$_REQUEST['id'];
		}

		$sqlQuery = $sql.$postData.$where;

		imw_query($sqlQuery) or die('Unable to save record.');
		echo json_encode(array('status' => 'success'));
		exit;
	break;

	case 'dssDelData':
		$id = $_REQUEST['id'];
		$sqlQuery = "UPDATE `dss_test_services` SET `status` = 1 WHERE `id` = ".$id;
		imw_query($sqlQuery);
		echo json_encode(array('status' => 'success'));
	break;
}
?>