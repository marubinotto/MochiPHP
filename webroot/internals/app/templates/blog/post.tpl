{include file='blog/_header.tpl'}

<title>{$post->title}</title>
</head>

<body>
<h1>Simple Blog</h1>

<div class="menu">
	<a href="index">[Home]</a>
</div>

{if $edit}
{include file='blog/_post-form.tpl'}
{else}
<div class="post">
	<div class="title">
		{if $post->title}
			<span class="title">{$post->title}</span>
		{/if}
		<span class="register-datetime">
			{$post->time('registerDatetime', 'Y/m/d H:i:s')}
		</span>
		<span class="tools">
			<a href="{$basePath}{$resourcePath}?id={$post->id}&amp;edit">edit</a>&nbsp;
			<a href="#" onclick="deletePost('{$post->id}', this); return false;">delete</a>
		</span>
	</div>
	<div class="content">{$post->content|nl2br}</div>
</div>
{/if}

</body>
</html>
