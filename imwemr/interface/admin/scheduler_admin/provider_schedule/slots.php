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
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
$disable_templates_slot="";
if($_REQUEST['sbmit_btn']){
	if($_REQUEST['chkbox_slot']){
		$arr_disable_templates_slot=$_REQUEST['chkbox_slot'];
		$available_slot=array();
		foreach($arr_disable_templates_slot as $time_val){
			list($start_time,$end_time)=explode("-",$time_val);
			if($start_time && $end_time){
				$slot = array();
				$slot["start"]=trim($start_time);
				$slot["end"]=trim($end_time);
				array_push($available_slot, $slot);
			}
		}
		$enable_templates_slot=urlencode(htmlentities(serialize($available_slot)));
	}
	if($_REQUEST['frm_pro_sch_id']){
		$qry_update="UPDATE provider_schedule_tmp set iportal_enable_slot='".$enable_templates_slot."' WHERE id='".$_REQUEST['frm_pro_sch_id']."'";
		$res_update=imw_query($qry_update);
		echo "<script>window.close();</script>";
	}else{
		echo "<script>top.opener.document.getElementById('enable_templates_slot').value='".$enable_templates_slot."';window.close();</script>";		
	}
}

$start_time=($_REQUEST['start_time'])?$_REQUEST['start_time']:$_REQUEST['frm_start_time'];
$end_time=($_REQUEST['end_time'])?$_REQUEST['end_time']:$_REQUEST['frm_end_time'];
$slot=constant("DEFAULT_TIME_SLOT");
$pro_sch_id=($_REQUEST['pro_sch_id'])?$_REQUEST['pro_sch_id']:$_REQUEST['frm_pro_sch_id'];
$available_slot = get_time_loop($start_time,$end_time,$slot);
$new_time_arr=array();
if($pro_sch_id){
	
	$qry_pro_sch="select iportal_enable_slot FROM provider_schedule_tmp WHERE id='".$pro_sch_id."'";
	$res_pro_sch=imw_query($qry_pro_sch);
	while($row_pro_sch=imw_fetch_assoc($res_pro_sch)){
			$arr_iportal_enable_slot=unserialize(html_entity_decode(urldecode($row_pro_sch["iportal_enable_slot"])));
			foreach($arr_iportal_enable_slot as $arr_time_val){
				$start_end_time=$arr_time_val["start"]."-".$arr_time_val["end"];
				$new_time_arr[$start_end_time]=$start_end_time;
			}
	}
}

?>
	<div class="mainwhtbox">
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="phrase">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
       <form name="frm1" method="post">
        <table class="table table-condensed">
            <thead>
                <tr class="grythead">
                    <td style="width:3%;" class="text-center">
                    	<div class="checkbox">
							<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
							<label for="chk_sel_all"></label>
						</div>
					</td>
                    <td style="width:95%;" onClick="LoadResultSet('','','','phrase',this);">Schedule Template Slots<span></span></td>
                 </tr>
            </thead>
        </table><?php $height_min=580;if($_SESSION['wn_height']>1000){$height_min=710;} ?>
        <div style="height:<?php echo ($_SESSION['wn_height']-$height_min);?>px; overflow-x:hidden; overflow:auto;">
            <table class="table table-condensed table-bordered resultset">
                <tbody id="result_set">
                <?php $t=0;
					foreach($available_slot as $slots_val){$t++; $row_class='';if($t%2==0){$row_class='alt';}
						$all_time=$slots_val["start"]."-".$slots_val["end"];$checked="";
						if($new_time_arr[$all_time]){$checked="checked";}
					?>
					<tr class="<?php echo $row_class;?>">
                    	<td style="width:3%;" ><div class="checkbox"><input <?php echo $checked; ?> type="checkbox" class="chk_sel" id="chkbox_slot_<?php echo $t; ?>" value="<?php echo $all_time;?>" name="chkbox_slot[]" /><label for="chkbox_slot_<?php echo $t; ?>"></label></div></td>
                        <td style="width:95%; "><label style="cursor:pointer;" for="chkbox_slot_<?php echo $t; ?>"><?php echo date("h:i A",$slots_val["start"])." - ".date("h:i A",$slots_val["end"]); ?></label></td>
                    </tr>		
				<?php }	?>
                </tbody>
            </table>
        </div>
        <div class="text-center">
        	<input type="hidden" name="frm_pro_sch_id" value="<?php echo $pro_sch_id; ?>" />
            <input type="hidden" name="frm_start_time" value="<?php echo $start_time; ?>" />
            <input type="hidden" name="frm_end_time" value="<?php echo $end_time; ?>" />
      	   	<input type="submit" class="btn btn-success" name="sbmit_btn" value="&#10004; Done">
        	<input type="button" class="btn btn-danger" value="Close" onClick="window.close();">
        </div>
        </form>
	</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#chk_sel_all').click(function()
	{
		var status = this.checked; // "select all" checked status
		$('.chk_sel').each(function(){ //iterate all listed checkbox items
			this.checked = status; //change ".checkbox" checked status
		});
	});
});
</script>
<?php require_once('../../admin_footer.php'); ?>