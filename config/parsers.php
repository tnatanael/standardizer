<?php

$config = [
    'testing' => [
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
         */
        'data_line_count' => 1, // Default 1 parse input file line by line
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
        ],
        /**
         * Discard every line that contains this values
         */
        'discard_contains' => [
            // Strings o discard
        ],
        /**
         * When this string was found, the file processing ends
         */ 
        'end_file_string' => 'rodape'
    ]
];
