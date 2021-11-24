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
	Purpose: Define classes and functions for providers listing
	Access Type: Direct
*/
include_once("../admin_header.php");
include_once($GLOBALS['srcdir'].'/classes/admin/class.providers.php');
$library_path = $GLOBALS['webroot'].'/library';
$providers = new Providers();
?>
	<script>
		var vocabulary = <?php echo json_encode($providers->vocabulary);?>;
		var hAlg = '<?php echo constant('HASH_METHOD');?>';
		var init_page = true;
	</script>
	<script src='<?php echo $library_path; ?>/js/grid_color/spectrum.js'></script>
	<link rel='stylesheet' href='<?php echo $library_path; ?>/js/grid_color/spectrum.css' />
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/md5.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/admin/admin_providers.js?<?php echo filemtime(dirname(__FILE__).'/../../../library/js/admin/admin_provider.js') ?>"></script>	

<style>
	.sp-preview{width:80%!important;border:none!important;}
	.sp-replacer{border:none!important;}
	.chk_sel~label:before, .chk_sel~label:after{ top:-2px; }
	.prov_lbl{margin-top:3px;}
</style>
	<body cz-shortcut-listen="true">
		<style>
			.no_style_ul{padding:0px;margin:0px;list-style:none;text-align:left;}
		</style>
		<div class=" container-fluid">
    	<div class="whtbox">
        <div class="table-responsive" style="">
        	<textarea id="hidd_reason_text" style="display:none;"></textarea>
			<input type="hidden" name="ord_by_field" id="ord_by_field" value="lname">
			<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
			<table class="table table-bordered adminnw">
				<thead>
					<tr>
						<th width="2%">
							<div class="checkbox">
								<input type="checkbox" name="chk_sel_all" id="chk_sel_all" onClick="sel_all(this.checked);">  
							<label for="chk_sel_all"></label>
							</div>
						</th>
						<th width="30%">
							<div class="row ">
								<div class="col-sm-3 pointer prov_lbl" onClick="get_provider_listing('','u.lname',this);">
									<label>Provider</label><span></span>
								</div>	
								<div class="col-sm-9 form-inline content_box">
									<div class="row">
										<div class="col-sm-5">
											<div class="input-group">
												<input type="text" id="search_provider_name" size="15" value="" placeholder="Search..." autocomplete="off" class="form-control">
												<label class="input-group-addon pointer" onClick="javascript:get_provider_listing('search'); return false;">
													<i class="glyphicon glyphicon-search" aria-hidden="true"></i>
												</label>
											</div>
										</div>	
										<div class="col-sm-7">
											<select class="form-control minimal" style="width:90%; margin:3px;" name="show_prov_drop" id="show_prov_drop" onChange="get_provider_listing(this.value);">
												<option value="show all" selected>Active</option>
												<option value="deleted" <?php echo ($_REQUEST['srh_del_status'])?'selected':'';?>>Deleted</option>
											</select>
										</div>	
									</div>
								</div>	
							</div>
						</th>
						<th width="14%" class="pointer" onClick="get_provider_listing('','ut.user_type_name',this);">Type<span></span></th>
						<th width="16%" class="pointer" onClick="get_provider_listing('','groupName',this);">User Group<span></span></th>
						<th width="11%" class="pointer" onClick="get_provider_listing('','facilityName',this);">Default Facility<span></span></th>
						<th width="9%" align="center">Color</th>
						<th width="11%" align="center">Password</th>
						<th width="7%" align="center">Lock/Unlock</th>
					</tr>
				</thead>
				<tbody id="result_set">
					<?php echo $providers->providers_listing();	?>
         		</tbody>
         	</table>
      	</div>
        
        <div class="clearfix"></div>
     	</div>
   		
      <div id="reset_password"></div>
      
      <div id="direct_credentials"></div>
	  
	  <div id="refill_direct_access_portal"></div>
      
      <div id="zeiss_credentials"></div>
      
      
      <?php 
			if( ($_REQUEST['pro_id'] || $_REQUEST['add_new'] == 'yes') && $_REQUEST['err'] == '' && $_REQUEST['done'] != 'true' ){
				echo $providers->providers_form();
				echo '
				<script>
					$("#provider_add_edit_div").modal(\'show\');
					top.fmain.setColorPicker($(\'#EnableOpt7\').find(\'.grid_color_picker\').val());
				</script>';
				echo '<script type="text/javascript" src="'.$library_path.'/js/admin/admin_prvlgs_popup.js"></script>';
			}
		?>	
      
    </div>
<?php 
	include_once('../admin_footer.php');
?>