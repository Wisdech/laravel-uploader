# 富文本编辑器文件上传 Laravel 实现

## 安装使用

前端组件
```bash
npm install @wisdech/react-editor
```

Laravel项目中

```bash
#添加依赖
composer require wisdech/laravel-uploader

#发布迁移文件
php artisan vendor:publish --tag=uploader-migrations

#运行迁移
php artisan migrate
```

在路由文件中
```php

use Wisdech\Uploader\Http\Controllers\UploaderController;
...

Route::post('uploader', [UploaderController::class, 'store'])->name('uploader.store');

```

在Editor组件中
```typescript jsx

import { Editor } from '@wisdech/react-editor';

export default function(){

    return (
        <Editor
            uploader={route('uploader.store') // 使用Ziggy route() 或路径 '/uploader'}
        />
    )
}
```
