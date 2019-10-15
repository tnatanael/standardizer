<?php declare(strict_types=1);

namespace Standardizer\Tests;

use PHPUnit\Framework\TestCase;
use Standardizer\Factories\ParserFactory;
use Standardizer\Interfaces\ParserInterface;

final class ParserFactoryTest extends TestCase
{
    public function testCanICreateAParserFromFactory(): void
    {
        $this->assertInstanceOf(
            ParserInterface::class,
            ParserFactory::create('testing')
        );
    }
}