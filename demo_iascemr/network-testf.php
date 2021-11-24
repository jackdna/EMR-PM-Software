<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$filename ='\\\192.168.0.3\\Documents\\testNetwork.txt';
$somecontent = "Current Date On Server is".date("Y-m-d")."\n";
$somecontent.="Testing Network File Write Function \n";
// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {
 
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'a')) {
         echo "Cannot open file ($filename)";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }
    
    echo "<br>Success, wrote ($somecontent) to file ($filename)";
    echo("<br>File is at this location<b>".$filename."</b>");
    fclose($handle);

} else {
    echo "The file $filename is not writable";
}

?> 
