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

include_once($GLOBALS['srcdir']."/classes/medical_hx/phms.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");

$phms_obj = new PHMS($medical->current_tab);

$search_by = '';
if(isset($_REQUEST['searchby'])){
	$search_by = $_REQUEST['searchby'];
	
	//Changing dropdown
	$dropdown = $phms_obj->get_new_dropdown();
}

//To set default alert_val 
//Set alertFor val && Search val
$phms_obj->set_init($search_by);


//Get table data
//Gets data based on val set in set_init() 
$data = $phms_obj->get_table_data();


$sessionHeightInMH4= $GLOBALS['gl_browser_name']=='ipad' ? $_SESSION["wn_height"] - 62 : $_SESSION['wn_height']-370;
?>
<body>
<div class="row">
	<form method="post" name="frnFilterPHMS" action="">  
		<!-- Heading -->
		<div class="radtop col-sm-12">
			<div class="row">
				<div class="col-sm-2 col-sm-offset-10 form-inline text-right">
					<div class="allflter">
						<div class="row">
							<div class="col-sm-4">
								<label for="filter_val_sel">FILTER&nbsp;:</label>	
							</div>	
							<div class="col-sm-8">
								<select id="filter_val_sel" name="searchby" class="selectpicker" data-width="100%" onChange="document.forms.frnFilterPHMS.submit();">
								<?php if(isset($dropdown) && trim($dropdown) != ''){
									echo $dropdown;
								}else{ ?>
									<optgroup label="PHMS Type">
										<option value="">All</option>
										<option value="immu">Immunization</option>
										<option value="phms">PHMS</option>
									</optgroup>
									<optgroup label="Plan">
										<option value="1">All</option>                                       
									</optgroup>
									 <optgroup label="Status">
										<option value="">All</option>
										<option value="Administered">Administered</option>
										<option value="Declined">Declined</option>
										<option value="Not Due">Not Due</option>                                       
									</optgroup>
									<optgroup label="Document">
										 <option value="Doc All">All</option>
									</optgroup>
									<?php } ?>
								</select>
							</div>	
						</div>
					</div>	
				</div>	
			</div>
		</div>

		<!-- Table to view  -->
		<div class="col-sm-12" style="height:<?php echo $sessionHeightInMH4; ?>px;overflow-y:auto">
			<table class="table table-striped table-bordered table-hover table-condensed">
				 <tr class="grythead">
					<th class="text-nowrap">Date </th>
					<th class="text-nowrap">Time </th>
					<th class="text-nowrap">HMS Type</th>
					<th class="text-nowrap">HMS Plan</th>
					<th class="text-nowrap">HMS Details</th>
					<th class="text-nowrap">Frequency</th>
					<th class="text-nowrap">Status</th>
					<th class="text-nowrap">Override reason</th>
					<th class="text-nowrap">Comments </th>					
					<th class="text-nowrap">User</th>
                </tr> 
				<?php
					if(trim($data) == ''){
						echo '<tr class="text-center"><td colspan="10">No Record Found</td></tr>';
					}else{
						echo $data;
					}
				?>	
			</table>
		</div>	
	</form>
</div>

</body>
</html>
<?php 
	echo $phms_obj->set_lab_cls_alerts();
	/*$frm="patient_specific_chart_note_med_hx";
	require_once(dirname(__FILE__)."/../../chart_notes/chart_alerts.php");	
	alert_div_common($_SESSION['patient'],'',$frm,$blIncludeAdminAlert = false,$blIncludePatientSpecificAlert = true);
	*/
?>