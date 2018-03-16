<?php

namespace Tests\Feature\Traits;

trait Auth
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    private $AUTH_TYPE_DEFAULT = 'Auth';
    private $AUTH_TYPE_SITE_PROVIDER = 'SiteProvider';

    //------------------------------------------------------------------------------------------------------------------
    // PRIVATED METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Creates a valid headers
     *
     * @access private
     * @param  string $authType
     * @param  string $url
     * @param  array $body
     * @return string
     */
    private function getHeadersAsSiteProvider(string $url, array $body) : array
    {
        return $this->getHeaders($this->AUTH_TYPE_SITE_PROVIDER, $url, $body);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Creates a valid headers
     *
     * @access private
     * @param  string $authType
     * @param  string $url
     * @param  array $body
     * @return string
     */
    private function getHeadersAsAuth(string $url, array $body) : array
    {
        return $this->getHeaders($this->AUTH_TYPE_DEFAULT, $url, $body);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Creates a valid headers
     *
     * @access private
     * @param  string $authType
     * @param  string $url
     * @param  mixed $body
     * @return string
     */
    private function getHeaders(string $authType, string $url, array $body) : array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            $authType => $this->createAuth($url, $body),
        ];
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Creates a valid Auth token
     *
     * @access private
     * @param  string $url
     * @param  array $body
     * @return string
     */
    private function createAuth(string $url, $body) : string
    {
        $hash_id = 'login';
        $qwindo_key = 'key';
        $timestamp = microtime(true);
        $data = json_encode($body);

        // Here we generate the auth token that will allow us to check the authorization for the call
        $token = hash_hmac('sha512', $url.$timestamp.$data, $qwindo_key);

        // Generate the authorization base64 encoded
        return base64_encode(sprintf('%s:%s:%s', $hash_id, $timestamp, $token));
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}