<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

/**
 * Connection Details
 */
require_once(__DIR__.'/config.php');

/**
 * Autoloader for contrinuted libraries
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Rabbit Mq standard libraries
 */
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPIOException;


/**
 * Message Logging
 */
$log_path = dirname(__FILE__).'/../../admin/pdfFiles';
$log_path = realpath($log_path);
$log_path = $log_path.'/sync_api_log';

$flagFile = $log_path.'/flag.txt';

do
{
    $flag = (int)file_get_contents($flagFile);
    $dir_check_flag = intval($flag);
    
    if( $dir_check_flag && $dir_check_flag === (int)1 )
    {
        /**
         * Log storage based on Time stamp
         */
        $log_dir = date('Y_m_d');
        $log_directory = $log_path.'/'.$log_dir;

        $error_log = $log_directory.'/error.log';

        /**
         * Connection object and channel
         */
        do
        {
            $isConnected = false;

            try
            {
                $connection = new AMQPStreamConnection($inteHost, $intePort, $inteUser, $intePass); //[host, port, user, pass]
                $channel = $connection->channel();
                $channel->exchange_declare($inteExchange, $inteExchangeType, false, $inteDurable, false);

                $isConnected  =true;

                break(1);
            }
            catch( AMQPIOException $exception )
            {
                $error = date("Y-m-d H:i:s").' '.$exception->getMessage();
                $error = trim($error);
                file_put_contents($error_log, $error."\n", FILE_APPEND);
            }
            catch( Exception $exception )
            {
                $error = date("Y-m-d H:i:s").' '.$exception->getMessage();
                $error = trim($error);
                file_put_contents($error_log, $error."\n", FILE_APPEND);
            }

            sleep(5);

        }while( $isConnected === false );

        
        
        /**
         * Check if archieve directory exists
         */
        $archieve_directory = $log_directory.'/archieve';
        if( !is_dir($archieve_directory) )
        {
            mkdir($archieve_directory, 0700, true);
        }
       
        $files = scandir($log_directory);

        foreach( $files as $file )
        {
            if( in_array($file, array('.', '..')) || $file === 'error.log' )
            {
                continue;
            }

            if( is_dir($log_directory.'/'.$file) )
            {
                continue;
            }

            $messageData = file_get_contents( $log_directory.'/'.$file );
            $messageData = trim($messageData);

            do
            {
                $messageSent = false;
                try
                {
                    $msg = new AMQPMessage($messageData);
                    $channel->basic_publish($msg, $inteExchange,$inteOutboundRoutingKey);

                    $messageSent = true;

                    /**
                     * Move the message file to archieve directory
                     */
                    rename( $log_directory.'/'.$file, $archieve_directory.'/'.$file);

                    break(1);
                }
                catch( Exception $exception )
                {
                    $error = date("Y-m-d H:i:s").' '.$exception->getMessage();
                    $error = trim($error);
                    file_put_contents($error_log, $error."\n", FILE_APPEND);
                }
            }
            while( $messageSent === false );
        }

        /* $channel->close();
        $connection->close(); */

        file_put_contents($flagFile, 0);
    }
    else
    {
        sleep(5);
    }

}while( true );