<h2>Post #<?php $breeze->h($post['id']); ?></h2>

<?php echo $breeze->partial('admin/posts/_message'); ?>

<p>
  <strong>Title:</strong>
  <?php $breeze->h($post['title']) ?>
</p>

<p>
  <strong>Contents:</strong>
  <?php $breeze->h($post['contents']) ?>
</p>

<p>
  <strong>Published:</strong>
  <?php $breeze->h($post['created_at']) ?>
</p>


<a href="<?php echo $breeze->p($post['id']); ?>/edit">Edit</a>
<a href="/admin/posts">Back</a>