<?php
require_once('PHPUnit/Framework.php');
require_once('mochi/utils/HtmlBuilder.class.php');

class HtmlBuilderTest extends PHPUnit_Framework_TestCase
{
	private $object;
	
	function setUp() {
		$this->object = new HtmlBuilder();
	}
	
	function test_append() {
		$this->object
			->append("1 + 1 = ")
			->append(2);
		
		$this->assertEquals("1 + 1 = 2", $this->object->getHtml());
	}
	
	function test_appendEscaped() {
		$this->object->appendEscaped('<tag attr="value"/>');
		$this->assertEquals("&lt;tag attr=&quot;value&quot;/&gt;", $this->object->getHtml());
	}
	
	function test_startTag() {
		$this->object->startTag("tag-name");
		$this->assertEquals("<tag-name", $this->object->getHtml());
	}
	
	function test_closeTag() {
		$this->object->startTag("tag-name")->closeTag();
		$this->assertEquals("<tag-name>", $this->object->getHtml());
	}
	
	function test_endEmptyElement() {
		$this->object->startTag("tag-name")->endEmptyElement();
		$this->assertEquals("<tag-name/>", $this->object->getHtml());
	}
	
	function test_endTagWithName() {
		$this->object->endTag("tag-name");
		$this->assertEquals("</tag-name>", $this->object->getHtml());
	}
	
	function test_nullAttr() {
		$this->object->startTag("tag-name")->attr("attr", NULL);
		$this->assertEquals("<tag-name", $this->object->getHtml());
	}
		
	function test_attr() {
		$this->object->attr("attr", "value");
		$this->assertEquals(' attr="value"', $this->object->getHtml());
	}
	
	function test_attrRaw() {
		$this->object->attrRaw("attr", "<tag>");
		$this->assertEquals(' attr="<tag>"', $this->object->getHtml());
	}
	
	function test_attrEscaped() {
		$this->object->attr("attr", "<tag>");
		$this->assertEquals(' attr="&lt;tag&gt;"', $this->object->getHtml());
	}
	
	function test_attrs() {
		$this->object->attrs(array("type" => "text", "name" => "title"));
		$this->assertEquals(' type="text" name="title"', $this->object->getHtml());
	}
	
	function test_startTagWithAttrs() {
		$this->object->startTagWithAttrs("tag", array("attr" => "value"));
		$this->assertEquals('<tag attr="value"', $this->object->getHtml());
	}
	
	function test_tagWithText() {
		$this->object->tagWithText("p", "hello");
		$this->assertEquals('<p>hello</p>', $this->object->getHtml());
	}
	
	function test_tagWithTextAndAttrs() {
		$this->object->tagWithText("p", "hello", array("id" => "1"));
		$this->assertEquals('<p id="1">hello</p>', $this->object->getHtml());
	}
}
?>
