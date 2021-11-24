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
$Height = $_SESSION['wn_height']-280;
$scriptAlert = '';
$target_dir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/gn_images";

$logo_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'delete_status','0');
if(isset($_POST["submit"])){
	$filePathDel = '';
	$n			= str_replace(array('{','}'),'',$_POST['n']);
	$n			= str_replace(' ','_',$_POST['n']);
	$edit_id	= intval($_POST['edit_id']);
	// Check if image file is a actual image or fake image
	$check = getimagesize($_FILES["f"]["tmp_name"]);
	if($check){
		if(!is_dir($target_dir) || !file_exists($target_dir)){
			$old_umask = umask(0);
			mkdir($target_dir,0777);
			umask($old_umask);
		}else{
			@chmod($target_dir,0777);
		}
	}
	if($check !== false || $edit_id>0){
		$cp = false;
		if($check !== false) {
			if(is_dir($target_dir) && file_exists($target_dir)){
				$file_name = time().basename($_FILES["f"]["name"]);
				$file_name = str_ireplace('%20','',trim($file_name));
				$file_name = str_ireplace(' ','',$file_name);
				$db_filepath = $target_dir.'/'.$file_name;
				$http_host=$_SERVER['HTTP_HOST']; if($protocol==''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
				$imageFileType = strtolower(pathinfo($db_filepath,PATHINFO_EXTENSION));
				//if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif"){
				if(mime_content_type($db_filepath) && (strtolower(mime_content_type($db_filepath))!='image/jpeg' || strtolower(mime_content_type($db_filepath))!='image/png' || strtolower(mime_content_type($db_filepath))!='image/gif' ) ){
					$scriptAlert = 'Sorry, only JPG, JPEG, PNG &amp; GIF files are allowed.';
				}else{
					$cp = copy($_FILES["f"]["tmp_name"],$db_filepath);
				}
			}else{
				$scriptAlert = 'Unable to create disc space to save uploaded content.';	
			}
		}
		
		if($cp && $edit_id==0){//If copied and it is new record->then insert.
			$db_filepath = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$db_filepath);
			$q = "INSERT INTO document_logos SET var_name='".$n."', img_url='".$db_filepath."', file_type='".$check["mime"]."', width='".$check[0]."', height='".$check[1]."'";
		}else if($cp && $edit_id>0){//if copied and it is old record->then update.
			$del_res = imw_query("SELECT img_url FROM document_logos WHERE id='".$edit_id."' LIMIT 0,1");
			if($del_res && imw_num_rows($del_res)==1){
				$del_rs = imw_fetch_assoc($del_res);
				// Fix issue for unlinking new and old image
				$filePathDel = $target_dir.'/'.basename($del_rs['img_url']);
				// unlink($target_dir.'/'.basename($del_rs['img_url']));
			}
			$db_filepath = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$db_filepath);
			$q = "UPDATE document_logos SET var_name='".$n."', img_url='".$db_filepath."', file_type='".$check["mime"]."', width='".$check[0]."', height='".$check[1]."' WHERE id='".$edit_id."'";
		}else if(!$cp && $edit_id>0){//if img not provided, then only update variable caption.
			$q = "UPDATE document_logos SET var_name='".$n."' WHERE id='".$edit_id."'";
		}else if(!$cp){//if img not uploaded and its new record, no saving..
			$scriptAlert = "Unable to copy image to server.";
		}else{
			$scriptAlert = "Some error occurred.";
		}

		$res = imw_query($q);
		if(!$res) {
			$scriptAlert = "Unable to save information.";
			if(imw_error()!='' && strpos(strtolower(imw_error()),'duplicate')>=0) $scriptAlert .= "<br><b>Duplicate Variable Caption not allowed.</b>";
		}
		if(imw_affected_rows() > 0) {
			if(file_exists($filePathDel)) {
				unlink($filePathDel);
			}
			echo "<script type='text/javascript'>window.location.reload()</script>";
		}
	}
}
?>
	<style type="text/css">
		.saved_img,.variable_caption{cursor:pointer;}
		.variable_caption:after{content:"}"}
		.variable_caption:before{content:"{"}
	</style>
	<script type="text/javascript">
		var scriptAlert = '<?php echo $scriptAlert;?>';
		var no_preview = '<img src="<?php echo $library_path; ?>/images/nopreview.png" style="height:<?php echo ($Height-20); ?>px;">';
	</script>
	<div class="row pd10">
		<div class="col-sm-4">
			<div class="adminbox">
				<div class="head">				
					Upload Document
				</div>
				<div class="tblBg">
					<div class="chooseuplod">
						<img src="<?php echo $library_path; ?>/images/doc_upload.png" alt=""/>
						<div class="clearfix"></div>
						<label class="btn-upload" for="f">
							<input name="f" id="f" onChange="checkImg(this.files,this);" type="file" class="form-control hidden" autocomplete="off">Choose files to Upload 
						</label>
					</div>
					<div class="clearfix"></div>
					<div class="form-group logovari">
						<label for="n">Logo Variable Caption</label>
						<input type="text" class="form-control" name="n" id="n">
					</div>
					<div class="clearfix"></div>
					<div class="text-center">
						<input type="submit" name="submit" id="submit" value="SAVE" class="btn btn-save">
						<input type="reset" value="CANCEL" class="btn btn-cancel" onClick="resetImg();">
					</div>
				</div>
			</div>
			<input type="hidden" name="edit_id" id="edit_id" value="">
			<input type="hidden" id="previous_img">
			<div class="clearfix"></div>
			<div class="adminbox">
				<div class="head">
					Saved Logo Variables
				</div>
				<div class="tblBg">
					<div id="div_AllSubTags" class="row">
						<?php 
							foreach($logo_data as $obj){
								if($obj['width']>150) $obj['width'] = 150;
								if($obj['width']<=350 && $obj['height']>120) $obj['height'] = 100;	
							?>
								<div class="col-sm-3">
									<img class="saved_img" src="<?php echo $obj['img_url'];?>" style="width:<?php echo $obj['width'];?>px; height:<?php echo $obj['height'];?>px;">
									<div class="savevarib">
										<span class="fr icon_delete glyphicon glyphicon-remove pull-right" style="color:red;" title="Delete" onClick="delTemplate(<?php echo $obj['id'];?>)"></span><span class="variable_caption text-center" title="Click to Edit" record_id="<?php echo $obj['id'];?>"><?php echo $obj['var_name'];?></span>
									</div>
								</div>
							<?php
							}
						?>	
					</div>
				</div>
			</div>
			</div>
			<div class="col-sm-8">
				<div id="preview_div" class="adminbox text-center">
					<img src="<?php echo $library_path; ?>/images/nopreview.png">
				</div>
			</div>
		</div>
