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
$title = "ERA"; 
require_once('acc_header.php'); 
$patient_id = $_SESSION['patient'];
?>
<script type="text/javascript" src="../../library/js/acc_common.js"></script> 
<?php
	$enc_id = "";
	if($_REQUEST['enc_id']>0){
		$enc_id=$_REQUEST['enc_id'];
	}  
	$add_zero_pat_id='000000'.$patient_id;
	$sel_era=imw_query("select era_835_patient_details.835_Era_Id,era_835_proc_details.REF_prov_identifier,era_835_proc_details.DTM_date,
						era_835_proc_details.835_Era_proc_Id,era_835_patient_details.ERA_patient_details_id,electronicfiles_tbl.file_name,
						era_835_proc_details.SVC_proc_code,era_835_proc_details.SVC_mod_code,electronicfiles_tbl.id,electronicfiles_tbl.post_status,
						era_835_proc_details.835_Era_proc_Id
						from era_835_patient_details join 
						era_835_proc_details on  era_835_patient_details.ERA_patient_details_id = era_835_proc_details.ERA_patient_details_id
						join era_835_details on era_835_details.835_Era_Id = era_835_patient_details.835_Era_Id
						join electronicfiles_tbl on electronicfiles_tbl.id=era_835_details.electronicFilesTblId
						where CLP_claim_submitter_id ='$patient_id' or CLP_claim_submitter_id ='$add_zero_pat_id'") or die(imw_error());
	while($era_row=imw_fetch_array($sel_era)){
		$CLP_claim_submitter_id=$patient_id;
		$REF_prov_identifier=$era_row['REF_prov_identifier'];
		$DOS = $era_row['DTM_date'];
		$file_name = $era_row['file_name'];
		$SVC_proc_code = $era_row['SVC_proc_code'];
		$SVC_mod_code = $era_row['SVC_mod_code'];
		
		$Era_proc_Id_835 = $era_row['835_Era_proc_Id'];
		$ERA_patient_details_id = $era_row['ERA_patient_details_id'];
		$Era_835_Id = $era_row['835_Era_Id'];
		
		$mcrPos = strpos($REF_prov_identifier, 'MCR');
		if($mcrPos){
			// REF*6R EXISTS
			$encounter_id = trim(substr($REF_prov_identifier, 0, $mcrPos));
			$restStr = substr($REF_prov_identifier, $mcrPos+3);
			if(strpos($restStr, '_TSUC_')){
				$tsucPos = strpos($restStr, '_TSUC_');
				$tsucId = $tsucPos+6;
			}else if(strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator)){
				$tsucPos = strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator);
				if($billing_global_tsuc_separator==""){
					$tsucId = $tsucPos+4;
				}else{
					$tsucId = $tsucPos+6;
				}
			}else if(strpos($REF_prov_identifier, 'TSUC')){
				$tsucPos = strpos($restStr, 'TSUC');
				$tsucId = $tsucPos+4;
			}
			if($tsucId){
				$chargeListDetailId = substr($restStr, 0, $tsucPos);
				$tsuc_identifier = substr($restStr, $tsucId);					
				if(strpos($tsuc_identifier, ',')){
					$tsuc_identifier = trim(substr($tsuc_identifier, 0, strpos($tsuc_identifier, ',')));
				}
			}else{
				$chargeListDetailId = '';
			}
		}else{
			// REF*6R DOES NOT EXISTS
			$encounter_id = '';
			$chargeListDetailId = '';
			
			// GET ENCOUNTER AND CHARGE LIST DETAILS BASED ON PATIENT ID
			if(is_numeric($CLP_claim_submitter_id)){
				// GET PROC ID FROM CPT4_CODE
				$cpt_fee_id_arr=array();	
				$cpt_qry=imw_query("select cpt_fee_id from cpt_fee_tbl where cpt4_code='$SVC_proc_code'");
				while($cpt_row=imw_fetch_array($cpt_qry)){
					$cpt_fee_id_arr[$cpt_row['cpt_fee_id']]=$cpt_row['cpt_fee_id'];
				}							
				$cpt_fee_id=implode(',',$cpt_fee_id_arr);	
				// GET PROC ID FROM CPT4_CODE
				
				//GET MODIFIERS ID
					$SVC_mod_code_exp="";
					$SVC_mod_code_exp=explode(',',$SVC_mod_code);
					$modifiersId1="";
					$modifiersId2="";
					if($SVC_mod_code_exp[0]!=""){
						$modifiers_code1=trim($SVC_mod_code_exp[0]);
						$qry_mod1 = imw_query("select modifiers_id,modifier_code from modifiers_tbl where mod_prac_code='".$modifiers_code1."' limit 0,1");
						$getModID = imw_fetch_assoc($qry_mod1);
						$modifiersId1 = $getModID["modifiers_id"];
					}
					if($SVC_mod_code_exp[1]!=""){
						$modifiers_code2=trim($SVC_mod_code_exp[1]);
						$qry_mod1 = imw_query("select modifiers_id,modifier_code from modifiers_tbl where mod_prac_code='".$modifiers_code2."' limit 0,1");
						$getModID = imw_fetch_assoc($qry_mod1);
						$modifiersId2 = $getModID["modifiers_id"];
					}
				//GET MODIFIERS ID
				
				$getChargeListDetailsStr = "SELECT * FROM 
											patient_charge_list a,
											patient_charge_list_details b
											WHERE a.patient_id = '$CLP_claim_submitter_id'
											AND a.charge_list_id = b.charge_list_id
											AND a.date_of_service = '$DOS'
											AND b.procCode in($cpt_fee_id)
											and b.del_status='0'";
				if($modifiersId1>0){
					$getChargeListDetailsStr.=" AND b.modifier_id1 = '$modifiersId1'";
				}
				if($modifiersId2>0){
					$getChargeListDetailsStr.=" AND b.modifier_id2 = '$modifiersId2'";
				}
				if($modifiersId1<=0 && $modifiersId2<=0){
					$getChargeListDetailsStr.=" AND b.modifier_id1 = '' and b.modifier_id2 = ''";
				}						
				$getChargeListDetailsQry = imw_query($getChargeListDetailsStr);
				$countRows = imw_num_rows($getChargeListDetailsQry);
				if($countRows){
					while($getChargeListDetailsRows = imw_fetch_assoc($getChargeListDetailsQry)){
						$encounterId = $getChargeListDetailsRows['encounter_id'];
						$charge_list_id = $getChargeListDetailsRows['charge_list_id'];
						$listChargeDetailId = $getChargeListDetailsRows['charge_list_detail_id'];							
					}
					if($countRows==1){
						$encounter_id = $encounterId;
						$chargeListDetailId = $listChargeDetailId;
					}
				}
			}
			// GET ENCOUNTER AND CHARGE LIST DETAILS BASED ON PATIENT ID
		}
		$era_enc_arr[$encounter_id][$Era_835_Id]=$era_row;
		$era_enc_pat_arr[$encounter_id][$Era_835_Id][$ERA_patient_details_id]=$ERA_patient_details_id;
	}
	$qry = imw_query("select * from patient_data where pid = '$patient_id'");
	$row = imw_fetch_array($qry); 
	$patientName = ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname'])).'-'.$patient_id;
?>
<div class="table-responsive" style="height:365px; overflow:auto; width:100%;">
	<div class="purple_bar"> 
    	<span>ERA Files</span>
        <span style="padding-left:30%;"><?php echo $patientName; ?></span>
    </div>
	<table class="table table-bordered table-hover table-striped">
		<thead>
			<tr class='grythead'>
				<th>
                    S. No.
                </th>
                <th>
                   File Name
                </th>
                <th>
                	Encounter Id
                </th>
               <th>
                  Check Date
                </th>
                 <th>
                  Status
                </th>
			</tr>
		</thead>		
            <?php
			if($enc_id != ""){
              	$era_enc_arr_list=$era_enc_arr[$enc_id];
				$i=0;
				foreach($era_enc_arr_list as $era_key => $era_enc_arr_list_id){
					$i++;
					$fileId=$era_enc_arr_list[$era_key]['id'];
					$Era_proc_835_Id = $era_enc_arr_list[$era_key]['835_Era_proc_Id'];
					$ERA_patient_details_id = implode(',',$era_enc_pat_arr[$enc_id][$era_key]);
					$getEFTDateStr = "SELECT date_format(chk_issue_EFT_Effective_date,'%m-%d-%y') as chkEffectiveDate,
										electronicFilesTblId,TRN_payment_type_number,835_Era_Id
										FROM era_835_details
										WHERE electronicFilesTblId = '$fileId' and 835_Era_Id='".$era_enc_arr_list[$era_key]['835_Era_Id']."'";
					$getEFTDateQry = imw_query($getEFTDateStr);
					$getEFTDateRows = imw_fetch_array($getEFTDateQry);
					$chkEffectiveDate = $getEFTDateRows['chkEffectiveDate'];
					$Era_Id_835 = $getEFTDateRows['835_Era_Id'];
					$TRN_payment_type_number = $getEFTDateRows['TRN_payment_type_number'];
                    
            ?>
                <tr class="text-center">
                    <td>
                       <?php echo $i; ?>
                    </td>
                    <td class="text-left">
                    	<a class="text_purple" href="javascript:viewDetailsFn('<?php echo $fileId; ?>', '<?php echo $Era_Id_835; ?>', '<?php echo $ERA_patient_details_id; ?>');">
                       <?php echo $era_enc_arr_list[$era_key]['file_name']; ?>
                       </a>
                    </td>
                    <td>
                    	<?php
							echo $enc_id; 
						?>
                    </td>
                   <td>
                      <?php echo $chkEffectiveDate; ?>
                    </td>
                     <td>
                      <?php echo $era_enc_arr_list[$era_key]['post_status']; ?>
                    </td>
                </tr>
            <?php }
			}else{
				$i=0;
				foreach($era_enc_arr as $era_key => $era_enc_arr_list_id){
					$era_enc_arr_list=$era_enc_arr[$era_key];
					$enc_id = $era_key;
					foreach($era_enc_arr_list as $era_key => $era_enc_arr_list_id){
						$i++;
						$fileId=$era_enc_arr_list[$era_key]['id'];
						$Era_proc_835_Id = $era_enc_arr_list[$era_key]['835_Era_proc_Id'];
						$ERA_patient_details_id = implode(',',$era_enc_pat_arr[$enc_id][$era_key]);
						$getEFTDateStr = "SELECT date_format(chk_issue_EFT_Effective_date,'%m-%d-%y') as chkEffectiveDate,
											electronicFilesTblId,TRN_payment_type_number,835_Era_Id
											FROM era_835_details
											WHERE electronicFilesTblId = '$fileId' and 835_Era_Id='".$era_enc_arr_list[$era_key]['835_Era_Id']."' limit 0,1";
						$getEFTDateQry = imw_query($getEFTDateStr);
						$getEFTDateRows = imw_fetch_array($getEFTDateQry);
						$chkEffectiveDate = $getEFTDateRows['chkEffectiveDate'];
						$Era_Id_835 = $getEFTDateRows['835_Era_Id'];
						$TRN_payment_type_number = $getEFTDateRows['TRN_payment_type_number'];
						
        	    ?>
            	    <tr class="text-center">
                	    <td>
                    	  <?php echo $i; ?>
	                    </td>
    	                <td class="text-left">
                        	<a class="text_purple" href="javascript:viewDetailsFn('<?php echo $fileId; ?>', '<?php echo $Era_Id_835; ?>', '<?php echo $ERA_patient_details_id; ?>');">
        	               <?php echo $era_enc_arr_list[$era_key]['file_name']; ?>
                           </a>
            	        </td>
                	    <td>
	                    	<?php
								echo $enc_id; 
							?>
            	        </td>
	                   <td>
    	                  <?php echo $chkEffectiveDate; ?>
        	            </td>
            	         <td>
                	      <?php echo $era_enc_arr_list[$era_key]['post_status']; ?>
	                    </td>
    	            </tr>
        	    <?php }
				}
			 }
			 ?>
            <?php if($i==0){?>
			<tr>
				<td colspan="5" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
			</tr>
      <?php } ?>
     </table>
</div>
</div>
<footer>
	<div class="text-center" id="module_buttons">
		<input type="button" id="close" class="btn btn-danger" value="Close"  onClick="window.close();">
	</div>
</footer>
</body>
</html>
