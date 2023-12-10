<p align="center"><img src="art/brain.webp"></p>

![Latest Version on Packagist](https://img.shields.io/packagist/v/helgesverre/brain.svg?style=flat-square) ![Total Downloads](https://img.shields.io/packagist/dt/helgesverre/brain.svg?style=flat-square)

# 🧠 Brain - The Adorably Simple OpenAI Wrapper

Just a small and simple wrapper around the OpenAI SDK.

----

## 📦 Installation

```shell
composer require helgesverre/brain
```

```shell
php artisan vendor:publish --provider="OpenAI\Laravel\ServiceProvider"
```

```dotenv
OPENAI_API_KEY="your-key-here"
OPENAI_REQUEST_TIMEOUT=60
```

## 📖 Method Documentation

| Method                 | Parameters                                     | Description                                                                                                                                                                          |
|------------------------|------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `Brain::maxTokens()`   | `int $maxTokens`                               | Sets the maximum number of tokens (words) the AI response can contain.                                                                                                               |
| `Brain::temperature()` | `float $temperature`                           | Sets the 'temperature' for the AI responses, influencing the randomness of the output.                                                                                               |
| `Brain::fast()`        | *None*                                         | Sets the AI model to 'gpt-3.5-turbo-1106' for faster responses.                                                                                                                      |
| `Brain::slow()`        | *None*                                         | Sets the AI model to 'gpt-4-1106-preview' for more detailed responses.                                                                                                               |
| `Brain::text()`        | `$prompt, ?int $max = null, bool $fast = true` | Sends a text prompt to the AI and returns a text response. Optionally set a custom maximum token limit for this request.                                                             |
| `Brain::json()`        | `$prompt, ?int $max = null, bool $fast = true` | Sends a prompt to the AI and returns a response in JSON format. Optionally set a custom maximum token limit for this request.                                                        |
| `Brain::list()`        | `$prompt, ?int $max = null, bool $fast = true` | Sends a prompt to the AI and returns a list of items in an array, useful for generating multiple suggestions or ideas. Optionally set a custom maximum token limit for this request. |
| `Brain::toText()`      | `CreateResponse $response, $fallback = null`   | Converts an OpenAI `CreateResponse` object to a text string. Includes an optional fallback value.                                                                                    |
| `Brain::toJson()`      | `CreateResponse $response, $fallback = null`   | Converts an OpenAI `CreateResponse` object to a JSON object. Includes an optional fallback value.                                                                                    |

## 🛠 Usage

### 🔧 Basic Setup

After installation, set up the AI Facade in `config/app.php`:

```php
'aliases' => [
    'Brain' => HelgeSverre\Brain\Facades\Brain::class,
],
```

### 📝 Example: Generating Blog Titles

Generate a list of blog titles about a given subject:

```php
use HelgeSverre\Brain\Facades\Brain;

$subject = 'Technology';
$count = 5;

$titles = Brain::list("Suggest $count blog titles about '$subject'", 200);

foreach ($titles as $title) {
    echo $title . PHP_EOL;
}
```

### 📄 Example: Generating a Structured Blog Post

Generate a structured blog post in JSON format:

```php
use HelgeSverre\Brain\Facades\Brain;

$title = 'The Future of Technology';
$style = 'informative';
$minWords = 500;

$response = Brain::slow()->json(<<<PROMPT
Create an $style blog post with the title '$title'. 
Write over $minWords words.
PROMPT
);

echo "Title: " . $response['title'] . PHP_EOL;
echo "Body: " . $response['body'] . PHP_EOL;
```

## 📜 License

This package is licensed under the MIT License. For more details, refer to the [License File](LICENSE.md).
