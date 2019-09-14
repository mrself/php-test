<?php declare(strict_types=1);

namespace App\ValidationResponse;

use App\Exception\AppException;

class InvalidChecklistRequestException extends AppException
{
    /**
     * @var array
     */
    private $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct('Invalid checklist request fields');
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}