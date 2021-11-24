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
if(empty($elem_opidTestOrdered) && !empty($form_id)){
	list($elem_opidTestOrdered,$elem_opidTestOrderedDate) = $objTests->get_chart_order_info($get_test_name,$patient_id,$chart_form_id);
}

$this_test_scan_upload_abbr = $objTests->get_scan_upload_test_name($this_test_properties['test_table'],$this_test_properties['test_type']);

//Synergy function
$synegy_link="";
if($GLOBALS['Show_Synergy_Icon']=="1"){
$sf_ufnm = $_SESSION["authUser"]; // $_SESSION["authUser"];
$sf_row = getRecords("patient_data", "id", $_SESSION["patient"]);
$sf_pt_f = strtoupper($sf_row["fname"]); $sf_pt_f = trim($sf_pt_f);
$sf_pt_l = strtoupper($sf_row["lname"]); $sf_pt_l = trim($sf_pt_l);
$sf_pt_dob = get_date_format($sf_row["DOB"]); //$sf_pt_dob = str_replace("-","/", $sf_pt_dob);
$sf_onclick = "op_synergy('".$sf_ufnm."', '".$sf_pt_f."', '".$sf_pt_l."', '".$sf_pt_dob."');";
$synegy_link = "<img src=\"".$library_path."/images/synergy.jpg\" alt=\"Synergy\" title=\"Synergy\" onclick=\"".$sf_onclick."\" style=\"height:25px !important;cursor:pointer;\">";
}
//--

?>
				<div class="testtbar">
                    <div class="row">
                    	<?php
							$first_col_width='3'; $second_col_width='7';
                        	if($test_table_name=='vf_gl' || $test_table_name=='oct_rnfl'){$first_col_width='2'; $second_col_width='8';}
                            else if($test_table_name=='iol_master_tbl' || $test_table_name=='surgical_tbl'){$first_col_width='2'; $second_col_width='8';}?>
                        <div class="col-sm-<?php echo $first_col_width;?> form-inline">
                        	<?php if(($this_test_properties['test_table']=='test_other' && $this_test_properties['test_type']==0) || (isset($callFromInterface) && $callFromInterface=='admin')){
								echo '<label for="">Test Name</label><input type="text" name="elem_testOtherName" id="elem_testOtherName" value="'.$elem_testOtherName.'" class="form-control" style="width:80%;">';
							}else if($this_test_properties['test_table']=='test_labs'){
								echo '<label for="">Lab Name</label><input type="text" name="elem_testLabsName" id="elem_testLabsName" value="'.$elem_testLabsName.'" class="form-control" style="width:80%;">';
							}else{
								 echo '<h3 class="margin_0 mt10">&nbsp;'.$this_test_screen_name.'</h3>';
							}?>
							<?php if(!empty($purged)){echo '<strong class="red-flag">(Purged)</strong>';}?>
                        </div>
                        <div class="col-sm-<?php echo $second_col_width;?> form-inline">
                            <div class="form-group">
                               <label for="elem_opidTestOrdered">Order By</label>
                                <select name="elem_opidTestOrdered" tabindex="1" id="elem_opidTestOrdered" class="form-control minimal" <?php if(isset($callFromInterface) && $callFromInterface=='admin') echo 'disabled';?>>
                                    <option value="">-SELECT-</option>
                                    <?php foreach($order_by_users as $uid=>$uname){
                                        $sel = '';
                                        if(!empty($elem_opidTestOrdered) && $elem_opidTestOrdered==$uid) $sel = ' selected';
                                        if(empty($elem_opidTestOrdered)) $sel = ($logged_user == $uid) ? " selected" : "";
                                        if(isset($_SESSION['res_fellow_sess']) && !empty($_SESSION['res_fellow_sess']) && $_SESSION['res_fellow_sess'] == $uid){ $sl = " selected"; }
                                        echo '<option value="'.$uid.'"'.$sel.'>'.$uname.'</option>';
                                    }?>
                                </select>
                            </div>
                            <div class="form-group mlr5">
                                <label for="elem_opidTestOrderedDate">Order Date </label>
                                <div class="input-group">
                                  <input type="text" tabindex="1" class="form-control datePicker" name="elem_opidTestOrderedDate" id="elem_opidTestOrderedDate" value="<?php echo $elem_opidTestOrderedDate;?>" <?php if(isset($callFromInterface) && $callFromInterface=='admin') echo 'disabled';?> style="width:90px !important;" />
                                  <label class="input-group-addon" for="elem_opidTestOrderedDate"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="elem_examDate">DOS</label>
                                <?php
									if($elem_examDate=='' || $elem_examDate=='0000-00-00') $elem_examDate = date('Y-m-d');
								?>
                                <input type="text" tabindex="1" class="form-control" id="elem_examDate" name="elem_examDate" value="<?php echo $elem_examDate;?>" style="width:90px !important;" <?php if(isset($callFromInterface) && $callFromInterface=='admin') echo 'disabled';?>>
                            </div>
                            <?php if($test_table_name=='vf_gl' || $test_table_name=='oct_rnfl'){?>
                            <div class="form-group">
                                <label for="elem_testTime">Time</label>
	                            <input type="text" tabindex="1" class="form-control" id="elem_testTime" name="elem_testTime" value="<?php echo $elem_testTime;?>" style="width:65px !important;" onclick="insertTime(this);">
                            </div>
                            <?php }else if($callFromInterface != 'admin' && ($test_table_name=='surgical_tbl' || $test_table_name=='iol_master_tbl')){
								if(constant("ZEISS_FORUM") == "YES"){
									if($test_table_name=='surgical_tbl') $procedure_opts = $objTests->zeissProcOpts(1);
									else $procedure_opts = $objTests->zeissProcOpts(7);?>
                                    <div class="form-group">
                                        <select id="forum_procedure" name="forum_procedure" class="form-control minimal">
                                            <option value="">FORUM PROCEDURE</option>
                                            <?php
                                                foreach($procedure_opts as $key=>$proc){
                                                    $selected = "";
                                                    if($key==$forum_procedure){$selected='selected="selected"';}
                                                    print '<option '.$selected.' value="'.$key.'">'.$proc.'</optionn>';
                                                }
                                            ?>
                                        </select>
                                    </div><?php
								}?>
                                    <div class="form-group" tabindex="1">
                                        <label for="">Pref. Card</label>
                                        <?php echo $objTests->DropDown_Interpretation_Profile($this_test_properties['id']);?>
                                    </div>
                            <?php }?>                            
                        </div>
                        <?php if(!isset($callFromInterface) || $callFromInterface!='admin'){;?>
                        <div class="col-sm-2 form-inline text-right">
                            <img src="<?php echo $library_path; ?>/images/scan_upload.png" alt="Scan" class=" link_cursor" style="height:25px !important;" onclick="openScan('scan','<?php echo $test_edid;?>','<?php echo $this_test_scan_upload_abbr;?>','<?php echo $form_id;?>','<?php echo $this_test_properties['id'];?>');" /> 
                            <img src="<?php echo $library_path; ?>/images/fund2.png" alt="Upload" class="link_cursor" onclick="openScan('upload','<?php echo $test_edid;?>','<?php echo $this_test_scan_upload_abbr;?>','<?php echo $form_id;?>','<?php echo $this_test_properties['id'];?>');"/> 
			<img src="<?php echo $library_path; ?>/images/Patient-at-a-glanceicon2.png" alt="PAG" title="Patient at a glance" onclick="opPag()" style="height:25px !important;cursor:pointer;">
				<?php echo $synegy_link; ?>
                            <?php /*if (constant('AV_MODULE')=='YES'){?><img src="../../library/images/fund5.png" alt="Audio/Video" class=" link_cursor" onClick="top.showRecordingControl('<?php echo $AV_testname;?>','<?php echo $tId.'_'.$form_id;?>','show_media_list()');" title="Record Audio/Video Message" /><?php }*/ ?>
                        </div>
                        <?php }?>
                    </div>
                </div>