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
            , 'options' => ['min' => 1, 'max' => 500]
        ]
    ]
    , 'required' => true
]);