<?php 
/*
File: paging_new.php
Coded in PHP7
Purpose: Pagination File
Access Type: Include File
*/
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
	// Initial page num setup
	if ($page == 0){$page = 1;}
	$prev = $page - 1;	
	$next = $page + 1;	
	$lastpage = ceil($total_pages);
	$LastPagem1 = $lastpage - 1;	
	
	
	$paginate = '';
	if($lastpage > 1)
	{	
	
$paginate .= "<div class='paginate'>";
// Previous
if ($page > 1){
	$paginate.= "<a href='$targetpage&page=$prev'>Previous</a>";
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
	$paginate.= "<a href='$targetpage&page=$counter'>$counter</a>";}	
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
$paginate.= "<a href='$targetpage&page=$counter'>$counter</a>";}	
}
$paginate.= "...";
$paginate.= "<a href='$targetpage&page=$LastPagem1'>$LastPagem1</a>";
$paginate.= "<a href='$targetpage&page=$lastpage'>$lastpage</a>";
	}
	// Middle hide some front and some back
	elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2))
	{
$paginate.= "<a href='$targetpage&page=1'>1</a>";
$paginate.= "<a href='$targetpage&page=2'>2</a>";
$paginate.= "...";
for ($counter = $page - $stages; $counter <= $page + $stages; $counter++)
{
	if ($counter == $page){
$paginate.= "<span class='current'>$counter</span>";
	}else{
$paginate.= "<a href='$targetpage&page=$counter'>$counter</a>";}	
}
$paginate.= "...";
$paginate.= "<a href='$targetpage&page=$LastPagem1'>$LastPagem1</a>";
$paginate.= "<a href='$targetpage&page=$lastpage'>$lastpage</a>";
	}
	// End only hide early pages
	else
	{
$paginate.= "<a href='$targetpage&page=1'>1</a>";
$paginate.= "<a href='$targetpage&page=2'>2</a>";
$paginate.= "...";
for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++)
{
	if ($counter == $page){
$paginate.= "<span class='current'>$counter</span>";
	}else{
$paginate.= "<a href='$targetpage&page=$counter'>$counter</a>";}	
}
	}
}
	
// Next
if ($page < $counter - 1){ 
	$paginate.= "<a href='$targetpage&page=$next'>Next</a>";
}else{
	$paginate.= "<span class='disabled'>Next</span>";
	}
	
$paginate.= "</div>";
	
	
}
// echo $total_pages.' Results';
 // pagination
 echo $paginate;

?>