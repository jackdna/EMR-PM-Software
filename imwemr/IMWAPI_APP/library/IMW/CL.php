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
 Purpose: MySQLi Extension Functions
 Access Type: Indirect Access.
*/

namespace IMW;

/**
 * DB
 *
 * Main DB connection Class
 */
class CL
{
	
	public function __construct($db_obj = '',$cur_sec = 1){
		if(empty($db_obj) == false){
			$this->dbh_obj = $db_obj;
		}
		if(empty($cur_sec) == false){
			$this->current_sec = $cur_sec;
		}
	}
	
	public function contactLensMakeData(){
		$qry = "Select make_id, manufacturer, style, type FROM contactlensemake";
		$rs = $this->dbh_obj->imw_query($qry);
		while($res = $this->dbh_obj->imw_fetch_assoc($rs)){
			$id=$res['make_id'];
			$result[$id]=$res;
		}
		return $result;
	}
	public function consentPackage(){
		$qry = "Select 	package_category_id	, package_category_name FROM consent_package";
		$rs = $this->dbh_obj->imw_query($qry);
		while($res = $this->dbh_obj->imw_fetch_assoc($rs)){
			$id=$res['package_category_id'];
			$result[$id]=$res['package_category_name'];
		}
		return $result;
	}

}