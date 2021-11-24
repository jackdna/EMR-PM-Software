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

class PtSurgery extends patient_app{
	public $ar_req;
	
	private $patient_id;
	private $form_id;
	private $encounter_id;
	
	public function __construct($patient_id){
		parent::__construct();
		$this->patient_id= $patient_id;				
	}
	
	public function getOculerSx(){
		
		require_once $GLOBALS["incdir"]."/chart_notes/common/functions.php";
		require_once $GLOBALS["incdir"]."/main/main_functions.php";
		
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;
		$arrMainRet=array();
		//--
		$getArrStr = "select title,DATE_FORMAT(begdate, '%m-%d-%Y') as begdate,referredby,comments,sites from lists 
						where type IN ('6') and pid = '".$this->patient_id."' 
						and allergy_status = 'Active' 
						ORDER BY begdate DESC ";
		$rez = sqlStatement($getArrStr);		
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$sites="";
			if($row["sites"]=="3"){$sites="OU";}else if($sites=="2"){$sites="OD";}else if($sites=="1"){$sites="OS";}
			
			$arrMainRet[]["Name"]="".$row["title"];
			$arrMainRet[]["Site"]=$sites;
			$arrMainRet[]["Date_of_Surgery"]=$row["begdate"];
			$arrMainRet[]["Physician"]=$row["referredby"];
			$arrMainRet[]["Comments"]=$row["comments"];	
		}		
		//--
		
		return $arrMainRet; 		
	}	
}

?>