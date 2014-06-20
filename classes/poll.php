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
			return true;	
		}
		

	}
}