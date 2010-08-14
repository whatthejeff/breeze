<?php if (!count($posts)): ?>
<p>There are no posts.</p>
<?php else: ?>

<?php foreach($posts as $post): ?>
    <?php echo $breeze->partial('posts/_post', array('post'=>$post)); ?>
    <hr />
<?php endforeach; ?>

<?php endif; ?>