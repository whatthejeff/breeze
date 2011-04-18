<h2>Posts list</h2>

{$breeze->partial('admin/posts/_message')}

{if (!count($posts))}
<p>There are no posts.  Would you like to <a href="/admin/posts/new">add some</a>?</p>
{else}
<table>
    <tr>
        <th>Title</th>
        <th>Contents</th>
        <th>Published</th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
{foreach $posts post}
    <tr>
        <td>{$post.title|escape:"htmlall":"UTF-8"}</td>
        <td>{$post.contents|escape:"htmlall":"UTF-8"}</td>
        <td>{$post.created_at|escape:"htmlall":"UTF-8"}</td>
        <td><a href="{$breeze->p($post.id)}">Show</a></td>
        <td><a href="{$breeze->p($post.id)}/edit">Edit</a></td>
        <td><a href="{$breeze->p($post.id)}/delete">Delete</a></td>
    </tr>
{/foreach}
{/if}
</table>
<br />
<a href="/admin/posts/new">Add post</a>