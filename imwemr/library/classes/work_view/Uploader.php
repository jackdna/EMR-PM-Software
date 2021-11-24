<?php

class Uploader{
	private $pid, $uid, $fid;
	public function __construct($pid="0", $fid="0", $uid="0"){
		$this->pid = !empty($pid) ? $pid : $_SESSION["patient"];
		$this->uid = !empty($uid) ? $uid : $_SESSION["authId"];
		$this->fid = $fid;
	}

	function get_php_server(){
		//--
		$tmp_webroot =  $GLOBALS['webroot'] ;
		//$GLOBALS['php_server'] = $GLOBALS['php_server'] ;
		//--

		//port
		$phpServerPort="";
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
			$phpHTTPProtocol="http://";
		}else{
			$phpHTTPProtocol="https://";
		}

		// Hack for urt--
		$phpServerIP = $_SERVER['HTTP_HOST'];
		//if($phpServerIP != $_SERVER['HTTP_HOST']){
			$tmp_php_server = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$tmp_webroot;
		//}
		// Hack for urt--
		return $tmp_php_server;
	}

	public function get_uploaded_files(){
		$strDiv="";
		$patient_id = $this->pid;

		//FormId
		$form_id = $this->fid;

		//Move File --
		$oSaveFile = new SaveFile($patient_id);
		//Move File --

		$sql = "SELECT * FROM chart_ar_scan WHERE form_id='".$form_id."' AND patient_id = '".$patient_id."' AND deleted_by='0' ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$id = $row["id"];
			$pth = $row["path"];
			if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
				$pathBg = $oSaveFile->getFilePath($pth,"w");
			}else{
				$pathBg = $oSaveFile->getFilePath($pth,"h");
			}
			$hei_widt="";
			$pth_abs = $oSaveFile->getFilePath($pth,"i");
			if(file_exists($pth_abs)){
				//$ftype = mime_content_type($pth);
				$pthInfo = pathinfo($pth_abs);
				$ftype = $pthInfo['extension'];
				if($ftype == "text/xml" || $ftype == "xml"){continue;}
				if($ftype != "application/pdf" && $ftype != "pdf"){
					$pathNew = $pth_abs;
					$pth = $oSaveFile->createThumbs($pth_abs);
					if(is_array($pth) == true){
						$pth = $pathNew;

						if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
							$pth = $oSaveFile->getFilePath($pth,"w");
						}else{
							$pth = $oSaveFile->getFilePath($pth,"h");
						}

						$tempImgWH = "style=\"max-width:".$pth["imgWidth"]."px; height:".$pth["imgHeight"]."px;\"";
					}
					else{
						if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
							$pth = $oSaveFile->getFilePath($pth,"w");
						}else{
							$pth = $oSaveFile->getFilePath($pth,"h");
						}
					}

				}else{
					//Pdf icon
					$pth = $GLOBALS['webroot']."/library/images/pdfimg.png";
					$pth = $oSaveFile->createThumbs($pth_abs);
					if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
						$pth = $oSaveFile->getFilePath($pth,"w");
					}else{
						$pth = $oSaveFile->getFilePath($pth,"h");
					}
					if(trim($pth)==""){$pth = $GLOBALS['webroot']."/library/images/pdfimg.png";}
					$hei_widt="width:75px; height:80px; ";
				}

				//style=\"".$hei_widt."border:1px solid #999; padding:5px; margin:5px; \"
				//Div
				$strDiv .= "<div class=\"row\">
						<img src=\"".$pth."\" $tempImgWH alt=\"img\" class=\"img-thumbnail \" style=\"".$hei_widt."\"  onclick=\"showBgImg('".$pathBg."');\"/>
						<span class=\"glyphicon glyphicon-remove\" onclick=\"fundel('".$id."')\"></span>
						</div>";
			}

		}
		return $strDiv;
	}

	public function scan_ar($finalize_flag){

		$op_sec_nm = "AR";
		$opType=$_GET["op"];

		//
		$oChartNote = new ChartNote($this->pid, $this->fid);
		list($isReviewable,$isEditable,$iscur_user_vphy) = $oChartNote->isChartReviewable($_SESSION["authId"],1);

		//$op="m";
		if($opType == "m" || $opType == "mp"){  $op_name="Marco"; if($opType == "mp"){ $op_sec_nm = "PC"; } }
		if($opType == "s"){  $op_name="Scan"; }
		if($opType == "u"){  $op_name="Upload"; }
		if($opType == "p" || empty($opType)){  $op_name="Preview"; $opType="p"; }

		//delete
		if(isset($_GET["strid"])){
			$tmp = trim($_GET["strid"]);
			$tmp = trim($tmp,",");
			if(!empty($tmp)){
				$sql = "UPDATE chart_ar_scan SET deleted_by='".$_SESSION["authId"]."' WHERE id IN (".$tmp.") ";
				$row=sqlQuery($sql);
			}
			exit();
		}


		//
		$tmp_php_server = $this->get_php_server();

		if($opType == "s" || $opType == "u"){
			$upload_url = $tmp_php_server."/interface/chart_notes/saveCharts.php?elem_saveForm=uploadScan_AR&imwemr=".session_id()."&upType=".$opType;
		}
		if($opType == "u"){
			$upload_url=$GLOBALS['webroot']."/interface/chart_notes/saveCharts.php?elem_saveForm=uploadScan_AR&imwemr=".session_id()."&upType=".$opType;
			$upload_from='pdfsplitter';
			$scanUploadSrc = $GLOBALS['incdir']."/../library/upload/index.php";
		}

		//
		if(!isset($opType) || empty($opType) || $opType=="p" ){
			$strDiv = $this->get_uploaded_files();
		}//End

		include($GLOBALS['incdir']."/chart_notes/view/scan_ar.php");
	}

	function insertARScan($file_pointer){
		if(!empty($this->pid) && !empty($this->fid)){
			//Insert
			$sql = "INSERT INTO chart_ar_scan (id, patient_id, form_id, oprtr_id, up_date, path, comments)
					VALUES (NULL, '".$this->pid."','".$this->fid."','".$this->uid."','".wv_dt('now')."','".$file_pointer."','')
					";

			$rid = sqlInsert($sql);
		}
	}

	function save_ar($finalize_flag){
		$patient_id=$this->pid;
		$form_id=$this->fid;

		//Check
		if(empty($patient_id) || empty($form_id)){
			echo "Error: Patient Id / Form id is empty.";
			//exit();

		}else{

			//Get Param
			$upType = $_REQUEST["upType"];

			//Move File --
			$oSaveFile = new SaveFile($patient_id);
			//Move File --


			 if($upType == "m" || $upType == "mp"){
			 //marco --------------------------------
				//Marco file Parser--
				$pc_only = ($upType == "mp") ? "1" : "0";
				$oMarcoConn = new MarcoConn($patient_id,$form_id, $finalize_flag, $pc_only);
				//Marco file Parser--

				$num = $_POST["fnum"];

				$msgErr="";
				$flgReload="";

				for($i=1;$i<=$num;$i++){

					//echo "<br>CHECK 0 - "."marco".$i;

					$fileinfo = $_FILES["marco".$i];
					if(count($fileinfo)<=0)continue;

					$original_file=$fileinfo;
					$filename=$fileinfo['name'];
					$filetype=$fileinfo['type'];
					$filesize=$fileinfo['size'];
					$file_tmp=$fileinfo['tmp_name'];

					//echo "<br>CHECK 1 - ".$filetype." - ".$filename;

					//iif file is an xml file, try to parse and save data : marco xml files--
					if($filetype == "text/xml" || ($filetype == "application/octet-stream" && strpos($filename, "xml")!==false)){


						if(wv_check_mime("xml", $file_tmp)){

						//echo "<br>CHECK 2";
						//
						$tmp_ar = ($upType == "mp") ? "PC" : "AR";

						//Copy File
						$file_pointer = "";
						$file_pointer = $oSaveFile->copyfile($original_file,$tmp_ar);
						if(!empty($file_pointer)){
							if($upType != "mp"){
							$this->insertARScan($file_pointer);
							}
						}

						///PatientId_55591/AR/ARK510AOutput-1358234943.xml
						$incFilePath = $oSaveFile->getFilePath($file_pointer,'i'); //Get Include Path

						if(file_exists($incFilePath)){

							$flgRet = $oMarcoConn->saveFile($incFilePath); //Save Marco File

							if($flgRet=="1"){
								//$msgErr = "alert('PatientId in marco file is inaccurate!'); ";
								$msgErr .=" ".$filename.", ";
							}else{
								$flgReload="1";
							}


						}else{
							//echo "FILE DO NOT EXISTS: ".$file_pointer;
						}
						}
					}
					//iif file is an xml file, try to parse and save data : marco xml files--

				}//End For

				$echo= "
					<html>
					<body>
					<script>";

					if(!empty($msgErr)){
						$echo .="alert('PatientId in following marco files is inaccurate:-".addslashes($msgErr)."');";

					}

					if(!empty($flgReload)){

						$echo.="//Upload work view
						if(typeof(opener.top.fmain)!='undefined' && typeof(window.opener.top.fmain.reloadWorkView) != 'undefined'){
							window.opener.top.fmain.reloadWorkView();
						}else if(typeof(opener.top.fmain)!='undefined'){
							window.opener.top.fmain.location.reload();
						}
						";
					}

					$echo.="
					window.close();
					</script>
					</body>
					</html>
					";


				echo $echo;
			//marco --------------------------------
			 }else if($upType == "u"){
			//UPLOAD --------------------------------
				$res_arr=array();
				$upload_arr_new[0]=array();
				$upload = $_FILES['files'];
				if($_REQUEST['control']=='l9'){
					$upload_arr_new=array();
					$upload_arr_new=$upload;
					$uplode_string="";
					foreach($upload as $nkey=> $up_data){
						$upload_arr_new[0][$nkey]=$up_data;
					}
				}else{
					foreach($upload as $nkey=> $up_data){
						foreach($up_data as $my_key => $main_val_arr){
							$upload_arr_new[0][$nkey]=$main_val_arr;
						}
					}
				}

				foreach($upload_arr_new as $tagname=>$fileinfo) {
					// get the name of the temporarily saved file (e.g. /tmp/php34634.tmp)
					$tempPath = $fileinfo['tmp_name'];
					// The filename and relative path within the Upload-Tree (eg. "/my documents/important/Laura.jpg")
					$relativePath = $_POST[$tagname . '_relativePath'];

					$original_file=$fileinfo;
					$filename=$fileinfo['name'];
					$filetype=$fileinfo['type'];
					$filesize=$fileinfo['size'];
					$file_tmp=$fileinfo['tmp_name'];

					if($filetype=="image/gif" || $filetype=="image/jpeg" || $filetype=="application/pdf" || $filetype=="image/png"){


						if(wv_check_mime("img+pdf", $file_tmp)){

						$file_pointer = "";
						$file_pointer = $oSaveFile->copyfile($original_file,"AR");
						if(!empty($file_pointer)){
							$this->insertARScan($file_pointer);
						}

						}
					}

					$files[$relativePath] = $tempPath;

					$res_arr["files"][] = $fileinfo;

				}//end foreach


				echo json_encode($res_arr);


			//UPLOAD --------------------------------
			}else if($upType == "s"){
			//SCAN --------------------------------
				$files_to_upload = 1;
				for($i = 0 ; $i < $files_to_upload; $i++){
					if($_FILES["file"]['name'][$i]){
						$uploads = true;
						if($_FILES["file"]['name'][$i]){
							//$original_file=$fileinfo;
							$fileName = $_FILES["file"]['name'][$i];
							$fileType = $_FILES["file"]['type'][$i];
							$PSize = $_FILES["file"]['size'][$i];
							$tmp_file = $_FILES["file"]["tmp_name"][$i];

							if(wv_check_mime("img", $tmp_file)){
							$original_file=array();
							$original_file["name"]=$fileName;
							$original_file["type"]=$fileType;
							$original_file["size"]=$PSize;
							$original_file["tmp_name"]=$tmp_file;

							//Copy File --
							$file_pointer = $oSaveFile->copyfile($original_file,"AR");
							//Copy File --
							if(!empty($file_pointer)){
								$this->insertARScan($file_pointer);
							}
							}
						}
					}
				}

			//SCAN --------------------------------
			}
		}
	}


	function receive_uploaded_files($dir=""){

		$oSaveFile = new SaveFile($this->uid,1,"users");

		$res_arr=array();
		$upload_arr_new[0]=array();
		$upload = $_FILES['files'];

		foreach($upload as $nkey=> $up_data){
			foreach($up_data as $my_key => $main_val_arr){
				$upload_arr_new[0][$nkey]=$main_val_arr;
			}
		}


		foreach($upload_arr_new as $tagname=>$fileinfo) {
			// get the name of the temporarily saved file (e.g. /tmp/php34634.tmp)
			$tempPath = $fileinfo['tmp_name'];
			// The filename and relative path within the Upload-Tree (eg. "/my documents/important/Laura.jpg")
			$relativePath = $_POST[$tagname . '_relativePath'];

			$original_file=$fileinfo;
			$filename=$fileinfo['name'];
			$filetype=$fileinfo['type'];
			$filesize=$fileinfo['size'];
			$file_tmp=$fileinfo['tmp_name'];

			if($filetype=="image/gif" || $filetype=="image/jpeg" ||
					$filetype=="application/pdf" || $filetype=="image/png" ||
					$filetype=="text/plain" || $filetype=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
					$filetype=="application/msword" || $filetype=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
					){
				if(wv_check_mime("img+pdf+doc", $file_tmp)){
					$file_pointer = "";
					$file_pointer = $oSaveFile->copyfile($original_file,$dir);
					if(!empty($file_pointer)){
						$fileinfo["curfile"] = $file_pointer;
					}
				}
			}

			$files[$relativePath] = $tempPath;
			$res_arr["files"][] = $fileinfo;

		}//end foreach

		echo json_encode($res_arr);
	}

}

?>
