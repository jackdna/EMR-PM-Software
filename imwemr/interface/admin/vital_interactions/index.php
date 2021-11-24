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
 * Purpose: Write Back Comments
 * Access Type: Direct
*/

require_once(dirname(__FILE__).'/../../../config/globals.php');

if(isset($_POST['action']) && $_POST['action']==='save'){
	
	
	$op_id = $_SESSION['authId'];
	$vi_id = (int)trim($_POST['vi_id']);
	$vi = xss_rem(trim($_POST['vital_interaction']));
	
	$vital_inter_arr = array("yes"=>"1","no"=>"0");
	$vital_inter_val = $vital_inter_arr[$vi];
	
	$sql = '';
	$where = '';
	if($vi_id === 0)
	{	
		$sql = "INSERT INTO vital_interactions (write_back_comments, op_id) values ($vital_inter_val, $op_id)";
	}
	else
	{
		$sql = "UPDATE vital_interactions SET write_back_comments=$vital_inter_val, op_id=$op_id where vi_id=$vi_id";
	}
	
	imw_query($sql);
	$msg = 'Record Saved Succesfully';
}

$sql = 'SELECT * FROM vital_interactions';
$resp = imw_query($sql);
if($resp && imw_num_rows($resp)>0){
	$row = imw_fetch_assoc($resp);
}



require_once('../admin_header.php');
?>

<body>
<div class="whtbox">
	<div class="section" style="height:<?php print $_SESSION['wn_height']-336?>px;">
		<form method="POST" action="" id="vitalInteractions" />
			<table class="table table-bordered">
				<tbody>
					<tr>
					  <td>
						<div class="radio">
								<input type="radio" name="vital_interaction" id="vital_interaction_yes" value="yes" <?php echo (!isset($row['write_back_comments']) || $row['write_back_comments'] == 1) ?'checked':''; ?>>
								<label for="vital_interaction_yes"> Write Back Comments = YES </label>
							</div>
							<div class="radio radio">
								<input type="radio" name="vital_interaction" id="vital_interaction_no" value="no" <?php echo (isset($row['write_back_comments']) && $row['write_back_comments'] == 0) ? 'checked':''; ?>>
								<label for="vital_interaction_no">Write Back Comments = NO</label>
							</div>
						</td>
				  </tr>
					<tr>
						<td>
							** Please check the appropriate box to enable or disable your Vital Interactions integration to write back appointment comments to imwemr
						</td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" name="action" id="action" value="">
			<input type="hidden" name="vi_id" id="vi_id" value="<?php echo $row['vi_id'];?> ">
		</form>
	</div>
</div>
<?php
if(trim($msg))
{
	echo '<script type="text/javascript">top.alert_notification_show("'.$msg.'");</script>';
}
?>
<script type="text/javascript">
	var ar = [["save","Save","top.fmain.saveData();"]];
	top.btn_show("ADMN",ar);
	set_header_title('Vital Interactions');
	show_loading_image('none');
	
	$('input.vi').on('change', function() {
		$('input.vi').not(this).prop('checked', false);  
	});	
	
	$(document).ready(function(){
		/*Hide Loader Image*/
		parent.show_loading_image('none');
	});
	
	function saveData(){
		parent.show_loading_image('');
		$('#action').val('save');
		$('#vitalInteractions').submit();
	}
</script>
<?php 
	require_once('../admin_footer.php');
?>