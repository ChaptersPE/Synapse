<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */
 
namespace synapse\network\protocol\spp;

class ConnectPacket extends DataPacket{
	const NETWORK_ID = Info::CONNECT_PACKET;

	public $protocol = Info::CURRENT_PROTOCOL;
	public $maxPlayers;
	public $description;
	public $encodedPassword;

	public function encode(){
		$this->reset();
		$this->putInt($this->protocol);
		$this->putLShort($this->maxPlayers);
		$this->putString($this->description);
		$this->putString($this->encodedPassword);
	}

	public function decode(){
		$this->protocol = $this->getInt();
		$this->maxPlayers = $this->getLShort();
		$this->description = $this->getString();
		$this->encodedPassword = $this->getString();
	}
}