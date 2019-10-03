<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Standardizer\Objects\ParserOptions;
use Standardizer\Parser;

final class ParserTest extends TestCase
{
    public function testCanICreateAParserInstance(): Parser
    {
        $options = new ParserOptions(config('parsers')->get('case1'));
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
    public function testCanIParseAComplexStringWithDelimiterInsideQuotes(Parser $parser): void
    {
        $result = $parser->split(',', '1,"2,3","4",5,"sds,sds,sds,",7,8",","9"');
        $this->assertEquals(8, count($result));

    }
}