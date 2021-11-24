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
File: imp_folder_listener.php
Purpose: This file provides processing of dicom file for directory listener.
Access Type : Include file
*/
?>
<?php
chdir(dirname(__FILE__));
//require_once('class_dicom.php');

/**DEBUG**/
/*
set_time_limit(0);
$ignoreAuth = true;
require_once(dirname(__FILE__).'/../../globals.php');
require_once('../common/SaveFile.php');
require_once('class_dicom.php');
require_once('dicom_db.php');

$z_dir = "C:\Imedic\apache\htdocs\directoryHandler\path\to\files";

$z_file = "1.2.276.0.7230010.3.1.4.534418788.1908.1363640583.734.dcm";

//*/
//*********//

$patientId=""; //default
$dir = isset($z_dir) ? $z_dir : ''; // Directory our DICOM file is
$file = isset($z_file) ? $z_file : ''; // Filename of the DICOM file

$sent_to_ae = ''.DICOM_AE; //"DICOM4IMEDIC";
$sent_from_ae = ''.DICOM_AE_FOLDERLISTENER; //"FOLDERIMPORT";

##//For Testing 
//DEFAULT VALUES
//$dir="".DCM_DIR;
//$file = "dean.dcm";
##

// check input
if (!$file || !$dir) {
  print("Missing file info");  
  return 1;
}

// o data tag
$d = new dicom_tag;

// file check
$d->file = "$dir/$file"; //
if (!file_exists($d->file)) { 
  $d->logger($d->file . ": does not exist");
  return 2;
}

//if Dicom file
//*

if(!$d->is_dcm("$dir/$file")){	
	$d->logger($d->file . ":is NOT a correct DCM file.");
	return 3;	
}
//*/

//Copy BakDir in start folder --
$d_bakup = "$dir/backup"; //
if (!file_exists($d_bakup)) {   
  mkdir($d_bakup, 0777, true); 
}
//Copy BakDir in start folder --

// Load the tags from the images
$d->load_tags();

// get some information from the DICOM file and store it in an array 
$img = array();

// get name from images as last^first. and seperate it .
$img['name'] = $d->get_tag('0010', '0010');
list($img['lastname'], $img['firstname']) = explode('^', $img['name']);

// Patient ID
$patientId =$img['id'] = (int)$d->get_tag('0010', '0020');


//TESTINg
//$patientId = $img['id']=1;
//--

//Check Patient Id 
if(empty($patientId)){
	$d->logger($d->file . ": patient Id does not exist in file.");
	return 4;
}


// get the year, month, day for use in the file name
$img['appt_date'] = $d->get_tag('0008', '0020');
$img['appt_date'] = date('Y-m-d', strtotime($img['appt_date']));
list($img['year'], $img['month'], $img['day']) = explode('-', $img['appt_date']);

// patient's birth date
$img['dob'] = $d->get_tag('0010', '0030');

// study uid : uniquely identify the study
$img['study_uid'] = $d->get_tag('0020', '000d');

//  study description
$img['study_desc'] = $d->get_tag('0008', '1030');

// This should also uniquely identify the study
$img['accession'] = $d->get_tag('0008', '0050');

// Patient history
$img['history'] = $d->get_tag('0010', '21B0');

// The name of the facility taking the images
$img['institution'] = $d->get_tag('0008', '0080');

// These define the order the images should be displayed
$img['series_number'] = $d->get_tag('0020', '0011');
$img['instance_number'] = $d->get_tag('0020', '0013');

// This is unique to this image
$img['sop_instance'] = $d->get_tag('0008', '0018');

// How is the pixel data of the image encoded?
$img['transfer_syntax'] = $d->get_tag('0002', '0010');

// depending on the modality, this should be the specific body part in the image (hand, leg, arm, ect)
$img['body_part_examined'] = $d->get_tag('0018', '0015');

// The date/time the image was created. This is spread over two tags. Also, lets make it SQL friendly
$img['image_date'] = $d->get_tag('0008', '0020');
$img['image_time'] = round($d->get_tag('0008', '0030'));
$img['image_time'] = str_pad($img['image_time'], 6, "0", STR_PAD_LEFT);
$img['image_date'] = date('Y-m-d G:i:s', strtotime($img['image_date'] . ' ' . $img['image_time']));
//Get Image MiMe Type
$img['mime_type'] = $d->get_tag('0042', '0012');

// The modality of the image
$img['modality'] = $d->get_tag('0008', '0060');

//MIMETypeOfEncapsulatedDocument
$img['MIMETypeOfEncapsulatedDocument'] = $d->get_tag('0042', '0012');

//ReferringPhysicianName
$img['ReferringPhysicianName'] = $d->get_tag('0008','0090');

//StationName : 0008,1010
$img['StationName'] = $d->get_tag('0008','1010');
//DocumentTitle : 0042,0010
$img['DocumentTitle'] = $d->get_tag('0042','0010');
//Manufacturer  : 0008,0070
$img['Manufacturer'] = $d->get_tag('0008','0070');

//Ends -----

//dir to store images
//$store_dir = dirname(__FILE__)."/received_images/$sent_from_ae/" . $img['year'] . "/" . $img['month'] . "/" . $img['day'] . "/".$img['study_uid'];


//*
//O Save File
$oSaveFile = new SaveFile($patientId);
$dicomDir=$oSaveFile->ptDir("DICOM_FILES/".$img['study_uid'],"s");
$store_dir = $dicomDir;
//*/

//creating dir if not exists
if (!file_exists($store_dir)) {
  mkdir($store_dir, 0777, true);  
}

//echo "HELLO";
/*
//Stopping Image Duplicate check
//Check if images exists
if (file_exists("$store_dir/$file.dcm")) {
  $d->logger("$store_dir/$file.dcm already exists, new file is probably a duplicate, ignoring.");
  unlink($d->file); //do not unlink
  exit;
}
*/

//check missing extension
if(strpos($file, ".dcm")===false){
	$file.=".dcm";
}

//just copy to Bak Dir
copy($d->file, "$d_bakup/$file"); 
// Move received image into the storage directory.
rename($d->file, "$store_dir/$file"); 
chmod("$store_dir/$file", 0666);

//*
//creeating imags
$d = new dicom_convert;
$d->file = "$store_dir/$file";

//*
//MIMETypeOfEncapsulatedDocument

//*
if($img['MIMETypeOfEncapsulatedDocument'] == "application/pdf" || $img['mime_type']=="application/pdf"){		
	$tn_file = $d->dcm_to_pdf();	
}else{
	$tn_file = $d->dcm_to_tn();
}
//*/

//*
//rename thumbnail
$thumb_path="$store_dir/tn";
if(!file_exists($thumb_path)) {
  mkdir($thumb_path);
}
$tmp_tn_file = $thumb_path."/" . basename($tn_file);
if(!file_exists($tmp_tn_file)){
	rename($tn_file, $tmp_tn_file);
}else{
	echo "File Exists.".$tmp_tn_file;
	return 6;
}
//*/

//O Save File
//$oSaveFile = new SaveFile($patientId);
//$dicomDir=$oSaveFile->ptDir("DICOM_FILES/".$img['study_uid'],"s");

//Add Img path to img array 
//*
$img["imgpath_dcm"] = $oSaveFile->getFilePath($d->file,"db2");
$img["imgpath_output"] = $oSaveFile->getFilePath($tmp_tn_file,"db2");
//$img["imgpath_output_savepath"] = $dicomDir."/" . basename($tn_file);
//$img["imgpath_output_path_pointer"] = $oSaveFile->getFilePath($img["imgpath_output_savepath"],"db2");
//*/
///$img["imgpath_dcm"] = $d->file;
//$img["imgpath_output"] = $tn_file;

//print_r($img);
//exit();

///Add sender info
$img["sent_from_ae"] = $sent_from_ae;
$img["sent_to_ae"] = $sent_to_ae;

//Db connection
$db = new dicom_db;
$img["operator_id"] = $db->getUserId();
$db->saveDcm($img);
//--db

//*/

?>