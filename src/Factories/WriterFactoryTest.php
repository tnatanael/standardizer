<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class WriterFactoryTest extends TestCase
{
    public function testCanICreateAWriterInstanceForCsv(): void
    {
        // First we need to create a reader from example asset file
        $reader = Standardizer\Factories\ReaderFactory::create('xls');
        $spreadsheet = $reader->load('tests/assets/empty.xls');

        $this->assertInstanceOf(
            PhpOffice\PhpSpreadsheet\Writer\IWriter::class,
            Standardizer\Factories\WriterFactory::create(
                $spreadsheet,
                ucfirst(config('global')->get('output_type'))
            )
        );
    }
}