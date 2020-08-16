# PIMGento 2 API change Log

### 100.1.1 :
Add Magento 2.3 compatibility

Fix wrong encoding for de_DE.csv file

Fix family variant class

### 100.1.2 :
Fix connector behavior with Magento table prefix

Handle full path URL for products

Fix duplicated URL

Fix undefined offset 0 error on product import task

Fix duplicate SKU with product model

### 100.1.3 :
Fix Magento 2.3 compatibility

Fix configurable product creation with 2 axis

Fix documentation

Reformat translations

Fix localized product URL

### 100.1.4 :
Add PHP 7.2 compatibility

Fix error on product_model table

Add configuration to enable or disable product URL rewriting

### 100.2.1 :
Fix ACL for import jobs

Fix family exclusion configuration that was impossible to set empty

Fix configurable product association import

Fix product website association from channel mapping

Add configuration to set channel for admin values

Add configuration to choose to update product URL rewrite or not

Prevent price from being set to "null" if empty

Add no-whitespace validation to Akeneo API credential fields

### 100.2.2 :
Fix option import if first attribute has no valid options

Force attribute code to be lowercase

Manage attribute scope on import

Fix SQL error when no association column is found using advanced filter

### 100.2.3 :
Fix undefined offset on product import

### 100.2.4 :
Change default pagination value to 100

Change wording for advanced JSON configuration

Add SQL statement possibility in configurable mapping

Add category configurations for anchor, status and display in menu

### 100.2.5 :
Refactor API requests from configured mapping to improve performance and reduce data inserted in temporary table

### 100.2.6 :
Add proxy class for command

Fix product not imported it is the only one returned by API call

Display warning message when category or family is missing label

Fix error on format value for attribute price

Fetch Akeneo channels by API

Fix wrong step number in family import

Fix issue with family exclusion multiselect in the configuration

Display composer module version in the admin

Fix attribute code not lowercase causing issue on product import and simple product association

Add new options for updated filters

### 100.2.7 :
Fix ACL issues in Magento 2 admin

Add category tree filter

Add multi-import in shell

Add color in shell

Fix attribute group import error when code and name don't match