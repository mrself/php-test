<?php declare(strict_types=1);

namespace App\Response;

use App\Services\ContentChecker;
use App\ValidationResponse\ChecklistValidationResponse;
use App\ValidationResponse\InvalidChecklistRequestException;

class ChecklistResponse
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var ChecklistValidationResponse
     */
    private $validationResponse;

    /**
     * @var ContentChecker
     */
    private $contentChecker;

    public function __construct(
        ChecklistValidationResponse $validationResponse,
        ContentChecker $contentChecker
    ) {
        $this->validationResponse = $validationResponse;
        $this->contentChecker = $contentChecker;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function makeResponse()
    {
        $response = $this->getValidationResponse();
        if ($response) {
            return $response;
        }

        $this->initContentChecker();

        if (!$this->contentChecker->check()) {
            return $this->makeLackOfWordsResponse();
        }

        return $this->makeSuccessResponse();
    }

    private function makeSuccessResponse()
    {
        return [
            'content' => $this->options['content'],
            'keywords used' => $this->contentChecker->getKeywordsUsedCount(),
            'average keywords density' => $this->contentChecker->getKeywordsDensity()
        ];
    }

    private function makeLackOfWordsResponse()
    {
        return [
            'success' => false,
            'reason' => 'Lack of words in the content',
            'words_count' => $this->contentChecker->getWordsCount(),
            'minimal_words_count' => $this->options['params']['min_words_count']
        ];
    }

    private function getValidationResponse(): ?array
    {
        $this->validationResponse->setFields([
            'content' => $this->options['content']
        ]);

        try {
            $this->validationResponse->validate();
        } catch (InvalidChecklistRequestException $e) {
            return [
                'success' => false,
                'reason' => 'Invalid request',
                'errors' => $e->getErrors()
            ];
        }

        return null;
    }

    private function initContentChecker()
    {
        $this->contentChecker->setOptions([
            'minWordsCount' => $this->options['params']['min_words_count'],
            'keywords' => $this->options['params']['keywords']
        ]);

        $this->contentChecker->init($this->options['content']);
    }
}