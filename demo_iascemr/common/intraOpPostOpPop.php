<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$intraOpQry	=	"select * from intra_op_post_op_order order by `name`";
$intraOpSql =	imw_query($intraOpQry) or die(imw_error());
$intraOpCnt	=	imw_num_rows($intraOpSql);
?>
<script>
function getInnerHtmlIntraOpPostOpOrder(obj){
	var  val = obj;
	var obj2 = document.getElementById('intraOpPostOpOrderTxtId');
	if(obj2.value != ''){ val = ', ' + val; }
	obj2.value += val;
	obj2.style.backgroundColor = '#FFFFFF';
	textAreaAdjust(obj2);
}
</script>
<div id="intraOpPostOpDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('intraOpPostOpDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4">
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Post-Op Orders<span onClick="document.getElementById('intraOpPostOpDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;">
    <?php
	$intraOpCounter	=	0;
	while ($intraOpRow = imw_fetch_assoc($intraOpSql))
	{
		$intraOpCounter++;
	?>
		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;"  onClick="return getInnerHtmlIntraOpPostOpOrder('<?php echo stripslashes($intraOpRow['name']);?>')">  
			<?php echo stripslashes($intraOpRow['name']); ?>
        </div>
	<?php
	}
	?>
 	</div>
</div>