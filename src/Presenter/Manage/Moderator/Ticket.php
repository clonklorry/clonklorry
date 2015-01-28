<?php

namespace Lorry\Presenter\Manage\Moderator;

use Lorry\Presenter;
use Lorry\ModelFactory;
use Lorry\Exception\FileNotFoundException;
use Lorry\EmailFactory;

class Ticket extends Presenter {

	public static function getTicket($id) {
		$ticket = ModelFactory::build('Ticket')->byId($id);
		if(!$ticket) {
			throw new FileNotFoundException();
		}
		return $ticket;
	}

	public function get($id) {
		$this->security->requireModerator();
		$this->offerIdentification();
		$this->security->requireIdentification();

		$ticket = $this->getTicket($id);
		$this->context['number'] = $ticket->getId();
		$this->context['request'] = $ticket->getRequest();
		$this->context['acknowledged'] = $ticket->isAcknowledged();
		$this->context['escalated'] = $ticket->isEscalated();

		$user = $ticket->fetchUser();
		if($user) {
			$this->context['user'] = $user->forPresenter();
		}
		$staff = $ticket->fetchStaff();
		if($staff) {
			$this->context['staff'] = $staff->forPresenter();
		}

		$this->display('manage/moderator/ticket.twig');
	}

	public function post($id) {
		$this->security->requireModerator();
		$this->security->requireIdentification();

		$this->security->requireValidState();

		$ticket = $this->getTicket($id);
		
		if(!$ticket->isEscalated() && !$ticket->isAcknowledged()) {
			$staff = $this->session->getUser();
			if(isset($_POST['escalate'])) {
				$mail = EmailFactory::build('Ticket');

				$user = $ticket->fetchUser();
				if($user) {
					$mail->setReplyTo($user->getEmail());
					$mail->setUser($user);
				}
				
				$mail->setMessage($ticket->getRequest());
				$mail->setStaff($staff);
				
				if($this->mail->send($mail)) {
					$ticket->escalate();
				} else {
					$this->error('ticket', gettext('The ticket could not be escalated.'));
				}
			} elseif(isset($_POST['acknowledge'])) {
				$ticket->acknowledge();
			}
			$ticket->setStaff($staff->getId());
			if($ticket->modified()) {
				$ticket->save();
			}
		}

		$this->get($id);
	}

}