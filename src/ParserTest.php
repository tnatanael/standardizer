<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Standardizer\Objects\ParserOptions;
use Standardizer\Parser;

final class ParserTest extends TestCase
{
    public function testCanICreateAParserInstance(): Parser
    {
        $options = new ParserOptions(config('parsers')->get('testing'));
        $parser = new Parser($options);
        $this->assertInstanceOf(
            Parser::class,
            $parser
        );

        return $parser;
    }

    /**
     * @depends testCanICreateAParserInstance
     */
    public function testCanIDecodeALineMapper(Parser $parser): void
    {
        $lines = [
            '"1,X","2","3"',
            '"4","5a5b5c","6"',
            '"7","8","(19)999999999  (21)33333333"'
        ];
        $result = $parser->parseLines($lines);
        $this->assertEquals("", $result['split_and_equals_test']);
        $this->assertEquals("NNN", $result['number_test']);
        $this->assertEquals("199999999992133333333123", $result['phone_test']);
    }

    /**
     * @depends testCanICreateAParserInstance
     */
    public function testCanIParseAComplexStringWithDelimiterInsideQuotes(Parser $parser): void
    {
        $result = $parser->split(',', '1,"2,3","4",5,"sds,sds,sds,",7,8",","9"');
        $this->assertEquals(8, count($result));

    }
}