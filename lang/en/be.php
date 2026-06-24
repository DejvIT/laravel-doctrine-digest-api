<?php

return [
    'responses' => [
        'ok'         => 'OK',
        'badRequest' => 'Bad request',
        'notFound'   => [
            'blogger'         => 'Blogger not found',
            'subscriber'      => 'Subscriber not found',
            'article'         => 'Article not found',
            'articleCategory' => 'Article category not found',
        ],
        'forbidden' => [
            'distributed' => 'Cannot modify or delete a distributed article',
            'category'    => 'You are not assigned to this category',
            'notOwner'    => 'You do not own this article',
        ],
        'auth' => [
            'invalidCredentials' => 'Invalid email or password',
            'missingToken'       => 'Bearer token is required',
            'invalidToken'       => 'Invalid or expired token',
        ],
    ],
];
