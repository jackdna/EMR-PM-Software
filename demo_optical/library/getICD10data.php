<?php 
$ignoreAuth = true;
include(dirname(__FILE__)."/../config/config.php"); 

function icd10_getLSSDesc($ar){
	$ret_str="";
	$lat = $ar["lat"];
	$sever = $ar["sever"];
	$stage = $ar["stage"];
	$no_bilateral = $ar["no_bilateral"];	
	
	if($lat>0){ 
		$query2 = "SELECT title, code  FROM icd10_laterality WHERE under = '$lat' AND under>0 AND deleted=0";
		$res2	= imw_query($query2);		
		while ($rs2 = imw_fetch_assoc($res2)){
			if($no_bilateral==1 && $rs2['title']=="Both Eyes"){
			}else{				
				if(!empty($ar["clat1"]) && $ar["clat1"] == $rs2["code"]){
					$ret_str .= $rs2['title']." "; 
				}
			}
		}
	}
	if($sever>0){ 
		$query2 = "SELECT title, code FROM icd10_laterality WHERE under = '$sever' AND under>0 AND deleted=0";
		$res2	= imw_query($query2);		
		while ($rs2 = imw_fetch_assoc($res2)){	
			if(!empty($ar["clat2"]) &&  $ar["clat2"] == $rs2["code"]){
				$ret_str .= $rs2['title']." "; 
			}
		}
	}
	if($stage>0){ 
		$query2 = "SELECT title, code FROM icd10_laterality WHERE under = '$stage' AND under>0 AND deleted=0";
		$res2	= imw_query($query2);			
		while ($rs2 = imw_fetch_assoc($res2)){	
			if(!empty($ar["clat2"]) &&  $ar["clat2"] == $rs2["code"]){
				$ret_str .= $rs2['title']." "; 
			}
		}
	}
	$ret_str=trim($ret_str);
	return $ret_str;	
}


function icd10_isFullDxCode($q){
	
	$icd10_t=$icd10_desc="";
	$icd10_t_code_nolss=0;
	$q1=substr($q,0,-1)."-";
	$q2=substr($q,0,-2)."--";
	$q3=substr($q,0,-3)."-x-";
	
	$sql = "SELECT id,laterality,staging,severity,icd10,icd10_desc, no_bilateral FROM icd10_data WHERE (icd10 LIKE '$q' OR icd10 LIKE '$q1' OR icd10 LIKE '$q2' or LOWER(icd10) LIKE '".strtolower($q3)."')
			AND deleted=0  ";
	$row = sqlQuery($sql);
	if($row!=false){
	
		if((!empty($row["laterality"]) && $row["laterality"]!="NA") || !empty($row["staging"]) || !empty($row["severity"]) ){
			$icd10_t = $row["icd10"]; //is full code with lss
		}else{
			$icd10_t_code_nolss=1;
		}
		
		if(!empty($row["icd10_desc"])){
			$icd10_desc = $row["icd10_desc"];
			$ar=array();
			$ar["lat"]	= intval($row['laterality']);
			$ar["stage"]	= intval($row['staging']);
			$ar["sever"]	= intval($row['severity']);
			$ar["no_bilateral"]	= $row['no_bilateral'];
			
			if(strtoupper($q1)==strtoupper($icd10_t)){
				$ar["clat1"] = substr($q, -1);	
			}else if(strtoupper($q2)==strtoupper($icd10_t)){
				$clat = substr($q, -2);
				$ar["clat1"] = substr($clat, 0,1);
				$ar["clat2"] = substr($clat, 1,1);
				
			}else if(strtoupper($q3)==strtoupper($icd10_t)){
				$clat = substr($q, -3);
				$ar["clat1"] = substr($clat, 0,1);
				$ar["clat2"] = substr($clat, -1);
			}
			
			$str_lss_desc = icd10_getLSSDesc($ar);
			if(!empty($str_lss_desc)){ $icd10_desc=$icd10_desc."; ".$str_lss_desc;  }
		}
	}
	return array($q,$icd10_t,$icd10_t_code_nolss,$icd10_desc);
}


$getLaterality = $getStageSever = false;
$q = strtolower($_GET["term"]);

if(!empty($_GET["show_pop"])){ 
	$q_multi="";
	if(!empty($q) && strpos($q,",")!==false){
		$q_multi=$q;
		$arr_q=explode(",",$q);
		$tmp_inx = count($arr_q);
		$q=trim($arr_q[$tmp_inx-1]);//last word
	}

	list($q_tmp, $qdb_tmp,$dx_no_lss_tmp,$qdesc_tmp) = icd10_isFullDxCode($q);
	if(!empty($qdb_tmp)){ $q = $qdb_tmp; }
}

$q_arr = explode('>>',$q);
if(strpos($q,'.')){
	$LatPos = strpos($q,'-');
	$SevPos = strrpos($q,'-');
}
else{
	$LatPos = 0;
	$SevPos = 0;
}
$flg_LitSev=0;

if($LatPos>0 && $LatPos<7){
	$getLaterality = true;
}
else if($SevPos>6){
	$getStageSever = true;
}
if(count($q_arr) > 1 && $getLaterality==false && $getStageSever==false){
	$qc = count($q_arr) - 2;
	$q = $q_arr[$qc];
	$q = "SELECT id FROM icd10_data WHERE icd10_desc LIKE '$q' AND deleted=0";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$id = '';
		while($rs = imw_fetch_assoc($res)){
			 $id .= $rs['id'].',';
		}
		$id = substr($id,0,-1);
		$query = imw_query("SELECT icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10 FROM icd10_data WHERE parent_id IN ($id) AND deleted=0 ORDER BY icd10_desc");
		$results = array();
		while ($row = imw_fetch_assoc($query)){
			if($row['icd10']==''){
				$results[] = $row['icd10_desc'].'>>';
			}else{
				$results[] = $row['icd10_desc'].$row['code'];
			}
		}		
	}			
}else if($getLaterality==ture || $getStageSever==true){

	if(!empty($_GET["show_pop"])){
		$flg_LitSev=1;
		$icd10_dxcode_db="";
		$results = array();
		$query	= "SELECT laterality,staging,severity,icd10,no_bilateral  FROM icd10_data WHERE icd10 = '$q' AND deleted=0 LIMIT 0,1";
		$res	= imw_query($query);
		$rs	= imw_fetch_assoc($res);
		$lat	= intval($rs['laterality']);
		$stage	= intval($rs['staging']);
		$sever	= intval($rs['severity']);
		$icd10_dxcode_db = $rs['icd10'];
		$no_bilateral	= $rs['no_bilateral'];
		$results[0]=$results[1]=$results[2]=array();
		if($lat>0){ 
			$query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$lat' AND under>0 AND deleted=0";
			$res2	= imw_query($query2);
			
			while ($rs2 = imw_fetch_assoc($res2)){
				if($no_bilateral==1 && $rs2['name']=="Both Eyes - 3"){
				}else{
					$results[0][] = $rs2['name']; 
				}
			}
		}
		if($sever>0){ 
			$query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$sever' AND under>0 AND deleted=0";
			$res2	= imw_query($query2);
			
			while ($rs2 = imw_fetch_assoc($res2)){	
				$results[1][] = $rs2['name']; 
			}
		}
		if($stage>0){ 
			$query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$stage' AND under>0 AND deleted=0";
			$res2	= imw_query($query2);			
			while ($rs2 = imw_fetch_assoc($res2)){	
				$results[2][] = $rs2['name']; 
			}
		}
	}
	else{

		if($getLaterality==ture){
			$query	= "SELECT laterality,staging,severity,no_bilateral FROM icd10_data WHERE icd10 = '$q' AND deleted=0 LIMIT 0,1";
			$res	= imw_query($query);
		}	
		if($getStageSever==true){
			/*--Replace here Laterality character which is before 7th position--*/
			if(strtolower($q[6])!='x') $q[6]='-';
			$query	= "SELECT staging,severity FROM icd10_data WHERE icd10 = '$q' AND deleted=0 LIMIT 0,1";
			$res	= imw_query($query);
			if($res && imw_num_rows($res)==0){
				if(strtolower($q[6])=='x'){
					$q[5]='-';
				}
				
				$query	= "SELECT staging,severity FROM icd10_data WHERE icd10 = '$q' AND deleted=0 LIMIT 0,1";
				$res	= imw_query($query);	
			}
			
		}
		if($res){
			$rs		= imw_fetch_assoc($res);
			$stop_both_qry="";
			if($getLaterality==ture){
				$lat	= intval($rs['laterality']);
				if($rs['no_bilateral']>0){
					$stop_both_qry=" and code!='3'";
				}
			}else if($getStageSever==true){
				$stage	= intval($rs['staging']);
				$sever	= intval($rs['severity']);
			}
			//echo $lat.'-'.$sever.'-'.$stage;die;
			if($lat>0) $query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$lat' AND under>0 AND deleted=0 $stop_both_qry";
			else if($sever>0) $query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$sever' AND under>0 AND deleted=0";
			else if($stage>0) $query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '$stage' AND under>0 AND deleted=0";
			else $query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE deleted=0 AND under>0";
			//echo $query2;die;
			$res2	= imw_query($query2);
			$results = array();
			while ($rs2 = imw_fetch_assoc($res2)){	
				$results[] = $rs2['name']; 
			}
		}
	}
}
else{
	
	$qq = "SELECT id as mainId,icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10, (select count(id) from icd10_data where parent_id = mainId and deleted=0) as parents FROM icd10_data WHERE ((icd9 LIKE '$q%') OR (icd10 LIKE '$q%') OR (icd9_desc LIKE '$q%') OR (icd10_desc LIKE '$q%')) AND deleted=0 AND parent_id=0 group by icd10_desc ORDER BY parents DESC,icd10_desc asc";
	$query = imw_query($qq);
	$results = array();	
	while ($row = imw_fetch_assoc($query)){
		$tmp_res_icd10="";
		if($row['icd10']==''){
			$tmp_res_icd10 = $row['icd10_desc'].'>>';
		}else{
			$tmp_res_icd10 = $row['icd10_desc'].$row['code'];
		}
		if(!empty($tmp_res_icd10) && !in_array($tmp_res_icd10, $results)){ //
			$results[] = $tmp_res_icd10;
		}
	}	
	
	//-- ICD 10 checked description for every keyword in description : WV
	if($_REQUEST['callFrom']=="wv"){
		$qq = "SELECT id as mainId,icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10, (select count(id) from icd10_data where parent_id = mainId and deleted=0) as parents FROM icd10_data WHERE ((icd9 LIKE '$q%') OR (icd10 LIKE '$q%') OR (icd9_desc LIKE '%$q%') OR (icd10_desc LIKE '%$q%')) AND deleted=0 AND parent_id=0 group by icd10_desc ORDER BY parents DESC,icd10_desc asc, icd9_desc asc";
		$query = imw_query($qq);	
		while ($row = imw_fetch_assoc($query)){
			$tmp_res_icd10="";
			if($row['icd10']==''){
				$tmp_res_icd10 = $row['icd10_desc'].'>>';
			}
			else{
				$tmp_res_icd10 = $row['icd10_desc'].$row['code'];
			}
			if(!empty($tmp_res_icd10) && !in_array($tmp_res_icd10, $results)){
				$results[] = $tmp_res_icd10;
			}
		}
	}
}

if(!isset($_REQUEST['callFrom']) || $_REQUEST['callFrom']!="wv"){
	if(!empty($_GET["show_pop"])){
		if($dx_no_lss_tmp==1){$results=array();$flg_LitSev=1;}
			echo json_encode(array("results"=>$results,"flg_LitSev"=>$flg_LitSev,"icd10_dxdb"=>$icd10_dxcode_db,"srchd_code"=>$q_tmp,"icd10_dxdesc"=>$qdesc_tmp));
	}
	else{
		echo json_encode($results);
	}
}
?>