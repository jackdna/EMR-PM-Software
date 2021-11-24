<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartApXml.php
Coded in PHP7
Purpose: This is another class file for creating xml operations on assessment and plan.
Access Type : Include file
*/
?>
<?php
//class
//ChartApXml.php

//XML Template
/*
<?xml version="1.0" encoding="UTF-8"? >
<assess_plan>
	<data>
		<ap>
			<assessment></assessment>
			<plan></plan>
			<ne></ne>
			<resolve></resolve>
			<conmed></conmed>
		</ap>
	</data>
	<updates>
		<update>
			<dt_time></dt_time>
			<usrId></usrId>
		</update>
	</updates>
</assess_plan>
*/
//

class ChartApXml{
	private $db;
	private $pId;
	private $fId;
	private $xhdr;

	public function __construct($pid='',$fid=''){
		$this->db = $GLOBALS["adodb"]["db"];
		$this->pId = $pid;
		$this->fId = $fid;
		$this->xhdr = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
	}
	
	public function encodeSplChars($str){
		if(!empty($str)){
			$str = $this->clearBadChars($str);			
		}
		return $str;
		//return str_replace(array("&","<",">","\\","\"","�","-"),array("&amp;","&lt;","&gt;","&apos;","&quot;","&#45;","&#45;"),$str);
		//return htmlspecialchars($str,ENT_QUOTES);
	}

	//Get str xml for saving
	public function getXml($arr,$uid,$retModi=""){

		$arrAssess=$arr[0];
		$arrPlan=$arr[1];
		$arrResolve=$arr[2];
		$arrNe=$arr[3];
		$arrEye=$arr[4];
		$arrConMed=$arr[5];
		$arrProbListId=$arr[6];
		
		
		//--
		// Make new Xml
		// Get Old xml if any
		// GET USER INFO from old xml
		// Create a new xml with new info and user info
		//--

		//Step 1. Get Older xml if form id
		// GET User Info in array
		$arrXml = $this->getVal();
		
		//echo "<pre>";
		//print_r($arrXml);
		//echo "<br/>";
		//exit();

		//Create DOM
		$dom = new DOMDocument('1.0','utf-8');
		$assess_plan = $dom->appendChild($dom->createElement('assess_plan'));
		$data = $assess_plan->appendChild($dom->createElement('data'));

		//Step 2. Make new data block
		//Loop array values
		$length = (count($arrAssess) >= count($arrPlan)) ? count($arrAssess) : count($arrPlan);
		//$strBlockData = "";
		for($i=0;$i<$length;$i++){
			if(trim($arrAssess[$i]) || trim($arrPlan[$i])) {
				$ap = $data->appendChild($dom->createElement('ap'));
				
				$asCdata = $dom->createCDATASection($this->encodeSplChars($arrAssess[$i]));
				$assessment = $dom->createElement('assessment');
				$assessment->appendChild($asCdata);
				$ap->appendChild($assessment);
				
				$apCdata = $dom->createCDATASection($this->encodeSplChars($arrPlan[$i]));
				$plan = $dom->createElement('plan');
				$plan->appendChild($apCdata);
				$ap->appendChild($plan);
	
				$ne = $ap->appendChild($dom->createElement('ne',$arrNe[$i]));
				$resolve = $ap->appendChild($dom->createElement('resolve',$arrResolve[$i]));
				$eye = $ap->appendChild($dom->createElement('eye',$arrEye[$i]));
				$conmed = $ap->appendChild($dom->createElement('conmed',$arrConMed[$i]));
				$pbid = $ap->appendChild($dom->createElement('pbid',$arrProbListId[$i]));
			}
		}
		
		//Test--
			
		if($retModi==1){
			//String modifications
			$strModi="";
			$strUsrinfo="";
			$strUsrinfo="".date("m-d-y H:i")." ";
			if(!empty($uid)){
			
				if(!class_exists("User")){
					require(dirname(__FILE__)."/User.php");				
				}
				
				$ouser = new User($uid);
				$strUsrinfo.="".$ouser->getName(1);
			}
			
			if(count($arrXml["data"]["ap"]) > 0){
			
				//Get Last userId
				if(count($arrXml["updates"]["update"]) > 0){
					$prvUid = $arrXml["updates"]["update"][0]["usrId"];	
				}
				if(!empty($prvUid) && $prvUid!=$uid){
					
					foreach($arrXml["data"]["ap"] as $j => $val){					
						$flgModi=0;
						$key = array_search($val["assessment"], $arrAssess);					
						if($key!==false){
							
							if($val["plan"]!=$arrPlan[$key] || $val["eye"]!=$arrEye[$key] ){
								$flgModi=1;	
							}
						
						}else{
							$flgModi=1;	
						}
						
						//
						if($flgModi==1){
							$strModi.="<div>
									$strUsrinfo<br/>
									<div class=\"md_s\">".($j+1).".</div>
									<div class=\"md_as\">".nl2br($val["assessment"])."</div>
									<div class=\"md_i\">".$val["eye"]."</div>
									<div class=\"md_pln\">".nl2br($val["plan"])."</div>
									</div>
									";
						}
					}
				}	
			}	
		}//ifretModii=1		
			
		//echo "$prvUid != $uid";exit();
		//echo "OUTPUT - ".$strModi." -- ";	
		//Test --	
		
		//exit("DONE");
		
		//Add new user update block
		if(!empty($uid)){
			$updates = $assess_plan->appendChild($dom->createElement('updates'));

			$update = $updates->appendChild($dom->createElement('update'));
			$dt_time = $update->appendChild($dom->createElement('dt_time',time()));
			$usrId = $update->appendChild($dom->createElement('usrId',$uid));

			//Get Older Update block Block
			//$strBlocksUpdate_old = "";
			if(count($arrXml["updates"]["update"]) > 0){
				//$strBlocksUpdate_old = "";
				foreach($arrXml["updates"]["update"] as $val){
					$update = $updates->appendChild($dom->createElement('update'));
					$dt_time = $update->appendChild($dom->createElement('dt_time',$val["dt_time"]));
					$usrId = $update->appendChild($dom->createElement('usrId',$val["usrId"]));
					
				}
			}
		}
		
		if($retModi==1){
			return array($dom->saveXML(), $strModi);
		}else{
			//return xml string
			return $dom->saveXML();
		}
	}

	function getVal_Str($strXml){
		//Ret Array
		$arrRet = array();

		//Check empty string
		if(empty($strXml)){
			return $arrRet;
		}

		//Clean Bad Chars
		$strXml = $this->clearBadChars($strXml,1);

		//4 Errors
		libxml_use_internal_errors(true);

		//Make DOM xml
		$dom = new DOMDocument;

		//Ignore White space in xml
		$dom->preserveWhiteSpace = false;

		//Load xml string
		$flgRes = $dom->loadXML($strXml);

		//Check for bad xml
		if($flgRes == false){
			$errors = libxml_get_errors();
			if(!empty($errors))
			{
				$lines = explode("r", $strXml);
				$line = $lines[($error->line)-1];
				$error = $errors[ 0 ];
				if ($error->level < 3)
				{

				}else{
					$message = $error->message . ' at line ' . $error->line . ': ' . htmlentities($line);

					echo "<font color=\"Red\">Error:".$message."</font> ".
						 "<xmp>".str_replace($this->xhdr,"",$strXml)."</xmp>";
					//exit;
					return $arrRet;
				}
			}
		}

		//root element
		$root = $dom->documentElement;

		//Data
		$oData = $dom->getElementsByTagname("data")->item(0);
		//object ap under data for individual assessments

		//Array
		$arrTND = array("assessment", "plan", "ne", "resolve","eye","conmed","pbid");

		//Loop
		$c=0;
		$oData_cns = $oData->childNodes;

		if($oData_cns->length > 0){
			foreach($oData_cns as $ap){
				//Check node 4 element
				if($ap->nodeType == 1){
					//Loop for data values
					foreach($arrTND as $key => $nmd){
						//$nmd
						$oNmd = $ap->getElementsByTagname($nmd)->item(0);
						$vNmd = $oNmd->firstChild->nodeValue;
						$arrRet["data"]["ap"][$c][$nmd]=$vNmd;
						//$nmd
					}
					$c++;
				}
			}
		}
		//Updates
		$oUpdates = $dom->getElementsByTagname("updates")->item(0);
		//Get Individual update tags

		//Array
		$arrTNU = array("dt_time", "usrId");

		//Loop
		$c=0;
		$oUp_cns = $oUpdates->childNodes;

		if($oUp_cns->length > 0){
			foreach($oUp_cns as $update){
				//Check if Node is element
				if($update->nodeType == 1){
					//Loop for values
					foreach($arrTNU as $key => $nmu){
						//$nmu
						$oNmu = $update->getElementsByTagname($nmu)->item(0);
						$vNmu = $oNmu->firstChild->nodeValue;
						$arrRet["updates"]["update"][$c][$nmu]=$vNmu;
						//$nmu
					}
					$c++;
				}
			}
		}

		//Return Array
		return($arrRet);
	}

	//Get Xml values in array for use
	function getVal(){

		//Get xml string from db
		$strXml = $this->getDbVal();
		$arrRet = $this->getVal_Str($strXml);

		//Return Array
		return($arrRet);
	}

	//Get xml values from database
	public function getDbVal(){

		$sql= "SELECT assess_plan FROM chart_assessment_plans ".
				"WHERE form_id='".$this->fId."'".
				"AND patient_id = '".$this->pId."'";
		$res = get_array_records_query($sql);
		if($res !== false){
			$strXml = $res[0]["assess_plan"];
		}

		return !empty($strXml) ? $strXml : "";
	}

	public function clearBadChars($str,$flg=0){
		
		
		if($flg == 1){

			$indx = strpos($str,"?>");
			if($indx !== false){
				$str = substr($str,$indx+2);
			}
			//$str = str_replace(array("�","-","�","'","&acirc;"),array("&#45;","&#45;","&#39;","&#39;","-"),$str);			
			$str = preg_replace('/\x10|\x92/', '', $str); //remove no printable character DLE 
			$str = $this->xhdr.$str;
		}else{
			//$str = str_replace(array("�","-","�","'"),array("&#45;","&#45;","&#39;","&#39;"),$str);			
			$str = preg_replace('/\x10|\x92/', '', $str); //[\x10-\x1F\x80-\xFF]  //remove no printable character DLE
		}
		

		return $str;
	}

	function fuGetXmlValsArr($xfup){
		// this function is copy from chartNoteSaveFunction.php
		$arr = array();
		if(!empty($xfup)){

			$xfup = $this->clearBadChars($xfup,1);

			$ox = simplexml_load_string($xfup);
			$len = count($ox->fu);
			if($len > 0){
				foreach($ox->fu as $fux){
					$arrTmp = array();
					$arrTmp["number"] = $fux->number;
					$arrTmp["time"] = $fux->time;
					$arrTmp["visit_type"] = $fux->visit_type;
					$arr[] = $arrTmp;
				}
			}
		}
		return array($len, $arr);
	}

	function compare2Asesment($as1,$as2){

		$arr=array();
		//Temporal arteritis - OU (446.5), POAG - OU, Transient visual loss (368.12)
		//get str before site
		//1
		$as_nosite_1 = $as1;
		$ind = strpos($as1,"- O");
		if($ind!==false){
			$as_nosite_1 =  trim(substr($as1,0,$ind));
		}else{
			$ind = strpos($as1,"(");
			if($ind!==false){
				$as_nosite_1 =  trim(substr($as1,0,$ind));
			}
		}
		//2
		$as_nosite_2=$as2;
		$ind = strpos($as2,"- O");
		if($ind!==false){
			$as_nosite_2 =  trim(substr($as2,0,$ind));
		}else{
			$ind = strpos($as2,"(");
			if($ind!==false){
				$as_nosite_2 =  trim(substr($as2,0,$ind));
			}
		}

		if(strcasecmp($as_nosite_1,$as_nosite_2)==0){
			//true
			$arr["res"]=true;
			//New String --
			if(strpos($as2,"OU")!==false||
				(strpos($as1,"OD")!==false&&strpos($as2,"OS")!==false)||
				(strpos($as1,"OS")!==false&&strpos($as2,"OD")!==false)){
				$arr["newAsmt"] = preg_replace('/\bO(U|D|S)\b/', "OU",$as1);
			}
			//New String --
		}else{
			//false
			$arr["res"]=false;
		}

		return $arr;
	}

	function mergeApArr($arr1,$arr2){
		$ln = count($arr2["A"]);
		for($i=0;$i<$ln;$i++){
			$tmpAs = $this->clearBadChars($arr2["A"][$i]);
			$flg=true;
			$ln2 = count($arr1["assess"]);
			for($j=0;$j<$ln2;$j++){
				$arrChk = $this->compare2Asesment($arr1["assess"][$j],$tmpAs);
				if($arrChk["res"]==true){
					if(isset($arrChk["newAsmt"])&&!empty($arrChk["newAsmt"])){
						$arr1["assess"][$j]=$arrChk["newAsmt"];
					}
					$flg=false;
					break;
				}
			}

			if($flg==true){
				$arr1["assess"][] = $arr2["A"][$i];
				$arr1["plan"][] = $arr2["P"][$i];
				$arr1["resolve"][] = $arr2["R"][$i];
				$arr1["ne"][] = $arr2["N"][$i];
				$arr1["eye"][] = $arr2["EYE"][$i];
				$arr1["pbid"][] = $arr2["PBID"][$i];
			}
		}
		return $arr1;
	}
	
	function getPrevPlans($dos){
		
		$sql = "SELECT c2.assess_plan FROM chart_master_table c1
				INNER JOIN chart_assessment_plans c2 ON c2.form_id = c1.id
				WHERE c1.patient_id = '".$this->pId."' AND c1.id < '".$this->fId."' AND c1.date_of_service <= '".$dos."' 
				ORDER BY c1.date_of_service DESC, c1.id DESC
				LIMIT 0,1 ";		
		$res = get_array_records_query($sql);
		if($res !== false){
			$strXml = $res[0]["assess_plan"];
		}		
		
		$arrRet = $this->getVal_Str($strXml);	
		
		return $arrRet;	
	}
}

/*
//TEST to Check

$ass = array("a11","a12");
$pss = array("p11","p12");
$ne = array("1","0");
$re = array("1","1");
$uId=1;

$oChartApXml = new ChartApXml(1,2);
$strXml = $oChartApXml->getXml($ass,$pss,$ne,$re,$uId);
print $strXml;

*/

?>