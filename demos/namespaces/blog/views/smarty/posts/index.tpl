{foreach from=$posts item=post}
{partial file='posts/_post' post=$post}
<hr />
{foreachelse}
<p>There are no posts.</p>
{/foreach}