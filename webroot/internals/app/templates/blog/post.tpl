{include file='blog/header.tpl'}

<title>{$post->title}</title>
</head>

<body>
<h1>Simple Blog</h1>

<div class="menu">
	<a href="index">[Home]</a>
</div>

<div class="post">
	<div class="title">
		{if $post->title}
			<span class="title">{$post->title}</span>
		{/if}
		<span class="register-datetime">
			{$post->time('registerDatetime', 'Y/m/d H:i:s')}
		</span>
		<span class="delete-button">
			<a href="#" onclick="deletePost('{$post->id}', this); return false;">delete</a>
		</span>
	</div>
	<div class="content">{$post->content|nl2br}</div>
</div>

</body>
</html>
