<?php

beforeEach(function () {
    $this->brain = new HelgeSverre\Brain\Brain();
});

it('It can generate text', function () {

    expect($this->brain->fast()->text('Say hello'))->toBeString();
});

it('It can give me json', function () {
    $result = $this->brain->fast()->json('generate details for a fictional person with a name, job_title and age, output as JSON');
    expect($result)->toBeArray()
        ->and($result)->toHaveKeys(['name', 'job_title', 'age']);
});

it('It can give me json list', function () {
    $result = $this->brain->fast()->list('list 3 fruits');

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(3);
});

it('It can classify something', function () {
    $result = $this->brain->fast()->classify('banana', ['fruit', 'car', 'animal']);

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

    $result = $this->brain->fast()->classify('banana', Category::class);

    expect($result)->toEqual(Category::fruit);
});
