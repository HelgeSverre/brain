<?php

namespace HelgeSverre\Brain;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Throwable;

class Brain
{
    const FAST_MODEL = 'gpt-3.5-turbo-1106';

    const SLOW_MODEL = 'gpt-4-1106-preview';

    protected ?string $apiKey = null;

    protected ?int $timeout = null;

    protected string $model = self::FAST_MODEL;

    protected int $maxTokens = 4096;

    protected float $temperature = 0.5;

    protected ?string $baseUrl = null;

    public function maxTokens(int $maxTokens): self
    {
        $this->maxTokens = $maxTokens;

        return $this;
    }

    public function temperature(float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function model(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function baseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function apiKey(?string $apiKey = null): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function timeout(?int $timeout = null): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @deprecated This will eventually be removed
     */
    public function fast(): self
    {
        return $this->usingOpenAI()->model(self::FAST_MODEL);
    }

    /**
     * @deprecated This will eventually be removed
     */
    public function slow(): self
    {
        return $this->usingOpenAI()->model(self::SLOW_MODEL);
    }

    public function usingOpenAI(): self
    {
        $this->baseUrl = 'api.openai.com/v1';

        return $this;
    }

    /**
     * Use the Together.AI API.
     *
     * @see https://www.together.ai/
     *
     * @return $this
     */
    public function usingTogetherAI(): self
    {
        $this->baseUrl = 'api.together.xyz/v1';

        return $this;
    }

    /**
     * Use the Mistral.AI API.
     *
     * @see https://docs.mistral.ai/
     *
     * @return $this
     */
    public function usingMistralAI(): self
    {
        $this->baseUrl = 'api.mistral.ai/v1';

        return $this;
    }

    /**
     * Use the Perplexity API.
     *
     * @see https://docs.perplexity.ai/docs/getting-started
     *
     * @return $this
     */
    public function usingPerplexity(): self
    {
        $this->baseUrl = 'api.perplexity.ai';

        return $this;
    }

    /**
     * Use the Groq API.
     *
     * @see https://console.groq.com/docs/quickstart
     *
     * @return $this
     */
    public function usingGroq(): self
    {
        $this->baseUrl = 'https://api.groq.com/openai/v1';

        return $this;
    }

    public function client()
    {
        return OpenAI::factory()
            ->withApiKey($this->apiKey ?? config('openai.api_key'))
            ->withOrganization(config('openai.organization'))
            ->withHttpHeader('OpenAI-Beta', 'assistants=v1')
            ->withBaseUri($this->baseUrl ?: 'api.openai.com/v1')
            ->withHttpClient(new Client([
                'timeout' => $this->timeout ?? config('openai.request_timeout', 30),
            ]))
            ->make();
    }

    /**
     * @param  string<"text-embedding-3-small"|"text-embedding-3-large"|"text-embedding-ada-002">  $model
     */
    public function embedding(
        string|array|Collection $input,
        string $model = 'text-embedding-3-small',
        ?int $dimensions = 1536
    ): array {
        $params = array_filter([
            'model' => $model,
            'input' => $input,
            'dimensions' => $model == 'text-embedding-ada-002' ? null : $dimensions,
        ]);

        $response = $this->client()->embeddings()->create($params);

        if (is_array($input) || $input instanceof Collection) {
            return array_map(fn ($embedding) => $embedding->embedding, $response->embeddings);
        } else {
            return $response->embeddings[0]->embedding;
        }
    }

    public function text($prompt, ?int $max = null): string
    {

        $response = $this->client()->chat()->create([
            'model' => $this->model,
            'max_tokens' => $max ?? $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        return self::responseToText($response);
    }

    public function json($prompt, ?int $max = null): ?array
    {
        try {
            $response = $this->client()->chat()->create([
                'model' => $this->model,
                'max_tokens' => $max ?? $this->maxTokens,
                'temperature' => $this->temperature,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => 'Output in JSON format'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return self::responseToJson($response);
        } catch (Throwable) {
            return null;
        }
    }

    public function list($prompt, ?int $max = null): array
    {
        try {
            $response = $this->client()->chat()->create([
                'model' => $this->model,
                'max_tokens' => $max ?? $this->maxTokens,
                'temperature' => $this->temperature,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "{$prompt}\n Output the list as a JSON array, under the key 'items'",
                    ],
                ],
            ]);

            return Arr::get(self::responseToJson($response), 'items');
        } catch (Throwable) {
            return [];
        }
    }

    public function classify(string $input, $classes, ?int $max = null)
    {
        if (is_array($classes)) {
            $values = $classes;
            $isEnum = false;
        } elseif (enum_exists($classes)) {
            $values = array_column($classes::cases(), 'value');
            $isEnum = true;
        } else {
            throw new InvalidArgumentException('classes provided is not an array, nor an enum.');
        }

        try {
            $response = $this->client()->chat()->create([
                'model' => $this->model,
                'max_tokens' => $max ?? $this->maxTokens,
                'temperature' => $this->temperature,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => implode("\n", [
                            "Classify '$input' into one of the following classifications:".
                            implode(', ', $values).
                            "The output should a JSON object with the key of 'classification' and the selected classification as the sole value:",
                        ]),
                    ],
                ],
            ]);

            $classification = Arr::get(self::responseToJson($response), 'classification');

            if ($isEnum) {
                return $classes::tryFrom($classification);
            }

            return $classification;

        } catch (Throwable) {
            return null;
        }
    }

    public function responseToText(CreateResponse $response, $fallback = null): ?string
    {
        return rescue(
            callback: fn () => $response->choices[0]->message->content,
            rescue: $fallback
        );
    }

    public function responseToJson(CreateResponse $response, $fallback = null): ?array
    {
        return rescue(
            callback: fn () => json_decode(
                json: self::responseToText($response),
                associative: true
            ),
            rescue: $fallback
        );
    }
}
