<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Standardizer\Factories\ExporterFactory;

final class ConverterFactoryTest extends TestCase
{
    private $uniondataCadastroExporter;
    private $uniondataDefaultCobrancaExporter;
    private $uniondataAcordoCobrancaExporter;

    protected function setUp() : void
    {
        $this->uniondataCadastroExporter = ExporterFactory::create(
            'tests/assets/uniondata/cadastro/default/cadastro.xls'
        );
        $this->uniondataDefaultCobrancaExporter = ExporterFactory::create(
            'tests/assets/uniondata/cobranca/default/inadimplencia.xls'
        ); 
        $this->uniondataAcordoCobrancaExporter = ExporterFactory::create(
            'tests/assets/uniondata/cobranca/acordo/acordos.xls'
        );
    }

    public function testCanICreateAUniondataDefaultCadastroConverterFromFactory(): void
    {
        $this->assertInstanceOf(
            Standardizer\Converters\CadastroConverter::class,
            Standardizer\Factories\ConverterFactory::create($this->uniondataCadastroExporter)
        );
    }

    public function testCanICreateAUniondataDefaultCobrancaConverterFromFactory(): void
    {
        $this->assertInstanceOf(
            Standardizer\Converters\CobrancaConverter::class,
            Standardizer\Factories\ConverterFactory::create($this->uniondataDefaultCobrancaExporter)
        );
    }

    public function testCanICreateAUniondataAcordoCobrancaConverterFromFactory(): void
    {
        $this->assertInstanceOf(
            Standardizer\Converters\CobrancaConverter::class,
            Standardizer\Factories\ConverterFactory::create($this->uniondataAcordoCobrancaExporter)
        );
    }
}