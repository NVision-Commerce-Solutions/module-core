# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.6] - 2024-01-01
### Changed
- Maintenance release

## [2.0.0] - 2023-07-31
### Added
- OAuth 2.0 client credentials flow support for Business Central authentication (`OAuthPost`, `RefreshOAuthToken`)
- `PostFactory` and `GetFactory` to select authentication strategy at runtime
- `GetParentCustomerId` service
- `GetParentProduct` service with in-memory cache for configurable parent lookup
- `ParentResolveCustomerSession` to transparently resolve sub-account (contact) sessions to parent customer for sales document queries
- `AdditionalConfig` model for advanced Business Central configuration
- Dedicated `Commerce365Logger` writing to `var/log/Commerce365.log`

### Changed
- Migrated HTTP transport layer to Guzzle
- `GetSalesDocument` and `GetSalesDocuments` now route through `PrepareSalesRequestQuery`

## [1.0.0] - 2023-02-11
### Added
- Initial release
- Basic authentication support for Business Central OData API
- `GetSalesDocument` and `GetSalesDocuments` services
- `SalesDocumentInterface` table number constants (36, 110, 112, 114)
- `AbstractList` block with pagination, sorting, and search helpers
- Configurable product image sharing plugins (`ConfigurableImageShare`, `ConfigurableImageHelperShare`, `ConfigurableGalleryImagesShare`)
- `Module\Version` service for reading installed package versions
- Hyvä config registration observer
