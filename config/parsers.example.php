<?php

$config = [
    /**
     * Define your custom parser name
     */
    'my_parser_name' => [
        /**
         * Mode of operation: fixed, dinamic
         */
        'mode' => 'fixed',
        /**
         * Discard this number of lines from top of file
         */
        'discard_top' => 0, // Default 0 dont discart any top lines
        /**
         * Discard this number of lines from the end of file
         */
        'discard_bottom' => 0, // Default 0 dont discard any bottom lines
        /**
         * The number of lines to sumarize before start parsing the content
         * OBS: If the line count not match a divisor, las lines will be ignored
         */
        'line_counter' => 1, // Default 1 parse input file line by line
        /**
         * Divisor text for dinamic mode
         */
        'divisor_text' => 'divisor',
        /**
         * Discard every line that contains this values
         */
        'discard_contains' => [
            // Values to discard when found
        ],
        /**
         * The input file encoding for correct parsing Ex: ISO-8859-1
         */
        'input_encoding' => 'UTF-8', // Default encoding
        /**
         * When this string was found, the file processing ends and the current line is discarded
         */
        'end_file_string' => '', // Default empty
        /**
         * Input file delimiter ONLY for csv and txt files
         */
        'delimiter' => ',', // Default to comma ,
        /**
         * Mapper configuration rules
         * See more in: http://todo
         */
        'mapper' => [
            'c1' => 'dinamic:fn1',
            'c2' => 'dinamic:fn2',
        ],
        /**
         * Customized function definition
         */
        'custom_steps' => [
            'fn1' => function ($lines) {

                dd($lines);

            },
            'fn2' => function ($lines) {

                dd($lines);
                
            }
        ]
    ]
];
