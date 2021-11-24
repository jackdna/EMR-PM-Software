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
require_once(dirname(__FILE__).'/../../config/globals.php');
//require_once(dirname(__FILE__).'/../../library/patient_must_loaded.php');
require_once(dirname(__FILE__).'/../../library/classes/class.imedicmonitor.php');

if(!isset($_SESSION["patient"]) || empty($_SESSION["patient"]) || $_SESSION["patient"]=='0'){
	die('<div class="alert alert-warning"><strong>Info!</strong> No patient loaded yet.</div>');
}

$objiMonitor = new imedicmonitor();
$pt_chkedin_appt_today 	= $objiMonitor->pt_checked_in_appts_today();
$pt_location_records 	= (object)$objiMonitor->get_patient_locaton_record('','','',true);
$all_fields_disabled 	= '';
if(!$pt_chkedin_appt_today || (is_array($pt_chkedin_appt_today) && count($pt_chkedin_appt_today)==0)){
	$all_fields_disabled = ' disabled';
}

if($all_fields_disabled!=''){?><div class="alert alert-warning"><strong>Info!</strong> Patient not checked-in yet.</div><?php die; }
?>
<form name="imon_settings" id="imon_settings" method="post">
<div class="row">
	<div class="col-sm-12">
    <table class="table">
    	<tr>
        	<th>Appointment</th>
            <td>
                <select class="form-control minimal" name="imon_sch_id" id="imon_sch_id" onchange="refill_imon_settings(this.value);">
					<?php foreach($pt_chkedin_appt_today as $schid=>$rs){
                        echo '<option value="'.$rs['id'].'">'.$rs['acronym'].': '.substr($rs['sa_app_starttime'],0,-3).' - '.substr($rs['sa_app_endtime'],0,-3).'</option>';
                    }?>
                </select>
            </td>
        </tr>
    	<tr>
        	<th>Ready for</th>
        	<td>
            	<div class="row">
                	<div class="col-sm-6">
	                    <div class="checkbox"><input onClick="top.only1checkbox(arr_ready_elements,'r4doc');" value="1" id="r4doc" name="r4doc" type="checkbox"<?php echo $all_fields_disabled;?>><label for="r4doc">Doctor</label></div>
	                    <div class="checkbox"><input onClick="top.only1checkbox(arr_ready_elements,'r4tech');" value="2" id="r4tech" name="r4tech" type="checkbox"<?php echo $all_fields_disabled;?>><label for="r4tech">Technician</label></div>
	                    <div class="checkbox"><input onClick="top.only1checkbox(arr_ready_elements,'r4test');" value="3" id="r4test" name="r4test" type="checkbox"<?php echo $all_fields_disabled;?>><label for="r4test">Test</label></div>
                    </div>
                    <div class="col-sm-6">
	                    <div class="checkbox"><input onClick="top.only1checkbox(arr_ready_elements,'r4wr');" value="4" id="r4wr" name="r4wr" type="checkbox"<?php echo $all_fields_disabled;?>><label for="r4wr">Waiting Room</label></div>
	                    <div class="checkbox"><input onClick="top.only1checkbox(arr_ready_elements,'r4done');" value="6" id="r4done" name="r4done" type="checkbox"<?php echo $all_fields_disabled;?>><label for="r4done">Done</label></div>
                    </div>
            	</div>
            </td>
    	</tr>
    	<tr>
        	<th>Patient Room</th>
        	<td><select class="form-control minimal"<?php echo $all_fields_disabled;?> id="imon_rooms" name="imon_rooms">
            		<option value='N/A'>-Select Patient Room-</option>
                    <?php echo $objiMonitor->practice_rooms('options');?>
            	</select>
            </td>
    	</tr>
    	<tr>
        	<th>Patient Priority</th>
        	<td><div class="col-sm-3"><div class="checkbox"><input onClick="top.only1checkbox(arr_prior_elements,'prio0');" value="0" id="prio0" name="prio0" type="checkbox"<?php echo $all_fields_disabled;?>><label for="prio0">Normal</label></div></div>
                <div class="col-sm-3"><div class="checkbox"><input onClick="top.only1checkbox(arr_prior_elements,'prio1');" value="1" id="prio1" name="prio1" type="checkbox"<?php echo $all_fields_disabled;?>><label for="prio1">One</label></div></div>
                <div class="col-sm-3"><div class="checkbox"><input onClick="top.only1checkbox(arr_prior_elements,'prio2');" value="2" id="prio2" name="prio2" type="checkbox"<?php echo $all_fields_disabled;?>><label for="prio2">Two</label></div></div>
                <div class="col-sm-3"><div class="checkbox"><input onClick="top.only1checkbox(arr_prior_elements,'prio3');" value="3" id="prio3" name="prio3" type="checkbox"<?php echo $all_fields_disabled;?>><label for="prio3">Three</label></div></div>
            </td>
    	</tr>
        <tr>
        	<th>Comments</th>
        	<td><textarea id="imon_comments" name="imon_comments" class="form-control" rows="2" cols="40"<?php echo $all_fields_disabled;?>></textarea></td>
    	</tr>
    </table>
    
    </div>
</div>
</form>
<script type="text/javascript">
var arr_prior_elements = new Array('prio0','prio1','prio2','prio3');
var arr_ready_elements = new Array('r4doc','r4tech','r4test','r4wr','r4done');
function only1checkbox(arr_ele_IDs,curr_id){
	for(x in arr_ele_IDs){
		loop_id = arr_ele_IDs[x];
		if(curr_id != loop_id) $('#'+loop_id).prop('checked',false);
	}
	$('#'+curr_id).prop('checked',true);
}

patient_location_rs_str = '<?php echo json_encode(htmlentities(stripslashes($pt_location_records), ENT_QUOTES));?>';
if(patient_location_rs_str.length >5){
	var patient_location_rs = jQuery.parseJSON(patient_location_rs_str);
}else{
	var patient_location_rs = new Array();	
}
pt_checked_in_appt_str = '<?php echo json_encode($pt_chkedin_appt_today);?>';
var pt_checked_in_appt_today = jQuery.parseJSON(pt_checked_in_appt_str);
refill_imon_settings($('#imon_sch_id').val());
</script>