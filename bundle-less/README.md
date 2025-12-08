# Bundle-less Structure

OroCommerce customizations can be organized inside Symfony bundles - useful when you want to reuse them across multiple projects - or implemented directly using a **bundle-less** directory structure for project-specific changes.

The examples here use the **bundle-less** approach and do not require creating a bundle.

Support for **bundle-less** directory structures was introduced in OroCommerce v5.1 to align with Symfony best practices and to make getting started easier for new developers.

**In short**, the **bundle-less** structure lets you place custom code directly within the application root directory, following Symfony's and Oro's conventional directory layout. Instead of creating a bundle to hold controllers, templates, configuration, or frontend assets, you organize these elements under the standard application folders (`config/`, `templates/`, `assets/`, `src/` when PHP classes are involved, etc.). OroCommerce automatically discovers and processes these locations, so no bundle scaffolding is required for typical project-level customizations.

This makes it possible to override templates, register JS or CSS assets, add configuration, or introduce new PHP code using the same structure that the application itself uses. The result is a simpler, more direct way to customize an OroCommerce project while staying aligned with modern Symfony best practices. Bundles remain useful when packaging reusable modules, but they are no longer necessary for ordinary in-project customizations.

For more details, see the [OroCommerce documentation](https://doc.oroinc.com/5.1/backend/architecture/bundle-less-structure/) on "bundle-less" customizations.
