<?php

namespace Lorry\Presenter\Publish;

use Lorry\Presenter;
use Lorry\ModelFactory;
use Lorry\Exception\FileNotFoundException;
use Lorry\Exception\ModelValueInvalidException;

class Release extends Presenter {

	public static function getRelease($id, $version) {
		$release = ModelFactory::build('Release')->byVersion($version, $id);
		if(!$release) {
			throw new FileNotFoundException();
		}
		return $release;
	}

	const UPLOAD_DIR = '../app/upload/publish';

	public function get($id, $version) {
		$this->security->requireLogin();

		$addon = Edit::getAddon($id, $this->session->getUser());
		$release = Release::getRelease($addon->getId(), $version);

		$this->context['title'] = sprintf(gettext('Edit %s'), $addon->getTitle().' '.$release->getVersion());
		$this->context['game'] = $addon->fetchGame()->getShort();
		$this->context['addontitle'] = $addon->getTitle();
		$this->context['addonid'] = $addon->getId();
		$this->context['version'] = $release->getVersion();

		$this->context['whatsnew'] = $release->getWhatsnew();
		$this->context['changelog'] = $release->getChangelog();

		$this->context['released'] = $release->isReleased();
		$latest = ModelFactory::build('Release')->latest($addon->getId());
		$this->context['latest'] = ($latest && $latest->getId() == $release->getId());
		$this->context['scheduled'] = $release->isScheduled();
		if(!isset($this->context['new_version'])) {
			$this->context['new_version'] = $release->getVersion();
		}
		if(isset($_GET['version-changed'])) {
			$this->success('version', gettext('Release saved.'));
		}

		$this->display('publish/release.twig');
	}

	public function post($id, $version) {
		$this->security->requireLogin();
		$this->security->requireValidState();

		$addon = Edit::getAddon($id, $this->session->getUser());
		$release = Release::getRelease($addon->getId(), $version);

		if(isset($_POST['general-form'])) {
			$new_version = filter_input(INPUT_POST, 'version');
			$this->context['new_version'] = $new_version;

			$errors = array();

			try {
				$existing = ModelFactory::build('Release')->byVersion($new_version, $id);
				if($existing && $existing->getId() != $release->getId()) {
					$errors[] = gettext('Version already exists.');
				}

				$release->setVersion($new_version);
			} catch(ModelValueInvalidException $ex) {
				$errors[] = sprintf(gettext('Version is %s.'), $ex->getMessage());
			}

			if(empty($errors)) {
				if($release->modified()) {
					if($release->save()) {
						$this->success('version', gettext('Version saved.'));
						$this->redirect('/publish/'.$addon->getId().'/'.$new_version.'?version-changed');
					} else {
						$this->error('version', gettext('Error saving the release.'));
					}
				}
			} else {
				$this->error('version', implode($errors, '<br>'));
			}
		}

		if(isset($_POST['changes-form'])) {
			$whatsnew = trim(filter_input(INPUT_POST, 'whatsnew'));
			$changelog = trim(filter_input(INPUT_POST, 'changelog'));

			$errors = array();

			try {
				$release->setWhatsnew($whatsnew);
			} catch(ModelValueInvalidException $ex) {
				$errors[] = sprintf(gettext('"%s" is %s.'), gettext('What\'s new?'), $ex->getMessage());
			}
			try {
				$release->setChangelog($changelog);
			} catch(ModelValueInvalidException $ex) {
				$errors[] = sprintf(gettext('Changelog is %s.'), $ex->getMessage());
			}

			if(empty($errors)) {
				if($release->modified()) {
					if($release->save()) {
						$this->success('changes', gettext('Changes saved.'));
					} else {
						$this->error('changes', gettext('Error saving the changes.'));
					}
				}
			} else {
				$this->error('changes', implode($errors, '<br>'));
			}
		}

		return $this->get($id, $version);
	}

}
