<?php

$config = [
    'testing' => [
        /**
         * Discard this number of lines from top of file
         */  
        'discard_top' => 1,
        /**
         * Discard this number of lines from the end of file
         */
        'discard_bottom' => 0,
        /**
         * The number of lines to index before start parsing the content
         */
        'row_counter' => 1,
        /**
         * Mapper configuration rules
         * See more in: http://todo
         */
        'mapper' => [
            'split_and_equals_test' => 'position:A;split:",",2;equals:"X","true";equals:"true",SELF;equals:"invalid",,"inv";equals:"inv",',
            'number_test' => 'position:B,2;numbers;replace:"5","N"',
            'phone_test' => 'position:C,3;custom:customPhoneParser;custom:customWithParams,1,2,3',
        ],
        /**
         * Customized function definition
         */
        'custom_steps' => [
            'customPhoneParser' => function ($string) {
                $phones = explode('  ', $string);
                foreach($phones as $key => $phone) {
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
        ],
        /**
         * Discard every line that contains this values
         */
        'discard_contains' => [
            //
        ],
        /**
         * When this string was found, the file processing ends!
         */ 
        'end_file_string' => 'rodape'
    ]
];