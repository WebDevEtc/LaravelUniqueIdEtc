<?php
namespace WebDevEtc\LaravelUniqueIdEtc\UniqueGenerator;

interface UniqueGeneratorInterface
{
    public function __construct();
    public function generateUniqueIdAttempt(int $attempt_number);
}