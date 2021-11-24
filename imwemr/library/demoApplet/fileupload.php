<?php
include '../../config/globals.php';
//Show the number of files to upload
$files_to_upload = 1;

//Directory where the uploaded files have to come
//RECOMMENDED TO SET ANOTHER DIRECTOR THEN THE DIRECTORY WHERE THIS SCRIPT IS IN!!
//$upload_dir = "uploaddir";
$pid = (int)$_REQUEST['pid'];
$pid = (isset($_SESSION['patient']) && !empty($_SESSION['patient'])) ? $_SESSION['patient'] : $pid;
if( !$pid) die('Error');

$upload_dir = $GLOBALS['fileroot'].'/data/'.constant('PRACTICE_PATH'); 
  /// Create Directory of Patient and upload image
if($pid) {
    //Patient Directory Name
		$patientDir = "/PatientId_".$pid;

		if(!is_dir($upload_dir.$patientDir)) {
			//Create patient directory
			mkdir($upload_dir.$patientDir, 0777,true);
		}
		
		$patientDirScanTmp = "/PatientId_".$pid.'/tmp_scan'; 
		if(!is_dir($upload_dir.$patientDirScanTmp))
		{
			//Create patient directory
			mkdir($upload_dir.$patientDirScanTmp, 0777,true);
		}
}
  $upload_dir = $upload_dir.$patientDirScanTmp;

  // **************** ADDED BY LAB Asprise! ********************

  $allowed_ext = array("jpeg","jpg", "gif", "png");
  $max_size = 1024 * 500; // Max: 500K.

  //Any other action the user must be logged in!
  if($_GET['method'])
  {
    //Upload the file
    if($_GET['method'] == "upload")
    {
      $_SESSION['message'] = "";
      $uploads = false;

      for($i = 0 ; $i < $files_to_upload; $i++) {
        
        if($_FILES['file']['name'][$i]) {
          
          if( file_exists($_FILES['file']['tmp_name'][$i]) ) {  
            $uploads = true;

            // Check file extention
            $pathArr = pathinfo($_FILES['file']['name'][$i]);
            $extension = $pathArr['extension'];
            $orig_name = $pathArr['filename'];
            
            if( !in_array($extension,$allowed_ext) ) {
              $_SESSION['message'] = $orig_name . " has invalid extension.<br>";
              continue;
            }
             
            // validate file content type
            if( !check_img_mime($_FILES['file']['tmp_name'][$i]) ) {
              $_SESSION['message'] = $orig_name . " is an invalid image.<br>";
              continue;
            }

            // Change file name to save into new location
            $fileName = $orig_name."-".time();
			      $fileName = $fileName.".".$extension;
            $file_to_upload = $upload_dir."/".$fileName;
				
            if( move_uploaded_file($_FILES['file']['tmp_name'][$i],$file_to_upload) ) {
              chmod($file_to_upload,0777);
              $_SESSION['message'] .= $orig_name." - File uploaded successfully.<br>";
            } 
            else {
              $_SESSION['message'] .= $orig_name." - File upload error.<br>";  
            }
          }
        }
      }
      if(!$uploads)  $_SESSION['message'] = "No files selected!";
    }
  }
  
?>