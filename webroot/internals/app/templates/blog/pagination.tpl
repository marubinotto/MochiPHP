{if $paginated->getPageCount() > 1}
<div class="pagination">
{if !$paginated->isFirstPage()}
	<a href="{$basePath}{$resourcePath}">&lt;&lt;</a>&nbsp;
	<a href="{$basePath}{$resourcePath}?page={$paginated->getPageIndex()-1}">&lt;Previous</a>&nbsp;
{/if}
{foreach from=$paginated->getNeighborPageIndexes()|smarty:nodefaults item=pageIndex}
	{if $pageIndex == $paginated->getPageIndex()}
		&nbsp;{$pageIndex+1}&nbsp;&nbsp;
	{else}
		<a href="{$basePath}{$resourcePath}?page={$pageIndex}">{$pageIndex+1}</a>&nbsp;
	{/if}
{/foreach}
{if !$paginated->isLastPage()}
	<a href="{$basePath}{$resourcePath}?page={$paginated->getPageIndex()+1}">Next&gt;</a>&nbsp;
	<a href="{$basePath}{$resourcePath}?page={$paginated->getPageCount()-1}">&gt;&gt;</a>
{/if}
</div>
{/if}
