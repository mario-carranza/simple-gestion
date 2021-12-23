<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@hasanyrole('Super admin|Administrador')
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-lock"></i> Control acceso</a>
	<ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Usuarios</span></a></li>

        @can('permission.role')
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permisos</span></a></li>
        @endcan

        @hasanyrole('Super admin')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('company') }}'><i class='nav-icon la la-building'></i> Empresas</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('branch') }}'><i class='nav-icon la la-store-alt'></i> Sucursales</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('branchcompany') }}'><i class='nav-icon la la-sitemap'></i> Empresa / Sucursal</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('companyuser') }}'><i class='nav-icon la la-sitemap'></i> Empresa / Usuario</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('branchuser') }}'><i class='nav-icon la la-sitemap'></i> Sucursal / Usuario</a></li>
        @endhasanyrole
	</ul>
</li>
@endhasanyrole

@hasanyrole('Super admin')
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-gear"></i> Configuración</a>
	<ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('setting') }}'><i class='nav-icon la la-gear'></i> Configuración</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('country') }}'><i class='nav-icon la la-globe'></i> Países</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('region') }}'><i class='nav-icon la la-globe'></i> Regiones</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('province') }}'><i class='nav-icon la la-globe'></i> Provincias</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('commune') }}'><i class='nav-icon la la-globe'></i> Comunas</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('currency') }}'><i class='nav-icon la la-dollar'></i> Monedas</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('businessactivity') }}'><i class='nav-icon la la-cubes'></i> Giros</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('tax') }}'><i class='nav-icon la la-calculator'></i> Impuestos</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('invoicetype') }}'><i class='nav-icon la la-exchange'></i> Documentos electrónicos</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('unit') }}'><i class='nav-icon la la-arrows-h'></i> Unidades</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('bank') }}'><i class='nav-icon la la-landmark'></i> Bancos</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('bankaccounttype') }}'><i class='nav-icon la la-briefcase'></i> Tipos cuentas</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('contacttype') }}'><i class='nav-icon la la-thumbs-up'></i> Tipos contactos</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('producttype') }}'><i class='nav-icon la la-book'></i> Tipos productos</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('paymentmethod') }}'><i class='nav-icon la la-cash-register'></i> Métodos pago</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('shippingmethod') }}'><i class='nav-icon la la-truck'></i> Métodos shipping</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('faqanswer') }}'><i class='nav-icon la la-question'></i> FaqAnswers</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('faqtopic') }}'><i class='nav-icon la la-question'></i> FaqTopics</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('banners') }}'><i class='nav-icon la la-question'></i> Banners</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('slider') }}'><i class='nav-icon la la-question'></i> Sliders</a></li>
	</ul>
</li>

<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-lightbulb-o"></i> Atributos</a>
	<ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('attributemodule') }}'><i class='nav-icon la la-plus-square'></i> Módulos</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('attributefamily') }}'><i class='nav-icon la la-th-large'></i> Familiar</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('attribute') }}'><i class='nav-icon la la-check-square'></i> Atributos</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('attributegroup') }}'><i class='nav-icon la la-table'></i> Grupos</a></li>
	</ul>
</li>
@endhasanyrole

@hasanyrole('Supervisor Marketplace')
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-gear"></i> CMS</a>
	<ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('banners') }}'><i class='nav-icon la la-question'></i> Banners</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('slider') }}'><i class='nav-icon la la-question'></i> Sliders</a></li>
	</ul>
</li>

@endhasanyrole

@canany(['customer.list'])
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-user"></i> Clientes</a>
	<ul class="nav-dropdown-items">
        @can('customer.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('customer') }}'><i class='nav-icon la la-user'></i> Clientes</a></li>
        @endcan

        @can('customersegment.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('customersegment') }}'><i class='nav-icon la la-shapes'></i> Segmentos</a></li>
        @endcan
	</ul>
</li>
@endcanany

@canany(['seller.list'])
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-briefcase"></i> Vendedores</a>
	<ul class="nav-dropdown-items">
        @can('seller.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('seller') }}'><i class='nav-icon la la-briefcase'></i> Vendedores</a></li>
        @endcan

        @can('sellercategory.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sellercategory') }}'><i class='nav-icon la la-layer-group'></i> Categorías</a></li>
        @endcan
	</ul>
</li>
@endcanany

@canany(['product.list'])
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-box"></i> Productos</a>
	<ul class="nav-dropdown-items">
        @can('product.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('product') }}'><i class='nav-icon la la-box'></i> Productos</a></li>
        @endcan

        @can('productbrand.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('productbrand') }}'><i class='nav-icon la la-tags'></i> Marcas</a></li>
        @endcan

        @can('productcategory.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('productcategory') }}'><i class='nav-icon la la-thumbtack'></i> Categorías</a></li>
        @endcan

        @can('productclass.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('productclass') }}'><i class='nav-icon la la-hashtag'></i> Clases</a></li>
        @endcan

        @can('productclassattribute.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('productclassattribute') }}'><i class='nav-icon la la-info'></i> Atributos clases</a></li>
        @endcan

        @can('productinventorysource.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('productinventorysource') }}'><i class='nav-icon la la-warehouse'></i> Bodegas</a></li>
        @endcan

   
        @can('productreservation.list')
                @if (backpack_user()->can('product.admin') || (backpack_user()->seller->is_turismo_rural ?? null))
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('productreservation') }}'><i class='nav-icon la la-map-marked'></i> Reservas</a></li>
                @endif
        @endcan
        </ul>
</li>
@endcanany

@canany(['quotation.list', 'order.list', 'invoice.list', 'payments.list'])
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-money-bill"></i> Ventas</a>
	<ul class="nav-dropdown-items">
        @can('quotation.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('quotation') }}'><i class='nav-icon la la-calculator'></i> Cotizaciones</a></li>
        @endcan

        @can('order.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('order') }}'><i class="nav-icon las la-file-invoice"></i> Órdenes</a></li>
        @endcan
        @can('payments.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('payments') }}'><i class='nav-icon la la-dollar'></i> Pagos</a></li>
        @endcan
        @can('invoice.list')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('invoice') }}'><i class='nav-icon la la-file-invoice-dollar'></i> Doc. Electrónicos </a></li>
        @endcan
        @can('sales.report')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('report/sales') }}'><i class="nav-icon las la-file-invoice"></i> Reporte</a></li>
        @endcan
	</ul>
</li>
@endcanany

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('communeshippingmethod') }}'><i class='nav-icon la la-truck'></i> Metodos de envío</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('plans') }}'><i class='nav-icon la la-question'></i> Plans</a></li>
@canany(['support.list'])
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-money-bill"></i> Servicio al cliente</a>
    <ul class="nav-dropdown-items">
        @can('support.list')
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('customersupport') }}'><i class='nav-icon la la-question'></i> Solicitudes</a></li>
        @endcan
    </ul>
</li>
@endcanany

@can('section.list')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('section') }}'><i class='nav-icon la la-table'></i> Secciones</a></li>
@endcan
