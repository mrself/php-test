<?php declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ChecklistRequest
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public $content;

    public function __construct($content)
    {
        $this->content = $content;
    }
}