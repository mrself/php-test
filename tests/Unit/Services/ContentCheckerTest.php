<?php declare(strict_types=1);

namespace App\Tests\Unit\Services;

use App\Services\ContentChecker;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContentCheckerTest extends KernelTestCase
{
    /**
     * @var ContentChecker
     */
    private $service;

    /**
     * @param $content
     * @param $expected
     * @throws \ReflectionException
     * @dataProvider getGetWordsData
     */
    public function testGetWordsReturnsCorrectResult($content, $expected)
    {
        $reflectionMethod = new ReflectionMethod($this->service, 'getWords');
        $reflectionMethod->setAccessible(true);
        $actual = $reflectionMethod->invoke($this->service, $content);
        $this->assertEquals($expected, $actual);
    }

    public function getGetWordsData(): array
    {
        return [
            ['a b', ['a', 'b']],
            ['A b', ['A', 'b']],
            ['ab cd', ['ab', 'cd']],
            ['a b.', ['a', 'b']],
            ['a - b', ['a', 'b']],
            ['a1 b', ['a1', 'b']],
            ['', []],
            [' ', []],
            [' !', []],
        ];
    }

    public function testCheckReturnsFalseIfContentWordsCountIsLessThanNeeded()
    {
        $this->service->setOptions([
            'minWordsCount' => 2,
            'keywords' => ['lorem', 'ipsum'],
        ]);
        $this->service->init('one');
        $actual = $this->service->check();
        $this->assertFalse($actual);
    }

    public function testCheckReturnsTrueIfContentWordsCountIsEnough()
    {
        $this->service->setOptions([
            'minWordsCount' => 2,
            'keywords' => ['lorem', 'ipsum'],
        ]);
        $this->service->init('one two three');
        $actual = $this->service->check();
        $this->assertTrue($actual);
    }

    public function testGetKeywordsUsedCountReturnsCountOfKeywordsOccurrencesInContent()
    {
        $this->service->setOptions([
            'keywords' => ['lorem', 'ipsum'],
            'minWordsCount' => 2
        ]);
        $this->service->init('one two lorem three ipsum');
        $this->service->check();

        $actual = $this->service->getKeywordsUsedCount();
        $this->assertEquals(2, $actual);
    }

    public function testGetKeywordsUsedCountIgnoresDuplicates()
    {
        $this->service->setOptions([
            'keywords' => ['lorem', 'ipsum'],
            'minWordsCount' => 2
        ]);
        $this->service->init('lorem one two lorem three ipsum');
        $this->service->check();

        $actual = $this->service->getKeywordsUsedCount();
        $this->assertEquals(2, $actual);
    }

    public function testGetKeywordsDensity()
    {
        $this->service->setOptions([
            'keywords' => ['lorem', 'ipsum'],
            'minWordsCount' => 2
        ]);
        $this->service->init('lorem one lorem three ipsum');
        $actual = $this->service->getKeywordsDensity();
        $this->assertEquals(0.4, $actual);
    }

    public function testGetKeywordsDensityWithResultWith2DecimalNumbers()
    {
        $this->service->setOptions([
            'keywords' => ['lorem', 'ipsum'],
            'minWordsCount' => 2
        ]);
        $this->service->init('lorem one lorem two three ipsum four');
        $actual = $this->service->getKeywordsDensity();
        $this->assertEquals(0.29, $actual);
    }

    public function testGetKeywordsDensityWithResultWithIntResult()
    {
        $this->service->setOptions([
            'keywords' => ['lorem', 'ipsum'],
            'minWordsCount' => 2
        ]);
        $this->service->init('lorem ipsum');
        $actual = $this->service->getKeywordsDensity();
        $this->assertEquals(1, $actual);
    }

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
        $this->service = static::$container->get(ContentChecker::class);
    }
}