ALTER TABLE `sales_flat_order` DROP `extra_fee`;
ALTER TABLE `sales_flat_invoice` DROP `extra_fee`;
ALTER TABLE `sales_flat_quote_item` DROP `unit_extra_fee`;
ALTER TABLE `sales_flat_order_item` DROP `unit_extra_fee`;
ALTER TABLE `sales_flat_invoice_item` DROP `unit_extra_fee`;
ALTER TABLE `sales_flat_creditmemo_item` DROP `unit_extra_fee`;