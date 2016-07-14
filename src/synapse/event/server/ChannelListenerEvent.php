<?php

namespace synapse\event\server;

use synapse\event\Event;

class ChannelListenerEvent extends Event {
	public static $handlerList = null;

	var $channel;
	var $data;

	public function __construct($channel, $data){
		$this->channel = $channel;
		$this->data = $data;
	}
}