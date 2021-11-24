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
File: vocabulary.php
Purpose: vocabulary replacement in data for iPortal autoresponder templates
Access Type: Include
*/
class vocabulary{
	
	public $pt_id, $phy_id, $fac_name, $reg_token;
	private $variables, $variables_found, $values;
	
	/*
		Public Constructor
		Set the vocabulary elemtns
	*/
	public function __construct(){
		
		$this->variables = array();
		$this->variables['pt']['id']="{PATIENT ID}";
		$this->variables['pt']['title']="{PATIENT NAME TITLE}";
		$this->variables['pt']['fname']="{PATIENT FIRST NAME}";
		$this->variables['pt']['mname']="{PATIENT MIDDLE NAME}";
		$this->variables['pt']['lname']="{PATIENT LAST NAME}";
		$this->variables['pt']['temp_key']="{TEMP KEY}";
		$this->variables['phy']['pro_title']="{PHYSICIAN NAME TITLE}";
		$this->variables['phy']['fname']="{PHYSICIAN FIRST NAME}";
		$this->variables['phy']['mname']="{PHYSICIAN MIDDLE NAME}";
		$this->variables['phy']['lname']="{PHYSICIAN LAST NAME}";
		$this->variables['phy']['pro_suffix']="{PHYSICIAN NAME SUFFIX}";
		$this->variables['phy']['phy_name']="{PHYSICIAN NAME}";
		$this->variables['fac']['name']="{FACILITY NAME}";
		$this->variables['fac']['street']="{FACILITY MAILING ADDRESS}";
		$this->variables['fac']['postal_code']="{FACILITY ZIP CODE}";
		$this->variables['fac']['city']="{FACILITY CITY}";
		$this->variables['fac']['state']="{FACILITY STATE}";
		$this->variables['fac']['address']="{FACILITY Address}";
		$this->variables['ip_reg']['reg_token']="{REG TOKEN}";
	}
	
	/*Parse Data / Replace vocabulary with their relevant values in provided content*/
	public function parse($html){
		$this->find_vocabulary($html);
		$this->get_values();
		$resp = $this->replace($html);
		return($resp);
	}
	
	/*Find vocabulary elements used in the string given*/
	private function find_vocabulary($html){
		$this->variables_found = array();
		foreach($this->variables as $key=>$data){
			foreach($data as $key1=>$data1){
				if(strpos($html,$data1)!==FALSE){
					$this->variables_found[$key][$key1]=$data1;
				}
			}
		}
	}
	
	/*Get relevant values for the vocabulary elemts found in the sting gievn to parse()*/
	private function get_values(){
		$tablestoquery = array_keys($this->variables_found);
		$resp = array();
		
		foreach($tablestoquery as $key=>$table){
			switch($table){
				case 'pt':
					if(!$this->pt_id){break;}
					$sql_patient = "SELECT `id`, `title`, `fname`, `mname`, `lname`, `temp_key` FROM `patient_data` WHERE `id`='".$this->pt_id."'";
					$data = imw_query($sql_patient);
					if($data){
						$resp[$key+1] = imw_fetch_assoc($data);
					}
				break;
				case 'phy':
					if($this->phy_id){break;}
					$sql_phy = "SELECT `pro_title`, `fname`, `mname`, `lname`, `pro_suffix` FROM `users` WHERE `id`='".$this->phy_id."'";
					$data = imw_query($sql_phy);
					if($data){
						$resp[$key+1] = imw_fetch_assoc($data);
					}
				break;
				case 'fac':
					if($this->fac_name){
						$sql_fac = "SELECT `name`, `street`, `postal_code`, `city`, `state`, CONCAT(`street`, '<br>', `postal_code`, ' ', `city`, ', ', `state`) AS 'address' FROM `facility` WHERE TRIM(`name`)='".trim($this->fac_name)."' ";
					}
					else{
						$sql_fac = "SELECT `name`, `street`, `postal_code`, `city`, `state`, CONCAT(`street`, '<br>', `postal_code`, ' ', `city`, ', ', `state`) AS 'address' FROM `facility` WHERE `facility_type`='1' ";
					}
					$data = imw_query($sql_fac);
					if($data){
						$resp[$key+1] = imw_fetch_assoc($data);
					}
				break;
				case 'ip_reg':
					$resp[$key+1] = array('reg_token'=>$this->reg_token);
				break;
			}
		}
			
		if(count($resp)>0){			
			foreach($tablestoquery as $key=>$value){
				foreach($this->variables_found[$value] as $key1=>$value1){
					$this->values[$value][$key1]=$resp[$key+1][$key1];
				}
			}
		}
	}
	
	/*Replcae vocabulary elements with their relevant values*/
	private function replace($html){
		$resp = $html;
		foreach($this->variables_found as $key=>$value){
			foreach($value as $key1=>$value1){
				$resp = str_replace($value1,$this->values[$key][$key1],$resp);
			}
		}
		return($resp);
	}
}
?>