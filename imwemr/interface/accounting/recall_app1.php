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
	$height12="12px";
	
	$del_id=trim($_REQUEST['del_id']);
	$delete=trim($_REQUEST['delete']);
	$erp_error=array();
	$recall_date=date("Y-m-d",mktime(0,0,0,date("m")+$recall_m,date("d"),date("y")));
	//----------------- Delete Recall -----------------//
	if($_REQUEST['post_action']=="del"){
		//----------------- Delete Recall -----------------//
		if($_REQUEST['chkbx']){
			foreach($_REQUEST['chkbx'] as $key => $val){
				if($val>0){
					$qry = "delete from patient_app_recall where id='".$val."'";
					imw_query($qry);

					if(isERPPortalEnabled()){
						try {
							include_once($GLOBALS['fileroot']."/library/erp_portal/erp_portal_core.php");
							include_once($GLOBALS['srcdir'].'/erp_portal/recalls.php');
							$patient_arr = array();
							$patient_arr["externalId"] = $val;
							$oIncSecMsg = new Recalls();
							$oIncSecMsg->update_pt_portal($patient_arr,1);
						} catch(Exception $e) {
							$erp_error[]='Unable to connect to ERP Portal';
						}
					}
				}
			}
		}
	}

	if($patient_id<>""){
	//----------------- Recall Detail -----------------//
		$patient_app_recall_query=" SELECT par.*, fac.name as facility_name, sp.active_status FROM patient_app_recall par
									LEFT JOIN slot_procedures sp ON par.procedure_id = sp.id
									LEFT JOIN facility fac ON par.facility_id = fac.id
									where par.patient_id='$patient_id' AND
									par.descriptions != 'MUR_PATCH'
									ORDER BY par.recalldate desc ";

	//$patient_app_recall_query="SELECT * FROM patient_app_recall where patient_id='$patient_id' AND descriptions != 'MUR_PATCH' order by recalldate desc ";

	$patient_app_recall_result=imw_query($patient_app_recall_query);
	$patient_app_recall_numrows =imw_num_rows($patient_app_recall_result);
	?>
	<table class="recall_desp_table table table-hover table-striped table-bordered">

	<tr class="grythead">
    	<td>
        	<div class="checkbox">
                <input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();"/>
                <label for="chkbx_all"></label>
            </div>
        </td>
		<td class="text-left text-nowrap">Recall Date</td>
		<td class="text-left">Procedure</td>
		<td class="text-left">Facility</td>
		<td class="text-left">Description</td>
		<td class="text-left text-nowrap">Recall </td>
		<td class="text-left">Operator</td>
	</tr>
	<?php
	if($patient_app_recall_numrows>0){

		//----------------- User data -----------------//
		$getProviderDetailsStr = "SELECT fname,mname,lname,id FROM users";
		$getProviderDetailsQry = imw_query($getProviderDetailsStr);
		while($getProviderDetailsRow=imw_fetch_array($getProviderDetailsQry)){
			$providerFname = substr($getProviderDetailsRow['fname'],0,1);
			$providerMname = substr($getProviderDetailsRow['mname'],0,1);
			$providerLname = substr($getProviderDetailsRow['lname'],0,1);
			$id = $getProviderDetailsRow['id'];
			$providerName =$providerFname.$providerMname.$providerLname;
			$provider_name_arr[$id]=$providerName;
		}

		$i=0;
		while($rw=imw_fetch_array($patient_app_recall_result)){
			$id=$rw['id'];
			$proc_id=$rw['procedure_id'];
			$desc=html_entity_decode(stripslashes($rw['descriptions']));
			$recall_months=$rw['recall_months'];
			$operator=$rw['operator'];
			$recall_Date=date("m-d-Y",strtotime($rw['recalldate']));
			$proc_facility_name =$fontColor='';
			$proc_facility_name = $rw['facility_name'];
			if($rw['active_status']=='no' || $rw['active_status']=='del')
			{
				$fontColor = "style=\"color:#CC0000;\"";
			}
		?>
		<tr id="<?php echo $id;?>">
          <td>
            <div class="checkbox">
                <input name="chkbx[]" type="checkbox" id="chkbx<?php echo $id; ?>" class="chk_box_css" value="<?php echo $id; ?>"/>
                <label for="chkbx<?php echo $id; ?>"></label>
            </div>
          </td>
		  <td class="text-left text-nowrap"><a href="<?php if($acc_view_chr_only == 2){ ?> javascript:view_only_acc_call(0); <?php }else{ ?>recall_desc_save.php?editid=<?php echo $id;?>&patient_id=<?php echo $patient_id;?>&loc=<?php echo $loc; }?>" class="text_purple"><?php echo $recall_Date;?></a></td>
		  <td class="text-left"><?php echo $slot_proc_disp_arr[$proc_id];?></td>
		  <td class="text-left"><?php echo $proc_facility_name;?></td>
 		  <td class="text-left"><?php echo $desc;?></td>
		  <td class="text-left text-nowrap"><?php if($recall_months<10){ echo "0".$recall_months." Months"; }else{ echo $recall_months." Months";}?></td>
		  <td class="text-left"><?php echo $provider_name_arr[$operator];?></td>
	  </tr>
		<?php
			$i++;
		}
	}else{
		echo "<tr><td class=\"text-center lead\" colspan=\"7\">";
		echo imw_msg('no_rec');
		echo "</td></tr>";
	}

	?>

<?php	}?>


</table>
