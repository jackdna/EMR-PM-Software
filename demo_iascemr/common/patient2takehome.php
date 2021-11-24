<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_diagnosis = "select * from patient2takehome order by `name`";
$rsNotes = imw_query($qry_diagnosis) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLhome(obj){
	var  val = obj;
	var OrdObj = top.frames[0].frames[0].document.getElementsByName('physicianOrderName[]');
	var TspObj = top.frames[0].frames[0].document.getElementsByName('physicianOrderTime[]');
	
	for(var i = 0; i < OrdObj.length ; i++)
	{
		var Ord	=	OrdObj[i];
		var Tsp	=	TspObj[i];
		
		if(Ord.value  === '' )
		{	
			Ord.value	= val ;
			Tsp.focus();
			break; 
		}
	}
		
}
function getAjaxTime(cn,obj)
	{	
		$.ajax({
				url : 'getAjaxTime.php',
				type:'POST',
				dataType:"json",
				data:{ 'v' : encodeURI(cn) },
				success:function(data)
				{
					obj.val(data);
				}
			});
		
	}
</script>
<div id="evaluationTakeHomeDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationTakeHomeDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; margin:0px;;border:1px solid #CCC;border-radius:2px;z-index:9999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">  

<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px"><span onClick="document.getElementById('evaluationTakeHomeDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer;">X</span></div>
	
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;">	
    	<?php
	$rows = 5; 
		$take_counter=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$take_counter++;;
			?>
            <!--<tr style="cursor:pointer; height:25px;" id="take_tr<?php echo $take_counter;//$seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','take_tr')">
				<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10 alignLeft"  onClick="return getInnerHTMLhome(this)"><?php echo stripslashes($row_rsNotes['name']);// $getRecordSetRows['name']; ?></td>
			</tr>-->
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLhome('<?php echo stripslashes($row_rsNotes['name']); ?>')">
			<?php echo stripslashes($row_rsNotes['name']); ?>
        </div>
			<?php
		}
?>
</div>
</div>