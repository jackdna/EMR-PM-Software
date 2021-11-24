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
$arr_fac = $arr_pro = array();
if(((int)$_REQUEST["level"] == 1) && ((string)$_REQUEST["sel_pro"] == "all")){
	//getting fac and prov available for this day
	$qrySelFac = "select sa_facility_id, sa_doctor_id, count(id) as totApp from schedule_appointments where sa_app_start_date = '".$_REQUEST["load_dt"]."' and sa_doctor_id IN (".$_REQUEST["selProCombo"].") 
					and sa_patient_app_status_id NOT IN (203,201,18,19,20) GROUP BY sa_doctor_id, sa_facility_id HAVING totApp > 0";
	$rsSelFac = imw_query($qrySelFac);
	if(imw_num_rows($rsSelFac) > 0){
		while($rowSelFac = imw_fetch_array($rsSelFac)){
			$dbFacIdApp = $dbProIdApp = 0;
			$dbFacIdApp = $rowSelFac["sa_facility_id"];
			$dbProIdApp = $rowSelFac["sa_doctor_id"];
			$arr_fac[] = $dbFacIdApp;
			$arr_pro[] = $dbProIdApp;			
		}
		//imw_free_result($rsSelFac);
		$arr_fac = array_unique($arr_fac);
		$arr_pro = array_unique($arr_pro);
	}
	$arr_default = $obj_scheduler->get_prov_default_facilities($_SESSION["authId"]);
	$str_sel_pro = $_REQUEST["selProCombo"];
	$str_sel_fac = "'".implode("','", $arr_default)."'";
}
elseif(((int)$_REQUEST["level"] == 2) && ((int)$_REQUEST["sel_pro"] > 0)){
	$providerFile=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml/".$_REQUEST["load_dt"]."-".$_REQUEST["sel_pro"].".sch";
	if(file_exists($providerFile)){
		$providerFileData = "";
		$providerFileData = file_get_contents($providerFile);
		$arrProviderFileData = array();
		$arrProviderFileData = unserialize($providerFileData);
		$str_sel_fac = $arrProviderFileData[$_REQUEST["sel_pro"]]["fac_ids"];
		$str_sel_pro = $_REQUEST["sel_pro"];
	}
}
$arr = array();
$qry = "select id, name from facility where id in (".$str_sel_fac.") order by name";
$res = imw_query($qry);
if(imw_num_rows($res) > 0){
	while($tmpData=imw_fetch_assoc($res))
	{
		$arr[]=$tmpData;	
	}
}

$arrp = array();
$qryp = "select id, fname, lname, mname from users where id in (".$str_sel_pro.") and Enable_Scheduler = '1' and delete_status = '0' order by lname, fname";
$resp = imw_query($qryp);
if(imw_num_rows($resp) > 0){
	while($tmpData=imw_fetch_assoc($resp))
	{
		$arrp[]=$tmpData;	
	}
}
?>
<div class="row">
	<div class="col-sm-4">
    	<div class="radio">
            <input type="radio" name="print_act" id="print_fullday" value="block" checked>
            <label for="print_fullday">
                Fullday
            </label>
        </div>
    </div>
	<div class="col-sm-4">
        <div class="radio">
            <input type="radio" name="print_act" id="print_morning" value="open">
            <label for="print_morning">
                Morning
            </label>
        </div>
    </div>
	<div class="col-sm-4">
        <div class="radio">
            <input type="radio" name="print_act" id="print_evening" value="open">
            <label for="print_evening">
                Afternoon
            </label>
        </div>
    </div>
</div>

<div class="row">
	<div class="col-sm-6">
    	<div class="form-group">
            <label for="">Physician</label><br>

            <select id="print_prov" name="print_prov[]" multiple class="selectpicker minimal selecicon" data-width="100%" data-actions-box="true" data-done-button="true">
				<?php 
				for($p = 0; $p < count($arrp); $p++){
					$sel = "";
					if($_REQUEST["sel_pro"] != "all" && $_REQUEST["sel_pro"] == $arrp[$p]["id"]){
						$sel = "selected";
					}else if($_REQUEST["sel_pro"] == "all"){
						foreach($arr_pro as $intVal){
							if($arrp[$p]["id"] == $intVal){
								$sel = "selected";
							}	
						}
					}
					?>
					<option value="<?php echo $arrp[$p]["id"];?>" <?php echo $sel;?>><?php echo core_name_format($arrp[$p]["lname"], $arrp[$p]["fname"], $arrp[$p]["mname"]);?></option>
					<?php
				}
				?>
			</select>
       </div>
    </div>
	<div class="col-sm-6">
    	<div class="form-group">
            <label for="">Facility</label><br>

            <select id="print_loca" name="print_loca[]" multiple class="selectpicker minimal selecicon" data-width="100%" data-actions-box="true" data-done-button="true">
				<?php 
				for($f = 0; $f < count($arr); $f++){
					if((int)$_REQUEST["level"] == 1){
						$sel = "";
						foreach($arr_fac as $intVal){
							if($arr[$f]["id"] == $intVal){
								$sel = "selected";
							}	
						}	
					}
					elseif((int)$_REQUEST["level"] == 2){
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
</div><div class="clearfix"></div>
~~~~~<button type="button" class="btn btn-success" onclick="javascript:day_print_process('<?php echo $_REQUEST["load_dt"];?>');" value="Print">Print</button>
<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>