{include file='header.tpl'}

<style type="text/css">
{literal}
p.entry {
	width: 500px;
}
{/literal}
</style>
<title>Form Example</title>
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
