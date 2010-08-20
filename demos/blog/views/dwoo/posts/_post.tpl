<h2><a href="/posts/{$post.id}">{$post.title|escape:"htmlall":"UTF-8"}</a></h2>
<p>{$post.created_at|strtotime|date_format:"%x"}</p>
<p>{$post.contents|escape:"htmlall":"UTF-8"}</p>