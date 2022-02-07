<?php

/**
 * @file
 * Contains payload_version\payload_version.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */


/**
* Implements hook_views_data().
*/
function payload_version_views_data() {

    $data['views']['table']['group'] = t('Custom Global');
    $data['views']['table']['join'] = [
      // #global is a special flag which allows a table to appear all the time.
      '#global' => [],
    ];
    $data['views']['payload_versions'] = [
        'title' => t('Payload Versions'),
        'help' => t('custom views field plugin for payload version in paragraph.'),
        'field' => [
            'id' => 'payload_versions',
        ],
    ];
    return $data;
}
