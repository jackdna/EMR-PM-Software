<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

/*
<sel  elem_name="elem_cdValOs" ></sel><text  elem_name="elem_cdOs" ></text>
C:D: 0.35
*/

//$rrr = '{"objects":[{"type":"citext","originX":"left","originY":"top","left":150,"top":420,"width":60,"height":18.35,"fill":"#171717","stroke":null,"strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","text":"C:D: 0.15","fontSize":"14","fontWeight":"normal","fontFamily":"Arial","fontStyle":"","lineHeight":1.16,"textDecoration":"","textAlign":"left","textBackgroundColor":"","styles":{}},{"type":"citext","originX":"left","originY":"top","left":500,"top":420,"width":60,"height":18.35,"fill":"#171717","stroke":null,"strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","text":"C:D: 0.85","fontSize":"14","fontWeight":"normal","fontFamily":"Arial","fontStyle":"","lineHeight":1.16,"textDecoration":"","textAlign":"left","textBackgroundColor":"","styles":{}},{"type":"cPath","originX":"center","originY":"center","left":327.31,"top":152.96,"width":128.06,"height":126.22,"fill":null,"stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"round","strokeLineJoin":"round","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","path":[["M",390.42896174863387,94.593147751606],["Q",390.42896174863387,94.593147751606,390.92896174863387,94.593147751606],["Q",391.42896174863387,94.593147751606,391.17896174863387,92.10385438972162],["Q",390.92896174863387,89.61456102783725,366.99453551912563,90.11241970021413],["Q",343.06010928961746,90.610278372591,339.5696721311475,99.57173447537473],["Q",336.07923497267757,108.53319057815845,361.0109289617486,127.45182012847965],["Q",385.94262295081967,146.37044967880084,373.9754098360656,163.79550321199142],["Q",362.0081967213115,181.220556745182,317.629781420765,198.64561027837257],["Q",273.25136612021856,216.07066381156315,268.26502732240436,216.07066381156315],["L",263.27868852459017,216.07066381156315]],"pathOffset":{"x":327.31093191779854,"y":152.961465601734}},{"type":"cPath","originX":"center","originY":"center","left":365.63,"top":221.05,"width":57.11,"height":165.29,"fill":null,"stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"round","strokeLineJoin":"round","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","path":[["M",393.4207650273224,138.40471092077087],["Q",393.4207650273224,138.40471092077087,393.9207650273224,138.40471092077087],["Q",394.4207650273224,138.40471092077087,392.1762295081967,140.89400428265523],["Q",389.93169398907105,143.3832976445396,389.93169398907105,195.16059957173445],["Q",389.93169398907105,246.93790149892934,382.95081967213116,262.8693790149893],["Q",375.96994535519127,278.80085653104925,356.52322404371586,291.2473233404711],["L",337.07650273224044,303.69379014989295]],"pathOffset":{"x":365.63325206460456,"y":221.0492505353319}},{"type":"cPath","originX":"center","originY":"center","left":450.88,"top":287.76,"width":48.1,"height":191.18,"fill":null,"stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"round","strokeLineJoin":"round","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","path":[["M",474.19945355191254,192.17344753747324],["Q",474.19945355191254,192.17344753747324,474.69945355191254,192.17344753747324],["Q",475.19945355191254,192.17344753747324,471.4590163934426,195.65845824411133],["Q",467.71857923497265,199.14346895074945,457.74590163934425,251.91648822269804],["Q",447.77322404371586,304.68950749464665,443.7841530054645,332.0717344753747],["Q",439.7950819672131,359.4539614561028,433.31284153005464,371.40256959314775],["L",426.8306010928962,383.3511777301927]],"pathOffset":{"x":450.8809721577334,"y":287.76231263383295}},{"type":"cPath","originX":"center","originY":"center","left":499.84,"top":331.08,"width":42.3,"height":94.59,"fill":null,"stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"round","strokeLineJoin":"round","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","path":[["M",520.0737704918033,283.779443254818],["Q",520.0737704918033,283.779443254818,520.5737704918033,283.779443254818],["Q",521.0737704918033,283.779443254818,520.8237704918033,284.27730192719486],["Q",520.5737704918033,284.77516059957173,505.1161202185792,327.093147751606],["Q",489.6584699453552,369.41113490364023,484.17349726775956,373.89186295503214],["L",478.6885245901639,378.372591006424]],"pathOffset":{"x":499.8382543221702,"y":331.076017130621}}],"background":""}';

//$rrt = '<?xml version="1.0" encoding="ISO-8859-1" ?*><cd ><od ><cdr examname="C:D" ><sel  elem_name="elem_cdValOd" >0.45</sel><text  elem_name="elem_cdOd" ></text><cup examname="CUP" ><small  elem_name="elem_onOd_cup_small" ></small><moderate  elem_name="elem_onOd_cup_moderate" ></moderate><large  elem_name="elem_onOd_cup_large" ></large></cup><sup_rim examname="Superior Rim" ><intact  elem_name="elem_onOd_srim_Intact" ></intact><thin  elem_name="elem_onOd_srim_Thin" ></thin><cupd2_rim  elem_name="elem_onOd_srim_c2rim" ></cupd2_rim></sup_rim><inf_rim examname="Inferior Rim" ><intact  elem_name="elem_onOd_irim_Intact" ></intact><thin  elem_name="elem_onOd_irim_Thin" ></thin><cupd2_rim  elem_name="elem_onOd_irim_c2rim" ></cupd2_rim></inf_rim><opt_nrv_hmg examname="Optic Nerve Hmg" ><at_clock  elem_name="elem_onOd_onhmg" ></at_clock></opt_nrv_hmg></cdr></od></cd>';

$ptrn1 = "/<sel\s*elem\_name\=\"elem_cdValO(s|d)\"\s*\>\d*(\.\d*)?\<\/sel\>/"; //\<text\s*elem\_name\=\"elem_cdOs\"\s*\>\<\/text\>
$ptrn2 = "/C\:D\:\s*\d*(\.\d*)?/";
$ptrn3 = "/C\:D\:/";

$sql = " select id, drw_data_json, patient_form_id, patient_id  from ".constant("IMEDIC_SCAN_DB").".idoc_drawing WHERE deletedby ='0' AND drw_data_json != ''  ";
$res = sqlStatement($sql);
for($i=1; $row=sqlFetchArray($res);$i++){
	
	$dt_json = $row["drw_data_json"];
	$patient_form_id = $row["patient_form_id"];
	$patient_id = $row["patient_id"];
	$drw_id = $row["id"];

	$cd_on_od = $cd_on_os=""; 
	$cd_drw_od = $cd_drw_os=""; 
	
	if(!empty($dt_json)){			
		if(strpos($dt_json, "C:D:")!==false){		
			if(preg_match_all($ptrn3, $dt_json, $match_drw)){				
				if(preg_match_all($ptrn2, $dt_json, $match_drw)){					
					if(!empty($match_drw[0][0])){
						$cd_on_od = $match_drw[0][0];
						$cd_on_od = str_replace("C:D:", "",$cd_on_od);
						$cd_on_od = trim($cd_on_od);
						//$echo .= ", Drw1: ".$match_drw[0];
					}
					if(!empty($match_drw[0][1])){
						$cd_on_os = $match_drw[0][1];
						$cd_on_os = str_replace("C:D:", "",$cd_on_os);
						$cd_on_os = trim($cd_on_os);
						//$echo .= ", Drw2: ".$match_drw[1];	
					}	
				}
			}			
		}
	}	
	
	$sql = "SELECT cdr_od, cdr_os FROM `chart_optic` WHERE patient_id='".$patient_id."' AND form_id='".$patient_form_id."' AND purged='0'  ";
	$row1 = sqlQuery($sql);
	if($row1!=false){
		
		$cdr_od = trim($row1["cdr_od"]);
		$cdr_os = trim($row1["cdr_os"]);
		
		if(!empty($cdr_od)){
			//echo "<xmp>".$cdr_od."</xmp>";
			if(preg_match_all($ptrn1, $cdr_od, $match_drw)){
				if(isset($match_drw[0][0]) && !empty($match_drw[0][0])){
					$cd_od = str_replace(array("<sel  elem_name=\"elem_cdValOd\" >", "<sel elem_name=\"elem_cdValOd\">", "</sel>"), "", $match_drw[0][0]);
					if(!empty($cd_od)){
						//echo "<xmp>".$cd_od."</xmp>";
						$cd_drw_od = trim($cd_od);
					}
				}
			}			
			//echo "<br/><br/>------------<br/><br/>";
		}
		
		if(!empty($cdr_os)){
			if(preg_match_all($ptrn1, $cdr_os, $match_drw)){
				if(isset($match_drw[0][0]) && !empty($match_drw[0][0])){
					$cd_os = str_replace(array("<sel  elem_name=\"elem_cdValOs\" >", "<sel elem_name=\"elem_cdValOs\">", "</sel>"), "", $match_drw[0][0]);
					if(!empty($cd_os)){
						//echo "<xmp>".$cd_os."</xmp>";
						$cd_drw_os = trim($cd_os);
					}
				}
			}			
			//echo "<br/><br/>------------<br/><br/>";
		}
	
	
	}
	
	//
	$echo="";
	$flg = 0;
	if(!empty($cd_drw_od) && !empty($cd_drw_os) && !empty($cd_on_od) && !empty($cd_on_os)){		
		if($cd_drw_od!=$cd_on_od || $cd_drw_os!=$cd_on_os){
			$flg = 1;
			$echo.=" - ".$cd_drw_od.", ".$cd_drw_os.", ".$cd_on_od.", ".$cd_on_os;
		}
	}	
	
	if(!empty($flg)){
	
		$sql = "SELECT date_of_service FROM chart_master_table where id = '".$patient_form_id."'";
		$row =  sqlQuery($sql);
		if($row!=false){	$dos = $row["date_of_service"]; }
	
	
		echo "<br/> Patient_id: ".$patient_id.", Form_id:".$patient_form_id.", Draw Id:".$drw_id.", DOS:".$dos.$echo;
	}
}

exit("<br>DONE");

?>
<html>
<head>
<title> SPL. Update </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>