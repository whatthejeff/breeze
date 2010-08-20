<h2>Add post</h2>

<?php echo $breeze->partial('admin/posts/_message'); ?>
<?php echo $breeze->partial('admin/posts/_form', array('button'=>'Create', 'back'=>'/admin/posts')); ?>