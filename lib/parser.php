<?php
class Parser
{   
    /**
    * Get JSON data from XML feed
    *
    * @param string 
    * @return string - json 
    */
    public static function getJsonFromXml($feedUrl){ 

        $xmlString = file_get_contents($feedUrl);     

        $xmlString = self::stripCDATA($xmlString);     

        $xmlString = self::formatDescription($xmlString);     

        return json_encode((array)simplexml_load_string($xmlString));

    }

    /**
    * Strip CDATA tags from a string
    *
    * @param string
    * @return string
    */
	private static function stripCDATA($string){

        return str_replace(array('<![CDATA[', ']]>'), array('', ''), $string);
    }

    /**
    * Format HTML description as xml
    *
    * @param string
    * @return string
    */
    private static function formatDescription($string){   

        return str_replace(array('<strong>', '</strong>', '<br />'), array('<readings><key>', '</key><value>', '</value></readings>'), $string);
    }
}