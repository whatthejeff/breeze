{foreach from=$flash->asArray() item=message key=level}
{if in_array($level, array('warning','error','notice'))}

<div class="flash {$level}">
    {$message}

    {if $level == 'error'}
    <ul>
        {foreach from=$flash.errors item=error key=field}
        <li>The {$field} field can not be blank.</li>
        {/foreach}
    </ul>
    {/if}
</div>

{/if}
{/foreach}