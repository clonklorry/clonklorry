<?php

namespace Lorry\Presenter\Account;

use Lorry\Presenter;
use Lorry\ModelFactory;
use Lorry\Exception\FileNotFoundException;
use Lorry\Exception\ForbiddenException;
use Lorry\Exception\BadRequestException;

class Activate extends Presenter {

	public function get($username) {
		$user = ModelFactory::build('User')->byUsername($username);
		if(!$user) {
			throw new FileNotFoundException('user '.$username);
		}

		$expires = filter_input(INPUT_GET, 'expires');
		$address = urldecode(filter_input(INPUT_GET, 'address'));

		$hash = filter_input(INPUT_GET, 'hash');
		if(empty($hash)) {
			throw new BadRequestException();
		}
		$hash = $hash ? $hash : '';

		$expected = $this->security->signActivation($user, $expires, $address);

		if(hash_equals($expected, $hash) !== true) {
			throw new ForbiddenException('hash does not match expected value');
		}

		if($expires < time()) {
			throw new ForbiddenException('token expired');
		}

		if($address != $user->getEmail()) {
			throw new ForbiddenException('token is for another email address');
		}

		$user->activate();
		$user->save();
		$this->redirect('/settings');
	}

}
