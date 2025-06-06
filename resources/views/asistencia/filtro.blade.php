<style>
    div.dt-buttons {
        margin-bottom: 15px;
    }
</style>

<div class="table-responsive">
    <table id="consulta" class="table table-bordered table-striped table-hover" style="font-size: 11px;">
        <thead>
            <tr>
                <th>Período</th>
                <th>Dependencia</th>
                <th>Oficina</th>
                <th>Centro de Costo</th>
                <th>Nombre</th>
                <th class="text-center">Nro de Documento</th>
                <th>Servicio</th>
                @foreach ($dias as $value)
                    <th>{{ $value->dia }}</th>
                @endforeach

            </tr>
        </thead>
    </table>
</div>
<script>
    json = @json($asistencia)


    $('#consulta').DataTable({
        destroy: true,
        dom: 'lBfrtip',
        buttons: [{
            extend: 'excel',
            text: '<i class="fa fa-download"></i> Exportar a Excel',
            className: 'btn btn-success btn-sm',
            title: 'Asistencias {{ $periodo }}',
            sheetName: '{{ $periodo }}',
            exportOptions: {
                columns: ':not(.notexport)'
            }
        }],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.1/i18n/es-ES.json',
        },
        iDisplayLength: 25,
        bAutoWidth: false,
        data: json,
        columns: [{
                data: null,
                render: function(data) {
                    return "{{ $periodo }}"
                }
            }, {
                data: 'dependencia',
            },
            {
                data: 'oficina',
            },
            {
                data: 'centro_costo',
            },
            {
                data: 'fullname',
            },
            {
                data: 'dni',
                className: 'text-center'
            },
            {
                data: 'servicio'
            },
            @foreach ($dias as $key => $value)
                {
                    data: null,
                    render: function(data) {
                        var dia = "{{ $value->dia }}";
                        if (data.marcaciones && data.marcaciones[dia]) {
                            var ingreso = data.marcaciones[dia][0]['ingreso']
                            var salida = data.marcaciones[dia][0]['salida']
                            if (ingreso == salida) {
                                salida = "";
                            }
                            //console.log(salida);
                            return ingreso + " -<br>" + salida;
                        } else {
                            return ''; // o 'Sin datos' si prefieres un mensaje visible
                        }
                    }
                },
            @endforeach
        ],
        rowCallback: function(row, data) {
            @foreach ($dias as $key => $value)
                var dia = "{{ $value->dia }}";
                var dia_semana = "{{ $value->dia_semana }}";
                var feriado = "{{ $value->feriado }}"
                //console.log(feriado);
                var colIndex = 7 + {{ $key }}; // Las primeras 4 columnas son fijas
                if (data.marcaciones && data.marcaciones[dia]) {
                    var ingreso = data.marcaciones[dia][0]['ingreso'];
                    var salida = data.marcaciones[dia][0]['salida'];
                    // Validar si solo existe ingreso y salida es vacío o null
                    if (ingreso) {
                        // Convertir ingreso a minutos para comparar fácilmente
                        var parts = ingreso.split(':');
                        var mins = parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);

                        if (mins >= 480 && mins <= 495) { // 08:00 a 08:15
                            $('td:eq(' + colIndex + ')', row).css('background-color', '#28A745');
                        } else if (mins >= 496 && mins <= 510) { // 08:16 a 08:30
                            $('td:eq(' + colIndex + ')', row).css('background-color', '#F2B707');
                        } else if (mins >= 511) { // 08:31 en adelante
                            $('td:eq(' + colIndex + ')', row).css('background-color', '#ea707a');
                        } else {
                            // Antes de 08:00, puedes dejarlo sin color o asignar otro si lo deseas
                            $('td:eq(' + colIndex + ')', row).css('background-color', '#28A745');
                        }
                    }
                    if (salida) {

                        periodo = "{{ $periodo }}"
                        nowPeriodo = "{{ $nowPeriodo }}"
                        nowDia = "{{ $nowDia }}"
                        var dia = "{{ $value->dia }}";

                        if (!(periodo == nowPeriodo && nowDia == dia)) {
                            if (ingreso == salida) {
                                $('td:eq(' + colIndex + ')', row).css('background-color', '#9C27B0');
                                $('td:eq(' + colIndex + ')', row).css('color', 'white');
                            }
                        }

                    }
                } else {
                    if (!['Saturday', 'Sunday'].includes(dia_semana)) {
                        $('td:eq(' + colIndex + ')', row).css('background-color', '#DC3545');
                    } else {
                        $('td:eq(' + colIndex + ')', row).css('background-color', '#c6ccd1');
                    }
                }

                if (feriado == "1") {
                    $('td:eq(' + colIndex + ')', row).css('background-color', '#17A2B8');
                }
            @endforeach
        }
    });
</script>
