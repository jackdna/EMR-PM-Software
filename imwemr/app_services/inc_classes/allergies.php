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
class allergies extends medical_hx{
	
	public function __construct(){
		parent::__construct();
	}
	function get_allergies(){
		$status = $_REQUEST['status'];
		
		if($status == ""){
			 $this->db_obj->qry = "SELECT id, 
										IF(ag_occular_drug='fdbATDrugName','Drug',
											(IF(ag_occular_drug='fdbATIngredient','Ingredient',
												IF(ag_occular_drug='fdbATAllergenGroup','Allergen','')
												)
											)
										) AS drug,
										title AS name,
										DATE_FORMAT(begdate,'%m-%d-%Y') as 'begdate', 
										comments,
										allergy_status AS status,
										ccda_code AS code
									FROM lists 
									WHERE pid='".$this->patient."' AND pid > 0
										AND type IN (3,7)
										AND allergy_status != 'Deleted' 
									ORDER BY id DESC";
									
			$result = $this->db_obj->get_resultset_array();	
		}
		else{
				$this->db_obj->qry = "SELECT id, 
									IF(ag_occular_drug='fdbATDrugName','Drug',
										(IF(ag_occular_drug='fdbATIngredient','Ingredient',
											IF(ag_occular_drug='fdbATAllergenGroup','Allergen','')
											)
										)
									) AS drug,
									title AS name,
									DATE_FORMAT(begdate,'%Y-%m-%d') as 'begdate', 
									comments,
									allergy_status AS status,
									ccda_code AS code
								FROM lists 
								WHERE pid='".$this->patient."'
									AND type IN (3,7)
									AND allergy_status = '$status'
									AND allergy_status != 'Deleted'
								ORDER BY id DESC";
								
			$result = $this->db_obj->get_resultset_array();	
		}
		return $result;
	}
	
	function save_allergies(){
		$title = (isset($_REQUEST['title']) && $_REQUEST['title']!="")?imw_real_escape_string($_REQUEST['title']):"";
		$begdate = (isset($_REQUEST['begdate']) && $_REQUEST['begdate']!="")?date('Y-m-d', strtotime($_REQUEST['begdate'])):"";
		$comments = (isset($_REQUEST['comments']) && $_REQUEST['comments']!="")?imw_real_escape_string($_REQUEST['comments']):"";
		$code = (isset($_REQUEST['code']) && $_REQUEST['code']!="")?imw_real_escape_string($_REQUEST['code']):"";
		$status = (isset($_REQUEST['status']) && $_REQUEST['status']!="")?imw_real_escape_string($_REQUEST['status']):"Active";
		$drug = '';
		if(isset($_REQUEST['drug']) && $_REQUEST['drug']!=""){
			switch($_REQUEST['drug']){
				case "Drug":
				$drug = "fdbATDrugName";
				break;
				case "Ingredient":
				$drug = "fdbATIngredient";
				break;
				case "Allergen":
				$drug = "fdbATAllergenGroup";
				break;
				default :
				$drug = "fdbATDrugName";
				break;
			}
		}
		if($title != "" && $this->patient!="" && $this->authId !="" && $this->authId!=0){
			$this->db_obj->qry = "INSERT INTO lists 
								SET type = 7,
									date = '".date('Y-m-d H:i:s')."',
									title = '".$title."',
									begdate = '".$begdate."',
									pid = '".$this->patient."' ,
									user = '".$this->authId."',
									comments = '".$comments."',
									allergy_status = '".$status."',
									ccda_code = '".$code."',
									ag_occular_drug = '".$drug."'
							";
			$result = $this->db_obj->run_query($this->db_obj->qry); 
			}
			
			return $result;
	}
	
	
	function update_allergies(){
		$title_1 = (isset($_REQUEST['title']) && $_REQUEST['title']!="")?imw_real_escape_string($_REQUEST['title']):"";
		$begdate = (isset($_REQUEST['begdate']) && $_REQUEST['begdate']!="")?date('Y-m-d', strtotime($_REQUEST['begdate'])):"";
		$comments = (isset($_REQUEST['comments']) && $_REQUEST['comments']!="")?imw_real_escape_string($_REQUEST['comments']):"";
		$code = (isset($_REQUEST['code']) && $_REQUEST['code']!="")?imw_real_escape_string($_REQUEST['code']):"";
		$status = (isset($_REQUEST['status']) && $_REQUEST['status']!="")?imw_real_escape_string($_REQUEST['status']):"";
		$drug = '';
		$id = $_REQUEST['allergy_id'];
		$title = trim($title_1);
		if(isset($_REQUEST['drug']) && $_REQUEST['drug']!=""){
			switch($_REQUEST['drug']){
				case "Drug":
				$drug = "fdbATDrugName";
				break;
				case "Ingredient":
				$drug = "fdbATIngredient";
				break;
				case "Allergen":
				$drug = "fdbATAllergenGroup";
				break;
				default :
				$drug = "fdbATDrugName";
				break;
			}
		}
		

		if($id != "" && $title != "" && $this->patient!="" && $this->authId !="" && $this->authId!=0){
				$this->db_obj->qry = "Update lists 
								SET type = 7,
									pid = '".$this->patient."' ,
									user = '".$this->authId."',
									date = '".date('Y-m-d H:i:s')."'";
									/*title = '".$title."',
									begdate = '".$begdate."',
									comments = '".$comments."',
									allergy_status = '".$status."',
									ccda_code = '".$code."',
									ag_occular_drug = '".$drug."'*/
						if($title != "")$this->db_obj->qry.=",title = '".$title."'";
						if($begdate != "")$this->db_obj->qry.=",begdate = '".$begdate."'";
						if($comments != "")$this->db_obj->qry.=",comments = '".$comments."'";
						if($status != "")$this->db_obj->qry.=",allergy_status = '".$status."'";
						if($code != "")$this->db_obj->qry.=",ccda_code = '".$code."'";
						if($drug != "")$this->db_obj->qry.=",ag_occular_drug= '".$drug."'";
							$this->db_obj->qry.=" WHERE id ='".$id."'
						 							AND pid = '".$this->patient."'";	
				$result = $this->db_obj->run_query($this->db_obj->qry); 
				return true;
			}
				else {
						return false;
					}
		
	}
	
	function delete_allergies(){
		$id = (isset($_REQUEST['allergy_id']) && $_REQUEST['allergy_id']!="")?$_REQUEST['allergy_id']:"";
		if($id != "" && $this->patient!=""){
		
			$this->db_obj->qry = "UPDATE lists
								 SET allergy_status = 'Deleted'
								 WHERE id IN (".$id.")
								 AND pid = '".$this->patient."'
							";
			$result = $this->db_obj->run_query($this->db_obj->qry);
			return $result;
		}
	}
}
?>
