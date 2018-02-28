<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Define which configuration should be used
    |--------------------------------------------------------------------------
    */

    'use' => 'local',

    /*
    |--------------------------------------------------------------------------
    | AMQP properties separated by key
    |--------------------------------------------------------------------------
    */

    'properties' => [
        'local' => [
            'host'                  => getenv('RABBITMQ_HOST'),
            'port'                  => getenv('RABBITMQ_PORT'),
            'username'              => getenv('RABBITMQ_USERNAME'),
            'password'              => getenv('RABBITMQ_PASSWORD'),
            'vhost'                 => '/',
            'connect_options'       => [],
            'ssl_options'           => [],

            'exchange'              => getenv('RABBITMQ_EXCHANGE'),
            'exchange_type'         => 'topic',
            'exchange_passive'      => false,
            'exchange_durable'      => true,
            'exchange_auto_delete'  => false,
            'exchange_internal'     => false,
            'exchange_nowait'       => false,
            'exchange_properties'   => [],

            'queue_force_declare'   => false,
            'queue_passive'         => false,
            'queue_durable'         => true,
            'queue_exclusive'       => false,
            'queue_auto_delete'     => false,
            'queue_nowait'          => false,
            'queue_properties'      => ['x-ha-policy' => ['S', 'all']],

            'consumer_tag'          => '',
            'consumer_no_local'     => false,
            'consumer_no_ack'       => false,
            'consumer_exclusive'    => false,
            'consumer_nowait'       => false,
            'timeout'               => 0,
            'persistent'            => false,
        ],
    ],
];
