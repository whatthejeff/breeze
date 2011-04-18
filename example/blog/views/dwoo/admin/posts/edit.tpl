<h2>Edit post #{$post.id}</h2>

{partial 'admin/posts/_message'}
{partial 'admin/posts/_form' array(button='Update', back=p($post.id))}