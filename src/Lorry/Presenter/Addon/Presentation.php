<?php

namespace Lorry\Presenter\Addon;

use Lorry\Presenter;
use Lorry\ModelFactory;
use Lorry\Exception\FileNotFoundException;

class Presentation extends Presenter {

	public function get($gamename, $addonname, $release = 'latest') {
		$game = ModelFactory::build('Game')->byShort($gamename);
		if(!$game) {
			throw new FileNotFoundException('game '.$gamename);
		}

		$addon = ModelFactory::build('Addon')->byShort($addonname, $game->getId());
		if(!$addon) {
			throw new FileNotFoundException('addon '.$addonname);
		}

		$this->context['title'] = $addon->getTitle();

		$this->display('addon/release.twig');
	}

}