<?php

namespace HelgeSverre\Brain;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use StringBackedEnum;
use Throwable;

class Brain
{
    const FAST_MODEL = 'gpt-3.5-turbo-1106';

    const SLOW_MODEL = 'gpt-4-1106-preview';

    public ?string $model = self::FAST_MODEL;

    public int $maxTokens = 4096;

    public float $temperature = 0.5;

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

    public function text($prompt, ?int $max = null, bool $fast = true): string
    {
        return self::toText(OpenAI::chat()->create([
            'model' => $this->model ?? $fast ? self::FAST_MODEL : self::SLOW_MODEL,
            'max_tokens' => $max ?? $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]));
    }

    public function json($prompt, ?int $max = null, bool $fast = true): ?array
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => $this->model ?? $fast ? self::FAST_MODEL : self::SLOW_MODEL,
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

    public function list($prompt, ?int $max = null, bool $fast = true): array
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => $this->model ?? $fast ? self::FAST_MODEL : self::SLOW_MODEL,
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

    public function classify(string $input, array|StringBackedEnum $classes, ?int $max = null, bool $fast = true): null|string|StringBackedEnum
    {
        if (is_array($classes)) {
            $values = $classes;
            $isEnum = false;
        } elseif ($classes instanceof StringBackedEnum) {
            $values = array_column($classes::cases(), 'value');
            $isEnum = true;
        } else {
            throw new InvalidArgumentException('classes provided is not an array, nor an enum.');
        }

        try {
            $response = OpenAI::chat()->create([
                'model' => $this->model ?? $fast ? self::FAST_MODEL : self::SLOW_MODEL,
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
