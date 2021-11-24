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
require_once("../../../../config/globals.php");
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
$ins_comp_name=$_REQUEST['ins_com_name'];
if($ins_comp_name || $_REQUEST['search']=='y'){
	if($_REQUEST['search']=="y"){
		$ins_comp_name=$_REQUEST['text'];		
	}
	$name=$ins_comp_name;
	$arr_replace=array("-","(",")","'",'"',"%");
	$name=str_replace($arr_replace,"",$name);	
	if($name){
		$nameQry="%".$name."%' OR in_house_code LIKE '%".$name."%' OR replace(phone,'-','') LIKE '%".$name."";	
	}
}
if(empty($record) != true){
	$result = ("Select name from insurance_companies 
			where (trim(name) like '$name%' $num_ins_whr) and ins_del_status = '0' ORDER BY name ASC");
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
$grpID = (isset($grpID))? $grpID:'';
if($grpID != ""){
	$where = " AND groupedIn = '".$grpID."'";
}else $where = "";
$query = "select count(*) from insurance_companies where (trim(name) like '$name%' $num_ins_whr) $where";
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

//////////Function Change Post appostrophy character to Upper Case/////
function changePostAppostrophyCharToUpper($rawstring){
	$placeholders = array('`a', '`b', '`c', '`d','`e','`f', '`g','`h','`i','`j','`k','`l','`m','`n','`o','`p','`q','`r','`s','`t','`u','`v','`w','`x','`y','`z',"'a", "'b", "'c", "'d","'e","'f", "'g","'h","'i","'j","'k","'l","'m","'n","'o","'p","'q","'r","'s","'t","'u","'v","'w","'x","'y","'z'");
	$ReplaceVals = array('`A', '`B', '`C', '`D','`E','`F', '`G','`H','`I','`J','`K','`L','`M','`N','`O','`P','`Q','`R','`S','`T','`U','`V','`W','`X','`Y','`Z',"'A", "'B", "'C", "'D","'E","'F", "'G","'H","'I","'J","'K","'L","'L","'N","'O","'P","'Q","'R","'S","'T","'U","'V","'W","'X","'Y","'Z'");
	$rawstring = str_replace($placeholders, $ReplaceVals, $rawstring);
	return $rawstring;
}

//------ End Next Pagination --------------		
if($pageLink){
	$pageLinks = "$previousLink <td style=\"background-color:#bfd3e6\" class='text_10ab alignCenter'>$pageLink</td>$startLink";
	$pageLinksGrp = " <td style=\"background-color:#bfd3e6\" class='text_10ab alignCenter'>$previousLinkGrp &nbsp;&nbsp;$pageLinkGrp &nbsp;&nbsp;$startLinkGrp</td>";
}

$arrPage = range('A','Z');	
$text = strtoupper($text);
$imgsrc = array_search($text,$arrPage) + 1;
if(is_numeric($text) === true){
	$imgsrc = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>imwemr</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']; ?>/library/css/admin.css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot']; ?>/library/css/style.css">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
	<script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
	<script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/core_main.js"></script>
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
		parent.change_class('image_'+imgsrc);
	}
</script>	
</head>
<body>
<div class="mainwhtbox">
		<div class="table-responsive respotable">
			<table class="table table-bordered table-hover table-striped" id="tblInsComp">	
				<thead>
					<tr class="grythead">
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
				<tbody id="result_set">

		<?php
		//--- GET DEFAULT CLAIM TYPE ---
		
		$query = "select name from copay_policies";		
		$queryRes = imw_fetch_array($query);
		$default_claim_type = $queryRes['name'];
		if($grpID != ""){
			$where = " AND groupedIn = '".$grpID."'";
		}else $where = "";
		$qryIns=" trim(name) like '$name%'";
		if($_REQUEST['ins_com_name'] || $_REQUEST['search']=='y'){
			$qryIns=" trim(name) like '$nameQry%'";
			$srch="y";	
		}
		$vquery_c = "select * from insurance_companies where ($qryIns $num_ins_whr $where) 
					 ORDER BY name ASC limit $startLimit,$limit";		
		$vsql_c = imw_query($vquery_c);
		$j = 0;
		
	?>
	<tr>
		<td style="background-color:#FFF" colspan="8" class="text_10 alignCenter"><?php echo $htxt;?></td>
	</tr>
	<?php	
		if(imw_num_rows($vsql_c)>0){
			while($vrs=imw_fetch_array($vsql_c)){																	
				$i++;
				if($j % 2 == 0){ $class = 'alt3'; } else{ $class = ''; }
				if($vrs['id'] == $_REQUEST['ins_id']){ $class = 'hover'; } else{ $class = $class; }
				?>			
		<tr class="<?=$class?>" style="height:20px;">
			<td class="text_10">
				<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>&id=<?php echo $grpID;?>&amp;search=<?php echo $srch; ?>' target="_parent">								
					<?php
						echo changePostAppostrophyCharToUpper($vrs['name']);
					?>		
				</a>				
			</td>									
			<td class="text_10 alignCenter">																		
				<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>&id=<?php echo $grpID?>' target="_parent" class="text_10ab">
					<?php
						if ($vrs['in_house_code']){
							print $vrs['in_house_code'];
						}							
					?>
				</a>	
			</td>
			<!--<td class="text_10 alignCenter">
				<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>' target="_parent" class="text_10ab">
					<?php
						if ($vrs['contact_name']) {
							print $vrs['contact_name'];
						}							
					?>
				</a>
			</td>-->
			<td class="text_10 alignCenter">
				<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>&id=<?php echo $grpID?>' target="_parent" class="text_10ab">
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
						/*if ($vrs['contact_address']) {	
							print $vrs['contact_address'].', '.$vrs['City'].', '.$vrs['State'].' '.$vrs['Zip'];
						}
						elseif($vrs['City'] != '' && $vrs['State'] != ''){
							print $vrs['City'].', '.$vrs['State'].' '.$vrs['Zip'];
						}*/						
					?>
				</a>	
			</td>
			<td class="text_10 alignCenter">
				<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>&id=<?php echo $grpID?>'>
					<?php
						if ($vrs['insurance_Practice_Code_id']) {
							print $vrs['insurance_Practice_Code_id'];
						}							
					?>
				</a>	
			</td>
			<td class="text_10 alignCenter nowrap">
				<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>&id=<?php echo $grpID?>'>
					<?php
						if ($vrs['phone']) {
							print core_phone_format($vrs['phone']);
						}
					?>
				</a>
			</td>
			<td class="text_10 alignCenter">
				<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>&id=<?php echo $grpID?>'>
					<?php
						if($vrs['Insurance_payment']=='Electronics'){$vrs['Insurance_payment']='Electronic';}
						if($vrs['secondary_payment_method']=='Electronics'){$vrs['secondary_payment_method']='Electronic';}
						print ucfirst($vrs['Insurance_payment']);
						print '&nbsp;/&nbsp;'.ucfirst($vrs['secondary_payment_method']);
					?>
				</a>
			</td>
			<td class="text_10 alignCenter">
				<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>&id=<?php echo $grpID?>'>
				<?php
					$claim_type = $default_claim_type;
					if($vrs['claim_type'] > 0){
						$claim_type = 'Medicare';
					}
					print $claim_type;
				?>
                </a>
			</td>
        <?php if($grpID == ""){?>
				<td class="alignCenter">
                	<?php if($vrs['ins_del_status']==1) { ?>
<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>' target="_parent" class="text_10ab"><img src="../../../../library/images/inactive.jpg" title="Inactive" class="noborder"></a>
                    <?php } else { ?>
<a href='index.php?text=<?php echo $name;?>&amp;page=<?php echo $pageNum;?>&amp;ins_id=<?=$vrs['id']?>&amp;st=<?php echo $st; ?>' target="_parent" class="text_10ab"><img src="../../../../library/images/active.jpg" title="Active" class="noborder"></a>
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
<table class="table_collapse alignCenter" style="background-color:#FFE2C6" id="tblpaging">
	<tr>
    <?php if($grpID != ""){
			 echo $pageLinksGrp;
			}
			else echo $pageLinks;
	?>
	</tr>
    <?php if($grpID != ""){?>
    <tr><td>
    <?php $arrPage = range('A','Z');
			$imgsrc = array_search($name,$arrPage);
			if($imgsrc){
				$src = $imgsrc + 1;
			}
			else{
				$src = 1;
			}
			$arrPage1 = array('0-9');
			$arrPage = array_merge($arrPage1,$arrPage);
			$alphaPaging = '';
			for($i=0;$i<count($arrPage);$i++){
				$key = $i;
				$val = $arrPage[$i];
				if($val=='0-9'){
					$class_pag='class="num"';
				}else{
					$class_pag='class="pagenation_alpha tblBg alignCenter valignTop"';
				}
				if($key == $src){
					$alphaPaging .= '<a href="#" class="activealpha" id="image_'.$key.'" onClick="getInsComp('.$grpID.',1,\''.$val.'\');change_class(\'image_'.$key.'\');" >'.$val.'</a>';
				}
				else{
					$alphaPaging .= '<a href="#" '.$class_pag.' id="image_'.$key.'" onClick="getInsComp('.$grpID.',1,\''.$val.'\');change_class(\'image_'.$key.'\');" >'.$val.'</a>';
					
				}
			}print '<div class="pagenation_alpha tblBg alignCenter valignTop" >'.$alphaPaging."</div>";
			?>
    </td></tr>
    <?php }?>
</table>

<?php if($grpID != ""){?>
<style>

.arrow{
cursor:default;
font-family:"verdana";
font-size:12px; 
color:#333333;
font-weight:normal;
}
</style>
<script>

$("#tblInsComp tr td a").click(function(e){
		return false;});
$("#tblInsComp tr td a").addClass('arrow')

</script>
<?php }?>

</body>
</html>