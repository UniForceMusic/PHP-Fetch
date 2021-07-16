<?php
    function fetch($url, $arguments = []) {
        $method     = ( isset($arguments["method"]) ? $arguments["method"] : "GET" );
        $parameters = ( isset($arguments["params"]) ? $arguments["params"] : [] );
        $headers    = ( isset($arguments["headers"]) ? $arguments["headers"] : [] );
        $body       = ( isset($arguments["body"]) ? $arguments["body"] : "" );
        $secure     = ( isset($arguments["secure"]) ? $arguments["secure"] : true );

        switch (strtoupper($method)) {
            case "GET":
                return FetchApi::get($url, $parameters, $headers, $secure);
                break;
            case "POST":
                return FetchApi::post($url, $parameters, $headers, $body, $secure);
                break;
            case "PUT":
                return FetchApi::put($url, $parameters, $headers, $body, $secure);
                break;
            case "PATCH":
                return FetchApi::patch($url, $parameters, $headers, $body, $secure);
                break;
            case "DELETE":
                return FetchApi::delete($url, $parameters, $headers, $body, $secure);
                break;
            default:
                throw new Exception( sprintf("%s method not supported", $method) );
                break;
        }
    }

    class FetchApi {
        public function get($url, $parameters = [], $headers = [], $secure) {
            $curl       = curl_init();
            $url        = FetchApi::serialize_parameters($url, $parameters);
            $headers    = FetchApi::serialize_headers($headers);

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, true);

            if ($secure) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }  

            return new FetchResponse($curl);
        }

        public function post($url, $parameters = [], $headers = [], $data = [], $secure) {
            $curl       = curl_init();
            $url        = FetchApi::serialize_parameters($url, $parameters);
            $headers    = FetchApi::serialize_headers($headers);
            
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HEADER, true);

            if ($secure) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            return new FetchResponse($curl);
        }

        public function put($url, $parameters = [], $headers = [], $data = [], $secure) {
            $curl       = curl_init();
            $url        = FetchApi::serialize_parameters($url, $parameters);
            $headers    = FetchApi::serialize_headers($headers);
            
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HEADER, true);

            if ($secure) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            return new FetchResponse($curl);
        }

        public function patch($url, $parameters = [], $headers = [], $data = [], $secure) {
            $curl       = curl_init();
            $url        = FetchApi::serialize_parameters($url, $parameters);
            $headers    = FetchApi::serialize_headers($headers);
            
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HEADER, true);

            if ($secure) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            return new FetchResponse($curl);
        }

        public function delete($url, $parameters = [], $headers = [], $data = [], $secure) {
            $curl       = curl_init();
            $url        = FetchApi::serialize_parameters($url, $parameters);
            $headers    = FetchApi::serialize_headers($headers);
            
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HEADER, true);

            if ($secure) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            return new FetchResponse($curl);
        }

        public function serialize_parameters($url, $parameters) {
            if ( !empty($parameters) ) {
                $parameters_array = [];

                foreach($parameters as $parameter_name => $parameter_value) {
                    $url_encoded_parameter  = sprintf( "%s=%s", urlencode($parameter_name), urlencode($parameter_value) );
                    $parameters_array[]     = $url_encoded_parameter;
                }

                $query_string   = implode( "&", $parameters_array );
                $url            = sprintf( "%s?%s", $url, $query_string );
            }

            return $url;
        }

        public function serialize_headers($headers) {
            if ( !empty($headers) ) {
                $headers_array = [];

                foreach($headers as $header_name => $header_value) {
                    $url_encoded_header = sprintf( "%s: %s", $header_name, $header_value );
                    $headers_array[]    = $url_encoded_header;
                }

                $headers = $headers_array;
            }

            return $headers;
        }
    }

    class FetchResponse {
        public $url;
        public $code;
        public $http;
        public $headers;
        public $body;
        public $error;

        public function __construct($curl) {
            $response           = curl_exec($curl);
            $error              = curl_error($curl);
            $split_double_eol   = explode( "\r\n\r\n", $response, 2 );
            $split_headers_eol  = explode( "\r\n", $split_double_eol[0] );
            $split_http_space   = explode( " ", $split_headers_eol[0] );
            $this->url          = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
            $this->http         = trim($split_http_space[0]);
            $this->code         = trim($split_http_space[1]);
            $this->headers      = $this->unserialize_headers($split_headers_eol);
            $this->body         = trim($split_double_eol[1]);
            $this->error        = trim($error);

            curl_close($curl);
        }

        public function json($array = true) {
            return json_decode( $this->body, $array );
        }

        public function xml($array = true) {
            $object = simplexml_load_string($this->body);

            if ($array) {
                return json_decode( json_encode($object), true );
            }

            return $object;
        }

        public function unserialize_headers($headers) {
            $headers        = array_slice( $headers, 1 );
            $headers_array  = [];

            foreach($headers as $header) {
                $header_split_colon     = explode( ":", $header, 2 );
                $key                    = trim($header_split_colon[0]);
                $value                  = trim($header_split_colon[1]);

                $headers_array[$key]    = $value;
            }

            return $headers_array;
        }
    }
?>