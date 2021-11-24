<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	include('adodb5/adodb.inc.php'); 
  $db =& ADONewConnection('odbc_mssql');	
  $dsn = "Driver={SQL Server};Server=dd-AQ43EHYG;Database=hospital;";	
  $db->Connect($dsn,'sa','123456');
 /* 
  $db =& ADONewConnection('mssql');	
  $db->Execute('localhost', 'userid', 'password', 'northwind');
  */
  $sql="SELECT * FROM UserAccount";
  
  $rs = $db->Execute($sql);
  /*foreach($rs as $k => $row) {
  		echo "r1=".$row[0]." r2=".$row[1]."<br>";	
		}*/
		print '<pre>';
		print_r($rs);
?>