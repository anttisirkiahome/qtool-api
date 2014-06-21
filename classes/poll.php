<?php


class Poll {

	public function publishPoll($id) {
		if(isset($id)) {
			#var_dump(intval($id));
			$people = ORM::for_table('person')->raw_query('UPDATE poll SET publishtime= NOW(), published = TRUE WHERE ID = :id', array('id' => $id))->find_many();
	
			#$t = ORM::for_table('poll')->find_one(68);
			#$t->creator_ID = 3;
			#$t->save();
			#	var_dump($t);
		#if($update->id) return true;
		#return false;
		}

		
	}

	public function getLatestPoll() {
		$latestId = ORM::for_table('poll')->max('ID');
		$ret = array();

		$latestPoll = ORM::for_table('poll')
			->left_outer_join('answer', array('answer.poll_ID', '=', 'poll.ID'))
			->select_many('poll.ID', 'poll.duration', 'poll.created', 'poll.publishtime', 'poll.published', 'poll.question', 'poll.theme', 'answer.answer')
			->where('poll.ID', $latestId)
			->find_array();

			if(isset($latestPoll)) {
				//FIXME dont save duration as a string
				//value of ret[duration] is in seconds
				$ret['duration'] = intval(substr($latestPoll[0]['duration'], 1,2)) * 60 + intval(substr($latestPoll[0]['duration'], 3,2));
				$ret['created'] = strtotime($latestPoll[0]['created']);
				$ret['publishTime'] = strtotime($latestPoll[0]['publishtime']);
				$ret['expired'] = true;
				$ret['ID'] = $latestPoll[0]['ID'];

				if($ret['publishTime'] && $ret['publishTime'] + $ret['duration'] > time()) {
					$ret['expired'] = false;
				}
				$ret['published']	=$latestPoll[0]['published'];
				$ret['success'] 	= true;
				$ret['question'] 	= $latestPoll[0]['question'];
				$ret['theme'] 		= $latestPoll[0]['theme'];

				foreach ($latestPoll as $poll) {
					$ret['answers'][] = array('answer' => $poll['answer'], 'order' => $poll['order']); 
				}

			} else {
				$ret['success'] = false;	
			}
		
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

			$pollId = $newPoll->id();

			$i = 1;
			foreach ($poll['answers'] as &$answer) {
				$answers = ORM::for_table('answer')->create();
				$answers->answer = $answer;
				$answers->order = $i;
				$answers->poll_ID = $pollId;
				$answers->save();	
				$i++;
			}
			
			return true;	
		}
	}

}