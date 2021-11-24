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
File: main.php
Purpose: Main interface of iMedicMonitor 
Access Type: Include File
*/
include_once("../globals.php");
include_once("common_functions.php");

$task			= isset($_REQUEST['task']) 			? trim($_REQUEST['task']) 			: '';
$returnType		= isset($_REQUEST['returnType']) 	? trim($_REQUEST['returnType']) 	: '';

switch($task){
	case 'savenewprofile':
		$newproname		= isset($_REQUEST['newproname']) ? addslashes(trim($_REQUEST['newproname'])) : '';
		$default_pro		= isset($_REQUEST['default_pro']) ? trim($_REQUEST['default_pro']) : 0;
		if($newproname!=''){
			$sres = imw_query("SELECT title FROM imonitor_roomview_profiles WHERE title LIKE '$newproname' AND delete_status=0");
			$sres2 = imw_query("SELECT title FROM imonitor_roomview_profiles WHERE delete_status=0");
			if($sres2 && imw_num_rows($sres2)==0){
				$default_pro=1;	
			}
			
			//SET ALL TO NO-DEFAULT
			if($default_pro==1){imw_query("UPDATE imonitor_roomview_profiles SET default=0");}
			
			if($sres && imw_num_rows($sres)==0){
				imw_query("INSERT INTO imonitor_roomview_profiles (title,`default`) values('$newproname','$default_pro')");
				$new_id = imw_insert_id();
				if($returnType=='regular'){echo $new_id.'@~@'.$newproname;}
				else{echo 'true';}
			}else if($sres && imw_num_rows($sres)>0){
				echo 'duplicate';
			}
		}
		break;
	case 'saveSettings'://profileName
		$allsettings	= isset($_REQUEST['allsettings']) ? trim($_REQUEST['allsettings']) : '';
		$profile_id		= isset($_REQUEST['profileName']) ? trim($_REQUEST['profileName']) : 0;
		$totalGroups	= isset($_REQUEST['totalGroups']) ? trim($_REQUEST['totalGroups']) : '';
		$totalRooms		= isset($_REQUEST['totalRooms'])  ? trim($_REQUEST['totalRooms'])  : '';
		if($profile_id>0){
			$q = "UPDATE imonitor_roomview_profiles SET imon_groups='$totalGroups', imon_rooms='$totalRooms', profile_data='$allsettings' WHERE id='$profile_id'";
			$res = imw_query($q);
			if($res){echo '1';}
			else{echo '0';}
		}
		break;
	case 'AddEditGroup':
		$group_id		= isset($_REQUEST['groupId']) 		? intval($_REQUEST['groupId']) 	: 0;
		$group_text		= isset($_REQUEST['groupText']) 	? trim($_REQUEST['groupText']) 	: '';
		$pro_id			= isset($_REQUEST['pro_id']) 		? intval($_REQUEST['pro_id']) 	: 0;
		$sres = imw_query("SELECT id FROM imonitor_room_groups WHERE group_name LIKE '$group_text' AND id <> '$group_id' AND delete_status=0");
		if($sres && imw_num_rows($sres)==0){
			$q = "INSERT INTO ";
			if($group_id>0){$q = "UPDATE ";}
			$q .= "imonitor_room_groups SET group_name='".$group_text."'";
			if($group_id>0){$q .= " WHERE id='".$group_id."'";}
			$res = imw_query($q);
			if($res && $group_id>0){
				echo 'saved';
			}else if($res && $group_id==0){
				$new_group_id = imw_insert_id();
				echo $new_group_id;
			}else{
				echo 'false';
			}
		}else if($sres && imw_num_rows($sres)>0){
			echo 'duplicate';
		}
		break;
	case 'deleteGroup':
		$group_id		= isset($_REQUEST['groupId']) 		? intval($_REQUEST['groupId']) 	: 0;
		$res = imw_query("UPDATE imonitor_room_groups SET delete_status=1 WHERE id = '$group_id' LIMIT 1");
		break;
	case 'DeleteProfile':
		$pro_id		= isset($_REQUEST['pro_id']) 			? intval($_REQUEST['pro_id']) 	: 0;
		$res = imw_query("UPDATE imonitor_roomview_profiles SET delete_status=1 WHERE id = '$pro_id' LIMIT 1");
		if(!$res) echo 'false';
		break;
}

?>