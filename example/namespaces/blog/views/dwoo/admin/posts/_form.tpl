<form method="post" action="{$back|escape:"htmlall":"UTF-8"}">
    <fieldset>

{if !empty($post)}
        <input type="hidden" name="_method" value="PUT" id="_method">
{/if}

{if isset($flash.post)}
    {assign $flash.post post}
{/if}

        <p{if isset($flash.errors.title)} class="error"{/if}>
            <label for="post_title">Title</label>
            <input type="text" name="post[title]" value="{$post.title|escape:"htmlall":"UTF-8"}" id="post_title">
        </p>

        <p{if isset($flash.errors.contents)} class="error"{/if}>
            <label for="post_contents">Contents</label>
            <textarea name="post[contents]" id="post_contents" rows="8" cols="40">{$post.contents|escape:"htmlall":"UTF-8"}</textarea>
        </p>

        <p>
            <input type="submit" value="{$button|escape:"htmlall":"UTF-8"}">
            or <a href="{$back|escape:"htmlall":"UTF-8"}">cancel</a>
        </p>

    </fieldset>
</form>