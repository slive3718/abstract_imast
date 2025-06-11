<?php

namespace App\Models\Core;

use CodeIgniter\Model;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class Api extends Model
{
    private string $api_endpoint;
    private string $api_version;
    private string $api_url;
    private Guzzle $guzzle;
    private stdClass $middlewareResponse;

    public function __construct()
    {
        $this->api_endpoint = $_ENV['api.endpoint']??'MISSING_API_URL_IN_ENV_FILE';
        $this->api_version = $_ENV['api.version']??'MISSING_API_VERSION_IN_ENV_FILE';
        $this->api_url = "{$this->api_endpoint}/{$this->api_version}/";

        $this->guzzle = new Guzzle([
            'base_uri' => $this->api_url
        ]);

        $this->middlewareResponse = new stdClass();
        $this->middlewareResponse->status = false;
        $this->middlewareResponse->data = null;
    }

    /**
     * @param string $uri
     *
     * @return stdClass [status, data]
     */
    public function getRequest(string $uri): stdClass
    {
        try {
            $response = $this->guzzle->get($uri);
            $responseContents = $response->getBody()->getContents();
            $this->middlewareResponse->status = true;
            $this->middlewareResponse->data = json_decode($responseContents)->data;
            return $this->middlewareResponse;
        }
        catch (GuzzleException $e) {
            $this->middlewareResponse->data = $e;
            return $this->middlewareResponse;
            //$status_code = $e->getCode();
            //$error_msg = ($e->getResponse())->getReasonPhrase();
        }
    }

    public function post($uri, $field): stdClass{

        $response = $this->guzzle->request('POST', $uri,  [
            'json'=> $field,
            'http_errors'=> false,
        ]);
        try {
            $responseContents = $response->getBody()->getContents();
            $responseStatus = $response->getStatusCode();
            $this->middlewareResponse->reason = $response->getReasonPhrase();
            // if ($responseStatus === 200) {
                $this->middlewareResponse->status = $responseStatus;
                $this->middlewareResponse->data = json_decode($responseContents);
            // }else{
            //     $this->middlewareResponse = $e;
            // }
        }
        catch (GuzzleException $e) {
            $this->middlewareResponse->data = $e;
            return $this->middlewareResponse;
        }
        return $this->middlewareResponse;
    }
}