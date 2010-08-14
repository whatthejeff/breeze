<h2>Edit post #<?php h($post['id']); ?></h2>

<?php echo partial('admin/posts/_message'); ?>
<?php echo partial('admin/posts/_form', array('button'=>'Update', 'back'=>p($post['id']))); ?>