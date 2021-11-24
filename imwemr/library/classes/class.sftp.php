<?php
/* EXAMPLE USAGES
try
{
    $sftp = new SFTPConnection("localhost", 22);
    $sftp->login("username", "password");
    $sftp->uploadFile("/tmp/to_be_sent", "/tmp/to_be_received");
}
catch (Exception $e)
{
    echo $e->getMessage() . "\n";
}


*/



class SFTPConnection
{
    private $connection;
    private $sftp;

    public function __construct($host, $port=22)
    {
        $this->connection = @ssh2_connect($host, $port);
        if (! $this->connection)
            throw new Exception("Could not connect to $host on port $port.");
    }

    public function login($username, $password)
    {
        if (! @ssh2_auth_password($this->connection, $username, $password))
            throw new Exception("Could not authenticate with username $username " .
                                "and password $password.");

        $this->sftp = @ssh2_sftp($this->connection);
        if (! $this->sftp)
            throw new Exception("Could not initialize SFTP subsystem.");
    }

    public function uploadFile($local_file, $remote_file)
    {
        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');

        if (! $stream)
            throw new Exception("Could not open file: $remote_file");

        $data_to_send = @file_get_contents($local_file);
        if ($data_to_send === false)
            throw new Exception("Could not open local file: $local_file.");

        if (@fwrite($stream, $data_to_send) === false)
            throw new Exception("Could not send data from file: $local_file.");

        @fclose($stream);
    }
	
	public function receiveFile($remote_file, $local_file)
    {
        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'r');
        if (! $stream)
            throw new Exception("Could not open file: $remote_file");
        $size = $this->getFileSize($remote_file);           
        $contents = '';
        $read = 0;
        $len = $size;
        while ($read < $len && ($buf = fread($stream, $len - $read))) {
          $read += strlen($buf);
          $contents .= $buf;
        }       
        file_put_contents ($local_file, $contents);
        @fclose($stream);
    }

    public function getFileSize($file){
      $sftp = $this->sftp;
        return filesize("ssh2.sftp://$sftp$file");
    }
	
	public function scanFilesystem($dir) {
		$tempArray = array();
		$handle = opendir($dir);
	  // List all the files
		while (false !== ($file = readdir($handle))) {
			if (substr("$file", 0, 1) != "."){
				   if(is_dir($file)){
					$tempArray[$file] = $this->scanFilesystem("$dir/$file");
				} else {
					$tempArray[]=$file;
				}
			}
		}
		closedir($handle);
		return $tempArray;
	}
}

?>