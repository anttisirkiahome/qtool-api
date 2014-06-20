<?php

class Poll {

	public function getLatestPoll() {
		$ret = ['success' => false];
		return $ret;
	}

	public function savePoll($poll) {
		if( !isset($poll) || !isset($poll['duration']) || !isset($poll['question']) || !isset($poll['theme']) || !isset($poll['answers']) ) {
			return false;
		}  else {
			$theme = ORM::for_table('themes')->where('name', $poll['theme'])->find_one();
			if(!isset($theme->ID)) {
				return false;
			}
			//FIXME missing user_id
			$newPoll = ORM::for_table('poll')->create();
			$newPoll->duration = $poll['duration'];
			$newPoll->theme = $theme->ID;
			$newPoll->question = $poll['question'];
			$newPoll->save();

			$id = $newPoll->id();

			$i = 1;
			foreach ($poll['answers'] as &$answer) {
				$answers = ORM::for_table('answer')->create();
				$answers->answer = $answer;
				$answers->order = $i;
				$answers->poll_ID = $id;
				$answers->save();	
				$i++;
			}
			
			return true;	
		}
	}

}