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
//variable defined to omit warning message
$sel_prov=(isset($sel_prov))?$sel_prov:0;
$sel_fac=(isset($sel_fac))?$sel_fac:0;
$_SESSION["patient"]=(isset($_SESSION["patient"]))?$_SESSION["patient"]:0;

?>

<div class="col-lg-5 col-md-5 col-sm-5 form-inline schflt">
    <div class="form-group multiselect">
        <label for="sel_pro_month">Physician</label>
        <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-size="10" data-actions-box="true" id="sel_pro_month" name="sel_pro_month[]">
        <?php 
			//store it in varialbe for later use #do not alter this code because this varialbe being used on further included files also
			$provider_drop_options=$obj_scheduler->load_providers("OPTIONS", $sel_prov);
			echo $provider_drop_options;
			?>
        </select>
    </div>
    <div class="form-group multiselect">
        <label for="exampleInputEmail2">Facility</label>
        <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-actions-box="true" data-size="10" id="facilities" name="facilities[]">
			<?php 
			//store it in variable for later use #do not alter this code because this varialbe being used on further included files also
			$facility_drop_options=$obj_scheduler->load_facilities($_SESSION["authId"], "OPTIONS", $sel_fac);
			echo $facility_drop_options;
			?>
        </select>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-2 form-inline"><div style="font-size: 14px; font-weight: bold; width: 96%; bor" class=" text-center" id="show_pt_name">&nbsp;</div></div>
<div class="col-lg-5 col-md-5 col-sm-5 uspos"><?php if(constant('ZEISS_FORUM')=='YES'){
 $zeiss_forum_status = get_Zeiss_details(); if($zeiss_forum_status!=''){?><a href="#" onclick="top.ZeissViewer(<?php echo $zeiss_forum_status;?>);" title="Zeiss Forum"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/zeiss_scheduler.png" alt="" title="Zeiss Forum" ></a>&nbsp;<?php }}?><a href="#" onclick="top.tb_popup(this);" title="Quick Scan" class="icon"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/quickscan.png" alt="" title="Quick Scan" style="width:24px;" ></a>&nbsp;<a href="#" onclick="top.tb_popup(this);" title="Patient Communication" class="icon"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/Patient-Communicationicon.png" alt="" title="Pt. Communication" ></a>&nbsp;<a href="#" onclick="top.icon_popups('pt_at_glance');" class="icon"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/Patient-at-a-glanceicon.png" alt="" title="Patient at a glance"></a>&nbsp;
<button class="btn btn-success" onclick="verification_sheet();" type="button">AUTH/VERIFY SHEET</button>&nbsp;<button class="btn btn-success" onclick="ref_management();" type="button">REFERRAL MANAGER</button>&nbsp;<button class="btn btn-success" onclick="collect_labels();" type="button">NEXT AVAILABLE</button>
<div class="btn-group" role="group" aria-label="..." id="scroll_controls">
  <button type="button" class="btn btn-default" onClick="javascript:change_date('yesterday');" title="Yesterday"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/lftarrow.png" alt=""/></button>
  
  <button type="button" class="btn btn-default" id="scroll_control1" onClick="load_sch_on_scroll('previous');" title="Previous Slide" disabled><img src="<?php echo $GLOBALS['webroot'];?>/library/images/nxt1.png" alt=""/></button>
  
  <button type="button" class="btn btn-default" id="scroll_control2" onClick="load_sch_on_scroll('next');" title="Next Slide"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/prev.png" alt=""/></button>
   
  <button type="button" class="btn btn-default" onClick="javascript:change_date('tomorrow');" title="Tomorrow"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/rhtarrow.png" alt=""/></button>
</div>

<a href="javascript:void(0);" class="stma" onClick="javascript:change_date('sel_date');" title="Reload Scheduler">
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/reload.png" alt="Reload Scheduler" title="Reload Scheduler"/>
</a> 

<a href="javascript:void(0);" onClick="javascript:top.fmain.to_do('scheduler');" class="stma" title="First Available">
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/first_available.png" alt="First Available" title="First Available"/>
</a>
<?php
if(isERPPortalEnabled()) {
?>
<a href="javascript:void(0);" onClick="javascript:top.fmain.appt_cancel_portal('scheduler');" class="stma" title="Appointment Cancel Request from Portal">
	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/appt_cancel.png" alt="Appointment Cancel Request from Portal" title="Appointment Cancel Request from Portal"/>
</a>
<?php
}
?>    
</div>
<!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="next_available_slot_div" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                   		<div class="schmodtop"><div class="row">
                            <div class="col-sm-5"><h4 class="modal-title">Next Available Slots</h4></div>
                            
                           
                            <div class="col-sm-6 nxtavalslot">
                            	<ul>
<li><button type="button" class="btn btn-default" onclick="get_avaiable_slot('','prev')" title="Previous Month"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/lftarrow.png" alt=""></button></li>
                                <li><span id="div_curr_month">&nbsp;</span></li>
                               <li> <button type="button" class="btn btn-default" onclick="get_avaiable_slot('','next')" title="Next Month"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/rhtarrow.png" alt=""></button></li>
</ul>
                            </div>
                           
                            <div class="col-sm-1"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                        </div></div>
                        <div class="pd10"><div class="row">
                        	<div class="col-sm-3">
                                <div class="form-group">
                                <label>Physician:</label>
                                <select id="provider_label" name="provider_label[]" class="selectpicker minimal selecicon" multiple data-actions-box="true" data-done-button="true" data-size="10">
                                <?php echo $provider_drop_options;?>
                                </select>
                                </div>
                            </div>
                        	<div class="col-sm-3">
                            	<div class="form-group">
                                <label>Facility:</label>
                                <select id="facilities_label" name="facilities_label[]" class="selectpicker minimal selecicon" multiple data-actions-box="true" data-size="10" data-done-button="true">
                                <?php echo $facility_drop_options;?>
                                </select>
                                </div>
                            </div>
                        	<div class="col-sm-3">
                                <div class="form-group">
                            	<label>Label:</label>
                            	<select id="sel_all_labels" name="sel_all_labels[]"  class="selectpicker minimal selecicon" multiple data-actions-box="true" data-size="10" data-done-button="true"></select>
                            	</div>
                            </div>
                        	<div class="col-sm-3 avadayopt">
                                <div class="form-group">
                                <button type="button" class="btn btn-info" id="run_label">
                                    <span class="glyphicon glyphicon-search"></span> GO
                                  </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        	<div class="col-sm-3">
                                <div class="form-group">
                            	<label>Chain Event:</label>
                                <select id="chain_event" name="chain_event" class="selectpicker">
	                            <option value="">-- Select --</option>
                                    <?php 
                                    $q=imw_query("SELECT event_id,event_name FROM chain_event where del_status=0 order by event_name");
                                    while($d=imw_fetch_assoc($q))
                                    {
                                            echo"<option value=\"$d[event_id]\">$d[event_name]</option>";
                                    }
                                    ?>
                                 </select>
                                 </div>
                            </div>
                        	<div class="col-sm-3">
                                <div class="form-group">
                            	<label>Day(s) of the Week:</label>
                                <select id="days_of_week" name="days_of_week[]" multiple class="selectpicker minimal selecicon" data-actions-box="true">
                                	<option selected="selected" value="mon">Mon</option>
                                    <option selected="selected" value="tue">Tue</option>
                                    <option selected="selected" value="wed">Wed</option>
                                    <option selected="selected" value="thu">Thu</option>
                                    <option selected="selected" value="fri">Fri</option>
                                    <option selected="selected" value="sat">Sat</option>
                                    <option selected="selected" value="sun">Sun</option>
                                </select>
                                </div>
                            </div>
                        	<div class="col-sm-6 avadayopt">
                            <div class="form-group">
	                            <div class="radio radio-inline">
									<input type="radio" id="sch_timing_all_day" value="all_day" name="sch_timing" checked="checked" class="sch_timing_radio">
									<label for="sch_timing_all_day"> All Day </label>
								</div>
                                &nbsp;
                                <div class="radio radio-inline">
									<input type="radio" id="sch_timing_morning" value="morning" name="sch_timing" class="sch_timing_radio">
									<label for="sch_timing_morning"> Morning </label>
								</div>
                                &nbsp;
                                <div class="radio radio-inline">
									<input type="radio" id="sch_timing_afternoon" value="afternoon" name="sch_timing" class="sch_timing_radio">
									<label for="sch_timing_afternoon"> Afternoon </label>
								</div>
                            </div>
                            </div>
                         </div></div>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="current_avail_date" />
                        <div id="search-form-container" class=" bg-primary">
						
                        </div>
                        <div class="clearfix"></div>
						<div id="next_available_slot" class="scroll_400px text-center">Please select label.</div>
                    </div>
                    <div class="modal-footer" style="overflow:visible">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
		</div>