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
class RFS extends chart_notes{	
	public function __construct(){
		parent::__construct();
	}
	function get_RFS(){
		$arrReturn = array();
		$site = (isset($_REQUEST['site']) && $_REQUEST['site'] !="")?$_REQUEST['site']:"OD";
		$form_id = (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] !="")?$_REQUEST['form_id']:"";
		$proc_type = (isset($_REQUEST['proc_type']) && $_REQUEST['proc_type'] !="")?$_REQUEST['proc_type']:"";
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
								AND op.ret_gl = 1
								AND op.del_status != '1'
								ORDER BY cmt.date_of_service DESC
							";
		//echo $this->db_obj->qry;
							
		$result = $this->db_obj->get_resultset_array();	
		$countInj = 0;
		$countLas = 0;
		foreach($result as $record){
			$countInj = count($arrReturn['injection']);
			$countLas = count($arrReturn['laser']);
			$record['Physician'] = $this->get_user_initials($record['user_id']);
			$arrMeds = explode("|~|",$record['intravit_meds']);
			$medsArr = $medResult = array();
			if(count($arrMeds)>0){
				foreach($arrMeds as $med){
					$medsArr[] = "'".$med."'";
				}
				$strMeds = implode(",",$medsArr);
				$record['intravit_meds'] = str_replace('|~|',',',$record['intravit_meds']);
				$this->db_obj->qry = "SELECT * FROM medicine_data 
										WHERE medicine_name IN (".$strMeds.")
										AND ret_injection = 1
										AND del_status = '0'
									 ";
				$medResult = $this->db_obj->get_resultset_array();	
			}
			if(count($medResult)>0){
				
				//$arrReturn['injection'][$count] = $record;
				if($record['site'] == "OU"){
					$record['site'] = "OS";
					$arrReturn['injection'][$countInj][] = $record;
					$record['site'] = "OD";
					$arrReturn['injection'][$countInj][] = $record;
				}
				else{
					$arrReturn['injection'][$countInj] = $record;
				}
				
			}else{
				//$arrReturn['laser'][$record['site']][] = $record;
				if($record['site'] == "OU"){
					$record['site'] = "OS";
					$arrReturn['laser'][$countLas][] = $record;
					$record['site'] = "OD";
					$arrReturn['laser'][$countLas][] = $record;
				}
				else{
					$arrReturn['laser'][$countLas] = $record;
				}
			}
			$count++;
		}
		//if($proc_type != "")
		//return $arrReturn[$proc_type];
		return $arrReturn;
	}
	function get_RFS_app(){
		$arrReturn = array();
		$site = (isset($_REQUEST['site']) && $_REQUEST['site'] !="")?$_REQUEST['site']:"OD";
		$form_id = (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] !="")?$_REQUEST['form_id']:"";
		$proc_type = (isset($_REQUEST['proc_type']) && $_REQUEST['proc_type'] !="")?$_REQUEST['proc_type']:"";
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
								AND op.ret_gl = 1
								AND op.del_status != '1'
								ORDER BY cmt.date_of_service DESC
							";
		//echo $this->db_obj->qry;
							
		$result = $this->db_obj->get_resultset_array();	
		$count_inj = 0;
		$count_inj_1 = 0;
		$count_las = 0;
		$count_las_1 = 0;
		foreach($result as $record){
			 $countInj = count($arrReturn['injection']);
			$countLas = count($arrReturn['laser']);
			$record['Physician'] = $this->get_user_initials($record['user_id']);
			$arrMeds = explode("|~|",$record['intravit_meds']);
			$medsArr = $medResult = array();
			if(count($arrMeds)>0){
				foreach($arrMeds as $med){
					$medsArr[] = "'".$med."'";
				}
				$strMeds = implode(",",$medsArr);
				$record['intravit_meds'] = str_replace('|~|',',',$record['intravit_meds']);
				$this->db_obj->qry = "SELECT * FROM medicine_data 
										WHERE medicine_name IN (".$strMeds.")
										AND ret_injection = 1
										AND del_status = '0'
									 ";
				$medResult = $this->db_obj->get_resultset_array();	
			}
			if(count($medResult)>0){
				//$arrReturn['injection'][$count] = $record;
				if($record['site'] == "OU"){
					$record['site'] = "OS";
					$arrReturn['injection']["OU"][] = $record;
					$record['site'] = "OD";
					$arrReturn['injection']["OU"][] = $record;
					$count_ins++;
				}
				else{
					$arrReturn['injection']['OS & OD'][] = $record;
					$count_ins_1++;
				}
				
			}else{
				//$arrReturn['laser'][$record['site']][] = $record;
				if($record['site'] == "OU"){
					$record['site'] = "OS";
					$arrReturn['laser']["OU"][] = $record;
					$record['site'] = "OD";
					$arrReturn['laser']["OU"][] = $record;
					$count_las++;
				}
				
				else{
					$arrReturn['laser']['OS & OD'][]= $record;
					$count_las_1++;
				}
				//end of changes
			}
			// new code array for app //
		if($count_ins==0)$arrReturn['injection']['OU']=array();
		if($count_ins_1==0)$arrReturn['injection']['OS & OD']=array();
		if($count_las==0)$arrReturn['laser']['OU']=array();
		if($count_las_1==0)$arrReturn['laser']['OS & OD']=array();
		
		// end of new code //
		}
		
		//if($proc_type != "")
		//return $arrReturn[$proc_type];
		return $arrReturn;
	}
}

?>