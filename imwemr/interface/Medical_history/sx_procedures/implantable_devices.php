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
include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir'] . "/classes/implantable_devices/class.implantable_device_list.php");
$objImpDeviceList = new implantable_device_list();

$device_detail = array();
$parse_udi = array();
if ((isset($_REQUEST["udi_submit"]) == 'udi_submitted' && $_REQUEST["udi_num"] != '') || (isset($_REQUEST["task"]) == 'comment_popup' && $_REQUEST["task"] != '')) {
	if((isset($_REQUEST["udi_submit"]) == 'udi_submitted' && $_REQUEST["udi_num"] != '')) {
		$udi = urlencode(trim($_REQUEST["udi_num"]));

		$parse_udi = $objImpDeviceList->getParseUdi($udi);
		if (!isset($parse_udi['error'])) {
			$device_detail = $objImpDeviceList->getDeviceByUdi($udi);
		}
		
		if (empty($device_detail)) {
			$details = '<table width="100%"><tr><td>' . isset($parse_udi['error']) ? '<span style="color:red;">'.$parse_udi['error'].'</span>' : 'No record found.' . '</td></tr></table>';
			
			$parms = array();
			$parms['details'] = $details;
			$parms['parse_udi'] = $parse_udi;
			$parms['device_detail'] = $device_detail;
			//$parms['device_desc'] = isset($device_detail['deviceDescription'])?$device_detail['deviceDescription']:'';
			echo json_encode($parms);
			die();
		}
	}
	if (isset($_REQUEST["task"]) == 'comment_popup' && $_REQUEST["task"] != '') {
		$id = $_REQUEST["id"];
		$query = "select comments from lists where id=$id ";
		$sql = imw_query($query);
		$cmnt = imw_fetch_assoc($sql);
        $cmnt['comments'] = (str_replace(array('/','\\'), "", $cmnt['comments']));
		$comments = explode('||', $cmnt['comments']);
		//$parse_udi = 
		$jsonData1 = stripslashes(html_entity_decode($comments[1]));
		$parse_udi = json_decode($jsonData1,true);
		//var_dump($parse_udi);
		$jsonData2 = stripslashes(html_entity_decode($comments[2]));
		$device_detail = json_decode($jsonData2,true);
		
	}
	$details = '';
	$details .= '<table width="100%">';
	$details .= '<h3>Parse UDI Details</h3>';
	$details .= '<tr>
				<td width="20%"><label>Device UDI</label></td>
				<td width="80%">' . $parse_udi['udi'] . '</td>
			</tr>
			<tr>
				<td width="20%"><label>Device ID</label></td>
				<td width="80%">' . $parse_udi['di'] . '</td>
			</tr>
			<tr>
				<td><label>Serial Number</label></td>
				<td>' . $parse_udi['serial_number'] . '</td>
			</tr>
			<tr>
				<td><label>Lot Number</label></td>
				<td>' . $parse_udi['lot_number'] . '</td>
			</tr>
			<tr>
				<td><label>Manufacturing Date</label></td>
				<td>' . date('m-d-Y', strtotime($parse_udi['manufacturing_date'])) . '</td>
			</tr>
			<tr>
				<td><label>Expiration Date</label></td>
				<td>' . date('m-d-Y', strtotime($parse_udi['expiration_date'])) . '</td>
			</tr>
		</table>';
	$details .= '<table width="100%">';
	$details .= '<h3>Device Description</h3>';
	foreach($device_detail['gmdnTerms'] as $gmdn) {
		$details .= '<tr>
						<td width="20%"><label>gmdn PT Name</label></td>
						<td width="80%">' . $gmdn['gmdnPTName'] . '</td>
					</tr>
					<tr>
						<td><label>gmdn PT Definition</label></td>
						<td>' . $gmdn['gmdnPTDefinition'] . '</td>
					</tr>';
	}
	
	$details .= '<tr>
				<td width="20%"><label>Brand Name</label></td>
				<td width="80%">' . $device_detail['brandName'] . '</td>
			</tr>
			<tr>
				<td><label>Model Number</label></td>
				<td>' . $device_detail['versionModelNumber'] . '</td>
			</tr>
			<tr>
				<td><label>MRI Safety Status</label></td>
				<td>' . $device_detail['MRISafetyStatus'] . '</td>
			</tr>
			<tr>
				<td><label>Company Name</label></td>
				<td>' . $device_detail['companyName'] . '</td>
			</tr>
			<tr>
				<td><label>Labeled Contains NRL</label></td>
				<td>' . $device_detail['labeledContainsNRL'] . '</td>
			</tr>
			<tr>
				<td><label>Labeled Contains No NRL</label></td>
				<td>' . $device_detail['labeledNoNRL'] . '</td>
			</tr>';
	
	$details .=	'</table>';

	$parms = array();
	$parms['details'] = $details;
	$parms['parse_udi'] = $parse_udi;
	$parms['device_detail'] = $device_detail;
	//$parms['device_desc'] = isset($device_detail['deviceDescription'])?$device_detail['deviceDescription']:'';
	echo json_encode($parms);
	die();
}

$height = ($_SESSION['wn_height']-450);
?>
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" type="text/css" rel="stylesheet">
<style>
	#div_loading_image{
		margin-top: 0px;
		display: none;
		width: 100%;
		height: 100%;
		top: 0;
		padding-top: 15%;
		background: rgba(0,0,0,0.1);
	}

	#div_loading_text { display: none; }
	h3 {margin-top:5px;}
	@media (min-width: 1200px) {
		.modal-lg {
			width: 1190px;
		}
	}
</style>


<div id="implantable_device" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h4 class="modal-title" id="modal_title">Device Details</h4>
			</div>
			<div class="modal-body mainwhtbox" style="max-height:<?php echo $height.'px'?>; overflow: auto;">
				<form name="udi_frm" id="udi_frm" method="POST" class="form-inline pdl_10">
					<label>UDI Number</label>
					<input type="text" class="form-control" name="udi_num" style="width:70%" id="udi_num" value="" placeholder="Enter UDI Number."/>
					<input type="hidden" class="form-control" name="udi_submit" id="udi_submit" value="udi_submitted"/>
					<input type="hidden" class="form-control" name="rowid" id="rowid" value=""/>
				</form>

				<span style="margin:10px;" id="device_dtl"></span>

			</div>
			<div class="clearfix"></div>
			<div class="modal-footer pd0 panel-footer">
				<div class="row">
					<div class="col-sm-12 text-center pt5 pdb5" id="module_buttons">
						<button type="button" class="btn btn-success" onclick="submit_udi_frm(this);" id="show_detail">Show Detail</button>
						<button type="button" class="btn btn-success" onclick="insert_udi_fields(this);" id="insert_btn">Insert</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>


<div id="comment_detail" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h4 class="modal-title" id="modal_title">Comment Device Details</h4>
			</div>
			<div class="modal-body mainwhtbox" style="max-height:<?php echo $height.'px'?>; overflow: auto;">
				<span id="comnt_device_dtl"></span>
			</div>
			<div class="clearfix"></div>
			<div class="modal-footer pd0 panel-footer">
				<div class="row">
					<div class="col-sm-12 text-center pt5 pdb5" id="module_buttons">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>


<script>
	var parse_udi = '';
	var device_detail = '';
	function submit_udi_frm(elem) {
	var rowid = $('#rowid').val();
		rowid = rowid.split('::');
		rowid = rowid[1];
		
	if ($('#udi_num').val() == '') {
		top.fAlert('Please enter valid UDI for device.');
		return false;
	}

	$("#div_loading_image").show();
	
	var formData = $('#udi_frm').serialize();
	$.ajax({
	url: top.JS_WEB_ROOT_PATH+'/interface/Medical_history/sx_procedures/implantable_devices.php',
			type: 'POST',
			data: formData,
			success: function (resultData) {
				var result = JSON.parse(resultData);
				$('#device_dtl').html(result.details);
				$('#device_dtl').show('fast');
				if(parse_udi != '' && device_detail != '') {
					$('#show_detail').hide('fast');
				}
				$("#div_loading_image").hide();

				parse_udi = result.parse_udi;
				device_detail = result.device_detail;
			}
		});
	}


	
	function insert_udi_fields(elem) {
		if(parse_udi == '' || device_detail == '' || $('#udi_num').val() != parse_udi.udi) {
			top.fAlert('Please use Show Detail button to get UDI details.');
			return false;
		}
		var rowid = $('#rowid').val();
		rowid = rowid.split('::');
		rowid = rowid[1];
		$('#sx_title_text' + rowid).val(parse_udi.udi);
		$('#sg_comments' + rowid).text(device_detail.gmdnTerms.gmdn.gmdnPTDefinition+'||'+JSON.stringify(parse_udi)+'||'+JSON.stringify(device_detail));
		$('#implantable_device').modal('toggle');
	}
	
	
	function comment_detail(obj, id) {
		if (id === undefined) {
			return false;
		}

		$("#div_loading_image").show();

		var formData = {id:id};
		$.ajax({
		url: top.JS_WEB_ROOT_PATH+'/interface/Medical_history/sx_procedures/implantable_devices.php?task=comment_popup',
			type: 'POST',
			data: formData,
			success: function (resultData) {
				var result = JSON.parse(resultData);
				$('#comnt_device_dtl').html(result.details);
				$('#comnt_device_dtl').show('fast');
				$("#div_loading_image").hide();
				$('#comment_detail').modal({show: 'true'});
				//$('#'+obj.id).tooltip('toggle');
				parse_udi = result.parse_udi;
				device_detail = result.device_detail;
			}
		});
	}
</script>