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

$relats = array('self','Father','Mother','Son','Daughter','Spouse','Guardian','POA','Employee',"Aunt","Aunt/Uncle","Brother/Sister","Child:No Fin Responsibility","Dep Child:Fin Responsibility","Donor Live","Donor-Dceased","Employee","Foster Child","Grand Child","Grandparent","Handicapped Dependant","Injured Plantiff","Inlaw","Legal Guardian","Minor Dependent Of a Dependent","Niece/Nephew","Relative","Sponsored Dependent","Step Child","Student","Ward of The Court","Husband","Wife","Significant Other");
sort($relats);
array_push($relats,'Other');
$new_casetype_id = $_SESSION['new_casetype'];
$ins_caseid = $_SESSION['currentCaseid'];
$patient_id = $_SESSION['patient'];
$ph_format = $GLOBALS['phone_format'];
//getting current insurance case name
$current_case_name = get_insurance_case_name($_SESSION["currentCaseid"]);

//--- copay policies data -----
$query = "select copay_type,accept_assignment from copay_policies";
$sql = imw_query($query);
$polices_res = imw_fetch_assoc($sql);
$policies_copay_type=$polices_res['copay_type'];
$policies_accept_assignment=$polices_res['accept_assignment'];

//--- insurance case type check -----
$caseQryRes = get_array_records('insurance_case_types','case_id',$new_casetype_id,'vision, normal');


//--- Authorisation Hx -----
$auth_chl_arr = array();
$auth_chl_qry = imw_query("SELECT auth_id FROM patient_charge_list WHERE del_status='0' AND patient_id='".$patient_id."' AND case_type_id='".$ins_caseid."'");
while($auth_chl_row = imw_fetch_array($auth_chl_qry)){
	$auth_chl_arr[] = $auth_chl_row['auth_id'];
}

$insurance_array = array('1' => 'primary' , '2' => 'secondary' , '3' => 'tertiary' );
$primaryComDetail = array();
$secondaryComDetail = array();
$tertiaryComDetail = array();
?>
<div id="tool_tip_div" onmouseover="clearTimeout(t);" onmouseout="closeWindow();" style=""></div>  	

<div class="accordion-box">
	<div class="accordion">
  
      <dl>
      	<input type="hidden" name="view_from_dd" id="view_from_dd" value="1"  />  
        <input type="hidden" name="session_patient" id="session_patient" value="<?php echo $_SESSION['patient']; ?>"  />
        <input type="hidden" name="session_currentCaseid" id="session_currentCaseid" value="<?php echo $_SESSION['currentCaseid']; ?>"  />
				<?php 
				$xml_file_name = (constant("EXTERNAL_INS_MAPPING") == "YES") ? 'Insurance_Comp_Cross_Map' : 'Insurance_Comp';
				$insCompXMLFile =	data_path() . "xml/".$xml_file_name.".xml";
				$XML	= file_get_contents($insCompXMLFile);
				
				// extracting XML Data into Array
				$ins_xml_extract = insurance_provider_xml_extract();
				foreach($insurance_array as $i_key => $i_type)
        {
          $s_name = substr($i_type,0,3);
          $inactive_ins_comp 	= 'inactive'.ucfirst($s_name).'InsComp';
          
					//----- Start Qurey To get Primary Insurance Company ---------
          $query = "select date_format(expiration_date,'".get_sql_date_format()."') as expiration_date,id,provider,
                           date_format(effective_date,'".get_sql_date_format()."') as effective_date,scan_card,scan_card2 
                           from insurance_data where ins_caseid = '".$ins_caseid."' and pid = '".$patient_id."' 
                           and type = '".$i_type."' and actInsComp = '1' and provider > '0' order by actInsComp Desc, effective_date DESC";
          $sql = imw_query($query);
          $row = imw_fetch_assoc($sql);
          
          $expiration_date = $row['expiration_date'];
          $comid  = $row['id'];
          $comProvider = $row['provider'];
          $actPrevious = $row['effective_date'];
          $scan_card  = $row['scan_card'];
          $scan_card2 = $row['scan_card2'];
          
          if(isset($view_from_dd))
          {
            $queryDD = "select scan_card,scan_card2,id from insurance_data where id='".$$inactive_ins_comp."' ";	
            $qryDD = imw_query($queryDD);	
						$rowDD = imw_fetch_assoc($qryDD);
            $scan_card = $rowDD['scan_card'];
						$scan_card2 = $rowDD['scan_card2'];
						$comidDD = $rowDD['id'];
					}
          
          $actPreviousDate = $actPrevious;
          $expirationDate = $expiration_date;
          $comID = $comidDD ? $comidDD : $comid;
          $isRecordExists = $comID;
          //--- Resize  Insurance Scanned first document Image --------
					$image = $image2 = '';
          if($scan_card)
          {
            $image = $data_obj->insurance_image_tag($scan_card, $comID, 1, ''.ucfirst($i_type).' Scanned Document');
          }
        
          //--- Resize Insurance Scanned second document Image --------
          if($scan_card2){
            $image2 = $data_obj->insurance_image_tag($scan_card2, $comID, 2, ''.ucfirst($i_type).' Scanned Document');
          }
          
          //---- get Document from scan_table without insurance company -------
					$scan_documents_id = $scan_documents_id1 = '';
          if($comid == '')
          {
            $query = "select scan_documents_id,scan_card,scan_card2 from insurance_scan_documents 
                        where type = '".$i_type."' and ins_caseid = '".$ins_caseid."'
                        and patient_id = '".$patient_id."' and document_status = '0'";
            $sql = imw_query($query);
            $cnt = imw_num_rows($sql);
            $insDetails = imw_fetch_assoc($sql);
            if($cnt > 0)
            {
              $scan_card = $insDetails['scan_card'];
              $scan_card2 = $insDetails['scan_card2'];
              
              $scan_documents_id = $insDetails['scan_documents_id'];
              $scan_documents_id1 = $insDetails['scan_documents_id'];
              
              $image = $data_obj->insurance_image_tag($scan_card, $scan_documents_id, 1, ''.ucfirst($i_type).' Scanned Document','scan_card');
              $image2 = $data_obj->insurance_image_tag($scan_card2, $scan_documents_id, 2, ''.ucfirst($i_type).' Scanned Document','scan_card');
            }
          }
        
          $query = "select insurance_data.*, insurance_companies.in_house_code as pracCodeVS, insurance_companies.claim_type as claimType, 
                    insurance_companies.name as comp_name,insurance_companies.claim_type as InsClaimType,insurance_companies.ins_type as ic_ins_type,
                    insurance_companies.msp_type as ic_msp_type 
                    from insurance_data LEFT JOIN insurance_companies on 
                    insurance_companies.id = insurance_data.provider 
                    where insurance_data.id = '".$$inactive_ins_comp."'";
          $sql = imw_query($query);
          if(imw_num_rows($sql) > 0)
          {
            $newComp = 0;
            $comDetail = imw_fetch_object($sql);
					}
          else{
            $newComp = 1;
						// to reset all prev values/variable if exists
						$comDetail = (object) array();
          }
          
         	//----- Activation Date ----------
          if($comDetail->effective_date == '0000-00-00 00:00:00' || $comDetail->effective_date == ''){
            $effective_date = '';
          }
          else{
            list($effective_date_tmp,$time) = explode(" ",$comDetail->effective_date);
            $effective_date = get_date_format($effective_date_tmp);
          }
          //----- Expiration Date ----------
          if($comDetail->expiration_date == '0000-00-00 00:00:00' || $comDetail->expiration_date == ''){
            $expiration_date = '';
          }
          else{
            list($expiration_date_tmp,$time) = explode(" ",$comDetail->expiration_date);
            $expiration_date = get_date_format($expiration_date_tmp);
          }
          //----- Subscriber DOB -----------
          if($comDetail->subscriber_DOB == '0000-00-00' || $comDetail->subscriber_DOB == ''){
            $subscriber_DOB = get_date_format($patientDetail->DOB);
          }
          else{
            $subscriber_DOB = get_date_format($comDetail->subscriber_DOB);
          }
          
					if($i_key == '1' || $i_key == '2')
					{	
						include ('eligibility_data.php');
					}
					
					$request = ($i_key == '1') ? 'typeahead||resp_comp||match_provider' : 'match_provider';
					$ins_provder_data = insurance_provider($i_key, $ins_xml_extract,$request,$comDetail->provider,$comDetail->comp_name,$data_obj->res_name);
					if( $i_key == '1') {
						// Get Data for TypeAhead
						$data_obj->res_name_comp = isset($ins_provder_data['res_name_comp']) ? $ins_provder_data['res_name_comp'] : $data_obj->res_name_comp;
						$data_obj->typeahead_data= isset($ins_provder_data['typeahead']) ? $ins_provder_data['typeahead'] : $data_obj->typeahead_data;
					}
					
					$insCompanyName = $ins_provder_data['company_name'];
					$insCompanyId = $ins_provder_data['ins_company_id'];
					$insInHouseCode = $ins_provder_data['in_house_code'];
					
					// Update Class Variable
					$class_ins_company_name = $s_name.'InsCompanyName';
					$class_ins_company_id = $s_name.'InsCompanyId';
					$class_ins_in_house_code = $s_name.'InsInHouseCode';
          
					$data_obj->$class_ins_company_name = $insCompanyName;
					$data_obj->$class_ins_company_id = $insCompanyId;
          $data_obj->$class_ins_in_house_code = $insInHouseCode;
        
          $class = 'form-control';
          $strInsCompanyName = "";
          if(strlen($insCompanyName)>12){
            $strInsCompanyName = substr($insCompanyName,0,12)."..";
          }
          else{
            $strInsCompanyName = $insCompanyName;
          }
          
          $updateSubDataArr = array();
          
					$is_collapsed = ($comDetail->provider > 0 || $comDetail->provider != '') ? 'false' : 'true';
					$is_expanded = ($comDetail->provider > 0 || $comDetail->provider != '') ? 'true' : 'false';
					$cont_class = ($is_collapsed == 'false' ) ? 'is-expanded animateIn' : 'is-collapsed';
					$cont_box_class = ($is_expanded == 'true' ) ? 'is-collapses is-expanded' : '';
					
					$var_scan_document_id 	= $s_name.'_scan_documents_id';
					$var_create_acc_ins_sub = 'hid_create_acc_'.$s_name.'_ins_sub';
					$var_inactive_ins_comp 	= 'inactive'.ucfirst($s_name).'InsComp';
					
					$data_inactive_ins_comp 	= 'inactive'.ucfirst($s_name).'Ins';
        ?>	
        <input type="hidden" name="<?php echo $var_scan_document_id;?>" id="<?php echo $var_scan_document_id;?>" value="<?php echo $scan_documents_id1; ?>"  />
       	<input type="hidden" name="<?php echo $var_create_acc_ins_sub;?>" id="<?php echo $var_create_acc_ins_sub;?>" value="no" />
          
       	<dt>
          	<a href="#accordion<?php echo $i_key; ?>" aria-expanded="<?php echo $is_expanded; ?>" aria-controls="accordion<?php echo $i_key; ?>" class="accordion-title accordionTitle js-accordionTrigger <?php echo $cont_box_class; ?>"><?php echo ucfirst($i_type); ?> Ins. <?php echo("Case&nbsp;[".$current_case_name."]");?></a>
            
            <div class="actnbar">
              <ul>
                <li><?php echo $image ?></li>
                <li><?php echo $image2; ?></li>
                <?php if($vsStatus && ($i_key == '1' || $i_key == '2')) { ?>
                	<li data-toggle="tooltip" data-placement="bottom" title="<?php echo $vsToolTip; ?>" ><?php echo $vsStatus; ?></li>
                <?php }  ?>
                
                <li class="insuvalid text-left">
                	<?php
										if($i_key == 1){
											$on_change = 'javascript:insuranceCaseFrm.submit();"';
										}
										else{
											$on_change = 'getNewCom('.(int) $i_key.');';
										}
									?>
                  <select class="selectpicker" id="<?php echo $var_inactive_ins_comp;?>" name="<?php echo $var_inactive_ins_comp;?>" title="<?php echo imw_msg('drop_sel');?>" onChange="<?php echo $on_change; ?>" data-width="100%">
                    <option value=""><?php echo imw_msg('drop_sel');?></option>
                    
                    <?php
											echo $$data_inactive_ins_comp;
										?>
                  </select>
                </li>
                <?php if($i_key == '1' || $i_key == '2') { ?>
                <li>
                  <?php 
                    if((constant("ENABLE_REAL_ELIGILIBILITY") == "YES") && ((int)$comDetail->id > 0))
                    {
                        if($intTotVSCertInsComp > 0)
                        {
                          $realTimeOnClick	=	'onclick="getRealTimeEligibility(\''.$comDetail->id.'\');"';	
                        }
                        elseif($intTotVSCertInsComp == 0)
                        {
                          $realTimeOnClick	=	'onclick="getRealTimeEligibility(\''.$comDetail->id.'\', \'1\');"';
                        }
                    }
                  ?>
                  <?php if(!empty($comDetail->id) && !empty($comDetail->policy_number)){?>
                  <a id="anchorEligibility" href="javascript:void(0);" class="realtime_icon <?php echo $imgRealTimeEli;?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $vsToolTip ?>" <?php echo $realTimeOnClick?>></a>
                  <?php }else{?>
				  <a id="anchorEligibility" href="javascript:void(0);" class="realtime_icon_disabled <?php echo $imgRealTimeEli;?>" data-toggle="tooltip" data-placement="bottom" title="Required information missing. Check Payer/Policy# details." onclick="top.fancyAlert('Required information missing. Check Payer/Policy# details.')"></a>
				  <?php }?>
                </li>
                <?php }
				if($i_key == '1'){?>
                <?php if(!empty($comDetail->id) && !empty($comDetail->policy_number)){?>
                <li>
                  <a href="#" class="preautho" data-toggle="tooltip" data-placement="bottom" title="Pre-Authorization" onclick="getPreAuthorization('<?php echo $comDetail->id; ?>')"></a>
                </li>
                <?php }?>
                <li>
                  <a class="opencase" data-toggle="tooltip" data-placement="bottom" title="Patient Open Case Insurance Summary" href="javascript:insOpenCaseSummary();"></a>
                </li>
                <li data-toggle="modal" data-target="#copy_ins_comp_id">
                  <a class="pcopy" data-toggle="tooltip" data-placement="bottom" title="Copy" href="javascript:copy_insurance_div('block','primary');"></a>
                </li>
                <li data-toggle="modal" data-target="#re_arrange_id" >
                  <a class="irearrenge pointer" data-toggle="tooltip" data-placement="bottom" title="Re-Arrange" ></a>
                </li>
                <?php } ?>
                <li>
                  <a href="#" class="iscani" data-toggle="tooltip" data-placement="bottom" title="Scan" onClick="scan_card('<?php echo $i_type; ?>','<?php echo $isRecordExists;?>');" id="<?php echo $s_name; ?>_scan_img"></a>
                </li>
              
              
              </ul>
         	</div>
            
            
        </dt>
        
        <dd class="accordion-content accordionItem <?php echo $cont_class; ?>" id="accordion<?php echo $i_key; ?>" aria-hidden="<?php echo $is_collapsed; ?>">
          
          
          
          <div class="clearfix"></div>
          
          <div class="inscasesect">
          	<?php require(getcwd().'/insurance_detail.php');?>
        	</div>
        
        </dd>	
          
              
        <?php	
			switch($i_type) {
				case 'primary':
					$primaryComDetail = $comDetail;
					break;
				case 'secondary':
					$secondaryComDetail = $comDetail;
					break;
				case 'tertiary':
					$tertiaryComDetail = $comDetail;
					break;
			}
        }
        ?>    
			</dl>
	
  </div>
</div>      


<?php
if($data['policy_status'] == 1)
{
	require_once(getcwd()."/audit_caseview.php");
	echo '<input type="hidden" value="'.urlencode(serialize($arrAuditTrailPri)).'" name="hidDataPri">';
	echo '<input type="hidden" value="'.urlencode(serialize($arrAuditTrailSec)).'" name="hidDataSec">';
	echo '<input type="hidden" value="'.urlencode(serialize($arrAuditTrailTer)).'" name="hidDataTer">';
}
?>


