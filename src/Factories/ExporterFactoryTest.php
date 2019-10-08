<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Standardizer\Factories\ParserFactory;

final class ExporterFactoryTest extends TestCase
{
    public function testCanICreateAExporterInstance(): void
    {
        $parser = ParserFactory::create('testing');
        $this->assertInstanceOf(
            Standardizer\Exporter::class,
            Standardizer\Factories\ExporterFactory::create($parser, 'tests/assets/empty.xls')
        );
    }
}