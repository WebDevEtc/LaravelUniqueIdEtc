<?php

// from webdevetc.com/uniqueid

return [

    /**
     * How long should the unique id be - to begin with... (default: 5)
     */
    'unique_id_initial_length' => 5,

    /**
     *  But after this number of attempts (default: 25), we can start increasing the string length of unique id...
     */
    'max_num_of_attempts_before_adding_length_to_unique_id' => 25,

    /**
     * Increase it (after max_num_of_attempts_before_adding_length_to_unique_id attempts) to this number (default: 10)
     */
    'unique_id_max_length' => 10,

    /**
     * Maximum number of attempts in total, before throwing an exception. Default: 35
     */
    'max_number_of_unique_id_attempts' => 50,

    /**
     *  Should the unique_id be lowercase? (default: true)
     */
    'unique_id_lowercase' => true,

    /**
     * Should unique Id be uppercase (default: false)
     * Obviously only this or unique_id_lowercase should be true, not both.
     */
    'unique_id_uppercase' => false,

    /**
     * What should the default field name be. You can change this on a per-model basis by overriding the uniqueIdField() method in your models. Default: unique_id
     * Remember to make a DB migration and create the new table column.
     */
    'unique_id_field' => 'unique_id',


];