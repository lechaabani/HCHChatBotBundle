<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use HCH\ChatBotBundle\Exception\TranslationException;
use Psr\Cache\CacheItemPoolInterface;

class TranslationService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheItemPoolInterface $cache,
        private string $apiKey,
        private array $config
    ) {}

    public function translate(string $text, string $targetLang, ?string $sourceLang = null): string
    {
        $cacheKey = md5($text . $targetLang . ($sourceLang ?? ''));
        
        return $this->cache->get($cacheKey, function() use ($text, $targetLang, $sourceLang) {
            try {
                $response = $this->httpClient->request('POST', 'https://translation.googleapis.com/language/translate/v2', [
                    'query' => [
                        'key' => $this->apiKey
                    ],
                    'json' => [
                        'q' => $text,
                        'target' => $targetLang,
                        'source' => $sourceLang,
                        'format' => 'text'
                    ]
                ]);

                $data = $response->toArray();
                return $data['data']['translations'][0]['translatedText'] ?? $text;
            } catch (\Exception $e) {
                throw new TranslationException('Erreur de traduction: ' . $e->getMessage());
            }
        });
    }

    public function detectLanguage(string $text): string
    {
        try {
            $response = $this->httpClient->request('POST', 'https://translation.googleapis.com/language/translate/v2/detect', [
                'query' => [
                    'key' => $this->apiKey
                ],
                'json' => [
                    'q' => $text
                ]
            ]);

            $data = $response->toArray();
            return $data['data']['detections'][0][0]['language'] ?? 'en';
        } catch (\Exception $e) {
            throw new TranslationException('Erreur de détection de langue: ' . $e->getMessage());
        }
    }

    public function getAvailableLanguages(): array
    {
        return $this->config['translation']['available_languages'];
    }
} 