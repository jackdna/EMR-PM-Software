<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
?>
<script>
function getInnerHTMLsuppliesUsed(suppName,qtyChkBox,catName,supplyId){
	$("#inputSupplyId").val(suppName);
	$("#inputSupplyId").attr('data-qcb',qtyChkBox);
	$("#inputSupplyId").attr('data-cat-name',catName);
	$("#inputSupplyId").attr('data-supp-id',supplyId);
	$("#addMoreSuppliesButton").trigger('click');
	$("evaluationPreDefineSuppliesUsedDiv").hide(100);
}
</script>
<style type="text/css">
.pop-grid { position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:999;}
.pop-head {height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;}
.pop-close { float:right; color:#FFF; cursor:pointer; }
.pop-body {height:160px;  overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;}
.pop-disabled { cursor:not-allowed; background-color:#EEE;}
</style>

<div id="evaluationPreDefineSuppliesUsedDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreDefineSuppliesUsedDiv');" class="col-md-4 col-lg-3 col-xs-4 col-sm-4 pop-grid">
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 pop-head">Supplies Used
        <span class="pop-close" onClick="document.getElementById('evaluationPreDefineSuppliesUsedDiv').style.display='none';">X</span>
    </div>
    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_0 pop-body" id="childDiv"> 
    <?php
    $tmpArr = array();
    foreach($moreAvailableArr as $more)
    {
      if( !array_key_exists($more->suppliesUsedId,$tmpArr[$more->cat_name]))
        $tmpArr[$more->cat_name][$more->suppliesUsedId] = array('name' => $more->name,'qtyChkBox' => $more->qtyChkBox);
    }
    
    ksort ($tmpArr);
    $tmpOther = $tmpArr['Other'];
    unset($tmpArr['Other']);
    $tmpArr['Other'] = $tmpOther;
    $cat_counter = 0;
    echo '<ul class="group-item padding_0 margin_0" id="accordion">';
    foreach($tmpArr as $suppliesUsedCatName => $suppliesUsedArr)
    {
      if( is_array($suppliesUsedArr) && count($suppliesUsedArr) > 0 )
      {
        $cat_counter++;
        $class = 'supply_cat_'.$cat_counter;
        
        echo '<li class="list-group-item padding_0 active"><a class="show padding_10" style="color:#333" data-toggle="collapse" data-parent="#accordion" href="#'.$class.'"><i class="fa fa-hand-o-right"></i>&nbsp;<b>'.stripslashes($suppliesUsedCatName).'</b></a></li>';

        echo '<ul class="list-group collapse" style="float:none; margin-bottom:0; border-radius:0;" id="'.$class.'">';
                        
        foreach( $suppliesUsedArr as $suppliesUsedId => $suppliesUsed)
        {
          $suppliesUsed_seq++;
                            $pre_supp_name = stripslashes($suppliesUsed['name']);
                            $pre_div_name = preg_replace('#[ -]+#', '-', strtolower($pre_supp_name));
                            $pre_div_name = preg_replace('/[^A-Za-z0-9-]+/', '', $pre_div_name);
                                                
      ?>
          <li class="list-group-item" onClick="return getInnerHTMLsuppliesUsed('<?php echo addslashes($suppliesUsed['name']); ?>','<?=$suppliesUsed['qtyChkBox']?>','<?=stripslashes($suppliesUsedCatName)?>','<?=stripslashes($suppliesUsedId)?>')" data-cat-name="<?=stripslashes($suppliesUsedCatName)?>" data-supp-name="<?php echo addslashes($pre_supp_name);?>" data-supp-slug="<?php echo $pre_div_name;?>" data-qcb ="<?=$suppliesUsed['qtyChkBox']?>"  data-supp-id ="<?php echo $suppliesUsedId;?>"><?php echo $pre_supp_name ; ?></li>
          
          <?php
        }
        echo '</ul>';
      }
    }
    echo '</ul>';
  ?>
    </div>
</div>
