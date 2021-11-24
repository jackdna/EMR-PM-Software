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
<?php  
include_once(dirname(__FILE__).'/medical_hx.php');
class gen_health extends medical_hx{
	
	public function __construct(){
		parent::__construct();
	}
	function get_gen_health(){
		$arrReturn = array();
		$this->db_obj->qry = "SELECT *
								FROM general_medicine 
								WHERE patient_id='".$this->patient."' 
								LIMIT 1
								";
		$result = $this->db_obj->get_resultset_array();	
		
		$arrCondYou = explode(",",$result[0]['any_conditions_you']);
		$arrReturn['any_condition_you']["high_blood_pressure"][] = (in_array("1",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["heart_problem"][] = (in_array("2",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["arthritis"][] = (in_array("7",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["lung_problems"][] = (in_array("4",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["stroke"][] = (in_array("5",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["thyroid_problems"][] = (in_array("6",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["diabetes"][] = (in_array("3",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["ldl"][] = (in_array("13",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["ulcers"][] = (in_array("8",$arrCondYou))?1:0;
		$arrReturn['any_condition_you']["cancer"][] = (in_array("14",$arrCondYou))?1:0;
		
		$arrCondRel = explode(",",$result[0]['any_conditions_relative']);
		$arrReturn['any_condition_rel']["high_blood_pressure"][] = (in_array("1",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["heart_problem"][] = (in_array("2",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["arthritis"][] = (in_array("7",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["lung_problems"][] = (in_array("4",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["stroke"][] = (in_array("5",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["thyroid_problems"][] = (in_array("6",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["diabetes"][] = (in_array("3",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["ldl"][] = (in_array("13",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["ulcers"][] = (in_array("8",$arrCondRel))?1:0;
		$arrReturn['any_condition_rel']["cancer"][] = (in_array("14",$arrCondRel))?1:0;
		
		$arrCondOth = explode(",",$result[0]['any_conditions_others_both']);
		$arrReturn['any_condition_you']["other"][] = (in_array("1",$arrCondOth))?1:0;
		$arrReturn['any_condition_rel']["other"][] = (in_array("2",$arrCondOth))?1:0;
		
		/*$arrCondYouComm = explode("~|~",$result[0]['chronicDesc']);
		$arrCondYouComm = explode("~!!~~",$arrCondYouComm[0]);
		$arrComm = array();
		foreach($arrCondYouComm as $comm){
			$arrTmp = explode(':*:',$comm);
			$arrComm[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_you']["high_blood_pressure"][] = $arrComm[1];
		$arrReturn['any_condition_you']["heart_problem"][] = $arrComm[2];
		$arrReturn['any_condition_you']["arthritis"][] = $arrComm[7];
		$arrReturn['any_condition_you']["lung_problems"][] = $arrComm[4];
		$arrReturn['any_condition_you']["stroke"][] = $arrComm[5];
		$arrReturn['any_condition_you']["thyroid_problems"][] = $arrComm[6];
		$arrReturn['any_condition_you']["diabetes"][] = $arrComm[3];
		$arrReturn['any_condition_you']["ldl"][] = $arrComm[13];
		$arrReturn['any_condition_you']["ulcers"][] = $arrComm[8];
		$arrReturn['any_condition_you']["cancer"][] = $arrComm[14];
		$arrReturn['any_condition_you']["other"][] = $arrComm[6];*/
		
		
		
		/*$arrCondRelComm = explode("~|~",$result[0]['chronicDesc']);
		$arrCondRelComm = explode("~!!~~",$arrCondRelComm[1]);
		$arrRelComm = array();
		foreach($arrCondRelComm as $comm){
			$arrTmp = explode(':*:',$comm);
			$arrRelComm[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_rel']["high_blood_pressure"][] = $arrRelComm[1];
		$arrReturn['any_condition_rel']["heart_problem"][] = $arrRelComm[2];
		$arrReturn['any_condition_rel']["arthritis"][] = $arrRelComm[7];
		$arrReturn['any_condition_rel']["lung_problems"][] = $arrRelComm[4];
		$arrReturn['any_condition_rel']["stroke"][] = $arrRelComm[5];
		$arrReturn['any_condition_rel']["thyroid_problems"][] = $arrRelComm[6];
		$arrReturn['any_condition_rel']["diabetes"][] = $arrRelComm[3];
		$arrReturn['any_condition_rel']["ldl"][] = $arrRelComm[13];
		$arrReturn['any_condition_rel']["ulcers"][] = $arrRelComm[8];
		$arrReturn['any_condition_rel']["cancer"][] = $arrRelComm[14];
		$arrReturn['any_condition_rel']["other"][] = $arrRelComm[6];
		
		$arrCondRelDrop = explode("~!!~~",$result[0]['chronicRelative']);
		$arrRelDrop = array();
		foreach($arrCondRelDrop as $drop){
			$arrTmp = explode(':*:',$drop);
			$arrRelDrop[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_rel']["high_blood_pressure"][] = $arrRelDrop[1];
		$arrReturn['any_condition_rel']["heart_problem"][] = $arrRelDrop[2];
		$arrReturn['any_condition_rel']["arthritis"][] = $arrRelDrop[7];
		$arrReturn['any_condition_rel']["lung_problems"][] = $arrRelDrop[4];
		$arrReturn['any_condition_rel']["stroke"][] = $arrRelDrop[5];
		$arrReturn['any_condition_rel']["thyroid_problems"][] = $arrRelDrop[6];
		$arrReturn['any_condition_rel']["diabetes"][] = $arrRelDrop[3];
		$arrReturn['any_condition_rel']["ldl"][] = $arrRelDrop[13];
		$arrReturn['any_condition_rel']["ulcers"][] = $arrRelDrop[8];
		$arrReturn['any_condition_rel']["cancer"][] = $arrRelDrop[14];
		$arrReturn['any_condition_rel']["other"][] = $arrRelDrop[6];*/
		return $arrReturn;
	}
}
?>