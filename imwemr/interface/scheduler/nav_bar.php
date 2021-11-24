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
<div class="col-sm-6">
    <div class="schtop">
        <div class="row">
            <div class="col-sm-2 datesched">
                <a href="javascript:void(0);" class="active">1</a> 
                <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('week');">7</a> 
                <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('month');">31</a></div>
                <div class="col-sm-6 text-center calndmonth">
                    <ul>
                        <li>
                            <a href="javascript:void(0);" onClick="change_date('prev_year');" title="Previous Year">
                                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/calarrow1.png" alt="Previous Year" title="Previous Year"/>
                            </a>
                        </li> 
                        
                        <li>
                            <a href="javascript:void(0);" onClick="change_date('prev_month');" title="Previous Month">
                                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/lft1.png" alt="Previous Month" title="Previous Month"/>
                            </a>
                        </li>
                        
                        <li id="sel_month_year_container">
                            <select class="form-control minimal" id="sel_month_year" name="sel_month_year" onChange="change_date('this_date', this.value);">
                            <?php echo $month_data; ?>
                            </select>
                        </li> 
                        
                        <li>
                            <a href="javascript:void(0);" onClick="change_date('next_month');" title="Next Month">
                                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/rtarrow.png" alt="Next Month" title="Next Month"/>
                            </a>
                        </li> 
                        
                        <li>
                            <a href="javascript:void(0);" onClick="change_date('next_year');" title="Next Year">
                                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/calarrow2.png" alt="Next Year" title="Next Year"/>
                            </a>
                        </li>
                    </ul>
                </div>
            
                <div class="col-sm-4 period "><div class="btn-group" role="group" aria-label="...">
                  <button type="button" class="btn btn-default" name="today_button" id="today_button" value="Today" onClick="change_date('current');">Today</button>
                  <button type="button" class="btn btn-default" name="3_mon" id="3_mon" value="3 Mon" onClick="change_date('next_3_month');">3 Mon</button>
                  <button type="button" class="btn btn-default" name="6_mon" id="6_mon" value="6 Mon" onClick="change_date('next_6_month');">6 Mon</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6">
    <div class="schtop schrhttop">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-5 daydat" id="DayNameId"><?php echo ucfirst($strGetDayName).', '.get_date_format($db_load_date,'mm-dd-yy'); ?></div>
            <div class="col-lg-4 col-md-4 col-sm-7 pticons">
                <ul>
                    <li style="display:<?php echo ((strtolower($arr_billing_policies["Allow_erx_medicare"]) == "yes") ? "inline-block" : "none");?>">
                    	<a href="javascript:void(0);" onClick="javascript:patient_erx_registration();" title="Patient eRx registration">
                        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/erx.png" alt="Patient eRx registration" title="Patient eRx registration"/>
                        </a>
                    </li>
                    <li id="divERChk" style="display:<?php echo ((constant("ENABLE_REAL_ELIGILIBILITY") == "YES") ? "inline-block" : "none");?>;">
                    	<a href="javascript:void(0);" onClick="javascript:realtime_medicare_eligibility('<?php echo $_SESSION['wn_height'] - 140; ?>');" title="Realtime Eligibility Request">
                        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/er1.png" alt="Realtime Eligibility Request" title="Realtime Eligibility Request"/>
                        </a>
                    </li>
                    <li id="div278">
                    	<a href="javascript:void(0);" onClick="javascript:pre_auth('<?php echo $_SESSION['wn_height'] - 140; ?>');" title="Pre-Authorization">
                        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/sc1.png" alt=""/>
                        </a>
                    </li>
                    <li id="Print_Day_Appt_Link">
                    	<a href="javascript:void(0);" onClick="javascript:day_print_options('all', '', '1');" title="Print Day Appointments">
                        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/sc2.png" alt="Print Day Appointments" title="Print Day Appointments"/>
                        </a>
                    </li>
                    <li id="Day_Summary_Link">
                    	<a href="javascript:void(0);" onClick="javascript:day_proc_summary();" title="Day Summary">
                        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/sc3.png" alt="Day Summary" title="Day Summary"/>
                        </a>
                    </li>
                    <?php
						$sch_lockblock_privilege=(core_check_privilege(array("priv_sch_lock_block")) == false) ? 0 : 1;
						if($sch_lockblock_privilege==1){
					?>
                    <li id="Day_Block" style="display:none">
                    	<a href="javascript:void(0);" onClick="javascript:blk_lk_options('', 'block');" title="Block / Lock Options">
                        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/sc4.png" alt="Block / Lock Options" title="Block / Lock Options"/>
                        </a>
                    </li>
                    <?php }?>
                    <li id="divERChkFile" style="display:<?php echo ((constant("ENABLE_REAL_ELIGILIBILITY") == "YES") ? "inline-block" : "none");?>;">
                    	<a href="javascript:void(0);" onClick="javascript:realtime_eligibility_file('<?php echo $_SESSION['wn_height'] - 140; ?>');" title="Real Time Eligibility File">
                        	<img src="<?php echo $GLOBALS['webroot'];?>/library/images/sc5.png" alt="Real Time Eligibility File" title="Real Time Eligibility File"/>
                        </a>
                    </li>
                    <?php if(constant("HIE_FORUM") == "YES"){$forum_btn_val = 'HIE'; ?>
                    <li>
                        <button class="btn btn-info" onClick="javascript:send_to_forum('<?php echo $_SESSION['wn_height'] - 140; ?>');"><?php echo $forum_btn_val;?></button>
					</li>
					<?php }?>
                </ul>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12  form-inline jumpto" >
                <div class="form-group">
                    <label for="jmpto">Jump To : </label>
                    <input type="text" class="form-control" id="jmpto" name="jmpto" placeholder="10">
                
                    <select class="form-control minimal" name="op_typ" id="op_typ">
                        <option value="day">Days</option>
                        <option value="week">Weeks</option>
                        <option value="month">Months</option>
                    </select>
                <button type="button" class="gobut" onClick="javascript:change_date('go_to');">GO</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>