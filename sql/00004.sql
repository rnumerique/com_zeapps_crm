ALTER TABLE `zeapps_invoices` ADD `id_origin` INT UNSIGNED NOT NULL AFTER `numerotation`;
ALTER TABLE `zeapps_deliveries` ADD `id_origin` INT UNSIGNED NOT NULL AFTER `numerotation`;
ALTER TABLE `zeapps_orders` ADD `id_origin` INT UNSIGNED NOT NULL AFTER `numerotation`;
ALTER TABLE `zeapps_quotes` ADD `id_origin` INT UNSIGNED NOT NULL AFTER `numerotation`;


CREATE VIEW zeapps_stocks as
select `zeapps_product_stocks`.`id` AS `id_stock`,
`zeapps_product_stocks`.`label` AS `label`,
`zeapps_product_stocks`.`ref` AS `ref`,
`zeapps_product_stocks`.`value_ht` AS `value_ht`,
`zeapps_warehouses`.`id` AS `id_warehouse`,
`zeapps_warehouses`.`label` AS `warehouse`,
`zeapps_warehouses`.`resupply_delay` AS `resupply_delay`,
`zeapps_warehouses`.`resupply_unit` AS `resupply_unit`,
sum(`zeapps_stock_movements`.`qty`) AS `total`,
`zeapps_product_stocks`.`deleted_at` AS `deleted_at` 
from ((`zeapps_product_stocks` join ``)
left join `zeapps_stock_movements` 
on(((`zeapps_product_stocks`.`id` = `zeapps_stock_movements`.`id_stock`) 
and (`zeapps_warehouses`.`id` = `zeapps_stock_movements`.`id_warehouse`)))) 
where (isnull(`zeapps_stock_movements`.`deleted_at`) 
and isnull(`zeapps_product_stocks`.`deleted_at`) 
and isnull(`zeapps_warehouses`.`deleted_at`)) 
group by `zeapps_warehouses`.`id`,`zeapps_product_stocks`.`id`


select  s.id as id_stock,
        s.ref as ref,
        s.label as label,
        s.value_ht as value_ht,
        w.resupply_delay as resupply_delay,
        w.resupply_unit as resupply_unit,
        sum(m.qty) as total,
        avg(if((m.date_mvt BETWEEN CURDATE() - INTERVAL 90 DAY AND CURDATE() + INTERVAL 1 DAY) and (m.qty < 0) , m.qty, null)) as average
from zeapps_product_stocks s
left join zeapps_stock_movements m
        on  s.id = m.id_stock
        and m.ignored = '0'
        and m.id_warehouse = 1
        and m.deleted_at is null
left join zeapps_warehouses w
        on w.id = 1
        and w.deleted_at is null
where   s.deleted_at is null
group by s.id
order by label
limit 15 offset 0
