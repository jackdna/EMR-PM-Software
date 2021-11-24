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
File: demographics_history.php
Purpose: Handle Ajax for Demographics History
Access Type: Direct 
*/
include_once("../../../config/globals.php");
$_REQUEST = array_map('trim',$_REQUEST);
extract($_REQUEST);
$return = array('action'=>$action);
$pid = $_SESSION['patient'];


function show_prev_data_fun($pid,$section_name)
{
	$return = array();
	$query = "SELECT * FROM `patient_previous_data` WHERE patient_id='".$pid."' AND patient_id!='' AND patient_section_name='".$section_name."' ORDER BY save_date_time DESC";
	$sql = imw_query($query) or die(imw_error());
	$count = imw_num_rows($sql);
	if($count > 0){
		while($row = imw_fetch_array($sql)){
			$return[] = $row;
		}
		return $return;
	}
}

function filter_record($key,$r_array)
{
	$prev_val = $new_val = $change_date_time = $operator_name = '';
	$key == strtolower($key);
	
	$r_array	=	array_map('stripslashes',$r_array);
	$r_array	=	array_map('trim',$r_array);
	
	if('name' == $key)
	{
		$show_prev_title 		= $r_array['prev_title'];
		$show_prev_fname 		= $r_array['prev_fname'];
		$show_prev_lname 		= $r_array['prev_lname'];
		$show_prev_mname 		= $r_array['prev_mname'];
		$show_prev_suffix 	= $r_array['prev_suffix'];
		$show_prev_mstatus 	= $r_array['prev_mstatus'];
		$show_new_title 		= $r_array['new_title'];
		$show_new_fname 		= $r_array['new_fname'];
		$show_new_lname 		= $r_array['new_lname'];
		$show_new_mname 		= $r_array['new_mname'];
		$show_new_suffix 		= $r_array['new_suffix'];
		$show_new_mstatus 	= $r_array['new_mstatus'];
		// prev full name setting
		if($show_prev_title) {$show_prev_title	=	$show_prev_title.' ';}
		if($show_prev_lname) {$show_prev_lname	=	' '.$show_prev_lname;}
		if($show_prev_mname) {$show_prev_mname	=	' '.$show_prev_mname;}
		if($show_prev_suffix){$show_prev_suffix	=	' '.$show_prev_suffix;}
		$prev_val	= $show_prev_title.$show_prev_fname.$show_prev_lname.$show_prev_mname.$show_prev_suffix;
		// new full name setting
		if($show_new_title) {$show_new_title	=	$show_new_title.' ';}
		if($show_new_lname) {$show_new_lname	=	' '.$show_new_lname;}
		if($show_new_mname) {$show_new_mname	=	' '.$show_new_mname;}
		if($show_new_suffix){$show_new_suffix	=	' '.$show_new_suffix;}
		$new_val = $show_new_title.$show_new_fname.$show_new_lname.$show_new_mname.$show_new_suffix;
		
	}
	
	else if( 'marital_status' === $key)
	{
		$prev_val	= $r_array['prev_mstatus'];
		$new_val	= $r_array['new_mstatus'];
	}
	
	else if( 'gender' === $key )
	{
		$prev_val	=	$r_array['prev_sex'];
		$new_val	=	$r_array['new_sex'];
	}
	
	else if( 'social_security' === $key)
	{
		$prev_val	=	$r_array['prev_ss'];
		$new_val	=	$r_array['new_ss'];
	}
	
	else if( 'dob' === $key)
	{
		$prev_val	=	get_date_format($r_array['prev_dob']);
		$new_val	=	get_date_format($r_array['new_dob']);
	}
	
	else if( 'address' === $key)
	{
		$show_prev_street 		= $r_array['prev_street'];
		$show_prev_street2 		= $r_array['prev_street2'];
		$show_prev_postal_code= $r_array['prev_postal_code'];
		$show_prev_city 		= $r_array['prev_city'];
		$show_prev_state 		= $r_array['prev_state'];
		
		if($show_prev_street && $show_prev_street2) { $show_prev_street2	= ', '.$show_prev_street2;}
		if($show_prev_postal_code) 	{ $show_prev_postal_code= ', '.$show_prev_postal_code;}
		if($show_prev_city) 		{ $show_prev_city		= ', '.$show_prev_city;}
		if($show_prev_state) 		{ $show_prev_state		= ', '.$show_prev_state;}
		$prev_val = $show_prev_street.$show_prev_street2.$show_prev_city.$show_prev_state.$show_prev_postal_code;
		
		//new address settings
		$show_new_street = $r_array['new_street'];
		$show_new_street2 = $r_array['new_street2'];
		$show_new_postal_code = $r_array['new_postal_code'];
		$show_new_city = $r_array['new_city'];
		$show_new_state = $r_array['new_state'];
		if($show_new_street && $show_new_street2) { $show_new_street2	= ', '.$show_new_street2;}
		if($show_new_postal_code){$show_new_postal_code= ', '.$show_new_postal_code;}
		if($show_new_city){$show_new_city	= ', '.$show_new_city;}
		if($show_new_state){$show_new_state = ', '.$show_new_state;}
		$new_val = $show_new_street.$show_new_street2.$show_new_city.$show_new_state.$show_new_postal_code;
		
	}
	
	else if( 'phone_email' === $key)
	{
		//prev phone-email settings
		$show_prev_phone_home = $r_array['prev_phone_home'];
		$show_prev_phone_biz 	= $r_array['prev_phone_biz'];
		$show_prev_phone_cell = $r_array['prev_phone_cell'];
		$show_prev_email 			= $r_array['prev_email'];
			 
		
		if($show_prev_phone_home) $prev_val .= "<b>Home Phone:</b> ".core_phone_format($show_prev_phone_home).'<br>';
		if($show_prev_phone_biz) $prev_val .= "<b>Work Phone:</b> ".core_phone_format($show_prev_phone_biz).'<br>';
		if($show_prev_phone_cell) $prev_val .= "<b>Cell Phone:</b> ".core_phone_format($show_prev_phone_cell).'<br>';
		if($show_prev_email) $prev_val .= "<b>Email:</b> ".$show_prev_email;
		
		//new phone-email settings
		$show_new_phone_home	=	$r_array['new_phone_home'];
		$show_new_phone_biz 	=	$r_array['new_phone_biz'];
		$show_new_phone_cell 	= $r_array['new_phone_cell'];
		$show_new_email 			= $r_array['new_email'];
		
		if($show_new_phone_home) 	$new_val  .= "<b>Home Phone:</b> ".core_phone_format($show_new_phone_home).'<br>';
		if($show_new_phone_biz) 	$new_val  .= "<b>Work Phone:</b> ".core_phone_format($show_new_phone_biz).'<br>';
		if($show_new_phone_cell) 	$new_val  .= "<b>Cell Phone:</b> ".core_phone_format($show_new_phone_cell).'<br>';
		if($show_new_email) 			$new_val  .= "<b>Email:</b> ".$show_new_email;
		
	}
	
	else if( 'advance_directive' === $key)
	{
		$prev_Val	=	$r_array['prev_ado_option'];
		$new_val	= $r_array['new_ado_option'];
	}
	
	$change_date_time = display_prev_date_format($r_array['save_date_time']);
	$operator_id = $r_array['operator_id'];
	$operator_name = show_prev_operator_fun($operator_id);
		
	$return = array('prev_val'=>$prev_val, 'new_val' => $new_val, 'change_date_time' => $change_date_time, 'operator_name' => $operator_name );
	
	return $return;
}

function show_prev_operator_fun($opId)
{
	$showOperatorName='';
	if($opId) {
		$showOperatorQry = "select * from users where id='".$opId."'";
		$showOperatorRes = imw_query($showOperatorQry) or die(imw_error());
		if(imw_num_rows($showOperatorRes)>0) {
			$showOperatorRow = imw_fetch_array($showOperatorRes);
			$showOperatorFname = $showOperatorRow['fname'];
			//$showOperatorMname = $showOperatorRow['mname'];
			$showOperatorLname = $showOperatorRow['lname'];
			$showOperatorName = $showOperatorFname.' '.$showOperatorLname;
			
		}
	}
	return $showOperatorName;
}

function display_prev_date_format($selectDt) {
	$setDate='';
	if($selectDt && $selectDt!='0000-00-00' && $selectDt!='0000-00-00 00:00:00') {
		$setDate = get_date_format(date('Y-m-d',strtotime($selectDt))).' '.date('h:i A',strtotime($selectDt));
	}
	return $setDate;
}

$sectionPtNameArr 		= show_prev_data_fun($pid,'patientName');
$sectionPtMstatusArr 	= show_prev_data_fun($pid,'patientMstatus');
$sectionPtGenderArr 	= show_prev_data_fun($pid,'patientGender');
$sectionPtSSArr 			= show_prev_data_fun($pid,'patientSS');
$sectionPtDOBArr 			= show_prev_data_fun($pid,'patientDOB');
$sectionPtAddressArr 	= show_prev_data_fun($pid,'patientAddress');
$sectionPtContactArr 	= show_prev_data_fun($pid,'patientContact');
$sectionPtADOArr 			= show_prev_data_fun($pid,'patientADOopt');

$list_array = array(	'name' => array('title' => 'Name','data' => $sectionPtNameArr), 
											'marital_status' => array('title' => 'Marital Status','data' => $sectionPtMstatusArr),
											'gender' => array('title' => 'Patient Gender','data' => $sectionPtGenderArr),
											'social_security' => array('title' => 'Social Security #','data' => $sectionPtSSArr),
											'dob' => array('title' => 'Patient Date of Birth','data' => $sectionPtDOBArr),
											'address' => array('title' => 'Address','data' => $sectionPtAddressArr),
											'phone_email' => array('title' => 'Phone/Email','data' => $sectionPtContactArr),
											'advance_directive' => array('title' => 'Advance Directive','data' => $sectionPtADOArr),
									);
$data  = '';
$data	.=	'<table class="table table-bordered table-hover table-striped scroll release-table">';
$data	.=	'<thead class="header">';
$data	.=	'<tr class="grythead">';	
$data	.=	'<th class="col-xs-1">#</th>';
$data	.=	'<th class="col-xs-3">Date Time</th>';
$data	.=	'<th class="col-xs-3">Original</th>';
$data	.=	'<th class="col-xs-3">Changed To</th>';
$data	.=	'<th class="col-xs-2">Operator</th>';
$data	.=	'</tr>';
$data	.=	'</thead>';
$data	.=	'<tbody>';

foreach($list_array as $key => $list)
{
	$title	= $list['title'];
	$rows 	=	$list['data'];
	
	$data	.=	'<tr><td colspan="5" class="bg-info"><b>'.$title.'</b></td></tr>';
	$counter = 0;
	
	if(is_array($rows) && count($rows) > 0 )
	{
		foreach($rows as $row)
		{
			$counter++;
			$filter	=	filter_record($key,$row);
			
			$data	.=	'<tr >';
			$data	.=	'<td data-label="#">'.$counter.'</td>';
			$data	.=	'<td data-label="Date Time">'.$filter['change_date_time'].'</td>';
			$data	.=	'<td data-label="Original">'.$filter['prev_val'].'</td>';
			$data	.=	'<td data-label="Changed To">'.$filter['new_val'].'</td>';
			$data	.=	'<td data-label="Operator">'.$filter['operator_name'].'</td>';
			$data	.=	'</tr>';
		}
	}
	else
	{
			$data	.=	'<tr><td colspan="5" height="30" class="bg-default">No Record Found.</td></tr>';
	}
	
}



$data	.=	'</tbody>';
$data	.=	'</table>';

$return['title'] = 'Patient History';
$return['html'] = $data;
echo json_encode($return);