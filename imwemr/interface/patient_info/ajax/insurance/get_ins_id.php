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
File: get_ins_id.php
Purpose: Get insurance companies of insurance case
Access Type: Direct 
*/
require_once("../../../../config/globals.php");
//require_once('../../common/functions.inc.php');
//$objManageData = new DataManage;
$caseId = $_REQUEST['caseId'];
$patient_id = $_SESSION['patient'];

//--- GET INSURANCE COMPANY OF SINGLE INSURANCE CASE --------
$qry = "select insurance_data.id as insurance_data_id, insurance_data.type,
		insurance_companies.in_house_code,insurance_companies.name
		from insurance_data left join insurance_companies
		on insurance_companies.id = insurance_data.provider 
		where insurance_data.ins_caseid = '$caseId' 
		and insurance_data.pid = '$patient_id' and insurance_data.actInsComp = '1'
		order by insurance_data.type";
$insQryRes = get_array_records_query($qry);
//$optionsData = '<select name="copy_ins_data_from" class="input_text_10" style="width:300px;">';
$optionsData = '';
for($n=0;$n<count($insQryRes);$n++){
	$insurance_data_id = $insQryRes[$n]['insurance_data_id'];
	$type = $insQryRes[$n]['type'];
	$in_house_code = $insQryRes[$n]['in_house_code'];
	if(trim($in_house_code) == ''){
		$in_house_code = substr($insQryRes[$n]['in_house_code'],0,8);
	}
	$insurance_data_val = $type.'__'.$insurance_data_id;
	$type = ucfirst($type);
	
	$optionsData .= <<<DATA
		<option value="$insurance_data_val">$type - $in_house_code</option>
DATA;
}
//$optionsData .= '</select>';
print $optionsData;
?>