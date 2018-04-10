<?php

use Illuminate\Support\Facades\Schema;

/**
 * Main testing class for UniqueId trait
 * This uses the Models/DummyModel for testing.
 * It assumes you have a Laravel installation working for these tests to work.
 *
 * ../../../vendor/bin/phpunit
 *
 */
class MainTest extends \Tests\TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;
    /**
     * @var string
     */
    static $temporary_table_name = "webdevetc_dummy_model_testing_temporary";

    /**
     * before test, create a testing table
     */
    public function setUp()
    {
        parent::setUp();

        Schema::create(self::$temporary_table_name, function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('unique_id')->unique();
            $table->timestamps();
            $table->temporary();
        });

    }

    /**
     * after test drop the temporary table
     */
    public function tearDown()
    {
        Schema::dropIfExists(self::$temporary_table_name);
    }

    /**
     * test creating new, and updating
     */
    public function testCreatingAndUpdating()
    {
        // create first
        $dummyModel = new \Models\DummyModel();
        $this->assertTrue($dummyModel->unique_id == null);
        $dummyModel->save();
        $this->assertTrue($dummyModel->unique_id != null);
        $this->assertTrue(strlen($dummyModel->unique_id) > 0);

        // create a new one
        $d2 = new \Models\DummyModel();
        $d2->save();
        $this->assertTrue($d2->unique_id != null);
        $this->assertTrue(strlen($d2->unique_id) > 0);

        $this->assertTrue($d2->unique_id != $dummyModel->unique_id);

        // what was the uid
        $d2_uid = $d2->unique_id;

        // update it/save it...
        $d2->name = "a new name " . str_random();
        $d2->save();
        $d2->fresh();

        // ... and check the UID is the same even after updating
        $this->assertTrue($d2->unique_id === $d2_uid);

    }

    /**
     * test with model::create();
     */
    public function testCreate()
    {
        $created = \Models\DummyModel::create(['name' => str_random()]);
        $created->fresh();
        $this->assertTrue($created->unique_id != null);
        $this->assertTrue($created->id == \Models\DummyModel::where("unique_id", $created->unique_id)->firstOrFail()->id);
    }

    /**
     * test with new Model()
     */
    public function testNew()
    {
        $new = new \Models\DummyModel(['name' => str_random()]);
        $this->assertTrue($new->unique_id === null);
        $new->save();
        $new->fresh();
        $this->assertTrue($new->unique_id !== null);
        $this->assertTrue(is_string($new->unique_id));

        // try getting it from the db
        $this->assertTrue($new->id == \Models\DummyModel::where("unique_id", $new->unique_id)->firstOrFail()->id);
    }

    /**
     * Set it up so it should see an exception
     */
    public function testCreateAnError()
    {

        config(['uniqueid.unique_id_max_length' => 1]);
        $i = 0;
        $this->expectException(\WebDevEtc\LaravelUniqueIdEtc\Exceptions\UnableToCreateLaravelUniqueId::class);
        while ($i < 400) {
            $i++;
            $new = new \Models\DummyModel(['name' => "round_" . $i]);
            // set the max len to 1, it should hit a duplicate very quickly
            $new->save();
        }
        // uh oh!
    }


    /**
     * Create lots (with a large max length) of entries, make sure that no exceptions are thrown.
     */
    public function testCreateManyAndNoErrors()
    {
        $i = 0;

        while ($i < 4000) {
            $i++;
            $new = new \Models\DummyModel(['name' => "round_" . $i]);
            config(['uniqueid.unique_id_max_length' => 10]);
            $new->save();
        }
        // ok, we have made it this far, all is good. Do a simple test so that PHPUnit doesn't complain of no tests in this method
        $this->assertTrue(true);
    }

    /**
     * Test that it will still create unique ids even if it can't find any in the first round.
     * because the $unique_id_initial_length is 1, after around 36 previous unique ids it should run out of available 1 character unique ids, so then it will (after 25 tries) change the length to $unique_id_max_length.
     */
    public function testSmallInitialLengthButLargeMax()
    {
        $i = 0;

        config(['uniqueid.unique_id_max_length' => 10]);
        config(['uniqueid.unique_id_initial_length' => 1]);
        config(['uniqueid.max_num_of_attempts_before_adding_length_to_unique_id' => 4]);

        while ($i < 100) {
            $i++;
            $new = new \Models\DummyModel(['name' => "round_" . $i]);
            $new->save();
        }
        // ok, we have made it this far, all is good. Do a simple test so that PHPUnit doesn't complain of no tests in this method
        $this->assertTrue(true);
    }


    /**
     * test the unique id is UPPERCASE when $unique_id_uppercase is true
     */
    public function testUppercase()
    {
        config(['uniqueid.unique_id_uppercase' => true]);
        config(['uniqueid.unique_id_lowercase' => false]);

        $new = new \Models\DummyModel(['name' => "new"]);
        $new->save();
        $this->assertTrue(strtoupper($new->unique_id) == $new->unique_id);
    }


    /**
     * test if the unique id is lowercase when $unique_id_lowercase is true
     */
    public function testLowercase()
    {
        config(['uniqueid.unique_id_uppercase' => false]);
        config(['uniqueid.unique_id_lowercase' => true]);

        $new = new \Models\DummyModel(['name' => "new"]);
        $new->save();
        $new->fresh();
        $this->assertTrue(strtolower($new->unique_id) == $new->unique_id);
    }

}
