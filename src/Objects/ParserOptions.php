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
        'line_counter',
        'delimiter',
        'mapper',
        'custom_steps',
        'discard_contains',
        'end_file_string'
    ];

    private $values = [];

    /**
     * Class constructor.
     */
    public function __construct(array $parserConfig)
    {
        // Bind parameters to options
        foreach ($parserConfig as $key => $value) {
            if (!in_array($key, $this->options)) {
                throw new \Exception("Config parameter not set: ".$key);
            }
            $this->values[$key] = $value;
        }

        // Validate empty mapper
        if (count($this->values['mapper']) == 0) {
            throw new \Exception('O mapper não foi definido!');
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

        if (!isset($this->values[$option])) {
            throw new \Exception("Invalid option: ".$option);
        }

        return $this->values[$option];
    }
    
}
