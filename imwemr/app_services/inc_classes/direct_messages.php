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
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');
include_once (dirname(__FILE__).'/../../library/classes/msgConsole.php');
include_once(dirname(__FILE__)."/../../library/classes/direct_class.php");

class direct_messages extends patient_app{	
	var $reqModule;
	var $arrProvider = array();
	public function __construct($patient){
		parent::__construct($patient);
		$msgConsoleObj = new msgConsole();
		$this->arrProvider = $msgConsoleObj->pt_direct_credentials($this->authId);
	}
	public function inbox(){
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		$this->db_obj->qry = "SELECT id, from_email, subject, message, MID,
							 DATE_FORMAT(local_datetime,'%m-%d-%Y %h:%i %p') as local_datetime,
							 message,
							 is_read 	
							 FROM direct_messages 	
							 WHERE `to_email` = '".$this->arrProvider["email"]."' 
							 		AND imedic_user_id = '".$this->authId."' 
									AND del_status = 0 
									AND folder_type=1
							 ORDER BY $sort_by $sort_order
							 LIMIT $limit_from, $limit_to
							";
		$result = $this->db_obj->get_resultset_array();	
		$arrReturn = array();
		foreach($result as $key=>$arr){
			$arrReturn[$key] = $arr;
			$arrReturn[$key]['attachment'] = $this->getDirectAttachment($arr['id']);
			$arrReturn[$key]['attachment1']="";
			//add a new url for attachment
			if($arrReturn[$key]['attachment'] != ""){
				$arrReturn[$key]['attachment1'] = $this->getDirectAttachment($arr['id']);
				$arrReturn[$key]['attachment1'] = str_replace("..","", $arrReturn[$key]['attachment1']);
				
			//end
			
			} 
			$arrReturn[$key]['message'] = str_replace("\n","", $arrReturn[$key]['message']);
		}
		return $arrReturn;
	}
	public function inbox_total_count(){
		$this->db_obj->qry = "SELECT COUNT(*) AS total_count
							 FROM direct_messages	
							 WHERE 
							 `to_email` = '".$this->arrProvider["email"]."' 
							 		AND imedic_user_id = '".$this->authId."'
									AND 
							 folder_type = 1 and del_status = 0 
							";
		$result_count = $this->db_obj->get_resultset_array();
		return $result_count[0]['total_count'];
	}
	public function sent(){
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		$this->db_obj->qry = "SELECT id, to_email, from_email, subject, message, MID,
							 DATE_FORMAT(local_datetime,'%m-%d-%Y %h:%i %p') as local_datetime,
							 message
							 FROM direct_messages 	
							 WHERE `from_email` = '".$this->arrProvider["email"]."' 
							 		AND imedic_user_id = '".$this->authId."' 
									AND del_status = 0 
									AND folder_type=3
							 ORDER BY $sort_by $sort_order
							 LIMIT $limit_from, $limit_to
							";
		$result = $this->db_obj->get_resultset_array();	
		$arrReturn = array();
		foreach($result as $key=>$arr){
			$arrReturn[$key] = $arr;
			$arrReturn[$key]['attachment'] = $this->getDirectAttachment($arr['id']);
		}
		return $arrReturn;
	}
	public function sent_total_count(){
		$this->db_obj->qry = "SELECT COUNT(*) AS total_count
							 FROM direct_messages	
							 WHERE 
							 `from_email` = '".$this->arrProvider["email"]."' 
							 		AND imedic_user_id = '".$this->authId."' AND
							 folder_type = 3 and del_status = 0 
							";
		$result_count = $this->db_obj->get_resultset_array();
		return $result_count[0]['total_count'];
	}
	function getDirectAttachment($direct_msg_id)
	{
		$direct_attachments_arr = "";
		/* $rq = "SELECT CONCAT('".$GLOBALS['php_server']."/interface/main/uploaddir/users',complete_path) AS complete_path
				FROM  direct_messages_attachment 
				WHERE direct_message_id = '".$direct_msg_id."';"; */
		$rq = "SELECT CONCAT('".$this->upDir."/users',complete_path) AS complete_path
				FROM  direct_messages_attachment 
				WHERE direct_message_id = '".$direct_msg_id."';";		
		$rq_obj = imw_query($rq);
		while($direct_attachment = imw_fetch_assoc($rq_obj))
		{
			$direct_attachments_arr = $direct_attachment['complete_path'];
		}
		return $direct_attachments_arr;
	} 
	function new_message(){
		$objDirect = new Direct($this->arrProvider["email"],$this->arrProvider["email_password"]);
		$objDirect->arrMail['to_email'] = $_REQUEST['to_email'];
		$objDirect->arrMail['from_email'] = $this->arrProvider["email"];
		$objDirect->arrMail['subject'] = $_REQUEST['subject'];
		$objDirect->arrMail['body'] = $_REQUEST['body'];
		$patientId = $_REQUEST["patId"];
		$form_id = $_REQUEST["form_id"];
		//pre($objDirect->arrMail);
		$sql = "SELECT * FROM log_ccda_creation WHERE id = '".$_REQUEST['ccda_log_id']."' AND type = 1";
		$res = imw_query($sql);
		if(imw_num_rows($res)>0){
			$row = imw_fetch_assoc($res);
			$objDirect->arrMail['attachment'][] = array(
												"complete_path"=>$GLOBALS['incdir']."/main/uploaddir/users".$row['file_path'],
												"mime"=>$row['mime'],
												"file_name"=>$row['file_name'],
												"size"=>$row['size'],
												"file_path"=>$row['file_path']
												);
		}
		$MID = $objDirect->sendMail();
		if($MID != "" && $MID>0){
			$folder_type = "3";	
		}else{
			$folder_type = "2";	
		}
		if($folder_type = "3"){
			$sql_ins = "INSERT INTO direct_messages SET 
							to_email = '".$objDirect->arrMail['to_email']."',
							from_email = '".$objDirect->arrMail['from_email']."',
							subject = '".$objDirect->arrMail['subject']."',
							message = '".$objDirect->arrMail['body']."',
							folder_type = '".$folder_type."',
							MID = '".$MID."',
							del_status = 0,
							imedic_user_id = '".$this->authId."',
							local_datetime = '".date('Y-m-d H:i:s')."'
							";
							
				imw_query($sql_ins);
				$direct_message_id = imw_insert_id();
				if(isset($objDirect->arrMail['attachment']) && $direct_message_id>0){
					$complete_path = $objDirect->arrMail['attachment'][0]['file_path'];
					$file_name = $objDirect->arrMail['attachment'][0]['file_name'];
					$mime = $objDirect->arrMail['attachment'][0]['mime'];
					$size = $objDirect->arrMail['attachment'][0]['size'];
					
					if($file_name != ""){
						$this->db_obj->qry = "INSERT INTO direct_messages_attachment SET 
									direct_message_id = '".$direct_message_id."',
									file_name = '".$file_name."',
									size = '".$size."',
									mime = '".$mime."',
									complete_path = '".imw_real_escape_string($complete_path)."',
									patient_id = '".$patientId."',
									form_id = '".$form_id."'								
									";
						$this->db_obj->run_query($this->db_obj->qry);			
					}
				}
		}
		return true;
	}
	
	function reply_message(){
		$objDirect = new Direct($this->arrProvider["email"],$this->arrProvider["email_password"]);
		$objDirect->arrMail['to_email'] = $_REQUEST['to_email'];
		$objDirect->arrMail['from_email'] = $this->arrProvider["email"];
		$objDirect->arrMail['subject'] = $_REQUEST['subject'];
		$objDirect->arrMail['body'] = $_REQUEST['body'];
		$patientId = $_REQUEST["patId"];
		$form_id = $_REQUEST["form_id"];
		//pre($objDirect->arrMail);
		$sql = "SELECT * FROM log_ccda_creation WHERE id = '".$_REQUEST['ccda_log_id']."' AND type = 1";
		$res = imw_query($sql);
		if(imw_num_rows($res)>0){
			$row = imw_fetch_assoc($res);
			$objDirect->arrMail['attachment'][] = array(
												"complete_path"=>$GLOBALS['incdir']."/main/uploaddir/users".$row['file_path'],
												"mime"=>$row['mime'],
												"file_name"=>$row['file_name'],
												"size"=>$row['size'],
												"file_path"=>$row['file_path']
												);
		}
		$MID = $objDirect->sendMail();
		if($MID != "" && $MID>0){
			$folder_type = "3";	
		}else{
			$folder_type = "2";	
		}
		if($folder_type = "3"){
			$sql_ins = "INSERT INTO direct_messages SET 
							to_email = '".$objDirect->arrMail['to_email']."',
							from_email = '".$objDirect->arrMail['from_email']."',
							subject = '".$objDirect->arrMail['subject']."',
							message = '".$objDirect->arrMail['body']."',
							folder_type = '".$folder_type."',
							MID = '".$MID."',
							del_status = 0,
							reply_of = '".$_REQUEST['reply_of']."',
							imedic_user_id = '".$this->authId."',
							local_datetime = '".date('Y-m-d H:i:s')."'
							";
							
				imw_query($sql_ins);
				$direct_message_id = imw_insert_id();
				if(isset($objDirect->arrMail['attachment']) && $direct_message_id>0){
					$complete_path = $objDirect->arrMail['attachment'][0]['file_path'];
					$file_name = $objDirect->arrMail['attachment'][0]['file_name'];
					$mime = $objDirect->arrMail['attachment'][0]['mime'];
					$size = $objDirect->arrMail['attachment'][0]['size'];
					
					if($file_name != ""){
						$this->db_obj->qry = "INSERT INTO direct_messages_attachment SET 
									direct_message_id = '".$direct_message_id."',
									file_name = '".$file_name."',
									size = '".$size."',
									mime = '".$mime."',
									complete_path = '".imw_real_escape_string($complete_path)."',
									patient_id = '".$patientId."',
									form_id = '".$form_id."'								
									";
						$this->db_obj->run_query($this->db_obj->qry);			
					}
				}
		}
	}
	function get_dos(){
			$this->db_obj->qry = "SELECT id as form_id, date_of_service 
				FROM chart_master_table 
				WHERE patient_id = '".$this->patient."' order by id desc";
			$result = $this->db_obj->get_resultset_array();		
			return $result;
	}
	function create_ccda(){
		$arrDataTmp = array();
		$arrDataTmp['pat_id'] = $this->patient;
		$arrDataTmp['form_id'] = $_REQUEST['form_id'];
		$arrDataTmp['pat_name'] = $_REQUEST['txt_patient_name'];
		$arrTmp = array();
		$arrTmp[0] = $arrDataTmp;
		$_REQUEST['arrData'] = json_encode($arrTmp);
		$_REQUEST['ccdDocumentOptions'] = '';
		$_REQUEST['create_type'] = 'attachment';
		$_REQUEST['phyId'] = $_REQUEST['phyId'];
		$_REQUEST['app_services'] = 1;
		include_once(dirname(__FILE__)."/../../interface/reports/ccd/create_ccda_r2_xml.php");
		return $arr_ccda_log;
		
	}
	function create_ccda_app(){
		$arrDataTmp = array();
		$arrDataTmp['pat_id'] = $this->patient;
		$arrDataTmp['form_id'] = $_REQUEST['form_id'];
		$arrDataTmp['pat_name'] = $_REQUEST['txt_patient_name'];
		$arrTmp = array();
		$arrTmp[0] = $arrDataTmp;
		$_REQUEST['arrData'] = json_encode($arrTmp);
		//$_REQUEST['ccdDocumentOptions'] = '';
		$_REQUEST['create_type'] = 'attachment';
		$_REQUEST['phyId'] = $_REQUEST['phyId'];
		$_REQUEST['app_services'] = 1;
		include_once(dirname(__FILE__)."/../../interface/reports/ccd/create_ccda_r2_xml.php");
		$arr = $arr_ccda_log['ccda_log_id'];
		return $arr;
		
	}
	function receive_direct(){
		$objDirect = new Direct($this->arrProvider["email"],$this->arrProvider["email_password"]);	
		$objDirect->readInbox();
		foreach($objDirect->arrInbox as $arr){
			$qry = "SELECT * FROM direct_messages 
					WHERE imedic_user_id = '".$this->authId."' 
					AND MID = '".$arr['mID']."'
					AND folder_type = 1
					";
			$res = imw_query($qry);	
			if(imw_num_rows($res)<=0){
				$sql_ins = "INSERT INTO direct_messages SET 
							to_email = '".$this->arrProvider["email"]."',
							from_email = '".$arr['from']."',
							subject = '".$arr['subject']."',
							message = '".$arr['body']."',
							folder_type = '1',
							MID = '".$arr['mID']."',
							MSID = '".$arr['msID']."',
							FromUID = '".$arr['fromUID']."',
							msgSize = '".$arr['msgSize']."',
							del_status = 0,
							reply_of = '".$arr['msgSize']."',
							imedic_user_id = '".$this->authId."',
							direct_datetime = '".$arr['datTime']."',
							local_datetime = '".date('Y-m-d H:i:s')."'
							";
				imw_query($sql_ins);	
				$direct_message_id = imw_insert_id();
				foreach($arr['attachment'] as $arrAttachment){
					$sql_ins = "INSERT INTO direct_messages_attachment SET 
								direct_message_id = '".$direct_message_id."',
								file_name = '".$arrAttachment['name']."',
								size = '".$arrAttachment['size']."',
								mime = '".$arrAttachment['mime']."',
								complete_path = '".imw_real_escape_string($arrAttachment['complete_path'])."'
								";
					imw_query($sql_ins);	
				}
			}
		}
		return $this->inbox();
	}
	public function delete_messgae(){
		$this->db_obj->qry = "UPDATE direct_messages SET del_status = 1 WHERE id IN (".$_REQUEST['message_id'].") AND imedic_user_id = '".$this->authId."'";
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	
		
	}
	public function message_read(){
		$this->db_obj->qry = "UPDATE direct_messages SET is_read = 1 WHERE id = ".$_REQUEST['message_id'];
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	
	}
	function get_referring_direct($strRefPhy, $source=''){
		$result = array();
		$strRefPhy = $_REQUEST['name'];
		$strRefPhyArr = explode(";",$strRefPhy);
		$strRefPhy= trim($strRefPhyArr[0]);
		$arrTitle = array("DR","DR.", "Dr", "Dr.", "dr", "dr.", "D.R", "D.r.", "d.r", "d.r.", 
							"DO", "DO.", "Do", "Do.", "do", "do.", "D.O", "D.o.", "d.o.", 
							"OD", "OD.", "Od", "Od.", "od", "od.", "O.D", "O.d.", "o.d.", 
							"MD", "MD.", "Md", "Md.", "md", "md.", "M.D", "M.D.", "M.d.", "m.d.", 
							"MR.", "MR", "Mr", "Mr.", "mr", "mr.", "M.R.", "MR.", "M.r.", "m.r.",
							"MRS.", "Mrs.", "MRS", "Mrs", "mrs.", "mrs", "M.R.S.", "M.r.s.", "M.R.S", "m.r.s.",
							"MISS", "Miss", "MISS.", "Miss.", "miss.", "miss", "M.I.S.S.", "M.i.s.s.", "M.i.s.s.", "m.i.s.s.", 
							"MS", "MS.", "ms", "ms.", "M.s.","M.S.", "m.s", "M.s.");
							
		$arrTitleDR = array("DR","DR.", "Dr", "Dr.", "dr", "dr.", "D.R", "D.r.", "d.r", "d.r.");
		$arrTitleDO = array("DO", "DO.", "Do", "Do.", "do", "do.", "D.O", "D.o.", "d.o.");
		$arrTitleOD = array("OD", "OD.", "Od", "Od.", "od", "od.", "O.D", "O.d.", "o.d.");
		$arrTitleMD = array("MD", "MD.", "Md", "Md.", "md", "md.", "M.D", "M.D.", "M.d.", "m.d.");
		$arrTitleMR = array("MR.", "MR", "Mr", "Mr.", "mr", "mr.", "M.R.", "MR.", "M.r.", "m.r.");
		$arrTitleMRS = array("MRS.", "Mrs.", "MRS", "Mrs", "mrs.", "mrs", "M.R.S.", "M.r.s.", "M.R.S", "m.r.s.");
		$arrTitleMISS = array("MISS", "Miss", "MISS.", "Miss.", "miss.", "miss", "M.I.S.S.", "M.i.s.s.", "M.i.s.s.", "m.i.s.s.");
		$arrTitleMS = array("MS", "MS.", "ms", "ms.", "M.s.","M.S.", "m.s", "M.s.");
		
		$strPhyTitle = $strPhyLname = $strPhyFname = $strPhyMname = "";
		$intRefPhyIdDB = 0;
		$strRefPhyTitleDB = $strRefPhyFNameDB = $strRefPhyMNameDB = $strRefPhyLNameDB = $strRefPhyName = "";
		$arrRefPhy = $arrReFPhyLastName = $arrReturn = array();
		$arrRefPhy = explode(',',trim($strRefPhy));		
		foreach($arrRefPhy as $key => $value){
			$arrRefPhy[$key] = trim($value);
		}
		//--------NOT FOR BOSTON SERVER------
		if(!isset($GLOBALS["REF_PHY_FORMAT"]) || strtolower($GLOBALS["REF_PHY_FORMAT"])!='boston'){
				$arrPhyLname = explode(' ',trim($arrRefPhy[0]));
				$arrPhyFname = explode(' ',trim($arrRefPhy[1]));
				if(count($arrPhyLname) == 1){
					$strPhyLname = trim(ucfirst($arrPhyLname[0]));	
				}
				else if(count($arrPhyLname) == 2){
					$strPhyLname = trim(ucfirst(end($arrPhyLname)));	
					$strPhyTitleChk = trim(ucfirst($arrPhyLname[0]));
					if(in_array(trim($strPhyTitleChk), $arrTitle) == true){
						$strPhyTitle = trim($strPhyTitleChk);
						if(in_array(trim($strPhyTitle), $arrTitleDR) == true){
							$strPhyTitle = "Dr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleDO) == true){
							$strPhyTitle = "DO";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleOD) == true){
							$strPhyTitle = "OD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMD) == true){
							$strPhyTitle = "MD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMR) == true){
							$strPhyTitle = "Mr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMRS) == true){
							$strPhyTitle = "Mrs.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMISS) == true){
							$strPhyTitle = "Miss.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMS) == true){
							$strPhyTitle = "Ms";
						}
				//$strPhyLname = trim(ucfirst($arrReFPhyLastName[1]));
				}
					else{
						$strPhyTitle = '';
					}
				}
			
				if(count($arrPhyFname) == 1){
					$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
				}
				else{
					$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
					$strPhyMname = trim(ucfirst($arrPhyFname[1]));
				}
		}	//--------END NOT FOR BOSTON SERVER-----------------------------
		else if(isset($GLOBALS["REF_PHY_FORMAT"]) && strtolower($GLOBALS["REF_PHY_FORMAT"]) == 'boston'){ //--------FOR BOSTON SERVER------------------------
					$strPhyLname = trim(ucfirst($arrRefPhy[0]));
					$arrPhyFname = explode(' ',trim($arrRefPhy[1]));
					if(count($arrPhyFname) == 1){
						$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
					}
					else if(count($arrPhyFname) == 2){
				$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
				$strPhyMnameChk = trim(ucfirst(end($arrPhyFname)));
				if(in_array(trim($strPhyMnameChk), $arrTitle) == true){
						$strPhyTitle = trim($strPhyMnameChk);
						if(in_array(trim($strPhyTitle), $arrTitleDR) == true){
							$strPhyTitle = "Dr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleDO) == true){
							$strPhyTitle = "DO";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleOD) == true){
							$strPhyTitle = "OD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMD) == true){
							$strPhyTitle = "MD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMR) == true){
							$strPhyTitle = "Mr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMRS) == true){
							$strPhyTitle = "Mrs.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMISS) == true){
							$strPhyTitle = "Miss.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMS) == true){
							$strPhyTitle = "Ms";
						}
						//$strPhyLname = trim(ucfirst($arrReFPhyLastName[1]));
				}else{
						$strPhyMname = trim(ucfirst(end($arrPhyFname)));
				}
			}
					else if(count($arrPhyFname) == 3){
				$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
				$strPhyMname = trim(ucfirst($arrPhyFname[1]));
				$phyTitle = trim(ucfirst(end($arrPhyFname)));
				if(in_array(trim($phyTitle), $arrTitle) == true){
						$strPhyTitle = trim($phyTitle);
						if(in_array(trim($strPhyTitle), $arrTitleDR) == true){
							$strPhyTitle = "Dr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleDO) == true){
							$strPhyTitle = "DO";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleOD) == true){
							$strPhyTitle = "OD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMD) == true){
							$strPhyTitle = "MD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMR) == true){
							$strPhyTitle = "Mr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMRS) == true){
							$strPhyTitle = "Mrs.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMISS) == true){
							$strPhyTitle = "Miss.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMS) == true){
							$strPhyTitle = "Ms";
						}
						//$strPhyLname = trim(ucfirst($arrReFPhyLastName[1]));
				}
			}
		}//--------END FOR BOSTON SERVER-----------------------------
			$strMqry = "";
			if(empty($strPhyMname) == false){
				$strMqry = " and MiddleName like '".addslashes($strPhyMname)."%' ";
			}
			if((empty($strPhyLname) == false)){
			$this->db_obj->qry = "select physician_Reffer_id,";
			if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
				$this->db_obj->qry .= "CONCAT(IF(Title!='',CONCAT(Title, ' '),''),
											  IF(LastName!='',CONCAT(LastName, ', '),''),
											  IF(FirstName!='',CONCAT(FirstName, ' '),''),
											  IF(MiddleName!='',MiddleName,'')
										) AS name,
										";
			}
			else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
					$this->db_obj->qry .= "CONCAT(
											  IF(LastName!='',CONCAT(LastName, ', '),''),
											  IF(FirstName!='',CONCAT(FirstName, ' '),''),
											  IF(MiddleName!='',CONCAT(MiddleName,' '),''),
											  IF(Title!='',Title,'')
										) AS name,
										";
				}
				
			$this->db_obj->qry .= "direct_email from refferphysician where LastName like '".addslashes($strPhyLname)."%' "; 
				if($strPhyFname != "")
				$this->db_obj->qry .= "and FirstName like '".addslashes($strPhyFname)."%'";
				$this->db_obj->qry .= " ".$strMqry." and (delete_status = 0  or delete_status = 2 ) AND direct_email != '' ORDER BY delete_status ASC";
			$result = $this->db_obj->get_resultset_array();		
			
		}
		return $result;
	}
	function getRefPhyName($id){
		$strName = '';
		
		if(!empty($id)){
			$qry = "select Title,FirstName,MiddleName,LastName from refferphysician where physician_Reffer_id = ".$id;
			$res = imw_query($qry);
			if(imw_num_rows($res)>0){
				$row = imw_fetch_assoc($res);
				if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
					$strName .= $row['Title'] != '' ? trim($row['Title']).' ':'';
					$strName .= $row['LastName'] != '' ? trim($row['LastName']).', ':'';
					$strName .= $row['FirstName'] != '' ? trim($row['FirstName']).' ':'';
					$strName .= $row['MiddleName'] != '' ? trim($row['MiddleName']):'';
				}
				else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
					$strName .= $row['LastName'] != '' ? trim($row['LastName']).', ':'';
					$strName .= $row['FirstName'] != '' ? trim($row['FirstName']).' ':'';
					$strName .= $row['MiddleName'] != '' ? trim($row['MiddleName']).' ':'';
					$strName .= $row['Title'] != '' ? trim($row['Title']):'';
				}
			}
		}
		return $strName;
	}
	function ccda_viewer(){
		$ccda_file = $_REQUEST['attachment_url'];
		$arrName = explode("/",$ccda_file);
		$file_name = end($arrName);
		if(strpos($file_name,".zip") !== false){
			$folder_name = str_replace(".zip","",$file_name);
			$zip = new ZipArchive;
			if($zip->open($ccda_file) == TRUE){
				for($i=0; $i<$zip->numFiles; $i++){
					$name = $zip->getNameIndex($i);
					
					if(strpos($name,".xml") !== false){
						$content = $zip->getFromIndex($i);
					
						
					}
				}
			}
		}else{
			$content = file_get_contents ($ccda_file);
		}
		
		$proc=new XSLTProcessor();
		$dom = new DOMDocument;
		$proc->importStylesheet($dom->load("CDA.xsl")); //load XSL script
		$html = $proc->transformToXML($dom->loadXML($content)); //load XML file and echo
		echo $html;
		die();
	}
}

?>