<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
include("../../../../library/classes/work_view/ExamXml.php");
include("../../../../library/classes/SaveFile.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

function update_prv_rec(){
	

	$oExamXml = new ExamXml();
	
	$sql = "SELECT id, exam, tab, obsrv, full_obsrv FROM chart_exam_ext WHERE del='0' AND grade='' AND location='' AND comments='0' ORDER BY id  ";			
	$rez = sqlStatement($sql);
	for($i=0; $row=sqlFetchArray($rez);$i++){
		//L%26A--Lids
		
		$ar = $oExamXml ->get_exam_ext_findings($row["id"], $row["exam"]."--".$row["tab"]);
		
		$ar_find = $ar["finding"];
		$ar_finding_type = $ar["finding_type"];
		
		$str_find_grd = $str_find_loc = $str_find_com = "";
		if(count($ar_find)>0){
			foreach($ar_find as $k => $v){
				$v=trim($v);
				if(!empty($v)){
				if($ar_finding_type[$k] == "grd"){ if(!empty($str_find_grd)){ $str_find_grd .= "!@!";  }  $str_find_grd .= $v; }
				else if($ar_finding_type[$k] == "loc"){ if(!empty($str_find_loc)){ $str_find_loc .= "!@!";  } $str_find_loc .= $v; }
				else if($ar_finding_type[$k] == "com"){ $str_find_com = "1"; }
				}
			}
			
			//
			if(!empty($str_find_grd) || !empty($str_find_loc) || !empty($str_find_com)){
			$sql = "UPDATE chart_exam_ext set grade='".sqlEscStr($str_find_grd)."', location='".sqlEscStr($str_find_loc)."', comments='".sqlEscStr($str_find_com)."' where id = '".$row["id"]."' ";	
			$row1 = sqlQuery($sql);
			}
		}
		
		//echo "<br/>".$row["obsrv"]."<br/>";
		//print_r($ar);
		
	}


}

if(isset($_GET["st"]) && !empty($_GET["st"])){

update_prv_rec();
exit("Process done!");
}




$q = array();
$q[] = 'ALTER TABLE `chart_exam_ext` ADD `grade` TEXT NOT NULL AFTER `full_obsrv`, ADD `location` TEXT NOT NULL AFTER `grade`, ADD `comments` INT(1) NOT NULL AFTER `location`';
  
foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 39 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 39 completed successfully. </b>";
	$color = "green";
	update_prv_rec();
	
}
?>
<html>
<head>
<title>Update 39</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>