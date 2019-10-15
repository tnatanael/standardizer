<?php declare(strict_types=1);

namespace Standardizer\Tests;

use PHPUnit\Framework\TestCase;
use Standardizer\Objects\ParserOptions;

final class ParserOptionsTest extends TestCase
{
    public function testCanICreateAParserInstance(): void
    {
        $options = new ParserOptions(config('parsers')->get('testing'));
        $this->assertInstanceOf(
            ParserOptions::class,
            $options
        );
    }

    public function testCanIGetAnErrorWhenMapperIsEmpty(): void
    {
        $this->expectException(\Exception::class);
        $options = new ParserOptions(config('parsers')->get('testing_invalid'));
    }
}