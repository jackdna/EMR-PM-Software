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

$do 		= isset($_GET['do']) ? trim($_GET['do']) : '';
$pro_id 	= isset($_GET['pro_id']) ? intval($_GET['pro_id']) : 0;

if($do=='SavedProfiles'){
	$q = "SELECT ip.id,ip.test_id,ip.physician_id,ip.profile_name,ip.profile_data,ip.favorite,tn.temp_name as test_name,tn.test_table,tn.script_file FROM interpretation_profiles ip LEFT JOIN tests_name tn ON (tn.id=ip.test_id) WHERE physician_id='".$pro_id."' AND tn.status='1' AND tn.del_status='0' AND ip.deleted=0 ORDER BY ip.profile_name";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$return = array();
		while($rs = imw_fetch_assoc($res)){
			//$rs['profile_data'] = html_entitiy_decode($rs['profile_data']);
			$rs['profile_data'] = str_replace(array("\r\n","\n","\r"),"<br>",$rs['profile_data']);
			$rs['profile_data'] = addslashes($rs['profile_data']);
			$return[] = $rs;
		}
		die(json_encode(array('result'=>$return)));
	}else{
		die(imw_error());
	}
	echo 'No Result';
	die();
}else if($do=='SaveMainData'){
	//pre($_GET);
	$profile_id		= $_GET['profile_id'];
	$profile_name	= trim($_GET['profile_name']);
	$physician_id	= $_GET['physician_id'];
	$test_id		= $_GET['test_id'];
	$profile_data	= htmlentities(json_encode($_POST));
	$favorite		= $_GET['favorite'];
	$created_by		= $_SESSION['authId'];
	$created_on		= date('Y-m-d H:i:s');
	if($favorite==1){imw_query("UPDATE interpretation_profiles SET favorite=0 WHERE physician_id='$physician_id' AND test_id='$test_id'");}	
	$q = "interpretation_profiles SET test_id='$test_id', physician_id='$physician_id', profile_name='".addslashes($profile_name)."', profile_data='".addslashes($profile_data)."', favorite=$favorite";
	if($profile_id==''){
		$q = "INSERT INTO ".$q.", created_by='$created_by', created_on='$created_on'";
	}else{
		$q = "UPDATE ".$q.", modified_by='$created_by', modified_on='$created_on' WHERE id='$profile_id'";
	}
	imw_query($q) or die('Unable to save record.');
	echo 'Record Saved Successfully.';
	die;
}else if($do=='DeleteProfile'){
	imw_query("UPDATE interpretation_profiles SET deleted=1 WHERE id='$pro_id'");
	die;	
}else if($do=='load_copy_profile_dd'){
    $test_master_id = isset($_REQUEST['test_master_id']) ? trim($_REQUEST['test_master_id']) : '';
    if($test_master_id!='') {
        $copy_profile_dd_str='';
        $usersArr = array();
        $res = imw_query("SELECT id,lname,fname,mname FROM users WHERE user_type IN (1,11,12,19) AND delete_status=0 ORDER BY lname,fname");
        if($res && imw_num_rows($res)>0){
            while($rs = imw_fetch_assoc($res)){
                $phyID = $rs['id'];
                $phyNM = $rs['lname'].', '.$rs['fname'].' '.substr($rs['mname'],0,1);
                $usersArr[$phyID] = $phyNM;
            }
        }
        
        $return = array();
        $tests_nameArr = array();
        $res1 = imw_query("SELECT * FROM tests_name WHERE del_status=0 AND status='1' ORDER BY id");
        if($res1 && imw_num_rows($res1)>0){
            while($rs1 = imw_fetch_assoc($res1)){
                $return[$rs1['id']] = $rs1['test_table'].'~@~'.$rs1['id'].'~@~'.$rs1['script_file'];
                $tests_nameArr[$rs1['id']] = $rs1['temp_name'];
            }
        }
        
        //$q = "SELECT ip.id,ip.test_id,ip.physician_id,ip.profile_name,ip.profile_data,ip.favorite,tn.temp_name as test_name,tn.test_table,tn.script_file FROM interpretation_profiles ip LEFT JOIN tests_name tn ON (tn.id=ip.test_id) WHERE physician_id='".$pro_id."' AND tn.status='1' AND tn.del_status='0' AND ip.deleted=0 ORDER BY ip.profile_name";
        $q = "SELECT * FROM interpretation_profiles WHERE test_id='".$test_master_id."' AND deleted=0 ORDER BY profile_name";
        $res = imw_query($q);
        if($res && imw_num_rows($res)>0){
            while($row=imw_fetch_assoc($res)) {
                $row['profile_data'] = str_replace(array("\r\n","\n","\r"),"<br>",$row['profile_data']);
                $row['profile_data'] = addslashes($row['profile_data']);
                $copy_profile_dd_str.='<option data-profile_name="'.$row['profile_name'].'" data-test_ids="'.$return[$test_master_id].'" data-profile_data="'.$row['profile_data'].'" data-physician_id="'.$row['physician_id'].'" value="'.$row['id'].'">'.$usersArr[$row['physician_id']].' - '.$row['profile_name'].' ('.$tests_nameArr[$row['test_id']].')</option>';
            }
        }
        
        echo json_encode($copy_profile_dd_str);
        die;
    }
    
}

?>