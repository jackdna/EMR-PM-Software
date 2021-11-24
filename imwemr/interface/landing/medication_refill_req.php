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

require_once(dirname(__FILE__).'/../../config/globals.php');
ini_set("memory_limit","3072M");
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);
//check settings
if(!isERPPortalEnabled()){ exit("Error: 'ERP_API_PATIENT_PORTAL' setting is not enabled!"); }

include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/Patient.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/User.php");
include_once($GLOBALS['fileroot']."/library/classes/work_view/Facility.php");
include_once($GLOBALS['fileroot']."/library/erp_portal/medications_refill.php");

$OBJRabbitmqExchange = new Rabbitmq_exchange();
$OBJMedications_refill = new Medications_refill();
$erp_error=array();
if(isset($_GET["op"])){
	try {
		if($_GET["op"] == "aprv"||$_GET["op"] == "dcln"){
			$OBJMedications_refill->updatePortal($_GET["id"], $_GET["op"]);
		}
		if(isset($_GET["op"])=="refresh"){
			$OBJMedications_refill->getMedicationRefill();
		}
	} catch(Exception $e) {
		$erp_error[]='Unable to connect to ERP Portal';
	}
	$medRefArr = $OBJMedications_refill->getMedicationRefillRequestData();
	echo $medRefArr;
	exit();
}

?>
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script>
function load_erx_window(obj) {
	var patientId = $(obj).data("ptid");
	window.open('../chart_notes/erx_patient_selection.php?patientFromSheduler='+patientId,'erx_window_new','resizable=1,width=1200,height='+screen.height+',scrollbars=1');
}

function refresh_med_refill_reqs(){
	top.show_loading_image("show");
	$.get("medication_refill_req.php?op=refresh&updtd=1", function(d){
	$('#med_refill_req').html('');
	if(d) {
		var med_str=d;
		$('#med_refill_req').html(med_str);
	}
	$(".btn_aprv, .btn_dcln").on("click", function(){ proc_req(this); });
	$('#erx_popup').on('click', function() { load_erx_window(this); });
	top.show_loading_image("hide");
	}).fail(function() {
	top.show_loading_image("hide");
	alert( "Error: could not connect!" );
	});
}
	
function proc_req(o){
  if($(o).hasClass("btn_aprv")){
	var act = "Approved";
	var op = "aprv";
	var tr_cs="success";
  }else if($(o).hasClass("btn_dcln")){
	var act = "Declined";
	var op = "dcln";
	var tr_cs="danger";
  }else{return;}
  var med_refill_id = $(o).data("med_refill_id");
  top.show_loading_image("show");
  $.get("medication_refill_req.php?op="+op+"&id="+med_refill_id, function(d){
	$('#med_refill_req').html('');
	if(d) {
		var med_str=d;
		$('#med_refill_req').html(med_str);
	}
	$(".btn_aprv, .btn_dcln").on("click", function(){ proc_req(this); });
	$('#erx_popup').on('click', function() { load_erx_window(this); });
	 top.show_loading_image("hide");
  }).fail(function() {
	top.show_loading_image("hide");
	alert( "Error: could not connect!" );
  });
}

$(document).ready(function(){
	$(".btn_aprv, .btn_dcln").on("click", function(){ proc_req(this); });
	$('#erx_popup').on('click', function() { load_erx_window(this); });
	<?php if(!isset($_GET["updtd"])){ ?>
		refresh_med_refill_reqs();
	<?php } ?>
});

</script>
<div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-12">

			<div class="whitebox" style="min-height:200px;">
				<div class="boxheader">
					<h2 data-toggle="collapse" data-target="#medicationRefill" class="link_cursor" style="padding:0px;">Medication Refill Request </h2>
					<div class="hdoption">
						<img src="<?php echo $GLOBALS['webroot'];?>/library/images/refresh.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Reload" onClick="refresh_med_refill_reqs();" />
					</div>
				</div>
				<div class="clearfix"></div>
				<div id="medicatoion_refills" class="scroll-content mCustomScrollbar tablcont collaspe in">
					<div class="table-responsive respotable">
					<table class="table table-bordered table-striped table-hover">
							<thead>
								<tr class="unfinhead">
								  <th>DOS</th>
								  <th>Patient Name </th>
								  <th>Medication</th>
								  <th>Allergies</th>
								  <th>Comments</th>
								  <th>Action</th>
								</tr>
							  </thead>
							<tbody id="med_refill_req">
							
							</tbody>
					</table>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			
	</div>
</div>

