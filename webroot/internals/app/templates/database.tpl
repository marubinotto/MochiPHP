{include file='header.tpl'}

<link type="text/css" rel="stylesheet" href="{$basePath}/assets/prettify/prettify.css"/>
<script type="text/javascript" src="{$basePath}/assets/js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="{$basePath}/assets/prettify/prettify.js"></script>
<style type="text/css">
{literal}
table th, table td {
	padding: 3px;
}
table th {
	text-align: left;
}
#env-info {
	font-size: 12px;
}
div.section {
	margin-top: 20px;
}
pre.code {
	padding: 20px;
	margin-left: 5px;
	margin-bottom: 15px;
	border: 1px solid #aaa;
	width: 80%;
	background-color: #ffffee;
}
pre.table-def {
	padding-top: 0px;
}
table.rows {
	font-size: 10px;
}
#column-names {
	font-size: 12px;
}
.accessors {
	font-size: 12px;
}
{/literal}
</style>
<title>Database Admin</title>
</head>

<body onload="prettyPrint()">

{* Database Info *}
<table id="env-info" border="1">
<tr>
	<th>PHP</th>
	<td>
		Version: {$phpVersion}<br/>
		Private access support: {if $database->supportsPrivateAccess()}true{else}false{/if}
	</td>
</tr>
<tr>
	<th>DSN</th>
	<td>{$database->dataSourceName}</td>
</tr>
<tr>
	<th>Tables</th>
	<td>
	{foreach from=$database->tableNames|smarty:nodefaults item=table name=tables}
		{$table}{if !$smarty.foreach.tables.last},{/if}
	{/foreach}	
	</td>
</tr>
<tr>
	<th>Classes</th>
	<td>
	{foreach from=$classes|smarty:nodefaults item=class name=classes}
		{if $class == $selectedClass}
			<b>{$class}</b>
		{else}
			<a href="database?class={$class}">{$class}</a>
		{/if}
	{/foreach}
	{$createTables->startTag()|smarty:nodefaults}
		<input type="submit" value=" Create Tables " 
			onclick="if (!window.confirm('Are you sure?')) return false;"/>
	{$createTables->endTag()|smarty:nodefaults}
	</td>
</tr>
</table>


{* Created Tables *}
{if $tableDefs}
<div class="section">
The following tables has been created:
{foreach from=$tableDefs|smarty:nodefaults item=tableDef}
<pre class="code table-def prettyprint lang-sql">
{$tableDef}
</pre>
{/foreach}
</div>
{/if}


{if $selectedClass}
<div class="section">
{$createTable->startTag()|smarty:nodefaults}
Class[<b>{$selectedClass}</b>] {if !$tableExists}(table not found){/if}
<input type="submit" value=" Create Table " 
	onclick="if (!window.confirm('Are you sure?')) return false;"/>
{$createTable->endTag()|smarty:nodefaults}

{* Rows - start *}
{if !is_null($rows|smarty:nodefaults)}
<table class="rows" border="1">
<tr>
	<th>ID</th>
	{foreach from=$instance->getPersistentPropertyNames() item=propertyName}
		<th>{$propertyName}</th>
	{/foreach}
</tr>
{foreach from=$rows|smarty:nodefaults key=index item=row}
<tr>
	<td>{$row->getId()}</td>
	{foreach from=$row->getPersistentPropertyNames() item=propertyName}
		<td>
		{if is_null($row->getPersistentPropertyValue($propertyName)|smarty:nodefaults)}
			<span style="color: silver;">NULL</span>
		{else}
			{$row->getPersistentPropertyValue($propertyName)}
		{/if}
		</td>
	{/foreach}
</tr>
{/foreach}
</table>
{/if}
{* Rows - end *}

<div class="section accessors">
The accessors code for this class: 
(<a href="#" onclick="jQuery('pre.accessors').toggle(); return false;">show/hide</a>)
<pre class="code accessors prettyprint lang-php" style="display: none;">
{$instance->generateAccessorsCode()}
</pre>
</div>

{if $columnNames}
<div class="section">
Table[<b>{$tableName}</b>]:
<div id="column-names">
{foreach from=$columnNames key=index item=columnName name=columns}
	{$columnName}{if !$smarty.foreach.columns.last},{/if}
{/foreach}
</div>
</div>
{/if}

</div>
{/if} {* selectedClass *}

</body>
</html>
