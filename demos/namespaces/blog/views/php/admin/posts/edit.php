<h2>Edit post #<?php $breeze->h($post['id']); ?></h2>

<?php echo $breeze->partial('admin/posts/_message'); ?>
<?php echo $breeze->partial('admin/posts/_form', array('button'=>'Update', 'back'=>$breeze->p($post['id']))); ?>