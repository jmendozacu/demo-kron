<?php

/**
 * HTTP Helper class.
 *
 */
class Ebizmarts_BakerlooRestful_Helper_Http {

    /**
     * POST request to given url.
     *
     * @param string $url
     * @param string $requestBody
     * @param array $requestHeaders
     */
    public function POST($url, $requestBody, $requestHeaders) {
        return $this->request($url, $requestBody, $requestHeaders, true);
    }

    /**
     * GET request to given url.
     *
     * @param string $url
     * @param string $requestBody
     * @param array $requestHeaders
     */
    public function GET($url, $requestBody, $requestHeaders) {
        return $this->request($url, $requestBody, $requestHeaders);
    }

    /**
     * Request to given url.
     *
     * @param string $url
     * @param string $requestBody
     * @param array $requestHeaders
     */
	public function request($url, $requestBody, $requestHeaders, $post = false, $verifyPeer = false, $verifyHost = false) {

        $output = array();

        $curlSession = curl_init();

        $UA = isset($requestHeaders['B-User-Agent']) ? $requestHeaders['B-User-Agent'] : null;
        if(!is_null($UA)) {
        	curl_setopt($curlSession, CURLOPT_USERAGENT, $UA);
    	}

        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_HEADER, false);
        curl_setopt($curlSession, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($curlSession, CURLOPT_POST, $post);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_TIMEOUT, 90);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, $verifyPeer);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, $verifyHost);

        $rawresponse = curl_exec($curlSession);

        // Check that a connection was made
        if (curl_error($curlSession)) {
        	$rawresponse = curl_error($curlSession);
        }

        curl_close($curlSession);

        return $rawresponse;

	}

    public function getJsonPayload($request) {
        $payload = $request->getRawBody();

        $data = json_decode($payload);

        if(!is_object($data)) {
            Mage::throwException("Invalid post data.");
        }

        return $data;
    }
}