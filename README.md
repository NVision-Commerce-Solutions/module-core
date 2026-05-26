# Commerce365 Module: Core

**Package:** `commerce365/module-core`
**Version:** 2.0.6
**License:** OSL-3.0

Shared foundation for all Commerce 365 for Magento extensions. Provides HTTP transport, authentication, session resolution, logging, sales document retrieval, and shared UI blocks. No standalone functionality is exposed to end users.

## Purpose

Acts as a service layer that all other Commerce365 modules depend on. Every HTTP call to the Commerce365 Hub API and every call to Business Central flows through this module.

## Key Services

### HTTP Transport (Hub API)
- `Service\Request\Get` - issues GET requests to the Commerce365 Hub API
- `Service\Request\Post` - issues POST requests to the Hub API
- `Service\Request\GetClient` - builds a Guzzle client authenticated with `appId` and `secretKey`
- `Service\Request\PostFactory` / `GetFactory` - selects the correct auth strategy at runtime

### Business Central Direct Authentication
- `Service\Request\BusinessCentral\BasicPost` - HTTP Basic auth POST to BC OData
- `Service\Request\BusinessCentral\OAuthPost` - OAuth 2.0 client credentials POST to BC OData
- `Service\Request\BusinessCentral\RefreshOAuthToken` - fetches a new token from Microsoft Identity
- `Service\Request\BusinessCentral\GetBCEndpointUrl` - builds the BC OData V4 endpoint URL

### Sales Documents
- `Service\GetSalesDocument` - fetch a single sales document via Hub `v2/SalesDocumentHistory/Get`
- `Service\GetSalesDocuments` - fetch a paginated list via `v2/SalesDocumentHistory/GetList`
- `Service\PrepareSalesRequestQuery` - injects `customerId`, `webOrdersOnly`, and `releasedOnly` into every query
- `Service\SalesDocumentInterface` - constants for BC table numbers (36, 110, 112, 114) and default page size

### Customer
- `Service\Customer\GetParentCustomer` - resolves a sub-account (contact) to its parent company customer
- `Service\Customer\GetParentCustomerId` - returns the parent customer ID for a given customer ID
- `Service\Customer\ParentResolveCustomerSession` - session extension that transparently returns the parent customer so that sub-accounts see the correct documents

### Product
- `Service\Product\GetParentProduct` - returns the configurable parent of a simple product (cached in-memory)

### Other
- `Service\Logger` - PSR-3 wrapper writing to `var/log/Commerce365.log`
- `Service\Module\Version` - reads the installed version of a Composer package

### Blocks
- `Block\AbstractList` - base block for all paginated list views (handles page, pageSize, search string, sorting)

### Plugins
- `Plugin\ConfigurableImageShare` - shares the configurable product image to its child products
- `Plugin\ConfigurableImageHelperShare` - shares via the image helper
- `Plugin\ConfigurableGalleryImagesShare` - shares gallery images

## Configuration

Configure via **Stores > Configuration > Commerce 365**:

- **Hub URL / App ID / Secret Key** - Commerce365 Hub connection (used by `GetClient`)
- **BC Endpoint / Environment / Company** - Business Central OData URL parts
- **Auth mode** - Hub, Basic, or OAuth
- **Tenant ID / Client ID / Client Secret** - for OAuth mode
- **Include Non-Web Orders** - controls `webOrdersOnly` query flag

## Logging

All errors are written to `var/log/Commerce365.log` via the `Commerce365Logger` virtual type (Monolog).

## Requirements

- `magento/framework`

## Further Reading

[Commerce 365 for Magento](https://n.vision/products/commerce-365-for-magento/)
