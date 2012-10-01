<?php
class NdbcFeed extends Model
{

	//------------------------------------------
	// Class constants
	//------------------------------------------
	const BASE_URL = 'http://www.ndbc.noaa.gov/data/latest_obs/',
		  FILE_EXT = '.rss',
		  DESCRIPTION_TAG = 'description',
		  TITLE_TAG = 'strong',
		  SEPERATOR_TAG = 'br';
    
    //------------------------------------------
	// Private properties
	//------------------------------------------
	private $url,
			$xml,
			$json,
			$rootElement;
    
    //------------------------------------------
	// Public functions
	//------------------------------------------
	
	/**
	 * Initialise feed for specified station
	 * 
	 * @param int $stationId - ndbc stastion id
	 */
	public function init($stationId){
		$this->url = self::BASE_URL . $stationId . self::FILE_EXT;
	}

	/* 
	* Return json formatted data
	*/
	public function getJsonData(){		

		$this->fetchXmlData();

		$this->xmlToJson();

		return $this->json;
	} 

	//------------------------------------------
	// Private functions
	//------------------------------------------

	/*
	* Get xml data feed
	*/
	private function fetchXmlData(){

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
    * Reformat XML data as JSON
    */
    private function xmlToJson(){

		$this->getFeedItem();

		$this->addNamespacedrootElement();

		$this->stripCdataTags();

        $this->formatDescriptionAsXml();

        $this->json = json_encode((array)simplexml_load_string($this->xml));

    }

	/**
	* Strip all unnecessary XML data to leave just feed item
	*/
    private function getFeedItem(){

    	$xmlObj = simplexml_load_string($this->xml);

    	$this->buildNamespacedRootElement($xmlObj);

    	$this->xml = $xmlObj->channel->item->asXML();
    }

    /**
     * Recreate an empty copy of the xml root element with namespace attributes
     * 
     * @param  SimpleXML $xmlObj
     */
    private function buildNamespacedRootElement($xmlObj){

    	$namespaces = $xmlObj->getNameSpaces(true);    	

    	$this->rootElement = '<root';
    	foreach ($namespaces as $name => $url) {
    		$this->rootElement .= ' xmlns:'. $name .'="'. $url .'"';
    	}
    	$this->rootElement .= '>';
    }   

    /**
     * Add the root element (with namespace attributes) to the required xml data
     */
    private function addNamespacedrootElement(){
    	$this->xml = $this->rootElement . $this->xml . '</root>';
    }

	/**
    * Strip CDATA tags from xml
    */
	private function stripCdataTags(){
		
		$this->xml = str_replace(array('<![CDATA[', ']]>'), array('', ''), $this->xml);

    }

    /**
    * Format HTML description as xml
    */
    private function formatDescriptionAsXml(){

    	$this->xml = str_replace(array('<'. self::DESCRIPTION_TAG .'>', '</'. self::DESCRIPTION_TAG .'>'), array('<readings>', '</readings>'), $this->xml);

    	// <strong>key</strong>val<br /> => <key>val</key>
        $this->xml = preg_replace_callback(
        				'/<'. self::TITLE_TAG .'>(.+):<\/'. self::TITLE_TAG .'>(.+)<'. self::SEPERATOR_TAG .' \/>/',
        				function($m){
        					$m1 = str_replace(' ', '-', $m[1]);
        					$m2 = trim($m[2]);
        					return '<'. $m1 .'>'. $m2 .'</'. $m1 .'>';
        				},
        				$this->xml
        			);

        $this->xml = str_replace('<'. self::SEPERATOR_TAG .' />', '', $this->xml);

    }

}