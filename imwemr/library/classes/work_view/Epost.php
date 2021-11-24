<?php
class Epost{
	public $pid, $exam;
	public function __construct($pid="", $exam=""){		
		$this->pid = $pid;
		$this->exam = !empty($exam) ? $exam : "chartnote" ;
	}	
	
	function getEpostAdminOpts($flg="0"){
		$arr=array();
		$qry = "select * from admin_epost";
		$rez = sqlStatement($qry);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$arr[] = $row['epost_pre_defines'];
		}
		if(!empty($flg)){
			return json_encode($arr);
		}else{
			return $arr;
		}
	}
	
	function getEposts(){
		
		$pid = $this->pid;
		$examName = $this->exam;
		$str="";
		if(empty($pid) || empty($examName)){ return $str; }
		//
		$arr_admn_epost_opt = $this->getEpostAdminOpts();
		$json_arr_admn_epost_opt = json_encode($arr_admn_epost_opt);	
		
		$i=1;
		//for($i=1;$i<=25;$i++){
		//$qry_epost = "select 
		$var_epost = 'none';
		$attrDis="0";
		$strPrePhrase = "";
		$qry_epost = "Select * from eposted where patient_id='".$_SESSION["patient"]."' and examName='$examName' ";
		$res_epost_qry = sqlStatement($qry_epost);
		$num_epost = imw_num_rows($res_epost_qry);
		$var_epost = 'block';
		$arr_epost=array();
		while($row = sqlFetchArray($res_epost_qry)){
			$left = $row['epost_left'];
			$top = $row['epost_top'];
			$epost_data = stripslashes($row['epost_data']);
			$strPrePhrase = $row['prePhrase'];
			$attrDis="1";
			$epostId= $row["epost_id"];
			$dt = wv_formatDate($row["dtdate"]);
			if(!empty($row["uid"])){
				if (class_exists('User')) {
					$tmpUsr = new User($row["uid"]);
				}else{
					include_once(dirname(__FILE__)."/User.php");
					$tmpUsr = new User($row["uid"]);
				}
				
				$unm = $tmpUsr->getName(2);
				$unm =$unm[2];
			}else{
				$unm ="";
			}

			$tmp_unm_dt = "".$unm;
			if(!empty($dt)) $tmp_unm_dt .= " (".$dt.")";

			if(!empty($row["mod_date"]) && $row["mod_date"]!="0000-00-00"){ $strModDt = "&bull; Modified on ".wv_formatDate($row["mod_date"]);   }
			
			
			
			$arr_epost[] = array( "left" => $left,
							"top" => $top,
							"examName" => $examName,
							"epostId" => $epostId,
							"tmp_unm_dt" => $tmp_unm_dt,
							"epost_data" => $epost_data,
							"strModDt" => $strModDt,
							"attrDis" => $attrDis
						);
		}
		
		//include
		// get hdr
		ob_start();
		include($GLOBALS['incdir']."/chart_notes/view/epost.php");
		$out2 = ob_get_contents();
		ob_end_clean();
		$str = $out2;
		
		return $str; 
	}

	function savehandler(){
		//extract($_GET);
		$q=addslashes(urldecode($_GET["q"]));
		$name = $_GET['name'];
		$left = $_GET['left_div'];
		$top = $_GET['top_div'];
		$del=$_GET["del"];
		$dbId = $_GET["elem_dbId"];
		$op=$_GET["op"];
		$pid = $this->pid;

		$examName = empty($_GET["examName"]) ? "chartnote" : $_GET["examName"];
		$strPrePhrase = $_GET["prephrase"];
		$strPrePhrase = (!empty($strPrePhrase) && ( $strPrePhrase != "*|*" )) ? $strPrePhrase : "";
		//$insertId = $_GET["insertId"];
		if(($op != 'delete') ){
		
			//if($num_saveEpost<=0){
			if(empty($dbId)){
				$epostQry = "insert into eposted set 
							 epost_data = '$q',
							 T_time ='".date("H:i:s")."',
							 table_name = '$name',
							 patient_id = '".$pid."',
							 dtdate = '".wv_dt('now')."',
							 prePhrase = '".$strPrePhrase."',
							 epost_left = '$left',
							 epost_top = '$top',
							 examName = '$examName',
							 uid='".$_SESSION["authId"]."' ";							
							 $insertID = sqlInsert($epostQry);
							 $msg =  $insertID;//'Record inserted';
			}
			else
			{
				//Date of EPost should not change if it is moved and not changed 
				$phraseDate="";
				$sql = "SELECT epost_data,prePhrase from eposted WHERE epost_id='".$dbId."' ";
				$row = sqlQuery($sql);
				if($row!=false){
					if(trim($row["epost_data"]) != trim($q) || trim($row["prePhrase"]) != trim($strPrePhrase)){
						$phraseDate = "
									epost_data = '$q',
									mod_date = '".wv_dt('now')."', 
									prePhrase = '".$strPrePhrase."',
									";
					}
				}		
				
				$epostUpdateQry = "update eposted set						
								$phraseDate						
								epost_left = '$left',
								epost_top = '$top'".
								//where patient_id = '".$_SESSION["patient"]."' and table_name = '$name' and examName = '$examName' ";
								"where epost_id='".$dbId."' ";
								
								sqlQuery($epostUpdateQry);	
								$msg =  $dbId; //'Record updated';	
			}
			
			echo $msg;
		}elseif(($op == 'delete') && ( !empty($dbId) ) ){
			$epostQry = "delete from eposted where patient_id = '".$pid."' and epost_id='".$dbId."' ";
					 //table_name='$del' and  and examName='".$examName."' ";
			sqlQuery($epostQry);
			//echo mysql_affected_rows();		
		}		
	}

}

?>