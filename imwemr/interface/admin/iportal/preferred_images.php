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
?>
<script type="text/javascript">
function del_user_img(del_id,cnfrm){
	if(typeof(cnfrm)=="undefined") {
		top.fancyConfirm("Are you sure you want to delete this image ?<br />This may cause the problem of displaying image in iPortal.", '', 'top.fmain.del_user_img("'+del_id+'",true)');
		return;
	}
	else{
		window.top.fmain.location.href = '../admin/iportal/del_preferred_img.php?pf_id='+del_id; /*Set Path relative interface/core*/
	}
}
</script>
<body>
<div class="whtbox">
	<div class="row">
		<div class="col-sm-12">
			<div class="head">
				<span>iPortal - Preferred Images</span>	
			</div>
		</div>
		<div class="col-sm-12">
			<div class="tblBg">
				<div class="row">
					<div class="col-sm-12">
						<form method="post" action="upload_preferred_images.php" enctype="multipart/form-data">
							<div class="row">
							<div class="col-sm-3"><input type="file" name="u_img" id="u_img" class="btn btn-primary" /></div>
							<div class="col-sm-9"><input type="submit" value="Upload Image" id="u_submit" name="u_submit" class="btn btn-success" />
								(<strong> Note: </strong> Only JPG, JPEG, PNG, GIF image types are allowed)</div>
							</div>
						</form>
					</div>	
				</div>
			</div>	
		</div>
		<div class="col-sm-12">
			<div class="tblBg">
				<?php
					$req_qry = "SELECT * FROM iportal_preferred_images";
					$result_obj = imw_query($req_qry);
					if(imw_num_rows($result_obj) == 0){
						echo '<h1 style="color:red;"> No image available </h1>' ;
					} else {
						while($result_data = imw_fetch_assoc($result_obj)){ ?>
						<div class="iportal_pf_img_cl">
							<img src="<?php echo $GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/preferred_images/'.$result_data["name"]; ?>" />
							<a href="# retun false;" onClick="del_user_img(<?php echo $result_data["id"]; ?>);"> DELETE </a>
						</div>	
						<?php		
						}	
					}
				?>
			</div>	
		</div>	
	</div>
</div> 
<script type="text/javascript">
	var msg = '<?php echo $_REQUEST["msg"]; ?>';
	if(msg!=""){
		top.alert_notification_show(msg);
	}
	set_header_title('Preferred Images');
</script>  	
<?php 
	require_once('../admin_footer.php');
?>	