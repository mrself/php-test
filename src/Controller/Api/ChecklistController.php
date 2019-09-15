<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Services\ContentChecker;
use App\ValidationResponse\ChecklistValidationResponse;
use App\ValidationResponse\InvalidChecklistRequestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChecklistController extends AbstractController
{
    /**
     * @Route("/checklist", name="checklist", methods={"POST"})
     * @param Request $request
     * @param ChecklistValidationResponse $validationResponse
     * @param ContentChecker $contentChecker
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(
        Request $request,
        ChecklistValidationResponse $validationResponse,
        ContentChecker $contentChecker
    ) {
        $content = $request->request->get('content', '');
        $validationResponse->setFields([
            'content' => $content
        ]);

        try {
            $validationResponse->validate();
        } catch (InvalidChecklistRequestException $e) {
            return $this->json([
                'success' => false,
                'errors' => $e->getErrors()
            ]);
        }

        $checklistParams = $this->getParameter('checklist');
        $contentChecker->setOptions([
            'minWordsCount' => $checklistParams['min_words_count'],
            'keywords' => $checklistParams['keywords']
        ]);

        $contentChecker->init($content);
        if (!$contentChecker->check()) {
            return $this->json([
                'success' => false,
                'reason' => 'Lack of words in the content',
                'words_count' => $contentChecker->getWordsCount(),
                'minimal_words_count' => $checklistParams['min_words_count']
            ]);
        }

        return $this->json([
            'content' => $content,
            'keywords used' => $contentChecker->getKeywordsUsedCount(),
            'average keywords density' => $contentChecker->getKeywordsDensity()
        ]);
    }
}