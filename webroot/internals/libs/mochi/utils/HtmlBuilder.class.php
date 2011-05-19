<?php
class HtmlBuilder
{
	private $html = '';
	
	function getHtml() {
		return $this->html;
	}
	
	function append($value) {
		$this->html .= strval($value);
		return $this;
	}
	
	function appendEscaped($value) {
		$this->append(self::escapeHtml($value));
		return $this;
	}
	
	function newline() {
		$this->append("\n");
		return $this;
	}
	
	function startTag($name) {
		$this->append('<');
		$this->append($name); 
		return $this;
	}
	
	function startTagWithAttrs($name, array $attrs) {
		$this->startTag($name);
		$this->attrs($attrs);
		return $this;
	}
	
	function closeTag() {
		$this->append('>');
		return $this;
	}
	
	function endEmptyElement() {
		$this->append('/>'); 
		return $this;
	}
	
	function endTag($name) {
		$this->append('</');
		$this->append($name);
		$this->append('>');
		return $this;
	}
	
	function attrRaw($name, $value) {
		if (!is_null($value)) {
			$this->append(' ');
			$this->append($name);
			$this->append('="');
			$this->append($value);
			$this->append('"');
		}
		return $this;
	}
	
	function attr($name, $value) {
		if (!is_null($value)) {
			$this->attrRaw($name, self::escapeHtml($value));
		}
		return $this;
	}
	
	function attrs(array $attrs) {
		foreach ($attrs as $name => $value) {
			$this->attr($name, $value);
		}
		return $this;
	}
	
	function tagWithText($name, $text, array $attrs = NULL) {
		$this->startTag($name);
		if (!is_null($attrs)) $this->attrs($attrs);
		$this->closeTag();
		$this->appendEscaped($text);
		$this->endTag($name);
		return $this;
	}
		
	static function escapeHtml($value) {
		return htmlentities(strval($value), ENT_QUOTES, mb_internal_encoding());
	}
}
?>
