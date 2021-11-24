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
File: dicom_db.php
Purpose: This class file provides functions to save values to application database.
Access Type : Include file
*/
?>
<?php
class dicom_db extends dicom_utility{

	function get_real_test_name($str){
		$sql = "SELECT test_name FROM `tests_name` WHERE del_status='0' and status='1' AND temp_name LIKE '".$str."'";
		$row = sqlQuery($sql);
		if($row!=false){
			if(!empty($row["test_name"])){$str = strtoupper($row["test_name"]); }
		}
		return $str;
	}
	
	function get_pt_studies($pid){
		$str = "";
		$sql = "SELECT dicom_images.seq, appt_date, firstname, lastname, sent_from_ae, tags, 
					dicom_studies.study_path as sp1, dicom_images.study_path  as sp2 FROM dicom_studies 
				INNER JOIN  dicom_images ON study_seq = dicom_studies.seq
				WHERE patient_id = '".$pid."' ";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez); $i++){
			extract($row);
			$appt_date = wv_formatDate($appt_date);
			if(!empty($firstname)){
			$nm = $firstname." ".$lastname;
			
			$sp = !empty($sp2) ? $sp2 : $sp1;  
			if(file_exists($sp)){
				$nm = "<a class=\"text-primary\" href=\"dcm_data.php?dsid=".$seq."\">".$nm."</a>";
			}
			
			$str .= "<tr>
					<td>".$i."</td>
					<td>".$nm."</td>
					<td>".$appt_date."</td>
					<td>".$sent_from_ae."</td>
					<td>".$tags."</td>
					</tr>";
			}
		}
		
		if(!empty($str)){
			$str = "<div class=\"table-responsive\"><table class=\"table table-bordered\" ><tr><td>Sr.</td><td>Name</td><td>App.Date</td><td>Sent From</td><td>Tags</td></tr>".$str."</table></div>";
		}else{ $str = "No Record Found!"; }
		return $str;
	}
	
	function get_file_path($seq){		
		$ret = "";
		$sql = "SELECT dicom_studies.study_path as sp1, dicom_images.study_path  as sp2 FROM dicom_images
				INNER JOIN dicom_studies ON dicom_images.study_seq = dicom_studies.seq
				WHERE dicom_images.seq = '".$seq."' ";
		$row = sqlQuery($sql);
		if($row!=false){ 
			$ret = $row["sp1"];
			if(empty($ret)){ $ret = $row["sp2"]; }	
		}
		return $ret;
	}
	
	function get_test_order_by($pid, $exam_dt){
		if(empty($pid)){ return array('0', '0000-00-00'); }
		$exam_dt = date( "Y-m-d", strtotime( $exam_dt ));
		
		$crdt = $exam_dt;
		if(empty($crdt) || $crdt=="0000-00-00"){ $crdt = "".date("Y-m-d")."";   }
		
		//get Patient Doctor Appointment for  testing date / Today
		$sql = "SELECT c1.sa_doctor_id, c2.user_type FROM schedule_appointments c1
				INNER JOIN users c2 ON c1.sa_doctor_id=c2.id
				WHERE sa_patient_id='".$pid."' AND sa_app_start_date = '".$crdt."' ";
		$res = sqlStatement($sql);
		
		for($i=1; $row=sqlFetchArray($res); $i++){
			if(!empty($row["sa_doctor_id"])){
				$id = $row["sa_doctor_id"];
				//
				if(!empty($id)){
					//Get Type
					$utype = $row["user_type"];
					
					if(in_array($utype,$GLOBALS['arrValidCNPhy'])){
						return array($id, $exam_dt);
					}
				}
			}
		}
		
		$opt = new Patient($pid);
		$id = $opt->getPtPrimaryPhy();
		if(empty($id)){ $exam_dt="0000-00-00"; }
		return array($id, $exam_dt);
	}

	function getPtIdFromMrn($fld, $val, $nm="", $dob="", $sex="", $adrs="", $tel=""){
		$sql = "SELECT id, fname, mname, lname, DOB, sex  FROM patient_data WHERE ".$fld." = '".$val."' AND id!='".$val."' AND patientStatus='Active' "; //imdicom
		$rez = sqlStatement($sql);
		$num = imw_num_rows($rez);			
		if($num==0){ //asheville	
			$sql = "SELECT id, fname, mname, lname, DOB, sex FROM patient_data WHERE ".$fld." = '0".$val."' AND id!='".$val."' AND patientStatus='Active' "; //imdicom
			$rez = sqlStatement($sql);
		}	
		
		$pid=0;
		//if more than one records
		$num = imw_num_rows($rez);
		if($num > 1){
			//$this->logger("IN pass 1");	
			$cntr_lm=0; $pid_p=0;
			for($i=1; $row=sqlFetchArray($rez); $i++){
				
				$cntr=0;				
				$fn_t = trim($row["fname"]);				
				if(!empty($nm) && stripos($nm,$fn_t)!==false){				
					$cntr+=1;
				}
				
				$mn_t = trim($row["mname"]);				
				if(!empty($nm) && stripos($nm,$mn_t)!==false){				
					$cntr+=1;
				}
				
				$ln_t = trim($row["lname"]);				
				if(!empty($nm) && stripos($nm,$ln_t)!==false){				
					$cntr+=1;
				}
				
				$dob_t = trim($row["DOB"]);
				if(!empty($dob)){
					$dob_t = str_replace("-","",$dob_t);
					if($dob_t==$dob){ $cntr+=1; }
				}
				
				$sex_t = strtoupper($row["sex"]);
				if($sex_t=="MALE"){$sex_t="M";}else if($sex_t=="FEMALE"){$sex_t="F";}
				if(!empty($sex) && stripos($sex,$sex_t)!==false){				
					$cntr+=1;
				}
				
				/*
				$tel_1 = trim($row["phone_cell"]);
				$tel_2 = trim($row["phone_home"]);
				$tel_3 = trim($row["phone_biz"]);
				$tel_4 = trim($row["phone_contact"]);
				if(!empty($tel) && ($tel == $tel_1 || $tel == $tel_2 || $tel == $tel_3 || $tel == $tel_4)){				
					$cntr+=1;
				}
				
				$street_t = $row["street"]; 
				$street2_t = $row["street2"];
				$postal_code_t = $row["postal_code"];
				$city_t = $row["city"]; 
				$state_t = $row["state"];
				$country_code_t = $row["country_code"];				
				if(!empty($adrs) && (stripos($adrs,$street_t)!==false || stripos($adrs,$street2_t)!==false || stripos($adrs,$postal_code_t)!==false ||
								stripos($adrs,$city_t)!==false || stripos($adrs,$state_t)!==false || stripos($adrs,$country_code_t)!==false) ){
					$cntr+=1;
				}
				*/
				
				//first record's id will be set as default
				if($i==1){ $pid_p=$row["id"]; }
				//
				if($cntr>0 && $cntr>$cntr_lm){
					$pid_p=$row["id"];
					$cntr_lm = $cntr;
				}
			}
			
			//
			if(!empty($pid_p)){ $pid = $pid_p; 	}
			
		}else if($num>0){
			$row=sqlFetchArray($rez);
			$pid = $row["id"];			
		}else{
			//check with patient id
			$sql = "SELECT id FROM patient_data WHERE  id='".$val."' AND patientStatus='Active' "; //imdicom
			$row = sqlQuery($sql);
			if($row != false){ $pid = $row["id"]; }			
		}		
		return $pid;
	}

	function getUserId(){
		$sql = "SELECT id FROM users WHERE username = 'imdicom' OR fname='DICOM' LIMIT 0,1 "; //imdicom
		$row = sqlQuery($sql);
		if($row != false){
			return $row["id"];
		}
		return 0;
	}
	
	function saveDicomOutPut($testId,$formName, $img){
		
		if(empty($testId)||empty($formName)){ return "1"; }
		$pid = $img["id"];
		$filename = sqlEscStr(basename($img["imgpath_output"]));
		$filetype = (!empty($img['MIMETypeOfEncapsulatedDocument'])) ? sqlEscStr($img['MIMETypeOfEncapsulatedDocument']) : sqlEscStr($img['mime_type']);
		$file_pointer = $img["imgpath_output"];
		$operator_id = $img["operator_id"]; //No Getting yet in DCM File
		
		//move file to patient forllder
		if(!empty($img["imgpath_output_savepath"])){
			if(copy($img["imgpath_output"],$img["imgpath_output_savepath"])){				
				$file_pointer = $img["imgpath_output_path_pointer"];
				unlink($img["imgpath_output"]);
			}
		}
		
		//acq dt tm
		$created_date=$img['image_acq_date_tm'];
		if(empty($created_date)||$created_date=="00000000000000"){ $created_date=$img['image_date']; }
		$tm_crt_dt = strtotime($created_date);
		if(empty($tm_crt_dt)){ 
			$created_date = date('Y-m-d H:i:s');
			$tm_crt_dt = strtotime($created_date);
		}
		
		//PDF to store
		$sql = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scans 
				SET
				patient_id = '".$pid."',".
				"test_id = '".$testId."', ".
				"image_form = '".$formName."', ".
				"image_name = '".$filename."',
				file_type = '".$filetype."',
				scan_or_upload='upload',
				doc_upload_date='".$created_date."',
				modi_date='".$tm_crt_dt."',
				created_date='".$created_date."',
				file_path = '".$file_pointer."',
				operator_id='$operator_id'
				";			
				
		$insertD=sqlInsert($sql);
		if(empty($insertD)){
			$this->logger("Query Failed:: ".$sql);
		}
		
		//START CODE TO ADD THUMBNAIL FOR TESTS
		$fileInfoArr 		= pathinfo($img["imgpath_output_savepath"]);
		$extArr 			= array("jpg","jpeg","png","gif","tif","tiff");
		if(trim($testId) && trim($formName) && (strtolower($filetype) == "application/pdf" || strtolower($fileInfoArr['extension']) == 'pdf' || strtoupper($fileInfoArr['extension']) == 'PDF' || in_array(strtolower($fileInfoArr['extension']),$extArr))){
			
			$osavefile = new SaveFile();
			$up_path = $osavefile->getUploadDirPath();
			
			$imgSrc_file 	= $up_path.$file_pointer;
			if(file_exists($imgSrc_file)){
				$pdf_info 			= pathinfo($imgSrc_file);
				$pdf_basename 		= $pdf_info['basename'];
				$pdf_dir	  		= $pdf_info['dirname'];
				$pdf_name	  		= $pdf_info['filename'];
				$pdf_thumbnail_dest	= $pdf_dir."/thumbnail";
				$pdf_thumb_dest		= $pdf_dir."/thumb";
				$pdf_jpg_dest		= $pdf_dir."/".$pdf_name.".jpg";
				
				$pdf_jthumbnail_dest= $pdf_thumbnail_dest."/".$pdf_name.".jpg";
				$pdf_jthumb_dest	= $pdf_thumb_dest."/".$pdf_name.".jpg";
				
				if(is_dir($pdf_thumb_dest) == false){
					mkdir($pdf_thumb_dest, 0777, true);
				}
				if(is_dir($pdf_thumbnail_dest) == false){
					mkdir($pdf_thumbnail_dest, 0777, true);
				}
				$source 			= realpath($pdf_dir."/".$pdf_basename).'[0]';
				$exe_path 			= $GLOBALS['IMAGE_MAGIC_PATH'];
				if(!empty($exe_path)){$exe_path .= "/";}else{$exe_path='';}	
				
				if (!file_exists($pdf_jpg_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
					exec($exe_path.'convert -density 300 -flatten "'.$source.'" -quality 95 -thumbnail 1500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jpg_dest.'"');
				}
				if (!file_exists($pdf_jthumbnail_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
					exec($exe_path.'convert -flatten "'.$source.'" -quality 95 -thumbnail 78 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumbnail_dest.'"');
				}
				if (!file_exists($pdf_jthumb_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
					exec($exe_path.'convert -flatten "'.$source.'" -quality 95 -thumbnail 500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumb_dest.'"');
				}									
			}
		}
		//END CODE TO ADD THUMBNAIL FOR TESTS		
		
	}
	
	function saveCellCount($img){
		$testName = "CellCount";
		//*
			
		//
		//$sql = "SELECT test_cellcnt_id FROM test_cellcnt WHERE patientId='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";
		$sql = "SELECT test_cellcnt_id FROM test_cellcnt WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["test_cellcnt_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);			
			$sql = "INSERT INTO test_cellcnt 
					(  
					examDate,
					patientId, cur_date, examTime,
					performedBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	
	}
	
	function saveAscan($img){
		$testName = "Ascan";
		//*
			
		//
		//$sql = "SELECT surgical_id FROM surgical_tbl WHERE patient_id='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT surgical_id FROM surgical_tbl WHERE patient_id='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."'  ";	//".sqlEscStr($img["study_uid"])."		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["surgical_id"];			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO surgical_tbl 
					(  
					examDate,
					patient_id, examTime	,
					performedByOD, performedByOS	, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 				
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);		
		
		}
		
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/	
	}
	
	function saveBscan($img){
		$testName = "BScan";
		//*
			
		//
		//$sql = "SELECT test_bscan_id FROM test_bscan WHERE patientId='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT test_bscan_id FROM test_bscan WHERE patientId='".$img["id"]."' AND  study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["test_bscan_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO test_bscan 
					(  
					examDate,
					patientId, cur_date, examTime,
					performedBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	
	}

	function saveFundus($img){
		$testName = "Disc";
		//*
			
		//
		//$sql = "SELECT disc_id FROM disc WHERE patientId='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT disc_id FROM disc WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["disc_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO disc 
					(  
					examDate,
					patientId, cur_date, examTime,
					performedBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}
	
	function saveExternal($img){
		$testName = "discExternal";
		//*
			
		//
		//$sql = "SELECT disc_id FROM disc WHERE patientId='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT disc_id FROM disc_external WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["disc_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO disc_external 
					(  
					examDate,
					patientId, cur_date, examTime,
					performedBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}

	function saveGDXHRT($img){
		$testName = "GDX";
		//*
			
		//
		//$sql = "SELECT gdx_id FROM test_gdx WHERE patient_id='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT gdx_id FROM test_gdx WHERE patient_id='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["gdx_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO test_gdx 
					(  
					examDate,
					patient_id, cur_date, examTime,
					performBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}

	function saveICG($img){
		$testName = "ICG";
		//*
			
		//
		//$sql = "SELECT icg_id FROM icg WHERE patient_id='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT icg_id FROM icg WHERE patient_id='".$img["id"]."' AND study_uid!='' AND exam_date='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["icg_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO icg 
					(  
					exam_date,
					patient_id, cur_date, examTime,
					performed_by, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}

	function saveIOLMaster($img){
		$testName = "IOL_Master";
		//*
			
		//
		//$sql = "SELECT iol_master_id FROM iol_master_tbl WHERE patient_id='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT iol_master_id FROM iol_master_tbl WHERE patient_id='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["iol_master_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO iol_master_tbl 
					(  
					examDate,
					patient_id, examTime,
					performedByOD, performedByOS, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 					 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	
	}

	function saveIVFA($img){
		$testName = "IVFA";
		//*
			
		//
		//$sql = "SELECT vf_id FROM ivfa WHERE patient_id='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT vf_id FROM ivfa WHERE patient_id='".$img["id"]."' AND study_uid!='' AND exam_date='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["vf_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO ivfa 
					(  
					exam_date,
					patient_id, cur_date, examTime,
					performed_by, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}

	function saveOCT($img){
		$testName = "OCT";
		//*
			
		//
		//$sql = "SELECT oct_id FROM oct WHERE patient_id='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT oct_id FROM oct WHERE patient_id='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["oct_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO oct 
					(  
					examDate,
					patient_id, cur_date, examTime,
					performBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}


	function saveOCTRNFL($img){
		$testName = "OCT-RNFL";
		//*
			
		//
		//$sql = "SELECT oct_rnfl_id FROM oct_rnfl WHERE patient_id='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT oct_rnfl_id FROM oct_rnfl WHERE patient_id='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["oct_rnfl_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO oct_rnfl 
					(  
					examDate,
					patient_id, cur_date, examTime,
					performBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}

	function saveTopography($img){
		$testName = "Topogrphy";
		//*
			
		//
		//$sql = "SELECT topo_id FROM topography WHERE patientId='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT topo_id FROM topography WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["topo_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO topography 
					(  
					examDate,
					patientId, cur_date, examTime,
					performedBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}

	function saveVF($img){
		$testName = "VF";
		//*
			
		//
		//$sql = "SELECT vf_id FROM vf WHERE patientId='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT vf_id FROM vf WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["vf_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO vf 
					(  
					examDate,
					patientId, examTime,
					performedBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."',
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";
			
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}

	function saveVFGL($img){
		$testName = "VF-GL";
		//*
			
		//
		//$sql = "SELECT vf_gl_id FROM vf_gl WHERE patientId='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT vf_gl_id FROM vf_gl WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["vf_gl_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO vf_gl 
					(  
					examDate,
					patientId, examTime,
					performedBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}
	
	function savePachy($img){
		$testName = "Pacchy";
		//*
			
		//
				
		$sql = "SELECT pachy_id FROM pachy WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ";		
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["pachy_id"];
			
		}
		
		if(!isset($insertD) || empty($insertD)){
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO pachy 
					(  
					examDate,
					patientId, examTime,
					performedBy, study_uid, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";		
			$insertD=sqlInsert($sql);	

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
	
	}
	
	function getTemplateId($str){
		$id=0;
		if(!empty($str)){
			$sql = " SELECT id FROM  tests_name WHERE (test_name='".$str."' OR temp_name='".$str."') AND status=1 AND test_type=1 ";
			$row = sqlQuery($sql);
			if($row != false){
				$id=$row["id"];
			}
		}
		return $id;
	}

	function saveTestOther($img){
		$testName = "TestOther";
		//*		

		//--
		//if Empty set default
		$img['StationName'] = trim($img['StationName']);
		if(empty($img['StationName'])){
			$img['StationName'] = $testName;
		}
		
		$test_other = $img['StationName'];
		
		//check Test Template : if any template is made with station name
		$tempId = $this->getTemplateId($img['StationName']);			
		if(!empty($tempId)){ $test_other = "TemplateTests"; $testName = "TemplateTests"; $chkPhrse=" AND test_template_id='".$tempId."' "; }

		//--

		//
		//$sql = "SELECT test_other_id FROM test_other WHERE patientId='".$img["id"]."' AND study_uid='".sqlEscStr($img["study_uid"])."' ";		
		$sql = "SELECT test_other_id, test_template_id FROM test_other WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ".$chkPhrse." ";
		$row = sqlQuery($sql);
		if($row != false){
			$insertD=$row["test_other_id"];
			if(!empty($row["test_template_id"])){	$testName = "TemplateTests";	}
		}else{
			//20-09-2016: For OKEI --
			$sql = "SELECT test_other_id, test_template_id FROM test_other WHERE patientId='".$img["id"]."' AND (study_uid='' OR study_uid IS NULL) AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' ".$chkPhrse." ";
			$row = sqlQuery($sql);
			if($row != false){
				$insertD=$row["test_other_id"];
				if(!empty($row["test_template_id"])){	$testName = "TemplateTests";	}
			}
			//--
		}
		
		if(!isset($insertD) || empty($insertD)){			
			list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
			$sql = "INSERT INTO test_other 
					( 
					test_other,
					examDate,
					patientId, cur_date, examTime,
					performedBy, study_uid, test_template_id, ordrby, ordrdt
					)
					VALUES(
					'".sqlEscStr($test_other)."',
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["id"])."', 
					'".sqlEscStr($img["image_date"])."', 
					'".sqlEscStr($img["image_date"])."',
					'".sqlEscStr($img["operator_id"])."',
					'".sqlEscStr($img["study_uid"])."', '".$tempId."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."'
					)";
			$insertD=sqlInsert($sql);

		}
		$this->saveDicomOutPut($insertD, $testName, $img);
		
		//*/
	}
	
	function saveCustomTest($img){
		
		$sql = "SELECT * FROM tests_name WHERE id ='".$img["id_custm_test"]."' ";		
		$row = sqlQuery($sql);
		if($row!=false){
			$testName = $row["temp_name"];
			$version = $row["version"];
			
			 //test_custom_patient
			$sql = "SELECT test_id FROM test_custom_patient WHERE patientId='".$img["id"]."' AND study_uid!='' AND examDate='".date( "Y-m-d", strtotime( $img["image_date"] ))."' AND test_template_id='".$img["id_custm_test"]."' AND version='".$version."' ";
			$row = sqlQuery($sql);
			if($row != false){
				$insertD=$row["test_id"];
			}
			
			//insert
			if(!isset($insertD) || empty($insertD)){
				if(!empty($version)){
				list($order_by, $order_dt) = $this->get_test_order_by($img["id"], $img["image_date"]);
				$sql = "INSERT INTO test_custom_patient 
						(  
						examDate,
						patientId, examTime,
						performedBy, study_uid, ordrby, ordrdt,
						test_template_id, version
						)
						VALUES(
						'".sqlEscStr($img["image_date"])."',
						'".sqlEscStr($img["id"])."', 
						'".sqlEscStr($img["image_date"])."',
						'".sqlEscStr($img["operator_id"])."',
						'".sqlEscStr($img["study_uid"])."', '".sqlEscStr($order_by)."', '".sqlEscStr($order_dt)."',
						'".sqlEscStr($img["id_custm_test"])."','".sqlEscStr($version)."'
						)";	
				$insertD=sqlInsert($sql);
				}
			}
			$this->saveDicomOutPut($insertD, "CustomTests", $img);
		}
	}
	
	function is_custom_test_exists($tst){
		$ret=0;
		$sql = "SELECT id, version  FROM tests_name WHERE temp_name LIKE '".$tst."' AND del_status='0' AND status = '1' ORDER BY id ";		
		$row = sqlQuery($sql);
		if($row!=false){
			if(!empty($row["version"])){ $ret=$row["id"]; }
		}		
		return $ret; 
	}
	
	function get_mapping_rules($tmp_station, $img, $path_map){		
		$ret_name="";
		if(!empty($path_map)){			
			//$str = file_get_contents($path_map);
			$str = trim($path_map);
			if(!empty($str)){					
				$ar_str = explode("!!~~!!", $str);
				$ar_con_checks = array();
				if(count($ar_str)>0){					
					foreach($ar_str as $k => $v){
						$v = trim($v);
						if(!empty($v)){
							$ar_v = explode("!~!", $v);							
							$tag_name = trim($ar_v[0]);
							$tag_value = trim($ar_v[1]);
							$test_name = trim($ar_v[2]);
							$condition_test_name = trim($ar_v[3]);
							
							
							if(!empty($tag_name) && empty($condition_test_name)){
								
								
								if(!empty($img[$tag_name]) && strtolower($img[$tag_name]) == strtolower($tag_value)){
									
									$ret_name=$test_name;
								}
								
							}else{
								$ar_con_checks[] = $ar_v;
							}
						}
					}
				}
				
				if(empty($ret_name)){ $ret_name=trim($tmp_station); }
				
				if(!empty($ret_name)){
					$ret_name = trim($ret_name);
					//check conditional
					if(count($ar_con_checks) > 0){
					
						foreach($ar_con_checks as $k => $v){							
							if(count($v)>0){
								$tag_name = trim($v[0]);
								$tag_value = trim($v[1]);
								$test_name = trim($v[2]);
								$condition_test_name = trim($v[3]);
								
								if(!empty($condition_test_name) && strtolower($condition_test_name) == strtolower($ret_name)){
									
									if(!empty($img[$tag_name]) && strtolower($img[$tag_name]) == strtolower($tag_value)){
									$ret_name=$test_name;
									}
								}
							}
						}
					}
				}
			}
		}
		
		if(empty($ret_name)){ $ret_name=trim($tmp_station); }
		
		$ret_name = strtoupper($ret_name);
		return  $ret_name;
	}
	
	function getTagsStr($ar){
		$str="";$ret=array();
		$ar_at = array('StationName', 'SeriesDescription', 'CodeMeaning', 'DocumentTitle', 'ManufacturerModelName', 
					'study_desc', 'Manufacturer', 'SourceApplicationEntityTitle',
					'ImageType','DeviceSerialNumber');
		foreach($ar_at as $k => $v){
			if(isset($ar[$v]) && !empty($ar[$v])){ $ret[$v] = $ar[$v];  }
		}
		$str = json_encode($ret);
		return $str; 
	}	

	function saveDcm($img){
	
		global $web_RootDirectoryName, $myExternalIP, $dfp;
	
		//
		foreach($img as $key => $value) {
		  $img["$key"] = sqlEscStr($value);
		}		
		
		$sent_from_ae=$img["sent_from_ae"];
		$sent_to_ae=$img["sent_to_ae"];		
		
		$tags = $this->getTagsStr($img);
		
		$study_seq = 0; // This will store the existing study if found
		$sql = "SELECT seq FROM dicom_studies WHERE study_uid = '" . $img['study_uid'] . "' AND accession = '" . $img['accession'] .
		       "' AND patient_id = '" . $img['id'] . "' AND lastname = '" . $img['lastname'] . "' AND sent_from_ae = '$sent_from_ae' LIMIT 1" ;
		
		$row =sqlQuery($sql);
		if($row['seq']) {
		  $study_seq = $row['seq']; // Found and existing study
		}
		else {
			
		  // SQL INSERT
		  $sql = "INSERT INTO dicom_studies(" .
		    "`firstname`, " .
		    "`lastname`, " .
		    "`patient_id`, " .
		    "`appt_date`, " .
		    "`dob`, " .
		    "`study_uid`, " .
		    "`study_desc`, " .
		    "`accession`, " .
		    "`history`, " .
		    "`institution`, " .
		    "`sent_from_ae`, " .
		    "`sent_to_ae`, " .
		    " study_path ".		   
		    ") VALUES (" .
		    "\"".$img['firstname']."\", " .
		    "\"".$img['lastname']."\", " .
		    "\"".$img['id']."\", " .
		    "\"".$img['appt_date']."\", " .
		    "\"".$img['dob']."\", " .
		    "\"".$img['study_uid']."\", " .
		    "\"".$img['study_desc']."\", " .
		    "\"".$img['accession']."\", " .
		    "\"".$img['history']."\", " .
		    "\"".$img['institution']."\", " .
		    "\"".$sent_from_ae."\", " .
		    "\"".$sent_to_ae."\", " .
		    "\"".$img['imgpath_dcm']."\" " .		   
		    ")";
		  
		  $study_seq = sqlInsert($sql);
		  /*
		  // get seq
		  $sql = "SELECT seq FROM dicom_studies WHERE study_uid = '" . $img['study_uid'] . "' AND accession = '" . $img['accession'] .
		    "' AND patient_id = '" . $img['id'] . "' AND lastname = '" . $img['lastname'] . "' ORDER by seq DESC LIMIT 1" ;		  
		  $row =sqlQuery($sql);
		  if(!empty($row['seq'])) {
		    $study_seq = $row['seq'];
		  }
		  */
		}

		// The images know what study they belong to by the study_seq, if it doesn't exist at this point something went wrong.
		if(empty($study_seq)) {
		  $this->logger("There is no study_seq to assign to the image.");
		  return 5 ;
		}

		// sql for the image
		$sql = "INSERT INTO dicom_images(" .
		  "`study_seq`, " .
		  "`series_number`, " .
		  "`instance_number`, " .
		  "`sop_instance`, " .
		  "`transfer_syntax`, " .
		  "`body_part_examined`, " .
		  "`image_date`, " .
		  "`modality`, " .
		  "`study_path`, " .
		  "`tags` " .	
		  "".
		  ") VALUES (" .
		  "\"$study_seq\", " .
		  "\"".$img['series_number']."\", " .
		  "\"".$img['instance_number']."\", " .
		  "\"".$img['sop_instance']."\", " .
		  "\"".$img['transfer_syntax']."\", " .
		  "\"".$img['body_part_examined']."\", " .
		  "\"".$img['image_date']."\", " .
		  "\"".$img['modality']."\", " .
		  "\"".$img['imgpath_dcm']."\", " .
		  "\"".sqlEscStr($tags)."\" " .
		  ")";
		$row =sqlQuery($sql);
	//END DEFAULT SAVING
		
		//Add in chart Note --
		$tmp_station = trim("".$img['StationName']);
		$tmp_station = strtoupper($tmp_station);
		
		//SeriesDescription
		$tmp_SeriesDesc = strtoupper($img['SeriesDescription']);
		
		// 
		if(strpos($tmp_SeriesDesc,"CIRCLE")!==false){
			$tmp_station = "OCT-RNFL";
		}		
		
		//CodeMeaning
		$tmp_CodeMeaning = strtoupper($img['CodeMeaning']);
		if($tmp_CodeMeaning == "FUNDUS CAMERA"){ $tmp_station = "FUNDUS";  }
		elseif($tmp_CodeMeaning == "FLUORESCEIN"){ $tmp_station = "IVFA"; }

		if(stripos($tmp_station, "CIRRUS HD-OCT") !== false){ 
			$tmp_station="OCT";
		}
		
		//set between oct or oct-rnl
		if($tmp_station == "OCT"){
			if(!empty($img['DocumentTitle'])){
				if(stripos($img['DocumentTitle'],"RNFL")!==false || stripos($img['DocumentTitle'],"Guided Progression Analysis")!==false){
					$tmp_station="OCT-RNFL";
				}else if(stripos($img['DocumentTitle'],"Retina")!==false){
					$tmp_station="OCT";
				}
			}
		} 
		
		if(stripos($img['ManufacturerModelName'], "HFA") !== false){$tmp_station="VF";}
		
		if($tmp_station!="A-SCAN" && $tmp_station!="B-SCAN" && $tmp_station!="CELL COUNT" && $tmp_station!="FUNDUS" && $tmp_station!="GDXHRT" &&
			$tmp_station!="GDX" && $tmp_station!="ICG" && $tmp_station!="IOLMASTER" && $tmp_station!="IOL MASTER" && 
			$tmp_station!="IVFA" && $tmp_station!="OCT" && $tmp_station!="CIRRUS" && $tmp_station!="PACHY" &&
			$tmp_station!="OCT-RNFL" && $tmp_station!="TOPOGRAPHY" && $tmp_station!="VF" && $tmp_station!="VF-GL"){
			
			if(stripos($img['StationName'], "Atlas 9000-3465") !== false || stripos($img['StationName'], "Atlas 9000") !== false){	$tmp_station="TOPOGRAPHY";	}
			else if(stripos($img['StationName'], "Iolm") !== false){	$tmp_station="IOL MASTER";	}
			else if(stripos($img['StationName'], "3d-oct-1") !== false){	$tmp_station="OCT";	}//alkaff
			else if(!$img['StationName'] || $img['StationName']==""){$tmp_station="VF";}
			
		}		
		
		if(!empty($dfp) && file_exists($dfp."/dicom_map_rules.php")){
			include($dfp."/dicom_map_rules.php");
			
			$tmp_station=$this->get_mapping_rules($tmp_station, $img, $mapping_str);
			$img['StationName']=$tmp_station;
		}
		
		//Check custom test if exists -- 
		$id_custm_test = $this->is_custom_test_exists($tmp_station);
		if(!empty($id_custm_test)){	
			
			$tmp_station="CUSTOM_TEST";
			$img["id_custm_test"] = $id_custm_test;
		}else if($tmp_station!="A-SCAN" && $tmp_station!="B-SCAN" && $tmp_station!="CELL COUNT" && $tmp_station!="FUNDUS" && $tmp_station!="GDXHRT" &&
			$tmp_station!="GDX" && $tmp_station!="ICG" && $tmp_station!="IOLMASTER" && $tmp_station!="IOL MASTER" && 
			$tmp_station!="IVFA" && $tmp_station!="OCT" && $tmp_station!="CIRRUS" && $tmp_station!="PACHY" &&
			$tmp_station!="OCT-RNFL" && $tmp_station!="TOPOGRAPHY" && $tmp_station!="VF" && $tmp_station!="VF-GL"){			
			$tmp_station=$this->get_real_test_name($tmp_station);
		}
		//--
		
		switch($tmp_station){
			case "CUSTOM_TEST":	
				$this->saveCustomTest($img);
			break;

			case "A-SCAN":
			
			//-- AScan --
			$this->saveAscan($img);
			//-- AScan --
			
			break;	

			case "B-SCAN":
			
			//-- BScan --
			$this->saveBscan($img);
			//-- BScan --
			
			break;	

			case "CELL COUNT":
			
			//---  CELL Count --	
			$this->saveCellCount($img);
			// -- Cell Count --		
			
			break;	

			case "FUNDUS":
			
			$this->saveFundus($img);
			
			break;	

			case "EXTERNAL":
			
			$this->saveExternal($img);
			
			break;

			case "GDXHRT":
			case "GDX":
			
			$this->saveGDXHRT($img);
			
			break;	

			case "ICG":
			
			$this->saveICG($img);
			
			break;	
			
			case "IOLMASTER":
			case "IOL MASTER":
			
			$this->saveIOLMaster($img);
			
			break;	

			case "IVFA":
			
			$this->saveIVFA($img);
			
			break;	

			case "OCT":
			
			$this->saveOCT($img);
			
			break;	
			
			//CIRRUS is an OCT machine that provide RNFL analysis.  So can we extend our logic such that if Station name is CIRRUS then the test is loaded in the OCT-RNFL 
			case "CIRRUS":
			case "OCT-RNFL":
			
			$this->saveOCTRNFL($img);
			
			break;	

			case "TOPOGRAPHY":
			
			$this->saveTopography($img);
			
			break;	

			case "VF":
			
			$this->saveVF($img);
			
			break;	

			case "VF-GL":
			
			$this->saveVFGL($img);
			
			break;	
			
			case "PACHY":
				$this->savePachy($img);
			break;
			
			case "IGNORE":
				//IGNORE
			break;
			
			
			default:
			
			$this->saveTestOther($img);
			
			break;	

		}
		////Add in chart Note --
		
	}
}

?>