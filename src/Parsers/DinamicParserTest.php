<?php declare(strict_types=1);

namespace Standardizer\Tests;

use PHPUnit\Framework\TestCase;
use Standardizer\Objects\ParserOptions;
use Standardizer\Parsers\DinamicParser;
use Standardizer\Interfaces\ParserInterface;

final class DinamicParserTest extends TestCase
{
    private $lines_mock = [
        '"divisor"',
        '"1"',
        '"2"',
        '"divisor"',
        '"1"',
        '"2"',
        '"3"',
        '"divisor"',
        '"1"',
        '"2"',
        '"3"',
        '"4"',
        '"5"',
        '"divisor"',
        '"1"',
        '"2"'
    ];

    public function testCanICreateADinamicParserInstance(): DinamicParser
    {
        $options = new ParserOptions(config('parsers')->get('testing_dinamic'));
        $parser = new DinamicParser($options);
        $this->assertInstanceOf(
            ParserInterface::class,
            $parser
        );

        return $parser;
    }

    /**
     * @depends testCanICreateADinamicParserInstance
     */
    public function testCanIGetASummarizedLinesResult(ParserInterface $parser): void
    {
        $result = $parser->summarizeLines($this->lines_mock);
        $this->assertEquals(['"1"', '"2"'], $result[0]);
        $this->assertEquals([ '"1"', '"2"', '"3"', '"4"', '"5"'], $result[2]);
        $this->assertEquals(['"1"', '"2"'], $result[3]);
    }

    /**
     * @depends testCanICreateADinamicParserInstance
     */
    public function testCanIDecodeALineMapper(ParserInterface $parser): void
    {
        $lines = $parser->summarizeLines($this->lines_mock);
        $result = $parser->parseLines($lines[3]);

        $this->assertEquals('"1""2"', $result);
    }
}