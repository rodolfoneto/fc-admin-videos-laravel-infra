<?php

namespace Core\Domain\Entity;

use Core\Domain\Notification\Notification;
use Exception;

abstract class Entity
{
    protected Notification $notification;

    public function __construct()
    {
        $this->notification = new Notification();
    }

    public function __get($param)
    {
        if(isset($this->{$param})) {
            return $this->{$param};
        }

        $className = get_class($this);
        throw new Exception("Property {$param} not founded in class {$className}");
    }

    public function id()
    {
        return (string) $this->id;
    }

    public function createdAt($format = 'Y-m-d H:i:s')
    {
        return $this->createdAt->format($format);
    }

    public function updatedAt($format = 'Y-m-d H:i:s')
    {
        return $this->updatedAt()->format($format);
    }
}
