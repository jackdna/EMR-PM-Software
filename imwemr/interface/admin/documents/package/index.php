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
$package_data = $doc_obj->get_package_data($order_asc_desc);
$list_data = $package_data['data'];

$arrow = 'glyphicon glyphicon-chevron-down';
if($order_asc_desc == 'ASC'){
	$arrow = 'glyphicon glyphicon-chevron-up';
}

?>
	<input type="hidden" name="edit_id" id="edit_id" value="">
	<input type="hidden" name="preObjBack" id="preObjBack"/>
	<input type="hidden" name="saveBtn" id="saveBtn">
	<table class="table table-bordered adminnw">
		<thead>
			<tr>
				<th width="1%">
					<div class="checkbox">
						<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="" autocomplete="off">
						<label for="chk_sel_all">
						</label>
					</div>
				</th>
				<th class="pointer" onclick="set_order();">
					Packages
					<?php 
						if($sort_first_time != 'yes'){
							echo '<span class="pull-right '.$arrow.'"></span>';
						}
					?>
				</th>
				<th>Form Names</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$js_package_arr = array();		
				$list_rows = '';
				foreach($list_data as $obj){
					$form_name_str = '<span>';
					foreach($obj['consent_data'] as $consent_obj){
						$form_name_str .= $consent_obj['consent_form_name'].'<br />';
					}
					$form_name_str .= '</span>';
					
					$list_rows .= '
					<tr>
						<td width="1%">
							<div class="checkbox"><input type="checkbox" name="id" class="chk_sel" id="chk_sel_'.$obj['package_category_id'].'" value="'.$obj['package_category_id'].'"><label for="chk_sel_'.$obj['package_category_id'].'"></label></div>
						</td>
						<td onclick="get_package_modal('.$obj['package_category_id'].')">'.$obj['package_category_name'].'</td>	
						<td onclick="get_package_modal('.$obj['package_category_id'].')">'.$form_name_str.'</td>	
					</tr>';
					$js_package_arr[$obj['package_category_id']] = $obj;
				}
				// To be used in js file for filling package edit data
				$js_php_arr['package_data'] = $js_package_arr;
				
				echo $list_rows;
			?>
		</tbody>	
	</table>