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
	* constructor - create new ndbc feed
	* 
	* @param int - station id
	*/
	/*public function __construct($stationId){
		$this->url = self::BASE_URL . $stationId .'.rss';

		$this->getXml();
	}*/

	/**
	* constructor - create new ndbc feed
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
		$this->xml = file_get_contents($this->url);
	}

	/**
    * Get JSON data from XML feed
    *
    * @param string 
    * @return string - json 
    */
    private function xmlToJson(){              

        $this->stripCdataTags();     

        $this->formatDescriptionAsXml();     

        $this->json = json_encode((array)simplexml_load_string($this->xml));

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

        $this->xml = str_replace(array('<strong>', '</strong>', '<br />'), array('<readings><key>', '</key><value>', '</value></readings>'), $this->xml);
    }
}