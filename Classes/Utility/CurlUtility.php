<?php

declare(strict_types=1);

namespace Site\Core\Utility;

class CurlUtility
{
    /**
     * @var \CurlHandle|false|resource
     */
    protected $curl;

    /**
     * @var bool|string
     */
    protected $response = false;

    /**
     * Retrieves the curl obejct.
     *
     * @return \CurlHandle|false|resource
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * Retrieves the response of the executed cURL.
     *
     * @return bool|string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Executes a cURL request to the provided URI with additional query-params.
     * Method does not supports post parameters. You might try out in such
     * an use-case the executeParams-method.
     *
     * @param string $uri
     * @param array  $params
     *
     * @see executeParams
     *
     * @return $this
     */
    public function execute($uri)
    {
        // Get cURL resource
        $curl = curl_init();

        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $uri,
        ]);

        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $this->response = $resp;

        $this->curl = $curl;

        // Close request to clear up some resources
        curl_close($curl);

        return $this;
    }

    /**
     * @todo finish this
     */
    public function executeParams()
    {
    }
}
