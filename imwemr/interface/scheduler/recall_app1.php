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
	$del_id=trim($_REQUEST['del_id']);
	$delete=trim($_REQUEST['delete']);
	$erp_error=array();
	$recall_date=date("Y-m-d",mktime(0,0,0,date("m")+$recall_m,date("d"),date("y")));
	if($delete=="yes" && $del_id<>""){

		imw_query("delete from patient_app_recall where id=$del_id");
		if(isERPPortalEnabled()){
			try {
				include_once($GLOBALS['fileroot']."/library/erp_portal/erp_portal_core.php");
				include_once($GLOBALS['srcdir'].'/erp_portal/recalls.php');
				$patient_arr = array();
				$patient_arr["externalId"] = $del_id;
				$oIncSecMsg = new Recalls();
				$oIncSecMsg->update_pt_portal($patient_arr,1);
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
		}
	}

	if($patient_id<>""){

	 $patient_app_recall_query="
	 	SELECT
			par.*,
			fac.name as facility_name,
			sp.active_status
		FROM
			patient_app_recall par
			LEFT JOIN slot_procedures sp ON par.procedure_id = sp.id
			LEFT JOIN facility fac ON par.facility_id = fac.id
		where
			par.patient_id='$patient_id' AND
			par.descriptions != 'MUR_PATCH'
		ORDER BY
			par.recalldate desc ";

	$patient_app_recall_result=imw_query($patient_app_recall_query) or die(imw_error());
	$patient_app_recall_numrows =imw_num_rows($patient_app_recall_result);
	?>
	<table class="table table-striped table-bordered table-hover adminnw">
	<thead>
	<tr>
		<th>Recall Date</th>
		<th>Procedure</th>
		<th>Facility</th>
		<th>Description</th>
		<th>Recall </th>
		<th>Operator</th>
		<th>Saved On</th>
		<th>&nbsp;</th>
	</tr>
    </thead>
    <tbody>
	<?php
	if($patient_app_recall_numrows>0){
		$i=0;
		while($rw=imw_fetch_array($patient_app_recall_result)){
			$id=$rw['id'];
			$proc_id=$rw['procedure_id'];
			$desc=$rw['descriptions'];
			$recall_months=$rw['recall_months'];
			$operator=$rw['operator'];
			$recall_Date=get_date_format(date("Y-m-d",strtotime($rw['recalldate'])));
			$proc_facility_name =$fontColor='';
			$proc_facility_name = $rw['facility_name'];
			if($rw['active_status']=='no' || $rw['active_status']=='del')
			{
				$fontColor = "style=\"color:#CC0000;\"";
			}
		?>
		<tr id="<?php echo $id_name;?>">
		   <td><a href="recall_desc_save.php?editid=<?php echo $id;?>&patient_id=<?php echo $patient_id;?>&loc=<?php echo $loc;?>"  class="text_purple"><?php echo $recall_Date;?></a></td>
 		  <td><?php echo $proc_id = (($proc_id == "import from csv") ? "<i>Import Data</i>" : getProcedureName($proc_id));?></td>
 		  <td><?php echo  $proc_facility_name;?></td>
 		  <td><?php echo  $desc;?></td>
		  <td><?php if($recall_months<10){ echo "0".$recall_months." Months"; }else{ echo $recall_months." Months";}?></td>
		  <td><?php echo getUserName($operator);?></td>
		  <td><?php
			list($dt,$tm)=explode(' ',$rw['current_date1']);
			$dt=get_date_format($dt, "m-d-Y");
			echo $dt.' ',$tm;
			?></td>
		  <td><a href="recall_desc_save.php?del_id=<?php echo $id;?>&delete=yes&patient_id=<?php echo $patient_id;?>&loc=<?php echo $loc;?>" onClick="javascript:if(!confirm('Are you sure to remove this record')) return false;" title="Remove Record"><span class="glyphicon glyphicon-remove"></span></a></td>
	  </tr>
		<?php
			$i++;
		}
	}else{
		echo "<tr><td colspan=\"8\" class=\"text-center\">";
		echo "No Record";
		echo "</td></tr>";
	}
}?>
	</tbody>
</table>
