<?php

namespace Tests\Feature;

use App\Services\BadWord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BadWordTest extends TestCase
{
    protected BadWord $badService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->badService = new BadWord('it dallama geri zekalı');
    }

    /**
     * @test
     */
    public function checkBadWordFailed(): void
    {
        dd($this->badService->check());
    }
}
