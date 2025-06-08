<?php
return [
    'router' => [
        // 框架类型 (auto, laravel, thinkphp, query_string)
        'adapter' => 'auto',

        // 查询字符串适配器配置
        'query_string' => [
            'entry_file' => 'api.php',
            'controller_param' => 'controller',
            'action_param' => 'action'
        ],

        // 控制器扫描目录
        'controller_dirs' => [
        ]
    ],

];