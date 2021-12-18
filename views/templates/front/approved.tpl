{extends file="page.tpl"}

{block name='page_content'}
<h1>Great!!! Your order has been pay by Finvero credit.</h1>
<p>Now your developer needs implement some code for place the order of the client. :)</p>
</br></br>
<p><b>Implementaciones pendientes por falta de tiempo de desarrollo:</b></p>
</br>
<p>Validar la orden para darla de alta con la información de los parametros del carrito</p>
<p>Desplegar mensaje de confirmación para el cliente en la orden</p>
<p>Enviar correo de confirmación al cliente</p>
</br></br>
<p><b>Bugs encontrados pendientes de arreglar:</b></p>
</br>
<p>El switch Finvero de la lista de productos no refleja los datos del campo is_finvero_product de la tabla ps_finvero_products</p>
<p>El switch Finvero del formulario de edición de productos no refleja los datos del campo is_finvero_product de la tabla ps_finvero_products.</p>
<p>El switch Finvero del formulario de creación de productos no guarda los datos del campo is_finvero_product de la tabla ps_finvero_products.</p>
</br></br>
<p><b>TROUBLESHOOTING</b></p>
</br>
<p>Para efectos de prueba del módulo, debido a que el bug no deja crear registros en la tabla, ni modificarlos, se adjunta archivo ps_finvero_products.sql y, modificando el campo is_finvero_products a 1 para todos los productos que estén en el carrito de prueba, se puede observar que se muestra la opción de pago de acuerdo a la regla de negocio presentada para esta prueba técnica.</p>
{/block}