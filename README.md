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
| `Brain::classify()`    | `$input, $categories`                          | Classifies the given input text into one of the provided categories. Categories can be an array of strings or an Enum class.                                                         |
| `Brain::toText()`      | `CreateResponse $response, $fallback = null`   | Converts an OpenAI `CreateResponse` object to a text string. Includes an optional fallback value.                                                                                    |
| `Brain::toJson()`      | `CreateResponse $response, $fallback = null`   | Converts an OpenAI `CreateResponse` object to a JSON object. Includes an optional fallback value.                                                                                    |

## 📜 License

This package is licensed under the MIT License. For more details, refer to the [License File](LICENSE.md).
