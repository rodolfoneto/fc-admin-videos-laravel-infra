<?php

namespace Core\Domain\Notification;

class Notification
{
    protected array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array[context, message] $error
     * @return void
     */
    public function addError(array $error): void
    {
        array_push($this->errors, $error);
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function getMessage(string $context = ''): string
    {
        $message = '';
        foreach ($this->errors as $error) {

            if($context === '' || $error['context'] == $context) {
                $message .= "{$error['context']}: {$error['message']}, ";
            }
        }
        if(strlen($message) > 0) {
            $message = substr($message, 0, strlen($message) - 2);
        }
        return $message;
    }

    public function __toString(): string
    {
        $this->getMessage();
    }
}
