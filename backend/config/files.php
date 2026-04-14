<?php

/**
 * This configuration file defines which Eloquent model fields contain file paths,
 * so that file cleanup operations (such as deleting old or unused files) can be automated.
 *
 * The 'fileFieldsMap' array maps each model class to an array of attribute names
 * that store file paths. These fields will be used by traits or services responsible
 * for file deletion when a model is updated or permanently deleted.
 *
 * Example usage:
 * - When a model is updated and a file field changes, the old file will be deleted.
 * - When a model is force deleted, all files in the listed fields will be deleted.
 */

return [
    'fileFieldsMap' => [
        \Modules\Students\Models\Student::class => [
            'profile_photo_url',
            'id_card_photo_url'
        ],
    ],
];

