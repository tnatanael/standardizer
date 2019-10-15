<?php declare(strict_types=1);

namespace Standardizer\Tests;

use PHPUnit\Framework\TestCase;
use Standardizer\Objects\ParserOptions;
use Standardizer\Parsers\FixedParser;
use Standardizer\Interfaces\ParserInterface;
use Standardizer\Filesystem;

final class FixedParserTest extends TestCase
{
    private $fileToCutTest = 'tests/assets/cut.txt';

    public function testCanICreateAFixedParserInstance(): ParserInterface
    {
        $options = new ParserOptions(config('parsers')->get('testing'));
        $parser = new FixedParser($options);
        $this->assertInstanceOf(
            ParserInterface::class,
            $parser
        );

        return $parser;
    }

    /**
     * @depends testCanICreateAFixedParserInstance
     */
    public function testCanIDecodeALineMapper(ParserInterface $parser): void
    {
        $lines = [
            '"1,X","2","3"',
            '"4","5a5b5c6","6"',
            '"7","8","(19)999999999  (21)33333333"'
        ];
        $result = $parser->parseLines($lines);
        $this->assertStringContainsString("X", $result);
        $this->assertStringContainsString("", $result);
        $this->assertStringContainsString("556", $result);
        $this->assertStringContainsString("199999999992133333333123", $result);
    }

    /**
     * @depends testCanICreateAFixedParserInstance
     */
    public function testCanIParseAComplexStringWithDelimiterInsideQuotes(ParserInterface $parser): void
    {
        $result = $parser->split(',', '1,"2,3","4",5,"sds,sds,sds,",7,8",","9"');
        $this->assertEquals(8, count($result));
    }

    /**
     * @depends testCanICreateAFixedParserInstance
     */
    public function testCanIGetASummarizedLinesResult(ParserInterface $parser) : void
    {
        $lines = Filesystem::getLines($this->fileToCutTest);
        // Change parser config count_lines
        $parser->options->set('line_counter', 2);
        // Run summarize
        $resultLines = $parser->summarizeLines($lines);

        // Need to result in 10 lines after concat
        $this->assertEquals(10, count($resultLines));

        // First line need to contains 1 and 2
        $this->assertEquals($resultLines[0], ["1","2"]);
    }
}