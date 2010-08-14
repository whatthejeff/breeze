<h2><a href="/posts/<?php echo $post['id']; ?>"><?php $breeze->h($post['title']); ?></a></h2>

<p><?php echo strftime('%x', strtotime($post['created_at'])); ?></p>
<p><?php $breeze->h($post['contents']); ?></p>