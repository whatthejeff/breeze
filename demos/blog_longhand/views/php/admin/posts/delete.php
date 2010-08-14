<h2>Delete post #<?php $breeze->h($post['id']); ?></h2>

<form method="post" action="<?php echo $breeze->p($post['id']); ?>">
    <fieldset>
        <input type="hidden" name="_method" value="DELETE" id="_method">
        <p>
            Are you sure you want to delete: <strong><?php $breeze->h($post['title']); ?></strong>?
        </p>
        <p>
            <input type="submit" value="Yes"> or <a href="/admin/posts">no</a>
        </p>
    </fieldset>
</form>