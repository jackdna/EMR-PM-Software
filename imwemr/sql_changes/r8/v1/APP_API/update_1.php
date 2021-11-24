<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql = array();
$error = array();

$sql[] = "CREATE  TABLE  `fmh_iportal_api_call_log` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
 `path` varchar( 50  )  NOT  NULL  COMMENT  'Request Path Called',
 `method` char( 10  )  NOT  NULL ,
 `headers` text NOT  NULL ,
 `user_agent` varchar( 255  )  NOT  NULL ,
 `ip` varchar( 20  )  NOT  NULL ,
 `parameters` text NOT  NULL ,
 `response_code` char( 5  )  NOT  NULL  COMMENT  'HTTP response code',
 `response` text NOT  NULL  COMMENT  'Complete Response Data',
 `call_date_time` datetime NOT  NULL ,
 `response_date_time` datetime NOT  NULL ,
 `token_id` int( 11  )  NOT  NULL ,
 PRIMARY  KEY (  `id`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1;";

$sql[] = "CREATE  TABLE `fmh_iportal_api_token_log` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
 `token` varchar( 64  )  NOT  NULL ,
 `user_id` int( 11  )  NOT  NULL ,
 `create_date_time` datetime NOT  NULL ,
 `expire_date_time` datetime NOT  NULL ,
 `usertype` tinyint( 4  )  NOT  NULL ,
 PRIMARY  KEY (  `id`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1;";

$sql[] = "CREATE  TABLE  `fmh_iportal_api_users` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
 `username` varchar( 50  )  NOT  NULL ,
 `password` varchar( 50  )  NOT  NULL ,
 `locked` tinyint( 1  )  NOT  NULL DEFAULT  '0',
 `created_time` datetime NOT  NULL ,
 `locked_time` datetime NOT  NULL ,
 PRIMARY  KEY (  `id`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1;
 ";


$problemId = "CREATE  TABLE  `fmh_iportal_med_fields` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
 `name` varchar( 255  )  NOT  NULL ,
 `type` tinyint( 1  )  NOT  NULL  COMMENT  '1 - Patient, 2 - Family',
 `sec_id` tinyint( 4  )  NOT  NULL  COMMENT  '1 - Ocular, 2 - Gen. Health',
 `field_key` int( 11  )  NOT  NULL ,
 PRIMARY  KEY (  `id`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8;
" ;

$respProblemId = imw_query($problemId) or $error[] = imw_error();
if( $respProblemId )
{
	$problems = "INSERT INTO `fmh_iportal_med_fields` (`id`, `name`, `type`, `sec_id`, `field_key`) VALUES
					(1, 'Cataracts', 1, 1, 5),
					(2, 'Dry Eyes', 1, 1, 1),
					(3, 'Glaucoma', 1, 1, 3),
					(4, 'Retinal Detachment', 1, 1, 4),
					(5, 'Macular Degeneration', 1, 1, 2),
					(6, 'Keratoconus', 1, 1, 6),
					(7, 'Others', 1, 1, 0),
					(8, 'Cataracts', 2, 1, 5),
					(9, 'Dry Eyes', 2, 1, 1),
					(10, 'Glaucoma', 2, 1, 3),
					(11, 'Keratoconus', 2, 1, 6),
					(12, 'Macular Degeneration', 2, 1, 2),
					(13, 'Retinal Detachment', 2, 1, 4),
					(14, 'Others', 2, 1, 0),
					(15, 'High Blood Pressure', 1, 2, 1),
					(16, 'Heart Problem', 1, 2, 2),
					(17, 'Arthritis', 1, 2, 7),
					(18, 'Lung Problems', 1, 2, 4),
					(19, 'Stroke', 1, 2, 5),
					(20, 'Thyroid Problems', 1, 2, 6),
					(21, 'Diabetes', 1, 2, 3),
					(22, 'LDL', 1, 2, 13),
					(23, 'Ulcers', 1, 2, 8),
					(24, 'Cancer', 1, 2, 14),
					(26, 'High Blood Pressure', 2, 2, 1),
					(27, 'Heart Problem', 2, 2, 2),
					(28, 'Arthritis', 2, 2, 7),
					(29, 'Lung Problems', 2, 2, 4),
					(30, 'Stroke', 2, 2, 5),
					(31, 'Thyroid Problems', 2, 2, 6),
					(32, 'Diabetes', 2, 2, 3),
					(33, 'LDL', 2, 2, 13),
					(34, 'Ulcers', 2, 2, 8),
					(35, 'Cancer', 2, 2, 14);";
	imw_query($problems) or $error[] = imw_error();
}

foreach($sql as $qry)
{
	imw_query($qry) or $error[] = imw_error();
}

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 1 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 1 Success.</b>";
	$color = "green";	
}

?>

<html>
<head>
<title>Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>