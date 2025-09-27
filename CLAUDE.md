# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

JBZoo/Utils is a PHP utility library providing a collection of functions, mini classes, and snippets for everyday PHP development. It contains 24 utility classes covering array manipulation, string operations, file system operations, date handling, email validation, image processing, and more.

### Key Architecture

- **Namespace**: All classes are under `JBZoo\Utils` namespace
- **PSR-4 Autoloading**: Source code in `src/` directory follows PSR-4 standard
- **Static Method Classes**: Each utility class (Arr, Str, FS, etc.) provides static methods for specific functionality
- **Function Aliases**: The `src/aliases.php` file provides convenient function aliases for common operations
- **Constants**: Global constants defined in `src/defines.php`

### Core Utility Classes

The library includes 24 main utility classes:
- **Arr**: Array manipulation and operations
- **Str**: String processing and manipulation
- **FS**: File system operations
- **Filter**: Data filtering and sanitization
- **Dates**: Date/time operations
- **Email**: Email validation and processing
- **Env**: Environment variable handling
- **Http**: HTTP header operations
- **Image**: Image processing utilities
- **Url**: URL manipulation
- **Xml**: XML processing
- **Cli**: Command-line interface utilities
- Plus 12 additional specialized classes (Csv, IP, Ser, Slug, Stats, Sys, Timer, Vars, PhpDocs, etc.)

## Development Commands

### Core Development Tasks
```bash
# Install/update dependencies
make update

# Run all tests
make test

# Run code style checks
make codestyle

# Run all project tests and quality checks
make test-all
```

### Testing
```bash
# Run PHPUnit tests
vendor/bin/phpunit

# Run PHPUnit with specific configuration
vendor/bin/phpunit --configuration phpunit.xml.dist

# Run tests for specific file
vendor/bin/phpunit tests/ArrayTest.php
```

### Code Quality
The project uses JBZoo Toolbox codestyle standards via Makefile targets:
- `make codestyle` - runs all code quality checks
- Code style configuration comes from `vendor/jbzoo/codestyle/src/init.Makefile`

### Build System
- **Makefile**: Main build configuration with standard targets
- **Dependencies**: Uses `jbzoo/toolbox-dev` for development dependencies
- **PHP Version**: Requires PHP 8.2+ (as of current version)

## Testing Architecture

### Test Structure
- **Location**: All tests in `tests/` directory
- **Naming**: Test files follow `*Test.php` pattern
- **Bootstrap**: Tests use `tests/autoload.php` for initialization
- **Coverage**: Configured for full source code coverage analysis

### Test Organization
- Each utility class has a corresponding test file (e.g., `Arr.php` → `ArrayTest.php`)
- Tests use PHPUnit framework with comprehensive coverage reporting
- Test isolation and cleanup handled via `revertServerVar()` function

### CI/CD Pipeline
- **GitHub Actions**: `.github/workflows/main.yml`
- **PHP Versions**: Tests run on PHP 8.2, 8.3, 8.4
- **Matrix Testing**: Tests with different composer flags (`--prefer-lowest`)
- **Coverage**: Uses Xdebug for coverage reporting
- **Quality Gates**: Separate jobs for PHPUnit tests, linters, and reports

## File Structure Patterns

### Source Code Organization
```
src/
├── aliases.php          # Function aliases for common operations
├── defines.php          # Global constants
├── Exception.php        # Base exception class
└── [UtilityClass].php   # Individual utility classes (Arr, Str, FS, etc.)
```

### Development Standards
- **PHP 8.2+**: Uses modern PHP features including typed properties and strict types
- **PSR Standards**: Follows PSR-4 autoloading and coding standards
- **Static Methods**: All utility functions implemented as static class methods
- **Immutable Operations**: Most operations return new values rather than modifying input

### Key Dependencies
- **Production**: Minimal dependencies (only PHP 8.2+)
- **Development**: `jbzoo/toolbox-dev` for testing and code quality tools
- **Optional**: `symfony/process` for CLI operations, `jbzoo/data` for data handling

## Working with the Codebase

### Adding New Utilities
1. Create new class in `src/` following existing patterns
2. Add corresponding test file in `tests/`
3. Update documentation if adding major functionality
4. Follow existing code style and static method patterns

### Common Development Patterns
- All utility classes follow similar structure with static methods
- Input validation and type checking handled consistently
- Error handling uses custom Exception class when needed
- Comprehensive test coverage expected for all new functionality