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
include_once(dirname(__FILE__)."/../../config/globals.php"); 
$patient_id = $_SESSION['patient'];
?>
<?php
	$sel_chart_rec=imw_query("select date_format(chart_master_table.date_of_service,'%m-%d-%Y') as date_of_service,chart_master_table.id,chart_master_table.encounterId 
	from chart_master_table left join chart_left_cc_history On chart_left_cc_history.form_id=chart_master_table.id where 
	chart_master_table.isSuperBilled=0 and chart_master_table.patient_id='$patient_id' and chart_master_table.date_of_service!='' 
	order by chart_master_table.date_of_service desc");
	if(imw_num_rows($sel_chart_rec)>1){
?>
	<div>
		 Chart Note DOS :  
		 <select name="chart_dos" id="chart_dos" class=" minimal selecicon" style="width:40%">
			<option value="">DOS</option>
			<?php
				while($fet_chart_rec=imw_fetch_array($sel_chart_rec)){
			?>
				<option value="<?php echo $fet_chart_rec['encounterId'].'--'.$fet_chart_rec['id']; ?>"><?php echo $fet_chart_rec['date_of_service']; ?></option>
			<?php } ?>
		</select>
	 </div>
<?php }else if(imw_num_rows($sel_chart_rec)>0){?>
    <div>
    	<?php $fet_chart_rec=imw_fetch_array($sel_chart_rec);?>
    	Chart Note DOS : <?php echo $fet_chart_rec['date_of_service']; ?> 
    	<input type="hidden" name="chart_dos" id="chart_dos" value="<?php echo $fet_chart_rec['encounterId'].'--'.$fet_chart_rec['id']; ?>">
    </div>
<?php }?>
