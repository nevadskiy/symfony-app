<?php

declare(strict_types=1);

namespace App\Controller\Profile\OAuth;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\SocialNetwork\Detach\Command;
use App\Model\User\UseCase\SocialNetwork\Detach\Handler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/oauth")
 */
class DetachController extends AbstractController
{
    private $handler;

    public function __construct(ErrorHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Route("/detach/{network}/{identity}", name="profile.oauth.detach", methods={"DELETE"})
     * @param Request $request
     * @param string $network
     * @param string $identity
     * @param Handler $handler
     * @return Response
     */
    public function detach(Request $request, string $network, string $identity, Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('profile');
        }

        $command = new Command($this->getUser()->getId(), $network, $identity);

        try {
            $handler->handle($command);
            return $this->redirectToRoute('profile');
        } catch (DomainException $e) {
            $this->handler->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}
