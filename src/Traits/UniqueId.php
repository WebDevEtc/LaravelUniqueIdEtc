<?php namespace WebDevEtc\LaravelUniqueIdEtc\Traits;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Log;
use WebDevEtc\LaravelUniqueIdEtc\Exceptions\UnableToCreateLaravelUniqueId;
use WebDevEtc\LaravelUniqueIdEtc\UniqueGenerator\UniqueGenerator;
use WebDevEtc\LaravelUniqueIdEtc\UniqueGenerator\UniqueGeneratorInterface;

/**
 * Class UniqueId
 *
 */
trait UniqueId
{
    /**
     * Run the unique id generator when creating new Eloquent models.
     */
    public static function bootUniqueId()
    {
        static::creating(static function (Model $model) {
            /** @var UniqueId $model */
            $model->generateUniqueIdOnCreate();
        });
    }

    /**
     * Set the unique id field ($this->uniqueidField()) on the current model
     * @return null
     */
    public function generateUniqueIdOnCreate()
    {
        $this->{$this->uniqueIdField()} = $this->newUniqueid();
    }

    /**
     * Which field stores the unique id.
     * Please remember to add this to a DB migration for relevant tables.
     *
     * By default it will check the config/uniqueid.php file, but you
     * can override it on a per-model basis here.
     * You will have to do use UniqueId { uniqueIdField as origUniqueIdField } to override this method though.
     *
     * default: 'unique_id'
     * @return string
     */
    protected function uniqueIdField()
    {
        return config('uniqueid.unique_id_field', 'unique_id');
    }

    /**
     * Generate a new Unique ID.
     *
     * @return string
     * @throws Exception
     */
    protected function newUniqueid()
    {
        $generator = app()->make(UniqueGeneratorInterface::class);

        // how many attempts should we do, before throwing exception
        $max_attempts = config("uniqueid.max_number_of_attempts", 50);

        // when should we log a warning message to log, indicating that we are doing a lot of attempts
        $send_warning_at = floor($max_attempts * 0.7);

        /** @var UniqueGenerator $generator */
        for ($attempt_num = 0; $attempt_num < $max_attempts; $attempt_num++) {
            $attempt = $generator->generateUniqueIdAttempt($attempt_num);
            $exists = DB::table($this->getTable())->where($this->uniqueIdField(), $attempt)->exists();

            if (!$exists) {
                // this attempt didn't exist in DB, it should be safe to use (as long as there was no race condition here, but I think it is safe to assume it is ok. If you are worried about this, then set the unique_id_initial_length config high!
                return $attempt;
            }

            if ($attempt_num === $send_warning_at) {
                Log::warning("Laravel Unique ID Etc Warning: " . __METHOD__ .
                    " is using at least $send_warning_at attempts and still not finding a unique ID on " .
                    $this->getTable() . ". Consider increasing unique_id_initial_length()'s value for " .
                    get_class($this));
            }
            // cannot use $attempt, so lets loop again and try again
        }

        // too many attempts.
        throw new UnableToCreateLaravelUniqueId('Laravel Unique ID Error: Unable to find a new unique ' .
            "ID within the first $max_attempts attempts on " . $this->getTable() . ' so quitting');
    }

}
