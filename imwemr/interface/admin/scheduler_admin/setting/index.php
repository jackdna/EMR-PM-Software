<?php 
	require_once("../../admin_header.php");
	require_once('../../../../library/classes/admin/scheduler_admin_func.php');
if($_POST['update']=='save')
{
	$listings=$_POST['list_order'];
	$sql="";
	for($key=0;$key<=18;$key++)
	{
		$val=$listings[$key];
		$id=$key+1;
		$sql="update schedule_icon_list_order set icon_order=$val where id=$id";
		imw_query($sql);
	}
	//imw_query($sql)or die(imw_error());
	header("location:index.php?save=succ");
	exit;
}
?>
       <style>
		   .disabled{color: #939393}
</style>
        <script type="text/javascript">
		
			var arrAllShownRecords = new Array();
			var totalRecords	   = 0;
			
			function save(){
				document.getElementById('update_icon_order').submit();
			}
			
			
		</script>
	<div class="whtbox">
       <form name="update_icon_order" id="update_icon_order" method="post" action="">
    	<input type="hidden" name="update" id="update" value="save">
        <table class="table table-bordered adminnw tbl_fixed">
            <thead>
                <tr>
                    <th class="col-sm-3">Schedule Icon</th>
					<th class="col-sm-1">List On</th>
					<th class="col-sm-3">Schedule Icon</th>
					<th class="col-sm-1">List On</th>
					<th class="col-sm-3">Schedule Icon</th>
					<th class="col-sm-1">List On</th>
                </tr>
            </thead>
			<tbody>
				<tr>
			<?php
				$q=imw_query("select * from schedule_icon_list_order order by id asc");
				while($d=imw_fetch_object($q))
				{
					$td++;
					echo"<td>$d->icon_name</td>
					<td><select name=\"list_order[]\" id=\"$d->id\" class=\"form-control selectpicker\">";
					for($i=1;$i<=19;$i++)
					{
						$selected=($i==$d->icon_order)?'selected':'';
						echo"<option value=\"$i\" $selected>$i</option>";
					}
					echo"</select></td>";
					if($td==3)
					{
						$td=0;
						echo'</tr><tr>';
					}
				}
			?>
					
				</tr>
			</tbody>
        </table>
        </form>
	</div>
    <script type="text/javascript">
		
		var ar = [["save","Save","top.fmain.save();"]];
		top.btn_show("ADMN",ar);
			$(document).ready(function(){
				set_header_title('Customizable Icon Tray');
			});	
		
	</script>
<?php 
	include('../../admin_footer.php');
?>