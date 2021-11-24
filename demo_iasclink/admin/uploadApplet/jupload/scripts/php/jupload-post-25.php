<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("../../../../../common/conDb.php");

include("../../../../adminLinkfile.php");
include_once("../../../../classObjectFunction.php");
$objManageData = new manageData;

$pConfirmId = $_REQUEST['pConfirmId'];
$patient_id = $_REQUEST['patient_id']; 
$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];

$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];
$folderId = $_REQUEST['folderId'];
$folder = $_REQUEST['folder'];
$selectedFolder = $_REQUEST['selectedFolder'];
//include_once("../../../../../globals.php");		
//include_once($GLOBALS['incdir']."/admin/manage_folder/folder_function.php");
/*
function scan_image_by_ashwani($pid,$doctitle,$original_file,$filename,$filetype,$filesize,$file_tmp,$vf,$folderId,$editid,$url)	{
		//Remove Space		
		$arr = array(" ","&nbsp;","%20");		
		$doctitle = str_replace($arr,"_",urldecode($doctitle));		
		
		$sPhotoFileName = $filename;
		if ($sPhotoFileName) // file uploaded
		{	$aFileNameParts = explode(".", $sPhotoFileName);
			$sFileExtension = end($aFileNameParts); 
			if ($sFileExtension != "jpg" && $sFileExtension != "JPEG" && $sFileExtension != "JPG" && $sFileExtension!="gif" &&  $sFileExtension!="png"  &&  $sFileExtension!="pdf")
			{	
				die ("Choose a JPG/GIF/PNG/PDF for the upload");
			}
		}
		
		$nPhotoSize = $filesize;
		$nPhototype=$filetype;
		$sTempFileName =$file_tmp; 
		$oTempFile = fopen($sTempFileName, "r");		
		$sBinaryPhoto = fread($oTempFile, fileSize($sTempFileName));		
		
		if ($sFileExtension != "pdf") {
		$oSourceImage = imagecreatefromstring($sBinaryPhoto); // try to create image
		$nWidth = imagesx($oSourceImage); 
		$nHeight = imagesy($oSourceImage);
		$nDestinationWidth1 = 200;
		$nDestinationHeight1 =150;
		if($nWidth<=$nDestinationWidth1 && $nHeight<=$nDestinationHeight1){
			$ret[0]=$nWidth;
			$ret[1]=$nHeight;
		
		}else{
			 $pc_width=$nDestinationWidth1/$nWidth; 
			 $pc_height=$nDestinationHeight1/$nHeight; 
			 $pc_width=number_format($pc_width,2);
			 $pc_height=number_format($pc_height,2);
	//echo("Percentage Width=".$pc_width."and Perscentage height=".$pc_height);
		 if($pc_width<=$pc_height){
		   $rd_image_width=number_format(($nWidth*$pc_width),2);
		   $rd_image_height=number_format(($nHeight*$pc_width),2);
		   $ret[0]=$rd_image_width;
		   $ret[1]=$rd_image_height;
		  
		   }else if($pc_height<=$pc_width){   
			   $rd_image_width=number_format(($nWidth*$pc_height),2);
			   $rd_image_height=number_format(($nHeight*$pc_height),2);
			   $ret[0]=$rd_image_width;
			   $ret[1]=$rd_image_height;
			   
		   }
		}
		
		//echo $ret[0]." ".$ret[1];
		$nDestinationWidth = $ret[0];
		$nDestinationHeight =$ret[1]; 
		$oDestinationImage = imagecreatetruecolor($nDestinationWidth, $nDestinationHeight);
		imagecopyresized($oDestinationImage, $oSourceImage,0, 0, 0, 0,$nDestinationWidth, $nDestinationHeight,$nWidth, $nHeight); 
		ob_start(); 
		if($sFileExtension=="jpeg" || $sFileExtension=="jpg" || $sFileExtension=="JPG")	{
			imagejpeg($oDestinationImage);
		}
		
		 if($sFileExtension=="gif" || $sFileExtension=="GIF")	{
			imagegif($oDestinationImage);
		}
		 if($sFileExtension=="png" || $sFileExtension=="PNG")	{
			imagepng($oDestinationImage);
		}
		$sBinaryThumbnail = ob_get_contents(); 
		ob_end_clean(); 
		} else { // if check for pdf files
			$sPDFFileName = "pdflogo.jpg";
			$sPDFFileName = $GLOBALS['incdir']."/front_office/pdflogo.jpg";
			$oPDFFile = fopen($sPDFFileName, "r");
			$sBinaryThumbnail = fread($oPDFFile, fileSize($sPDFFileName));
		}
		//Set Mysql LimiT
		$increase = imw_query("SET GLOBAL max_allowed_packet=1000000000");		// 1000MB
		//addslashes($sBinaryPhoto)		
		if($editid=="")	{			
			$query = "INSERT INTO imedic_scan_db.scan_doc_tbl  set 
			patient_id='$pid',
			folder_categories_id='$folderId',
			doc_title='$doctitle',";			
			$query .= "scan_doc='".addslashes($sBinaryPhoto)."', ";			
			$query.= "thumb_scan_doc='".addslashes($sBinaryThumbnail)."',			
			doc_type='$sFileExtension',
			doc_size='$nPhotoSize',
			upload_date='".date("Y-m-d")."',
			vf='$vf',
			pdf_url = '$url'";			
			$res=imw_query($query) or die(imw_error());
			$insertId = imw_insert_id();
			
		}else	{
			
			$query = "update imedic_scan_db.scan_doc_tbl  set 
			patient_id='$pid',
			folder_categories_id='$folderId',
			doc_title='$doctitle',";				
			$query .= "scan_doc='".addslashes($sBinaryPhoto)."', ";						
			$query .= "thumb_scan_doc='".addslashes($sBinaryThumbnail)."',
			doc_type='image/jpeg',
			doc_size='$nPhotoSize',
			upload_date='".date("Y-m-d")."',
			vf='$vf',pdf_url = '$url' where scan_doc_id='$editid'";
			$res=imw_query($query) or die(imw_error());			
			
		}		
		//Set Mysql LimiT
		$decrease = imw_query("SET GLOBAL max_allowed_packet=1000000");		// 1MB		
		@unlink($original_file);
}
*/
//Test
	#print_r($_SESSION);
	#echo "<BR>---<BR>";
	#print_r($_GET);
	#echo "<BR>---<BR>";
	#print_r($_POST);
	#echo "<BR>---<BR>";	
	#exit;
//Test

/**
 * JUpload-Post Handler
 * 
 * These scripts are not for re-distribution and for use with JUpload only.
 * 
 * If you want to use these scripts outside of its JUpload-related context,
 * please write a mail and check back with us @ info@jupload.biz
 * 
 * @author Dominik Seifert, dominik.seifert@smartwerkz.com
 * @copyright Smartwerkz, Haller Systemservices: www.jupload.biz
 */

global $_ju_listener, $_ju_uploadRoot, $_ju_fileDir, $_ju_thumbDir, $_ju_maxSize;

// Include a file which provides several helper functions and is configured through the jupload.cfg.php
include_once(dirname(__FILE__) . "/inc/jupload.inc.php");

// Upload is starting
$_ju_listener->onStart($_SERVER["HTTP_X_JUPLOAD_ID"]);

/**
 * Iterate over all received files.
 */
foreach($_FILES as $tagname=>$fileinfo) {
	// get the name of the temporarily saved file (e.g. /tmp/php34634.tmp)
	$tempPath = $fileinfo['tmp_name'];

	// The filename and relative path within the Upload-Tree (eg. "/my documents/important/Laura.jpg")
	$relativePath = $_POST[$tagname . '_relativePath'];
	
	// Do we have a valid file?
	if (!checkSavePath($relativePath) || !$_ju_listener->checkValid($relativePath, $tempPath)) {
		continue;
	}
	
	//START
		$fpath = $fileinfo['name'];
		$fext = array_pop(explode('.', $fpath));
		$original_file=$fileinfo;
		
		//$imageName = $_FILES["uploadFile"]["name"];
		$imageName = $fileinfo['name'];
		
		$imageName = str_replace(" ","-",$imageName);
		$imageName = str_replace(",","-",$imageName);
		
		
		//$tmp = $_FILES["uploadFile"]["tmp_name"];
		$tmp = $fileinfo['tmp_name'];
		
		//$imageType = $_FILES["uploadFile"]["type"];
		$imageType = $fileinfo['type'];
		
		
		//$PSize = $_FILES["uploadFile"]["size"];
		$PSize = $fileinfo['size'];
		
		//$oTempFile = fopen($_FILES["uploadFile"]["tmp_name"], "r");		
		$oTempFile = fopen($fileinfo['tmp_name'], "r");		
		
		$image = fread($oTempFile, $PSize);
		
		$parentFolder = $_REQUEST['parentFolder'];
	
		unset($arrayRecord);
		$arrayRecord['image_type'] = $imageType;
		//$arrayRecord['img_content'] = $image;
		$arrayRecord['document_name'] = $imageName;
		$arrayRecord['document_size'] = $PSize;
		$arrayRecord['confirmation_id'] = $pConfirmId;
		$arrayRecord['patient_id'] = $patient_id;
		$arrayRecord['document_id'] = $folderId;
		
		//CODE FOR PDF FILE OR OTHER FILE
		if($imageType == 'application/pdf'){
			//DO NOTHING
		}else {
			//SAVE IMAGE
			$image = addslashes($image);
			$arrayRecord['img_content'] = $image;
		}
		//END CODE FOR PDF FILE OR OTHER FILE
		$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
		
		//Set Mysql LimiT
		//$decrease = imw_query("SET GLOBAL max_allowed_packet=1000000");		// 1MB		
		//@unlink($original_file);
	
	//END
	
	
	//Test
		/*
		$fpath = $fileinfo['name'];
		$fext = array_pop(explode('.', $fpath));
		//$_SESSION['filename'][] = $fpath;
		//$_SESSION['uploaded']='Yes';
		$original_file=$fileinfo;
		$filename=$fileinfo['name'];//
		$filetype=$fileinfo['type'];//
		$filesize=$fileinfo['size'];//
		$file_tmp=$fileinfo['tmp_name'];//
		$doctitle = $filename; //$_POST["doc_title"];
		
		//$refer_id=$_SESSION['refer_id']; 
		$folderId=$_REQUEST['folderId'];
		$editid=$_REQUEST['editid'];
		$pid = $_SESSION['patient'];
		
		scan_image_by_ashwani($pid,$doctitle,$original_file,$filename,$filetype,$filesize,$file_tmp,$vf,$folderId,$editid);					
		//folder_update2($folderId,$pid);		
		
	//Test
	
	
	
	
	$files[$relativePath] = $tempPath;
	*/
}

/*
if ($files) {
	foreach ($files as $relativePath => $tempPath)  {
		// Do we have a thumbnail? If it is not a thumbnail, it is a regular file.
		$isThumb = $_POST[$tagname . '_thumbnail'];
	
		// Where to save the file? Determine the target-directory, depending on if it is a thumbnail or a file
		$filepath = $_ju_uploadRoot . ($isThumb ? $_ju_thumbDir : $_ju_fileDir) . "/$relativePath";
	
		// Create folders
		mkdirs(dirname($filepath = normalize($filepath)));
		
		// Move the temporary file to the target directory
		//move_uploaded_file($tempPath, $filepath) or die("Error while moving temporary file to target path: " . $relativePath);
		
		// Tell the listener that another file has successfully been received.
		$_ju_listener->onReceived($filepath, $relativePath, $isThumbs);
	}
}

$_ju_listener->finished();
*/
?>