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
echo $mn."/".$dt."/".$yr."~~~~~";

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
	{
		$arr[] = $tmpData;
	}
}

$arrp = array();
//selected facilities condition is remove as it was raising issue in cache 18802 :: id in (".$str_sel_pro.") and
$qryp = "select id, fname, lname, mname from users where  Enable_Scheduler = '1' and delete_status = '0' order by lname, fname";
$resp = imw_query($qryp);
if(imw_num_rows($resp) > 0){
	while($tmpData=imw_fetch_assoc($resp))
	{
		$arrp[] = $tmpData;
	}
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


	if(isset($_REQUEST["act_type"]) && $_REQUEST["act_type"] == "unblock"){
		$default_unblk_checked = "checked=\"checked\"";
		$default_blk_checked = "";
	}
	//start time
	$sel_app_tmp = $_REQUEST["ap_sttm"];
	list($sel_hr, $sel_mn, $sel_sc) = explode(":", $sel_app_tmp);
	//echo "$sel_hr, $sel_mn, $sel_sc";
	$sel_am_pm = "1";//pm
	if($sel_hr > 12){
		$sel_hr = (int)$sel_hr - 12;
		if(strlen($sel_hr) < 2){
			$sel_hr = "0".$sel_hr;
		}
		$sel_am_pm = "2";//am
	}
	if($sel_hr >= 12){//additional case to sort out 12 am to pm problem
		$sel_hrn = (int)$sel_hr - 12;
		if($sel_hrn==0)$sel_am_pm = "2";//am
	}
	if(strlen($sel_mn) < 2){
		$sel_mn = "0".$sel_mn;
	}

	//end time
	$sel_hr_tmp = (int)$sel_hr;
	$sel_mn_tmp = (int)$sel_mn;
	list($sel_ed_hr, $sel_ed_mn, $sel_ed_sc) = explode(":", date("h:i:s", mktime($sel_hr_tmp, $sel_mn_tmp + DEFAULT_TIME_SLOT, 0)));
	//echo "$sel_ed_hr, $sel_ed_mn, $sel_ed_sc";

	//selected doctor
	$sel_pro_val = $_REQUEST["ap_doc"];

	//selected facitliy
	$sel_fac_val = $_REQUEST["ap_fac"];
	
	//echo $_REQUEST["ap_lbtx"];

?>

<link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-colorpicker.css" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-formhelpers-colorpicker.js"></script>
<form name="frm_proc_time">
<input type="hidden" name="doremove" value="">
<input type="hidden" name="tempSelectedCache" id="tempSelectedCache" value="">
<div class="blcopt">
    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
            <div class="form-group">
           
            <label for="">Physician</label>
            <select id="label_opt_prov" name="label_opt_prov" class="form-control minimal">
                <?php 
                for($p = 0; $p < count($arrp); $p++){	
                    $sel = "";
                    if($arrp[$p]["id"] == $sel_pro_val){// && $_REQUEST["mode"] == "get"
                        $sel = "selected";
                    }/*
					//that was creating issue n scheduler add/edit labe
					else if($_REQUEST["mode"] != "get"){
                        $sel = "selected";
                    }*/
                    ?>
                    <option value="<?php echo $arrp[$p]["id"];?>" <?php echo $sel;?>><?php echo core_name_format($arrp[$p]["lname"], $arrp[$p]["fname"], $arrp[$p]["mname"]);?></option>
                    <?php
                }
                ?>
            </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <div class="form-group"> 
				<label for="">Facility</label>
            <select id="label_opt_loca" name="label_opt_loca" class="form-control minimal">
            <?php 
            for($f = 0; $f < count($arr); $f++){
                $sel = "";
                if($arr[$f]["id"] == $sel_fac_val){// && $_REQUEST["mode"] == "get"
                    $sel = "selected";
                }/*
				//that was creating issue n scheduler add/edit labe
				else if($_REQUEST["mode"] != "get"){
                    $sel = "selected";
                }*/
                ?>
                <option value="<?php echo $arr[$f]["id"];?>" <?php echo $sel;?>><?php echo $arr[$f]["name"];?></option>
                <?php
            }
            ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
	        <div class="form-group">
            <label for="">From Time</label>
            <div class="row">
                <?php	
                $tm_array = range(1, 12);
                ?>
                <div class="col-sm-4">
                    <select id="label_time_from_hour" name="label_time_from_hour" class="form-control minimal">												
                        <?php
                        foreach($tm_array as $tm){
                            if($tm < 10){
                                $tm = '0'.$tm;
                            }
                            if($tm == $sel_hr){														
                                $chk_sel = "selected";
                            }						
                            print "<option value='".$tm."' ".$chk_sel.">".$tm."</option>";
                            $chk_sel = "";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-4">
                    <select id="label_time_from_mins" name="label_time_from_mins" class="form-control minimal">
                        <?php 
                        $time_drop_value = "";
                        for($min = 0; $min < 60; $min++){
                            if($min < 10){
                                $min = '0'.$min;
                            }							
                            if($min == $sel_mn){														
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
                    <select id="label_ap1" name='label_ap1' class="form-control minimal">													
                        <option value='1' <?php if($sel_am_pm==1) echo "SELECTED";?>>AM</option>
                        <option value='2' <?php if($sel_am_pm==2) echo "SELECTED";?>>PM</option>
                    </select>
                </div>
            </div>
            </div>
        </div>
       
        <div class="col-sm-6">
	        <div class="form-group">
            <label for="">To Time</label>
            <div class="row" id="timeContainer">
                <div class="col-sm-4">
                    <select id="label_time_to_hour" name="label_time_to_hour" class="form-control minimal">
                        <option value=''></option>
                        <?php
                        $chk_sel = "";
                        foreach ($tm_array as $tm){							
                            if($tm < 10){
                                $tm = '0'.$tm;
                            }
                            
                            if($tm == $sel_ed_hr){														
                               // $chk_sel = "selected";
                            }
                            print "<option value='".$tm."' ".$chk_sel.">".$tm."</option>";
                            $chk_sel = "";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-4">
                    <select id="label_time_to_mins" name="label_time_to_mins" class="form-control minimal">											<option value=''></option>
                        <?php 
                        $chk_sel_min = "";
                        $time_drop_value = "";
                        for($min = 0; $min < 60; $min++){
                            if($min < 10){
                                $min = '0'.$min;
                            }							
                            if($min == $sel_ed_mn){														
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
                    <select id= "label_ap2" name='label_ap2' class="form-control minimal">
                        <option value=''></option>
                        <option value='1' <?php //if($sel_ed_am_pm==1) echo "SELECTED";?>>AM</option>
                        <option value='2' <?php //if($sel_ed_am_pm==2) echo "SELECTED";?>>PM</option>
                    </select>
                </div>
           </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
            <label>Type</label>
            <select id="label_type" name="label_type" onchange="javascript:set_reset_options(this.value);" class="form-control minimal">
                <option value="Information">Appt Type</option>
                <option value="Lunch">Lunch</option>
                <option value="Reserved">Reserved</option>
                <option value="Procedure" selected>Procedure</option>
            </select>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
            <label for="">Labels</label>
            <div id="input_acro" style="display:none;">
            <input type="text" name="template_label" id="template_label" class="form-control" value="<?php echo $_REQUEST["ap_lbtx"];?>" /><br><I>Label</I>
            </div>
            <div id="select_acro" style="display:block;">
                <input type="text" name="proc_acro" id="proc_acro" class="form-control" value="<?php echo $_REQUEST["ap_lbtx"];?>" />
            </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
            <label>Color</label>
            <input type="text" class="grid_color_picker" name="label_color" id="label_color" value="">
            </div>
        </div>
    </div>
    <div class="row" id="show_proc_options" style="display:block;">
    	<div class="col-sm-12 text-left">
            <div class="form-group">
                <label for="">List of All Procedures</label>
                <div id="divAvailableOptions">
                    <select id="availableOptions" name="availableOptions[]" multiple  class="selectpicker minimal selecicon form-control" data-done-button="true" data-width="100%">
                <?php
					$sql_proc = "SELECT id, acronym, proc, proc_color FROM slot_procedures WHERE doctor_id = 0 AND proc != '' AND active_status = 'yes' and source='' group by proc order by acronym ASC";
                    $res_proc = imw_query($sql_proc);					
                    $arr_lbtx = array();
                    $arr_lbtx = explode(";", $_REQUEST["ap_lbtx"]);					
                    if(count($arr_lbtx) > 0){
                        $arr_lbtx_tmp = $arr_lbtx;
                        $zzz = 0;
                        foreach($arr_lbtx_tmp as $this_lbtx){
                            $arr_lbtx[$zzz] = trim($this_lbtx);
                            $zzz++;
                        }
                    }					
    
                     if(imw_num_rows($res_proc) > 0){
                        while($this_proc=imw_fetch_assoc($res_proc))
                        {	?>
                            <option value="<?php echo $this_proc["id"].'~~~'.$this_proc["acronym"];?>"><?php echo $this_proc["acronym"];?></option>
                            <?php
                        }
                        
                    }
                    ?>                                     
                    </select> 
                </div>
            </div>
        </div>
    </div>
        
</div>
~~~~~
<button type="button" class="btn btn-success" value="Save" onclick="javascript:save_label_options('save');">Save</button>
<button type="button" class="btn btn-danger" value="Remove" onclick="javascript:save_label_options('remove');">Remove</button>
<button type="button" class="btn btn-success" value="Default" onclick="javascript:save_label_options('default');">Default</button>
<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</form>
<script type="text/javascript">
$(document).on('change', '#availableOptions', function(e) {
	if($("#availableOptions").val())
	{
		$("#tempSelectedCache").val(($("#availableOptions").val()).join("~:~"));
		var arrReturn = $("#availableOptions").val();
		var strLen = arrReturn.length;
		strReturn = "";
		for(i = 0; i < strLen; i++){
			var arrTemp = arrReturn[i].split("~~~");
			strReturn += arrTemp[1]+"; ";
		} 
		var strLength = parseInt(strReturn.length)-2;
		strReturn = strReturn.substring(0,strLength);
		$("#proc_acro").val(strReturn);
	}
	else
	{
		$("#tempSelectedCache").val('');
		$("#proc_acro").val('');	
	}
	
});
</script>