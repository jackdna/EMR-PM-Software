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
File: confidential_text_detail.php
Purpose: This file provide a list of all confidential information in Patient Confidential Information section.
Access Type : Direct
*/
?>
<?php
include_once(dirname(__FILE__)."/../../config/globals.php");

$str_log_qry ="SELECT *, pcta.action_performed, DATE_FORMAT(pcta.action_performed_on,'".get_sql_date_format()." %h:%I %p') as date_and_time, u.fname, u.lname FROM patient_confidential_text_access pcta LEFT JOIN users u  ON u.id = pcta.action_performed_by WHERE pcta.id = '".$_REQUEST['t_id']."' ORDER BY action_performed_on DESC";
$rs_log = imw_query($str_log_qry);
if(imw_num_rows($rs_log) > 0){
	$cnt = 1;
	while($arr_log = imw_fetch_array($rs_log)){
		$str_prov_name = ucfirst($arr_log["lname"]);
		if($arr_log["fname"] != ""){
			$str_prov_name .= ", ".ucfirst($arr_log["fname"]);
		}
		switch($arr_log["action_performed"]){
			case "VIEWED":
				$str_activity .= '
				
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12 purple_bar mb5">
							<div class="col-sm-4"><label>Viewed information</label></div>
							<div class="col-sm-4">'.$str_prov_name.'</div>
							<div class="col-sm-4 text-right">'.$arr_log["date_and_time"].'</div>
						</div>
					</div>
				</div>
				
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12 border">
							<strong>'.$arr_log["view_reason"].'</strong>
						</div>
					</div>
				</div>';
			break;
			case "ADDED":
				$str_activity .= '<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12 purple_bar mb5">
							<div class="col-sm-4"><label>Added information</label></div>
							<div class="col-sm-4">'.$str_prov_name.'</div>
							<div class="col-sm-4 text-right">'.$arr_log["date_and_time"].'</div>
						</div>
					</div>
				</div>
				
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12 border">
							<strong>'.$arr_log["new_text"].'</strong>
						</div>
					</div>
				</div>';
			break;
			case "MODIFIED":
				$str_activity .= '<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12 purple_bar mb5">
							<div class="col-sm-4"><label>Modified information</label></div>
							<div class="col-sm-4">'.$str_prov_name.'</div>
							<div class="col-sm-4 text-right">'.$arr_log["date_and_time"].'</div>
						</div>
					</div>
				</div>
				
				<div class="col-sm-12">
					<div class="row">
						<table class="table table-bordered table-striped margin_0">
							<tr class="grythead">
								<td>Old Text</td>
								<td>New Text</td>
							</tr>
							<tr>
								<td><strong>'.$arr_log["old_text"].'</strong></td>
								<td><strong>'.$arr_log["new_text"].'</strong></td>
							</tr>	
						</table>
					</div>
				</div>';								
			break;
			case "DELETED":
				$str_activity .= '<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12 purple_bar mb5">
							<div class="col-sm-4"><label>Deleted information</label></div>
							<div class="col-sm-4">'.$str_prov_name.'</div>
							<div class="col-sm-4 text-right">'.$arr_log["date_and_time"].'</div>
						</div>
					</div>
				</div>
				
				<div class="col-sm-12 ">
					<div class="row">
						<div class="col-sm-12 border">
							<strong>'.$arr_log["old_text"].'</strong>
						</div>
					</div>
				</div>';									
			break;
		}
		$cnt++;
	}
	echo $str_activity;
	exit();
}
?>