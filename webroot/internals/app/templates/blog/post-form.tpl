<div class="post-form">
{$form->startTag()|smarty:nodefaults}
{$form->renderErrors()|smarty:nodefaults}
<div class="input-field">
	Title: <br/>
	{$form->fields.title->render()|smarty:nodefaults}
</div>
<div class="input-field">
	Content: <br/>
	{$form->fields.content->render()|smarty:nodefaults}
</div>
<div class="buttons">
	{$form->fields.preview->render()|smarty:nodefaults}
	{$form->fields.cancel->render()|smarty:nodefaults}
</div>
{$form->endTag()|smarty:nodefaults}
</div>

{* preview *}
{if $preview}
<div class="post post-preview">
<div class="title">
	<span style="color: gray;">[Preview]</span> {$form->fields.title->value}
</div>
<div class="content">
	{$form->fields.content->value|nl2br}
</div>
</div>
{/if}
