<?php

$config = [
    'uniondata_cadastro' => [
        /**
         * Discard this number of lines from top of file
         */  
        'discard_top' => 3, // Default 0 dont discart any top lines
        /**
         * Discard this number of lines from the end of file
         */
        'discard_bottom' => 1, // Default 0 dont discard any bottom lines
        /**
         * The number of lines to sumarize before start parsing the content
         * OBS: If the line count not match a divisor, las lines will be ignored
         */
        'line_counter' => 5, // Default 1 parse input file line by line
        /**
         * Discard every line that contains this values
         */
        'discard_contains' => [
            'Sistemas Union Data - www.uniondata.com.br',
            'Excellence Administradora de Condomínios Ltda',
            'Relatório de Condôminos Simples',
            'Condomínio:'
        ],
        /**
         * When this string was found, the file processing ends
         */ 
        'end_file_string' => '', // Default empty
        /**
         * Mapper configuration rules
         * See more in: http://todo
         */
        'mapper' => [
            'unidade' => 'position:B,1;split:"/",2',
            'bloco' => 'position:B,1;split:"/",1;equals:"00",,SELF',
            // 'fracao',
            // 'area',
            // 'abatimento',
            'proprietario_nome' => 'position:D,1;split:"CPF:",1',
            'proprietario_telefone' => 'position:D,3;custom:phoneParser',
            // 'proprietario_celular',
            // 'proprietario_forma_de_entrega',
            'proprietario_cpf/cnpj' => 'position:D,1;split:"CPF:",2',
            'proprietario_rg' => 'position:D,2;split:"RG:",2',
            'proprietario_email' => 'position:D,4;split:"e-mail:",2',
            'proprietario_endereco' => 'position:C,5;replace:",",',
            // 'proprietario_complemento',
            'proprietario_cep' => 'position:F,5;split:"  ",2',
            'proprietario_cidade' => 'position:F,5;split:"  ",1;split:"-",1',
            'proprietario_bairro' => 'position:F,5;split:"  ",3',
            'proprietario_estado' => 'position:F,5;split:"  ",1;split:"-",2',
            // 'inquilino_nome',
            // 'inquilino_telefone',
            // 'inquilino_celular',
            // 'inquilino_forma_de_entrega',
            // 'inquilino_cpf/cnpj',
            // 'inquilino_rg',
            // 'inquilino_email',
        ],
        /**
         * Customized function definition
         */
        'custom_steps' => [
            'phoneParser' => function ($string) {
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
            }
        ]
    ],
    'uniondata_cob_inad' => [
        /**
         * Discard this number of lines from top of file
         */  
        'discard_top' => 6, // Default 0 dont discart any top lines
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
            'Processo:',
            'Gerado o Acordo',
            '"Vencto","","Recibo"',
            'Acordo Nro.',
            'Conta Bancária',
            'Relação de Recibos (Objeto do Acordo)',
            'Honorários:',
            'Adicional:',
            'TOTAL:',
        ],
        /**
         * When this string was found, the file processing ends
         */ 
        'end_file_string' => 'Total do Condomínio', // Default empty
        /**
         * Mapper configuration rules
         * See more in: http://todo
         */
        'mapper' => [
            'unidade' => 'position:A;equals:"",PREV,SELF;equals:"0","",SELF',
            'bloco' => 'position:B;equals:"",PREV,SELF',
            'vencimento' => 'position:I',
            //'nosso_numero' => 'position:C;equals:"",PREV,SELF',
            // 'debitar_taxa_bancária',
            // 'data_de_competência',
            // 'atualização_monetária',
            // 'taxa_de_juros_(%)',
            // 'taxa_de_multa_(%)',
            // 'taxa_de_desconto_(%)',
            // 'cobrança_extraordinária',
            // 'data_crédito',
            // 'forma_de_cobrança',
            // 'data_liquidação',
            // 'valor_pago',
            // 'RECEITA_APROPRIACAO[0][conta_categoria]',
            // 'RECEITA_APROPRIACAO[0][complemento]',
            // 'RECEITA_APROPRIACAO[0][valor]',
        ],
        /**
         * Customized function definition
         */
        'custom_steps' => [
            'phoneParser' => function ($string) {
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
            }
        ]
    ],
    'brcondominio_cad' => [
        /**
         * Discard this number of lines from top of file
         */  
        'discard_top' => 1, // Default 0 dont discart any top lines
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
        ],
        /**
         * When this string was found, the file processing ends and the current line is discarded
         */ 
        'end_file_string' => '', // Default empty
        /**
         * Mapper configuration rules
         * See more in: http://todo
         */
        'mapper' => [
            'unidade' => 'position:B;split:" - ",1;custom:myFunction',
            'bloco' => 'position:B;split:" - ",1;custom:myFunction2;equals:"A","Bl. A"',
            // // 'fracao',
            // // 'area',
            // // 'abatimento',
            // 'proprietario_nome' => 'position:D,1;split:"CPF:",1',
            // 'proprietario_telefone' => 'position:D,3;custom:phoneParser',
            // // 'proprietario_celular',
            // // 'proprietario_forma_de_entrega',
            // 'proprietario_cpf/cnpj' => 'position:D,1;split:"CPF:",2',
            // 'proprietario_rg' => 'position:D,2;split:"RG:",2',
            // 'proprietario_email' => 'position:D,4;split:"e-mail:",2',
            // 'proprietario_endereco' => 'position:C,5;replace:",",',
            // // 'proprietario_complemento',
            // 'proprietario_cep' => 'position:F,5;split:"  ",2',
            // 'proprietario_cidade' => 'position:F,5;split:"  ",1;split:"-",1',
            // 'proprietario_bairro' => 'position:F,5;split:"  ",3',
            // 'proprietario_estado' => 'position:F,5;split:"  ",1;split:"-",2',
            // // 'inquilino_nome',
            // // 'inquilino_telefone',
            // // 'inquilino_celular',
            // // 'inquilino_forma_de_entrega',
            // // 'inquilino_cpf/cnpj',
            // // 'inquilino_rg',
            // // 'inquilino_email',
        ],
        /**
         * Customized function definition
         */
        'custom_steps' => [
            'myFunction' => function ($string) {

                return substr($string,1,2);
            },
            'myFunction2' => function ($string) {
                return substr($string,0,1);
            }
        ]
    ]
];
