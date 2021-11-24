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
require_once("../admin_header.php");

if(isset($_POST["frmSubmit"]) && $_POST["frmSubmit"] == 1){
	$iportal_print_status_list = $_POST["iportal_print_status_list"];
	foreach ($iportal_print_status_list as $iportal_val) {
		if(isset($_POST["print_status_".$iportal_val])){
			$show_status = 1;
		}
		else{
			$show_status = 0;
		}
		$reqQry = "UPDATE iportal_print_control SET show_status = $show_status WHERE id = $iportal_val";
		imw_query($reqQry);
	}
	echo "<script>top.alert_notification_show('Record Saved Successfully');</script>";
}
?>
<script type="text/javascript">
	function submit_form(){
		$("#iportal_print_preferences_frm").submit();
	}
</script>
</head>
<body>	
<div class="whtbox">
	<form id="iportal_print_preferences_frm" method="post">
		<div id="iportal_sec_questions_list" class="table-responsive respotable">
			<table class="table table-bordered adminnw">
				<thead>    
					<tr>
						<th class="text-nowrap">Clinical Visit Details</th>
						<th class="text-nowrap">Show Status</th>    
					</tr>
				</thead>
				<tbody>
					<?php
						$req_qry = "SELECT * FROM iportal_print_control";
						$req_qry_obj = imw_query($req_qry);
						while($result_obj = imw_fetch_assoc($req_qry_obj)){
							if($result_obj['id']==14){?>
								</tbody>
								<thead>    
									<tr>
										<th class="text-nowrap">Clinical Summary</th>    
										<th class="text-nowrap">Show Status</th>    
									</tr>
								</thead>
								<tbody>
					<?php } ?>
						<tr class="pointer">
							<td><?php echo $result_obj['label']; ?></td>
							<input type="hidden" name="iportal_print_status_list[]" value="<?php echo $result_obj['id']; ?>" />
							<td style="width:110px;" class="text-center">
								<div class="checkbox">
									<input <?php if($result_obj['show_status'] == 1){ echo 'checked = "checked"'; } ?> type="checkbox" name="print_status_<?php echo $result_obj['id']; ?>" id="print_status_<?php echo $result_obj['id']; ?>" />
									<label for="print_status_<?php echo $result_obj['id']; ?>"></label>
								</div>
							</td>
						</tr>
					<?php } ?>
					<input type="hidden" name="frmSubmit" value="1">
				</tbody>
			</table>
		</div>
	</form>
</div>
<script type="text/javascript">
	var ar = [["alerts_new","Save","top.fmain.submit_form();"]];
	top.btn_show("ADMN",ar);
	set_header_title('Print Preferences');
	show_loading_image('none');
</script>
<?php 
	require_once('../admin_footer.php');
?>