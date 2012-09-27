<?php
class NdbcFeed extends Model
{

	//------------------------------------------
	// Class constants
	//------------------------------------------
	const BASE_URL = 'http://www.ndbc.noaa.gov/data/latest_obs/';
    
    //------------------------------------------
	// Private properties
	//------------------------------------------
	private $url,
			$xml,
			$json;
    
    //------------------------------------------
	// Public functions
	//------------------------------------------

	/**
	* load new ndbc feed
	* 
	* @param int - station id
	*/
	public function load($stationId){
		$this->url = self::BASE_URL . $stationId .'.rss';

		$this->getXml();
	}

	/* 
	* Output feed as json
	*/
	public function getJson(){

		$this->xmlToJson();

		return $this->json;
	} 

	//------------------------------------------
	// Private functions
	//------------------------------------------

	/*
	* Load xml feed
	*/
	private function getXml(){

		$this->xml = file_get_contents(
						$this->url,
						false,
					    stream_context_create(
					        array(
					            'http' => array(
					                'ignore_errors' => true
					            )
					        )
    					)
    				);

	
		if (!$this->xml || $http_response_header[0] == 'HTTP/1.1 404 Not Found') {
			throw new Exception();
		}
			
		
	}

	/**
    * Get JSON data from XML feed
    *
    * @param string 
    * @return string - json 
    */
    private function xmlToJson(){   

    	$simpleXml = simplexml_load_string($this->xml);
  
        $this->stripCdataTags();

        $this->formatDescriptionAsXml();     

        $this->json = json_encode((array)simplexml_load_string($this->xml));

    }

	/**
    * Strip CDATA tags from xml
    */
	private function stripCdataTags(){

        $this->xml = str_replace(array('<![CDATA[', ']]>'), array('<readings>', '</readings>'), $this->xml);
    }

    /**
    * Format HTML description as xml
    */
    private function formatDescriptionAsXml(){   

        $this->xml = preg_replace_callback(
        				'/<strong>(.+?):<\/strong>(.+?)<br \/>/',
        				function($m){
        					$m1 = str_replace(' ', '-', $m[1]);
        					$m2 = trim($m[2]);
        					return '<'. $m1 .'>'. $m[2] .'</'. $m1 .'>';
        				},
        				$this->xml
        			);
        
        //
        //http://gskinner.com/RegExr/
        //<strong>ELE MENT:</strong>VAL UE<br />
        //<strong>(.+):</strong>(.+)<br />
        //<$1>$2<$1>
    }
}