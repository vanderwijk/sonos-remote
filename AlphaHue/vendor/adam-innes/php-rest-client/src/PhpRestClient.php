<?php namespace PhpRestClient;

class PhpRestClient
{
    use Http;

    /** @var bool $return_json_as_array False for object, true for array. */
    public $return_json_as_array = true;

    /** @var string $base_url Base URL to use when calling the REST API. */
    public $base_url = false;

    /**
     * Initializes class with the API Base URL.
     *
     * @param string $base_url Base URL of the REST API.
     *
     * @return void
     */
    public function __construct($base_url)
    {
        $this->base_url = rtrim($base_url, '/') . '/';
    }

    /**
     * Send HTTP request.
     *
     * @param string $path   Request path.
     * @param array  $optons {
     *     @var mixed $CURLOPT_*  Any valid CURLOPT_ setting as a (string) key and 
     *                             associated value. Do not pass the CURLOPTS in with
     *                             array keys set to their constant integer values.
     *     @var bool  $NO_COOKIES Set to true to prevent request from using and 
     *                            storing cookies.
     * }
     *
     * @return mixed Boolean false on failure, parsed response on success.
     */
    public function call($path, $options = array())
    {
        // Base URL has a trailing slash, remove this if passed in.
        $url = $this->base_url . ltrim($path, '/');

        $response = $this->httpRequest($url, $options);
        return $this->parseResponse($response);
    }

    /**
     * Sets the authentication method for requests.
     *
     * @param string $username Username
     * @param string $password Password
     * @param int    $auth     Authentication Method (CURLAUTH_BASIC or CURLAUTH_DIGEST)
     *
     * @return void
     */
    public function setAuthentication($username, $password, $auth=CURLAUTH_BASIC)
    {
        $headers['CURLOPT_HTTPAUTH'] = $auth;
        $headers['CURLOPT_USERPWD']  = "{$username}:{$password}";
        $this->setDefaultCurlopts($headers);
    }

    /**
     * Unsets the authentication headers.
     *
     * @return void
     */
    public function unsetAuthentication()
    {
        $headers['CURLOPT_HTTPAUTH'] = null;
        $headers['CURLOPT_USERPWD'] = null;
        $this->setDefaultCurlopts($headers);
    }

    /**
     * Determine if the response is XML or JSON and returns parsed response.
     *
     * @param string $response Response string.
     *
     * @return mixed SimpleXMLObject, JSON as object or array, string if no match,
     *               false on failure.
     */
    public function parseResponse($response) {
        $first_char = substr(trim($response), 0, 1);

        switch ($first_char) {
            case '<': // XML.
                $response = simplexml_load_string($response);
                if ($response === false) {} // todo: Error.              
                break;

            case '[': case '{': // JSON.
                $response = json_decode($response, $this->return_json_as_array);
                if ($response === false) {} // todo: Error.
                break;
            default: break;
        }
        
        // return parsed results or response string if neither JSON or XML.
        return $response;
    }

    /**
     * Makes a GET request from the API.
     *
     * @param string $path    Path of request, not including domain.
     * @param mixed  $query   Array of request parameters or query string.
     * @param array  $headers Headers should be passed with the header name as the key.
     * 
     * @return mixed Response object or array from the server, false on failure.
     */
    public function get($path, $query = null, $headers = null)
    {
        $query = is_array($query) ? http_build_query($query) : $query;
        $path .= $query ? '?' . ltrim($query, '?') : '';

        if ($headers) {
            $this->setHeaders($headers);
        }

        return $this->call($path);
    }

    /**
     * Makes a PUT request from the API.
     *
     * @param string $path    Path of request, not including the domain.
     * @param mixed  $query   Array of request parameters or query string.
     * @param array  $headers Headers should be passed with the header name as the key.
     *
     * @return mixed Response object or array from the server, false on failure.
     */
    public function put($path, $query = null, $headers = null)
    {
        if (is_string($query)) {
            $this->curl_headers[] = 'Content-Length: ' . strlen($query);
        }
        $options['CURLOPT_CUSTOMREQUEST'] = 'PUT';        
        $options['CURLOPT_POSTFIELDS'] = $query;

        return $this->call($path, $options);
    }

    /**
     * Makes a POST request from the API.
     *
     * @param string $path    Path of request, not including domain.
     * @param mixed  $query   Array of request parameters or query string.
     * @param array  $headers Headers should be passed with the header name as the key.
     * 
     * @return mixed Response object or array from the server, false on failure.
     */
    public function post($path, $query = null, $headers = null)
    {
        if (is_string($query)) {
            $this->curl_headers[] = 'Content-Length: ' . strlen($query);
        }

        $options['CURLOPT_CUSTOMREQUEST'] = 'POST';
        $options['CURLOPT_POST'] = true;
        $options['CURLOPT_POSTFIELDS'] = $query;

        if ($headers) {
            $this->setHeaders($headers);
        }

        return $this->call($path, $options);
    }

    /**
     * Makes a GET request from the API.
     *
     * @param string $path    Path of request, not including domain.
     * @param mixed  $query   Array of request parameters or query string.
     * @param array  $headers Headers should be passed with the header name as the key.
     * 
     * @return mixed Response object or array from the server, false on failure.
     */
    public function delete($path, $headers = null)
    {
        $options['CURLOPT_CUSTOMREQUEST'] = 'DELETE';

        if ($headers) {
            $this->setHeaders($headers);
        }

        return $this->call($path, $options);
    }

    /**
     * Makes a PATCH request from the API.
     *
     * @param string $path    Path of request, not including domain.
     * @param mixed  $query   Array of request parameters or query string.
     * @param array  $headers Headers should be passed with the header name as the key.
     * 
     * @return mixed Response object or array from the server, false on failure.
     */
    public function patch($path, $query = null, $headers = null)
    {
        if (is_array($query)) {
            $query = http_build_query($query);
        }

        $this->curl_headers[] = 'Content-Length: ' . strlen($query);

        $options['CURLOPT_CUSTOMREQUEST'] = 'PATCH';
        if ($query) {
            $options['CURLOPT_POSTFIELDS'] = $query;
        }
        
        if ($headers) {
            $this->setHeaders($headers);
        }

        return $this->call($path, $options);
    }
}
