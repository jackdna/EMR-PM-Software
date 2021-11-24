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
//error_reporting(-1);
//ini_set("display_errors",-1);
$_REQUEST = array_map('trim',$_REQUEST);
extract($_REQUEST);
$return = array('action'=>$action);
$data = array();
$pid 	= $_SESSION["patient"];
$pt_override_priv	=	$_SESSION['sess_privileges']['priv_pt_Override'];

if( $action === 'show_patient_access_log')
{
	$fields	=	" *,date_format(logtime, '".get_sql_date_format()." %h:%i %p') as log_time";
	$extra=	" AND patient_id = '".$pid."' ";
	$extra .= ($ptrp == 1) ? ' AND pt_rp_id <> 0' : 'AND pt_rp_id = 0' ;
	$rows = get_array_records('pt_and_rp_logs','1','1', $fields, $extra, 'id','DESC');
	if(is_array($rows) && count($rows) > 0 )
	{
		foreach($rows as $row)
		{
			$data[] = array('action' => $row['u_action'], 'desc' => $row['desc'], 'time' => $row['log_time']);
		}
	}
	$return['title']= 'Patient'.($ptrp == 1 ? '&nbsp;Auth. Representative&nbsp;' : '&nbsp;') .'Access Log';
	$return['total']= count($rows);
	$return['data']	= $data;
}
elseif( $action === 'login_history')
{
	
	$fields	=	"date_format(logindatetime, '".get_sql_date_format()."') as login_date, date_format(logindatetime, '%h:%i:%s %p') as login_time";
	$extra=	" AND patient_id = '".$pid."' ";
	if(isset($_REQUEST['ptrp']))
		$extra .= ($ptrp == 1) ? ' AND pt_rp_id <> 0' : ' AND pt_rp_id = 0' ;
	$rows = get_array_records('patient_loginhistory','1','1', $fields, $extra, 'id','DESC');
	if(is_array($rows) && count($rows) > 0 )
	{
		$counter = 0;
		foreach($rows as $row)
		{
			$counter++;
			$data[] = array('counter' => $counter, 'date' => $row['login_date'], 'time' => $row['login_time']);
		}
	}
	$title = "Login History";
	if(isset($_REQUEST['ptrp']))
		$title	= 'Patient'.($ptrp == 1 ? '&nbsp;Auth. Representative&nbsp;' : '&nbsp;') .'Login History';
		
	$return['title']= $title;
	$return['total']= count($rows);
	$return['data']	= $data;
	
	
}

elseif( $action === 'temp_key_generate')
{
	$temp_key_val = temp_key_gen($tempKeySize,$pid);
	$check_priv = 0; $return_val = "0";
	if($regenKey == '1')
	{
		if($pt_override_priv == 0 || !$pt_override_priv  || $pt_override_priv=="")
		{
			$return_val = "no_priv";
			$check_priv = 1;
		}
	}
	
	if($tmpUserPass && $tmpUserPass != 'DoNtChEcK')
	{
		$pass_user = hashPassword($tmpUserPass);
		$query	=	"SELECT access_pri FROM users WHERE `password`='".$pass_user."' AND delete_status <> '1' ";
		$return_val	.=	$query;
		$sql		=	imw_query($query);
		if(imw_num_rows($sql) > 0)
		{
			$row	=	imw_fetch_assoc($sql);
			$user_priviliges_prev = unserialize(html_entity_decode(trim($row['access_pri'])));
			if($user_priviliges_prev['priv_pt_Override']==1){
				$check_priv=0;
			}else{
				$return_val = "user_has_no_priv";
				$check_priv=1;	
			}
		}else{
			$return_val="user_incorrect";
			$check_priv=1;
		}
	}
	if($pid && $temp_key_val && $check_priv==0)
	{
		$query_1 = "UPDATE patient_data SET temp_key = '".$temp_key_val."', temp_key_expire='', username='', password='', preferred_image = '',temp_key_chk_val='' WHERE id='".$pid."'";	
		$sql_1	=	imw_query($query_1);
		$return_val	=	$temp_key_val;
		
		$query_2	= "UPDATE resp_party SET resp_username = '', resp_password='', preferred_image='' WHERE patient_id='".$pid."'";	
		$sql_2	= imw_query($query_2);
		
	}
	$return['tempKeySize']=	$tempKeySize;
	$return['response']	=	$return_val;
}

elseif($action == 'race_modal' )
{
	include '../../../../library/classes/demographics.class.php';
	$obj = new Demographics();
	$return['data'] = $obj->race_modal();
}

elseif($action == 'ethnicity_modal' )
{
	include '../../../../library/classes/demographics.class.php';
	$obj = new Demographics();
	$return['data'] = $obj->ethnicity_modal();
}

elseif($action == 'language_modal' )
{
	include '../../../../library/classes/demographics.class.php';
	$obj = new Demographics();
	$return['data'] = $obj->language_modal();
}
elseif($action == 'interpreter_modal' )
{
	include '../../../../library/classes/demographics.class.php';
	$obj = new Demographics();
	$return['data'] = $obj->interpreter_modal();
}

echo json_encode($return);
?>