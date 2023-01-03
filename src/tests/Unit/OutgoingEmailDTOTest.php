<?php

namespace Tests\Unit;

use App\Exceptions\ValidationException;
use App\Services\AsyncEmail\DataTransferObjects\OutgoingEmailDTO;
use Exception;
use Tests\TestCase;

class OutgoingEmailDTOTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_Constructor_Creates_Object_On_Success()
    {
        $emailData = new OutgoingEmailDTO([$this->faker->email], $this->faker->country, $this->faker->paragraph);

        $this->assertInstanceOf(OutgoingEmailDTO::class, $emailData);
    }

    /**
     * @throws Exception
     */
    public function test_Constructor_Throws_On_NonArray_Recipients()
    {
        $this->expectException(ValidationException::class);

        (new OutgoingEmailDTO($this->faker->email, $this->faker->country, $this->faker->paragraph));
    }

    /**
     * @throws Exception
     */
    public function test_Constructor_Throws_On_Zero_Recipients()
    {
        $this->expectException(ValidationException::class);

        (new OutgoingEmailDTO([], $this->faker->country, $this->faker->paragraph));
    }

    /**
     * @throws Exception
     */
    public function test_Constructor_Throws_On_Invalid_Recipients()
    {
        $this->expectException(ValidationException::class);

        (new OutgoingEmailDTO([$this->faker->email, $this->faker->name], $this->faker->country, $this->faker->paragraph));
    }

    /**
     * @throws Exception
     */
    public function test_Constructor_Throws_On_Empty_Subject()
    {
        $this->expectException(ValidationException::class);

        (new OutgoingEmailDTO([$this->faker->email], '', $this->faker->paragraph));
    }

    /**
     * @throws Exception
     */
    public function test_Constructor_Throws_On_Empty_Body()
    {
        $this->expectException(ValidationException::class);

        (new OutgoingEmailDTO([$this->faker->email], $this->faker->country, ''));
    }
}
