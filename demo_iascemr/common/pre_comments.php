<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_comments = "select * from preopcomments order by `comments`";
$rsNotes = imw_query($qry_comments) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLComments(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('pre_operative_comment_id');
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += '\n'+val;
			}
}
</script>


<div id="evaluationPreCommentsDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreCommentsDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; margin:0px;;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
	<span onClick="document.getElementById('evaluationPreCommentsDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
</div>
<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 

	<?php
	$rows = 5;
	$sequence=0;
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$sequence++;
		?>
		<!--tr style="cursor:pointer; height:25px;" id="comment_tr<?php echo $sequence;//$seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','comment_tr')">
			<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="return getInnerHTMLComments(this)">
				<?php echo $row_rsNotes['comments'].'';// $getRecordSetRows['name']; ?>
			</td>
		</tr-->
		
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="comment_tr<?php echo $sequence;?>">
            	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLComments(this)"><?php echo $row_rsNotes['comments'].'';?></div>
        </div>
		<?php
	}
?>
</div>
</div>