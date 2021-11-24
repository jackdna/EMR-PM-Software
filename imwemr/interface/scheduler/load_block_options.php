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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

//scheduler object
$obj_scheduler = new appt_scheduler();

//setting date format
list($yr, $mn, $dt) = explode("-", $_REQUEST["load_dt"]);
//echo $mn."/".$dt."/".$yr."~~~~~";
echo get_date_format($_REQUEST["load_dt"],'','','',"/")."~~~~~";
//getting fac and prov available for this day
$order_file =$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml/".$_REQUEST["load_dt"]."-order.sch";
if(file_exists($order_file)){
	$str_tmp_order = file_get_contents($order_file);
	list($str_order, $str_fac, $str_prov_time, $str_office_time) = explode("~~~~~", $str_tmp_order);
	//echo $str_order;
	//echo $str_fac;
	$arr_pro = unserialize($str_order);
	$arr_fac = unserialize($str_fac);
		
	if(is_array($arr_pro) && count($arr_pro) > 0){
		$str_sel_pro = "'".implode("','", $arr_pro)."'";
	}
	if(is_array($arr_fac) && count($arr_fac) > 0){
		$str_sel_fac = "'".implode("','", $arr_fac)."'";
	}
}

$arr = array();
//selected facilities condition is remove as it was raising issue in cache 18802 :: where id in (".$str_sel_fac.")
$qry = "select id, name from facility order by name";
$res = imw_query($qry);
if(imw_num_rows($res) > 0){
	while($tmpData=imw_fetch_assoc($res))
	{$arr[] = $tmpData;}
}

$arrp = array();
//selected facilities condition is remove as it was raising issue in cache 18802 :: id in (".$str_sel_pro.") and
$qryp = "select id, fname, lname, mname from users where Enable_Scheduler = '1' and delete_status = '0' order by lname, fname";
$resp = imw_query($qryp);
if(imw_num_rows($resp) > 0){
	while($tmpData=imw_fetch_assoc($resp))
	{$arrp[] = $tmpData;}
}

$sel_am_pm = $sel_hr = $sel_mn = $sel_sc = $sel_appt_tm = $sel_ed_hr = $sel_ed_mn = $sel_ed_am_pm = "";

// GET START - END TIME (FOR SINGLE PROVIDER OR OFFICE TIME)
if(sizeof($arrp) > 1)
{
	$arr_office_time = unserialize($str_office_time);
	$ret_time = $obj_scheduler->getAmPmTime($arr_office_time['st']);
	list($sel_hr, $sel_mn, $sel_am_pm) = explode("~", $ret_time);
	
	$ret_time = $obj_scheduler->getAmPmTime($arr_office_time['et']);
	list($sel_ed_hr, $sel_ed_mn, $sel_ed_am_pm) = explode("~", $ret_time);

}else{
	$arr_prov_time = unserialize($str_prov_time);
	$pro_id = $arrp[0]['id'];

	$ret_time = $obj_scheduler->getAmPmTime($arr_prov_time[$pro_id]['st']);
	list($sel_hr, $sel_mn, $sel_am_pm) = explode("~", $ret_time);

	$ret_time = $obj_scheduler->getAmPmTime($arr_prov_time[$pro_id]['ed']);
	list($sel_ed_hr, $sel_ed_mn, $sel_ed_am_pm) = explode("~", $ret_time);
}


$sel_fac_val = $sel_pro_val = 0;

$default_blk_checked = "checked=\"checked\"";
$default_unblk_checked = "";
if(isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "get"){

	if(isset($_REQUEST["act_type"]) && $_REQUEST["act_type"] == "unblock"){
		$default_unblk_checked = "checked=\"checked\"";
		$default_blk_checked = "";
	}
	//start time
	$sel_app_tmp = $_REQUEST["ap_sttm"];
	list($sel_hr, $sel_mn, $sel_sc) = explode(":", $sel_app_tmp);	

	//end time
	$sel_hr_tmp = (int)$sel_hr;
	$sel_mn_tmp = (int)$sel_mn;

	list($sel_ed_hr, $sel_ed_mn, $sel_ed_sc) = explode(":", date("H:i:s", mktime($sel_hr_tmp, $sel_mn_tmp + DEFAULT_TIME_SLOT, 0)));
	//echo "$sel_ed_hr, $sel_ed_mn, $sel_ed_sc";
	$sel_am_pm = "1";
	if($sel_hr >= 12){
		if($sel_hr > 12){
			$sel_hr = (int)$sel_hr - 12;
		}
		if(strlen($sel_hr) < 2){
			$sel_hr = "0".$sel_hr;
		}
		$sel_am_pm = "2";
	}
	if(strlen($sel_mn) < 2){
		$sel_mn = "0".$sel_mn;
	}
	$sel_ed_am_pm = "1";
	if($sel_ed_hr >= 12){
		if($sel_ed_hr > 12){
			$sel_ed_hr = (int)$sel_ed_hr - 12;
		}
		if(strlen($sel_ed_hr) < 2){
			$sel_ed_hr = "0".$sel_ed_hr;
		}
		$sel_ed_am_pm = "2";
	}
	if(strlen($sel_ed_mn) < 2){
		$sel_ed_mn = "0".$sel_ed_mn;
	}

	//selected doctor
	$sel_pro_val = $_REQUEST["ap_doc"];

	//selected facitliy
	$sel_fac_val = $_REQUEST["ap_fac"];
	
	
}
?>
<div class="blcopt">
<div class="row">
	<div class="col-sm-6 text-left">
    	<div class="radio radio-inline">
            <input type="radio" id="blk_lk_act_block" value="block" name="blk_lk_act" <?php echo $default_blk_checked;?> onclick="javascript:swap_block_unblock('block');">
            <label for="blk_lk_act_block">Block</label>
        </div>
    	<div class="radio radio-inline">
            <input type="radio" id="blk_lk_act_unblock" value="open" name="blk_lk_act" <?php echo $default_unblk_checked;?> onclick="javascript:swap_block_unblock('none');">
            <label for="blk_lk_act_unblock">Unblock</label>
        </div>
    </div>
	<div class="col-sm-6 text-right">
    	<div class="radio radio-inline">
            <input type="radio" id="lk_act_unblock" value="open" name="blk_lk_act" onclick="javascript:swap_block_unblock('none',this.value);">
            <label for="lk_act_unblock">Unlock</label>
        </div>
    	<div class="radio radio-inline">
            <input type="radio" id="lk_act_block" value="locked" name="blk_lk_act" onclick="javascript:swap_block_unblock('block',this.value);">
            <label for="lk_act_block">Lock</label>
        </div>
    </div>
</div>
	<div class="row">
	<div class="col-sm-4 col-md-2 col-lg-2">
    	<B>Physician</B>
    </div>
    <div class="col-sm-8 col-md-10 col-lg-10">
    	<select id="blk_lk_prov" name="blk_lk_prov[]" multiple class="selectpicker minimal selecicon" data-width="100%" data-done-button="true" data-actions-box="true">
			<?php 
            for($p = 0; $p < count($arrp); $p++){	
                $sel = "";
                if($arrp[$p]["id"] == $sel_pro_val && $_REQUEST["mode"] == "get"){
                    $sel = "selected";
                }else if($_REQUEST["mode"] != "get"){
                    $sel = "selected";
                }
                ?>
                <option value="<?php echo $arrp[$p]["id"];?>" <?php echo $sel;?>><?php echo core_name_format($arrp[$p]["lname"], $arrp[$p]["fname"], $arrp[$p]["mname"]);?></option>
                <?php
            }
            ?>
        </select>
    </div>
</div>
<div class="row">
	<div class="col-sm-4 col-md-2 col-lg-2">
    	<B>Facility</B>
    </div>
    <div class="col-sm-8 col-md-10 col-lg-10">
    	<select id="blk_lk_loca" name="blk_lk_loca[]" multiple class="selectpicker minimal selecicon" data-width="100%" data-done-button="true" data-actions-box="true">
		<?php 
        for($f = 0; $f < count($arr); $f++){
            $sel = "";
            if($arr[$f]["id"] == $sel_fac_val && $_REQUEST["mode"] == "get"){
                $sel = "selected";
            }else if($_REQUEST["mode"] != "get"){
                $sel = "selected";
            }
            ?>
            <option value="<?php echo $arr[$f]["id"];?>" <?php echo $sel;?>><?php echo $arr[$f]["name"];?></option>
            <?php
        }
        ?>
			</select>
    </div>
</div>
<div class="row">
	<div class="col-sm-4 col-md-2 col-lg-2">
    	<B>Time</B>
    </div>
    <div class="col-sm-8 col-md-10 col-lg-10">
    	<div class="row">
            <div class="col-sm-5">
            	<div class="row">
					<?php	
                    $tm_array = range(1, 12);
                    ?>
                    <div class="col-sm-4">
                        <select id="block_time_from_hour" name="block_time_from_hour" class="form-control minimal">												
                            <?php
                            foreach($tm_array as $tm){
                                if($tm < 10){
                                    $tm = '0'.$tm;
                                }
                                if($tm == $sel_hr && $_REQUEST["mode"] == "get"){
                                    $chk_sel = "selected";
                                }else if($tm == $sel_hr && $_REQUEST["mode"] != "get"){														
                                    $chk_sel = "selected";
                                }						
                                print "<option value='".$tm."' ".$chk_sel.">".$tm."</option>";
                                $chk_sel = "";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select id="block_time_from_mins" name="block_time_from_mins" class="form-control minimal">
                            <?php 
                            $time_drop_value = "";
                            for($min = 0; $min < 60; $min++){
                                if($min < 10){
                                    $min = '0'.$min;
                                }							
                                if($min == $sel_mn && $_REQUEST["mode"] == "get"){
                                    $chk_sel_min = "selected";
                                }else if($min == $sel_mn && $_REQUEST["mode"] != "get"){														
                                    $chk_sel_min = "selected";
                                }
                                $time_drop_value .= "<option value='".$min."' ".$chk_sel_min.">".$min."</option>";
                                $min += (DEFAULT_TIME_SLOT - 1);
                                $chk_sel_min = "";
                            }
                            print $time_drop_value;
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select id="block_ap1" name='block_ap1' class="form-control minimal">													
                            <option value='1' <?php if($sel_am_pm==1) echo "SELECTED";?>>AM</option>
                            <option value='2' <?php if($sel_am_pm==2) echo "SELECTED";?>>PM</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 text-center">
            To
            </div>
            <div class="col-sm-5">
            	<div class="row">
                    <div class="col-sm-4">
                        <select id="block_time_to_hour" name="block_time_to_hour" class="form-control minimal">
                            <option value=''></option>
                            <?php
                            $chk_sel = "";
                            foreach ($tm_array as $tm){							
                                if($tm < 10){
                                    $tm = '0'.$tm;
                                }
                                
                                if($tm == $sel_ed_hr && $_REQUEST["mode"] == "get"){
                                  //  $chk_sel = "selected";
                                }else if($tm == $sel_ed_hr && $_REQUEST["mode"] != "get"){														
                                   // $chk_sel = "selected";
                                }
                                print "<option value='".$tm."' ".$chk_sel.">".$tm."</option>";
                                $chk_sel = "";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select id="block_time_to_mins" name="block_time_to_mins" class="form-control minimal">											<option value=''></option>
                            <?php 
                            $chk_sel_min = "";
                            $time_drop_value = "";
                            for($min = 0; $min < 60; $min++){
                                if($min < 10){
                                    $min = '0'.$min;
                                }							
                                if($min == $sel_ed_mn && $_REQUEST["mode"] == "get"){
                                    //$chk_sel_min = "selected";
                                }else if($min == $sel_ed_mn && $_REQUEST["mode"] != "get"){														
                                   // $chk_sel_min = "selected";
                                }
                                $time_drop_value .= "<option value='".$min."' ".$chk_sel_min.">".$min."</option>";
                                $min += (DEFAULT_TIME_SLOT - 1);
                                $chk_sel_min = "";
                            }
                            print $time_drop_value;
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select id= "block_ap2" name='block_ap2' class="form-control minimal">
                            <option value=''></option>
                            <option value='1' <?php //if($sel_ed_am_pm==1) echo "SELECTED";?>>AM</option>
                            <option value='2' <?php //if($sel_ed_am_pm==2) echo "SELECTED";?>>PM</option>
                        </select>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
<div class="row" id="comments_section">
	<div class="col-sm-4 col-md-2 col-lg-2"><B>Comments</B></div>
    <div class="col-sm-8 col-md-10 col-lg-10">
        <input type="text" id="blk_lk_comment" name="blk_lk_comment" value="<?php echo ucfirst($_REQUEST["act_type"])."ed"; ?>" class="form-control" />
    </div>
</div>

<div class="row">
    <div id="block_warning" class="col-sm-12" style="color:#ff0000">
		Please note that all the appointments, if any, within the Blocked Time range will be moved to Re-Schedule list.
	</div>
</div>
</div>
<div class="clearfix"></div>
~~~~~
<button type="button" class="btn btn-success" onClick="javascript:save_blk_lk();">Save</button>
<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>