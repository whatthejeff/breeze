<h2>Post #{$post.id}</h2>

{partial file='admin/posts/_message'}

<p>
  <strong>Title:</strong>
  {$post.title|escape:"htmlall":"UTF-8"}
</p>

<p>
  <strong>Contents:</strong>
  {$post.contents|escape:"htmlall":"UTF-8"}
</p>

<p>
  <strong>Published:</strong>
  {$post.created_at|escape:"htmlall":"UTF-8"}
</p>


<a href="{$breeze->p($post.id)}/edit">Edit</a>
<a href="/admin/posts">Back</a>