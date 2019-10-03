<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ReaderFactoryTest extends TestCase
{
    public function testCanICreateAReaderInstanceForXls(): void
    {
        $this->assertInstanceOf(
            PhpOffice\PhpSpreadsheet\Reader\IReader::class,
            Standardizer\Factories\ReaderFactory::create('xls')
        );
    }

    public function testCanICreateAReaderInstanceForXlsx(): void
    {
        $this->assertInstanceOf(
            PhpOffice\PhpSpreadsheet\Reader\IReader::class,
            Standardizer\Factories\ReaderFactory::create('xlsx')
        );
    }

    public function testICantCreateAUnsupportedReader(): void
    {
        $this->expectException(\Exception::class);

        $this->assertInstanceOf(
            PhpOffice\PhpSpreadsheet\Reader\IReader::class,
            Standardizer\Factories\ReaderFactory::create('invalid')
        );
    }
}