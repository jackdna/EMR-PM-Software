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
	Purpose: DSS Integration Admin Interface
	Access Type: Direct
*/

include_once('../admin_header.php');

// Get DSS credentials from DB
$creds = array();
$sql = 'SELECT * FROM `dss_credentials` WHERE `id`=1';
$resp = imw_query($sql);
if( $resp && imw_num_rows( $resp ) > 0)
{
	$creds = imw_fetch_assoc( $resp );
}
?>
<style>
	.input-group {width:100%!important;}
	.input-group-addon{padding:6px 12px!important;width:20%!important;text-align:right!important;}
	.panel-body{padding:5px!important;}
	.panel-primary{border-color: #673782!important;}
    .form-control {height: 28px!important;}
</style>
<body>
	<div class="container-fluid">
		<div class="whtbox">
			<div class="row pd5">
				<div class="col-sm-5">
					<div class="row">
						<div class="panel-group">
							<div class="panel panel-primary">
								<div class="panel-heading purple_bar">DSS Credentials</div>
								<input type="hidden" name="cred_record_id" id="cred_record_id" value="<?php echo (isset($creds['id']))?(int)$creds['id']:''; ?>" />
								<div class="panel-body">
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Access Code</span>
												<input type="password" name="accessCode" id="accessCode" value="<?php echo (isset($creds['accessCode']))?$creds['accessCode']:''; ?>" class="form-control">
											</div>
										</div>
									</div>
									
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Verify Code</span>
												<input type="password" name="verifyCode" id="verifyCode" value="<?php echo (isset($creds['verifyCode']))?$creds['verifyCode']:''; ?>" class="form-control">
											</div>
										</div>
									</div>	
									
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Menu Context</span>
												<input type="text" name="menuContext" id="menuContext" value="<?php echo (isset($creds['menuContext']))?$creds['menuContext']:''; ?>" class="form-control">
											</div>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">API URL</span>
												<input type="text" name="url" id="url" value="<?php echo (isset($creds['url']))?urldecode($creds['url']):''; ?>" class="form-control">
											</div>
										</div>
									</div>

                                    <div class="col-sm-12">
										<div class="form-group text-center">
											<button class="btn btn-success" id="saveCredentials" type="button">Save</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		<?php
		if( isset($_GET['save']) && $_GET['save']=='2')
			echo 'top.alert_notification_show ( \'Credentials Saved Successfully.\' )';
		?>
	</script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_dss.js"></script>
	
<?php include_once('../admin_footer.php'); ?>