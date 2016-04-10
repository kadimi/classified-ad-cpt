<?php
/*
Title: Ad Details
Post Type: ad
*/

piklist('field', [
    'field' => 'price'
    , 'label' => 'Price'
    , 'validate' => [
        [
            'type' => 'range'
            , 'options' => ['min' => .01, 'max' => 100 * 1e6]
        ]
    ]
    , 'required' => true
]);