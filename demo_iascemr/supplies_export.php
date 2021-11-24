<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
//error_reporting(-1);
//ini_set("display_errors",-1);
$newLineTag = " \n";
if(trim($_SERVER["DOCUMENT_ROOT"])) {
	session_start();//RUN FILE THROUGH APPLICATION WITH SESSION REQUIRED
	$newLineTag 	= " <br>";
}
include_once("common/conDb.php");
$rootServerPath 	= str_ireplace("\\","/",dirname(dirname(__FILE__)));
$selected_date 		= $_REQUEST["dos"];
if(!trim($selected_date)) {
	$selected_date 	= date("Y-m-d");
}
$ascidReq = trim($_REQUEST["ascid"]);
$fileDate 			= date("Ymd",strtotime($selected_date));
$showDate 			= date("m-d-Y",strtotime($selected_date));
$outboundDir 		= 'outbound';

$local_directory	= $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles/supplies_log";
if(!is_dir($local_directory)){		
	mkdir($local_directory);
	chown($local_directory, 'apache');
	chgrp($local_directory, 'apache');
}
if(!is_dir($local_directory.'/'.$outboundDir)){		
	mkdir($local_directory.'/'.$outboundDir);
	chown($local_directory.'/'.$outboundDir, 'apache');
	chgrp($local_directory.'/'.$outboundDir, 'apache');

}
$outboundDirPath 	= $local_directory.'/'.$outboundDir.'/'.$showDate;
if(!is_dir($outboundDirPath)){		
	mkdir($outboundDirPath);
	chown($outboundDirPath, 'apache');
	chgrp($outboundDirPath, 'apache');
}

$log_file_name		= $outboundDirPath.'/send_log_'.$selected_date.'.log';
function LogSuppExp($msgLog) {
	global $log_file_name;
	file_put_contents($log_file_name, "\n============================\n".date("Y-m-d H:i:s")."\n".$msgLog, FILE_APPEND);	//ADD INFO IN LOG FILE		
	chown($log_file_name, 'apache');
	chgrp($log_file_name, 'apache');
	
}
$surgeryQry= "select * from surgerycenter where surgeryCenterId=1";
$surgeryRes= imw_query($surgeryQry) or die($surgeryQry.imw_error());
if(imw_num_rows($surgeryRes)>0) {
	$surgeryRow			= imw_fetch_array($surgeryRes);
	$ftp_server 		= $surgeryRow["suppliesHostName"];
	$strServerPort 		= $surgeryRow["suppliesPortNumber"];
	$user 				= $surgeryRow["suppliesUsername"];
	$pass 				= $surgeryRow["suppliesPassword"];
	$filePathToSftp 	= $surgeryRow["suppliesPathToSftp"];
	$filePathToSftp 	= str_ireplace("\\","/",$filePathToSftp);
	$filePathToSftpArr 	= pathinfo($filePathToSftp);
	$switchDir 			= $filePathToSftpArr["basename"];
	if($filePathToSftpArr["extension"]) {
		$switchDir 		= $filePathToSftpArr["dirname"];
	}else {
		$switchDir		= $filePathToSftp;
		if(substr($switchDir, -1,1)=='/') {
			$switchDir	= substr($switchDir, 0,-1);
		}
	}
	$strTimeOut			= 1000;//seconds
}
if(!trim($ftp_server) || !trim($strServerPort) || !trim($user) || !trim($pass) || !trim($switchDir)) {
	echo $fileMsg = 'SFTP Credentials Required';
	echo $newLineTag;
	LogSuppExp($fileMsg);
}

$ftp_conn 	= ftp_ssl_connect($ftp_server,$strServerPort,$strTimeOut) or die("Could not connect to $ftp_server");
$login 		= ftp_login($ftp_conn, $user, $pass);
if($login){
	ftp_chdir($ftp_conn,$switchDir); // changing to directory after login.	
	$pwd 	= ftp_pwd($ftp_conn);
	if(stristr($pwd,$switchDir)){
		echo $fileMsg = 'Switched to directory "'.$switchDir.'"';
		echo $newLineTag;
		LogSuppExp($fileMsg);
		//Set passive mode
		ftp_pasv( $ftp_conn, true );
		//START
		$andQry = " AND pc.dos = '".$selected_date."' ";
		if($ascidReq) {
			$andQry = " AND pc.ascId = '".$ascidReq."' ";
		}
		$qry = "SELECT ps.*, ops.suppName, ops.suppList, ops.predefine_supp_id, pc.ascId, op.iol_serial_number FROM operatingroomrecords op 
                INNER JOIN patientconfirmation pc ON (pc.patientConfirmationId = op.confirmation_id ".$andQry." AND pc.ascId != '0') 
				LEFT JOIN operatingroomrecords_supplies ops ON (ops.confirmation_id = op.confirmation_id AND ops.suppChkStatus='1') 
                LEFT JOIN predefine_suppliesused ps ON (ps.suppliesUsedId = ops.predefine_supp_id) 
				WHERE op.operatingRoomRecordsId !='0' 
				ORDER BY pc.ascId, ops.confirmation_id, ops.suppName";
		$res = imw_query($qry) or die($qry.imw_error());
		if(imw_num_rows($res)>0) {
			
			$suppliesItemDetailArr 	= array();
			$sidQry 			= "SELECT si.item_id, si.suppliesUsedId, si.supply_quick_code, si.stock_id, si.serial_number, si.lot_number, ps.name 
									FROM predefine_suppliesused_item_detail si 
									INNER JOIN predefine_suppliesused ps ON (ps.suppliesUsedId  = si.suppliesUsedId AND ps.supply_quick_code!='') 
									ORDER BY si.item_id ASC";
			$sidRes				= imw_query($sidQry) or die($sidQry.imw_error());
			$suppliesItemDetailArr = array();
			if(imw_num_rows($sidRes)>0) {
				while($sidRow 	= imw_fetch_array($sidRes)) {
					$suppliesItemDetailArr[trim(strtolower($sidRow["serial_number"]))] 	= $sidRow;
				}
			}
			
			$dataXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>";
			$dataXML .= "<SupplyInfos>";
			$cnt=0;
			
			$dataExistBool = false;
			while($row = imw_fetch_assoc($res)) {
				$cnt++;
				if($cnt == 1) {
					$ascIdArr[] = $row['ascId'];	
				}
				if(!in_array($row['ascId'],$ascIdArr)) {
					$ascIdArr[] 	= $row['ascId'];
					$dataXML 		.= $implantXML; //ADD IMPLANT SUPPLIES AT THE END
					$dataXML 		.= "</SupplyInfos>";
					if($dataExistBool == false) {
						//NO RECORD FOUND
					}elseif(file_put_contents($sourceFilePath, $dataXML)) {
						chown($sourceFilePath, 'apache');
						chgrp($sourceFilePath, 'apache');
						if(ftp_put($ftp_conn, $destFilePath, $sourceFilePath, FTP_BINARY)){
							echo $fileMsg = 'File '.$destFilePath.' successfully uploaded to FTP. ';
							echo $newLineTag;
							LogSuppExp($fileMsg);
						}else{
							echo $fileMsg = 'File upload failed.';
							echo $newLineTag;
							LogSuppExp($fileMsg);
						}
					}else {
						echo $fileMsg = 'File not found.';
						echo $newLineTag;
						LogSuppExp($fileMsg);
					}
						
					$dataXML  = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>";
					$dataXML .= "<SupplyInfos>";
				}

				$filename 		= 'IMEDICWARE_'.$fileDate.'_'.$row['ascId'].'.xml';
				$sourceFilePath = $outboundDirPath.'/'.$filename;
				$destFilePath 	= $filename;
				$dataExistBool = false;
				if(trim($row['suppName'])) {
					$dataExistBool = true;
					$dataXML .= 	"<SupplyInfo>";
					$dataXML .= 		"<SupplyName>".$row['suppName']."</SupplyName>";
					$dataXML .= 		"<SupplyItemNumber>".$row['supply_quick_code']."</SupplyItemNumber>";
					$dataXML .= 		"<SupplyAdjustments>";
					$dataXML .= 			"<Adjustment>";
					$dataXML .= 				"<TransactionCode></TransactionCode>";
					$dataXML .= 				"<QuantityUsed>".str_ireplace("X","",$row['suppList'])."</QuantityUsed>";
					$dataXML .= 				"<ItemDetails>";
					$dataXML .= 					"<ItemDetail>";
					$dataXML .= 						"<StockId>".$row['stock_id']."</StockId>";
					$dataXML .= 						"<serialnumber>".$row['serial_number']."</serialnumber>";
					$dataXML .= 						"<lotnumber>".$row['lot_number']."</lotnumber>";
					$dataXML .= 					"</ItemDetail>";
					$dataXML .= 				"</ItemDetails>";
					$dataXML .= 			"</Adjustment>";
					$dataXML .= 		"</SupplyAdjustments>";
					$dataXML .= 	"</SupplyInfo>";
				}
				$implantXML = "";
				$implantRow = array();
				if(trim($row['iol_serial_number']) && $suppliesItemDetailArr[trim(strtolower($row['iol_serial_number']))]) {
					$dataExistBool = true;
					$implantRow  = $suppliesItemDetailArr[trim(strtolower($row['iol_serial_number']))];
					$implantXML  = 	"<SupplyInfo>";
					$implantXML .= 		"<SupplyName>".$implantRow['name']."</SupplyName>";
					$implantXML .= 		"<SupplyItemNumber>".$implantRow['supply_quick_code']."</SupplyItemNumber>";
					$implantXML .= 		"<SupplyAdjustments>";
					$implantXML .= 			"<Adjustment>";
					$implantXML .= 				"<TransactionCode></TransactionCode>";
					$implantXML .= 				"<QuantityUsed>1</QuantityUsed>";
					$implantXML .= 				"<ItemDetails>";
					$implantXML .= 					"<ItemDetail>";
					$implantXML .= 						"<StockId>".$implantRow['stock_id']."</StockId>";
					$implantXML .= 						"<serialnumber>".$implantRow['serial_number']."</serialnumber>";
					$implantXML .= 						"<lotnumber>".$implantRow['lot_number']."</lotnumber>";
					$implantXML .= 					"</ItemDetail>";
					$implantXML .= 				"</ItemDetails>";
					$implantXML .= 			"</Adjustment>";
					$implantXML .= 		"</SupplyAdjustments>";
					$implantXML .= 	"</SupplyInfo>";
				}
			}
			$dataXML .= $implantXML; //ADD IMPLANT SUPPLIES AT THE END
			$dataXML .= "</SupplyInfos>";
			if($dataExistBool == false) {
				//NO RECORD FOUND
			}elseif(file_put_contents($sourceFilePath, $dataXML)) {
				chown($sourceFilePath, 'apache');
				chgrp($sourceFilePath, 'apache');
				if(ftp_put($ftp_conn, $destFilePath, $sourceFilePath, FTP_BINARY)){
					echo $fileMsg = 'File '.$destFilePath.' successfully uploaded to FTP. ';
					echo $newLineTag;
					LogSuppExp($fileMsg);
				}else{
					echo $fileMsg = 'File upload failed.';
					echo $newLineTag;
					LogSuppExp($fileMsg);
				}
			}else {
				echo $fileMsg = 'File not found.';
				echo $newLineTag;
				LogSuppExp($fileMsg);
			}
		}else {
			$dataExistBool = false;
		}
		if($dataExistBool == false) {
			echo $fileMsg = 'No record found.';
			echo $newLineTag;
			LogSuppExp($fileMsg);
		}
		//END
	}else{
		echo $fileMsg = 'Not able to switch to directory "'.$switchDir.'"';
		echo $newLineTag;
		LogSuppExp($fileMsg);
	}
}else {
	echo $fileMsg = "Couldn't connect as $user\n";
	echo $newLineTag;
	LogSuppExp($fileMsg);	
}
?>
