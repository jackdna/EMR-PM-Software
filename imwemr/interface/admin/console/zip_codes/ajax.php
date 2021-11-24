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

require_once("../../../../config/globals.php");

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : 'show_list';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'zip_code';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$limit	= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '1';

$name	= isset($_REQUEST['p']) ? imw_real_escape_string(trim($_REQUEST['p'])) : 'a';	/* Escape arbitrary characters in user supplied input - Security Fix */	
$search	= isset($_REQUEST['s']) ? imw_real_escape_string(trim($_REQUEST['s'])) : '';	/* Escape arbitrary characters in user supplied input - Security Fix */

$recode_per_page= isset($_REQUEST['page']) ? trim($_REQUEST['page']) : '18';
if($name){
	$name=" AND state_abb LIKE '".$name."%' ";
}
if($limit){$limit=($limit-1);}
if($limit>0){$limit=($limit*$recode_per_page);}
$table	= "zip_codes";
$pkId	= "zip_id";
$chkFieldAlreadyExist = "zip_code";
$qry_search="";
if($search){$name="";
	if(is_numeric($search)){
		$qry_search=" and zip_code like '%".$search."%' ";	
	}else{
		$qry_search=" and (state_abb like '%".$search."%' OR state like '%".$search."%' OR city like '%".$search."%') ";
	}
}
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
	    foreach($_POST as $k=>$v){
	        $_POST[$k] = trim(addslashes($v));       // Escape special characters
	    }
	    $id = $_POST[$pkId];
	    $zipCode = $_POST['zip_code'];
	    $city = $_POST['city'];
	    $stateAbb = $_POST['state_abb'];
	    $state = $_POST['state'];
	    $county = $_POST['county'];
	    $country = $_POST['country'];
	    $query = "";
	    $message = "";
	    
	    if($id){       // If valid id is coming from request
	        // Update record
	        $query = "UPDATE ".$table." SET zip_code='".$zipCode."', city='".$city."', state_abb='".$stateAbb."', state='".$state."', country='".$country."', county='".$county."' WHERE ".$pkId." = '".$id."'";
	        $message = "Record saved successfully";
	    }
	    else
	    {     
	        // Check if this zipcode already exists for this city, state, county
	        $q = "select zip_id from ".$table." where zip_code = '".$zipCode."' and city = '".$city."' and state ='".$state."' and county ='".$county."'";
	        $result = imw_query($q);
	        if(imw_num_rows($result) == 0){       // If zip code for this city does not exist
	            $query = "insert into ".$table."(zip_code, city, state, state_abb, country, county) values('$zipCode', '$city', '$state', '$stateAbb', '$country', '$county')";
	            $message = "Record saved successfully";
	        }
	    }
	    $res = "";
	    if(strlen(trim($query)) > 0){
	       $res = imw_query($query);
	       if($res){
	           echo $message;
	       }else{
	           echo 'Record Saving failed.'.imw_error()."\n".$query;
	       }
	    }else{
	        echo "enter_unique";
	    }
		break;
	case 'show_list':
		$q= "SELECT zip_id,if(zip_ext!='',concat(zip_code,'-',zip_ext),zip_code) as zip_code_ext,city,state_abb,state,zip_code,zip_ext,county,country FROM ".$table." WHERE 1=1  ".$name." ".$qry_search." ORDER BY $so $soAD";
		$ql=" LIMIT $limit,$recode_per_page";
		$r = imw_query($q.$ql);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$rn=imw_query($q);
		$nu=imw_num_rows($rn);
		$cnt_rows=rows_count($nu,$recode_per_page);
		echo json_encode(array('records'=>$rs_set,'cnt_rows'=>$cnt_rows,'qry'=>$q.$ql));
		break;
	default: 
}
function rows_count($no_rows,$recode_per_page){
	$row_return=0;
	if($no_rows>0){
		$row_return=ceil($no_rows/$recode_per_page);
	}
	return $row_return;
}
?>