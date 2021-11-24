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
require_once("../admin_header.php");

function get_custom_test_variations($tests_name_pkid){
	$q = "SELECT test_main_options_ids,test_main_options_text,id as version_id FROM tests_version WHERE test_main_options_ids!='' AND test_main_options_text!='' AND tests_name_id = '$tests_name_pkid' ORDER BY id desc LIMIT 0,1";
	$res = imw_query($q);
	if($res && imw_num_rows($res)==1){
		return imw_fetch_assoc($res);
	}
	return false;
}

function get_custom_test_variation_name_by_id($tests_name_pkid,$variation_id){
	$rs = get_custom_test_variations($tests_name_pkid);
	if($rs && is_array($rs)){
		$custom_test_variations_ids = explode(',',$rs['test_main_options_ids']);
		$custom_test_variations_text= explode(',',$rs['test_main_options_text']);
		if(is_array($custom_test_variations_ids) && count($custom_test_variations_ids)>0){
			for($i=0;$i<count($custom_test_variations_ids);$i++){
				$custom_test_variation_subid = end(explode('_',$custom_test_variations_ids[$i]));
				if($custom_test_variation_subid==$variation_id){
					return $custom_test_variations_text[$i];
				}
			}
		}
	}
}

function get_cpt_Ophtha(){
	$arr=array();
	$sql = " SELECT cpt_prac_code FROM `cpt_fee_tbl`where cpt4_code IN ('92201', '92202', '92225', '92226') AND status='Active' AND delete_status='0' ";
	$res = sqlStatement($sql);
	for($i=0;$row=sqlFetchArray($res);$i++){
		$arr[]=trim($row["cpt_prac_code"]);
	}
	return $arr;
}

function is_duplicate_Ophtha($tid=0, $pcpt, $eid=0){
	$flg=0; $ret=0;
	if(!empty($eid) && empty($tid)){
		$sql = "SELECT superbill_test_id FROM superbill_test_cpt
				WHERE id='".$eid."' ";
		$res = sqlQuery($sql);
		if($res!=false){
			$tid=$res["superbill_test_id"];
		}
	}	
	
	if(!empty($tid)){
		$sql = "SELECT count(*) as num FROM superbill_test
				WHERE id='".$tid."' AND (test='Ophthalmoscopy Optic Nerve & Macula' OR test='Ophthalmoscopy Retina drawing and scleral depression') ";
		$res = sqlQuery($sql);
		if($res!=false && $res["num"]>0){$flg=1;}
	}	
	
	$pcpt=trim($pcpt);
	if(!empty($flg) && !empty($tid) && !empty($pcpt)){

		$ar_ophtha_codes = get_cpt_Ophtha();
		if(count($ar_ophtha_codes)>0 && in_array($pcpt, $ar_ophtha_codes)){
			$str_ophtha_codes = "'".implode("','", $ar_ophtha_codes)."'";
			$phrase_prct_code = " AND practice_cpt IN (".$str_ophtha_codes.") ";			
		}else{
			$phrase_prct_code = " AND practice_cpt='".$pcpt."' ";
		}

		$sql = "SELECT count(*) as num FROM superbill_test_cpt WHERE superbill_test_id='".$tid."' ".$phrase_prct_code;
		if(!empty($eid)){
			$sql .= " AND id!='".$eid."' ";
		}		
		$res = sqlQuery($sql);
		if($res!=false && $res["num"]>0){
			$ret=1;
		}
	}
	return $ret;
}

//-------GETTING TESTS NAME---
$res_testsname = imw_query("SELECT test_name,temp_name from tests_name WHERE del_status=0 AND status=1");
$tests_names = array();
while($rs = imw_fetch_assoc($res_testsname)){
	$tests_names[$rs['test_name']] = $rs['temp_name'];
}


if(isset($_POST['del_cpt_test_id']) && $_POST['del_cpt_test_id'] > 0)
{
	$del_cpt_test_id = (int) $_POST['del_cpt_test_id'];	
	$del_cpt_qry = "DELETE FROM superbill_test_cpt WHERE id = ".$del_cpt_test_id;
	//imw_query($del_cpt_qry);
}

if(isset($_POST['edit_cpt_tests_sbt']) && $_POST['edit_cpt_tests_sbt']==1)
{
	$spt_cpt_ids = $_POST['spt_cpt_ids'];
	foreach($spt_cpt_ids as $spt_key => $spt_cpt_id)
	{
		if($spt_cpt_id != "")
		{
			//$sb_test_id = $_POST['superbill_test'][$spt_key];
			$ins_commercial = isset($_POST['ins_commercial'.$spt_cpt_id]) ? 1 : 0;
			$ins_medicare =  isset($_POST['ins_medicare'.$spt_cpt_id]) ? 1 : 0;
			//echo '<br/>';
			$practice_cpt = $_POST['practice_cpt'][$spt_key];
			$site = $_POST['site'.$spt_cpt_id];
			$flg = is_duplicate_Ophtha(0, $practice_cpt, $spt_cpt_id);
			if($flg){
			//donothing
			}else{
			$req_qry = "UPDATE superbill_test_cpt SET ins_commercial = $ins_commercial,ins_medicare = $ins_medicare, practice_cpt = '".$practice_cpt."', site= '".$site."' WHERE id = ".$spt_cpt_id;
			imw_query($req_qry);	
			}	
		}
	}
	
	// for inserting the new records
	$new_cpt_code = $_POST['new_cpt_code'];
	foreach($new_cpt_code as $cur_cpt_code)
	{
		$new_test_id = $_POST['new_test_id'.$cur_cpt_code];
		$new_ins_commercial = isset($_POST['new_ins_commercial'.$cur_cpt_code]) ? 1:0;
		$new_ins_medicare = isset($_POST['new_ins_medicare'.$cur_cpt_code]) ? 1:0;	
		$new_ins_medicare = $_POST['new_ins_medicare'.$cur_cpt_code];
		$new_site = $_POST['new_site'.$cur_cpt_code];	
		$new_practice_cpt = $_POST['new_practice_cpt'.$cur_cpt_code];			
		if($new_practice_cpt != "")
		{
			$flg = is_duplicate_Ophtha($new_test_id, $new_practice_cpt);
			if($flg){
			echo "<script>top.alert_notification_show('Record for ".$new_practice_cpt." already exists.');</script>";
			}else{
			$sbCptQry = "INSERT INTO superbill_test_cpt(superbill_test_id,practice_cpt,ins_commercial,ins_medicare,site) VALUES($new_test_id,'".$new_practice_cpt."','".$new_ins_commercial."','".$new_ins_medicare."','".$new_site."')";			
			imw_query($sbCptQry);
			}	
		}
	}
	echo "<script>top.alert_notification_show('Record Saved Successfully.');</script>";
}

if(isset($_POST['add_practice_cpt_sbt']) && $_POST['add_practice_cpt_sbt'] != ""){
	$variation_id = 0;
	$test_id = $_POST['superbill_test'];
	if(strpos($test_id,'_')>0){
		$temp_test_id_arr = explode('_',$test_id);
		$test_id = $temp_test_id_arr[0];
		$variation_id = $temp_test_id_arr[1];
	}
	$practice_cpt = $_POST['practice_cpt'];
	$commercial_cpt = $_POST['commercial_cpt'];
	$medicare_cpt = $_POST['medicare_cpt'];
	$site = $_POST['site'];
	$flg = is_duplicate_Ophtha($test_id, $practice_cpt);
	if($flg){
	echo "<script>top.alert_notification_show('Record for this test already exists.');</script>";
	}else{
	$sbCptQry = "INSERT INTO superbill_test_cpt(superbill_test_id,practice_cpt,ins_commercial,ins_medicare,site,custom_test_variation_id) VALUES($test_id,'".$practice_cpt."','".$commercial_cpt."','".$medicare_cpt."','".$site."','".$variation_id."')";	
	imw_query($sbCptQry);
	echo "<script>top.alert_notification_show('Record Saved Successfully.');</script>";
	}
}


// Getting all the tests
$get_tests = "SELECT * FROM superbill_test order by superbill_test.test";
$tests_obj = imw_query($get_tests);
$sb_tests_arr = array();
while($superbill_test_row = imw_fetch_assoc($tests_obj))
{
	$sb_tests_arr[$superbill_test_row['id']] = $superbill_test_row['test'];
}

$prac_code_qry = "select cpt_prac_code from cpt_fee_tbl where cpt_prac_code != '' and delete_status = '0' group by cpt_prac_code";
$prac_code_qry_obj = imw_query($prac_code_qry);
$prac_code_arr = array();
while($prac_code_val = imw_fetch_assoc($prac_code_qry_obj)){
	$prac_code_arr[] = $prac_code_val['cpt_prac_code'];
}
$js_arr = $prac_code_arr;

?>
<script type="text/javascript">	
	var prac_code_arr = <?php echo json_encode($js_arr); ?>;
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_cn_test.js"></script>
<style>
	.form-control{width:93%;}
</style>
<body>
<div class="whtbox">
	<form action="" method="post" name="del_cpt_tests">
			<input type="hidden" value="" id="del_cpt_test_id" name="del_cpt_test_id" />
		</form>
		<form method="post" name="edit_cpt_tests" action="" id="save_test_prac_ins">
		<input type="hidden" name="edit_cpt_tests_sbt" value="1" />
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
					<th style="width:20%;">Test CPT Prefrence</th>               
					<th class="text-center" style="width:5%;">Commercial</th>
					<th class="text-center" style="width:5%;">Medicare</th>
					<th class="text-center" style="width:5%;">OU</th>
					<th class="text-center" style="width:5%;">OD</th>
					<th class="text-center" style="width:5%;">OS</th>                                        
					<th style="width:40%;">Practice CPT</th>                    
				</tr>
			</thead>
			<tbody>
			 <?php
				$count_qry = "SELECT count(spt_cpt.id) as spt_cpt_test_count, spt.id as spt_test_id FROM superbill_test spt INNER JOIN superbill_test_cpt spt_cpt ON spt.id = spt_cpt.superbill_test_id group by spt.test";
				$count_cpt_result = imw_query($count_qry);
				$count_cpt_arr = array();
				while($count_cpt = imw_fetch_assoc($count_cpt_result))
				{
					$count_cpt_arr[$count_cpt['spt_test_id']] = $count_cpt['spt_cpt_test_count'];	
				}
				$previous_id = 0;
				$count_match = 0;
				$superbill_test_qry = "SELECT spt.test, spt.id as spt_test_id, spt.tests_name_pkid, spt_cpt.* FROM superbill_test_cpt spt_cpt INNER JOIN superbill_test spt ON (spt.id = spt_cpt.superbill_test_id) order by spt.test, spt_cpt.ins_commercial DESC, spt_cpt.ins_medicare DESC, spt_cpt.site, spt_cpt.practice_cpt";			
				$result_obj = imw_query($superbill_test_qry);
				while($test_prac_row = imw_fetch_assoc($result_obj))
				{
					if($previous_id != $test_prac_row['spt_test_id'] || (int)$test_prac_row['custom_test_variation_id']>0)
					{
						$show_superbill_test_name = $test_prac_row['test'];
						if($tests_names[$show_superbill_test_name] != ''){
							$show_superbill_test_name = $tests_names[$show_superbill_test_name];
						}
						$show_superbill_test_name_sufix = '';
						if((int)$test_prac_row['custom_test_variation_id']>0){
							$show_superbill_test_name_sufix = '-'.get_custom_test_variation_name_by_id($test_prac_row['tests_name_pkid'],$test_prac_row['custom_test_variation_id']);
						}
						
						$count_match = 0;
				?>
					<tr class="danger">
						<td colspan="7" style="padding:5px;"><?php echo $show_superbill_test_name.$show_superbill_test_name_sufix; ?></td>
					</tr>
				<?php									
					}					
					$count_match++;

					$hiddensite="";
					if($show_superbill_test_name == "Ophthalmoscopy Optic Nerve & Macula" || 
						$show_superbill_test_name == "Ophthalmoscopy Retina drawing and scleral depression"
						){
						$hiddensite=" hidden ";
					}

			?>
				<tr class="tests_tab_row warning"> 
					<td><input type="hidden" name="spt_cpt_ids[]" class="cpt_rc_cl" value="<?php echo $test_prac_row['id']; ?>" /></td>                       
					<td align="text-center"><div class="col-xs-2 col-xs-offset-4"><div class="checkbox checkbox-inline"><input type="checkbox" class="ins_commercial_cl" name="ins_commercial<?php echo $test_prac_row['id']; ?>" id="ins_commercial<?php echo $test_prac_row['id']; ?>" value="1" <?php if($test_prac_row['ins_commercial'] == 1){ echo 'checked="checked"'; } ?>  /><label  for="ins_commercial<?php echo $test_prac_row['id']; ?>"></label></div></div></td>
					<td align="text-center"><div class="col-xs-2 col-xs-offset-4"><div class="checkbox checkbox-inline"><input type="checkbox" class="ins_medicare_cl" name="ins_medicare<?php echo $test_prac_row['id']; ?>" id="ins_medicare<?php echo $test_prac_row['id']; ?>" <?php if($test_prac_row['ins_medicare'] == 1){ echo 'checked="checked"'; } ?> value="1" /><label for="ins_medicare<?php echo $test_prac_row['id']; ?>"></label></div></div></td>
					<td class="text-center"><div class="radio radio-inline"><input type="radio" class="ou_cl" name="site<?php echo $test_prac_row['id']; ?>" id="site<?php echo $test_prac_row['id']; ?>OU" value="OU" <?php if($test_prac_row['site'] == 'OU'){echo 'checked="checked"';} ?> /><label for="site<?php echo $test_prac_row['id']; ?>OU"></label></div></td>						
					<td class="text-center"><div class="radio radio-inline <?php echo $hiddensite; ?>"><input type="radio" class="od_cl" name="site<?php echo $test_prac_row['id']; ?>" id="site<?php echo $test_prac_row['id']; ?>OD" value="OD" <?php if($test_prac_row['site'] == 'OD'){echo 'checked="checked"';} ?> /><label for="site<?php echo $test_prac_row['id']; ?>OD" ></label></div></td>						
					<td class="text-center"><div class="radio radio-inline <?php echo $hiddensite; ?>"><input type="radio" class="os_cl" name="site<?php echo $test_prac_row['id']; ?>" id="site<?php echo $test_prac_row['id']; ?>OS" value="OS" <?php if($test_prac_row['site'] == 'OS'){echo 'checked="checked"';} ?> /><label for="site<?php echo $test_prac_row['id']; ?>OS"></label></div></td>						                                                
					<td>
						<input type="text" class="prac_cpt_cl form-control pull-left" name="practice_cpt[]" value="<?php echo $test_prac_row['practice_cpt']; ?>" />
						&nbsp;&nbsp;
						<a href="#return false;" onClick="del_cpt_test(<?php echo $test_prac_row['id']; ?>,'<?php echo $test_prac_row['practice_cpt']; ?>','<?php echo $test_prac_row['test']; ?>',this)"><img border="0" src="../../../library/images/close_small.png" alt="" /></a>
						<?php 
							if($count_match == $count_cpt_arr[$test_prac_row['spt_test_id']])
							{
						?>                            	
							<a onClick="add_prac_cpt_test(<?php echo $test_prac_row['spt_test_id']; ?>,this)" href="#return false;"><img border="0" src="../../../library/images/add_small.png" alt="" title="Add Practice CPT" /></a>
						<?php			
							}
						?>
					</td>                        
					<!-- <td align="center"><a href="# return false;" onClick="selSbCPt(<?php echo $test_prac_row['spt_test_id']; ?>,this,<?php echo $test_prac_row['ins_commercial']; ?>,<?php echo $test_prac_row['ins_medicare']; ?>);"><img src="../../../images/edit.png" style="border:0px;"></a></td> -->
				</tr>            
			<?php						
				$previous_id = $test_prac_row['spt_test_id'];					
				}
			?> 
			<tbody>
		</table>
		</div>
	</form>
	</div>

<div id="divForm_cn_test" class="modal" role="dialog">
	<div class="modal-dialog"> 
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Add Test Practice CPT</h4>
			</div>
			<form name="add_practice_cpt" id="add_practice_cpt" method="post" onSubmit="return save_new_test_cpt();">
			<div class="modal-body">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-3">
							<label>Choose Test</label>
						</div>
						<div class="col-sm-9">
							<?php
							$sup_tests_options_str = '';
                            $get_tests = "SELECT * FROM superbill_test order by superbill_test.test";
                            $tests_obj = imw_query($get_tests);
                            while($superbill_test_row = imw_fetch_assoc($tests_obj)){
                                $opt_superbill_test_name = $superbill_test_row['test'];
                                if($tests_names[$opt_superbill_test_name] != ''){
                                    $opt_superbill_test_name = $tests_names[$opt_superbill_test_name];
                                }
                                $opt_superbill_test_type = $superbill_test_row['test_type'];
                                $opt_superbill_test_pkid = $superbill_test_row['tests_name_pkid'];
                                $sup_tests_options_str .= '<option value="'.$superbill_test_row['id'].'">'.$opt_superbill_test_name.'</option>';
                                /*******work start to manage different variations of template based tests*******/
                                if($opt_superbill_test_type=='1'){
                                    $custom_test_variations_rs = get_custom_test_variations($opt_superbill_test_pkid);
                                    if($custom_test_variations_rs && is_array($custom_test_variations_rs) && !empty($custom_test_variations_rs['test_main_options_ids'])){
										$custom_test_variations_ids = explode(',',$custom_test_variations_rs['test_main_options_ids']);
										$custom_test_variations_text= explode(',',$custom_test_variations_rs['test_main_options_text']);
										if(is_array($custom_test_variations_ids) && count($custom_test_variations_ids)>0){
											for($i=0;$i<count($custom_test_variations_ids);$i++){
												$custom_test_variation_subid = end(explode('_',$custom_test_variations_ids[$i]));
												$subopt_superbill_test_name = $opt_superbill_test_name.' : '.$custom_test_variations_text[$i];
												$sup_tests_options_str .= '<option value="'.$superbill_test_row['id'].'_'.$custom_test_variation_subid.'">'.$subopt_superbill_test_name.'</option>';
											}
										}
									}
                                }
                                /*******work stop to manage different variations of template based tests*******/									
                            }
                            ?>
							<input type="hidden" name="update_frm" id="update_frm" value="0" />
							<select name="superbill_test" id="superbill_test" class="selectpicker" data-width="93%" data-size="8">
								<option value="">Select Test</option>
								<?php echo $sup_tests_options_str;?>
							</select>
						</div>
					</div><br />
					<div class="row">
						<div class="col-sm-3">
							<label>Practice CPT</label>
							
						</div>
						<div class="col-sm-9">
							<input type="text" name="practice_cpt" id="practice_cpt" class="prac_cpt_cl form-control" />
						</div>
					</div><br />
					<div class="row">
						<div class="col-sm-3">
							<label>Commercial</label>
							
						</div>
						<div class="col-sm-9">
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="commercial_cpt" id="commercial_cpt" value="1" />
								<label for="commercial_cpt"></label>
							</div>
						</div>
					</div><br />
					<div class="row">
						<div class="col-sm-3">
							<label>Medicare</label>
							
						</div>
						<div class="col-sm-9">
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="medicare_cpt" id="medicare_cpt" value="1" />
								<label for="medicare_cpt"></label>
							</div>		
						</div>
					</div><br />
					<div class="row">
						<div class="col-sm-3">
							<label>Site</label>
						</div>
						<div class="col-sm-9">
							<div class="radio radio-inline" >
								<input type="radio" name="site" id="site_1" value="OU" /> 
								<label for="site_1" style="color:purple;">OU</label>
							</div>
							<div class="radio radio-inline" id="rdsite_2">
								<input type="radio" name="site" id="site_2" value="OD" />
								<label for="site_2" style="color:blue;">OD</label>
							</div>
							<div class="radio radio-inline" id="rdsite_3">	
								<input type="radio" name="site" id="site_3" value="OS" />
								<label for="site_3" style="color:green;">OS</label>	
							</div>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="add_practice_cpt_sbt" value="Save New" />
			<div id="module_buttons" class="ad_modal_footer modal-footer">
				<input type="button" name="elem_btnSave" value="Save" class="btn btn-success" onClick="top.fmain.save_new_test_cpt();">
				<input type="button" name="elem_btncancel" value="Cancel" class="btn btn-danger" onClick="show_data_form(1)">
			</div>
      </form>
		</div>
		
	</div>
</div>
<?php	
	require_once("../admin_footer.php");
?>