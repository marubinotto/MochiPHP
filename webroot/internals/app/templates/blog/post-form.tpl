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
	<input type="submit" value=" Preview "/>
</div>
{$form->endTag()|smarty:nodefaults}
</div>
