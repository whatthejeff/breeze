<?php
foreach(array('warning','error','notice') as $level):
    if ($flash[$level]):
?>

<div class="flash <?php echo $level; ?>">
<?php echo $flash[$level]; ?>

<?php if ($flash['errors']): ?>
    <ul>
<?php foreach ($flash['errors'] as $key => $error): ?>
        <li><?php $breeze->h("The $key field can not be blank."); ?></li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>

</div>

<?php
    endif;
endforeach;
?>