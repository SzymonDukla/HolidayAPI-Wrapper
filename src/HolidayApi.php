<?php

namespace SzymonDukla\HolidayApi\HolidayApi;

use GuzzleHttp;

class HolidayApi {
    
    const BASE_URI = 'https://api.getfestivo.com';
    const VERSION = 2;
    
    protected $client;
    protected $apiKey;
    
    public function __construct($apiKey) {
        try {
            if(!is_string($apiKey) || !$apiKey || is_null($apiKey))
                throw new \Exception("API key not provided!");
            $this->apiKey = $apiKey;
            return $this;
        } catch (\Exception $ex) {
            return $ex;
        }
        
    }
    
    public function makeClient()
    {
        try
        {
            $this->client = new GuzzleHttp\Client([
                'base_uri' => sprintf('%s/v%d/',
                    self::BASE_URI,
                    self::VERSION),
            ]);
            
            return $this;
        } catch (\Exception $exception)
        {
            return die($exception->getMessage());
        }
    }
    
    public function getHolidays($country, $year = null, $month = null, $day = null, $previous = false, $upcoming = false, $pretty = false) {
        try
        {
            if($previous && $upcoming)
                return header("HTTP/1.1 400");
            
            $year 	= $year ?? date('Y');
            
            $holidays = $this->client->get('holidays',
                [
                    'query' => [
                        'api_key'   => $this->apiKey,
                        'country' 	=> $country,
                        'year' 		=> $year,
                        'month' 	=> $month,
                        'day' 		=> $day,
                        'previous'  => $previous,
                        'upcoming'  => $upcoming,
                        'pretty'    => $pretty
                    ],
                ]);
            
            if($holidays->getStatusCode() == 200)
            {
                $res = json_decode($holidays->getBody());
                $holidays = !empty($res->holidays) ? $res->holidays : [];
            }
            
            return $holidays;
            
        } catch (\Exception $exception)
        {
            return die($exception->getMessage());
        }
    }
    
}
