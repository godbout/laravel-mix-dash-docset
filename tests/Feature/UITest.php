<?php

namespace Tests\Feature;

use App\Docsets\LaravelMix;
use Godbout\DashDocsetBuilder\Services\DocsetBuilder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UITest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->docset = new LaravelMix();
        $this->builder = new DocsetBuilder($this->docset);

        if (! Storage::exists($this->docset->downloadedDirectory())) {
            fwrite(STDOUT, PHP_EOL . PHP_EOL . "\e[1;33mGrabbing laravel-mix..." . PHP_EOL);
            Artisan::call('grab laravel-mix');
        }

        if (! Storage::exists($this->docset->file())) {
            fwrite(STDOUT, PHP_EOL . PHP_EOL . "\e[1;33mPackaging laravel-mix..." . PHP_EOL);
            Artisan::call('package laravel-mix');
        }
    }

    /** @test */
    public function the_header_gets_removed_from_the_dash_docset_files()
    {
        $header = '<header class="sticky';

        $this->assertStringContainsString(
            $header,
            Storage::get($this->docset->downloadedIndex())
        );

        $this->assertStringNotContainsString(
            $header,
            Storage::get($this->docset->innerIndex())
        );
    }

    /** @test */
    public function the_left_sidebar_gets_removed_from_the_dash_docset_files()
    {
        $leftSidebar = 'id="nav';

        $this->assertStringContainsString(
            $leftSidebar,
            Storage::get($this->docset->downloadedIndex())
        );

        $this->assertStringNotContainsString(
            $leftSidebar,
            Storage::get($this->docset->innerIndex())
        );
    }

    /** @test */
    public function the_right_kinda_sidebar_gets_removed_from_the_dash_docset_files()
    {
        $rightKindaSidebar = '<div class="hidden pt-4 px-6';

        $this->assertStringContainsString(
            $rightKindaSidebar,
            Storage::get($this->docset->downloadedIndex())
        );

        $this->assertStringNotContainsString(
            $rightKindaSidebar,
            Storage::get($this->docset->innerIndex())
        );
    }

    /** @test */
    public function the_footer_gets_removed_from_the_dash_docset_files()
    {
        $footer = '<footer';

        $this->assertStringContainsString(
            $footer,
            Storage::get($this->docset->downloadedIndex())
        );

        $this->assertStringNotContainsString(
            $footer,
            Storage::get($this->docset->innerIndex())
        );
    }
}
