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

include '../../../config/globals.php';
$i=0;
$qry = "SELECT insurance_companies.id AS iMW_PayerID, insurance_companies.emdeon_payer_eligibility AS RTE_PayerID, insurance_companies.name AS Payer_Name,  insurance_companies.State AS Payer_state, insurance_companies.in_house_code AS Practice_code, count(insurance_data.provider) AS Num_of_Subscribers, insurance_companies.rte_chk as `RTE_Enabled(0/1)` FROM insurance_data,insurance_companies where insurance_companies.id=insurance_data.provider and insurance_data.type='primary' GROUP BY insurance_data.provider ORDER BY count(insurance_data.provider)  DESC limit 0,100";
$insQry = imw_query($qry);

?>	
<style type="text/css">
.mainrow{ font-size:18px; color:#fff; }
.fontstyle{ font-size:18px; color:black; }
.borderleft{border-left:1px solid #fff;}
.border{border:1px solid #CCC;}
.pl5{padding-left:5px;}
.bgcolor{background-color:#1b9e95;}
.pt5{padding-top:5px;}
.pbtm5{padding-bottom:5px;}
.center{text-align:center;}
.left{text-align:left;}
</style>
<div>
	<div class="mainrow">
	<!--<h2>MOST SUBSCRIBED INSURANCE LIST</h2>-->
		<table style="width:100%;" cellpadding="0" cellspacing="0" align="center" >
			<tr>
				<th class="mainrow pl5 bgcolor pt5 pbtm5 left" style="width:5%;">Sr. No.</th>
				<th class="mainrow borderleft pl5 bgcolor pt5 pbtm5 left" style="width:30%;">Payer Name</th>
				<th class="mainrow borderleft pl5 bgcolor pt5 pbtm5 left" style="width:10%;">Payer State</th>
				<th class="mainrow borderleft pl5 bgcolor pt5 pbtm5 left" style="width:20%;">Practice Code</th>
				<th class="mainrow borderleft pl5 bgcolor pt5 pbtm5 left" style="width:10%;">Payer ID</th>
				<th class="mainrow borderleft pl5 bgcolor pt5 pbtm5 left" style="width:35%;">No. of Subscribers</th>
			</tr>
			<?php if(imw_num_rows($insQry)>0){
					//===START WHILE LOOP===
					while($insRow = imw_fetch_assoc($insQry)){
						$i++;
			?>
			<tr>
				<td class="fontstyle border pl5 pt5 pbtm5" style="width:5%;"><?php echo $i; ?></td>
				<td class="fontstyle border pl5 pt5 pbtm5" style="width:30%;"><?php echo $insRow['Payer_Name']; ?></td>
				<td class="fontstyle border pl5 pt5 pbtm5" style="width:15%;"><?php echo $insRow['Payer_state']; ?></td>
				<td class="fontstyle border pl5 pt5 pbtm5" style="width:15%;"><?php echo $insRow['Practice_code']; ?></td>
				<td class="fontstyle border pl5 pt5 pbtm5" style="width:15%;"><?php echo $insRow['RTE_PayerID']; ?></td>
				<td class="fontstyle border pl5 pt5 pbtm5" style="width:25%;"><?php echo $insRow['Num_of_Subscribers']; ?></td>
			</tr>
			<?php
					}
					//===END WHILE LOOP===
				}else{
			?>		
			<tr>
				<td class="fontstyle border pl5 pt5 pbtm5 center" style="width:100%;" colspan="6" >No Record Exists</td>
			</tr>		
			<?php
				}
			?>
		</table>	
	</div>
</div> 