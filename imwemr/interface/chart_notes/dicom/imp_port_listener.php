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
File: imp_port_listener.php
Purpose: This file provides processing for dicom file in port listener.
Access Type : Include file
*/
?>
<?php
set_time_limit(0);
chdir(dirname(__FILE__));
$ignoreAuth = true;
require(dirname(__FILE__).'/dicom_link.php');
require_once(dirname(__FILE__).'/class_dicom.php');
require_once(dirname(__FILE__).'/dicom_db.php');
//require_once($GLOBALS['srcdir'].'/classes/SaveFile.php');

$dir = (isset($argv[2]) ? $argv[2] : ''); // Directory our DICOM file is
$file = (isset($argv[3]) ? trim($argv[3]) : ''); // Filename of the DICOM file

$dir = preg_replace('/\/*$/',"",$dir);//removing ending '/'

$sent_to_ae = (isset($argv[4]) ? $argv[4] : ''); // AE Title the image was sent to
$sent_from_ae = ((isset($argv[5]) ? $argv[5] : '')); // AE Title the image was sent from

//print("HELLO".$dir." \n ".$file." \n ".$sent_to_ae." \n ".$sent_from_ae);

// o data tag
$d = new dicom_tag;

//$d->logger(" Hello ");

//check AE title
if($sent_to_ae != "".DICOM_AE){ //
	$d->logger("Error: AE title is not correct for $file.dcm.");
	exit();
}

// check input
if (!$file || !$dir) {
  $d->logger("$dir/$file" . "Missing file info");
  exit;
}

//if Dicom file
//*
if(!$d->is_dcm("$dir/$file")){	
	$d->logger("$dir/$file" . ":is NOT a correct DCM file.");
	return 1;
}
//*/

// Lets make sure the DICOM file exists before proceeding
$d->file = "$dir/$file"; //
if (!file_exists($d->file)) {
  $d->logger($d->file . ": does not exist");
  exit;
}

// Load the tags from the images
$d->load_tags();

// get some information from the DICOM file and store it in an array 
$img = array();

// get name from images as last^first. and seperate it .
$img['name'] = $d->get_tag('0010', '0010');
list($img['lastname'], $img['firstname']) = explode('^', $img['name']);

// patient's birth date
$img['dob'] = $d->get_tag('0010', '0030');

//patient sex
$img['sex'] = $d->get_tag('0010', '0040');

// Patient ID	
$tmp_id = $d->get_tag('0010', '0020');
if(is_array($tmp_id)){ $tmp_id = $tmp_id[0]; }
$patientId =$img['id'] = (int)$tmp_id;
//$patientId =$img['id'] = 67907;

// if some MRN is used to show --
	$tmp_use_mrn = "".DICOM_USE_MRN;
	if(!empty($tmp_use_mrn)){  
		$db1 = new dicom_db;
		$tmp_patientId = $db1->getPtIdFromMrn($tmp_use_mrn, $patientId, $img['name'], $img['dob'], $img['sex']);
		if(!empty($tmp_patientId)){
			$patientId = $tmp_patientId;  
			$img['id'] = $patientId;
		}
	}
//--

// get the year, month, day for use in the file name
$img['appt_date'] = $d->get_tag('0008', '0020');
$img['appt_date'] = date('Y-m-d', strtotime($img['appt_date']));
list($img['year'], $img['month'], $img['day']) = explode('-', $img['appt_date']);

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
if(is_array($img['institution'])){$img['institution'] = $img['institution'][0];}

// These define the order the images should be displayed
$img['series_number'] = $d->get_tag('0020', '0011');
$img['instance_number'] = $d->get_tag('0020', '0013');

// This is unique to this image
$img['sop_instance'] = $d->get_tag('0008', '0018');

// How is the pixel data of the image encoded?
$img['transfer_syntax'] = $d->get_tag('0002', '0010');

// depending on the modality, this should be the specific body part in the image (hand, leg, arm, ect)
$img['body_part_examined'] = $d->get_tag('0018', '0015');

//ManufacturerModelName : 0008,1090
$img['ManufacturerModelName'] = $d->get_tag('0008','1090');
$img['ManufacturerModelName'] = trim($img['ManufacturerModelName']);

// The date/time the image was created. This is spread over two tags. Also, lets make it SQL friendly
$img['image_date'] = $d->get_tag('0008', '0020');
$img['image_time'] = round($d->get_tag('0008', '0030'));
$img['image_time'] = str_pad($img['image_time'], 6, "0", STR_PAD_LEFT);
$img['image_date'] = date('Y-m-d G:i:s', strtotime($img['image_date'] . ' ' . $img['image_time']));

//Img AcquisitionDateTime
$img['image_acq_date_tm'] = $d->get_tag('0008','002a');
$img['image_acq_date_tm'] = trim($img['image_acq_date_tm']);
if(!empty($img['image_acq_date_tm'])){
if(strlen($img['image_acq_date_tm'])>14){ $img['image_acq_date_tm'] = substr($img['image_acq_date_tm'], 0, 14); }
$img['image_acq_date_tm'] = str_pad($img['image_acq_date_tm'], 14, "0", STR_PAD_LEFT);
$img['image_acq_date_tm'] = date('Y-m-d G:i:s', strtotime($img['image_acq_date_tm']));
}

//for all Carl Zeiss Visucam diagnostic machines
if($img['ManufacturerModelName']=="VISUCAM NM FA 2" && !empty($img['image_acq_date_tm'])){ $img['image_date']=$img['image_acq_date_tm']; }

// The modality of the image
$img['modality'] = $d->get_tag('0008', '0060');

//MIMETypeOfEncapsulatedDocument
$img['MIMETypeOfEncapsulatedDocument'] = $d->get_tag('0042', '0012');

//ReferringPhysicianName
$img['ReferringPhysicianName'] = $d->get_tag('0008','0090');

//StationName : 0008,1010
$img['StationName'] = $d->get_tag('0008','1010');
if(is_array($img['StationName'])){$img['StationName'] = $img['StationName'][0];}
$img['StationName'] = trim("".$img['StationName']);
if(empty($img['StationName'])){  $img['StationName']= $img['ManufacturerModelName']; }

//DocumentTitle : 0042,0010
$img['DocumentTitle'] = $d->get_tag('0042','0010');

//Manufacturer  : 0008,0070
$img['Manufacturer'] = $d->get_tag('0008','0070');
if(is_array($img['Manufacturer'])){$img['Manufacturer'] = $img['Manufacturer'][0];}

//SeriesDescription: 0008,103e
$img['SeriesDescription'] = $d->get_tag('0008','103e');
$img['SeriesDescription'] = trim($img['SeriesDescription']);

//CodeMeaning  : 0008,0104
$img['CodeMeaning'] = $d->get_tag('0008','0104');
if(is_array($img['CodeMeaning'])){$img['CodeMeaning'] = $img['CodeMeaning'][1];}
$img['CodeMeaning'] = trim($img['CodeMeaning']);

//DocumentTitle  : 0042, 0010
$img['DocumentTitle'] = $d->get_tag('0042', '0010');
$img['DocumentTitle'] = trim($img['DocumentTitle']);

//AE TITLE
$img["sent_from_ae"] = $sent_from_ae;
$img["sent_to_ae"] = $sent_to_ae;

//SourceApplicationEntityTitle : 0002,0016
$img['SourceApplicationEntityTitle'] = $d->get_tag('0002', '0016');
$img['SourceApplicationEntityTitle'] = trim($img['SourceApplicationEntityTitle']);

//DeviceSerialNumber : 0018,1000
$img['DeviceSerialNumber'] = $d->get_tag('0018', '1000');
$img['DeviceSerialNumber'] = trim($img['DeviceSerialNumber']);

//ImageType : 0008,0008
$img['ImageType'] = $d->get_tag('0008', '0008');
$img['ImageType'] = trim($img['ImageType']);

//Ends -----

//dir to store images
$sent_from_ae_folder = preg_replace('/[^0-9a-zA-Z_]/',"",$sent_from_ae);
$store_dir = DICOM_PRACTICE_DIR."/received_images/$sent_from_ae_folder/" . $img['year'] . "/" . $img['month'] . "/" . $img['day'] ."/".$patientId. "/".$img['study_uid'];

//creating dir if not exists
if (!file_exists($store_dir)) {
  if(!mkdir($store_dir, 0777, true)){ $d->logger($d->file . ": could not create directory.");exit(); }else{ chown( $store_dir , "apache" ); }  
}

//Check if images exists
if (file_exists("$store_dir/$file.dcm")) {
  $d->logger("$store_dir/$file.dcm already exists, new file is probably a duplicate, ignoring.");
  unlink($d->file); //do not unlink
  exit;
}

// Move our received image into the storage directory.
rename($d->file, "$store_dir/$file.dcm"); 
//copy($d->file, "$store_dir/$file.dcm"); //just copy
chmod("$store_dir/$file.dcm", 0666);

//creeating imags
$d = new dicom_convert;
$d->file = "$store_dir/$file.dcm";
if($img['MIMETypeOfEncapsulatedDocument'] == "application/pdf" ){
	$tn_file = $d->dcm_to_pdf();
}else{	
	$tn_file = $d->dcm_to_jpg();
}

//check if file exists
if(!file_exists($tn_file)){$d->logger("Err: could not create data file for $tn_file "); exit();}

//check if valid file --
$mime = $d->dcm_get_mime_type($tn_file, $img['MIMETypeOfEncapsulatedDocument']);
if($mime == "image/jpeg" && strpos($tn_file, ".pdf")!==false){ //rename file to jpg			
	$tn_file_old = $tn_file;
	$tn_file = str_replace(".pdf", ".jpg", $tn_file);
	
}else if($mime == "application/pdf" && strpos($tn_file, ".jpg")!==false){
	$tn_file_old = $tn_file;
	$tn_file = str_replace(".jpg", ".pdf", $tn_file);
}

if(isset($tn_file_old) && !empty($tn_file_old)){
	if(!rename($tn_file_old, $tn_file)){ $d->logger("Err: could not rename to $tmp_tn_file"); }
	$img['MIMETypeOfEncapsulatedDocument'] = $mime;
}
//check if valid file --

//rename thumbnail
$thumb_path="$store_dir/tn";
if(!file_exists($thumb_path)) {
  mkdir($thumb_path, 0777, true);
}
$tmp_tn_file = $thumb_path."/" . basename($tn_file);
if(!rename($tn_file, $tmp_tn_file)){ $d->logger("Err: could not rename to $tmp_tn_file"); }

//O Save File
$oSaveFile = new SaveFile($patientId);
$dicomDir=$oSaveFile->ptDir("DICOM_FILES/".$img['study_uid'],"s");

//Add Img path to img array 
//*
$img["imgpath_dcm"] = $d->file; //$oSaveFile->getFilePath($d->file,"db2");
$img["imgpath_output"] = $tmp_tn_file; //$oSaveFile->getFilePath($tmp_tn_file,"db2");
$img["imgpath_output_savepath"] = $dicomDir."/" . basename($tn_file);
$img["imgpath_output_path_pointer"] = $oSaveFile->getFilePath($img["imgpath_output_savepath"],"db2");
//*/

//Db connection
$db = new dicom_db;
$img["operator_id"] = $db->getUserId();
$db->saveDcm($img);
//--db

//unlink received image
$a = LOG_RECEIVED_IMAGES;
if(empty($a) && file_exists($d->file) && is_file($d->file)){ unlink($d->file); }
?>
