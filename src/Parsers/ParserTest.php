<?php declare(strict_types=1);

namespace Standardizer\Tests;

use PHPUnit\Framework\TestCase;
use Standardizer\Objects\ParserOptions;
use Standardizer\Parsers\Parser;

final class ParserTest extends TestCase
{
    public function testCanICreateAParserInstance(): Parser
    {
        // Uses fixed parser as example
        $options = new ParserOptions(config('parsers')->get('testing'));
        $parser = new Parser($options);
        $this->assertInstanceOf(
            Parser::class,
            $parser
        );

        return $parser;
    }
}