<?php
require_once(dirname(__FILE__). "/class.mur_reports.php");
require_once(dirname(__FILE__).'/../../../library/classes/cqm_import.php');
class QRDA_3 extends MUR_Reports{
	public function _getTotalGender($arr){
		$arrReturn = array("male"=>0,"female"=>0,"count"=>0);
		$strPatIDs = implode(",",$arr);
		$sql = "SELECT count(id) as total_count,sex FROM patient_data WHERE id IN (".$strPatIDs.") AND (sex = 'Male' or sex = 'male' or sex = 'Female' or sex = 'female') group by sex";
		$res = imw_query($sql);
		if(imw_num_rows($res)>0){
			while($row_sex=imw_fetch_assoc($res)){
				$total_count=$row_sex['total_count'];
				$sex_key_val=strtolower($row_sex['sex']);
				$arrReturn[$sex_key_val] = $row_sex['total_count'];
			}
			$arrReturn["count"]=array_sum($arrReturn);
		}
		
		return $arrReturn;
	}
	public function _getTotalGenderCount($arr){
		$arrReturn = array("male"=>0,"female"=>0,"count"=>0);
		$arr_male=$arr_female=array();
		if(count($arr)>0){
			foreach($arr as $pid){
				$sql = "SELECT id,sex FROM patient_data WHERE id ='".$pid."' AND (sex = 'Male' or sex = 'male' or sex = 'Female' or sex = 'female') ";
				$res = imw_query($sql);
				$row = imw_fetch_assoc($res);
				if(strtolower($row["sex"])=="male"){
					$arr_male[]=$row["id"];
				}else if(strtolower($row["sex"])=="female"){
					$arr_female[]=$row["id"];
				}
			}
			
			if(count($arr_male)>0){
				$arrReturn["male"]=count($arr_male);
			}
			if(count($arr_female)>0){
				$arrReturn["female"]=count($arr_female);
			}
			if(count($arr_female)>0 || count($arr_male)>0 ){
				$arrReturn["count"]=(count($arr_female)+count($arr_male));
			}
		}
		return $arrReturn;
	}
	public function _getTotalEthnicity($arr){
		$arr_return = array();
		if(count($arr)>0){
			$strPatIDs = implode(",",$arr);
			$sql = "SELECT count(id) as total_count,ethnicity FROM patient_data WHERE id IN (".$strPatIDs.") AND ethnicity!='' GROUP by ethnicity";
			$res = imw_query($sql);
			while($row = imw_fetch_assoc($res)){
				$total_count=$row["total_count"];
				$ethnicity=strtolower(trim($row["ethnicity"]));
				if($ethnicity==""){$ethnicity="unknown";}
				$return_code=ethnicity_srh($ethnicity);
				$display_name=$return_code["display_name"];
				$code=$return_code["code"];
				if($code){
				$arr_return[$code]=$total_count;
				}
				
			}
			//$arr_return["count"]=array_sum($arr_return);
		}
		return $arr_return;
	}
	public function _getTotalEthnicityCount($arr){
		$arr_return =$arr_return_code= array();
		if(count($arr)>0){
			foreach($arr as $pid){
				$sql1 = "SELECT id,ethnicity FROM patient_data WHERE id ='".$pid."' AND ethnicity!=''  ";
				$res1 = imw_query($sql1);
				$row1 = imw_fetch_assoc($res1);
				$id=trim($row1["id"]);
				$ethnicity=strtolower(trim($row1["ethnicity"]));
				$return_code=ethnicity_srh($ethnicity);
				$code=$return_code["code"];
				if($code){
					$arr_return_code[$code][]=$id;
				}
			}
			foreach($arr_return_code as $code_key=>$code_val){
				$arr_return[$code_key]=count($code_val);
			}
			//pre($arr_return);die;
		}
		return $arr_return;
	}
	
	public function _getTotalRace($arr){
		$arr_return = array();
		if(count($arr)>0){
			$strPatIDs = implode(",",$arr);
			$sql = "SELECT count(id) as total_count,race, race_code FROM patient_data WHERE id IN (".$strPatIDs.") GROUP by race";
			$res = imw_query($sql);
			while($row = imw_fetch_assoc($res)){
				$total_count=$row["total_count"];
				$race=strtolower(trim($row["race"]));
                                $race_code=trim($row["race_code"]);
				if($race!="" && $race_code != ""){
                                   $return_code["code"]= $race_code; 
                                }
                                elseif($race!=""){
                                   $return_code=race_srh($race);
				}
                                else if($race==""){
                                    $return_code["code"]="2131-1";	
				}
				
				$display_name=$return_code["display_name"];
				$code=$return_code["code"];
				if($code){
                                    $arr_return[$code]=$total_count;
				}
				
			}
			//$arr_return["count"]=array_sum($arr_return);
		}
		return $arr_return;
	}
	
	public function _getTotalRaceCount($arr){
		$arr_return =$arr_return_code= array();
		if(count($arr)>0){
			foreach($arr as $pid){
				$sql1 = "SELECT id,race FROM patient_data WHERE id ='".$pid."' ";
				$res1 = imw_query($sql1);
				$row1 = imw_fetch_assoc($res1);
				$id=trim($row1["id"]);
				$race=strtolower(trim($row1["race"]));
				if($race!=""){
					$return_code=race_srh($race);
				}else if($race==""){
					$return_code["code"]="2131-1";	
				}
				$display_name=$return_code["display_name"];
				$code=$return_code["code"];
				if($code){
					$arr_return_code[$code][]=$id;
				}
				
			}
			foreach($arr_return_code as $code_key=>$code_val){
				$arr_return[$code_key]=count($code_val);
			}
		}
		return $arr_return;
	}
	
	public function _getTotalPayer($arr){
		$arr_return=array();
		if(count($arr)>0){
			$strPatIDs = implode(",",$arr);
			$sql = "SELECT count(id) as total_count,patient_payer as valueCode FROM patient_data WHERE id IN (".$strPatIDs.") AND patient_payer!='' GROUP by patient_payer";
			$res = imw_query($sql);
			while($row=imw_fetch_assoc($res)){
				$total_count=$row["total_count"];
				$valueCode=$row["valueCode"];
				$arr_return[$valueCode]=$total_count;
			}
			//$arr_return["count"]=array_sum($arr_return);
		}
		return $arr_return;
	}
	public function _getTotalPayerCount($arr){
		$arr_return =$arr_return_code= array();
		if(count($arr)>0){
			foreach($arr as $pid){
				$sql1 = "SELECT id,valueCode FROM patientPayer WHERE pid ='".$pid."' ";
				$res1 = imw_query($sql1);
				$row1 = imw_fetch_assoc($res1);
				$id=trim($row1["id"]);
				$valueCode=(trim($row1["valueCode"]));
				if($valueCode){
					$arr_return_code[$valueCode][]=$id;
				}
			}
			foreach($arr_return_code as $code_key=>$code_val){
				$arr_return[$code_key]=count($code_val);
			}
		}
		return $arr_return;
	}
}
?>