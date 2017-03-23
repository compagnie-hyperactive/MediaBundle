<?php

namespace Lch\MediaBundle;

final class LchMediaEvents
{
    const DOWNLOAD = 'lch.media.download';
    const TRANSFORM = 'lch.media.transform';
    const REVERSE_TRANSFORM = 'lch.media.reverse_transform';
    const PRE_PERSIST = 'lch.media.pre_persist';
    const POST_PERSIST = 'lch.media.post_persist';
    const STORAGE = 'lch.media.storage';
    const THUMBNAIL = 'lch.media.thumbnail';
    const LIST_ITEM = 'lch.media.list_item';
    const URL = 'lch.media.url';
}
