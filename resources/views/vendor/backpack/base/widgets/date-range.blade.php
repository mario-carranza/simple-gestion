<div class="col-6">

<div class="row col-md-12 d-flex flex-row-reverse">
    <span class="font-weight-bold">Rango de fechas</span>
</div>
<div class="row col-md-12 d-flex flex-row-reverse">
    <div class="form-group col-sm-6">
        <span>Hasta</span>
        <input type='date' class="form-control" id="date-to"  data-date="" data-date-format="DD/MM/YYYY" max="{{ today()->toDateString() }}"/>
    </div>
    <div class="form-group col-sm-6">
        <span>Desde</span>
        <input type='date' class="form-control" id="date-from" data-date="" data-date-format="DD/MM/YYYY" max="{{ today()->toDateString() }}"/>
    </div>
</div>
</div>
