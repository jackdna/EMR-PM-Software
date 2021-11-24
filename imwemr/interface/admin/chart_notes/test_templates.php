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
<script>var isDssEnable = '<?php echo isDssEnable(); ?>';</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_test_templates.js"></script>
<script type="text/javascript">
function testPreferences(){
 window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/chart_notes/interpretation_profiles.php';	
}
function call_save_test_layout(){
window.template_test_frame.save_test_layout();
setTimeout(function(){$("#myModal2 #module_buttons2 .btn-danger").trigger("click");}, 2000);
}
</script>
<body>
	<form name="test_templates" id="test_templates">
	<input type="hidden" name="action" value="test_templates_switch" />
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" onClick="switch_allVals(this)" name="select_all" id="select_all" <?php echo $allChecked; ?> /><label for="select_all"></label></div></th>
						<th>Test name<span></span></th>
						<th style="width:125px;">Test Manager<span></span></th>
				  </tr>
				</thead>
				<tbody id="tests_data"></tbody>
			</table>
		</div>
	</div>
	</form>
	<div id="myModal" class="modal" role="dialog">
		<div class="modal-dialog modal-sm"> 
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
                <form name="add_edit_frm" id="add_edit_frm" onSubmit="saveTemplate();return false;">
                <div class="modal-body">
                    <div class="form-group">
                        <div>
                            <input type="hidden" name="t_template_id" id="t_template_id" />
                            <label for="temp_name">Template Name</label>
                            <input class="form-control" name="temp_name" id="temp_name" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <input type="checkbox" name="t_manager" id="t_manager" value="1"><label for="t_manager">Show in Test Manager</label>
                        </div>
                    </div>
                </div>
                <div id="module_buttons" class="ad_modal_footer modal-footer">
                    <button type="button" onClick="saveTemplate();" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            	</form>
			</div>
		</div>
	</div>
    <div id="myModal2" class="modal" role="dialog">
		<div class="modal-dialog" style="width:1200px;"> 
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div class="form-group pull-right" style="margin-right:50px;">
                        <div class="checkbox">
                            <input type="checkbox" name="t_manager1" id="t_manager1" value="1"><label for="t_manager1">Show in Test Manager</label>
                        </div>
                    </div>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
                <div class="modal-body">
                    <iframe src="about:blank" name="template_test_frame" id="template_test_frame" class="border" width="99%" frameborder="0" height="<?php echo $_SESSION['wn_height']-460;?>px"></iframe>
                </div>
                <div id="module_buttons2" class="ad_modal_footer modal-footer">
                    <button type="button" onClick="call_save_test_layout()" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
			</div>
		</div>
	</div>
<?php	
	require_once("../admin_footer.php");
?>