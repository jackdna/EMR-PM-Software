<?php

/*PHP Klein Guide*/

//i	=	integer
//a	=	alphanumeric
//h	=	hexadecimal
//s	=	slug
//
//
//====================================================================================================================================
//
//Router / Klein:
//	->post()			//	Handle Post Request
//	->get()				//	Handle Get Request
//	->abort(n)			//	Abort Further Execution with Status code provided as parameter
//	->skipRemaining()	//	Skip furhter execution of Event Handlers with 404 Status code
//	->skipNext(n)		//	Skip furhter routes n = not of routes to skip
//	->skipThis()		//	Skipe further execution of current route with 404 Status code
//	->afterDispatch()	//	Set Callback function to be called after dispatching the request
//	->onHttpError()		//	Callback function on occourance of HTTP Error
//	->onError()			//	Callback function to be called for Error Handling
//	->validateRegularExpression()	//	Validate Regular Expression
//	->with()			//	Handles Multiple Routes within
//	->respond()			//	Common Routung Method
//	
//	->app()				//	App Object
//	->service()			//	Service Object
//	->response()		//	Response Object
//	->request()			//	Request Object
//
//====================================================================================================================================
//
//Request:
//	->method()			//	GEt the requesting method	GET	/	POST
//	->query()			//	Modifies Parameter in queryString
//	->pathname()		//	Get the request path to be called (URL)
//	->uri()				//	Get Complete request URL including parameters (?)
//	->userAgent()		//	Get UserAgent (Browser/Clinet) name
//	->ip()				//	Get Client IP Address (Request IP Address)
//	->isSecure()		//	Is Called from HTTPS URL
//	->__unset()			//	Delete Parameter
//	->__set()			//	Add / Update paramter in current Request
//	->__get()			//	Retrieve Request Parameter	(NULL if does not exists)
//	->__isset()			//	Check if parameter is available	(boolean)
//	->params()			//	List all parameters (GET, POST, Cookies, Named Parameters)
//	->body()			//	Retrieve Request body (php://input)
//	->files()			//	List files uploaded in the Request
//	->headers()			//	Get Request Header
//	->server()			//	$_SERVER
//	->paramsPost()		//	Get All Parameters sent by Post { ->all() }
//	->id()				//	Generate Unique Request ID
//
//====================================================================================================================================
//
//Response:
//	->redirect()		//	Redirect request with HTTP status code and lock the response for further modifications
//	->noCache()			//	Set header to cache (Preventing browser from caching the response)
//	->header()			//	Set response header element {$key, $value}
//	->isSent()			//	Check if response has been sent {return boolean}
//	->unlock()			//	Unlock the previously locked response
//	->lock()			//	Lock the response from furhter modifications
//	->isLocked()		//	Check if response is locked {return boolean}
//	->code()			//	Get or Set http status code for the response
//	->headers()			//	Get response headers
//	->
//	->status()			//	Return the HTTP status Object
	
?>