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

require_once("../../../../config/globals.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
require_once('../../../../library/classes/cls_common_function.php');

//get provider schedule detail
$qryStr="select * from provider_schedule_tmp where id=$id";
$query=imw_query($qryStr);
$data=imw_fetch_object($query);
$templates=$data->sch_tmp_id;
//get child detail if any
$qryStr="select * from provider_schedule_tmp_child where pid=$data->id and status=1";
$query_child=imw_query($qryStr);
$data_child=imw_fetch_object($query_child);
if($data_child->sch_tmp_id){
	$templates.=','.$data_child->sch_tmp_id;
}
//get template name
$qryStr = "select id, schedule_name, morning_start_time, morning_end_time, date_status from schedule_templates where id IN ($templates) order by id asc";
$query_temp = imw_query($qryStr);
while($data_temp = imw_fetch_object($query_temp)){
	if($data_temp->id == $data->sch_tmp_id)//this is parent template
	{
		$parent_temp=$data_temp->id.'<>'.$data_temp->schedule_name;
		$time_str=$data_temp->morning_start_time.'~:~'.$data_temp->morning_end_time.'~:~'.$data_temp->date_status;
	}
	else
	{
		$child_temp=$data_temp->id.'<>'.$data_temp->schedule_name;
		//get child template on basis of date 
		$qStr="select pid from provider_schedule_tmp_child 
				WHERE status=1 
				AND IF(UNIX_TIMESTAMP(start_date) != 0, '$dated' BETWEEN start_date AND end_date, 1=1) 
				AND pid IN($id)";
		$query=imw_query($qStr);
		if(imw_num_rows($query)>0)
		{
			$time_str=$data_temp->morning_start_time.'~:~'.$data_temp->morning_end_time.'~:~'.$data_temp->date_status;
		}
	}
}
//get day number
list($y,$m,$d)=explode($data->today_date);
$week = getWeekCount($d);

//get temlpate expiry date if any
$temp_expiry_date = '';
				
$tmp_pre_qry1 =  "SELECT id,del_status FROM provider_schedule_tmp WHERE provider = '".$data->provider."' and facility = '".$data->facility."'
				and sch_tmp_id = '".$data->sch_tmp_id."' and week$week='$data->week$week' and today_date = '$data->today_date' and status = 'no' 
				order by id DESC LIMIT 0,1";

$tmp_pre_qry1_obj = imw_query($tmp_pre_qry1);
$tmp_pre_qry1_result = imw_fetch_assoc($tmp_pre_qry1_obj);
$provider_sch_tmp_id=$tmp_pre_qry1_result['id'];
if(imw_num_rows($tmp_pre_qry1_obj)==0 || $tmp_pre_qry1_result['del_status']==1)
{
	$tmp_pre_qry = "SELECT id FROM provider_schedule_tmp WHERE provider = '".$data->provider."' and facility = '".$data->facility."'
					and sch_tmp_id = '".$data->sch_tmp_id."' and week$week='$data->week$week' and today_date <= '$data->today_date' 
					and del_status = 0 and status = 'yes' order by id DESC LIMIT 0,1";

	$tmp_pre_qry_obj = imw_query($tmp_pre_qry);
	if(imw_num_rows($tmp_pre_qry_obj) >0)
	{	$row_pre_qry_fet=imw_fetch_assoc($tmp_pre_qry_obj);
		$provider_sch_tmp_id=$row_pre_qry_fet['id'];				
		$temp_expiry_qry = "SELECT today_date,id FROM provider_schedule_tmp WHERE provider = '".$data->provider."' and facility = '".$data->facility."'
							and sch_tmp_id = '".$data->sch_tmp_id."' and week$week='$data->week$week' and today_date > '$data->today_date'
							and status='no' and del_status = 1 and delete_row = 'all' order by id ASC LIMIT 0,1";
		$temp_expiry_qry_obj = imw_query($temp_expiry_qry);
		if(imw_num_rows($temp_expiry_qry_obj)>0)
		{
			$temp_expiry_qry_result = imw_fetch_assoc($temp_expiry_qry_obj);
			$temp_expiry_date = $temp_expiry_qry_result['today_date'];	
			$provider_sch_tmp_id = $temp_expiry_qry_result['id'];
		}
	}		
}

$child_from=core_date_format($data_child->start_date,'m-d-Y');
$child_to=core_date_format($data_child->end_date,'m-d-Y');
	

$return = $data->today_date.'~:~'.$temp_expiry_date.'~:~'.$data->provider.'~:~'.$data->facility.'~:~'.$parent_temp.'~:~'.$child_temp.'~:~'.$child_from.'~:~'.$child_to.'~:~'.$time_str;

echo $return;
?>