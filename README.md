# PrestoMVC

Is a new lightweight framework, made for people who is starting on the world of MVC patterns and wants to create new applications.


supported php version > 5.6, 7, 8

Databases supports many drivers (mysql, sqlite, PostgreSQL, mssql, sybase, Oracle Call Interface -oci-)
            <div class="col-md-6">
                <div class="page-header">
                    <h2>Features</h2>
                </div>
                <ul>
                    <li>Lightweight Framework</li>
                    <li>Easy to install</li>
                    <li>Easy to use</li>
                    <li>Easy to extend</li>
                    <li>Events system</li>
                    <li>Templates for each view</li>
                    <li>Many and useful helpers</li>
                    <li>Easy to manage</li>
                    <li>Secure and fast</li>
                    <li>For newbies</li>
                    <li>And much more</li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="page-header">
                    <h2>Getting Started</h2>
                </div>
                <p>Well, you did cloned the framework but, you need to set some parameters.</p>
                <ol>
                    <li>Create a virtualhost (apache, nginx, etc)</li>
                    <li>Go to <code>/app/config/config.php</code> and set up your configurations</li>
                    <li>Go to <code>/app/config/database_config.php</code> and set up your connection to your database and his respective table.</li>
                    <li>Finally set up your routes on the file:<code>/app/Routes.php</code></li>
                    <li>Ready to go</li>
                </ol>
            </div>
Sorry no composer, batteries included.

## Testing

PrestoMVC includes a comprehensive test suite using PHPUnit:

### Running Tests

```bash
# From project root
cd tests
run-tests.bat  # Windows
# or manually:
php phpunit.phar --configuration=phpunit.xml
```

### Test Structure

```
tests/
├── phpunit.xml          # PHPUnit configuration
├── bootstrap.php        # Test bootstrap
├── TestCase.php         # Base test class
├── run-tests.bat        # Windows test runner
├── unit/               # Unit tests
│   ├── CacheTest.php
│   ├── ValidationTest.php
│   └── CsrfTest.php
└── integration/        # Integration tests
    └── DatabaseTest.php
```

### Test Coverage

- **Unit Tests**: Test individual components in isolation
- **Integration Tests**: Test component interactions
- **Coverage Reports**: Generated in `tests/reports/`

### Writing Tests

```php
<?php

use system\cache\FileCache;

class MyTest extends TestCase
{
    public function testSomething()
    {
        $cache = new FileCache();
        $cache->set('key', 'value');

        $this->assertEquals('value', $cache->get('key'));
    }
}
```
