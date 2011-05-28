{include file='_header.tpl'}

<title>MochiPHP</title>
</head>

<body>
<h1>
	MochiPHP 
	<span style="font-size: 12pt;">V{$version}</span>
	<span style="font-size: 10pt; font-weight: normal;">on PHP {$phpVersion}</span>
</h1>

<div style="margin-left: 10px;">

Demos:
<ul>
	<li><a href="hello" target="_blank">"Hello world"</a></li>
	<li><a href="form" target="_blank">Form Example</a></li>
	<li><a href="stateful-form" target="_blank">Stateful Form Example</a></li>
	<li>
		<a href="blog/" target="_blank">Simple Blog</a> 
		(CRUD for persistent objects)
		<ul>
			<li>You need to create a database tables with 
				<a href="database" target="_blank">Database Management</a></li>
		</ul>
	</li>
</ul>

Tools:
<ul>
	<li>
		<a href="database" target="_blank">Database Management</a>
		<ul>
			<li>Configure database settings: <code>app/config/settings.php</code></li>
			<li>Define getPersistentObjectClasses(): <code>app/config/factory.php</code></li>
		</ul>
	</li>
</ul>

</div>

</body>
</html>

