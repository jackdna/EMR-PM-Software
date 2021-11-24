<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
$strGridPoint = "";
if((isset($_REQUEST['temp']) == true) && empty($_REQUEST['temp']) == false){
	$strGridPoint = $_REQUEST['temp'];	
}
?>
<!DOCTYPE html>
<html>
  <head>
  	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1"/>        
    <title>Surgery Center Grid</title>
    <link rel="icon" type="image/png" href="scGrid.png" />
    <link type="text/css" href="grid.css" rel="stylesheet" />
    <script src="grid.js"></script>
    </head>
    <body>
        <form action="" name="frmSCGrid" id="frmSCGrid" method="post">
        <input type="hidden" name="temp" id="temp"/>
        <input type="hidden" name="gridPoint" id="gridPoint" value="<?php echo $strGridPoint; ?>" />
            <table class="no-copy">
                <tr>
                    <td style="height:25px;">
                        <img id="imageEvent" src="blank.gif"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="divCanvas">                             
                            <canvas id="cCanvas" name="cCanvas" class="no-copy">                    	 
                            </canvas>   
                        </div>            	
                    </td>
                </tr>        
                <tr >
                    <td>
                        <table width="100%"  class="text_10" border="0">																									
                            <tr>
                                <td align="center" onClick="setEvent('funDrawDownTirangle');" ><img src="TDn.png" />
                                <td align="center" onClick="setEvent('funDrawUpTirangle');" ><img src="TUp.png" />
                                <td align="center" onClick="setEvent('funCircleWtInterColor');" ><img src="CFill.bak.png" /></td>
                                <td align="center" onClick="setEvent('funCircleWtOutInterColor');"><img src="CDr.png" /></td>
                                <td align="center" onClick="erase(); setEvent('erase');"><img src="eraser.gif" /></td>
                                <td align="center" onClick="setEvent('text');"><img src="text.png" /></td>
                                <td align="center" onClick="setEvent('reblock');"><img src="red_dot.png" style="height:15px; width:1px;" /></td>                                                
                                <td align="center" id="tdUndo" onClick="setEvent('undo'); processUndo();"><img src="undo-icon.png" /></td>                        
                                <td align="center" id="tdRedo" onClick="setEvent('redo'); processRedo();"><img src="Redo-icon.png" /></td>                         
                                <td align="center" onClick="setEvent('drag');"><img src="cursor-drag-hand16.png" /></td>                        
                                <td align="center"><input type="button" name="getCanDta" id="getCanDta" value="Save Canvas Data" onClick="saveAnesthesiaGrid();"/></td>                        
                            </tr>
                       </table>
                    </td>
                </tr>     
            </table>
        </form>
    </body>
</html>