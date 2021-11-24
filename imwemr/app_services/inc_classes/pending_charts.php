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
include_once(dirname(__FILE__).'/user_app.php');
class pending_charts extends user_app{	
	public function __construct(){
		parent::__construct();
		//echo $this->user_type; echo "::";echo $this->core_check_privilege(array("priv_admin"));die();
		if(($this->user_type != 1))
		{
			die('Provider is not privileged to see the content');
		}
	}
	public function show_pending_charts()
	{		
			$this->db_obj->qry  =  "SELECT ".
							   "CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ', patient_data.id) AS patient_name,".	
								"patient_data.id AS patient_id,".
								"DATE_FORMAT(chart_master_table.date_of_service,'%m-%d-%Y') AS date_of_service, ".
								"CONCAT(users.lname,', ',users.fname,' ',users.mname) AS provider_name,".
								"facility.name AS facility_name, ".
								"chart_master_table.id ".
							   "FROM chart_master_table ".
							   "LEFT JOIN chart_assessment_plans ON chart_assessment_plans.form_id = chart_master_table.id ".
							   "LEFT JOIN patient_data ON chart_master_table.patient_id = patient_data.id ".
							   "LEFT JOIN users ON users.id = chart_assessment_plans.doctorId ". 
							   "LEFT JOIN facility ON facility.fac_prac_code = patient_data.default_facility and facility.fac_prac_code!=0 ".
							   "WHERE chart_master_table.finalize = '0' 
							   and chart_master_table.delete_status = '0' and patient_data.id != 0 and chart_master_table.not2show = '0' ". 
							   "AND (chart_assessment_plans.doctorId = '".$this->authId."' 
										OR chart_master_table.providerId = '".$this->authId."') ".
							   " GROUP BY patient_data.id
							   ORDER BY patient_data.lname,patient_data.fname";			
				
			$result_arr = $this->db_obj->get_resultset_array();
			return $result_arr;
		
	}
	public function delete_pending_charts(){
		$this->db_obj->qry = "UPDATE chart_master_table SET not2show = 1 WHERE id IN (".$_REQUEST['id'].")";
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	
	}
	
}

?>