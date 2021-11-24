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
?>
<div class="row">
	
	<div class="col-lg-6 col-md-6 col-sm-6">
	    <div class="form-group">
        <label for="anps_sel_pro">Physician</label>
    	<select id='anps_sel_pro' name="anps_sel_pro" class="form-control minimal selecicon">
            <option value=""></option>
			<?php echo $provider_drop_options;?>
        </select>
    	</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
    	<div class="form-group">
        <label for="anps_sel_fac">Facility</label>
        <select id='anps_sel_fac' name='anps_sel_fac' class="form-control minimal selecicon">
            <option value=""></option>
            <?php echo $facility_drop_options;?>
        </select>
        </div>
    </div>
    
</div>
<div class="clearfix"></div>
<div class="row" id="show_tmp_option">
	<div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
        	<label for="anps_sel_tmp">Template</label>
        	<span id="temp_dd">
            <?php $str_temp_option=$obj_scheduler->load_sch_templates("OPTIONS","1");?>
                <select id='anps_sel_tmp' name='anps_sel_tmp' onchange="javascript:load_template_timings(this.value);" class="form-control minimal selecicon">
                    <option value="new">New Template</option>
                    <?php echo $str_temp_option;?>
                </select>
                <span id="sel_tmp_options" style="display:none"><?php echo $str_temp_option;?></span>
            </span>
            <div id="temp_text">
	          	<input type="text" name="anps_sel_tmp" value="New Template" readonly />
            </div>
        </div>
     </div>
	<div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
        	<div class="checkbox checkbox-inline">
            <input type="checkbox" name="show_emergency_templates" id="show_emergency_templates" title="Show Emergency Templates">
            <label for="show_emergency_templates"> Show Emergency Templates</label>
        	</div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6">
        <div class="form-group">
        	<label>Timings From</label>
            <div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4">
            	<select id="start_hour" name="start_hour" class="form-control minimal selecicon">
                    <option value="" >--</option>
                    <?php
					$start_hour=(isset($start_hour))?$start_hour:0;
					$hour_option ='';
                    for($h=1;$h<=12;$h++){
                        if($h<10){
                            $h = '0'.$h;
                        }
                        $hour_sel = $h == $start_hour ? 'selected' : '';
                        $hour_option .= '<option value="'.$h.'" '.$hour_sel.' >'.$h.'</option>';
                    }
                    print $hour_option;
                    ?>
                </select>
            	</div>
				<div class="col-lg-4 col-md-4 col-sm-4">
                <select id="start_min" name="start_min" class="form-control minimal selecicon">
                    <option value="" >--</option>
                    <?php
					$start_min=(isset($start_min))?$start_min:0;
					$min_option='';
                    $timeSlot = DEFAULT_TIME_SLOT;
                    for($m=0;$m<60;$m++){
                        if($m<10){
                            $m = '0'.$m;
                        }
                        $min_sel = $m == $start_min ? 'selected' : '';
                        $min_option .= '<option value="'.$m.'" '.$min_sel.' >'.$m.'</option>';
                        $m += $timeSlot - 1;
                    }
                    print $min_option;
                    ?>
                </select>
            	</div>
				<div class="col-lg-4 col-md-4 col-sm-4">
                <select id="start_time" name="start_time" class="form-control minimal selecicon">
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            	</div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 col-md-6 col-sm-6">
	    <div class="form-group">
        	<label>To</label>
            <div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4">
            	<select id="end_hour" name="end_hour" class="form-control minimal selecicon">
				<option value="" >--</option>
				<?php
				$end_hour=(isset($end_hour))?$end_hour:0;
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
			</select>
            	</div>
				<div class="col-lg-4 col-md-4 col-sm-4">
                <select id="end_min" name="end_min" class="form-control minimal selecicon">
				<option value="" >--</option>
				<?php
				$end_min=(isset($end_min))?$end_min:0;
				$min_option = '';
				for($m=0;$m<60;$m++){
					if($m<10){
						$m = '0'.$m;
					}
					$min_sel = $m == $end_min ? 'selected' : '';
					$min_option .= '<option value="'.$m.'" '.$min_sel.' >'.$m.'</option>';
					$m += $timeSlot - 1;
				}
				print $min_option;
				?>
			</select>
            	</div>
				<div class="col-lg-4 col-md-4 col-sm-4">
                <select id="end_time" name="end_time" class="form-control minimal selecicon">
				<option value="">--</option>
				<option value="AM">AM</option>
				<option value="PM">PM</option>
			</select>
            	</div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="row" id="commentDiv">
	<div class="col-lg-12 col-md-12 col-sm-12">
    	<div class="form-group">
        <label>Comments</label>
        <input type="text" id="comments" name="comments" value="" class="form-control" />
        </div>
    </div>
</div>