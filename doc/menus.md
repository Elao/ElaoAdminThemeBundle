# Working with menus

## Main menu

```php
<?php

namespace App\Controller;

/**
 * @Route("/user", defaults={"_menu_root"="admin", "_menu_branch"="user"})
 */
class UserController extends AbstractController
{
}
```
