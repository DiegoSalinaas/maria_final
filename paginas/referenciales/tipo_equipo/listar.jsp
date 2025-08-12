<%@page import="java.sql.ResultSet"%>
<%@page import="clases.conexion"%>
<input type="text" value="0" hidden id="id_tipo_equipo">
<input type="text" value="" hidden id="nombre_anterior">
<div class="row">
    <div class="col-md-12">
        <hr> 
    </div>

    <div class="col-md-4" >
        <label class="control-label">Descripcion</label>
        <input type="text" class="form-control" id="descripcion" placeholder="Ingrese una descripcion"/>
    </div>

   

    </div>

    <div class="col-md-12" style="margin-top: 25px;">
       
        <button class="btn btn-success" onclick="guardarTipoEquipo(); return false;"><i class="far fa-save"></i> Guardar</button>
        <button class="btn btn-primary" onclick="mostrarReporteProveedor();"><i class="fa fa-print" aria-hidden="true"></i> Imprimir</button>

    </div>


</div>
<hr> 
<div class="row" id="tabla-busqueda">
    <div class="col-md-6">
        <label >Buscador</label>
        <input type="text" id="b_tipo_equipo" class="form-control">
    </div>
    <div class="col-md-12" style="margin-top: 30px;">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th >DESCRIPCION</th>
                    <th >ESTADO</th>
                    <th >Operaciones</th>

                </tr>
            </thead>
            <tbody id="tipo_equipo_tb"> </tbody>
        </table>
    </div>
</div>



