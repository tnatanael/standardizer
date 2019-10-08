# "Padronizador" e "Conversor" de planilhas estruturadas

Esta aplicação converte planilhas estruturadas em arquivos de saida modificados para que sejam importados em sistemas de maneira automática, evitando a necessidade de retrabalho manual ou readequações de código.

Para este objetivo criamos uma configuração de parseamento, para cada tipo de planilha a converter, é possível configurar multiplas padrões de parseamento, e a ideia é suportar virtualmente qualquer tipo de planilha existente.

O parseamento de planilhas é baseado em padrões, ou seja, para que seja possível converter uma planilha automáticamente, é preciso identificar um padrão válido de dados na mesma, e criar uma configuração de extração (parser) baseada neste padrão. 

Talvez no futuro tenhamos uma IA que faça isso, quem sabe...

## Instalação

Clonar o repositório:

```
git clone https://github.com/tnatanael/standardizer.git
```

Instalar as dependências
```
composer install
```

OBS: O comando acima precisa executar com sucesso, sem erros ou falhas, é provavel que ele necessite instalar alguma dependência do php, faça a instalação usando o apt.

## Configuração

Os arquivos de configuração são o ponto principal para que o programa funcione, eles são encontrados da pasta 'config' do projeto, quando você fizer o clone do projeto haverá 2 arquivos com o sulfixo .example.php para utilizalos.

IMPORTANTE: Renomeie os 2 arquivos para global.php e parser.php respectivamente.

### Arquivo global.php
Contém algumas configurações gerais do projeto e da saída gerada, abaixo listadas:

```
<?php

$config = [

    // Defines the output folder for processed files
    'output_folder' => 'output/',
    
    // Temporary folder for pré conversion output
    'temp_folder' => 'temp/',

    // Defines the output delimiter for csv file
    'delimiter' => ',',

    // Only xls and xlsx are supported by now
    'supported_extensions' => ['xls', 'xlsx'],

    // Only csv is supported by now
    'output_type' => 'csv',
];
```
    
### Arquivo parser.php
Contém as configurações de conversão possíveis dentro do projeto, podemos gerar inúmeros tipos de saida através destas configurações, é importante entender cada uma delas para que você consiga ter versatilidade quando estiver construindo o seu conversor, abaixo a lista de configurações deste arquivo:

```
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
```

OBS: Neste arquivo você pode definir quantas configurações de parseamento precisar, recomendamos
também que salve estas configurações em local seguro caso precise utilizar novamente no futuro!
    
## Utilização
    
Para executar o conversor basta chamar o arquivo run.php da seguinte maneira:

php run.php -p my_parser_name -f path/to/inputfile.xls

Onde -p representa um padrão de parseamento configurado no arquivo config/parser.php .
O parâmetro -f representa o arquivo a ser processado .

O script suporta também o comando -h, que irá mostrar esta documentação.

## Documentação
    
#### Mappers
Um mapeador é uma string estruturada que define, a posição do valor a mapear e
as etapas de mapeamento a serem executadas (steps)
    
#### Steps
São etapas de mapeamento, que geram uma saida para o campo definido, e são executadas
sequencialmente, sendo sua saida injetada no proximo step e finalmente no arquivo de output.
Cada step aceita seus parâmetros especificos, veja a documentação para exemplos.

#### Configurando um Mapper
Sintaxe:
```
'position:"A",1;step1:"val1";step2:"valor1","valX"'
```

IMPORTANTE:
O primeiro step deve informar a posição do valor desejado (step 'pos').
Os mapeador deve ser definido entre aspas simples ''.
Valores de steps do tipo string são definidos entre aspas duplas "".
Cada novo step adicionado recebe o valor de saida do ultimo step.

#### Lista de Steps Disponíveis
**position step** - Posição do valor na planilha.

Sintaxe:
```
'position:"[column_letter]",[Opcional: row_counter_index]'
```
Exemplo: 
Retorna o valor no indice de coluna A linha unificada 1 para a saida. 
Entrada: 'texto do campo'
```
    'campo' => 'position:"A",1'
```
Saida: 'texto do campo'
IMPORTANTE: O campo index, representa o número da linha contada quando os dados estiverem presentes em multiplas linhas, ele não é necessário e pode ser omitido, caso o processamento seja linha a linha.
            
**split step** - Separa o valor e retorna uma parte baseado na posição.
Sintaxe:
```
'split:"[separator]",[index]'
```
Exemplo: 
Separa o valor na posição usando / e retorna o valor na posição 3
Entrada: 'campo/separado/por/barras'
```
'campo' => 'position:1,"A";split:"/",3'
```
Saida: 'por'
            
**equals step** - Muda o valor de saida caso o valor atual seja igual a um valor especificado
Sintaxe:
```
'equals:"[valor]","[value_if_true]","[value_if_false]"'
```
Exemplo 1:
Entrada: 'valor_atual'
```
'campo' => 'position:1,"A";equals:"valor_atual","novo_valor"'
```
Saida: 'novo_valor'

Exemplo 2:
Entrada: 'valor_atual'
```
'campo' => 'position:1,"A";equals:"diferente","novo_valor","valor_diferente"'
```
Saida: 'valor_diferente'

Observações:
Se o *value_if_false* não for informado, a função irá retornar vazio.
Se o *value_if_true* esteja vazio ou em branco, a função irá retornar vazio.
A palavra chave SELF (sem aspas) retorna o valor atual
A palavra chave PREV (sem aspas) retorna o valor encontrado o mesmo field da linha anterior
            
**numbers step** - Filtra o valor do campo deixando sómente os números
Sintaxe: 
```
'numbers'
```
Exemplo:
Entrada: '1a2b3c'
```
'campo' => 'position:1,"A";numbers'
```
Saida: '123'
            
**replace step** - Substitui todas as ocorrencias no valor
Sintaxe: 'replace:[valor_original],[novo_valor]'
Exemplo:
Entrada: '555'
```
'campo' => 'position:1,"A";replace:"5","N"'
```
Saida: 'NNN'
        
**custom step** - Executa uma função de step customizada
Sintaxe: 'custom:customFunction,[Opcional $param1],[Opcional $param2]'
Exemplo:
Entrada: '(19)999999999  (21)33333333'
```
'campo' => 'position:1,"A";custom:customPhoneParser'
```
Saida: '19999999999;2133333333'


### Criando steps customizados
As vezes é preciso criar um step customizado para aplicar em um determinado campo, ou vários campos.
Para fazer isso utilize o campo 'custom_steps' no arquivo de configurações da seguinte forma:
```
...
'custom_steps' => [
    'customFunction' => function ($string) {
        $phones = explode('  ', $string);
        foreach($phones as $key => $phone) {
            // Faça seu código
            // Returne uma string
        }
    },
],
...
```
Exemplo: 
Função utilizada como no exemplo acima
```
...
'custom_steps' => [
    /**
        * Executa um parse customizado para um campo de telefone
        */
    'customPhoneParser' => function ($string) {
        $phones = explode('  ', $string);
        foreach($phones as $key => $phone) {
            $phoneCleaned = preg_replace('/[^0-9]/', '', $phone);
            if ($phoneCleaned == '') {
                unset($phones[$key]);
            } else {
                $phones[$key] = $phoneCleaned;
            }
            return implode(";", $phones);
        }
    },
],
...
```
IMPORTANTE:
O parâmetro principal da função será a string contendo o valor atual da posição corrente.
O segundo parâmetro é um array contendo todos os parâmetros adicionais informados na configuração do mapper.
É possível passar N numero de parâmetros para a função customizada.
A função customizada deve retornar um valor do tipo string.
