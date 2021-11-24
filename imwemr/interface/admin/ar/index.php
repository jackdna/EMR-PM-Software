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
require_once(dirname(__FILE__).'/../../../config/globals.php');

if($_POST['action']=='save')
{
	imw_query("update ar_worksheet set ar_detail_column='". serialize($_POST['col_id']) ."'");
	$msg = 'Record Updated Succesfully';
}
$sql = 'SELECT * FROM ar_worksheet';
$resp = imw_query($sql);
if($resp && imw_num_rows($resp)>0){
	$row = imw_fetch_assoc($resp);
	$selected_col=unserialize($row['ar_detail_column']);
	$selected_col=array_combine($selected_col,$selected_col);
}
$detailColArr=array("Facility", "Patient Name - ID", "DOB", "Ins. Type", "Ins. ID", "Provider", "CFD", "PD", "DOS", "DOC", "CPT", "ICD10", "Charge", "R", "Aging", "Balance", "1st Claim", "Prt Pt St", "Note", "Reminder Date", "Case Type", "Assign To", "AR Status");
asort($detailColArr);
require_once('../admin_header.php');
//require_once('../../../library/classes/admin/scheduler_admin_func.php');
?>

<body>
<div class="whtbox">
	<div class="section" style="height:<?php print $_SESSION['wn_height']-336?>px;">
		<form method="POST" action="" name="arworksheet" id="arworksheet" onSubmit="return validate_ar()" />
			<table class="table table-bordered adminnw tbl_fixed">
            <thead>
                <tr>
					<th class="text-center col-sm-1">
						<div class="checkbox">
							<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
							<label for="chk_sel_all">
							</label>
						</div>
					</th>
                    <th class="col-sm-5"><span>Field Name</span></th>
					<th class="text-center col-sm-1">&nbsp;</th>
                    <th class="col-sm-5"><span>Field Name</span></th>
                    
                </tr>
            </thead>
			<tbody id="result_set">
				<tr>
				<?php
				$i=0;
				foreach($detailColArr as $col){	
					$i++;
					$sel=($selected_col[$col])?"checked":"";
				?>
				<td class="text-center">
					<div class="checkbox">
						<input type="checkbox" name="col_id[]" class="chk_sel" id="chk_sel_<?php echo $i;?>" value="<?php echo $col;?>" <?php echo $sel;?>>
						<label for="chk_sel_<?php echo $i;?>"></label>
					</div>
				</td>
				<td class="leftborder"><?php echo $col;?></td>
				<?php 
				if($i%2==0)echo"</tr><tr>";
				}?>
				</tr>
			</tbody>
        </table>
			<input type="hidden" name="action" id="action" value="">
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
	check_checkboxes();
	set_header_title('AR Worksheet - Detailed View');
	show_loading_image('none');
	
	
	$(document).ready(function(){
		/*Hide Loader Image*/
		parent.show_loading_image('none');
	});
	
	function saveData(){
		parent.show_loading_image('');
		$('#action').val('save');
		$('#arworksheet').submit();
	}
	function validate_ar()
	{
		if($('#result_set').find('input[type=checkbox]:checked').length <5)
		{
			top.fAlert('Please select minimum 5 field names');
			return false;
		}
		return true;
	}
</script>
<?php 
	require_once('../admin_footer.php');
?>