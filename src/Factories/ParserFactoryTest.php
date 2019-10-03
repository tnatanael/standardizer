<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ParserFactoryTest extends TestCase
{
    public function testCanICreateAParserFromFactory(): void 
    {
        $this->assertInstanceOf(
            Standardizer\Parser::class,
            Standardizer\Factories\ParserFactory::create('case1')
        );
    }
}