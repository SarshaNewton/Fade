<?php

//Supported Mail Providers
if (!function_exists('isSupportedMailProvider')) {
    function isSupportedMailProvider(string $provider): bool {
        return in_array(strtolower($provider), ['google', 'microsoft'], true);
    }
}


if (!function_exists('parseRawHeaders')) {
    function parseRawHeaders(?string $rawHeaders): array {
        if (!$rawHeaders) {
            return [];
        }

        $headers = [];
        $currentHeader = null;

        $lines = preg_split("/\r\n|\n|\r/", $rawHeaders);

        foreach ($lines as $line) {
            // folded header continuation
            if (preg_match('/^\s+/', $line) && $currentHeader) {
                $headers[$currentHeader] .= ' ' . trim($line);
                continue;
            }

            if (!str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);

            $currentHeader = strtolower(trim($key));
            $headers[$currentHeader] = trim($value);
        }

        return $headers;
    }
}

if(!function_exists('findSecurityCategory')) {
    function findSecurityCategory(string $ruleKey): ?string {
        $categories = ['attachment', 'header', 'link', 'sender', 'text'];

        foreach ($categories as $category) {
            if (config("security.$category.$ruleKey")) {
                return $category;
            }
        }

        return null;
    }
}

if (!function_exists('defaultAccountAppearance')) {
    function defaultAccountAppearance(string $provider, int $accountNumber): array {
        $presets = [
            [
                'nickname' => 'Personal',
                'color' => '#354E96',
                'icon' => 'fade',
            ],
            [
                'nickname' => 'Work',
                'color' => '#3BB273',
                'icon' => 'briefcase',
            ],
            [
                'nickname' => 'School',
                'color' => '#7300FF',
                'icon' => 'graduation-cap',
            ],
            [
                'nickname' => ucfirst($provider),
                'color' => '#F58438',
                'icon' => 'mail',
            ],
        ];

        return $presets[$accountNumber] ?? [
            'nickname' => ucfirst($provider) . ' ' . ($accountNumber + 1),
            'color' => '#354E96',
            'icon' => 'fade',
        ];
    }
}