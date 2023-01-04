<?php

namespace Tests\Unit;

use App\Exceptions\EmailException;
use App\Models\Email;
use App\Services\AsyncEmail\AsyncEmailService;
use App\Services\AsyncEmail\DataTransferObjects\OutgoingEmailDTO;
use App\Services\AsyncEmail\Transport\IEmailTransport;
use Exception;
use Tests\TestCase;

class AsyncEmailServiceTest extends TestCase
{
    private Email $emailRecord;

    private IEmailTransport $goodEmailTransport;
    private IEmailTransport $badEmailTransport;

    public function setUp(): void
    {
        parent::setUp();

        /*
         * Valid Email record
         */
        $this->emailRecord            = new Email();
        $this->emailRecord->status    = Email::STATUS_PENDING;
        $this->emailRecord->recipient = $this->faker->email;
        $this->emailRecord->subject   = $this->faker->country;
        $this->emailRecord->body      = $this->faker->paragraph;

        /*
         * EmailTransport that will always result in success
         */
        $goodMockEmailTransport = $this->getMockBuilder(IEmailTransport::class)->onlyMethods(['send'])->getMock();
        $goodMockEmailTransport->expects($this->any())->method('send')->willReturn(true);

        $this->goodEmailTransport = $goodMockEmailTransport;


        /**
         * EmailTransport that will always fail
         */
        $badMockEmailTransport = $this->getMockBuilder(IEmailTransport::class)->onlyMethods(['send'])->getMock();
        $badMockEmailTransport->expects($this->any())->method('send')->willReturn(false);

        $this->badEmailTransport = $badMockEmailTransport;
    }

    /**
     * @throws Exception
     */
    public function test_sendEmail_Throws_On_Already_Sent()
    {
        $this->emailRecord->status = Email::STATUS_SENT;

        $asyncEmailService = new AsyncEmailService();

        $this->expectException(EmailException::class);
        $asyncEmailService->sendEmail($this->emailRecord);
    }

    /**
     * @throws Exception
     */
    public function test_sendEmail_Throws_On_No_Email_Transports()
    {
        $asyncEmailService = new AsyncEmailService();

        $this->expectException(EmailException::class);
        $asyncEmailService->sendEmail($this->emailRecord);
    }

    /**
     * @throws Exception
     */
    public function test_sendEmail_Returns_True_On_Success()
    {
        $asyncEmailService = new AsyncEmailService();
        $asyncEmailService->addEmailTransport($this->goodEmailTransport);

        $result = $asyncEmailService->sendEmail($this->emailRecord);
        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function test_sendEmail_Returns_True_On_Success2()
    {
        $asyncEmailService = new AsyncEmailService();
        $asyncEmailService->addEmailTransport($this->badEmailTransport);
        $asyncEmailService->addEmailTransport($this->badEmailTransport);
        $asyncEmailService->addEmailTransport($this->goodEmailTransport);

        $result = $asyncEmailService->sendEmail($this->emailRecord);
        $this->assertTrue($result);
    }


    /**
     * @throws Exception
     */
    public function test_sendEmail_Returns_True_On_Success3()
    {
        $asyncEmailService = new AsyncEmailService();
        $asyncEmailService->addEmailTransport($this->goodEmailTransport);
        $asyncEmailService->addEmailTransport($this->badEmailTransport);

        $result = $asyncEmailService->sendEmail($this->emailRecord);
        $this->assertTrue($result);
    }


    /**
     * @throws Exception
     */
    public function test_sendEmail_Returns_False_On_failure()
    {
        $asyncEmailService = new AsyncEmailService();
        $asyncEmailService->addEmailTransport($this->badEmailTransport);

        $result = $asyncEmailService->sendEmail($this->emailRecord);
        $this->assertFalse($result);
    }


    /**
     * @throws Exception
     */
    public function test_sendEmail_Updates_Status_On_Success()
    {
        $asyncEmailService = new AsyncEmailService();
        $asyncEmailService->addEmailTransport($this->goodEmailTransport);

        $asyncEmailService->sendEmail($this->emailRecord);
        $this->assertEquals(Email::STATUS_SENT, $this->emailRecord->status);
    }

    /**
     * @throws Exception
     */
    public function test_sendEmail_Updates_Status_On_Failure()
    {
        $asyncEmailService = new AsyncEmailService();
        $asyncEmailService->addEmailTransport($this->badEmailTransport);

        $asyncEmailService->sendEmail($this->emailRecord);
        $this->assertEquals(Email::STATUS_FAILED, $this->emailRecord->status);
    }

    /**
     * @throws Exception
     */
    public function test_storeAndQueueEmails_Throws_On_Invalid_Data()
    {
        $this->withoutJobs();

        $asyncEmailService = new AsyncEmailService();

        $outgoingEmailDto = $this->getMockBuilder(OutgoingEmailDTO::class)
            ->disableOriginalConstructor()
            ->getMock();
        $outgoingEmailDto->expects($this->any())->method('getRecipients')->willReturn([null]);

        $this->expectException(EmailException::class);
        $asyncEmailService->storeAndQueueEmails($outgoingEmailDto);

    }

    /**
     * @throws Exception
     */
    public function test_storeAndQueueEmails_Saves_Records_On_Success()
    {
        $this->withoutJobs();

        $asyncEmailService = new AsyncEmailService();

        $recipientCount = 5;
        $recipients     = array_fill(0, $recipientCount, $this->faker->email);

        $outgoingEmailDto = new OutgoingEmailDTO($recipients, $this->faker->country, $this->faker->paragraph);

        $emailIds = $asyncEmailService->storeAndQueueEmails($outgoingEmailDto);

        $this->assertCount($recipientCount, $emailIds);
    }

}
