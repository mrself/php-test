<?php declare(strict_types=1);

namespace App\Services;

class ContentChecker
{
    /**
     * @var string[]
     */
    private $words;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $keywordsUsed;

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function init(string $content)
    {
        $this->words = $this->getWords($content);
        $this->keywordsUsed = array_intersect($this->options['keywords'], $this->words);
    }

    public function check(): bool
    {
        if (count($this->words) < $this->options['minWordsCount']) {
            return false;
        }

        return true;
    }

    public function getWordsCount(): int
    {
        return count($this->words);
    }

    /**
     * @return int
     */
    public function getKeywordsUsedCount(): int
    {
        return count($this->keywordsUsed);
    }

    public function getKeywordsDensity()
    {
        $density = count($this->keywordsUsed) / count($this->words);
        return number_format($density, 2);
    }

    private function getWords(string $content)
    {
        if (!trim($content)) {
            return [];
        }

        $alphabeticalString = $this->getAlphabeticalString($content);

        if (!$alphabeticalString) {
            return [];
        }

        $stringWithRegularSpaces = preg_replace('/\s+/', ' ', $alphabeticalString);

        return explode(' ', $stringWithRegularSpaces);
    }

    private function getAlphabeticalString(string $source)
    {
        $alphabeticalString = preg_replace('/[^\w\d]/', ' ', $source);
        return trim($alphabeticalString);
    }
}