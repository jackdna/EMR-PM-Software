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
include_once(dirname(__FILE__).'/patient_app.php');
class user_app extends patient_app{
	var $authId;
	var $responseArray = array();
	var $user_type =  '';
	var $user_previ = '';
	//var $db_obj;
	
	public function __construct($authId=''){
		global $responseArray, $db_obj, $authId;
		parent::__construct();
		$this->authId = (isset($authId) && $authId!="")?$authId:$_REQUEST['phyId'];
		$this->db_obj = $db_obj;
		$this->responseArray = $responseArray;
		$this->get_user_type();
	}
	public function get_user_type(){
		$this->db_obj->qry = "SELECT user_type,access_pri,concat(lname,', ',fname) AS name FROM users WHERE id = '".$this->authId."'";
		$result  = $this->db_obj->get_resultset_array();	
		$this->user_type = $result[0]['user_type'];
		$this->user_name = $result[0]['name'];
		$this->user_previ = unserialize(html_entity_decode($result[0]['access_pri']));
	}
	public function core_check_privilege($arr_priv_names, $any_or_all = "all"){ //"any"
		$bl_is_privileged = false;
		if(is_array($arr_priv_names) && count($arr_priv_names) > 0){
			if($any_or_all == "all"){
				foreach($arr_priv_names as $this_priv_name){
					$bl_is_privileged = false;
					if(isset($this->user_previ[$this_priv_name]) && $this->user_previ[$this_priv_name] == 1){
						$bl_is_privileged = true;
					}
				}
			}else if($any_or_all == "any"){
				foreach($arr_priv_names as $this_priv_name){
					if(isset($this->user_previ[$this_priv_name]) && $this->user_previ[$this_priv_name] == 1){
						$bl_is_privileged = true;
						break;
					}
				}
			}
		}
		return $bl_is_privileged;
	}
	public function get_user_initials($authId ='0'){
		if($authId!="0"){
			$this->db_obj->qry = "SELECT fname,lname FROM users WHERE id = '".$authId."'";
		}else{
			$this->db_obj->qry = "SELECT fname,lname FROM users WHERE id = '".$this->authId."'";
		}
		$result  = $this->db_obj->get_resultset_array();	
		$fName=substr($result[0]['fname'],0,1);
		$lName=substr($result[0]['lname'],0,1);
		return $fName.$lName;
	}
	
}
?>