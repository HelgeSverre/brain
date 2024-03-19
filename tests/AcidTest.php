<?php

beforeEach(function () {
    $this->brain = new HelgeSverre\Brain\Brain();
});

it('It can generate text', function () {
    $text = $this->brain->fast()
        ->maxTokens(100)
        ->text('Say hello');

    expect($text)->toBeString();
});

it('It can give me json', function () {
    $result = $this->brain->fast()
        ->maxTokens(200)
        ->json('generate details for a fictional person with a name, job_title and age, output as JSON');

    expect($result)->toBeArray()
        ->and($result)->toHaveKeys(['name', 'job_title', 'age']);
});

it('It can give me json list', function () {
    $result = $this->brain->fast()
        ->maxTokens(100)
        ->list('list 3 fruits');

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(3);
});

it('It can classify something', function () {
    $result = $this->brain->fast()
        ->maxTokens(50)
        ->classify('banana', ['fruit', 'car', 'animal']);

    expect($result)->toBeString()
        ->and($result)->toEqual('fruit');
});

it('It can classify something with enum', function () {
    enum Category: string
    {
        case fruit = 'fruit';
        case animal = 'animal';
        case car = 'car';
    }

    $result = $this->brain
        ->fast()
        ->maxTokens(50)
        ->classify('banana', Category::class);

    expect($result)->toEqual(Category::fruit);
});

it('it can embed a string and get flat embedding vector array back with text-embedding-ada-002', function () {
    $result = $this->brain->embedding('helge sverre', model: 'text-embedding-ada-002');

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(1536);
});

it('it can embed a string and get flat embedding vector array back with text-embedding-3-small', function () {
    $result = $this->brain->embedding('helge sverre', model: 'text-embedding-3-small');

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(1536);
});

it('it can embed a string and get flat embedding vector array back with text-embedding-3-large', function () {
    $result = $this->brain->embedding('helge sverre', model: 'text-embedding-3-large');

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(1536);
});

it('it can embed a string and get flat embedding vector array back', function () {
    $result = $this->brain->embedding('helge sverre');

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(1536);
});

it('it can embed multiple strings and get an array of flat embedding vector arrays back', function () {
    $result = $this->brain->embedding(['helge', 'sverre']);

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toHaveCount(1536)
        ->and($result[1])->toHaveCount(1536);
});

it('it can embed collection of strings and get an array of flat embedding vector arrays back', function () {
    $result = $this->brain->embedding(collect(['helge', 'sverre']));

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toHaveCount(1536)
        ->and($result[1])->toHaveCount(1536);
});

it('it can use an OpenAI Compatible api endpoint (together.ai)', function () {
    config()->set('openai.api_key', env('TOGETHER_API_KEY'));

    $result = $this->brain
        ->usingTogetherAI()
        ->model('mistralai/Mixtral-8x7B-Instruct-v0.1')
        ->temperature(0.2)
        ->maxTokens(10)
        ->text('Say hello');

    expect($result)->toBeString();
})->skip(fn () => env('TOGETHER_API_KEY') === null, 'No Together API key found');

it('it can use an OpenAI Compatible api endpoint (mistral.ai)', function () {
    config()->set('openai.api_key', env('MISTRAL_API_KEY'));

    $result = $this->brain
        ->usingMistralAI()
        ->model('mistral-tiny')
        ->temperature(0.2)
        ->maxTokens(10)
        ->text('Say hello');

    expect($result)->toBeString();
})->skip(fn () => env('MISTRAL_API_KEY') === null, 'No Mistral API key found');

it('it can use an OpenAI Compatible api endpoint (perplexity.ai)', function () {
    config()->set('openai.api_key', env('PERPLEXITY_API_KEY'));

    $result = $this->brain
        ->usingPerplexity()
        ->model('pplx-7b-chat')
        ->temperature(0.2)
        ->maxTokens(20)
        ->text('Say hello');

    expect($result)->toBeString();
})->skip(fn () => env('PERPLEXITY_API_KEY') === null, 'No Perplexity API key found');

it('it can use an OpenAI Compatible api endpoint (groq.ai)', function () {
    config()->set('openai.api_key', env('GROQ_API_KEY'));

    $result = $this->brain
        ->usingGroq()
        ->model('llama2-70b-4096')
        ->temperature(0.2)
        ->maxTokens(20)
        ->text('Say hello');

    expect($result)->toBeString();
})->skip(fn () => env('GROQ_API_KEY') === null, 'No Groq API key found');

it('It can give me JSON with Groq', function () {
    config()->set('openai.api_key', env('GROQ_API_KEY'));

    $result = $this->brain
        ->usingGroq()
        ->model('llama2-70b-4096')
        ->maxTokens(200)
        ->json('generate details for a fictional person with a name, job_title and age, output as JSON');

    expect($result)->toBeArray()
        ->and($result)->toHaveKeys(['name', 'job_title', 'age']);

})->skip(fn () => env('GROQ_API_KEY') === null, 'No Groq API key found');
