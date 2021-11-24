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
require_once(dirname(__FILE__).'/../../../../config/globals.php');
$msg='';

if(isset($_POST['action']) && $_POST['action']==='save')
{
	$ccda_sftp_id 	= (int)trim($_POST['ccda_sftp_id']);
	$ccda_host_name = xss_rem(trim($_POST['ccda_host_name']));
	$port_number 	= xss_rem(trim($_POST['port_number']));
	$ccda_sftp_username	= xss_rem(trim($_POST['ccda_sftp_username']));
	$ccda_sftp_password	= xss_rem(trim($_POST['ccda_sftp_password']));
	$ccda_directory_path= trim($_POST['ccda_directory_path']);
	$date_time			= date('Y-m-d H:i:s');
    
	$sql = '';
	$where = '';
	if($ccda_sftp_id === 0)
	{
		$sql = 'INSERT INTO ';
    }
	else
	{
		$sql = 'UPDATE';
		$where = '`id`='.$ccda_sftp_id;
	}
	
	$sql .= " 
				`ccda_sftp_credentials` 
			SET
				`ccda_host_name`='".$ccda_host_name."',
				`port_number`='".$port_number."',
				`ccda_sftp_username`='".$ccda_sftp_username."',
				`ccda_sftp_password`='".$ccda_sftp_password."',
				`ccda_directory_path`='".$ccda_directory_path."',
				`operator`='".$_SESSION['authId']."',
				`date_time`='".$date_time."'
			";
	imw_query($sql);
	$msg = 'Record Saved Succesfully';
}

$creds = array();
$sql = 'SELECT * FROM `ccda_sftp_credentials`';
$resp = imw_query($sql);
if($resp && imw_num_rows($resp)>0)
{
	$creds = imw_fetch_assoc($resp);
}

require_once('../../admin_header.php');
?>

<body>
<div class="whtbox">
	<div class="section" style="height:<?php print $_SESSION['wn_height']-336?>px;">
		<form method="POST" action="" id="ccda_sftp_frm" >
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td style="width:250px;"><label>Host Name</label></td>
						<td>
							<input class="form-control" type="text" name="ccda_host_name" id="ccda_host_name" value="<?php echo (isset($creds['ccda_host_name']))?$creds['ccda_host_name']:''; ?>">
							<input type="hidden" name="ccda_sftp_id" id="ccda_sftp_id" value="<?php echo (isset($creds['id']))?$creds['id']:''; ?>">
							<input type="hidden" name="action" id="action" value="">
						</td>
					</tr>
					<tr>
						<td><label>Port Number</label></td>
						<td>
							<input class="form-control" type="text" name="port_number" id="port_number" value="<?php echo (isset($creds['port_number']))?$creds['port_number']:''; ?>">
						</td>
					</tr>
					<tr>
						<td><label>Username</label></td>
						<td>
							<input class="form-control" type="text" name="ccda_sftp_username" id="ccda_sftp_username" value="<?php echo (isset($creds['ccda_sftp_username']))?$creds['ccda_sftp_username']:''; ?>">
						</td>
					</tr>
					<tr>
						<td><label>Password</label></td>
						<td>
							<input class="form-control" type="password" name="ccda_sftp_password" id="ccda_sftp_password" value="<?php echo (isset($creds['ccda_sftp_password']))?$creds['ccda_sftp_password']:''; ?>">
						</td>
					</tr>
                    <tr>
						<td><label>Directory Path To SFTP</label></td>
						<td>
							<input class="form-control" type="text" name="ccda_directory_path" id="ccda_directory_path" value="<?php echo (isset($creds['ccda_directory_path']))?$creds['ccda_directory_path']:''; ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript">
    <?php if(trim($msg)!='') { ?>
        top.fAlert("<?php echo $msg;?>");
    <?php } ?>
        
	var ar = [["save","Save","top.fmain.saveData();"]];
	top.btn_show("ADMN",ar);
	set_header_title('CCDA SFTP Credentials');
	show_loading_image('none');
		
	$(document).ready(function(){
		/*Hide Loader Image*/
		parent.show_loading_image('none');
	});
	
	function saveData(){
		parent.show_loading_image('');
		$('#action').val('save');
		$('#ccda_sftp_frm').submit();
	}
</script>
<?php require_once('../../admin_footer.php');?>