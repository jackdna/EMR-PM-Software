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
 * Purpose: Updox Integration Admin Interface
 * Access Type: Direct
*/

require_once(dirname(__FILE__).'/../../../config/globals.php');

if(isset($_POST['action']) && $_POST['action']==='save'){
	
	$uid = (int)trim($_POST['uid']);
	$account_id = xss_rem(trim($_POST['account_id']));
	$fax_no = xss_rem(trim($_POST['fax_no']));
	$fax_name = xss_rem(trim($_POST['fax_name']));
	$user_id = xss_rem(trim($_POST['user_id']));
	
	$sql = '';
	$where = '';
	if($uid === 0)
		$sql = 'INSERT INTO ';
	else{
		$sql = 'UPDATE';
		$where = '`id`='.$uid;
	}
	
	$sql .= " `updox_credentials` SET
				`account_id`='".$account_id."',
				`fax_no`='".$fax_no."',
				`fax_name`='".$fax_name."',
				`user_id`='".$user_id."'
				";
	imw_query($sql);
	$msg = 'Record Saved Succesfully';
}

$creds = array();
$sql = 'SELECT * FROM `updox_credentials`';
$resp = imw_query($sql);
if($resp && imw_num_rows($resp)>0){
	$creds = imw_fetch_assoc($resp);
}
require_once('../admin_header.php');
?>

<body>
<div class="whtbox">
	<div class="section" style="height:<?php print $_SESSION['wn_height']-336?>px;">
		<form method="POST" action="" id="updoxCreds" />
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td style="width:250px;"><label>Account ID</label></td>
						<td>
							<input class="form-control" type="text" name="account_id" id="account_id" value="<?php echo (isset($creds['account_id']))?$creds['account_id']:''; ?>">
							<input type="hidden" name="uid" id="uid" value="<?php echo (isset($creds['id']))?$creds['id']:''; ?>">
							<input type="hidden" name="action" id="action" value="">
						</td>
					</tr>
					<tr>
						<td><label>Fax No.</label></td>
						<td>
							<input class="form-control" type="text" name="fax_no" id="fax_no" value="<?php echo (isset($creds['fax_no']))?$creds['fax_no']:''; ?>">
						</td>
					</tr>
					<tr>
						<td><label>Fax Sender Name</label></td>
						<td>
							<input class="form-control" type="text" name="fax_name" id="fax_name" value="<?php echo (isset($creds['fax_name']))?$creds['fax_name']:''; ?>">
						</td>
					</tr>
					<tr style="display: none;">
						<td><label>User Id</label></td>
						<td>
							<input class="form-control" type="text" name="user_id" id="user_id" value="<?php echo (isset($creds['user_id']))?$creds['user_id']:''; ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<?php
if(trim($msg)) {
	echo '<script type="text/javascript">top.alert_notification_show("'.$msg.'");</script>';
}
?>
<script type="text/javascript">
	var ar = [["save","Save","top.fmain.saveData();"]];
	top.btn_show("ADMN",ar);
	set_header_title('Updox');
	show_loading_image('none');
		
	$(document).ready(function(){
		/*Hide Loader Image*/
		parent.show_loading_image('none');
	});
	
	function saveData(){
		parent.show_loading_image('');
		$('#action').val('save');
		$('#updoxCreds').submit();
	}
</script>
<?php 
	require_once('../admin_footer.php');
?>