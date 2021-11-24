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

$ap_doc = $_REQUEST['ap_doc'];

//setting date format
$load_d=get_date_format($_REQUEST["load_dt"],'','','',"/")."~~~~~";
echo $load_d;

//getting fac and prov available for this day
$order_file=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml/".$_REQUEST["load_dt"].'-order.sch';
if(file_exists($order_file)){
	$str_tmp_order = file_get_contents($order_file);
	list($str_order, $str_fac, $str_prov_time, $str_office_time) = explode("~~~~~", $str_tmp_order);
}

// GET NAME OF PHYSICIAN
$qry0 = "select fname, mname , lname from users where id = ".$ap_doc;
$res0 = imw_query($qry0);
if(imw_num_rows($res0) > 0){
	$arr0 = imw_fetch_assoc($res0);
	$phy_name = $arr0["lname"].", ".$arr0["fname"];
}


$sel_am_pm = $sel_hr = $sel_mn = $sel_sc = $sel_appt_tm = $sel_ed_hr = $sel_ed_mn = $sel_ed_am_pm = "";

// GET START - END TIME (FOR SINGLE PROVIDER OR OFFICE TIME)
if($ap_doc !='')
{
	$arr_prov_time = unserialize($str_prov_time);
	$pro_id = $arrp[0]['id'];

	$ret_time = $obj_scheduler->getAmPmTime($arr_prov_time[$ap_doc]['st']);
	list($sel_hr, $sel_mn, $sel_am_pm) = explode("~", $ret_time);
	
	$ret_time = $obj_scheduler->getAmPmTime($arr_prov_time[$ap_doc]['ed']);
	list($sel_ed_hr, $sel_ed_mn, $sel_ed_am_pm) = explode("~", $ret_time);
}


$ap_fac = $_REQUEST['ap_fac'];
$arr = array();
$qry = "select id, name from facility where id in (".$ap_fac.") order by name";
$res = imw_query($qry);
if(imw_num_rows($res) > 0){
	while($tmpData = imw_fetch_assoc($res))
	{
		$arr[] = $tmpData;
	}
}
?>
<div class="row">
	<div class="col-sm-4 text-left">Physician</div>
	<div class="col-sm-6 text-left"><?php echo $phy_name; ?></div>
</div>
<div class="row">
	<div class="col-sm-4 text-left">Facility
    	<input name="phy_id" id="phy_id" type="hidden" value="<?php echo $ap_doc;?>" />
        <input name="todo_time_from_hour" id="todo_time_from_hour" type="hidden" value="<?php echo $sel_hr;?>" />
        <input name="todo_time_from_mins" id="todo_time_from_mins" type="hidden" value="<?php echo $sel_mn;?>" />
        <input name="todo_ap1" id="todo_ap1" type="hidden" value="<?php echo $sel_am_pm;?>" />
        <input name="todo_time_to_hour" id="todo_time_to_hour" type="hidden" value="<?php echo $sel_ed_hr;?>" />
        <input name="todo_time_to_mins" id="todo_time_to_mins" type="hidden" value="<?php echo $sel_ed_mn;?>" />
        <input name="todo_ap2" id="todo_ap2" type="hidden" value="<?php echo $sel_ed_am_pm;?>" /></div>
	<div class="col-sm-6 text-left multiselect"> 
        <select id="blk_lk_loca" class="selectpicker minimal selecicon" name="blk_lk_loca[]" multiple="multiple">
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