<?php

namespace HelgeSverre\Brain;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Throwable;

class Brain
{
    const FAST_MODEL = 'gpt-3.5-turbo-1106';

    const SLOW_MODEL = 'gpt-4-1106-preview';

    protected string $model = self::FAST_MODEL;

    protected int $maxTokens = 4096;

    protected float $temperature = 0.5;

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

    public function fast(): self
    {
        $this->model = self::FAST_MODEL;

        return $this;
    }

    public function slow(): self
    {
        $this->model = self::SLOW_MODEL;

        return $this;
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

        $response = OpenAI::embeddings()->create($params);

        if (is_array($input) || $input instanceof Collection) {
            return array_map(fn ($embedding) => $embedding->embedding, $response->embeddings);
        } else {
            return $response->embeddings[0]->embedding;
        }
    }

    public function text($prompt, ?int $max = null): string
    {
        return self::toText(OpenAI::chat()->create([
            'model' => $this->model,
            'max_tokens' => $max ?? $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]));
    }

    public function json($prompt, ?int $max = null): ?array
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => $this->model,
                'max_tokens' => $max ?? $this->maxTokens,
                'temperature' => $this->temperature,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return self::toJson($response);
        } catch (Throwable) {
            return null;
        }
    }

    public function list($prompt, ?int $max = null): array
    {
        try {
            $response = OpenAI::chat()->create([
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

            return Arr::get(self::toJson($response), 'items');
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
            $response = OpenAI::chat()->create([
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

            $classification = Arr::get(self::toJson($response), 'classification');

            if ($isEnum) {
                return $classes::tryFrom($classification);
            }

            return $classification;

        } catch (Throwable) {
            return null;
        }
    }

    public function toText(CreateResponse $response, $fallback = null): ?string
    {
        return rescue(fn () => $response->choices[0]->message->content, rescue: $fallback);
    }

    public function toJson(CreateResponse $response, $fallback = null): ?array
    {
        return rescue(fn () => json_decode(self::toText($response), associative: true), rescue: $fallback);
    }
}
