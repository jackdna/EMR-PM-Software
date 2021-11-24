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


if($_POST['form_submitted']) {
	$reminder_choices = array_combine($reminder_choices, $reminder_choices);
}

//--- GET Groups SELECT BOX ----
$group_query = "Select  gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
	if(in_array($group_id,$groups))$sel='SELECTED';

    $groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}


//--- GET ALL OPERATORS DETAILS ----
$selOperId=implode(',',$operator_id);
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, '', '');


$datelabel = "Period";
?>
<div class="reportlft" style="height:100%;">
  <div class="practbox">
    <div class="anatreport">
      <h2>Practice Filter</h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
      <div class="row">
        <div class="col-sm-6">
          <label>Groups</label>
          <select name="groups[]" id="groups" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php echo $groupName; ?>
          </select>
        </div>
	
        <div class="col-sm-6">
          <label>Operator</label>
          <select name="operator_id[]" id="operator_id" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
            <?php echo $operatorOption; ?>
          </select>
        </div>
        <div class="col-sm-12">
        <label id="lbl_date_title"><?php echo $datelabel; ?></label>
          <div id="dateFieldControler">
            <select name="dayReport" id="dayReport" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
              <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
              <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
              <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
              <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
              <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
            </select>
          </div>
          <div class="row" style="display:none" id="dateFields">
            <div class="col-sm-5">
              <div class="input-group">
                <input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
                <label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
              </div>
            </div>
            <div class="col-sm-5">
              <div class="input-group">
                <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick">
                <label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
              </div>
            </div>
            <div class="col-sm-2" id="div_back_arrow">
              <button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
            </div>
          </div>
        </div>


     	</div>
    </div>
  </div>
  <div class="appointflt">
    <div class="anatreport">
      <h2>Analytic Filter</h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
		<div class="row">
			<div class="col-sm-6">
				<label>Message Output</label>
				<select name="message_output" id="message_output" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true">
                    <option value="" <?php if($message_output=='')echo 'SELECTED';?>>All</option>
                    <option value="sent" <?php if($message_output=='sent')echo 'SELECTED';?>>Sent</option>
                    <option value="failed" <?php if($message_output=='failed')echo 'SELECTED';?>>Failed</option>
				</select>
			</div>
      </div>
    </div>
  </div>

  <div class="grpara">
    <div class="anatreport">
      <h2>Format</h2>
    </div>
    <div class="clearfix"></div>
    <div class="pd5" id="searchcriteria">
      <div class="row">
        <div class="col-sm-4">
          <div class="radio radio-inline pointer">
            <input type="radio" name="output_option" id="output_view_only" value="1" <?php if ($_POST['output_option']=='1' || $_POST['output_option']=='') echo 'CHECKED'; ?>/>
            <label for="output_view_only">View Only</label>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="radio radio-inline pointer">
            <input type="radio" name="output_option" id="output_pdf" value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/>
            <label for="output_pdf">PDF</label>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="radio radio-inline pointer" >
            <input type="radio" name="output_option" id="output_csv" value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/>
            <label for="output_csv">CSV</label>
          </div>
        </div>
      </div>
    </div>
		<div class="clearfix">&nbsp;</div>
  </div>
</div>
<div id="module_buttons" class="ad_modal_footer text-center">
	<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
</div>