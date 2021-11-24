<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	$posSt = strpos($prdtVrsnDt,' ',(strpos($prdtVrsnDt,' ')+1));
	$version = substr($prdtVrsnDt,strpos($prdtVrsnDt,' '),$posSt-1);
	$versionDate = substr($prdtVrsnDt,$posSt);

?>
<div class="modal fade" id="releaseNoteModal" data-backdrop="false" style="max-width:900px; top:13%">
    <div class="modal-dialog modal-lg" style="max-width:875px;">
        <div class="modal-content">
                
            <div class="modal-header text-center" >
            
                <div id="ModalTitle" style="position:absolute; text-align:center; width:100%; color:white"></div>
                	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    	<span aria-hidden="true">&times;</span>
                  	</button>
                    <h4 class="modal-title rob" style="text-align:left; color:white;">
                        <span class="" style="width:305px; color:white;">
                            Version Release Document<br>
                            Version: <?=$version?><small style="color:white;">Date: <?=$versionDate?></small>
                        </span>
                    </h4>  
            </div>
            
            <div class="clearfix margin_adjustment_only"></div>
            
            <div class="modal-body" style="postion:relative; min-height:400px; max-height:400px; overflow:hidden; overflow-x:auto;">
               &nbsp;<h2> Release Note : <?=$version?></h2>
            </div>
            
            <div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
                <a class="btn btn-danger"  id="releaseCloseBtn" data-dismiss="modal">Close</a>
            </div>
            
        </div><!-- End Modal Content -->
    

    </div> <!-- end Modal Dialogue -->

</div><!-- End Modal -->