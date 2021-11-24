<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartNote.php
Coded in PHP7
Purpose: This Class provide many basic functions common done in a chart note.
Access Type : Include file
*/
?>
<?php
//ChartNote
class ChartNote{
	public $fid;
	public $pid;
	private $dos;
	private $enctrId;
	private $uid;
	private $ar_archive_tbls;
	public function __construct($pid, $fid){
		$this->fid = $fid;
		$this->pid = $pid; //
		$this->dos = "";
		$this->enctrId=0;
		$this->uid = $_SESSION["authId"];
		$this->ar_archive_tbls = array();
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			$this->ar_archive_tbls = array("chart_drawings", "chart_lac_sys", "chart_lesion", "chart_lids", "chart_lid_pos",
									"chart_blood_vessels", "chart_macula", "chart_periphery", "chart_retinal_exam", "chart_vitreous",
									"chart_ant_chamber", "chart_conjunctiva", "chart_cornea", "chart_iris", "chart_lens", "chart_optic");
		}
	}

	public function getFormId(){
		return $this->fid;
	}

	public function setFormId($id){
		$this->fid=$id;
	}

	public function setPtId($id){
		$this->pid=$id;
	}

	public function getDos($unformatted=0){
		if(!empty($this->dos)){
			return ($unformatted==1) ? $this->dos : wv_formatDate($this->dos);
		}else{
			$sql = "SELECT c1.create_dt,c1.date_of_service FROM chart_master_table c1 ".
				   //"LEFT JOIN chart_left_cc_history c2 ON c2.form_id=c1.id ".
				   "WHERE c1.id='".$this->fid."' ";
			//$res = $this->db->Execute($sql);
			$res = sqlQuery($sql);
			if($res !== false){
				$dos = $res["date_of_service"];
				if(empty($dos) || $dos == "0000-00-00"){
					$dos = $res["create_dt"];
				}
				$this->dos = $dos;
				return ($unformatted==1) ? $this->dos : wv_formatDate($dos);
			}
		}
	}

	public function getChartEncounterId(){
		if(empty($this->enctrId) && !empty($this->fid)){
			$sql = "SELECT encounterId FROM chart_master_table WHERE id='".$this->fid."' AND patient_id='".$this->pid."' ";
			//$res = $this->db->Execute($sql);
			$res = sqlQuery($sql);
			if($res != false){
				$this->enctrId = $res["encounterId"];
			}
		}
		return $this->enctrId;
	}

	public function setChartEncounterId($id){
		$this->enctrId = $id;
	}

	function getPurgePhrase($tbl,$prfx=""){

		if(!empty($prfx)){ $prfx=$prfx."." ; }
		//Purge Phrase --
		if( $tbl=="chart_eom" || $tbl=="chart_external_exam" || $tbl=="chart_iop" || $tbl=="chart_ood" || $tbl=="chart_dialation" || $tbl=="chart_la" ||
			$tbl=="chart_eom" || $tbl=="chart_optic" || $tbl=="chart_ref_surgery" || $tbl=="chart_rv" || $tbl=="chart_slit_lamp_exam" ||
			$tbl=="chart_lids" || $tbl=="chart_lesion" || $tbl=="chart_lid_pos" || $tbl=="chart_lac_sys" || $tbl=="chart_drawings" ||
			$tbl=="chart_vitreous" || $tbl=="chart_macula" || $tbl=="chart_periphery" || $tbl=="chart_blood_vessels" || $tbl=="chart_retinal_exam" ||
			$tbl=="chart_conjunctiva" || $tbl=="chart_cornea" || $tbl=="chart_ant_chamber" || $tbl=="chart_iris" || $tbl=="chart_lens" || $tbl=="chart_pupil"
			){
			$purgePhase = " AND ".$prfx."purged = '0'  ";
		}else{
			$purgePhase = "";
		}
		//Purge Phrase --
		return $purgePhase;

	}

	function getLastRecord($tblNm,$tblFK,$LF="0",$sel=" * ",$dt="", $exm=""){
		global $cryfwd_form_id;

		$use_indx = "";
		$phrs_exm="";
		if(!empty($exm)){ $phrs_exm=" AND c2.exam_name='".$exm."' "; }

		if($LF == "1") {$LF = "AND c1.finalize = '1' ";}else{$LF="";}

		if(!empty($dt)&&$dt!="0000-00-00"){
			$tmp="";
			if(!empty($this->fid)){
				//$dt=" AND (IFNULL(c3.date_of_service,DATE_FORMAT(c1.create_dt,'%Y-%m-%d')) <=  '".$dt."' AND c1.id < '".$this->fid."' ) ";
				$dt=" AND (c1.date_of_service <=  '".$dt."' AND c1.id < '".$this->fid."' ) ";
				$use_indx = " USE INDEX (patient_id) ";
			}else{
				//$dt = " AND (IFNULL(c3.date_of_service,DATE_FORMAT(c1.create_dt,'%Y-%m-%d')) <  '".$dt."' ) ";
				$dt = " AND (c1.date_of_service <  '".$dt."' ) ";
			}

		}else{
			$dt ="";
		}

		//DOS BASED carry forward
		if(!empty($cryfwd_form_id) && $cryfwd_form_id!=$this->fid){
			$dt = " AND (c1.id =  '".$cryfwd_form_id."' ) ";
		}

		//Purge Phrase --
		$purgePhase = $this->getPurgePhrase($tblNm,"c2");
		//Purge Phrase --

		$sql =	"SELECT ".$sel." , c1.date_of_service AS chk_dos, c1.id AS chk_formid FROM chart_master_table c1 ".$use_indx.
				"INNER JOIN ".$tblNm." c2 ON c2.".$tblFK." = c1.id ".$purgePhase.
				//"LEFT JOIN chart_left_cc_history c3 ON c3.form_id = c1.id ".
				"WHERE c1.patient_id='".$this->pid."' AND c1.delete_status='0' AND c1.purge_status='0' ".
				$LF.
				$dt.
				$phrs_exm.
				//"ORDER BY IFNULL(c3.date_of_service,DATE_FORMAT(c1.create_dt,'%Y-%m-%d')) DESC, c1.id DESC ".
				"ORDER BY c1.date_of_service DESC, c1.id DESC ".
				"LIMIT 0,1 ";

		$res = sqlQuery($sql);//$this->db->Execute($sql) or die("Error".$sql);

		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"]) && in_array($tblNm, $this->ar_archive_tbls)){
			if($res != false){
				$chk_dt1 = $res["chk_dos"];
				$chk_form_id1 = $res["chk_formid"];
			}

			//Check in archive table
			$sql = str_replace($tblNm,$tblNm."_archive",$sql);
			$res1 = sqlQuery($sql);

			if($res1 != false ){
				if($res != false){
					$chk_dt2 = $res1["chk_dos"];
					$chk_form_id2 = $res1["chk_formid"];

					if($chk_dt1>$chk_dt2 || ($chk_dt2==$chk_dt1 && $chk_form_id1>$chk_form_id2)){
						return $res;
					}
				}
				return $res1;
			}
		}
		//-------- End ----------------------------------------------------------------------------------

		return $res;
	}

	function isRecordExists($tbl,$fFid="form_id", $fPid="patient_id", $exm=""){

		$phrs_exm="";
		if(!empty($exm)){ $phrs_exm=" AND exam_name='".$exm."' "; }

		//Purge Phrase --
		$purgePhase = $this->getPurgePhrase($tbl);
		//Purge Phrase --

		$sql =	"SELECT count(*) AS num FROM ".$tbl." ".
				"WHERE ".$fFid."='".$this->fid."' AND ".$fPid."='".$this->pid."' ".$purgePhase.$phrs_exm;
		//$res = $this->db->Execute($sql);
		$res = sqlQuery($sql);
		if($res!=false && $res["num"]>0){
			return true;
		}

		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"]) && in_array($tblNm, $this->ar_archive_tbls)){
			if($res == false){
				$sql = str_replace($tbl,$tbl."_archive",$sql);
				$res = sqlQuery($sql);
				if($res!=false && $res["num"]>0){
					return true;
				}
			}
		}
		//-------- End ----------------------------------------------------------------------------------

		return false;
	}

	function getRecord($tbl,$sel=" * ",$fFid="form_id", $fPid="patient_id", $exm=""){

		$phrs_exm="";
		if(!empty($exm)){ $phrs_exm=" AND exam_name='".$exm."' "; }

		//Purge Phrase --
		$purgePhase = $this->getPurgePhrase($tbl);
		//Purge Phrase --

		$sql = "SELECT ".$sel." FROM ".$tbl." ".
				"WHERE ".$fFid."='".$this->fid."' AND ".$fPid."='".$this->pid."' ".$purgePhase.$phrs_exm;
		$res =  sqlQuery($sql);

		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"]) && in_array($tblNm, $this->ar_archive_tbls)){
			if($res == false){
				$sql = str_replace($tbl,$tbl."_archive",$sql);
				$res = sqlQuery($sql);
			}
		}
		//-------- End ----------------------------------------------------------------------------------

		return $res;
	}

	//GET Carry Forward OR Reset Sql
	public function getCarryForSql($tblNm,$insertId,$IdLF="",$ignoreFields=""){
		$ret = "";
		if(empty($insertId)) return $sql;

		$pkNm = "";
		$sql= "SHOW COLUMNS FROM ".$tblNm."";
		$res = sqlStatement($sql);//$this->db->Execute($sql);
		if($res != false){

			$arrIgnoreFields=array();
			if(!empty($ignoreFields)){
				$ignoreFields=str_replace(array(" ","\r","\n"),"",$ignoreFields);//remove spaces
				$arrIgnoreFields=explode(",", $ignoreFields);
				$arrIgnoreFields=array_map('trim',$arrIgnoreFields);
			}

			for($i=1;$row=sqlFetchArray($res);$i++){
				$field = $row["Field"];
				$key = $row["Key"];

				if(!empty($ignoreFields) && $field!="id" && in_array($field,$arrIgnoreFields)){
					//ignore fields
				}else{
					if($key=="PRI"){
						$pkNm=$field;
					}else if(!empty($IdLF)){
						$ret .= "c1.".$field."=c2.".$field.", ";
					}else{
						$ret .= "c1.".$field."='', ";
					}
				}
			}

			$ret = wv_strReplace($ret,"LASTCOMMA");//remove last comma
			if(!empty($IdLF)){
				$ret = "UPDATE ".$tblNm." c1, ".$tblNm." c2 SET ".$ret." ";
				$ret .=	"WHERE c1.".$pkNm."='".$insertId."' ".
						"AND c2.".$pkNm."='".$IdLF."' ";
			}else{
				$ret = "UPDATE ".$tblNm." c1 SET ".$ret." ";
				$ret .=	"WHERE c1.".$pkNm."='".$insertId."' ";
			}
		}

		return $ret;
	}

	//Carry Forward
	public function carryForwardExe($tblNm,$insertId,$IdLF,$ignoreFields="",$exam="",$tblPK=""){
		$sql = $this->getCarryForSql($tblNm,$insertId,$IdLF,$ignoreFields);
		if(!empty($sql)){
			//$res=$this->db->Execute($sql);
			$res = sqlQuery($sql);

			//iDOC Draw
			if(!empty($exam)){
				$arrIN["pid"]=$this->pid;
				$arrIN["formId"]=$this->fid;
				$arrIN["examId"]=$insertId;
				$arrIN["exam"]=$exam;
				$arrIN["examIdLF"]=$IdLF;
				$arrIN["examMasterTable"]=$tblNm;
				$arrIN["examMasterTablePriColumn"]=$tblPK;
				$this->oPIDocDraw($arrIN,"CarryFor");
			}
			//
		}
	}

	public function oPIDocDraw($arrIN,$op){
		$OBJDrawingData = new CLSDrawingData();
		if($op=="CarryFor"){
			$OBJDrawingData->carryForward($arrIN);
		}else if($op=="Reset"){
			$OBJDrawingData->resetVals($arrIN);
		}
	}

	//Reset
	public function resetValsExe($tblNm,$insertId,$ignoreFields="",$exam="",$tblPK=""){
		$sql = $this->getCarryForSql($tblNm,$insertId,"",$ignoreFields);
		if(!empty($sql)){
			//$res=$this->db->Execute($sql);
			$res = sqlQuery($sql);

			//iDOC Draw
			if(!empty($exam)){
				$arrIN["pid"]=$this->pid;
				$arrIN["formId"]=$this->fid;
				$arrIN["examId"]=$insertId;
				$arrIN["exam"]=$exam;
				$arrIN["examMasterTable"]=$tblNm;
				$arrIN["examMasterTablePriColumn"]=$tblPK;
				$this->oPIDocDraw($arrIN,"Reset");
			}
		}
	}

	//SetWnl
	function setExmWnlPos($sumOd,$sumOs,$wnl,$wnlOd,$wnlOs,$pos,$wnlMain="",$posSe=""){
		if(!empty($sumOd)){
			$wnlMain = $wnlOd =$wnl= "0";
			$posSe=$pos = "1";
		}
		if(!empty($sumOs)){
			$wnlMain = $wnlOs =$wnl= "0";
			$posSe=$pos = "1";
		}
		return array($wnl,$wnlOd,$wnlOs,$pos,$wnlMain,$posSe);
	}

	//Status Elem
	function setStatusElem($exmStr,$status,$sumOd,$sumOs,$siteExm){
		if(!empty($status)){
			$tmp = array("elem_chng_div".$exmStr."_Od=0,","elem_chng_div".$exmStr."_Os=0,");
			$status = str_replace($tmp,"",$status);
		}
		if(!empty($sumOd)) {
			if(strpos($siteExm, "OU")!==false || strpos($siteExm, "OD")!==false){
				if(strpos($status,"elem_chng_div".$exmStr."_Od=1,") === false){
					$status .= "elem_chng_div".$exmStr."_Od=1,";
				}
			}
		}
		if(!empty($sumOs)) {
			if(strpos($siteExm, "OU")!==false || strpos($siteExm, "OS")!==false){
				if(strpos($status,"elem_chng_div".$exmStr."_Os=1,") === false){
					$status .= "elem_chng_div".$exmStr."_Os=1,";
				}
			}
		}

		return $status;
	}

	function setStatus($stts,$tbl,$fFid="form_id", $fPid="patient_id"){
		if(!empty($this->fid)&&!empty($this->pid)){

			//Purge Phrase --
			$purgePhase = $this->getPurgePhrase($tbl);
			//Purge Phrase --

			$sql="UPDATE ".$tbl." SET statusElem='".$stts."'
				  WHERE ".$fFid."='".$this->fid."' AND ".$fPid."='".$this->pid."' ".$purgePhase."";
			//$res=$this->db->Execute($sql);
			$res = sqlQuery($sql);
		}
	}

	//Xml
	function processxmlbyvalues($dom, $arrval=array()){

		$retArr=array();
		$flgSetVal=0;
		if(count($arrval)>0){ $flgSetVal=1; }

		$root1 = $dom->documentElement->childNodes;
		if($root1->length<=0){exit("No data");}
		$root1 =$root1->item(0);


		foreach($root1->childNodes as $rootChildren0){

			if($rootChildren0->nodeType!=1){continue;}

			$attr_elem_name0="";
			//$attr_examname0="";
			if($rootChildren0->hasAttributes()){
				$attr_elem_name0 = $rootChildren0->getAttribute("elem_name");
				//$attr_examname0 = $rootChildren0->getAttribute("examname");
			}

			if(!empty($attr_elem_name0)){

				//echo "<br/>0-".$attr_elem_name0." - ".$rootChildren0->nodeValue;
				if(!empty($flgSetVal)){
					$rootChildren0->nodeValue = "".$arrval[$attr_elem_name0];
				}else{
					$retArr[$attr_elem_name0] = "".$rootChildren0->nodeValue;
				}


			}else{

				foreach($rootChildren0->childNodes as $rootChildren1){

					if($rootChildren1->nodeType!=1){continue;}

					$attr_examname="";

					if($rootChildren1->hasAttributes()){
						$attr_examname = $rootChildren1->getAttribute("examname");
					}

					//echo "<br/>1-".$attr_examname;

					if(empty($attr_examname)){
						$attr_elem_name = $rootChildren1->getAttribute("elem_name");
						//echo "<br/>1-".$attr_elem_name." - ".$rootChildren1->nodeValue;
						if(!empty($flgSetVal)){
							$rootChildren1->nodeValue = "".$arrval[$attr_elem_name];
						}else{
							$retArr[$attr_elem_name] = "".$rootChildren1->nodeValue;
						}
					}else{

						//--
						foreach($rootChildren1->childNodes as $rootChildren2){

							if($rootChildren2->nodeType!=1){continue;}

							$attr_examname2="";
							if($rootChildren2->hasAttributes()){
								$attr_examname2 = $rootChildren2->getAttribute("examname");
							}

							if(empty($attr_examname2)){
								$attr_elem_name2 = $rootChildren2->getAttribute("elem_name");
								//echo "<br/>2-".$attr_elem_name2." - ".$rootChildren2->nodeValue;
								if(!empty($flgSetVal)){
									$rootChildren2->nodeValue = "".$arrval[$attr_elem_name2];
								}else{
									$retArr[$attr_elem_name2] = "".$rootChildren2->nodeValue;
								}

							}else{
								//--
								foreach($rootChildren2->childNodes as $rootChildren3){
									if($rootChildren3->nodeType!=1){continue;}

									$attr_examname3="";
									if($rootChildren3->hasAttributes()){
										$attr_examname3 = $rootChildren3->getAttribute("examname");
									}

									if(empty($attr_examname3)){
										$attr_elem_name3 = $rootChildren3->getAttribute("elem_name");
										//echo "<br/>3-".$attr_elem_name3." - ".$rootChildren3->nodeValue;
										if(!empty($flgSetVal)){
											$rootChildren3->nodeValue = "".$arrval[$attr_elem_name3];
										}else{
											$retArr[$attr_elem_name3] = "".$rootChildren3->nodeValue;
										}
									}

								}
								//--
							}
						}
						//--

					}

				}

			}
		}

		if($flgSetVal==1){
			return $dom;
		}else{
			return $retArr;
		}

	}

	function getDom($str,$pth){
		$dom = new DomDocument;
		$dom->preserveWhiteSpace = false;

		//Load
		if(!empty($pth)){
			$dom->load($pth);
		}

		if(!empty($str)){ //process
			$dom_old = new DomDocument;
			$dom_old->preserveWhiteSpace = false;
			$dom_old->loadXML($str);
			//
			if(!empty($pth)){
				$arrVal = $this->processxmlbyvalues($dom_old); //get values
				$dom = $this->processxmlbyvalues($dom,$arrVal); //set values
			}else{
				$dom = $dom_old;
			}
		}

		return $dom;
	}

	function processNode($field, $level,$site,$eye){

		//$chkTag = strtolower($field->tagName);
		if($field->hasAttributes()){
			$attr_elem_name = $field->getAttribute("elem_name");
		}

		if(!empty($attr_elem_name) && isset($level[$attr_elem_name])){
			$tmp = $level[$attr_elem_name];
			$tmp = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $tmp); //replace <br/> with \n :
			$field->nodeValue = $tmp;
		}else{
			$field->nodeValue = "";
		}

		/*
		switch($chkTag){
			case "negative":
			case "neg":
				$field->nodeValue = (strpos($level,"-ve")!==false) ? "-ve" : "";
			break;

			case "t":
			///case "trace":
				$field->nodeValue = (strpos($level,"T")!==false) ? "T" : "";
			break;

			case "pos1":
			case "pos_1":
				$field->nodeValue = (strpos($level,"1")!==false) ? "+1" : "";
			break;

			case "pos2":
			case "pos_2":
				$field->nodeValue = (strpos($level,"2")!==false) ? "+2" : "";
			break;

			case "pos3":
			case "pos_3":
				$field->nodeValue = (strpos($level,"3")!==false) ? "+3" : "";
			break;

			case "pos4":
			case "pos_4":
				$field->nodeValue = (strpos($level,"4")!==false) ? "+4" : "";
			break;

			case "rul":
				if($eye=="OD"){
					$field->nodeValue = (strpos($site,"RUL")!==false) ? "RUL" : "";
				}else if($eye=="OS"){
					$field->nodeValue = (strpos($site,"LUL")!==false) ? "LUL" : "";
				}
			break;

			case "rll":
				if($eye=="OD"){
					$field->nodeValue = (strpos($site,"RLL")!==false) ? "RLL" : "";
				}else if($eye=="OS"){
					$field->nodeValue = (strpos($site,"LLL")!==false) ? "LLL" : "";
				}
			break;

			case "lul":
				$field->nodeValue = (strpos($site,"LUL")!==false) ? "LUL" : "";
			break;

			case "lll":
				$field->nodeValue = (strpos($site,"LLL")!==false) ? "LLL" : "";
			break;
		}
		*/

		return $field;
	}

	//--

	function processDomExe($field, $attr_examName, $main_exam, $level,$site,$eye,$exmChk,$tabname=""){
		$arr_elemNames=array();

		foreach($field->childNodes as $field_2){ //Loop 2
			$attr_examName_2="";
			$attr_elemName_2 = "";
			$attr_commonexam_2="";
			if($field_2->hasAttributes()){
				$attr_examName_2 = "".$field_2->getAttribute("examname");
			}
			if(empty($attr_examName_2)){
				if($exmChk==strtoupper($attr_examName)||
					$exmChk==strtoupper($main_exam."/".$attr_examName) ||
					$exmChk==strtoupper($tabname."/".$attr_examName) ||
					$exmChk==strtoupper($main_exam."/".$tabname."/".$attr_examName) ||
					stripos($main_exam."/".$attr_examName, $exmChk."/") !== false){
					$field_2 = $this->ProcessNode($field_2, $level,$site,$eye);

					//Get elem names for smart charting
					if(!empty($field_2->nodeValue)){
						$arr_elemNames[] = $field_2->getAttribute("elem_name");
					}
				}
			}else{



				$arr_elemNames_tmp = $this->processDomExe($field_2, $attr_examName."/".$attr_examName_2, $main_exam, $level,$site,$eye,$exmChk,$tabname);
				$arr_elemNames = array_merge($arr_elemNames,$arr_elemNames_tmp);



				/*
				echo "<br/>Chk3 : ".$attr_examName."/".$attr_examName_2." - ".$main_exam."/".$attr_examName."/".$attr_examName_2." - ".$attr_examName.
								" - ".$main_exam."/".$tabname."/".$attr_examName."/".$attr_examName_2;

				////Loop 3 and last
				if($exmChk==strtoupper($attr_examName."/".$attr_examName_2)||
					$exmChk==strtoupper($main_exam."/".$attr_examName."/".$attr_examName_2) ||
					$exmChk==strtoupper($attr_examName) ||
					$exmChk==strtoupper($main_exam."/".$tabname."/".$attr_examName."/".$attr_examName_2) ){
					foreach($field_2->childNodes as $field_3){ //Loop 3
						$field_3 = $this->ProcessNode($field_3, $level,$site,$eye);

						//Get elem names for smart charting
						if(!empty($field_3->nodeValue)){
							$arr_elemNames[] = $field_3->getAttribute("elem_name");
						}
					}
				}
				*/

			}
		}

		return $arr_elemNames;

	}

	//--

	//Note:  Exam Name to add in Xml
	//Pending Inner Level Check and Check for Exam/SubNm
	function processDom($dom,$exm,$arr,$eye){
		$arr_elemNames=array();
		$exmChk = strtoupper($exm);
		$site=$arr["site"];
		$level= (!is_array($arr["level"])) ? unserialize($arr["level"])  : $arr["level"] ;

		if($level["elem_temp_check_exam_name"] == "SLE" && $level["elem_sc_symptom"] == "IOL" && $exmChk == "IOL"){ $exmChk="IOL/IOL";  }

		//Process --
		$root = $dom->documentElement;
		$main_exam = ($root->hasAttributes()) ? $root->getAttribute("main_exam") : $root->tagName;
		$root2 = $root->firstChild;


		foreach($root2->childNodes as $field){ //Loop 1
			$attr_examName="";
			$attr_tabName="";
			if($field->hasAttributes()){
				$attr_examName = $field->getAttribute("examname");
				$attr_tabName = "".$field->getAttribute("tabname");
			}

			//if tabname
			if(!empty($attr_tabName)){

				foreach($field->childNodes as $field_in_tab){ //Loop inside tabname
					$attr_examName_FT="";
					if($field_in_tab->hasAttributes()){
						$attr_examName_FT = $field_in_tab->getAttribute("examname");
					}

					if(!empty($attr_examName_FT)){
						$arr_elemNames_tmp = $this->processDomExe($field_in_tab, $attr_examName_FT, $main_exam, $level,$site,$eye,$exmChk,$attr_tabName);
						$arr_elemNames = array_merge($arr_elemNames,$arr_elemNames_tmp);
					}
				}

			}else{

				if(!empty($attr_examName)){
					$arr_elemNames_tmp = $this->processDomExe($field, $attr_examName, $main_exam, $level,$site,$eye,$exmChk);
					$arr_elemNames = array_merge($arr_elemNames,$arr_elemNames_tmp);
				}else{
					if(stripos($exmChk,"Comments:")!==false){
						$field_2 = $this->ProcessNode($field, $level,$site,$eye);
						//Get elem names for smart charting
						if(!empty($field_2->nodeValue)){
							$arr_elemNames[] = $field_2->getAttribute("elem_name");
						}
					}
				}

			}//else tabname
		}
		//Process --

		return array($dom, $arr_elemNames);
	}

	function getDomSumExe($field, $attr_examName, $attr_examName_summary, $summ,$arrExmDone=array(), $main_exam="" ){
		//$arrExmDone=array();
		$arr_cm_sub_ex=array("Stromal");

		$summ_tmp = "";
		$summ_tmp_2 = "";
		$summ_modi ="";

		$attr_commonexam="";
		if($field->hasAttributes()){
			$attr_commonexam = $field->getAttribute("commonexam");
		}


		foreach($field->childNodes as $field_2){ //Loop2
			$attr_examName_2=$attr_examName_summary_2="";
			if($field_2->hasAttributes()){
				$attr_examName_2 = $field_2->getAttribute("examname");
				$attr_examName_summary_2 = $field_2->getAttribute("examname_summary");
				if(empty($attr_examName_summary_2)) $attr_examName_summary_2 = $attr_examName_2;
				$attr_modifiers = $field_2->getAttribute("modifiers");
			}


			if($attr_modifiers=="1"){

				$tmp_location = $tmp_hori = $tmp_verti = $tmp_Other = "";

				foreach($field_2->childNodes as $field_modi){ //loop modi

					if($field_modi->nodeType!=XML_COMMENT_NODE && trim($field_modi->nodeValue)!=""){
						$tmp = "".$field_modi->nodeValue;

						if($field_modi->tagName=="location"){
							$tmp_location="with ".$tmp." ";
						}else if($field_modi->tagName=="horizontal_size"){
							$tmp_hori=$tmp." mm horizontally ";
						}else if($field_modi->tagName=="vertical_size"){
							$tmp_verti="X ".$tmp." mm vertically ";
						}else{
							$tmp_Other.="".$tmp.", ";
						}
					}
				}

				//modifiers
				$summ_modi = $tmp_hori.$tmp_verti.$tmp_location.$tmp_Other;
				if(!empty($summ_modi)){
					$summ_modi=wv_strReplace($summ_modi,"LASTCOMMA");
					$summ_modi=" ".$summ_modi;
				}

			}else if(empty($attr_examName_2)){


				//if(!empty($field_2->nodeValue)) $summ_tmp = $field_2->nodeValue." ".$summ_tmp;
				if($field_2->nodeType!=XML_COMMENT_NODE && trim($field_2->nodeValue)!=""){
					if($field_2->hasAttributes()){
						//suffix
						$suffix =$field_2->getAttribute("suffix");
					}


					$tmp = "".$field_2->nodeValue;

					if(!empty($suffix)){  $tmp=$tmp." ".$suffix;  }
					$summ_tmp = $tmp." ".$summ_tmp;
				}

			}else{
				//Loop 3-
				$summ_tmp_3 = "";
				list($summ_tmp_3, $arrExmDone) = $this->getDomSumExe($field_2, $attr_examName_2, $attr_examName_summary_2, "",$arrExmDone, $main_exam );

				if(trim($summ_tmp_3)!=""){
					/*
					if(in_array($attr_examName_summary_2, $arr_cm_sub_ex)){
						$summ_tmp_3 = "".trim($summ_tmp_3)." ".$attr_examName."/".$attr_examName_2.";";
					}else{
						$summ_tmp_3 = "".trim($summ_tmp_3)." ".$attr_examName_summary_2.";";
					}*/
					$summ_tmp_2 .= " ".$summ_tmp_3;
					if(strpos($summ_tmp_3,"-ve")===false && strpos($summ_tmp_3,"Absent")===false){
						$tmpEm=$attr_examName."/".$attr_examName_2;
						$arrExmDone[$tmpEm]=$attr_examName;
					}
				}
				//*/

			}
		}

		if(!empty($summ_tmp)||!empty($summ_modi)){
			$summ_tmp = "".trim($summ_tmp)." ".$attr_examName_summary."".$summ_modi.";";
			$summ.=" ".$summ_tmp;
			if(strpos($summ_tmp,"-ve")===false && strpos($summ_tmp,"Absent")===false){
				$levelEx1 = (!empty($attr_commonexam)) ? $main_exam."/".$attr_examName : $attr_examName;
				$arrExmDone[$levelEx1]= $attr_examName;
			}
		}
		if(!empty($summ_tmp_2)){
			$summ = $summ." ".$summ_tmp_2;
		}

		return array($summ, $arrExmDone);

	}

	//for advance options --
	function getDomSumExe_v2($field, $attr_examName, $attr_examName_summary, $summ,$arrExmDone=array(), $main_exam="" ){
		//$arrExmDone=array();

		$summ_tmp = "";
		//$summ_tmp_2 = "";
		$summ_modi ="";

		$attr_commonexam="";
		if($field->hasAttributes()){
			$attr_commonexam = $field->getAttribute("commonexam");
		}

		foreach($field->childNodes as $field_2){ //Loop2
			$attr_examName_2=$attr_examName_summary_2=$suffix="";
			if($field_2->hasAttributes()){
				$attr_examName_2 = $field_2->getAttribute("examname");
				$attr_examName_summary_2 = $field_2->getAttribute("examname_summary");
				if(empty($attr_examName_summary_2)) $attr_examName_summary_2 = $attr_examName_2;
				$attr_modifiers = $field_2->getAttribute("modifiers");
			}

			if($attr_modifiers=="1"){

				$tmp_location = $tmp_hori = $tmp_verti = $tmp_Other = "";

				foreach($field_2->childNodes as $field_modi){ //loop modi

					if($field_modi->nodeType!=XML_COMMENT_NODE && trim($field_modi->nodeValue)!=""){
						$tmp = "".$field_modi->nodeValue;

						if($field_modi->tagName=="location"){
							$tmp_location="with ".$tmp." ";
						}else if($field_modi->tagName=="horizontal_size"){
							$tmp_hori=$tmp." mm horizontally ";
						}else if($field_modi->tagName=="vertical_size"){
							$tmp_verti="X ".$tmp." mm vertically ";
						}else{
							$tmp_Other.="".$tmp.", ";
						}
					}
				}

				//modifiers
				$summ_modi = $tmp_hori.$tmp_verti.$tmp_location.$tmp_Other;
				if(!empty($summ_modi)){
					$summ_modi=wv_strReplace($summ_modi,"LASTCOMMA");
					$summ_modi=" ".$summ_modi;
				}

			}else if(empty($attr_examName_2)){

				//if(!empty($field_2->nodeValue)) $summ_tmp = $field_2->nodeValue." ".$summ_tmp;
				if($field_2->nodeType!=XML_COMMENT_NODE && trim($field_2->nodeValue)!=""){

					if($field_2->hasAttributes()){
						//suffix
						$suffix =$field_2->getAttribute("suffix");
					}

					$tmp = "".$field_2->nodeValue;
					if(!empty($suffix)){  $tmp=$tmp." ".$suffix;  }
					$summ_tmp = $tmp." ".$summ_tmp;
				}

			}else{
				//Loop 3-
				$summ_tmp_3 = "";
				list($summ_tmp_3, $arrExmDone) = $this->getDomSumExe_v2($field_2, $attr_examName_2, $attr_examName_summary_2, "",$arrExmDone, $main_exam );
				/*
				foreach($field_2->childNodes as $field_3){ //Loop3

					//if(!empty($field_3->nodeValue)) $summ_tmp_3 = $field_3->nodeValue." ".$summ_tmp_3;
					if($field_3->nodeType!=XML_COMMENT_NODE && trim($field_3->nodeValue)!=""){

						if($field_3->hasAttributes()){
							//suffix
							$suffix =$field_3->getAttribute("suffix");
						}

						$tmp = "".$field_3->nodeValue;
						if(!empty($suffix)){ $tmp=$tmp." ".$suffix;  }
						$summ_tmp_3 = $tmp." ".$summ_tmp_3;

					}
				}
				*/
				if(trim($summ_tmp_3)!=""){
					//$summ_tmp_3 = "".trim($summ_tmp_3)." ".$attr_examName_summary_2.", ";
					$summ_tmp_2 .= " ".$summ_tmp_3;
					//$summ_tmp .= " ".$summ_tmp_3;
					if(strpos($summ_tmp_3,"-ve")===false && strpos($summ_tmp_3,"Absent")===false){
						$tmpEm=$attr_examName."/".$attr_examName_2;
						$arrExmDone[$tmpEm]=$attr_examName;
					}
				}
			}
		}

		if(!empty($summ_tmp)){

			//--
			//replace last comma
			$summ_tmp=wv_strReplace($summ_tmp,"LASTCOMMA");
			//--

			$summ_tmp = "".trim($summ_tmp)." ".$attr_examName_summary."".$summ_modi.";";
			$summ.=" ".$summ_tmp;
			if(strpos($summ_tmp,"-ve")===false && strpos($summ_tmp,"Absent")===false){
				$levelEx1 = (!empty($attr_commonexam)) ? $main_exam."/".$attr_examName : $attr_examName;
				$arrExmDone[$levelEx1]= $attr_examName;
			}
		}
		//*
		if(!empty($summ_tmp_2)){
			$summ = $summ." ".$summ_tmp_2;
		}
		//*/

		//exit();


		return array($summ, $arrExmDone);

	}

	function getDomSum($dom,$flgRet=""){
		$summ="";
		$arrExmDone=array();

		//Process --
		$root = $dom->documentElement;
		$main_exam = ($root->hasAttributes()) ? $root->getAttribute("main_exam") : $root->tagName;
		$root2 = $root->firstChild;
		foreach($root2->childNodes as $field){ //Loop1
			$attr_examName=$attr_examName_summary="";
			if($field->hasAttributes()){
				$attr_examName = $field->getAttribute("examname");
				$attr_examName_summary = $field->getAttribute("examname_summary");
				if(empty($attr_examName_summary)) $attr_examName_summary = $attr_examName;
				$attr_tabName = $field->getAttribute("tabname");
			}

			if(!empty($attr_tabName)){

				$summ_tab="";

				foreach($field->childNodes as $field_in_tab){ //Loop inside tabname
					$attr_examName_FT="";
					if($field_in_tab->hasAttributes()){
						$attr_examName_FT = $field_in_tab->getAttribute("examname");
						$attr_examName_summary_FT = $field->getAttribute("examname_summary");
						if(empty($attr_examName_summary_FT)) $attr_examName_summary_FT = $attr_examName_FT;
					}

					if(!empty($attr_examName_FT)){
						list($summ_tab, $arrExmDone) = $this->getDomSumExe_v2($field_in_tab, $attr_examName_FT, $attr_examName_summary_FT, $summ_tab, $arrExmDone,$main_exam );
					}
				}

				//--
				if(!empty($summ_tab)){

					$summ.= ($attr_tabName=="advanced_plastics" || $attr_tabName=="Advanced Plastics") ? $summ_tab : " ".$attr_tabName.": ".$summ_tab;

				}
				//--

			}else if(!empty($attr_examName)){
				list($summ, $arrExmDone) = $this->getDomSumExe($field, $attr_examName, $attr_examName_summary, $summ, $arrExmDone , $main_exam);
				//list($summ, $arrExmDone) = $this->getDomSumExe_v2($field, $attr_examName, $attr_examName_summary, $summ, $arrExmDone );
			}else{

				if($field->nodeType!=XML_COMMENT_NODE && trim($field->nodeValue)!=""){


					if($field->tagName=="advanceoptions" || $field->tagName == "pupil_desc"){ $summ.="\nComments: "; }
					$summ.="".$field->nodeValue.";";
				}

			}
		}
		//Process --

		//exit();

		//--
		//replace last comma
		$summ=wv_strReplace($summ,"LASTSEMICOLON");
		//--

		if($flgRet=="1" || 1==1){$summ = nl2br($summ);} //set new line to break


		return ($flgRet=="1") ? array("Summary"=>$summ,"ExmDone"=>$arrExmDone) : $summ ;
	}

	function insertDomComments($descOd, $descOs, $domOd,$domOs){
		$descOd=trim($descOd);
		$descOs=trim($descOs);
		$tmp=array();
		if(!empty($descOd)){
			$descOd = wv_strReplace($descOd,"LASTCOMMA");

			$obj= $domOd->getElementsByTagname("advanceoptions")->item(0);
			$x = trim("".$obj->nodeValue);
			$obj->nodeValue = (!empty($x) && strpos($x, $descOd)===false) ? $x.", ".$descOd : $descOd ;
			$tmp[] = $obj->getAttribute("elem_name");
		}


		if(!empty($descOs)){
			$descOs = wv_strReplace($descOs,"LASTCOMMA");

			$obj= $domOs->getElementsByTagname("advanceoptions")->item(0);
			$x = trim("".$obj->nodeValue);
			$obj->nodeValue = (!empty($x) && strpos($x, $descOs)===false) ? $x.", ".$descOs : $descOs ;
			$tmp[] = $obj->getAttribute("elem_name");
		}

		return array($domOd,$domOs,$tmp);
	}

	function editXml_v2($arr){
		$xOd = $arr["xStr"]; //editxml
		$xFileOd = $arr["xFile"]; //path
		$arrVals = $arr["arrVals"] ; //values
		//$ut_elem = $arr["ut_elem"];

		//Dom
		$domOd=$this->getDom($xOd,$xFileOd);
		//simplexml
		$simplexml = simplexml_import_dom($domOd);

		foreach($arrVals as $key => $val){
			$elem_name = $key;
			$exmVal = $val;

			$myDataObjects = $simplexml->xpath('//*[@elem_name="'.$elem_name.'"]');
			if (count($myDataObjects) >= 1){
				$myDataObjects[0][0]="".$exmVal;//working here
			}

		}

		return $simplexml->saveXml();
	}

	function editXml($arr){
		$xOd = $arr["xOd"];
		$xOs = $arr["xOs"];
		$xFileOd = $arr["xFileOd"];
		$xFileOs = $arr["xFileOs"];
		$arrSc = $arr["arrSc"] ; //values
		$ut_elem = $arr["ut_elem"];

		$arrElemSC = array();

		//Dom
		$domOd=$this->getDom($xOd,$xFileOd);
		$domOs=$this->getDom($xOs,$xFileOs);

		//Loop exams--
		$descOD=$descOS=$desc = "";
		$siteExm = "";
		if(count($arrSc)>0){

			foreach($arrSc as $key=>$val){
				$exam = $key;
				$exmVal = $val;
				$site = $val["site"];
				$level= $val["level"];
				$desc = !empty($val["comments"]) ? $val["comments"]."," : "";
				$siteExm .= !empty($site) ? $site."," : "";

				//if Empty Level AND $desc, OR Site  the Continue
				if((empty($level)&&empty($desc)) || empty($site))continue;

				if(strpos($site,"OU")!==false||strpos($site,"OD")!==false){
					list($domOd,$tmp) = $this->processDom($domOd,$exam,$exmVal,"OD");

					$arrElemSC = array_merge($arrElemSC,$tmp);

					$descOD .= $desc;

				}

				if(strpos($site,"OU")!==false||strpos($site,"OS")!==false){
					list($domOs,$tmp) = $this->processDom($domOs,$exam,$exmVal,"OS");

					$arrElemSC = array_merge($arrElemSC,$tmp);

					$descOS .= $desc;
				}
			}

			//Add Desc in comments in exam : 05-02-2013
			list($domOd,$domOs,$tmp) = $this->insertDomComments($descOD, $descOS, $domOd,$domOs);
			$arrElemSC = array_merge($arrElemSC,$tmp);

			//Remove from  $ut_elem
			if(count($arrElemSC)>0&&!empty($ut_elem)){
				foreach($arrElemSC as $key2 => $val2){
					$tmp = $val2;
					//Remove from ut_elem
					if(!empty($ut_elem) && strpos($ut_elem,$tmp.",")!==false){
						$ut_elem = str_replace($tmp.",","",$ut_elem);
					}
				}
			}
		}
		//Loop exams--

		//GetSums--
		$sumOd = $this->getDomSum($domOd);
		$sumOs = $this->getDomSum($domOs);
		//Get Sums--

		//return--
		$arr["xOd"]=$domOd->saveXml();
		$arr["xOs"]=$domOs->saveXml();
		$arr["siteExm"]=$siteExm;
		$arr["desc"]=""; //wv_strReplace($desc,"LASTCOMMA"); Stopped as values will go inside exam comments
		$arr["sumOd"]=$sumOd;
		$arr["sumOs"]=$sumOs;
		$arr["elemEd"]=$arrElemSC;
		$arr["ut_elem"]=$ut_elem;
		//return--

		return $arr;
	}

	function getExamSummary($xmlStr){
		$dom=$this->getDom($xmlStr,"");
		return $this->getDomSum($dom,"1");
	}

	//Xml

	//AutoFinalize --
	function autoFinalize($proIdDefault=0){

		if(!empty($proIdDefault)){
			$proIdStr = " providerId='".$proIdDefault."', ";

			$sql="UPDATE chart_master_table
					SET
					finalize='1',
					finalizerId='".$proIdDefault."',
					finalizeDate='".wv_dt('now')."',
					".$proIdStr."
					autoFinalize='1'
					WHERE id='".$this->fid."' AND patient_id='".$this->pid."'
					";
			//$res=$this->db->Execute($sql);
			$res = sqlQuery($sql);

			//Medical hx---

			$this->archiveChartGenHx();

			if(constant("connect_optical")==1){
				$oMedHx =  new MedHx($this->pid);
				$oMedHx->setFormId($this->fid);
				$oMedHx->optical_order_action($this->fid);
			}
			//Medical hx---

			//Save Log
			$oClog = new ChartLog($this->pid, $this->fid);
			$oClog->save_log(1,'Autofinalize');

			//sign
			$oSign = new Signature($this->fid, $this->pid);
			$oSign->setUsrSign($proIdDefault);
		}
	}

	function isNoChanged(){
		$res= $this->getRecord("examined_no_change,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["examined_no_change"]) ){
				return true;
			}
		}
		return false;
	}



	//check if chart is finalized and finalizer is logged in user
	function checkFinalizer(){
		$ret = 0;
		$sql = "SELECT id FROM chart_master_table
				WHERE patient_id ='".$this->pid."'
				AND id='".$this->fid."'
				AND (finalize = '0' OR (finalize='1' AND (finalizerId='".$this->uid."' OR autoFinalize='1')))
				AND delete_status = '0' ";
		//$res=$this->db->Execute($sql);
		$res = sqlQuery($sql);
		if($res!=false && !empty($res["id"])){
			$ret = 1;
		}
		return $ret;
	}

	function chkFinalizeStatusB4Save($post_finalize, $post_finalize_dt){
		$ret = 0;
		if(empty($post_finalize)||empty($post_finalize_dt)){
			$sql = "SELECT id FROM chart_master_table
				WHERE patient_id ='".$this->pid."'
				AND id='".$this->fid."'
				AND finalize = '0'
				AND delete_status = '0' ";
			//$res=$this->db->Execute($sql);
			$res = sqlQuery($sql);
			if($res==false || empty($res["id"])){ $ret = 1; }
		}
		return $ret;
	}

	function archiveChartGenHx($arrGridOcularMeds=''){

		require_once(dirname(__FILE__)."/ChartRecArc.php");
		$objChartRec = new ChartRecArc($this->pid, $this->fid, $_SESSION["authId"]);
		if(!empty($_SESSION["finalize_id"]) || $objChartRec->getArcRecId(1)){
			//$arrList[4]=$arrGridOcularMeds;
			return; // when chart is finalized or if chart is open for editing:: do not update chart_genhealth_table
		}

		//Ocular --
		$ocular = "";
		$sql="SELECT * FROM ocular WHERE patient_id='".$this->pid."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$ocular = sqlEscStr(serialize($row));
		}

		//lists

		$lists="";
		$arrList = array();
		$sql="SELECT * FROM lists WHERE pid='".$this->pid."' AND del_allergy_status ='0' ORDER BY begdate DESC ";
		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$type=$row["type"];
			//$arrList[$type][]=$row;
			if($type != 4 ){
				$arrList[$type][]=$row;
			}else if($type == 4 ){
				if(empty($_SESSION["finalize_id"]) && !$objChartRec->getArcRecId(1)){
					$arrList[$type][]=$row;
				}
			}
		}

		$lists = sqlEscStr(serialize($arrList));

		//general_medicine
		$general_medicine = "";
		$sql="SELECT * FROM general_medicine WHERE patient_id='".$this->pid."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$general_medicine = sqlEscStr(serialize($row));
		}

		//patient_blood_sugar
		$patient_blood_sugar = "";
		$arrPBS=array();
		$sql="SELECT * FROM patient_blood_sugar WHERE patient_id='".$this->pid."' ORDER BY creation_date DESC,time_of_day_sequence ";
		$rez=sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$arrPBS[]=$row;
		}
		$patient_blood_sugar = sqlEscStr(serialize($arrPBS));

		//patient_cholesterol
		$patient_cholesterol = "";
		$arrPtC=array();
		$sql="SELECT * FROM patient_cholesterol WHERE patient_id='".$this->pid."' ORDER BY creation_date DESC ";
		$rez=sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$arrPtC[]=$row;
		}
		$patient_cholesterol = sqlEscStr(serialize($arrPtC));

		//social_history
		$social_history = "";
		$sql="SELECT * FROM social_history WHERE patient_id='".$this->pid."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$social_history = sqlEscStr(serialize($row));
		}

		//immunizations
		$arrImm=array();
		$immunizations = "";
		$sql="SELECT * FROM immunizations WHERE patient_id='".$this->pid."' ";
		$rez=sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$arrImm[]=$row;
		}
		$immunizations = sqlEscStr(serialize($arrImm));


		//pt_problem_list
		$arrPPL=array();
		$pt_problem_list = "";
		$sql="SELECT * FROM pt_problem_list WHERE pt_id='".$this->pid."' order By onset_date desc ";
		$rez=sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$arrPPL[]=$row;
		}
		$pt_problem_list = sqlEscStr(serialize($arrPPL));

		//commonNoMedicalHistory
		$arrCNMH=array();
		$commonNoMedicalHistory = "";
		$sql="SELECT * FROM commonNoMedicalHistory WHERE patient_id='".$this->pid."' ";
		$rez=sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$tmp = $row["module_name"];
			$arrCNMH[$tmp]=$row;
		}
		$commonNoMedicalHistory = sqlEscStr(serialize($arrCNMH));

		//patient_data
		$patient_data = "";
		$sql="SELECT ado_option FROM patient_data WHERE id='".$this->pid."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$patient_data = sqlEscStr(serialize($row));
		}

		//Check
		$sql = "SELECT * FROM chart_genhealth_archive WHERE patient_id='".$this->pid."' AND form_id='".$this->fid."' ";
		$row= sqlQuery($sql);
		if($row!=false){
			//Update
			$sql="UPDATE chart_genhealth_archive SET
						ocular='".$ocular."', lists='".$lists."', general_medicine='".$general_medicine."',
						patient_blood_sugar='".$patient_blood_sugar."', patient_cholesterol='".$patient_cholesterol."',
						social_history='".$social_history."', immunizations='".$immunizations."',
						pt_problem_list='".$pt_problem_list."', patient_data='".$patient_data."'
				WHERE patient_id='".$this->pid."' AND form_id='".$this->fid."'
				";
			$row=sqlQuery($sql);
		}else{
			//Insert
			$sql= "INSERT INTO chart_genhealth_archive ( id, patient_id, form_id,
						ocular, lists, general_medicine, patient_blood_sugar, patient_cholesterol,
						social_history, immunizations, pt_problem_list, patient_data  )
						VALUES (NULL, '".$this->pid."', '".$this->fid."',
						'".$ocular."', '".$lists."', '".$general_medicine."', '".$patient_blood_sugar."', '".$patient_cholesterol."',
						'".$social_history."', '".$immunizations."', '".$pt_problem_list."', '".$patient_data."' ) ";
			$row=sqlQuery($sql);
		}
	}
	//--

	//get server id--
	function getServerId(){
		$ret="";
		/*
		if(constant("REMOTE_SYNC") == 1){
			include_once(dirname(__FILE__)."/../../../remote_sync/classes/ChartNoteRemoteHandler.php");

			$nm = (!empty($GLOBALS["remote"]["LOCAL_SERVER"])) ? $GLOBALS["remote"]["LOCAL_SERVER"] : "";
			$oChartNoteRemoteHandler = new ChartNoteRemoteHandler();
			$tmp = $oChartNoteRemoteHandler->getServerInfo($nm);
			if($tmp != false){
				$ret = $tmp["id"];
			}
		}
		*/
		return $ret;
	}
	//
	function addUserSignOnChart(){

		$flg_done=0;
		$tmpDirPth_up = dirname(__FILE__)."/../../main/uploaddir";

		//include_once(dirname(__FILE__)."/SaveFile.php");
		$oSaveFile = new SaveFile($this->pid);
		$tmpDirPth_sign = $oSaveFile->ptDir("sign");

		$tmpDirPth_pt = "/PatientId_".$this->pid;
		$form_sign_path = $tmpDirPth_pt.$tmpDirPth_sign;
		$tmp_sign_path=realpath($tmpDirPth_up.$form_sign_path);
		$signType=1;

		//Make Image
		$img_nm = "/sig".$signType."_".time()."_".$this->fid.".jpg";
		$tmp_sign_path2=$tmp_sign_path.$img_nm;

		//global $gdFilename;
		$imgName= dirname(__FILE__)."/../../../images/white2.jpg";

		$sql = "SELECT sign, sign_path FROM users WHERE id = '".$this->uid."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$strpixls = trim($row["sign"]);
			$str_sign_path = trim($row["sign_path"]);

			$chk1=$chk2=0;
			if((!empty($strpixls) && $strpixls!="0-0-0:;")){  $chk1=1; }
			if((!empty($str_sign_path) && strpos($str_sign_path,"UserId") !== false && file_exists($GLOBALS['incdir']."/main/uploaddir".$str_sign_path) )){  $chk2=1; }

			if($chk1==1||$chk2==1){

				//-------------
				//Make Image
				$img_nm = "/sig".$num."_".time()."_".$this->fid.".jpg";
				$tmp_sign_path1=$tmp_sign_path.$img_nm;
				//global $gdFilename;
				if($chk2==1){
					if(copy($GLOBALS['incdir']."/main/uploaddir".$str_sign_path, $tmp_sign_path1)){ }else{ $form_sign_path=$img_nm=""; }
				}else{
					include_once($GLOBALS['incdir']."/main/imgGdFun.php");
					drawOnImage_new($strpixls,"",$tmp_sign_path1);
				}
				//-------------

				//update records
				if(!empty($tmp_sign_path1)){
					//and save sign Path WHEN chart is finalized
					if(!empty($this->uid)){

						$sql ="SELECT count(*) as num FROM chart_signatures WHERE pro_id='".$this->uid."' AND form_id='".$this->fid."' ";
						$row = sqlQuery($sql);
						if($row!=false && $row["num"]>0){
							$sql="UPDATE chart_signatures SET ".
								" sign_path='".sqlEscStr($form_sign_path.$img_nm)."',
								  sign_coords ='".sqlEscStr($strpixls)."',
								  sign_coords_dateTime = '".date("Y-m-d H:i:s")."'
								".
								"WHERE pro_id='".$this->uid."' AND form_id='".$this->fid."' ";
							sqlQuery($sql);
						}else{
							$sql="INSERT INTO chart_signatures SET ".
									" sign_path='".sqlEscStr($form_sign_path.$img_nm)."',
									  sign_coords ='".sqlEscStr($strpixls)."',
									  sign_coords_dateTime = '".date("Y-m-d H:i:s")."',".
									"pro_id='".$this->uid."', form_id='".$this->fid."', sign_type='1' ";
							sqlQuery($sql);
						}
						$flg_done=1;
					}
				}
			}
		}

		return $flg_done;
	}

	//
	function isChartNoteSigned($flgaddsign=0){

		$ret=0;
		$sql="SELECT count(*) as num from chart_signatures WHERE form_id='".$this->fid."' AND pro_id='".$this->uid."' AND (sign_coords!='' OR sign_path!='') ";
		$row=sqlQuery($sql);
		if($row!=false && $row["num"]>0){

			$ret=1;
		}else{
			//add sign from user
			if($flgaddsign==1){
				$ret = $this->addUserSignOnChart();
			}
		}
		return $ret;
	}

	// for clinical app
	function finalizeChartNote(){
		include_once(dirname(__FILE__)."/User.php");

		$finalizerId = $phyid = $_REQUEST['phyId'];

		$userObj = New User($phyid);
		//Add Provider Id
		$chkprovIds = $_REQUEST['phyId'].",";
		$update_date = $elem_masterFinalDate = date("Y-m-d H:i:s");
		$msg_err = "";

		//phy : Doctor
		$u_tp_cn = $userObj->getUType(1);
		$u_acc_vo = $userObj->checkPermissions("checkAccessChartVO");

		if($u_tp_cn!=1||$u_acc_vo==1){
			$msg_err = "User do not have permissions to finalize a chart note.";
		}

		//signature
		if(empty($msg_err) && $u_tp_cn==1 && !$this->isChartNoteSigned(1)){
			$msg_err = "Chart note is not signed. ";
		}

		if(empty($phyid)){ $msg_err = "User ID is empty. "; }

		//

		if($msg_err==""){

			$sql= "SELECT count(*) AS num from chart_master_table WHERE id='".$this->fid."' AND patient_id='".$this->pid."' AND finalize = '0' ";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$sql = "UPDATE chart_master_table ".
					  "SET ";
				$sql .= "update_date = '".$update_date."', ";
				$sql .= "finalize = '1', ".
					 "record_validity = '1', ".
					 "providerId = '".$phyid."', ".
					 "finalizerId = '".$finalizerId."', ".
					 "finalizeDate='".$elem_masterFinalDate."', ".
					 "provIds = CONCAT(provIds, IF(LOCATE('".$chkprovIds."', provIds)>0,'','".$chkprovIds."')) ";
				$sql .=	" WHERE id= '".$this->fid."' ";
				$res = sqlQuery($sql);
			}
			$opres=1;

		}else{
			$opres=0;
		}

		return array("result"=>$opres, "msg"=>$msg_err);
	}
	//

	function isAllChartNoteFinalized(){
		$sql = "SELECT id FROM chart_master_table ".
				"WHERE patient_id = '".$this->pid."' ".
				"AND finalize = '0' ".
				"AND delete_status='0' ";
		$res = imw_query($sql);
		if($res != false){
			$num = imw_num_rows($res);
			return ($num > 0) ? false : true ;
		}
		return false;
	}


	// for clinical app
	function unfinalizeChartNote(){

		if(!$this->isAllChartNoteFinalized()){
			return 0; //there is a active chart.
		}else{

		if(isset($_SESSION["authId"]) && !empty($_SESSION["authId"]) ){
			$phyid = $_SESSION["authId"];
		}
		//Add Provider Id
		$chkprovIds = $phyid.",";

		if(empty($phyid)){ return 0; } //empty phyid

		$sql= "SELECT count(*) AS num from chart_master_table WHERE id='".$this->fid."' AND patient_id='".$this->pid."' AND finalize = '1' ";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]>0){
			$sql = "UPDATE chart_master_table ".
				  "SET ";
			$sql .= "update_date = '".$update_date."', ";
			$sql .= "finalize = '0', ".
				 //"record_validity = '1', ".
				 "providerId = '".$phyid."', ".
				 "finalizerId = '0', ".
				 "finalizeDate='0000-00-00 00:00:00', autoFinalize='0', ".
				 "provIds = CONCAT(provIds, IF(LOCATE('".$chkprovIds."', provIds)>0,'','".$chkprovIds."')) ";
			$sql .=	" WHERE id= '".$this->fid."' AND patient_id='".$this->pid."' ";
			$res = sqlQuery($sql);
		}

		}

		return 1;
	}
	//

	function getTemplateId(){
		$templateId=0;
		$sql = "SELECT templateId FROM chart_master_table WHERE id='".$this->fid."' AND patient_id='".$this->pid."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$templateId = $row["templateId"];
		}
		return $templateId;
	}

	function resetEncounterId(){
		$oFacilityfun = new Facility();
		$encounterId = $oFacilityfun->getEncounterId();
		if(!empty($this->fid)){
			$sql = "UPDATE chart_master_table ".
					  "SET ";
			$sql .= "encounterId = '".$encounterId."' ";
			$sql .=	" WHERE id= '".$this->fid."' AND patient_id='".$this->pid."' ";
			$res = sqlQuery($sql);
		}
		return $encounterId;
	}

	//Function works in smart chart functionality to extract status flag --
	function getCurStatusFromPost($arr, $fod, $fos, $sttPrv=""){
		$statusElemCur="";
		//get all post data
		$al_post=array();
		if(count($arr)>0){
			foreach($arr as $k=>$v){
				if(isset($v["level"])&&!empty($v["level"])){
					$al_post = unserialize($v["level"]);
					break;
				}
			}
		}
		if(isset($al_post[$fod])&&isset($al_post[$fos])){
			$statusElemCur="".$fod."=".$al_post[$fod].",".$fos."=".$al_post[$fos].",";
		}

		if(!empty($sttPrv)){
			$sttPrv=trim($sttPrv);
			$arrSttPrv = explode(",", $sttPrv);
			foreach($arrSttPrv as $k => $v){
				if(!empty($v) && strpos($v, $fod)===false&&strpos($v, $fos)===false){
					$statusElemCur.=$v.",";
				}
			}
		}

		//change
		$statusElemCur=str_replace(",,","",$statusElemCur);
		return $statusElemCur;
	}

	function isChartReviewable($loggedId,$flgEd=0){
		$isReviewable=false;
		$isEditable=false;
		$iscur_user_vphy=false;
		//Finalize Provider
		$sql = "SELECT c1.id, c1.update_date, c1.patient_id, c1.providerId,
					c1.finalizerId, autoFinalize ". // c2.pro_id AS doctorId, c3.pro_id AS cosigner_id ".
				"FROM chart_master_table c1 ".
				//"LEFT JOIN chart_assessment_plans ON chart_assessment_plans.form_id = chart_master_table.id ".
				//"LEFT JOIN chart_signatures c2 ON c2.form_id = c1.id AND c2.sign_type='1' ".
				//"LEFT JOIN chart_signatures c3 ON c3.form_id = c1.id AND c3.sign_type='2' ".
				"WHERE c1.id='".$this->fid."' ";
		$row = sqlQuery($sql);
		if($row != false)
		{
			$finalizeDate = $row["update_date"];
			$patientId = $row["patient_id"];
			$providerId = $row["providerId"];
			//$doctorId = $row["doctorId"];
			$finalizeDoctorId = $row["finalizerId"];
			//$coSignerId = $row["cosigner_id"];
			$autoFinalize = $row["autoFinalize"];

			//--
			$oSig = new Signature($row["id"]);
			$ar_chrt_phy = $oSig->getChartPhysicians();
			//---
		}

		//check type
		$ousr = new User($finalizeDoctorId);
		$utyp = $ousr->getUType();

		//Check for previous data
		if(empty($finalizeDoctorId) || !in_array($utyp, $GLOBALS['arrValidCNPhy']) || !empty($autoFinalize)){
			$finalizeDoctorId = (!empty($providerId)) ? $providerId : $ar_chrt_phy[0];
		}
		//Check Doctor
		//$doctorId = (!empty($doctorId)) ? $doctorId : $providerId;

		if(($finalizeDoctorId == $loggedId)){
			//time
			$sql = "SELECT chart_timer FROM facility WHERE facility_type = '1' ORDER BY id limit 0,1 ";
			$row = sqlQuery($sql);
			if($row != false){
				$reviewTime = $row["chart_timer"];
			}

			//if $reviewTime is empty, check in id = 1 ---
			if(!empty($reviewTime)){
				//time
				$sql = "SELECT chart_timer FROM facility WHERE id = '1' ";
				$row = sqlQuery($sql);
				if($row != false){
					$reviewTime = $row["chart_timer"];
				}
			}
			//if $reviewTime is empty, check in id = 1 ---


			if(!empty($reviewTime)){
				$reviewTimeHrs = 24 * $reviewTime;
				$sql = "SELECT UNIX_TIMESTAMP(DATE_ADD('".$finalizeDate."', INTERVAL ".$reviewTimeHrs." HOUR)) as reviewableTime, ".
					 "UNIX_TIMESTAMP(NOW()) as curTime ";
				$row = sqlQuery($sql);
				if($row != false){
					if($row["reviewableTime"] > $row["curTime"]){
						$isReviewable=true;
					}
				}
			}
		}

		//isEditable
		//if Loggin user is signer/cosigner
		if(($finalizeDoctorId == $loggedId) || (in_array($loggedId, $ar_chrt_phy))){

			//Check if all records are finalized
			$oPt = new Patient($patientId);
			if($oPt->isAllCNFinalized()){
				$isEditable=true;
			}
			//Check valid physician
			$iscur_user_vphy = true;
		}

		return ($flgEd==1) ? array($isReviewable,$isEditable,$iscur_user_vphy):$isReviewable ;
	}

	function getChartICD10Code(){
		$icd10=0;
		$sql="SELECT enc_icd10 from chart_master_table ";
		$sql.=" where id='".$this->fid."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$icd10=$row["enc_icd10"];
		}
		return $icd10;

	}

	function getCareGiverColors(){
		$arr_ret=array();

		$sql="SELECT provIds, create_by, finalizerId  FROM chart_master_table WHERE id = '".$this->fid."'  ";
		$row = sqlQuery($sql);
		if($row != false){
			$provIds=trim($row["provIds"]);
			$create_by=trim($row["create_by"]);
			$finalizer_id=trim($row["finalizerId"]);

			if(!empty($provIds)){
				$provIds = preg_replace("/,\s*/",",",$provIds);
				if(!preg_match("/,$/",$provIds)){
					$provIds .=",";
				}
			}
			if(!empty($create_by)){$provIds .=$create_by.",";}
			if(!empty($finalizer_id)){$provIds .=$finalizer_id.",";}
		}

		// tech/scribe will come here
		$oRoleAs = new RoleAs();
		$ar = $oRoleAs->get_care_giver_colors($this->fid);

		if(count($ar)>0){
			foreach($ar as $k => $arv){

				$flg_con=0;
				if(count($arr_ret)>0){
					foreach($arr_ret as $k1 => $cv){
						if($cv["id"] == $arv["id"] && $cv["type"] == $arv["type"]){ $flg_con=1; break;}
					}
				}
				if(!empty($flg_con)){ continue ; }

				$arr_ret[] = $arv;
			}
		}

		if(!empty($provIds)){

			//last comma
			$provIds=trim($provIds);
			$provIds=trim($provIds,",");
			$provIds = str_replace(" ","",$provIds);
			$t = explode(",",$provIds);
			$t = array_filter($t);
			$provIds = implode(",",$t);
			//$provIds = substr($provIds, 0, -1);

			if(strlen(trim($provIds)) > 0){

				$str="";
				$sql = "SELECT
						c1.user_type, c2.color, c1.fname, c1.mname, c1.lname, c1.id, c2.user_type_name
						FROM users c1
						INNER JOIN user_type c2 ON c2.user_type_id = c1.user_type
						WHERE c1.id IN (".$provIds.")
						ORDER BY c1.fname, c1.mname, c1.lname ";
				//order by display_order
				$rez = sqlStatement($sql);
				for($i=0;$row=sqlFetchArray($rez);$i++){

					$type = $row["user_type_name"];
					$type_abb = substr($type,0,4).".";

					//check tech/scribe if role exists, then skip
					$flg_con=0;
					if(count($arr_ret)>0){
						foreach($arr_ret as $k1 => $cv){
							if($cv["id"] == $row["id"]){ $flg_con=1; break;}
						}
					}
					if(!empty($flg_con)){ continue ; }

					$nm="";
					if(!empty($row["fname"])){$nm.="".$row["fname"]." ";}
					if(!empty($row["mname"])){$nm.="".$row["mname"]." ";}
					if(!empty($row["lname"])){$nm.="".$row["lname"]." ";}
					$nm=trim($nm);

					$color=$row["color"];

					$color1 = $color2 = "";
					if(strpos($color,",")!==false){ $tmp=explode(",", $color); $color1 = $tmp[0];$color2 = $tmp[1]; }else{ $color2=$color; }

					//clickable
					$cls = ( ($row["user_type"] == "3" || $row["user_type"] == "13") && $row["id"] == $_SESSION["authId"]) ? "clickable" : "";

					//$str .= "<tr><td class=\"legendUT\" style=\"background-color: ".$color2.";\">&nbsp;</td><td class=\"legendUT\" style=\"background-color: ".$color1.";\">&nbsp;</td><td width=\"80%\">".$nm."</td><td>".$type_abb."</td></tr>";
					$tmp = array();
					$tmp["id"]=$row["id"];
					$tmp["name"]=$nm;
					$tmp["type"]=$type_abb;
					$tmp["color1"]=$color1;
					$tmp["color2"]=$color2;
					$tmp["clickable"]=$cls;
					$arr_ret[] = $tmp;
				}

			}

		}



			/*
			if(!empty($str)){
				//<div id=\"legendDiv\">
				$str= "
				<label>Care Giver Colors:</label>
				<div>
				<table border=\"0\" width=\"100%\">".$str."</table>
				</div>
				";
				//</div>
			}
			*/



		return $arr_ret;
	}

	function se_elemStatus($exm,$act,$chkStr="",$vOd="0",$vOs="0",$flgDraw=1,$flgRetina="new"){

		switch($exm){

			case "PUPIL":
				$arr = array("Pupil");
			break;
			case "EXTERNAL":
				$arr = array("Con");
				if($flgDraw==1){	$arr[] = "Draw";}
			break;
			case "Lids":
				$arr = array("1");
			break;
			case "Lesion":
				$arr = array("2");
			break;
			case "LidPos":
				$arr = array("3");
			break;
			case "LacSys":
				$arr = array("4");
			break;
			case "DrawLa":
				$arr = array("5");
			break;
			case "LA":
				$arr = array("1","2","3","4");
				if($flgDraw==1){	$arr[] = "5";}
			break;
			case "GONIO":
				$arr = array("Iop");
				if($flgDraw==1){	$arr[] = "Iop3";}
			break;

			case "Conj":
			case "Conjunctiva":
				$arr = array("1");
			break;
			case "Corn":
			case "Cornea":
				$arr = array("2");
			break;
			case "Ant":
			case "AntChamber":
				$arr = array("3");
			break;
			case "Iris":
				$arr = array("4");
			break;
			case "Lens":
				$arr = array("5");
			break;
			case "DrawSle":
				$arr = array("6");
			break;

			case "SLE":
				$arr = array("1","2","3","4","5");
				if($flgDraw==1){	$arr[] = "6";}
			break;

			case "Vitreous":
				$arr = array("1");
			break;

			case "RetinalExam":
				$arr = array("7");
			break;
			case "Periphery":
				$arr = array("3");
			break;

			case "Vessels":
			case "BloodVessels":
				$arr = array("4");
			break;
			case "Macula":
				$arr = array("2");
			break;
			case "OpticNerve":
				$arr = array("6");
			break;
			case "DrawRv":
				$arr = array("5");
			break;

			case "RV":
				if($flgRetina=="old"){
					$arr = array("1","3","4","6");
				}else{
					$arr = array("1","2","3","4","6","7");
				}

				if($flgDraw==1){	$arr[] = "5";}
			break;
			case "OPTIC":
				$arr = array("6");
			break;
			case "EOM":
				$str = "elem_chng_divEom=1,elem_chng_divEom2=1";
				if($flgDraw==1){	$str .= ",elem_chng_divEom3=1";}
				return $str;
			break;
			case "REF_SURG":
				$arr = array("1");
			break;

		}

		$len = count($arr);
		if($act == 0){ //Check
			$arr2 = array();

			if(!empty($chkStr)){

				for($i=0;$i<$len;$i++){
					$chkOd = $arr[$i]."_Od=1";
					$chkOs = $arr[$i]."_Os=1";
					if(strpos($chkStr,$chkOd) !== false){
						$arr2[$arr[$i]]["od"]="1";
					}else{
						$arr2[$arr[$i]]["od"]="0";
					}

					if(strpos($chkStr,$chkOs) !== false){
						$arr2[$arr[$i]]["os"]="1";
					}else{
						$arr2[$arr[$i]]["os"]="0";
					}
				}
			}

			return $arr2;

		}else if($act == "1"){
			$str = "";
			for($i=0;$i<$len;$i++){
				$str .= "elem_chng_div".$arr[$i]."_Od=".$vOd.",".
						"elem_chng_div".$arr[$i]."_Os=".$vOs.",";
			}
			return $str;
		}
	}

	function setEyeStatus($w, $exmEye,$statusElem,$flgDrw=1,$flgRetina="new"){
		//Set Change Eye Status
		if(($exmEye != "OD" && $exmEye != "OS")){ //OU
			$statusElem=$this->se_elemStatus($w,"1","","1","1",$flgDrw,$flgRetina);

		}else{

			if($exmEye == "OD")	{

				if(!empty($statusElem)){
					$statusElem = str_replace("Od=0","Od=1",$statusElem);
				}else{
					$statusElem=$this->se_elemStatus($w,"1","","1","0",$flgDrw,$flgRetina);
				}

			}else if($exmEye == "OS"){

				if(!empty($statusElem)){
					$statusElem = str_replace("Os=0","Os=1",$statusElem);
				}else{
					$statusElem=$this->se_elemStatus($w,"1","","0","1",$flgDrw,$flgRetina);
				}
			}
		}

		//Set Change Eye Status
		return $statusElem;
	}

	function getExamWnlStr($exam){

		$phraseTemp="";
		if(!empty($this->pid) && !empty($this->fid)){
			$template_id = $this->getTemplateId();
			if(!empty($template_id)){
				$phraseTemp = " AND chart_template_id = '".$template_id."' ";
			}
		}

		$arrDefWnl=array(
			"Pupil" => "PERRLA, -ve APD",
			"EOM" => "Full ductions, alternate cover test - ortho distance and near.",
			"External" =>"WNL",
			"Lids" =>"WNL",
			"Lesion" =>"WNL",
			"Lid Position" =>"WNL",
			"Lacrimal System" => "WNL",
			"Gonio" => "WNL",
			"Conjunctiva"=>"Clear",
			"Cornea"=>"Clear",
			"Ant. Chamber"=>"Deep and quiet",
			"Iris & Pupil"=>"WNL",
			"Lens"=>"Clear",
			"Optic Nerve"=>"Pink & Sharp",
			"Vitreous"=>"Clear",
			"Retinal Exam"=>"Macula normal, vessels normal course and caliber",
			"CVF"=>"WNL",
			"Amsler Grid"=>"WNL",
			"Macula" => "Macula Benign",
			"Blood Vessels" => "Blood Vessels Benign",
			"Periphery"=>"WNL"
		);

		//--
		$res_fellow_sess = trim($_SESSION['res_fellow_sess']);
		if($res_fellow_sess != "" && isset($res_fellow_sess))
		{
			$providerId = $res_fellow_sess;
		}
		else
		{
			$providerId = $_SESSION["authId"];

			//Check if providerId is tech or scribe: if follow to physician, then apply his assessments and policy
			if(!empty($providerId)){
				include_once("User.php");
				$ousr =  new User($providerId);
				$isTech = $ousr->getFollowPhyId4Tech();
				if(!empty($isTech)){
					$providerId = $isTech;
				}
			}
		}
		//--

		$wnl="";

		//Check in physician console
		if(empty($wnl)){
			$sql = "SELECT wnl FROM chart_admin_wnl WHERE deleted = '0' AND UPPER(exam) = '".strtoupper($exam)."' AND phyid='".$providerId."'  ".$phraseTemp." ";
			$row = sqlQuery($sql);
			if($row!=false){
				$wnl=$row["wnl"];
			}
		}

		//Check in admin
		if(empty($wnl)){
			$sql = "SELECT wnl FROM chart_admin_wnl WHERE deleted = '0' AND UPPER(exam) = '".strtoupper($exam)."' AND phyid='0'  ".$phraseTemp." ";
			$row = sqlQuery($sql);
			if($row!=false){
				$wnl=$row["wnl"];
			}
		}

		//get Default wnl
		if(empty($wnl)){
			$wnl=$arrDefWnl[$exam];
		}

		return $wnl;
	}

	//Get Wnl From physician or admin
	function getExamWnlStr_fromPrvExm($exam, $site){

		$ret="";
		$sql="";
		$pid = $this->pid;
		$formId = $this->fid;

		if($exam=="Pupil"){
			//"Pupil" => "PERRLA, -ve APD",
			if($site == "OD"){	$phrs = " AND c2.wnlPupilOd=1 AND c2.statusElem LIKE '%Od=1%' ";	}
			if($site == "OS"){	$phrs = " AND c2.wnlPupilOs=1 AND c2.statusElem LIKE '%Os=1%' ";	}

			$sql = "SELECT c2.wnl_value FROM chart_master_table c1
					LEFT JOIN chart_pupil c2 ON c2.formId = c1.id
					where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.pupil_id DESC LIMIT 0, 1 ";

		}else if($exam=="EOM"){
		//"EOM" => "Full ductions, alternate cover test - ortho distance and near.",


		}else if($exam=="External"){
			//"External" =>"WNL",
			if($site == "OD"){	$phrs = " AND c2.wnlEeOd=1 AND c2.statusElem LIKE '%Con_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlEeOs=1 AND c2.statusElem LIKE '%Con_Os=1%' ";}

			$sql = "SELECT c2.wnl_value FROM chart_master_table c1
					LEFT JOIN  chart_external_exam c2 ON c2.form_id = c1.id
					where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.ee_id DESC LIMIT 0, 1 ";

		}else if($exam=="Lids"){
			//"Lids" =>"WNL",
			if($site == "OD"){	$phrs = " AND c2.wnlLidsOd=1 AND c2.statusElem LIKE '%1_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlLidsOs=1 AND c2.statusElem LIKE '%1_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Lids AS wnl_value FROM chart_master_table c1
					LEFT JOIN  chart_lids c2  ON c2.form_id = c1.id
					where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Lesion"){
			//"Lesion" =>"WNL",
			if($site == "OD"){	$phrs = " AND c2.wnlLesionOd=1 AND c2.statusElem LIKE '%2_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlLesionOs=1 AND c2.statusElem LIKE '%2_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Lesion AS wnl_value FROM chart_master_table c1
					LEFT JOIN  chart_lesion c2 ON c2.form_id = c1.id
					where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Lid Position"){
			//"Lid Position" =>"WNL",
			if($site == "OD"){	$phrs = " AND c2.wnlLidPosOd=1 AND c2.statusElem LIKE '%3_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlLidPosOs=1 AND c2.statusElem LIKE '%3_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_LidPos AS wnl_value FROM chart_master_table c1
					LEFT JOIN  chart_lid_pos c2 ON c2.form_id = c1.id
					where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Lacrimal System"){
			//"Lacrimal System" => "WNL",
			if($site == "OD"){	$phrs = " AND c2.wnlLacSysOd=1 AND c2.statusElem LIKE '%4_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlLacSysOs=1 AND c2.statusElem LIKE '%4_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_LacSys AS wnl_value FROM chart_master_table c1
						LEFT JOIN  chart_lac_sys c2 ON c2.form_id = c1.id
						where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Gonio"){
			//"Gonio" => "WNL",
			if($site == "OD"){	$phrs = " AND c2.wnlOd=1 AND c2.statusElem LIKE '%Iop_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlOs=1 AND c2.statusElem LIKE '%Iop_Os=1%' ";}

			$sql = "SELECT c2.wnl_value FROM chart_master_table c1
						LEFT JOIN  chart_gonio c2 ON c2.form_id = c1.id
						where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.gonio_id DESC LIMIT 0, 1 ";

		}else if($exam=="Conjunctiva"){
			//"Conjunctiva"=>"Clear",
			if($site == "OD"){	$phrs = " AND c2.wnlConjOd=1 AND c2.statusElem LIKE '%1_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlConjOs=1 AND c2.statusElem LIKE '%1_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Conjunctiva AS wnl_value FROM chart_master_table c1
					LEFT JOIN  chart_conjunctiva  c2  ON c2.form_id = c1.id
					where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Cornea"){
			//"Cornea"=>"Clear",
			if($site == "OD"){	$phrs = " AND c2.wnlCornOd=1 AND c2.statusElem LIKE '%2_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlCornOs=1 AND c2.statusElem LIKE '%2_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Cornea AS wnl_value FROM chart_master_table c1
					LEFT JOIN  chart_cornea  c2 ON c2.form_id = c1.id
					where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Ant. Chamber"){
			//"Ant. Chamber"=>"Deep and quiet",
			if($site == "OD"){	$phrs = " AND c2.wnlAntOd=1 AND c2.statusElem LIKE '%3_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlAntOs=1 AND c2.statusElem LIKE '%3_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Ant AS wnl_value FROM chart_master_table c1
						LEFT JOIN  chart_ant_chamber  c2  ON c2.form_id = c1.id
						where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Iris & Pupil"){
			//"Iris & Pupil"=>"WNL",
			if($site == "OD"){	$phrs = " AND c2.wnlIrisOd=1 AND c2.statusElem LIKE '%4_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlIrisOs=1 AND c2.statusElem LIKE '%4_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Iris AS wnl_value FROM chart_master_table c1
						LEFT JOIN  chart_iris  c2 ON c2.form_id = c1.id
						where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Lens"){
			//"Lens"=>"Clear",
			if($site == "OD"){	$phrs = " AND c2.wnlLensOd=1 AND c2.statusElem LIKE '%5_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlLensOd=1 AND c2.statusElem LIKE '%5_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Lens AS wnl_value FROM chart_master_table c1
						LEFT JOIN  chart_lens  c2  ON c2.form_id = c1.id
						where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Optic Nerve"){
			//"Optic Nerve"=>"Pink & Sharp",
			if($site == "OD"){	$phrs = " AND c2.wnlOpticOd=1 AND c2.statusElem LIKE '%6_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlOpticOs=1 AND c2.statusElem LIKE '%6_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Optic AS wnl_value FROM chart_master_table c1
						LEFT JOIN  chart_optic  c2  ON c2.form_id = c1.id
						where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.optic_id DESC LIMIT 0, 1 ";

		}else if($exam=="Vitreous"){
			//"Vitreous"=>"Clear",
			if($site == "OD"){	$phrs = " AND c2.wnlVitreousOd=1 AND c2.statusElem LIKE '%1_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlVitreousOs=1 AND c2.statusElem LIKE '%1_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Vitreous AS wnl_value FROM chart_master_table c1
						LEFT JOIN  chart_vitreous  c2  ON c2.form_id = c1.id
						where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Retinal Exam"){
			//"Retinal Exam"=>"Macula normal, vessels normal course and caliber",
			if($site == "OD"){	$phrs = " AND c2.wnlRetinalOd=1 AND c2.statusElem LIKE '%7_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlRetinalOs=1 AND c2.statusElem LIKE '%7_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_RetinalExam AS wnl_value FROM chart_master_table c1
							LEFT JOIN  chart_retinal_exam  c2  ON c2.form_id = c1.id
							where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";


		}else if($exam=="CVF"){
		//"CVF"=>"WNL",

		}else if($exam=="Amsler Grid"){
		//"Amsler Grid"=>"WNL",

		}else if($exam=="Macula"){
			//"Macula" => "Macula Benign",
			if($site == "OD"){	$phrs = " AND c2.wnlMaculaOd=1 AND c2.statusElem LIKE '%2_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlMaculaOs=1 AND c2.statusElem LIKE '%2_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Macula AS wnl_value FROM chart_master_table c1
							LEFT JOIN  chart_macula  c2 ON c2.form_id = c1.id
							where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Blood Vessels"){
			//"Blood Vessels" => "Blood Vessels Benign",
			if($site == "OD"){	$phrs = " AND c2.wnlBVOd=1 AND c2.statusElem LIKE '%4_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlBVOs=1 AND c2.statusElem LIKE '%4_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_BV AS wnl_value FROM chart_master_table c1
						LEFT JOIN  chart_blood_vessels  c2  ON c2.form_id = c1.id
						where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";

		}else if($exam=="Periphery"){
			//"Periphery"=>"WNL"
			if($site == "OD"){	$phrs = " AND c2.wnlPeriOd=1 AND c2.statusElem LIKE '%3_Od=1%' ";}
			if($site == "OS"){	$phrs = " AND c2.wnlPeriOs=1 AND c2.statusElem LIKE '%3_Os=1%' ";}

			$sql = "SELECT c2.wnl_value_Peri AS wnl_value FROM chart_master_table c1
							LEFT JOIN  chart_periphery  c2 ON c2.form_id = c1.id
							where c1.patient_id='".$pid."'  AND c1.id < '".$formId."'  ".$phrs." ORDER BY c1.date_of_service DESC, c2.id DESC LIMIT 0, 1 ";
		}


		if(!empty($sql)){
			$row = sqlQuery($sql);
			if($row!=false){
				$wnl_v = $row["wnl_value"];
				$ar_wnl_v = explode("~!!~",$wnl_v);
				if(count($ar_wnl_v)==2){
					if($site=="OD"){ $ret=$ar_wnl_v[0];  }
					else if($site=="OS"){ $ret=$ar_wnl_v[1];  }
				}else{
					$ret=$ar_wnl_v[0];
				}
			}
		}

		return trim($ret);
	}

	function isAppletDrawn($str){
		return ((trim($str) != "") && (trim($str) != "0-0-0:;") && (trim($str) != "255-0-0:;")) ? true : false;
	}

	//Get Archived  Ocular Med.
	function getArcOcuMedHx($medication = ''){
		$flg=0;
		$arrC=array();
		$sql = "SELECT lists FROM chart_genhealth_archive WHERE patient_id='".$this->pid."' AND form_id='".$this->fid."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$arrC_tmp = array();
			$arrLists = unserialize($row["lists"]);

			$medQryRes = $arrLists[4];
			$len=is_array($medQryRes) ? count($medQryRes) : 0 ;
			$arrFields=array();
			for($m=0;$m<$len;$m++){
				if($medQryRes[$m]['allergy_status']!='Active'){continue;}
				$med_name = ucfirst($medQryRes[$m]['title']);

				//$med_destination = $medQryRes[$m]['destination'];
				if($medQryRes[$m]['sites'] == 3){
					$site = "OU";
				}elseif($medQryRes[$m]['sites'] == 2){
					$site = "OD";
				}elseif($medQryRes[$m]['sites'] == 1){
					$site = "OS";
				}elseif($medQryRes[$m]['sites'] == 4){
					$site = "PO";
				}else $site = '';
				$med_sig = $medQryRes[$m]['sig'];
				$dosage = $medQryRes[$m]['destination'];
				$comment = $medQryRes[$m]['med_comments'];
				$compliant = $medQryRes[$m]['compliant'];
				$date = $medQryRes[$m]['date'];
				$tmp ="";
				$tmp = trim($med_name);
				if(!empty($dosage))
				$tmp .=" ".$dosage;
				if(!empty($site))
				$tmp .=" ".$site;
				if(!empty($med_sig))
				$tmp .=" ".$med_sig;
				if(!empty($comment))
				$tmp .="; ".$comment;

				$bgdate = $medQryRes[$m]['begdate'];
				$arrFields[$med_name]['compliant']= $compliant;
				$arrFields[$med_name]['date']= $date;
				if(!empty($bgdate)){
					$arrC_tmp[$bgdate][]=array($tmp,$med_name,$compliant,$date);
				}else{
					$arrC_tmp["0000-00-00"][]=array($tmp,$med_name." ".$site,$compliant,$date);
				}
			}

			//Check for Duplicacy
			if(count($arrC_tmp)>0){
				$arrUniqueCheck = array();
				//sort by key
				krsort($arrC_tmp);
				$arrC=array();
				foreach($arrC_tmp as $key=>$val){
					if(count($val)>0){
						foreach($val as $key2=>$val2){
							$med_name=$val2[1];
							if(!empty($med_name) && in_array($med_name,$arrUniqueCheck)){continue;}
							$arrUniqueCheck[]=$med_name;
							$arrC[]=$val2[0];
						}
					}
				}
			}

			if(count($arrC)>0){
				$flg=1;
			}

		}
		return array($arrC,$flg,$arrFields);
	}

//Make User Type Elements String to save --
function getUTElemString($elem_utElems,$elem_utElems_cur){
	//refine past values
	$arTmp1 = explode(",",$elem_utElems_cur);
	if(count($arTmp1)>0){
		foreach($arTmp1 as $key0=>$val0){
			if(!empty($val0)){
				if(strpos($elem_utElems,",")!==false){
					$val0 = $val0.",";
				}

				$elem_utElems=str_replace($val0,"",$elem_utElems);
			}
		}
	}
	//REMOVE
	$ptrn = array("/\d+@\|/","/\|\|/");
	$rep = array("","|");
	$elem_utElems=preg_replace($ptrn, $rep, $elem_utElems);
	//
	$tmp_usr_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"] ;
	$sepUT="|".$tmp_usr_type."@"; //put bar so that 11 is not taken for 1
	if(!empty($elem_utElems)&&strpos("|".$elem_utElems,$sepUT)!==false){
		$arrUTE = explode("|",$elem_utElems);
		if(count($arrUTE)>0){
			$tmp="";
			foreach($arrUTE as $key=>$val){
				if(!empty($val)){
					if(strpos($val,$sepUT)!==false){
						$val = str_replace($sepUT,"",$val);
						$elem_utElems_cur = $val.$elem_utElems_cur;
					}else{
						$tmp.=$val."|";
					}
				}
			}
			if(!empty($tmp) && $tmp!="|"){
				$elem_utElems =	 $tmp;
			}
		}
	}

	if(!empty($elem_utElems_cur)){
		//unique--
		$arTmp = explode(",",$elem_utElems_cur);
		$arTmp = array_unique($arTmp);
		$elem_utElems_cur = implode(",",$arTmp);
		//Add
		if(!empty($elem_utElems_cur)){$elem_utElems_cur = $sepUT.$elem_utElems_cur."|";}
	}
	$ut_elem = $elem_utElems.$elem_utElems_cur;
	return $ut_elem;
}
//Make User Type Elements String to save --

function getNxtPrevFid($dos_ymd){
	$flgNum = $stEq = "";
	$sql = "SELECT count(*) AS num
			FROM chart_master_table c1 ".
			//"LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id ".
			"WHERE c1.patient_id='".$this->pid."'".
			//"AND IFNULL(c2.date_of_service,DATE_FORMAT(c1.create_dt,'%Y-%m-%d')) = '".$dos_ymd."' ".
			"AND c1.date_of_service = '".$dos_ymd."' ".
			"AND c1.delete_status='0'
			";	//AND c1.purge_status='0'
	$row = sqlQuery($sql);
	if($row != false){
		if($row["num"] > 1){
			$flgNum = "1";
			$stEq = "=";
		}
	}

	$arrdir = array("Prev"=>"<","Nxt"=>">");
	$arrRet=array();
	foreach($arrdir as $key => $val){

		$dir = $val;
		$sort = ($dir==">") ? "ASC" : "DESC";
		$phrseTid = "";
		if($flgNum == "1"){
			$phrseFid = "AND c1.id ".$dir." '".$this->fid."' " ;
		}

		$sql = "SELECT c1.id,c1.memo,c1.finalize,c1.releaseNumber
				FROM chart_master_table c1 ".
				//"LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id".
				"WHERE c1.patient_id='".$this->pid."' AND c1.delete_status='0' ".
				//"AND IFNULL(c2.date_of_service,DATE_FORMAT(c1.create_dt,'%Y-%m-%d')) ".$dir.$stEq." '".$dos_ymd."' ".
				"AND c1.date_of_service ".$dir.$stEq." '".$dos_ymd."' ".
				"".$phrseFid.
				//"ORDER BY IFNULL(c2.date_of_service,c1.create_dt) ".$sort.",  c1.id ".$sort." ".
				"ORDER BY c1.date_of_service ".$sort.",  c1.id ".$sort." ".
				"LIMIT 0,1 ";

		$row = sqlQuery($sql);
		$arr = array();
		if($row != false){
			$arr["id"]=$row["id"];
			$arr["typeChart"]=(!empty($row["memo"])) ? "Memo Chart Note" : "Chart Note";
			$arr["chartStatus"]=($row["finalize"] == "1") ? "Final" : "Active"; //"Finalized"
			$arr["releaseNum"]=$row["releaseNumber"];
		}

		$arrRet[$key] = json_encode($arr);
	}

	return $arrRet;
}

function getChartNotesExamsDone(){
	$sql = "SELECT ".
		   "chart_master_table.*, ".
		   "chart_assessment_plans.*, ".
		   //Amsler
		   "amsler_grid.id AS amslerGridId, ".
		   "amsler_grid.amsler_os, amsler_grid.amsler_od, ".
		   //EOM
		   "chart_eom.eom_id AS eomId, ".
		   "chart_eom.npc_wnl_abn, ".
		   "chart_eom.npc_cm, ".
		   "chart_eom.eom_full, ".
		   "chart_eom.eom_ortho, ".
		   "chart_eom.eom_abn_right_left_alter, ".
		   "chart_eom.eom_abn_near_far_both, ".
		   "chart_eom.eom_abn_desc, ".
		   "chart_eom.eom_hori_eso_exo, ".
		   "chart_eom.eom_hori_trophia_phoria, ".
		   "chart_eom.eom_hori_near_far_both, ".
		   "chart_eom.eom_hori_desc, ".
		   "chart_eom.eom_verti_hyper_hypo, ".
		   "chart_eom.eom_verti_near_far_both, ".
		   "chart_eom.eom_verti_trophia_phoria, ".
		   "chart_eom.eom_verti_desc, ".
		   "chart_eom.wnl AS wnlEOM, ".
		   "chart_eom.examined_no_change AS examined_no_change_eom , ".
		   "chart_eom.isPositive AS isPos_EOM, ".
		   "chart_eom.diplopia_summary, ". //Separate
		   "chart_eom.cvf_summary, ". //Separate
		   "chart_eom.statusElem AS seEom, ".

		   //EE
		   "chart_external_exam.ee_id AS eeId, ".
		   "chart_external_exam.external_exam_summary,".
		   "chart_external_exam.sumOsEE,". //insert
		   "chart_external_exam.ee_desc,".
		   "chart_external_exam.wnl AS wnlEE, ".
		   "chart_external_exam.wnlEeOd, ".
		   "chart_external_exam.wnlEeOs, ".
		   "chart_external_exam.isPositive AS isPos_EE, ".
		   "chart_external_exam.examined_no_change AS examined_no_change_ee, ".
		   "chart_external_exam.statusElem AS seEe, ".

		   //IOP
		   "chart_iop.iop_id AS iopId, ".
		   "chart_iop.puff, ".
		   "chart_iop.puff_od, ".
		   "chart_iop.puff_os_1, ".
		   "chart_iop.applanation, ".
		   "chart_iop.app_od, ".
		   "chart_iop.app_os_1, ".
		   "chart_iop.puff_trgt_od, ".
		   "chart_iop.puff_trgt_os, ".
		   "chart_iop.app_trgt_od, ".
		   "chart_iop.app_trgt_os, ".
		   "chart_iop.app_time, ".
		   "chart_iop.puff_time, ".
		   "chart_iop.squeezing, ".
		   "chart_iop.unreliable, ".
		   "chart_iop.unable, ".
		   "chart_iop.squeezingApp, ".
		   "chart_iop.unreliableApp, ".
		   "chart_iop.unableApp, ".
		   "chart_iop.multiple_pressure,chart_iop.anesthetic,chart_iop.sumOdIop,chart_iop.sumOsIop, ".

		   //Gonioscopy
		   "chart_gonio.gonio_id AS gonioId, ".
		   "chart_gonio.isPositive AS isPos_gonio, ".
		   "chart_gonio.wnl AS wnlGonio, ".
		   "chart_gonio.wnlDrawOd AS wnlDrawOdGonio, chart_gonio.wnlDrawOs AS wnlDrawOsGonio, ".
		   "chart_gonio.wnlOd AS wnlFrmGonioOd, chart_gonio.wnlOs AS wnlFrmGonioOs, ".
		   "chart_gonio.examined_no_change AS examined_no_change_gonio, ".
		   "chart_gonio.statusElem AS seGonio, ".

		   //LA
		   "chart_lids.id AS lidsId, ".
		   "chart_lesion.id AS lesionId, ".
		   "chart_lid_pos.id AS lidPosId, ".
		   "chart_lac_sys.id AS lacSysId, ".
		   "la_drawings.id AS laDrawId, ".
		   //"chart_la.lid_margin_summary, ".
		   "chart_lids.lid_conjunctiva_summary, ".
		   "chart_lesion.lesion_summary, ".
		   "chart_lid_pos.lid_deformity_position_summary, ".
		   "chart_lac_sys.lacrimal_system_summary, ".
		   //"chart_la.trauma_summary, ".
		   //"chart_la.la_other_summary, ".
		   "la_drawings.drw_od_txt as la_od_txt, ".
		   "la_drawings.drw_os_txt as la_os_txt, ".
		   "chart_lids.sumLidsOs, ". // insert
		   "chart_lid_pos.sumLidPosOs, ". // insert
		   "chart_lesion.sumLesionOs, ". // insert
		   "chart_lac_sys.sumLacOs, ". // insert
		   //"chart_la.wnl AS wnlLA, ".

		   "chart_lids.wnlLidsOd AS wnlLidsOd_LA, ".
		   "chart_lids.wnlLidsOs AS wnlLidsOs_LA, ".
		   "chart_lesion.wnlLesionOd AS wnlLesionOd_LA, ".
		   "chart_lesion.wnlLesionOs AS wnlLesionOs_LA, ".
		   "chart_lid_pos.wnlLidPosOd AS wnlLidPosOd_LA, ".
		   "chart_lid_pos.wnlLidPosOs AS wnlLidPosOs_LA, ".
		   "chart_lac_sys.wnlLacSysOd AS wnlLacSysOd_LA, ".
		   "chart_lac_sys.wnlLacSysOs AS wnlLacSysOs_LA, ".
		   "la_drawings.wnlDrawOd AS wnlDrawOd_LA, ".
		   "la_drawings.wnlDrawOs AS wnlDrawOs_LA, ".

		   //"chart_la.la_examined_nochange, ".
		   "chart_lids.posLids, ".
		   "chart_lesion.posLesion, ".
		   "chart_lid_pos.posLidPos, ".
		   "chart_lac_sys.posLacSys, ".
		   "la_drawings.posDraw, ".

		   "chart_lids.statusElem AS seLids, ".
		   "chart_lesion.statusElem AS seLesion, ".
		   "chart_lid_pos.statusElem AS seLidPos, ".
		   "chart_lac_sys.statusElem AS seLacSys, ".
		   "la_drawings.statusElem AS seLaDraw, ".

		   //OPTIC
		   "chart_optic.optic_id AS opticId, ".
		   "chart_optic.od_text, ".
		   "chart_optic.os_text, ".
		   "chart_optic.optic_nerve_od_summary, ".
		   "chart_optic.optic_nerve_os_summary, ".
		   "chart_optic.wnl AS wnlOptic, ".

		   "chart_optic.wnlOpticOd, ".
		   "chart_optic.wnlOpticOs, ".

		   "chart_optic.examined_no_change AS examined_no_change_optic, ".
		   "chart_optic.isPositive AS isPos_Optic, ".
		   "chart_optic.statusElem AS seOptic, ".

		   //PUPIL
		   "chart_pupil.pupil_id AS pupilId, ".
		   "chart_pupil.apdMinusOdSummary, ".
		   "chart_pupil.apdMinusOsSummary, ".
		   "chart_pupil.apdPlusOdSummary, ".
		   "chart_pupil.apdPlusOsSummary, ".
		   "chart_pupil.reactionOdSummary, ".
		   "chart_pupil.reactionOsSummary, ".
		   "chart_pupil.shapeOdSummary, ".
		   "chart_pupil.shapeOsSummary, ".

		   "chart_pupil.sumOdPupil, ". //insert
		   "chart_pupil.sumOsPupil, ". //insert
		   "chart_pupil.wnl AS wnlPupil, ".
		   "chart_pupil.wnlPupilOd, ".
		   "chart_pupil.wnlPupilOs, ".
		   "chart_pupil.perrla AS perrlaPupil, ".
		   "chart_pupil.isPositive AS isPos_Pupil, ".
		   "chart_pupil.examinedNoChange AS examinedNoChange_pupil, ".
		   "chart_pupil.statusElem AS sePupil, ".

		   //RV
		   "chart_retinal_exam.id AS rvId, ".
		   "chart_vitreous.vitreous_od_summary, ".
		   "chart_vitreous.vitreous_os_summary, ".
		   "chart_blood_vessels.blood_vessels_od_summary, ".
		   "chart_blood_vessels.blood_vessels_os_summary, ".
		   "chart_macula.macula_od_summary, ".
		   "chart_macula.macula_os_summary, ".
		   "chart_retinal_exam.retinal_od_summary as retina_od_summary, ".
		   "chart_retinal_exam.retinal_os_summary as retina_os_summary, ".
		   //"chart_rv.od_desc, ".
		   //"chart_rv.os_desc, ".
		   "chart_periphery.periphery_od_summary, ". //insert
		   "chart_periphery.periphery_os_summary, ". //insert
		   "chart_retinal_exam.wnlRetinal AS wnlRV, ".

		   "chart_vitreous.wnlVitreousOd AS wnlVitreousOd_RV, ".
		   "chart_vitreous.wnlVitreousOs AS wnlVitreousOs_RV, ".
		   "chart_macula.wnlMaculaOd AS wnlMaculaOd_RV, ".
		   "chart_macula.wnlMaculaOs AS wnlMaculaOs_RV, ".
		   "chart_periphery.wnlPeriOd AS wnlPeriOd_RV, ".
		   "chart_periphery.wnlPeriOs AS wnlPeriOs_RV, ".
		   "chart_blood_vessels.wnlBVOd AS wnlBVOd_RV, ".
		   "chart_blood_vessels.wnlBVOs AS wnlBVOs_RV, ".
		   "chart_draw_rv.wnlDrawOd AS wnlDrawOd_RV, ".
		   "chart_draw_rv.wnlDrawOs AS wnlDrawOs_RV, ".
		   "chart_draw_rv.draw_type AS drawTypeRV, ".

		   "chart_retinal_exam.periNotExamined, ".
		   "chart_retinal_exam.peri_ne_eye, ".
		   "chart_retinal_exam.posRetinal as isPos_RV, ".
		   "chart_retinal_exam.ncRetinal AS examined_no_change_rv, ".
		    "chart_draw_rv.id AS idRvDrw, ".
		    "chart_draw_rv.statusElem AS seRvDrw, ".
		   "chart_draw_rv.exm_drawing as od_drawing, ".
		   //"chart_rv.os_drawing, ".
		   "chart_retinal_exam.statusElem AS seRv, ".

		   //CHART SLIT LAMP EXAM
		   "chart_conjunctiva.id AS conjId, ".
		   "chart_conjunctiva.conjunctiva_od_summary, ".
		   "chart_conjunctiva.conjunctiva_os_summary, ".
		   "chart_conjunctiva.wnlConj, ".
		   "chart_conjunctiva.wnlConjOd AS wnlConjOd_SLE, ".
		   "chart_conjunctiva.wnlConjOs AS wnlConjOs_SLE, ".
		   "chart_conjunctiva.posConj, ".
		   "chart_conjunctiva.statusElem AS seConj,".

		   "chart_cornea.id AS cornId, ".
		   "chart_cornea.cornea_od_summary, ".
		   "chart_cornea.cornea_os_summary, ".
		   "chart_cornea.wnlCorn, ".
		   "chart_cornea.wnlCornOd AS wnlCornOd_SLE, ".
		   "chart_cornea.wnlCornOs AS wnlCornOs_SLE, ".
		   "chart_cornea.posCorn, ".
		   "chart_cornea.statusElem AS seCorn,".

		   "chart_ant_chamber.id AS antId, ".
		   "chart_ant_chamber.anf_chamber_od_summary, ".
		   "chart_ant_chamber.anf_chamber_os_summary, ".
		   "chart_ant_chamber.wnlAnt, ".
		   "chart_ant_chamber.wnlAntOd AS wnlAntOd_SLE, ".
		   "chart_ant_chamber.wnlAntOs AS wnlAntOs_SLE, ".
		   "chart_ant_chamber.posAnt, ".
		   "chart_ant_chamber.statusElem AS seAnt,".

		   "chart_iris.id AS irisId, ".
		   "chart_iris.iris_pupil_od_summary, ".
		   "chart_iris.iris_pupil_os_summary, ".
		   "chart_iris.wnlIris, ".
		   "chart_iris.wnlIrisOd AS wnlIrisOd_SLE, ".
		   "chart_iris.wnlIrisOs AS wnlIrisOs_SLE, ".
		   "chart_iris.posIris, ".
		   "chart_iris.statusElem AS seIris,".

		   "chart_lens.id AS lensId, ".
		   "chart_lens.lens_od_summary, ".
		   "chart_lens.lens_os_summary, ".
		   "chart_lens.wnlLens, ".
		   "chart_lens.wnlLensOd AS wnlLensOd_SLE, ".
		   "chart_lens.wnlLensOs AS wnlLensOs_SLE, ".
		   "chart_lens.posLens, ".
		   "chart_lens.statusElem AS seLens,".

		   //Ascan
		   "surgical_tbl.surgical_id, ".
		   "surgical_tbl.performedByOD, ".
		   "surgical_tbl.performedByOS, ".
		   "surgical_tbl.signedById, ".
		   "surgical_tbl.signedByOSId, ".

		   //IOL_Master
		   "iol_master_tbl.iol_master_id, ".
		   "iol_master_tbl.performedByOD AS performedByOD_IOLM, ".
		   "iol_master_tbl.performedByOS AS performedByOS_IOLM, ".
		   "iol_master_tbl.signedById AS signedById_OD_IOLM, ".
		   "iol_master_tbl.signedByOSId AS signedById_OS_IOLM, ".

		   //IVFA
		   "ivfa.vf_id AS vfId, ".
		   "ivfa.ivfa,".
		   "ivfa.ivfa_od, ".
		   "ivfa.phy AS phyNameIvfa, ".

		   //ICG
		   "icg.icg_id AS icgId, ".
		   "icg.icg,".
		   "icg.icg_od, ".
		   "icg.phy AS phyNameIcg, ".

		   //Disc
		   "disc.disc_id, ".
		   "disc.fundusDiscPhoto, ". //Separeted 2 DISC
		   "disc.photoEye, ".  //Separeted 2 DISC OU OD OS
		   "disc.phyName AS phyNameDisc, ".
		   //OPHTHA
		   "ophtha.ophtha_id AS ophthaId, ".
		   "ophtha.ophtha_os, ".
		   "ophtha.ophtha_od, ".

		   //VFNFA
		   //VF
		   "vf.vf_id AS vfNewId, ".  //VF
		   "vf.vf, ".
		   "vf.vf_eye, ".
		   "vf.descOd AS vfSumOd, ".
		   "vf.descOs AS vfSumOs, ".
		   "vf.phyName AS phyNameVF, ".

		   //VF-GL
		   "vf_gl.vf_gl_id AS vfGLId, ".  //VF-GL
		   "vf_gl.vf_gl, ".
		   "vf_gl.vf_gl_eye, ".
		   "vf_gl.descOd AS vfGLSumOd, ".
		   "vf_gl.descOs AS vfGLSumOs, ".
		   "vf_gl.phyName AS phyNameVFGL, ".

		   // NFA
		   "nfa.nfa_id, ".
		   "nfa.scanLaserNfa, ".
		   "nfa.scanLaserEye, ".
		   "nfa.phyName AS phyNameNfa, ".
		   // Pachy
		   "pachy.pachy_id, ".
		   "pachy.pachyMeter, ".
		   "pachy.pachyMeterEye, ".
		   "pachy.phyName AS phyNamePachy, ".
		   //DiscExternal
		   "disc_external.disc_id AS disc_id_DiscExter, ".
		   "disc_external.fundusDiscPhoto AS discExterPhoto, ".
		   "disc_external.photoEye AS discExterEye, ".
		   "disc_external.phyName AS phyNameDiscExter, ".

		   //Oct
		   "oct.oct_id, ".
		   "oct.scanLaserOct, ".
		   "oct.scanLaserEye AS scanLaserEyeOct, ".
		   "oct.phyName AS phyNameOct, ".

		   //Oct-RNFL
		   "oct_rnfl.oct_rnfl_id, ".
		   "oct_rnfl.scanLaserEye AS scanLaserEyeOct_RNFL, ".
		   "oct_rnfl.phyName AS phyNameOct_RNFL, ".

		   //test_gdx
		   "test_gdx.gdx_id, ".
		   "test_gdx.scanLaserEye AS scanLaserEyeGdx, ".
		   "test_gdx.phyName AS phyNameGdx, ".

		   //test_cellcnt
		   "test_cellcnt.test_cellcnt_id, ".
		   "test_cellcnt.test_cellcnt_eye , ".
		   "test_cellcnt.phyName AS phyNameCellCount, ".

		   //test_bscan
		   "test_bscan.test_bscan_id, ".
		   "test_bscan.test_bscan_eye , ".
		   "test_bscan.phyName AS phyNameBScan, ".

		   //Topography
		   "topography.topo_id, ".
		   "topography.topoMeter, ".
		   "topography.topoMeterEye, ".
		   "topography.phyName AS phyNameTopo, ".

		   //Chart_LEFT_CC_HISTORY
		   "chart_left_cc_history.reason, ".
		   "chart_left_cc_history.neuro_ao, ".
		   "chart_left_cc_history.neuro_aff, ".
		   //Neuro/Psych
		   "chart_left_cc_history.neuroPsych, ". //insert

		   //Dialation
		   "chart_dialation.dia_id, ".
		   "chart_dialation.pheny25, ".
		   "chart_dialation.tropicanide, ".
		   "chart_dialation.cyclogel, ".
		   "chart_dialation.other, ".
		   "chart_dialation.dilated_mm, ".
		   "chart_dialation.warned_n_advised, ".
		   "chart_dialation.patient_not_driving, ".
		   "chart_dialation.patientAllergic, ".
		   "chart_dialation.unableDilation, ".
		   "chart_dialation.noDilation, ".
		   "chart_dialation.dilation, ".
		   "chart_dialation.eyeSide, ".

		   //CVF
		   "chart_cvf.cvf_id, ".
		  //chart_left_provider_issue
		   "chart_left_provider_issue.complaint1Str, ".
		   "chart_left_provider_issue.complaint2Str, ".
		   "chart_left_provider_issue.complaint3Str, ".
		   "chart_left_provider_issue.vpDistance, ".
		   "chart_left_provider_issue.vpMidDistance, ".
		   "chart_left_provider_issue.vpNear, ".
		   "chart_left_provider_issue.vpGlare, ".
		   "chart_left_provider_issue.vpOther, ".
		   "chart_left_provider_issue.irrLidsExternal, ".
		   "chart_left_provider_issue.irrOcular, ".
		   "chart_left_provider_issue.psSpots, ".
		   "chart_left_provider_issue.psFloaters, ".
		   "chart_left_provider_issue.psFlashingLights, ".
		   "chart_left_provider_issue.psAmslerGrid, ".
		   "chart_left_provider_issue.neuroDblVision, ".
		   "chart_left_provider_issue.neuroTempArtSymp, ".
		   "chart_left_provider_issue.neuroVisionLoss, ".
		   "chart_left_provider_issue.neuroHeadaches, ".
		   "chart_left_provider_issue.neuroMigHead, ".
		   "chart_left_provider_issue.neuroOther, ".
		   "chart_left_provider_issue.rvspostop, ".
		   "chart_left_provider_issue.rvsfollowup, ".
		   "chart_left_provider_issue.vpComment, ".

		   //Chart Assessment plan
		   "chart_assessment_plans.assessment_1, ".
		   "chart_assessment_plans.assessment_2, ".
		   "chart_assessment_plans.assessment_3, ".
		   "chart_assessment_plans.assessment_4, ".

		   "chart_master_table.id AS formID ".
		   "FROM ".
		   "chart_master_table ".
		   "LEFT JOIN amsler_grid ON amsler_grid.form_id = chart_master_table.id ".
		   "LEFT JOIN chart_assessment_plans ON chart_assessment_plans.form_id = chart_master_table.id ".
		   "LEFT JOIN chart_eom ON chart_eom.form_id = chart_master_table.id  AND chart_eom.purged='0'  ".
		   "LEFT JOIN chart_external_exam ON chart_external_exam.form_id = chart_master_table.id  AND chart_external_exam.purged='0'  ".
		   "LEFT JOIN chart_iop ON chart_iop.form_id = chart_master_table.id AND chart_iop.purged='0'  ".
		   "LEFT JOIN chart_gonio ON chart_gonio.form_id = chart_master_table.id AND chart_gonio.purged='0' ".
		   "LEFT JOIN chart_lids ON chart_lids.form_id = chart_master_table.id  AND chart_lids.purged='0'  ".
		   "LEFT JOIN chart_lesion ON chart_lesion.form_id = chart_master_table.id  AND chart_lesion.purged='0'  ".
		   "LEFT JOIN chart_lid_pos ON chart_lid_pos.form_id = chart_master_table.id  AND chart_lid_pos.purged='0'  ".
		   "LEFT JOIN chart_lac_sys ON chart_lac_sys.form_id = chart_master_table.id  AND chart_lac_sys.purged='0'  ".
		   "LEFT JOIN chart_drawings la_drawings ON la_drawings.form_id = chart_master_table.id  AND la_drawings.purged='0' AND la_drawings.exam_name='LA'  ".

		   "LEFT JOIN chart_optic ON chart_optic.form_id = chart_master_table.id  AND chart_optic.purged='0'  ".
		   "LEFT JOIN chart_pupil ON chart_pupil.formId = chart_master_table.id  AND chart_pupil.purged='0'  ".

		   "LEFT JOIN chart_vitreous ON chart_vitreous.form_id = chart_master_table.id AND chart_vitreous.purged='0'  ".
		   "LEFT JOIN chart_macula ON chart_macula.form_id = chart_master_table.id AND chart_macula.purged='0'  ".
		   "LEFT JOIN chart_retinal_exam ON chart_retinal_exam.form_id = chart_master_table.id AND chart_retinal_exam.purged='0'  ".
		   "LEFT JOIN chart_periphery ON chart_periphery.form_id = chart_master_table.id AND chart_periphery.purged='0'  ".
		   "LEFT JOIN chart_blood_vessels ON chart_blood_vessels.form_id = chart_master_table.id AND chart_blood_vessels.purged='0'  ".
		    "LEFT JOIN chart_drawings chart_draw_rv ON chart_draw_rv.form_id = chart_master_table.id  AND chart_draw_rv.purged='0' AND chart_draw_rv.exam_name='FundusExam'  ".

		   "LEFT JOIN chart_conjunctiva ON chart_conjunctiva.form_id = chart_master_table.id  AND chart_conjunctiva.purged='0'  ".
		   "LEFT JOIN chart_cornea ON chart_cornea.form_id = chart_master_table.id  AND chart_cornea.purged='0'  ".
		   "LEFT JOIN chart_ant_chamber ON chart_ant_chamber.form_id = chart_master_table.id  AND chart_ant_chamber.purged='0'  ".
		   "LEFT JOIN chart_iris ON chart_iris.form_id = chart_master_table.id  AND chart_iris.purged='0'  ".
		   "LEFT JOIN chart_lens ON chart_lens.form_id = chart_master_table.id  AND chart_lens.purged='0'  ".
		   //"LEFT JOIN chart_drawings chart_draw_sle ON chart_draw_sle.form_id = chart_master_table.id  AND chart_draw_sle.purged='0' AND chart_draw_sle.exam_name='SLE'  ".

		   "LEFT JOIN ivfa ON ivfa.form_id = chart_master_table.id AND ivfa.purged = '0' AND ivfa.del_status='0' ".
		   "LEFT JOIN icg ON icg.form_id = chart_master_table.id AND icg.purged = '0' AND icg.del_status='0' ".
		   "LEFT JOIN ophtha ON ophtha.form_id = chart_master_table.id AND ophtha.purged = '0' ".
		   "LEFT JOIN vf_nfa ON vf_nfa.form_id = chart_master_table.id ".
		   "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".

		   "LEFT JOIN vf ON vf.formId = chart_master_table.id AND vf.purged = '0' AND vf.del_status='0' ".
		   "LEFT JOIN vf_gl ON vf_gl.formId = chart_master_table.id AND vf_gl.purged = '0' AND vf_gl.del_status='0' ".
		   "LEFT JOIN nfa ON nfa.form_id = chart_master_table.id AND nfa.purged = '0' AND nfa.del_status='0' ".
		   "LEFT JOIN pachy ON pachy.formId = chart_master_table.id AND pachy.purged = '0' AND pachy.del_status='0' ".
		   "LEFT JOIN disc ON disc.formId = chart_master_table.id AND disc.purged = '0' AND disc.del_status='0' ".

		   "LEFT JOIN chart_dialation ON chart_dialation.form_id = chart_master_table.id AND chart_dialation.purged='0'  ".
		   "LEFT JOIN chart_cvf ON chart_cvf.formId = chart_master_table.id ".
		   "LEFT JOIN chart_left_provider_issue ON chart_left_provider_issue.form_id = chart_master_table.id ".

		   "LEFT JOIN disc_external ON disc_external.formId = chart_master_table.id AND disc_external.purged = '0' AND disc_external.del_status='0' ".
		   "LEFT JOIN surgical_tbl ON surgical_tbl.form_id = chart_master_table.id AND surgical_tbl.purged = '0' AND surgical_tbl.del_status='0' ".
		   "LEFT JOIN iol_master_tbl ON iol_master_tbl.form_id = chart_master_table.id AND iol_master_tbl.purged = '0' AND iol_master_tbl.del_status='0' ".
		   "LEFT JOIN oct ON oct.form_id = chart_master_table.id AND oct.purged = '0' AND oct.del_status='0' ".
		   "LEFT JOIN oct_rnfl ON oct_rnfl.form_id = chart_master_table.id AND oct_rnfl.purged = '0' AND oct_rnfl.del_status='0' ".
		   "LEFT JOIN topography ON topography.formId = chart_master_table.id AND topography.purged = '0' AND topography.del_status='0' ".
		   "LEFT JOIN test_gdx ON test_gdx.form_id = chart_master_table.id AND test_gdx.purged = '0' AND test_gdx.del_status='0' ".
		   "LEFT JOIN test_cellcnt ON test_cellcnt.formId = chart_master_table.id AND test_cellcnt.purged = '0' AND test_cellcnt.del_status='0' ".
		   "LEFT JOIN test_bscan ON test_bscan.formId = chart_master_table.id AND test_bscan.purged = '0' AND test_bscan.del_status='0' ".

		   "WHERE chart_master_table.id='".$this->fid."' ";

	//echo "<br>".$sql;
	$row = sqlQuery($sql);
	return $row;
}

function isChartCellCount(){
	$ret = 0;
	$sql = "SELECT ".
		   "c2.temp_name ".
		   "FROM chart_master_table c1 ".
		   "LEFT JOIN chart_template c2 ON c2.id=c1.templateId ".
		   "WHERE c1.id = '".$this->fid."' ";

	$row = sqlQuery($sql);
	if($row != false){
		$ret = (strtolower($row["temp_name"]) == strtolower("Cell Count")) ? 1 : 0;
	}
	return $ret;
}

function isVip(){
	$ret =  "";
	$sql = "SELECT isVip as ret FROM chart_master_table WHERE id='".$this->fid."'";
	$row = sqlQuery($sql);
	if($row != false){
		$ret = $row["ret"];
	}
	return $ret;
}

function makeChartNotesValid(){
	//update chart_master_table
	$chk = "".$this->uid.",";
	$sql2 = "UPDATE chart_master_table ".
		 "SET ".
		 "update_date = '".wv_dt('now')."', ".
		 "record_validity = '1', ".
		 "provIds=CONCAT(provIds, IF(LOCATE('".$chk."',provIds)>0,'','".$chk."')) ".
		 "WHERE id= '".$this->fid."' ";
	$res = sqlQuery($sql2);
}

function setPtMedHxReviewd(){
	$patient_id=$this->pid;
	$form_id=$this->fid;
	//Save pt. Med List Showed
	$sql = "UPDATE chart_master_table SET update_date = '".wv_dt('now')."', ptMedHxShowed='1'
			WHERE id = '".$form_id."' AND patient_id='".$patient_id."' ";
	$res = sqlQuery($sql);
}


}//End Class

?>
