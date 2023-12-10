<p align="center"><img src="art/header-1.webp"></p>

![Latest Version on Packagist](https://img.shields.io/packagist/v/helgesverre/ai.svg?style=flat-square) ![Total Downloads](https://img.shields.io/packagist/dt/helgesverre/ai.svg?style=flat-square)

Just a simple wrapper around the OpenAI SDK to make it easier to do quick-n-dirty GenAI-stuff.

----

## 📦 Installation

```shell
composer require helgesverre/ai
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

After installation, set up the AI Facade in `config/app.php`:

```php
'aliases' => [
    'AI' => HelgeSverre\AI\Facades\AI::class,
],
```

### 📝 Example: Generating Blog Titles

Generate a list of blog titles about a given subject:

```php
use HelgeSverre\AI\Facades\AI;

$subject = 'Technology';
$count = 5;

$titles = AI::list("Suggest $count blog titles about '$subject'", 200);

foreach ($titles as $title) {
    echo $title . PHP_EOL;
}
```

### 📄 Example: Generating a Structured Blog Post

Generate a structured blog post in JSON format:

```php
use HelgeSverre\AI\Facades\AI;

$title = 'The Future of Technology';
$style = 'informative';
$minWords = 500;

$response = AI::slow()->json(<<<PROMPT
Create an $style blog post with the title '$title'. 
Write over $minWords words.
PROMPT
);

echo "Title: " . $response['title'] . PHP_EOL;
echo "Body: " . $response['body'] . PHP_EOL;
```

## 📜 License

This package is licensed under the MIT License. For more details, refer to the [License File](LICENSE.md).
