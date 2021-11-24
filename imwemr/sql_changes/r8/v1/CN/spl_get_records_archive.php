<?php
ini_set('max_execution_time', 0);
$ignoreAuth = true;
include("../../../../config/globals.php");
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

//--
$flg=0;
if(isset($_GET["st"]) && !empty($_GET["st"])){
	$oChartRecArc = new ChartRecArc(0,0,0);
	$oChartRecArc->print_arc_record($_GET["st"]);
	$flg=1;
}else if(!empty($_GET["pid"]) && !empty($_GET["fid"]) && !empty($_GET["ins_bin_id"]) && !empty($_GET["tbl"])){
	$oChartRecArc = new ChartRecArc($_GET["pid"],$_GET["fid"],1);
	$oChartRecArc->reset_binary_data($_GET["ins_bin_id"], $_GET["tbl"]);
	$flg=1;
}
/*
else if(!empty($_GET["pid"]) && !empty($_GET["fid"]) &&  !empty($_GET["tbl"])){
	
	$ar = array('elem_sumOdVitreous'=>array('vitreous_od_summary', '1+ Floaters', 'smof', 'wnlVitreousOd', 'WNL_VIT2', ''),
			'elem_sumOsVitreous'=>array('vitreous_os_summary', 'Present Pigment', 'smof', 'wnlVitreousOs', 'WNL_VIT2', ''),
			'elem_sumOdRetinal'=>array('retinal_od_summary', 'Superotemporal Cotton Wool Spot', 'smof', 'wnlRetinalOd', 'WNL_RET2', ''),
			'elem_sumOsRetinal'=>array('retinal_os_summary', 'Present Retinal Pigment Epithelial Detachment', 'smof', 'wnlRetinalOs', 'WNL_RET2', ''),
			'elem_sumOdMacula'=>array('macula_od_summary', '', 'smof', 'wnlMaculaOd', '', ''),
			'elem_sumOsMacula'=>array('macula_os_summary', '', 'smof', 'wnlMaculaOs', '', ''),
			'elem_sumOdPeriphery'=>array('periphery_od_summary', '', 'smof', 'wnlPeriOd', '', ''),
			'elem_sumOsPeriphery'=>array('periphery_os_summary', '', 'smof', 'wnlPeriOs', '', ''),
			'elem_sumOdBV'=>array('blood_vessels_od_summary', '', 'smof', 'wnlBVOd', '', ''),
			'elem_sumOsBV'=>array('blood_vessels_od_summary', '', 'smof', 'wnlBVOd', '', '')			
			);
	
	$oChartRecArc = new ChartRecArc($_GET["pid"],$_GET["fid"],1);
	$oChartRecArc->setChkTbl($_GET["tbl"]);
	$oChartRecArc->getArcRec($ar);
	
}
*/

if($flg==1){
print("<br>DONE!");
}

?>