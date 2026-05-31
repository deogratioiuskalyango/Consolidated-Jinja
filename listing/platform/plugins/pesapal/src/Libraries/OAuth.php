<?php

namespace Botble\Pesapal\Libraries;

// Generic exception class
if (! class_exists('Botble\Pesapal\Libraries\OAuthException')) {
    class OAuthException extends \Exception
    {
        // pass
    }
}

if (! class_exists('Botble\Pesapal\Libraries\OAuthConsumer')) {
    class OAuthConsumer
    {
        public $key;
        public $secret;
        public $callback_url;

        function __construct($key, $secret, $callback_url = null)
        {
            $this->key = $key;
            $this->secret = $secret;
            $this->callback_url = $callback_url;
        }

        function __toString()
        {
            return "OAuthConsumer[key=$this->key,secret=$this->secret]";
        }
    }
}

if (! class_exists('Botble\Pesapal\Libraries\OAuthToken')) {
    class OAuthToken
    {
        public $key;
        public $secret;

        function __construct($key, $secret)
        {
            $this->key = $key;
            $this->secret = $secret;
        }

        function to_string()
        {
            return "oauth_token=" .
                   OAuthUtil::urlencode_rfc3986($this->key) .
                   "&oauth_token_secret=" .
                   OAuthUtil::urlencode_rfc3986($this->secret);
        }

        function __toString()
        {
            return $this->to_string();
        }
    }
}

if (! class_exists('Botble\Pesapal\Libraries\OAuthSignatureMethod')) {
    class OAuthSignatureMethod
    {
        public function check_signature(&$request, $consumer, $token, $signature)
        {
            $built = $this->build_signature($request, $consumer, $token);
            return $built == $signature;
        }
    }
}

if (! class_exists('Botble\Pesapal\Libraries\OAuthSignatureMethod_HMAC_SHA1')) {
    class OAuthSignatureMethod_HMAC_SHA1 extends OAuthSignatureMethod
    {
        function get_name()
        {
            return "HMAC-SHA1";
        }

        public function build_signature($request, $consumer, $token)
        {
            $base_string = $request->get_signature_base_string();
            $request->base_string = $base_string;

            $key_parts = array(
                $consumer->secret,
                ($token) ? $token->secret : ""
            );

            $key_parts = OAuthUtil::urlencode_rfc3986($key_parts);
            $key = implode('&', $key_parts);

            return base64_encode(hash_hmac('sha1', $base_string, $key, true));
        }
    }
}

if (! class_exists('Botble\Pesapal\Libraries\OAuthRequest')) {
    class OAuthRequest
    {
        private $parameters;
        private $http_method;
        private $http_url;
        public $base_string;
        public static $version = '1.0';
        public static $POST_INPUT = 'php://input';

        function __construct($http_method, $http_url, $parameters = null)
        {
            @$parameters or $parameters = array();
            $this->parameters = $parameters;
            $this->http_method = $http_method;
            $this->http_url = $http_url;
        }

        public static function from_consumer_and_token($consumer, $token, $http_method, $http_url, $parameters = null)
        {
            @$parameters or $parameters = array();
            $defaults = array("oauth_version" => OAuthRequest::$version,
                              "oauth_nonce" => OAuthRequest::generate_nonce(),
                              "oauth_timestamp" => OAuthRequest::generate_timestamp(),
                              "oauth_consumer_key" => $consumer->key);
            if ($token)
                $defaults['oauth_token'] = $token->key;

            $parameters = array_merge($defaults, $parameters);

            return new OAuthRequest($http_method, $http_url, $parameters);
        }

        public function set_parameter($name, $value, $allow_duplicates = true)
        {
            if ($allow_duplicates && isset($this->parameters[$name])) {
                if (is_scalar($this->parameters[$name])) {
                    $this->parameters[$name] = array($this->parameters[$name]);
                }
                $this->parameters[$name][] = $value;
            } else {
                $this->parameters[$name] = $value;
            }
        }

        public function get_parameter($name)
        {
            return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
        }

        public function get_parameters()
        {
            return $this->parameters;
        }

        public function get_signable_parameters()
        {
            $params = $this->parameters;
            if (isset($params['oauth_signature'])) {
                unset($params['oauth_signature']);
            }
            return OAuthUtil::build_http_query($params);
        }

        public function get_signature_base_string()
        {
            $parts = array(
                $this->get_normalized_http_method(),
                $this->get_normalized_http_url(),
                $this->get_signable_parameters()
            );

            $parts = OAuthUtil::urlencode_rfc3986($parts);

            return implode('&', $parts);
        }

        public function get_normalized_http_method()
        {
            return strtoupper($this->http_method);
        }

        public function get_normalized_http_url()
        {
            $parts = parse_url($this->http_url);

            $port = @$parts['port'];
            $scheme = $parts['scheme'];
            $host = $parts['host'];
            $path = @$parts['path'];

            $port or $port = ($scheme == 'https') ? '443' : '80';

            if (($scheme == 'https' && $port != '443')
                || ($scheme == 'http' && $port != '80')) {
                $host = "$host:$port";
            }
            return "$scheme://$host$path";
        }

        public function to_url()
        {
            $post_data = $this->to_postdata();
            $out = $this->get_normalized_http_url();
            if ($post_data) {
                $out .= '?' . $post_data;
            }
            return $out;
        }

        public function to_postdata()
        {
            return OAuthUtil::build_http_query($this->parameters);
        }

        public function sign_request($signature_method, $consumer, $token)
        {
            $this->set_parameter(
                "oauth_signature_method",
                $signature_method->get_name(),
                false
            );
            $signature = $this->build_signature($signature_method, $consumer, $token);
            $this->set_parameter("oauth_signature", $signature, false);
        }

        public function build_signature($signature_method, $consumer, $token)
        {
            $signature = $signature_method->build_signature($this, $consumer, $token);
            return $signature;
        }

        private static function generate_timestamp()
        {
            return time();
        }

        private static function generate_nonce()
        {
            mt_srand((double)microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = chr(123)
                    . substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12)
                    . chr(125);
            return $uuid;
        }

        public function __toString()
        {
            return $this->to_url();
        }
    }
}

if (! class_exists('Botble\Pesapal\Libraries\OAuthUtil')) {
    class OAuthUtil
    {
        public static function urlencode_rfc3986($input)
        {
            if (is_array($input)) {
                return array_map(array('Botble\Pesapal\Libraries\OAuthUtil', 'urlencode_rfc3986'), $input);
            } else if (is_scalar($input)) {
                return str_replace(
                    '+',
                    ' ',
                    str_replace('%7E', '~', rawurlencode($input))
                );
            } else {
                return '';
            }
        }

        public static function build_http_query($params)
        {
            if (!$params) return '';

            $keys = OAuthUtil::urlencode_rfc3986(array_keys($params));
            $values = OAuthUtil::urlencode_rfc3986(array_values($params));
            $params = array_combine($keys, $values);

            uksort($params, 'strcmp');

            $pairs = array();
            foreach ($params as $parameter => $value) {
                if (is_array($value)) {
                    natsort($value);
                    foreach ($value as $duplicate_value) {
                        $pairs[] = $parameter . '=' . $duplicate_value;
                    }
                } else {
                    $pairs[] = $parameter . '=' . $value;
                }
            }

            return implode('&', $pairs);
        }
    }
}

