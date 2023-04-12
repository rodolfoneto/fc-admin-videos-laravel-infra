<?php

namespace Tests\Unit\Domain\Notification;

use Core\Domain\Notification\Notification;
use PHPUnit\Framework\TestCase;

class NotificationUnitTest extends TestCase
{
    public function test_notification_add_error()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'Errors 001'
           ]);
        $errors = $notification->getErrors();
        $this->assertIsArray($errors);
        $this->assertCount(1, $errors);
    }

    public function test_notification_return_of_get_errors()
    {
        $notification = new Notification();
        $errors = $notification->getErrors();
        $this->assertIsArray($errors);
    }

    public function test_notification_has_errors_true()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'Errors 001'
        ]);
        $this->assertTrue($notification->hasErrors());
    }

    public function test_notification_has_errors_false()
    {
        $notification = new Notification();
        $this->assertFalse($notification->hasErrors());
    }

    public function test_notification_message()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'title min 2 characters',
        ]);

        $notification->addError([
            'context' => 'video',
            'message' => 'notification is required',
        ]);

        $this->assertEquals(
            expected: "video: title min 2 characters, video: notification is required",
            actual: $notification->getMessage()
        );
    }

    public function test_notification_message_empty_errors()
    {
        $notification = new Notification();
        $this->assertEmpty($notification->getMessage());
    }

    public function test_filter_by_context()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'title min 2 characters',
        ]);

        $notification->addError([
            'context' => 'category',
            'message' => 'name is required',
        ]);
        $message = $notification->getMessage(
            context: 'video',
        );
        $this->assertEquals(
            expected: 'video: title min 2 characters',
            actual: $message
        );
    }
}
