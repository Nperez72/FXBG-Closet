# Contributing to FXBG-Closet

Thank you for your interest in contributing to FXBG-Closet! This document provides guidelines for contributing to the project.

## Getting Started

1. Fork the repository
2. Clone your fork locally
3. Set up the commit message template (optional but recommended):
   ```bash
   git config commit.template .gitmessage
   ```
4. Create a new branch for your feature or bug fix
5. Make your changes following conventional commits
6. Test your changes thoroughly
7. Submit a pull request

## Commit Message Guidelines

We use [Conventional Commits](https://www.conventionalcommits.org/) to maintain a clean and readable commit history. This also enables automated versioning and changelog generation.

### Format
```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

### Types
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation only changes
- `style`: Changes that do not affect the meaning of the code (formatting, etc.)
- `refactor`: A code change that neither fixes a bug nor adds a feature
- `perf`: A code change that improves performance
- `test`: Adding missing tests or correcting existing tests
- `build`: Changes that affect the build system or external dependencies
- `ci`: Changes to our CI configuration files and scripts
- `chore`: Other changes that don't modify src or test files
- `revert`: Reverts a previous commit

### Examples
- `feat: add user profile page`
- `fix(auth): resolve login validation issue`
- `docs: update installation instructions`
- `style: fix indentation in header component`
- `refactor(database): simplify connection logic`

### Rules
- Use the imperative mood in the subject line ("add" not "added")
- Don't end the subject line with a period
- Keep the subject line under 100 characters
- Use the body to explain what and why vs. how

## Development Guidelines

### Code Style

- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions small and focused

### Database

- Always use prepared statements for database queries
- Follow existing naming conventions
- Document any schema changes

### Security

- Sanitize all user inputs
- Use proper authentication and authorization
- Never commit sensitive information (passwords, API keys, etc.)

### Testing

- Test your changes in multiple browsers if UI-related
- Verify database operations work correctly
- Test edge cases and error conditions

## Pull Request Process

1. Update documentation if needed
2. Ensure your code passes all checks
3. Fill out the pull request template completely
4. Request review from maintainers
5. Address any feedback promptly

## Reporting Issues

- Use the appropriate issue template
- Provide clear reproduction steps
- Include relevant screenshots or error messages
- Be respectful and constructive

## Questions?

Feel free to open an issue for questions about contributing or reach out to the maintainers.
