<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('common/conDb.php');
//imw_close($link); //CLOSE SURGERYCENTER CONNECTION
//include('connect_imwemr.php'); // imwemr connection
$preDefineModQry	= "SELECT modifierId,modifierCode,practiceCode,description FROM modifiers WHERE deleted = '0' ORDER BY modifierCode ASC";
$preDefineModSql	= imw_query($preDefineModQry) or die(imw_error());
$preDefineModCnt 	= imw_num_rows($preDefineModSql);
?>
<script>
function getInnerHTMLMods(obj)
{
	var  valpro	= obj;
	var objFor	= document.getElementById('hiddPopUpField').value;
	var obj2		=	document.getElementById(objFor);
	
	if(obj2)
	{
		obj2.value	=	valpro ;	
	}
}
</script>
<!--
onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('preDefineModifiers');"
-->
<div id="preDefineModifiers"  style="" class="col-md-4 col-lg-3 col-xs-4 col-sm-4 preDefinePopUp padding_0">
	
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Modifiers<span onClick="document.getElementById('preDefineModifiers').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 
    	<?php
			
			while ($preDefineModRow = imw_fetch_assoc($preDefineModSql))
			{
				$modifier		=	$preDefineModRow['practiceCode'] . ' - '. stripslashes($preDefineModRow['description']);
		?>
        		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC; font-weight:bold; " onclick="getInnerHTMLMods('<?=$preDefineModRow['practiceCode']?>')"><?=$modifier?></div>
        <?php
			}
		?>
	</div>
</div>
<?php
//imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
//include("common/conDb.php");  //SURGERYCENTER CONNECTION
?>