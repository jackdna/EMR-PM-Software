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
require_once("../admin_header.php");
require_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
require_once($GLOBALS['srcdir']."/classes/work_view/exam_options.php");

$uploaddir = $uploadPath = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
$drawicon = "/drawicon";
if(is_dir($uploaddir.$drawicon)){	//
}else{	
	//make dir
	mkdir($uploaddir.$drawicon,0700);
	//make dir tmp
	mkdir($uploaddir.$drawicon."/upload",0700); //upload
	mkdir($uploaddir.$drawicon."/L",0700);	
}

function img_resize($pth,$type,$width=200, $height=200){
	if(empty($type)){ return "";  }
	
	global $uploaddir, $drawicon;
	$tmp_upload_folder = $uploaddir.$drawicon; 
	$filename =basename($pth);

	$filename = str_replace(array(" ","-"),"_", $filename);
	$filename = preg_replace('/[^A-Za-z0-9\_\.]/', '', $filename);
	$filename ="/".$filename;

	// Get new dimensions
	list($width_orig, $height_orig) = getimagesize($pth);

	$ratio_orig = $width_orig/$height_orig;
	
	if($width_orig > $width || $height_orig > $height ){
		if ($width/$height > $ratio_orig) {
		   $width = $height*$ratio_orig;
		} else {
		   $height = $width/$ratio_orig;
		}	
		
		$image_p = imagecreatetruecolor($width, $height);
		if($type == "image/jpeg" || $type == "image/jpg"){
			$image = imagecreatefromjpeg($pth);			
		}else if($type == "image/png"){
			$image = imagecreatefrompng($pth);			
		}
					
		$color = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
		imagecolortransparent($image_p, $color); 
		imagefill($image_p, 0, 0, $color);
		imagesavealpha($image_p, true);
		
		if(imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig)){
			//echo "CR done";
		}
		if($type == "image/jpeg" || $type == "image/jpg"){
			imagejpeg($image_p, $tmp_upload_folder.$filename, 100);
		}else if($type == "image/png"){
			imagepng($image_p, $tmp_upload_folder.$filename, 9);
		}
	}else{
		copy($pth,$tmp_upload_folder.$filename);		
	}
	return $filename;
}

if(isset($_POST["elem_edit_drawicon"]) && !empty($_POST["elem_edit_drawicon"])){
	if($_POST["elem_edit_drawicon"] == "2"){ // DELETE
		$arr_drwico_del  = $_POST["elem_drawicon_del"];
		$len = count($arr_drwico_del);
		for($i=0;$i<$len;$i++){
			if(!empty($arr_drwico_del[$i])){
				$sql = "DELETE FROM chart_drawicon WHERE id = '".$arr_drwico_del[$i]."' ";
				$row = sqlQuery($sql);
			}	
		}
	}
	// --
	if($_POST["elem_edit_drawicon"] == "1"){ 
	//Update  Symptoms
	$len2 = count($_POST["elem_drawicon_edid"]);
		for($i=0;$i<$len2;$i++){
			$drwico_edid  = $_POST["elem_drawicon_edid"][$i];	
			$drwico_symptom = $_POST["elem_drawicon_symptom"][$i];
			$drwico_name = $_POST["elem_drawicon_name"][$i];
			if(isset($_FILES["elem_drawicon_file".$i])){
				$temp = explode(".", $_FILES["elem_drawicon_file".$i]["name"]);
				$extension = end($temp);
				$type = $_FILES["elem_drawicon_file".$i]["type"];
				if(($type == "image/jpeg" || $type == "image/jpg" || $type == "image/png") && wv_check_mime("img", $_FILES["elem_drawicon_file".$i]["tmp_name"])){
					
					//give new name so that name issues can be avoided --
					$new_file_name = "drwico".$i."_".time().".".$extension;
					//--
					if(move_uploaded_file($_FILES["elem_drawicon_file".$i]["tmp_name"], $uploaddir.$drawicon."/upload/".$new_file_name)){
						copy($uploaddir.$drawicon."/upload/" . $new_file_name, $uploaddir.$drawicon."/L/".$new_file_name);
						$drwico_filename = img_resize($uploaddir.$drawicon."/upload/".$new_file_name, $type,20,20);//
						if(!empty($drwico_filename)){
							$sql="";
							if(empty($drwico_edid)){
								//insert into db base
								$sql = "INSERT INTO chart_drawicon (id, drwico_name, drwico_path, drwico_symptom) values(NULL, '".imw_real_escape_string($drwico_name)."', '".imw_real_escape_string($drwico_filename)."', '".imw_real_escape_string($drwico_symptom)."') ";	
							}else if(!empty($drwico_edid)){	
								$sql = "UPDATE chart_drawicon SET drwico_name='".imw_real_escape_string($drwico_name)."' , drwico_path='".imw_real_escape_string($drwico_filename)."', drwico_symptom='".imw_real_escape_string($drwico_symptom)."' WHERE id = '".$drwico_edid."'  ";
								
							}
							if(!empty($sql)){
								$row=sqlQuery($sql);		
							}
						}		
					}
				}else{
					$msg = "Invalid file";		
				}
			}else{
				if(!empty($drwico_edid)){	
					$sql = "UPDATE chart_drawicon SET drwico_name='".imw_real_escape_string($drwico_name)."' ,  drwico_symptom='".imw_real_escape_string($drwico_symptom)."' WHERE id = '".$drwico_edid."'  ";			
					$row=sqlQuery($sql);
				}
			}
		}
	}
	header("location: drawicon.php");
	exit();	
}
?>
<script type="text/javascript">
	var arrTHSym = new Array(<?php echo "'".implode("','",$arrMain)."'";?>);
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_drawicon.js"></script>
<body>	
	<form method="post" name="frm_drawicon" action="" id="frm_drawicon" enctype="multipart/form-data" >
		<input type="hidden" id="elem_edit_drawicon" name="elem_edit_drawicon" value="1">
		<div class="whtbox">
			<div class="table-responsive respotable adminnw">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>         
							<th class="alignCenter">Name</th>                                                           
							<th class="alignCenter">Drawing Icon</th>  
							<th class="alignCenter">Symptom</th>  
							<th class="alignCenter" style="width:20px;">Delete</th>
						</tr>
					</thead>
					<tbody id="tblupload">
						<?php
						$sql = "SELECT * FROM chart_drawicon ORDER BY id ";
						$res = sqlStatement($sql);
						for($i=0; $row=sqlFetchArray($res);$i++){
						$id = $row["id"];
						$name = $row["drwico_name"];
						$pth = $row["drwico_path"];
						$symptom = $row["drwico_symptom"];

						$uploaddir = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH');
						$tmp_pth = $uploaddir.$drawicon.$pth;			

						echo "<tr><td>".
						"<input type=\"text\" class=\"form-control\" name=\"elem_drawicon_name[]\" value=\"".$name."\"  >".
						"</td>                                                           ".
						"<td  style=\position:relative;\">".
						"<img src=\"".$tmp_pth."\" style=\"max-width:50px;display:inline-block;position:absolute;top:2px;\">".
						"<input type=\"file\" id=\"elem_drawicon_edit".$i."\" value=\"Edit File\" style=\"margin-left:60px;\" onclick=\"setfiletag(this)\">".
						"</td>   ".

						"<td >".
						"<input type=\"text\" class=\"form-control\" id=\"elem_drawicon_symptom$i\"  name=\"elem_drawicon_symptom[]\" value=\"".$symptom."\">".				
						"</td>".				

						"<td style=\"width:20px; padding-left:20px;\" class=\"text-center\">".
						"<div class=\"checkbox\"><input type=\"checkbox\" name=\"elem_drawicon_del[]\" id=\"elem_drawicon_del[]$i\" value=\"".$id."\"><label for=\"elem_drawicon_del[]$i\">&nbsp;</label></div>".
						"<input type=\"hidden\" name=\"elem_drawicon_edid[]\" value=\"".$id."\">".
						"</td></tr>";
					}
					?>
					</tbody>
				</table>
		  </div>
		  <input type="hidden" id="elem_cntr" name="elem_cntr" value="<?php echo $i; ?>">
		</div>
	</form>	
<?php	
	require_once("../admin_footer.php");
?>