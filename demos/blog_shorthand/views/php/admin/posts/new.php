<h2>Add post</h2>

<?php echo partial('admin/posts/_message'); ?>
<?php echo partial('admin/posts/_form', array('button'=>'Create', 'back'=>'/admin/posts')); ?>