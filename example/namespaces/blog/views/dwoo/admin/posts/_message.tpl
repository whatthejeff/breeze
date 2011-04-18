{foreach array('warning','error','notice') level}
    {if $flash[$level]}
<div class="flash {$level}">
        {$flash[$level]}

        {if $level == 'error'}
        <ul>
            {foreach $flash.errors field error}
            <li>The {$field} field can not be blank.</li>
            {/foreach}
        </ul>
        {/if}
</div>
    {/if}
{/foreach}