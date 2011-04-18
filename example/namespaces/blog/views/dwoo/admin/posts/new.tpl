<h2>Add post</h2>

{$breeze->partial('admin/posts/_message')}
{$breeze->partial('admin/posts/_form' array(button='Create', back='/admin/posts'))}