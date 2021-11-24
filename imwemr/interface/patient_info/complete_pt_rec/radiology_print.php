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
$sql_select = "select * , date_format(rad_order_date,'".get_sql_date_format()."') as ordered_date
		from rad_test_data where rad_status != '3' and rad_patient_id = '".$pid."'";
$sql_result = imw_query($sql_select);
if(imw_num_rows($sql_result)>0){?>
<table style="width: 100%;"  class="paddingTop" cellpadding="2" cellspacing="0">	
	<tr>
		<td style="width: 100%;" class="tb_heading">Radiology Results</td>
	</tr>
</table>	
<table style="width: 100%;" cellpadding="2" cellspacing="0">	
		<tr>
            <td class="text_lable" style="width: 20%;">Contact Name</td>
            <td class="text_lable" style="width: 20%;">Radiology Name</td>
            <td class="text_lable" style="width: 20%;">Results</td>
			<td class="text_lable" style="width: 10%;">Indication</td>
            <td class="text_lable" style="width: 10%;">Order Date</td>
            <td class="text_lable" style="width: 10%;">Status</td>
            <td class="text_lable" style="width: 10%;">Order By</td>
        </tr>
		
	<?php
	while($sql_array = imw_fetch_array($sql_result)){
	?>
		 <tr>
            <td class="text_value" style="width: 150px; vertical-align:top;"><?php echo($sql_array["rad_fac_name"]);?></td>
            <td class="text_value" style="width: 150px; vertical-align:top;"><?php echo($sql_array["rad_name"]);?></td>
            <td class="text_value" style="width: 150px; vertical-align:top;"><?php echo($sql_array["rad_results"]);?></td>
		    <td class="text_value" style="width: 100px; vertical-align:top;"><?php echo($sql_array["rad_indication"]);?></td>
            <td class="text_value" style="width: 100px; vertical-align:top;"><?php echo ($sql_array['ordered_date'] != '00-00-0000')?$sql_array['ordered_date']:"";?></td>
            <td class="text_value" style="width: 100px; vertical-align:top;"><?php echo($sql_array["rad_status"]==1)?"Ordered":"Completed";?></td>
            <td class="text_value" style="text-align:center; width: 50px; vertical-align:top;"><?php $retNAmeArr=getUserFirstName($sql_array["rad_order_by"],$flgFull=2); print($retNAmeArr[1]); //echo(getProviderName($sql_array["rad_order_by"]));?></td>
        </tr>
<?php
 }	
?>
</table>
<?php
}
?>
