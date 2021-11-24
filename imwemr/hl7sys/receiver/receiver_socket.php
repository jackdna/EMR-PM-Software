<?php
/*
File: listener_socket.php
Purpose:  A common socket listener to access inbound HL7 messages.
Access Type: Direct Access
*/

set_time_limit(0);
$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
$listenerCount = 0;
if( $argv[1] )
		{
	$practicePath = trim($argv[1]);
	if( isset($argv[2]) && (int)$argv[2] > 0)
	{
		$listenerCount = (int)$argv[2];
	}
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/commonFunctions.php');

//error_reporting(0);
//ini_set('display_errors', 0);


/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$address	= constant('LISTENING_IP');
$port		= ( isset($GLOBALS['HL7_LISTENING'][$listenerCount]) ) ? $GLOBALS['HL7_LISTENING'][$listenerCount]['PORT'] : 0;


if( ($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false )
{
	$message = "socket_create() failed: reason: ".socket_strerror( socket_last_error() );
	LogResponse( $message );
}

if( socket_bind($sock, $address, $port) === false )
{
	$message = "socket_bind() failed: reason: ".socket_strerror( socket_last_error($sock) );
	LogResponse($message);
}

if( socket_listen($sock, 5) === false )
{
    $message = "socket_listen() failed: reason: ".socket_strerror( socket_last_error($sock) );
	LogResponse($message);
}

do{

	/*Close Previous Connection - If unable to Listen*/
	if( socket_listen($sock, 5) === false )
	{
		$message = "socket_listen() failed: reason: ".socket_strerror( socket_last_error($sock) );
		LogResponse($message);

		@socket_close($sock);
		unset($sock);
		@socket_close($msgsock);
		unset($msgsock);

		if( ($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false )
		{
			$message = "socket_create() failed: reason: ".socket_strerror( socket_last_error() );
			LogResponse( $message );
		}

		if( socket_bind($sock, $address, $port) === false )
		{
			$message = "socket_bind() failed: reason: ".socket_strerror( socket_last_error($sock) );
			LogResponse($message);
		}

		if( socket_listen($sock, 5) === false )
		{
		    $message = "socket_listen() failed: reason: ".socket_strerror( socket_last_error($sock) );
			LogResponse($message);
		}
	}

    if( ($msgsock = @socket_accept($sock)) === false )
    {
        $message = "socket_accept() failed: reason: ".socket_strerror( socket_last_error($sock) );
        LogResponse($message);
        break;
    }

    /*Buffer Container*/
	$bufAll = '';

    do{

        if( false === ( $buf = @socket_read($msgsock, 2048, PHP_NORMAL_READ) ) ) 
		{
            $message = "socket_read() failed: reason: ".socket_strerror( socket_last_error($msgsock) );
            LogResponse($message);
            break;
        }

        /*continue untill some data captured in buffer*/
        // if ( !$buf = trim($buf) )
        // {
        //     continue;
        // }

        /*Log data Read and send to Hl7 Parse for Logginf in to DB.*/
        try{
        	
			LogResponse($buf);

			$bufAll.="".$buf;

			/*Log Message - If File separator character detected in the buffer text*/
			if( strpos($buf,"\034")!==false )
			{
				$bufAll_tr = trim($bufAll);

				if( !empty($bufAll_tr) )
				{
					$res = do_post_request( $bufAll );
					$res = chr(11).$res.chr(28).chr(13);	/*Parsing Response - ACK*/

					$bufAll = '';	/*Clear buffer container*/
					socket_write($msgsock, $res, strlen($res));	/*Send ACK back to the Client*/
				//	sleep(0);
				}
			}
		}
		catch(Exception $e){
			$msg = 'Caught exception: '.  $e->getMessage(). "\n";
			LogResponse($msg);
		}
    }while( true );

    /*Log Data if connection interrupted*/
	if(!empty($bufAll)){
		$res = do_post_request($bufAll);
		LogResponse('Message Posted to parser. Connection interrupted');
		$bufAll="";
	}

    socket_close($msgsock);

}while( true );

socket_close($sock);
?>