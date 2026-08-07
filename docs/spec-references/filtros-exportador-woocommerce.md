# Filtros para el exportador de productos de WooCommerce

Documento de contexto del proyecto. Todo lo que hay aquí está verificado contra el
código fuente de **WooCommerce 11.0.0** (no contra documentación ni memoria).

---

## 1. Objetivo

Añadir opciones de filtrado al exportador CSV de productos que trae WooCommerce de
serie, **extendiéndolo mediante hooks**, sin reemplazarlo ni duplicar su lógica de
lotes, columnas ni descarga.

El primer filtro es por fecha:

- Un selector con tres opciones: *Todas las fechas*, *Fecha de creación*,
  *Fecha de última modificación*.
- Al elegir una de las dos fechas, se muestran dos selectores: fecha inicio y
  fecha final.
- Ambas pueden ser la misma fecha, porque interesa poder exportar un único día.

El plugin está pensado para crecer con más filtros de forma incremental.

**Nombre:** pendiente de decidir.

---

## 2. Cómo funciona el exportador nativo

Vale la pena tenerlo claro porque condiciona todo el diseño.

### Ficheros implicados

| Fichero | Qué hace |
|---|---|
| `includes/admin/class-wc-admin-exporters.php` | Registra la página, encola el JS, atiende el AJAX |
| `includes/admin/views/html-admin-page-product-export.php` | El formulario |
| `assets/js/admin/wc-product-export.js` | Envía los lotes por AJAX de forma recursiva |
| `includes/export/class-wc-product-csv-exporter.php` | Construye la query y genera las filas |
| `includes/export/abstract-wc-csv-batch-exporter.php` | Escritura en fichero y control de lotes |
| `includes/export/abstract-wc-csv-exporter.php` | Delimitador, nombre de fichero, `fputcsv` |

### El flujo

1. El usuario envía el formulario. El JS hace `$( this ).serialize()` y guarda ese
   string en una variable local `data`.
2. `processStep()` lanza una petición AJAX a `woocommerce_do_ajax_product_export`
   con un payload que incluye `form: data` más las claves que el JS lee a mano de
   los `<select>` (`selected_columns`, `export_meta`, `export_types`,
   `export_category`, `export_product_ids`, `filename`, `security`).
3. El PHP genera un lote, responde con el porcentaje, y el JS vuelve a llamar a
   `processStep()` **con el mismo `data` de siempre**, hasta llegar al 100%.
4. Al terminar, redirige a la URL de descarga.

### El detalle clave

Los campos que WooCommerce trae de serie (`#woocommerce-exporter-columns`,
`-types`, `-category`, `-meta`) **no tienen atributo `name`**. Por eso el JS los
lee explícitamente y monta el payload a mano.

Pero **el formulario sí se serializa entero y se manda como `form`**, y
WooCommerce nunca lo lee. Esto significa:

- Un campo propio con `name` viaja gratis hasta el servidor, sin tocar el JS.
- Como `data` se captura una vez en el `submit` y se reenvía idéntico en cada
  iteración, el valor **persiste entre lotes automáticamente**. No hace falta
  transient ni sesión.

Se recupera con `parse_str( wp_unslash( $_POST['form'] ), $form )`.

---

## 3. Puntos de extensión

### En la interfaz

`do_action( 'woocommerce_product_export_row' )`
— `html-admin-page-product-export.php:138`

Está justo antes de `</tbody>`, **fuera** del `if ( ! $is_exporting_product_ids )`,
así que se ejecuta siempre. Hay que devolver `<tr>`.

### En la query

`apply_filters( "woocommerce_product_export_{$this->export_type}_query_args", $args )`
— `class-wc-product-csv-exporter.php:220`

En la práctica `woocommerce_product_export_product_query_args`. Existe desde la
3.5.0. Filtra los argumentos que van a `wc_get_products()`. **Este es el punto de
entrada de todos los filtros.**

Los `$args` que llegan por defecto:

```php
array(
    'status'   => array( private, publish, draft, future, pending ),
    'limit'    => $this->get_limit(),
    'page'     => $this->get_page(),
    'orderby'  => array( 'ID' => 'ASC' ),
    'return'   => 'objects',
    'paginate' => true,
    'type'     => $this->product_types_to_export,   // si no hay IDs concretos
    'category' => $this->product_category_to_export, // si se eligió categoría
)
```

### Otros filtros útiles

| Hook | Para qué |
|---|---|
| `woocommerce_product_export_row_data` | Modificar una fila ya generada |
| `woocommerce_product_export_skip_product_row` | Descartar filas (ver aviso abajo) |
| `woocommerce_product_export_product_column_{$id}` | Rellenar una columna propia |
| `woocommerce_product_export_column_names` | Añadir columnas |
| `woocommerce_product_export_delimiter` | Cambiar el separador del CSV |
| `woocommerce_product_export_batch_limit` | Tamaño del lote |
| `woocommerce_exporter_product_types` | Tipos de producto exportables |
| `woocommerce_product_export_skip_meta_keys` | Excluir metas del volcado |

**Aviso sobre `skip_product_row`:** filtrar después de la query rompe el cálculo
del porcentaje, porque `total_rows` viene de la query original. Filtrar siempre en
`query_args`.

---

## 4. Trampas verificadas

### 4.1 Poner `date_created` o `date_modified` borra todo el `meta_query`

`class-wc-product-data-store-cpt.php:2405-2423`:

```php
foreach ( $date_queries as $query_var_key => $db_key ) {
    if ( isset( $query_vars[ $query_var_key ] ) && '' !== $query_vars[ $query_var_key ] ) {
        $existing_queries = wp_list_pluck( $wp_query_args['meta_query'], 'key', true );
        foreach ( $existing_queries as $query_index => $query_contents ) {
            unset( $wp_query_args['meta_query'][ $query_index ] );
        }
        $wp_query_args = $this->parse_date_for_wp_query( ... );
    }
}
```

El comentario dice que es para evitar conflictos con las mismas keys, pero
**desindexa todas**, no solo las conflictivas. A esas alturas el `meta_query` ya
lleva lo autogenerado: `stock_status`, `_sku`, `total_sales`, precios, peso,
dimensiones, `manage_stock`...

**Consecuencia práctica:** "modificados en marzo + sin stock" devolvería todos los
modificados en marzo, con stock y sin él, en silencio.

**Solución.** En `class-wc-data-store-wp.php:310` el comentario dice *"Other vars
get mapped to wp_query args or just left alone"*: las claves desconocidas pasan
tal cual a `WP_Query`. Así que se construye el `date_query` a mano y no se toca
`date_created` / `date_modified`:

```php
$args['date_query'][] = array(
    'column'    => 'post_modified',   // o 'post_date'
    'after'     => array( 'year' => 2026, 'month' => 3, 'day' => 1 ),
    'before'    => array( 'year' => 2026, 'month' => 3, 'day' => 31 ),
    'inclusive' => true,
);
```

Es exactamente lo que genera `parse_date_for_wp_query()` para precisión de día,
pero saltándose el bloque que borra el `meta_query`.

**Decisión de arquitectura: en cuanto se añada el segundo filtro, migrar a
`date_query` manual.** Mientras el de fechas sea el único filtro, `date_created`
funciona bien y es más legible.

### 4.2 Las variaciones esquivan el filtro

`class-wc-product-csv-exporter.php`, en `prepare_data_to_export()`: si `$args['include']`
o `$args['category']` están puestos, los productos variables detectados se
resuelven con una **segunda query**:

```php
$products = wc_get_products( array(
    'parent' => $parent_id,
    'type'   => array( ProductType::VARIATION ),
    'return' => 'objects',
    'limit'  => -1,
) );
```

Esa query **no pasa por `woocommerce_product_export_product_query_args`**. Con solo
el filtro de fecha da igual (esa rama no se activa), pero si el usuario además
elige categoría, las variaciones salen todas sin importar la fecha. Además
`total_rows` sale solo de la query principal, así que el porcentaje se descuadra.
Es comportamiento propio de Woo.

### 4.3 Formato y precisión de las fechas

`parse_date_for_wp_query()` — `class-wc-data-store-wp.php:342`

```php
$query_parse_regex = '/([^.<>]*)(>=|<=|>|<|\.\.\.)([^.<>]+)/';
$valid_operators   = array( '>', '>=', '=', '<=', '<', '...' );
```

- El regex **no admite puntos**: `01.02.2026` no funciona. Solo `YYYY-MM-DD`.
- Con `YYYY-MM-DD` la precisión es de día y compara contra `post_date` /
  `post_modified`, es decir **hora local del sitio**, no las columnas `_gmt`. Es
  lo que espera el usuario que mira la columna "Fecha" del listado de productos.
- Con timestamp la precisión pasa a segundo y usa las columnas `_gmt`.
- Con `...` y precisión de día genera `after` y `before` en formato array con
  `inclusive => true` (líneas 402-412, con referencia al ticket
  [core #29908](https://core.trac.wordpress.org/ticket/29908)). Por eso el mismo
  día en ambos extremos funciona.

### 4.4 No exponer `orderby`

El exportador fija `orderby => array( 'ID' => 'ASC' )` y el paginado por lotes
depende de que ese orden sea estable. Cambiarlo puede duplicar o saltar filas
entre lotes.

---

## 5. Creación vs. modificación

Son dos columnas distintas de `wp_posts`. Mapeo en
`class-wc-product-data-store-cpt.php:2406`:

```php
$date_queries = array(
    'date_created'      => 'post_date',
    'date_modified'     => 'post_modified',
    'date_on_sale_from' => '_sale_price_dates_from',
    'date_on_sale_to'   => '_sale_price_dates_to',
);
```

Matiz útil: la bajada de stock al vender se hace con `UPDATE {$wpdb->postmeta}`
directo (`update_product_stock()`, línea 1782), así que **por sí sola no toca
`post_modified`**. `date_modified` refleja ediciones reales del producto, no
ventas.

---

## 6. Código actual

Funcional y probado contra 11.0.0. Falta renombrar prefijos cuando se decida el
nombre del plugin.

```php
<?php
/**
 * Filtro por fecha para el exportador de productos de WooCommerce.
 */

defined( 'ABSPATH' ) || exit;

class JC_Product_Export_Date_Filter {

	const FIELD_TYPE = 'jc_date_field';
	const FIELD_FROM = 'jc_date_from';
	const FIELD_TO   = 'jc_date_to';

	public static function init() {
		add_action( 'woocommerce_product_export_row', array( __CLASS__, 'render_rows' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ), 20 );
		add_filter( 'woocommerce_product_export_product_query_args', array( __CLASS__, 'query_args' ) );
	}

	/**
	 * Filas del formulario. El hook está dentro del <tbody>, así que hay que
	 * devolver <tr>. Los inputs llevan name: el JS de Woo serializa el
	 * formulario y lo envía como $_POST['form'] en cada paso del lote.
	 */
	public static function render_rows() {
		?>
		<tr>
			<th scope="row">
				<label for="jc-export-date-field"><?php esc_html_e( 'Filtrar por fecha', 'jc' ); ?></label>
			</th>
			<td>
				<select id="jc-export-date-field" name="<?php echo esc_attr( self::FIELD_TYPE ); ?>" style="width:100%;max-width:400px;">
					<option value=""><?php esc_html_e( 'Todas las fechas', 'jc' ); ?></option>
					<option value="date_created"><?php esc_html_e( 'Fecha de creación', 'jc' ); ?></option>
					<option value="date_modified"><?php esc_html_e( 'Fecha de última modificación', 'jc' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="jc-export-date-range" style="display:none;">
			<th scope="row">
				<label for="jc-export-date-from"><?php esc_html_e( 'Rango de fechas', 'jc' ); ?></label>
			</th>
			<td>
				<input type="date" id="jc-export-date-from" name="<?php echo esc_attr( self::FIELD_FROM ); ?>" />
				<span aria-hidden="true">&mdash;</span>
				<input type="date" id="jc-export-date-to" name="<?php echo esc_attr( self::FIELD_TO ); ?>" />
				<p class="description">
					<?php esc_html_e( 'Ambos extremos se incluyen. Pon la misma fecha en los dos campos para exportar un único día. Si dejas uno vacío, el rango queda abierto por ese lado.', 'jc' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Solo mostrar/ocultar el rango. Prioridad 20: WC registra
	 * 'wc-product-export' en admin_enqueue_scripts con prioridad 10.
	 */
	public static function enqueue( $hook ) {
		if ( 'product_page_product_exporter' !== $hook ) {
			return;
		}

		wp_add_inline_script(
			'wc-product-export',
			"jQuery( function ( $ ) {
				var \$field = $( '#jc-export-date-field' ),
					\$range = $( '.jc-export-date-range' );

				function jcToggle() {
					\$range.toggle( '' !== \$field.val() );
				}

				\$field.on( 'change', jcToggle );
				jcToggle();
			} );"
		);
	}

	/**
	 * Traduce la selección a un query var de wc_get_products().
	 */
	public static function query_args( $args ) {

		if ( ! wp_doing_ajax() || empty( $_POST['form'] ) ) {
			return $args;
		}

		// check_ajax_referer() ya corrió en do_ajax_product_export(), pero el
		// filtro puede dispararse desde otros contextos. No damos nada por hecho.
		if ( empty( $_POST['security'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'wc-product-export' ) ) {
			return $args;
		}

		parse_str( wp_unslash( $_POST['form'] ), $form );

		$field = isset( $form[ self::FIELD_TYPE ] ) ? sanitize_key( $form[ self::FIELD_TYPE ] ) : '';

		if ( ! in_array( $field, array( 'date_created', 'date_modified' ), true ) ) {
			return $args;
		}

		$from = self::clean_date( isset( $form[ self::FIELD_FROM ] ) ? $form[ self::FIELD_FROM ] : '' );
		$to   = self::clean_date( isset( $form[ self::FIELD_TO ] ) ? $form[ self::FIELD_TO ] : '' );

		if ( ! $from && ! $to ) {
			return $args;
		}

		// Si las meten al revés, ordenamos en vez de devolver cero resultados.
		if ( $from && $to && $from > $to ) {
			list( $from, $to ) = array( $to, $from );
		}

		if ( $from && $to ) {
			$args[ $field ] = $from . '...' . $to;
		} elseif ( $from ) {
			$args[ $field ] = '>=' . $from;
		} else {
			$args[ $field ] = '<=' . $to;
		}

		return $args;
	}

	/**
	 * Valida YYYY-MM-DD y que la fecha exista de verdad.
	 * El regex de parse_date_for_wp_query() no admite puntos, así que
	 * cualquier otro formato reventaría en silencio.
	 */
	private static function clean_date( $value ) {
		$value = sanitize_text_field( $value );

		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m ) ) {
			return '';
		}

		return checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ? $value : '';
	}
}

add_action( 'plugins_loaded', array( 'JC_Product_Export_Date_Filter', 'init' ) );
```

Notas de implementación:

- El `<select>` va sin `wc-enhanced-select` a propósito. Select2 funcionaría
  (mantiene el `<select>` original, así que `serialize()` lo recoge), pero para
  tres opciones no compensa y evitas que su evento `change` se cruce con el
  listener de `.woocommerce-exporter-types`.
- El hook suffix de la pantalla es `product_page_product_exporter`. Lo confirma
  `menu_highlight_for_product_export()`, que comprueba ese mismo `$screen->id`.
- Prioridad 20 en `admin_enqueue_scripts` porque WC registra el handle en la 10.

---

## 7. Ideas de filtros a añadir

### Casi gratis (ya son query var nativa de `wc_get_products()`)

Todas están en `WC_Product_Query::get_default_query_vars()`:

| Filtro | Query var | Nota |
|---|---|---|
| Etiquetas | `tag` | La ausencia más llamativa: hay categoría pero no etiqueta |
| Estado de publicación | `status` | Ahora está fijo; no se puede exportar solo borradores |
| Estado de stock | `stock_status` | |
| Clase de envío | `shipping_class` | |
| Destacados | `featured` | |
| Visibilidad en catálogo | `visibility` | |
| Rango de precio | `price`, `regular_price`, `sale_price` | Mismos operadores que las fechas |
| Stock por debajo de X | `stock_quantity` con `<` | |
| Gestión de stock activa | `manage_stock` | |
| Virtual / descargable | `virtual`, `downloadable` | |
| Nunca vendidos | `total_sales` = 0 | Justifica el plugin por sí solo |
| SKU | `sku` | `LIKE`; `*` da "los que tienen SKU" |
| Sin SKU | `sku` con `NOT EXISTS` | Caso de auditoría muy pedido |
| En oferta ahora | `date_on_sale_from` / `date_on_sale_to` | |
| Excluir IDs | `exclude` | |

### No son filtros pero ahorran tickets

- **Delimitador** (`woocommerce_product_export_delimiter`). Está fijo en coma.
  Excel en español espera `;` y la gente abre el CSV y ve todo en una columna.
  Muy relevante para el mercado ES/PT.
- **Tamaño del lote** (`woocommerce_product_export_batch_limit`). En hostings
  lentos el exportador revienta con catálogos grandes y no hay forma de bajarlo
  desde la interfaz.

### Cuestan más pero diferencian

- **Marca** (`product_brand`): taxonomía nativa desde la 9.4, ya presente en la
  11.0.0, pero el exportador no la ofrece. Va por `tax_query`.
- **Atributos**: "todos los de talla XL". `tax_query` sobre `pa_*`.
- **Auditoría de catálogo**: sin imagen, sin descripción corta, sin peso ni
  dimensiones teniendo envío activado, sin categoría asignada.
- **Perfiles guardados**: guardar "columnas + filtros" con un nombre y
  reutilizarlo. Es lo que convierte una utilidad en producto.

### Territorio premium

Exportación programada con Action Scheduler, entrega por email o SFTP, y una URL
con token para que un ERP o un feed tire del CSV sin entrar al admin. Encaja con
FacturaScripts Sync.

---

## 8. Decisiones pendientes

1. **Nombre y slug.** Propuesta sobre la mesa: *Export Filters for WooCommerce*
   (`export-filters-for-woocommerce`), que deja sitio para crecer. Alternativas:
   *Product Export Filters for WooCommerce*, *Export Date Filter for WooCommerce*.
   El nombre para mostrar puede llevar "WooCommerce"; el **slug no puede empezar**
   por marca ajena en wordpress.org.
2. **Alcance de la v1.** Propuesta: fechas, etiquetas, estado de publicación,
   estado de stock, nunca vendidos y delimitador. Perfiles, marca, atributos,
   auditoría y programación quedarían para la premium.
3. **Prefijo del código.** `JC_` es coherente con lo ya existente pero corto para
   un plugin público. Alternativas: `JCPEF_` o namespace `JoseConti\ExportFilters`.
4. **Refactor antes del segundo filtro.** El `if/elseif` no escala. Conviene un
   array de definiciones (`id`, `label`, tipo de control, callback que devuelve su
   trozo de `$args`), un bucle que pinta filas y otro que las aplica. Al sexto
   filtro, si no, hay un método de doscientas líneas.

---

## 9. Checklist de pruebas

- [ ] Un solo día: misma fecha en inicio y fin devuelve exactamente ese día
- [ ] Rango abierto por la izquierda (solo fecha fin) y por la derecha (solo inicio)
- [ ] Fechas invertidas: se ordenan solas, no devuelven cero
- [ ] "Todas las fechas" ignora los valores de los selectores aunque tengan contenido
- [ ] Catálogo que requiera varios lotes: el filtro se mantiene en todos
- [ ] Fecha inválida (30 de febrero) no rompe la exportación
- [ ] Combinado con selección de categoría: comprobar el comportamiento de las
      variaciones descrito en 4.2
- [ ] Combinado con "Exportar productos seleccionados" desde el listado
- [ ] Productos programados (`future`) y borradores entran en el rango
- [ ] Producto creado a las 23:50 con desfase horario del sitio: comprobar que cae
      en el día correcto (compara contra `post_date`, no `post_date_gmt`)
