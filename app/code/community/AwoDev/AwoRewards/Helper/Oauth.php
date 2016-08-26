<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Oauth extends Mage_Core_Helper_Abstract {

	////////////////// global.php open//////////////
	
 
	function api($url,$logtype,$params,$access_token, $usePost=false, $passOAuthInHeader=true,$port=443) {

		$params['oauth_callback'] = $this->callback;
        $params['oauth_token'] = !empty($access_token['oauth_token']) ? $access_token['oauth_token'] : '';
		$response = $this->oAuthRequest($url,$logtype, $params,!empty($access_token['oauth_token_secret']) ? $access_token['oauth_token_secret']:null,$usePost);

		$json_object = array();
		if (!empty($response)) {
			list($info, $header, $body) = $response;
			if ($body) {
				$this->logit($logtype.":INFO:response:");
				$json_object = json_decode($this->json_pretty_print($body), true);
			}
		}
		
		return $json_object;
	}
	

	function get_request_token($usePost=false, $passOAuthInHeader=false, $returnFullResponse=false) {

		$url = $this->requestTokenURL;
		$params['oauth_callback'] = $this->callback;
		$response = $this->oAuthRequest($url, 'getreqtok', $params, null, $usePost, $passOAuthInHeader);

		if($returnFullResponse) return $response;
		
		$body_parsed = array();
		// extract successful response
        if (!empty($response)) {
			list($info, $header, $body) = $response;
			$body_parsed = $this->oauth_parse_str($body);
			if (!empty($body_parsed)) {
				$this->logit("getreqtok:INFO:response_body_parsed:");
				//print_r($body_parsed);
			}
		}

		if(!empty($body_parsed['oauth_token']) && !empty($body_parsed['oauth_token_secret'])) {
			$session = Mage::getSingleton('customer/session');
			$session->setData('aworewards_oauth_token',$this->rfc3986_decode($body_parsed['oauth_token']));
			$session->setData('aworewards_oauth_token_secret',$this->rfc3986_decode($body_parsed['oauth_token_secret']));
		}
		
        return $body_parsed;
    }
	
	function get_access_token($request_token, $request_token_secret, $oauth_verifier, $usePost=false, $passOAuthInHeader=true) {

		$url = $this->accessTokenURL;
		$params['oauth_token'] = $this->rfc3986_decode($request_token);
		$params['oauth_verifier'] = $this->rfc3986_decode($oauth_verifier);
		$response = $this->oAuthRequest($url, 'getacctok', $params, $this->rfc3986_decode($request_token_secret), $usePost, $passOAuthInHeader);
 
		$body_parsed = array();
		if (!empty($response)) {
			list($info, $header, $body) = $response;
			$body_parsed = $this->oauth_parse_str($body);
			if (!empty($body_parsed)) {
				$this->logit("getacctok:INFO:response_body_parsed:");
				foreach($body_parsed as $k=>$val) $body_parsed[$k] = $this->rfc3986_decode($val);
			}
		}
		return $body_parsed;
	}
	
    function get_contacts ($access_token, $emails_count, $usePost=false, $passOAuthInHeader=true, $port=443) {

        $url = method_exists($this,'override_contact_url') ? $this->override_contact_url() : $this->contactsURL;
        $params['alt'] = 'json';
		$params['format'] = 'json';
		$params['view'] = 'compact';
        $params['max-results'] = $emails_count;
        $params['oauth_token'] = !empty($access_token['oauth_token']) ? $access_token['oauth_token'] : '';

		$response = $this->oAuthRequest($url, 'callcontact', $params, !empty($access_token['oauth_token_secret']) ? $access_token['oauth_token_secret'] : null, $usePost, $passOAuthInHeader,$port);
		
		if (!empty($response)) {
			list($info, $header, $body) = $response;
			if ($body) {
				$this->logit("callcontact:INFO:response:");
				return json_decode($this->json_pretty_print($body), true);
			}
		}

		return $response;
    }
 	
	function oAuthRequest($url, $logtype, $params, $token_secret=null, $usePost=false, $passOAuthInHeader=true,$port=443) {

		$retarr = array();  // return value
		$response = array();

		$params['oauth_version'] = '1.0';
		$params['oauth_nonce'] = mt_rand();
		$params['oauth_timestamp'] = time();
		$params['oauth_consumer_key'] = $this->oauth_consumer_key;
		if(!empty($this->extra_params)) {
			foreach($this->extra_params as $k=>$v) $params[$k] = $v;
		}
 
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_signature'] = $this->oauth_compute_hmac_sig($usePost ? 'POST' : 'GET', $url, $params, $this->oauth_consumer_secret, $token_secret);
//     
		$headers = array();
		if ($passOAuthInHeader) {
			$query_parameter_string = $this->oauth_http_build_query($params, false);
			$header = $this->build_oauth_header($params);
			$headers[] = $header;
		} else {
			$query_parameter_string = $this->oauth_http_build_query($params);
		}


		if ($usePost){
			$request_url = $url;
			$this->logit("$logtype:INFO:request_url:$request_url");
			$this->logit("$logtype:INFO:post_body:$query_parameter_string");
			$headers[] = 'Content-Type: application/x-www-form-urlencoded';
			$response = $this->do_post($request_url, $query_parameter_string, $port, $headers);
		} else {
			$request_url = $url . ($query_parameter_string ? ('?' . $query_parameter_string) : '' );
			$this->logit("$logtype:INFO:request_url:$request_url");
			$response = $this->do_get($request_url, $port, $headers);
		}

		return $response;
		
		if (!empty($response)) {
			list($info, $header, $body) = $response;
			$body_parsed = $this->oauth_parse_str($body);
			if (!empty($body_parsed)) {
				$this->logit("$logtype:INFO:response_body_parsed:");
				foreach($body_parsed as $k=>$val) $body_parsed[$k] = $this->rfc3986_decode($val);
			}
			$retarr = $response;
			$retarr[] = $body_parsed;
		}
		return $body_parsed;
	}
	
	
	
	
	function logit($msg, $preamble=true) {
		if(!$this->debug) return;
		//  date_default_timezone_set('America/Los_Angeles');
		$now = date(DateTime::ISO8601, time());
		error_log(($preamble ? "+++${now}:" : '') . $msg);
	}

   
	function do_get($url, $port=80, $headers=NULL) {
		$retarr = array();  // Return value
		$curl_opts = array(CURLOPT_URL => $url,
			CURLOPT_PORT => $port,
			CURLOPT_POST => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true);


		if ($headers) {
			$curl_opts[CURLOPT_HTTPHEADER] = $headers;
		}

		$response = $this->do_curl($curl_opts);

		if (!empty($response)) {
			$retarr = $response;
		}

		return $retarr;
	}

 
	function do_post($url, $postbody, $port=80, $headers=NULL) {
		$retarr = array();  // Return value

		$curl_opts = array(CURLOPT_URL => $url,
			CURLOPT_PORT => $port,
			CURLOPT_POST => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POSTFIELDS => $postbody,
			CURLOPT_RETURNTRANSFER => true);

		if ($headers) {
			$curl_opts[CURLOPT_HTTPHEADER] = $headers;
		}

		$response = $this->do_curl($curl_opts);

		if (!empty($response)) {
			$retarr = $response;
		}

		return $retarr;
	}

  
	function do_curl($curl_opts) {

		$retarr = array();  // Return value

		if (!$curl_opts) {
			$this->logit("do_curl:ERR:curl_opts is empty");
			return $retarr;
		}


		// Open curl session

		$ch = curl_init();

		if (!$ch) {
			$this->logit("do_curl:ERR:curl_init failed");
			return $retarr;
        }

		// Set curl options that were passed in
		curl_setopt_array($ch, $curl_opts);

		// Ensure that we receive full header
		curl_setopt($ch, CURLOPT_HEADER, true);

		if ($this->debug) {
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
		}

		// Send the request and get the response
		ob_start();
		$response = curl_exec($ch);
		$curl_spew = ob_get_contents();
		ob_end_clean();
		$this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($curl_spew) {
			$this->logit("do_curl:INFO:curl_spew begin");
			$this->logit($curl_spew, false);
			$this->logit("do_curl:INFO:curl_spew end");
		}

		// Check for errors
		if (curl_errno($ch)) {
			$errno = curl_errno($ch);
			$errmsg = curl_error($ch);
			$this->logit("do_curl:ERR:$errno:$errmsg");
			curl_close($ch);
			unset($ch);
			return $retarr;
		}

		if ($this->debug) {
			$this->logit("do_curl:DBG:header sent begin");
			$header_sent = curl_getinfo($ch, CURLINFO_HEADER_OUT);
			$this->logit($header_sent, false);
			$this->logit("do_curl:DBG:header sent end");
		}

		// Get information about the transfer
		$info = curl_getinfo($ch);

		// Parse out header and body
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		// Close curl session
		curl_close($ch);
		unset($ch);

		if ($this->debug) {
			$this->logit("do_curl:DBG:response received begin");
			if (!empty($response)) {
				$this->logit($response, false);
			}
			$this->logit("do_curl:DBG:response received end");
		}

		// Set return value
		array_push($retarr, $info, $header, $body);

		return $retarr;
	}

 
	function json_pretty_print($json, $html_output=false) {
		$spacer = '  ';
		$level = 1;
		$indent = 0; // current indentation level
		$pretty_json = '';
		$in_string = false;

		$len = strlen($json);

		for ($c = 0; $c < $len; $c++) {
			$char = $json[$c];
			switch ($char) {
				case '{':
				case '[':
					if (!$in_string) {
						$indent += $level;
						$pretty_json .= $char . "\n" . str_repeat($spacer, $indent);
					} else {
						$pretty_json .= $char;
					}
					break;
				case '}':
				case ']':
					if (!$in_string) {
						$indent -= $level;
						$pretty_json .= "\n" . str_repeat($spacer, $indent) . $char;
					} else {
						$pretty_json .= $char;
					}
					break;
				case ',':
					if (!$in_string) {
						$pretty_json .= ",\n" . str_repeat($spacer, $indent);
					} else {
						$pretty_json .= $char;
					}
					break;
				case ':':
					if (!$in_string) {
						$pretty_json .= ": ";
					} else {
						$pretty_json .= $char;
					}
					break;
				case '"':
					if ($c > 0 && $json[$c - 1] != '\\') {
						$in_string = !$in_string;
					}
				default:
					$pretty_json .= $char;
					break;
			}
		}

		return ($html_output) ?
				'<pre>' . htmlentities($pretty_json) . '</pre>' :
				$pretty_json . "\n";
	}


	function oauth_http_build_query($params, $excludeOauthParams=false) {

		$query_string = '';
		if (!empty($params)) {

			// rfc3986 encode both keys and values
			$keys = $this->rfc3986_encode(array_keys($params));
			$values = $this->rfc3986_encode(array_values($params));
			$params = array_combine($keys, $values);


			uksort($params, 'strcmp');


			$kvpairs = array();
			foreach ($params as $k => $v) {
				if ($excludeOauthParams && substr($k, 0, 5) == 'oauth') {
					continue;
				}
				if (is_array($v)) {
					// If two or more parameters share the same name,
					// they are sorted by their value. OAuth Spec: 9.1.1 (1)
					natsort($v);
					foreach ($v as $value_for_same_key) {
						array_push($kvpairs, ($k . '=' . $value_for_same_key));
					}
				} else {
					// For each parameter, the name is separated from the corresponding
					// value by an '=' character (ASCII code 61). OAuth Spec: 9.1.1 (2)
					array_push($kvpairs, ($k . '=' . $v));
				}
			}

			// Each name-value pair is separated by an '&' character, ASCII code 38.
			// OAuth Spec: 9.1.1 (2)
			$query_string = implode('&', $kvpairs);
		}
		return $query_string;
	}


	function oauth_parse_str($query_string) {
		$query_array = array();

		if (!empty($query_string)) {

			// Separate single string into an array of "key=value" strings
			$kvpairs = explode('&', $query_string);
	
			if(!empty($kvpairs)) {
				// Separate each "key=value" string into an array[key] = value
				foreach ($kvpairs as $pair) {
					@list($k, $v) = explode('=', $pair, 2);

					// Handle the case where multiple values map to the same key
					// by pulling those values into an array themselves
					if (isset($query_array[$k])) {
						// If the existing value is a scalar, turn it into an array
						if (is_scalar($query_array[$k])) {
							$query_array[$k] = array($query_array[$k]);
						}
						array_push($query_array[$k], $v);
					} else {
						$query_array[$k] = $v;
					}
				}
			}
		}

		return $query_array;
	}


	function build_oauth_header($params, $realm='') {
		$header = 'Authorization: OAuth';
		if(!empty($realm)) $header .= ' realm="'.$realm.'"';
		
		foreach ($params as $k => $v) {
			if (substr($k, 0, 5) == 'oauth') {
				$header .= ',' . $this->rfc3986_encode($k) . '="' . $this->rfc3986_encode($v) . '"';
			}
		}
		return $header;
	}

  
	function oauth_compute_plaintext_sig($consumer_secret, $token_secret) {
		return ($consumer_secret . '&' . $token_secret);
	}


	function oauth_compute_hmac_sig($http_method, $url, $params, $consumer_secret, $token_secret) {

		$base_string = $this->signature_base_string($http_method, $url, $params);
		$signature_key = $this->rfc3986_encode($consumer_secret) . '&' . $this->rfc3986_encode($token_secret);
		$sig = base64_encode(hash_hmac('sha1', $base_string, $signature_key, true));
		$this->logit("oauth_compute_hmac_sig:DBG:sig:$sig");
		return $sig;
	}

	/**
	 * Make the URL conform to the format scheme://host/path
	 * @param string $url
	 * @return string the url in the form of scheme://host/path
	 */
	function normalize_url($url) {
		$parts = parse_url($url);

		$scheme = $parts['scheme'];
		$host = $parts['host'];
		if(isset($parts['port'])) {
			$port = $parts['port'];
		}
		else $port = '';
		$path = $parts['path'];

		if (!$port) {
			$port = ($scheme == 'https') ? '443' : '80';
		}
		if (($scheme == 'https' && $port != '443')
		|| ($scheme == 'http' && $port != '80')) {
			$host = "$host:$port";
		}

		return "$scheme://$host$path";
	}

	/**
	 * Returns the normalized signature base string of this request
	 * @param string $http_method
	 * @param string $url
	 * @param array $params
	 * The base string is defined as the method, the url and the
	 * parameters (normalized), each urlencoded and the concated with &.
	 * @see http://oauth.net/core/1.0/#rfc.section.A.5.1
	 */
	function signature_base_string($http_method, $url, $params) {
		// Decompose and pull query params out of the url
		$query_str = parse_url($url, PHP_URL_QUERY);
		if ($query_str) {
			$parsed_query = $this->oauth_parse_str($query_str);
			// merge params from the url with params array from caller
			$params = array_merge($params, $parsed_query);
		}

		// Remove oauth_signature from params array if present
		if (isset($params['oauth_signature'])) {
			unset($params['oauth_signature']);
		}

		// Create the signature base string. Yes, the $params are double encoded.
       
		$base_string = $this->rfc3986_encode(strtoupper($http_method)) . '&' .
				$this->rfc3986_encode($this->normalize_url($url)) . '&' .
				$this->rfc3986_encode($this->oauth_http_build_query($params));

		$this->logit("signature_base_string:INFO:normalized_base_string:$base_string");

		return $base_string;
	}

	/**
	 * Encode input per RFC 3986
	 * @param string|array $raw_input
	 * @return string|array properly rfc3986 encoded raw_input
	 * If an array is passed in, rfc3896 encode all elements of the array.
	 * @link http://oauth.net/core/1.0/#encoding_parameters
	 */
	function rfc3986_encode($raw_input){

		if (is_array($raw_input)) {
			//return array_map($this->rfc3986_encode, $raw_input);
			return array_map(array($this, 'rfc3986_encode'), $raw_input);

			// return $this->rfc3986_encode($raw_input);
		} else if (is_scalar($raw_input)) {
			return str_replace('%7E', '~', rawurlencode($raw_input));
		} else {
			return '';
		}
	}

	function rfc3986_decode($raw_input) {
		return rawurldecode($raw_input);
	}

}

