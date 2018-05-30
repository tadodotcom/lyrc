

## **L**yrc - Laravel YAML Routes Compiler
Made with ❤️ by tado°

### Installation Steps

1. Require the Package

After creating your new Laravel application you can include the Lyrc package with the following commands: 

```bash
composer require tadodotcom/lyrc
```

```bash
php artisan vendor:publish --provider="Tado\Lyrc\LyrcServiceProvider"
```

2. Add the `Tado\Lyrc\LyrcServiceProvider::class` to `config/app.php` file in providers list

3. Set your configurations in the file `config/lyrc.php`

### Generate routes from YAML file and using the generated file

1. After setting up your YAML file path in `config/lyrc` and the path for the compiled file, run command ```php artisan tado:lyrc```.

2. On your routes file ```require 'path/to/compiled_routes.php';```

And enjoy the simplicity of managing multi locale, big route files in your YAML files


