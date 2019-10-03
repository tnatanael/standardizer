<?php namespace Standardizer\Objects;

use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Parser options object model
 */
class ParserOptions
{
    private $options = [
        'discard_top',
        'discard_bottom',
        'row_counter',
        'mapper',
        'discard_contains',
        'end_file_string'
    ];

    /**
     * Class constructor.
     */
    public function __construct(array $parserConfig)
    {
        // Bind parameters to options
        foreach (array_keys($parserConfig) as $key) {
            if (!in_array($key, $this->options)) {
                throw new Exception("Config parameter not set: ".$key);
            }
        }
    }

    /**
     * Get option value
     *
     * @param string $option Option to get value
     * @throws Exception When option not found
     **/
    public function get(string $option = null)
    {
        if (is_null($option)) {
            return $this->options;
        }

        if (!isset($this->options[$option])) {
            throw new Exception("Invalid option: ".$option);
        }

        return $this->options[$option];
    }
    
}
