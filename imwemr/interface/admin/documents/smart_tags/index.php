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

//Data variables
$smart_temp_data = $doc_obj->get_smart_tags('0','rec');
$smart_tag_data = array();
foreach($smart_temp_data as $key => $val){
	$tmp_arr = array();
	$tmp_arr = $doc_obj->get_smart_tags($val['id'],'rec');
	$smart_tag_data[$val['tagname']] = $tmp_arr;
}

$sel_smart_tag_id = '';
if(isset($_REQUEST['smart_tag_id']) && empty($_REQUEST['smart_tag_id']) == false){
	$sel_smart_tag_id = $_REQUEST['smart_tag_id'];
	$js_php_arr['sel_smart_tag_id'] = $sel_smart_tag_id;	
}
$js_php_arr['smart_tag_dt'] =  $smart_tag_data;
?>
	<input type="hidden" name="edit_id" id="edit_id" value="">
	<input type="hidden" name="preObjBack" id="preObjBack"/>
	<input type="hidden" name="saveBtn" id="saveBtn">
	<div class="head">
		<div class="row">
			<div class="col-sm-4">
				<div class="row">
					<div class="col-sm-3">
						<span>Smart Tags</span>	
					</div>
					<div class="col-sm-9 content_box">
						<div class="input-group">
							<input  id="tagname" name="tagname" type="text" class="form-control">
							<div class="input-group-btn">
								<button id="tag_ac_btn" type="button" class="btn btn-success" onclick="top.fmain.save_sub_tags('main');">
									Add
								</button>
							</div>
						</div>		
					</div>	
				</div>	
			</div>
		</div>
	</div>
	<div class="tblBg">
		<div class="row">
			<!-- Main tag -->
			<div id="main_tag" class="col-sm-12 lft_pnl">
				<div class="row">
					<table class="table table-bordered">
					<?php 
						$smart_tag_str = '<tr>';
						$counter = 1;
						foreach($smart_temp_data as $obj){
							$smart_tag_str .= '
								<td>
									<span class="pointer" onclick="get_sub_tag(this);" data-id="'.$obj['id'].'" data-tag="'.$obj['tagname'].'">'.$obj['tagname'].'</span>
								</td>
								<td class="text-center" style="width:2%">
									<span class="glyphicon glyphicon-remove pointer" onclick="delete_tag(this,true);" data-id="'.$obj['id'].'" data-cat="'.$obj['under'].'"></span>
								</td>	
								<td style="width:1%"></td>
							';
							if($counter % 4 == 0){
								$smart_tag_str .= '</tr><tr>';
							}
							$counter++;
						}
						echo $smart_tag_str;	
					?>
					</table>
				</div>
			</div>
		</div>
	</div>	
	
	
	<!-- Sub tag -->	
	<div id="sub_tag">
		<div class="head">
			<div class="row">
				<div class="col-sm-9">
					<span>Sub tags</span>
					<input type="hidden" name="main_cat" id="main_cat_sub">
				</div>
				<div class="col-sm-3 text-right add_row hide">
					<span class="glyphicon glyphicon-plus" onclick="add_sub_field()"></span>	
				</div>	
			</div>
			
		</div>
		<div class="tblBg">
			<div class="row">
				<div id="sub_tag_content"></div>
			</div>
		</div>	
	</div>
	