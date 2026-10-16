@props(['email'])

@php
    $safeHtml = $email->body_html
        ? Purifier::clean($email->body_html, 'email')
        : nl2br(e($email->body_text ?? $email->body_snippet ?? 'No content available.'));

    $iframeHtml = <<<HTML

<!doctype html>
<html>
<head>
    <base target="_blank">
    
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap">
    
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: white;
            overflow-wrap: break-word;
            font-family: 'Figtree', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        img[alt] {
            font-size: 0;
        }
    </style>
</head>
<body>
    {$safeHtml}
</body>
</html>
HTML;
@endphp

<iframe
    class="email-body-frame w-full rounded-xl bg-white min-h-[600px]"
    sandbox="allow-same-origin allow-popups allow-popups-to-escape-sandbox"
    srcdoc="{{ $iframeHtml }}"
    onload="resizeEmailFrame(this)"
></iframe>