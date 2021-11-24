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
set_time_limit(900);
include_once(dirname(__FILE__).'/user_app.php');
class chart_notes extends user_app{	
	var $reqModule;
	var $arrProvider = array();
	var $form_id = 0;
	var $finalized = 0;
	public function __construct(){
		parent::__construct();
		$this->form_id = $_REQUEST['form_id'];
		$this->finalized = $this->get_chart_status();
		
	}
	function get_chart_details(){
		
		$arrReturn = array("DOS"=>"","facility"=>"","enbl_finalize_btn"=>1);
		$this->db_obj->qry = "SELECT cmt.date_of_service,
									 fac.name AS facility_name
								FROM  chart_master_table cmt 
								LEFT JOIN facility fac ON cmt.facilityid = fac.id
								WHERE cmt.patient_id = '".$this->patient."' 
									AND cmt.id = '".$this->form_id."' 
								";
		$result = $this->db_obj->get_resultset_array();	
		foreach($result as $row){
			$arrReturn['DOS'] = $row['date_of_service'];
			
			$arrReturn['facility'] = ($row['facility_name'] == NULL)?"":$row['facility_name'];
		}
		$arrReturn['enbl_finalize_btn'] = $this->display_finalize_button();
		return $arrReturn;
		//$arrReturn['DOS'] = $result[]
	}
	public function display_finalize_button(){
		include_once(dirname(__FILE__)."../interface/chart_globals.php");
		if(in_array($this->user_type,$GLOBALS['arrValidCNPhy'])){
			return "YES";
		}
		else return "NO";
	}
	public function get_chart_status(){
		if($this->form_id!=0){
			$this->db_obj->qry = "SELECT finalize
								FROM chart_master_table 
								WHERE id=".$this->form_id."
							";
			$result = $this->db_obj->get_resultset_array();	
		}
		return $result[0]['finalize'];
	}
	public function get_allergies(){
		$this->db_obj->qry = "SELECT title AS name ,DATE_FORMAT(begdate,'%m-%d-%Y') AS startdate ,comments 
								FROM lists 
								WHERE pid=".$this->patient." AND type IN(3,7)
								AND allergy_status != 'Deleted' 
							";
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
	public function get_pt_notes(){
		$returnArr = array();
		$this->db_obj->qry = "SELECT showTitle,title,pnotes
								FROM pnote_cat 
								WHERE pid=".$this->patient." 
								AND title = 'Ocular Dx.' 
							";
		$result = $this->db_obj->get_resultset_array();	
		$returnArr['Diagnosis'] = $result[0]['pnotes'];
		
		$this->db_obj->qry = "SELECT showTitle,title,pnotes
								FROM pnote_cat 
								WHERE pid=".$this->patient." 
								AND title = 'Ocular Sx.' 
							";
		$result = $this->db_obj->get_resultset_array();	
		$returnArr['Ocular_Sx'] = $result[0]['pnotes'];
		
		$this->db_obj->qry = "SELECT showTitle,title,pnotes
								FROM pnote_cat 
								WHERE pid=".$this->patient." 
								AND title = 'Consult' 
							";
		$result = $this->db_obj->get_resultset_array();	
		$returnArr['Consult'] = $result[0]['pnotes'];
		
		$this->db_obj->qry = "SELECT showTitle,title,pnotes
								FROM pnote_cat 
								WHERE pid=".$this->patient." 
								AND title = 'Med Dx.' 
							";
		$result = $this->db_obj->get_resultset_array();	
		$returnArr['Med_dx'] = $result[0]['pnotes'];
		
		return $returnArr;
	}
	function get_ocular_pt_notes(){
		$arrPtChroCond = array();
		$this->db_obj->qry = "select any_conditions_you,any_conditions_others_you, chronicDesc, chronicRelative, OtherDesc FROM ocular WHERE patient_id='".$this->patient."' ";
		$row= $this->db_obj->get_resultset_array();	
		if($row != false){
			$any_conds_u = $row["any_conditions_you"];
			$any_conds_other_u = $row["any_conditions_others_you"];
			$chronicDesc = $row["chronicDesc"];
			$chronicRelative = $row["chronicRelative"];
			$otherDesc = !empty($row["OtherDesc"]) ? $row["OtherDesc"] : "";
			
			//desc
			$strSep="~!!~~";
			$strSep2=":*:";
			$strDesc = $chronicDesc;				
			$arrDesc = array();
			$arrRelative = array();
			
			if(!empty($strDesc)){
				$arrDescTmp = explode($strSep, $strDesc);
				if(count($arrDescTmp) > 0){
					foreach($arrDescTmp as $key => $val){
						$arrTmp = explode($strSep2,$val);
						$arrDesc[$arrTmp[0]] = $arrTmp[1];							
					}
				}				
			}	
			
			//Relative
			if( !empty($chronicRelative) ){
				$arrRelTmp = explode($strSep, $chronicRelative);
				if( count($arrRelTmp) > 0 ){
					foreach( $arrRelTmp as $key => $val ){
						$arrTmp = explode($strSep2, $val);
						$arrRelative[$arrTmp[0]] = $arrTmp[1];	
					}
				}
			}	
			
			//chronic	
			$arrChroCond = array("Dry Eyes","Macula Degeneration","Glaucoma","Retinal Detachment","Cataracts" );
			$any_conditions_you_arr=explode(" ",trim(str_replace(","," ",$any_conds_u)));
			
			if( count($any_conditions_you_arr) > 0 ){					
				$arrPtChroCond = array();	
				foreach($any_conditions_you_arr as $keyTmp => $valTmp){
					if(!empty($arrChroCond[$valTmp-1])){
						$tmp = "";
						$tmp .= $arrChroCond[$valTmp-1];
						$tmp.= (!empty($arrRelative[$valTmp])) ? " (".$arrRelative[$valTmp].")" : "";
						$tmp.= (!empty($arrDesc[$valTmp])) ? " ".$arrDesc[$valTmp]."" : "";	
						$tmp = trim($tmp);
						$tmp = str_replace("~|~","",$tmp);	
						$arrPtChroCond[]= $tmp;
					}
				}
			}
			
			if(!empty($any_conds_other_u)){
				$tmp = "";
				$tmp .= $otherDesc;
				$tmp .= (!empty($arrRelative["other"])) ? " (".$arrRelative["other"].") " : "";
				$tmp .= $arrDesc["other"];
				$tmp = trim($tmp);
				$tmp = str_replace("~|~","",$tmp);
				$arrPtChroCond[]= $tmp;
			}
		}
	}
	public function save_pt_notes(){
		$returnArr = array();
		$Diagnosis = isset($_REQUEST['Diagnosis'])?$_REQUEST['Diagnosis']:"NULL";
		$Ocular_Sx = isset($_REQUEST['Ocular_Sx'])?$_REQUEST['Ocular_Sx']:"NULL";
		$Consult = isset($_REQUEST['Consult'])?$_REQUEST['Consult']:"NULL";
		$Med_dx = isset($_REQUEST['Med_dx'])?$_REQUEST['Med_dx']:"NULL";
		if($Diagnosis != "NULL"){
			$this->db_obj->qry = "UPDATE pnote_cat 
									SET pnotes = '".$Diagnosis."'
									WHERE pid=".$this->patient." 
									AND title = 'Ocular Dx.' 
								";
			$returnArr['Diagnosis'] = $this->db_obj->run_query($this->db_obj->qry);
		}
		if($Ocular_Sx != "NULL"){
			$this->db_obj->qry = "UPDATE pnote_cat 
									SET pnotes = '".$Ocular_Sx."'
									WHERE pid=".$this->patient." 
									AND title = 'Ocular Sx.' 
								";
			$returnArr['Ocular_Sx'] = $this->db_obj->run_query($this->db_obj->qry);
		}
		if($Consult != "NULL"){
			$this->db_obj->qry = "UPDATE pnote_cat 
									SET pnotes = '".$Consult."'
									WHERE pid=".$this->patient." 
									AND title = 'Consult' 
								";
			$returnArr['Consult'] = $this->db_obj->run_query($this->db_obj->qry);
		}
		if($Med_dx != "NULL"){
			$this->db_obj->qry = "UPDATE pnote_cat 
									SET pnotes = '".$Med_dx."'
									WHERE pid=".$this->patient." 
									AND title = 'Med Dx.' 
								";
			$returnArr['Med_dx'] = $this->db_obj->run_query($this->db_obj->qry);
		}
		return $returnArr;
	}
	function get_set_pat_rel_values_retrive($dbValue,$methodFor,$delimiter = "~|~",$hifenOptional= ""){
		$dbValue 	= trim($dbValue);		
		$methodFor 	= trim($methodFor);
		$delimiter	= trim($delimiter);
		if($methodFor == "pat"){
			//echo '<br>dbv='.$dbValue;
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtPat;
				//echo '<br>'.$valueToShow;
			}
			else{
				$valueToShow = $dbValue;
			}
		}
		elseif($methodFor == "rel"){
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtRel;
			}
			else{				
				$valueToShow = "";
			}
		}
		
		if($valueToShow) { $valueToShow = $hifenOptional.$valueToShow; }//FOR FACESHEET PDF
		
		return $valueToShow;
	}
	function str_replace_html_chars($str){
		if(!empty($str)){
			$str = str_replace(array("&amp;", "&gt;", "&lt;", "&quot;"), array("&", ">", "<", "\""), $str);
		}
		return $str;
	}
	function get_week($date1,$date2){
		$weeks="";
		if($date2 && $date1 && $date2!='--' && $date1!='--'){
			$daylen = 60*60*24;
			$date1=trim($date1);
			$date2=trim($date2);
			$date1." ".$date2."<br>";;
			$days=ceil((strtotime($date1)-strtotime($date2))/$daylen);
			$weeks=floor($days/7);
			if($weeks==0 && $days!=0){
				$weeks=$days." <span style='font-size:10px;font-weight:;'>Day</span>";
			}
		}
		return $weeks;
	}
	
}

?>