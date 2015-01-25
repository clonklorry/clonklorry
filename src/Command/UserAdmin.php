<?php

namespace Lorry\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lorry\Environment;
use Lorry\ModelFactory;
use Lorry\Model\User;

class UserAdmin extends Command {

	protected function configure() {
		$this
				->setName('user:admin')
				->setDescription('Make a user admin')
				->addArgument(
						'username', InputArgument::REQUIRED, 'The username of the new admin'
				)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$lorry = new Environment();
		$lorry->setup();

		$username = $input->getArgument('username');
		$user = ModelFactory::build('User')->byUsername($username, true);
		if($user === null) {
			throw new \RuntimeException('Couldn\'t find user with username "'.$username.'".');
		}
		$user->setPermission(User::PERMISSION_ADMINISTRATE);
		if($user->save()) {
			$output->writeln('<info>'.$user->getUsername().' is now an administrator</info>');
		}
		else {
			$output->writeln('<error>Error saving the user</error>');
		}
	}

}
