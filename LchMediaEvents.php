<?php

namespace Lch\MediaBundle;

final class LchMediaEvents
{
    const DOWNLOAD          = 'lch.media.download';
    const LIST_ITEM         = 'lch.media.list_item';
    const PRE_DELETE        = 'lch.media.pre_delete';
    const PRE_PERSIST       = 'lch.media.pre_persist';
    const POST_DELETE       = 'lch.media.post_delete';
    const POST_PERSIST      = 'lch.media.post_persist';
    const REVERSE_TRANSFORM = 'lch.media.reverse_transform';
    const STORAGE           = 'lch.media.storage';
    const THUMBNAIL         = 'lch.media.thumbnail';
    const TRANSFORM         = 'lch.media.transform';
    const URL               = 'lch.media.url';
}
