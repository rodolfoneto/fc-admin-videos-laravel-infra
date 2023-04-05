<?php

namespace Tests\Unit\App\Models;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

abstract class ModelTestCase extends TestCase
{
    abstract protected function model(): Model;
    abstract protected function traits(): array;
    abstract protected function fillable(): array;
    abstract protected function incrementing(): bool;
    abstract protected function casting(): array;

    public function testIfUseTraits()
    {
        $traitsNeed= $this->traits();
        $model = $this->model();
        $traitsUsed = array_keys(class_uses($model));
        $this->assertEquals($traitsNeed, $traitsUsed);
    }

    public function testIncrementingIsFalse(): void
    {
        $model = $this->model();
        $this->assertEquals($this->incrementing(), $model->incrementing);
    }

    public function testHasFillables(): void
    {
        $need = $this->fillable();
        $model = $this->model();
        $has = $model->getFillable();
        $this->assertEquals($need, $has);
    }

    public function testHasCasting():  void
    {
        $castNeed = $this->casting();
        $model = $this->model();
        $castsHas = $model->getCasts();
        $this->assertEquals($castNeed, $castsHas);
    }
}
