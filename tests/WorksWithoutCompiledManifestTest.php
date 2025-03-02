<?php

namespace Tonysm\TailwindCss\Tests;

use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Tonysm\TailwindCss\Testing\InteractsWithTailwind;

class WorksWithoutCompiledManifestTest extends TestCase
{
    use InteractsWithTailwind;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('_testing/missing-manifest', fn() => View::file(__DIR__ . '/stubs/welcome.blade.php'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function throws_exception_when_missing_manifest(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The Tailwind CSS manifest does not exist.');

        $this->withoutExceptionHandling()
            ->get('_testing/missing-manifest');

        $this->fail('Expected an exception to be thrown, but it did not.');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function works_without_compiled_manifest_file(): void
    {
        $this->withoutTailwind()
            ->get('_testing/missing-manifest')
            ->assertOk();
    }
}
