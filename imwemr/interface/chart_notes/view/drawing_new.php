<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
File: drawing.php
Purpose: This file provides basic html structure for drawing function.
Access Type : Include file
*/
?>
<?php  
	if(!empty($imgDB)){ //for DB background
		echo "<style>".$imgDB."</style>";		
	}
 ?>
<div class="row" >
<div class="idocdrwtopbar" >
	<div id="toolTop" class="flltToolTop"  >
	    <span class="glyphicon toolImg toolImgNoImage" title="No Image" ></span> 
	    <span class="glyphicon toolImg toolImgFace" title="Face" ></span> 
	    <span class="glyphicon toolImg toolImgOptical" title="Optical" ></span> 
	    <span class="glyphicon toolImg toolImgLa" title="La" ></span> 
	    <span class="glyphicon toolImg toolImgOphtha" title="Ophtha" ></span> 
	    <span class="glyphicon toolImg toolImgPicCon" title="Pic-Con" ></span> 
	    <span class="glyphicon toolImg toolImgGonio" title="Gonio" ></span>
	    <span class="glyphicon toolImg toolImgEOM" title="EOM" ></span>
	    <span class="glyphicon toolImg toolImgCornea" title="Cornea R L" ></span>
	</div>
	<div class="fllt toolXtra nextButton" title="Show/hide More Templates" ></div>

	<span>
	    <input type="button" class="dff_button btn btn-info btn-xs" id="btChooseTextImage" name="btChooseTextImage" value="Test Image" >
	</span>

	<span class="glychicon toolIcon16 scanIcon16" title="Scan Drawing Image" ></span>
	<span class="glychicon toolIcon16 uploadIcon16" title="Upload Drawing Image" ></span>
	<span class="glychicon toolIcon16 webcamIcon16" title="Upload Drawing Image - Camera" ></span>
	<span id="plusDrw"  class="glychicon toolIcon16 drwDel" title="Add More Drawing"  ></span>	
		
	<input type="checkbox" name="elem_drwNE<?php echo $intTempDrawCount; ?>" id="elem_drwNEOD<?php echo $intTempDrawCount; ?>" value="OD"   > 
	<label for="elem_drwNEOD<?php echo $intTempDrawCount; ?>">NE <span class="od">OD</span></label>		

	<input type="checkbox" name="elem_drwNE<?php echo $intTempDrawCount; ?>" id="elem_drwNEOS<?php echo $intTempDrawCount; ?>" value="OS"   > 
	<label for="elem_drwNEOS<?php echo $intTempDrawCount; ?>">NE <span class="os">OS</span></label>
	
	<span id="DelDrw" title="Delete Drawing" class="glyphicon toolIcon16 drwAdd" ></span>
</div>
</div>

<div class="row" >
	
	<!-- Left Tools -->
	<div id="divTools" class="col-sm-1">
		<div> 
		<span class="toolIcon toolPencil" title="Pencil" ></span>			
		    <span class="toolIcon toolSelect" title="Select" ></span>                   
		</div>
		<div> 
		   <span class="toolIcon toolBrush" title="Brush" ></span>
		<span class="toolIcon toolSparyColor" title="Spray Color" ></span>  
		<span class="toolIcon toolReleaseEvent hidden" title="Clear Selection" ></span>
		</div>                
		<div> 
		    <span class="toolIcon toolArrow" title="Draw Arrow" ></span>
		    <span class="toolIcon toolText" title="Write Your Text" ></span> 
		</div>
		
		<div>	
		<span class="toolIcon toolRemSelected" title="Remove Selected" ></span>    
		<span class="toolIcon toolClear" title="Clear Drawing" ></span> 
		</div>
		
	      <div> 
		    <span class="toolIcon toolLine" title="Draw Line"  ></span> 
		    <span class="toolIcon toolArc" title="Draw Arc" ></span> 
		</div>
		<div> 
		    <span class="toolIcon toolRect" title="Draw Rectangle" ></span> 
		    <span class="toolIcon toolRoundRect" title="Draw Rounded Rectangle" ></span> 
		</div>
		<div> 
		    <span class="toolIcon toolEllipse" title="Draw Ellipse"  ></span>
		    <span class="toolIcon toolCircle" title="Draw Circle" ></span> 
		</div>
		<div> 
		    <span class="toolIcon toolFilledRect" title="Draw Filled Rectangle" ></span> 
		    <span class="toolIcon toolFilledRoundRect" title="Draw Filled Rounded Rectangle" ></span> 
		</div>
		<div>
		    <span class="toolIcon toolFilledEllipse" title="Draw Filled Ellipse" ></span> 
		    <span class="toolIcon toolFilledCircle" title="Draw Filled Circle" ></span> 
		</div>
	       <div>
		</div>
	
	  <?php //Colors--  ?>
	<div title="colors" class="idoc-colors">
	
		<div>     
		     <span class="colorSpanBorder" style="background-color:#171717;" >&nbsp;&nbsp;</span> 
			<span class="colorSpanBorder" style="background-color:#808080;" >&nbsp;&nbsp;</span> 
		</div>	
		<div>	
			<span class="colorSpanBorder" style="background-color:#990099;" >&nbsp;&nbsp;</span> 
			<span class="colorSpanBorder" style="background-color:#FFC800;" >&nbsp;&nbsp;</span> 
		</div>	
		<div>	
			<span class="colorSpanBorder" style="background-color:#FFFFFF;" >&nbsp;&nbsp;</span> 
		<span class="colorSpanBorder" style="background-color:#C0C0C0;" >&nbsp;&nbsp;</span> 
		</div>	
		<div>
			<span class="colorSpanBorder" style="background-color:#999900;" >&nbsp;&nbsp;</span> 
			<span class="colorSpanBorder" style="background-color:#FF00FF;" >&nbsp;&nbsp;</span> 
		</div>	
		<div>	
			<span class="colorSpanBorder" style="background-color:#FF0000;" >&nbsp;&nbsp;</span> 
			<span class="colorSpanBorder" style="background-color:#00FF00;" >&nbsp;&nbsp;</span>
		</div>	
		<div>			
		<span class="colorSpanBorder" style="background-color:#FFFF00;" >&nbsp;&nbsp;</span> 
			<span class="colorSpanBorder" style="background-color:#0000FF;" >&nbsp;&nbsp;</span> 
		</div>	
		<div>		
			<span class="colorSpanBorder" style="background-color:#660000;" >&nbsp;&nbsp;</span> 
			<span class="colorSpanBorder" style="background-color:#666600;" >&nbsp;&nbsp;</span> 
		</div>                       
		<div >
		     <input type='color' id="drawing-color" value="#000000" class="drwctrl" />
		</div>  
		
	</div>
	<?php //Colors--  ?>
	</div>
	<!-- Left Tools -->
	<div class="col-sm-10 idoc_drw_canvas">
	<div id="divCanvas<?php echo $intTempDrawCount; ?>" >		
		<canvas id="cCanvas<?php echo $intTempDrawCount; ?>" class="<?php echo ($blDrwaingGray == true) ? "canvasPrevBorder" : "cCanvas" ?>" height="465" width="730">This Application Will Work In Safari or IE9</canvas>
	</div>
	</div>
	<div class="col-sm-1" id="divToolsRight">		
		<?php
			
			echo "".$idoc_htmlDrwIco."";			
			
		?>		
	</div>
</div>

<div class="row drw-tools-bottom">
	<div id="divlinewidth" class="col-sm-3" >
		<label for="drawing-line-width" >Line Width:</label>
		<input type="text" id="drawing-line-width" readonly size="1" class="drwctrl">
		<div id="slider-range-min" ></div>
	</div>
	<div id="divSendBackForth" class="col-sm-1" >
		<input type="button" id="obj-send-b" value="Send to Back" class="btn btn-warning btn-xs">
		<input type="button" id="obj-send-f" value="Bring to Front" class="btn btn-warning btn-xs">
	</div>
	<div id="textOptions_font" class="col-sm-2" >
		<label>Font:</label>
		<select id="font-family" class="drwctrl btn-object-action" title="Change Font" >
			<option value="arial">Arial</option>
			<option value="helvetica" selected>Helvetica</option>
			<option value="myriad pro">Myriad Pro</option>
			<option value="delicious">Delicious</option>
			<option value="verdana">Verdana</option>
			<option value="georgia">Georgia</option>
			<option value="courier">Courier</option>
			<option value="comic sans ms">Comic Sans MS</option>
			<option value="impact">Impact</option>
			<option value="monaco">Monaco</option>
			<option value="optima">Optima</option>
			<option value="hoefler text">Hoefler Text</option>
			<option value="plaster">Plaster</option>
			<option value="engagement">Engagement</option>
		</select>
	</div>
	<div id="textOptions_font_size" class="col-sm-1">
		<label for="text-font-size" >Font size:</label>			
		<input type="text" id="text-font-size" readonly size="2" class="drwctrl" >
		<div id="slider-range-font-size"></div>
	</div>
	<div id="textOptions_font_deco" class="col-sm-2">		
		<input type="button" id="text-font-b" value="B" style="font-weight:bold" title="Bold" class="btn btn-info btn-xs">
		<input type="button" id="text-font-i" value="I" style="font-style: italic;" title="Italic" class="btn btn-info btn-xs">
		<input type="button" id="text-font-l" value="L" style="text-decoration: line-through;" title="Line through" class="btn btn-info btn-xs">
	</div>
	<div id="dvEraser" class="col-sm-1"><span class="toolIcon toolEraserMain" title="Eraser" ></span></div>
	<!-- div pop -->
	<div id="divTestImages" class="disableArea hidden"  ></div>
	<div id="divTestImagesMain" class="imgDim hidden" style="overflow:hidden;"></div>
	<div id="ajax_load_drawing">Loading Drawings! Please wait.</div>
	<!--<div id="totLoad" class="counter" ></div>-->
	<!-- div pop -->	
</div>

<?php

if(!isset($idoc_intialize_idoc_icons)){

$idoc_intialize_idoc_icons	= 1;
echo "
<script>
var arrDrawIcon_main = ".json_encode($idoc_arrDrwIcon).";
var idoc_nolabelwicon = '".$GLOBALS["no_exm_label_w_icon"]."';
</script>
";

}

?>