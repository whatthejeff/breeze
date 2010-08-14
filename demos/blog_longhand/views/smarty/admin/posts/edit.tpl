<h2>Edit post #{$post.id}</h2>

{partial file='admin/posts/_message'}
{partial file='admin/posts/_form' button='Update' back=$breeze->p($post.id)}