<?php

namespace Lch\MediaBundle;

final class LchMediaEvents
{
    const TRANSFORM = 'lch.media.transform';
    const REVERSE_TRANSFORM = 'lch.media.reverse_transform';
    const PRE_PERSIST = "lch.media.pre_persist";
    const POST_PERSIST = "lch.media.post_persist";
    const THUMBNAIL = "lch.media.thumbnail";
}
