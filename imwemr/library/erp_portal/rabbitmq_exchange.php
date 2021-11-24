<?php
use phpAmqpLib\Connection\AMQPStreamConnection;
use phpAmqpLib\Connection\AMQPSSLConnection;
use phpAmqpLib\Message\AMQPMessage;
use phpAmqpLib\Exception\AMQPIOException;

use phpAmqpLib\Exception\AMQPTimeoutException;
use phpAmqpLib\Exchange\AMQPExchangeType;
use phpAmqpLib\Wire\AMQPTable;


class Rabbitmq_exchange
{
    private $rabbitmqHost = RABBITMQ_HOST;
    private $rabbitmqPort = RABBITMQ_PORT;
    private $rabbitmqUser = RABBITMQ_USER;
    private $rabbitmqPass = RABBITMQ_PASS;
    private $rabbitmqRequestExchange = RABBITMQ_REQUEST_EXCHANGE;
    private $rabbitmqReponseExchange = RABBITMQ_RESPONSE_EXCHANGE;
    private $rabbitmqExchangeType = RABBITMQ_EXCHANGE_TYPE;
    private $rabbitmqDurable = RABBITMQ_DURABLE;
    private $rabbitmqRequestRoutingKey = RABBITMQ_REQUEST_ROUTING_KEY;
    private $rabbitmqResponseRoutingKey = RABBITMQ_RESPONSE_ROUTING_KEY;
    private $rabbitmqRequestQueue = RABBITMQ_REQUEST_QUEUE;
    private $rabbitmqResponseQueue = RABBITMQ_RESPONSE_QUEUE;
    private $rabbitmqSSLOptions = array('verify_peer' => false);
    private $connection = false;
    private $channel = false;
    
    private $accountId;
    private $response;
	private $accountNumber;
    private $synchronizationUserName;
	private $synchronizationPassword;
    
	public function __construct()
	{
        /* Create a connection with rabbit MQ */
        $this->load_credentials();
        //$this->create_connection();
	}
    
    /*
     * Load the synchronization credentials from database and store in variables
     */
	private function load_credentials()
	{
        try
        {
            $sql = 'SELECT `account_id`,`account_number`,`synchronization_username`,`synchronization_password` FROM `erp_api_credentials` WHERE `id`=1';
            $resp = imw_query($sql);
            if( $resp && imw_num_rows( $resp ) == 1 )
            {
                $credsData = imw_fetch_assoc( $resp );
                $creds['account_id'] = $credsData['account_id'];
                $creds['accountNumber'] = $credsData['account_number'];
                $creds['synchronizationUserName'] = $credsData['synchronization_username'];
                $creds['synchronizationPassword'] = $credsData['synchronization_password'];

                /*All details are required
                 through exception if username or password not entered in admin */
                if( trim($creds['synchronizationUserName']) == '' || trim($creds['synchronizationPassword']) == '' ) {
                    throw new Exception('Call Error: Missiong ERP Synchronization credentials. Please check.');
                }
                $this->account_id  = $creds['account_id'];
                $this->accountNumber = $creds['accountNumber'];
                $this->synchronizationUserName  = $creds['synchronizationUserName'];
                $this->synchronizationPassword  = $creds['synchronizationPassword'];
            }
            else {
                /*API credentials are Required*/
                throw new Exception('Call Error: Please add ERP Synchronization credentials.');
            }
        } 
        catch(Exception $e) {
            $this->handle_error($e);
        }
	}
    
    private function create_connection() 
    {
        /* [host, port, user, pass, vHost, ssl_options] */
        $this->connection = new AMQPStreamConnection($this->rabbitmqHost, $this->rabbitmqPort, $this->rabbitmqUser, $this->rabbitmqPass, "/", $this->rabbitmqSSLOptions); 
		
        $this->channel = $this->connection->channel();
    }
    
    private function close_connection() 
    {
        $this->channel->close();
        $this->connection->close();
    }
    
    public function send_request($data_arr=array(),$messageId=0,$resource='',$method='POST') 
    {
		$erp_error=array();
		try{
			$this->create_connection();
			/* [exchange, exchangeType, passive, durable, auto_delete] */
			$this->channel->exchange_declare($this->rabbitmqRequestExchange, $this->rabbitmqExchangeType, false, $this->rabbitmqDurable, false); 

			$this->channel->queue_declare($this->rabbitmqRequestQueue, false, true, false, false);

			if(count($data_arr) > 0) {
				$data=json_encode($data_arr);
			} else {
				$data="";
			}
			
			$message = new AMQPMessage($data);
			
			$header=array(
				'MessageId' => "$messageId",
				'HttpMethod' => "$method",
				'ApiResource' => "$resource",
				'ApiSyncUsername' => "$this->synchronizationUserName",
				'ApiSyncPassword' => "$this->synchronizationPassword",
				'ResponseExchangeName' => "$this->rabbitmqReponseExchange",
				'ResponseRoutingKey' => "$this->rabbitmqResponseRoutingKey"
			);
			
			$headers = new AMQPTable($header);
			
			$message->set('application_headers', $headers);
			
			$date_time = date('Y-m-d H:i:s');
			/*save the request log in erp_api_log*/
			$log_req_qry = "INSERT INTO erp_api_log (request_header,request_type,request_url,request_data,request_date_time,operator_id)
							VALUES ('".json_encode($header)."','".$method."','".$resource."','".json_encode($message)."','".$date_time."','".$_SESSION['authId']."') ";
			$log_req_sql = imw_query($log_req_qry);
			$log_req_id = imw_insert_id();
		
        
			$this->channel->basic_publish($message, $this->rabbitmqRequestExchange,$this->rabbitmqRequestRoutingKey); //[msg, exchange, routing_key]
			
			return $this->recieve_response($method,$log_req_id,$messageId);
		}catch(Exception $e){
			return $erp_error[]='Unable to Connect in RabbitMQ Publish';
		}
        
    }
    
    
    public function recieve_response($method='',$log_req_id=0,$messageId=0) {
        
		$erp_error=array();
        $this->response = null;
		
		try{
			$this->channel->queue_declare($this->rabbitmqResponseQueue, false, true, false, false);

			$callback = function ($msg) {
				$this->response=$msg->body;
				if(!$this->response) {
					$headers = $msg->get('application_headers');
					$headerArr = $headers->getNativeData();
					if($headerArr['HttpMethod']=='DELETE' && $headerArr['ApiResponseStatusCode']=='OK'){
						$this->response=$headerArr['ApiResponseStatusCode'];
					}
				}
			};

			$this->channel->basic_consume($this->rabbitmqResponseQueue, '', false, true, false, false, $callback);

			$timeout = 25;
			while (!$this->response) {
				try{
					$this->channel->wait(null, false , $timeout);
				}catch(Exception $e){
					$this->response = 'Unable to Connect in RabbitMQ Consume disconnected after 25 seconds to break loop.';
				}
			}

			$this->close_connection();
			
			// Update response for each executed request by imwemr
			$log_res_qry = "UPDATE erp_api_log SET response_data = '".$this->response."', response_date_time = '".date('Y-m-d H:i:s')."' WHERE id = ".$log_req_id." ";
			$log_res_sql = imw_query($log_res_qry);

			return $this->response;

		}catch(Exception $e){
			return $erp_error[]='Unable to Connect in RabbitMQ Consume';
		}
    }
    
    
}

