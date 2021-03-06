<?php

namespace Waraqa\Articles;

use GuzzleHttp\Client as GuzzleClient;

class Article
{
    const GET_ARTICLE_URI = 'client-api/articles/content/';

    const PUBLISH_ARTICLE_URI = 'client-api/articles/publish';

    /**
     * base_uri
     *
     * @var mixed
     */
    private $base_uri;

    /**
     * client_access_id
     *
     * @var mixed
     */
    private $client_access_id;

    /**
     * client_password
     *
     * @var mixed
     */
    private $client_password;

    /**
     * client
     *
     * @var mixed
     */
    private $client;

    /**
     * token
     *
     * @var mixed
     */
    private $token;

    /**
     * __construct
     *
     * @param  mixed $base_uri
     * @return void
     */
    public function __construct(String $base_uri, String $client_access_id, String $client_password)
    {
        $this->base_uri = $base_uri;
        $this->client_access_id = $client_access_id;
        $this->client_password = $client_password;

        // Create a client with a base URI        
        $this->client = new GuzzleClient(['base_uri' => $this->base_uri]);
        $this->token = $this->getClientToken();
    }

    /**
     * Get the article from waraqa API
     *
     * @param  Int $article_id
     * @return string
     */
    public function fetchSingle(Int $article_id)
    {
        $request_uri = self::GET_ARTICLE_URI . $article_id;
        // Send a request to $request_uri
        $response = $this->client->request('GET', $request_uri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json'
            ]
        ]);

        $responseContent = json_decode($response->getBody()->getContents());
        return $responseContent->data;
    }

    /**
     * Publish the article to update the status in waraqa API
     *
     * @param  Int $article_id
     * @return string
     */
    public function publishArticle(Int $article_id)
    {
        $request_uri = self::PUBLISH_ARTICLE_URI;

        // Send a request to $request_uri
        $response = $this->client->request('PUT', $request_uri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json'
            ],
            'form_params' => [
                'articles' => [$article_id]
            ]
        ]);

        $responseContent = json_decode($response->getBody()->getContents());
        return $responseContent->data;
    }

    /**
     * Authenticate waraqa API using client credentials and get token
     *
     * @return string
     */
    public function getClientToken()
    {
        $response = $this->client->request(
            'POST',
            'client-api/login',
            [
                'form_params' => [
                    'access_id' => $this->client_access_id,
                    'password' => $this->client_password,
                ]
            ]
        );
        $responseToken = json_decode($response->getBody()->getContents());

        return $responseToken->data->token;
    }
}
