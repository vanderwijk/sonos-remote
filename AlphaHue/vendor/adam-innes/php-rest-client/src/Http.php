<?php namespace PhpRestClient;


trait Http
{
    /** @var array $default_curlopts Default settings for cURL requests. */
    public $default_curlopts = array(
        'CURLOPT_CONNECTTIMEOUT' => 5,
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_AUTOREFERER'    => true,
        'CURLOPT_FOLLOWLOCATION' => true,
        'CURLOPT_SSL_VERIFYPEER' => false,
    );

    /** 
     * @var array $curl_headers Custom headers to be sent during a request. These
     *                          headers do not persist across requests and need to
     *                          be set with every call to httpRequest().
     */
    public $curl_headers = array();

    /**
     * Sets the default cURL options.
     *
     * Adds new cURL options to the default array, these will be used on each
     * request. Defaults can be unset by sending a null value.
     *
     * @param array $optons {
     *     @var mixed $CURLOPT_*  Any valid CURLOPT_ setting as a (string) key and 
     *                            associated value. Do not pass the CURLOPTS in with
     *                            array keys set to their constant integer values.
     * }
     *
     * @return void
     */
    public function setDefaultCurlopts(array $options)
    {
        array_merge($this->default_curlopts, $options);

        // Remove default options set to null.
        foreach ($this->default_curlopts as $option=>$value) {
            if (null == $value) {
                unset($this->default_curlopts[$option]);
            }
        }
    }

    /**
     * Sets Headers to be passed in the CURLOPT_HEADER cURL option.
     *
     * @param array $headers {
     *     @var string $header Header name should be passed over as the key with value.
     * }
     *
     * @return void
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $header=>$value) {
            $this->curl_headers[$header] = $value;
        }
    }

    /**
     * Flattens the Header overrides in an array for use with CURLOPT_HTTPHEADER.
     *
     * @return array Array of Http Headers flattened into an array.
     */
    public function flattenHeaders()
    {
        $flattened = array();
        foreach ($this->curl_headers as $header=>$value) {
            $flattened[] = "{$header}: {$value}";
        }
        return $flattened;
    }

    /**
     * Makes an HTTP Request.
     *
     * @param string $url Request URL.
     * @param array  $options {
     *     @var mixed $CURLOPT_*  Any valid CURLOPT_ setting as a (string) key and
     *                             associated value. Do not pass the CURLOPTS in with
     *                             array keys set to their constant integer values.
     *     @var bool  $NO_COOKIES Set to true to prevent request from using and
     *                            storing cookies.
     * }
     *
     * @return mixed Response string on success, false on failure.
     */
    public function httpRequest($url, $options=array())
    {
        // Validate URL.
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $options['CURLOPT_URL'] = $url;
        } else return false;

        $ch = curl_init();

        // Default to use cookies.
        if (!$options['NO_COOKIES']) {
            $this->cookie_file = $options['CURLOPT_COOKIEFILE'] ?: false;
            // If no cookie file passed in, create a cookie in the temp directory.
            $this->cookie_file = $this->cookie_file  ?: tempnam(sys_get_temp_dir(), 'Http_Cookie_');
            if (is_writeable($this->cookie_file)) {
                $options['CURLOPT_COOKIEFILE'] = $this->cookie_file;
                $options['CURLOPT_COOKIEJAR']  = $this->cookie_file;
            }
        }

        // Create array of cURL options.
        foreach ($options as $option=>$value) {
            if (strpos($option, 'CURLOPT_') !== false) {
                $curlopts[constant($option)] = $value;
            }
        }

        // Set default values not passed in.
        foreach ($this->default_curlopts as $option=>$value) {
            if (!isset($curlopts[$option])) {
                $curlopts[constant($option)] = $value;
            }
        }

        // Set custom headers, these should override any previously set headers.
        $flattenedHeaders = $this->flattenHeaders();
        if (count($flattenHeaders)) {
            $options['CURLOPT_HTTPHEADER'] = $flattenedHeaders;
        }

        curl_setopt_array($ch, $curlopts);
        $this->curl_response = curl_exec($ch);

        // Save last request.
        $this->curl_info = curl_getinfo($ch);

        // Save last error.
        if (false === $this->curl_repsponse) {
            $this->curl_error = curl_error($ch);
        } else $this->curl_error = false;

        curl_close($ch);

        // Reset headers.
        $this->curl_headers = array();

        return $this->curl_response;
    }
}
