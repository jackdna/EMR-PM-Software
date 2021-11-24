<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php"); 
include("common_functions.php");
$objManageData = new manageData;
$headerBar='yes';
$pConfId= $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId= $_SESSION['pConfId'];
}
function getMedListFn($tableName,$fieldNameComma,$condtionField,$condtionValue,$prevMedArr=array(),$extraCondition='') {
	global $objManageData;
	$mdArr = $fieldNameArr = array();
	$qry = "SELECT ".$fieldNameComma." FROM ".$tableName." WHERE ".$condtionField." = ".$condtionValue." ".$extraCondition;
	$res = imw_query($qry) or die(imw_error());
	$fieldNameArr = explode(",",$fieldNameComma);
	if(imw_num_rows($res)>0) {
		while($row = imw_fetch_array($res)) {
			foreach($fieldNameArr as $fieldName) {
				$colArr = array();
				if(trim($row[$fieldName])<>"") {
					$colArr = explode(",",$row[$fieldName]);
					foreach($colArr as $colVal) {
						$mdArr[] = trim(stripslashes($colVal));	
					}
				}
			}
		}
	}
	return array_merge($prevMedArr,$mdArr);
}

function getHeightFun($val,$valNew) {
	$hghtTemp1 = '20';
	$lengthVal = strlen($val);
	if($lengthVal>=50) {
		$hghtTemp1 = ceil($lengthVal/3);
	}
	$lengthValNew = strlen($valNew);
	if($lengthValNew>=50) {
		$hghtTemp2 = ceil($lengthValNew/3);
	}
	$hght = $hghtTemp1;
	if($hghtTemp2>$hghtTemp1) { $hght = $hghtTemp2; }
	return 	$hght;
}
$tableMeds =	'';
include_once("new_header_print.php");
if($headerBar=='yes') {$tableMeds.='<page backtop="5" >';}
$tableMeds.=$head_table;

$medsArr = array();

//Pre-Op Nursing -> Medication Taken Today
$medsArr = getMedListFn("patient_prescription_medication_tbl","prescription_medication_name","confirmation_id",$pConfId,$medsArr);

//Pre-Op Physician Order -> List of Pre-OP Medication Orders
$medsArr = getMedListFn("patientpreopmedication_tbl","medicationName","patient_confirmation_id",$pConfId,$medsArr);

//Post-Op Physician Order -> Physician Orders
$medsArr = getMedListFn("postopphysicianorders","patientToTakeHome","patient_confirmation_id",$pConfId,$medsArr);

//Local Anesthesia -> Holding Area through Intra-Op
$blank1Qry=$blank2Qry=$blank3Qry=$blank4Qry=$propofolQry=$midazolamQry=$ketamineQry=$labetalolQry=$FentanylQry='';
for($aa=1;$aa<=20;$aa++) {
	$blank1Qry 		.= ", lm.blank1_".$aa;
	$blank2Qry 		.= ", lm.blank2_".$aa;
	$blank3Qry 		.= ", lm.blank3_".$aa;
	$blank4Qry 		.= ", lm.blank4_".$aa;
	$propofolQry 	.= ", lm.propofol_".$aa;
	$midazolamQry 	.= ", lm.midazolam_".$aa;
	$ketamineQry 	.= ", lms.ketamine_".$aa;
	$labetalolQry 	.= ", lms.labetalol_".$aa;
	$FentanylQry 	.= ", lms.Fentanyl_".$aa;
}

$localAnesQry = "SELECT lm.blank1_label $blank1Qry $blank2Qry $blank3Qry $blank4Qry $propofolQry $midazolamQry $ketamineQry $labetalolQry $FentanylQry, lm.blank2_label, lm.blank3_label, lm.blank4_label,lm.mgPropofol_label,lm.mgMidazolam_label,lms.mgKetamine_label,lms.mgLabetalol_label,lms.mcgFentanyl_label, 
					 la.Reblock,la.TopicalBlock1Block2,la.Block1Block2Aspiration,la.Block1Block2Full,la.Block1Block2BeforeInjection,la.Block1Block2Comment,
					 la.topical4PercentLidocaine,la.Intracameral,la.Intracameral1percentLidocaine,la.Peribulbar,la.Peribulbar2percentLidocaine,
					 la.Retrobulbar,la.Retrobulbar4percentLidocaine,la.Hyalauronidase4percentLidocaine,la.VanLindr,la.VanLindrHalfPercentLidocaine,la.bupivacaine75,la.marcaine75,la.lidTxt,
					 la.lidEpi5ug,la.otherRegionalAnesthesiaTxt1,la.otherRegionalAnesthesiaWydase15u,la.otherRegionalAnesthesiaTxt2
				 FROM localanesthesiarecord la
				 LEFT JOIN localanesthesiarecordmedgrid lm ON la.confirmation_id = lm.confirmation_id
				 LEFT JOIN localanesthesiarecordmedgridsec lms ON la.confirmation_id = lms.confirmation_id
				 WHERE la.confirmation_id = '".$pConfId."'
				";
$localAnesRes = imw_query($localAnesQry) or die(imw_error());
$localAnesArr = array();
if(imw_num_rows($localAnesRes)>0) {
	$localAnesRow = imw_fetch_array($localAnesRes);
	extract($localAnesRow);	
	$boolBlank1=$boolBlank2=$boolPropofol=$boolMidazolam=$boolKetamine=$boolLabetalol=$boolFentanyl=false;
	for($bb=1;$bb<=20;$bb++){
		$blCnd1 = 'blank1_'.$bb;	if(trim($$blCnd1)!="" && trim($$blCnd1)!="@@") 	{$boolBlank1	= true;	}
		$blCnd2 = 'blank2_'.$bb;	if(trim($$blCnd2)!="" && trim($$blCnd2)!="@@") 	{$boolBlank2	= true;	}
		$blCnd3 = 'blank3_'.$bb;	if(trim($$blCnd3)!="" && trim($$blCnd3)!="@@") 	{$boolBlank3	= true;	}
		$blCnd4 = 'blank4_'.$bb;	if(trim($$blCnd4)!="" && trim($$blCnd4)!="@@") 	{$boolBlank4	= true;	}
		$blCnd5 = 'propofol_'.$bb;	if(trim($$blCnd5)!="" && trim($$blCnd5)!="@@") 	{$boolPropofol	= true;	}
		$blCnd6 = 'midazolam_'.$bb;	if(trim($$blCnd6)!="" && trim($$blCnd6)!="@@") 	{$boolMidazolam	= true;	}
		$blCnd7 = 'ketamine_'.$bb;	if(trim($$blCnd7)!="" && trim($$blCnd7)!="@@") 	{$boolKetamine	= true;	}
		$blCnd8 = 'labetalol_'.$bb;	if(trim($$blCnd8)!="" && trim($$blCnd8)!="@@") 	{$boolLabetalol	= true;	}
		$blCnd9 = 'Fentanyl_'.$bb;	if(trim($$blCnd9)!="" && trim($$blCnd9)!="@@") 	{$boolFentanyl	= true;	}
	}
	
	if(trim($blank1_label)!='' && $boolBlank1 == true)			{$localAnesArr[] = trim(stripslashes($blank1_label)); 		}
	if(trim($blank2_label)!='' && $boolBlank2 == true) 			{$localAnesArr[] = trim(stripslashes($blank2_label)); 		}
	if(trim($blank3_label)!='' && $boolBlank3 == true)			{$localAnesArr[] = trim(stripslashes($blank3_label)); 		}
	if(trim($blank4_label)!='' && $boolBlank4 == true) 			{$localAnesArr[] = trim(stripslashes($blank4_label)); 		}
	if(trim($mgPropofol_label)!='' && $boolPropofol == true) 	{$localAnesArr[] = trim(stripslashes($mgPropofol_label)); 	}
	if(trim($mgMidazolam_label)!='' && $boolMidazolam == true)	{$localAnesArr[] = trim(stripslashes($mgMidazolam_label)); 	}
	if(trim($mgKetamine_label)!='' && $boolKetamine == true) 	{$localAnesArr[] = trim(stripslashes($mgKetamine_label)); 	}
	if(trim($mgLabetalol_label)!='' && $boolLabetalol == true) 	{$localAnesArr[] = trim(stripslashes($mgLabetalol_label)); 	}
	if(trim($mcgFentanyl_label)!='' && $boolFentanyl == true) 	{$localAnesArr[] = trim(stripslashes($mcgFentanyl_label)); 	}
	
	if($Reblock=='Yes') 							{$localAnesArr[] = 'Reblock'; 											}
	if($TopicalBlock1Block2 && $TopicalBlock1Block2!='NA') { 
		$localAnesArr[] = $TopicalBlock1Block2;
		if($TopicalBlock1Block2=='Block1' || $TopicalBlock1Block2=='Block2') {
			if($Block1Block2Aspiration=='Yes') 		{$localAnesArr[] = 'Aspiration'; 										}
			if($Block1Block2Full=='Yes') 			{$localAnesArr[] = 'Full EOM'; 											}
			if($Block1Block2BeforeInjection=='Yes')	{$localAnesArr[] = 'Before Injection'; 									}
			if(trim($Block1Block2Comment)!='')		{$localAnesArr[] = trim(stripslashes($Block1Block2Comment)); 			}
		}
	}
	if($TopicalBlock1Block2!='NA') { 
		if($topical4PercentLidocaine=='Yes')		{$localAnesArr[] = '4% lidocaine'; 										}
		if($Intracameral!='') 						{$localAnesArr[] = 'Intracameral'; 										}
		if($Intracameral1percentLidocaine=='Yes') 	{$localAnesArr[] = '1% lidocaine MPF'; 									}
		if($Peribulbar  !='') 						{$localAnesArr[] = 'Peribulbar'; 										}
		if($Peribulbar2percentLidocaine=='Yes') 	{$localAnesArr[] = '2% lidocaine'; 										}
		if($Retrobulbar !='') 						{$localAnesArr[] = 'Retrobulbar'; 										}
		if($Retrobulbar4percentLidocaine=='Yes') 	{$localAnesArr[] = '3% lidocaine'; 										}
		if($Hyalauronidase4percentLidocaine=='Yes') {$localAnesArr[] = '4% lidocaine'; 										}
		if($VanLindr    !='') 						{$localAnesArr[] = 'Van Lindt'; 										}
		if($VanLindrHalfPercentLidocaine=='Yes') 	{$localAnesArr[] = '0.5% Bupivacaine'; 									}
		if($bupivacaine75=='Yes') 					{$localAnesArr[] = '0.75% Bupivacaine'; 								}
		if($marcaine75=='Yes') 						{$localAnesArr[] = '0.75% Marcaine';	 								}
		if(trim($lidTxt)!='') 						{$localAnesArr[] = trim(stripslashes($lidTxt).' lid'); 					}
		if($lidEpi5ug=='Yes') 						{$localAnesArr[] = 'Epi 5 ug/ml'; 										}
		if(trim($otherRegionalAnesthesiaTxt1)!='') 	{$localAnesArr[] = trim(stripslashes($otherRegionalAnesthesiaTxt1)); 	}
		if($otherRegionalAnesthesiaWydase15u=='Yes'){$localAnesArr[] = 'Wydase 15 u/ml'; 									}
		if(trim($otherRegionalAnesthesiaTxt2)!='') 	{$localAnesArr[] = trim(stripslashes($otherRegionalAnesthesiaTxt2));	}
	}
}
$medsArr = array_merge($localAnesArr,$medsArr);

//Operating Room Record -> Post Op Medication
$opRoomQry = "SELECT postOpDrops,
				bssValue,Epinephrine03,Vancomycin01,Vancomycin02,infusionBottleOther,
				Healon,Occucoat,Provisc,Miostat,HealonGV,Discovisc,AmviscPlus,TrypanBlue,Healon5,
				Viscoat,Miochol,XylocaineMPF,OtherSuppliesUsed,
				Betadine,Saline,Alcohol,Prcnt5Betadinegtts,prepSolutionsOther,
				Solumedrol,Dexamethasone,Kenalog,Vancomycin,Ancef,Gentamicin,Depomedrol,postOpInjOther,
				patch,shield,needleSutureCount
			 FROM operatingroomrecords 
			 WHERE confirmation_id = '".$pConfId."'
				";
$opRoomRes = imw_query($opRoomQry) or die(imw_error());
$opRoomArr = array();
if(imw_num_rows($opRoomRes)>0) {
	$opRoomRow = imw_fetch_array($opRoomRes);
	extract($opRoomRow);
	
	//Post-Op Medication
	if(trim($postOpDrops)!='') {
		$postOpDropsArr = explode(",",$postOpDrops);
		foreach($postOpDropsArr as $postOpDropsVal) {
			$opRoomArr[] = $postOpDropsVal; 
		}
	}
	
	//Product Control
	if($bssValue=="bss"){
		$opRoomArr[] = 'BSS'; 
	}elseif($bssValue=="bssPlus") {
		$opRoomArr[] = 'BSS Plus';
	}
	if($Epinephrine03=='Yes')			{$opRoomArr[] = 'Epinephrine 0.3ml (300mcg)'; 				}
	if($Vancomycin01=='Yes')			{$opRoomArr[] = 'Vancomycin 0.1 ml (5mg)'; 					}
	if($Vancomycin02=='Yes')			{$opRoomArr[] = 'Vancomycin 0.2 ml (10 mg)'; 				}
	if(trim($infusionBottleOther)!='')	{$opRoomArr[] = trim(stripslashes($infusionBottleOther));	}
	
	//Supplies Used
	if($Healon=='Yes')					{$opRoomArr[] = 'Healon'; 									}
	if($Occucoat=='Yes')				{$opRoomArr[] = 'Occucoat'; 								}
	if($Provisc=='Yes')					{$opRoomArr[] = 'Provisc'; 									}
	if($Miostat=='Yes')					{$opRoomArr[] = 'Miostat'; 									}
	if($HealonGV=='Yes')				{$opRoomArr[] = 'HealonGV'; 								}
	if($Discovisc=='Yes')				{$opRoomArr[] = 'Duovisc'; 									}
	if($AmviscPlus=='Yes')				{$opRoomArr[] = 'Amvisc Plus'; 								}
	if($TrypanBlue=='Yes')				{$opRoomArr[] = 'Trypan Blue'; 								}
	if($Healon5=='Yes')					{$opRoomArr[] = 'Healon5'; 									}
	if($Viscoat=='Yes')					{$opRoomArr[] = 'Viscoat'; 									}
	if($Miochol=='Yes')					{$opRoomArr[] = 'Miochol'; 									}
	if($XylocaineMPF=='Yes')			{$opRoomArr[] = 'Xylocaine MPF 1%'; 						}
	if(trim($OtherSuppliesUsed)!='')	{$opRoomArr[] = trim(stripslashes($OtherSuppliesUsed)); 	}

	//Prep Solutions
	if($Betadine=='Yes')				{$opRoomArr[] = 'Betadine 10%'; 							}
	if($Saline=='Yes')					{$opRoomArr[] = 'Saline'; 									}
	if($Alcohol=='Yes')					{$opRoomArr[] = 'Alcohol'; 									}
	if($Prcnt5Betadinegtts=='Yes')		{$opRoomArr[] = '5% Betadine gtts'; 						}
	if(trim($prepSolutionsOther)!='')	{$opRoomArr[] = trim(stripslashes($prepSolutionsOther)); 	}

	//Inter Op INJ
	if($Solumedrol=='Yes')				{$opRoomArr[] = 'Solumedrol'; 								}
	if($Dexamethasone=='Yes')			{$opRoomArr[] = 'Dexamethasone'; 							}
	if($Kenalog=='Yes')					{$opRoomArr[] = 'Kenalog'; 									}
	if($Vancomycin=='Yes')				{$opRoomArr[] = 'Vancomycin'; 								}
	if($Ancef=='Yes')					{$opRoomArr[] = 'Ancef'; 									}
	if($Gentamicin=='Yes')				{$opRoomArr[] = 'Gentamicin'; 								}
	if($Depomedrol=='Yes')				{$opRoomArr[] = 'Depomedro'; 								}
	if(trim($postOpInjOther)!='')		{$opRoomArr[] = trim(stripslashes($postOpInjOther)); 		}
	if(trim($patch)!='')				{$opRoomArr[] = 'Patch '.trim(stripslashes($patch)); 		}
	if($shield=='Yes')					{$opRoomArr[] = 'Shield'; 									}
	if($needleSutureCount=='Yes')		{$opRoomArr[] = 'Needle/Suture count Correct'; 				}

}
$medsArr = array_merge($opRoomArr,$medsArr);
$medsNewArr = array();
if(count($medsArr)>0) {
	$medsArr = array_unique($medsArr);
	@natcasesort($medsArr);
	foreach($medsArr as $med) {$medsNewArr[] = htmlentities($med); }
}

$tableMeds.='
	<table style="width:750px;" cellpadding="0" cellspacing="0">
		<tr>
			<td style="width:750px;text-align:center;" class="tb_headingHeader ">List Of Medication Used</td>
		</tr>
	 </table>
	<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
		<tr>';
			$a=0;
			foreach($medsNewArr as $key => $val) {
				if($key==0) {
					$hght = getHeightFun($val,$medsNewArr[$key+1])	;	
				}
				$hghtTemp1 = ceil($lengthVal/3);
				$hghtTemp2 = $hghtTemp1;
				if(($a%2)==0) {
					$tableMeds.='</tr><tr>';
					$hght = getHeightFun($val,$medsNewArr[$key+1])	;
				}
				$a++;
$tableMeds.='				
				<td style="width:5px;vertical-align:text-top;height:'.$hght.'px;" class="bdrbtm_new"><img src="../images/arrow_right.gif" style="border:none;"></td>
				<td style="width:350px;vertical-align:text-top;height:'.$hght.'px;" class="bdrBtmRght">'.$val.'</td>';
			
				if(($key+1)==count($medsNewArr) && ($a%2)!=0) {
$tableMeds.='				
				
				<td colspan="2" style="width:365px;vertical-align:text-top;height:'.$hght.'px;" class="bdrBtmRght"></td>';
				}
			}
$tableMeds.='
		</tr>
	</table> ';

if($headerBar=='yes') {$tableMeds.='</page>';}	
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$tableMeds);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';

?>	

 <form name="printDischargeSheet" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form>

<script language="javascript">
	window.focus();
	function submitfn(){
		document.printDischargeSheet.submit();
	}
</script>

<script type="text/javascript">
	submitfn();
</script>

