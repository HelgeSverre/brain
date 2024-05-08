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

## 🛠 Usage

### 🔧 Basic Setup

After installation, set up the Brain Facade in `config/app.php`:

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

### 📄 Example: Text Classification with Arrays or Enums

`Brain::classify` simplifies the categorization of text. You can classify text using either an array of options or an
Enum class.

#### Array Classification

Pass a list of categories as an array to classify your text.

```php
use HelgeSverre\Brain\Facades\Brain;

$input = 'banana';
$categories = ["bread", "animal", "car", "plane"];

$result = Brain::fast()->classify($input, $categories);
```

This method evaluates 'banana' and categorizes it as one of the provided options.

#### Enum Classification

For structured categorization, use an Enum class.

```php
use HelgeSverre\Brain\Facades\Brain;

enum Category: string {
    case Fruit = 'fruit';
    case Animal = 'animal';
    case Car = 'car';
}

$input = 'banana';

$result = Brain::fast()->classify($input, Category::class);
```

Here, 'banana' is classified into the most fitting Enum category.

### 📄 Example: Generating a Vector Embedding

This method returns a vector embedding of the input, or a list of vector embeddings if you pass an array or a
collection.

```php
use HelgeSverre\Brain\Facades\Brain;

// Single embedding
$result = Brain::embedding('banana');
// Returns ['0.123', '0.456', '0.789' ....]

// Or, for multiple inputs:
$result = Brain::embedding(['banana', 'apple', 'orange']);
// Returns [['0.123', '0.456', '0.789' ....], ['0.123', '0.456', '0.789' ....], ['0.123', '0.456', '0.789' ....]]

// Or, for a collection of inputs:
$result = Brain::embedding(collect(['banana', 'apple', 'orange']));
// Returns [['0.123', '0.456', '0.789' ....], ['0.123', '0.456', '0.789' ....], ['0.123', '0.456', '0.789' ....]]
```

## Changing the Base URL to use Together.AI, Mistral.AI, Perplexity.AI or other compatible API

You can change the base URL to use other compatible APIs by using the `usingTogetherAI`, `usingMistralAI`
or `usingPerplexity` methods.

```php
use HelgeSverre\Brain\Facades\Brain;

Brain::usingTogetherAI()->text('Hello, world!');
Brain::usingMistralAI()->text('Hello, world!');
Brain::usingPerplexity()->text('Hello, world!');
```

Or you can set the base URL directly, note that the API must be compatible with OpenAI's API, specifically the Chat
completion endpoints.

Also note that JSON Mode (as used by `Brain::json`, `Brain::classify`, `Brain::embedding` and `Brain::list`) is not
supported by all APIs.

```php
use HelgeSverre\Brain\Facades\Brain;

Brain::baseUrl('api.example.com');
```

## 📖 Available Methods

| Method                     | Parameters                                    | Description                                                                                                                                                                          |
|----------------------------|-----------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `Brain::maxTokens()`       | `int $maxTokens`                              | Sets the maximum number of tokens (words) the AI response can contain.                                                                                                               |
| `Brain::temperature()`     | `float $temperature`                          | Sets the 'temperature' for the AI responses, influencing the randomness of the output.                                                                                               |
| `Brain::apiKey()`          | `string $apiKey`                              | Sets the API Key to use for subsequent requests.                                                                                                                                     |
| `Brain::timeout()`         | `int $timeout`                                | Sets the request timeout in seconds.                                                                                                                                                 |
| `Brain::fast()`            | *None*                                        | Sets the AI model to 'gpt-3.5-turbo-1106' for faster responses.                                                                                                                      |
| `Brain::slow()`            | *None*                                        | Sets the AI model to 'gpt-4-1106-preview' for more detailed responses.                                                                                                               |
| `Brain::text()`            | `$prompt, ?int $max = null`                   | Sends a text prompt to the AI and returns a text response. Optionally set a custom maximum token limit for this request.                                                             |
| `Brain::json()`            | `$prompt, ?int $max = null`                   | Sends a prompt to the AI and returns a response in JSON format. Optionally set a custom maximum token limit for this request.                                                        |
| `Brain::list()`            | `$prompt, ?int $max = null`                   | Sends a prompt to the AI and returns a list of items in an array, useful for generating multiple suggestions or ideas. Optionally set a custom maximum token limit for this request. |
| `Brain::classify()`        | `$input, array\|StringBackedEnum $categories` | Classifies the given input text into one of the provided categories. Categories can be an array of strings or an Enum class.                                                         |
| `Brain::embedding()`       | `$input`                                      | Uses the `text-embedding-ada-002` model to generate an embedding vector, returns a single vector for string input, an array of vectors when passed an array or collection.           |
| `Brain::toText()`          | `CreateResponse $response, $fallback = null`  | Converts an OpenAI `CreateResponse` object to a text string. Includes an optional fallback value.                                                                                    |
| `Brain::toJson()`          | `CreateResponse $response, $fallback = null`  | Converts an OpenAI `CreateResponse` object to a JSON object. Includes an optional fallback value.                                                                                    |
| `Brain::usingTogetherAI()` | *None*                                        | Uses the [Together.AI](https://www.together.ai/) API instead of OpenAI                                                                                                               |
| `Brain::usingMistralAI()`  | *None*                                        | Uses the [Mistral.AI](https://mistral.ai/) API instead of OpenAI                                                                                                                     |
| `Brain::usingPerplexity()` | *None*                                        | Uses the [Perplexity.AI](https://www.perplexity.ai/) API instead of OpenAI                                                                                                           |
| `Brain::usingGroq()`       | *None*                                        | Uses the [Groq](https://console.groq.com/playground) API instead of OpenAI                                                                                                           |

## Using other providers (Mistral, Together, Perplexity, Groq etc)

```php
use HelgeSverre\Brain\Facades\Brain;

Brain::apiKey("api-key-from-together")->usingTogetherAI()->text('Hello, world!');

Brain::apiKey("api-key-from-mistral")->usingMistralAI()->text('Hello, world!');

Brain::apiKey("api-key-from-perplexity")->usingPerplexity()->text('Hello, world!');

Brain::apiKey("api-key-from-groq")->usingGroq()->text('Hello, world!');
```

## 📜 License

This package is licensed under the MIT License. For more details, refer to the [License File](LICENSE.md).
