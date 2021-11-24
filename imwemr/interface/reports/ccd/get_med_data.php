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
FILE : get_med_data.php
PURPOSE : Getting medical data for CCD report.
ACCESS TYPE : Indirect
*/

//Global File
include("../../../config/globals.php");
	$pid = $_REQUEST['pid'];
	$form_id = $_REQUEST['form_id'];
	
	//START CODE TO GET ARRAY OF PATIENT NAME	
	$ptNameArr = array();
	$ptQuery = "SELECT pd.id as pat_id,CONCAT(pd.lname,', ',pd.fname) as pat_name FROM patient_data pd ORDER BY pd.id";
	$ptRes = imw_query($ptQuery);	
	while($ptRow = imw_fetch_assoc($ptRes))	{
		$ptNameArr[$ptRow["pat_id"]] = $ptRow["pat_name"];
	}
	//END CODE TO GET ARRAY OF PATIENT NAME
	
	//START CODE TO GET ARRAY OF DOS
	$dosArr = array();
	$dosQuery = "SELECT cmt.id, DATE_FORMAT(cmt.date_of_service, '".get_sql_date_format('','Y','-')."') as dos FROM chart_master_table cmt ORDER BY cmt.id";
	$dosRes = imw_query($dosQuery);	
	while($dosRow = imw_fetch_assoc($dosRes))	{
		$dosArr[$dosRow["id"]] = $dosRow["dos"];
	}	
	//END CODE TO GET ARRAY OF DOS

	$data_type = $_REQUEST['data_type'];
	switch($data_type){
		case "medications":
			$arrType = array("1","4");
			$arrData = get_medical_data($form_id,$arrType,$pid);
		break;
		case "allergies":
			$arrType = array("3","7");
			$arrData = get_medical_data($form_id,$arrType,$pid);
		break;
		case "problem_list":
			$arrType = array("1","4");
			$arrData = get_pt_problem_list($form_id,$pid);
		break;
	}
	$str = '';
	
	if(count($arrData)>0){
		foreach($arrData as $data){
			if($data_type == "medications" || $data_type == "allergies")
				$title = $data['title'];
			else if($data_type == "problem_list"){
				$title = $data['problem_name'];
			}
			//$str .= "<div ><input type='checkbox' name='".$data_type."[]'  value='".$title."'>".$title."</div>";
			$str .= "<div class=\"checkbox\"><input type=\"checkbox\" name='".$data_type."[]'  id='".$title."'  value='".$title."'><label  for='".$title."' style=\"cursor:pointer\">".$title."</label></div><div class=\"clearfix\"></div>";	
		}
	}
	$pid = $_REQUEST['pid'];
	$form_id = $_REQUEST['form_id'];
	$msgInfo="";
	if($pid && $form_id) {
		$msgInfo = "for Patient ".$ptNameArr[$pid]." - ".$pid." with DOS: ".$dosArr[$form_id];	
	}
	echo "<b>".ucwords($data_type). " to exclude ".$msgInfo."</b><br>";
	if($str != "")
	echo $str;
	else
	echo "No ".ucwords($data_type)." found";
	

function get_medical_data($form_id='', $arrType, $pid){
	$strType = implode(',',$arrType);
	$dataFinal = array();
	if(isset($form_id) && $form_id != '' && $form_id != 'all'){
		$sql_arc  = "select lists 
				from  
				chart_genhealth_archive 
				where patient_id='".$pid."' and
				form_id = '".$form_id."'";
	}else{
		$sql_list  = "select * ,
						date_format(begdate,'%m/%d/%y') as DateStart from lists where pid='".$pid."' and
						allergy_status = 'Active' and type in($strType) order by id";
	}
	if($sql_list != ""){
		$res_list = imw_query($sql_list);	
		while($row_list = imw_fetch_assoc($res_list))	{
			$dataFinal[] = $row_list;
		}
	}
	if($sql_arc != ""){
		$res_arc = imw_query($sql_arc);

		$dataFinal = array();
		while($row_arc = imw_fetch_assoc($res_arc)){
			$arrList = unserialize($row_arc['lists']);
			foreach($arrList as $arrData){
				foreach($arrData as $data){
					if(in_array($data['type'],$arrType)){
						if($data['allergy_status'] == 'Active'){
							$dataFinal[] = $data;
						}
					}
				}
			}
		}
	}
	return $dataFinal;
}

function get_pt_problem_list($form_id='', $pid){
	$strType = implode(',',$arrType);
	$dataFinal = array();
	if(isset($form_id) && $form_id != ''){
		$sql_arc  = "select pt_problem_list 
				from  
				chart_genhealth_archive 
				where patient_id='".$pid."' and
				form_id = '".$form_id."'";
	}else{
		$sql  = "SELECT * FROM pt_problem_list WHERE pt_id = '".$pid."' AND status = 'Active'";
	}
	if($sql != ""){
		$res = imw_query($sql);	
		while($row = imw_fetch_assoc($res))	{
			$dataFinal[] = $row;
		}
	}
	if($sql_arc != ""){
		$res_arc = imw_query($sql_arc);

		$dataFinal = array();
		while($row_arc = imw_fetch_assoc($res_arc)){
			$arrList = unserialize($row_arc['pt_problem_list']);
			foreach($arrList as $arrData){
					if($arrData['status'] == 'Active'){
						$dataFinal[] = $arrData;
					}
			}
		}
	}
	return $dataFinal;
}
?>