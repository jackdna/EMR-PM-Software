<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$intraOpQry	=	"select * from intra_op_post_op_order where deleted = '0' order by `name`";
$intraOpSql =	imw_query($intraOpQry) or die(imw_error());
$intraOpCnt	=	imw_num_rows($intraOpSql);
?>
<script>
function getInnerHtmlIntraOpPostOpOrderAdmin(obj){
	var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].document.getElementById('intraOpPostOpOrderId');
	if(!obj2 )
	{
		obj2 = top.frames[0].frames[0].frames[0].document.getElementById('intraOpPostOpOrderId');		
	}
	if(obj2.value != ''){ val = ', ' + val; }
	obj2.value += val;
} 
</script>

<div class="modal fade " id="intraOpPostOpAdminDiv">
     <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">Intra Op Post Op Orders  </h4>  
            </div>
            <div class="modal-body">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                <?php
				$intraOpCounter	=	0;
				while ($intraOpRow = imw_fetch_assoc($intraOpSql))
				{
					$intraOpCounter++;
				?>
                	<tr style="height:25px; cursor:hand;" id="IntraOpPostOpRowAdmin_tr<?=$intraOpCounter?>">
                    	<td colspan="3" style="padding-left:2px; cursor:pointer" onClick="return getInnerHtmlIntraOpPostOpOrderAdmin(this)"><?php echo stripslashes($intraOpRow['name']).'';?></td>
                  	</tr>
              	<?php
				}
				?>
				</table>
         	</div>
		</div>
	</div>
</div>