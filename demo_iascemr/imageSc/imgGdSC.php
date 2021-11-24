<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("imgGd.php");
////
// id,tbl,pixelField,idField,imgName
////

	//Set values
	$pixels = "TUp:138,33;CDr:193,42;CFill:290,229;96:458,400;23:202,126;96:362,254;1.5:154,400;0.5:218,400;0:250,400;ram:330,318;36:138,512;SB:138,544;22:154,558;mg Succinylchol..:41,543;Helo world:41,558;TUp:251,436;Cr:268,452;CDr:284,468;CFill:300,483;TDn:315,500;96:250,578;63:330,594;ST:282,610;100:346,624;66:394,640;1.5:330,384;7.5:378,416;96:458,142;36:314,126;12:186,206;00:602,14;36:138,14;66:138,366;36:602,366;TDn:349,5;TDn:443,356;CFill:203,354;CFill:219,354;CFill:395,3;CFill:604,148;CFill:140,132;CDr:524,355;CDr:139,212;CDr:604,212;CDr:284,3;TUp:333,354;TUp:491,2;TUp:604,243;TUp:140,275;";	
	$pathSave = "";
	
	//Get Values
	if(!empty($_GET["id"]))
	{
		$id = $_GET["id"];
		$tbl = $_GET["tbl"];
		$pixelField = $_GET["pixelField"];
		$idField = $_GET["idField"];
		$imgName = $_GET["imgName"];
		$pathSave = $_GET["pathSave"];
		$qry = "SELECT $pixelField FROM $tbl WHERE $idField = $id";		
		$row = sqlQuery($qry);	
		$pixels = $row[$pixelField];		
	}else if(isset($path2Save) && !empty($path2Save)){
		$pathSave = $path2Save;
	}
	//Get Image	
	drawOnImage($pixels,$imgName,$pathSave); 

?>