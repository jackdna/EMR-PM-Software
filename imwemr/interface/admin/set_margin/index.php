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

require_once('../admin_header.php');

if(empty($txt_save_margin) == false){
	if(empty($txt_margin_id) == false){
		$txt_margin_id=imw_real_escape_string($txt_margin_id);
		$_POST['top_line_margin'] = json_encode($_POST['top_line_margin']);
		UpdateRecords($txt_margin_id,'margin_id',$_POST,'create_margins');
		$msg = 'Record Saved Succesfully';
		
	}
}
if($margin_type){
	$margin_type=imw_real_escape_string($margin_type);
	$qry = "select * from create_margins";
	$qry .= " where margin_type='$margin_type'";
	$marginQryRes = imw_query($qry);
	$marginQryRes = imw_fetch_assoc($marginQryRes);
}
?>
<body>
	<input type="hidden" name="preObjBack" id="preObjBack" value="">
	<form name="setMarginForm" id="setMarginForm" method="post" style="margin:0px;">
	<input type="hidden" name="modified_by" id="modified_by" value="<?php echo $_SESSION['authId']; ?>">
	<input type="hidden" name="txt_margin_id" id="txt_margin_id" value="<?php print $marginQryRes['margin_id']; ?>">
<div class="whtbox">
	<div id="houseInfoTable" class="table-responsive respotable adminnw">
		<span id="select_pos" style="position:absolute;"></span>
		<table class="table table-bordered">
			<tr>
				<td style="width:250px;"><label>Margin Type :</label></td>
				<td>
					<select name="margin_type" onChange="document.setMarginForm.submit();" class="selectpicker content_box" data-width="100%" data-title="Select Type" data-container="#select_pos">
					<option value="statement" <?php if($margin_type=="statement") echo "selected"; ?>>Statement</option>
					<option value="recall" <?php if($margin_type=="recall") echo "selected"; ?>>Recall</option>
					<option value="HCFA" <?php if($margin_type=="HCFA") echo "selected"; ?>>HCFA</option>
					<option value="UB04" <?php if($margin_type=="UB04") echo "selected"; ?>>UB04</option>
					<option value="post_card" <?php if($margin_type=="post_card") echo "selected"; ?>>Post Card</option>
					<option value="scanner_config" <?php if($margin_type=="scanner_config") echo "selected"; ?>>Scanner Configuration</option>
					</select>
				</td>
			</tr>	
           <?php
				if($margin_type != "UB04" && $margin_type != "scanner_config"){
			?>
			<tr>
				<td class="text_10b">Top Margin :</td>
				<td>
					<input type="text" class="form-control" tabindex="1" name="top_margin" value="<?php print $marginQryRes['top_margin']; ?>" />
				</td>
			</tr>
            <?php } ?>
			<?php
				if($margin_type != "HCFA" && $margin_type != "UB04" && $margin_type != "post_card" && $margin_type != "scanner_config"){
			?>
			<tr>
				<td class="text_10b">Bottom Margin :</td>
				<td>
					<input type="text" class="form-control" tabindex="2" name="bottom_margin" value="<?php print $marginQryRes['bottom_margin']; ?>" />
				</td>
			</tr>
			<?php
			}
			else if($margin_type == "HCFA" || $margin_type == "UB04" || $margin_type == "post_card"){
			?>
			<tr>
				<td class="text_10b">Left Margin :</td>
				<td>
					<input type="text" class="form-control" tabindex="2" name="left_margin" value="<?php print $marginQryRes['left_margin']; ?>" />
				</td>
			</tr>
			<?php
			} 
			if($margin_type=="recall"){?>
			<tr>
				<td class="text_10b">Line Margin :</td>
				<td>
					<input type="text" class="form-control" tabindex="2" name="line_margin" value="<?php  print $marginQryRes['line_margin']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="text_10b">Column Margin :</td>
				<td>
					<input type="text" class="form-control" tabindex="2" name="column_margin" value="<?php print $marginQryRes['column_margin']; ?>" />
				</td>
			</tr>
			<?php } ?>
            <?php if($margin_type=="UB04"){ 
					$line_num=0;
					$jason_string = $marginQryRes['top_line_margin'];
					$jason_string = stripslashes(html_entity_decode($jason_string));
					$top_line_margin_arr = json_decode($jason_string,true);
			?>
            <tr>
				<td class="text_10b">Top Margin :</td>
			</tr>
            <tr>
            	<td colspan="2">
                	<table class="table table-bordered table-hover table-striped">
                    	<tr style="background:#ffffff;">
                        	<td style="width:310px;">
                            	<table class="table_collapse">
                                	<tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="2" name="top_line_margin[top_1_1]" value="<?php print $top_line_margin_arr['top_1_1']; ?>" />
                                            (Box 1, 2, 3a)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width:310px;">
                            	<table class="table_collapse">
                                	<tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td style="text-align:left;">
                                            <input type="text"  style="width:50px;" tabindex="3" name="top_line_margin[top_1_2]" value="<?php print $top_line_margin_arr['top_1_2']; ?>" />
                                            (Box 1, 2, 3b, 4)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width:310px;">
                            	<table class="table_collapse">
                                	 <tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td style="text-align:left;">
                                            <input type="text"  style="width:50px;" tabindex="4" name="top_line_margin[top_1_3]" value="<?php print $top_line_margin_arr['top_1_3']; ?>" />
                                            (Box 1, 2, 3a)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width:310px;">
                            	<table class="table_collapse">
                                	  <tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td style="text-align:left;">
                                            <input type="text"  style="width:50px;" tabindex="5" name="top_line_margin[top_1_4]" value="<?php print $top_line_margin_arr['top_1_4']; ?>" />
                                            (Box 1, 2, 5, 6, 7)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr style="background:#ffffff;">
                         	<td>
                            	<table class="table_collapse">
                                	 <tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="6" name="top_line_margin[top_8_1]" value="<?php print $top_line_margin_arr['top_8_1']; ?>" />
                                            (Box 8a, 9a)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        	<td>
                            	<table class="table_collapse">
                                	<tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="7" name="top_line_margin[top_8_2]" value="<?php print $top_line_margin_arr['top_8_2']; ?>" />
                                            (Box 8b, 9a, 9c, 9d, 9e)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                            	<table class="table_collapse">
                                	<tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="8" name="top_line_margin[top_10_1]" value="<?php print $top_line_margin_arr['top_10_1']; ?>" />
                                            (Box 10 to 30)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                             <td>
                            	<table class="table_collapse">
                                	<tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="9" name="top_line_margin[top_38_1]" value="<?php print $top_line_margin_arr['top_38_1']; ?>" />
                                            (Box 38, 39a, 40a, 41a)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr style="background:#ffffff;">
                         	<td>
                            	<table class="table_collapse">
                                	    <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="10" name="top_line_margin[top_38_2]" value="<?php print $top_line_margin_arr['top_38_2']; ?>" />
                                                (Box 38, 39b, 40b, 41b)
                                            </td>
                                        </tr>
                                </table>
                             </td>
                             <td>
                            	<table class="table_collapse">
                                	<tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="11" name="top_line_margin[top_38_3]" value="<?php print $top_line_margin_arr['top_38_3']; ?>" />
                                            (Box 38, 39c, 40c, 41c)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                             <td>
                            	<table class="table_collapse">
                                	 <tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="12" name="top_line_margin[top_38_4]" value="<?php print $top_line_margin_arr['top_38_4']; ?>" />
                                            (Box 38, 39d, 40d, 41d)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                             <?php for($i=1;$i<=23;$i++){?>
                             	<td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="<?php echo 12+$i;?>" name="top_line_margin[top_42_<?php echo $i; ?>]" value="<?php print $top_line_margin_arr['top_42_'.$i]; ?>" />
                                                (Box 42.<?php echo $i; ?> to 49.<?php echo $i; ?>)
                                            </td>
                                        </tr>
                                     </table>
                                  </td>      
                            <?php if($i==1 or $i==5 or $i==9 or $i==13 or $i==17 or $i==21){ echo '</tr><tr style="background:#ffffff;">';}?>   
                            <?php } ?>
                             <td>
                            	<table class="table_collapse">
                                	  <tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="35" name="top_line_margin[top_56_1]" value="<?php print $top_line_margin_arr['top_56_1']; ?>" />
                                            (Box 56)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                             <td>
                            	<table class="table_collapse">
                                	<tr>
                                        <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                        <td>
                                            <input type="text"  style="width:50px;" tabindex="36" name="top_line_margin[top_50_1]" value="<?php print $top_line_margin_arr['top_50_1']; ?>" />
                                            (Box 50a to 57a)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                           </tr> 
                            <tr style="background:#ffffff;">
                                <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="37" name="top_line_margin[top_50_2]" value="<?php print $top_line_margin_arr['top_50_2']; ?>" />
                                                (Box 50b to 57b)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="38" name="top_line_margin[top_50_3]" value="<?php print $top_line_margin_arr['top_50_3']; ?>" />
                                                (Box 50c to 57c)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                         <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="39" name="top_line_margin[top_58_1]" value="<?php print $top_line_margin_arr['top_58_1']; ?>" />
                                                (Box 58a to 62a)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="40" name="top_line_margin[top_58_2]" value="<?php print $top_line_margin_arr['top_58_2']; ?>" />
                                                (Box 58b to 62b)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                            </tr> 
                             <tr style="background:#ffffff;">
                                <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="41" name="top_line_margin[top_58_3]" value="<?php print $top_line_margin_arr['top_58_3']; ?>" />
                                                (Box 58c to 62c)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                         <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="42" name="top_line_margin[top_63_1]" value="<?php print $top_line_margin_arr['top_63_1']; ?>" />
                                                (Box 63a to 65a)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="42" name="top_line_margin[top_63_2]" value="<?php print $top_line_margin_arr['top_63_2']; ?>" />
                                                (Box 63b to 65b)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                       <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="43" name="top_line_margin[top_63_3]" value="<?php print $top_line_margin_arr['top_63_3']; ?>" />
                                                (Box 63c to 65c)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                            </tr>
                             <tr style="background:#ffffff;">
                                <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="44" name="top_line_margin[top_66_1]" value="<?php print $top_line_margin_arr['top_66_1']; ?>" />
                                                (Box 67A to 67H)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                         <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="45" name="top_line_margin[top_66_2]" value="<?php print $top_line_margin_arr['top_66_2']; ?>" />
                                                (Box 67J to 67Q)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                         <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="46" name="top_line_margin[top_74_1]" value="<?php print $top_line_margin_arr['top_74_1']; ?>" />
                                                (Box 76.1)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="47" name="top_line_margin[top_74_2]" value="<?php print $top_line_margin_arr['top_74_2']; ?>" />
                                                (Box 74a, 76.2)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                            </tr> 
                            <tr style="background:#ffffff;">
                                <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="48" name="top_line_margin[top_74_3]" value="<?php print $top_line_margin_arr['top_74_3']; ?>" />
                                                (Box 77.1)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                         <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="49" name="top_line_margin[top_74_4]" value="<?php print $top_line_margin_arr['top_74_4']; ?>" />
                                                (Box 74c, 77.2)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="50" name="top_line_margin[top_80_1]" value="<?php print $top_line_margin_arr['top_80_1']; ?>" />
                                                (Box 80a, 81a, 78.1)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                  <td>
                                    <table class="table_collapse">
                                       <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="51" name="top_line_margin[top_80_2]" value="<?php print $top_line_margin_arr['top_80_2']; ?>" />
                                                (Box 80b, 81b, 78.2)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                            </tr> 
                            <tr style="background:#ffffff;">
                            	<td>
                                    <table class="table_collapse">
                                      	<tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="52" name="top_line_margin[top_80_3]" value="<?php print $top_line_margin_arr['top_80_3']; ?>" />
                                                (Box 80c, 81c, 79.1)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                 <td colspan="3">
                                    <table class="table_collapse">
                                      	  <tr>
                                            <td class="text_10b" style="width:80px;">Line <?php echo $line_num=$line_num+1; ?>:</td>
                                            <td>
                                                <input type="text"  style="width:50px;" tabindex="53" name="top_line_margin[top_80_4]" value="<?php print $top_line_margin_arr['top_80_4']; ?>" />
                                                 (Box 80d, 81d, 79.2)
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                            </tr>       
                    </table>
                </td>
            </tr>
            <?php } ?>
		</table>
        <?php
            if($margin_type == "scanner_config")
                { ?>
                <div class="text-center"><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/Scanner_Config_Barcode.png"></div>
        <?php } ?>
	</div>
	<div class="hide"><input type="submit" name="txt_save_margin" id="txt_save_margin" value="save_margin"></div>
	</div>
	</form>
	<?php
	if(trim($msg)) {
		echo '<script type="text/javascript">top.alert_notification_show("'.$msg.'");</script>';
	}
	?>
	<script type="text/javascript">
        var ar = [["saveMargin","Save","top.fmain.document.getElementById('txt_save_margin').click();"]];
        top.btn_show("ADMN",ar);
		set_header_title('Set Margins');
		show_loading_image('none');
    </script>
<?php 
	require_once('../admin_footer.php');
?>