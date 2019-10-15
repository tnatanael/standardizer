<?php namespace Standardizer;

/**
 * Class for common filesystem operations
 */
class Filesystem
{
    /**
     * Scan a folder recursively and return an array of elements
     *
     * @param string $dir Directory to scan
     * @return array
     */
    public static function scanAllDir(string $dir) : array
    {
        $result = [];
        foreach (scandir($dir) as $filename) {
            if ($filename[0] === '.') {
                continue;
            }
            $filePath = $dir . '/' . $filename;
            if (is_dir($filePath)) {
                foreach (self::scanAllDir($filePath) as $childFilename) {
                    $result[] = $filename . '/' . $childFilename;
                }
            } else {
                $result[] = $filename;
            }
        }
        return $result;
    }

    /**
     * Cunts the number of lines in a text file
     *
     * @param string $file File to count lines
     * @return integer
     */
    public static function countLines(string $file) : int
    {
        $linecount = 0;
        $handle = fopen($file, "r");
        while (!feof($handle)) {
            fgets($handle); //Needed to advance the file pointer
            $linecount++;
        }
        fclose($handle);
        return $linecount;
    }

    /**
     * Get the inputFile lines array
     *
     * @param string $path Caminho do arquivo
     *
     * @return array
     * @throws \Exception when the file is not found
     **/
    public static function getLines(string $path) : array
    {
        if (!file_exists($path)) {
            throw new \Exception('Arquivo n�o encontrado!');
        }
        $filesize = filesize($path);
        if ($filesize == 0) {
            throw new \Exception('Arquivo vazio!');
        }
        $file = fread(fopen($path, 'r'), $filesize);

        // Convert content to ISO-8859-1 compatible and explode
        return explode(PHP_EOL, utf8_decode($file));
    }

    /**
     * Create a file pointer to put contents
     *
     * @param string $path Path for the new file
     * @return Resource
     * @throws \Exception when the path is inacessible
     **/
    public static function createResource(string $path)
    {
        // Get the output folder from config
        $outputFolder = config('global')->get('output_folder');

        // Create output folder if not exists
        self::makeFolder($outputFolder);

        return fopen($outputFolder.$path, "w");
    }

    /**
     * Write line to file resource
     *
     * @param Resource $file Recource file open to write
     * @param string $line String of data to write
     * @throws Exception when not valid resource was provided
     **/
    public static function writeLine($resource, string $line)
    {
        if (!is_resource($resource)) {
            throw new \Exception('Recurso inv�lido!');
        }
        // Write a line to file
        fwrite($resource, $line);
    }

    /**
     * Close an open resource
     *
     * @param Resource $resource Resource to close
     * @throws Exception when not valid resource was provided
     **/
    public function closeResource($resource)
    {
        if (!is_resource($resource)) {
            throw new \Exception('Recurso inv�lido!');
        }
        fclose($resource);
    }

    /**
     * Get the file info array for specified file
     *
     * @param string $path File path
     * @return array
     * @throws \Exception when not found
     **/
    public static function getInfo(string $path)
    {
        return pathinfo($path);
    }

    /**
     * Ensure a output folder exists
     */
    public static function makeFolder($path)
    {
        // Create the temp folder if not exists
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }
}