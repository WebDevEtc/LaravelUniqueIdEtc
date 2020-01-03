<?php

namespace WebDevEtc\LaravelUniqueIdEtc\UniqueGenerator;

use Exception;

/**
 * Class UniqueGenerator
 *
 * @package WebDevEtc\LaravelUniqueIdEtc\UniqueGenerator
 */
class UniqueGenerator implements UniqueGeneratorInterface
{
    protected $options = [];

    /**
     * UniqueGenerator constructor.
     */
    public function __construct()
    {
        $this->options = [
            //      How many attempts at $unique_id_initial_length until we start making it longer?
            'max_attempts_before_longer' => (int)config('uniqueid.max_num_of_attempts_before_adding_length_to_unique_id',
                25),
            // initial length (this can increase to max_length if we can't find a free unique_id
            'initial_length' => (int)config('uniqueid.unique_id_initial_length', 5),
            // What should the max length be? It can be the same as the initial length.
            'max_length' => (int)config('uniqueid.unique_id_max_length', 10),
            // should the final result be lowercase?
            'is_lower' => (bool)config('uniqueid.unique_id_lowercase', true),
            // should the final result be uppercase?
            'is_upper' => (bool)config('uniqueid.unique_id_uppercase', false),
        ];
    }

    /**
     * Generate a random unique id, length of $len
     * @param int $attempt_number
     * @return string
     */
    protected function generate_initial_random_string($attempt_number)
    {
        $len = $this->options['initial_length'];
        if ($attempt_number > $this->options['max_attempts_before_longer']) {
            $len = $this->options['max_length'];
        }

        $attempt = str_random($len);

        if ($attempt_number > $this->options['max_attempts_before_longer']) {
            // getting desperate now - add more characters to the start!
            $attempt .= $attempt;
        }

        return $attempt;
    }

    /**
     * Generate a unique id, for attempt number $attempt_number
     *
     * @param $attempt_number
     * @return string
     * @throws Exception
     */
    public function generateUniqueIdAttempt(int $attempt_number)
    {
        $attempt = $this->generate_initial_random_string($attempt_number);

        // further config this->options
        if ($this->options['is_lower']) {
            $attempt = strtolower($attempt);
        }
        if ($this->options['is_upper']) {
            $attempt = strtoupper($attempt);
        }

        // Remove any non alphanum characters.
        $attempt = preg_replace('/[^a-zA-Z0-9]/i', '', (substr($attempt, 0, $this->options['max_length'])));

        return $attempt;
    }

}
