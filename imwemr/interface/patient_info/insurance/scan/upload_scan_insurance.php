<?php 

include '../../../../config/globals.php';
include $GLOBALS['srcdir'].'/classes/SaveFile.php';

$operator_id = $_SESSION['authId'];
$pid = $_SESSION['patient'];
$pid = (int) $pid;
$save = new SaveFile($pid);

$type = ($_GET['type']) ? $_GET['type'] : $_SESSION['patient_ins_type'] ;
$ins_caseid = $_SESSION['currentCaseid'];
$isRecordExists = $_GET['isRecordExists'];

if($isRecordExists)
{
	$qry = "select pid,ins_caseid  from insurance_data where id = ".$isRecordExists." and pid='".$pid."'";
	$res = imw_query($qry);
	$row = imw_fetch_assoc($res);
	$ins_caseid = $row['ins_caseid'];
}
$uplFlag = 0;
if(isset($_POST['actType']) && $_POST['actType']=="upload"){
	$uplFlag = 1;
}

//Show the number of files to upload
if($uplFlag == 1){ 
	$files_to_upload = count($_FILES['file']['name']);
}else{
	$files_to_upload = 1;
}

$allowed_ext = array("jpeg","jpg","gif","png","pdf");
$max_size = 1024 * 500; // Max: 500K.

echo '<center>Please wait....</center>';

if($_GET['method'] && $_GET['method'] == "upload")
{
	$uploads = false;
	for($i = 0 ; $i < $files_to_upload; $i++)
	{
		$upload_dir = $save->upDir;
		$save->pDir = $pid > 0 ? $save->pDir : '/tmp';
		$patientDir = $save->pDir;
		
		$file_data = array();
		$file_data["name"]	=	$_FILES['file']['name'][$i]	;
		$file_data["type"]	=	$_FILES['file']['type'][$i];
		$file_data["size"]	=	$_FILES['file']['size'][$i];
		$file_data["tmp_name"] = $_FILES['file']['tmp_name'][$i];
		$file_data["error"] = $_FILES['file']['error'][$i];
		
		// Check file extention
		$pathArr = pathinfo($file_data["name"]);
		$extension = $pathArr['extension'];
		$orig_name = $pathArr['filename'];
		
		if( !in_array($extension,$allowed_ext) ) {
		  $_SESSION['message'] = $orig_name . " has invalid extension.<br>";
		  continue;
		}
		 
		// validate file content type
		if( !wv_check_mime('img+pdf',$file_data["tmp_name"]) ) {
		  $_SESSION['message'] = $orig_name . " is an invalid image.<br>";
		  continue;
		}

		$scandoc = $save->copyfile($file_data);
		$doctitle = $_POST["DocTitle"];
		$file_moved = file_exists(data_path().substr($scandoc,1));
		
		if($scandoc && $file_moved)
		{
				$uploads = true;
				$_SESSION["scan_card_type"] = $type;
				$scan_card_new_name = "scan_card_new_".$type;
				$scan_card_name = "scan_card_".$type;
	
				$_SESSION[$scan_card_new_name]=NULL;
				$_SESSION[$scan_card_new_name]="";					
				$_SESSION[$scan_card_new_name]=$scandoc;
	
				$_SESSION[$scan_card_name]=NULL;
				$_SESSION[$scan_card_name]="";					
				$_SESSION[$scan_card_name]=$scandoc;
			
				if( $isRecordExists )
				{
					$query= "select scan_card,scan_card2  from insurance_data where id = ".$isRecordExists;
					$sql = imw_query($query);
					$row = imw_fetch_assoc($sql);
					extract($row);
					
					$qry = '';
					if($scan_card != '' && file_exists(data_path().substr($scan_card,1)) && ( $scan_card2 == '' || ( $scan_card2 != '' && !file_exists(data_path().substr($scan_card2,1)) ) ) )
					{
						if($uplFlag == 1){ 
							$fields = ", scan_or_upload_card2='upload'";
						}
						else{
							$fields = ", scan_or_upload_card2='scan'";
						}
						
						$qry = "update insurance_data set scan_card2='".$scandoc."', scan_label2='".$doctitle."', cardscan1_datetime= now() ".$fields." where id = ".$isRecordExists." And pid='".$pid."'";
					}
					else if($scan_card == '' || ( $scan_card != '' && !file_exists(data_path().substr($scan_card,1)) ) )
					{
						if($uplFlag==1){
							$fields = ", scan_or_upload_card1='upload'";
						}
						else{
							$fields = ", scan_or_upload_card1='scan'";
						}
						$qry = "update insurance_data set scan_card='".$scandoc."', scan_label='".$doctitle."', cardscan_date = now() $fields where id = ".$isRecordExists." and pid='".$pid."'";
					}
					
					if($qry) $qryupd = imw_query($qry);
				}
				else
				{
					$insuranceSess = "scan_card_".$type;				
					$insuranceSessLabel = "scan_label_".$type;				
					$_SESSION[$insuranceSess] = $scandoc;
					$_SESSION[$insuranceSessLabel] = $doctitle;	
					
					$qry = "select scan_documents_id,scan_card from insurance_scan_documents 
													where type = '".$type."' and ins_caseid = ".$ins_caseid."
													and patient_id = ".$pid." and document_status = '0'";
					$qryId = imw_query($qry);
					$row = imW_fetch_assoc($qryId);
					$scan_documents_id = $row['scan_documents_id'];
					$scan_card = $row['scan_card'];
					
					if($scan_documents_id == '')
					{
						if($uplFlag==1){
							$fields = ", scan_or_upload_card1='upload'";
						}
						else{
							$fields = ", scan_or_upload_card1='scan'";
						}
						$qry = "insert into insurance_scan_documents set 
											type = '".$type."',ins_caseid = ".$ins_caseid.",
											patient_id = ".$pid.", scan_card = '".$scandoc."',
											scan_label = '".$doctitle."',created_date = now(), cardscan_date = now(),
											operator_id = ".$operator_id." " .$fields;
					}
					else if($scan_documents_id != '' && ($scan_card == '' || ($scan_card <> '' && !file_exists(data_path().substr($scan_card,1))) ))
					{
						if($uplFlag == 1){ 
							$fields = ", scan_or_upload_card1='upload'";
						}
						else{
							$fields = ", scan_or_upload_card1='scan'";
						}
						$qry = "update insurance_scan_documents set 
											type = '".$type."',ins_caseid = ".$ins_caseid.",
											patient_id = ".$pid.", scan_card = '".$scandoc."',
											scan_label = '".$doctitle."',created_date = now(), cardscan_date = now(),
											operator_id = ".$operator_id." " .$fields."
											where scan_documents_id = ".$scan_documents_id;
					}
					else if($scan_documents_id != '' && $scan_card != '')
					{
						if($uplFlag==1){ 
							$fields = ", scan_or_upload_card2='upload'";
						}
						else{
							$fields = ", scan_or_upload_card2='scan'";
						}
						$qry = "update insurance_scan_documents set 
												type = '".$type."',ins_caseid = ".$ins_caseid.",
												patient_id = ".$pid.",scan_card2 = '".$scandoc."',
												scan_label2 = '".$doctitle."',cardscan1_date = now(),
												operator_id = ".$operator_id." ".$fields."
												where scan_documents_id = ".$scan_documents_id;
					}
					imw_query($qry);
				}
			
				$message .= $file_data["name"]." uploaded.<br>";		
		}	
       
   	if(!$uploads)  $message = "No files selected!";
		
 	}	
	
	if($uplFlag==1)
	{
		if($isRecordExists)
		{
			$qry = "update insurance_data set cardscan_operator = '".$operator_id."', cardscan_comments = '".imw_real_escape_string($_POST['comments'])."' where pid = '".$pid."' and type = '".$type."' and ins_caseid ='".$ins_caseid."'";
		}
		else{
			$qry = "update insurance_scan_documents set cardscan_operator='".$operator_id."', cardscan_comments = '".imw_real_escape_string($_POST['comments'])."' where patient_id = '".$pid."' and type = '".$type."' and ins_caseid = '".$ins_caseid."'";
		}
		imw_query($qry);
	
	}
	
	if($uplFlag == 1){
		echo '<script>'.($_REQUEST['call_from']==''?'window.opener.top.show_loading_image("show");window.opener.top.document.getElementById("hidChkChangeDemoTabDb").value="yes";window.opener.top.fmain.askSepAccount();':'').'</script>';
		if( 'scheduler' == strtolower($_REQUEST['call_from']) ) {
			echo '<script>
			if(typeof(window.opener.get_action)!="undefined") {
				window.opener.get_action(\'submit_form\');
			}else if(typeof(window.opener.top.callChildWinCheckIn)!="undefined") {
				window.opener.top.callChildWinCheckIn();	
			}</script>';
		}
		echo '<script>window.close();</script>';
	}
	
	sleep(1);die();
}


?>