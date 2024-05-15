insert into investimento_details (created_at, updated_at, investiment_at, total, invoice_code, sold_id, consumer_id)
select 
	created_at, updated_at, created_at, 
	CASE 
		WHEN (total-(ROUND(CAST(fees as numeric), 2) + TRUNC(CAST(fees_adm as numeric), 2))) < 100 
		THEN CAST(100 as double precision) 
		ELSE (total-(ROUND(CAST(fees as numeric), 2) + TRUNC(CAST(fees_adm as numeric), 2))) END AS total, 
	invoice_code, id, consumer_id from public.sales 
where business_id = (select CAST (value AS INTEGER) from public.configurations where id = 4)


SELECT 
	id, 
	created_at, 
	updated_at, 
	investiment_at, 
	('2019-01-30' - investiment_at) as dias, 
	total, 
	invoice_code, 
	sold_id, 
	consumer_id, 
	interest, 
	balance
	FROM public.investimento_details
where investiment_at < '2019-01-30' AND AGE('2019-01-30', investiment_at) >= '30 days' 
order by investiment_at desc					


insert into investimento_details (created_at, updated_at, investiment_at, total, invoice_code, sold_id, consumer_id)
select 
	created_at, updated_at, sold_at, 
	CASE 
		WHEN (total-(ROUND(CAST(fees as numeric), 2) + TRUNC(CAST(fees_adm as numeric), 2))) < 100 
		THEN CAST(100 as double precision) 
		ELSE (total-(ROUND(CAST(fees as numeric), 2) + TRUNC(CAST(fees_adm as numeric), 2))) END AS total, 
	invoice_code, id, consumer_id from public.sales 
where business_id = (select CAST (value AS INTEGER) from public.configurations where id = 4)