<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
//error_reporting(-1);
//ini_set("display_errors",-1);
if(trim($_SERVER["DOCUMENT_ROOT"])) {
	session_start();//RUN FILE THROUGH APPLICATION WITH SESSION REQUIRED
}
include_once(dirname(dirname(__FILE__))."/common/conDb.php");
$rootServerPath 	= str_ireplace("\\","/",dirname(dirname(dirname(__FILE__))));
$selected_date 		= date("Y-m-d");
$fileDate 			= date("Ymd",strtotime($selected_date));
$showDate 			= date("m-d-Y",strtotime($selected_date));
$inboundDir 		= 'inbound';

$local_directory	= $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles/supplies_log";
if(!is_dir($local_directory)){		
	mkdir($local_directory);
	chown($local_directory, 'apache');
	chgrp($local_directory, 'apache');
}
if(!is_dir($local_directory.'/'.$inboundDir)){		
	mkdir($local_directory.'/'.$inboundDir);
	chown($local_directory.'/'.$inboundDir, 'apache');
	chgrp($local_directory.'/'.$inboundDir, 'apache');
}
$inboundDirPath 	= $local_directory.'/'.$inboundDir.'/'.$showDate;
$inboundDirPathError= $local_directory.'/'.$inboundDir.'/error_log';
if(!is_dir($inboundDirPath)){		
	mkdir($inboundDirPath);
	chown($inboundDirPath, 'apache');
	chgrp($inboundDirPath, 'apache');
}
if(!is_dir($inboundDirPathError)){		
	mkdir($inboundDirPathError);
	chown($inboundDirPathError, 'apache');
	chgrp($inboundDirPathError, 'apache');
}
$log_file_name		= $inboundDirPath.'/receive_log_'.$selected_date.'.log';
$error_file_name	= $inboundDirPathError.'/hybrent_error_'.$fileDate.'.log';
if(file_exists($error_file_name)) {
	unlink($error_file_name);	
}
function LogSuppImp($msgLog,$logType="") {
	global $log_file_name;
	global $error_file_name;
	$fileLog = $log_file_name;
	if($logType == "error") {
		$fileLog = $error_file_name;
	}
	file_put_contents($fileLog, "\n============================\n".date("Y-m-d H:i:s")."\n".$msgLog, FILE_APPEND);	//ADD INFO IN LOG FILE		
	chown($fileLog, 'apache');
	chgrp($fileLog, 'apache');
}
function replaceArrayToStringSupplies($arr = array()) {
    $newArr = array();
    foreach($arr as $key=>$value)
    {
        if (is_array($value))
        {
           unset($arr[$key]);

            //Is it an empty array, make it a string
            if (empty($value)) {
                $newArr[$key] = '';
            }
            else {
                $newArr[$key] = replaceArrayToStringSupplies($value);
            }

        }
        else {
            $newArr[$key] = $value; 
        }

    }
    return $newArr;

}

$surgeryQry= "select * from surgerycenter where surgeryCenterId=1";
$surgeryRes= imw_query($surgeryQry) or die($surgeryQry.imw_error());
if(imw_num_rows($surgeryRes)>0) {
	$surgeryRow			= imw_fetch_array($surgeryRes);
	$ftp_server 		= $surgeryRow["suppliesHostName"];
	$strServerPort 		= $surgeryRow["suppliesPortNumber"];
	$user 				= $surgeryRow["suppliesUsername"];
	$pass 				= $surgeryRow["suppliesPassword"];
	$filePathFromSftp 	= $surgeryRow["suppliesPathFromSftp"];
	$filePathFromSftp 	= str_ireplace("\\","/",$filePathFromSftp);
	$filePathFromSftp 	= str_ireplace("{DATE}",$fileDate,$filePathFromSftp);
	$filePathFromSftpArr = pathinfo($filePathFromSftp);	
	$switchDir 			= $filePathFromSftpArr["dirname"];
	$fileName 			= $filePathFromSftpArr["basename"];
	$fileExt 			= $filePathFromSftpArr["extension"];
	if(strtolower($fileExt)=="xml") { //ONLY XML FILE ALLOWED
		$destFilePath 		= $inboundDirPath.'/'.$fileName; //LOCAL FILE PATH
		$sourceFilePath		= $fileName;  //SERVER FILE PATH
	}
	$strTimeOut			= 1000;//seconds
}
if(!trim($ftp_server) || !trim($strServerPort) || !trim($user) || !trim($pass) || !trim($sourceFilePath)) {
	echo $fileMsg = 'SFTP Credentials Required';
	LogSuppImp($fileMsg);
	exit();	
}

$ftp_conn 	= ftp_ssl_connect($ftp_server,$strServerPort,$strTimeOut) or die("Could not connect to $ftp_server");
$login 		= ftp_login($ftp_conn, $user, $pass);
if($login){
	ftp_chdir($ftp_conn,$switchDir); // changing to directory after login.	
	$pwd 	= ftp_pwd($ftp_conn);
	if(stristr($pwd,$switchDir)){
		$fileMsg = 'Switched to directory "'.$switchDir.'"';
		LogSuppImp($fileMsg);
		//Set passive mode
		ftp_pasv( $ftp_conn, true );
		if(ftp_get($ftp_conn, $destFilePath, $sourceFilePath, FTP_BINARY)){
			chown($destFilePath, 'apache');
			chgrp($destFilePath, 'apache');
			$fileMsg = 'File successfully downloaded from FTP. ';
			LogSuppImp($fileMsg);
			//START CODE TO SAVE SUPPLIES
			$local_file_path = $destFilePath;
			$xmlArr = array();
			$cnt = 0;
			if(file_exists($local_file_path)) {
				//START CODE TO CONVERT XML TO ASSOCIATIVE ARRAY
				//$xmlContent 	= file_get_contents($local_file_path);
				//$ob			= simplexml_load_string($xmlContent);
				$ob				= simplexml_load_file($local_file_path, 'SimpleXMLElement', LIBXML_NOCDATA);
				$json  			= json_encode($ob);
				$xmlArr 		= json_decode($json, true);
				$xmlArr 		= replaceArrayToStringSupplies($xmlArr);
				//END CODE TO CONVERT XML TO ASSOCIATIVE ARRAY
				
				if(count($xmlArr)>0) {
					//START GET CATEGORY ID OF OTHER
					$catId 			= "";
					$catIdArr 		= array();
					$catQry 		= "SELECT id,`name` FROM supply_categories ORDER BY id";
					$catRes			= imw_query($catQry) or die($catQry.imw_error());
					if(imw_num_rows($catRes)>0) {
						while($catRow = imw_fetch_array($catRes)) {
							$catId 		= $catRow["id"];
							$catName	= trim($catRow["name"]);
							$catIdArr[strtolower($catName)] = $catId;
						}
					}
					//END GET CATEGORY ID OF OTHER
					
					$suppliesUsedIdArr 	= array();
					$chkQry 			= "SELECT suppliesUsedId, supply_quick_code FROM predefine_suppliesused WHERE supply_quick_code != '' ORDER BY suppliesUsedId DESC";
					$chkRes				= imw_query($chkQry) or die($chkQry.imw_error());
					if(imw_num_rows($chkRes)>0) {
						while($chkRow 	= imw_fetch_array($chkRes)) {
							$suppliesUsedIdArr[trim(strtolower($chkRow["supply_quick_code"]))] 	= $chkRow["suppliesUsedId"];
						}
					}
					
					$suppliesItemDetailArr 	= array();
					$sidQry 			= "SELECT item_id, suppliesUsedId, supply_quick_code, stock_id, serial_number FROM predefine_suppliesused_item_detail ORDER BY item_id ASC";
					$sidRes				= imw_query($sidQry) or die($sidQry.imw_error());
					if(imw_num_rows($sidRes)>0) {
						while($sidRow 	= imw_fetch_array($sidRes)) {
							$suppliesItemDetailArr[trim(strtolower($sidRow["supply_quick_code"]))][trim(strtolower($sidRow["serial_number"]))] 	= $sidRow["item_id"];
						}
					}
					
					
					//START CODE TO IMPORT SUPPLIES
					foreach($xmlArr as $supplyInfosArr) {
						foreach($supplyInfosArr as $supplyInfoArr) {
							$supplyName 		= $supplyInfoArr['SupplyName'];
							$supplyQuickCode 	= trim($supplyInfoArr['SupplyQuickCode']);
							$supplyBillable 	= $supplyInfoArr['SupplyBillable'];
							$supplyActiveStatus = $supplyInfoArr['SupplyActiveStatus'];
							$supplyUsageUnit 	= $supplyInfoArr['SupplyUsageUnit'];
							
							$deleteStatus 		= '1';
							if($supplyActiveStatus=='1') {
								$deleteStatus 	= '0';	
							}
							$category 			= trim($supplyInfoArr['Category']);
							$categoryId 		= $catIdArr[strtolower($category)];
							if(!$categoryId) { //INSERT CATEGORY IF NOT EXISTS
								$catSavQry 		= "INSERT INTO supply_categories SET `name` = '".$category."'";
								$catSavRes		= imw_query($catSavQry) or die($catSavQry.imw_error());
								$categoryId 	= imw_insert_id();
							}
							
							if(trim($supplyName) && trim($supplyQuickCode)) {
								$suppliesUsedId = $suppliesUsedIdArr[strtolower($supplyQuickCode)];
								$savQry 		= " INSERT INTO ";
								$savWhr 		= "";
								if($suppliesUsedId) {
									$savQry 	= " UPDATE ";
									$savWhr 	= " WHERE suppliesUsedId = '".$suppliesUsedId."' ";	 
								}
								$savQry			.=" predefine_suppliesused SET cat_id = '".$categoryId."', `name` = '".$supplyName."', supply_quick_code = '".$supplyQuickCode."', supply_billable = '".$supplyBillable."', deleted = '".$deleteStatus."', supply_usage_unit = '".$supplyUsageUnit."' ".$savWhr;
								$savRes			= imw_query($savQry) or die($savQry.imw_error());
								file_put_contents($log_file_name, "\n============================\n".date("Y-m-d H:i:s")."\n".$savQry, FILE_APPEND);	//ADD INFO IN LOG FILE		
								
								
								$cnt++;
								
								//START CODE TO SAVE ITEM DETAILS
								if(strtolower($category)!="implant") {
									continue;	
								}
								if(!$suppliesUsedId) {
									$suppliesUsedId = imw_insert_id();	
								}
								if(count($supplyInfoArr["ItemDetails"])>0) {
									foreach($supplyInfoArr["ItemDetails"] as $itemDetailsArr) {
										foreach($itemDetailsArr as $itemDetailArr) {
											$stockId 		= $itemDetailArr['StockId'];
											$serialNumber	= trim($itemDetailArr['SerialNumber']);
											$lotNumber 		= $itemDetailArr['LotNumber'];
											$expiration 	= $itemDetailArr['Expiration'];
											if(!$serialNumber) {
												LogSuppImp("Serial number does not exist for "."\n"."Supply Item - ".$supplyName."\n"."Quick Code - ".$supplyQuickCode."\n"."Stock ID - ".$stockId,"error");
											}
											$item_id 		= $suppliesItemDetailArr[strtolower($supplyQuickCode)][strtolower($serialNumber)];
											$savItemQry 	= " INSERT INTO ";
											$savItemWhr 	= "";
											if($item_id) {
												$savItemQry	= " UPDATE ";
												$savItemWhr	= " WHERE item_id = '".$item_id."' ";	 
											}
											$savItemQry		.=" predefine_suppliesused_item_detail 
																SET suppliesUsedId = '".$suppliesUsedId."', 
																supply_quick_code = '".$supplyQuickCode."', 
																stock_id = '".$stockId."', 
																serial_number = '".$serialNumber."', 
																lot_number = '".$lotNumber."', 
																expiration_date = '".$expiration."' ".$savItemWhr;
											$savItemRes		= imw_query($savItemQry) or die($savItemQry.imw_error());
											if(!$item_id) {
												$item_id = imw_insert_id();
												$suppliesItemDetailArr[strtolower($supplyQuickCode)][strtolower($serialNumber)] = $item_id;
											}
											file_put_contents($log_file_name, "\n***************\n".date("Y-m-d H:i:s")."\n".$savItemQry, FILE_APPEND);
										
										}
									}
								}else {
									LogSuppImp("Item details do not exist for "."\n"."Supply Item - ".$supplyName."\n"."Quick Code - ".$supplyQuickCode,"error");	
								}
								//END CODE TO SAVE ITEM DETAILS
							}
						}
					}
					//END CODE TO IMPORT SUPPLIES
				}
			}else {
				echo $fileMsg = 'File not found to import supplies';	
				LogSuppImp($fileMsg);
				exit();
			}
			if($cnt>0) {
				echo $fileMsg = 'Success'; //FILE DOWNLOADED SUCCESSFULLY	
				LogSuppImp($fileMsg);
			}
			//END CODE TO SAVE SUPPLIES
			
		}else{
			echo $fileMsg = 'File download failed.';	
			LogSuppImp($fileMsg);
		}
	}else{
		echo $fileMsg = 'Not able to switch to directory "'.$switchDir.'". ';	
		LogSuppImp($fileMsg);
	}
}else {
	echo $fileMsg = "Couldn't connect as $user\n";	
	LogSuppImp($fileMsg);
}


?>
