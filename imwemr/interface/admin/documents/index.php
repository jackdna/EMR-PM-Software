<?php
/*
 /*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 
 File: index.php
 Purpose: A router to route to selected document section
 Access Type: Indirect Access.
*/
require_once("../admin_header.php");
require_once($GLOBALS['srcdir']."/classes/admin/documents/document.class.php");

$sub_section = (isset($_REQUEST['sub']) && empty($_REQUEST['sub']) == false) ? $_REQUEST['sub'] : false;
$doc_obj = new Documents($_REQUEST['showpage'],$sub_section);

$perform_action = (isset($_REQUEST['perform_action']) && empty($_REQUEST['perform_action']) == false) ? $_REQUEST['perform_action'] : 'add';
$order_asc_desc = (isset($_REQUEST['sort_by'])) ? $_REQUEST['sort_by'] : 'ASC';
$sort_first_time = (isset($_REQUEST['sort_first_time'])) ? $_REQUEST['sort_first_time'] : 'yes';

//This array is used to access php variables in js file
	//js_php_arr['field_name'] = 'field_value' --> PHP
	//var foo = js_array.field_name --> jQuery
$js_php_arr = array();
$doc_base_path = $doc_obj->get_base_path();
$js_php_arr['base_path'] = $doc_base_path;
$js_php_arr['ajax_url'] = $doc_base_path.'/ajax_handler.php';
$js_php_arr['billing_server'] = $billing_global_server_name;
echo '<script>top.show_loading_image("show");</script>';
?>
<body>
	<div class="whtbox documents_sec">
		<form id="template_form" name="template_form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="perform_action" value="<?php echo $perform_action; ?>" id="perform_action">
			<input type="hidden" name="current_tab" value="<?php echo $doc_obj->current_tab; ?>" id="current_tab">
			<input type="hidden" name="sort_order" id="sort_order" value="<?php echo $order_asc_desc; ?>" data-first-time="<?php echo $sort_first_time; ?>">
			<?php
				//Education/Instructions uses same page i.e /education/index.php
				$folder = $_REQUEST['showpage'];
				if($folder == '') $folder = 'collection';
				$path = $GLOBALS['fileroot']. '/interface/admin/documents/'.$folder.'/index.php';
				include_once $path;
			?>
		</form>
		<!-- Scanner Box -->
		<div class="col-sm-9  pull-right" style="position:absolute; top:185px; right:0;">
			<div id="divScnDocId" class="col-sm-12 pt10 hide">
				<div class="row">
					<div class="col-sm-9" >
						<?php include_once 'scan_documents.php'; ?>
					</div>
					<div class="col-sm-3">
						 <?php if($thmb_path && $doc_from == 'scanDoc') {?>
								<img name="thumbImgScnUpld" class="img-responsive img-thumbnail pointer" src="<?php echo $thmb_path;?>" onClick="javascript:window.open('<?php echo $upload_dir_web.$scan_doc_file_path;?>','','width=800,height=650,resizable=yes,scrollbars=yes');" <?php echo show_tooltip('Click here to view','top'); ?>>
						<?php }?>
					</div>	
				</div>
			</div>
		</div>
					
	</div>
<?php
//Includes modals used in Document section
include_once('document_modals.php');

$com_php_var = $doc_obj->get_js_arr($doc_obj->current_tab); 	//Consists of variables needed in js file
foreach($com_php_var as $key => $val){
	$js_php_arr[$key] = $val;	
}
$js_php_arr = json_encode($js_php_arr);

// jQuery variables
$js_vars =  '
<script>
	var js_php_arr = '.$js_php_arr.';
</script>';
echo $js_vars;
?>
	<script src="<?php echo $library_path ?>/js/admin/admin_documents.js"></script>
<?php 
	require_once("../admin_footer.php");
?>