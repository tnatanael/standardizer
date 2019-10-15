<?php namespace Standardizer\Objects;

/**
 * Parser options object model
 */
class ParserOptions
{
    private $fixed_options = [
        'line_counter'
    ];

    private $dinamic_options = [
        'divisor_text'
    ];

    private $default_options = [
        'mode',
        'discard_top',
        'discard_bottom',
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
        // Confirm if mode was set
        if (!isset($parserConfig['mode'])) {
            throw new \Exception("Config parameter mode not found");
        }

        // Merge options configuration based on mode
        $options = [];
        switch ($parserConfig['mode']) {
            case 'fixed':
                $options = array_merge($this->default_options, $this->fixed_options);
                break;
            case 'dinamic';
                $options = array_merge($this->default_options, $this->dinamic_options);
                break;
            default:
                throw new \Exception("Wrong config mode set: ".$parserConfig['mode']);
                break;
        }

        // Bind parameters to options
        foreach ($parserConfig as $key => $value) {
            if (!in_array($key, $options)) {
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

    /**
     * Set option value
     *
     * @param string $option Option to get value
     * @throws Exception When option not found
     **/
    public function set(string $option = null, $value)
    {
        if (is_null($option)) {
            return $this->options;
        }

        if (!isset($this->values[$option])) {
            throw new \Exception("Invalid option: ".$option);
        }

        $this->values[$option] = $value;
    }
}
