{foreach $posts post}
{partial 'posts/_post' array(post=$post)}
<hr />
{foreachelse}
<p>There are no posts.</p>
{/foreach}