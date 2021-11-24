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
File: dcm_data.php
Purpose: This file provides data of a dicom file.
Access Type : Direct file
*/
?>
<?php

set_time_limit(0);
chdir(dirname(__FILE__));
$ignoreAuth = true;
require(dirname(__FILE__).'/dicom_link.php');
require_once(dirname(__FILE__).'/class_dicom.php');
require_once(dirname(__FILE__).'/dicom_db.php');
require_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
//require_once($GLOBALS['incdir'].'/chart_notes/common/SaveFile.php');

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$d = new dicom_db;

//
if(!empty($_REQUEST["dsid"])){	
	$file = $d->get_file_path($_REQUEST["dsid"]); 	
	$_POST["eta"] = $file;
}

$er_msg = "";
if(!empty($_POST["eta"])){

	$dir = "";///$GLOBALS['incdir']."/main/uploaddir";
	$file = $_POST["eta"]; 

	//"D:/xampp/htdocs/iMedicR6-Dev/interface/chart_notes/dicom/received_images/STORESCU/2015/09/03/1.2.276.0.75.2.1.20.0.1.150903112626421.1116829.2058/OPb.1.2.276.0.75.2.1.20.0.3.150903112701546.1116829.19428.dcm";

	$htm_pt_data="";
	if(is_numeric($file)){ //patient id		
		$htm_pt_data = $d->get_pt_studies($file);	
	}else{
		$file = str_replace("\\","/",$file);

		$dir = preg_replace('/\/*$/',"",$dir);//removing ending '/'

		//print("HELLO");

		// o data tag
		$d = new dicom_tag;

		// check input
		if (!$file && !$dir) {
		  $er_msg = "$dir$file" . ":is NOT a correct DCM file.";	
		 // $d->logger("$dir/$file" . "Missing file info");
		  //exit;
		}

		//if Dicom file
		//*
		//if(!$d->is_dcm("$dir$file")){	
			//$d->logger("$dir$file" . ":is NOT a correct DCM file.");
			//echo "$dir$file" . ":is NOT a correct DCM file.";
			//return 1;
		//}
		//*/

		if(empty($er_msg)){
			// Lets make sure the DICOM file exists before proceeding
			$d->file = "$dir$file"; //
			if (!file_exists($d->file)) {
			  //$d->logger($d->file . ": does not exist");
			  $er_msg = "$dir$file" . ":does not exist";
			  //exit;
			}
		}
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link type="text/css" href="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/cache_cntrlr.php?op=wvexmcss" rel="stylesheet">
</head>
<body>

<div >
<form action="dcm_data.php" method="post" >
	<div class="form-group">
		<div class="col-sm-3">
			<label for="eta"><strong>Enter DCM file path / Patient ID:</strong></label>
		</div>
		<div class=" col-sm-7">			
			<input type="text" class="form-control" rows="1" id="eta" name="eta" >
		</div>
		<div class="col-sm-1">
			<button type="submit" class="btn btn-default" name="ss">Show data</button>
		</div>
	</div>
</form>
</div>
<div class="clearfix" ></div>
<div >
<?php 
	if(!empty($_POST["eta"])){	
		if(!empty($er_msg)){
			echo $er_msg;
		}else if(!empty($htm_pt_data)){	
			echo $htm_pt_data;
		}else{
			// Load the tags from the images
			$d->load_tags_data();
		}
	}
	unset($_POST);
?>
</div>
</body>
</html>
