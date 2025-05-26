## Required Changes to the Original Code

The code provided required several modifications to function correctly and meet modern development standards.

### Installation and Compatibility Fixes

1. In the file `Security/CASAuthenticator.php` of the `ecphp/cas-bundle 2.3` package, line 50 contains a function call with a trailing comma in the argument list. While this is valid in PHP 8.X, it causes a parser error in PHP 7.X.
   Since upgrading to PHP 8.X is not viable due to compatibility issues with other dependencies (e.g., `league/uri-query-parser`), this line must be manually corrected after running `composer install --no-dev`.

2. The dependency `"nyholm/psr7": "^1.8"` had to be manually added to `composer.json` to resolve missing interface issues.

3. To enable internal login (bypassing CAS), the installation manual instructs running a SQL query to create an administrator user. However, the query was incorrect. The correct one is:

   ```sql
   INSERT INTO user
   (id, email, password, roles, user_id, is_active, created_at, updated_at)
   VALUES
   (1, 'admin@intef.es', '$2y$13$iarvwGmJjLbsnY3yZEADz.jfxh7y0FEzHctpRZvK3ymBIRhn4Cxju', '', 'admin', 1, NOW(), NOW());
   ```

   The original version was missing the `user_id` field.

4. The following missing translations were added to the file `translations/en/messages.en.po`:

   ```po
   msgid "app.name"
   msgstr "eXeLearning"

   msgid "security.login.logged.in.as"
   msgstr "Logged as"

   msgid "security.login.logout"
   msgstr "Close session"

   msgid "security.login.welcome"
   msgstr "Welcome"

   msgid "security.login.app"
   msgstr "eXeLearning"

   msgid "security.login.start"
   msgstr "Start"

   msgid "security.login.email"
   msgstr "Email"

   msgid "security.login.password"
   msgstr "Password"

   msgid "security.login.submit"
   msgstr "Submit"
   ```

---

## Technical Improvements and Refactoring

In addition to the corrections above, the following major improvements were made:

* Switched the translation system from `.po` files to Symfony-native **XLIFF** format.
* The entire application was **dockerized** for consistency across environments.
* **Offline installers** were generated using Electron and nativePHP.
* Codebase was **linted and standardized** according to Symfony coding standards.
* **Executable generation** scripts were added for desktop distribution.
* The application now supports installation in a **subdirectory** (not just root domain).
* Authentication system was extended and fixed to support:

  * **CAS**
  * **Guest mode**
  * **OpenID Connect**
  * **Standard email/password login**
* Legacy and unused libraries were removed to reduce bloat and potential conflicts.
* **Continuous Integration pipelines** were added to:

  * Validate code quality
  * Run linters
  * Execute tests
* A **Makefile** was included to streamline development commands and environment setup.
* Fixes were applied to:

  * **File import system**
  * **Legacy `.elp` support**
* User interface improvements for better **usability and accessibility**.
* Enhanced **real-time features** using [Mercure](https://mercure.rocks/) instead of inefficient polling.
* **Performance and caching improvements** were implemented throughout the application.
* Added CLI commands to simplify **resource management**.
* Database abstraction allows using:

  * **MySQL**
  * **PostgreSQL**
  * **SQLite**
* Major improvements to **string handling**, **UI simplification**, and **workflow stability**.
* Added unit, integration, and end-to-end **automated tests** to improve reliability and development workflow.

