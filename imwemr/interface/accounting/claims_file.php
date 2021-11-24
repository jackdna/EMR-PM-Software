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
$title = "Related Claims Files"; 
$without_pat = 'true';
require_once('acc_header.php'); 
if($_REQUEST['patient_id']>0){
	$patient_id = $_REQUEST['patient_id'];
}else{
	$patient_id = $_SESSION['patient'];
}
?>
<script type="text/javascript" src="../../library/js/acc_common.js"></script> 
<?php
	$encounter_id="";
	if($_REQUEST['enc_id']>0){
		$encounter_id=$_REQUEST['enc_id'];
	}   
	
	$qry = imw_query("select * from patient_data where pid = '$patient_id'");
	$row = imw_fetch_array($qry); 
	$patientName = ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname'])).'-'.$patient_id;
	
	$InsGroupArr = array();
	$grp_qry = imw_query("select gro_id from groups_new where group_institution>0");
	while($row = imw_fetch_array($grp_qry)){	
		$InsGroupArr[$row["gro_id"]] = $row["gro_id"];
	}
?>
<div class="table-responsive" style="height:365px; overflow:auto; width:100%;">
	<div class="purple_bar"> 
    	<span>Related Claims Files</span>
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
					Created Date
				</th>
			</tr>
		</thead>	
        <?php
		if($encounter_id>0){
			$sel_qry="SELECT encounter_id,pcld_id,create_date,file_name,Transaction_set_unique_control
			,status,clearing_house,file_format, 
			Batch_file_submitte_id, Interchange_control, gn.name AS file_group_name,bfs.ins_comp,bfs.group_id 
			FROM batch_file_submitte bfs 
			LEFT JOIN groups_new gn ON (gn.gro_id=bfs.group_id)
			WHERE file_name != '' and encounter_id like '%$encounter_id%' order by create_date desc";
			$run_qry=imw_query($sel_qry);
			$i=0;
			while($row_qry=imw_fetch_array($run_qry)){
				$enc_arr=array();
				$enc_arr=explode(',',$row_qry['encounter_id']);
				if(in_array($encounter_id,$enc_arr)){
					$i++;	
					$create_date_exp=explode('-',$row_qry['create_date']);
					$create_date_final=$create_date_exp[1].'-'.$create_date_exp[2].'-'.$create_date_exp[0];
					
					$ins_comp_chk = $row_qry['ins_comp'];
					$group_id_chk = $row_qry['group_id'];
					$batch_file_submitte_id = $row_qry['Batch_file_submitte_id'];
					$Interchange_controls1 = $row_qry['Interchange_control'];
					$cont_length1 = (4 - strlen($Interchange_controls1));
					$addSpaces1 = NULL;
					for($a=0;$a<$cont_length1;$a++){
						$addSpaces1 .= '0';
					}
					$InterchangeControlNumber1 = $addSpaces1.$Interchange_controls1;
					$batch_file_name = substr($row_qry['file_name'],0,-4);	
				
					$clearing_house = trim($row_qry['clearing_house']);
					$file_format = trim($row_qry['file_format']);
					$file_group_name = trim($row_qry['file_group_name']);
				if($file_format==5010){
					if($clearing_house != ''){$clearing_house = strtoupper(substr($clearing_house,0,1)).'_';}
					if($clearing_house != 'E_' && $clearing_house != 'M_'){
						$clearing_house = '';
					}
					if(str_word_count($file_group_name)==1){
						$file_group_name = strtoupper(substr($file_group_name,0,3)).'_';
					}else{
						$arr_file_group_name = str_word_count($file_group_name,1);
						$tmp_group_name = '';
					foreach($arr_file_group_name as $val){
						$tmp_group_name .= substr($val,0,1);
					}
					if(strlen($tmp_group_name)>3){$tmp_group_name = substr($tmp_group_name,0,3);}
						$file_group_name = strtoupper($tmp_group_name).'_';
					}
				}else{
					$file_group_name = '';
					$clearing_house = '';
				}
				
				// Get Charge List Id From Encounter ID
				$qry_chargelist_id = "select charge_list_id,primaryInsuranceCoId,secondaryInsuranceCoId,billing_type from patient_charge_list where del_status='0' and encounter_id='".$encounter_id."' limit 0,1";
				$res_chargelist_id = imw_query($qry_chargelist_id);
				$row_chargelist_id = imw_fetch_assoc($res_chargelist_id);
				$charge_list_id = $row_chargelist_id["charge_list_id"];
				$primaryInsuranceCoId = $row_chargelist_id["primaryInsuranceCoId"];
				$secondaryInsuranceCoId = $row_chargelist_id["secondaryInsuranceCoId"];
				$billing_type = $row_chargelist_id["billing_type"];
				
				//Get Insurance Type 
				$ins_prof_chk="";
				if($ins_comp_chk=="primary"){
					$ins_comp="1";
					$row_prof=imw_query("select id from insurance_companies where institutional_type='INST_PROF' and id='$primaryInsuranceCoId'");
					if(imw_num_rows($row_prof)>0){
						$ins_prof_chk=1;
					}
				}else if($ins_comp_chk=="secondary"){
					$ins_comp="2";
					$row_prof=imw_query("select id from insurance_companies where institutional_type='INST_PROF' and id='$secondaryInsuranceCoId'");
					if(imw_num_rows($row_prof)>0){
						$ins_prof_chk=1;
					}
				}
				
				if($billing_type==3 || $billing_type==1){
					$printHcfa="1";	
				}else if($billing_type==2){
					$printHcfa="2";	
				}else if($InsGroupArr[$group_id_chk]>0 && $ins_prof_chk==""){
					$printHcfa="2";
				}else{
					$printHcfa="1";
				}		
				if($row_qry['pcld_id']!=""){
					$pcld_id=$row_qry['pcld_id'];
					$chld_ids_arr=array();
					$sel_qry = imw_query("Select charge_list_detail_id from
					patient_charge_list_details where 
					del_status='0' and charge_list_id='$charge_list_id' and charge_list_detail_id in($pcld_id)");
					while($row=imw_fetch_array($sel_qry)){
						$chld_ids_arr[$row['charge_list_detail_id']]=$row['charge_list_detail_id'];
					}
					$charge_list_detail_ids=implode(',',$chld_ids_arr);
				}
				?>
				<tr class="text-center">
				<td><?php echo $i; ?></td>
				<td class="text-left">
					<a class="text_purple" href="javascript:print_hcfa_ub('<?php echo $charge_list_id; ?>','<?php echo $charge_list_detail_ids; ?>','<?php echo $ins_comp; ?>','<?php echo $printHcfa; ?>','<?php echo $batch_file_submitte_id; ?>');">	
						<?php echo $file_group_name.$clearing_house.$batch_file_name .' ('.$InterchangeControlNumber1.')'; ?>
					</a> 
				</td>
				<td><?php echo $encounter_id; ?></td>
				<td><?php echo $create_date_final; ?></td>
				</tr>
				<?php 
				}
			}					
		}else{
			$i=0;
			$encounter_qry ="select encounter_id from patient_charge_list where del_status='0' and patient_id='".$patient_id."'";
			$encounter_res = imw_query($encounter_qry);
			while($encounter_row = imw_fetch_array($encounter_res)){
				$encounter_id = $encounter_row["encounter_id"];
				$sel_qry="SELECT encounter_id,pcld_id,create_date,file_name,Transaction_set_unique_control
				,status,clearing_house,file_format, 
				Batch_file_submitte_id, Interchange_control, gn.name AS file_group_name,bfs.ins_comp,bfs.group_id 
				FROM batch_file_submitte bfs 
				LEFT JOIN groups_new gn ON (gn.gro_id=bfs.group_id)
				WHERE file_name != '' and encounter_id like '%".$encounter_id."%' order by create_date desc";
				$run_qry=imw_query($sel_qry);
				while($row_qry=imw_fetch_array($run_qry)){
					$enc_arr=array();
					$enc_arr=explode(',',$row_qry['encounter_id']);
					$ins_comp_chk = $row_qry['ins_comp'];
					$group_id_chk = $row_qry['group_id'];
					if(in_array($encounter_id,$enc_arr)){
						$i++;	
						$create_date_exp=explode('-',$row_qry['create_date']);
						$create_date_final=$create_date_exp[1].'-'.$create_date_exp[2].'-'.$create_date_exp[0];
					
						$Interchange_controls1 = $row_qry['Interchange_control'];
						$cont_length1 = (4 - strlen($Interchange_controls1));
						$addSpaces1 = NULL;
						for($a=0;$a<$cont_length1;$a++){
							$addSpaces1 .= '0';
						}
						$InterchangeControlNumber1 = $addSpaces1.$Interchange_controls1;
						$batch_file_name = substr($row_qry['file_name'],0,-4);	
					
						$clearing_house = trim($row_qry['clearing_house']);
						$file_format = trim($row_qry['file_format']);
						$batch_file_submitte_id = $row_qry['Batch_file_submitte_id'];
						$file_group_name = trim($row_qry['file_group_name']);
						if($file_format==5010){
							if($clearing_house != ''){$clearing_house = strtoupper(substr($clearing_house,0,1)).'_';}
							if($clearing_house != 'E_' && $clearing_house != 'M_'){
								$clearing_house = '';
							}
							if(str_word_count($file_group_name)==1){
								$file_group_name = strtoupper(substr($file_group_name,0,3)).'_';
							}else{
								$arr_file_group_name = str_word_count($file_group_name,1);
								$tmp_group_name = '';
								foreach($arr_file_group_name as $val){
									$tmp_group_name .= substr($val,0,1);
								}
								if(strlen($tmp_group_name)>3){$tmp_group_name = substr($tmp_group_name,0,3);}
								$file_group_name = strtoupper($tmp_group_name).'_';
							}
						}else{
							$file_group_name = '';
							$clearing_house = '';
						}
					// Get Charge List Id From Encounter ID
					$qry_chargelist_id = "select charge_list_id,primaryInsuranceCoId,secondaryInsuranceCoId,billing_type from patient_charge_list where del_status='0' and encounter_id='".$encounter_id."' limit 0,1";
					$res_chargelist_id = imw_query($qry_chargelist_id);
					$row_chargelist_id = imw_fetch_assoc($res_chargelist_id);
					$charge_list_id = $row_chargelist_id["charge_list_id"];
					$primaryInsuranceCoId = $row_chargelist_id["primaryInsuranceCoId"];
					$secondaryInsuranceCoId = $row_chargelist_id["secondaryInsuranceCoId"];
					$billing_type = $row_chargelist_id["billing_type"];
					
					$ins_prof_chk="";
					//Get Insurance Type 
					if($ins_comp_chk=="primary"){
						$ins_comp="1";
						$row_prof=imw_query("select id from insurance_companies where institutional_type='INST_PROF' and id='$primaryInsuranceCoId'");
						if(imw_num_rows($row_prof)>0){
							$ins_prof_chk=1;
						}
					}else if($ins_comp_chk=="secondary"){
						$ins_comp="2";
						$row_prof=imw_query("select id from insurance_companies where institutional_type='INST_PROF' and id='$secondaryInsuranceCoId'");
						if(imw_num_rows($row_prof)>0){
							$ins_prof_chk=1;
						}
					}
					
					if($billing_type==3 || $billing_type==1){
						$printHcfa="1";	
					}else if($billing_type==2){
						$printHcfa="2";	
					}else if($InsGroupArr[$group_id_chk]>0 && $ins_prof_chk==""){
						$printHcfa="2";
					}else{
						$printHcfa="1";
					}
					if($row_qry['pcld_id']!=""){
						$pcld_id=$row_qry['pcld_id'];
						$chld_ids_arr=array();
						$sel_qry = imw_query("Select charge_list_detail_id from
						patient_charge_list_details where 
						del_status='0' and charge_list_id='$charge_list_id' and charge_list_detail_id in($pcld_id)");
						while($row=imw_fetch_array($sel_qry)){
							$chld_ids_arr[$row['charge_list_detail_id']]=$row['charge_list_detail_id'];
						}
						$charge_list_detail_ids=implode(',',$chld_ids_arr);
					}
					$create_date_final_exp=explode('-',$create_date_final);
					$use_create_date=$create_date_final_exp[2].$create_date_final_exp[0].$create_date_final_exp[1];
					$arr_data[$use_create_date][]='
					<td class="text-left">
						<a class="text_purple" href="javascript:print_hcfa_ub(\''.$charge_list_id.'\',\''.$charge_list_detail_ids.'\',\''.$ins_comp.'\',\''.$printHcfa.'\',\''.$batch_file_submitte_id.'\');">
						'.$file_group_name.$clearing_house.$batch_file_name .' ('.$InterchangeControlNumber1.')</a>
					</td>
					<td>'.$encounter_id.'</td>
					<td>'.$create_date_final.'</td>';
					}
				}
			}
			krsort($arr_data);
			$k=0;
			foreach($arr_data as $arr_key=>$arr_val){
				foreach($arr_data[$arr_key] as $arr_final_data){
					$k++;
					echo '<tr class="text-center">
					<td>'.$k.'</td>';
					echo $arr_final_data;
					echo '</tr>';
				}
			}
		}
	   	if($i==0){?>
			<tr>
				<td colspan="4" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td>
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

