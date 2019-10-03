<?php

$config = [
    'case1' => [
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
            'field1' => 'copy',
            'field2',
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
