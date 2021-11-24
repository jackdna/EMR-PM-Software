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


/**************************** HL7 integration skipped ******************************/
include_once($GLOBALS['srcdir']."/classes/medical_hx/lab.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$lab_obj = new Lab($medical->current_tab);
$cls_common_function = new CLSCommonFunction();
$patient_id = $_SESSION['patient'];
$operator_id=$_SESSION['authId'];

//Saving new order details
if(isset($_REQUEST['form_action']) && $_REQUEST['form_action'] == 'save_new_order'){
	$save_status = $lab_obj->save_new_lab_ord($_REQUEST);
	if(trim($save_status) != '' && $save_status > 0){
	?>
	<script>
		top.show_loading_image("show", 100);
		if(top.document.getElementById('medical_tab_change')) {
			if(top.document.getElementById('medical_tab_change').value!='yes') {
				top.alert_notification_show('Record saved successfully');
			}
			if(top.document.getElementById('medical_tab_change').value=='yes') {
				top.chkConfirmSave('yes','set');		
			}
			top.document.getElementById('medical_tab_change').value='';
		}
		top.fmain.location.href = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/index.php?showpage=lab';
		top.show_loading_image("hide");
	</script>
	<?php
	}
}

$sessionHeightInMH4= $GLOBALS['gl_browser_name']=='ipad' ? $_SESSION["wn_height"] - 70 : $_SESSION['wn_height']-315;

?>
<script type="text/javascript">var isDssEnable='<?php echo isDssEnable(); ?>';</script>	
<script src="<?php echo $library_path; ?>/js/med_lab.js" type="text/javascript"></script>	
<style>
#div_disable{
	position:absolute;
	width:100%;
	height:<?php echo $_SESSION['wn_height']-315?>px;
	text-align:center;
	z-index:1001;
	background-color:#fff;
	opacity:0.6;
}

.modal-lg{
	width:90%;
}

.purple_bar label {
    font-weight: bolder;
    font-size: 14px;
}
</style>
<body>
<div id="div_disable" class="hide"></div>
<!-- HL7 import/export div  -->
 <div id="lab_hl7_upload_div" class="modal hide" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal" onClick="close_lab_order();">×</button>
				<h4 class="modal-title" id="modal_title">UPLOAD LAB DATA HL7</h4>
			</div>
			<div class="div_shadow_container">
                <iframe id="lab_im_ex_iframe" name="lab_im_ex_iframe" style="border:0px; margin:0px; width:100%; height:<?php echo $_SESSION['wn_height']-370; ?>px;" src="about:blank"></iframe>
            </div>
			<div class="clearfix"></div>
			<div class="modal-footer pd0 panel-footer">
				<div class="row">
					<div class="col-sm-12 text-center pt5 pdb5" id="module_buttons">
						<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="close_lab_order();">Close</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<!--<div class="div_popup div_shadow hide" id="lab_hl7_upload_div">
	<div class="div_shadow_container">
    	<div class="section_header"><span class="closeBtn" onClick="close_lab_order();"></span> <span id="popup_title">UPLOAD LAB DATA HL7</span></div>
        <iframe id="lab_im_ex_iframe" name="lab_im_ex_iframe" style="border:0px; margin:0px; width:980px; height:<?php echo $_SESSION['wn_height']-380; ?>px;" src="about:blank"></iframe>
    </div>
</div>-->
<div class="row">
	<div class="col-sm-12">
		<table id="lab_record_content" class="table table-hover table-bordered table-condensed table-striped">
			<tr class="grythead">
				<th>Order#</th>
				<th>Order By</th>
				<th>Source</th>
				<th>Order Date/Time</th>
				<th>Requested Service</th>
				<th>Result</th>
                <th>Specimen</th>
				<?php if(isDssEnable()){ ?>
					<th>Samples</th>
				<?php } ?>
				
				<th>Status</th>
				<th>Action</th>
			</tr>
		<?php
		$lab_test_id=5;
		$lab_test_data = $lab_obj->load_lab_test_data();
		if(count($lab_test_data) > 0){
			foreach($lab_test_data as $obj){
				$provider1_name="";

				$labProviderId = $lab_obj->getPhyIdByName($obj['lab_test_order_by_name']);
				if($labProviderId == $obj['lab_test_order_by']){
					$provider1_name = $obj['lab_test_order_by_name'];
				}else{
					$priProvider = getRecords('users','id',$labProviderId);
					$provider1_name = core_name_format($priProvider['lname'], $priProvider['fname'], $priProvider['mname']);
				}
				$order_no="";
				$import_name="";
				if($obj['hl7_mu_id']>0){
					$order_no=$obj['impoted_order_id'];
					$import_name='<a class="a_clr1 link_cursor" onClick="javascript:new_lab_order(\''.$obj['hl7_mu_id'].'\',\'hl7imported\');">HL7</a>';
				}else{
					$order_no='iDoc'.$obj['lab_test_data_id'];
				}
				?>
				<tr class="pointer" id="lab_order_<?php echo $obj['lab_test_data_id']; ?>">
					<td onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
						<?php echo $order_no; ?><input type="hidden" name="labOrder<?php echo $obj['lab_test_data_id'];?>" id="labOrder<?php echo $obj['lab_test_data_id'];?>" value="<?php echo $order_no;?>">
					</td>
					<td onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
						<?php echo $provider1_name; ?>
					</td>
					<td onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
						<?php echo $import_name; ?>
					</td>
					<td nowrap onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
						<?php echo get_date_format($obj['lab_order_date']).' '.$obj['lab_order_time']; ?>
					</td>
					<td onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
						<?php echo $lab_obj->request_function($obj['lab_test_data_id']); ?>
					</td>
					<td onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
						<?php echo $lab_obj->result_function($obj['lab_test_data_id']); ?>
					</td>
					<td onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
						<?php echo $lab_obj->specimen_function($obj['lab_test_data_id']); ?>
					</td>
                    <?php if(isDssEnable()){ ?>
                        <td onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
                            <?php echo $lab_obj->sample_function($obj['lab_test_data_id']); ?>
                        </td>
					<?php } ?>
					
					<td onClick="javascript:new_lab_order(<?php echo $obj['lab_test_data_id']; ?>);" style="vertical-align:top!important;">
						<?php
							echo $lab_obj->lab_observation_result($obj['lab_test_data_id']);
						?>
					</td>
					<td  nowrap style="vertical-align:top!important;">
						<span onClick="javascript:top.fancyConfirm('Are you sure you want to delete this record?','Delete Record','top.fmain.delete_lab_rec(\'<?php echo $obj['lab_test_data_id']; ?>\')');" class="glyphicon glyphicon-remove pointer" alt="Delete" title="Delete">
						</span>
					</td>
				</tr>
				<?php
			}
		}else{
			?>
			<tr class="text-center">
                <td colspan="<?php echo (isDssEnable())?'10':'9';?>"><strong>No Record Found.</strong></td>
			</tr>
			<?php
		} ?>
		</table>
	</div>
	<div id="modal_show_cont"></div>
</div>
<script type="text/javascript">
//btns --- 
top.btn_show("LAB");
top.show_loading_image('hide');
</script>
<?php
//--- SET JAVASCRIPT ALERTS ----
echo $lab_obj->set_lab_cls_alerts();
?>
</body>
</html>