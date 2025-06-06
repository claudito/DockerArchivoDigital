<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Asistencia</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.1/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.bootstrap4.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fa fa-clock-o"></i> Asistencias</h2>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary" id="btn-filtro"><i class="fa fa-filter"></i>
                            Filtro</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for=""><strong>Leyenda:</strong></label>
                                    <span class="badge badge-pill badge-success">Puntual</span>
                                    <span class="badge badge-pill badge-warning">Tardanza</span>
                                    <span class="badge badge-pill badge-danger">Falta</span>
                                    <span class="badge badge-pill badge-info">Feriado</span>
                                    <span class="badge badge-pill badge-secondary"
                                        style="background-color: #c6ccd1;color:black">Fin de Semana</span>
                                    <span class="badge badge-pill badge-danger"
                                        style="background-color: #ea707a">Tardanza después de 8:30 am</span>
                                    <span class="badge badge-pill badge-danger" style="background-color: #9C27B0">Una
                                        Marcación</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <ul>
                                    <li><strong>Hora de Ingreso:</strong> 08:00 am</li>
                                    <li><strong>Hora de Salida:</strong> 17:30 pm</li>
                                    <li><strong>Tolerancia Ingreso:</strong> 08:00 am a 08:15 am</li>
                                    <li><strong>Tardanza:</strong> 08:15 am a 08:30 am</li>
                                    <li><strong>Falta:</strong> 08:30 am en Adelante</li>
                                </ul>
                            </div>
                        </div>
                        <div id="data"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="filtro" autocomplete="off">
        <!-- Modal -->
        <div class="modal fade" id="modal-filtro" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="">Periodo: <span style="color:red">(*)</span></label>
                            <input type="month" name="periodo" id="" class="form-control"
                                placeholder="Periodo" value="{{ date('Y-m') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="">Sede:  <span style="color:red">(*)</span> </label>
                            <select name="sede" id="sede" class="form-control select2" required>
                                <option value="">Seleccionar</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Dependencia: <span style="color:red">(*)</span></label>
                            <select name="dependencia" id="dependencia" class="form-control select2" required>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Oficina: <span style="color:red">(*)</span></label>
                            <select name="oficina" id="oficina" class="form-control select2" required>
                                <option value="TODOS">TODOS</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Centro de Costo: <span style="color:red">(*)</span></label>
                            <select name="centro_costo" id="centro_costo" class="form-control select2" required>
                                <option value="TODOS">TODOS</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Consultar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.bootstrap4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.colVis.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.full.min.js"></script>

    <script>
        $('.select2').select2({
            width: '100%',
            allowClear: true,
            dropdownParent: $('#modal-filtro')
        });


        $(document).ready(function() {
            $('.modal-title').html(`<i class="fa fa-filter"></i> Filtro`);
            $('#modal-filtro').modal('show');
        })

        $(document).on('click', '#btn-filtro', function(e) {
            $('.modal-title').html(`<i class="fa fa-filter"></i> Filtro`);
            $('#modal-filtro').modal('show');
            e.preventDefault();
        });


        $(document).on('submit', '#filtro', function(e) {
            parametros = $(this).serialize();
            $.ajax({
                url: '{{ route('asistencia.store') }}',
                type: 'POST',
                data: parametros,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Procesando Marcaciones...',
                        text: 'Por favor espere un momento',
                        allowOutsideClick: true,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    Swal.close();
                    $('#modal-filtro').modal('hide');
                    $('#data').html(data);
                }
            });
            e.preventDefault();
        });

        //Filtros:
        //Sede
        $(document).on('change', '#sede', function(e) {
            sede = $(this).val();
            if (sede != '') {
                $.ajax({
                    url: '{{ route('asistencia.filtro') }}',
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sede: sede,
                        op: 1
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Cargando',
                            text: 'Por favor espere un momento',
                            allowOutsideClick: true,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {

                        options = '<option value="">Seleccionar</option>';
                        $.each(data, function(index, value) {
                            options += `<option value="${value.id}">${value.name}</option>`;
                        });

                        $('#dependencia').html(options);

                        Swal.close();

                    }
                });
                e.preventDefault();
            }

            e.preventDefault();
        });

        $(document).on('change', '#dependencia', function(e) {
            dependencia = $(this).val();
            if (sede != '') {
                $.ajax({
                    url: '{{ route('asistencia.filtro') }}',
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sede: sede,
                        dependencia: dependencia,
                        op: 2
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Cargando',
                            text: 'Por favor espere un momento',
                            allowOutsideClick: true,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {

                        options = '<option value="">Seleccionar</option>';
                        options += '<option value="TODOS">TODOS</option>';
                        $.each(data, function(index, value) {
                            options += `<option value="${value.id}">${value.name}</option>`;
                        });

                        $('#oficina').html(options);

                        Swal.close();

                    }
                });
                e.preventDefault();
            }

            e.preventDefault();
        });

        $(document).on('change', '#oficina', function(e) {
            oficina = $(this).val();
            if (sede != '') {
                $.ajax({
                    url: '{{ route('asistencia.filtro') }}',
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sede: sede,
                        dependencia: dependencia,
                        oficina: oficina,
                        op: 3
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Cargando',
                            text: 'Por favor espere un momento',
                            allowOutsideClick: true,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {

                        options = '<option value="">Seleccionar</option>';
                        options += '<option value="TODOS">TODOS</option>';

                        $.each(data, function(index, value) {
                            options += `<option value="${value.id}">${value.name}</option>`;
                        });

                        $('#centro_costo').html(options);

                        Swal.close();

                    }
                });
                e.preventDefault();
            }

            e.preventDefault();
        });
    </script>
</body>

</html>
