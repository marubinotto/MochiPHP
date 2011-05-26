{include file='header.tpl'}

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
</ul>

Tools:
<ul>
	<li>
		<a href="database" target="_blank">Database Management</a>
		<span style="font-size: 10pt;">
			(can be configured via <code>app/config/settings.php</code>, <code>app/config/factory.php</code>)
		</span>
	</li>
</ul>

</div>

</body>
</html>

