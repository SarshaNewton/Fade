<?php

namespace App\Services\Security\Checks;

use App\Models\Email;
use App\Services\Security\DTO\SecurityScanResult;

use DOMDocument;
use DOMXPath;

class LinkRiskCheck extends BaseRiskCheck{

    protected array $currentScores = [
        'ip_address_url' => 0,
        'url_shortener' => 0,
        'insecure_http' => 0,
        'suspicious_tld' => 0,
        'visible_url_mismatch' => 0,
    ];

    public function __construct() {
        $this->config = config('security.link');
    }

    public function run(Email $email, SecurityScanResult $result): void{
        $links = $this->extractLinks($email);

        if(empty($links)){
            return;
        }

        foreach($links as $link){
            $url = trim($link['url']);
            $visibleText = trim($link['text'] ?? '');

            $host = parse_url($url, PHP_URL_HOST);
            $host = $host ? strtolower($host) : null;

            if(!$host){
                continue;
            }

            $this->checkIpAddressUrl($url, $host, $result);
            $this->checkUrlShortener($url, $host, $result);
            $this->checkInsecureHttp($url, $host, $result);
            $this->checkSuspiciousTld($url, $host, $result);
            $this->checkVisibleUrlMatch($url, $visibleText, $result);
        }
    }

    private function checkIpAddressUrl(string $url, string $host, SecurityScanResult $result): void{
        if(filter_var($host, FILTER_VALIDATE_IP)){
            $this->handleResult($result, 'ip_address_url','ip_address_url', "Link uses an IP address instead of a domain: {$url}");
        }
    }

    private function checkUrlShortener(string $url, string $host, SecurityScanResult $result): void{
        if(in_array($host, $this->config['url_shortener']['types'], true)){
            $this->handleResult($result,  'url_shortener',  'url_shortener', "Link uses a URL shortener: {$url}");
        }
    }

    private function checkInsecureHttp(string $url, string $host, SecurityScanResult $result): void{
        if(str_starts_with($url, 'http://')){
            $this->handleResult($result,  'insecure_http',  'insecure_http', "Link uses insecure HTTP: {$host}");
        }
    }

    private function checkSuspiciousTld(string $url, string $host, SecurityScanResult $result): void{
        $tld = $this->getTld($host);

        if(in_array($tld, $this->config['suspicious_tld']['types'], true)){
            $this->handleResult($result,  'suspicious_tld',  'suspicious_tld', "Link uses a suspicious TLD: {$url}");
        }
    }

    private function checkVisibleUrlMatch(string $url, string $visibleText, SecurityScanResult $result): void{
         if (!str_starts_with($visibleText, 'http')) {
            return;
        }

        $actualHost = $this->normalizeHost(parse_url($url, PHP_URL_HOST));
        $visibleHost = $this->normalizeHost(parse_url($visibleText, PHP_URL_HOST));

        if(!$actualHost || !$visibleHost){
            return;
        }

        if($actualHost !== $visibleHost){
            $this->handleResult($result,  'visible_url_mismatch',  'visible_url_mismatch', "Visible link text points to '{$visibleHost}', but actual link goes to '{$actualHost}'.");
        }
    }
    

    private function extractLinks(Email $email): array{
        $links = [];

        $html = $email->body_html ?? '';
        $text = $email->body_text ?? '';

        if ($html !== '') {
            $links = array_merge($links, $this->extractLinksFromHtml($html));
        }

        if ($text !== '') {
            $links = array_merge($links, $this->extractLinksFromText($text));
        }

        return $links;
    }

    private function extractLinksFromHtml(string $html): array{
        $links = [];

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);

        $xpath = new DOMXPath($dom);
        $anchors = $xpath->query('//a[@href]');

        foreach ($anchors as $anchor) {
            $links[] = [
                'url' => $anchor->getAttribute('href'),
                'text' => $anchor->textContent,
            ];
        }

        libxml_clear_errors();

        return $links;
    }

    private function extractLinksFromText(string $text): array{
        preg_match_all('/https?:\/\/[^\s<>"\']+/i', $text, $matches);

        return collect($matches[0] ?? [])
            ->map(fn ($url) => [
                'url' => $url,
                'text' => $url,
            ])
            ->toArray();
    }

    private function getTld(string $host): string{
        $parts = explode('.', $host);
        return end($parts);
    }

    private function normalizeHost(?string $host): ?string {
        if (!$host) {
            return null;
        }

        $host = strtolower($host);

        return str_starts_with($host, 'www.')
            ? substr($host, 4)
            : $host;
    }
}