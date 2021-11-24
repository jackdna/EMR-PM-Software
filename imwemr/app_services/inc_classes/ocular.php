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
class ocular extends medical_hx{
	
	public function __construct(){
		parent::__construct();
	}
	function get_ocular(){
		$arrReturn = array();
		$this->db_obj->qry = "SELECT *
								FROM ocular 
								WHERE patient_id='".$this->patient."' 
								LIMIT 1
								";
		$result = $this->db_obj->get_resultset_array();	
		
		$arrReturn['eye_history']["do_you_wear"] = ($result[0]['you_wear']!="")?$result[0]['you_wear']:'';
		$arrReturn['eye_history']["last_exam_date"] = ($result[0]['last_exam_date']!="")?$result[0]['last_exam_date']:'';
		
		$arrCondYou = explode(",",$result[0]['any_conditions_you']);
		$arrReturn['any_condition_you']["dry_eyes"][] = (in_array("1",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["macular_degeneration"][] = (in_array("2",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["glaucoma"][] = (in_array("3",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["retinal_detachment"][] = (in_array("4",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["cataracts"][] = (in_array("5",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["keratoconus"][] = (in_array("6",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["others"][] = ($result[0]['any_conditions_others_you']!=NULL)?$result[0]['any_conditions_others_you']:'0';
		
		$arrCondYouComm = explode("~|~",$result[0]['chronicDesc']);
		$arrCondYouComm = explode("~!!~~",$arrCondYouComm[0]);
		$arrComm = array();
		foreach($arrCondYouComm as $comm){
			$arrTmp = explode(':*:',$comm);
			$arrComm[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_you']["dry_eyes"][] = ($arrComm[1]!="")?$arrComm[1]:'';
		$arrReturn['any_condition_you']["macular_degeneration"][] = ($arrComm[2]!="")?$arrComm[2]:'';
		$arrReturn['any_condition_you']["glaucoma"][] = ($arrComm[3]!="")?$arrComm[3]:'';
		$arrReturn['any_condition_you']["retinal_detachment"][] = ($arrComm[4]!="")?$arrComm[4]:'';
		$arrReturn['any_condition_you']["cataracts"][] = ($arrComm[5]!="")?$arrComm[5]:'';
		$arrReturn['any_condition_you']["keratoconus"][] = ($arrComm[6]!="")?$arrComm[6]:'';
		$arrReturn['any_condition_you']["others"][] = ($arrComm['other']!=NULL)?$arrComm['other']:'0';;
		
		$arrCondYou = explode(",",$result[0]['any_conditions_relative']);
		$arrReturn['any_condition_rel']["dry_eyes"][] = (in_array("1",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["macular_degeneration"][] = (in_array("2",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["glaucoma"][] = (in_array("3",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["retinal_detachment"][] = (in_array("4",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["cataracts"][] = (in_array("5",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["keratoconus"][] = (in_array("6",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["others"][] = ($result[0]['any_conditions_others_you']!=NULL)?$result[0]['any_conditions_others_you']:'0';
		
		$arrCondRelComm = explode("~|~",$result[0]['chronicDesc']);
		$arrCondRelComm = explode("~!!~~",$arrCondRelComm[1]);
		$arrRelComm = array();
		foreach($arrCondRelComm as $comm){
			$arrTmp = explode(':*:',$comm);
			$arrRelComm[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_rel']["dry_eyes"][] = ($arrRelComm[1]!="")?$arrRelComm[1]:'';
		$arrReturn['any_condition_rel']["macular_degeneration"][] = ($arrRelComm[2]!="")?$arrRelComm[2]:'';
		$arrReturn['any_condition_rel']["glaucoma"][] = ($arrRelComm[3]!="")?$arrRelComm[3]:'';
		$arrReturn['any_condition_rel']["retinal_detachment"][] = ($arrRelComm[4]!="")?$arrRelComm[4]:'';
		$arrReturn['any_condition_rel']["cataracts"][] = ($arrRelComm[5]!="")?$arrRelComm[5]:'';
		$arrReturn['any_condition_rel']["keratoconus"][] = ($arrRelComm[6]!="")?$arrRelComm[6]:'';
		$arrReturn['any_condition_rel']["others"][] =($arrRelComm['other']!=NULL)?$arrRelComm['other']:'';
		
		$arrCondRelDrop = explode("~!!~~",$result[0]['chronicRelative']);
		$arrRelDrop = array();
		foreach($arrCondRelDrop as $drop){
			$arrTmp = explode(':*:',$drop);
			$arrRelDrop[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_rel']["dry_eyes"][] = ($arrRelDrop[1]!="")?$arrRelDrop[1]:'';
		$arrReturn['any_condition_rel']["macular_degeneration"][] = ($arrRelDrop[2]!="")?$arrRelDrop[2]:'';
		$arrReturn['any_condition_rel']["glaucoma"][] = ($arrRelDrop[3]!="")?$arrRelDrop[3]:'';
		$arrReturn['any_condition_rel']["retinal_detachment"][] = ($arrRelDrop[4]!="")?$arrRelDrop[4]:'';
		$arrReturn['any_condition_rel']["cataracts"][] = ($arrRelDrop[5]!="")?$arrRelDrop[5]:'';
		$arrReturn['any_condition_rel']["keratoconus"][] = ($arrRelDrop[6]!="")?$arrRelDrop[6]:'';
		$arrReturn['any_condition_rel']["others"][] = ($arrRelDrop['other']!=NULL)?$arrRelDrop['other']:'';
		return $arrReturn;
	}
	
	
	function get_ocular_app(){
		$arrReturn = array();
		$this->db_obj->qry = "SELECT *
								FROM ocular 
								WHERE patient_id='".$this->patient."' 
								LIMIT 1
								";
		 $result = $this->db_obj->get_resultset_array();	
		
		 $arrReturn['eye_history']["do_you_wear"] = ($result[0]['you_wear']!="")?$result[0]['you_wear']:'';
		 $arrReturn['eye_history']["last_exam_date"] = ($result[0]['last_exam_date']!="")?$result[0]['last_exam_date']:'';
		
		$arrCondYou = explode(",",$result[0]['any_conditions_you']);
		$arrReturn['any_condition_you']["dry_eyes"]["key"] = (in_array("1",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["macular_degeneration"]["key"] = (in_array("2",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["glaucoma"]["key"] = (in_array("3",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["retinal_detachment"]["key"] = (in_array("4",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["cataracts"]["key"] = (in_array("5",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["keratoconus"]["key"] = (in_array("6",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_you']["others"]["key"] = ($result[0]['any_conditions_others_you']!=NULL)?$result[0]['any_conditions_others_you']:'0';
		
		$arrCondYouComm = explode("~|~",$result[0]['chronicDesc']);
		$arrCondYouComm = explode("~!!~~",$arrCondYouComm[0]);
		$arrComm = array();
		foreach($arrCondYouComm as $comm){
			$arrTmp = explode(':*:',$comm);
			$arrComm[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_you']["dry_eyes"]["desc"] = ($arrComm[1]!="")?$arrComm[1]:'';
		$arrReturn['any_condition_you']["macular_degeneration"]["desc"] = ($arrComm[2]!="")?$arrComm[2]:'';
		$arrReturn['any_condition_you']["glaucoma"]["desc"] = ($arrComm[3]!="")?$arrComm[3]:'';
		$arrReturn['any_condition_you']["retinal_detachment"]["desc"] = ($arrComm[4]!="")?$arrComm[4]:'';
		$arrReturn['any_condition_you']["cataracts"]["desc"] = ($arrComm[5]!="")?$arrComm[5]:'';
		$arrReturn['any_condition_you']["keratoconus"]["desc"] = ($arrComm[6]!="")?$arrComm[6]:'';
		$arrReturn['any_condition_you']["others"]["desc"] = ($arrComm['other']!=NULL)?$arrComm['other']:'0';;
		
		$arrCondYou = explode(",",$result[0]['any_conditions_relative']);
		$arrReturn['any_condition_rel']["dry_eyes"]["key"] = (in_array("1",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["macular_degeneration"]["key"] = (in_array("2",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["glaucoma"]["key"] = (in_array("3",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["retinal_detachment"]["key"] = (in_array("4",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["cataracts"]["key"] = (in_array("5",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["keratoconus"]["key"] = (in_array("6",$arrCondYou))?'1':'0';
		$arrReturn['any_condition_rel']["others"]["key"] = ($result[0]['any_conditions_others_you']!=NULL)?$result[0]['any_conditions_others_you']:'0';
		
		$arrCondRelComm = explode("~|~",$result[0]['chronicDesc']);
		$arrCondRelComm = explode("~!!~~",$arrCondRelComm[1]);
		$arrRelComm = array();
		foreach($arrCondRelComm as $comm){
			$arrTmp = explode(':*:',$comm);
			$arrRelComm[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_rel']["dry_eyes"]["desc"] = ($arrRelComm[1]!="")?$arrRelComm[1]:'';
		$arrReturn['any_condition_rel']["macular_degeneration"]["desc"] = ($arrRelComm[2]!="")?$arrRelComm[2]:'';
		$arrReturn['any_condition_rel']["glaucoma"]["desc"] = ($arrRelComm[3]!="")?$arrRelComm[3]:'';
		$arrReturn['any_condition_rel']["retinal_detachment"]["desc"] = ($arrRelComm[4]!="")?$arrRelComm[4]:'';
		$arrReturn['any_condition_rel']["cataracts"]["desc"] = ($arrRelComm[5]!="")?$arrRelComm[5]:'';
		$arrReturn['any_condition_rel']["keratoconus"]["desc"] = ($arrRelComm[6]!="")?$arrRelComm[6]:'';
		$arrReturn['any_condition_rel']["others"]["desc"] =($arrRelComm['other']!=NULL)?$arrRelComm['other']:'';
		
		$arrCondRelDrop = explode("~!!~~",$result[0]['chronicRelative']);
		//$arrRelDrop = array();
		foreach($arrCondRelDrop as $drop){
			$arrTmp = explode(':*:',$drop);
			$arrRelDrop[$arrTmp[0]] = $arrTmp[1];
		}
		$arrReturn['any_condition_rel']["dry_eyes"]["name"] = ($arrRelDrop[1]!="")?$arrRelDrop[1]:'';
		$arrReturn['any_condition_rel']["macular_degeneration"]["name"] = ($arrRelDrop[2]!="")?$arrRelDrop[2]:'';
		$arrReturn['any_condition_rel']["glaucoma"]["name"] = ($arrRelDrop[3]!="")?$arrRelDrop[3]:'';
		$arrReturn['any_condition_rel']["retinal_detachment"]["name"] = ($arrRelDrop[4]!="")?$arrRelDrop[4]:'';
		$arrReturn['any_condition_rel']["cataracts"]["name"] = ($arrRelDrop[5]!="")?$arrRelDrop[5]:'';
		$arrReturn['any_condition_rel']["keratoconus"]["name"] = ($arrRelDrop[6]!="")?$arrRelDrop[6]:'';
		$arrReturn['any_condition_rel']["others"]["name"] = ($arrRelDrop['other']!=NULL)?$arrRelDrop['other']:'';
		return $record[] = $arrReturn;
		
	}
}
?>