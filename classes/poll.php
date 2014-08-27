<?php

class Poll {

	public function getThemes() {
		$themes = ORM::for_table('themes')->find_array();
		return $themes;
	}

	public function vote($id) {
		if(isset($id)) {
			$pdo = ORM::get_db();
			$raw_query = 'UPDATE Answer SET votes=votes+1 WHERE ID = ?';
			$raw_parameters = array($id);
			$statement = $pdo->prepare($raw_query);
			return $statement->execute($raw_parameters);
		}
	}

	public function publishPoll($id, $duration) {
		if(isset($id)) {
			$pdo = ORM::get_db();
			$raw_query = 'UPDATE Poll SET publishtime= NOW(), published = TRUE, expirationtime= DATE_ADD(NOW(), INTERVAL ? SECOND) WHERE ID = ?';
			$raw_parameters = array($duration, $id);
			$statement = $pdo->prepare($raw_query);
			return $statement->execute($raw_parameters);
		}
	}

	public function getLatestPoll() {



		$latestId = ORM::for_table('poll')->max('ID');
		$ret = array();

		$latestPoll = ORM::for_table('Poll')
			->left_outer_join('answer', array('answer.poll_ID', '=', 'poll.ID'))
			->left_outer_join('themes', array('poll.theme' , '=', 'themes.ID'))
			->select_many('poll.ID', array('answer_id' => 'answer.ID'), 'answer.votes', 'poll.duration', 'poll.created', 'themes.url', 'poll.publishtime', 'poll.published', 'poll.question', 'poll.expirationtime', 'poll.theme', 'answer.answer', 'answer.order')
			->where('poll.ID', $latestId)
			->find_array();

			if(isset($latestPoll)) {



				$curTime = ORM::for_table('Poll')->raw_query('SELECT NOW() AS n')->find_one();
				$currentTime = strtotime($curTime->n);

				//FIXME dont save duration as a string
				//value of ret[duration] is in seconds
				$ret['duration'] = intval(substr($latestPoll[0]['duration'], 1,2)) * 60 + intval(substr($latestPoll[0]['duration'], 3,2));
				$ret['created'] = strtotime($latestPoll[0]['created']);
				$ret['publishTime'] = strtotime($latestPoll[0]['publishtime']);
				$ret['expired'] = true;
				$ret['ID'] = $latestPoll[0]['ID'];
				$ret['expirationTime'] = strtotime($latestPoll[0]['expirationtime']);		

				if($ret['created'] && $ret['expirationTime'] && ($currentTime < ($ret['duration'] + $ret['created']) ) ) {
					$ret['expired'] = false;
				}
				$ret['timeLeft'] = ($ret['duration'] + $ret['created']) - $currentTime;
				$ret['published']	=$latestPoll[0]['published'];
				$ret['success'] 	= true;
				$ret['question'] 	= $latestPoll[0]['question'];
				$ret['theme'] 		= $latestPoll[0]['url'];
				$tempTotalVotes = 0;
				foreach ($latestPoll as $poll) {
					$tempTotalVotes += $poll['votes'];
					$ret['answers'][] = array('answer' => $poll['answer'], 'votes' =>$poll['votes'], 'order' => $poll['order'], 'id' => $poll['answer_id']); 
				}
				$ret['totalVotes'] = $tempTotalVotes;

			} else {
				$ret['success'] = false;	
			}
		
		return $ret;
	}

	public function savePoll($poll) {
	
		//find the correct theme_ID
		$theme = ORM::for_table('themes')->where('name', $poll['theme'])->find_one();

		$newPoll = ORM::for_table('poll')->create();

		$newPoll->duration = $poll['duration'];
		$newPoll->theme = $theme->ID;
		$newPoll->question = $poll['question'];
		$newPoll->published = true;
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

		$duration = intval(substr($poll['duration'], 1,2)) * 60 + intval(substr($poll['duration'], 3,2));

		$pdo = ORM::get_db();
		$raw_query = 'UPDATE Poll SET expirationtime= DATE_ADD(NOW(), INTERVAL ? SECOND) WHERE ID = ?';
		$raw_parameters = array($duration, $pollId);
		$statement = $pdo->prepare($raw_query);
		$statement->execute($raw_parameters);
		
		return true;	
		

	}

}