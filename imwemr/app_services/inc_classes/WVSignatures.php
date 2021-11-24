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
/*
File: AssessPlan.php
Purpose: This file provides chartnote Assessment and plans of a patient visit.
Access Type : Include file
*/
?>
<?php
//GetWVSummery
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');

class WVSignatures extends patient_app{
	public $ar_req;
	
	private $patient_id;
	private $form_id;
	private $encounter_id;
	
	public function __construct($patient_id, $form_id){
		parent::__construct();
		$this->patient_id= $patient_id;
		$this->form_id = $form_id;		
	}
	
	public function getWVSignatures(){	
	
		include_once($GLOBALS['incdir']."/chart_notes/onload_wv_functions.inc.php");
		
		$arrMainRet=array();
		
		$sql = "SELECT pro_id,c1.sign_path,sign_type, fname, mname,lname   FROM chart_signatures c1
				LEFT JOIN users c2 ON c1.pro_id = c2.id
				WHERE form_id = '".$this->form_id."' AND c1.sign_path!='' ORDER BY c1.id ";
		$rez = sqlStatement($sql);
		for($i=0; $row=sqlFetchArray($rez); $i++){
			
			$pro_id=$row["pro_id"];
			$sign_path=attachHttp2file($row["sign_path"]);
			$sign_type=$row["sign_type"];
			$arrMainRet[] = array("pro_id"=>$pro_id, "sign_path"=>$sign_path, "sign_type"=>$sign_type, 
							"provider"=>$row["fname"]." ".$row["mname"]." ".$row["lname"]);
			
		}
		
		return $arrMainRet; 		
	}	
}

?>