<?php declare(strict_types=1);

namespace App\Tests\Unit\ValidationResponse;


use App\ValidationResponse\ChecklistValidationResponse;
use App\ValidationResponse\InvalidChecklistRequestException;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ChecklistResponseTest extends KernelTestCase
{

    /**
     * @var ChecklistValidationResponse
     */
    private $service;

    /**
     * @expectedException \App\ValidationResponse\InvalidChecklistRequestException
     */
    public function testValidateThrowsExceptionIfContentIsBlank()
    {
        $this->service->setFields(['content' => '']);
        $this->service->validate();
    }

    /**
     * @throws \App\ValidationResponse\InvalidChecklistRequestException
     */
    public function testValidateDoesNotThrowExceptionIfContentIsNotBlank()
    {
        $this->service->setFields(['content' => 'content']);
        $this->service->validate();
        $this->assertTrue(true);
    }

    public function testValidateThrowsExceptionWithErrorsPropertyIfContentIsInvalid()
    {
        $this->service->setFields(['content' => '']);
        $expected = [
            'message' => 'This value should not be blank.',
            'property' => 'content'
        ];
        try {
            $this->service->validate();
        } catch (InvalidChecklistRequestException $e) {
            $this->assertEquals($expected, $e->getErrors());
            return;
        }

        $this->assertTrue(false);
    }

    /**
     * @throws \ReflectionException
     */
    public function testErrorsToArrayConvertsConstraintViolationListToArray()
    {
        $list = new ConstraintViolationList([
            new ConstraintViolation(
                'message',
                'template',
                [],
                ['path' => 'invalidValue'],
                'path',
                null,
                6
            )
        ]);

        $reflectionMethod = new ReflectionMethod($this->service, 'errorsToArray');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invoke($this->service, $list);

        $expected = [
            'message' => 'message',
            'property' => 'path'
        ];

        $this->assertEquals($expected, $result);
    }

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
        $this->service = static::$container->get(ChecklistValidationResponse::class);
    }
}