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

namespace synapse\network\synlib;

use synapse\utils\Binary;

class ClientConnection{

	const MAGIC_BYTES = "\x35\xac\x66\xbf";

	private $receiveQueue = [];
	private $sendQueue = [];
	/** @var resource */
	private $socket;
	private $ip;
	private $port;
	
	private $lastBuffer = '';

	public function __construct(ClientManager $clientManager, $socket){
		$this->clientManager = $clientManager;
		$this->socket = $socket;
		socket_getpeername($this->socket, $address, $port);
		socket_set_nonblock($this->socket);
		$this->ip = $address;
		$this->port = $port;
		$clientManager->getServer()->getLogger()->notice("Client [$address:$port] has connected.");
	}

	public function getHash(){
		return $this->ip . ':' . $this->port;
	}

	public function getIp() : string {
		return $this->ip;
	}

	public function getPort() : int{
		return $this->port;
	}

	public function update(){
		$err = socket_last_error($this->socket);
		if($err !== 0 && $err !== 11){
			$this->clientManager->getServer()->getLogger()->error("Synapse client [$this->ip:$this->port] has disconnected unexpectedly error:{$err}");
			return false;
		}else{
			$currenBuffer = $this->lastBuffer;
			$this->lastBuffer = '';
			while (strlen($buffer = @socket_read($this->socket, 65535, PHP_BINARY_READ)) > 0) {
				$currenBuffer .= $buffer;
			}
			
			
			if (($dataLen = strlen($currenBuffer)) > 0) {
				$offset = 0;
				while ($offset < $dataLen) {
						$len = unpack('N', substr($currenBuffer, $offset, 4));
						$len = $len[1];
						if(($offset + $len + 4) > $dataLen) {
							$this->lastBuffer = substr($currenBuffer, $offset);
							break;
						}
						$offset += 4;	
						$data = zlib_decode(substr($currenBuffer, $offset, $len));
						$offset += $len;
						if(!empty($data)) {	
							$this->receiveQueue[] = $data;
						}						
					
				}
			}
			
			if(!empty($this->sendQueue)) {
				$buffer = array_shift($this->sendQueue);
				$buffer = zlib_encode($buffer,  ZLIB_ENCODING_DEFLATE, 7);
				socket_write($this->socket, pack('N', strlen($buffer)) . $buffer);
			}
			return true;
		}
	}

	public function getSocket(){
		return $this->socket;
	}

	public function close(){
		@socket_close($this->socket);
	}

	public function readPacket(){
		$buffer = array_shift($this->receiveQueue);
		if($buffer != null) {
			return $buffer;
		}
		return null;
	}

	public function writePacket($data){
		$this->sendQueue[] = $data;
	}
}