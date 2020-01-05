<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\Name;
use App\ReadModel\User\UserFetcher;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/name")
 */
class NameController extends AbstractController
{
    private $users;
    private $handler;

    public function __construct(UserFetcher $users, ErrorHandler $handler)
    {
        $this->users = $users;
        $this->handler = $handler;
    }

    /**
     * @Route("", name="profile.name")
     * @param Request $request
     * @param Name\Handler $handler
     * @return Response
     */
    public function request(Request $request, Name\Handler $handler): Response
    {
        $user = $this->users->get($this->getUser()->getId());

        $command = Name\Command::fromUser($user);

        $form = $this->createForm(Name\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('profile');
            } catch (DomainException $e) {
                $this->handler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/profile/name.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
