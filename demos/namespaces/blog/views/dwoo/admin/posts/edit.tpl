<h2>Edit post #{$post.id}</h2>

{$breeze->partial('admin/posts/_message')}
{$breeze->partial('admin/posts/_form' array(button='Update', back=$breeze->p($post.id)))}