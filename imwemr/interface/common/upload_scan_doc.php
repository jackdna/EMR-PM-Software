<?php
include_once("../../config/globals.php");
include_once("../../library/classes/SaveFile.php");

extract($_REQUEST);

$save = new SaveFile();

$pagesArr = array('adminDoc' => array('folder' => 'admin_documents/scan') );

//Show the number of files to upload
$files_to_upload = 1;

//Directory where the uploaded files have to come  
$upLoadPath = $save->upDir;

// Extention Allowed to store
$allowed_ext = array('jpeg','jpg','gif','png','pdf');

//Scan the file
if(isset($_GET['method']) && $_GET['method'] == "scan")
{
		$uploads = false;
		for($i = 0 ; $i < $files_to_upload; $i++){
			
			$file = array();
			$file['name'] = $_FILES['file']['name'][$i];
			$file['type'] = $_FILES['file']['type'][$i];
			$file['size'] = $_FILES['file']['size'][$i];
			$file['tmp_name'] = $_FILES['file']['tmp_name'][$i];
			
			if( file_exists($file['tmp_name'])) $uploads = true;
			else continue;	
			
			// ******** CHECK FILE EXTENSION *******************
			$extension = pathinfo($_FILES['file']['name'][$i]);					
			$extension = $extension[extension];
			$extensionAccepted = in_array($extension,$allowed_ext) ? 1 : 0;
			
			if(! $extensionAccepted) {
				$message .= $file['name'] . " has invalid extension.<br>";
				continue;
			}
		 
			// Create Directory of site care and upload image
			$image_path = $save->copyfile($file,$pagesArr[$page]['folder']);
			
			$file_name = end(explode('/',$image_path));
			
			if( $recordId) {
				if( $page == 'adminDoc') {
					$qry = "update document set upload_doc_file_path = '', scan_doc_file_path='".$image_path."',scan_doc_date='".date('Y-m-d')."',upload_doc_type='".$extension."' where id='".$recordId."'";
				}
				
				if( $qry ) $sql = imw_query($qry);
				
				$_SESSION['scan_image_document']=NULL;
				$_SESSION['scan_image_document']="";	
			}
			else{
				$_SESSION['scan_image_document']=NULL;
				$_SESSION['scan_image_document']="";					
				$_SESSION['scan_image_document']=$image_path;	
			}
			$message .=	$file['name']." uploaded.<br>";
      
  	}
    
		if(!$uploads)  $message = "No files selected!";
	}

exit;