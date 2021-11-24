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
File: dirlistener.php
Purpose: This file provides listener for directory listener.
Access Type : Direct
*/
?>
<?php
set_time_limit(0);
$ignoreAuth = true;
require_once(dirname(__FILE__).'/../../../config/globals.php');
require_once($GLOBALS['srcdir'].'/classes/SaveFile.php');
require_once(dirname(__FILE__).'/class_dicom.php');
require_once(dirname(__FILE__).'/dicom_db.php');

$pth = "".DCM_DIR; //path to files
$arrAllFiles=array();

function writeOnPaper($text){
	$str="\n".date("m-d-Y H:i:s");
	
	$str.=$text;
	
	//write
	$nm="/file_".date("m-d-Y").".txt";
	$fp = fopen(dirname(__FILE__).'/dllog'.$nm, 'a+');
	$t = fwrite($fp, $str);	
	$t = fclose($fp);	
}


function processdcm($filepth){
	global $pth;
	///echo "\n".$filepth;
	writeOnPaper($filepth);
	
	if(file_exists($pth."/".$filepth)){
		
		$z_dir=$pth;
		$z_file=$filepth;		
		
		///print_r("\nBye ".$z_dir."/".$z_file);		
		
		include(dirname(__FILE__)."/imp_folder_listener.php");
		//include("test.php");	
		
		//Test---
		/*
		$d = new dicom_net;
		$d->file =  $z_dir."/".$z_file; //$z_dir//"../class_dicom_php/examples/dean.dcm";

		//print "Sending file...\n";

		$out = $d->send_dcm(''.DICOM_IP, ''.DICOM_PORT, ''.DICOM_AE, ''.DICOM_AE_FOLDERLISTENER);
		//$d->echoscu(''.DICOM_IP, ''.DICOM_PORT, ''.DICOM_AE, ''.DICOM_AE_FOLDERLISTENER); 

		if ($out) {
		  //print "$out\n";
		  writeOnPaper("$out\n");
		  //exit;
		}

		//print "Sent!\n";
		writeOnPaper("Sent!\n");
		//*/
		//Test --
		
		//---
	}
}

function fileReader(){

	global $pth,$arrAllFiles;

	$flg=0;
	$arrNewF=array();	
	
	$str="";
	if ($handle = opendir($pth)) {
	    
	    while (false !== ($entry = readdir($handle))) {
		if ($entry == '.' || $entry == '..' || is_dir($pth."/".$entry)) {
		    continue;
		}     
	    
		if(!in_array($entry,$arrAllFiles)){
			$arrAllFiles[]=$entry;
			$flg=1;
			$arrNewF[]=$entry;
		}
	    }

	    closedir($handle);
	}
	
	//file
	if($flg==1){
		
		if(count($arrNewF) > 0){
			
			foreach($arrNewF as $key => $val){	
				
				//
				processdcm($val);
			
			}
		
		}	
		
	}
	
}//

//*
//listener
while(true){

	fileReader();

	sleep(''.LISTENER_SLEEP_TIME); //sleep 
}
//*/

?>
