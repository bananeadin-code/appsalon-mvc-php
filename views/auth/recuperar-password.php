<h1 class="nombre-pagina">Reestablecer contraseña</h1>
<p class="descripcion-pagina">Ingresa tu nueva contraseña a continuación</p>

<?php 
  include_once __DIR__ . "/../templates/alertas.php";
?>

<?php 
  if($error) return;
?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Contraseña</label>
        <input 
          type="password"
          id="password"
          name="password"
          placeholder="Tu nueva contraseña"
          >
    </div>
    <input type="submit" class="boton" value="Guardar">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Inicia sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Crear una</a>
</div>