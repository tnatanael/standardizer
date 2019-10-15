<?php

// Load bootstrap
require 'bootstrap.php';

// Thyrd party libraries
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;
use Configula\Exception\ConfigLoaderException;

// Local classes
use Standardizer\Filesystem;
use Standardizer\Factories;

use Standardizer\Factories\ExporterFactory;
use Standardizer\Factories\ParserFactory;
use Standardizer\Factories\ConverterFactory;

/**
 * Main Program
 */
class Standardizer extends CLI
{
    public $options;

    /**
     * Setup options and helpers
     *
     * @param  Options $options Array of options
     * @return void
     */
    protected function setup(Options $options)
    {
        $options->setHelp('Spreadsheet standardizer and converter');
        $options->registerOption('version', utf8_encode('Prints current version'), 'v');
        $options->registerOption('parser', utf8_encode('Parser configuration to use'), 'p', true);
        $options->registerOption('file', utf8_encode('File to standardize and convert'), 'f', true);
        $options->registerOption('debug', utf8_encode('Run in debug mode'), 'd');

        $this->options = $options;
    }

    /**
     * Standardizer CLI
     *
     * @param Options $options
     * @return void
     */
    protected function main(Options $options)
    {
        if ($options->getOpt('version')) {
            $this->info('1.0.0');
            exit;
        }

        // Validate options
        $parserName = $options->getOpt('parser');
        if (!$parserName) {
            $this->validate_error('A parser configuration is needed');
        }
        $inputFilePath = $options->getOpt('file');
        if (!$inputFilePath) {
            $this->validate_error('A file is needed');
        }

        // Confirm that input file exists
        if (!file_exists($inputFilePath)) {
            $this->validate_error('Input file not found');
        }

        // Create the parser instance
        $parser = ParserFactory::create($parserName);

        // Create the exporter instance
        $exporter = ExporterFactory::create($parser, $inputFilePath);

        // Create the converter instance
        $converter = ConverterFactory::create($parser, $exporter);

        $converter->run();

        $this->info("Output file generated at: ".$converter->getOutputFilePath());
    }

    /**
     * Return error and exit
     *
     * @param string $msg Error description
     * @return void
     **/
    public function validate_error(string $msg)
    {
        // Return converted output to command line
        $this->error(utf8_encode($msg));
        // Exit script execution
        exit;
    }
}
// execute it
$cli = new Standardizer();

try {
    $cli->run();
} catch (ConfigLoaderException $e) {
    $cli->validate_error('Wrong configuration syntax, check your configuration files!');
} catch (\Exception $e) {
    // Print extra debug information for generic or untraited errors
    if ($cli->options->getOpt('debug')) {
        dd($e);
    }
    $cli->validate_error($e->getMessage());
}
