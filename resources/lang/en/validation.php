<?php

return [
    'required' => ':attribute 不能为空',
    'string'   => ':attribute 必须是字符串',
    'max'      => [
        'string' => ':attribute 不能超过 :max 个字符',
        'file'   => ':attribute 不能超过 :max KB',
    ],
    'in'       => ':attribute 的值无效',
    'email'    => ':attribute 格式不正确',
    'image'    => ':attribute 必须是图片文件',
    'nullable' => '',
    'attributes' => [],
];
