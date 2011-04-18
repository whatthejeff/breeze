<h2>Post #<?php h($post['id']); ?></h2>

<?php echo partial('admin/posts/_message'); ?>

<p>
  <strong>Title:</strong>
  <?php h($post['title']) ?>
</p>

<p>
  <strong>Contents:</strong>
  <?php h($post['contents']) ?>
</p>

<p>
  <strong>Published:</strong>
  <?php h($post['created_at']) ?>
</p>


<a href="<?php echo p($post['id']); ?>/edit">Edit</a>
<a href="/admin/posts">Back</a>