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
require_once("../../admin_header.php");
require_once("../../../../library/classes/common_function.php");
require_once("../../../../library/classes/cpt_fee_class.php");
$operator_id = $_SESSION['authUserID'];
$order_asc_desc = (isset($_REQUEST['sort_by'])) ? $_REQUEST['sort_by'] : 'ASC';
$order_field = (isset($_REQUEST['sort_field'])) ? $_REQUEST['sort_field'] : 'cpt_category_tbl.cpt_category,cpt_fee_tbl.cpt_prac_code';
$sort_first_time = (isset($_REQUEST['sort_first_time'])) ? $_REQUEST['sort_first_time'] : 'yes';
$fee_tbl_cols = (isset($_REQUEST['fee_tbl_cols'])) ? $_REQUEST['fee_tbl_cols'] : array();
$arrow = 'glyphicon glyphicon-chevron-down';
if($order_asc_desc == 'ASC'){
 $arrow = 'glyphicon glyphicon-chevron-up';
}
$arrow1 = $arrow2 = $arrow3 = "";
if($sort_first_time != 'yes'){
	if($order_field == 'cpt_category_tbl.cpt_category') {
		$arrow1 = $arrow;
	}
	if($order_field == 'cpt_fee_tbl.cpt_prac_code') {
		$arrow2 = $arrow;
	}
	if($order_field == 'cpt_fee_tbl.cpt_desc') {
		$arrow3 = $arrow;
	}
}
$cpt_fee_obj = New CPT_Fee($operator_id,$_REQUEST['fee_tbl_cat'],$order_field,$order_asc_desc);
if(empty($_REQUEST['fee_tbl_cat']) === false){
	$fee_tbl_cat_arr = explode(',',$_REQUEST['fee_tbl_cat']);	
}
$currency = str_ireplace("&nbsp;"," ",show_currency());
?>
<body>
<div class=" container-fluid">
	<div class="whtbox" style="margin: 5px;">
	<div  class="table-responsive respotable" style="overflow:hidden;height:<?php echo ($_SESSION['wn_height']-300);?>px;">
		<div class="freetabtop">
			<div class="row">
			<form name="cptFrm" id="cptFrm" action="" method="post" style="margin:0px;">
				<input type="hidden" name="saveDataFld" id="saveDataFld" value="" />
				<input type="hidden" name="id" id="id" value="" />
				<input type="hidden" name="cat_fee_tbl" id="cat_fee_tbl" value="" />
				<input type="hidden" name="sort_order" id="sort_order" value="<?php echo $order_asc_desc; ?>" data-first-time="<?php echo $sort_first_time; ?>">
				<input type="hidden" name="order_field" id="order_field" value="<?php echo $order_field; ?>">
				<div class="col-sm-4"><div class="row">
				<div class="col-sm-7 form-inline">
					<div class="form-group freetablecol">
						<label for="copy_fee_tbl_col_opt">Fee Table Column:</label>
						<select name="copy_fee_tbl_col_opt" id="copy_fee_tbl_col_opt" class="form-control minimal" style="padding: 4px 3px!important;">
							<option value="">Copy From Fee Column</option>
							<?php 
							foreach($cpt_fee_obj->cpt_fee_name_arr as $obj){
								echo "<option value='".$obj['fee_table_column_id']."'>".$obj['column_name']."</option>";
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-5 form-inline">
				<input type="text" class="form-control" name="table_column" id="table_column"> 
				<button type="button" class="freetabut" id="saveDataBtn" name="saveDataBtn"  onClick="save_new_field();">Add New</button>
			</div>
				</div></div>
		</form>	
		<form name="update_fee_frm" id="update_fee_frm" action="" method="post" style="margin:0px;">
			<input type="hidden" name="inc_dec_save" id="inc_dec_save" value="" />
			<input type="hidden" name="cat_fee_tbl" id="cat_fee_tbl_u" value="" />
				<div class="col-sm-3 form-inline">
					<div class="form-group incfees">
						<label for="fee_tbl_col_opt">Increase/Decrease Fees:</label>
						<select name="fee_tbl_col_opt[]" id="fee_tbl_col_opt" multiple class="selectpicker" data-size="11" data-title="Select Fee Column Name" data-actions-box="true" data-style="btn-default btn-sm">
							<?php 
							foreach($cpt_fee_obj->cpt_fee_name_arr as $obj){
								echo "<option value='".$obj['fee_table_column_id']."'>".$obj['column_name']."</option>";
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-3 form-inline increslt">
					<select name="inc_dec_opt" id="inc_dec_opt" class="form-control minimal" style="padding: 4px 3px!important;">
						<option value="increase">Increase</option>
						<option value="decrease">Decrease</option>
					</select>
					<input type="text" class="form-control" name="inc_dec_per" id="inc_dec_per"><label>%</label>
					<button type="button" class="freetabut" id="updatefeebtn" name="updatefeebtn" onClick="updatefee();">UPDATE</button>
				</div>
			</form>
			
			<div class="col-xs-2 form-inline">
				<?php if( is_array($cpt_fee_obj->cpt_fee_name_arr) && count($cpt_fee_obj->cpt_fee_name_arr) > 3) { ?>
				<form name="update_fee_cols" id="update_fee_cols" action="" method="post" style="margin:0px;">
				<select name="fee_tbl_cols[]" id="fee_tbl_cols" multiple class="selectpicker" data-title="Select Fee Columns" data-size="11" data-style="btn-default " data-max-options="3" >
					<?php
					$total_sel = count($fee_tbl_cols);
					$pending_sel = ($total_sel < 3) ? 3 - $total_sel : 0; 
					$viewable_cols = array();
					$cntr = -1;
					foreach($cpt_fee_obj->cpt_fee_name_arr as $obj){
						$cntr++;
						
						if(stristr('Default', $obj['column_name']) && $cntr==0) {
							$viewable_cols[] = $obj;
							continue;
						}
							
						$tmpFeeColId = $obj['fee_table_column_id'];
						$sel = (in_array($tmpFeeColId,$fee_tbl_cols)) ? 'selected' : '';
						
						if( !$sel && $pending_sel > 0 ) {
							$sel = 'selected';
							$pending_sel--;
						}
						
						if( $sel ) $viewable_cols[] = $obj;
						
						echo "<option value='".$tmpFeeColId."' ".$sel." >".$obj['column_name']."</option>";
					}
					?>
				</select>
				<button type="submit" class="freetabut hide" id="updateColBtn" name="updateColBtn">SUBMIT</button>
				</form>
				<?php } else { $viewable_cols = $cpt_fee_obj->cpt_fee_name_arr; }?>							
			</div>
			
			</div>
		</div>
	<div class="clearfix"></div>
	<form name="feeTableFrm" id="feeTableFrm" method="post" style="margin:0px;">
	<input type="hidden" name="DelColumn" id="DelColumn" value="">
    <input type="hidden" name="DelColumnName" id="DelColumnName" value="">
	<input type="hidden" name="saveData" id="saveData" value="">
	<input type="hidden" name="cat_fee_tbl" id="cat_fee_tbl_u" value="" />
	<div class="frtabara">
		<div class="row"  id="main_data_div">		
			<div id="div_cpt_fee_table_left" class="col-sm-6 adminnw" style="overflow: hidden;">
				<table  style="width:100%;">
				<thead>
					<tr style="height:41px;">
						<th class="col-sm-5">
							<div class="row">
								<div class="col-sm-4 text-center" onclick="set_order('cpt_category_tbl.cpt_category');">&nbsp;Category <span class="<?php echo $arrow1;?>"></span></div>
								<div class="col-sm-8">
									<select name="fee_tbl_cat" id="fee_tbl_cat" multiple class="selectpicker content_box" data-size="11" data-title="Select Category" data-actions-box="true" data-width="95%" data-style="btn-default btn-xs" onChange="make_cat_search();" data-selected-text-format="count > 3">
									<?php 
										foreach($cpt_fee_obj->cpt_cat_arr as $obj){
											$selected="";
											if(in_array($obj['cpt_cat_id'],$fee_tbl_cat_arr)){
												$selected=" SELECTED ";	
											}
											echo "<option $selected value='".$obj['cpt_cat_id']."'>".$obj['cpt_category']."</option>";
										}
									?>
									</select>
								</div>
							</div>
						</th>
						<th onclick="set_order('cpt_fee_tbl.cpt_prac_code');">Practice Code <span class="<?php echo $arrow2;?>"></span></th>
						<th onclick="set_order('cpt_fee_tbl.cpt_desc');">Description <span class="<?php echo $arrow3;?>"></span></th>
					</tr>
				</thead>
				<tbody id="cpt_fee_rows">	
					<?php 
						foreach($cpt_fee_obj->cpt_global_arr as $obj){
							if($clr % 2 == 0){ $bgClr = 'alt3'; } else{ $bgClr = ''; }
							$cols=4;
							$cpt_id = $obj['cpt_fee_id'];	
							$inputData='';
							$workRVU =	$cpt_fee_obj->cpt_rvu_records[$cpt_id]['work_rvu'];
							$peRVU =	$cpt_fee_obj->cpt_rvu_records[$cpt_id]['pe_rvu'];
							$mpRVU = 	$cpt_fee_obj->cpt_rvu_records[$cpt_id]['mp_rvu'];
							
							//---- Start Query To get Text Box ---------
							$Detail = $viewable_cols;
							$wid = floor(800 / (count($Detail)+3)); $wid = $wid<140 ? 140 : $wid;
							$tabelWid = ($wid* (count($Detail)+3))+630;
						//	pre($Detail);
							for($d=0;$d<count($Detail);$d++){
								$columnId = $Detail[$d]['fee_table_column_id'];
								//----- Start Query To Get Fee Value Of Every Field ------
								$cpt_fee = $cpt_fee_obj->cpt_fee_table[$cpt_id][$columnId]['cpt_fee'];
								$cpt_fee_table_id = $cpt_fee_obj->cpt_fee_table[$cpt_id][$columnId]['cpt_fee_table_id'];
								$cpt_fee = $cpt_fee >= 0 ? $currency.number_format($cpt_fee,2) : '';
								$workRVU= $workRVU >= 0 ? $currency.number_format($workRVU,2) : '';
								$peRVU= $peRVU >= 0 ? $currency.number_format($peRVU,2) : '';
								$mpRVU= $mpRVU >= 0 ? $currency.number_format($mpRVU,2) : '';
								//----- End Query To Get Fee Value Of Every Field ------
								$inputData .= '
								<td style="width:'.$wid.'px; text-align:left; valign:middle;">
								<textarea class="form-control" style="width:100px;height:26px !important;" name="'.str_replace(" ","_",$Detail[$d]['column_name']."_".$Detail[$d]['fee_table_column_id']).'['.$cpt_id.']">'.$cpt_fee.'</textarea>
								</td>';

								if($d==0){
								$inputData.= '
								<td>
								<textarea class="form-control" style="width:100px;height:26px !important;" name="work_rvu['.$cpt_id.']">'.$workRVU.'</textarea> 
								</td>
								<td>
								<textarea class="form-control" style="width:100px;height:26px !important;" name="pe_rvu['.$cpt_id.']">'.$peRVU.'</textarea>
								</td>
								<td>
								<textarea class="form-control" style="width:100px;height:26px !important;" name="mp_rvu['.$cpt_id.']">'.$mpRVU.'</textarea>
								</td>';				
								}
							$cols++;
							}
                            $obj_tooltiptext='';
							if(strlen(trim($obj['cpt_desc'])) >= "30"){
                                $obj_tooltiptext = (str_ireplace("&amp;","&",trim($obj['cpt_desc'])));
								$obj['cpt_desc'] = substr((str_ireplace("&amp;","&",$obj['cpt_desc'])),0,30)."...";
							}
							if(strlen(trim($obj['cpt_prac_code'])) >= "10"){
								$obj['cpt_prac_code'] = substr($obj['cpt_prac_code'],0,10)."...";
							}
							if(strlen(trim($obj['cpt_category'])) >= "10"){
								$obj['cpt_category'] = substr($obj['cpt_category'],0,10)."...";
							}
							//$checked = $obj['not_covered'] > 0 ? 'checked' : '';
							//---- End Query To get Text Box ---------
							$data .= '
							<tr style="height:25px;">
								<td>'.$obj['cpt_category'].'</td>
								<td>'.$obj['cpt_prac_code'].'</td>
								<td data-toggle="tooltip" title="'.$obj_tooltiptext.'">'.str_ireplace("&amp;","&",(htmlentities($obj['cpt_desc']))).'</td>
							</tr>
							';
							$data1 .= '<tr style="height:25px;">'.$inputData.'</tr>';

							$clr++;
						}
						$totalcptRowsPrinted = $clr;
					echo $data;		
					?>
					</tbody>
				</table>
			</div>
			<div class="col-sm-6">
				<div id="div_cpt_fee_table_right" class="frtabrht adminnw" style="overflow:hidden;">
					<table style="width:100%;">
						<thead>
						<tr style="height:41px;">
						<?php
							$i = 0;
							$headingData = '';
							foreach($viewable_cols as $obj){
								$displayColumnName = '';
								if($i != 0){
									$imgSrc = '&nbsp;&nbsp;<span class="glyphicon glyphicon-remove tbclose" aria-hidden="true" onclick="delColumn(\''.$obj['fee_table_column_id'].'\',\'\',\''.$obj['column_name'].'\');"></span>';
									$displayColumnName = '<a title="Edit" href="javascript:void(0)" onclick="editName(\''.$obj['fee_table_column_id'].'\',\''.$obj['column_name'].'\');">
									'.str_replace(" ","-",$obj['column_name']).'
									</a>';
								}
								else{
									$displayColumnName = $obj['column_name'];
								}	
								
								$displayColumnName = strlen($obj['column_name'])>12 ? '<span title="'.$obj['column_name'].'">'.substr($obj['column_name'],0,12).'..</span>' : $obj['column_name'];
								$headingData .= '
									<th style="width:'.$wid.'px; text-align:left;">
										'.$displayColumnName.$imgSrc.'
									</th>';
								if(stristr('Default', $displayColumnName) && $i==0){
									$headingData.= '
									<th style="width:150px; text-align:left;">Work RVU</th>
									<th style="width:150px; text-align:left;">PE RVU</th>
									<th style="width:150px; text-align:left;">MP RVU</th>
									';		
								}
							$i++;
							}
							echo $headingData;
						?>
						</tr>
						</thead>
						<tbody>
							<?php print $data1; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	</form>
	<div class="clearfix"></div>
	</div>
	</div>
</div>
<?php
	$js_array = array();
	$js_array['rowsToPrint'] = $totalcptRowsPrinted;
	$js_array['json_column_name'] = $cpt_fee_obj->json_cpt_fee_name_arr;
	$js_json_arr = json_encode($js_array);
?>
<script type="text/javascript">
	var json_arr = JSON.parse('<?php echo $js_json_arr; ?>');
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/cpt_fee_js.js"></script>
<script type="text/javascript">
	var ar = new Array();
	ar[0] = new Array("save_cpt_fee","Save","top.fmain.saveFeeData();");
	ar[1] = new Array("export_cpt","Export CSV", "top.fmain.make_csv();");
	top.btn_show("ADMN",ar);
	set_header_title('Fee Table');
	show_loading_image('none');
    $('[data-toggle="tooltip"]').tooltip();
	
	$(document).ready(function(){
		var height = parseInt($(window).innerHeight());
		$('#main_data_div').height(height);
		$('#div_cpt_fee_table_left').height(height-80);
		$('#div_cpt_fee_table_right').height(height);
		$('#div_cpt_fee_table_right').css('overflow','scroll');
	});
	var oldX=0; var oldY=0;
	$(document).ready(function(){
		function ScrollType(x,y)
		{
			if(x!=oldX){oldX=x;oldY=y;return 'ver';}
			else if(y!=oldY){oldX=x;oldY=y;return 'hor';}
		}
		
		$('#div_cpt_fee_table_right').height($(window).height()-70);
		$('#div_cpt_fee_table_right').scroll(function(e){
			dtype = ScrollType($(this).scrollTop(),$(this).scrollLeft());
			if(dtype=="hor"){
				yy = '-'+$(this).scrollLeft()+'px';
				$('#cpt_fee_table_header_top').css('margin-left',yy);
			}
			if(dtype=="ver"){
				xx = '-'+$(this).scrollTop()+'px';
				$('#div_cpt_fee_table_left').scrollTop($(this).scrollTop());
			}
		});
	});
</script>
<?php require_once("../../admin_footer.php"); ?>
