<?php

return [
    'spam_threshold' => 70,

    'actions' =>[
        'verify_sender' => 'Verify the sender through another trusted channel before taking action.',
        'avoid_links' => 'Avoid clicking links in this email. Visit the official website manually instead.',
        'avoid_attachments' => 'Do not open unexpected attachments unless you trust the sender.',
        'do_not_share_info' => 'Do not share passwords, verification codes, payment details, or personal information.',
        'ignore_if_unexpected' => 'If you were not expecting this message, treat it with caution.',
        'confirm_transaction' => 'Confirm whether this transaction or request was initiated by you.',
    ],

    'attachment' => [
        'missing_metadata' => [
            'label' => 'Incomplete Attachment Metadata',
            'severity' => 'info',
            'description' => 'An attachment is missing some expected file information. This is usually harmless but can occasionally indicate malformed content.',
            'action_keys' => ['avoid_attachments'],
            'scores' => [
                'base' => 5,
                'repeat' => 2,
                'cap' => 15,
            ]
        ],

        'dangerous_extensions' => [
            'label' => 'Executable Attachment Detected',
            'severity' => 'danger',
            'description' => 'This email contains an executable file type that could run malicious code on your device.',
            'action_keys' => ['avoid_attachments'],
            'types' => [
                'exe', 'bat', 'cmd', 'com', 'scr', 'msi',
                'ps1', 'vbs', 'js', 'jse', 'wsf', 'lnk',
                'jar', 'apk', 'sh', 'iso', 'img', 'vhd', 'hta'
            ],
            'scores' => [
                'base' => 30,
                'repeat' => 5,
                'cap' => 50
            ]
        ],

        'macro_risk_extensions' => [
            'label' => 'Macro-Enabled Document',
            'severity' => 'warning',
            'description' => 'This attachment contains embedded macros, which may be used legitimately but are also commonly abused by malware.',
            'action_keys' => ['avoid_attachments'],
            'types' => [
                'docm', 'xlsm', 'pptm'
            ],
            'scores' => [
                'base' => 20,
                'repeat' => 5,
                'cap' => 40,
            ]
        ],

        'archive_extensions' => [
            'label' => 'Compressed Attachment',
            'severity' => 'info',
            'description' => 'This email includes a compressed archive. Archives are commonly used legitimately but can hide malicious files.',
            'action_keys' => ['avoid_attachments'],
            'types' => [
                'zip', 'rar', '7z', 'tar', 'gz'
            ],
            'scores' => [
                'base' => 10,
                'repeat' => 3,
                'cap' => 25,
            ]
        ],

        'expected_mime_types' => [
            'label' => 'Attachment Type Mismatch',
            'severity' => 'danger',
            'description' => 'An attachment’s actual file type does not match what its extension suggests. This can indicate disguised malicious content.',
            'action_keys' => ['avoid_attachments'],
            'types' => [
                'pdf' => ['application/pdf'],
                'jpg' => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png' => ['image/png'],
                'txt' => ['text/plain'],

                'zip' => ['application/zip', 'application/x-zip-compressed'],
                'rar' => ['application/vnd.rar', 'application/x-rar-compressed'],
                '7z' => ['application/x-7z-compressed'],

                'docx' => [
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ],
                'xlsx' => [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ],
                'pptx' => [
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ],

                'pptm' => [
                    'application/vnd.ms-powerpoint.presentation.macroenabled.12',
                ],

                'docm' => [
                    'application/vnd.ms-word.document.macroenabled.12'
                ],
                'xlsm' => [
                    'application/vnd.ms-excel.sheet.macroenabled.12'
                ],
            ],
        ],

        'unexpected_mime_types' => [
            'label' => 'Attachment Type Mismatch',
            'severity' => 'danger',
            'description' => 'An attachment’s detected file type does not match what its extension suggests.',
            'action_keys' => ['avoid_attachments'],
            'scores' => [
                'base' => 25,
                'repeat' => 5,
                'cap' => 45,
            ],
        ],
    ],

    'header' =>[
        'missing_authentication_results' => [
            'label' => 'Authentication Data Missing',
            'severity' => 'warning',
            'description' => 'This email does not include expected authentication results, making sender verification harder.',
            'action_keys' => ['verify_sender'],
            'scores' => [
                'base' => 15,
                'repeat' => 0,
                'cap' => 15,
            ]
        ],

        'spf_fail' => [
            'label' => 'SPF Verification Failed',
            'severity' => 'warning',
            'description' => 'The sending server failed SPF verification. This can happen legitimately but may also indicate sender spoofing.',
            'scores' => [
                'base' => 25,
                'repeat' => 0,
                'cap' => 25,
            ]
        ],

        'dkim_fail' => [
            'label' => 'DKIM Verification Failed',
            'severity' => 'warning',
            'description' => 'The email failed DKIM verification, meaning its authenticity could not be fully confirmed.',
            'scores' => [
                'base' => 25,
                'repeat' => 0,
                'cap' => 25,
            ]
        ],

        'dmarc_fail' => [
            'label' => 'DMARC Verification Failed',
            'severity' => 'danger',
            'description' => 'The email failed DMARC verification, which is a strong indicator that sender identity may not be trustworthy.',
            'action_keys' => ['ignore_if_unexpected'],    
            'scores' => [
                'base' => 35,
                'repeat' => 0,
                'cap' => 35,
            ]
        ],

        'dmarc_fail_arc_pass'=> [
            'label' => 'DMARC Failed but ARC Passed',
            'severity' => 'info',
            'description' => 'The email failed DMARC verification, but the ARC chain passed. This often occurs when legitimate messages are forwarded or distributed through mailing lists.',
            'action_keys' => ['verify_sender_if_unexpected'],
            'scores' => [
                'base' => 15,
                'repeat' => 0,
                'cap' => 15,
            ],
        ],

        'reply_to_mismatch' => [
            'label' => 'Reply-To Mismatch',
            'severity' => 'warning',
            'description' => 'Replies to this email would go to a different address than the visible sender.',
            'action_keys' => ['verify_sender'],
            'scores' => [
                'base' => 20,
                'repeat' => 0,
                'cap' => 20,
            ]
        ],

        'return_path_mismatch' => [
            'label' => 'Return-Path Mismatch',
            'severity' => 'warning',
            'action_keys' => ['verify_sender'],
            'description' => 'The technical return address differs from the visible sender. This can be legitimate but may warrant caution.',
            'scores' => [
                'base' => 15,
                'repeat' => 0,
                'cap' => 15,
            ]
        ],
    ],
    
    'link' => [
        'ip_address_url' => [
            'label' => 'IP Address Link',
            'severity' => 'warning',
            'description' => 'This email links directly to an IP address instead of a recognizable domain.',
            'action_keys' => ['avoid_links'],
            'scores' => [
                'base' => 25,
                'repeat' => 5,
                'cap' => 40,
            ],
        ],

        'url_shortener' => [
            'label' => 'Shortened Link',
            'severity' => 'warning',
            'description' => 'This email contains a shortened URL, which hides the final destination.',
            'action_keys' => ['avoid_links'],
            'types' => [
                'bit.ly',
                'tinyurl.com',
                't.co',
                'goo.gl',
                'ow.ly',
                'is.gd',
                'buff.ly',
                'cutt.ly',
                'rebrand.ly',
            ],
            'scores' => [
                'base' => 20,
                'repeat' => 5,
                'cap' => 35,
            ],
        ],

        'insecure_http' => [
            'label' => 'Insecure Link',
            'severity' => 'info',
            'description' => 'This email contains a link that does not use HTTPS encryption.',
            'action_keys' => ['avoid_links'],
            'scores' => [
                'base' => 10,
                'repeat' => 3,
                'cap' => 25,
            ],
        ],

        'suspicious_tld' => [
            'label' => 'Suspicious Domain Ending',
            'severity' => 'warning',
            'description' => 'This email links to a domain ending that is commonly abused in suspicious campaigns.',
            'action_keys' => ['avoid_links'],
            'types' => [
                'zip',
                'mov',
                'click',
                'top',
                'xyz',
                'tk',
                'ml',
                'ga',
                'cf',
                'gq',
            ],
            'scores' => [
                'base' => 15,
                'repeat' => 3,
                'cap' => 30,
            ],
        ],

        'visible_url_mismatch' => [
            'label' => 'Link Mismatch',
            'severity' => 'danger',
            'description' => 'The visible link text does not match the actual destination.',
            'action_keys' => ['avoid_links'],
            'scores' => [
                'base' => 30,
                'repeat' => 5,
                'cap' => 50,
            ],
        ],
    ],

    'sender' => [
        'missing_sender' => [
            'label' => 'Missing Sender',
            'severity' => 'warning',
            'description' => 'This email does not include clear sender information.',
            'action_keys' => ['verify_sender'],
            'scores' => [
                'base' => 20,
                'repeat' => 0,
                'cap' => 20,   
            ]
        ],

        'suspicious_display_name' => [
            'label' => 'Suspicious Display Name',
            'severity' => 'danger',
            'description' => 'The sender name appears to imitate a trusted brand or contact.',
            'action_keys' => ['verify_sender'],
            'scores' => [
                'base' => 30,
                'repeat' => 5,
                'cap' => 50,   
            ]
        ],

        'trusted_brands' => [
            'paypal' => ['paypal.com'],
            'google' => ['google.com'],
            'microsoft' => ['microsoft.com', 'outlook.com'],
            'apple' => ['apple.com'],
            'facebook' => ['facebook.com', 'meta.com'],
            'instagram' => ['instagram.com'],
            'netflix' => ['netflix.com'],
            'amazon' => ['amazon.com'],
        ],
    ],
    
   'text' => [
        'urgency_patterns' => [
            'label' => 'Urgent Language',
            'severity' => 'info',
            'description' => 'The message uses urgent or pressure-based wording.',
            'action_keys' => ['use_caution'],
            'types' => [
                '/\burgent\b/iu',
                '/\bimmediate action\b/iu',
                '/\bact now\b/iu',
                '/\bfinal warning\b/iu',
                '/\bwithin 24 hours\b/iu',
                '/\bexpires today\b/iu',
                '/\baccount will be closed\b/iu',
                '/\brespond immediately\b/iu',
                '/\blast chance\b/iu',

                // Romanian
                '/\burgent\b/iu',
                '/\bimediat\b/iu',
                '/\bultima incercare\b/iu',
                '/\bultima si ultima incercare\b/iu',
                '/\bcazul va fi inchis\b/iu',
                '/\bin termen de\b/iu',
                '/\bzile lucratoare\b/iu',
                '/\bnu se primeste niciun raspuns\b/iu',
            ],

            'scores' => [
                'base' => 8,
                'repeat' => 5,
                'cap' => 20,
            ],
        ],

        'credential_patterns' => [
            'label' => 'Credential Request',
            'severity' => 'danger',
            'description' => 'The message appears to request passwords, login details, verification, or personal account information.',
            'action_keys' => ['do_not_share_info', 'verify_sender'],
            'types' => [
                '/\bverify\s+(your\s+)?account\b/iu',
                '/\bconfirm\s+(your\s+)?account\b/iu',
                '/\bupdate\s+(your\s+)?password\b/iu',
                '/\breset\s+(your\s+)?password\b/iu',
                '/\bpassword\s+expired\b/iu',
                '/\blog\s?in\s+to\s+(your\s+)?account\b/iu',
                '/\bsign\s+in\s+to\s+(your\s+)?account\b/iu',
                '/\baccount\s+(has\s+been\s+)?suspended\b/iu',
                '/\baccount\s+(will\s+be\s+)?closed\b/iu',
                '/\bsecurity\s+verification\b/iu',
                '/\bconfirm\s+(your\s+)?identity\b/iu',
                '/\bverify\s+(your\s+)?identity\b/iu',
                '/\bunauthorized\s+(login|access|transaction)\b/iu',
                '/\bsuspicious\s+activity\b/iu',
                '/\baccount\s+locked\b/iu',
                '/\brestore\s+(your\s+)?access\b/iu',
                '/\btemporarily\s+restricted\b/iu',
                '/\bprovide your information\b/iu',
                '/\bsend your details\b/iu',
                '/\bconfirm your details\b/iu',
                '/\bpersonal information\b/iu',
                '/\bphone number\b/iu',
                '/\baddress\b/iu',
                '/\boccupation\b/iu',

                // Romanian
                '/\bfurnizati\b/iu',
                '/\btrimiteți informațiile\b/iu',
                '/\btrimiteti informatiile\b/iu',
                '/\binformatii necesare\b/iu',
                '/\bdatele dumneavoastra\b/iu',
                '/\bprenume si nume\b/iu',
                '/\btara\b/iu',
                '/\badresa\b/iu',
                '/\bnumar de telefon\b/iu',
                '/\bocupatie\b/iu',
            ],

            'scores' => [
                'base' => 15,
                'repeat' => 5,
                'cap' => 40,
            ],
        ],

        'financial_patterns' => [
            'label' => 'Financial Language',
            'severity' => 'info',
            'description' => 'The message references payments, invoices, refunds, banking, or financial activity.',
            'action_keys' => ['confirm_request'],
            'types' => [
                '/\bpayment\s+failed\b/iu',
                '/\bpayment\s+declined\b/iu',
                '/\bpayment\s+overdue\b/iu',
                '/\binvoice\s+attached\b/iu',
                '/\bunpaid\s+invoice\b/iu',
                '/\binvoice\b/iu',
                '/\bbilling\s+issue\b/iu',
                '/\bbank\s+account\b/iu',
                '/\brefund\b/iu',
                '/\btax\s+refund\b/iu',
                '/\bwire\s+transfer\b/iu',
                '/\bgift\s+card\b/iu',
                '/\bunpaid\s+balance\b/iu',
                '/\btransaction\s+failed\b/iu',
                '/\bsubscription\s+(renewal|expired|expires)\b/iu',
                '/\border\s+confirmation\b/iu',
                '/\brenewal\s+notice\b/iu',
                '/\bconfirm\s+(your\s+)?payment\b/iu',

                // Romanian
                '/\bplata\b/iu',
                '/\bdespagubire\b/iu',
                '/\bajutor\b/iu',
                '/\bvaloare de\b/iu',
                '/\beur\b/iu',
                '/\beuro\b/iu',
                '/\bun milion\b/iu',
                '/\bmilioane\b/iu',
                '/\btransfer bancar\b/iu',
                '/\bcompensatii\b/iu',
                '/\bfonduri\b/iu',
            ],

            'scores' => [
                'base' => 10,
                'repeat' => 5,
                'cap' => 40,
            ],
        ],

        'empty_text_image_heavy_email' => [
                'label' => 'Image-Heavy Email',
                'severity' => 'info',
                'description' => 'The email has little readable text and may rely heavily on images.',
                'action_keys' => ['use_caution'],
            'scores' => [
                'base' => 12,
                'repeat' => 5,
                'cap' => 20,
            ],
        ],
    ],
];