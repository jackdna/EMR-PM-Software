<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_opdrops = "select * from medications order by `name`";
$rsNotes = imw_query($qry_opdrops) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLOtherPreOpOrdersAdmin(obj){
	var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].document.getElementById('otherPreOpOrdersId');
	if(!obj2){ 
		obj2 = top.frames[0].frames[0].frames[0].document.getElementById('otherPreOpOrdersId');
	}
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
} 
</script>

<div class="modal fade " id="otherPreOpOrdersAdminDiv">
     <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">Other Pre-Op Orders  </h4>  
            </div>
            <div class="modal-body" style="height:250px;overflow:auto;">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
					<?php
                    $rows = 5;
                    $drops_seq=0;
                    while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
                    $drops_seq++;
                    ?>
                        <tr style="height:25px; cursor:hand;" id="DropsAdmin_tr<?php echo $drops_seq;//$seq; ?>">
                            <td colspan="3" style="padding-left:2px; cursor:pointer" onClick="return getInnerHTMLOtherPreOpOrdersAdmin(this)"><?php echo stripslashes($row_rsNotes['name']).'';// $getRecordSetRows['name']; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
             </div>
            
         
        </div>
     </div>
    </div>
    
