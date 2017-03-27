<?php if ( ! defined( 'ABSPATH' ) ) exit;  

/*
* If we need to add another api and its information should be here
* Return an array() of information for api request
*/
return array(

	/**
	*	format
	*  
	*   'api_name_or_version' = array(
	*		//Url of this api
	*		'url' => 'https://stg-api.linksync.com/api/v1/'
	*   );
	*/

	'test'	 => array(
		/*
		*	api url for test api
		*/
		'url'=> 'https://stg-api.linksync.com/api/v1/'
	),
	
	'v1' => array(
		/*
		*	api url for v1 api
		*/
		'url'=> 'https://api.linksync.com/api/v1/'
	),

    'dev' => array(
        /*
        *    api url for dev environment
        */
        'url'=> 'https://dev-api.linksync.com/api/v1/'
    )

);