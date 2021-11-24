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
require_once(dirname(__FILE__).'/../../../config/globals.php');
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");

$os = new SaveFile();
$ar_prv=array();
//$os->get

$arr = array("TOOLKIT_DIR"=>"Path for dcmtk", 
							"IMEDIC_DICOM"=>"Path of dicom folder in imwemr", 
							"DICOM_IP"=>"Dicom IP for receiver", 
							"LISTENER_SLEEP_TIME"=>"Listener sleep time: default is 10", 
							"TCONVRT"=>"Convert tools", 
							"PHP_HOME"=>"PHP HOME", 
							"DICOM_DB_PATH"=>"Dicom DB Path", 
							"DCM_DIR"=>"Path of folder where files are coming from dicom machine",
							"DICOM_PORT"=>"Dicom port for receiver", 
							"DICOM_AE"=>"Dicom AE Title for receiver", 
							"DICOM_AE_FOLDERLISTENER"=>"Dicom AE TITLE for folderlistener", 
							"DICOM_WL_PORT"=>"Dicom Work List Port",
							"DICOM_IS_WORKING"=>"Dicom Is Working  On This Server: put 1 if working", 
							"DICOM_AE_WLM"=>"Dicom AE Title for worklist", 
							"DICOM_LOG"=>"Dicom log  put '1' to enable it", 
							"DICOM_USE_MRN"=>"USE MRN  not Patient ID : insert field name of mrn",
							"DICOM_AE_WLM_DB"=>"Dicom AE To set in worklist file; Default is DICOM_AE_WLM", 
							"DICOM_MODALITY"=>"Dicom modality type to set in worklist file; Default is OPT",
							"SSL_ENABLE"=>"Enable SSL Enable: set 1 to enable",
							"PRIVATE_KEY_PATH"=>"Path to Private key file : ie /usr/cer/priv.pem",
							"PRIVATE_KEY_PWD"=>"Path to Private key password : ie string",
							"CERTIFICATE_PATH"=>"Path to Certificate file : ie /usr/cer/certi.pem",
							"SEED_FILE_PATH"=>"Path to Seed file : ie /usr/cer/seed.bin",
							"CLIENT_CERTIFICATE_PATH"=>"Path to Client Certificate file : ie /usr/cer/certi.pem",
							"FAC_BASE_WLM_DB"=>"facility based worklist db: put 1 to enable",
							"LOG_RECEIVED_IMAGES"=>"Log received dcm files: put 1 to enable"
							
							);



if(isset($_POST["submit_btn"]) && $_POST["submit_btn"]=="Done"){
	$str="";
	
	$dfp = $os->get_dicom_data_path(1);	
	foreach($arr as $k => $v){	
		$t = $_POST[$k];
		$t = str_replace(", ", ",", $t);
		$str.="\n//".$v;
		$str.="\ndefine(\"".$k."\", \"".$t."\");\n\n";
		
	}	
	
	if(!empty($str)){	
		//
		$str.="\n//Set Practice url:: ".PRACTICE_PATH."";
		$str.="\n".'$_SERVER["REQUEST_URI"] = "'.PRACTICE_PATH.'";'."\n\n";
		
		$str.="\n//dicom data directory ";
		$str.="\ndefine(\"DICOM_PRACTICE_DIR\", dirname(__FILE__));\n\n";
	
		$str = "<?php\n".$str."\n?>";		
		$fp = fopen($dfp.'/dicom_globals.php', 'w');
		fwrite($fp, $str);
		fclose($fp);
	}	
	
	echo "Process Done!";	
	exit();
}else{
	//Get 
	$dfp = $os->get_dicom_data_path();
	if(!empty($dfp) && file_exists($dfp.'/dicom_globals.php')){
		$str = file_get_contents($dfp.'/dicom_globals.php');		
		
		foreach($arr as $k => $v){	
			$ptrn = "/define\(\"".$k."\"\, \".*\"\)/";
			//echo $ptrn;
			preg_match($ptrn, $str, $matches);
			if(!empty($matches[0])){
				$t = $matches[0];
				$t = str_replace(array("define","(",")",'"'),"",$t);
				
				
				$art = explode(", ", $t);
				
				$ka = trim($art[0]);
				$va = trim($art[1]);
				
				$ka = trim($art[0],'"');
				$va = trim($art[1],'"');
				
				if(!empty($va)){
					$ar_prv[$ka] = trim($va);
				}
			}
		}
	}
}
//--

//print_r($ar_prv);


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.1.12.1.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
	
    </head>
	<body>
		<center>
		<h1>Dicom Configuration file for <mark><?php echo PRACTICE_PATH; ?></mark></h1>
		<form id="frmconf" name="frmconf" action="" method="post" >
			
			<?php				
				
				foreach($arr as $k => $v){	
							
			?>
		
			<div class="form-group">
			    <label class="control-label col-sm-4 text-right" for="<?php echo $k; ?>"><?php echo $v; ?>:</label>
			    <div class="col-sm-8">
			      <input type="text" class="form-control" name="<?php echo $k; ?>" placeholder="<?php echo $k; ?>" value="<?php echo !empty($ar_prv[$k]) ? $ar_prv[$k] : ""; ?>">
			    </div>			    
			</div>
			
			<?php
				}
			?>
			<button type="submit" name="submit_btn" value="Done" class="btn btn-success">Done</button>
		</form>
		</center>
	</body>
</html>