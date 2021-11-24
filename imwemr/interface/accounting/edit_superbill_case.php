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
include_once("../main/Functions.php");
$objManageData = new ManageData;
include_once("../main/classObjectFunction.php");
$objectManage = new manageImedicData;
$operatorName = $_SESSION['authUser'];
$patient_id = $_SESSION['patient'];
$operatorId = $_SESSION['authUserID'];

$sup_id = $_REQUEST['sup_id'];
$insSelected = $_REQUEST['insSelected'];
if($insSelected !=""){
	$ins_case_id_arr = explode('-',$insSelected);
	$insCaseId = end($ins_case_id_arr);

	$arrayRecord['insuranceCaseId'] = $insCaseId;
	$objectManage->updateRecords($arrayRecord, 'superbill', 'idSuperBill', $sup_id);
}
?>