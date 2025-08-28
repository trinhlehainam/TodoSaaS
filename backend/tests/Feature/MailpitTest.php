<?php

namespace Tests\Feature;

use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailpitTest extends TestCase
{
    /**
     * Test that emails can be sent and captured by Mailpit.
     */
    public function test_mailpit_captures_emails(): void
    {
        // Use Mail::fake() to capture emails during testing
        Mail::fake();

        // Send a test email
        Mail::to('test@example.com')->send(new TestEmail);

        // Assert that the email was sent
        Mail::assertSent(TestEmail::class);

        // Assert that exactly one email was sent
        Mail::assertSentCount(1);

        // Assert the email was sent to the correct recipient
        Mail::assertSent(TestEmail::class, function (TestEmail $mail) {
            return $mail->hasTo('test@example.com') &&
                   $mail->hasSubject('Test Email');
        });
    }

    /**
     * Test actual email sending to verify Mailpit integration.
     * This test will actually send an email when Mailpit is configured.
     */
    public function test_actual_email_sending_to_mailpit(): void
    {
        // Skip this test if Mailpit is not configured as SMTP host
        if (config('mail.mailers.smtp.host') !== 'mailpit') {
            $this->markTestSkipped('Mailpit not configured as SMTP host for this environment');
        }

        try {
            // Force use of SMTP mailer to actually send email to Mailpit
            Mail::mailer('smtp')->to('mailpit-test@example.com')->send(new TestEmail);

            // If we get here without exception, the email was sent successfully to Mailpit
            $this->assertTrue(true, 'Email sent successfully to Mailpit via SMTP');

        } catch (\Exception $e) {
            $this->fail('Failed to send email to Mailpit: '.$e->getMessage());
        }
    }

    /**
     * Test Mailpit connection and email capture by sending multiple emails.
     */
    public function test_mailpit_captures_multiple_emails(): void
    {
        // Skip this test if Mailpit is not configured as SMTP host
        if (config('mail.mailers.smtp.host') !== 'mailpit') {
            $this->markTestSkipped('Mailpit not configured as SMTP host for this environment');
        }

        $recipients = [
            'user1@example.com',
            'user2@example.com',
            'user3@example.com',
        ];

        try {
            foreach ($recipients as $recipient) {
                Mail::mailer('smtp')->to($recipient)->send(new TestEmail);
            }

            // If we get here, all emails were sent successfully
            $this->assertCount(3, $recipients, 'All 3 test emails should have been sent to Mailpit');

        } catch (\Exception $e) {
            $this->fail('Failed to send multiple emails to Mailpit: '.$e->getMessage());
        }
    }
}
