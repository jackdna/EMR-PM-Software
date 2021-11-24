<?php
class SuperBill extends Patient{

	public function __construct($pid){
		parent::__construct($pid);
	}

	function getPostedCharges($encId){
		$sql = "SELECT postedAmount from patient_charge_list WHERE del_status='0' and encounter_id='".$encId."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$postedAmt = $row["postedAmount"];
		}
		return !empty($postedAmt) ? $postedAmt : 0;
	}


	function get_icd10_desc($icd10,$len,$ardxids=array()){
		if($len>0){
			$len_cut=$len-3;
		}else{
			$len=10000;
		}
		$icd10_whr="";
		$arr_uni_srch=array();
		if(count($icd10)>0){
			for($h=0;$h<=count($icd10);$h++){
				$icd10_exp=explode('.',$icd10[$h]);
				$icd10_exp_str=trim($icd10_exp[0].'.'.substr($icd10_exp[1],0,1));
				$tmpdxid = !empty($ardxids[$h]) ? $ardxids[$h] : "";

				if(!empty($icd10_exp_str) && $icd10_exp_str!="." && !in_array($icd10_exp_str.$tmpdxid, $arr_uni_srch)){
					$arr_uni_srch[]=$icd10_exp_str.$tmpdxid;
					$tmp=" icd10 like '".$icd10_exp_str."%' ";
					if(!empty($tmpdxid)){ $tmp = $tmp." AND id='".$tmpdxid."' "; }
					$icd10_whr_arr[]=" (".$tmp.") ";
				}
			}
			$tmp = implode('or',$icd10_whr_arr);
			if(!empty($tmp)){$icd10_whr=" and (".$tmp.")";}
		}

		$qry_lat = "select under,code,id,title  from icd10_laterality where deleted='0'";
		$res_lat = sqlStatement($qry_lat);
		while($row_lat = sqlFetchArray($res_lat)){
			$lat_id_arr[$row_lat['under']][$row_lat['code']]=$row_lat['id'];
			$lat_desc_arr[$row_lat['under']][$row_lat['code']]=$row_lat['title'];
			$lat_code_arr[$row_lat['under']][]=$row_lat['code'];
		}

		$icd10_desc_arr=array();
		$pass_arr=array();

		$qry_dx = "select icd10,icd10_desc,laterality,staging,severity, id from icd10_data where icd10 !='' and deleted='0' $icd10_whr group by icd10, icd10_desc order by id ";
		$res_dx = sqlStatement($qry_dx);
		while($row_dx = sqlFetchArray($res_dx)){

			//if id is missing : group dx one
			$icd10_exp=explode('.',$row_dx['icd10']);
			$icd10_exp_str=trim($icd10_exp[0].'.'.substr($icd10_exp[1],0,1));
			$tmpdxid = $row_dx['id'];
			if(!empty($icd10_exp_str) && $icd10_exp_str!="." && (in_array($icd10_exp_str.$tmpdxid, $arr_uni_srch) || in_array($icd10_exp_str, $arr_uni_srch))){
			}else{ continue; }
			//--

			$icd10_desc="";
			$icd10_code="";
			$icd10_crt_stat_arr=array();
			if(in_array($row_dx['icd10'],$icd10)){
				$icd10_desc=$row_dx['icd10_desc'];
				$icd10_code=$row_dx['icd10'];
				if($icd10_desc!=""){
					if(strlen($icd10_desc) > $len){
						$icd10_desc=substr($icd10_desc,0,$len_cut)."...";
					}

					$icd10_desc_arr["id"][]=$row_dx['id'];
					$icd10_desc_arr["desc"][]=$icd10_desc;
					$icd10_desc_arr["dxcode"][]=$icd10_code;

				}
			}else{
				$icd10_crt_lat_arr = $icd10_crt_lat_arr=$icd10_crt_stat_arr=array();
				$icd10_crt_exp=explode('-',$row_dx['icd10']);
				if($row_dx['laterality']>0){
					foreach($lat_code_arr[$row_dx['laterality']] as $key=>$val){
						$icd10_crt_lat_arr[]=$icd10_crt_exp[0].$val;
					}
					$icd10_crt_exp_stat=$icd10_crt_exp[1];
				}else{
					$icd10_crt_exp_stat='';
					$icd10_crt_lat_arr[]=$icd10_crt_exp[0];
				}

				if($row_dx['staging']==4 or $row_dx['staging']==5 or $row_dx['severity']==3){
					if($row_dx['staging']==4){
						foreach($lat_code_arr[$row_dx['staging']] as $key=>$val){
							for($k=0;$k<=count($icd10_crt_lat_arr);$k++){
								$icd10_crt_stat_arr[]=$icd10_crt_lat_arr[$k].$icd10_crt_exp_stat.$val;
							}
						}
					}else{
						foreach($lat_code_arr[$row_dx['severity']] as $key=>$val){
							for($k=0;$k<=count($icd10_crt_lat_arr);$k++){
								$icd10_crt_stat_arr[]=$icd10_crt_lat_arr[$k].$icd10_crt_exp_stat.$val;
							}
						}
					}
				}else{
					$icd10_crt_stat_arr=$icd10_crt_lat_arr;
				}
				if(count($icd10_crt_stat_arr)>0){
					for($j=0;$j<=count($icd10_crt_stat_arr);$j++){
						if(in_array($icd10_crt_stat_arr[$j],$icd10)){
							$icd10_desc=$row_dx['icd10_desc'];
							$icd10_code=trim($icd10_crt_stat_arr[$j]);
							if(!empty($icd10_code)){
							if($icd10_desc!="" && $icd10_desc_arr[$icd10_code]==""){
								if(strlen($icd10_desc) > $len){
									$icd10_desc=substr($icd10_desc,0,$len_cut)."...";
								}

								$icd10_desc_arr["id"][]=$row_dx['id'];
								$icd10_desc_arr["desc"][]=$icd10_desc;
								$icd10_desc_arr["dxcode"][]=$icd10_code;
							}
							}
						}
					}
				}
			}
		}
		return $icd10_desc_arr;
	}

	function getICD10CodeFromFormId($prev_form_id_ap){
		$icd10=0;
		$sql="SELECT enc_icd10 from chart_master_table ";
		$sql.=" where id='".$prev_form_id_ap."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$icd10=$row["enc_icd10"];
		}
		return $icd10;
	}

	/**
	*right sir, now our code will only check super bill and accounting charges. if no record in these then patient is a new patient.
	*[2:20:37 AM] Arun Kapur: as well as any previous DOS work View
	*/

	function isPatientEstablish($formId)
        {

		//18-dec-2015:R7:c.       Superbill � If patient was last see 3 year ago (DOS clinical/Accounting) � New Patient warning should be displayed

		$sql = "SELECT idSuperBill, dateOfService FROM superbill WHERE patientId='".$this->pid."' AND formId != '".$formId."' AND formId != '0'  and del_status='0' order by dateOfService desc ";
		$row = sqlQuery($sql);
		if( ($row != false)){
			$retVal = (dt_getDtDiff($row["dateOfService"])<3) ?  true : false ;
		}else{
			//check if sperbill exists with formid zero but of different dos
			$sql = "SELECT idSuperBill, dateOfService FROM superbill WHERE patientId='".$this->pid."' and del_status='0' group by dateOfService order by dateOfService desc ";
			$rez = sqlStatement($sql);

			if( (imw_num_rows($rez) > 1)){ //more than one different dos
				$row = sqlFetchArray($rez);
				if($row != false){
					$retVal = (dt_getDtDiff($row["dateOfService"])<3) ?  true : false ;
				}else{
					$retVal = false;
				}
			}else if( (imw_num_rows($rez) > 0)){
				$row = sqlFetchArray($rez);
				$sql = "select date_of_service from chart_master_table where patient_id = '".$this->pid."' and id = '".$formId."' AND delete_status='0' ";
				$row1=sqlQuery($sql);
				if($row1!=false && $row1["date_of_service"]!=$row["dateOfService"]){
					$retVal = (dt_getDtDiff($row["dateOfService"])<3) ?  true : false ;
				}else{
					$retVal = false;
				}
			}else{
				$retVal = false;
			}
		}
		if($retVal == false){
			//Check Previous Charges
			$sql = "SELECT charge_list_id, date_of_service as dateOfService FROM patient_charge_list WHERE patient_id = '".$this->pid."' AND del_status='0' order by date_of_service desc ";
			$row = sqlQuery($sql);
			if( ($row != false) ){ //&& ( $row["num"] > 0 )
				$retVal = (dt_getDtDiff($row["dateOfService"])<3) ? true : false ;
			}
		}

		//Chart_master_table
		if($retVal == false){
			$sql = "SELECT id , date_of_service as dateOfService FROM chart_master_table WHERE patient_id='".$this->pid."' AND id != '".$formId."' AND delete_status='0' order by date_of_service desc ";
			$row = sqlQuery($sql);
			if( ($row != false)){
				$retVal = (dt_getDtDiff($row["dateOfService"])<3) ?  true : false ;
			}
		}

		/**
		*[2:15:44 AM] Arun Kapur: So our code should not include check for appointment type New Patient
		*/
		/*
		// schedular appointment
		if( $retVal == false ){

			$sql = "select count(c1.procedureid) as num from schedule_appointments c1
					LEFT JOIN slot_procedures c2 ON c1.procedureid = c2.id
					where c1.sa_patient_id = '".$patientId."' and c1.sa_patient_app_status_id != '18'
					and LCASE(c2.proc) LIKE '%new patient%'
					order by c1.id desc limit 0,1 ";

			$row = sqlQuery($sql);
			//$retVal = ( ($row != false) && ($row["procedureid"] == "1") ) ? false : true ;
			$retVal = ( ($row != false) && ($row["num"] > 0) ) ? false : true ;

		}
		*/

		return $retVal;
	}

}
?>
