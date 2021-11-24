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
Purpose: save schedule of provider
Access Type: Direct
*/
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
//file_put_contents('test.txt',print_r($_REQUEST,true));exit;
//---- Schedular apointments for provider aleardy exists check ----------
$Start_date_Arr = preg_split('/,/',$_REQUEST['Start_date_String']);
array_pop($Start_date_Arr);
$Start_date_Arr = array_values($Start_date_Arr);
$sel_pro = $_REQUEST['sel_pro'];
$Template_Label_Arr = preg_split('/<>/',$_REQUEST['sel_schedule_name']);
$sel_facility = $_REQUEST['sel_facility'];
$repeat = false;
$wrdata_params_flag = count($Start_date_Arr);
for($d=0;$d<count($Start_date_Arr);$d++){
	if(strtolower(substr(trim($GLOBALS['date_format']),0,2))=="dd"){// Internation Date Formating 
		list($d,$m,$y) = preg_split('/-/',$Start_date_Arr[$d]);
	}else{
		list($m,$d,$y) = preg_split('/-/',$Start_date_Arr[$d]);
	}
	$cm1 = $cm2 = 0;
	$wrdata_params_ms = '';
	$last_day_t = $_REQUEST['last_day_t'];
	if($d > $last_day_t)
	{
		$cm2 = date('N',mktime(0,0,0,$m,$d,$y));
		$cm1 = ceil($d/7);
		$wrdata_params_ms = $cm1.'|'.$cm2;
		$d = $last_day_t;
	}
	
	$Start_date = $y.'-'.$m.'-'.$d;	
	$wrdata_params = trim($_REQUEST['wrdata_params']);		
	if($wrdata_params != "" && $wrdata_params_flag == 1)
	{
		$schTmpArr = getSchTmpData($Start_date,$sel_pro,$wrdata_params);	
	}
	else
	{
		$schTmpArr = getSchTmpData($Start_date,$sel_pro,$wrdata_params_ms);	
	}
	$week = ceil($d/7);
	if($cm1 != 0) { $week = $cm1; }
	foreach($schTmpArr as $schTmpArrChild)
	{
		$facility = $schTmpArrChild['facility'];
		$provider = $schTmpArrChild['provider'];
		$sch_tmp_id = trim($schTmpArrChild['sch_tmp_id']);		
		if($provider == $sel_pro && $facility == $sel_facility && $sch_tmp_id == $Template_Label_Arr[0]){
			$repeat = true;
		}
	}
}
print $week.'__'.$repeat;
?>