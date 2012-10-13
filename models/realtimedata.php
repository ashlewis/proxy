<?php
class RealtimeData extends Model
{

	//------------------------------------------
	// Class constants
	//------------------------------------------
	const BASE_URL = 'http://www.ndbc.noaa.gov/data/realtime2/',
		  FILE_EXT = '.txt',
          DELIMITER = ' ';
    
    //------------------------------------------
	// Private properties
	//------------------------------------------
	private $url,
			$txt,
            $csv,
			$json;
    
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

		$this->fetchTxtData();

        $this->txtToCsv();

        $this->csvToJson();

		return $this->json;
	} 

	//------------------------------------------
	// Private functions
	//------------------------------------------

	/*
	* Get txt data feed
	*/
	private function fetchTxtData(){

        $this->text = file_get_contents(
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

    
        if (!$this->text || $http_response_header[0] == 'HTTP/1.1 404 Not Found') {
            throw new Exception();
        }

	}

    /**
     * Convert text data to CSV
     */
    private function txtToCsv(){

        // remove multiple space chars
        $this->text = preg_replace('/[ ]{2,}/', ' ', $this->text);

        $this->csv = str_getcsv($this->text, "\n");
    }

    /**
     * Convert CSV data to json
     */
    function csvToJson(){

        $header = null;
        $units = null;
        $data = array();

        foreach ($this->csv as $csvLine) {

            if (is_null($header)) {
                $header = explode(self::DELIMITER, $csvLine);

            } elseif (is_null($units)) {
                $units = explode(self::DELIMITER, $csvLine);

            } else {
                $items = explode(self::DELIMITER, $csvLine);

                for ($n = 0, $m = count($header); $n < $m; $n++) {

                    $prepareData[$header[$n]] = array('data'=>$items[$n], 'units'=>$units[$n]);

                }
                $data[] = $prepareData;
            }
        }
        $this->json = json_encode($data);
    }
}