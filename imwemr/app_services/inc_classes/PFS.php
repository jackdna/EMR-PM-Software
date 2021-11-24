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
include_once(dirname(__FILE__).'/chart_notes.php');
class PFS extends chart_notes{	
	public function __construct(){
		parent::__construct();
	}
	function get_PFS(){
		$arrReturn = array();
		$site = (isset($_REQUEST['site']) && $_REQUEST['site'] !="")?$_REQUEST['site']:"OD";
		$form_id = (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] !="")?$_REQUEST['form_id']:"";
		$this->db_obj->qry = "SELECT
							cp.site,
							op.procedure_name,
							cp.intravit_meds,
							IF(cp.complication=1,'Y','N') AS comp,
							cp.dx_code,
							CONCAT(c1.sel_od,' ',c1.txt_od) AS od_va,
							CONCAT(c1.sel_os,' ',c1.txt_os) AS os_va,
							cp.cmt,
							CONCAT(cp.iop_type,' ',iop_od) AS iop_od,
							CONCAT(cp.iop_type,' ',iop_os) AS iop_os,
							cp.user_id,
							date_format(cmt.date_of_service,'%m-%d-%Y') as dos,cmt.id as form_id
							FROM chart_procedures AS cp 
							JOIN chart_master_table AS cmt ON (cmt.id=cp.form_id AND cmt.patient_id=cp.patient_id) 
							LEFT JOIN operative_procedures op ON op.procedure_id = cp.proc_id
							LEFT JOIN chart_vis_master cv ON (cv.form_id = cp.form_id AND cv.patient_id=cp.patient_id)
							LEFT JOIN chart_acuity c1 ON c1.id_chart_vis_master = cv.id AND c1.sec_name = 'Distance' AND c1.sec_indx = '1'
							WHERE cp.patient_id='".$this->patient."'";
							
		if($form_id != "")
		$this->db_obj->qry .="  AND cp.form_id = '".$this->form_id."'";
		$this->db_obj->qry .="	
								AND cmt.finalize='1' 
								AND op.ret_gl IN (0,3)
								AND op.del_status != '1'
								ORDER BY cmt.date_of_service DESC
							";
		//echo $this->db_obj->qry;					
		$result = $this->db_obj->get_resultset_array();	
		$count = 0;
		$tmpArr = array();
		foreach($result as $record){			
			$record['Physician'] = $this->get_user_initials($record['user_id']);
			$arrMeds = explode("|~|",$record['intravit_meds']);
			$medsArr = $medResult = array();
			if(count($arrMeds)>0){
				$record['intravit_meds'] = str_replace('|~|',',',$record['intravit_meds']);
			}
			
			$record['od_va'] = str_replace('',$tmpArr,$record['od_va']);
			$record['os_va'] = str_replace('',$tmpArr,$record['os_va']);
							
			if($record['site'] == "OU"){
				$record['site'] = "OS";
				$arrReturn[$count][] = $record;
				$record['site'] = "OD";
				$arrReturn[$count][] = $record;
			}else{
				$arrReturn[$count] = $record;
			}
			$count++;
		}
		return $arrReturn;
	}
	// new function for app //
	function get_PFS_app(){
		$arrReturn = array();
		$site = (isset($_REQUEST['site']) && $_REQUEST['site'] !="")?$_REQUEST['site']:"OD";
		$form_id = (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] !="")?$_REQUEST['form_id']:"";
		$this->db_obj->qry = "SELECT
							cp.site,
							op.procedure_name,
							cp.intravit_meds,
							IF(cp.complication=1,'Y','N') AS comp,
							cp.dx_code,
							CONCAT(c1.sel_od,' ',c1.txt_od) AS od_va,
							CONCAT(c1.sel_os,' ',c1.txt_os) AS os_va,
							cp.cmt,
							CONCAT(cp.iop_type,' ',iop_od) AS iop_od,
							CONCAT(cp.iop_type,' ',iop_os) AS iop_os,
							cp.user_id,
							date_format(cmt.date_of_service,'%m-%d-%Y') as dos,cmt.id as form_id
							FROM chart_procedures AS cp 
							JOIN chart_master_table AS cmt ON (cmt.id=cp.form_id AND cmt.patient_id=cp.patient_id) 
							LEFT JOIN operative_procedures op ON op.procedure_id = cp.proc_id
							LEFT JOIN chart_vis_master cv ON (cv.form_id = cp.form_id AND cv.patient_id=cp.patient_id)
							LEFT JOIN chart_acuity c1 ON c1.id_chart_vis_master = cv.id AND c1.sec_name = 'Distance' AND c1.sec_indx = '1'
							WHERE cp.patient_id='".$this->patient."'";
							
		if($form_id != "")
		$this->db_obj->qry .="  AND cp.form_id = '".$this->form_id."'";
		$this->db_obj->qry .="	
								AND cmt.finalize='1' 
								AND op.ret_gl IN (0,3,2)
								AND op.del_status != '1'
								ORDER BY cmt.date_of_service DESC
							";
		//echo $this->db_obj->qry;					
		$result = $this->db_obj->get_resultset_array();	
		$count = 0;
		$count_1=0;
		
		foreach($result as $record){
			$record['Physician'] = $this->get_user_initials($record['user_id']);
			$arrMeds = explode("|~|",$record['intravit_meds']);
			//$medsArr = $medResult = array();
			if(count($arrMeds)>0){
				$record['intravit_meds'] = str_replace('|~|',',',$record['intravit_meds']);
			}
			if($record['site'] == "OU"){
				$record['site'] = "OS";
				$arrReturn["OU"][] = $record;
				$record['site'] = "OD";
				$arrReturn["OU"][] = $record;
				$count++;
			}else{
				$arrReturn["OS & OD"][]= $record;
				$count_1++;
			}
			// new array for app
			if($count==0)
			$arrReturn["OU"]=array(); 
			if($count_1==0)
			$arrReturn["OS & OD"]=array(); 
			// end of new code //
		}
		return $arrReturn;
	}
	// end of function //
}

?>