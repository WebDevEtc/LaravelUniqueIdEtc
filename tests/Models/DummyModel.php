<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use WebDevEtc\LaravelUniqueIdEtc\Traits\UniqueId;

/**
 * Class DummyModel.
 *
 * This is just used for testing the UniqueId trait.
 * It extends Eloquent's Model class.
 *
 * @package Models
 */
class DummyModel extends Model
{

    // the only important part:
    use UniqueId;

    /**
     * @var array
     */
    public $fillable = ['name'];
    /**
     * @var string
     */
    public $table = "webdevetc_dummy_model_testing_temporary";


}