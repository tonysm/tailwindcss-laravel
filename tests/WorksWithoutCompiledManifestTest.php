<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Tonysm\TailwindCss\Testing\InteractsWithTailwind;

uses(InteractsWithTailwind::class);

beforeEach(function () {
    Route::get('_testing/missing-manifest', function () {
        return View::file(__DIR__ . '/stubs/welcome.blade.php');
    });
});

it('throws exception when missing manifest', function () {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('The Tailwind CSS manifest does not exist.');

    $this->withoutExceptionHandling()
        ->get('_testing/missing-manifest');

    $this->fail('Expected an exception to be thrown, but it did not.');
});

it('works without compiled manifest file', function () {
    $this->withoutTailwind()
        ->get('_testing/missing-manifest')
        ->assertOk();
});
