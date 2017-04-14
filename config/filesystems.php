<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Supported: "local", "s3", "rackspace"
    |
    */
    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */
    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'chunks_expire_in' => 604800
        ],
        's3' => [
            'driver' => 's3',
            'key' => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ],
    ],
    'allowed_extensions' => [
        'pdf', 'odt', 'ods', 'odp', 'doc', 'xls', 'ppt', 'docx', 'pptx', 'zip', 'tar.gz', 'iso',
        'jpeg', 'jpg', 'png', 'gif'
    ],
    'allowed_mimetypes' => [
        'application/pdf',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation',
        'application/msword',
        'application/vnd.ms-office',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/zip',
        'application/gzip',
        'application/x-gzip',
        'application/x-iso9660-image',
        'image/jpeg',
        'image/png',
        'image/gif'
    ],
    'allowed_tags_and_limits' => [
        'avatar' => 1,
        'content' => 0 // 0 means no limit.
    ],
    'load_balancing' => [
        'enabled' => true,
        'length' => 2,
        'depth' => 2
    ]
];
