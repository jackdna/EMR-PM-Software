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
File: class_dicom.php
Purpose: This class file provides functions to process dicom files.
Access Type : Include file
*/
?>
<?php

class dicom_utility{

	//*
	function __construct() {
		$this->mkLogDir();
	}
	//*/

	//Execute
	function Execute($command) {

	  $command .= ' 2>&1';
	  $handle = popen($command, 'r');
	  $log = '';

	  while (!feof($handle)) {
	    $line = fread($handle, 1024);
	    $log .= $line;
	  }
	  pclose($handle);

	  return $log;
	}

	//is DCM
	function is_dcm($file) {
	  $dump_cmd = TOOLKIT_DIR . "/dcmdump +E -M +L +Qn $file";
	  $dump = $this->Execute($dump_cmd);

	  if (strstr($dump, 'error')) {
	    return (0);
	  } else if (strstr($dump, 'Modality')) {
	    return (1);
	  }

	  return (0);
	}

	//mk log dir
	function mkLogDir(){
		//mk log dir
		//creating dir if not exists
		$str_log_dir=DICOM_PRACTICE_DIR."/log";
		if (!file_exists($str_log_dir)) {
		  mkdir($str_log_dir, 0777, true);
		}
	}

	// This function will log a message to a file
	function logger($message) {
	  $now_time = date("Ymd G:i:s");

	  $message = "[IMPORT] $now_time - $message";

	  $fh = fopen(DICOM_PRACTICE_DIR."/log/dcm_folder_imp".date("m-d-Y").".log", 'a') or die("can't open file");
	  fwrite($fh, "$message\n");
	  fclose($fh);

	  //print($message);

	}

}


class dicom_tag extends dicom_utility {

  var $tags = array();
  var $file = -1;

  function load_tags() {
    $file = $this->file;
    $dump_cmd = TOOLKIT_DIR . "/dcmdump +E -M +L +Qn $file";
    $dump = $this->Execute($dump_cmd);

    if (!$dump) {
      return (0);
    }


//print "$dump\n";
//exit;

    $this->tags = array();

    foreach (explode("\n", $dump) as $line) {

      $ge = '';

      $t = preg_match_all("/\((.*)\) [A-Z][A-Z]/", $line, $matches);
      if (isset($matches[1][0])) {
        $ge = $matches[1][0];
        if (!isset($this->tags["$ge"])) {
          $this->tags["$ge"] = '';
        }
      }

      if (!$ge) {
        continue;
      }

      $val = '';
      $found = 0;
      $t = preg_match_all("/\[(.*)\]/", $line, $matches);
      if (isset($matches[1][0])) {
        $found = 1;
        $val = $matches[1][0];

        if (is_array($this->tags["$ge"])) { // Already an array
          $this->tags["$ge"][] = $val;
        } else { // Create new array
          $old_val = $this->tags["$ge"];
          if ($old_val) {
            $this->tags["$ge"] = array();
            $this->tags["$ge"][] = $old_val;
            $this->tags["$ge"][] = $val;
          } else {
            $this->tags["$ge"] = $val;
          }
        }
      }

      if (is_array($this->tags["$ge"])) {
        $found = 1;
      }

      if (!$found) { // a couple of tags are not in [] preceded by =
        $t = preg_match_all("/\=(.*)\#/", $line, $matches);
        if (isset($matches[1][0])) {
          $found = 1;
          $val = $matches[1][0];
          $this->tags["$ge"] = rtrim($val);
        }
      }

      if (!$found) { // a couple of tags are not in []
        $t = preg_match_all("/[A-Z][A-Z] (.*)\#/", $line, $matches);
        if (isset($matches[1][0])) {
          $found = 1;
          $val = $matches[1][0];
          if (strstr($val, '(no value available)')) {
            $val = '';
          }
          //$this->tags["$ge"] = rtrim($val);
	  //--
	  $val = rtrim($val);
	  if (isset($this->tags["$ge"])) {
		if (is_array($this->tags["$ge"])) { // Already an array
			$this->tags["$ge"][] = $val;
		}else { // Create new array
			$old_val = $this->tags["$ge"];
			if ($old_val) {
			    $this->tags["$ge"] = array();
			    $this->tags["$ge"][] = $old_val;
			    $this->tags["$ge"][] = $val;
			} else {
			    $this->tags["$ge"] = $val;
			}
		}
	  }else{
		$this->tags["$ge"] = $val;
	  }
	  //--
        }
      }
    }
  }

//
  function get_tag($group, $element) {
    $val = '';
    if (isset($this->tags["$group,$element"])) {
      $val = $this->tags["$group,$element"];
    }
    return ($val);
  }

  function load_tags_data() {
    $file = $this->file;
    $dump_cmd = TOOLKIT_DIR . "/dcmdump +E -M +L +Qn $file";
    $dump = $this->Execute($dump_cmd);

    if (!$dump) {
      return (0);
    }


	print "<pre>$dump\n</pre>";
	//exit;
   }

}

class dicom_convert extends dicom_utility {

  var $file = '';
  var $jpg_file = '';
  var $tn_file = '';
  var $jpg_quality = 100;
  var $tn_size = 125;

### Convert a DICOM image to JPEG.
  function dcm_to_jpg() {

    $filesize = 0;

    $this->jpg_file = $this->file . '.jpg';

    $convert_cmd = TOOLKIT_DIR . "/dcmj2pnm +oj +Jq " . $this->jpg_quality . " --use-window 1 \"" . $this->file . "\" \"" . $this->jpg_file . "\"";
    $out = $this->Execute($convert_cmd);

    if (file_exists($this->jpg_file)) {
      $filesize = filesize($this->jpg_file);
    }

    if ($filesize < 10) { //upto 5000 kb: assuming file is NOT OK.
      $convert_cmd = TOOLKIT_DIR . "/dcmj2pnm +Wm +cl +oj +Jq " . $this->jpg_quality . " \"" . $this->file . "\" \"" . $this->jpg_file . "\"";
      $out = $this->Execute($convert_cmd);
    }

    return ($this->jpg_file);

  }


### make JPEG thumbnail.
  function dcm_to_tn() {
    $filesize = 0;
    $this->tn_file = $this->file . '_tn.jpg';

    $convert_cmd = TOOLKIT_DIR . "/dcmj2pnm +oj +Jq 100 +Sxv " . $this->tn_size . " --use-window 1 \"" . $this->file . "\" \"" . $this->tn_file . "\"";
    $out = $this->Execute($convert_cmd);

    if (file_exists($this->tn_file)) {
      $filesize = filesize($this->tn_file);
    }

    if ($filesize < 10) {
      $convert_cmd = TOOLKIT_DIR . "/dcmj2pnm +Wm +oj +Jq 100 +Sxv  " . $this->tn_size . " \"" . $this->file . "\" \"" . $this->tn_file . "\"";
      $out = $this->Execute($convert_cmd);
    }

    return ($this->tn_file);
  }

function dcm_get_mime_type($url, $mime) {
    if (function_exists('finfo_file')) {
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $url);
    }
    return $mime;
}

function convert_jfll_images($file_source, $file_source_new){
	$t = "".TCONVRT;
	if(!empty($t)){
		$str_slash=(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')?"":"./";
		$cwd = getcwd();
		if(chdir ( "".TCONVRT )){
			exec($str_slash."ffmpeg -i ".$file_source." ".$file_source_new." 2>&1 ",$out);
			chdir ( $cwd );
		}
	}
}

### make pdf
 function dcm_to_pdf(){
	$this->tn_file = $this->file . '_tn.pdf';

	$convert_cmd = TOOLKIT_DIR . "/dcm2pdf -v  \"" . $this->file . "\" \"" . $this->tn_file . "\"";
	//return($convert_cmd);
	//exit();
	$out = $this->Execute($convert_cmd);

	if(strpos($out,"I/O suspension")!==false || strpos($out,"premature end of stream")!==false){
		$convert_cmd = TOOLKIT_DIR . "/dcm2pdf -f -ui +uc +ae -ll debug   \"" . $this->file . "\" \"" . $this->tn_file . "\"";
		$out = $this->Execute($convert_cmd);
	}

	if (!file_exists($this->tn_file)) {
	      //$filesize = filesize($this->tn_file);
	      $this->logger("Err in dcm_to_pdf: ".$out." [".$this->file."]");
	      //print($out);
	      return false;
	      //exit();
	}else{
		//check if not valid pdf: run magick or convert
		$cc = file_get_contents($this->tn_file, NULL, NULL, 0, 4);
		//$cc = substr($cc, 0, 4);
		if($cc != "%PDF"){
			if($cc == "BM6@"||strpos($cc,"BM6") !== false){ file_put_contents($this->tn_file, " ", FILE_APPEND);	} //append empty 1 character as sometime one character is less in bmp file extract
			$file_source = $this->tn_file;
			$file_source_new = str_replace(".pdf", "1.jpg", $file_source);
			$str_exec = exec("convert ".$file_source." ".$file_source_new." 2>&1", $output, $return_var);
			//$str_exec = exec("magick ".$file_source." ".$file_source_new." 2>&1", $output, $return_var);
			$str_exec_output = implode(",",$output);

			////check for jpeg lossless
			if(stripos($str_exec_output, "SOF type 0xc3")!==false){
				$this->convert_jfll_images($file_source, $file_source_new);
			}
			//

			if(file_exists($file_source_new)){
				rename($file_source, $file_source.".org");
				rename($file_source_new, $file_source);
			}
		}
		//--
	}

	return ($this->tn_file);
 }

}

class dicom_net extends dicom_utility {

  var $transfer_syntax = '';
  var $file = '';


function store_server($port, $dcm_dir, $handler_script, $config_file, $debug = 0, $tls=0) {
    $dflag = '';
    if ($debug) {
      $dflag = '-v -d ';
    }

    if(!empty($tls)){
	$str_tls="+tls ".PRIVATE_KEY_PATH." ".CERTIFICATE_PATH." ".
			"+rs ".SEED_FILE_PATH." ".
			"+pw ".PRIVATE_KEY_PWD." ".
			"+cf ".CLIENT_CERTIFICATE_PATH." ";
	$str_ssl_exe="-tls";
    }else{
	$str_tls="";
	$str_ssl_exe="";
    }

    system(TOOLKIT_DIR . "/storescp".$str_ssl_exe." $dflag -dhl -td 20 -ta 20 --fork -xf $config_file Default -od $dcm_dir -xcr \"$handler_script \"#p\" \"#f\" \"#c\" \"#a\"\" ".$str_tls." $port");
  }


  function echoscu($host, $port, $my_ae = 'INDO', $remote_ae = 'INDO' , $tls=0, $prv="", $cer="", $rs="", $pw="") {

	if(!empty($tls)){
	$str_tls="+tls ".$prv." ".$cer." ".
			"+rs ".$rs." ".
			"+pw ".$pw." ".
			"+cf ".CERTIFICATE_PATH." ";
	$str_ssl_exe="-tls";
	}else{
	$str_tls="";
	$str_ssl_exe="";
	}

    $ping_cmd = TOOLKIT_DIR . "/echoscu".$str_ssl_exe." -v -aet \"$my_ae\" -aec \"$remote_ae\" ".$str_tls." $host $port";
    $out = $this->Execute($ping_cmd);
    if (!$out) {
      return (0);
    }
    return ($out);
  }

  function get_wl_query(){
	$pth = str_replace("/bin", "", TOOLKIT_DIR);
	$pth = str_replace(array("/bin","\bin"),"",$pth);
	$pth = $pth."/share/dcmtk/wlistqry/wlistqry0.dump";
	if(file_exists($pth)){
		$pth2 = str_replace(".dump",".wl",$pth);
		if(!file_exists($pth2)){
			$ping_cmd = TOOLKIT_DIR . "/dump2dcm -g  ".$pth." ".$pth2;
			$out = $this->Execute($ping_cmd);
			if(file_exists($pth2)){
				return $pth2;
			}
		}else{
			return $pth2;
		}
	}
	return "error";
  }

  function findscu($host, $port, $my_ae = 'INDO', $remote_ae = 'INDO', $send_batch = 0,   $tls=0, $prv="", $cer="", $rs="", $pw=""){

	$wlq = $this->get_wl_query();
	if($wlq=="error"){ return ""; }
	if(!empty($tls)){
	$str_ssl_exe="-tls";
	}else{ $str_ssl_exe=""; }

	$cmd = TOOLKIT_DIR . "/findscu".$str_ssl_exe." -v  -aec $remote_ae  $host $port $wlq ";
	$out = $this->Execute($cmd);
	if (!$out) {
	return (0);
	}
	return ($out);
  }

### Sends $this_file to $host $port.
  function send_dcm($host, $port, $my_ae = 'INDO', $remote_ae = 'INDO', $send_batch = 0,   $tls=0, $prv="", $cer="", $rs="", $pw="") {

	if(!empty($tls)){
	$str_tls="+tls ".$prv." ".$cer." ".
			"+rs ".$rs." ".
			"+pw ".$pw." ".
			"+cf ".CERTIFICATE_PATH." ";
	$str_ssl_exe="-tls";
	}else{
	$str_tls="";
	$str_ssl_exe="";
	}

    if (!$this->transfer_syntax) {
      $tags = new dicom_tag;
      $tags->file = $this->file;
      $tags->load_tags();
      $this->transfer_syntax = $tags->get_tag('0002', '0010');
    }


    $ts_flag = '';
    switch ($this->transfer_syntax) {
      case 'JPEGBaseline':
        $ts_flag = '-xy';
        break;
      case 'JPEGExtended:Process2+4':
        $ts_flag = '-xx';
        break;
      case 'JPEGLossless:Non-hierarchical-1stOrderPrediction':
        $ts_flag = '-xs';
        break;
    }

    $to_send = $this->file;

    if ($send_batch) {
      $to_send = dirname($this->file);
      $send_command = TOOLKIT_DIR . "/storescu".$str_ssl_exe." -ta 10 -td 10 -to 10 $ts_flag -aet \"$my_ae\" -aec $remote_ae $host $port +sd \"$to_send\" ".$str_tls." ";
    } else {
      $send_command = TOOLKIT_DIR . "/storescu".$str_ssl_exe." -ta 10 -td 10 -to 10 $ts_flag -aet \"$my_ae\" -aec $remote_ae $host $port \"$to_send\" ".$str_tls." ";
      //$send_command = TOOLKIT_DIR . "/storescu -ta 10 -td 10 -to 10 -aet \"$my_ae\" -aec $remote_ae $host $port \"$to_send\"";
    }

    $out = $this->Execute($send_command);
    if ($out) {
      return ($out."\nFILE transfer_syntax  IS '".$this->transfer_syntax."'");
    }
    return (0);

  }

   function storage_server($proc_file, $str_dir){

	if(file_exists($proc_file) && file_exists($str_dir)){

		$tool_dir_home = TOOLKIT_DIR;
		$tool_dir_home = str_replace(array("/bin","\bin"),"",$tool_dir_home);
		if(DICOM_LOG == "1"){ $str_log = " -lc ".$tool_dir_home ."/etc/dcmtk/logger_storescp.cfg "; }else{ $str_log=""; }
		//$f

		//Enable SSL
		if(SSL_ENABLE == "1"){
			$str_ssl="+tls ".PRIVATE_KEY_PATH." ".CERTIFICATE_PATH." ".
					"+rs ".SEED_FILE_PATH." ".
					"+pw ".PRIVATE_KEY_PWD." ".
					"+cf ".CLIENT_CERTIFICATE_PATH." ";
			$str_ssl_exe="-tls";
		}else{
			$str_ssl= "";
			$str_ssl_exe="";
		}

		//Check Version of PHP and set Php exe
		$php_vrsn = phpversion();
		$php_exe = "php";
		if($php_vrsn >= 7 && file_exists(PHP_HOME."/php73")){
			$php_exe = "php73";
		}

		$storescp_cmd = TOOLKIT_DIR . "/storescp".$str_ssl_exe." -dhl -td 20 -ta 20 --fork " .
		  "-xcr \"".PHP_HOME."/".$php_exe." ".$proc_file." \"".$_SERVER["REQUEST_URI"]."\" \"#p\" \"#f\" \"#c\" \"#a\"\" " . //processing file
		  "-xf ".$tool_dir_home ."/etc/dcmtk/storescp.cfg Default " . // config file
		  "-od ".$str_dir."/ " . // Where to put images we receive
		  "-aet ".DICOM_AE." ".
		  $str_ssl.
		  $str_log.
		  "".DICOM_PORT." "; // Listen on this port

		 // */

		system($storescp_cmd);

	}

   }

   function wlm_server(){
	$tool_dir_home = TOOLKIT_DIR;
	$tool_dir_home = str_replace(array("/bin","\bin"),"",$tool_dir_home);
	if(DICOM_LOG == "1"){ $str_log = " -lc ".$tool_dir_home ."/etc/dcmtk/logger_wlm.cfg "; }else{ $str_log=""; }

	$wl_cmd = TOOLKIT_DIR . "/wlmscpfs -dfp ".DICOM_DB_PATH.
	" -dhl -dfr  ".
	$str_log.
	DICOM_WL_PORT ;
	 // */

	system($wl_cmd);
   }

}

?>
