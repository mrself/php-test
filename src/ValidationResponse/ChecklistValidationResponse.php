<?php declare(strict_types=1);

namespace App\ValidationResponse;

use App\Request\ChecklistRequest;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChecklistValidationResponse
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var array
     */
    private $fields;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @throws InvalidChecklistRequestException
     */
    public function validate()
    {
        $checklistRequest = new ChecklistRequest($this->fields['content']);
        $errors = $this->validator->validate($checklistRequest);
        if (count($errors)) {
            throw new InvalidChecklistRequestException($this->errorsToArray($errors));
        }
    }

    private function errorsToArray(ConstraintViolationListInterface $errors): array
    {
        $result = [];
        foreach ($errors as $error) {
            /** @var ConstraintViolationInterface $error */
            return [
                'message' => $error->getMessage(),
                'property' => $error->getPropertyPath()
            ];
        }
        return $result;
    }
}