<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Standardizer\Objects\ParserOptions;

final class ParserOptionsTest extends TestCase
{
    public function testCanICreateAParserInstance(): void
    {
        $options = new ParserOptions(config('parsers')->get('case1'));
        $this->assertInstanceOf(
            ParserOptions::class,
            $options
        );
    }
}