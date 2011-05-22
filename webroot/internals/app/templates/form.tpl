<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<title>Form Example</title>
<link type="text/css" rel="stylesheet" href="{$basePath}/assets/mochi/control.css"/>
<script type="text/javascript" src="{$basePath}/assets/mochi/control.js"></script>
<style type="text/css">
{literal}
p.entry {
	width: 500px;
}
{/literal}
</style>
</head>
<body>
<h1>Form Example</h1>

{$form->startTag()|smarty:nodefaults}
{$form->renderErrors()|smarty:nodefaults}
{$form->fields.content->render()|smarty:nodefaults}
<br/><input type="submit" value=" Send "/>
{$form->endTag()|smarty:nodefaults}

{foreach from=$entries|smarty:nodefaults item=entry}
<p class="entry">{$entry}</p>
{/foreach}

</body>
</html>
