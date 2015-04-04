<?php
class HC_Notifier
{
	var $msg = array();
	var $queue = array();
	var $users = array();

	protected function __construct()
	{
	}

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$instance = new HC_Notifier();
		}
		return $instance;
	}

/* returns msg id */
	function add_message( $msg )
	{
		$this->msg[] = $msg;
		return (count($this->msg) - 1);
	}

	function enqueue_message( $msg_id, $user, $group_id = 0 )
	{
		$this->users[$user->id] = $user;

		if( ! isset($this->queue[$user->id]) ){
			$this->queue[$user->id] = array();
		}
		
		if( ! $group_id ){
			$group_id = 0;
		}

		if( ! isset($this->queue[$user->id][$group_id]) ){
			$this->queue[$user->id][$group_id] = array();
		}

		$this->queue[$user->id][$group_id][] = $msg_id;
	}

/* call this in the post_controller hook */
	function run()
	{
		reset( $this->queue );
		foreach( $this->queue as $user_id => $ma ){
			$u = $this->users[ $user_id ];
			reset( $ma );
			foreach( $ma as $group_id => $msgs ){
				/* group similar messages */
				if( $group_id ){
					$final_msg = new stdClass();
					$final_msg->subject = '';
					$final_msg->body = array();

					reset( $msgs );
					foreach( $msgs as $msg_id ){
						$msg = $this->msg[$msg_id];
						$final_msg->subject = $msg->subject;
						$final_msg->body = array_merge( $final_msg->body, $msg->body );
						$final_msg->body[] = '';
					}

					if( count($msgs) > 1 ){
						$final_msg->subject .= ' (' . count($msgs) . ')'; 
					}
					$this->transport_email( $final_msg, $u );
				}
				else {
					reset( $msgs );
					foreach( $msgs as $msg_id ){
						$msg = $this->msg[$msg_id];
						$this->transport_email( $msg, $u );
					}
				}
			}
		}
		$this->queue = array();
	}

	function transport_email( $msg, $u )
	{
		$CI =& ci_get_instance();

		$subj = $msg->subject;
		$body = join( "\n", $msg->body );

		$CI->hc_email->setSubject( $subj );
		$CI->hc_email->setBody( $body );
		$CI->hc_email->sendToOne( $u->email );
	}
}