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
class app_db
{
	public $db_obj, $qry_obj, $qry_result;
	public $dBase;
	public $qry;
	public $imedic_scan_db;
	public $passWord;
	public $userName;
	public $host;
	public $port = 3306;
	public function __construct($connectArr = array())
	{	
		if(count($connectArr) == 0) die('No connection parameters provided');
		
		$this->host = $connectArr["host"];
		$this->userName = $connectArr["login"];
		$this->passWord = $connectArr["pass"];
		$this->dBase = $connectArr['dbName'];
		$this->port = (isset($connectArr['port']) && empty($connectArr['port']) == false) ? $connectArr['port'] : $this->port;
		$this->imedic_scan_db = IMEDIC_SCAN_DB;
		
		$this->db_obj = new mysqli($this->host, $this->userName, $this->passWord, $this->dBase, $this->port);
		if($this->db_obj->connect_error) die("Database Connection Error(". $this->db_obj->connect_errorno."): ". $this->db_obj->connect_error);
	}
	
	public function __destruct()
	{
		//$this->db_obj->close();	
	}	
		
	public function run_query($req_qry, $bind_params_arr = array(), $bind_format='')
	{	//echo $req_qry;
		$this->qry_obj = $this->db_obj->prepare($req_qry);
		if(count($bind_params_arr) > 0)
		{
			if(trim($bind_format) == "")
			{
				for($c=0; $c<count($bind_params_arr); $c++)
				{
					$bind_format .= 's';	
				}
			}
			
			$params[0] = $bind_format; 
			$params = array_merge($params, $bind_params_arr);
			call_user_func_array(array($this->qry_obj, "bind_param"), $this->refValues($params));
		}

		$result = $this->qry_obj->execute();
		return $result;
	}

    public function refValues($arr){
         if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
         {
             $refs = array();
             foreach($arr as $key => $value)
                 $refs[$key] = &$arr[$key];
             return $refs;
         }
         return $arr;
     }		
	
	public function set_qry_result()
	{
		
		$row = $this->bind_result_array();
		$data = array();
		if(!$this->qry_obj->error){
			
			while($this->qry_obj->fetch()){
				$data[] = $this->getCopy($row);
			}
		}
		$this->qry_result = $data;
	}
	
	public function get_qry_result()
	{
		$this->set_qry_result();
		return $this->qry_result;
	}
	
	public function get_resultset_array(){
		$this->run_query($this->qry);
		$arrResult = $this->get_qry_result();
		//$arrResult = array();
		//while($row = $res_obj->fetch_assoc()){
		//	$arrResult[] = $row;
		//}
		return $arrResult;
	}
	
	
	public function bind_result_array() {
		$this->qry_obj->store_result();
		$meta = $this->qry_obj->result_metadata();
		$result = array();
		while ($field = $meta->fetch_field()){
			$result[$field->name] = NULL;
			$params[] = &$result[$field->name];
		}
		call_user_func_array(array($this->qry_obj, 'bind_result'), $params);
		return $result;
	}
	 
	/**
	 * Returns a copy of an array of references
	 */
	public function getCopy($row)
	{
		return array_map(create_function('$a', 'return $a;'), $row);
	}
}
?>