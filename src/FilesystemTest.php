<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Standardizer\Filesystem;

final class FilesystemTest extends TestCase
{
    private $tempResource = 'test.txt';
    public function testCanReadInputFolderRecursive(): void
    {
        $this->assertIsArray(
            Filesystem::scanAllDir('tests/assets')
        );
    }

    public function testCanCountTextFileLines()
    {
        $this->assertEquals(
            10,
            Filesystem::countLines('tests/assets/test.txt')
        );
    }

    public function testCanIGetTextFileLinesArray()
    {
        $this->assertIsArray(
            Filesystem::getLines('tests/assets/test.txt')
        );

        $this->expectException(\Exception::class);
        Filesystem::getLines('invalid');
    }

    public function testCanICreateAnOutputFileInOutputFolder()
    {
        // Create file for converted output
        $file = 'test.txt';
        $resource = Filesystem::createResource($file);
        $this->assertIsResource($resource);

        $outputFolder = config('global')->get('output_folder');
        $this->assertFileExists($outputFolder.$this->tempResource);

        return $resource;
    }

    /**
     * @depends testCanICreateAnOutputFileInOutputFolder
     **/
    public function testCanIWriteALineToFile($resource)
    {
        $stringToWrite = 'test';
        Filesystem::writeLine($resource, $stringToWrite);
        Filesystem::closeResource($resource);

        $outputFolder = config('global')->get('output_folder');

        $this->assertStringEqualsFile(
            $outputFolder.$this->tempResource,
            $stringToWrite
        );

        // Erase generated test resource file
        unlink($outputFolder.$this->tempResource);
    }

    public function testCanIGetTextFileInfoArray()
    {
        $this->assertIsArray(
            Filesystem::getInfo('tests/assets/test.txt')
        );

        $this->expectException(\Exception::class);
        Filesystem::getLines('invalid');
    }
}