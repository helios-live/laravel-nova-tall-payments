<?php

namespace IdeaToCode\LaravelNovaTallPayments\Listeners;

use Laravel\Jetstream\Events\TeamEvent;



class SetTeamAffiliate
{
	/**
	 * Handle the event.
	 *
	 * @param  object  $event
	 * @return void
	 */
	public function handle(TeamEvent $event)
	{
		$t = $event->team;
		$o = $t->owner;
		if (is_null($o)) {
			return;
		}
		$aff = $o->affiliate;

		if (!is_null($aff)) {
			$t->affiliate()->associate($aff);
			$t->save();
		}
	}
}