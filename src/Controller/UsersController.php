<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User\Entity\User\User;
use App\ReadModel\User\UserFetcher;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\User\UseCase\Create;
use App\Model\User\UseCase\Edit;
use App\Model\User\UseCase\Role;
use App\Model\User\UseCase\SignUp\Confirm;
use App\Model\User\UseCase\Activate;
use App\Model\User\UseCase\Block;

class UsersController extends AbstractController
{
    private $users;
    private $logger;

    public function __construct(UserFetcher $users, LoggerInterface $logger)
    {
        $this->users = $users;
        $this->logger = $logger;
    }

    /**
     * @Route("/users", name="users")
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->users->all();

        return $this->render('app/users/index.html.twig', compact('users'));
    }

    /**
     * @Route("/users/create", name="users.create")
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('users');
            } catch (DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/{id}", name="users.show")
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('app/users/show.html.twig', compact('user'));
    }

    /**
     * @Route("/users/{id}/edit", name="users.edit")
     * @param User $user
     * @param Request $request
     * @param Edit\Handler $handler
     * @return Response
     */
    public function edit(User $user, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromUser($user);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/{id}/role", name="users.role")
     * @param User $user
     * @param Request $request
     * @param Role\Handler $handler
     * @return Response
     */
    public function role(User $user, Request $request, Role\Handler $handler): Response
    {
        if ($user->getId()->getValue() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to change role for yourself.');

            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = Role\Command::fromUser($user);

        $form = $this->createForm(Role\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
            } catch (DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/users/role.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/{id}/confirm", name="users.confirm", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param Confirm\Manual\Handler $handler
     * @return Response
     */
    public function confirm(User $user, Request $request, Confirm\Manual\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('confirm', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Confirm\Manual\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    /**
     * @Route("users/{id}/activate", name="users.activate", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param Activate\Handler $handler
     * @return Response
     */
    public function activate(User $user, Request $request, Activate\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('activate', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Activate\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }

    /**
     * @Route("users/{id}/block", name="users.block", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param Block\Handler $handler
     * @return Response
     */
    public function block(User $user, Request $request, Block\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('block', $request->request->get('token'))) {
            return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
        }

        $command = new Block\Command($user->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
    }
}
