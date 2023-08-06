<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class Sha256HashingTest extends TestCase
{
    public function testSha256HashingMethod()
    {
        $target_hash = '8d79f393cc294fd3daca0402209997db5ff8a2ad1a498702f0956952677881ae';

        $data_to_hash = '{"id":"63c79bd9303530645d1cca00"}';

        $hashed_data = hash('sha256', $data_to_hash);

        $this->assertEquals($target_hash, $hashed_data);
    }
}
