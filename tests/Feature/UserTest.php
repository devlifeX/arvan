<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\MyUtil\MyHelper;
use Illuminate\Support\Collection;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $urlTypes = [
            'http://devlife.ir' => 'http://www.devlife.ir',
            'http://DEVLIFE.IR' => 'http://www.devlife.ir',
            'http://www.devlife.ir' => 'http://www.devlife.ir',
            'http://devlife.ir/' => 'http://www.devlife.ir',
            'http://devlife.ir/index.php' => 'http://www.devlife.ir',
            'https://devlife.ir' => 'https://www.devlife.ir',
            'https://devlife.ir/mail' => 'https://www.devlife.ir',
            'https://test.devlife.ir/mail' => 'https://www.devlife.ir',
            'https://test.devlife.ir/' => 'https://www.devlife.ir',
        ];

        collect($urlTypes)
            ->each(function ($item, $key) {
                $this->assertEquals(MyHelper::urlSanitize($key), $item);
            });;
    }
}
