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

include_once('../../../config/globals.php');
$fname=trim($_GET['pname']);
$lname=trim($_GET['lastname']);
$title=trim($_GET['title']);
$fnametosearch=substr(trim($fname),0,1);
$vquery = "select * from patient_data where fname like '$fnametosearch%' and lname='$lname' order by lname,fname";		
$result1s = imw_query($vquery);
if(imw_num_rows($result1s)>0)	{
	echo "<Yes><SHOWME><input type=\"hidden\" name=\"exist\" id=\"exist\" value=\"alreadyexist\">";

}else{
	echo "<No><SHOWME><input type=\"hidden\" name=\"exist\" id=\"exist\" value=''>";
}
?>
<div id="scheduler_pt_search" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Patient(s) with similar name found</h4>
			</div>
			<div class="modal-body" style="height:500px;overflow-y:scroll">
				
				<?php if($result1s<>""){ ?>
					<table class="table table-bordered table-striped">
						<tr class="grythead">
							<th>ID</th>
							<th>Name</th>
							<th>SS</th>
							<th>Phone</th>
							<th>DOB</th>
						</tr>
						<?php
							$anum=imw_num_rows($result1s);
							if($anum > 0){
								while($iter=imw_fetch_array($result1s)){
									$iterpid   = $iter['id'];
									$iterlname = str_replace("'"," ",$iter['lname']);
									$iterfname = str_replace("'"," ",$iter['fname']);
									$itermname = str_replace("'"," ",$iter['mname']);
									$itersuffix = str_replace("'"," ",$iter['suffix']);
									
								   $phone_home = $iter['phone_home'];
								   $phone_cell = $iter['phone_cell'];
								   $sex = $iter['sex'];   
								   $street = $iter['street'];
								   $city = $iter['city'];
								   
								   $title = $iter['title'];
								   
								   $state = $iter['state'];						  
								   $phone_biz = $iter['phone_biz'];
								   $zip = $iter['postal_code'];						   
									$notes=$iter['patient_notes'];					   
								   $dob=explode("-",$iter['DOB']);
								   $yr=$dob[0];
								   $mo=$dob[1];
								   $dy=$dob[2];						
								   if($iterfname!="")	{
										$patientname=$iterfname;
									}
									if($itermname!="")	{
										$patientname.=' '.$itermname;
									}
									if($iterlname!="")	{
										$patientname.=' '.$iterlname;
									}
									if($itersuffix!="")	{
										$patientname.=' '.$itersuffix;
									} 
								   $dob_format=$mo."-".$dy."-".$yr;
									$anchor = "<a href=\"javascript:void(0);\" ". "onclick=\"return selpid($iterpid)\" class=\"text12\">";	
								   echo " <tr>";
								   echo "  <td  align=\"left\">$anchor" . $iter['id'] . "</a></td>\n";
								   echo "  <td  align=\"left\">$anchor$iterlname, $iterfname</a></td>\n";
								   echo "  <td  align=\"left\">$anchor" . $iter['ss'] . "</a></td>\n";
								   echo "  <td  align=\"left\">$anchor" . $iter['phone_home'] . "</a></td>\n";
								   echo "  <td  align=\"left\">$anchor" . $dob_format . "</a></td>\n";
								   echo " </tr>";  
								}
							}else{
								echo '<tr><td colspan="5" class="text-center">No record found</td></tr>';
								
							}
						?>	
					</table>	
				<?php } ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>