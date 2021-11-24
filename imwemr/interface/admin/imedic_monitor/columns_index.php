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


require_once(dirname(__FILE__).'/../../../config/globals.php');

$msg='';
if(isset($_POST['action']) && $_POST['action']==='save'){
    $chk_values=(isset($_POST['chk_values']) && $_POST['chk_values']!='')? explode(',',$_POST['chk_values']):array();
	if(empty($chk_values)==false) {
        imw_query('UPDATE imonitor_extended_cols SET show_status="0" ');
        foreach($chk_values as $cid){
            $columns_chkbx1=(isset($_POST['columns_chkbx'.$cid]) && $_POST['columns_chkbx'.$cid]!='')?$_POST['columns_chkbx'.$cid]:'';
            $where = ' id='.$cid;
            if($columns_chkbx1) { 
                $show_status = ' show_status="1" ';
            } else {
                $show_status = ' show_status="0" ';
            }
            $sql = 'UPDATE imonitor_extended_cols SET show_status="1" WHERE id='.$cid;
            imw_query($sql);
        }
        $msg = 'Record saved successfully.';
    }
}

$columnsArr = array();
$allChecked = "checked";
$sql = 'SELECT * FROM `imonitor_extended_cols` order by id ASC';
$resp = imw_query($sql);
if($resp && imw_num_rows($resp)>0){
    while($row = imw_fetch_assoc($resp)) {
        if($row['show_status']!='1'){$allChecked="";}
        $columnsArr[] = $row;
    }
}

require_once('../admin_header.php');
?>

<body>
<div class="whtbox">
	<div class="section" style="height:<?php print $_SESSION['wn_height']-336?>px;">
		<form method="POST" action="" name="immcolumns" id="immcolumns" >
            <input type="hidden" name="action" id="action" value="">
			<table class="table table-bordered adminnw tbl_fixed">
                <thead>
                    <th width="5%" class="text-center">
                        <div class="checkbox">
                            <input type="checkbox" onClick="switch_allVals(this)" name="select_all" id="select_all" <?php echo $allChecked; ?> />
                            <label for="select_all"></label>
                        </div>
                    </th>
                    <th width="25%">iMedic Monitor Columns</th>
                    <th width="70%">Column Description</th>
                </thead>
				<tbody id="columnsBody">
                    <?php if(empty($columnsArr)==false) {?>
                        <?php foreach($columnsArr as $column) {
                                $checked=($column['show_status']=='1')?' checked ':'';
                                $cid=$column['id'];
                            ?>
                            <tr>
                                <td width="5%" class="text-center">
                                    <div class="checkbox">
                                        <input type="checkbox" class="columns_chkbx" <?php echo $checked;?> onClick="switch_val()" value="<?php echo $cid;?>" name="columns_chkbx<?php echo $cid;?>" id="columns_chkbx<?php echo $cid;?>" />
                                        <label for="columns_chkbx<?php echo $cid;?>"></label>
                                    </div>
                                </td>
                                <td width="25%"><?php echo $column['column_name'];?></td>
                                <td width="70%"><?php echo $column['column_desc'];?></td>
                            </tr>
                        <?php } ?>
                            <input type="hidden" name="chk_values" id="chk_values" value="">
                    <?php } ?>
				</tbody>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript">
	var ar = [["save","Save","top.fmain.saveImmColumns();"]];
	top.btn_show("ADMN",ar);
	set_header_title('Manage Columns');
	show_loading_image('none');
		
	$(document).ready(function(){
		/*Hide Loader Image*/
		parent.show_loading_image('none');
	});
	
    function switch_allVals(obj){
        if($(obj).is(':checked')) {
            $(".columns_chkbx").prop("checked",true);
        }else{
            $(".columns_chkbx").prop("checked",false);
        }
    }
    function switch_val(){
        $("#select_all").prop("checked", true);
        $(".columns_chkbx").each(function(){
            if($(this).is(":checked")==false) {
                $("#select_all").prop("checked", false);
            }
        });
    }
	function saveImmColumns(){
		parent.show_loading_image('');
		$('#action').val('save');
        var chk_values = '';
		var chk_flag = 0;
        $('#columnsBody input[type="checkbox"]').each(function() {
            if($(this).is(":checked")==true) {
                if (chk_flag == 1) {
					chk_values += ',';
				}
                chk_values += $(this).val();
				chk_flag = 1;
            }
		});
        if (chk_values == '') {
			top.fAlert('Please select at least a record to proceed.');
			return false;
		}
        $('#chk_values').val(chk_values);

		$('#immcolumns').submit();
	}
</script>
<?php 
    if(trim($msg)) { echo '<script type="text/javascript">top.fAlert("'.$msg.'");</script>'; }
    
	require_once('../admin_footer.php');
?>