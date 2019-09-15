<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Response\ChecklistResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChecklistController extends AbstractController
{
    /**
     * @Route("/checklist", name="checklist", methods={"POST"})
     * @param Request $request
     * @param ChecklistResponse $response
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(
        Request $request,
        ChecklistResponse $response
    ) {
        $content = $request->request->get('content', '');
        $response->setOptions([
            'content' => $content,
            'params' => $this->getParameter('checklist')

        ]);
        return $this->json($response->makeResponse());
    }
}