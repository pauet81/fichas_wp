# Comandos de verificacion rapida

## Ver fichas objetivo
```powershell
docker exec -i fichas-db-1 mariadb --default-character-set=utf8mb4 -uwp -pwp -D favnzzb_bk3c1 -e "SELECT ID, post_title, post_name FROM mzvw_posts WHERE post_type='ficha' AND ID BETWEEN 439 AND 456 ORDER BY ID;"
```

## Comprobar contenido HTML cargado
```powershell
docker exec -i fichas-db-1 mariadb --default-character-set=utf8mb4 -uwp -pwp -D favnzzb_bk3c1 -e "SELECT p.post_name, CHAR_LENGTH(pm.meta_value) AS html_len FROM mzvw_posts p JOIN mzvw_postmeta pm ON pm.post_id=p.ID AND pm.meta_key='contenido_ficha_html' WHERE p.ID BETWEEN 439 AND 456 ORDER BY p.ID;"
```

## Detectar posibles caracteres rotos
```powershell
docker exec -i fichas-db-1 mariadb --default-character-set=utf8mb4 -uwp -pwp -D favnzzb_bk3c1 -e "SELECT post_id, meta_key FROM mzvw_postmeta WHERE post_id BETWEEN 439 AND 456 AND meta_key IN ('edad_recomendada','objetivos','instrucciones','descripcion','_yoast_wpseo_title','_yoast_wpseo_metadesc') AND meta_value LIKE '%?%';"
```

## Ver metas SEO
```powershell
docker exec -i fichas-db-1 mariadb --default-character-set=utf8mb4 -uwp -pwp -D favnzzb_bk3c1 -e "SELECT p.post_name, CHAR_LENGTH(mt.meta_value) AS title_len, CHAR_LENGTH(md.meta_value) AS desc_len FROM mzvw_posts p LEFT JOIN mzvw_postmeta mt ON mt.post_id=p.ID AND mt.meta_key='_yoast_wpseo_title' LEFT JOIN mzvw_postmeta md ON md.post_id=p.ID AND md.meta_key='_yoast_wpseo_metadesc' WHERE p.ID BETWEEN 439 AND 456 ORDER BY p.ID;"
```
