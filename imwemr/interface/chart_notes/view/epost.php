<div id="conEpost">
<?php

if(count($arr_epost)>0){
$i=1;
foreach($arr_epost as $k => $ar){
	extract($ar);
?>

<div class="epost panel panel-success" style="left:<?php echo $left; ?>; top:<?php echo $top; ?>; display:block; " 
		id="evaluationEPostDiv<?php echo $i;?>" 
		onMouseUp="saveEpost(<?php echo $i; ?>,'<?php echo $examName;?>');"
		attrDis="<?php echo $attrDis;?>"
		data-epost="{'dbId':'<?php echo $epostId;?>'}"
		>
			<div class="panel-heading">ePostIt : <?php echo $tmp_unm_dt;?><span class="glyphicon glyphicon-remove pull-right cur_hnd" onClick="deleteEPost('<?php echo $i;?>','<?php echo $examName;?>');"></span> </div>
			<!--<label >ePostIt</label><label ><?php echo $tmp_unm_dt;?></label>			
			<button type="button" class="close" aria-label="Close" onClick="deleteEPost('<?php echo $i;?>','<?php echo $examName;?>');">
			<span aria-hidden="true">&times;</span>
			</button>-->	
		<div class="panel-body">
		<?php
			if(count($arr_admn_epost_opt)>0){
			foreach($arr_admn_epost_opt as $k1=>$epost_opt){
		?>
		
		<input type="checkbox" id="pre_phrase<?php echo $i."_".$k1; ?>"  name="pre_phrase" value="<?php echo $epost_opt; ?>" onClick="saveEpost(<?php echo $i; ?>,'<?php echo $examName;?>');"  <?php  if( strpos($strPrePhrase, $epost_opt."*|*") !== false ){ echo "checked"; }  ?> class="frcb" ><label for="pre_phrase<?php echo $i."_".$k1; ?>" class="frcb"><?php echo $epost_opt; ?></label><br/>
		<?php }} ?>			
	
		<textarea name="eposting" id="eposting<?php echo $i; ?>" 
				onBlur="saveEpost(<?php echo $i; ?>,'<?php echo $examName;?>');" onkeyup="epost_setTaPlanHgt(this);" class="form-control" ><?php echo $epost_data; ?></textarea>
		</div>
		<div class="panel-footer"><?php echo $strModDt; ?></div>
</div>

<?php
$i+=1;
}
}
?>
<input type="hidden" id="epost_insertID" name="epost_insertID" value="<?php echo $i;?>">
</div>
<script>
var arrEpost=<?php echo $json_arr_admn_epost_opt;?>;
var epost_examNm = "<?php echo $examName; ?>";
var epost_crDt = "<?php echo wv_formatDate(date('Y-m-d'));?>";
<?php if($i>1){ ?>
$(document).ready(function () {
	$(".epost").draggable();
	$(".epost textarea").each(function(){ epost_setTaPlanHgt(this); });
});
<?php } ?>
</script>