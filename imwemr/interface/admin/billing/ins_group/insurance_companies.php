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
require_once("../../admin_header.php");
require_once("../../../../library/classes/common_function.php");	

$_SESSION["encounter"] = "";
$pid = $_SESSION['patient'];
$_SESSION['btn']="";
$st = $_REQUEST['st'];
$name = $_GET['text'] == ""?"A":$_GET['text'];
if($name=="0-9"){
	$num_ins_whr="";
	for($i=0;$i<=9;$i++){
		$num_ins_whr .= " or name like '$i%'";
	}
}
$pageNum = $_GET['page']; 
$limit  = 17;
$grpID = (isset($grpID))? $grpID:'';
if($grpID != ""){
	$where = " AND groupedIn = '".$grpID."'";
}else $where = "";
if(empty($record) != true){
	$result = "Select name from insurance_companies where ins_del_status = '0' $where ORDER BY name ASC";
	$record_result = imw_fetch_array($result);
	for($i=0;$i<count($record_result);$i++){
		if($record_result[$i]['name'] == $record){
			$record_no = $i+1;
		}
	}
	$pageNum = ceil($record_no/$limit);
}

if(empty($pageNum) == true){
	$pageNum = 1;
}

$startLimit = ($pageNum - 1) * $limit;

$query = "select count(*) from insurance_companies where  ins_del_status = '0' and groupedIn = '".$grpID."'";
$qry = imw_query($query);		
list($count) = imw_fetch_array($qry);
$totalPage = ceil($count / $limit);
$pageLimit = 10;
$startPage = $pageNum - $pageLimit;		
if($startPage < 1){
	$startPage =  1 ;
}

$endPage = $pageLimit + $pageNum;
if($endPage > $totalPage){
	$endPage = $totalPage;
}

for($i = $startPage;$i <= $endPage; $i++){
	if($i == $pageNum){
		$pageLink .= '<a class="text_10b">['.$i.']</a>&nbsp;&nbsp;';
		$pageLinkGrp .= '<a class="text_10b">['.$i.']</a>&nbsp;&nbsp;';
	}
	else{
		$pageLink .= '<a href="insurance_companies.php?page='.$i.'&amp;text='.$name.'"class="text_10b_purpule">'.$i.'</a>&nbsp;&nbsp;';
		$pageLinkGrp .= '<a href="#"  onClick="getInsComp('.$grpID.','.$i.',\''.$name.'\');" class="text_10b_purpule">'.$i.'</a>&nbsp;&nbsp;';
	}
}


//------ Start Next Pagination --------------

if($count > $limit + $startLimit){
	$pageNumber = $pageNum + 1;
	$startLink = "<a href='insurance_companies.php?page=$pageNumber&amp;text=$name' class='text_10b_purpule'>Next</a>";
	$startLinkGrp = "<a href='#'  onClick='getInsComp(".$grpID.",".$pageNumber.",\"".$name."\");' class='text_10b_purpule'>Next</a>";
}
if(0 < $startLimit - $pageNum){
	$pageNumber = $pageNum - 1;
	$previousLink = "<a href='insurance_companies.php?page=$pageNumber&amp;text=$name' class='text_10b_purpule'>Previous</a>";
	$previousLinkGrp = "<a href='#' onClick='getInsComp(".$grpID.",".$pageNumber.",\"".$name."\");' class='text_10b_purpule'>Previous</a>";
}

if($startLink){
	$startLink = "<td style=\"width:20%; background-color:#bfd3e6\" class='text_10ab alignCenter'>$startLink</td>";
}
else{
	$startLink = "<td style=\"width:20%; background-color:#bfd3e6\" class='text_10ab alignCenter'></td>";
}
if($previousLink){
	$previousLink = "<td style=\"width:20%; background-color:#bfd3e6\" class='text_10ab alignCenter'>$previousLink</td>";
}
else{
	$previousLink = "<td style=\"width:20%; background-color:#bfd3e6\" class='text_10ab alignCenter'></td>";
}

//------ End Next Pagination --------------		
if($pageLink){
	$pageLinks = "$previousLink <td style=\"background-color:#bfd3e6\" class='text_10ab alignCenter'>$pageLink</td>$startLink";
	$pageLinksGrp = " <td style=\"background-color:#bfd3e6\" class='text_10ab alignCenter'>$previousLinkGrp &nbsp;&nbsp;$pageLinkGrp &nbsp;&nbsp;$startLinkGrp</td>";
}

//////////Function Change Post appostrophy character to Upper Case/////
function changePostAppostrophyCharToUpper($rawstring){
	$placeholders = array('`a', '`b', '`c', '`d','`e','`f', '`g','`h','`i','`j','`k','`l','`m','`n','`o','`p','`q','`r','`s','`t','`u','`v','`w','`x','`y','`z',"'a", "'b", "'c", "'d","'e","'f", "'g","'h","'i","'j","'k","'l","'m","'n","'o","'p","'q","'r","'s","'t","'u","'v","'w","'x","'y","'z'");
	$ReplaceVals = array('`A', '`B', '`C', '`D','`E','`F', '`G','`H','`I','`J','`K','`L','`M','`N','`O','`P','`Q','`R','`S','`T','`U','`V','`W','`X','`Y','`Z',"'A", "'B", "'C", "'D","'E","'F", "'G","'H","'I","'J","'K","'L","'L","'N","'O","'P","'Q","'R","'S","'T","'U","'V","'W","'X","'Y","'Z'");
	$rawstring = str_replace($placeholders, $ReplaceVals, $rawstring);
	return $rawstring;
}

$arrPage = range('A','Z');	
$text = strtoupper($text);
$imgsrc = array_search($text,$arrPage) + 1;
if(is_numeric($text) === true){
	$imgsrc = 0;
}
?>
<script type="text/javascript">
	function confirm_del(val){
		if(val==1){
			var stat="Activate";
		}else{
			var stat="Inactivate";
		}
		var msg = stat+" selected record?";
		var confd = confirm(msg);
		return confd;
	}
	
	show_loading_image('none');
	var imgsrc = '<?php print $imgsrc; ?>';
	if(imgsrc >= 0){
		//parent.change_class('image_'+imgsrc);
	}
</script>	
<body>
<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover" id="tblInsComp">	
				<thead>
					<tr>
						<th nowrap>Company Name</th>
						<th nowrap>Practice Code</th>
						<th nowrap>Contact Address</th>
						<th nowrap>Practice Group Id</th>
						<th nowrap>Phone</th>
						<th nowrap>Pri / Sec Payments</th>
						<th nowrap>Claim Type</th>
						<?php if($grpID == ""){?>
						<th nowrap>Status</th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
		
	<?php
		//--- GET DEFAULT CLAIM TYPE ---
		$query = "select name from copay_policies";		
		$queryRes = imw_fetch_array($query);
		$default_claim_type = $queryRes[0]['name'];
		if($grpID != ""){
			$where = " AND groupedIn = '".$grpID."'";
		}else $where = "";
		$vquery_c = "select * from insurance_companies where  groupedIn = '".$grpID."' AND  ins_del_status = '0' ORDER BY name ASC ";
		$vsql_c = imw_query($vquery_c);
		$j = 0;
	?>
	<!-- <tr>
		<td style="background-color:#FFF" colspan="8" class="text_10 alignCenter"><?php echo $htxt;?></td>
	</tr> -->
	<?php	
		if(imw_num_rows($vsql_c)>0){
			while($vrs=imw_fetch_array($vsql_c)){																	
				?>			
		<tr style="height:20px;">
			<td>
												
					<?php
						echo changePostAppostrophyCharToUpper($vrs['name']);
					?>		
								
			</td>									
			<td>																		
				
					<?php
						if ($vrs['in_house_code']){
							print $vrs['in_house_code'];
						}							
					?>
				
			</td>
			<td>
				
					<?php
						$insCompFullAdd = "";
						if ($vrs['contact_address']){
							$insCompFullAdd .= trim($vrs['contact_address']);
						}
						if($vrs['City'] != '' && $vrs['State'] != ''){
							$insCompFullAdd .= " ".trim($vrs['City']).', '.trim($vrs['State']).' '.trim($vrs['Zip']);
						}
						if($vrs['zip_ext'] != ''){
							$insCompFullAdd .= "-".trim($vrs['zip_ext']);
						}
						echo $insCompFullAdd;					
					?>
				
			</td>
			<td>
				
					<?php
						if ($vrs['insurance_Practice_Code_id']) {
							print $vrs['insurance_Practice_Code_id'];
						}							
					?>
				
			</td>
			<td nowrap>
				
					<?php
						if ($vrs['phone']) {
							print core_phone_format($vrs['phone']);
						}
					?>
				
			</td>
			<td nowrap>
				
					<?php
						if($vrs['Insurance_payment']=='Electronics'){$vrs['Insurance_payment']='Electronic';}
						if($vrs['secondary_payment_method']=='Electronics'){$vrs['secondary_payment_method']='Electronic';}
						print ucfirst($vrs['Insurance_payment']);
						print ' / '.ucfirst($vrs['secondary_payment_method']);
					?>
			
			</td>
			<td>
				
				<?php
					$claim_type = $default_claim_type;
					if($vrs['claim_type'] > 0){
						$claim_type = 'Medicare';
					}
					print $claim_type;
				?>
                
			</td>
			<?php if($grpID == ""){?>
			<td>
			<?php if($vrs['ins_del_status']==1) { ?>
				<img src="../../../../library/images/inactive.jpg" title="Inactive">
			<?php } else { ?>
				<img src="../../../../library/images/active.jpg" title="Active">
			<?php } ?>
			</td>
            <?php }?>
		</tr>	
		<?php
			$j++;
		}
	}
	else{
		?>
		<tr>
			<td colspan="8"  class="failureMsg text-center">No Record Exists.</td>
		</tr>
		<?php
	}
	?>
</tbody>
			</table>
		</div>
	</div>
</body>
</html>