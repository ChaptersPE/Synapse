<?php

namespace synapse\network\protocol\spp;

class ChannelComsPacket extends DataPacket{
	const NETWORK_ID = Info::CHANNEL_COMS_PACKET;

	public $protocol = Info::CURRENT_PROTOCOL;
	public $channel;
	public $data;

	public function encode(){
		$this->reset();
		$this->putByte($this->channel);
		$this->put($this->data);
	}

	public function decode(){
		$this->channel = $this->getByte();
		$this->data = $this->get(count($this->buffer) - $this->offset);
	}
}