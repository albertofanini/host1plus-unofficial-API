<?php
/*
Copyright 2014 - Alberto Fanini

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

include("simple_html_dom.php");

class Host1Plus {

	var $ckfile;
	var $ch;

	function Host1Plus($username, $password) {
       
		$this->ckfile = tempnam ("/tmp", "COOKIES");
		$this->ch = curl_init();
		
		// get security token
		$url = "https://manage.host1plus.com/clientarea.php";
		curl_setopt ($this->ch,CURLOPT_URL, $url);
		curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $this->ckfile);
		curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec ($this->ch);
		
		$html = str_get_html($output);
		
		foreach($html->find('input') as $element)
			if (strlen($element->value) == 40)
				 $token = $element->value;
		
		
		// perform login
		$url = 'https://manage.host1plus.com/dologin.php';
		$fields = array('token' => $token,'username' => $username,'password' => $password);
		
		$fields_string = "";
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		curl_setopt($this->ch,CURLOPT_URL, $url);
		curl_setopt($this->ch,CURLOPT_COOKIEFILE, $this->ckfile);
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch,CURLOPT_POST, count($fields));
		curl_setopt($this->ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_exec($this->ch);

	}

	private function send_request ($vpsid, $type = "refresh") {
	
		$url = 'https://manage.host1plus.com/modules/servers/ovzp/ovzp_call.php';
		$fields = array('command' => $type,'ctid' => $vpsid);
		$fields_string="";
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		curl_setopt($this->ch,CURLOPT_URL, $url);
		curl_setopt($this->ch,CURLOPT_COOKIEFILE, $this->ckfile);
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch,CURLOPT_POST, count($fields));
		curl_setopt($this->ch,CURLOPT_POSTFIELDS, $fields_string);
		return curl_exec($this->ch);
	}
	
	function status ($vpsid) {
		return $this->send_request($vpsid);
	}
	
	function start ($vpsid) {
		if ($this->status($vpsid) == "stopped")
			return ($this->send_request($vpsid, "start") == "success" ? true : false);
		else return false;
	}

	function stop ($vpsid) {
		if ($this->status($vpsid) == "running")
			return ($this->send_request($vpsid, "stop") == "success" ? true : false);
		else return false;
	}
	
	function reboot ($vpsid) {
		if ($this->status($vpsid) == "running")
			return ($this->send_request($vpsid, "reboot") == "success" ? true : false);
		else return false;
	}
	
	function logout () {
		$url = "https://manage.host1plus.com/logout.php";
		curl_setopt($this->ch,CURLOPT_URL, $url);
		curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $this->ckfile);
		curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec ($this->ch);
		curl_close($this->ch);
	}
   
}

/*
EXAMPLE OF USAGE:

include "host1plus.class.php";

$username = "mylogin@information";
$password = "mypassword";
$ovzp_ctid = "####"; // get this number on your client area

$h1p = new Host1Plus($username, $password); // login to your client area on Host1Plus
$h1p->status($ovzp_ctid); // get vps status
$h1p->stop($ovzp_ctid); // shut down your vps
$h1p->start($ovzp_ctid); // start your vps
$h1p->reboot($ovzp_ctid); // restart your vps

$h1p->logout(); // recommended action

*/


?>