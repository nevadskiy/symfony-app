<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\Entity\User\Role as RoleValue;
use App\ReadModel\User\UserFetcher;
use App\Model\User\UseCase\Role;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoleCommand extends Command
{
    private $users;
    private $handler;
    private $validator;

    public function __construct(UserFetcher $users, ValidatorInterface $validator, Role\Handler $handler)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('user:role')
            ->setDescription('Changes user role');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        $user = $this->users->findByEmail($email);

        if (!$user) {
            throw new LogicException('User is not found');
        }

        $roles = [RoleValue::USER, RoleValue::ADMIN];

        $command = new Role\Command($user->id);
        $command->role = $helper->ask($input, $output, new ChoiceQuestion('Role: ', $roles, 0));

        $violations = $this->validator->validate($command);

        if ($violations->count()) {
            foreach ($violations as $violation) {
                $output->writeln("<error>{$violation->getPropertyPath()}:{$violation->getMessage()}}</error>");
            }
            return;
        }

        $this->handler->handle($command);

        $output->writeln('<info>Done!</info>');
    }
}
