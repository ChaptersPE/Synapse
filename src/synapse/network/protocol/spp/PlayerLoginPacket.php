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

use synapse\utils\UUID;

class PlayerLoginPacket extends DataPacket{
	const NETWORK_ID = Info::PLAYER_LOGIN_PACKET;

	/** @var UUID */
	public $uuid;
	public $address;
	public $port;
	public $isFirstTime;
	public $cachedLoginPacket;

	public function encode(){
		$this->reset();
		$this->putUUID($this->uuid);

		$this->putAddress($this->address, $this->port);

		$this->putByte($this->isFirstTime);
		$this->putLShort(strlen($this->cachedLoginPacket));
		$this->put($this->cachedLoginPacket);
	}

	public function decode(){
		$this->uuid = $this->getUUID();

		$this->getAddress($this->address, $this->port);

		$this->isFirstTime = ($this->getByte() == 1);
		$this->cachedLoginPacket = $this->get($this->getLShort());
	}
}