{include file='_header.tpl'}

<title>Stateful Form Example</title>
</head>

<body>
<h1>Stateful Form Example</h1>

{if !$finished}

{* start - form and preview *}
{if !$form->isSubmitted() || !$form->isValid()}

	{$form->startTag()|smarty:nodefaults}
	{$form->renderErrors()|smarty:nodefaults}
	
	<p><b>{$form->fields.username->displayName}:</b><br/>
	{$form->fields.username->render()|smarty:nodefaults}</p>
	
	<p><b>{$form->fields.email->displayName}:</b><br/>
	{$form->fields.email->render()|smarty:nodefaults}</p>
	
	<input type="submit" value=" Preview "/>
	{$form->endTag()|smarty:nodefaults}

{else} {* preview *}

	<p><b>{$form->fields.username->displayName}:</b><br/>
	{$form->fields.username->value}</p>
	
	<p><b>{$form->fields.email->displayName}:</b><br/>
	{$form->fields.email->value}</p>
	
	{$confirm->startTag()|smarty:nodefaults}
	<input type="button" value=" Back " onclick="location.href='{$basePath}{$resourcePath}'"/>
	<input type="submit" value=" Confirm "/>
	{$confirm->endTag()|smarty:nodefaults}

{/if}
{* end - form and preview *}

{else} {* $finished *}

	<p>Registration complete</p>

{/if}

</body>
</html>
