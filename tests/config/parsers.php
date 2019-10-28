<?php

$config = [
    'testing' => [
        'mode' => 'fixed',
        'discard_top' => 1,
        'discard_bottom' => 0,
        'line_counter' => 3,
        'discard_contains' => [
            //
        ],
        'input_encoding' => 'UTF-8', // Default encoding
        'delimiter' => "	",
        'end_file_string' => 'exittest', // Default empty
        'mapper' => [
            'split_test' => 'position:A;split:",",2',
            'equals_test' => 'position:A;equals:"1,X","true";equals:"true",SELF;equals:"invalid",,"inv";equals:"inv",',
            'number_test' => 'position:B,2;numbers;replace:"5","N"',
            'substr_test' => 'position:B,2;numbers;substr:2,3;substr:1',
            'phone_test' => 'position:C,3;custom:customPhoneParser;custom:customWithParams,1,2,3',
        ],
        'custom_steps' => [
            'customPhoneParser' => function ($string) {
                $phones = explode('  ', $string);
                foreach ($phones as $key => $phone) {
                    $phoneCleaned = preg_replace('/[^0-9]/', '', $phone);
                    if ($phoneCleaned == '') {
                        unset($phones[$key]);
                    } else {
                        $phones[$key] = $phoneCleaned;
                    }
                }
                return implode(";", $phones);
            },
            'customWithParams' => function ($string, $params) {
                return str_replace(';', '', $string).implode('', $params);
            }
        ]
    ],
    'testing_invalid' => [
        'mode' => 'fixed',
        'discard_top' => 1,
        'discard_bottom' => 0,
        'line_counter' => 3,
        'discard_contains' => [
            //
        ],
        'input_encoding' => 'UTF-8', // Default encoding
        'delimiter' => "	",
        'end_file_string' => 'exittest', // Default empty
        'mapper' => [
            //Empty must generate error
        ],
        'custom_steps' => [
            //
        ]
    ],
    'testing_dinamic' => [
        'mode' => 'dinamic',
        'divisor_text' => 'divisor',
        'discard_top' => 0,
        'discard_bottom' => 0,
        'discard_contains' => [
            //
        ],
        'input_encoding' => 'UTF-8', // Default encoding
        'delimiter' => "	",
        'end_file_string' => 'exittest', // Default empty
        'mapper' => [
            'c1' => 'dinamic:c1',
            'c2' => 'dinamic:c2',
        ],
        'custom_steps' => [
            'c1' => function ($lines) {
                return implode('', $lines);
            },
            'c2' => function ($lines) {
                return implode('', $lines);
            },
        ]
    ]
];
