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

//getting next 2 months from the current month
$loaded_month = $_REQUEST["loaded_month"];
list($lm_y, $lm_m, $lm_d) = explode("-", $loaded_month);
//echo "$lm_y, $lm_m, $lm_d";
if($lm_m == 11){
	$nx_m = date("m", mktime(0, 0, 0, (int)$lm_m + 1, 1, (int)$lm_y));
	$nx2_m = date("m", mktime(0, 0, 0, (int)$lm_m + 2, 1, (int)$lm_y + 1));
	$nx_y = (int)$lm_y;
	$nx2_y = (int)$lm_y + 1;
}else if($lm_m == 12){
	$nx_m = date("m", mktime(0, 0, 0, (int)$lm_m + 1, 1, (int)$lm_y + 1));
	$nx2_m = date("m", mktime(0, 0, 0, (int)$lm_m + 2, 1, (int)$lm_y + 1));
	$nx_y = (int)$lm_y + 1;
	$nx2_y = (int)$lm_y + 1;
}else{
	$nx_m = date("m", mktime(0, 0, 0, (int)$lm_m + 1, 1, (int)$lm_y));
	$nx2_m = date("m", mktime(0, 0, 0, (int)$lm_m + 2, 1, (int)$lm_y));
	$nx_y = (int)$lm_y;
	$nx2_y = (int)$lm_y;
}
//echo "$nx_y, $nx_m, $lm_d";
//echo "$nx2_y, $nx2_m, $lm_d";
$str_response = "";

//this month
$stts = mktime(0, 0, 0, (int)$lm_m, (int)$lm_d, (int)$lm_y);
$edtsdt = date("t", $stts);
$edts = mktime(0, 0, 0, (int)$lm_m, $edtsdt, (int)$lm_y);

for($stval = $stts; $stval <= $edts; $stval = $stval + 86400){
	$working_this_dt = date("Y-m-d", $stval);
	$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
	$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
	$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
	$str_response .= "";
	$str_response .= ":~:~:";
}

//next month
$stnxts = mktime(0, 0, 0, (int)$nx_m, (int)$lm_d, (int)$nx_y);
$ednxtsdt = date("t", $stnxts);
$ednxts = mktime(0, 0, 0, (int)$nx_m, $ednxtsdt, (int)$nx_y);

for($stval = $stnxts; $stval <= $ednxts; $stval = $stval + 86400){
	$working_this_dt = date("Y-m-d", $stval);
	$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
	$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
	$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
	$str_response .= "";
	$str_response .= ":~:~:";
}

//next 2 next month
$stnx2ts = mktime(0, 0, 0, (int)$nx2_m, (int)$lm_d, (int)$nx2_y);
$ednx2tsdt = date("t", $stnx2ts);
$ednx2ts = mktime(0, 0, 0, (int)$nx2_m, $ednx2tsdt, (int)$nx2_y);

for($stval = $stnx2ts; $stval <= $ednx2ts; $stval = $stval + 86400){
	$working_this_dt = date("Y-m-d", $stval);
	$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
	$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
	$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
	$str_response .= "";
	$str_response .= ":~:~:";
}
echo $str_response;
?>