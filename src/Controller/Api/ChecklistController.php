<?php declare(strict_types=1);

namespace App\Controller\Api;

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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(
        Request $request,
        ChecklistValidationResponse $validationResponse
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

        // @todo return result of content checker
    }
}