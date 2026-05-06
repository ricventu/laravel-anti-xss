<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Replacement string
    |--------------------------------------------------------------------------
    |
    | String used by the underlying voku/anti-xss engine to replace the
    | malicious content removed during sanitization. Empty string means
    | the malicious content is simply stripped out.
    |
    */
    'replacement' => '',

    /*
    |--------------------------------------------------------------------------
    | Keep <pre> and <code> tag content
    |--------------------------------------------------------------------------
    |
    | When enabled, the content inside <pre> and <code> tags is preserved
    | as-is and not sanitized. Useful for documentation/snippets. Defaults
    | to false: tag content is sanitized like the rest of the input.
    |
    */
    'keep_pre_and_code_tag_content' => false,

    /*
    |--------------------------------------------------------------------------
    | Strip 4-byte UTF-8 characters
    |--------------------------------------------------------------------------
    |
    | If your database/columns are not utf8mb4 you can ask the engine to
    | strip 4-byte UTF-8 characters (e.g. emoji) before storing.
    |
    */
    'strip_4byte_chars' => false,

    /*
    |--------------------------------------------------------------------------
    | Evil attributes
    |--------------------------------------------------------------------------
    |
    | Extend or shrink the default set of HTML attributes considered evil
    | by voku/anti-xss. Both keys accept an array of attribute names.
    |
    */
    'evil_attributes' => [
        'add' => [],
        'remove' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Evil HTML tags
    |--------------------------------------------------------------------------
    |
    | Extend or shrink the default set of HTML tags considered evil by
    | voku/anti-xss. Both keys accept an array of tag names (without
    | angle brackets).
    |
    */
    'evil_html_tags' => [
        'add' => [],
        'remove' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware options
    |--------------------------------------------------------------------------
    |
    | When the CleanXssInput middleware is registered, request fields whose
    | name appears in `except` are NOT sanitized. Useful for password
    | fields, signed payloads, raw markdown, etc.
    |
    */
    'middleware' => [
        'except' => [
            'password',
            'password_confirmation',
        ],
    ],

];
