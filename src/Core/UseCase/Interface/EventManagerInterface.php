<?php
namespace Core\UseCase\Interface;


interface EventManagerInterface
{
    public function dispatch(object $event): void;
}
