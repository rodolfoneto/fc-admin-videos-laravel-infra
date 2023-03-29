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

    public function testStrMaxLength()
    {
        $value = "Test";
        try {
            DomainValidation::strMaxLength($value, 3, 'Message Customized');
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Message Customized.');
        }
    }

    public function testStrMinLength()
    {
        $value = "Test";
        try {
            DomainValidation::strMinLength($value, 10);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Message Customized.');
        }
    }

    public function testStrCanNullOrMaxLength()
    {
        $value = "Teste";
        try {
            DomainValidation::strCanNullOrMaxLength($value, 4);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }
}
