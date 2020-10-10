<?php

use Aigletter\Menu\Render\MenuHtmlRenderer;

return [
    'test' => [
        'repository' => '',
        'renderer' => [
            'class' => MenuHtmlRenderer::class,
            'menu_wrapper' => [
                'tag' => 'div',
                'attributes' => [
                    'class' => 'nav'
                ]
            ],
            'item_wrapper' => [
                'tag' => 'span',
                'attributes' => [
                    'class' => 'nav-item'
                ]
            ]
        ]
    ],
];