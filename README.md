# Laravel ViewModel Generator

Laravel artisan generator for ViewModel classes (supports multiple models & collections)

# 1. Install package
```bash
composer require mdnayeemsarker/viewmodel-generator
```

# 2. Generate basic ViewModel
```bash
php artisan make:viewmodel PostViewModel
```

# 3. Generate single model ViewModel
```bash
php artisan make:viewmodel UserViewModel --model=User
```
```bash
php artisan make:viewmodel UserViewModel -m User
```

# 5. Generate multiple models ViewModel
```bash
php artisan make:viewmodel ReportViewModel --model=User,Post,Category
```
```bash
php artisan make:viewmodel ReportViewModel -m User,Post,Category
```

# 6. Generate single model as Collection
```bash
php artisan make:viewmodel UserCollectionViewModel --model=User --collection
```
```bash
php artisan make:viewmodel UserCollectionViewModel -m User -c
```

# 7. Generate multiple models as Collections
```bash
php artisan make:viewmodel AdminReportViewModel --model=User,Post --collection
```
```bash
php artisan make:viewmodel AdminReportViewModel -m User,Post -c
```

## Getting To Know MD NAYEEM SARKER

* Feel free to [learn more about MD NAYEEM SARKER](https://github.com/mdnayeemsarker).


## License

MIT © [MD NAYEEM SARKER][MIT License](LICENSE)
