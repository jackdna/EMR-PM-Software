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
Purpose: save schedule of provider
Access Type: Direct
*/
?>
<div style="height:150px;">
<input type="hidden" name="sch_app_id" id="sch_app_id" value="" />
<input type="hidden" name="week" id="week" value="" />

<div class=" schform">
	<div class="row">
		<div class="col-sm-4">
			<label>Template Start Date</label>
        	<input type="text" name="tmp_start_dt_view_us" id="tmp_start_dt_view_us" class="form-control" />       
		</div>	
		<div class="col-sm-4">
			<label>Template End Date</label>
			<input type="text" name="tmp_expiry_dt" id="tmp_expiry_dt" class="date-pick form-control"  />
		</div>
		<div class="col-sm-4">
			<label>Provider</label>
			<select name='sel_pro' id='sel_pro' data-size="11" class="selectpicker" data-width="100%">
				<option value=''>&nbsp;&nbsp;-- All Physician --</option>
				<?php
					echo $OBJCommonFunction->drop_down_providers($sel_pro_month,'1','');										
				?>
			</select>
		</div>	
		</div>
		<div class="row pt5">	
		<div class="col-sm-4 tepmopt" style="min-height: 52px">
			<label>Facility</label>
			<select name='sel_facility' id='sel_facility' class="selectpicker" data-width="100%">
				<?php print $fac_option;?>
			</select>
		</div>
		<div class="col-sm-4">
		<div class="row">
			<div class="col-sm-4 text-left">Template</div>
			<div class="col-sm-8 text-right"> <div class="checkbox"><input type="checkbox" value="1" name="show_em_temp" id="show_em_temp" onChange="show_hd_em_temp();" /><label for="show_em_temp">Show Emergency</label></div></div>
			<div class="col-sm-12 tepmopt">
				<select name='sel_schedule_name' id='sel_schedule_name' class="selectpicker" onchange="schedule_childTemp(this.value);schedule_label(this);">
				<option value=''><?php echo imw_msg('drop_sel') ?></option>
				<?php
					$qry = "select * from schedule_templates WHERE parent_id = 0 and template_type != 'SYSTEM' and del_status='' order by schedule_name";
					$sql_qry = imw_query($qry);
					$fac_res = fetchArray($sql_qry);
					$schedule_option = '';
					for($i=0;$i<count($fac_res);$i++){
						$id = $fac_res[$i]['id'];
						$template_type = $fac_res[$i]['template_type'];
						$schedule_name = str_ireplace("'","",$fac_res[$i]['schedule_name']);						
						$schedule_sel = $id.'<>'.$schedule_name == $sel_schedule_name ? 'selected' : '';
						$schedule_option .= '<option value="'.$id.'<>'.$schedule_name.'" '.$schedule_sel.'>'.$schedule_name.'</option>';
					}
					print $schedule_option;
				?>
			</select>
			</div>
		</div>
			
		</div>	
		<div class="col-sm-4">
			<label>
				<div class="checkbox"><input type="checkbox" value="1" name="new_sch" id="new_sch" onClick="setEnableDisable(this);" /><label for="new_sch">New Template Name</label></div>	
			</label>

			<div id="template_label_tr" class="hide"><input type="text" name="template_label" id="template_label" class="form-control" value="<?php print $template_label; ?>"></div>
			<input type="hidden" name="last_day_t" id="last_day_t" value="<?php echo $last_day; ?>" />
			<input type="hidden" name="wrdata" id="wrdata" value="" />
			<input type="hidden" name="Start_date_String" id="Start_date_String" class="text_10" size="12" value="<?php print $get_cur_date.",";?>" />
			<input type="hidden" name="Start_date" id="Start_date" class="text_10" size="12" value="<?php print $get_cur_date;  ?>" />
				
		</div>
		</div>
		<div class="row pt5">	
		<div class="col-sm-4">
			<div id="sch_child_temp">
				<label>Apply Child Template</label>
				<select name='sel_child_schedule_name' id='sel_child_schedule_name' class="selectpicker" onchange="schedule_TemplateTime(this.value);schedule_label(this);">
					<option value=''><?php echo imw_msg('drop_sel') ?></option>
					<?php
					if($sel_schedule_name != "")
					{
						$sel_schedule_arr = explode('<>',$sel_schedule_name);
						$optsQry = "SELECT id,schedule_name FROM schedule_templates WHERE parent_id = '$sel_schedule_arr[0]'";
						$optsQryObj = imw_query($optsQry);	
						$schedule_option = "";
						while($fac_res = imw_fetch_assoc($optsQryObj))
						{
							$id = $fac_res['id'];
							$schedule_name = $fac_res['schedule_name'];						
							$schedule_option .= '<option value="'.$id.'<>'.$schedule_name.'" >'.$schedule_name.'</option>';
						}
						echo $schedule_option;						
					}
					?>
				</select>           
			</div> 	
		</div>
		
		<div class="col-sm-2">
		<label>Child Apply From</label>
		<div class="input-group">
			<input type="text" class="datepicker form-control" tabindex="39" id="child_from" name="child_from" value="" onblur="checkdate(this);" autocomplete="off">
			<label class="input-group-addon pointer" for="child_from">
			  <i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
			</label>
		  </div>
		  
		</div>
		<div class="col-sm-2">
		<label>To</label>
		<div class="input-group">
			<input type="text" class="datepicker form-control" tabindex="39" id="child_to" name="child_to" value="" onblur="checkdate(this);" autocomplete="off">
			<label class="input-group-addon pointer" for="child_to">
			  <i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
			</label>
		  </div>
		</div>
		
		<div class="col-sm-4"><br>
			<button class="btn btn-danger" name="remove_child" id="remove_child" disabled onClick="delete_child_temp('test');">Remove Child Template</button>			
		</div>
		</div>
		<div class="row pt5">	
		<div class="col-sm-8">
		<label>Timings</label>
        <div class="clearfix"></div>	
        <div class="row">
        <div class="col-sm-12 form-inline timepick">
        <select name="start_hour" class="selectpicker" id="start_hour" data-width="100%">
				<option value="">--</option>
				<?php
					for($h=1;$h<=12;$h++){
						if($h<10){
							$h = '0'.$h;
						}
						$hour_sel = $h == $start_hour ? 'selected' : '';
						$hour_option .= '<option value="'.$h.'" '.$hour_sel.' >'.$h.'</option>';
					}
					print $hour_option;
				?>
			</select>	<select name="start_min" class="selectpicker" id="start_min" data-width="100%">
				<option value="" >--</option>
				<?php
					for($m=0;$m<60;$m = $m + DEFAULT_TIME_SLOT){
						if($m<10){
							$m = '0'.$m;
						}
						$min_sel = $m == $start_min ? 'selected' : '';
						$min_option .= '<option value="'.$m.'" '.$min_sel.' >'.$m.'</option>';
					}
					print $min_option;
				?>
			</select> <select name="start_time" class="selectpicker" id="start_time" data-width="100%">
				<option value="AM">AM</option>
				<option value="PM">PM</option>
			</select>
			<label>TO</label>
			<select name="end_hour" class="selectpicker" id="end_hour" data-width="100%">
				<option value="" >--</option>
				<?php
					$hour_option = '';
					for($h=1;$h<=12;$h++){
						if($h<10){
							$h = '0'.$h;
						}
						$hour_sel = $h == $end_hour ? 'selected' : '';
						$hour_option .= '<option value="'.$h.'" '.$hour_sel.' >'.$h.'</option>';
					}
					print $hour_option;
				?>
			</select>  <select name="end_min" class="selectpicker" id="end_min" data-width="100%">
				<option value="" >--</option>
				<?php
					$min_option = '';
					for($m=0;$m<60;$m = $m + DEFAULT_TIME_SLOT){
						if($m<10){
							$m = '0'.$m;
						}
						$min_sel = $m == $end_min ? 'selected' : '';
						$min_option .= '<option value="'.$m.'" '.$min_sel.' >'.$m.'</option>';
					}
					print $min_option;
				?>
			</select> <select name="end_time" class="selectpicker" id="end_time" data-width="100%">
				<option value="AM">AM</option>
				<option value="PM">PM</option>
			</select>	

			</div>
                        
                        
        
        </div>
		</div>	
		<div class="col-sm-4 schoptbut">
			
			<button name="save" onclick="win_opr_slots();" class="btn btn-success" id="save_slots_iportal">Slots for iPortal</button>
			<textarea id="enable_templates_slot" name="enable_templates_slot" class="form-control" style="display:none;"></textarea>
			<button name="save" onclick="save_schedule();" class="btn btn-success" id="Submit">Save</button>
			<button name="delete" onclick="delete_me();" class="btn btn-danger" id="delete" disabled>Delete</button>
		</div>
	</div>
</div>
</div>
<script>
	$(".datepicker").datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true, scrollInput:false});
</script>
<?php
	//include '../admin_footer.php'; 
?>