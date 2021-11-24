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
	
	function getInnerHTMLEkg(val){
		//var  val = obj.innerHTML;
		var obj2 = document.getElementById('ekgBigRowId');
			if(obj2.value==''){
				obj2.value = val;
				}else{
				obj2.value += ', '+val;
				}
		textAreaAdjust(obj2);
	}
</script>
<div id="ekgLocalAnesDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('ekgLocalAnesDiv');"   style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">EKG<span onClick="document.getElementById('ekgLocalAnesDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
	<?php
		 $eval_seq=0;
		 foreach($ekgBigRowArr as $ekgBigRowStaticPreDefine){
			$eval_seq++;
			?>
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLEkg('<?php echo stripslashes($ekgBigRowStaticPreDefine); ?>')"> 
	                <?php echo stripslashes($ekgBigRowStaticPreDefine); ?>
                </div>
				<?php
		}
	?>
	</div>
</div>