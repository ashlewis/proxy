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

		// @TODO: use cached feed data for development
		/*$this->xml = file_get_contents(
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
		}*/

		$this->xml = '<?xml version="1.0"?>
<?xml-stylesheet type="text/xsl" href="/rss/ndbcrss.xsl"?>
<rss version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
    <title>NDBC - Station 62303 - Pembroke Buoy Observations</title>
    <description><![CDATA[This feed shows recent marine weather observations from Station 62303.]]></description>
    <link>http://www.ndbc.noaa.gov/</link>
    <pubDate>Thu, 04 Oct 2012 17:47:00 UT</pubDate>
    <lastBuildDate>Thu, 04 Oct 2012 17:47:00 UT</lastBuildDate>
    <ttl>30</ttl>
    <language>en-us</language>
    <managingEditor>webmaster.ndbc@noaa.gov</managingEditor>
    <webMaster>webmaster.ndbc@noaa.gov</webMaster>
    <image>
      <url>http://weather.gov/images/xml_logo.gif</url>
      <title>NOAA - National Weather Service</title>
      <link>http://www.ndbc.noaa.gov/</link>
    </image>
    <item>
      <pubDate>Thu, 04 Oct 2012 17:47:00 UT</pubDate>
      <title>Station 62303 - Pembroke Buoy</title>
      <description><![CDATA[
        <strong>October 4, 2012 1700 UTC</strong><br />
        <strong>Location:</strong> 51.603N 5.1W<br />
        <strong>Wind Direction:</strong> SW (220&#176;)<br />
        <strong>Wind Speed:</strong> 18.1 knots<br />
        <strong>Significant Wave Height:</strong> 5.6 ft<br />
        <strong>Average Period:</strong> 5 sec<br />
        <strong>Atmospheric Pressure:</strong> 29.75 in (1007.6 mb)<br />
        <strong>Pressure Tendency:</strong> -0.04 in (-1.5 mb)<br />
        <strong>Air Temperature:</strong> 57.0&#176;F (13.9&#176;C)<br />
        <strong>Dew Point:</strong> 45.3&#176;F (7.4&#176;C)<br />
        <strong>Water Temperature:</strong> 58.5&#176;F (14.7&#176;C)<br />
      ]]></description>
      <link>http://www.ndbc.noaa.gov/station_page.php?station=62303</link>
      <guid>http://www.ndbc.noaa.gov/station_page.php?station=62303&amp;ts=1349370000</guid>
      <georss:point>51.603 -5.100</georss:point>
    </item>
  </channel>
</rss>';

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
        					$m1 = lcfirst(str_replace(' ', '', $m[1]));
        					$m2 = trim($m[2]);
        					return '<'. $m1 .'>'. $m2 .'</'. $m1 .'>';
        				},
        				$this->xml
        			);

        $this->xml = str_replace('<'. self::SEPERATOR_TAG .' />', '', $this->xml);

    }

}