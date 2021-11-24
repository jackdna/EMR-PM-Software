<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
File: menu_data.php
Purpose: This file provide Menu data for vision section in work view. It also have other common variables set in it.
Access Type : Include file
*/

include_once('../../config/globals.php');

include_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/ChartTemp.php');

function getPtVisit($id=0){
	$arr=array();
	$where = (!empty($id)) ? "WHERE tech_id='".$id."' " : "";
	$sql= "SELECT DISTINCT ptVisit, tech_id FROM tech_tbl ".$where." ORDER BY ptVisit";
	$rez = imw_query($sql);
	while($rezArr = imw_fetch_array($rez)){
		$row[] = $rezArr;	
	}
	for( $i=1;$i<count($row);$i++ ){
		if(!empty($row["ptVisit"]) && !empty($row["tech_id"])){
			$arr[$row["tech_id"]] = $row["ptVisit"];
		}
	}
	return $arr;
}


function getMrPersonnal($f=0,$flg="")
{
	$arr=array();
	$provSql  = getPersonnal($flg);
	while($provRt = imw_fetch_assoc($provSql))
	{
		//$mrProviderName = $provRt['lname'].",&nbsp;".$provRt['fname']."&nbsp;".$provRt['mname'];
		$mrProviderName = $provRt['fname'];
		$mrProviderName .= !empty($provRt['lname']) ? "&nbsp;".strtoupper(substr($provRt['lname'],0,1))."" : "" ;
		$mrProviderName = (strlen($mrProviderName) > 30) ? substr($mrProviderName,0,28).".." : $mrProviderName;
		$id = $provRt['id'];
		if($f == 0){
			$arr[$mrProviderName] = array($mrProviderName,$arrEmpty,$mrProviderName."-".$id);
		}else if($f == 1){
			$arr[$id] = $mrProviderName;
		}else if($f == 2){
			$mrProviderName = $provRt['fname'];
			$mrProviderName .= !empty($provRt['mname']) ? " ".strtoupper(substr($provRt['mname'],0,1))."" : "" ;
			$mrProviderName .= !empty($provRt['lname']) ? " ".$provRt['lname']."" : "" ; 
			$mrProviderName = (strlen($mrProviderName) > 100) ? substr($mrProviderName,0,98).".." : $mrProviderName;			
			$arr[$id] = $mrProviderName;
		}
	}
	return $arr;
}

function getPersonnal($flgCn="")
{
	if($flgCn == "cn"){
		$utId = "";
		$utId .= implode($GLOBALS['arrValidCNPhy'],",");
		if(!empty($utId) && count($GLOBALS['arrValidCNTech'])>0){$utId .= ",";}
		$utId .= implode($GLOBALS['arrValidCNTech'],",");
	}else	if($flgCn == "cn2"){
		$utId = "";
		$utId .= implode($GLOBALS['arrValidCNPhy'],",");		
	}else{
		$utId = "1,3";
	}

	$qry = "select lname,fname,mname,id from users where user_type IN (".$utId.") order by user_type, fname, lname ";	
	$res = imw_query($qry);
	return $res;
}

function getFuOptions($id=0,$flgArrOp=0,$srch=""){

	$sql ="SELECT * FROM chart_fu_options ";
	$sql .= (!empty($id)) ? "WHERE fu_id = '".$id."' " : "";
	$sql .= (!empty($srch)&&empty($id)) ? "WHERE optName LIKE '".$srch."%' " : "";
	$sql .="ORDER BY optName ";
	$rez = imw_query($sql);

	if($flgArrOp == 1){
		$arr = array();
		while($rzArr = imw_fetch_array($rez)){
			$row[] = $rzArr;
		}
		for( $i=1;$i<count($row);$i++ ){
			$arr[] = stripslashes($row["optName"]);
		}
		return $arr;

	}else{
		return $rez;
	}
}

/* include_once(dirname(__FILE__)."/../../main/main_functions.php");
include_once(dirname(__FILE__)."/ChartTemp.php");
include_once(dirname(__FILE__)."/functions.php"); */

/*
$arrSel1_menu=array('SC'=>array('SC',$arr,'SC'),
						'CC'=>array('CC',$arr,'CC'),
						'CL-S'=>array('CL-S',$arr,'CL-S'),
						'GPCL'=>array('GPCL',$arr,'GPCL'));

	$arrSel2_menu=array("PH"=>array('PH',$arr,'PH'),
						'GL'=>array('GL',$arr,'GL'),
						'SC'=>array('SC',$arr,'SC'),
						'CC'=>array('CC',$arr,'CC'));
	
	$arrSel3_menu=array('SC'=>array('SC',$arr,'SC'),
						'CC'=>array('CC',$arr,'CC'),
						'CL-S'=>array('CL-S',$arr,'CL-S'),
						'GPCL'=>array('GPCL',$arr,'GPCL'),
						'MV'=>array('MV',$arr,'MV'));	
						
	$arrSel4_menu=array('GL'=>array('GL',$arr,'GL'),
						'SC'=>array('SC',$arr,'SC'),
						'CC'=>array('CC',$arr,'CC'));	
						
	$arrSel5_menu=array('High'=>array('High',$arr,'High'),
						'Med'=>array('Med',$arr,'Med'),
						'Low'=>array('Low',$arr,'Low'));			
						
	$arrSel6_menu=array("SV"=>array('SV',$arr,'SV'),
						'BF'=>array('BF',$arr,'BF'),
						'Progs'=>array('Progs',$arr,'Progs'),
						'TRF'=>array('TRF',$arr,'TRF'));			
	
	$arrSel7_menu=array('PH'=>array('PH',$arr,'PH'),
						'GL'=>array('GL',$arr,'GL'));	
										
	$arrSel8_menu=array("Athwal Harjit Singh"=>array('Athwal Harjit Singh',$arr,'Athwal Harjit Singh'),
						'Kapur Arun'=>array('Kapur Arun',$arr,'Kapur Arun'),
						'Rana Rajan'=>array('Rana Rajan',$arr,'Rana Rajan'),
						'singh ramsaini'=>array('singh ramsaini',$arr,'singh ramsaini'));					
	$arrSel9_menu=array('Days'=>array('Days',$arr,'Days'),
						'Weeks'=>array('Weeks',$arr,'Weeks'),
						'Months'=>array('Months',$arr,'Months'),
						'Year'=>array('Year',$arr,'Year'));	
					
	$arrSel10_menu=array('OU'=>array('OU',$arr,'OU'),
						 'OD'=>array('OD',$arr,'OD'),
						 'OS'=>array('OS',$arr,'OS'));
						
	$arrSel11_menu=array('Cataract'=>array('Cataract',$arr,'Cataract'),
						 'Refractive Surgery'=>array('Refractive Surgery',$arr,'Refractive Surgery'),
						 'Other'=>array('Other',$arr,'Other'));	
								
	$arrSel12_menu=array('Cataract'=>array('Cataract',$arr,'Cataract'),
						 'Lens Implants'=>array('Lens Implants',$arr,'Lens Implants'),
						 'Yag Laser'=>array('Yag Laser',$arr,'Yag Laser'),
						 'Refractive Surgery'=>array('Refractive Surgery',$arr,'Refractive Surgery'),
						 'Other'=>array('Other',$arr,'Other'));
						 
	$arrSel13_menu=array('Athwal, Harjit Singh'=>array('Athwal, Harjit Singh',$arr,'Athwal, Harjit Singh'),
						 'Kapur, Arun'=>array('Kapur, Arun',$arr,'Kapur, Arun'),
						 'Sareen, Ritika'=>array('Sareen, Ritika',$arr,'Sareen, Ritika'),
						 'Refractive Surgery'=>array('Refractive Surgery',$arr,'Refractive Surgery'),
						 'Young, How'=>array('Young, How',$arr,'Young, How'));					 					
							
	
	$arrP=array("0"=>array("&nbsp;&nbsp;&nbsp;",$arrEmpty,"&nbsp;&nbsp;&nbsp;"),
			 "1"=>array("1",$arrEmpty,"1"),
			 "1.5"=>array("1.5",$arrEmpty,"1.5"),
			 "2"=>array("2",$arrEmpty,"2"),
			 "2.5"=>array("2.5",$arrEmpty,"2.5"),
			 "3"=>array("3",$arrEmpty,"3"),
			 "3.5"=>array("3.5",$arrEmpty,"3.5"),
			 "4"=>array("4",$arrEmpty,"4"),
			 "4.5"=>array("4.5",$arrEmpty,"4.5"),
			 "5"=>array("5",$arrEmpty,"5"),
			 "5.5"=>array("5.5",$arrEmpty,"5.5"),
			 "6"=>array("6",$arrEmpty,"6"),
			 "6.5"=>array("6.5",$arrEmpty,"6.5"),
			 "7"=>array("7",$arrEmpty,"7"),
			 "7.5"=>array("7.5",$arrEmpty,"7.5"),
			 "8"=>array("8",$arrEmpty,"8"),
			 "8.5"=>array("8.5",$arrEmpty,"8.5"),
			 "9"=>array("9",$arrEmpty,"9"),
			 "9.5"=>array("9.5",$arrEmpty,"9.5"),
			 "10"=>array("10",$arrEmpty,"10"));
	
	
	$arrPrism = array("BD"=>array("BD",$arrEmpty,"BD"),
				 "BU"=>array("BU",$arrEmpty,"BU"));
	$arrBiBo = array("BI"=>array("BI",$arrEmpty,"BI"),
				"BO"=>array("BO",$arrEmpty,"BO"));	
				
				
	$arrtest = array("NFA"=>array("NFA",$arrEmpty,"NFA"),
				"Macula"=>array("Macula",$arrEmpty,"Macula"));		
				
	$arrtest1 = array("Disc"=>array("Disc",$arrEmpty,"Disc"),
				"Macula"=>array("Macula",$arrEmpty,"Macula"));	
				
				
	$arrtest2 = array("COAG/POAG"=>array("COAG/POAG",$arrEmpty,"COAG/POAG"),
				"Macula"=>array("Macula",$arrEmpty,"Macula"),
				"Neuro"=>array("Neuro",$arrEmpty,"Neuro"),
				"DMV"=>array("DMV",$arrEmpty,"DMV"));	
*/				
/*
	$arrNeuroPsych = array("WNL" => array("WNL",$arrEmpty,"WNL"),
					   "Confused"=> array("Confused",$arrEmpty,"Confused"),
					   "Flat"=> array("Flat",$arrEmpty,"Flat"),
					   "Agitated"=> array("Agitated",$arrEmpty,"Agitated"),
					   "Uncooperative"=> array("Uncooperative",$arrEmpty,"Uncooperative"),
					   "Mental Retardation"=> array("Mental Retardation",$arrEmpty,"Mental Retardation"),
					   "AOx3"=> array("AOx3",$arrEmpty,"AOx3"),
					   	"Affect Normal"=> array("Affect Normal",$arrEmpty,"Affect Normal")
					  );
*/
	$arrNeuroPsych = array("Agitated"=> array("Agitated",$arrEmpty,"Agitated"),	
					   "AAOx3"=> array("AAOx3",$arrEmpty,"AAOx3"),
					   "Confused"=> array("Confused",$arrEmpty,"Confused"),
					   "Flat"=> array("Flat",$arrEmpty,"Flat"),
					   "Cognitive Impairment"=> array("Cognitive Impairment",$arrEmpty,"Cognitive Impairment"),
					   "Too Young (Pediatric Patient)"=> array("Too Young (Pediatric Patient)",$arrEmpty,"Too Young (Pediatric Patient)"),
					   "Uncooperative"=> array("Uncooperative",$arrEmpty,"Uncooperative")					   
					   /*"Affect Normal"=> array("Affect Normal",$arrEmpty,"Affect Normal")*/
					  );

	$arPtVisitTmp = getPtVisit();
	$arrPtVisit = array();
	if( count($arPtVisitTmp) > 0 ){
		foreach($arPtVisitTmp as $key => $var){
			$arrPtVisit[$var]=array($var, $arrEmpty, $var);
		}
	}	
	$arrPtVisit["OD"]=array("OD", $arrEmpty, "OD");
	$arrPtVisit["OS"]=array("OS", $arrEmpty, "OS");
	$arrPtVisit["OU"]=array("OU", $arrEmpty, "OU");
	$arrPtVisit["Other"]=array("Other", $arrEmpty, "");
	
	/*
	$arrPtVisit = array("CEE" => array("CEE",$arrEmpty,"CEE"),
				  "Follow-Up" => array("Follow-Up",$arrEmpty,"Follow-Up"),
				  "ER" => array("ER",$arrEmpty,"ER"),
				  "Consult" => array("Consult",$arrEmpty,"Consult"),
				  "Ophthalmoscopy" => array("Ophthalmoscopy",$arrEmpty,"Ophthalmoscopy"),
				  "Post-Op" => array("Post-Op",$arrEmpty,"Post-Op"),
				  "Minor Surgery" => array("Minor Surgery",$arrEmpty,"Minor Surgery"),
				  "Other" => array("Other",$arrEmpty,"")
				);			  
	*/
	//$arrDiskPhotoOp = array('External'=>array('External',$arrEmpty,'External'),
	//					'Anterior Segment'=>array('Anterior Segment',$arrEmpty,'Anterior Segment'));
	/*
	$arrDiskPhotoOp = array('External' => array('External',$arrEmpty,'External'),
						'Anterior Segment' => array('Anterior Segment',$arrEmpty,'Anterior Segment'));
	
	$arrPtTesting = array("Empty" => array("Empty",$arrEmpty,"Empty"),
				  "Gonio" => array("Gonio",$arrEmpty,"Gonio"),
				  "Disc Photo" => array("Disc Photo",$arrEmpty,"Disc Photo"),
				  "Pachy" => array("Pachy",$arrEmpty,"Pachy"),
				  "VF" => array("VF",$arrEmpty,"VF"),
				  "NFA/HRT" => array("NFA/HRT",$arrEmpty,"NFA/HRT"),
				  "Color Plates" => array("Color Plates",$arrEmpty,"Color Plates"),
				  "Other" => array("Other",$arrEmpty,"")
				);
	*/
	$arr_OdOs = array('OD'=>array('OD',$arr,'OD'),
				  'OS'=>array('OS',$arr,'OS'));			
	//			
	$arrPhysician = getPhysicianMenuArray(1,"cn");
	//Get Mr Providers
	$arrMrPersonnal = getMrPersonnal(1,"cn");
	//Acuities MR/ Dis
	$arrAcuitiesMrDis = array("20/15"=>array("20/15",$arrEmpty,"20/15"),
					 "20/20"=>array("20/20",$arrEmpty,"20/20"),
					 "20/25"=>array("20/25",$arrEmpty,"20/25"),
					 "20/30"=>array("20/30",$arrEmpty,"20/30"),
					 "20/40"=>array("20/40",$arrEmpty,"20/40"),					 
					 "20/50"=>array("20/50",$arrEmpty,"20/50"),
					 "20/60"=>array("20/60",$arrEmpty,"20/60"),
					 "20/70"=>array("20/70",$arrEmpty,"20/70"),
					 "20/80"=>array("20/80",$arrEmpty,"20/80"),					 
					 "20/100"=>array("20/100",$arrEmpty,"20/100"),
					 "20/150"=>array("20/150",$arrEmpty,"20/150"),
					 "20/200"=>array("20/200",$arrEmpty,"20/200"),
					 "20/300"=>array("20/300",$arrEmpty,"20/300"),					 
					 "20/400"=>array("20/400",$arrEmpty,"20/400"),
					 "20/600"=>array("20/600",$arrEmpty,"20/600"),
					 "20/800"=>array("20/800",$arrEmpty,"20/800"),
					 "CF"=>array("CF",$arrEmpty,"CF"),
					 "CF 1ft"=>array("CF 1ft",$arrEmpty,"CF 1ft"),
					 "CF 2ft"=>array("CF 2ft",$arrEmpty,"CF 2ft"),
					 "CF 3ft"=>array("CF 3ft",$arrEmpty,"CF 3ft"),
					 "CF 4ft"=>array("CF 4ft",$arrEmpty,"CF 4ft"),
					 "CF 5ft"=>array("CF 5ft",$arrEmpty,"CF 5ft"),
					 "CF 6ft"=>array("CF 6ft",$arrEmpty,"CF 6ft"),
					 "HM"=>array("HM",$arrEmpty,"HM"),
					 "LP"=>array("LP",$arrEmpty,"LP"),					 
					 "LP c p"=>array("LP c p",$arrEmpty,"LP c p"),
					 "LP s p"=>array("LP s p",$arrEmpty,"LP s p"),
					 "NLP"=>array("NLP",$arrEmpty,"NLP"),
					 "F&F"=>array("F&F",$arrEmpty,"F&F"),
					 "F/(F)"=>array("F/(F)",$arrEmpty,"F/(F)"),
					 "2/200"=>array("2/200",$arrEmpty,"2/200"),
					 "CSM"=>array("CSM",$arrEmpty,"CSM"),
					 "Enucleation"=>array("Enucleation",$arrEmpty,"Enucleation"),
					 "Prosthetic"=>array("Prosthetic",$arrEmpty,"Prosthetic"),
					 "Pt Uncoopera"=>array("Pt Uncoopera",$arrEmpty,"Pt Uncoopera"),
					 "Unable"=>array("Unable",$arrEmpty,"Unable"),
					 "5/200"=>array("5/200",$arrEmpty,"5/200"));

//Acuities Near
$arrAcuitiesNear = array('20/20(J1+)'=>array('20/20(J1+)',$arr,'20/20(J1+)'),
					  '20/25(J1)'=>array('20/25(J1)',$arr,'20/25(J1)'),
					  '20/30(J2)'=>array('20/30(J2)',$arr,'20/30(J2)'),
					  '20/40(J3)'=>array('20/40(J3)',$arr,'20/40(J3)'),
					  '20/32(J4)'=>array('20/32(J4)',$arr,'20/32(J4)'),
					  '20/50(J5)'=>array('20/50(J5)',$arr,'20/50(J5)'),
					  '20/60(J6)'=>array('20/60(J6)',$arr,'20/60(J6)'),
					  '20/70(J7)'=>array('20/70(J7)',$arr,'20/70(J7)'),
					  '20/63(J8)'=>array('20/63(J8)',$arr,'20/63(J8)'),
					  '20/80'=>array('20/80',$arr,'20/80'),
					  '20/100(J10)'=>array('20/100(J10)',$arr,'20/100(J10)'),
					  '20/200(J16)'=>array('20/200(J16)',$arr,'20/200(J16)'),
					  '20/400'=>array('20/400',$arr,'20/400'),
					  '20/800'=>array('20/800',$arr,'20/800'),
					  'APC 20/30'=>array('APC 20/30',$arr,'APC 20/30'),
					  'APC 20/40'=>array('APC 20/40',$arr,'APC 20/40'),
					  'APC 20/60'=>array('APC 20/60',$arr,'APC 20/60'),
					  'APC 20/80'=>array('APC 20/80',$arr,'APC 20/80'),
					  'APC 20/100'=>array('APC 20/100',$arr,'APC 20/100'),
					  'APC 20/160'=>array('APC 20/160',$arr,'APC 20/160'),
					  'APC 20/200'=>array('APC 20/200',$arr,'APC 20/200'),
					  'CSM'=>array('CSM',$arr,'CSM'),
					  '(C)SM'=>array('(C)SM',$arr,'(C)SM'),
					  'C(S)M'=>array('C(S)M',$arr,'C(S)M'),
					  'CS(M)'=>array('CS(M)',$arr,'CS(M)'),
					  'C(S)(M)'=>array('C(S)(M)',$arr,'C(S)(M)'),
					  '(C)(S)M'=>array('(C)(S)M',$arr,'(C)(S)M'),
					  '(C)S(M)'=>array('(C)S(M)',$arr,'(C)S(M)'),
					  '(C)(S)(M)'=>array('(C)(S)(M)',$arr,'(C)(S)(M)'),
					  'F&F'=>array('F&F',$arr,'F&F'),
					  'Unable'=>array('Unable',$arr,'Unable'));
					  
//*					  
//Vision snellan
$arrSnellan = array(		'Snellen Letters'=>array('Snellen Letters',$arr,'Snellen Letters'),
					'Snellen Number'=>array('Snellen Number',$arr,'Snellen Number'),
					'HOTV c CB'=>array('HOTV c CB',$arr,'HOTV c CB'),					
					'HOTV s CB'=>array('HOTV s CB',$arr,'HOTV s CB'),
					'Lea c CB'=>array('Lea c CB',$arr,'Lea c CB'),
					'Lea s CB'=>array('Lea s CB',$arr,'Lea s CB'),
					'Lea Symbols'=>array('Lea Symbols',$arr,'Lea Symbols'),
					'Lea Numbers'=>array('Lea Numbers',$arr,'Lea Numbers'),
					'Allen Card'=>array('Allen Card',$arr,'Allen Card'),
					'PLT-Teller'=>array('PLT-Teller',$arr,'PLT-Teller'),
					
					'BVAT Letters'=>array('BVAT Letters',$arr,'BVAT Letters'),
					'BVAT Letters c CB'=>array('BVAT Letters c CB',$arr,'BVAT Letters c CB'),
					'BVAT Numbers'=>array('BVAT Numbers',$arr,'BVAT Numbers'),
					'BVAT Numbers c CB'=>array('BVAT Numbers c CB',$arr,'BVAT Numbers c CB'),
					'BVAT Allen'=>array('BVAT Allen',$arr,'BVAT Allen'),
					'BVAT Allen CB'=>array('BVAT Allen CB',$arr,'BVAT Allen CB'),
					'BVAT HOTV'=>array('BVAT HOTV',$arr,'BVAT HOTV'),
					'BVAT HOTV c CB'=>array('BVAT HOTV c CB',$arr,'BVAT HOTV c CB'),
					
					'Sloan Letters'=>array('Sloan Letters',$arr,'Sloan Letters'),
					'Toys'=>array('Toys',$arr,'Toys'),
					'TAC'=>array('TAC',$arr,'TAC'),
					'Intense Light'=>array('Intense Light',$arr,'Intense Light'),
					'Isolated E'=>array('Isolated E',$arr,'Isolated E'),
					'Isolated Letters'=>array('Isolated Letters',$arr,'Isolated Letters'),
					'Tumbling Es'=>array('Tumbling Es',$arr,'Tumbling Es'),		
					'Landolt C'=>array('Landolt C',$arr,'Landolt C'),
					'Other'=>array('Other',$arr,'Other'));

//*/	
	//Fu number
	$arrFuNum_menu = array();	
	for($i=1;$i<=14;$i++){
		$txt = $i;
		if($i == 11){
			$txt = "Today";
		}
		if($i == 12){
			$txt = "Calendar";
		}
		if($i == 13){
			$txt = "PRN";
		}
		if($i == 14){
			$txt = "PMD";
		}		
		$arrFuNum_menu[] = array($txt,$emp,$txt);
	}
	$arrFuNum_menu[] = array(" - ",$emp,"-");
	
	//Fu Visit Type
	//8/21/2011: F/U drop down does not match what is defined in Admin
	//$arrFuVist = array("CEE/DFE","ER/Acute","CL Fit");
	$arrFuVist = array();
	$tmp = getFuOptions(0,1);	
	if(count($tmp) > 0){
		$arrFuVist = array_merge($arrFuVist,$tmp);
	}
	$arrFuVist_menu = array();
	foreach($arrFuVist as $key => $val){
		$arrFuVist_menu[] = array($val,$emp,$val);
	}
	$arrFuVist_menu[] = array("Other",$emp,"Other"); //Other	
	
	
///Drop down option for Contact Lens Section  By Ram ///
$ComfortOptionsArray = array("Comfortable"=>array("Comfortable",$arrEmpty,"Comfortable"),
					 "Uncomfortable"=>array("Uncomfortable",$arrEmpty,"Uncomfortable"),
					 "Dry"=>array("Dry",$arrEmpty,"Dry"),
					 "Itchy"=>array("Itchy",$arrEmpty,"Itchy"),					 
					 "Feel Edges"=>array("Feel Edges",$arrEmpty,"Feel Edges"));
										
 $MovementOptionsArray = array("&lt;� mm"=>array("&lt;� mm",$arrEmpty,"&lt;� mm"),
 "=� mm"=>array("=� mm",$arrEmpty,"=� mm"),
 "&gt;� mm"=>array("&gt;� mm",$arrEmpty,"&gt;� mm"),
 "Loose"=>array("Loose",$arrEmpty,"Loose"),					 
 "Tight"=>array("Tight",$arrEmpty,"Tight"));

$PositionOptionsArray = array("centered"=>array("Centered",$arrEmpty,"Centered"),
					 "Superior"=>array("Superior",$arrEmpty,"Superior"),
					 "Inferior"=>array("Inferior",$arrEmpty,"Inferior"),
					 "Nasal"=>array("Nasal",$arrEmpty,"Nasal"),					 
					 "Temporal"=>array("Temporal",$arrEmpty,"Temporal"),
					 "Clear Limbus"=>array("Clear Limbus",$arrEmpty,"Clear Limbus"),
					 "Over Limbus"=>array("Over Limbus",$arrEmpty,"Over Limbus"));
					 
$ConditionOptionsArray = array("Clean"=>array("Clean",$arrEmpty,"Clean"),
 "Deposits"=>array("Deposits",$arrEmpty,"Deposits"),
 "Tear"=>array("Tear",$arrEmpty,"Tear"),
 "Other"=>array("Other",$arrEmpty,"Other"));
 $ColorOptionsArray = array("Blue"=>array("Blue",$arrEmpty,"Blue"),
 "Green"=>array("Green",$arrEmpty,"Green"));

$FluoresceinPatternOptionsArray =array("Pooling"=>array("Pooling",$arrEmpty,"Pooling"),
 "Baring"=>array("Baring",$arrEmpty,"Baring"),
 "Parallel"=>array("Parallel",$arrEmpty,"Parallel"));
 
$InvertedLidsOptionsArray=array("Clean"=>array("Clean",$arrEmpty,"Clean"),
 "Papillae"=>array("Papillae",$arrEmpty,"Papillae"),
 "Mucous"=>array("Mucous",$arrEmpty,"Mucous"));
 
$BlendOptionsArray=array("Light"=>array("Light",$arrEmpty,"Light"),
 "Medium"=>array("Medium",$arrEmpty,"Medium"),
 "Heavy"=>array("Heavy",$arrEmpty,"Heavy"));
 
///Drop down option for Contact Lens Section  By Ram ///

//Chart Template Options
$arrPtTemplate = array();
$arrPtTemplate["Comprehensive"] = array("Comprehensive",$arrEmpty,"Comprehensive"."-[_]-"."0");

$oChartTemp = new ChartTemp();
$tmp = $oChartTemp->getAll();
$tmpLn = count($tmp);
if($tmpLn > 0){
	for($i=0;$i<$tmpLn;$i++){
		if(!empty($tmp[$i]["id"]) && !empty($tmp[$i]["name"])){
			$arrPtTemplate[$tmp[$i]["name"]] = array($tmp[$i]["name"],$arrEmpty,$tmp[$i]["name"]."-[_]-".$tmp[$i]["id"]);
		}		
	}
}

//Chart Template Options
///Code To make new Drop down with JQuery//
$strAcuitiesMrDisString ='"20/20","20/25","20/30","20/40","20/50","20/60","20/70","20/80","20/100","20/150","20/200","20/300","20/400","20/600","20/800","20/CF","20/HM","20/LP","20/NLP"';
$strAcuitiesNearString = "'20/20(J1+)','20/25(J1)','20/30(J2)','20/40(J3)','20/32(J4)','20/50(J5)','20/70(J7)','20/63(J8)','20/100(J10)','20/200(J16)'";
$ComfortOptionsString = '"Comfortable","Uncomfortable","Dry","Itchy","Feel Edges"';
$MovementOptionsString ='">1/2 mm","=1/2 mm","<1/2 mm","Loose","Tight","Good"';
$PositionOptionsString ='"Centered","Superior","Inferior","Nasal","Temporal","Clear Limbus","Over Limbus"';
$ConditionOptionsString ='"Clean","Deposits","Tear","Other"';
$ColorOptionsString= '"Blue","Green"';
$FluoresceinPatternOptionsString ='"Pooling","Baring","Parallel"';
$InvertedLidsOptionsString='"Clean","Papillae","Mucous"';
$BlendOptionsString='"Light","Medium","Heavy"';
///Code To make new Drop down with JQuery//


//Eye Color
$arrEyeColorOpts = array("Amber","Black","Blue","Brown","Grey","Green","Hazel","Violet","Other");
foreach($arrEyeColorOpts as $key => $val){
	$sv = ($val == "Other") ? "" : $val;
	$arrEyeColor[] = array($val,$emp,$sv);
}

?>
