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

if(isset($_POST["submit_btn"]) && $_POST["submit_btn"]=="Done"){
	$dfp = $os->get_dicom_data_path(1);
	$str ="";
	$c=0;
	while(true){		
		if(isset($_POST["tag_name".$c])){
			$t_tag = trim($_POST["tag_name".$c]);
			$t_vl = trim($_POST["tag_value".$c]);
			$t_tst = trim($_POST["test_name".$c]);
			$t_con_tst = trim($_POST["condition_test_name".$c]);
			if(empty($t_con_tst)) { $t_con_tst="0"; }
			
			if(!empty($t_tag) && !empty($t_vl) && !empty($t_tst) && strpos($str,$t_tag."!~!".$t_vl."!~!")===false){		
				$str .= $t_tag."!~!".$t_vl."!~!".$t_tst."!~!".$t_con_tst."!!~~!!";				
			}
		}else{ break; }
		$c++;
		if($c>200){ break; }; //stop unconditional
	}
	
	$str = trim($str);
	if(!empty($str)){
		$str = ' $mapping_str=\''.addslashes($str).'\'; ';
	}
	
	$comm = trim($_POST["map_code"]);
	if(!empty($comm)){
		$str .= "/*mapping_code*/".$comm;
	}	
	if(!empty($str)){  $str = "<?php  ".$str." ?>"; }
	
	$fp = fopen($dfp.'/dicom_map_rules.php', 'w');
	fwrite($fp, $str);
	fclose($fp);
	
	header("location: config_map_rules_ex.php");
	exit();
	
}else{
	//Get 
	$dfp = $os->get_dicom_data_path();
	if(!empty($dfp) && file_exists($dfp.'/dicom_map_rules.php')){
		$str = file_get_contents($dfp.'/dicom_map_rules.php');
		$str = trim($str);
		if(!empty($str)){
			$str = str_replace(array("<?php", "?>"), "", $str);$str = trim($str);
			if(!empty($str)){
			$tmp = explode("/*mapping_code*/", $str);
			if(isset($tmp[1]) && !empty($tmp[1])){
				$tmp[1] = trim($tmp[1]);
				if(!empty($tmp[1])){
					$map_code = $tmp[1];
				}
			}
			$str = trim($tmp[0]);			
			$str = str_replace(array('$mapping_str=\'', "';"), "", $str);
			$str = trim($str); 
			if(!empty($str)){
			$str = stripslashes($str);
			$ar_str = explode("!!~~!!", $str);
			if(count($ar_str)>0){
				foreach($ar_str as $k => $v){
					$v = trim($v);
					if(!empty($v)){
						$ar_v = explode("!~!", $v);
						$ar_prv[] = $ar_v;
					}
				}
			}
			}
			}
		}
	}
}

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
			<h1>Dicom Mapping for <mark><?php echo PRACTICE_PATH; ?></mark></h1>
		</center>		
		<form id="frmconf" name="frmconf" action="" method="post" >
		
			<?php
				$len = count($ar_prv);
				for($i=0;$i<$len;$i++){ 	
				$v = $ar_prv[$i];			
			?>
			<div class="form-group">
			    <div class="col-sm-3">
			      <input type="text" class="form-control" name="tag_name<?php echo $i; ?>" placeholder="Tag Name" value="<?php echo $v[0]; ?>">
			    </div>
			    <div class="col-sm-3">
			      <input type="text" class="form-control" name="tag_value<?php echo $i; ?>" placeholder="Tag Value" value="<?php echo $v[1]; ?>">
			    </div>
			    <div class="col-sm-3">
			      <input type="text" class="form-control" name="test_name<?php echo $i; ?>" placeholder="Test Name" value="<?php echo $v[2]; ?>">
			    </div>
			    <div class="col-sm-3">
			      <input type="text" class="form-control" name="condition_test_name<?php echo $i; ?>" placeholder="Main Test Name" value="<?php echo !empty($v[3]) ? $v[3] : "" ; ?>">
			    </div>
			</div>
			<?php
				} //
				
				for($j=0;$j<4;$j++,$i++){ 
			?>
			<div class="form-group">
			    <div class="col-sm-3">
			      <input type="text" class="form-control" name="tag_name<?php echo $i; ?>" placeholder="Tag Name" value="">
			    </div>
			    <div class="col-sm-3">
			      <input type="text" class="form-control" name="tag_value<?php echo $i; ?>" placeholder="Tag Value" value="">
			    </div>
			    <div class="col-sm-3">
			      <input type="text" class="form-control" name="test_name<?php echo $i; ?>" placeholder="Test Name" value="">
			    </div>
			    <div class="col-sm-3">
			      <input type="text" class="form-control" name="condition_test_name<?php echo $i; ?>" placeholder="Main Test Name" value="">
			    </div>
			</div>
			<?php } ?>
			<div class="clearfix" ></div>
			<div class="form-group">
			  <label for="map_code">Mapping Code:</label>
			  <textarea class="form-control" rows="5" id="map_code" name="map_code"><?php echo $map_code; ?></textarea>
			</div>
			<br/>
			<center>
			<button type="submit" name="submit_btn" value="Done" class="btn btn-success">Done</button>
			</center>
		</form>
		
	</body>
</html>