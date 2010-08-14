<h2>Delete post #{$post.id}</h2>

<form method="post" action="{$breeze->p($post.id)}">
    <fieldset>
        <input type="hidden" name="_method" value="DELETE" id="_method">
        <p>
            Are you sure you want to delete: <strong>{$post.title|escape:"htmlall":"UTF-8"}</strong>?
        </p>
        <p>
            <input type="submit" value="Yes"> or <a href="/admin/posts">no</a>
        </p>
    </fieldset>
</form>