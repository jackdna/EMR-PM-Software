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
class emdeon extends user_app{
	var $emdeon_url = '';
	var $eRx_user_name = '';
	var $erx_password = '';
	var $eRx_facility_id = '';
	
	public function __construct(){
		parent::__construct();
		$this->get_emdeon_url();
		$this->get_emdeon_details();
	}
	function get_emdeon_url(){
		$this->db_obj->qry = "select EmdeonUrl from copay_policies where Allow_erx_medicare = 'Yes'";
		//$qryRes = ManageData::getQryRes($qry);
		$result_arr = $this->db_obj->get_resultset_array();
		$this->emdeon_url = $result_arr[0]['EmdeonUrl'];
	}
	public function get_emdeon_details(){
		$this->db_obj->qry = "select eRx_user_name, erx_password, eRx_facility_id, concat(lname,', ',fname) as name from users where id = '".$this->authId."'";
		$result_phy_arr = $this->db_obj->get_resultset_array();
		$this->eRx_user_name = $result_phy_arr[0]['eRx_user_name'];
		$this->erx_password = $result_phy_arr[0]['erx_password'];
		$this->eRx_facility_id = $result_phy_arr[0]['eRx_facility_id'];
	}
}
?>