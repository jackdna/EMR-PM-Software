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

?><?php
$pid = $_SESSION['patient'];
$sql_select = "select *,date_format(lab_order_date,'%m-%d-%Y') as order_date
	from lab_test_data where  lab_patient_id = '".$pid."'  and lab_status!='5' ";
$sql_result = imw_query($sql_select);
if(imw_num_rows($sql_result)>0){?>
<table style="width: 100%;"  class="paddingTop" cellpadding="2" cellspacing="0">	
	<tr>
		<td style="width: 100%;" class="tb_heading">Lab Results </td>
	</tr>
</table>	
<table style="width: 100%;" cellpadding="2" cellspacing="0" border="">	
		<tr>
          <td class="text_lable" style="width:15%">Lab Name</td>
            <td class="text_lable" style="width:15%">Test Type</td>
            <td class="text_lable" style="width:10%">LOINC</td>
            <td class="text_lable" style="width:15%">Test Name</td>
            <td class="text_lable" style="width:10%">Results</td>
            <td class="text_lable" style="width:10%">Units</td>
            <td class="text_lable" style="width:10%">Source</td>
            <td class="text_lable" style="width:10%">Cond/Disp</td>
        </tr>
		
	<?php
	while($sql_array = imw_fetch_array($sql_result)){
	$labStatus="";
	if($sql_array['lab_status'] == 1){
			$labStatus="Active";
		}
	 if($sql_array['lab_status'] == 2){
			$labStatus="Discontinued";
		}
	 if($sql_array['lab_status'] == 3){
			$labStatus="Ordered";
		}
	 if($sql_array['lab_status'] == 4){
			$labStatus="Complete";
		}
	?>
		<tr>
            <td class="text_value"><?php echo($sql_array["lab_name"]);?></td>
            <td class="text_value"><?php echo($sql_array["lab_test_type"]);?></td>
            <td class="text_value"><?php echo($sql_array["lab_loinc"]);?></td>
			<td class="text_value"><?php echo($sql_array["lab_test_name"]);?></td>
			<td class="text_value"><?php echo($sql_array["lab_results"]);?></td>
			<td class="text_value"><?php echo($sql_array["lab_units"]);?></td>
			<td class="text_value"><?php echo($sql_array["lab_source"]);?></td>
			<td class="text_value"><?php echo($sql_array["lab_conditions"]);?></td>
		</tr>
		<tr>
			<td colspan="9">
				<table cellpadding="0" cellspacing="0">
						<tr>
							<td class="text_lable">Range:&nbsp;<span class="text_value"><?php echo($sql_array["lab_range"]);?></span></td>
							<td class="text_lable" style="width:20%">Status:&nbsp;<span class="text_value"><?php echo($labStatus);?></span></td>
							<td class="text_lable" style="width:20%">Order By:&nbsp;<span class="text_value"><?php $retNAmeArr=getUserFirstName($sql_array["lab_test_order_by"],$flgFull=2); print($retNAmeArr[1]);?></span></td>
							<td class="text_lable" style="width:20%">Order Date:&nbsp;<span class="text_value"><?php echo ($sql_array['order_date'] != '00-00-0000')?$sql_array['order_date']:"";?></span></td>
							<?php if($sql_array["lab_comments"]!=""){?>
							<td class="text_lable">Comments:&nbsp;<span class="text_value"><?php echo($sql_array["lab_comments"]);?></span></td>
							<?php }?>
						</tr>
						
				</table>
			</td>
		</tr>
<tr>
	<td colspan="9"><hr></td>
</tr>
		
<?php
 }	
?>
</table>
<?php
}
?>
