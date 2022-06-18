<?php

namespace Modules\AuthModule\User;

abstract class AccessLevels {
    const None = 0;
    const Read = 1;
    const ReadWrite = 2;
    const Admin = 3;
}
