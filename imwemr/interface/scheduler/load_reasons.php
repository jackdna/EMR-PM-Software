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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');

$pt_id = (isset($_REQUEST["pt_id"]) && !empty($_REQUEST["pt_id"])) ? $_REQUEST["pt_id"] : 0;
$ap_id = (isset($_REQUEST["ap_id"]) && !empty($_REQUEST["ap_id"])) ? $_REQUEST["ap_id"] : 0;

if($_REQUEST['ctype']=='first_available')
{
	$str_appt_details = "";
	if($pt_id > 0 && $ap_id > 0){
		$qry = "SELECT sa.sa_patient_name, sa.sa_app_time, sa.sa_patient_id, pr.acronym, pr.proc, pt.phone_home FROM schedule_appointments sa LEFT JOIN patient_data pt ON pt.id = sa.sa_patient_id LEFT JOIN slot_procedures pr ON pr.id = sa.procedureid WHERE sa.id = '".$ap_id."'";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			$str_appt_details = "(".$arr["acronym"].") - ";
			$str_appt_details .= $arr["sa_patient_name"]." - ".$arr["sa_patient_id"]." ";
			$str_appt_details .= $arr["phone_home"];
			$sa_app_time= $arr["sa_app_time"];
		}
	}


	echo htmlentities($str_appt_details);
	echo "~~~~~";
	echo load_reasons("OPTIONS");
	if($_REQUEST["cancel"] == 1):
	echo "~~~~~";
	
	//get present appointment time
	list($date,$time)= explode(' ',$sa_app_time);
	//get date parameter
	list($y,$m,$d)= explode('-',$date);
	$w=date('W',mktime(0,0,0,$m,$d,$y));
	?>
    <div class="row">
    	<div class="col-sm-4">
    	<div class="form-group">
        	<label for="">Month</label>
            <select name="month" id="month" class="form-control minimal" onChange="getWks(this.value)">
            <option value="">Month</option>
            <?php
            $curr_mon=$month_g." ".$c_yy;
            $load_date=$c_yy."-".$c_mm."-01";
            $curn_year=date('Y');
            $current_month=(date('m')+1);
            $loop_end=($current_month+11);//(12+(12-($current_month)));
            
            for($mon=$current_month;$mon<=$loop_end;$mon++){
                $month_v=date("F", mktime(0, 0, 0, $mon, 0, $curn_year));
                $c_mon=($mon);
                $c_mon=($c_mon-1);
                if($c_mon>12){$c_mon=($c_mon-12);}
                if($c_mon>12){$c_mon=($c_mon-12);}
                
                //if($curn_year=='2016'){echo $date_vla;echo "<br>";die();}
                if(strlen($c_mon)==1){$c_mon="0".$c_mon;}
                $date_vla=$curn_year."-".$c_mon;
                $sel_load_date="";
                if($c_mon==date('m')){
                    $sel_load_date=" SELECTED ";
                }
                
                $select_month.="<option ".$sel_load_date." value=\"".$date_vla."\">".$month_v." ".$curn_year."</option>";
                if(strtolower($month_v)=="december"){
                    $curn_year++;
                }
            }
            echo $select_month;
            ?>
        	</select>
        </div>
    
    </div>
    	<div class="col-sm-6">
    	<div class="form-group">
            <label for="">Week</label>
            <select name="week" id="week" class="form-control minimal">
                <?php
				$weekArr=getDatesForWK($m,$y);
				for($wk=1;$wk<=weeks_in_month($m, $y);$wk++)
				{
					$str=$weekArr[$wk];
					$str=trim($str);
					$sbstr=substr($str,0,strlen($weekArr[$wk])-2);
					$month=date('M', mktime(0,0,0,$m,01,$y));
					
					echo'<option value="'.$wk.'"';
					echo($wk==$w)?' selected':'';
					echo'>Week '.$wk.' ['.$sbstr.' '.$month.']</option>';
				}
				?>
            </select>
        </div>
    </div>
        <div class="col-sm-2">
            <div class="form-group">
                <label for="">Time</label>
                <select name="time" id="time" class="form-control minimal">
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>
        </div>
	</div>
    <div class="row">
    	<div class="col-sm-12">
            <div class="checkbox checkbox-inline">
                <input type="checkbox" id="keep_sa" name="keep_sa" value="keep_sa">
                <label for="keep_sa"> Keep appt. scheduled </label>
            </div>
        </div>
    </div>
	<?php
	endif;
}
else
{
	$str_appt_details = "";
	if($pt_id > 0 && $ap_id > 0){
		$qry = "SELECT sa.sa_patient_name, sa.sa_patient_id, pr.acronym, pr.proc, pt.phone_home FROM schedule_appointments sa LEFT JOIN patient_data pt ON pt.id = sa.sa_patient_id LEFT JOIN slot_procedures pr ON pr.id = sa.procedureid WHERE sa.id = '".$ap_id."'";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			$str_appt_details = "(".$arr["acronym"].") - ";
			$str_appt_details .= $arr["sa_patient_name"]." - ".$arr["sa_patient_id"]." ";
			$str_appt_details .= $arr["phone_home"];
		}
	}


	echo htmlentities($str_appt_details);
	echo "~~~~~";
	echo load_reasons("OPTIONS");
	if($_REQUEST["cancel"] == 1):
	echo "~~~~~";
	echo'<div class="row">
			<div class="col-sm-12">
				<div class="radio radio-inline">
					<input type="radio" id="fa_cancel_rd" value="cancel" name="fa_action" checked>
					<label for="sch_timing_afternoon"> Cancel </label>
				</div>
			</div>
    	</div>';

	endif;
}

?>
