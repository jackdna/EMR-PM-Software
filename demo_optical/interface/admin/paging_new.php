<?php 
/*
File: paging_new.php
Coded in PHP7
Purpose: Pagination File
Access Type: Include File
*/
$clickAction = '';
if(isset($_REQUEST['getPagesAjax'])){
	$limit = $_REQUEST['limit'];
	$total_pages = $_REQUEST['total_pages'];
	$stages = $_REQUEST['stages'];
	$page = $_REQUEST['page'];
	$clickAction = 'onClick="newPage(event, this)"';
}
?>
<style>
.paginate {
font-family:Arial, Helvetica, sans-serif;
	padding: 3px;
	margin: 3px;
}

.paginate a {
	padding:2px 5px 2px 5px;
	margin:2px;
	border:1px solid #52BFEA;
	text-decoration:none;
	color: #000;
}
.paginate a:hover, .paginate a:active {
	border: 1px solid #52BFEA;
	color: #52BFEA;
}
.paginate span.current {
    margin: 2px;
	padding: 2px 5px 2px 5px;
border: 1px solid #52BFEA;

font-weight: bold;
background-color: #52BFEA;
color: #FFF;
	}
	.paginate span.disabled {
padding:2px 5px 2px 5px;
margin:2px;
border:1px solid #eee;
color:#DDD;
	}
</style>
<?php 
	if(!$ext_parm){
		$targetpage.="?param=true";
	}
	/*Type filter for Lens Design*/
	if(isset($_REQUEST['filter_type']) && $_REQUEST['filter_type']!="" && $_REQUEST['filter_type']!="0"){
		$targetpage = ($targetpage!="")?$targetpage."&filter_type=".$_REQUEST['filter_type']:"&filter_type=".$_REQUEST['filter_type'];
	}
	if($_REQUEST['alpha']!=""){
		$targetpage.="&alpha=".$_REQUEST['alpha']."";
	}
	// Initial page num setup
	if ($page == 0){$page = 1;}
	$prev = $page - 1;	
	$next = $page + 1;	
	$lastpage = ceil($total_pages/$limit);
	$LastPagem1 = $lastpage - 1;	
	
	$paginate = '';
	if($lastpage > 1)
	{	
$paginate .= "<div class='paginate'>";
// Previous
if ($page > 1){
	$paginate.= "<a href='$targetpage&page=$prev' ".$clickAction.">Previous</a>";
}else{
	$paginate.= "<span class='disabled'>Previous</span>";	}
	


// Pages	
if ($lastpage < 7 + ($stages * 2))	// Not enough pages to breaking it up
{	
	for ($counter = 1; $counter <= $lastpage; $counter++)
	{
if ($counter == $page){
	$paginate.= "<span class='current'>$counter</span>";
}else{
	$paginate.= "<a href='$targetpage&page=$counter' ".$clickAction.">$counter</a>";}	
	}
}
elseif($lastpage > 5 + ($stages * 2))	// Enough pages to hide a few?
{
	// Beginning only hide later pages
	if($page < 1 + ($stages * 2))
	{
for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
{
	if ($counter == $page){
$paginate.= "<span class='current'>$counter</span>";
	}else{
$paginate.= "<a href='$targetpage&page=$counter' ".$clickAction.">$counter</a>";}	
}
$paginate.= "...";
$paginate.= "<a href='$targetpage&page=$LastPagem1' ".$clickAction.">$LastPagem1</a>";
$paginate.= "<a href='$targetpage&page=$lastpage' ".$clickAction.">$lastpage</a>";
	}
	// Middle hide some front and some back
	elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2))
	{
$paginate.= "<a href='$targetpage&page=1' ".$clickAction.">1</a>";
$paginate.= "<a href='$targetpage&page=2' ".$clickAction.">2</a>";
$paginate.= "...";
for ($counter = $page - $stages; $counter <= $page + $stages; $counter++)
{
	if ($counter == $page){
$paginate.= "<span class='current'>$counter</span>";
	}else{
$paginate.= "<a href='$targetpage&page=$counter' ".$clickAction.">$counter</a>";}	
}
$paginate.= "...";
$paginate.= "<a href='$targetpage&page=$LastPagem1' ".$clickAction.">$LastPagem1</a>";
$paginate.= "<a href='$targetpage&page=$lastpage' ".$clickAction.">$lastpage</a>";
	}
	// End only hide early pages
	else
	{
$paginate.= "<a href='$targetpage&page=1' ".$clickAction.">1</a>";
$paginate.= "<a href='$targetpage&page=2' ".$clickAction.">2</a>";
$paginate.= "...";
for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++)
{
	if ($counter == $page){
$paginate.= "<span class='current'>$counter</span>";
	}else{
$paginate.= "<a href='$targetpage&page=$counter' ".$clickAction.">$counter</a>";}	
}
	}
}
	
// Next
if ($page < $counter - 1){ 
	$paginate.= "<a href='$targetpage&page=$next' ".$clickAction.">Next</a>";
}else{
	$paginate.= "<span class='disabled'>Next</span>";
	}
	
$paginate.= "</div>";
	
	
}
// echo $total_pages.' Results';
 // pagination
 echo $paginate;

?>