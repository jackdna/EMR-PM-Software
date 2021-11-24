<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("../../globals.php");
include_once("../../admin/manage_folder/folder_function.php");
include_once("../../chart_notes/common/Savefile.php");

$scan_doc_id = $_SESSION['document_scan_id'];


/*
function scan_image_by_ashwani($pid,$doctitle,$original_file,$filename,$filetype,$filesize,$file_tmp,$vf,$folder_id,$editid,$url)	{
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
			$query = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl  set 
			patient_id='$pid',
			folder_categories_id='$folder_id',
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
			
			$query = "update ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl  set 
			patient_id='$pid',
			folder_categories_id='$folder_id',
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
$userauthorized = $_SESSION['authId'];
$patient_id = $_SESSION['patient'];
$folder_id = $_REQUEST['folder_id'];

$upload_dir = "uploaddir";

if(!empty($patient_id)){
	$oSaveFile = new SaveFile($patient_id);
}

/// Create Directory of Patient and upload image
if(isset($_SESSION['patient']) && !empty($_SESSION['patient']))
{
	$pid = $_SESSION['patient'];
	//Patient Directory Name
	$patientDir = "/PatientId_".$pid."/uploadJPG";
	
	//Check
	if(!is_dir($upload_dir.$patientDir))
	{
		//Create patient directory
		mkdir($upload_dir.$patientDir, 0700);
	}
}

$path = "uploaddir\PatientId_".$patient_id."\uploaddir";//'//192.168.0.3/documents/test';
$path_jpg = "uploaddir\PatientId_".$patient_id."\uploadJPG";
$ab =  opendir($path);
while(($filename = readdir($ab)) !== false)
	{
		$path1 = pathinfo($filename);
		if($path1['extension'] == 'jpg')
		{
			//copy($path.'\\'.$filename,$path_jpg.'\\'.$filename);
			$filesize = filesize($path.'\\'.$filename);
			//$qry = "INSERT into ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set pdf_url = '$filename',folder_categories_id = '$folder_id',patient_id = '$patient_id'";
			//$res = imw_query($qry);
			//echo $patient_id.'--'.$doctitle.'--'.$path_jpg.'\\'.$filename,$filename.'--'.'jpg'.'--'.$filesize.'--'.$file_tmp.'--'.$vf.'--'.$folder_id.'--'.$editid;
			$doctitle = $filename;			
			
			$orgFile = array();
			$orgFile["name"] = $path1['filename'];
			$orgFile["type"] =  filetype ($path.'\\'.$filename);
			$orgFile["size"] = $filesize;
			$orgFile["tmp_name"]= dirname(__FILE__)."/".$path.'/'.$filename;
			upload_image_by_guru($patient_id,$doctitle,$orgFile,$filename,'jpg',$filesize,$path_jpg.'\\'.$filename,$vf,$folder_id,$editid,$filename,$_REQUEST['comments']);
			//@unlink($path_jpg.'\\'.$filename);
			//@unlink($path.'\\'.$filename);
		}
	}
	if($_REQUEST['comments']){
	}
	
//header('location:../../chart_notes/folder_category.php?cat_id='.$folder_id);
//echo $table;
//$file = fopen('pdfFile.html','w');
//$data = fputs($file,$table);

//fclose($file);
?>
<!--<form name="frm" action="index.php">
	<input type="hidden" name="page" value="5">
	<input type="hidden" name="font_size" value="10">
	<input type="hidden" name="folder_id" value="<?php echo $_REQUEST['folder_id']; ?>"  />
	<input type="hidden" name="edit_id" value="<?php echo $_REQUEST['edit_id']; ?>"  />
</form>-->
<script language="javascript">


 window.close();

</script>