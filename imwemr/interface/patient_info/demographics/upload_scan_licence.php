<?php

include '../../../config/globals.php';
include $GLOBALS['srcdir'].'/classes/SaveFile.php';

$pid = $_SESSION['patient'];
$pid = (int) $pid;
$save = new SaveFile($pid);

$type = $_REQUEST['type'] ?? '';
$tblName = $type == 'rp' ? 'resp_party' : 'patient_data';
$licImgFld = $type == 'rp' ? 'licence_image' : 'licence_photo';
$idFld = $type == 'rp' ? 'patient_id' : 'id';
//Show the number of files to upload
$files_to_upload = 1;

$allowed_ext = array("jpeg","jpg","gif","png");
$max_size = 1024 * 500; // Max: 500K.

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
			
			if( !in_array($extension,$allowed_ext) ) {
			  $_SESSION['message'] = $file_data["name"] . " has invalid extension.<br>";
			  continue;
			}

			// validate file content type
			if( !check_img_mime($file_data["tmp_name"]) ) {
				$_SESSION['message'] = $file_data["name"] . " is an invalid image.<br>";
				continue;
			}
			
			$scandoc = $save->copyfile($file_data);
			$doctitle = $_POST["DocTitle"];
			
			if($scandoc)
			{
				$uploads = true;
				if( $pid > 0 )
				{
						$query = "select ".$licImgFld." as ptLicPic from ".$tblName." where ".$idFld."='".$pid."'";
						$sql = imw_query($query);
						
						if( imw_num_rows($sql) )
						{
							extract(imw_fetch_assoc($sql));
							$file_to_delete = $upload_dir.$ptLicPic;
							if($ptLicPic != "" && file_exists($file_to_delete)){
								unlink($file_to_delete);
							}
						}
						$qry = imw_query("update ".$tblName." set ".$licImgFld."='".$scandoc."', licenseDate = '".date('Y-m-d H:i:s')."' where ".$idFld."='".$pid."' ");
						$_SESSION[$type.'scan_image_new']=NULL;
						$_SESSION[$type.'scan_image_new']="";					
						$_SESSION[$type.'scan_image_new']=$scandoc;
			
					}
				else
				{
						// IF NEW PATEINT 				
						$_SESSION[$type.'scan_image']=NULL;
						$_SESSION[$type.'scan_image']="";					
						$_SESSION[$type.'scan_image']=$scandoc;
		
						$_SESSION[$type.'scan_image_new']=NULL;
						$_SESSION[$type.'scan_image_new']="";					
						$_SESSION[$type.'scan_image_new']=$scandoc;
					
				}
			
				$message .= $file_data["name"]." uploaded.<br>";		
			}
		
		}
		
		if(!$uploads)  $message = "No files selected!";
		exit;
}

//HTML STARTING
?>