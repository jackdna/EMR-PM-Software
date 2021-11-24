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
Purpose: Get timings for selected template
Access Type: Included
*/
require_once(dirname(__FILE__).'/../../config/globals.php');

$result = "";
if(isset($_REQUEST["tmp_id"]) && !empty($_REQUEST["tmp_id"])){
	$qry = "SELECT TIME_FORMAT(morning_start_time, '%h;;%m;;%p') as morning_start_time, TIME_FORMAT(morning_end_time, '%h;;%m;;%p') as morning_end_time FROM schedule_templates WHERE id = '".$_REQUEST["tmp_id"]."'";
	$res = imw_query($qry);
	
	if(imw_num_rows($res) > 0){
		$arr = imw_fetch_assoc($res);	
		$arr_st = explode(";;", $arr["morning_start_time"]);
		$arr_ed= explode(";;", $arr["morning_end_time"]);

		$st_hr = ((int)$arr_st[0] < 10) ? "0".(int)$arr_st[0] : (int)$arr_st[0];
		$st_mn = ((int)$arr_st[1] < 10) ? "0".(int)$arr_st[1] : (int)$arr_st[1];
		$ed_hr = ((int)$arr_ed[0] < 10) ? "0".(int)$arr_ed[0] : (int)$arr_ed[0];
		$ed_mn = ((int)$arr_ed[1] < 10) ? "0".(int)$arr_ed[1] : (int)$arr_ed[1];

		$result = $st_hr."~".$st_mn."~".strtoupper($arr_st[2])."~".$ed_hr."~".$ed_mn."~".strtoupper($arr_ed[2]);
	}
}
echo $result;
?>