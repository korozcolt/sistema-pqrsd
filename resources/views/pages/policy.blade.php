@extends('layouts.page')
@section('content-page')
    <!-- Page banner Area -->
    <div class="page-banner bg-2">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="page-content">
                        <h2>Políticas de Seguridad, Derechos y Deberes de los Pasarejos</h2>
                        <ul>
                            <li><a href="{{ url('/')}}">Inicio</a></li>
                            <li>Políticas de Seguridad y Derechos y Deberes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page banner Area -->

    <!-- Terms ConditionArea -->
    <div class="privacy-policy ptb-100">
        <div class="container">
            <div class="privacy-policy-text">
                <h2 id="terms">Terminos y condiciones</h2>
                <ul>
                    <li><i class='bx bx-check'></i><strong> 1. </strong> Tiquete es v&aacute;lido para viajar en la fecha, hora, puesto y destino indicado.</li>
                    <li><i class='bx bx-check'></i><strong> 2. </strong> El pasajero deberá presentarse 40 minutos antes de la hora de salida indicada en el tiquete de viaje adquirido en el punto de venta autorizado.</li>
                    <li><i class='bx bx-check'></i><strong> 3. </strong> Modificaci&oacute;n de la hora o fecha de este tiquete, es requisito indispensable, que el pasajero lo presente en la oficina expendedora por lo menos 3 horas antes de la iniciaci&oacute;n del viaje.</li>
                    <li><i class='bx bx-check'></i><strong> 4. </strong> Pasajero tiene derecho a llevar en bodega hasta 25 kg de maletas de viaje sin costo adicional, este equipaje no debe exceder las dimensiones de .80 cms de alto por .50 cms de ancho y .25 cms de fondo. Está permitida una pieza de equipaje de mano que no exceda 10 kg de peso y las dimensiones de .45 cms de alto,.30 cms de ancho y .25 cms de fondo, y una pieza personal que no exceda 5 kg; este equipaje es personal e intransferible. El equipaje deberá contener solo prendas de vestir y artículos de aseo personal, cualquier equipaje que contenga dinero, joyas, equipos electrónicos, etc. deberá ser declarado al momento de abordar el vehículo de lo contrario la empresa no responderá por este equipaje en el caso de pérdida o daño.</li>
                    <li><i class='bx bx-check'></i><strong> 5. </strong> Pasajero que no se presente ante la iniciaci&oacute;n del viaje, no tendr&aacute; derecho a la devoluci&oacute;n del valor pagado.</li>
                    <li><i class='bx bx-check'></i><strong> 6. </strong> Momento de registrar el equipaje de bodega, el pasajero debe reclamar al conductor o auxiliar la ficha de identificación de su equipaje. La empresa no responder&aacute; por equipaje sin ficha.</li>
                    <li><i class='bx bx-check'></i><strong> 7. </strong> Empresa no transportara a personas que se presenten en estado de embriaguez, bajo el efecto de estupefacientes o en notorio estado de desaseo.</li>
                    <li><i class='bx bx-check'></i><strong> 8. </strong> Pasajero no podr&aacute; llevar consigo, ni en sus equipajes, material inflamable o har&aacute; responsable al pasajero de todos los perjuicios que puedan suceder a la empresa o a los dem&aacute;s pasajeros. Igualmente, el pasajero no podr&aacute; transportar como equipaje cajas de cart&oacute;n, en caso contrario la empresa no responder&aacute; por deterioro o perdida.</li>
                    <li><i class='bx bx-check'></i><strong> 9. </strong> Empresa responder&aacute; por perdida o da&ntilde;o del equipaje hasta la suma de 5 SDMLV (SALARIOS DIARIOS MINIMOS LEGALES VIGENTES).</li>
                    <li><i class='bx bx-check'></i><strong> 10.</strong> Cualquier reclamaci&oacute;n, la empresa se acoge a lo estipulado en el c&oacute;digo de comercio.</li>
                    <li><i class='bx bx-check'></i><strong> 11.</strong> La empresa no responde por demoras, da&ntilde;os, perdidas etc. Que se ocasionen por fuerza mayor, caso fortuito, culpa del pasajero o hecho de un tercero.</li>
                    <li><i class='bx bx-check'></i><strong> 12.</strong> En caso de accidente de tr&aacute;nsito las lesiones que sufre el pasajero estar&aacute;n amparadas por la p&oacute;liza de seguro obligatorio de da&ntilde;os corporales causados a las personas en el accidente de tr&aacute;nsito que tiene contratada la empresa y el seguro de responsabilidad civil y contractual.</li>
                    <li>La Cooperativa Especializada de Transportadores Torcoroma con NIT 890.400.565 &ndash; 5, como responsable de los datos personales obtenidos a trav&eacute;s de los distintos canales de atenci&oacute;n, han puesto a mi disposici&oacute;n la l&iacute;nea de atenci&oacute;n (605) 4423390 / 3106543797, y el correo electr&oacute;nico cooptorcoroma@hotmail.com disponible de lunes a viernes de 7:00 am a 5:00 pm. Y s&aacute;bados de 8:00 am a 12:00 pm, para atenci&oacute;n de los derechos mencionados en la pol&iacute;tica de tratamiento de datos personales. Estos los puede ejercer a trav&eacute;s de los canales dispuesto por la Cooperativa Especializada de Transportadores Torcoroma para la atenci&oacute;n al p&uacute;blico y observando la pol&iacute;tica de tratamiento de datos personales de la Cooperativa Especializada de Transportadores Torcoroma.</li>
                </ul>

                <h2 id="security">POL&Iacute;TICA&nbsp;INTEGRAL</h2>
                <p>La Cooperativa Especializada de Transportadores Torcoroma., en la prestaci&oacute;n de servicio de transporte terrestre de pasajeros en la modalidad b&aacute;sica corriente y especial, est&aacute; comprometida con la oportunidad, confort, seguridad y cumplimiento de los requisitos legales y reglamentarios, ofreciendo ambientes de trabajo adecuado y seguros, en pro del crecimiento de la organizaci&oacute;n y la satisfacci&oacute;n de las necesidades de sus partes interesadas pertinentes, para lo anterior establece como eje principal:</p>
                <ul>
                    <li><i class='bx bx-check'></i>Atender&nbsp;y&nbsp;satisfacer&nbsp;las&nbsp;necesidades&nbsp;de&nbsp;nuestros&nbsp;clientes&nbsp;y/o&nbsp;usuarios</li>
                    <li><i class='bx bx-check'></i>Cumplir los requisitos legales en la prestaci&oacute;n del servicio de transporte terrestre de pasajeros, SST y los dem&aacute;s aplicables a la organizaci&oacute;n.</li>
                    <li><i class='bx bx-check'></i>Garantizar condiciones operativas &oacute;ptimas para la prestaci&oacute;n del servicio</li>
                    <li><i class='bx bx-check'></i>Identificar los peligros, evaluar y valorar los riesgos derivados del desarrollo de las actividades y determinar los respectivos controles.</li>
                    <li><i class='bx bx-check'></i>Prevenir la aparici&oacute;n de enfermedades, la ocurrencia de accidentes laborales asociados al riesgo p&uacute;blico por accidente de tr&aacute;nsito y biomec&aacute;nico identificados como riesgos prioritarios.</li>
                    <li><i class='bx bx-check'></i>Proteger la seguridad y la salud de los trabajadores mediante la mejora continua del Sistema de Gesti&oacute;n Integral.</li>
                </ul>
                <h2>POL&Iacute;TICA&nbsp;DE&nbsp;PREVENCI&Oacute;N&nbsp;DEL&nbsp;CONSUMO&nbsp;DE&nbsp;ALCOHOL,&nbsp;TABACO&nbsp;Y&nbsp;OTRAS&nbsp;SUSTANCIAS&nbsp;PSICOACTIVAS</h2>
                <p>Cooperativa Especializada de Transportadores Torcoroma., comprometida con procurar un ambiente sano y un estilo de vida saludable, teniendo en cuenta que el consumo de alcohol, y otras sustancias psicoactivas, así como el tabaquismo es un creciente problema social que afecta las condiciones de vida individual, familiar, social y laboral, en este último ámbito perjudicando el desempeño en términos de productividad de los trabajadores, así mismo aspectos como la salud, la seguridad y el bienestar; la empresa desarrolla estrategias enfocadas a la prevención del consumo de alcohol, y otras sustancias psicoactivas y tabaquismo, incluyendo a todos los niveles de la organización.</p>
                <ul>
                    <li><i class='bx bx-check'></i>Prohibir el consumo de alcohol y sustancias psicoactivas durante la jornada laboral y en todos los lugares de trabajo.</li>
                    <li><i class='bx bx-check'></i>Prohibir el ingreso de personal a todos los lugares de trabajo en estado de embriaguez, guayabo o bajo los efectos de sustancias psicoactivas.</li>
                    <li><i class='bx bx-check'></i>Implementar actividades para el fomento de estilos de vida saludable y la prevenci&oacute;n en torno al consumo de alcohol, sustancias psicoactivas y tabaquismo.</li>
                    <li><i class='bx bx-check'></i>Mantener todas nuestras instalaciones libres de humo prohibiendo el consumo de tabco dentro de la empresa.</li>
                    <li><i class='bx bx-check'></i>La empresa o sus clientes podr&aacute;n llevar a cabo pruebas de alcoholimetr&iacute;a a sus trabajadores para verificar el cumplimiento de esta pol&iacute;tica, si as&iacute; lo considera conveniente. El no acceder a la prueba se considerar&aacute; como indicio de sospecha y se tomar&aacute;n medidas disciplinarias.</li>
                </ul>

                <h2 id="rights">POL&Iacute;TICA&nbsp;DE&nbsp; EQUIPAJE</h2>
                <p>Para ver el contenido solo dale click en el enlace y descargalo para que puedas leerlo. <a target="_blank" href="{{ asset('docs/Politica Transporte Equipaje.pdf') }}">DOCUMENTO COMPLETO</a></p>

                <h2 id="rights">POL&Iacute;TICA&nbsp;DE&nbsp; REEMBOLSO</h2>
                <p>Para ver el contenido solo dale click en el enlace y descargalo para que puedas leerlo. <a target="_blank" href="{{ asset('docs/01 - P-GT-04 REEMBOLSO.pdf') }}">DOCUMENTO COMPLETO</a></p>

                <h2 id="rights">DERECHOS Y DEBERES DE LOS PASAJEROS</h2>
                <p>Para ver el contenido solo dale click en el enlace y descargalo para que puedas leerlo. <a target="_blank" href="{{ asset('docs/Cartilla-de-derechos-y-deberes-de-transporte-terrestre.pdf') }}">DOCUMENTO COMPLETO</a></p>
            </div>
        </div>
    </div>
    <!-- End Terms Condition Area -->

@endsection
