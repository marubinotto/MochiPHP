{include file='header.tpl'}

<title>Simple Blog</title>
</head>

<body>
<h1>Simple Blog</h1>

{include file='blog/post-form.tpl'}

{if $posts|smarty:nodefaults}
<div id="posts">
{foreach from=$posts->elements|smarty:nodefaults key=index item=post}
<div class="post">
	<div class="title">
		{if $post->title}
			<span style="margin-right: 15px;">{$post->title}</span>
		{/if}
		<span class="register-datetime">
			{$post->time('registerDatetime', 'Y/m/d H:i:s')}
		</span>
	</div>
	<div class="content">{$post->content|nl2br}</div>
</div>
{/foreach}
{include file='blog/pagination.tpl' paginated=$posts|smarty:nodefaults}
</div>
{/if}

</body>
</html>
