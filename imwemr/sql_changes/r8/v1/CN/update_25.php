<?php
$ignoreAuth = true;
set_time_limit(0);
include("../../../../config/globals.php");
include($GLOBALS['srcdir']."/classes/work_view/Dx.php");

//Backup table
$t1 = "operative_procedures";
$back_up1 = $t1."_".date("m_d_Y_H_i_s");

$sql = "CREATE TABLE ".$back_up1." LIKE ".$t1." ";
$row=sqlQuery($sql);
$sql ="INSERT INTO ".$back_up1." SELECT * FROM ".$t1." ";
$row=sqlQuery($sql);

//process
$msg_info=array();
$odx = new Dx();
$sql  = "select procedure_id, dx_code from operative_procedures order by procedure_id ";
$rez=sqlStatement($sql);
for($i=1;$row=sqlFetchArray($rez);$i++){	
	$dx = trim($row["dx_code"]);
	$proc_id =  $row["procedure_id"];
	if(!empty($dx) && !empty($proc_id)){
		$ar_dx_old = explode(";", $dx);
		if(count($ar_dx_old)>0){
			$ar_dx=array();
			foreach($ar_dx_old as $k => $v){
				if(!empty($v)){					
					$dxRef=$odx->convertICDDxCode($v, 9, 1);
					$dxRef=trim($dxRef);
					if(!empty($dxRef)){
						$ar_dx[]=$dxRef;
					}else{
						$ar_dx[]=$v; //retain old value if conversion is not possible
					}
				}
			}
			//update
			$dxRef_a= (count($ar_dx) > 0) ? implode(";",$ar_dx) : "" ;
			$sql = " update operative_procedures SET dx_code='".sqlEscStr($dxRef_a)."' where procedure_id='".$proc_id."'  ";			
			$q = sqlQuery($sql);
		}		
	}
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update Change Dx 9 to 10 in oprative procedures</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>