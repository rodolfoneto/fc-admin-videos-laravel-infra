<?php

namespace Tests\Unit\Domain\Validation;

use PHPUnit\Framework\TestCase;
use Throwable;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\Exception\EntityValidationException;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNull()
    {
        $value = "";
        try {
            DomainValidation::notNull($value);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testNotNullWithCustomMessage()
    {
        $value = "";
        $message = "Test of message if the field is null";
        try {
            DomainValidation::notNull($value, $message);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, $message);
        }
    }

    public function testStrMaxLenght()
    {
        $value = "Test";
        try {
            DomainValidation::strMaxLenght($value, 3, 'Message Customized');
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Message Customized.');
        }
    }

    public function testStrMinLenght()
    {
        $value = "Test";
        try {
            DomainValidation::strMinLenght($value, 10);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Message Customized.');
        }
    }
    
    public function testStrCanNullOrMaxLenght()
    {
        $value = "Teste";
        try {
            DomainValidation::strCanNullOrMaxLenght($value, 4);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }
}
