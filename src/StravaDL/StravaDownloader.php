<?php

namespace StravaDL;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use StravaDL\Exception\UnauthorizedException;

class StravaDownloader
{
    private $client;
    private $client_id;
    private $client_secret;

    /**
     * @param $client_secret
     * @param $client_id
     */
    public function __construct($client_secret, $client_id)
    {
        $this->client = new Client('https://www.strava.com/api/v3');
        $this->client_secret = $client_secret;
        $this->client_id = $client_id;
    }

    /**
     * @param $code
     * @return mixed
     */
    public function auth($code)
    {
        $request = $this->client->post('https://www.strava.com/oauth/token', null, array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
        ));

        $response = $request->send();

        return $response->json()['access_token'];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function deauth($key)
    {
        $request = $this->client->post('https://www.strava.com/oauth/deauthorize', null, array(
            'access_token' => $key
        ));

        $response = $request->send();

        return $response->json()['access_token'];
    }

    /**
     * @param $key
     * @return array|bool|float|int|string
     * @throws Exception\UnauthorizedException
     * @throws \Exception
     */
    public function getAthlete($key)
    {
        try{
            $request = $this->client->get('athlete', null, array(
                'query' => array(
                    'access_token' => $key,
                ),
            ));
            $response = $request->send();

            return $response->json();
        }
        catch(ClientErrorResponseException $e){
            $this->exceptionThrower($e);
        }

    }

    /**
     * @param $key
     */
    public function getActivities($key)
    {
        $this->client->get('v3/athlete/activities', array(), array(
            'access_token' => $this->key,
        ));
    }

    public function getStream($key, $id)
    {
        $types = array(
            'time',
            'latlng',
            'distance',
            'altitude',
            'velocity_smooth',
            'heartrate',
            'cadence',
            'watts',
            'temp',
            'moving',
            'grade_smooth',
        );
        $types = implode(',', $types);

        try{
            $request = $this->client->get("activities/$id/streams/$types", null, array(
                'query' => array(
                    'access_token' => $key,
                    'series_type' => 'time',
                ),
            ));
            $response = $request->send();
            var_dump($response);

            return $response->json();
        }
        catch(ClientErrorResponseException $e){
            $this->exceptionThrower($e);
        }
    }

    public function getActivityIds($key)
    {
        try{
            $request = $this->client->get('athlete/activities', null, array(
                'query' => array(
                    'access_token' => $key,
                    'per_page' => 200,
                    'page' => 5,
                ),
            ));
            $response = $request->send();

            $ids = array();
            foreach($response->json() as $activity){
                $ids[] = $activity['id'];
            }
            return count($ids);
        }
        catch(ClientErrorResponseException $e){
            $this->exceptionThrower($e);
        }
    }

    public function getActivity($key, $id)
    {
        try{
            $request = $this->client->get("activities/$id", null, array(
                'query' => array(
                    'access_token' => $key,
                ),
            ));
            $response = $request->send();
            //var_dump($response);

            return $response->json();
        }
        catch(ClientErrorResponseException $e){
            $this->exceptionThrower($e);
        }
    }

    public function exceptionThrower(\Exception $e)
    {
        $status_code = $e->getResponse()->getStatusCode();
        if($status_code == 401){
            throw new UnauthorizedException();
        }
        elseif($status_code == 403 ){
            throw new \Exception("Limit reached/Not authorised");
        }
        else{
            echo $e;
            throw $e;
        }
    }

}