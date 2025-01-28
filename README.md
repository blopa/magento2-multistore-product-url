# Magento 2 Multi-Language Product URL Extension

## Overview

This Magento 2 extension allows users running a multi-language Magento 2 store to have different URLs for each product based on the selected language. This feature enhances SEO by providing search engines with language-specific URLs, improving visibility and user experience.

## Features

- Supports multiple languages for product URLs.
- Automatically generates language-specific URLs for products.
- Improves SEO by allowing search engines to index language-specific content.

## Installation

1. Clone the repository:

   ```bash
   git clone <repository-url>
   ```

2. Navigate to the Magento root directory:

   ```bash
   cd /path/to/magento2
   ```

3. Copy the extension files to the `app/code` directory:

   ```bash
   cp -R /path/to/magento2-multistore-product-url app/code/
   ```

4. Enable the module:

   ```bash
   php bin/magento module:enable Werules_MultistoreProductUrl
   ```

5. Run the setup upgrade command:

   ```bash
   php bin/magento setup:upgrade
   ```

6. Clear the cache:

   ```bash
   php bin/magento cache:clean
   ```

## Usage

After installation, the extension will automatically generate language-specific URLs for your products. Ensure that your Magento store is configured to support multiple languages.

## Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue for any bugs or feature requests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
