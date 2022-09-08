<div class="row">
    <div class="col-md-6">
<form action="<?php echo base_url() . '/imagen/check' ?>" method="post" enctype="multipart/form-data">
    
    <h1>Propiedades de imagen</h1>
    <br>
    
    <input type="file" Accept="" id="seleccionArchivos" name="image" accept="image/*" class="form-control">
    <img class="img-thumbnail" id="imagenPrevisualizacion" src=""><br>

    <button Type="submit" class="btn-success">analizar imagen</button>
    </form>
    </div>
    
    <div class="col-md-6">
    <div class="notificaciones" id="notificaciones"></div>
    </div>
    <div class="row">
      <div class="col-md-6">
	<p>
		reconocimiento con Imagen
	</p>
	<h1>Selecciona un dispositivo</h1>
	<div>
		<select name="listaDeDispositivos" id="listaDeDispositivos"></select>
		<button id="boton">Tomar foto</button>
		<p id="estado"></p>
	</div>
	<br>
	<video muted="muted" id="video"></video>
	<canvas id="canvas" style="display: none;"></canvas>
      </div>
      <div class="col-md-6">
      <div class="notasimg" id="notasimg"></div>
      </div>
    </div>
</div>
   <br>
   


<script>
    const $seleccionArchivos = document.querySelector("#seleccionArchivos"),
    $imagenPrevisualizacion = document.querySelector("#imagenPrevisualizacion");

// Escuchar cuando cambie
$seleccionArchivos.addEventListener("change", () => {
  // Los archivos seleccionados, pueden ser muchos o uno
  const archivos = $seleccionArchivos.files;
  // Si no hay archivos salimos de la función y quitamos la imagen
  if (!archivos || !archivos.length) {
    $imagenPrevisualizacion.src = "";
    return;
  }
  // Ahora tomamos el primer archivo, el cual vamos a previsualizar
  const primerArchivo = archivos[0];
  // Lo convertimos a un objeto de tipo objectURL
  const objectURL = URL.createObjectURL(primerArchivo);
  // Y a la fuente de la imagen le ponemos el objectURL
  $imagenPrevisualizacion.src = objectURL;
  var imagenes = document.getElementById("seleccionArchivos").files[0].name;
  
  $("#notificaciones").load("<?php echo base_url() . '/foto/'?>"+ imagenes);
  
});
</script>

<script>
function tieneSoporteUserMedia() {
	return !!(navigator.getUserMedia || (navigator.mozGetUserMedia || navigator.mediaDevices.getUserMedia) || navigator.webkitGetUserMedia || navigator.msGetUserMedia)
}
function _getUserMedia() {
	return (navigator.getUserMedia || (navigator.mozGetUserMedia || navigator.mediaDevices.getUserMedia) || navigator.webkitGetUserMedia || navigator.msGetUserMedia).apply(navigator, arguments);
}
 
// Declaramos elementos del DOM
const $video = document.querySelector("#video"),
	$canvas = document.querySelector("#canvas"),
	$boton = document.querySelector("#boton"),
	$estado = document.querySelector("#estado"),
	$listaDeDispositivos = document.querySelector("#listaDeDispositivos");
 
// La función que es llamada después de que ya se dieron los permisos
// Lo que hace es llenar el select con los dispositivos obtenidos
const llenarSelectConDispositivosDisponibles = () => {
 
	navigator
		.mediaDevices
		.enumerateDevices()
		.then(function (dispositivos) {
			const dispositivosDeVideo = [];
			dispositivos.forEach(function (dispositivo) {
				const tipo = dispositivo.kind;
				if (tipo === "videoinput") {
					dispositivosDeVideo.push(dispositivo);
				}
			});
 
			// Vemos si encontramos algún dispositivo, y en caso de que si, entonces llamamos a la función
			if (dispositivosDeVideo.length > 0) {
				// Llenar el select
				dispositivosDeVideo.forEach(dispositivo => {
					const option = document.createElement('option');
					option.value = dispositivo.deviceId;
					option.text = dispositivo.label;
					$listaDeDispositivos.appendChild(option);
					console.log("$listaDeDispositivos => ", $listaDeDispositivos)
				});
			}
		});
}
 
(function () {
	// Comenzamos viendo si tiene soporte, si no, nos detenemos
	if (!tieneSoporteUserMedia()) {
		alert("Lo siento. Tu navegador no soporta esta característica");
		$estado.innerHTML = "Parece que tu navegador no soporta esta característica. Intenta actualizarlo.";
		return;
	}
	//Aquí guardaremos el stream globalmente
	let stream;
 
 
	// Comenzamos pidiendo los dispositivos
	navigator
		.mediaDevices
		.enumerateDevices()
		.then(function (dispositivos) {
			// Vamos a filtrarlos y guardar aquí los de vídeo
			const dispositivosDeVideo = [];
 
			// Recorrer y filtrar
			dispositivos.forEach(function (dispositivo) {
				const tipo = dispositivo.kind;
				if (tipo === "videoinput") {
					dispositivosDeVideo.push(dispositivo);
				}
			});
 
			// Vemos si encontramos algún dispositivo, y en caso de que si, entonces llamamos a la función
			// y le pasamos el id de dispositivo
			if (dispositivosDeVideo.length > 0) {
				// Mostrar stream con el ID del primer dispositivo, luego el usuario puede cambiar
				mostrarStream(dispositivosDeVideo[0].deviceId);
			}
		});
 
 
 
	const mostrarStream = idDeDispositivo => {
		_getUserMedia(
			{
				video: {
					// Justo aquí indicamos cuál dispositivo usar
					deviceId: idDeDispositivo,
				}
			},
			function (streamObtenido) {
				// Aquí ya tenemos permisos, ahora sí llenamos el select,
				// pues si no, no nos daría el nombre de los dispositivos
				llenarSelectConDispositivosDisponibles();
 
				// Escuchar cuando seleccionen otra opción y entonces llamar a esta función
				$listaDeDispositivos.onchange = () => {
					// Detener el stream
					if (stream) {
						stream.getTracks().forEach(function (track) {
							track.stop();
						});
					}
					// Mostrar el nuevo stream con el dispositivo seleccionado
					mostrarStream($listaDeDispositivos.value);
				}
 
				// Simple asignación
				stream = streamObtenido;
 
				// Mandamos el stream de la cámara al elemento de vídeo
				$video.srcObject = stream;
				$video.play();
 
				//Escuchar el click del botón para tomar la foto
				$boton.addEventListener("click", function () {
 
					//Pausar reproducción
					$video.pause();
 
					//Obtener contexto del canvas y dibujar sobre él
					let contexto = $canvas.getContext("2d");
					$canvas.width = $video.videoWidth;
					$canvas.height = $video.videoHeight;
					contexto.drawImage($video, 0, 0, $canvas.width, $canvas.height);
 
					let foto = $canvas.toDataURL(); //Esta es la foto, en base 64
					$estado.innerHTML = "Enviando foto. Por favor, espera...";
					fetch("<?php echo base_url() . '/guardar_imagen'?>", {
						method: "POST",
						body: encodeURIComponent(foto),
						headers: {
							"Content-type": "application/x-www-form-urlencoded",
						}
					})
						.then(resultado => {
							// A los datos los decodificamos como texto plano
							return resultado.text()
						})
						.then(nombreDeLaFoto => {
							// nombreDeLaFoto trae el nombre de la imagen que le dio PHP
							console.log("La foto fue enviada correctamente");
              $("#notasimg").load("<?php echo base_url() . '/fotoimg/'?>"+ nombreDeLaFoto);
							$estado.innerHTML = `Foto guardada con éxito. Puedes verla <a target='_blank' href='./${nombreDeLaFoto}'> aquí</a>`;
						})
 
					//Reanudar reproducción
					$video.play();
				});
			}, function (error) {
				console.log("Permiso denegado o error: ", error);
				$estado.innerHTML = "No se puede acceder a la cámara, o no diste permiso.";
			});
	}
})();
</script>
<style>
		@media only screen and (max-width: 700px) {
			video {
				max-width: 100%;
			}
		}
	</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" type="text/javascript"></script>
