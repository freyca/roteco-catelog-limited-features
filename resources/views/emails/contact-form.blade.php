<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Contacto a través del formulario</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="background-color: #dee3e0">
        <div style="margin: auto; background-color: white; padding: 2em;">
            <!-- Header -->
            <div class="margin-bottom: 2em;">
                <h1
                style="font-weight: 700; font-size: 2em;">
                    Contacto a través del formulario
                </h1>
            </div>
            <!-- Transaction Details -->
            <div>
                <p class="text-gray-700 mb-2"
                    style="color: #464746; margin-bottom: 1em;"
                ><strong>Nombre: </strong>{{ ' ' . $name }}</p>
                <p style="color: #464746; margin-bottom: 1em;"><strong>Email: </strong>{{ ' ' . $email }}</p>
                <p style="color: #464746;"><strong>Mensaje:</strong></p>
                <p style="margin-bottom: 1em">
                    {{ $user_message }}
                </p>
            </div>

            <!-- Footer -->
            <div style="margin-top: 1em);">
                <h2 style="color: #6b6e6c; font-size: 1.3em; font-weight: 700;">Roteco</h2>
            </div>
        </div>
    </body>
</html>