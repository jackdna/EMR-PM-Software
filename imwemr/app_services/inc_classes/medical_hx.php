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
class medical_hx extends user_app{
	
	public function __construct(){
		parent::__construct();
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
	function get_all_medications(){
		$this->db_obj->qry = "SELECT title,destination,referredby,comments,
									DATE_FORMAT(begdate,'%m-%d-%Y') as 'begdate', 
									DATE_FORMAT(enddate,'%m-%d-%Y') as 'enddate'
								FROM lists 
								WHERE pid='".$this->patient."' 
									AND type IN (1,4)
									AND allergy_status != 'Deleted' 
								ORDER BY begdate DESC";
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
}
?>