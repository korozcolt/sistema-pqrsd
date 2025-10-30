<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    |
    | This value represents the current version of the application following
    | Semantic Versioning 2.0.0 (https://semver.org/)
    |
    | Format: MAJOR.MINOR.PATCH
    |
    | MAJOR: Incompatible API changes
    | MINOR: Add functionality in a backwards compatible manner
    | PATCH: Backwards compatible bug fixes
    |
    */

    'version' => env('APP_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Build Number
    |--------------------------------------------------------------------------
    |
    | Optional build number or commit hash for detailed tracking
    |
    */

    'build' => env('APP_BUILD', null),

    /*
    |--------------------------------------------------------------------------
    | Release Date
    |--------------------------------------------------------------------------
    |
    | Date of the current version release
    |
    */

    'release_date' => env('APP_RELEASE_DATE', '2025-10-29'),

    /*
    |--------------------------------------------------------------------------
    | Version File
    |--------------------------------------------------------------------------
    |
    | Path to the version file that will be automatically updated
    |
    */

    'file' => base_path('VERSION'),
];
