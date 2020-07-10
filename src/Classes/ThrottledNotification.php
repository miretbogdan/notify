<?php

namespace Baronet\Notify\Classes;

interface ThrottledNotification
{
    public function throttleDecayMinutes(): int;

    public function throttleKeyId();
}
