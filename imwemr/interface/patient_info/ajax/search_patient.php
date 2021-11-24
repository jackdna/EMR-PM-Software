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
File: search_patient.php
Purpose: Handle Ajax for search patient 
Access Type: Direct 
*/
require_once("../../../config/globals.php");
$_REQUEST = array_map('trim',$_REQUEST);
extract($_REQUEST);
$return = array('action'=>$action,'grid'=>$grid);
$pid = $_SESSION['patient'];

if($grid == 0 && $action == 'search_patient')
{
	$searchStr = "";
	if( stristr($val,',') && $searchType == 'pname' )
	{
		$arr = explode(",",$val);
		$lname = trim($arr[0]);
		$fname = trim($arr[1]);
		
		$searchStr = " AND lname = '".addslashes($lname)."' ";
		if( $fname ) $searchStr .= " And fname like '".$fname."%' ";
	}
	else {
		$lname = addslashes($val);
		$searchStr = " AND lname like '".$lname."%' ";
	}
	
	$fields = 'id,title,fname,mname,lname,suffix,status,DOB,sex,street,street2,postal_code,zip_ext,city,state,ss,phone_home,phone_biz,phone_biz_ext,phone_cell';
	$rows = get_array_records('patient_data','1','1', $fields, " AND id != '".$pid."'".$searchStr, 'lname ASC, fname','ASC');
	
	$data_p = $data = array();
	$total_records	=	(int) count($rows);
	
	if(is_array($rows) && count($rows) > 0 )
	{
		foreach($rows as $row)
		{
			$name = $row['lname'].($row['fname'] ? ', '.$row['fname'] : '').' '.$row['mname'];
			$id = $row['id'];
			$dob = get_date_format($row['DOB']);
			$ss = $row['ss'];
			
			$row['DOB'] = $dob;
			$row['status'] = ucwords($row['status']);
			
			$data_p[] = array('name'=>$name,'ss'=>$ss,'dob'=>$dob,'id'=>$id);
			$data[$id] = $row;
		}
	}
	
	$return['total_records'] = $total_records;
	$return['pdata'] = $data_p;
	$return['data'] = $data;
	$return['iKey'] = $iKey;
	
}
else if( $grid > 0 && $action == 'search_patient')
{
	$searchStr = "";
	if( stristr($val,',') && $searchType == 'pname' )
	{
		$arr = explode(",",$val);
		$lname = trim($arr[0]);
		$fname = trim($arr[1]);
		
		$searchStr = " AND lname = '".addslashes($lname)."' ";
		if( $fname ) $searchStr .= " And fname like '".$fname."%' ";
	}
	else {
		$lname = addslashes($val);
		$searchStr = " AND lname like '".$lname."%' ";
	}
	
	
	$elem_status = empty($fld) ? "Active" : trim($fld);
	
	$fields = 'id,fname, mname, lname, street, street2, postal_code, city, state, phone_home, phone_biz, phone_cell, email, chk_mobile';
	$rows = get_array_records('patient_data','1','1', $fields, " AND patientStatus='".$elem_status."' ".$searchStr, 'lname ASC, fname','ASC');
	

	$data_p = $data = array();
	$total_records	=	(int) count($rows);

	if(is_array($rows) && count($rows) > 0 )
	{
		foreach($rows as $row)
		{
			$name = $row['lname'].($row['fname'] ? ', '.$row['fname'] : '').' '.$row['mname'];
			$id = $row['id'];
			$address  = $row['street'];
			$address .= trim($row['street2']) ? ', '.$row['street2'] : '';
			$address .= trim($row['city']) ? ', '.$row['city'] : '';
			$address .= trim($row['state']) ? ', '.$row['state'] : '';
			$address .= ' '.$row['postal_code'];	
			$phone_home = core_phone_format($row['phone_home']);
			
			$data_p[] = array('name'=>$name,'id'=>$id,'address'=>$address,'phone'=>$phone_home);
			$data[$id] = $row;
		}
	}
	
	$return['total_records'] = $total_records;
	$return['pdata'] = $data_p;
	$return['data'] = $data;

}

echo json_encode($return);