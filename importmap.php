<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 *
 * @return array<string, array{    // Import name as key, description of the imported file as value
 *     path: string,               // Logical, relative or absolute path to the file
 *     type?: 'js'|'css'|'json',   // Type of the file, defaults to 'js'
 *     entrypoint?: bool,          // Whether the file is an entrypoint, for 'js' only
 * }|array{
 *     version: string,            // Version of the remote package
 *     package_specifier?: string, // Remote "package-name/path" specifier, defaults to the import name
 *     type?: 'js'|'css'|'json',
 *     entrypoint?: bool,
 * }>
 */
return [
    'app' => ['path' => './assets/app.js', 'entrypoint' => true],
    '@hotwired/stimulus' => ['version' => '3.2.2'],
    '@symfony/stimulus-bundle' => ['path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js'],
    '@hotwired/turbo' => ['version' => '8.0.23'],
    '@toast-ui/editor' => ['version' => '3.2.2'],
    'prosemirror-model' => ['version' => '1.25.9'],
    'prosemirror-view' => ['version' => '1.30.1'],
    'prosemirror-transform' => ['version' => '1.12.0'],
    'prosemirror-state' => ['version' => '1.4.2'],
    'prosemirror-keymap' => ['version' => '1.2.1'],
    'prosemirror-commands' => ['version' => '1.5.0'],
    'prosemirror-inputrules' => ['version' => '1.2.0'],
    'prosemirror-history' => ['version' => '1.3.0'],
    'orderedmap' => ['version' => '2.1.1'],
    'w3c-keyname' => ['version' => '2.2.6'],
    'rope-sequence' => ['version' => '1.3.3'],
    'prosemirror-view/style/prosemirror.min.css' => ['version' => '1.30.1', 'type' => 'css'],
    '@toast-ui/editor/dist/toastui-editor.css' => ['version' => '3.2.2', 'type' => 'css'],
    '@toast-ui/editor/dist/theme/toastui-editor-dark.css' => ['version' => '3.2.2', 'type' => 'css'],
];
