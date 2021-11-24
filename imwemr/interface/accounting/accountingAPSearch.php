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
$title = "A&amp;P"; 
require_once('acc_header.php'); 
include_once(dirname(__FILE__)."/../../library/classes/work_view/ChartAP.php");
$patient_id = $_SESSION['patient'];
$sel_pat=imw_query("select fname,lname from patient_data where id='$patient_id' limit 0,1");
$fet_pat=imw_fetch_array($sel_pat);
$pat_lname=$fet_pat['lname'];
if($fet_pat['fname']){
	$pat_fname=', '.$fet_pat['fname'];
}
$pat_name=$pat_lname.$pat_fname.' - '.$patient_id;
$query="SELECT date_format(clch.date_of_service, '%m/%d/%Y') as show_dos,cap.id,clch.date_of_service FROM `chart_assessment_plans` as cap 
		LEFT JOIN chart_master_table cmt ON cmt.id = cap.form_id 
		LEFT JOIN chart_left_cc_history clch ON clch.form_id = cap.form_id 
		WHERE cap.patient_id = '$patient_id' ORDER BY clch.date_of_service DESC,cmt.id";
$sqlAssessQry =imw_query($query) or die(imw_error());
if(imw_num_rows($sqlAssessQry)>0){
	$assessmentPlanPrint=true;
}else{
	$assessmentPlanPrint=false;
}
?>
<div class="table-responsive" style="height:365px; overflow:auto; width:100%;">
	<div class="purple_bar text-center"> 
    	<span>Assessment & Plans</span>
    </div>
	<form action="accountingAPResult.php" name="ap" method="post">
		<table class="table table-bordered table-hover table-striped">
		<?php if($assessmentPlanPrint == true){?>
			<tr class="grythead">
				<th style="text-align:left !important;">
					<div class="checkbox">
						<input type="checkbox" name="all" id="chkbx_all" value="all" onClick="chk_all();"/>
						<label for="chkbx_all"></label>
					</div>
				</th>
				<th>Patient - ID</th>
				<th>A&amp;P Date</th>
			</tr>
		<?php 
			while($sqlAssessRows = imw_fetch_assoc($sqlAssessQry)){
				$dat_exp_ap=explode('-',$sqlAssessRows['date_of_service']);
				$dat_final_ap=$dat_exp_ap['1'].'-'.$dat_exp_ap['2'].'-'.$dat_exp_ap['0'];
				$cap_id="id_".$sqlAssessRows['id'];
		?>
				<tr>
					<td>
						<div class="checkbox">
							<input type="checkbox" name="ids[]" id="<?php echo $cap_id; ?>" value="<?php echo $sqlAssessRows['date_of_service']; ?>" class="chk_box_css"/>
							<label for="<?php echo $cap_id; ?>"></label>
						</div>
					</td>
					<td>
						<a class="text_purple" href="accountingAPResult.php?dat_id=<?php echo $sqlAssessRows['date_of_service']; ?>" class="text_10"><?php echo $pat_name; ?></a>
					</td>
					<td class="text-center">
						<a class="text_purple" href="accountingAPResult.php?dat_id=<?php echo $sqlAssessRows['date_of_service']; ?>" class="text_10"><?php echo $dat_final_ap; ?></a>
					</td>
				</tr>
				<?php 
				}		
				?>
			<?php		
			}else{
			?>	
				<tr><td colspan="3" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td></tr>
		<?php } ?>
		</table>
	</form>
</div>
</div>	
	<?php if($assessmentPlanPrint == true){?>
	<footer>
		<div class="text-center" id="module_buttons">
			<input type="button" id="a&p" class="btn btn-success" value="A&P"  onClick="document.ap.submit();">
			<input type="button" id="close" class="btn btn-danger" value="Close"  onClick="window.close();">
		</div>
	</footer>
	<?php } ?>
</body>
</html>
