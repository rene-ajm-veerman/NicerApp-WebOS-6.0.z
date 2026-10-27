<form style="height:calc(100% - 70px);">
<div id="tinymce_div">
<textarea id="tinymce3" class="tinymce" style="height:100%;"></textarea>
</div>
</form>
<div style="display:flex;align-items:center;gap:8px;padding:4px 0;">
<?php
global $naWebOS;
echo $naWebOS->html_vividButton (
    1001, 'position:relative;',

    'btnUploadPhotos',
    'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.c.onclick_btnUploadPhotos(event);',
                                 '',
                                 '',

                                 1001, 'Upload photos',

                                 null,
                                 null,
                                 'btnCssVividButton.greenBlue.png',
                                 'btnDocument2.png',   // swap for a camera icon if you have one

                                 '',

                                 'Upload photos',
                                 '', ''
);
?>
<span id="naCommentMediaBadge" style="display:none;color:#8f8;font-size:0.9em;text-shadow:1px 1px 2px rgba(0,0,0,0.7);"></span>
<?php
echo $naWebOS->html_vividButton (
    1001, 'position:relative;',

    'btnPostComment',
    'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.c.onclick_btnPostComment(event);',
                                 '',
                                 '',

                                 1001, 'Add comment',

                                 null,
                                 null,
                                 'btnCssVividButton.orange1c.png',
                                 'btnDocument2.png',

                                 '',

                                 'Add comment',
                                 '', ''
);
?>
</div>
