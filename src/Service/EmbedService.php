<?php

namespace App\Service;

class EmbedService
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function fetchEmbedData(string $url)
    {
        $url = "https://iframe.ly/api/oembed?url=$url&api_key=$this->apiKey";
        $json = file_get_contents($url);
        return json_decode($json);
    }
}