<?php

namespace App\Docsets;

use Godbout\DashDocsetBuilder\Docsets\BaseDocset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class LaravelMix extends BaseDocset
{
    public const CODE = 'laravel-mix';
    public const NAME = 'Laravel Mix';
    public const URL = 'laravel-mix.com';
    public const INDEX = 'docs/main/installation.html';
    public const PLAYGROUND = '';
    public const ICON_16 = 'favicon-16x16.png';
    public const ICON_32 = 'favicon-32x32.png';
    public const EXTERNAL_DOMAINS = [];


    public function grab(): bool
    {
        $toIgnore = implode('|', [
            'cdn-cgi',
            'docs/1.7/',
            'docs/2.0/',
            'docs/2.1/',
            'docs/3.0/',
            'docs/4.0/',
            'docs/4.1/',
            'docs/5.0/',
            'docs/6.0/',
        ]);

        system(
            "echo; wget laravel-mix.com/docs \
                --mirror \
                --trust-server-names \
                --reject-regex='{$toIgnore}' \
                --page-requisites \
                --adjust-extension \
                --convert-links \
                --span-hosts \
                --domains={$this->externalDomains()} \
                --directory-prefix=storage/{$this->downloadedDirectory()} \
                -e robots=off \
                --quiet \
                --show-progress",
            $result
        );

        return $result === 0;
    }

    public function entries(string $file): Collection
    {
        $crawler = HtmlPageCrawler::create(Storage::get($file));

        $entries = collect();

        //

        return $entries;
    }

    public function format(string $file): string
    {
        $crawler = HtmlPageCrawler::create(Storage::get($file));

        //

        return $crawler->saveHTML();
    }
}
