{include file='blog/_header.tpl'}

<title>Simple Blog</title>
</head>

<body>
<h1>Simple Blog</h1>

{include file='blog/_post-form.tpl'}

{if $posts|smarty:nodefaults}
<div id="posts">
{foreach from=$posts->elements|smarty:nodefaults key=index item=post}
<div class="post">
	<div class="title">
		{if $post->title}
			<span class="title">{$post->title}</span>
		{/if}
		<span class="register-datetime">
			<a href="post?id={$post->id}">
			{$post->time('registerDatetime', 'Y/m/d H:i:s')}</a>
		</span>
		<span class="tools">
			<a href="#" onclick="deletePost('{$post->id}', this); return false;">delete</a>
		</span>
	</div>
	<div class="content">{$post->content|nl2br}</div>
</div>
{/foreach}
{include file='blog/_pagination.tpl' paginated=$posts|smarty:nodefaults}
</div>
{/if}

</body>
</html>
