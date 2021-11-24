<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_evaluation = "select * from evaluation order by `name`";
$rsNotes = imw_query($qry_evaluation) or die(imw_error());

$ekgBigRowArr = array('NSR', 'AFIB', 'PACING', 'PVC�s', 'SVT', 'APC', 'Bigeminy', 'Couplet', 'SB');
$totalRows_rsNotes = 8;
?>
<script>
	
function getInnerHTMLEkgAdmin(obj){
	var  val = obj.innerHTML;
	top.frames[0].frames[0].frames[0].document.getElementById('selected_frame_name_id').value='';
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('ekgBigRowAdminId');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimeEkg; 
function closeAdminEkg(){
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].document.getElementById('ekgLocalAnesAdminDiv').style.display == "block"){
			top.frames[0].frames[0].document.getElementById('ekgLocalAnesAdminDiv').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimeEkg(){
	tOutAdminTimeEkg = setTimeout("closeAdminEkg()", 500);
}
function stopCloseEkgAdmin() {
	clearTimeout(tOutAdminTimeEkg);
}

</script>
<div id="listContent1" style="display:none;" class="">
    <ul class="list-group">
    <?php
		 $eval_seq=0;
		 foreach($ekgBigRowArr as $ekgBigRowStaticPreDefine){
			$eval_seq++;
			?> 
            <li class="list-group-item"  onClick="return getInnerHTMLEkgAdmin(document.getElementById('<?php echo 'ekg'.$eval_seq;?>'))"><a href="javascript:void(0)" id="<?php echo 'ekg'.$eval_seq;?>"> <?php echo stripslashes($ekgBigRowStaticPreDefine).''; ?></a></li>
			<?php
		}
	?>
    </ul>
</div>
<!--
<div id="ekgLocalAnesAdminDiv" onMouseOver="stopCloseEkgAdmin();" onMouseOut="closeAdminTimeEkg();"   style="position:absolute;background-color:#E0E0E0;width:250px;height:100px;display:none;overflow:auto;"> 
<table class="table_collapse" style="border:none;" onMouseOver="stopCloseCalAdmin();">
    <tr >
        <td class="alignRight" style=" background-color:#BCD2B0;"><img src="../images/left.gif" style=" width:3px; height:24px;"></td>
        <td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px;"><img src="../images/chk_off1.gif" style="cursor:pointer;" onClick="document.getElementById('ekgLocalAnesAdminDiv').style.display='none';"></td>
        <td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="../images/right.gif" style=" width:3px; height:24px; "></td>
    </tr> 	
		<?php
			 $eval_seq=0;
			 foreach($ekgBigRowArr as $ekgBigRowStaticPreDefine){
				$eval_seq++;
				?>
                <tr style="cursor:pointer; height:25px;" id="ekgBigRow_tr<?php echo $eval_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','ekgBigRow_tr');">
                    <td colspan="3" class="text_10 alignLeft" style=" width:100%;padding-left:2px; cursor:pointer;"  onClick="return getInnerHTMLEkgAdmin(this)"><?php echo stripslashes($ekgBigRowStaticPreDefine).''; ?></td>
                </tr> 
				<?php
			}
	?>
	</table>
</div>-->