<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ExporterFactoryTest extends TestCase
{
    public function testCanICreateAExporterInstance(): void
    {
        $this->assertInstanceOf(
            Standardizer\Exporter::class,
            Standardizer\Factories\ExporterFactory::create('test')
        );
    }
}