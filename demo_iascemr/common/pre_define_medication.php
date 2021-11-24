<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_med = "select * from medications order by `name`";
$rsNotes = imw_query($qry_med) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<div id="evaluationPreDefineMedDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreDefineMedDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; margin:0; border:1px solid #CCC;border-radius:2px; z-index:9999 !important;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
        <span onClick="document.getElementById('evaluationPreDefineMedDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 
        <?php
        $rows = 5;
        $med_counter = 0;
        while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
            $med_counter +=1;
            ?>
            <div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="med_tr<?php echo $med_counter; ?>">
                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLFn('<?php echo stripslashes($row_rsNotes['name']);?>', '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value,'1')"><?php echo stripslashes($row_rsNotes['name']);?></div>
            </div>
        <?php
        }
    ?>
    </div>
</div>