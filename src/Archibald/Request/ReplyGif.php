<?php

namespace Archibald\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class ReplyGif
 *
 * @package Archibald\Request
 */
class ReplyGif
{
    private $gifEndpoint = 'http://replygif.net/api/gifs';
    private $tagEndpoint = 'http://replygif.net/api/tags';
    private $apiKey = '39YAprx5Yi';

    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Get all reaction tags from the ReplyGif database.
     *
     * @return RequestError|array
     */
    public function getTags()
    {
        try {
            $response = $this->client->get(
                $this->tagEndpoint,
                [
                    'query' => [
                        'api-key' => $this->apiKey,
                        'reaction' => 1
                    ]
                ]
            );

            $responseBody = $response->getBody();
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? $e->getResponse() : false;

            return new RequestError('bad-request', $response, $e->getRequest());
        }

        $result = json_decode($responseBody);

        $tags = [];

        if (!empty($result)) {
            foreach ($result as $tag) {
                $tags[$tag->title] = $tag->count;
            }
        }

        return $tags;
    }

    /**
     * Search for a tag in the ReplyGif database.
     *
     * @param string $tag Tag to search for.
     *
     * @return RequestError|array
     */
    public function getGifs($tag)
    {
        try {
            $response = $this->client->get(
                $this->gifEndpoint,
                [
                    'query' => [
                        'api-key' => $this->apiKey,
                        'tag' => $tag
                    ]
                ]
            );

            $responseBody = $response->getBody();
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? $e->getResponse() : false;

            return new RequestError('bad-request', $response, $e->getRequest());
        }

        $result = json_decode($responseBody);

        if (empty($result)) {
            return new RequestError('not-found');
        }

        $gifs = [];

        foreach ($result as $gif) {
            $gifs[] = $gif->file;
        }

        return $gifs;
    }
}