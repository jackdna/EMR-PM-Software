<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
include("../../../../library/classes/class.tests.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

function update_prv_rec(){
	//Pending:'topo_treat'=>2,
	$arr = array(	'bscan'=>2,'labs'=>3,'cellcount'=>4,'icg'=>5,
				'nfa'=>6,'vf'=>7,'vfgl'=>8,'oct'=>9,'oct_rnfl'=>10,
				'gdx'=>11,'pacchy'=>12,
				'disc'=>14,'discexternal'=>15,'topo'=>16,
				'other'=>18				
			);
	$arr=array_flip($arr);		
	$objTests	= new Tests;
	$ar_tst = $objTests->get_active_tests();
	
	$arr_sub_type = array(				
				"oct" => array("Optic Nerve","Retina","Anterior Segment"),
				"disc" => array("Disc Photos", "Macula Photos", "Retina Photos"),
				"discexternal" => array("ES (External)","ASP (Anterior Segment Photos)"),
				"topo" => array("Topography","Treatment")
				);
	
	foreach($ar_tst as $key => $val){
		$test_master_id = $val["id"];
		if($test_master_id==1 || $test_master_id==17){ continue; }
		
		$val_opt= !empty($arr[$test_master_id]) ? $arr[$test_master_id] : "common";
		
		$stp_lm=1;
		if($val_opt=="oct" || $val_opt=="disc" ||$val_opt=="discexternal" ||$val_opt=="topo"){
			if($val_opt=="oct" || $val_opt=="disc"){
				$stp_lm=3;
			}else if($val_opt=="discexternal" || $val_opt=="topo"){
				$stp_lm=2;
			}
		}
		
		for($stp=1;$stp<=$stp_lm;$stp++){
			
			$tsub="";	
			$tsub=isset($arr_sub_type[$val_opt]) ? $arr_sub_type[$val_opt][$stp-1] : "" ;
			if($val_opt=="topo" && $stp==2){$val_opt="topo_treat";}

			$ar_opt = $objTests->getDiagOpts($stp,$val_opt);			
			if(count($ar_opt)>0){
				foreach($ar_opt as $kk => $vv){
					if(!empty($vv) && $vv != "Other"){
						
						$sql = "SELECT * FROM test_diagnosis WHERE diag_nm='".sqlEscStr($vv)."' AND test_id='".$test_master_id."' AND test_sub_type='".$tsub."' and del_by= '0' ";
						$row = sqlQuery($sql);
						if($row == false){
							$sql = "INSERT INTO test_diagnosis SET diag_nm='".sqlEscStr($vv)."', test_id='".$test_master_id."', test_sub_type='".$tsub."'; ";
							//echo "<br/>".$sql;
							sqlQuery($sql);
						}	
					}
				}
			}
		}
	}

}

if(isset($_GET["st"]) && !empty($_GET["st"])){

update_prv_rec();
exit("Process done!");
}else{
	echo "<a href=\"?st=1\">click</a>";
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