<?php

$config = [
    /**
     * Define your custom parser name
     */
    'my_parser_name' => [
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
         * Discard every line that contains this values
         */
        'discard_contains' => [
            // Values to discard when found
        ],
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
            // Implement your mappers
        ],
        /**
         * Customized function definition
         */
        'custom_steps' => [
            // Implement your custom function steps
        ]
    ]
];
