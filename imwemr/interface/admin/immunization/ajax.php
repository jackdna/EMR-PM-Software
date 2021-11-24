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
set_time_limit(600);
require_once(dirname(__FILE__).'/../../../config/globals.php');
require_once(dirname(__FILE__).'/../../../library/classes/common_function.php');

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$s		= isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'imnzn_name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$p		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$f		= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';
//ajax.php?task=show_list&s=Active&so=pos_prac_code&soAD=ASC

switch($task){
	case 'delete':
		$imnzn_id = $_POST['pkId'];
		$q 		= "DELETE FROM immunization_admin WHERE imnzn_id IN ($imnzn_id)";
		$res 	= imw_query($q);
		$res2 = imw_query("Delete from immunization_dosedetails where imnzn_id = '$imnzn_id'");		
		
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$imnzn_id = $_POST['imnzn_id'];
		unset($_POST['imnzn_id']);
		unset($_POST['task']);
		if(!$_POST['register_immunization']){ $_POST['register_immunization']='0';}
		
		$query_part = "";
		foreach($_POST as $k=>$v){
			if($k!='txtSave' && $k!='elemTargetName' && $k!='menuOptionValue' && $k!='cpt4_code' && substr($k,0,5)!='dose_'){
				$query_part .= $k."='".addslashes($v)."', ";
			}
		}
		$query_part = substr($query_part,0,-2);
		if($imnzn_id==''){
			$q = "INSERT INTO immunization_admin SET ".$query_part;
		}else{
			$q = "UPDATE immunization_admin SET ".$query_part." WHERE imnzn_id='".$imnzn_id."'";
		}
		$res = imw_query($q);
		if($imnzn_id=='') { $imnzn_id= imw_insert_id();}

		// INSERT SOME OTHER ELEMENTS
		$imnzn_name = addslashes($_POST['imnzn_name']);
		$imunz_cvx_coe = addslashes($_POST['imunz_cvx_coe']);
		$imunz_cpt_cat_id = addslashes($_POST['imunz_cpt_id']);
		$cpt4_code = addslashes($_POST['cpt4_code']);

		$allQry=insertCPTfieldEntry($imnzn_name,$imunz_cvx_coe,$imunz_cpt_cat_id,$cpt4_code);
		
		// ADD DOSE DETAILS
		if(empty($imnzn_id) == false){
			if($_POST['imnzn_numberofdoses']>0){
				for($counter=1;$counter<=$_POST['imnzn_numberofdoses'];$counter++){
					$insquery = "insert into immunization_dosedetails ";
					$wherequery='';
					$dose_quantity = $_POST["dose_doseQuantity".$counter];
					$dose_gap = $_POST["dose_imnzn_gapnumber".$counter];
					$dose_gapoption = $_POST["dose_imnzn_gapoption".$counter];
					$dose_booster = ($_POST["dose_booster".$counter])?"Yes":"No";
					$dose_number = $_POST["dose_imnzn_dosenumber".$counter];
					$existingID = dose_details_exist($imnzn_id,$dose_number);
					if($existingID>0){
						$insquery = "update immunization_dosedetails ";
						$wherequery = "where dose_id='".$existingID."'  ";
					}	
					$insFinal = $insquery." set dose_quantity='".$dose_quantity."', dose_gap='".$dose_gap."', dose_gapoption='".$dose_gapoption."',dose_booster='".$dose_booster."', imnzn_id='".$imnzn_id."',dose_number='".$dose_number."' $wherequery ";
					$res = imw_query($insFinal);
				}
			}
		}
		
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';//.imw_error()."\n".$allQry;
		}
		break;
	case 'show_list':
		
		$retArray = cpt_details();
		$arrCVXCodes = $retArray['arrCVXCodes'];
		$arrCPT4Code = $retArray['arrCPT4Code'];
		
		$q = "SELECT imnzn_id,imnzn_name,imunz_cvx_coe,imnzn_type,imnzn_numberofdoses,imnzn_manufacturer,imnzn_ptalerts,imnzn_ptinstruction,imunz_mfr_code,
		register_immunization,imunz_cpt_id,CPT_description  
		FROM immunization_admin ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs['cpt4_code'] = $arrCPT4Code[$rs['imunz_cvx_coe']][$rs['imnzn_name']];
				$rs_set[] = $rs;
			}
            $rs_set=mb_convert_encoding($rs_set, 'UTF-8');
		}
		
		$immznTypeArray= immunizationTypes();
		$cpt_category =CPT_category();

		echo json_encode(array('records'=>$rs_set, 'cpt_details'=>$arrCVXCodes, 'immunizationTypes'=>$immznTypeArray, 'cpt_category'=>$cpt_category));
		break;
		
	case 'get_immu_opts':
		$doseNumber = $_REQUEST["doseNumber"];
		$imnznID = $_REQUEST["imnznID"];
		$outPutHtml="<div class=\"adminbox\">
			<div class=\"row head\" style=\"float:none;\">
				<div class=\"col-sm-11\">
					<span>Add Dose Details</span>
				</div>
				<div class=\"col-sm-1 text-danger\">
					<span class=\"pull-right pointer\" onClick=\"hideDoseDiv();\"><b>Close</b></span>
				</div>
			</div>
			   <table class=\"table table-bordered table-hover table-striped adminnw\">
					<thead>
						<tr>
							<th>#</th>
							<th>Dose&nbsp;Quantity</th>
							<th>Frequency</th>
							<th></th>
							<th>Booster</th>
						</tr>
					</thead>
					<tbody>";
					
		for($counter=1;$counter<=$doseNumber;$counter++){
			$dose_gap="";
			$readonly="";
			$dose_quantity="";
			$dose_number="";
			$dose_gapoptionDays="";
			$dose_gapoptionWeeks="";
			$dose_gapoptionYear="";
			$dose_booster="";
			if($imnznID>0){
			$resultArray = dose_details_exist($imnznID,$counter,$mod="All");
				if(@is_array($resultArray)){
					$dose_quantity = ($resultArray["dose_quantity"] != 'undefined') ? $resultArray["dose_quantity"] : '';
					$dose_gap = ($resultArray["dose_gap"] != 'undefined') ? $resultArray["dose_gap"] : '';
					$dose_gapoptionDays=($resultArray["dose_gapoption"]=="Days")?"selected":"";
					$dose_gapoptionMonth=($resultArray["dose_gapoption"]=="Month")?"selected":"";
					$dose_gapoptionWeeks=($resultArray["dose_gapoption"]=="Weeks")?"selected":"";
					$dose_gapoptionYear=($resultArray["dose_gapoption"]=="Year")?"selected":"";
					$dose_booster=($resultArray["dose_booster"]=="Yes")?"checked":"";
					$dose_number=$resultArray["dose_number"];
				}
			}
			if($counter==1){
				$dose_gap="0";
				$readonly="readonly";
			}
			
			$frequency="<select class=\"form-control\" name=\"dose_imnzn_gapoption".$counter."\" tabindex=\"5\" class=\"text_9\">
					<option value=\"Days\" ".$dose_gapoptionDays.">Days</option>
					<option value=\"Weeks\" ".$dose_gapoptionWeeks.">Weeks</option>
					<option value=\"Month\" ".$dose_gapoptionMonth.">Month</option>
					<option value=\"Year\" ".$dose_gapoptionYear.">Year</option>
				</select>";

			$outPutHtml.="
			<tr>
				 <td>
					<input type=\"hidden\"  name=\"dose_imnzn_dosenumber".$counter."\" value=\"".$counter."\">".$counter."
				</td>
				<td class=\"eleAlign\">
					<input class=\"form-control\" type=\"text\" id=\"dose_doseQuantity".$counter."\" name=\"dose_doseQuantity".$counter."\" value=\"".$dose_quantity."\" >
				</td>
				<td>
					<input class=\"form-control\" type=\"text\" name=\"dose_imnzn_gapnumber".$counter."\" value=\"".$dose_gap."\"  ".$readonly." id=\"dose_imnzn_gapnumber".$counter."\">
				</td>
				<td>".$frequency."</td>		
				<td>
					<div class=\"checkbox\"><input class=\"form-control\" type=\"checkbox\" id=\"dose_booster".$counter."\" name=\"dose_booster".$counter."\" value=\"Yes\"  ".$dose_booster."><label for=\"dose_booster".$counter."\"></label></div>
				</td>	
			</tr>";							
		}
		$outPutHtml.="
		</table></div>
		";
		echo $outPutHtml;
	break;
	default: 
}

//type ahead for immunizations
function cpt_details(){
	$arrCVXCodes = array();
	$sql_cvx = "SELECT cpt_fee_id,cpt_desc,cvx_code,cpt_cat_id,cpt4_code FROM cpt_fee_tbl WHERE cvx_code != '' AND delete_status = '0' order by cpt_desc";
	$rs=imw_query($sql_cvx);
	while($res=imw_fetch_array($rs)){
		if($res["cpt4_code"]!="" && $res["cpt_desc"]!="" && $res["cpt_cat_id"]!=""){
			//$res['cpt_details']= $res["cpt_cat_id"]."~~".$res["cvx_code"]."~~".$res["cpt_desc"]."~~".$res["cpt4_code"];
			$arrCVXCodes[] = $res;
			$arrCPT4Code[$res['cvx_code']][$res['cpt_desc']] = $res['cpt4_code'];
		}
	}
	$retArray['arrCVXCodes'] = $arrCVXCodes;
	$retArray['arrCPT4Code'] = $arrCPT4Code;
	
	return $retArray;
}

function immunizationTypes(){
	$immznTypeArray = array();
	$immznTypeArray = array('Chicken pox (VZV)',
						'Diphtheria Tetanus and acellular Pertussis (DTaP)',
						'H influenza type B (HiB)',
						'Hepatitis A (Hep A)',
						'Hepatitis B (Hep B)',
						'HPV',
						'Influenza (flu)',
						'Measles Mumps and Rubella (MMR)',
						'Pneumococcal Conjugate (PCV)',
						'Polio(IPV)',
						'Rotavirus (RV)',
						'Tdap');
	return $immznTypeArray;
}

function CPT_category(){
	$arrCPTCategory=array();
	$rs = imw_query("SELECT * FROM cpt_category_tbl ORDER BY cpt_category");
	while($res=imw_fetch_array($rs)){
		$arrCPTCategory[$res['cpt_cat_id']]=$res['cpt_category'];
	}
	return $arrCPTCategory;
}

function insertCPTfieldEntry($imznname,$cvx_code,$cpt_cat_id,$cpt4Code){
	$sql = "SELECT cpt_fee_id FROM cpt_fee_tbl WHERE cpt_desc = '".trim($imznname)."' AND delete_status = '0' AND cvx_code = '".trim($cvx_code)."' LIMIT 1";
	$res1 = imw_query($sql);
	if(imw_num_rows($res1)<=0){
		$insertQuery="insert into cpt_fee_tbl set cpt_desc = '".trim($imznname)."',cvx_code = '".trim($cvx_code)."',cpt_cat_id='".trim($cpt_cat_id)."',cpt4_code='".trim($cpt4Code)."', cpt_prac_code='".trim($cpt4Code)."', status='Active' ";
		$res1 = imw_query($insertQuery);
	}
}
?>
