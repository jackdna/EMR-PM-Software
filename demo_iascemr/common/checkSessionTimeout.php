<?PHP

		$basename	=	basename($_SERVER['PHP_SELF']);
		$baseReq	=	array(
									'home_inner_front.php'
									
								);
		
		if(isset($_SESSION['session_last_update']) ) 
		{
				$session_life = time() - $_SESSION['session_last_update'] ;
				if( ( $session_life >  $_SESSION['loginUserSessionTimeout']  ) && $basename <> 'index.php' )
					header('Location:./logout.php');
		}else if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="admin") {
			echo '<script>top.location.href="index.php"</script>';
		}
		
		//if( $basename <> 'user_agent.php'  );
			$_SESSION['session_last_update'] = time();	
		
		//Sanitize Inputs
		//include('sanitize_inputs.php');
		
?>