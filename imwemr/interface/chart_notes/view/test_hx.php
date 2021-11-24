<div class="whtbox assignmen testhx" id="<?php echo $elmId;?>"  
	onmouseover="showdivArc('<?php echo $nm;?>',1,1);"
	onmouseout="showdivArc('<?php echo $nm;?>',0,1);"
>

<figure><span class="glyphicon glyphicon-remove-sign" onClick="$('#<?php echo $elmId;?>').hide()" ></span></figure>
<div class="asshead"><h2>Test History </h2></div>
<div class="clearfix"></div>
<div class="table-responsive">


<?php
if(is_array($arrHx) && count($arrHx)>0){
?>
<table class="table table-bordered">
<?php
foreach($arrHx as $testName => $arrSubTest){
	foreach($arrSubTest as $arrOdOs){
		array_reverse($arrOdOs);
		foreach($arrOdOs as $key=>$arrSub){
			if($arrSub['preVal'] != $arrSub['val']){
?>
		<tr>
		<td class="tsthis"><?php echo $testName;?></td>
		<td class="tsthis"><?php echo $key;?></td>	
		<td class="testhx_prev hxv" title="Previous value"><?php echo $arrSub['preVal'];?></td>
		<td class="hxv" title="New value"><?php echo $arrSub['time']."  ".$arrSub['usrnm']."<br>".nl2br($arrSub['val']);?></td>
		</tr>
<?php
			}
		}
	}
}
?>
</table>
<?php
}
?>

</table>


</div>
<div class="clearfix"></div>


</div>