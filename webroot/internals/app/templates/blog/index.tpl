{include file='header.tpl'}

<title>Simple Blog</title>
</head>

<body>
<h1>Simple Blog</h1>

{include file='blog/post-form.tpl'}

{if $posts|smarty:nodefaults}
<div id="posts">
{literal}
<script type="text/javascript">
//<![CDATA[
	function deletePost(id, button) {
		if (!window.confirm('Are you sure you want to delete this post?')) 
			return;
		jQuery.get("delete-post", {id: id});
		jQuery(button).closest("div.post").fadeOut("slow");
	}
//]]>
</script>
{/literal}
{foreach from=$posts->elements|smarty:nodefaults key=index item=post}
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
{/foreach}
{include file='blog/pagination.tpl' paginated=$posts|smarty:nodefaults}
</div>
{/if}

</body>
</html>
