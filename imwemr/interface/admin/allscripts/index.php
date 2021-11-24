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
	Purpose: Allscripts Integration Admin Interface
	Access Type: Direct
*/

include_once('../admin_header.php');

/*Get Unity Credentials*/
$creds = array();
$sql = 'SELECT * FROM `as_credentials` WHERE `id`=1';
$resp = imw_query($sql);
if( $resp && imw_num_rows( $resp ) > 0)
{
	$creds = imw_fetch_assoc( $resp );
}


/*Count of the directory Items*/
$itemCount = array();
$sqlCount = 'SELECT `Dictionary` AS \'dictionary\', COUNT(`id`) AS \'total\' FROM `as_dictionary` GROUP BY `Dictionary`';
$respCount = imw_query($sqlCount);
if( $respCount && imw_num_rows($respCount) > 0 )
{
	while( $row = imw_fetch_object( $respCount ) )
	{
		$itemCount[$row->dictionary] = $row->total;
	}
}


/*Problems Count*/
$totalProblems = 0;
$problemCount = imw_query('SELECT COUNT(`uid`) FROM `as_problems`');
if( $problemCount )
{
	$totalProblems = imw_fetch_assoc($problemCount);
	$totalProblems = array_pop($totalProblems);
}
?>
<style>
	.input-group-addon{padding: 6px 12px !important;}
	.panel-body{padding: 5px!important;}
	.panel-primary{border-color: #673782 !important;}
	span.input-group-addon+input.form-control{height: 28px !important;}

	.useOrgId{border-bottom: 1px solid #FFF !important;}
	label.switch{height:21px; margin-bottom:0; width:47px;}
	.slider.round{height: 20px;}
	.slider.round::before{bottom:1px; left:1px; height:18px; width:18px; }
	input:checked+.slider.round{background-color: #398439;}
	input+.slider.round{background-color: #c9302c;}

</style>
<body>
	<div class="container-fluid">
		<div class="whtbox">
			<div class="row pd5">
				<div class="col-sm-5">
					<div class="row">
						<div class="panel-group">
							<div class="panel panel-primary">
								<div class="panel-heading purple_bar">Unity API Credentials</div>
								<input type="hidden" name="cred_record_id" id="cred_record_id" value="<?php echo (isset($creds['id']))?(int)$creds['id']:''; ?>" />
								<div class="panel-body">
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Appname</span>
												<input type="text" name="appname" id="appname" value="<?php echo (isset($creds['appname']))?$creds['appname']:''; ?>" class="form-control">
											</div>
										</div>
									</div>
									
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Username</span>
												<input type="text" name="username" id="username" value="<?php echo (isset($creds['username']))?$creds['username']:''; ?>" class="form-control">
											</div>
										</div>
									</div>	
									
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Password</span>
												<input type="password" name="password" id="password" value="<?php echo (isset($creds['password']))?$creds['password']:''; ?>" class="form-control">
											</div>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Unity Service URL</span>
												<input type="text" name="url" id="url" value="<?php echo (isset($creds['url']))?urldecode($creds['url']):''; ?>" class="form-control">
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="panel panel-primary">
								<div class="panel-heading purple_bar">
									<div class="row">
										<div class="col-sm-11">
											<span>Ubiquity Unity API Credentials</span>
										</div>
										<div class="col-sm-1 text-center">
											<label class="switch">
												<input type="checkbox" name="ubqEnabled" id="ubqEnabled" value="1" autocomplete="off" <?php echo (($creds['ubq_status'])=='1')?'checked="checked"':''; ?> />
												<span class="slider round"></span>
											</label>
										</div>
									</div>
								</div>
								<div class="panel-body">
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Appname</span>
												<input type="text" name="ubqAppname" id="ubqAppname" value="<?php echo (isset($creds['ubq_appname']))?$creds['ubq_appname']:''; ?>"  class="form-control">
											</div>
										</div>
									</div>
									
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Username</span>
												<input type="text" name="ubqUsername" id="ubqUsername" value="<?php echo (isset($creds['ubq_username']))?$creds['ubq_username']:''; ?>"  class="form-control">
											</div>
										</div>
									</div>	
									
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Password</span>
												<input type="password" name="ubqPassword" id="ubqPassword" value="<?php echo (isset($creds['ubq_password']))?$creds['ubq_password']:''; ?>"  class="form-control">
											</div>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Unity Service URL</span>
												<input type="text" name="ubqUrl" id="ubqUrl" value="<?php echo (isset($creds['ubq_url']))?urldecode($creds['ubq_url']):''; ?>" class="form-control">
											</div>
										</div>
									</div>
									
								</div>
							</div>

							<div class="panel panel-primary">
								<div class="panel-heading purple_bar useOrgId">
									<div class="row">
										<div class="col-sm-11">
											<span>Use Organization Id in API calls</span>
										</div>
										<div class="col-sm-1 text-center">
											<label class="switch">
												<input type="checkbox" name="useOrdId" id="useOrdId" value="1" autocomplete="off" <?php echo (($creds['use_org_id'])=='1')?'checked="checked"':''; ?> />
												<span class="slider round"></span>
											</label>
										</div>
									</div>
								</div>
								<div class="panel-heading purple_bar">
									<div class="row">
										<div class="col-sm-12">
											<span>Touch Works Generic User Login</span>
										</div>
									</div>
								</div>
								<div class="panel-body">
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Username</span>
												<input type="text" name="ehrUsername" id="ehrUsername" value="<?php echo (isset($creds['ehr_username']))?$creds['ehr_username']:''; ?>"  class="form-control">
											</div>
										</div>
									</div>	
									
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Password</span>
												<input type="password" name="ehrPassword" id="ehrPassword" value="<?php echo (isset($creds['ehr_password']))?$creds['ehr_password']:''; ?>"  class="form-control">
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
							
							<div class="panel panel-primary">
								<div class="panel-heading purple_bar">
									<span>Sync Dictionaries</span>
								</div>
								<div class="panel-body">
									<div class="col-sm-12">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">Select Dictionary</span>
												<select class="selectpicker" id="dictionaryName" data-width="100%">
													<option value="">Please select</option>
													<option value="clinical_progress">Clinical Progress</option>
													<option value="problem_category">Problem Category</option>
													<option value="clinical_severity">Clinical Severity</option>
													<option value="problems">Problems</option>
													<option value="problem_status">Problem Status</option>
													<option value="allergy_status">Allergy Status</option>
													<option value="allergy_category">Allergy Category</option>
													<option value="allergen_reaction">Allergen Reaction</option>
													<option value="alert_type">Alert Type</option>
													<option value="relationship">Relationship</option>
													<option value="laterality_qualifier">Laterality Qualifier</option>
												</select>
											</div>
										</div>
									</div>
									
									<div class="col-sm-12">
										<div class="form-group text-center">
											<button class="btn btn-success" id="syncDictionary" type="button">Save</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-7">
					<div class="panel panel-primary">
						<div class="panel-heading purple_bar">Data Synced</div>
						<div class="panel-body">
							<table class="table table-bordered table-striped resultset credentials">
								<tbody>
									<tr>
										<td class="lpad4">Clinical Progress</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Management_Effective_DE']))?$itemCount['Management_Effective_DE']: ''; ?></td>
									</tr>
									<tr class="alt">
										<td class="lpad4">Problem Category</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Problem_Category_DE']))?$itemCount['Problem_Category_DE']: ''; ?></td>
									</tr>
									<tr>
										<td class="lpad4">Clinical Severity</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Clinical_Severity_DE']))?$itemCount['Clinical_Severity_DE']: ''; ?></td>
									</tr>
									<tr class="alt">
										<td class="lpad4">Problems</td>
										<td class="leftborder lpad4"><?php echo $totalProblems; ?></td>
									</tr>
									<tr>
										<td class="lpad4">Problem Status</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Problem_Status_DE']))?$itemCount['Problem_Status_DE']: ''; ?></td>
									</tr>
									<tr class="alt">
										<td class="lpad4">Allergy Status</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Allergy_Status_DE']))?$itemCount['Clinical_Severity_DE']: ''; ?></td>
									</tr>
									<tr>
										<td class="lpad4">Allergy Category</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Allergy_Category_DE']))?$itemCount['Allergy_Category_DE']: ''; ?></td>
									</tr>
									<tr class="alt">
										<td class="lpad4">Allergen Reaction</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Allergen_Reaction_DE']))?$itemCount['Allergen_Reaction_DE']: ''; ?></td>
									</tr>
									<tr>
										<td class="lpad4">Alert Type</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Alert_Type_DE']))?$itemCount['Alert_Type_DE']: ''; ?></td>
									</tr>
									<tr class="alt">
										<td class="lpad4">Relationship</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Relationship_DE']))?$itemCount['Relationship_DE']: ''; ?></td>
									</tr>
									<tr>
										<td class="lpad4">Laterality Qualifier</td>
										<td class="leftborder lpad4"><?php echo (isset($itemCount['Laterality_Qualifier_DE']))?$itemCount['Laterality_Qualifier_DE']: ''; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		<?php
		if( isset($_GET['save']) && $_GET['save']=='1')
			echo 'top.alert_notification_show ( \'Directory Synced Successfully.\' )';
		if( isset($_GET['save']) && $_GET['save']=='2')
			echo 'top.alert_notification_show ( \'Credentials Saved Successfully.\' )';
		?>
	</script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_allscripts.js"></script>
	
<?php include_once('../admin_footer.php'); ?>