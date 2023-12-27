<?php

/***************************************************************************
PHP vCard class v2.0
(c) Kai Blankenhorn
www.bitfolge.de/en
kaib@bitfolge.de


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
***************************************************************************/
namespace Library;

class vCard {

	public $properties, $filename;

	private function encode($string) {
		return $this->escape($this->quoted_printable_encode($string));
	}

	private function escape($string) {
		return str_replace(";", "\;", $string);
	}

	private function quoted_printable_encode($input, $line_max = 76) {
		$hex = array(
				'0',
				'1',
				'2',
				'3',
				'4',
				'5',
				'6',
				'7',
				'8',
				'9',
				'A',
				'B',
				'C',
				'D',
				'E',
				'F' 
		);
		$lines = preg_split("/(?:\r\n|\r|\n)/", $input);
		$eol = "\r\n";
		$linebreak = "=0D=0A";
		$escape = "=";
		$output = "";
		
		for($j = 0; $j < count($lines); $j ++) {
			$line = $lines[$j];
			$linlen = strlen($line);
			$newline = "";
			for($i = 0; $i < $linlen; $i ++) {
				$c = substr($line, $i, 1);
				$dec = ord($c);
				if (($dec == 32) && ($i == ($linlen - 1))) { // convert space at eol only
					$c = "=20";
				} elseif (($dec == 61) || ($dec < 32) || ($dec > 126)) { // always encode "\t", which is *not* required
					$h2 = floor($dec / 16);
					$h1 = floor($dec % 16);
					$c = $escape . $hex["$h2"] . $hex["$h1"];
				}
				if ((strlen($newline) + strlen($c)) >= $line_max) { // CRLF is not counted
					$output .= $newline . $escape . $eol; // soft line break; " =\r\n" is okay
					$newline = "    ";
				}
				$newline .= $c;
			}
			$output .= $newline;
			if ($j < count($lines) - 1)
				$output .= $linebreak;
		}
		return trim($output);
	}

	public function setPhoneNumber($number, $type = "") {
		$key = "TEL";
		if ($type != "")
			$key .= ";" . $type;
		$key .= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = $this->quoted_printable_encode($number);
	}

	public function setFormattedName($name) {
		$this->properties["FN"] = $this->quoted_printable_encode($name);
	}

	public function setName($family = "", $first = "", $additional = "", $prefix = "", $suffix = "") {
		$this->properties["N"] = "$family;$first;$additional;$prefix;$suffix";
		$this->filename = "$first%20$family.vcf";
		if ($this->properties["FN"] == "")
			$this->setFormattedName(trim("$prefix $first $additional $family $suffix"));
	}

	public function setBirthday($date) { // $date format is YYYY-MM-DD
		$this->properties["BDAY"] = $date;
	}

	public function setAddress($postoffice = "", $extended = "", $street = "", $city = "", $region = "", $zip = "", $country = "", $type = "HOME;POSTAL") {
		// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key = "ADR";
		if ($type != "")
			$key .= ";$type";
		$key .= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = $this->encode($name) . ";" . $this->encode($extended) . ";" . $this->encode($street) . ";" . $this->encode($city) . ";" . $this->encode($region) . ";" . $this->encode($zip) . ";" . $this->encode($country);
		
		if ($this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] == "") {
			// $this->setLabel($postoffice, $extended, $street, $city, $region, $zip, $country, $type);
		}
	}

	public function setLabel($postoffice = "", $extended = "", $street = "", $city = "", $region = "", $zip = "", $country = "", $type = "HOME;POSTAL") {
		$label = "";
		if ($postoffice != "")
			$label .= "$postoffice\r\n";
		if ($extended != "")
			$label .= "$extended\r\n";
		if ($street != "")
			$label .= "$street\r\n";
		if ($zip != "")
			$label .= "$zip ";
		if ($city != "")
			$label .= "$city\r\n";
		if ($region != "")
			$label .= "$region\r\n";
		if ($country != "")
			$country .= "$country\r\n";
		
		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($label);
	}

	public function setEmail($address) {
		$this->properties["EMAIL;INTERNET"] = $address;
	}

	public function setNote($note) {
		$this->properties["NOTE;ENCODING=QUOTED-PRINTABLE"] = $this->quoted_printable_encode($note);
	}
	
	// $type may be WORK | HOME
	public function setURL($url, $type = "") {
		$key = "URL";
		if ($type != "")
			$key .= ";$type";
		$this->properties[$key] = $url;
	}

	public function getVCard() {
		$text = "BEGIN:VCARD\r\n";
		$text .= "VERSION:2.1\r\n";
		foreach ($this->properties as $key => $value) {
			$text .= "$key:$value\r\n";
		}
		$text .= "REV:" . date("Y-m-d") . "T" . date("H:i:s") . "Z\r\n";
		// $text.= "MAILER:PHP vCard class by Kai Blankenhorn\r\n";
		$text .= "END:VCARD\r\n";
		return $text;
	}

	public function getFileName() {
		return $this->filename;
	}
}