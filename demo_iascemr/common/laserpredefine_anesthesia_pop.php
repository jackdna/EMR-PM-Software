<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
require_once('conDb.php'); 
$qry_anesthesia = "select * from laserpredefine_anesthesia_tbl  order by `name`";
$res_anesthesia = imw_query($qry_anesthesia) or die(imw_error());
$totalRows_anesthesia = imw_num_rows($res_anesthesia);
?>
<script>
function getInnerHTMLanesthesia(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('txtarea_anesthesia');
	
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
}
</script>
<div id="evaluationanesthesia" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationanesthesia');"  style="position:absolute;background-color:#E0E0E0;width:250px;height:150px;display:none;overflow:auto;"> 
<table class="table_collapse" style="border:none;">
	<tr>
		<td class="alignRight" style=" background-color:#BCD2B0;"><img src="images/left.gif" style=" width:3px; height:24px;"></td>
		<td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px; text-align:right;"><img src="images/chk_off1.gif" style="cursor:pointer;" onClick="document.getElementById('evaluationanesthesia').style.display='none';"></td>
		<td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="images/right.gif" style=" width:3px; height:24px; "></td>
	</tr>
	<?php
	$rows = 5;
		$anesthesiaer_anesthesia=0;
		 while ($res_anesthesia_row = imw_fetch_assoc($res_anesthesia)){
			$anesthesiaer_anesthesia++;
			?>
			<tr style="cursor:pointer; height:25px;" id="anesthesia_tr<?php echo $anesthesiaer_anesthesia; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_anesthesia;?>','anesthesia_tr')">
				<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" id="td<?php echo $res_anesthesia_row['anesthesiaID'];//$seq; ?>" onClick="return getInnerHTMLanesthesia(this)"><?php echo stripslashes($res_anesthesia_row['name']).''; ?></td>
			</tr>
			<?php
		}
?>
</table>
</div>