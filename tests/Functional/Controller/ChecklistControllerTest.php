<?php declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChecklistControllerTest extends WebTestCase
{
    public function testItReturnsSuccessfulResponse()
    {
        $client = self::createClient();
        $client->request('POST', '/api/v1/checklist', [
            'content' => 'a lorem b c d e f g h i l m n'
        ]);
        $content = $client->getResponse()->getContent();
        $content = json_decode($content, true);
        $expected = [
            'content' => 'a lorem b c d e f g h i l m n',
            'keywords used' => 1,
            'average keywords density' => 0.08
        ];
        $this->assertEquals($expected, $content);
    }
}