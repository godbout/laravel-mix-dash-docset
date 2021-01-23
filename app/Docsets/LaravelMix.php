<?php

namespace App\Docsets;

use Godbout\DashDocsetBuilder\Docsets\BaseDocset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        /**
         * can't use PCRE regex style, so need to
         * type the whole list of shit (versions)
         * to ignore.
         */
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

        $entries = $entries->union($this->guideEntries($crawler, $file));

        return $entries;
    }

    protected function guideEntries(HtmlPageCrawler $crawler, string $file)
    {
        $entries = collect();

        if (Str::contains($file, "{$this->url()}/docs/main/installation.html")) {
            $crawler->filter('nav#nav li a')->each(function (HtmlPageCrawler $node) use ($entries) {
                $entries->push([
                    'name' => trim($node->text()),
                    'type' => 'Guide',
                    'path' => $this->url() . '/docs/main/' . $node->attr('href'),
                ]);
            });
        }

        return $entries;
    }

    public function format(string $file): string
    {
        $crawler = HtmlPageCrawler::create(Storage::get($file));

        $this->removeHeader($crawler);

        //

        return $crawler->saveHTML();
    }

    protected function removeHeader(HtmlPageCrawler $crawler)
    {
        $crawler->filter('header.sticky')->remove();
    }
}
